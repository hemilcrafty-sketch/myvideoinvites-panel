<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserDataDeletedTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('user_data_deleted')) {
            Schema::create('user_data_deleted', function (Blueprint $table) {
                $table->id();
                $table->integer('user_int_id')->index();
                $table->string('uid')->index();
                $table->string('refer_id')->nullable();
                $table->string('stripe_cus_id')->nullable();
                $table->string('razorpay_cus_id')->nullable();
                $table->string('photo_uri')->nullable();
                $table->string('name');
                $table->string('country_code')->nullable();
                $table->string('number')->nullable();
                $table->string('email')->nullable();
                $table->string('login_type')->nullable();
                $table->string('utm_source')->nullable();
                $table->string('utm_medium')->nullable();
                $table->integer('coins')->default(0);
                $table->string('device_id')->nullable();
                $table->string('fldr_str')->nullable();
                $table->string('referral_user_id')->nullable();
                $table->string('creation_date')->nullable();
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
        if (Schema::hasTable('user_data_deleted')) {
            Schema::dropIfExists('user_data_deleted');
        }
    }
}
