<?php

return [
    'title' => '',
    'title_prefix' => '',
    'title_postfix' => '| Web Jugería',
    'use_ico_only' => true,
    'use_full_favicon' => false,
    'locale' => 'es',

    'google_fonts' => [
        'allowed' => true,
    ],

    'logo' => '<b>Juguería</b>WEB',
    'logo_img' => 'vendor/adminlte/dist/img/merakifruit.jpeg',
    'logo_img_class' => 'brand-image',
    'logo_img_xl' => null,
    'logo_img_xl_class' => '',
    'logo_img_alt' => 'Logo',

    'auth_logo' => [
        'enabled' => false,
        'img' => [
            'path' => 'vendor/adminlte/dist/img/merakifruit.jpeg',
            'alt' => 'Logo de autenticación',
            'class' => '',
            'width' => 50,
            'height' => 50,
        ],
    ],

    'preloader' => [
        'enabled' => true,
        'mode' => 'fullscreen',
        'img' => [
            'path' => 'vendor/adminlte/dist/img/merakifruit.jpeg',
            'alt' => 'Imagen de pre-carga de AdminLTE',
            'effect' => 'animation__shake',
            'width' => 50,
            'height' => 50,
        ],
    ],

    'usermenu_enabled' => true,
    'usermenu_header' => false,
    'usermenu_header_class' => 'bg-primary',
    'usermenu_image' => false,
    'usermenu_desc' => false,
    'usermenu_profile_url' => false,

    'layout_topnav' => null,
    'layout_boxed' => null,
    'layout_fixed_sidebar' => true,
    'layout_fixed_navbar' => null,
    'layout_fixed_footer' => null,
    'layout_dark_mode' => null,

    'classes_auth_card' => 'card-outline card-primary',
    'classes_auth_header' => '',
    'classes_auth_body' => '',
    'classes_auth_footer' => '',
    'classes_auth_icon' => '',
    'classes_auth_btn' => 'btn-flat btn-primary',

    'classes_body' => '',
    'classes_brand' => '',
    'classes_brand_text' => '',
    'classes_content_wrapper' => '',
    'classes_content_header' => '',
    'classes_content' => '',

    'classes_sidebar' => 'sidebar-light-primary elevation-4',

    'classes_sidebar_nav' => '',
    'classes_topnav' => 'navbar-white navbar-light',
    'classes_topnav_nav' => 'navbar-expand',
    'classes_topnav_container' => 'container',

    'sidebar_mini' => 'lg',
    'sidebar_collapse' => false,
    'sidebar_collapse_auto_size' => false,
    'sidebar_collapse_remember' => false,
    'sidebar_collapse_remember_no_transition' => true,
    'sidebar_scrollbar_theme' => 'os-theme-light',
    'sidebar_scrollbar_auto_hide' => 'l',
    'sidebar_nav_accordion' => true,
    'sidebar_nav_animation_speed' => 300,

    'right_sidebar' => false,
    'right_sidebar_icon' => 'fas fa-cogs',
    'right_sidebar_theme' => 'dark',
    'right_sidebar_slide' => true,
    'right_sidebar_push' => true,
    'right_sidebar_scrollbar_theme' => 'os-theme-light',
    'right_sidebar_scrollbar_auto_hide' => 'l',

    'use_route_url' => false,
    'dashboard_url' => '/',
    'logout_url' => 'logout',
    'login_url' => 'login',
    'register_url' => 'register',
    'password_reset_url' => 'password/reset',
    'password_email_url' => 'password/email',
    'profile_url' => false,
    'disable_darkmode_routes' => false,

    'laravel_asset_bundling' => false,
    'laravel_css_path' => 'css/app.css',
    'laravel_js_path' => 'js/app.js',

    'menu' => [
        // Elementos de la barra de navegación:
        [
            'type' => 'navbar-search',
            'text' => 'buscar',
            'topnav_right' => true,
        ],
        [
            'type' => 'fullscreen-widget',
            'topnav_right' => true,
        ],

        // Elementos del menú lateral:
        [
            'type' => 'sidebar-menu-search',
            'text' => 'buscar',
        ],
        [
            'text' => 'blog',
            'url' => 'admin/blog',
            'can' => 'manage-blog',
        ],
        [
            'text' => 'Dashboard',
            'route' => 'admin.home',
            'icon' => 'fab fa-fw fa-buffer',
            'can'   => 'admin.home',
        ],

        /* Accesos */
        [
            'text' => 'Accesos',
            'route' => 'admin.users.index',
            'icon' => 'fas fa-users',
            'can'   => 'admin.users.index',
        ],
        [
            'text' => 'Mesas',
            'icon' => 'fas fa-chair',
            'route' => 'admin.mesas.index',
            'can'   => 'admin.mesas.index',
        ],
        [
            'text' => 'Pedidos',
            'icon' => 'fas fa-shopping-cart',
            'route' => 'admin.nuevospedidosadmin.index',
            'can'   => 'admin.pedidos.index',
        ],
        [
            'text' => 'Ventas',
            'icon' => 'fas fa-shopping-cart',
            'route' => 'admin.ventas.index',
            'can'   => 'admin.ventas.index',
        ],
        [
            'text' => 'Categorias',
            'route' => 'admin.categoria.index',
            'icon' => 'fas fa-tags',
            'can'   => 'admin.categoria.index',
        ],
        [
            'text' => 'Producto',
            'icon' => 'fas fa-box',
            'route' => 'admin.producto.index',
            'can'   => 'admin.productos.index',
        ],

        [
            'text' => 'Empresa',
            'route' => 'admin.empresa.index',
            'icon' => 'fas fa-bars',
            'can'   => 'admin.empresa.index',
        ],
        [
            'text' => 'Paginas',
            'route' => 'admin.paginas.index',
            'icon' => 'fas fa-file-alt',
            'can'   => 'admin.paginas.index',
        ],

        [
            'text' => 'Reportes',
            'icon' => 'fas fa-chart-line',
            'route' => 'admin.reportes.index',
            'can'   => 'admin.reportes.index',
        ],
    ],

    'filters' => [
        JeroenNoten\LaravelAdminLte\Menu\Filters\GateFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\HrefFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\SearchFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\ActiveFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\ClassesFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\LangFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\DataFilter::class,
    ],

    'plugins' => [
        'Datatables' => [
            'active' => false,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js',
                ],
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//cdn.datatables.net/1.10.19/js/dataTables.bootstrap4.min.js',
                ],
                [
                    'type' => 'css',
                    'asset' => false,
                    'location' => '//cdn.datatables.net/1.10.19/css/dataTables.bootstrap4.min.css',
                ],
            ],
        ],
        'Select2' => [
            'active' => false,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js',
                ],
                [
                    'type' => 'css',
                    'asset' => false,
                    'location' => '//cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.css',
                ],
            ],
        ],
        'Chartjs' => [
            'active' => false,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.0/Chart.bundle.min.js',
                ],
            ],
        ],
        'Sweetalert2' => [
            'active' => false,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//cdn.jsdelivr.net/npm/sweetalert2@8',
                ],
            ],
        ],
        'Pace' => [
            'active' => false,
            'files' => [
                [
                    'type' => 'css',
                    'asset' => false,
                    'location' => '//cdnjs.cloudflare.com/ajax/libs/pace/1.0.2/themes/blue/pace-theme-center-radar.min.css',
                ],
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//cdnjs.cloudflare.com/ajax/libs/pace/1.0.2/pace.min.js',
                ],
            ],
        ],
    ],

    'iframe' => [
        'default_tab' => [
            'url' => null,
            'title' => null,
        ],
        'buttons' => [
            'close' => true,
            'close_all' => true,
            'close_all_other' => true,
            'scroll_left' => true,
            'scroll_right' => true,
            'fullscreen' => true,
        ],
        'options' => [
            'loading_screen' => 1000,
            'auto_show_new_tab' => true,
            'use_navbar_items' => true,
        ],
    ],

    'livewire' => true,
];
