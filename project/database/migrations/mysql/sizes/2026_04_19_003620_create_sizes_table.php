<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSizesTable extends Migration
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
        if (!Schema::hasTable('sizes')) {
            Schema::create('sizes', function (Blueprint $table) {
                $table->id();
            });
        }

        Schema::table('sizes', function (Blueprint $table) {
            if (!Schema::hasColumn('sizes', 'size_name')) {
                $table->string('size_name');
            }
            if (!Schema::hasColumn('sizes', 'paper_size')) {
                $table->string('paper_size')->nullable();
            }
            if (!Schema::hasColumn('sizes', 'thumb')) {
                $table->string('thumb');
            }
            if (!Schema::hasColumn('sizes', 'category_id')) {
                $table->string('category_id')->nullable();
            }
            if (!Schema::hasColumn('sizes', 'id_name')) {
                $table->string('id_name')->nullable();
            }
            if (!Schema::hasColumn('sizes', 'width_ration')) {
                $table->integer('width_ration')->default(0);
            }
            if (!Schema::hasColumn('sizes', 'height_ration')) {
                $table->integer('height_ration')->default(0);
            }
            if (!Schema::hasColumn('sizes', 'width')) {
                $table->integer('width')->default(0);
            }
            if (!Schema::hasColumn('sizes', 'height')) {
                $table->integer('height')->default(0);
            }
            if (!Schema::hasColumn('sizes', 'unit')) {
                $table->string('unit')->nullable();
            }
            if (!Schema::hasColumn('sizes', 'status')) {
                $table->integer('status')->default(1);
            }
            if (!Schema::hasColumn('sizes', 'emp_id')) {
                $table->integer('emp_id')->nullable();
            }
            if (!Schema::hasColumn('sizes', 'created_at')) {
                $table->timestamp('created_at')->nullable();
            }
            if (!Schema::hasColumn('sizes', 'updated_at')) {
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
        if (Schema::hasTable('sizes')) {
            Schema::table('sizes', function (Blueprint $table) {
                if (Schema::hasColumn('sizes', 'size_name')) {
                    $table->dropColumn('size_name');
                }
                if (Schema::hasColumn('sizes', 'paper_size')) {
                    $table->dropColumn('paper_size');
                }
                if (Schema::hasColumn('sizes', 'thumb')) {
                    $table->dropColumn('thumb');
                }
                if (Schema::hasColumn('sizes', 'category_id')) {
                    $table->dropColumn('category_id');
                }
                if (Schema::hasColumn('sizes', 'id_name')) {
                    $table->dropColumn('id_name');
                }
                if (Schema::hasColumn('sizes', 'width_ration')) {
                    $table->dropColumn('width_ration');
                }
                if (Schema::hasColumn('sizes', 'height_ration')) {
                    $table->dropColumn('height_ration');
                }
                if (Schema::hasColumn('sizes', 'width')) {
                    $table->dropColumn('width');
                }
                if (Schema::hasColumn('sizes', 'height')) {
                    $table->dropColumn('height');
                }
                if (Schema::hasColumn('sizes', 'unit')) {
                    $table->dropColumn('unit');
                }
                if (Schema::hasColumn('sizes', 'status')) {
                    $table->dropColumn('status');
                }
                if (Schema::hasColumn('sizes', 'emp_id')) {
                    $table->dropColumn('emp_id');
                }
                if (Schema::hasColumn('sizes', 'created_at')) {
                    $table->dropColumn('created_at');
                }
                if (Schema::hasColumn('sizes', 'updated_at')) {
                    $table->dropColumn('updated_at');
                }
            });
        }
    }
}
