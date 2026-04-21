<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePromoCodesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('promo_codes')) {
            Schema::create('promo_codes', function (Blueprint $table) {
                $table->id();
                $table->string('user_id')->nullable()->index();
                $table->string('promo_code')->unique();
                $table->integer('disc')->default(0);
                $table->integer('additional_days')->default(0);
                $table->string('type')->nullable();
                $table->integer('status')->default(1);
                $table->dateTime('expiry_date')->nullable();
                $table->integer('disc_upto_inr')->default(0);
                $table->integer('min_cart_inr')->default(0);
                $table->integer('disc_upto_usd')->default(0);
                $table->integer('min_cart_usd')->default(0);
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
        if (Schema::hasTable('promo_codes')) {
            Schema::dropIfExists('promo_codes');
        }
    }
}
