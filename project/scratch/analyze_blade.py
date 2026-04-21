#!/usr/bin/env python3
"""
Blade File Connection Analyzer
Analyzes which blade files are connected to Controllers and blade-to-blade references.
"""

import os
import re
import sys
from pathlib import Path
from collections import defaultdict

PROJECT_ROOT = Path(__file__).parent.parent
VIEWS_DIR    = PROJECT_ROOT / "resources" / "views"
CONTROLLERS_DIR = PROJECT_ROOT / "app" / "Http" / "Controllers"
ROUTES_DIR   = PROJECT_ROOT / "routes"

# ─────────────────────────────────────────────────────────────
# 1. Collect all blade files -> dot.path => absolute path
# ─────────────────────────────────────────────────────────────
def get_blade_files(base_dir: Path) -> dict:
    result = {}
    for p in sorted(base_dir.rglob("*.blade.php")):
        relative = p.relative_to(base_dir)
        dot_path = str(relative).replace(os.sep, ".").replace(".blade.php", "")
        result[dot_path] = str(p)
    return result

def normalize_view_name(name: str) -> str:
    """Normalize view name: convert slashes to dots and strip whitespace.
    Laravel accepts both 'videos/create_cat' and 'videos.create_cat'."""
    return name.strip().replace('/', '.')

# ─────────────────────────────────────────────────────────────
# 2. Extract view() calls from PHP files in a directory
# ─────────────────────────────────────────────────────────────
def get_view_calls(directory: Path) -> dict:
    """Returns {normalized_dot_view_name: [file_path, ...]}"""
    view_map = defaultdict(list)
    for p in directory.rglob("*.php"):
        content = p.read_text(encoding="utf-8", errors="ignore")
        matches = re.findall(r'\bview\s*\(\s*[\'"]([^\'"]+)[\'"]', content)
        for view_name in matches:
            normalized = normalize_view_name(view_name)
            view_map[normalized].append(str(p))
    return dict(view_map)

# ─────────────────────────────────────────────────────────────
# 3. Extract blade-to-blade references (@extends, @include, @component)
# ─────────────────────────────────────────────────────────────
def get_blade_to_blade(blade_files: dict) -> dict:
    """Returns {parent_dot_path: [referenced_dot_paths]}"""
    connections = {}
    for dot_path, full_path in blade_files.items():
        content = Path(full_path).read_text(encoding="utf-8", errors="ignore")
        refs = set()

        # @extends('layout.app') or @extends('layout/app')
        for m in re.finditer(r'@extends\s*\(\s*[\'"]([^\'"]+)[\'"]', content):
            refs.add(normalize_view_name(m.group(1)))

        # @include, @includeIf, @includeWhen, @includeUnless, @includeFirst
        for m in re.finditer(r'@include(?:If|When|Unless|First)?\s*\(\s*[\'"]([^\'"]+)[\'"]', content):
            refs.add(normalize_view_name(m.group(1)))

        # @component('...')
        for m in re.finditer(r'@component\s*\(\s*[\'"]([^\'"]+)[\'"]', content):
            refs.add(normalize_view_name(m.group(1)))

        if refs:
            connections[dot_path] = sorted(refs)

    return connections

# ─────────────────────────────────────────────────────────────
# MAIN
# ─────────────────────────────────────────────────────────────
all_blades = get_blade_files(VIEWS_DIR)

# All direct view() calls from controllers & routes
controller_views = get_view_calls(CONTROLLERS_DIR)
route_views      = get_view_calls(ROUTES_DIR)

# Merged direct views
all_direct_views = {}
for view_name, files in {**controller_views, **route_views}.items():
    all_direct_views[view_name] = files

# Blade-to-blade map
blade_to_blade = get_blade_to_blade(all_blades)

# Reverse map: which blades REFERENCE this dot_path
referenced_by_blade = defaultdict(list)
for parent, children in blade_to_blade.items():
    for child in children:
        referenced_by_blade[child].append(parent)

# ─────────────────────────────────────────────────────────────
# BFS: Walk UP the include chain to find if any ancestor is
# directly controller-connected. Handles arbitrary depth, e.g.:
#   layouts.header → layouts.masterhead → dashboard (controller)
# ─────────────────────────────────────────────────────────────
def is_transitively_connected(dot_path: str, ref_by: dict, direct_views: dict):
    """
    BFS up the blade-include graph from dot_path.
    Returns True if ANY ancestor is a directly controller-connected view.
    """
    visited = {dot_path}
    queue = list(ref_by.get(dot_path, []))
    while queue:
        current = queue.pop(0)
        if current in visited:
            continue
        visited.add(current)
        if current in direct_views:
            return True
        for grandparent in ref_by.get(current, []):
            if grandparent not in visited:
                queue.append(grandparent)
    return False

# ─────────────────────────────────────────────────────────────
# Classify each blade file
# ─────────────────────────────────────────────────────────────
directly_connected = {}
blade_connected    = {}
not_connected      = {}

for dot_path, full_path in all_blades.items():
    # Check both dot-form and slash-form (Laravel accepts both)
    is_direct = dot_path in all_direct_views
    immediate_parents = referenced_by_blade.get(dot_path, [])
    rel_path = str(Path(full_path).relative_to(PROJECT_ROOT))

    if is_direct:
        all_refs = list(set(all_direct_views.get(dot_path, [])))
        ref_names = [str(Path(r).relative_to(PROJECT_ROOT)) for r in all_refs]
        directly_connected[dot_path] = {
            "file": rel_path,
            "referenced_in": sorted(ref_names),
            "raw_view_calls": sorted(set(
                k for k in (controller_views | route_views)
                if normalize_view_name(k) == dot_path
            ))
        }
    elif is_transitively_connected(dot_path, referenced_by_blade, all_direct_views):
        # BFS up full chain: handles e.g. layouts.header -> layouts.masterhead -> dashboard
        blade_connected[dot_path] = {
            "file": rel_path,
            "included_by": immediate_parents,
        }
    else:
        not_connected[dot_path] = {
            "file": rel_path,
            "included_by_blade": immediate_parents,
        }

# ─────────────────────────────────────────────────────────────
# OUTPUT
# ─────────────────────────────────────────────────────────────
DIV  = "─" * 80
DIV2 = "═" * 80

print(f"\n{'═'*78}")
print(f"{'BLADE FILE CONNECTION ANALYSIS REPORT':^78}")
print(f"{'═'*78}\n")
print(f"  Total blade files found : {len(all_blades)}")
print(f"  Views directory         : {VIEWS_DIR}")
print()

# ── Section 1: Directly connected ──────────────────────────
print(DIV)
print(f"✅  DIRECTLY CONNECTED TO CONTROLLER / ROUTE  ({len(directly_connected)} files)")
print(DIV)
for dot_path, info in sorted(directly_connected.items()):
    print(f"  📄 {dot_path}")
    for ref in info["referenced_in"]:
        raw = info.get('raw_view_calls', [])
        raw_str = f"  (called as: {', '.join(repr(r) for r in raw)})" if raw and list(raw) != [dot_path] else ""
        print(f"      └─ {ref}{raw_str}")
print()

# ── Section 2: Connected via blade chain ────────────────────
print(DIV)
print(f"🔗  CONNECTED VIA BLADE-TO-BLADE  ({len(blade_connected)} files)")
print(DIV)
for dot_path, info in sorted(blade_connected.items()):
    print(f"  📄 {dot_path}")
    for parent in info["included_by"]:
        tag = "✅ controller-connected" if parent in all_direct_views else "🔗 blade-only"
        print(f"      └─ included/extended by: {parent}  [{tag}]")
print()

# ── Section 3: Not connected ────────────────────────────────
print(DIV)
print(f"❌  NOT CONNECTED (orphan blades)  ({len(not_connected)} files)")
print(DIV)
for dot_path, info in sorted(not_connected.items()):
    print(f"  🚫 {dot_path}")
    print(f"      └─ File: {info['file']}")
    for parent in info["included_by_blade"]:
        tag = "✅ controller-connected" if parent in all_direct_views else "❌ also orphan"
        print(f"      └─ Included by (blade): {parent}  [{tag}]")
print()

# ── Section 4: Blade-to-blade map ───────────────────────────
print(DIV)
print(f"📋  BLADE-TO-BLADE REFERENCES (all @extends/@include/@component)")
print(DIV)
for parent in sorted(blade_to_blade.keys()):
    children = blade_to_blade[parent]
    print(f"  📄 {parent}")
    for child in children:
        exists = "✅ exists" if child in all_blades else "⚠️  FILE NOT FOUND IN VIEWS"
        print(f"      └─ → {child}  [{exists}]")
print()

# ── Summary ─────────────────────────────────────────────────
print(DIV)
print("SUMMARY")
print(DIV)
print(f"  ✅ Directly controller/route connected : {len(directly_connected)}")
print(f"  🔗 Blade-to-blade connected            : {len(blade_connected)}")
print(f"  ❌ Orphan / Not connected              : {len(not_connected)}")
print(f"  📋 Total blade files scanned           : {len(all_blades)}")
print(DIV)
