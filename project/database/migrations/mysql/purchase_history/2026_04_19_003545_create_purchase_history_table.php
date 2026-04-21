<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePurchaseHistoryTable extends Migration
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
        if (!Schema::hasTable('purchase_history')) {
            Schema::create('purchase_history', function (Blueprint $table) {
                $table->id();
            });
        }

        Schema::table('purchase_history', function (Blueprint $table) {
            if (!Schema::hasColumn('purchase_history', 'emp_id')) {
                $table->integer('emp_id')->default(0);
            }
            if (!Schema::hasColumn('purchase_history', 'by_sales_team')) {
                $table->integer('by_sales_team')->default(0);
            }
            if (!Schema::hasColumn('purchase_history', 'user_id')) {
                $table->string('user_id')->index();
            }
            if (!Schema::hasColumn('purchase_history', 'contact_no')) {
                $table->string('contact_no')->nullable();
            }
            if (!Schema::hasColumn('purchase_history', 'product_id')) {
                $table->string('product_id')->nullable();
            }
            if (!Schema::hasColumn('purchase_history', 'product_type')) {
                $table->string('product_type')->nullable();
            }
            if (!Schema::hasColumn('purchase_history', 'subscription_id')) {
                $table->string('subscription_id')->nullable();
            }
            if (!Schema::hasColumn('purchase_history', 'order_id')) {
                $table->string('order_id')->nullable();
            }
            if (!Schema::hasColumn('purchase_history', 'transaction_id')) {
                $table->string('transaction_id')->nullable();
            }
            if (!Schema::hasColumn('purchase_history', 'payment_id')) {
                $table->string('payment_id')->nullable();
            }
            if (!Schema::hasColumn('purchase_history', 'currency_code')) {
                $table->string('currency_code')->nullable();
            }
            if (!Schema::hasColumn('purchase_history', 'amount')) {
                $table->float('amount')->default(0);
            }
            if (!Schema::hasColumn('purchase_history', 'paid_amount')) {
                $table->float('paid_amount')->nullable();
            }
            if (!Schema::hasColumn('purchase_history', 'net_amount')) {
                $table->float('net_amount')->default(0);
            }
            if (!Schema::hasColumn('purchase_history', 'next_amount')) {
                $table->string('next_amount')->nullable();
            }
            if (!Schema::hasColumn('purchase_history', 'promo_code_id')) {
                $table->integer('promo_code_id')->default(0);
            }
            if (!Schema::hasColumn('purchase_history', 'payment_method')) {
                $table->string('payment_method')->nullable();
            }
            if (!Schema::hasColumn('purchase_history', 'from_where')) {
                $table->string('from_where')->nullable();
            }
            if (!Schema::hasColumn('purchase_history', 'fbc')) {
                $table->string('fbc')->nullable();
            }
            if (!Schema::hasColumn('purchase_history', 'gclid')) {
                $table->string('gclid')->nullable();
            }
            if (!Schema::hasColumn('purchase_history', 'isManual')) {
                $table->boolean('isManual')->default(false);
            }
            if (!Schema::hasColumn('purchase_history', 'url')) {
                $table->string('url')->nullable();
            }
            if (!Schema::hasColumn('purchase_history', 'validity')) {
                $table->integer('validity')->default(0);
            }
            if (!Schema::hasColumn('purchase_history', 'yearly')) {
                $table->boolean('yearly')->default(false);
            }
            if (!Schema::hasColumn('purchase_history', 'plan_limit')) {
                $table->text('plan_limit')->nullable();
            }
            if (!Schema::hasColumn('purchase_history', 'raw_notes')) {
                $table->text('raw_notes')->nullable();
            }
            if (!Schema::hasColumn('purchase_history', 'is_trial')) {
                $table->boolean('is_trial')->default(false);
            }
            if (!Schema::hasColumn('purchase_history', 'is_e_mandate')) {
                $table->boolean('is_e_mandate')->default(false);
            }
            if (!Schema::hasColumn('purchase_history', 'payment_status')) {
                $table->string('payment_status')->nullable();
            }
            if (!Schema::hasColumn('purchase_history', 'refund_by')) {
                $table->integer('refund_by')->default(0);
            }
            if (!Schema::hasColumn('purchase_history', 'status')) {
                $table->integer('status')->default(1);
            }
            if (!Schema::hasColumn('purchase_history', 'total_purchases')) {
                $table->integer('total_purchases')->default(0);
            }
            if (!Schema::hasColumn('purchase_history', 'expired_at')) {
                $table->dateTime('expired_at')->nullable();
            }
            if (!Schema::hasColumn('purchase_history', 'created_at')) {
                $table->timestamp('created_at')->nullable();
            }
            if (!Schema::hasColumn('purchase_history', 'updated_at')) {
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
        if (Schema::hasTable('purchase_history')) {
            Schema::table('purchase_history', function (Blueprint $table) {
                if (Schema::hasColumn('purchase_history', 'emp_id')) {
                    $table->dropColumn('emp_id');
                }
                if (Schema::hasColumn('purchase_history', 'by_sales_team')) {
                    $table->dropColumn('by_sales_team');
                }
                if (Schema::hasColumn('purchase_history', 'user_id')) {
                    $table->dropColumn('user_id');
                }
                if (Schema::hasColumn('purchase_history', 'contact_no')) {
                    $table->dropColumn('contact_no');
                }
                if (Schema::hasColumn('purchase_history', 'product_id')) {
                    $table->dropColumn('product_id');
                }
                if (Schema::hasColumn('purchase_history', 'product_type')) {
                    $table->dropColumn('product_type');
                }
                if (Schema::hasColumn('purchase_history', 'subscription_id')) {
                    $table->dropColumn('subscription_id');
                }
                if (Schema::hasColumn('purchase_history', 'order_id')) {
                    $table->dropColumn('order_id');
                }
                if (Schema::hasColumn('purchase_history', 'transaction_id')) {
                    $table->dropColumn('transaction_id');
                }
                if (Schema::hasColumn('purchase_history', 'payment_id')) {
                    $table->dropColumn('payment_id');
                }
                if (Schema::hasColumn('purchase_history', 'currency_code')) {
                    $table->dropColumn('currency_code');
                }
                if (Schema::hasColumn('purchase_history', 'amount')) {
                    $table->dropColumn('amount');
                }
                if (Schema::hasColumn('purchase_history', 'paid_amount')) {
                    $table->dropColumn('paid_amount');
                }
                if (Schema::hasColumn('purchase_history', 'net_amount')) {
                    $table->dropColumn('net_amount');
                }
                if (Schema::hasColumn('purchase_history', 'next_amount')) {
                    $table->dropColumn('next_amount');
                }
                if (Schema::hasColumn('purchase_history', 'promo_code_id')) {
                    $table->dropColumn('promo_code_id');
                }
                if (Schema::hasColumn('purchase_history', 'payment_method')) {
                    $table->dropColumn('payment_method');
                }
                if (Schema::hasColumn('purchase_history', 'from_where')) {
                    $table->dropColumn('from_where');
                }
                if (Schema::hasColumn('purchase_history', 'fbc')) {
                    $table->dropColumn('fbc');
                }
                if (Schema::hasColumn('purchase_history', 'gclid')) {
                    $table->dropColumn('gclid');
                }
                if (Schema::hasColumn('purchase_history', 'isManual')) {
                    $table->dropColumn('isManual');
                }
                if (Schema::hasColumn('purchase_history', 'url')) {
                    $table->dropColumn('url');
                }
                if (Schema::hasColumn('purchase_history', 'validity')) {
                    $table->dropColumn('validity');
                }
                if (Schema::hasColumn('purchase_history', 'yearly')) {
                    $table->dropColumn('yearly');
                }
                if (Schema::hasColumn('purchase_history', 'plan_limit')) {
                    $table->dropColumn('plan_limit');
                }
                if (Schema::hasColumn('purchase_history', 'raw_notes')) {
                    $table->dropColumn('raw_notes');
                }
                if (Schema::hasColumn('purchase_history', 'is_trial')) {
                    $table->dropColumn('is_trial');
                }
                if (Schema::hasColumn('purchase_history', 'is_e_mandate')) {
                    $table->dropColumn('is_e_mandate');
                }
                if (Schema::hasColumn('purchase_history', 'payment_status')) {
                    $table->dropColumn('payment_status');
                }
                if (Schema::hasColumn('purchase_history', 'refund_by')) {
                    $table->dropColumn('refund_by');
                }
                if (Schema::hasColumn('purchase_history', 'status')) {
                    $table->dropColumn('status');
                }
                if (Schema::hasColumn('purchase_history', 'total_purchases')) {
                    $table->dropColumn('total_purchases');
                }
                if (Schema::hasColumn('purchase_history', 'expired_at')) {
                    $table->dropColumn('expired_at');
                }
                if (Schema::hasColumn('purchase_history', 'created_at')) {
                    $table->dropColumn('created_at');
                }
                if (Schema::hasColumn('purchase_history', 'updated_at')) {
                    $table->dropColumn('updated_at');
                }
            });
        }
    }
}
