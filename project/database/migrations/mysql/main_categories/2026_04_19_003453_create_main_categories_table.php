<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMainCategoriesTable extends Migration
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
        if (!Schema::hasTable('main_categories')) {
            Schema::create('main_categories', function (Blueprint $table) {
                $table->id();
            });
        }

        Schema::table('main_categories', function (Blueprint $table) {
            if (!Schema::hasColumn('main_categories', 'string_id')) {
                $table->string('string_id')->nullable()->index();
            }
            if (!Schema::hasColumn('main_categories', 'category_name')) {
                $table->string('category_name');
            }
            if (!Schema::hasColumn('main_categories', 'category_title')) {
                $table->string('category_title')->nullable();
            }
            if (!Schema::hasColumn('main_categories', 'slug')) {
                $table->string('slug')->unique();
            }
            if (!Schema::hasColumn('main_categories', 'tag_line')) {
                $table->string('tag_line')->nullable();
            }
            if (!Schema::hasColumn('main_categories', 'short_desc')) {
                $table->text('short_desc')->nullable();
            }
            if (!Schema::hasColumn('main_categories', 'long_desc')) {
                $table->text('long_desc')->nullable();
            }
            if (!Schema::hasColumn('main_categories', 'category_thumb')) {
                $table->string('category_thumb')->nullable();
            }
            if (!Schema::hasColumn('main_categories', 'banner')) {
                $table->string('banner')->nullable();
            }
            if (!Schema::hasColumn('main_categories', 'mockup')) {
                $table->string('mockup')->nullable();
            }
            if (!Schema::hasColumn('main_categories', 'parent_category_id')) {
                $table->integer('parent_category_id')->default(0)->index();
            }
            if (!Schema::hasColumn('main_categories', 'child_cat_ids')) {
                $table->string('child_cat_ids')->nullable();
            }
            if (!Schema::hasColumn('main_categories', 'sequence_number')) {
                $table->integer('sequence_number')->default(0);
            }
            if (!Schema::hasColumn('main_categories', 'priority')) {
                $table->integer('priority')->default(0);
            }
            if (!Schema::hasColumn('main_categories', 'status')) {
                $table->integer('status')->default(1);
            }
            if (!Schema::hasColumn('main_categories', 'total_templates')) {
                $table->integer('total_templates')->default(0);
            }
            if (!Schema::hasColumn('main_categories', 'meta_title')) {
                $table->string('meta_title')->nullable();
            }
            if (!Schema::hasColumn('main_categories', 'meta_desc')) {
                $table->text('meta_desc')->nullable();
            }
            if (!Schema::hasColumn('main_categories', 'h1_tag')) {
                $table->string('h1_tag')->nullable();
            }
            if (!Schema::hasColumn('main_categories', 'h2_tag')) {
                $table->string('h2_tag')->nullable();
            }
            if (!Schema::hasColumn('main_categories', 'primary_keyword')) {
                $table->text('primary_keyword')->nullable();
            }
            if (!Schema::hasColumn('main_categories', 'top_keywords')) {
                $table->text('top_keywords')->nullable();
            }
            if (!Schema::hasColumn('main_categories', 'canonical_link')) {
                $table->string('canonical_link')->nullable();
            }
            if (!Schema::hasColumn('main_categories', 'no_index')) {
                $table->boolean('no_index')->default(false);
            }
            if (!Schema::hasColumn('main_categories', 'contents')) {
                $table->text('contents')->nullable();
            }
            if (!Schema::hasColumn('main_categories', 'faqs')) {
                $table->text('faqs')->nullable();
            }
            if (!Schema::hasColumn('main_categories', 'emp_id')) {
                $table->integer('emp_id')->nullable();
            }
            if (!Schema::hasColumn('main_categories', 'seo_emp_id')) {
                $table->integer('seo_emp_id')->nullable();
            }
            if (!Schema::hasColumn('main_categories', 'fldr_str')) {
                $table->string('fldr_str')->nullable();
            }
            if (!Schema::hasColumn('main_categories', 'created_at')) {
                $table->timestamp('created_at')->nullable();
            }
            if (!Schema::hasColumn('main_categories', 'updated_at')) {
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
        if (Schema::hasTable('main_categories')) {
            Schema::table('main_categories', function (Blueprint $table) {
                if (Schema::hasColumn('main_categories', 'string_id')) {
                    $table->dropColumn('string_id');
                }
                if (Schema::hasColumn('main_categories', 'category_name')) {
                    $table->dropColumn('category_name');
                }
                if (Schema::hasColumn('main_categories', 'category_title')) {
                    $table->dropColumn('category_title');
                }
                if (Schema::hasColumn('main_categories', 'slug')) {
                    $table->dropColumn('slug');
                }
                if (Schema::hasColumn('main_categories', 'tag_line')) {
                    $table->dropColumn('tag_line');
                }
                if (Schema::hasColumn('main_categories', 'short_desc')) {
                    $table->dropColumn('short_desc');
                }
                if (Schema::hasColumn('main_categories', 'long_desc')) {
                    $table->dropColumn('long_desc');
                }
                if (Schema::hasColumn('main_categories', 'category_thumb')) {
                    $table->dropColumn('category_thumb');
                }
                if (Schema::hasColumn('main_categories', 'banner')) {
                    $table->dropColumn('banner');
                }
                if (Schema::hasColumn('main_categories', 'mockup')) {
                    $table->dropColumn('mockup');
                }
                if (Schema::hasColumn('main_categories', 'parent_category_id')) {
                    $table->dropColumn('parent_category_id');
                }
                if (Schema::hasColumn('main_categories', 'child_cat_ids')) {
                    $table->dropColumn('child_cat_ids');
                }
                if (Schema::hasColumn('main_categories', 'sequence_number')) {
                    $table->dropColumn('sequence_number');
                }
                if (Schema::hasColumn('main_categories', 'priority')) {
                    $table->dropColumn('priority');
                }
                if (Schema::hasColumn('main_categories', 'status')) {
                    $table->dropColumn('status');
                }
                if (Schema::hasColumn('main_categories', 'total_templates')) {
                    $table->dropColumn('total_templates');
                }
                if (Schema::hasColumn('main_categories', 'meta_title')) {
                    $table->dropColumn('meta_title');
                }
                if (Schema::hasColumn('main_categories', 'meta_desc')) {
                    $table->dropColumn('meta_desc');
                }
                if (Schema::hasColumn('main_categories', 'h1_tag')) {
                    $table->dropColumn('h1_tag');
                }
                if (Schema::hasColumn('main_categories', 'h2_tag')) {
                    $table->dropColumn('h2_tag');
                }
                if (Schema::hasColumn('main_categories', 'primary_keyword')) {
                    $table->dropColumn('primary_keyword');
                }
                if (Schema::hasColumn('main_categories', 'top_keywords')) {
                    $table->dropColumn('top_keywords');
                }
                if (Schema::hasColumn('main_categories', 'canonical_link')) {
                    $table->dropColumn('canonical_link');
                }
                if (Schema::hasColumn('main_categories', 'no_index')) {
                    $table->dropColumn('no_index');
                }
                if (Schema::hasColumn('main_categories', 'contents')) {
                    $table->dropColumn('contents');
                }
                if (Schema::hasColumn('main_categories', 'faqs')) {
                    $table->dropColumn('faqs');
                }
                if (Schema::hasColumn('main_categories', 'emp_id')) {
                    $table->dropColumn('emp_id');
                }
                if (Schema::hasColumn('main_categories', 'seo_emp_id')) {
                    $table->dropColumn('seo_emp_id');
                }
                if (Schema::hasColumn('main_categories', 'fldr_str')) {
                    $table->dropColumn('fldr_str');
                }
                if (Schema::hasColumn('main_categories', 'created_at')) {
                    $table->dropColumn('created_at');
                }
                if (Schema::hasColumn('main_categories', 'updated_at')) {
                    $table->dropColumn('updated_at');
                }
            });
        }
    }
}
