<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserDataTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('user_data')) {
            Schema::create('user_data', function (Blueprint $table) {
                $table->id();
                $table->string('uid')->index();
                $table->string('user_name')->nullable();
                $table->string('bio')->nullable();
                $table->integer('is_username_update')->default(0);
                $table->string('refer_id')->nullable();
                $table->string('stripe_cus_id')->nullable();
                $table->string('razorpay_cus_id')->nullable();
                $table->string('photo_uri')->nullable();
                $table->string('name');
                $table->string('contact_no')->nullable();
                $table->integer('contact_no_verified')->default(0);
                $table->string('email')->nullable();
                $table->string('password')->nullable();
                $table->string('login_type')->nullable();
                $table->string('utm_source')->nullable();
                $table->string('utm_medium')->nullable();
                $table->string('fldr_str')->nullable();
                $table->string('email_preference')->nullable();
                $table->integer('profile_count')->default(0);
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
        if (Schema::hasTable('user_data')) {
            Schema::dropIfExists('user_data');
        }
    }
}
