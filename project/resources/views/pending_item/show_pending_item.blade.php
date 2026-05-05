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
                <a href="{{ route('rejected_task') }}" class="btn btn-danger" style="border-radius: 6px; padding: 10px 20px;">
                    <i class="fa fa-times-circle"></i> Rejected
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
                                <td>Pending</td>
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
                                    No pending tasks found.
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

