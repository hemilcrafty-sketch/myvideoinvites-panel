<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOtpTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('otp_tables')) {
            Schema::create('otp_tables', function (Blueprint $table) {
                $table->id();
                $table->string('mail')->nullable()->index();
                $table->string('otp')->nullable();
                $table->string('msg')->nullable();
                $table->string('type')->nullable();
                $table->integer('status')->nullable()->default(0);
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
        if (Schema::hasTable('otp_tables')) {
            Schema::dropIfExists('otp_tables');
        }
    }
}
