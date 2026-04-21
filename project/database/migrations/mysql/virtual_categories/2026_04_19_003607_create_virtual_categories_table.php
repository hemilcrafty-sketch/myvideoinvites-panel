<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVirtualCategoriesTable extends Migration
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
        if (!Schema::hasTable('virtual_categories')) {
            Schema::create('virtual_categories', function (Blueprint $table) {
                $table->id();
            });
        }

        Schema::table('virtual_categories', function (Blueprint $table) {
            if (!Schema::hasColumn('virtual_categories', 'parent_category_id')) {
                $table->integer('parent_category_id')->nullable()->index();
            }
            if (!Schema::hasColumn('virtual_categories', 'slug')) {
                $table->string('slug')->nullable()->unique();
            }
            if (!Schema::hasColumn('virtual_categories', 'string_id')) {
                $table->string('string_id')->nullable()->index();
            }
            if (!Schema::hasColumn('virtual_categories', 'emp_id')) {
                $table->integer('emp_id')->nullable();
            }
            if (!Schema::hasColumn('virtual_categories', 'seo_emp_id')) {
                $table->string('seo_emp_id')->nullable();
            }
            if (!Schema::hasColumn('virtual_categories', 'canonical_link')) {
                $table->string('canonical_link')->nullable();
            }
            if (!Schema::hasColumn('virtual_categories', 'meta_title')) {
                $table->string('meta_title')->nullable();
            }
            if (!Schema::hasColumn('virtual_categories', 'meta_desc')) {
                $table->text('meta_desc')->nullable();
            }
            if (!Schema::hasColumn('virtual_categories', 'h1_tag')) {
                $table->string('h1_tag')->nullable();
            }
            if (!Schema::hasColumn('virtual_categories', 'h2_tag')) {
                $table->string('h2_tag')->nullable();
            }
            if (!Schema::hasColumn('virtual_categories', 'short_desc')) {
                $table->text('short_desc')->nullable();
            }
            if (!Schema::hasColumn('virtual_categories', 'long_desc')) {
                $table->text('long_desc')->nullable();
            }
            if (!Schema::hasColumn('virtual_categories', 'tag_line')) {
                $table->string('tag_line')->nullable();
            }
            if (!Schema::hasColumn('virtual_categories', 'category_name')) {
                $table->string('category_name');
            }
            if (!Schema::hasColumn('virtual_categories', 'size')) {
                $table->string('size')->nullable();
            }
            if (!Schema::hasColumn('virtual_categories', 'category_thumb')) {
                $table->string('category_thumb')->nullable();
            }
            if (!Schema::hasColumn('virtual_categories', 'mockup')) {
                $table->string('mockup')->nullable();
            }
            if (!Schema::hasColumn('virtual_categories', 'banner')) {
                $table->string('banner')->nullable();
            }
            if (!Schema::hasColumn('virtual_categories', 'contents')) {
                $table->text('contents')->nullable();
            }
            if (!Schema::hasColumn('virtual_categories', 'faqs')) {
                $table->text('faqs')->nullable();
            }
            if (!Schema::hasColumn('virtual_categories', 'fldr_str')) {
                $table->string('fldr_str')->nullable();
            }
            if (!Schema::hasColumn('virtual_categories', 'top_keywords')) {
                $table->text('top_keywords')->nullable();
            }
            if (!Schema::hasColumn('virtual_categories', 'cta')) {
                $table->string('cta')->nullable();
            }
            if (!Schema::hasColumn('virtual_categories', 'primary_keyword')) {
                $table->text('primary_keyword')->nullable();
            }
            if (!Schema::hasColumn('virtual_categories', 'imp')) {
                $table->integer('imp')->default(0);
            }
            if (!Schema::hasColumn('virtual_categories', 'sequence_number')) {
                $table->integer('sequence_number')->default(0);
            }
            if (!Schema::hasColumn('virtual_categories', 'no_index')) {
                $table->boolean('no_index')->default(false);
            }
            if (!Schema::hasColumn('virtual_categories', 'priority')) {
                $table->string('priority')->nullable();
            }
            if (!Schema::hasColumn('virtual_categories', 'frequency')) {
                $table->string('frequency')->nullable();
            }
            if (!Schema::hasColumn('virtual_categories', 'status')) {
                $table->integer('status')->default(1);
            }
            if (!Schema::hasColumn('virtual_categories', 'deleted')) {
                $table->boolean('deleted')->default(false);
            }
            if (!Schema::hasColumn('virtual_categories', 'created_at')) {
                $table->timestamp('created_at')->nullable();
            }
            if (!Schema::hasColumn('virtual_categories', 'updated_at')) {
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
        if (Schema::hasTable('virtual_categories')) {
            Schema::table('virtual_categories', function (Blueprint $table) {
                if (Schema::hasColumn('virtual_categories', 'parent_category_id')) {
                    $table->dropColumn('parent_category_id');
                }
                if (Schema::hasColumn('virtual_categories', 'slug')) {
                    $table->dropColumn('slug');
                }
                if (Schema::hasColumn('virtual_categories', 'string_id')) {
                    $table->dropColumn('string_id');
                }
                if (Schema::hasColumn('virtual_categories', 'emp_id')) {
                    $table->dropColumn('emp_id');
                }
                if (Schema::hasColumn('virtual_categories', 'seo_emp_id')) {
                    $table->dropColumn('seo_emp_id');
                }
                if (Schema::hasColumn('virtual_categories', 'canonical_link')) {
                    $table->dropColumn('canonical_link');
                }
                if (Schema::hasColumn('virtual_categories', 'meta_title')) {
                    $table->dropColumn('meta_title');
                }
                if (Schema::hasColumn('virtual_categories', 'meta_desc')) {
                    $table->dropColumn('meta_desc');
                }
                if (Schema::hasColumn('virtual_categories', 'h1_tag')) {
                    $table->dropColumn('h1_tag');
                }
                if (Schema::hasColumn('virtual_categories', 'h2_tag')) {
                    $table->dropColumn('h2_tag');
                }
                if (Schema::hasColumn('virtual_categories', 'short_desc')) {
                    $table->dropColumn('short_desc');
                }
                if (Schema::hasColumn('virtual_categories', 'long_desc')) {
                    $table->dropColumn('long_desc');
                }
                if (Schema::hasColumn('virtual_categories', 'tag_line')) {
                    $table->dropColumn('tag_line');
                }
                if (Schema::hasColumn('virtual_categories', 'category_name')) {
                    $table->dropColumn('category_name');
                }
                if (Schema::hasColumn('virtual_categories', 'size')) {
                    $table->dropColumn('size');
                }
                if (Schema::hasColumn('virtual_categories', 'category_thumb')) {
                    $table->dropColumn('category_thumb');
                }
                if (Schema::hasColumn('virtual_categories', 'mockup')) {
                    $table->dropColumn('mockup');
                }
                if (Schema::hasColumn('virtual_categories', 'banner')) {
                    $table->dropColumn('banner');
                }
                if (Schema::hasColumn('virtual_categories', 'contents')) {
                    $table->dropColumn('contents');
                }
                if (Schema::hasColumn('virtual_categories', 'faqs')) {
                    $table->dropColumn('faqs');
                }
                if (Schema::hasColumn('virtual_categories', 'fldr_str')) {
                    $table->dropColumn('fldr_str');
                }
                if (Schema::hasColumn('virtual_categories', 'top_keywords')) {
                    $table->dropColumn('top_keywords');
                }
                if (Schema::hasColumn('virtual_categories', 'cta')) {
                    $table->dropColumn('cta');
                }
                if (Schema::hasColumn('virtual_categories', 'primary_keyword')) {
                    $table->dropColumn('primary_keyword');
                }
                if (Schema::hasColumn('virtual_categories', 'imp')) {
                    $table->dropColumn('imp');
                }
                if (Schema::hasColumn('virtual_categories', 'sequence_number')) {
                    $table->dropColumn('sequence_number');
                }
                if (Schema::hasColumn('virtual_categories', 'no_index')) {
                    $table->dropColumn('no_index');
                }
                if (Schema::hasColumn('virtual_categories', 'priority')) {
                    $table->dropColumn('priority');
                }
                if (Schema::hasColumn('virtual_categories', 'frequency')) {
                    $table->dropColumn('frequency');
                }
                if (Schema::hasColumn('virtual_categories', 'status')) {
                    $table->dropColumn('status');
                }
                if (Schema::hasColumn('virtual_categories', 'deleted')) {
                    $table->dropColumn('deleted');
                }
                if (Schema::hasColumn('virtual_categories', 'created_at')) {
                    $table->dropColumn('created_at');
                }
                if (Schema::hasColumn('virtual_categories', 'updated_at')) {
                    $table->dropColumn('updated_at');
                }
            });
        }
    }
}
