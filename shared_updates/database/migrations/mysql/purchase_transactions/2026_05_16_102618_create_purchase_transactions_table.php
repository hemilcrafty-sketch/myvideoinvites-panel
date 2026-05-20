<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePurchaseTransactionsTable extends Migration
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
        if (!Schema::hasTable('purchase_transactions')) {
            Schema::create('purchase_transactions', function (Blueprint $table) {
                $table->id();
                $table->integer('emp_id')->nullable();
                $table->integer('by_sales_team')->default(0);
                $table->string('user_id')->index();
                $table->string('contact_no')->nullable();
                $table->string('subscription_id')->nullable();
                $table->string('order_id')->nullable();
                $table->string('transaction_id')->nullable()->index();
                $table->string('payment_id')->nullable();
                $table->string('currency_code')->nullable();
                $table->float('amount')->default(0);
                $table->float('paid_amount')->nullable();
                $table->float('net_amount')->default(0);
                $table->float('fee_percentage')->default(0);
                $table->string('next_amount')->nullable();
                $table->integer('promo_code_id')->default(0);
                $table->string('payment_method')->nullable();
                $table->string('from_where')->nullable();
                $table->string('fbc')->nullable();
                $table->string('gclid')->nullable();
                $table->boolean('isManual')->default(false);
                $table->string('url')->nullable();
                $table->integer('validity')->default(0);
                $table->boolean('yearly')->default(false);
                $table->text('plan_limit')->nullable();
                $table->text('raw_notes')->nullable();
                $table->boolean('is_trial')->default(false);
                $table->boolean('is_e_mandate')->default(false);
                $table->string('payment_status')->nullable();
                $table->integer('refund_by')->default(0);
                $table->integer('status')->default(1);
                $table->integer('total_purchases')->default(0);
                $table->dateTime('expired_at')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('purchase_transaction_products')) {
            Schema::create('purchase_transaction_products', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('purchase_transaction_id')->index();
                $table->string('product_id')->nullable();
                $table->string('product_type')->nullable();
                $table->float('amount')->default(0);
                $table->timestamps();

                $table->foreign('purchase_transaction_id', 'purchase_txn_id_foreign')
                    ->references('id')
                    ->on('purchase_transactions')
                    ->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('purchase_transaction_products');
        Schema::dropIfExists('purchase_transactions');
    }
}
