<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSlugHistoryTable extends Migration
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
        if (!Schema::hasTable('slug_history')) {
            Schema::create('slug_history', function (Blueprint $table) {
                $table->id();
            });
        }

        Schema::table('slug_history', function (Blueprint $table) {
            if (!Schema::hasColumn('slug_history', 'reference_id')) {
                $table->integer('reference_id')->index();
            }
            if (!Schema::hasColumn('slug_history', 'reference_type')) {
                $table->string('reference_type')->index();
            }
            if (!Schema::hasColumn('slug_history', 'slug')) {
                $table->string('slug')->index();
            }
            if (!Schema::hasColumn('slug_history', 'created_at')) {
                $table->timestamp('created_at')->nullable();
            }
            if (!Schema::hasColumn('slug_history', 'updated_at')) {
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
        if (Schema::hasTable('slug_history')) {
            Schema::table('slug_history', function (Blueprint $table) {
                if (Schema::hasColumn('slug_history', 'reference_id')) {
                    $table->dropColumn('reference_id');
                }
                if (Schema::hasColumn('slug_history', 'reference_type')) {
                    $table->dropColumn('reference_type');
                }
                if (Schema::hasColumn('slug_history', 'slug')) {
                    $table->dropColumn('slug');
                }
                if (Schema::hasColumn('slug_history', 'created_at')) {
                    $table->dropColumn('created_at');
                }
                if (Schema::hasColumn('slug_history', 'updated_at')) {
                    $table->dropColumn('updated_at');
                }
            });
        }
    }
}
