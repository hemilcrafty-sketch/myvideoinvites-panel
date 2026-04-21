<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLanguagesTable extends Migration
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
        if (!Schema::hasTable('language')) {
            Schema::create('language', function (Blueprint $table) {
                $table->id();
            });
        }

        Schema::table('language', function (Blueprint $table) {
            if (!Schema::hasColumn('language', 'name')) {
                $table->string('name');
            }
            if (!Schema::hasColumn('language', 'id_name')) {
                $table->string('id_name')->nullable();
            }
            if (!Schema::hasColumn('language', 'emp_id')) {
                $table->integer('emp_id')->nullable();
            }
            if (!Schema::hasColumn('language', 'status')) {
                $table->integer('status')->default(1);
            }
            if (!Schema::hasColumn('language', 'created_at')) {
                $table->timestamp('created_at')->nullable();
            }
            if (!Schema::hasColumn('language', 'updated_at')) {
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
        if (Schema::hasTable('language')) {
            Schema::table('language', function (Blueprint $table) {
                if (Schema::hasColumn('language', 'name')) {
                    $table->dropColumn('name');
                }
                if (Schema::hasColumn('language', 'id_name')) {
                    $table->dropColumn('id_name');
                }
                if (Schema::hasColumn('language', 'emp_id')) {
                    $table->dropColumn('emp_id');
                }
                if (Schema::hasColumn('language', 'status')) {
                    $table->dropColumn('status');
                }
                if (Schema::hasColumn('language', 'created_at')) {
                    $table->dropColumn('created_at');
                }
                if (Schema::hasColumn('language', 'updated_at')) {
                    $table->dropColumn('updated_at');
                }
            });
        }
    }
}
