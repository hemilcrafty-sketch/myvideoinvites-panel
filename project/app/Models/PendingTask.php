<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PendingTask extends Model
{
    use HasFactory;

    protected $table = 'pending_tasks';

    protected $fillable = [
        'string_id',
        'id_name',
        'page_type',
        'emp_id',
        'status',
        'reason',
        'changes_title',
        'changes_desc',
        'preview_route',
        'approve_by',
        'data',
        'table_name',
        'action',
        'change_log',
        'record_id',
    ];

    /**
     * Get the user who created the task.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'emp_id');
    }

    /**
     * Get the user who approved/rejected the task.
     */
    public function approver()
    {
        return $this->belongsTo(User::class, 'approve_by');
    }
}
