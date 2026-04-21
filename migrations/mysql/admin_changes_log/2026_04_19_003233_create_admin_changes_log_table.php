<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdminChangesLogTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('admin_changes_log')) {
            Schema::create('admin_changes_log', function (Blueprint $table) {
                $table->id();
                $table->integer('emp_id')->index();
                $table->string('model');
                $table->integer('model_id');
                $table->text('old_values')->nullable();
                $table->text('updated_fields')->nullable();
                $table->string('ip_address')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasTable('admin_changes_log')) {
            Schema::dropIfExists('admin_changes_log');
        }
    }
}
