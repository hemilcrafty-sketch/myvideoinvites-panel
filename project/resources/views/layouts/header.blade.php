@inject('roleManager', 'App\Http\Controllers\Utils\RoleManager')
@guest
@else
    <style>
        /* Custom styles for the sidebar */
        .selectSubMenu {
            color: #fff;
            background-color: rgb(169 169 169 / 40%);
        }

        .left-side-bar.open {
            left: -281px;
        }
    </style>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet" />
    <div class="header">
        <div class="header-left">
            <div class="menu-icon dw dw-menu" style="font-size: 20px;"></div>
        </div>
        <div class="header-right">
            <div class="user-info">
                {{ Auth::user()->name }}
                &nbsp;&nbsp;&nbsp;
                <a href="{{ route('logout') }}"
                    onclick="event.preventDefault();document.getElementById('logout-form').submit();">
                    <i class="dw dw-logout"></i> {{ __('Log Out') }}
                </a>
            </div>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                @csrf
            </form>
        </div>
    </div>

@endguest
<div class="left-side-bar">
    <div class="brand-logo">
        <a href="{{ route('dashboard') }}">
            <img src="{{ asset('assets/vendors/images/deskapp-logo.svg') }}" alt="" class="dark-logo">
            <img src="{{ asset('assets/vendors/images/deskapp-logo-white.svg') }}" alt="" class="light-logo">
        </a>
        <div class="close-sidebar" data-toggle="left-sidebar-close">
            <i class="ion-close-round"></i>
        </div>
    </div>
    <div class="menu-block customscroll">
        <div class="sidebar-menu">
            <ul id="accordion-menu">
                @if (!$roleManager::isSalesManager(Auth::user()->user_type) && !$roleManager::isSalesEmployee(Auth::user()->user_type))
                    @if ($roleManager::isSeoManager(Auth::user()->user_type))
                        <li class="dropdown">
                            <a href="{{ route('dashboard', 1) }}"
                                class="dropdown-toggle no-arrow {{ Route::currentRouteName() == 'dashboard' && Request::segment(2) == '1' ? 'selectSubMenu' : '' }}">
                                <span class="micon"><img src="{{ asset('assets/vendors/images/menu_icon/Dashboard.svg') }}"
                                        alt=""></span><span class="mtext">All Data</span>
                            </a>
                        </li>
                    @elseif ($roleManager::isDesignerManager(Auth::user()->user_type))
                        <li class="dropdown {{ Route::currentRouteName() == 'dashboard' ? 'show' : '' }}">
                            <a href="javascript:;" class="dropdown-toggle">
                                <span class="micon"><img src="{{ asset('assets/vendors/images/menu_icon/Dashboard.svg') }}"
                                        alt=""></span><span class="mtext">Dashboard</span>
                            </a>
                            <ul class="submenu"
                                style="display: {{ Route::currentRouteName() == 'dashboard' ? 'block' : 'none' }};">
                                <li>
                                    <a href="{{ route('dashboard', 1) }}"
                                        class="{{ Route::currentRouteName() == 'dashboard' && Request::segment(2) == '1' ? 'selectSubMenu' : '' }}">All
                                        Data</a>
                                </li>
                                <li>
                                    <a href="{{ route('dashboard') }}"
                                        class="{{ Route::currentRouteName() == 'dashboard' && Request::segment(2) != '1' ? 'selectSubMenu' : '' }}">Your
                                        Data</a>
                                </li>
                            </ul>
                        </li>
                    @elseif ($roleManager::isSeoExecutive(Auth::user()->user_type))
                        <li class="dropdown">
                            <a href="{{ route('dashboard') }}"
                                class="dropdown-toggle no-arrow {{ Route::currentRouteName() == 'dashboard' ? 'selectSubMenu' : '' }}">
                                <span class="micon"><img src="{{ asset('assets/vendors/images/menu_icon/Dashboard.svg') }}"
                                        alt=""></span><span class="mtext">Your Data</span>
                            </a>
                        </li>
                    @else
                        <li class="dropdown">
                            <a href="{{ route('dashboard') }}"
                                class="dropdown-toggle no-arrow {{ Route::currentRouteName() == 'dashboard' ? 'selectSubMenu' : '' }}">
                                <span class="micon"><img src="{{ asset('assets/vendors/images/menu_icon/Dashboard.svg') }}"
                                        alt=""></span><span class="mtext">Dashboard</span>
                            </a>
                        </li>
                    @endif
                @endif

                @if (!$roleManager::isSalesManager(Auth::user()->user_type) && !$roleManager::isSalesEmployee(Auth::user()->user_type))
                                <li class="dropdown {{ in_array(Route::currentRouteName(), [
                        'show_v_cat',
                        'show_v_item',
                        'create_v_cat',
                        'edit_v_cat',
                        'edit_seo_v_item',
                        'video_reviews.index',
                        'video_page_reviews.index',
                        'show_video_virtual_cat',
                        'create_video_virtual_cat',
                        'edit_video_virtual_cat',
                        'show_video_style',
                        'show_video_theme',
                        'show_video_search_tag',
                        'show_video_interest',
                        'show_video_lang',
                        'video_religions.index',
                        'video_sizes.index',
                        'video_sizes.create',
                        'video_sizes.edit',
                    ])
                        ? 'show'
                        : '' }}">
                                    <a href="javascript:;" class="dropdown-toggle">
                                        <span class="micon"><img src="{{ asset('assets/vendors/images/menu_icon/Video.svg') }}"
                                                alt=""></span>
                                        <span class="mtext">Video</span>
                                    </a>
                                    <ul class="submenu" style="display: {{ in_array(Route::currentRouteName(), [
                        'show_v_cat',
                        'show_v_item',
                        'create_v_cat',
                        'edit_v_cat',
                        'edit_seo_v_item',
                        'video_reviews.index',
                        'video_page_reviews.index',
                        'show_video_virtual_cat',
                        'create_video_virtual_cat',
                        'edit_video_virtual_cat',
                        'show_video_style',
                        'show_video_theme',
                        'show_video_search_tag',
                        'show_video_interest',
                        'show_video_lang',
                        'video_religions.index',
                        'video_sizes.index',
                        'video_sizes.create',
                        'video_sizes.edit',
                    ])
                        ? 'block'
                        : 'none' }};">
                                        <li><a href="{{ route('show_v_cat') }}"
                                                class="{{ Route::currentRouteName() == 'show_v_cat' || Route::currentRouteName() == 'create_v_cat' || Route::currentRouteName() == 'edit_v_cat' ? 'selectSubMenu' : '' }}">Categories</a>
                                        </li>
                                        <li><a href="{{ route('show_video_virtual_cat') }}"
                                                class="{{ Route::currentRouteName() == 'show_video_virtual_cat' || Route::currentRouteName() == 'create_video_virtual_cat' || Route::currentRouteName() == 'edit_video_virtual_cat' ? 'selectSubMenu' : '' }}">Virtual
                                                Categories</a>
                                        </li>
                                        <li><a href="{{ route('show_v_item') }}"
                                                class="{{ Route::currentRouteName() == 'show_v_item' || Route::currentRouteName() == 'edit_seo_v_item' ? 'selectSubMenu' : '' }}">Templates</a>
                                        </li>

                                        {{-- 🔹 Video Filters --}}
                                        <li class="dropdown {{ in_array(Route::currentRouteName(), [
                        'show_video_style',
                        'show_video_theme',
                        'show_video_search_tag',
                        'show_video_interest',
                        'show_video_lang',
                        'video_religions.index',
                        'video_sizes.index',
                        'video_sizes.create',
                        'video_sizes.edit',
                    ])
                        ? 'show'
                        : '' }}">
                                            <a href="javascript:;" class="dropdown-toggle">
                                                <span class="micon">
                                                    <img src="{{ asset('assets/vendors/images/menu_icon/Filter.svg') }}" alt="">
                                                </span>
                                                <span class="mtext">Filters</span>
                                            </a>
                                            <ul class="submenu" style="display:{{ in_array(Route::currentRouteName(), [
                        'show_video_style',
                        'show_video_theme',
                        'show_video_search_tag',
                        'show_video_interest',
                        'show_video_lang',
                        'video_religions.index',
                        'video_sizes.index',
                        'video_sizes.create',
                        'video_sizes.edit',
                    ])
                        ? 'block'
                        : 'none' }};">
                                                {{-- <li><a href="{{ route('show_video_style') }}"
                                                        class="{{ Route::currentRouteName() == 'show_video_style' ? 'selectSubMenu' : '' }}">Style</a>
                                                </li> --}}
                                                <li><a href="{{ route('show_video_theme') }}"
                                                        class="{{ Route::currentRouteName() == 'show_video_theme' ? 'selectSubMenu' : '' }}">Theme</a>
                                                </li>
                                                <li><a href="{{ route('show_video_search_tag') }}"
                                                        class="{{ Route::currentRouteName() == 'show_video_search_tag' ? 'selectSubMenu' : '' }}">Search
                                                        Tags</a>
                                                </li>
                                                {{-- <li><a href="{{ route('show_video_interest') }}"
                                                        class="{{ Route::currentRouteName() == 'show_video_interest' ? 'selectSubMenu' : '' }}">Interest</a>
                                                </li> --}}
                                                <li><a href="{{ route('show_video_lang') }}"
                                                        class="{{ Route::currentRouteName() == 'show_video_lang' ? 'selectSubMenu' : '' }}">Languages</a>
                                                </li>
                                                <li><a href="{{ route('video_religions.index') }}"
                                                        class="{{ Route::currentRouteName() == 'video_religions.index' ? 'selectSubMenu' : '' }}">Religion</a>
                                                </li>
                                                <li><a href="{{ route('video_sizes.index') }}"
                                                        class="{{ in_array(Route::currentRouteName(), ['video_sizes.index', 'video_sizes.create', 'video_sizes.edit']) ? 'selectSubMenu' : '' }}">Size</a>
                                                </li>
                                            </ul>
                                        </li>
                                        @if ($roleManager::isAdmin(Auth::user()->user_type) || $roleManager::isSeoManager(Auth::user()->user_type) || $roleManager::isManager(Auth::user()->user_type))
                                            <li><a href="{{ route('video_page_reviews.index') }}"
                                                    class="{{ Route::currentRouteName() == 'video_page_reviews.index' ? 'selectSubMenu' : '' }}">Page
                                                    Review</a>
                                            </li>
                                            <li><a href="{{ route('video_reviews.index') }}"
                                                    class="{{ Route::currentRouteName() == 'video_reviews.index' ? 'selectSubMenu' : '' }}">Reviews</a>
                                            </li>
                                        @endif
                                    </ul>
                                </li>
                @endif

                <li
                    class="dropdown {{ in_array(Route::currentRouteName(), ['show_pending_item', 'rejected_task']) ? 'show' : '' }}">
                    <a href="javascript:;" class="dropdown-toggle">
                        <span class="micon bi bi-list-task"></span><span class="mtext">Pending Task</span>
                    </a>
                    <ul class="submenu"
                        style="display: {{ in_array(Route::currentRouteName(), ['show_pending_item', 'rejected_task']) ? 'block' : 'none' }};">
                        <li><a href="{{ route('show_pending_item') }}"
                                class="{{ Route::currentRouteName() == 'show_pending_item' ? 'selectSubMenu' : '' }}">Pending
                                Task</a></li>
                        <li><a href="{{ route('rejected_task') }}"
                                class="{{ Route::currentRouteName() == 'rejected_task' ? 'selectSubMenu' : '' }}">Rejected
                                Tasks</a></li>
                    </ul>
                </li>
                @if ($roleManager::isAdminOrSeoManager(Auth::user()->user_type))
                    <li>
                        <a href="{{ route('show_employee') }}"
                            class="dropdown-toggle no-arrow {{ Route::currentRouteName() == 'show_employee' ? 'selectSubMenu' : '' }}">
                            <span class="micon"><img src="{{ asset('assets/vendors/images/menu_icon/Employee.svg') }}"
                                    alt=""></span><span class="mtext">Employees</span>
                        </a>
                    </li>
                @endif
                @if ($roleManager::isAdmin(Auth::user()->user_type))
                    <li>
                        <a href="{{ route('templateRate.index') }}"
                            class="dropdown-toggle no-arrow {{ Route::currentRouteName() == 'templateRate.index' ? 'selectSubMenu' : '' }}">
                            <span class="micon"><img src="{{ asset('assets/vendors/images/menu_icon/Review.svg') }}"
                                    alt=""></span><span class="mtext">Template Rate</span>
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('payment_configuration.index') }}"
                            class="dropdown-toggle no-arrow {{ Route::currentRouteName() == 'payment_configuration.index' ? 'selectSubMenu' : '' }}">
                            <span class="micon bi bi-calendar4-week"></span><span class="mtext">Paymeny Configuration</span>
                        </a>
                    </li>
                        <li>
                            <a href="{{ route('promocode.index') }}"
                                class="dropdown-toggle no-arrow {{ Route::currentRouteName() == 'promocode.index' ? 'selectSubMenu' : '' }}">
                                <span class="micon"><img src="{{ asset('assets/vendors/images/menu_icon/Dashboard.svg') }}"
                                        alt=""></span><span class="mtext">Promo Code</span>
                            </a>
                        </li>
                @endif

            </ul>
        </div>
    </div>
</div>
<div class="mobile-menu-overlay"></div>
<script>
    let roleKey = @json($roleManager::getUserType(Auth::user()->user_type));
    let isPreviewMode = @json($previewMode ?? false);
    let storageUrl = @json(config('filesystems.storage_url'));
    let APP_BASE_URL = @json(env('APP_URL'));
    let STORAGE_URL = @json(env('STORAGE_URL'));
</script>