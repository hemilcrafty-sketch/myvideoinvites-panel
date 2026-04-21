<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateThemesTable extends Migration
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
        if (!Schema::hasTable('themes')) {
            Schema::create('themes', function (Blueprint $table) {
                $table->id();
            });
        }

        Schema::table('themes', function (Blueprint $table) {
            if (!Schema::hasColumn('themes', 'name')) {
                $table->string('name');
            }
            if (!Schema::hasColumn('themes', 'id_name')) {
                $table->string('id_name')->nullable();
            }
            if (!Schema::hasColumn('themes', 'category_id')) {
                $table->string('category_id')->nullable();
            }
            if (!Schema::hasColumn('themes', 'emp_id')) {
                $table->integer('emp_id')->nullable();
            }
            if (!Schema::hasColumn('themes', 'status')) {
                $table->integer('status')->default(1);
            }
            if (!Schema::hasColumn('themes', 'created_at')) {
                $table->timestamp('created_at')->nullable();
            }
            if (!Schema::hasColumn('themes', 'updated_at')) {
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
        if (Schema::hasTable('themes')) {
            Schema::table('themes', function (Blueprint $table) {
                if (Schema::hasColumn('themes', 'name')) {
                    $table->dropColumn('name');
                }
                if (Schema::hasColumn('themes', 'id_name')) {
                    $table->dropColumn('id_name');
                }
                if (Schema::hasColumn('themes', 'category_id')) {
                    $table->dropColumn('category_id');
                }
                if (Schema::hasColumn('themes', 'emp_id')) {
                    $table->dropColumn('emp_id');
                }
                if (Schema::hasColumn('themes', 'status')) {
                    $table->dropColumn('status');
                }
                if (Schema::hasColumn('themes', 'created_at')) {
                    $table->dropColumn('created_at');
                }
                if (Schema::hasColumn('themes', 'updated_at')) {
                    $table->dropColumn('updated_at');
                }
            });
        }
    }
}
