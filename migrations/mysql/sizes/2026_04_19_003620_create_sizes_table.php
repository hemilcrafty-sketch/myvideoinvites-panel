<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSizesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('sizes')) {
            Schema::create('sizes', function (Blueprint $table) {
                $table->id();
                $table->string('size_name');
                $table->string('paper_size')->nullable();
                $table->string('thumb');
                $table->string('category_id')->nullable();
                $table->string('id_name')->nullable();
                $table->integer('width_ration')->default(0);
                $table->integer('height_ration')->default(0);
                $table->integer('width')->default(0);
                $table->integer('height')->default(0);
                $table->string('unit')->nullable();
                $table->integer('status')->default(1);
                $table->integer('emp_id')->nullable();
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
        if (Schema::hasTable('sizes')) {
            Schema::dropIfExists('sizes');
        }
    }
}
