<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSlugHistoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('slug_history')) {
            Schema::create('slug_history', function (Blueprint $table) {
                $table->id();
                $table->integer('reference_id')->index();
                $table->string('reference_type')->index();
                $table->string('slug')->index();
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
        if (Schema::hasTable('slug_history')) {
            Schema::dropIfExists('slug_history');
        }
    }
}
