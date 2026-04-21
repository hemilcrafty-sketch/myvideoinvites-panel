<?php

/**
 * Blade File Connection Analyzer
 * Analyzes which blade files are connected to controllers (directly or via blade-to-blade)
 */

$projectRoot = __DIR__ . '/..';
$viewsDir    = $projectRoot . '/resources/views';
$controllersDir = $projectRoot . '/app/Http/Controllers';
$routesDir   = $projectRoot . '/routes';

// ─────────────────────────────────────────────
// 1. Collect all blade files (dot-path format)
// ─────────────────────────────────────────────
function getBladeFiles(string $dir, string $baseDir): array {
    $result = [];
    $rii = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));
    foreach ($rii as $file) {
        if ($file->isFile() && str_ends_with($file->getFilename(), '.blade.php')) {
            $fullPath = $file->getRealPath();
            $relative = str_replace($baseDir . DIRECTORY_SEPARATOR, '', $fullPath);
            $relative = str_replace(DIRECTORY_SEPARATOR, '/', $relative);
            $dotPath  = str_replace('.blade.php', '', $relative);
            $dotPath  = str_replace('/', '.', $dotPath);
            $result[$dotPath] = $fullPath;
        }
    }
    ksort($result);
    return $result;
}

// ─────────────────────────────────────────────
// 2. Get all view() calls from Controllers + Routes
// ─────────────────────────────────────────────
function getViewCallsFromFiles(string $dir, string $pattern = '*.php'): array {
    $views = [];
    $files = [];

    // Recursive scan
    $rii = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));
    foreach ($rii as $file) {
        if ($file->isFile() && str_ends_with($file->getFilename(), '.php')) {
            $files[] = $file->getRealPath();
        }
    }

    foreach ($files as $filepath) {
        $content = file_get_contents($filepath);
        // Match view('...') and view("...")
        preg_match_all('/\bview\s*\(\s*[\'"]([^\'"]+)[\'"]/i', $content, $matches);
        foreach ($matches[1] as $viewName) {
            $views[$viewName][] = $filepath;
        }
    }
    return $views;
}

// ─────────────────────────────────────────────
// 3. Get blade-to-blade connections (@include, @extends, @component)
// ─────────────────────────────────────────────
function getBladeToBlade(array $bladeFiles): array {
    $connections = []; // $parentDotPath => [child dot paths]
    foreach ($bladeFiles as $dotPath => $fullPath) {
        $content = file_get_contents($fullPath);
        $refs = [];

        // @extends('...')
        preg_match_all('/@extends\s*\(\s*[\'"]([^\'"]+)[\'"]\s*\)/i', $content, $m);
        foreach ($m[1] as $ref) $refs[] = $ref;

        // @include('...')
        preg_match_all('/@include(?:If|When|Unless|First)?\s*\(\s*[\'"]([^\'"]+)[\'"]/i', $content, $m);
        foreach ($m[1] as $ref) $refs[] = $ref;

        // @component('...')
        preg_match_all('/@component\s*\(\s*[\'"]([^\'"]+)[\'"]/i', $content, $m);
        foreach ($m[1] as $ref) $refs[] = $ref;

        if (!empty($refs)) {
            $connections[$dotPath] = array_unique($refs);
        }
    }
    return $connections;
}

// ─────────────────────────────────────────────
// MAIN ANALYSIS
// ─────────────────────────────────────────────

$allBlades = getBladeFiles($viewsDir, $viewsDir);

// Views referenced directly by Controllers
$controllerViewCalls = getViewCallsFromFiles($controllersDir);
// Views referenced in routes/web.php etc.
$routeViewCalls = getViewCallsFromFiles($routesDir);

// Merge all direct controller + route view calls
$allDirectViews = [];
foreach (array_merge($controllerViewCalls, $routeViewCalls) as $viewName => $files) {
    $allDirectViews[$viewName] = true;
}

// Blade-to-blade connections
$bladeToBlade = getBladeToBlade($allBlades);

// Build a reverse map: which blade files are @included/@extended BY another blade
$referencedByBlade = []; // dotPath => [parent dot paths]
foreach ($bladeToBlade as $parent => $children) {
    foreach ($children as $child) {
        $referencedByBlade[$child][] = $parent;
    }
}

// ─────────────────────────────────────────────
// Classify each blade file
// ─────────────────────────────────────────────
$directlyConnected    = []; // directly used in controller/route
$bladeConnected       = []; // used via blade-to-blade (not directly in controller)
$notConnected         = []; // not connected anywhere

foreach ($allBlades as $dotPath => $fullPath) {
    $isDirectController = isset($allDirectViews[$dotPath]);

    // Also check if any parent blade that uses this is connected to a controller
    $connectedViaBladeChain = false;
    $bladeParents = [];

    if (isset($referencedByBlade[$dotPath])) {
        foreach ($referencedByBlade[$dotPath] as $parent) {
            $bladeParents[] = $parent;
            if (isset($allDirectViews[$parent])) {
                $connectedViaBladeChain = true;
            }
        }
    }

    if ($isDirectController) {
        $directlyConnected[$dotPath] = [
            'path' => $fullPath,
            'referenced_in' => array_map(function($f) use ($projectRoot) {
                return str_replace($projectRoot . '/', '', $f);
            }, array_unique(array_merge(
                $controllerViewCalls[$dotPath] ?? [],
                $routeViewCalls[$dotPath] ?? []
            )))
        ];
    } elseif ($connectedViaBladeChain) {
        $bladeConnected[$dotPath] = [
            'path' => $fullPath,
            'included_by' => $bladeParents,
        ];
    } else {
        $notConnected[$dotPath] = [
            'path' => $fullPath,
            'included_by_blade' => $bladeParents,
        ];
    }
}

// ─────────────────────────────────────────────
// OUTPUT
// ─────────────────────────────────────────────

$divider = str_repeat('─', 80);

echo "\n";
echo "╔══════════════════════════════════════════════════════════════════════════════╗\n";
echo "║           BLADE FILE CONNECTION ANALYSIS REPORT                            ║\n";
echo "╚══════════════════════════════════════════════════════════════════════════════╝\n\n";
echo "Total blade files found: " . count($allBlades) . "\n\n";

// ── SECTION 1: Directly connected to controller ──
echo $divider . "\n";
echo "✅  DIRECTLY CONNECTED TO CONTROLLER/ROUTE  (" . count($directlyConnected) . " files)\n";
echo $divider . "\n";
foreach ($directlyConnected as $dotPath => $info) {
    echo "  📄 {$dotPath}\n";
    foreach ($info['referenced_in'] as $ref) {
        echo "      └─ " . basename($ref) . " ({$ref})\n";
    }
}

echo "\n";

// ── SECTION 2: Connected via blade-to-blade ──
echo $divider . "\n";
echo "🔗  CONNECTED VIA BLADE-TO-BLADE (not direct controller)  (" . count($bladeConnected) . " files)\n";
echo $divider . "\n";
foreach ($bladeConnected as $dotPath => $info) {
    echo "  📄 {$dotPath}\n";
    foreach ($info['included_by'] as $parent) {
        $parentConnected = isset($allDirectViews[$parent]) ? "✅ controller-connected" : "🔗 blade-only";
        echo "      └─ included/extended by: {$parent} [{$parentConnected}]\n";
    }
}

echo "\n";

// ── SECTION 3: NOT connected anywhere ──
echo $divider . "\n";
echo "❌  NOT CONNECTED (no controller, no blade reference)  (" . count($notConnected) . " files)\n";
echo $divider . "\n";
foreach ($notConnected as $dotPath => $info) {
    $relative = str_replace($projectRoot . '/', '', $info['path']);
    echo "  🚫 {$dotPath}\n";
    echo "      └─ File: {$relative}\n";
    if (!empty($info['included_by_blade'])) {
        foreach ($info['included_by_blade'] as $parent) {
            echo "      └─ Included by (orphan blade): {$parent}\n";
        }
    }
}

echo "\n";

// ── SECTION 4: Blade-to-blade map (for reference) ──
echo $divider . "\n";
echo "📋  BLADE-TO-BLADE REFERENCES MAP\n";
echo $divider . "\n";
foreach ($bladeToBlade as $parent => $children) {
    echo "  📄 {$parent}\n";
    foreach ($children as $child) {
        $status = isset($allBlades[$child]) ? "✅ exists" : "⚠️ FILE NOT FOUND";
        echo "      └─ @includes/extends: {$child} [{$status}]\n";
    }
}

echo "\n";
echo $divider . "\n";
echo "SUMMARY\n";
echo $divider . "\n";
echo "  ✅ Directly controller-connected : " . count($directlyConnected) . "\n";
echo "  🔗 Connected via blade-to-blade  : " . count($bladeConnected) . "\n";
echo "  ❌ Orphan / Not connected        : " . count($notConnected) . "\n";
echo "  📋 Total blade files             : " . count($allBlades) . "\n";
echo $divider . "\n";
