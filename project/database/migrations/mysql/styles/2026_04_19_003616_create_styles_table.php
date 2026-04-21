<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStylesTable extends Migration
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
        if (!Schema::hasTable('styles')) {
            Schema::create('styles', function (Blueprint $table) {
                $table->id();
            });
        }

        Schema::table('styles', function (Blueprint $table) {
            if (!Schema::hasColumn('styles', 'name')) {
                $table->string('name');
            }
            if (!Schema::hasColumn('styles', 'id_name')) {
                $table->string('id_name')->nullable();
            }
            if (!Schema::hasColumn('styles', 'emp_id')) {
                $table->integer('emp_id')->nullable();
            }
            if (!Schema::hasColumn('styles', 'status')) {
                $table->integer('status')->default(1);
            }
            if (!Schema::hasColumn('styles', 'created_at')) {
                $table->timestamp('created_at')->nullable();
            }
            if (!Schema::hasColumn('styles', 'updated_at')) {
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
        if (Schema::hasTable('styles')) {
            Schema::table('styles', function (Blueprint $table) {
                if (Schema::hasColumn('styles', 'name')) {
                    $table->dropColumn('name');
                }
                if (Schema::hasColumn('styles', 'id_name')) {
                    $table->dropColumn('id_name');
                }
                if (Schema::hasColumn('styles', 'emp_id')) {
                    $table->dropColumn('emp_id');
                }
                if (Schema::hasColumn('styles', 'status')) {
                    $table->dropColumn('status');
                }
                if (Schema::hasColumn('styles', 'created_at')) {
                    $table->dropColumn('created_at');
                }
                if (Schema::hasColumn('styles', 'updated_at')) {
                    $table->dropColumn('updated_at');
                }
            });
        }
    }
}
