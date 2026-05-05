@inject('roleManager', 'App\Http\Controllers\Utils\RoleManager')
@inject('appBase', 'App\Http\Controllers\Admin\AppBaseController')
@include('layouts.masterhead')

<style>
    .pending-tasks-container {
        background: #f0f2f5;
        min-height: 100vh;
        padding: 20px;
    }

    .tasks-card {
        background: white;
        border-radius: 4px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        overflow: hidden;
    }

    .tasks-table {
        width: 100%;
        border-collapse: collapse;
    }

    .tasks-table thead th {
        background: #777;
        color: white;
        font-weight: 500;
        font-size: 14px;
        padding: 12px 15px;
        text-align: left;
        border: 1px solid #999;
    }

    .tasks-table tbody td {
        padding: 15px;
        border: 1px solid #eee;
        vertical-align: middle;
        font-size: 14px;
        color: #333;
    }

    .tasks-table tbody tr:hover {
        background-color: #f9f9f9;
    }

    .btn-preview {
        background-color: #ffc107;
        border-color: #ffc107;
        color: #000;
        width: 40px;
        height: 35px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 4px;
        transition: all 0.2s;
        border: none;
        cursor: pointer;
    }

    .btn-preview:hover {
        background-color: #e0a800;
        color: #000;
    }
</style>

<div class="main-container">
    <div class="pending-tasks-container">
        <div class="row mb-3">
            <div class="col-12">
                <a href="{{ route('show_pending_item') }}" class="btn btn-primary" style="border-radius: 6px; padding: 10px 20px;">
                    <i class="fa fa-list"></i> Pending Tasks
                </a>
            </div>
        </div>

        <div class="tasks-card">
            <div class="tasks-table-wrapper">
                <table class="tasks-table">
                    <thead>
                        <tr>
                            <th style="width: 60px;">Id</th>
                            <th>Name</th>
                            <th>Description</th>
                            <th>Changes BY</th>
                            <th>Category Name</th>
                            <th>Status</th>
                            <th>Created At</th>
                            <th style="text-align: center; width: 80px;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($tasks as $task)
                            @php
                                $creator = $task->creator;
                                $roleName = 'User';
                                if ($creator) {
                                    if ($creator->user_type == 1) $roleName = 'admin';
                                    elseif ($creator->user_type == 2) $roleName = 'manager';
                                    elseif ($creator->user_type == 3) $roleName = 'executive';
                                    elseif ($creator->user_type == 4) $roleName = 'SEO';
                                    elseif ($creator->user_type == 5) $roleName = 'SEO Executive';
                                }
                            @endphp
                            <tr>
                                <td>{{ $task->id }}</td>
                                <td style="color: #444; font-weight: 500;">{{ $task->changes_title }}</td>
                                <td>{{ $task->changes_desc }}</td>
                                <td>{{ $roleName }}</td>
                                <td>{{ $task->id_name }}</td>
                                <td class="text-danger" style="font-weight: 500;">Rejected</td>
                                <td>{{ $task->created_at }}</td>
                                <td style="text-align: center;">
                                    @if($task->preview_route)
                                        <a href="{{ $task->preview_route }}?preview=1" target="_blank" class="btn-preview">
                                            <i class="fa fa-eye"></i>
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center" style="padding: 40px; color: #6c757d;">
                                    No rejected tasks found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="pagination-wrapper">
                @include('partials.pagination', ['items' => $tasks])
            </div>
        </div>
    </div>
</div>

@include('layouts.masterscript')

