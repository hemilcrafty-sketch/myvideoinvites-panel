<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTemplateRatesTable extends Migration
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
        if (!Schema::hasTable('template_rates')) {
            Schema::create('template_rates', function (Blueprint $table) {
                $table->id();
            });
        }

        Schema::table('template_rates', function (Blueprint $table) {
            if (!Schema::hasColumn('template_rates', 'name')) {
                $table->string('name')->unique();
            }
            if (!Schema::hasColumn('template_rates', 'value')) {
                $table->text('value')->nullable();
            }
            if (!Schema::hasColumn('template_rates', 'type')) {
                $table->integer('type')->default(0);
            }
            if (!Schema::hasColumn('template_rates', 'created_at')) {
                $table->timestamp('created_at')->nullable();
            }
            if (!Schema::hasColumn('template_rates', 'updated_at')) {
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
        if (Schema::hasTable('template_rates')) {
            Schema::table('template_rates', function (Blueprint $table) {
                if (Schema::hasColumn('template_rates', 'name')) {
                    $table->dropColumn('name');
                }
                if (Schema::hasColumn('template_rates', 'value')) {
                    $table->dropColumn('value');
                }
                if (Schema::hasColumn('template_rates', 'type')) {
                    $table->dropColumn('type');
                }
                if (Schema::hasColumn('template_rates', 'created_at')) {
                    $table->dropColumn('created_at');
                }
                if (Schema::hasColumn('template_rates', 'updated_at')) {
                    $table->dropColumn('updated_at');
                }
            });
        }
    }
}
