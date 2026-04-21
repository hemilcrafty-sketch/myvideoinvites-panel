<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
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
                $table->string('user_id')->nullable()->index();
                $table->string('order_id')->unique();
                $table->string('payment_id')->nullable();
                $table->string('subscription_id')->nullable();
                $table->string('plan_id')->nullable();
                $table->decimal('amount', 10, 2)->default(0);
                $table->string('currency', 10)->default('INR');
                $table->string('gateway')->nullable();
                $table->string('status')->default('pending');
                $table->boolean('paid')->default(false);
                $table->string('crafty_id')->nullable();
                $table->integer('emp_id')->nullable();
                $table->string('followup_label')->nullable();
                $table->text('followup_note')->nullable();
                $table->timestamp('followup_call')->nullable();
                $table->text('raw_notes')->nullable();
                $table->string('type')->nullable();
                $table->string('url')->nullable();
                $table->string('user_agent')->nullable();
                $table->string('ip_address')->nullable();
                $table->text('ga')->nullable();
                $table->string('gclid')->nullable();
                $table->string('fbc')->nullable();
                $table->string('fbp')->nullable();
                $table->string('gcl_au')->nullable();
                $table->string('wbraid')->nullable();
                $table->string('gbraid')->nullable();
                $table->boolean('is_deleted')->default(false);
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
        if (Schema::hasTable('orders')) {
            Schema::dropIfExists('orders');
        }
    }
}
