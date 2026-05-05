<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pending_tasks', function (Blueprint $table) {
            $table->id();
            $table->string('string_id');
            $table->text('id_name')->nullable();
            $table->integer('page_type')->default(1);
            $table->string('emp_id');
            $table->integer('status')->default(0); // 0: Pending, 1: Approved, 2: Rejected
            $table->text('reason')->nullable();
            $table->string('changes_title')->nullable();
            $table->text('changes_desc')->nullable();
            $table->text('preview_route')->nullable();
            $table->integer('approve_by')->nullable();
            $table->longText('data'); // Stores the JSON-encoded model data
            $table->string('table_name');
            $table->string('action'); // 'add', 'update', or 'delete'
            $table->longText('change_log')->nullable();
            $table->timestamps();
            $table->integer('record_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pending_tasks');
    }
};
