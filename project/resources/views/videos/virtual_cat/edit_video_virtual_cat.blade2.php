@inject('roleManager', 'App\Http\Controllers\Utils\RoleManager')
@inject('contentManager', '\App\Http\Controllers\Admin\Utils\ContentManager')
@inject('helperController', 'App\Http\Controllers\Utils\HelperController')

<script>
    // Standard global function definition
    function updateCount(input, counterId) {
        if (!input) return;
        var max = parseInt(input.getAttribute('maxlength')) || 60;
        var currentLength = input.value ? input.value.length : 0;
        var remaining = max - currentLength;
        var counterElement = document.getElementById(counterId);
        if (counterElement) {
            counterElement.textContent = remaining + ' remaining of ' + max + ' characters';
            // console.log('Counter Updated:', counterId, remaining);
        }
    }
</script>
@include('layouts.masterhead')
<div class="main-container seo-all-container">

    <div class="pd-ltr-20 xs-pd-20-10">
        <div class="min-height-200px">
            @include('partials.density_checker', [
                'title' => 'Video Virtual Category Page',
                'slug' => $datas['cat']->slug,
                'type' => 1,
                'primary_keyword' => $datas['cat']->primary_keyword,
            ])
            <div class="pd-20 card-box mb-30">
                <form method="post" id="dynamic_form" enctype="multipart/form-data">

                    <span id="result"></span>

                    @csrf

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <h6>Category Name</h6>
                                <input class="form-control" type="textname" name="category_name"
                                    value="{{ $datas['cat']->category_name }}" id="categoryName" required>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group ">
                                <h6>Slug</h6>
                                <input class="form-control" type="text" name="slug" id="slug"
                                       placeholder="Please Enter Slug"
                                       value="{{ $datas['cat']->slug }}"
                                       required>
                            </div>
                        </div>

                        <div class="col-md-4 col-sm-12">
                            <div class="form-group">
                                <h6>Canonical Link</h6>
                                <div class="input-group custom mb-0">
                                    <input type="text" class="form-control canonical_link" name="canonical_link"
                                        value="{{ $datas['cat']->canonical_link }}" />
                                </div>
                                <p class="text-end" style="font-size: 12px;">Only admin or SEO Manager can modify canonical
                                    link</p>
                            </div>
                        </div>
                        @if ($roleManager::isAdminOrSeoManager(Auth::user()->user_type))
                            <div class="col-md-4 col-sm-12">
                                <div class="form-group">
                                    <h6>Assign Sub Categories Tag</h6>
                                    <select class="form-control" id="assignSubCatSelect" name="seo_emp_id">
                                        <option disabled {{ empty($datas['cat']->seo_emp_id) ? 'selected' : '' }}>
                                            Select
                                        </option>
                                        @foreach ($assignSubCat as $subcat)
                                            <option value="{{ $subcat->id }}"
                                                {{ isset($datas['cat']->seo_emp_id) && $datas['cat']->seo_emp_id == $subcat->id ? 'selected' : '' }}>
                                                {{ $subcat->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        @endif

                    </div>
                    @include('videos.partials.sitemap_seo_fields', [
                        'no_index' => $datas['cat']->no_index ?? 1,
                        'priority' => $datas['cat']->priority ?? 0.90,
                        'frequency' => $datas['cat']->frequency ?? 'daily',
                    ])
                    <div class="row">

                        <div class="col-md-6">
                            <div class="form-group">
                                <h6>Meta Title</h6>
                                <input class="form-control" type="text" name="meta_title" id="meta_title"
                                    maxlength="60"
                                    value="{{ $datas['cat']->meta_title }}" required>
                                <small id="metaCounter" class="text-muted"></small>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <h6>Primary Keyword</h6>
                                <input type="text" class="form-control" id="primary_keyword" maxlength="60"
                                    name="primary_keyword" placeholder="Enter Primary Keyword" required
                                    value="{{ $datas['cat']->primary_keyword ?? '' }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <h6>H1 Tag</h6>
                                <input class="form-control" type="text" name="h1_tag" id="h1_tag" maxlength="60"
                                    value="{{ $datas['cat']->h1_tag }}"
                                    required>
                                <small id="h1Counter" class="text-muted"></small>
                            </div>
                        </div>

                        {{-- <div class="col-md-6">
                            <div class="form-group">
                                <h6>H2 Tag</h6>
                                <input class="form-control" type="textname" name="h2_tag"
                                    value="{{ $datas['cat']->h2_tag }}">
                            </div>
                        </div> --}}
                        <div class="col-md-6">
                            <div class="form-group">
                                <h6>Meta Desc</h6>
                                <textarea style="height: 120px" class="form-control" name="meta_desc" id="meta_desc" maxlength="160">{{ $datas['cat']->meta_desc }}</textarea>
                                <small id="metaDescCounter" class="text-muted"></small>
                            </div>
                        </div>


                        <div class="col-md-6">
                            <div class="form-group">
                                <h6>Short Desc</h6>
                                <textarea style="height: 120px" class="form-control" name="short_desc" id="short_desc" maxlength="350">{{ $datas['cat']->short_desc }}</textarea>
                                <small id="shortDescCounter" class="text-muted"></small>
                            </div>
                        </div>




                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <h6>Category Thumb</h6>
                                <input type="file" data-accept=".jpg, .jpeg, .webp, .svg"
                                    class="form-control-file form-control height-auto dynamic-file"
                                    data-value="{{ $contentManager::getStorageLink($datas['cat']->category_thumb) }}"
                                    data-imgstore-id="category_thumb" data-nameset="true" />
                                <br />
                                <!-- <img src="{{ config('filesystems.storage_url') }}{{ $datas['cat']->category_thumb }}" width="100" />
                                <input class="form-control" type="textname" id="cat_thumb_path" name="cat_thumb_path"
                                  value="{{ $datas['cat']->category_thumb }}" style="display: none"> -->
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <h6>Mockup</h6>
                                <input type="file" data-accept=".jpg, .jpeg, .webp, .svg"
                                    class="form-control-file form-control height-auto dynamic-file"
                                    data-imgstore-id="mockup"
                                    data-value="{{ $contentManager::getStorageLink($datas['cat']->mockup) }}"
                                    data-nameset="true" />
                                <br />
                                <!-- <img src="{{ config('filesystems.storage_url') }}{{ $datas['cat']->mockup }}" width="100" />
                                <input class="form-control" type="textname" id="mockup_path" name="mockup_path"
                                  value="{{ $datas['cat']->mockup }}" style="display: none"> -->
                            </div>

                        </div>
                    </div>


                    <div class="form-group">
                        <h6>Parent Category</h6>
                        <select class="form-control form-control-sm video-category-select seo-all-container" name="parent_category_id" required>
                            <option value="">Select Category</option>
                            @foreach ($datas['groupedVideoCategories'] as $group)
                            <optgroup label="{{ $group['parent']->category_name }}">
                                @foreach ($group['children'] as $child)
                                <option value="{{ $child->id }}"
                                    @if ($datas['cat']->parent_category_id == $child->id) selected @endif>
                                    {{ $child->category_name }}
                                </option>
                                @endforeach
                            </optgroup>
                            @endforeach
                        </select>
                    </div>

                    @include('partials.content_section', [
                        'contents' =>  $datas['cat']->contents ?? old('contents'),
                        'ctaSection' => [],
                    ])
                    <div style="margin-bottom: 10px;">
                        @include('partials.faqs_section', ['faqs' => $datas['cat']->faqs ?? ''])
                    </div>

                    <div class="form-group">
                        <h6>Sequence Number</h6>
                        <input class="form-control" type="textname" name="sequence_number"
                            value="{{ $datas['cat']->sequence_number }}" required>
                    </div>
                    <div class="form-group">
                        <h6>Status</h6>
                        <div class="col-sm-20">
                            <select class="selectpicker form-control status" data-style="btn-outline-primary"
                                name="status">
                                @if ($datas['cat']->status == '1')
                                    <option value="1" selected>LIVE</option>
                                    <option value="0">NOT LIVE</option>
                                @else
                                    <option value="1">LIVE</option>
                                    <option value="0" selected>NOT LIVE</option>
                                @endif
                            </select>
                        </div>
                    </div>

                    {{-- Add hidden field for generatedQuery with empty value for video virtual categories --}}
                    <input type="hidden" name="generatedQuery" value="">

                    <div>
                        <input class="btn btn-primary submit-btn" type="submit" name="submit">
                    </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @include('layouts.masterscript')

    <script>
        // Initialize character counters and attach event listeners with safety checks
        (function() {
            var fields = [
                { id: 'meta_title', counter: 'metaCounter' },
                { id: 'h1_tag', counter: 'h1Counter' },
                { id: 'meta_desc', counter: 'metaDescCounter' },
                { id: 'short_desc', counter: 'shortDescCounter' }
            ];

            function initAllCounters() {
                if (typeof fields === 'undefined') return;
                fields.forEach(function(field) {
                    var el = document.getElementById(field.id);
                    if (el && typeof updateCount === 'function') {
                        updateCount(el, field.counter);
                        if (!el.dataset.counterAttached) {
                            ['input', 'keyup', 'paste', 'change'].forEach(function(evt) {
                                el.addEventListener(evt, function() {
                                    updateCount(el, field.counter);
                                });
                            });
                            el.dataset.counterAttached = "true";
                        }
                    }
                });
            }

            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', initAllCounters);
            } else {
                initAllCounters();
            }
            
            if (typeof jQuery !== 'undefined') {
                jQuery(document).ready(initAllCounters);
            }
            window.addEventListener('load', initAllCounters);
            setTimeout(initAllCounters, 100);
            setTimeout(initAllCounters, 500);
            setTimeout(initAllCounters, 1000);
        })();

        // Initialize Quill editor for More Template CTA modal
        if (typeof ctaMoreTemplateQuill === 'undefined') {
            var ctaMoreTemplateQuill = null;
        }

        if (typeof jQuery !== 'undefined') {
            jQuery('#api_virtual_modal').on('shown.bs.modal', function () {
                if (!ctaMoreTemplateQuill) {
                    var ctaMoreTemplateDesc = document.getElementById('ctaMoreTemplateDesc');
                    if (ctaMoreTemplateDesc && typeof Quill !== 'undefined') {
                        ctaMoreTemplateQuill = new Quill('#ctaMoreTemplateDesc', {
                            theme: 'snow',
                            modules: {
                                toolbar: [
                                    ['bold', 'italic', 'underline'],
                                    [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                                    ['link'],
                                    ['clean']
                                ]
                            }
                        });
                    }
                }
            });

            // Category & Subcategory selection handlers
            jQuery(document).on('click', '#parentCategoryInput', function() {
                jQuery('.parent-category-input').toggleClass('show');
            });

            jQuery(document).on("click", ".category", function(event) {
                jQuery(".category, .subcategory").removeClass("selected");
                var id = jQuery(this).data('id');
                jQuery("input[name='parent_category_id']").val(id);
                jQuery("#parentCategoryInput span").html(jQuery(this).data('catname'));
                jQuery('.parent-category-input').removeClass('show');
                jQuery(this).addClass("selected");
            });

            jQuery(document).on("click", ".subcategory", function(event) {
                event.stopPropagation();
                jQuery(".category, .subcategory").removeClass("selected");
                var id = jQuery(this).data('id');
                jQuery("input[name='parent_category_id']").val(id);
                jQuery('.parent-category-input').removeClass('show');
                jQuery("#parentCategoryInput span").html(jQuery(this).data('catname'));
                jQuery(this).addClass("selected");
            });

            jQuery(document).on("click", "li.category.none-option", function() {
                jQuery("input[name='parent_category_id']").val("0");
                jQuery('.parent-category-input').removeClass('show');
                jQuery("#parentCategoryInput span").html('== none ==');
            });

            jQuery(document).on('click', function(e) {
                if (typeof jQuery !== 'undefined' && !jQuery(e.target).closest('.form-group.category-dropbox-wrap').length) {
                    jQuery('.custom-dropdown.parent-category-input.show').removeClass('show');
                }
            });
        }
    </script>

    <script>
        // Define columns and sorting with existence checks
        if (typeof columns === 'undefined') {
            var columns = @json(config('videovirtualcolumns.columns', []));
        }
        if (typeof sorting === 'undefined') {
            var sorting = @json(config('videovirtualcolumns.sorting', []));
        }
    </script>

    <script src="{{ asset('assets/js/video_virtual.js') }}?v={{ time() }}"></script>

    <script>
        // Initialize virtual container query if it exists
        if (typeof jQuery !== 'undefined') {
            jQuery(document).ready(function() {
                var virtualcontainer = document.getElementById("virtualcontainer");
                if (virtualcontainer) {
                    try {
                        var virtualInputElem = virtualcontainer.querySelector("#virtualConditionQuery");
                        if (virtualInputElem && virtualInputElem.value) {
                            var virtualCondition = JSON.parse(decodeHTMLEntities(virtualInputElem.value));
                            virtualCondition.forEach(function(condition) {
                                if (typeof setValueInTable === 'function') {
                                    setValueInTable(
                                        condition.column,
                                        condition.columnName,
                                        condition.operator,
                                        condition.value,
                                        condition.secondValue,
                                        null,
                                        virtualcontainer
                                    );
                                }
                            });
                        }
                    } catch (e) {
                        console.error("Error parsing virtual condition query:", e);
                    }

                    var saveBtn = virtualcontainer.querySelector(".save-condition");
                    if (saveBtn) {
                        saveBtn.addEventListener("click", function(event) {
                            if (typeof saveCondition === 'function') saveCondition(event, virtualcontainer);
                        });
                    }

                    var addSortingBtn = virtualcontainer.querySelector(".add-sorting");
                    if (addSortingBtn) {
                        addSortingBtn.addEventListener("click", function(event) {
                            if (typeof addSorting === 'function') addSorting(event, virtualcontainer);
                        });
                    }
                }
            });

            // Form submission handler
            jQuery('#dynamic_form').on('submit', function(event) {
                event.preventDefault();
                jQuery.ajaxSetup({
                    headers: { 'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content') }
                });

                var formData = new FormData(this);
                formData.append('id', "{{ $datas['cat']->id }}");
                var url = "{{ url('update_video_virtual_cat', $datas['cat']->id) }}";

                jQuery.ajax({
                    url: url,
                    type: 'POST',
                    data: formData,
                    beforeSend: function() {
                        jQuery('#main_loading_screen').show();
                    },
                    success: function(data) {
                        jQuery('#main_loading_screen').hide();
                        window.alert(data.error || data.success);
                        setTimeout(function() { jQuery('#result').html(''); }, 3000);
                    },
                    error: function(error) {
                        jQuery('#main_loading_screen').hide();
                        window.alert(error.responseText);
                    },
                    cache: false,
                    contentType: false,
                    processData: false
                });
            });

            // Category Name to Slug auto-generation
            (function() {
                var toTitleCase = function(str) { 
                    return str.replace(/\b\w+/g, function(txt) { 
                        return txt.charAt(0).toUpperCase() + txt.substr(1).toLowerCase(); 
                    });
                };
                jQuery("#categoryName").off("input").on("input", function() {
                    var titleString = toTitleCase(jQuery(this).val());
                    var slugBase = titleString.toLowerCase().replace(/\s+/g, '-');
                    jQuery("#slug").val(slugBase);
                    jQuery(this).val(titleString);
                });
            })();
        }
    </script>
</body>
</html>
