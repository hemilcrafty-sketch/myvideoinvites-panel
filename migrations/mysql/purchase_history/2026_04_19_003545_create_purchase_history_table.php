<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePurchaseHistoryTable extends Migration
{
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
                $table->integer('emp_id')->default(0);
                $table->integer('by_sales_team')->default(0);
                $table->string('user_id')->index();
                $table->string('contact_no')->nullable();
                $table->string('product_id')->nullable();
                $table->string('product_type')->nullable();
                $table->string('subscription_id')->nullable();
                $table->string('order_id')->nullable();
                $table->string('transaction_id')->nullable();
                $table->string('payment_id')->nullable();
                $table->string('currency_code')->nullable();
                $table->float('amount')->default(0);
                $table->float('paid_amount')->nullable();
                $table->float('net_amount')->default(0);
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
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasTable('purchase_history')) {
            Schema::dropIfExists('purchase_history');
        }
    }
}
