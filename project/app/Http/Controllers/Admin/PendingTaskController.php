<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Utils\HelperController;
use App\Http\Controllers\Utils\RoleManager;
use App\Models\PendingTask;
use App\Models\User;
use App\Models\Video\VideoCategory;
use App\Models\Video\VideoVirtualCategory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PendingTaskController extends AppBaseController
{
    /**
     * Intercept and store a pending task.
     */
    public static function store(
        $data,
        $className,
        $title,
        $desc,
        $table,
        $action,
        $previewRoute = null,
        $isDirectSave = false,
        $idName = null
    ) {
        if ($isDirectSave) {
            $data->save();
            return response()->json([
                'success' => 'Changes applied directly.'
            ]);
        }

        $pendingTask = new PendingTask();
        $pendingTask->string_id = HelperController::generateRandomId(modelSource: PendingTask::class);
        $pendingTask->id_name = $idName;
        $pendingTask->emp_id = Auth::user()->id;
        $pendingTask->status = 0; // Pending
        $pendingTask->changes_title = $title;
        $pendingTask->changes_desc = $desc;
        $pendingTask->preview_route = $previewRoute;
        $pendingTask->data = json_encode($data->getAttributes());
        $pendingTask->table_name = $table;
        $pendingTask->action = $action;
        $pendingTask->record_id = $data->id;

        // Log changes if it's an update
        if ($action === 'update' && $data->id) {
            $existingModel = $className::find($data->id);
            if ($existingModel) {
                $oldData = $existingModel->getAttributes();
                $newData = $data->getAttributes();
                $diff = [];
                foreach ($newData as $key => $value) {
                    if (array_key_exists($key, $oldData) && $oldData[$key] != $value) {
                        $diff[$key] = [
                            'old' => $oldData[$key],
                            'new' => $value
                        ];
                    }
                }
                $pendingTask->change_log = json_encode($diff);
            }
        }

        $pendingTask->save();

        return response()->json([
            'success' => 'Task submitted for approval.'
        ]);
    }

    /**
     * Display a listing of pending tasks.
     */
    public function show_pending_item(Request $request)
    {
        $query = PendingTask::where('status', 0);

        // Filter by employee if not admin/manager
        if (!RoleManager::isAdminOrSeoManager(Auth::user()->user_type)) {
            $query->where('emp_id', Auth::user()->id);
        }

        $searchableFields = [
            ['id' => 'changes_title', 'value' => 'Title'],
            ['id' => 'table_name', 'value' => 'Module'],
            ['id' => 'action', 'value' => 'Action'],
        ];

        $tasks = $this->applyFiltersAndPagination($request, $query, $searchableFields);

        return view('pending_item.show_pending_item', compact('tasks', 'searchableFields'));
    }

    /**
     * Display a listing of rejected tasks.
     */
    public function rejected_task(Request $request)
    {
        $query = PendingTask::where('status', 2);

        // Filter by employee if not admin/manager
        if (!RoleManager::isAdminOrSeoManager(Auth::user()->user_type)) {
            $query->where('emp_id', Auth::user()->id);
        }

        $searchableFields = [
            ['id' => 'changes_title', 'value' => 'Title'],
            ['id' => 'table_name', 'value' => 'Module'],
        ];

        $tasks = $this->applyFiltersAndPagination($request, $query, $searchableFields);

        return view('pending_item.rejected_task', compact('tasks', 'searchableFields'));
    }

    /**
     * Approve a pending task.
     */
    public function approve($id): JsonResponse
    {
        if (!RoleManager::isAdminOrSeoManager(Auth::user()->user_type)) {
            return response()->json(['error' => 'Permission denied.'], 403);
        }

        $task = PendingTask::findOrFail($id);
        if ($task->status != 0) {
            return response()->json(['error' => 'Task is not in pending state.'], 400);
        }

        try {
            DB::beginTransaction();

            $this->applyChange($task);

            $task->status = 1; // Approved
            $task->approve_by = Auth::user()->id;
            $task->save();

            DB::commit();

            return response()->json(['success' => 'Task approved and applied successfully.']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Failed to apply changes: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Reject a pending task.
     */
    public function reject(Request $request, $id): JsonResponse
    {
        if (!RoleManager::isAdminOrSeoManager(Auth::user()->user_type)) {
            return response()->json(['error' => 'Permission denied.'], 403);
        }

        $task = PendingTask::findOrFail($id);
        $task->status = 2; // Rejected
        $task->reason = $request->input('reason');
        $task->approve_by = Auth::user()->id;
        $task->save();

        return response()->json(['success' => 'Task rejected successfully.']);
    }

    /**
     * Apply the changes from a pending task to the actual table.
     */
    private function applyChange(PendingTask $task)
    {
        $data = json_decode($task->data, true);
        $modelClass = $this->getModelFromTable($task->table_name);

        if (!$modelClass) {
            throw new \Exception("Unknown table: " . $task->table_name);
        }

        if ($task->action === 'add') {
            $model = new $modelClass();
            $model->fill($data);
            $model->save();
        } elseif ($task->action === 'update') {
            $model = $modelClass::findOrFail($task->record_id);
            $model->fill($data);
            $model->save();
        } elseif ($task->action === 'delete') {
            $model = $modelClass::findOrFail($task->record_id);
            $model->delete();
        }

        // Special handling for VideoCategory if needed
        if ($modelClass === VideoCategory::class) {
            // VideoCategory hooks (booted) handle hierarchy and slug updates automatically.
        }
    }

    /**
     * Map table names to Model classes.
     */
    private function getModelFromTable($tableName): ?string
    {
        return match ($tableName) {
            'v_cat' => VideoCategory::class,
            'v_vcat' => VideoVirtualCategory::class,
            default => null,
        };
    }
}