@inject('roleManager', 'App\Http\Controllers\Utils\RoleManager')
@inject('appBase', 'App\Http\Controllers\Admin\AppBaseController')
@include('layouts.masterhead')

<style>
    .pending-tasks-container {
        background: #f8f9fa;
        min-height: 100vh;
        padding: 20px;
    }

    .tasks-card {
        background: white;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        overflow: hidden;
    }

    .tasks-header {
        padding: 20px;
        border-bottom: 1px solid #e9ecef;
        background: white;
    }

    .tasks-table-wrapper {
        overflow-x: auto;
    }

    .tasks-table {
        width: 100%;
        margin: 0;
        border-collapse: separate;
        border-spacing: 0;
    }

    .tasks-table thead th {
        background: #f8f9fa;
        color: #495057;
        font-weight: 600;
        font-size: 13px;
        text-transform: uppercase;
        padding: 15px 12px;
        border-bottom: 2px solid #dee2e6;
        white-space: nowrap;
    }

    .tasks-table tbody td {
        padding: 12px;
        border-bottom: 1px solid #e9ecef;
        vertical-align: middle;
        font-size: 14px;
        color: #495057;
    }

    .tasks-table tbody tr:hover {
        background-color: #f8f9fa;
    }

    .pagination-wrapper {
        padding: 20px;
        background: white;
        border-top: 1px solid #e9ecef;
    }

    .id-cell {
        font-family: 'Courier New', monospace;
        font-size: 12px;
        color: #6c757d;
    }
</style>

<div class="main-container">
    <div class="pending-tasks-container">
        <div class="tasks-card">
            <div class="tasks-header">
                <div class="row align-items-center mb-3">
                    <div class="col-md-6">
                        <h4 class="mb-0" style="font-weight: 600; color: #212529;">Rejected Tasks</h4>
                        <p class="text-muted mb-0" style="font-size: 14px;">Review tasks that were rejected by managers</p>
                    </div>
                    <div class="col-md-6 text-right">
                        {{-- No action button needed for rejected tasks usually --}}
                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        @include('partials.filter_form', [
                            'action' => route('rejected_task'),
                        ])
                    </div>
                </div>
            </div>

            <div class="tasks-table-wrapper">
                <table class="tasks-table">
                    <thead>
                        <tr>
                            <th style="width: 80px;">ID</th>
                            <th>Employee</th>
                            <th>Module</th>
                            <th>Title</th>
                            <th>Rejection Reason</th>
                            <th>Rejected By</th>
                            <th style="text-align: center;">Details</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($tasks as $task)
                            <tr>
                                <td class="id-cell">#{{ $task->id }}</td>
                                <td>{{ $roleManager::getUploaderName($task->emp_id) }}</td>
                                <td><span class="badge badge-info">{{ $task->table_name }}</span></td>
                                <td>{{ $task->changes_title }}</td>
                                <td class="text-danger"><strong>{{ $task->reason }}</strong></td>
                                <td>{{ $roleManager::getUploaderName($task->approve_by) }}</td>
                                <td style="text-align: center;">
                                    <button class="btn btn-sm btn-outline-primary" type="button" data-toggle="collapse" data-target="#details_{{ $task->id }}">
                                        View Changes
                                    </button>
                                </td>
                            </tr>
                            <tr class="collapse" id="details_{{ $task->id }}">
                                <td colspan="7">
                                    <div class="p-3" style="background: #fdfdfd; border: 1px inset #eee;">
                                        <h6 class="mb-3">Changes that were rejected:</h6>
                                        <table class="table table-sm table-bordered">
                                            <thead class="thead-light">
                                                <tr>
                                                    <th>Field</th>
                                                    <th>Old Value</th>
                                                    <th>New Value</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @if($task->change_log)
                                                    @foreach(json_decode($task->change_log, true) as $key => $values)
                                                        <tr>
                                                            <td style="width: 20%;"><strong>{{ ucfirst(str_replace('_', ' ', $key)) }}</strong></td>
                                                            <td style="width: 40%;">{!! $appBase::renderChangeValue($key, $values['old'] ?? '') !!}</td>
                                                            <td style="width: 40%;">{!! $appBase::renderChangeValue($key, $values['new'] ?? '', $values['old'] ?? '') !!}</td>
                                                        </tr>
                                                    @endforeach
                                                @else
                                                    @foreach(json_decode($task->data, true) as $key => $value)
                                                        <tr>
                                                            <td style="width: 20%;"><strong>{{ ucfirst(str_replace('_', ' ', $key)) }}</strong></td>
                                                            <td style="width: 40%;">—</td>
                                                            <td style="width: 40%;">{!! $appBase::renderChangeValue($key, $value) !!}</td>
                                                        </tr>
                                                    @endforeach
                                                @endif
                                            </tbody>
                                        </table>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center" style="padding: 40px; color: #6c757d;">
                                    <i class="fa fa-info-circle mb-2" style="font-size: 24px;"></i>
                                    <p>No rejected tasks found.</p>
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

