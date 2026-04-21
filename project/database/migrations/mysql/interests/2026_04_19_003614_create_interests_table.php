<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInterestsTable extends Migration
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
    public function up(): void
    {
        if (!Schema::hasTable('interests')) {
            Schema::create('interests', function (Blueprint $table) {
                $table->id();
            });
        }

        Schema::table('interests', function (Blueprint $table) {
            if (!Schema::hasColumn('interests', 'name')) {
                $table->string('name');
            }
            if (!Schema::hasColumn('interests', 'id_name')) {
                $table->string('id_name')->nullable();
            }
            if (!Schema::hasColumn('interests', 'category_id')) {
                $table->string('category_id')->nullable();
            }
            if (!Schema::hasColumn('interests', 'emp_id')) {
                $table->integer('emp_id')->nullable();
            }
            if (!Schema::hasColumn('interests', 'status')) {
                $table->integer('status')->default(1);
            }
            if (!Schema::hasColumn('interests', 'created_at')) {
                $table->timestamp('created_at')->nullable();
            }
            if (!Schema::hasColumn('interests', 'updated_at')) {
                $table->timestamp('updated_at')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        if (Schema::hasTable('interests')) {
            Schema::table('interests', function (Blueprint $table) {
                if (Schema::hasColumn('interests', 'name')) {
                    $table->dropColumn('name');
                }
                if (Schema::hasColumn('interests', 'id_name')) {
                    $table->dropColumn('id_name');
                }
                if (Schema::hasColumn('interests', 'category_id')) {
                    $table->dropColumn('category_id');
                }
                if (Schema::hasColumn('interests', 'emp_id')) {
                    $table->dropColumn('emp_id');
                }
                if (Schema::hasColumn('interests', 'status')) {
                    $table->dropColumn('status');
                }
                if (Schema::hasColumn('interests', 'created_at')) {
                    $table->dropColumn('created_at');
                }
                if (Schema::hasColumn('interests', 'updated_at')) {
                    $table->dropColumn('updated_at');
                }
            });
        }
    }
}
