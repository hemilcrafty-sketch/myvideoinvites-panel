<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserDataTable extends Migration
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
        if (!Schema::hasTable('user_data')) {
            Schema::create('user_data', function (Blueprint $table) {
                $table->id();
            });
        }

        Schema::table('user_data', function (Blueprint $table) {
            if (!Schema::hasColumn('user_data', 'uid')) {
                $table->string('uid')->index();
            }
            if (!Schema::hasColumn('user_data', 'user_name')) {
                $table->string('user_name')->nullable();
            }
            if (!Schema::hasColumn('user_data', 'bio')) {
                $table->string('bio')->nullable();
            }
            if (!Schema::hasColumn('user_data', 'is_username_update')) {
                $table->integer('is_username_update')->default(0);
            }
            if (!Schema::hasColumn('user_data', 'refer_id')) {
                $table->string('refer_id')->nullable();
            }
            if (!Schema::hasColumn('user_data', 'stripe_cus_id')) {
                $table->string('stripe_cus_id')->nullable();
            }
            if (!Schema::hasColumn('user_data', 'razorpay_cus_id')) {
                $table->string('razorpay_cus_id')->nullable();
            }
            if (!Schema::hasColumn('user_data', 'photo_uri')) {
                $table->string('photo_uri')->nullable();
            }
            if (!Schema::hasColumn('user_data', 'name')) {
                $table->string('name');
            }
            if (!Schema::hasColumn('user_data', 'contact_no')) {
                $table->string('contact_no')->nullable();
            }
            if (!Schema::hasColumn('user_data', 'contact_no_verified')) {
                $table->integer('contact_no_verified')->default(0);
            }
            if (!Schema::hasColumn('user_data', 'email')) {
                $table->string('email')->nullable();
            }
            if (!Schema::hasColumn('user_data', 'password')) {
                $table->string('password')->nullable();
            }
            if (!Schema::hasColumn('user_data', 'login_type')) {
                $table->string('login_type')->nullable();
            }
            if (!Schema::hasColumn('user_data', 'utm_source')) {
                $table->string('utm_source')->nullable();
            }
            if (!Schema::hasColumn('user_data', 'utm_medium')) {
                $table->string('utm_medium')->nullable();
            }
            if (!Schema::hasColumn('user_data', 'fldr_str')) {
                $table->string('fldr_str')->nullable();
            }
            if (!Schema::hasColumn('user_data', 'email_preference')) {
                $table->string('email_preference')->nullable();
            }
            if (!Schema::hasColumn('user_data', 'profile_count')) {
                $table->integer('profile_count')->default(0);
            }
            if (!Schema::hasColumn('user_data', 'created_at')) {
                $table->timestamp('created_at')->nullable();
            }
            if (!Schema::hasColumn('user_data', 'updated_at')) {
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
        if (Schema::hasTable('user_data')) {
            Schema::table('user_data', function (Blueprint $table) {
                if (Schema::hasColumn('user_data', 'uid')) {
                    $table->dropColumn('uid');
                }
                if (Schema::hasColumn('user_data', 'user_name')) {
                    $table->dropColumn('user_name');
                }
                if (Schema::hasColumn('user_data', 'bio')) {
                    $table->dropColumn('bio');
                }
                if (Schema::hasColumn('user_data', 'is_username_update')) {
                    $table->dropColumn('is_username_update');
                }
                if (Schema::hasColumn('user_data', 'refer_id')) {
                    $table->dropColumn('refer_id');
                }
                if (Schema::hasColumn('user_data', 'stripe_cus_id')) {
                    $table->dropColumn('stripe_cus_id');
                }
                if (Schema::hasColumn('user_data', 'razorpay_cus_id')) {
                    $table->dropColumn('razorpay_cus_id');
                }
                if (Schema::hasColumn('user_data', 'photo_uri')) {
                    $table->dropColumn('photo_uri');
                }
                if (Schema::hasColumn('user_data', 'name')) {
                    $table->dropColumn('name');
                }
                if (Schema::hasColumn('user_data', 'contact_no')) {
                    $table->dropColumn('contact_no');
                }
                if (Schema::hasColumn('user_data', 'contact_no_verified')) {
                    $table->dropColumn('contact_no_verified');
                }
                if (Schema::hasColumn('user_data', 'email')) {
                    $table->dropColumn('email');
                }
                if (Schema::hasColumn('user_data', 'password')) {
                    $table->dropColumn('password');
                }
                if (Schema::hasColumn('user_data', 'login_type')) {
                    $table->dropColumn('login_type');
                }
                if (Schema::hasColumn('user_data', 'utm_source')) {
                    $table->dropColumn('utm_source');
                }
                if (Schema::hasColumn('user_data', 'utm_medium')) {
                    $table->dropColumn('utm_medium');
                }
                if (Schema::hasColumn('user_data', 'fldr_str')) {
                    $table->dropColumn('fldr_str');
                }
                if (Schema::hasColumn('user_data', 'email_preference')) {
                    $table->dropColumn('email_preference');
                }
                if (Schema::hasColumn('user_data', 'profile_count')) {
                    $table->dropColumn('profile_count');
                }
                if (Schema::hasColumn('user_data', 'created_at')) {
                    $table->dropColumn('created_at');
                }
                if (Schema::hasColumn('user_data', 'updated_at')) {
                    $table->dropColumn('updated_at');
                }
            });
        }
    }
}
