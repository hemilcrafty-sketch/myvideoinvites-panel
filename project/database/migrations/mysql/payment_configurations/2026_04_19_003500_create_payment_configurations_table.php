<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentConfigurationsTable extends Migration
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
        if (!Schema::hasTable('payment_configurations')) {
            Schema::create('payment_configurations', function (Blueprint $table) {
                $table->id();
            });
        }

        Schema::table('payment_configurations', function (Blueprint $table) {
            if (!Schema::hasColumn('payment_configurations', 'payment_scope')) {
                $table->string('payment_scope');
            }
            if (!Schema::hasColumn('payment_configurations', 'gateway')) {
                $table->string('gateway');
            }
            if (!Schema::hasColumn('payment_configurations', 'credentials')) {
                $table->text('credentials');
            }
            if (!Schema::hasColumn('payment_configurations', 'payment_types')) {
                $table->text('payment_types')->nullable();
            }
            if (!Schema::hasColumn('payment_configurations', 'is_active')) {
                $table->boolean('is_active')->default(true);
            }
            if (!Schema::hasColumn('payment_configurations', 'created_at')) {
                $table->timestamp('created_at')->nullable();
            }
            if (!Schema::hasColumn('payment_configurations', 'updated_at')) {
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
        if (Schema::hasTable('payment_configurations')) {
            Schema::table('payment_configurations', function (Blueprint $table) {
                if (Schema::hasColumn('payment_configurations', 'payment_scope')) {
                    $table->dropColumn('payment_scope');
                }
                if (Schema::hasColumn('payment_configurations', 'gateway')) {
                    $table->dropColumn('gateway');
                }
                if (Schema::hasColumn('payment_configurations', 'credentials')) {
                    $table->dropColumn('credentials');
                }
                if (Schema::hasColumn('payment_configurations', 'payment_types')) {
                    $table->dropColumn('payment_types');
                }
                if (Schema::hasColumn('payment_configurations', 'is_active')) {
                    $table->dropColumn('is_active');
                }
                if (Schema::hasColumn('payment_configurations', 'created_at')) {
                    $table->dropColumn('created_at');
                }
                if (Schema::hasColumn('payment_configurations', 'updated_at')) {
                    $table->dropColumn('updated_at');
                }
            });
        }
    }
}
