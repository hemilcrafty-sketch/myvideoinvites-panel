<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
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
        if (!Schema::hasTable('orders')) {
            Schema::create('orders', function (Blueprint $table) {
                $table->id();
            });
        }

        Schema::table('orders', function (Blueprint $table) {
            if (!Schema::hasColumn('orders', 'user_id')) {
                $table->string('user_id')->nullable()->index();
            }
            if (!Schema::hasColumn('orders', 'order_id')) {
                $table->string('order_id')->unique();
            }
            if (!Schema::hasColumn('orders', 'payment_id')) {
                $table->string('payment_id')->nullable();
            }
            if (!Schema::hasColumn('orders', 'subscription_id')) {
                $table->string('subscription_id')->nullable();
            }
            if (!Schema::hasColumn('orders', 'plan_id')) {
                $table->string('plan_id')->nullable();
            }
            if (!Schema::hasColumn('orders', 'amount')) {
                $table->decimal('amount', 10, 2)->default(0);
            }
            if (!Schema::hasColumn('orders', 'currency')) {
                $table->string('currency', 10)->default('INR');
            }
            if (!Schema::hasColumn('orders', 'gateway')) {
                $table->string('gateway')->nullable();
            }
            if (!Schema::hasColumn('orders', 'status')) {
                $table->string('status')->default('pending');
            }
            if (!Schema::hasColumn('orders', 'paid')) {
                $table->boolean('paid')->default(false);
            }
            if (!Schema::hasColumn('orders', 'crafty_id')) {
                $table->string('crafty_id')->nullable();
            }
            if (!Schema::hasColumn('orders', 'emp_id')) {
                $table->integer('emp_id')->nullable();
            }
            if (!Schema::hasColumn('orders', 'followup_label')) {
                $table->string('followup_label')->nullable();
            }
            if (!Schema::hasColumn('orders', 'followup_note')) {
                $table->text('followup_note')->nullable();
            }
            if (!Schema::hasColumn('orders', 'followup_call')) {
                $table->timestamp('followup_call')->nullable();
            }
            if (!Schema::hasColumn('orders', 'raw_notes')) {
                $table->text('raw_notes')->nullable();
            }
            if (!Schema::hasColumn('orders', 'type')) {
                $table->string('type')->nullable();
            }
            if (!Schema::hasColumn('orders', 'url')) {
                $table->string('url')->nullable();
            }
            if (!Schema::hasColumn('orders', 'user_agent')) {
                $table->string('user_agent')->nullable();
            }
            if (!Schema::hasColumn('orders', 'ip_address')) {
                $table->string('ip_address')->nullable();
            }
            if (!Schema::hasColumn('orders', 'ga')) {
                $table->text('ga')->nullable();
            }
            if (!Schema::hasColumn('orders', 'gclid')) {
                $table->string('gclid')->nullable();
            }
            if (!Schema::hasColumn('orders', 'fbc')) {
                $table->string('fbc')->nullable();
            }
            if (!Schema::hasColumn('orders', 'fbp')) {
                $table->string('fbp')->nullable();
            }
            if (!Schema::hasColumn('orders', 'gcl_au')) {
                $table->string('gcl_au')->nullable();
            }
            if (!Schema::hasColumn('orders', 'wbraid')) {
                $table->string('wbraid')->nullable();
            }
            if (!Schema::hasColumn('orders', 'gbraid')) {
                $table->string('gbraid')->nullable();
            }
            if (!Schema::hasColumn('orders', 'is_deleted')) {
                $table->boolean('is_deleted')->default(false);
            }
            if (!Schema::hasColumn('orders', 'created_at')) {
                $table->timestamp('created_at')->nullable();
            }
            if (!Schema::hasColumn('orders', 'updated_at')) {
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
        if (Schema::hasTable('orders')) {
            Schema::table('orders', function (Blueprint $table) {
                if (Schema::hasColumn('orders', 'user_id')) {
                    $table->dropColumn('user_id');
                }
                if (Schema::hasColumn('orders', 'order_id')) {
                    $table->dropColumn('order_id');
                }
                if (Schema::hasColumn('orders', 'payment_id')) {
                    $table->dropColumn('payment_id');
                }
                if (Schema::hasColumn('orders', 'subscription_id')) {
                    $table->dropColumn('subscription_id');
                }
                if (Schema::hasColumn('orders', 'plan_id')) {
                    $table->dropColumn('plan_id');
                }
                if (Schema::hasColumn('orders', 'amount')) {
                    $table->dropColumn('amount');
                }
                if (Schema::hasColumn('orders', 'currency')) {
                    $table->dropColumn('currency');
                }
                if (Schema::hasColumn('orders', 'gateway')) {
                    $table->dropColumn('gateway');
                }
                if (Schema::hasColumn('orders', 'status')) {
                    $table->dropColumn('status');
                }
                if (Schema::hasColumn('orders', 'paid')) {
                    $table->dropColumn('paid');
                }
                if (Schema::hasColumn('orders', 'crafty_id')) {
                    $table->dropColumn('crafty_id');
                }
                if (Schema::hasColumn('orders', 'emp_id')) {
                    $table->dropColumn('emp_id');
                }
                if (Schema::hasColumn('orders', 'followup_label')) {
                    $table->dropColumn('followup_label');
                }
                if (Schema::hasColumn('orders', 'followup_note')) {
                    $table->dropColumn('followup_note');
                }
                if (Schema::hasColumn('orders', 'followup_call')) {
                    $table->dropColumn('followup_call');
                }
                if (Schema::hasColumn('orders', 'raw_notes')) {
                    $table->dropColumn('raw_notes');
                }
                if (Schema::hasColumn('orders', 'type')) {
                    $table->dropColumn('type');
                }
                if (Schema::hasColumn('orders', 'url')) {
                    $table->dropColumn('url');
                }
                if (Schema::hasColumn('orders', 'user_agent')) {
                    $table->dropColumn('user_agent');
                }
                if (Schema::hasColumn('orders', 'ip_address')) {
                    $table->dropColumn('ip_address');
                }
                if (Schema::hasColumn('orders', 'ga')) {
                    $table->dropColumn('ga');
                }
                if (Schema::hasColumn('orders', 'gclid')) {
                    $table->dropColumn('gclid');
                }
                if (Schema::hasColumn('orders', 'fbc')) {
                    $table->dropColumn('fbc');
                }
                if (Schema::hasColumn('orders', 'fbp')) {
                    $table->dropColumn('fbp');
                }
                if (Schema::hasColumn('orders', 'gcl_au')) {
                    $table->dropColumn('gcl_au');
                }
                if (Schema::hasColumn('orders', 'wbraid')) {
                    $table->dropColumn('wbraid');
                }
                if (Schema::hasColumn('orders', 'gbraid')) {
                    $table->dropColumn('gbraid');
                }
                if (Schema::hasColumn('orders', 'is_deleted')) {
                    $table->dropColumn('is_deleted');
                }
                if (Schema::hasColumn('orders', 'created_at')) {
                    $table->dropColumn('created_at');
                }
                if (Schema::hasColumn('orders', 'updated_at')) {
                    $table->dropColumn('updated_at');
                }
            });
        }
    }
}
