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
                        <h4 class="mb-0" style="font-weight: 600; color: #212529;">Pending Tasks</h4>
                        <p class="text-muted mb-0" style="font-size: 14px;">Review and approve changes submitted by employees</p>
                    </div>
                    <div class="col-md-6 text-right">
                        {{-- Action buttons could go here if needed --}}
                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        @include('partials.filter_form', [
                            'action' => route('show_pending_item'),
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
                            <th>Action</th>
                            <th style="text-align: center;">Details</th>
                            <th style="text-align: center; width: 250px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($tasks as $task)
                            <tr>
                                <td class="id-cell">#{{ $task->id }}</td>
                                <td>{{ $roleManager::getUploaderName($task->emp_id) }}</td>
                                <td><span class="badge badge-info">{{ $task->table_name }}</span></td>
                                <td>{{ $task->changes_title }}</td>
                                <td>
                                    @if($task->action == 'add')
                                        <span class="badge badge-success">ADD</span>
                                    @elseif($task->action == 'update')
                                        <span class="badge badge-warning">UPDATE</span>
                                    @else
                                        <span class="badge badge-danger">DELETE</span>
                                    @endif
                                </td>
                                <td style="text-align: center;">
                                    <button class="btn btn-sm btn-outline-primary" type="button" data-toggle="collapse" data-target="#details_{{ $task->id }}">
                                        View Changes
                                    </button>
                                </td>
                                <td style="text-align: center;">
                                    @if($roleManager::isAdminOrSeoManager(Auth::user()->user_type))
                                        <button class="btn btn-sm btn-success" onclick="approveTask('{{ $task->id }}')">
                                            <i class="fa fa-check"></i> Approve
                                        </button>
                                        <button class="btn btn-sm btn-danger" onclick="openRejectModal('{{ $task->id }}')">
                                            <i class="fa fa-times"></i> Reject
                                        </button>
                                    @else
                                        <span class="text-muted italic">Awaiting Approval</span>
                                    @endif
                                    
                                    @if($task->preview_route)
                                        <a href="{{ $task->preview_route }}?preview=1" target="_blank" class="btn btn-sm btn-info">
                                            <i class="fa fa-eye"></i> Preview
                                        </a>
                                    @endif
                                </td>
                            </tr>
                            <tr class="collapse" id="details_{{ $task->id }}">
                                <td colspan="7">
                                    <div class="p-3" style="background: #fdfdfd; border: 1px inset #eee;">
                                        <h6 class="mb-3">Change Log:</h6>
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
                                    <p>No pending tasks found.</p>
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

<!-- Reject Reason Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Reject Task</h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="reject_task_id">
                <div class="form-group">
                    <label>Reason for rejection</label>
                    <textarea id="reject_reason" class="form-control" rows="3" placeholder="Enter reason..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" onclick="rejectTask()">Submit Rejection</button>
            </div>
        </div>
    </div>
</div>

@include('layouts.masterscript')

<script>
    function approveTask(id) {
        if (!confirm('Are you sure you want to approve and apply these changes?')) return;
        
        $.ajax({
            url: "{{ url('approve_pending_task') }}/" + id,
            type: 'POST',
            data: {
                _token: "{{ csrf_token() }}",
            },
            success: function(response) {
                if (response.success) {
                    alert(response.success);
                    location.reload();
                } else {
                    alert(response.error);
                }
            },
            error: function(err) {
                alert('Something went wrong.');
            }
        });
    }

    function openRejectModal(id) {
        $('#reject_task_id').val(id);
        $('#rejectModal').modal('show');
    }

    function rejectTask() {
        let id = $('#reject_task_id').val();
        let reason = $('#reject_reason').val();
        
        if (!reason) {
            alert('Please enter a reason.');
            return;
        }

        $.ajax({
            url: "{{ url('reject_pending_task') }}/" + id,
            type: 'POST',
            data: {
                _token: "{{ csrf_token() }}",
                reason: reason
            },
            success: function(response) {
                if (response.success) {
                    alert(response.success);
                    location.reload();
                } else {
                    alert(response.error);
                }
            },
            error: function(err) {
                alert('Something went wrong.');
            }
        });
    }
</script>

