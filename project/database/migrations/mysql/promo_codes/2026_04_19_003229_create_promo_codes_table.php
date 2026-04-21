<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePromoCodesTable extends Migration
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
        if (!Schema::hasTable('promo_codes')) {
            Schema::create('promo_codes', function (Blueprint $table) {
                $table->id();
            });
        }

        Schema::table('promo_codes', function (Blueprint $table) {
            if (!Schema::hasColumn('promo_codes', 'user_id')) {
                $table->string('user_id')->nullable()->index();
            }
            if (!Schema::hasColumn('promo_codes', 'promo_code')) {
                $table->string('promo_code')->unique();
            }
            if (!Schema::hasColumn('promo_codes', 'disc')) {
                $table->integer('disc')->default(0);
            }
            if (!Schema::hasColumn('promo_codes', 'additional_days')) {
                $table->integer('additional_days')->default(0);
            }
            if (!Schema::hasColumn('promo_codes', 'type')) {
                $table->string('type')->nullable();
            }
            if (!Schema::hasColumn('promo_codes', 'status')) {
                $table->integer('status')->default(1);
            }
            if (!Schema::hasColumn('promo_codes', 'expiry_date')) {
                $table->dateTime('expiry_date')->nullable();
            }
            if (!Schema::hasColumn('promo_codes', 'disc_upto_inr')) {
                $table->integer('disc_upto_inr')->default(0);
            }
            if (!Schema::hasColumn('promo_codes', 'min_cart_inr')) {
                $table->integer('min_cart_inr')->default(0);
            }
            if (!Schema::hasColumn('promo_codes', 'disc_upto_usd')) {
                $table->integer('disc_upto_usd')->default(0);
            }
            if (!Schema::hasColumn('promo_codes', 'min_cart_usd')) {
                $table->integer('min_cart_usd')->default(0);
            }
            if (!Schema::hasColumn('promo_codes', 'created_at')) {
                $table->timestamp('created_at')->nullable();
            }
            if (!Schema::hasColumn('promo_codes', 'updated_at')) {
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
        if (Schema::hasTable('promo_codes')) {
            Schema::table('promo_codes', function (Blueprint $table) {
                if (Schema::hasColumn('promo_codes', 'user_id')) {
                    $table->dropColumn('user_id');
                }
                if (Schema::hasColumn('promo_codes', 'promo_code')) {
                    $table->dropColumn('promo_code');
                }
                if (Schema::hasColumn('promo_codes', 'disc')) {
                    $table->dropColumn('disc');
                }
                if (Schema::hasColumn('promo_codes', 'additional_days')) {
                    $table->dropColumn('additional_days');
                }
                if (Schema::hasColumn('promo_codes', 'type')) {
                    $table->dropColumn('type');
                }
                if (Schema::hasColumn('promo_codes', 'status')) {
                    $table->dropColumn('status');
                }
                if (Schema::hasColumn('promo_codes', 'expiry_date')) {
                    $table->dropColumn('expiry_date');
                }
                if (Schema::hasColumn('promo_codes', 'disc_upto_inr')) {
                    $table->dropColumn('disc_upto_inr');
                }
                if (Schema::hasColumn('promo_codes', 'min_cart_inr')) {
                    $table->dropColumn('min_cart_inr');
                }
                if (Schema::hasColumn('promo_codes', 'disc_upto_usd')) {
                    $table->dropColumn('disc_upto_usd');
                }
                if (Schema::hasColumn('promo_codes', 'min_cart_usd')) {
                    $table->dropColumn('min_cart_usd');
                }
                if (Schema::hasColumn('promo_codes', 'created_at')) {
                    $table->dropColumn('created_at');
                }
                if (Schema::hasColumn('promo_codes', 'updated_at')) {
                    $table->dropColumn('updated_at');
                }
            });
        }
    }
}
