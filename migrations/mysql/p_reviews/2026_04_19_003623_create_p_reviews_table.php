<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePReviewsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('p_reviews')) {
            Schema::create('p_reviews', function (Blueprint $table) {
                $table->id();
                $table->string('user_id')->nullable()->index();
                $table->integer('p_type')->index();
                $table->string('p_id')->index();
                $table->string('name')->nullable();
                $table->string('email')->nullable();
                $table->string('photo_uri')->nullable();
                $table->text('feedback')->nullable();
                $table->integer('rate')->default(0);
                $table->integer('is_approve')->default(0);
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
        if (Schema::hasTable('p_reviews')) {
            Schema::dropIfExists('p_reviews');
        }
    }
}
