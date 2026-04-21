<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReviewsTable extends Migration
{
    /**
     * The database connection that should be used by the migration.
     *
     * @var string
     */
    protected $connection = 'mysql';
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('reviews')) {
            Schema::create('reviews', function (Blueprint $table) {
                $table->id();
            });
        }

        Schema::table('reviews', function (Blueprint $table) {
            if (!Schema::hasColumn('reviews', 'user_id')) {
                $table->string('user_id')->nullable()->index();
            }
            if (!Schema::hasColumn('reviews', 'name')) {
                $table->string('name')->nullable();
            }
            if (!Schema::hasColumn('reviews', 'email')) {
                $table->string('email')->nullable();
            }
            if (!Schema::hasColumn('reviews', 'photo_uri')) {
                $table->string('photo_uri')->nullable();
            }
            if (!Schema::hasColumn('reviews', 'feedback')) {
                $table->text('feedback')->nullable();
            }
            if (!Schema::hasColumn('reviews', 'rate')) {
                $table->integer('rate')->default(0);
            }
            if (!Schema::hasColumn('reviews', 'is_approve')) {
                $table->integer('is_approve')->default(0);
            }
            if (!Schema::hasColumn('reviews', 'created_at')) {
                $table->timestamp('created_at')->nullable();
            }
            if (!Schema::hasColumn('reviews', 'updated_at')) {
                $table->timestamp('updated_at')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasTable('reviews')) {
            Schema::dropIfExists('reviews');
        }
    }
}
