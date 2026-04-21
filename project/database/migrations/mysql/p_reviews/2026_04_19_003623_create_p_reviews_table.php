<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePReviewsTable extends Migration
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
        if (!Schema::hasTable('p_reviews')) {
            Schema::create('p_reviews', function (Blueprint $table) {
                $table->id();
            });
        }

        Schema::table('p_reviews', function (Blueprint $table) {
            if (!Schema::hasColumn('p_reviews', 'user_id')) {
                $table->string('user_id')->nullable()->index();
            }
            if (!Schema::hasColumn('p_reviews', 'p_type')) {
                $table->integer('p_type')->index();
            }
            if (!Schema::hasColumn('p_reviews', 'p_id')) {
                $table->string('p_id')->index();
            }
            if (!Schema::hasColumn('p_reviews', 'name')) {
                $table->string('name')->nullable();
            }
            if (!Schema::hasColumn('p_reviews', 'email')) {
                $table->string('email')->nullable();
            }
            if (!Schema::hasColumn('p_reviews', 'photo_uri')) {
                $table->string('photo_uri')->nullable();
            }
            if (!Schema::hasColumn('p_reviews', 'feedback')) {
                $table->text('feedback')->nullable();
            }
            if (!Schema::hasColumn('p_reviews', 'rate')) {
                $table->integer('rate')->default(0);
            }
            if (!Schema::hasColumn('p_reviews', 'is_approve')) {
                $table->integer('is_approve')->default(0);
            }
            if (!Schema::hasColumn('p_reviews', 'is_deleted')) {
                $table->boolean('is_deleted')->default(false);
            }
            if (!Schema::hasColumn('p_reviews', 'created_at')) {
                $table->timestamp('created_at')->nullable();
            }
            if (!Schema::hasColumn('p_reviews', 'updated_at')) {
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
        if (Schema::hasTable('p_reviews')) {
            Schema::table('p_reviews', function (Blueprint $table) {
                if (Schema::hasColumn('p_reviews', 'user_id')) {
                    $table->dropColumn('user_id');
                }
                if (Schema::hasColumn('p_reviews', 'p_type')) {
                    $table->dropColumn('p_type');
                }
                if (Schema::hasColumn('p_reviews', 'p_id')) {
                    $table->dropColumn('p_id');
                }
                if (Schema::hasColumn('p_reviews', 'name')) {
                    $table->dropColumn('name');
                }
                if (Schema::hasColumn('p_reviews', 'email')) {
                    $table->dropColumn('email');
                }
                if (Schema::hasColumn('p_reviews', 'photo_uri')) {
                    $table->dropColumn('photo_uri');
                }
                if (Schema::hasColumn('p_reviews', 'feedback')) {
                    $table->dropColumn('feedback');
                }
                if (Schema::hasColumn('p_reviews', 'rate')) {
                    $table->dropColumn('rate');
                }
                if (Schema::hasColumn('p_reviews', 'is_approve')) {
                    $table->dropColumn('is_approve');
                }
                if (Schema::hasColumn('p_reviews', 'is_deleted')) {
                    $table->dropColumn('is_deleted');
                }
                if (Schema::hasColumn('p_reviews', 'created_at')) {
                    $table->dropColumn('created_at');
                }
                if (Schema::hasColumn('p_reviews', 'updated_at')) {
                    $table->dropColumn('updated_at');
                }
            });
        }
    }
}
