<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdminChangesLogTable extends Migration
{
    /**
     * The database connection that should be used by the migration.
     *
     * @var string
     */
    protected $connection = 'mysql';
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
            });
        }

        Schema::table('admin_changes_log', function (Blueprint $table) {
            if (!Schema::hasColumn('admin_changes_log', 'emp_id')) {
                $table->integer('emp_id')->index();
            }
            if (!Schema::hasColumn('admin_changes_log', 'model')) {
                $table->string('model');
            }
            if (!Schema::hasColumn('admin_changes_log', 'model_id')) {
                $table->integer('model_id');
            }
            if (!Schema::hasColumn('admin_changes_log', 'old_values')) {
                $table->text('old_values')->nullable();
            }
            if (!Schema::hasColumn('admin_changes_log', 'updated_fields')) {
                $table->text('updated_fields')->nullable();
            }
            if (!Schema::hasColumn('admin_changes_log', 'ip_address')) {
                $table->string('ip_address')->nullable();
            }
            if (!Schema::hasColumn('admin_changes_log', 'created_at')) {
                $table->timestamp('created_at')->nullable();
            }
            if (!Schema::hasColumn('admin_changes_log', 'updated_at')) {
                $table->timestamp('updated_at')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasTable('admin_changes_log')) {
            Schema::table('admin_changes_log', function (Blueprint $table) {
                if (Schema::hasColumn('admin_changes_log', 'emp_id')) {
                    $table->dropColumn('emp_id');
                }
                if (Schema::hasColumn('admin_changes_log', 'model')) {
                    $table->dropColumn('model');
                }
                if (Schema::hasColumn('admin_changes_log', 'model_id')) {
                    $table->dropColumn('model_id');
                }
                if (Schema::hasColumn('admin_changes_log', 'old_values')) {
                    $table->dropColumn('old_values');
                }
                if (Schema::hasColumn('admin_changes_log', 'updated_fields')) {
                    $table->dropColumn('updated_fields');
                }
                if (Schema::hasColumn('admin_changes_log', 'ip_address')) {
                    $table->dropColumn('ip_address');
                }
                if (Schema::hasColumn('admin_changes_log', 'created_at')) {
                    $table->dropColumn('created_at');
                }
                if (Schema::hasColumn('admin_changes_log', 'updated_at')) {
                    $table->dropColumn('updated_at');
                }
            });
        }
    }
}
