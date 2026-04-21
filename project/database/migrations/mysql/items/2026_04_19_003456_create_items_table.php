<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateItemsTable extends Migration
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
        if (!Schema::hasTable('items')) {
            Schema::create('items', function (Blueprint $table) {
                $table->id();
            });
        }

        Schema::table('items', function (Blueprint $table) {
            if (!Schema::hasColumn('items', 'string_id')) {
                $table->string('string_id')->nullable()->index();
            }
            if (!Schema::hasColumn('items', 'video_name')) {
                $table->string('video_name');
            }
            if (!Schema::hasColumn('items', 'slug')) {
                $table->string('slug')->unique();
            }
            if (!Schema::hasColumn('items', 'video_url')) {
                $table->string('video_url')->nullable();
            }
            if (!Schema::hasColumn('items', 'video_thumb')) {
                $table->string('video_thumb')->nullable();
            }
            if (!Schema::hasColumn('items', 'video_zip_url')) {
                $table->string('video_zip_url')->nullable();
            }
            if (!Schema::hasColumn('items', 'folder_name')) {
                $table->string('folder_name')->nullable();
            }
            if (!Schema::hasColumn('items', 'category_id')) {
                $table->integer('category_id')->index();
            }
            if (!Schema::hasColumn('items', 'virtual_category_id')) {
                $table->integer('virtual_category_id')->nullable()->index();
            }
            if (!Schema::hasColumn('items', 'template_type')) {
                $table->integer('template_type')->default(0);
            }
            if (!Schema::hasColumn('items', 'template_size')) {
                $table->string('template_size')->nullable();
            }
            if (!Schema::hasColumn('items', 'width')) {
                $table->integer('width')->default(0);
            }
            if (!Schema::hasColumn('items', 'height')) {
                $table->integer('height')->default(0);
            }
            if (!Schema::hasColumn('items', 'pages')) {
                $table->integer('pages')->default(1);
            }
            if (!Schema::hasColumn('items', 'orientation')) {
                $table->string('orientation')->nullable();
            }
            if (!Schema::hasColumn('items', 'lang_id')) {
                $table->integer('lang_id')->nullable();
            }
            if (!Schema::hasColumn('items', 'religion_id')) {
                $table->integer('religion_id')->nullable();
            }
            if (!Schema::hasColumn('items', 'interest_id')) {
                $table->integer('interest_id')->nullable();
            }
            if (!Schema::hasColumn('items', 'style_id')) {
                $table->integer('style_id')->nullable();
            }
            if (!Schema::hasColumn('items', 'theme_id')) {
                $table->integer('theme_id')->nullable();
            }
            if (!Schema::hasColumn('items', 'color_ids')) {
                $table->string('color_ids')->nullable();
            }
            if (!Schema::hasColumn('items', 'relation_id')) {
                $table->integer('relation_id')->nullable();
            }
            if (!Schema::hasColumn('items', 'is_premium')) {
                $table->boolean('is_premium')->default(false);
            }
            if (!Schema::hasColumn('items', 'is_freemium')) {
                $table->boolean('is_freemium')->default(false);
            }
            if (!Schema::hasColumn('items', 'status')) {
                $table->integer('status')->default(1);
            }
            if (!Schema::hasColumn('items', 'priority')) {
                $table->integer('priority')->default(0);
            }
            if (!Schema::hasColumn('items', 'views')) {
                $table->integer('views')->default(0);
            }
            if (!Schema::hasColumn('items', 'daily_views')) {
                $table->integer('daily_views')->default(0);
            }
            if (!Schema::hasColumn('items', 'weekly_views')) {
                $table->integer('weekly_views')->default(0);
            }
            if (!Schema::hasColumn('items', 'creation')) {
                $table->integer('creation')->default(0);
            }
            if (!Schema::hasColumn('items', 'daily_creation')) {
                $table->integer('daily_creation')->default(0);
            }
            if (!Schema::hasColumn('items', 'weekly_creation')) {
                $table->integer('weekly_creation')->default(0);
            }
            if (!Schema::hasColumn('items', 'meta_title')) {
                $table->string('meta_title')->nullable();
            }
            if (!Schema::hasColumn('items', 'meta_description')) {
                $table->text('meta_description')->nullable();
            }
            if (!Schema::hasColumn('items', 'keyword')) {
                $table->string('keyword')->nullable();
            }
            if (!Schema::hasColumn('items', 'h2_tag')) {
                $table->string('h2_tag')->nullable();
            }
            if (!Schema::hasColumn('items', 'canonical_link')) {
                $table->string('canonical_link')->nullable();
            }
            if (!Schema::hasColumn('items', 'no_index')) {
                $table->boolean('no_index')->default(false);
            }
            if (!Schema::hasColumn('items', 'editable_text')) {
                $table->boolean('editable_text')->default(true);
            }
            if (!Schema::hasColumn('items', 'editable_image')) {
                $table->boolean('editable_image')->default(true);
            }
            if (!Schema::hasColumn('items', 'change_music')) {
                $table->boolean('change_music')->default(true);
            }
            if (!Schema::hasColumn('items', 'change_text')) {
                $table->boolean('change_text')->default(true);
            }
            if (!Schema::hasColumn('items', 'do_front_lottie')) {
                $table->boolean('do_front_lottie')->default(false);
            }
            if (!Schema::hasColumn('items', 'watermark_height')) {
                $table->integer('watermark_height')->nullable();
            }
            if (!Schema::hasColumn('items', 'start_date')) {
                $table->dateTime('start_date')->nullable();
            }
            if (!Schema::hasColumn('items', 'end_date')) {
                $table->dateTime('end_date')->nullable();
            }
            if (!Schema::hasColumn('items', 'emp_id')) {
                $table->integer('emp_id')->nullable();
            }
            if (!Schema::hasColumn('items', 'seo_emp_id')) {
                $table->integer('seo_emp_id')->nullable();
            }
            if (!Schema::hasColumn('items', 'seo_assigner_id')) {
                $table->integer('seo_assigner_id')->nullable();
            }
            if (!Schema::hasColumn('items', 'encrypted')) {
                $table->boolean('encrypted')->default(false);
            }
            if (!Schema::hasColumn('items', 'encryption_key')) {
                $table->string('encryption_key')->nullable();
            }
            if (!Schema::hasColumn('items', 'is_deleted')) {
                $table->boolean('is_deleted')->default(false);
            }
            if (!Schema::hasColumn('items', 'created_at')) {
                $table->timestamp('created_at')->nullable();
            }
            if (!Schema::hasColumn('items', 'updated_at')) {
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
        if (Schema::hasTable('items')) {
            Schema::table('items', function (Blueprint $table) {
                if (Schema::hasColumn('items', 'string_id')) {
                    $table->dropColumn('string_id');
                }
                if (Schema::hasColumn('items', 'video_name')) {
                    $table->dropColumn('video_name');
                }
                if (Schema::hasColumn('items', 'slug')) {
                    $table->dropColumn('slug');
                }
                if (Schema::hasColumn('items', 'video_url')) {
                    $table->dropColumn('video_url');
                }
                if (Schema::hasColumn('items', 'video_thumb')) {
                    $table->dropColumn('video_thumb');
                }
                if (Schema::hasColumn('items', 'video_zip_url')) {
                    $table->dropColumn('video_zip_url');
                }
                if (Schema::hasColumn('items', 'folder_name')) {
                    $table->dropColumn('folder_name');
                }
                if (Schema::hasColumn('items', 'category_id')) {
                    $table->dropColumn('category_id');
                }
                if (Schema::hasColumn('items', 'virtual_category_id')) {
                    $table->dropColumn('virtual_category_id');
                }
                if (Schema::hasColumn('items', 'template_type')) {
                    $table->dropColumn('template_type');
                }
                if (Schema::hasColumn('items', 'template_size')) {
                    $table->dropColumn('template_size');
                }
                if (Schema::hasColumn('items', 'width')) {
                    $table->dropColumn('width');
                }
                if (Schema::hasColumn('items', 'height')) {
                    $table->dropColumn('height');
                }
                if (Schema::hasColumn('items', 'pages')) {
                    $table->dropColumn('pages');
                }
                if (Schema::hasColumn('items', 'orientation')) {
                    $table->dropColumn('orientation');
                }
                if (Schema::hasColumn('items', 'lang_id')) {
                    $table->dropColumn('lang_id');
                }
                if (Schema::hasColumn('items', 'religion_id')) {
                    $table->dropColumn('religion_id');
                }
                if (Schema::hasColumn('items', 'interest_id')) {
                    $table->dropColumn('interest_id');
                }
                if (Schema::hasColumn('items', 'style_id')) {
                    $table->dropColumn('style_id');
                }
                if (Schema::hasColumn('items', 'theme_id')) {
                    $table->dropColumn('theme_id');
                }
                if (Schema::hasColumn('items', 'color_ids')) {
                    $table->dropColumn('color_ids');
                }
                if (Schema::hasColumn('items', 'relation_id')) {
                    $table->dropColumn('relation_id');
                }
                if (Schema::hasColumn('items', 'is_premium')) {
                    $table->dropColumn('is_premium');
                }
                if (Schema::hasColumn('items', 'is_freemium')) {
                    $table->dropColumn('is_freemium');
                }
                if (Schema::hasColumn('items', 'status')) {
                    $table->dropColumn('status');
                }
                if (Schema::hasColumn('items', 'priority')) {
                    $table->dropColumn('priority');
                }
                if (Schema::hasColumn('items', 'views')) {
                    $table->dropColumn('views');
                }
                if (Schema::hasColumn('items', 'daily_views')) {
                    $table->dropColumn('daily_views');
                }
                if (Schema::hasColumn('items', 'weekly_views')) {
                    $table->dropColumn('weekly_views');
                }
                if (Schema::hasColumn('items', 'creation')) {
                    $table->dropColumn('creation');
                }
                if (Schema::hasColumn('items', 'daily_creation')) {
                    $table->dropColumn('daily_creation');
                }
                if (Schema::hasColumn('items', 'weekly_creation')) {
                    $table->dropColumn('weekly_creation');
                }
                if (Schema::hasColumn('items', 'meta_title')) {
                    $table->dropColumn('meta_title');
                }
                if (Schema::hasColumn('items', 'meta_description')) {
                    $table->dropColumn('meta_description');
                }
                if (Schema::hasColumn('items', 'keyword')) {
                    $table->dropColumn('keyword');
                }
                if (Schema::hasColumn('items', 'h2_tag')) {
                    $table->dropColumn('h2_tag');
                }
                if (Schema::hasColumn('items', 'canonical_link')) {
                    $table->dropColumn('canonical_link');
                }
                if (Schema::hasColumn('items', 'no_index')) {
                    $table->dropColumn('no_index');
                }
                if (Schema::hasColumn('items', 'editable_text')) {
                    $table->dropColumn('editable_text');
                }
                if (Schema::hasColumn('items', 'editable_image')) {
                    $table->dropColumn('editable_image');
                }
                if (Schema::hasColumn('items', 'change_music')) {
                    $table->dropColumn('change_music');
                }
                if (Schema::hasColumn('items', 'change_text')) {
                    $table->dropColumn('change_text');
                }
                if (Schema::hasColumn('items', 'do_front_lottie')) {
                    $table->dropColumn('do_front_lottie');
                }
                if (Schema::hasColumn('items', 'watermark_height')) {
                    $table->dropColumn('watermark_height');
                }
                if (Schema::hasColumn('items', 'start_date')) {
                    $table->dropColumn('start_date');
                }
                if (Schema::hasColumn('items', 'end_date')) {
                    $table->dropColumn('end_date');
                }
                if (Schema::hasColumn('items', 'emp_id')) {
                    $table->dropColumn('emp_id');
                }
                if (Schema::hasColumn('items', 'seo_emp_id')) {
                    $table->dropColumn('seo_emp_id');
                }
                if (Schema::hasColumn('items', 'seo_assigner_id')) {
                    $table->dropColumn('seo_assigner_id');
                }
                if (Schema::hasColumn('items', 'encrypted')) {
                    $table->dropColumn('encrypted');
                }
                if (Schema::hasColumn('items', 'encryption_key')) {
                    $table->dropColumn('encryption_key');
                }
                if (Schema::hasColumn('items', 'is_deleted')) {
                    $table->dropColumn('is_deleted');
                }
                if (Schema::hasColumn('items', 'created_at')) {
                    $table->dropColumn('created_at');
                }
                if (Schema::hasColumn('items', 'updated_at')) {
                    $table->dropColumn('updated_at');
                }
            });
        }
    }
}
