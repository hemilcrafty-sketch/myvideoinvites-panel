<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLanguagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('language')) {
            Schema::create('language', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('id_name')->nullable();
                $table->integer('emp_id')->nullable();
                $table->integer('status')->default(1);
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
        if (Schema::hasTable('language')) {
            Schema::dropIfExists('language');
        }
    }
}
