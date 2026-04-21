<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTemplateTypesTable extends Migration
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
        if (!Schema::hasTable('template_types')) {
            Schema::create('template_types', function (Blueprint $table) {
                $table->id();
            });
        }

        Schema::table('template_types', function (Blueprint $table) {
            if (!Schema::hasColumn('template_types', 'type')) {
                $table->string('type');
            }
            if (!Schema::hasColumn('template_types', 'value')) {
                $table->integer('value')->default(0);
            }
            if (!Schema::hasColumn('template_types', 'created_at')) {
                $table->timestamp('created_at')->nullable();
            }
            if (!Schema::hasColumn('template_types', 'updated_at')) {
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
        if (Schema::hasTable('template_types')) {
            Schema::table('template_types', function (Blueprint $table) {
                if (Schema::hasColumn('template_types', 'type')) {
                    $table->dropColumn('type');
                }
                if (Schema::hasColumn('template_types', 'value')) {
                    $table->dropColumn('value');
                }
                if (Schema::hasColumn('template_types', 'created_at')) {
                    $table->dropColumn('created_at');
                }
                if (Schema::hasColumn('template_types', 'updated_at')) {
                    $table->dropColumn('updated_at');
                }
            });
        }
    }
}
