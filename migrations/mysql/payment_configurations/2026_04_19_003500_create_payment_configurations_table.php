<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentConfigurationsTable extends Migration
{
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
                $table->string('payment_scope');
                $table->string('gateway');
                $table->text('credentials');
                $table->text('payment_types')->nullable();
                $table->boolean('is_active')->default(true);
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
        if (Schema::hasTable('payment_configurations')) {
            Schema::dropIfExists('payment_configurations');
        }
    }
}
