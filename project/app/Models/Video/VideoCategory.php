<?php

namespace App\Models\Video;

use App\Http\Controllers\Utils\HelperController;
use App\Http\Controllers\Utils\QueryManager;
use App\Traits\HasFullSlug;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Closure;

/**
 * App\Models\Video\VideoCategory
 *
 * @property int $id
 * @property int|null $parent_category_id
 * @property mixed|null $child_cat_ids
 * @property int $total_templates
 * @property int $emp_id
 * @property int|null $seo_emp_id
 * @property string|null $string_id
 * @property string $category_name
 * @property string|null $category_title
 * @property string|null $slug
 * @property string|null $canonical_link
 * @property string|null $meta_title
 * @property string|null $primary_keyword
 * @property string|null $h1_tag
 * @property string|null $tag_line
 * @property string|null $meta_desc
 * @property string|null $short_desc
 * @property string|null $h2_tag
 * @property string|null $long_desc
 * @property string $category_thumb
 * @property string|null $mockup
 * @property string|null $banner
 * @property string|null $contents
 * @property string|null $faqs
 * @property array|null $top_keywords
 * @property int $sequence_number
 * @property int $status
 * @property int $imp
 * @property int $no_index
 * @property string $priority
 * @property string $frequency
 * @property string|null $fldr_str
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read string $full_slug
 * @property-read VideoCategory|null $parentCategory
 * @property Collection<int, VideoCategory> $subcategories
 * @property-read int|null $subcategories_count
 * @property-read Collection<int, VideoTemplate> $videoTemplates
 * @property-read int|null $video_templates_count
 * @method static Builder|VideoCategory newModelQuery()
 * @method static Builder|VideoCategory newQuery()
 * @method static Builder|VideoCategory query()
 * @method static Builder|VideoCategory whereBanner($value)
 * @method static Builder|VideoCategory whereCanonicalLink($value)
 * @method static Builder|VideoCategory whereCategoryName($value)
 * @method static Builder|VideoCategory whereCategoryThumb($value)
 * @method static Builder|VideoCategory whereCategoryTitle($value)
 * @method static Builder|VideoCategory whereChildCatIds($value)
 * @method static Builder|VideoCategory whereContents($value)
 * @method static Builder|VideoCategory whereCreatedAt($value)
 * @method static Builder|VideoCategory whereEmpId($value)
 * @method static Builder|VideoCategory whereFaqs($value)
 * @method static Builder|VideoCategory whereFldrStr($value)
 * @method static Builder|VideoCategory whereFrequency($value)
 * @method static Builder|VideoCategory whereH1Tag($value)
 * @method static Builder|VideoCategory whereH2Tag($value)
 * @method static Builder|VideoCategory whereId($value)
 * @method static Builder|VideoCategory whereImp($value)
 * @method static Builder|VideoCategory whereLongDesc($value)
 * @method static Builder|VideoCategory whereMetaDesc($value)
 * @method static Builder|VideoCategory whereMetaTitle($value)
 * @method static Builder|VideoCategory whereMockup($value)
 * @method static Builder|VideoCategory whereNoIndex($value)
 * @method static Builder|VideoCategory whereParentCategoryId($value)
 * @method static Builder|VideoCategory wherePrimaryKeyword($value)
 * @method static Builder|VideoCategory wherePriority($value)
 * @method static Builder|VideoCategory whereSeoEmpId($value)
 * @method static Builder|VideoCategory whereSequenceNumber($value)
 * @method static Builder|VideoCategory whereShortDesc($value)
 * @method static Builder|VideoCategory whereSlug($value)
 * @method static Builder|VideoCategory whereStatus($value)
 * @method static Builder|VideoCategory whereStringId($value)
 * @method static Builder|VideoCategory whereTagLine($value)
 * @method static Builder|VideoCategory whereTopKeywords($value)
 * @method static Builder|VideoCategory whereTotalTemplates($value)
 * @method static Builder|VideoCategory whereUpdatedAt($value)
 * @mixin \Eloquent
 */

class VideoCategory extends Model
{
    public static array $defaultCategorySelect = ['id', 'parent_category_id', 'string_id', 'category_name', 'category_thumb', 'banner', 'mockup', 'cat_link', 'child_cat_ids', 'imp'];

    protected $table = 'main_categories';
    use HasFactory, HasFullSlug;

    protected $fillable = [
        'category_name',
        'slug',
        'canonical_link',
        'seo_emp_id',
        'meta_title',
        'primary_keyword',
        'h1_tag',
        'tag_line',
        'meta_desc',
        'short_desc',
        'h2_tag',
        'long_desc',
        'category_thumb',
        'mockup',
        'banner',
        'contents',
        'faqs',
        'top_keywords',
        'parent_category_id',
        'sequence_number',
        'status',
        'emp_id',
        'fldr_str',
        'total_templates'
    ];

    protected $casts = [
        'top_keywords' => 'array',
    ];


    public function videoTemplates()
    {
        return $this->hasMany(VideoTemplate::class, 'category_id', 'id');
    }

    public function subcategories()
    {
        return $this->hasMany(VideoCategory::class, 'parent_category_id', 'id');
    }

    public function parentCategory()
    {
        return $this->belongsTo(VideoCategory::class, 'parent_category_id', 'id');
    }

    public function virtualPages(): HasMany
    {
        return $this->hasMany(VideoVirtualCategory::class, 'parent_category_id', 'id');
    }

    /**
     * Root main_categories id for size/theme filters (parity with NewCategory::getRootParentId).
     */
    public function getRootParentId(): int
    {
        $category = $this;
        while ($category->parentCategory && (int) $category->parentCategory->parent_category_id !== 0) {
            $category = $category->parentCategory;
        }

        return (int) $category->parent_category_id;
    }

    public static function getAllCategoriesWithSubcategories()
    {
        $categories = VideoCategory::where('parent_category_id', 0)->get();
        foreach ($categories as $category) {
            $category->subcategories = $category->getSubcategoriesTree();
        }
        return $categories;
    }


    public static function getCategoriesWithSubcategories($category)
    {
        $categories = VideoCategory::where('id', $category)->get();

        if (empty($categories->toArray())) {
            $categories = VideoCategory::where('slug', $category)->get();
        }

        foreach ($categories as $category) {
            $category->subcategories = $category->getSubcategoriesTree();
        }
        return $categories;
    }

    protected function getSubcategoriesTree()
    {
        $subcategories = $this->subcategories;
        foreach ($subcategories as $subcategory) {
            $subcategory->subcategories = $subcategory->getSubcategoriesTree();
        }
        return $subcategories;
    }

    protected static function booted()
    {
        static::created(function (VideoCategory $category) {
            self::generateStringId($category->id, $category->string_id);
            VideoSlugHistory::updateVideoSlug(id: $category->id, slug: $category->slug, type: "category");
            self::clearCategoryCache($category);
        });

        static::updated(function (VideoCategory $category) {
            self::generateStringId($category->id, $category->string_id);
            VideoSlugHistory::updateVideoSlug(id: $category->id, slug: $category->slug, type: "category", update: true);
            $oldValues = $category->getOriginal();
            $newValues = $category->getAttributes();
            $oldParentId = $oldValues['parent_category_id'];
            $newParentId = $newValues['parent_category_id'];
            self::clearCategoryCache($category);

            // parent_category_id changed - full hierarchy update
            if ($oldParentId != $newParentId) {
                if ($oldParentId != 0) {
                    self::updateHierarchyFromRoot($oldParentId);
                }
                if ($newParentId != 0) {
                    self::updateHierarchyFromRoot($newParentId);
                } else {
                    self::updateHierarchyFromRoot($category->id);
                }
            } else {
                self::updateHierarchyFromRoot($newParentId == null || $newParentId == 0 ? $category->id : $newParentId);
            }
        });
    }

    private static function generateStringId($catId, $stringID): void
    {
        if ($stringID && $stringID != "")
            return;

        $generateStringID = HelperController::generateRandomId(modelSource: VideoCategory::class);
        $category = VideoCategory::whereId($catId)->first();
        $category->string_id = $generateStringID;
        $category->saveQuietly();
    }

    /**
     * Clear cache for a category (all related tags)
     */
    private static function clearCategoryCache($category): void
    {
        Cache::tags(["vi_category_$category->id"])->flush();
        Cache::tags(['vi_category'])->flush();
    }

    /**
     * Get direct children of a category
     */
    private static function getChildren($categoryId): Collection
    {
        return self::where('parent_category_id', $categoryId)->get();
    }

    /**
     * Update entire hierarchy from root when parent changes
     */
    private static function updateHierarchyFromRoot($categoryId): void
    {
        $rootParent = self::findRootParent($categoryId);
        if (!$rootParent)
            return;

        self::updateTemplateCountsFromBottom($rootParent);
        self::clearHierarchyCacheRecursive($rootParent->id);
    }

    /**
     * Find the root parent (where parent_category_id = 0)
     */
    private static function findRootParent($categoryId): ?VideoCategory
    {
        $category = self::find($categoryId);

        while ($category && $category->parent_category_id != 0) {
            $category = self::find($category->parent_category_id);
        }

        return $category;
    }

    /**
     * Update template counts from bottom to top
     */
    private static function updateTemplateCountsFromBottom($category): void
    {
        $directChildren = self::getChildren($category->id);

        // First, recursively update all children
        foreach ($directChildren as $child) {
            self::updateTemplateCountsFromBottom($child);
        }

        // Now update this category's total_templates
        self::updateCategoryTemplateCount($category);
    }

    /**
     * Update total_templates for a single category
     */
    private static function updateCategoryTemplateCount($category): void
    {
        // Get own template count from Design table
        $ownCount = VideoTemplate::where('category_id', $category->id)
            ->whereStatus(1)
            ->count();

        // Get all direct children's template counts
        $directChildren = self::getChildren($category->id);
        $childrenCount = $directChildren->sum('total_templates');

        // Update total_templates
        $totalCount = $ownCount + $childrenCount;
        if ($category->total_templates !== $totalCount) {
            $category->updateQuietly(['total_templates' => $totalCount]);
        }
    }

    /**
     * Clear cache for a category and all its descendants recursively
     */
    private static function clearHierarchyCacheRecursive($categoryId): void
    {
        $category = self::find($categoryId);
        if (!$category)
            return;

        self::clearCategoryCache($category);

        // Clear cache for all direct children
        foreach (self::getChildren($categoryId) as $child) {
            self::clearHierarchyCacheRecursive($child->id);
        }
    }

    /**
     * @return VideoCategory|null Returns an associative array based on `VideoCategory` model with tree data, or null if not found.
     */
    public static function findId(
        ?array $select = [],
        ?int   $isStatus = null,
        ?int   $id = null,
        ?int   $parentId = null,
        ?bool  $getChild = true
    ): ?VideoCategory
    {
        $cacheKey = 'vi_categories_id_' . md5(json_encode([
                'select' => $select,
                'status' => $isStatus,
                'id' => $id,
                'parentId' => $parentId,
                'getChild' => $getChild,
            ]));

        $callback = function () use ($select, $isStatus, $id, $parentId, $getChild) {

            $select = self::resolveSelect($select);

            $query = VideoCategory::query()->when($select, fn($q) => $q->select($select));
            $query->whereId($id);
            if ($parentId !== null) $query->whereParentCategoryId($parentId);
            if ($isStatus !== null) $query->whereStatus($isStatus);
            $query->where('total_templates', '>', 0);

            /** @var VideoCategory $category */
            $category = $query->first();

            if (!$category) return null;
            if (!$getChild) return $category;

            $parentCat = null;
            if ($category->parent_category_id != 0) $parentCat = VideoCategory::find($category->parent_category_id);

            $buildTree = self::getChilds(isStatus: $isStatus, parentCat: $parentCat, select: $select);

            return $buildTree($category);
        };

        if (HelperController::$cacheEnabled) {
            return Cache::tags(["vi_category_$id"])->remember(
                $cacheKey,
                HelperController::$cacheTimeOut,
                $callback
            );
        }

        return $callback();
    }

    /**
     * @return VideoCategory|null Returns an associative array based on `VideoCategory` model with tree data, or null if not found.
     */
    public static function findBySlug(
        ?array  $select = [],
        ?int    $isStatus = null,
        ?string $id = null,
        ?int    $parentId = null,
        ?bool   $getChild = true
    ): ?VideoCategory
    {

        $cacheKey = 'vi_categories_slug_' . md5(json_encode([
                'select' => $select,
                'status' => $isStatus,
                'id' => $id,
                'parentId' => $parentId,
                'getChild' => $getChild,
            ]));

        $callback = function () use ($select, $isStatus, $id, $parentId, $getChild) {
            $select = self::resolveSelect($select);

            $query = VideoCategory::query()->when($select, fn($q) => $q->select($select));
            $query->whereSlug($id);
            if ($parentId !== null) $query->whereParentCategoryId($parentId);
            if ($isStatus !== null) $query->whereStatus($isStatus);
            $query->where('total_templates', '>', 0);

            /** @var VideoCategory $category */
            $category = $query->first();

            if (!$category) return null;
            if (!$getChild) return $category;

            $parentCat = null;
            if ($category->parent_category_id != 0) $parentCat = VideoCategory::find($category->parent_category_id);

            $buildTree = self::getChilds(isStatus: $isStatus, parentCat: $parentCat, select: $select);

            return $buildTree($category);
        };

        if (HelperController::$cacheEnabled) {
            return Cache::tags(["vi_category_$id"])->remember(
                $cacheKey,
                HelperController::$cacheTimeOut,
                $callback
            );
        }

        return $callback();
    }

    public static function getAllCatsWithChilds(
        ?array $select = [],
        ?array $filters = [],
        int    $limit = 10,
        ?int   $page = null): LengthAwarePaginator
    {

        $cacheKey = 'categories_with_childs_' . md5(json_encode([
                'select' => $select,
                'filters' => $filters,
                'limit' => $limit,
                'page' => $page,
            ]));

        $callback = function () use ($select, $filters, $limit, $page) {
            $select = self::resolveSelect($select);

            $query = VideoCategory::query()->when($select, fn($q) => $q->select($select))->where('parent_category_id', 0);
            $query->where('total_templates', '>', 0);
            QueryManager::getQuery($query, $filters);

            $query->orderBy('sequence_number', 'ASC');

            $isPaginated = $page !== null;

            $rootCategories = $isPaginated
                ? $query->paginate($limit, ['*'], 'page', $page)
                : $query->get();

            $parentIds = $isPaginated
                ? $rootCategories->getCollection()->pluck('id')
                : $rootCategories->pluck('id');

            $buildTree = self::getChilds(isStatus: $filters['status'] ?? null, parentIds: $parentIds->toArray(), select: $select);

            if ($isPaginated) {
                $rootCategories->getCollection()->transform($buildTree);
                return $rootCategories;
            }

            $built = $rootCategories->map($buildTree);

            return new LengthAwarePaginator(
                $built,
                $built->count(),
                max($built->count(), 1),
                1,
                ['path' => request()->url(), 'query' => request()->query()]
            );
        };

        if (HelperController::$cacheEnabled) {
            return Cache::tags(["vi_categories"])->remember(
                $cacheKey,
                HelperController::$cacheTimeOut,
                $callback
            );
        }

        return $callback();
    }

    public static function getParentCategories(
        ?array $select = [],
        ?int   $isStatus = null,
        ?int   $isImp = null,
        int    $limit = 10,
        ?int   $page = null): LengthAwarePaginator
    {

        $select = self::resolveSelect($select);

        $query = VideoCategory::query()->when($select, fn($q) => $q->select($select))->where('parent_category_id', 0);

        if ($isStatus !== null) $query->whereStatus($isStatus);
        if ($isImp !== null) $query->whereImp($isImp);

        $query->where('total_templates', '>', 0);
        $query->orderBy('sequence_number', 'ASC');

        $isPaginated = $page !== null;

        $rootCategories = $isPaginated
            ? $query->paginate($limit, ['*'], 'page', $page)
            : $query->get();

        if ($rootCategories instanceof LengthAwarePaginator) {
            return $rootCategories;
        }

        return new LengthAwarePaginator(
            $rootCategories,
            $rootCategories->count(),
            $rootCategories->count(),
            1,
            ['path' => request()->url(), 'query' => request()->query()]
        );

    }

    private static function resolveSelect(?array $select): ?array
    {
        if ($select === null) return self::$defaultCategorySelect;
        if (count($select) === 0) return null;
        return $select;
    }

    /**
     * @param int|null $isStatus
     * @param VideoCategory|null $parentCat
     * @param int[]|null $parentIds
     * @param array|null $select
     * @return Closure
     */

    private static function getChilds(?int $isStatus = null, ?VideoCategory $parentCat = null, ?array $parentIds = null, ?array $select = null): Closure
    {
        $childQuery = VideoCategory::query()->when($select, fn($q) => $q->select($select));

        if ($parentCat !== null) $childQuery->whereParentCategoryId($parentCat->id);
        elseif ($parentIds !== null) $childQuery->whereIn('parent_category_id', $parentIds);

        if ($isStatus !== null) $childQuery->whereStatus($isStatus);

        $childQuery->where('total_templates', '>', 0);

        $childrenGrouped = $childQuery->orderBy('sequence_number')->get()->groupBy('parent_category_id');

        return function ($category) use ($childrenGrouped, $parentCat) {
            $catArr = $category;

            if ($parentCat) {
                $catArr['parent'] = [
                    'id' => $parentCat->id,
                    'category_name' => $parentCat->category_name,
                    'cat_link' => $parentCat->cat_link,
                ];
            }

            $catArr['subcategories'] = ($childrenGrouped[$category->id] ?? collect())->map(function ($child) use ($category, $parentCat) {
                $childArr = $child;
                $childArr['parent'] = [
                    'id' => $category->id,
                    'category_name' => $category->category_name,
                    'cat_link' => $category->cat_link,
                ];


                return $childArr;
            })->values();

            return $catArr;
        };
    }

}
