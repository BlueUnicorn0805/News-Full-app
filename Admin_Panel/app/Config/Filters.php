<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class Filters extends BaseConfig
{

    // Makes reading things below nicer,
    // and simpler to change out script that's used.
    public $aliases = [
        'csrf'     => \CodeIgniter\Filters\CSRF::class,
        'toolbar'  => \CodeIgniter\Filters\DebugToolbar::class,
        'honeypot' => \CodeIgniter\Filters\Honeypot::class,
    ];
    // Always applied before every request
    public $globals = [
        'before' => [
            //'honeypot'
            'csrf' => [
                'except' => [
                    'checkOldPass',
                    'upload_img',
                    'upload_pages_img',
                    'Api.*+',
                    'Table.*+',
                    'delete_breaking_news',
                    'delete_category',
                    'delete_subcategory',
                    'get_subcategory_by_category',
                    'delete_tag',
                    'delete_user_roles',
                    'delete_comment',
                    'delete_comment_flag',
                    'delete_notification',
                    'delete_news',
                    'delete_news_image',
                    'delete_live_streaming',
                    'delete_question',
                    'delete_option',
                    'delete_language',
                    'get_categories_of_language',
                    'delete_pages',
                    'store_default_language',
                    'clone_news',
                    'get_categories_tree',
                    'get_custom_news',
                    'delete_featured_sections',
                    'update_featured_sections_order',
                    'get_category_by_language',
                    'get_tag_by_language',
                    'get_featured_sections_by_language',
                    'delete_ad_spaces',
                    'check_email'

                ]
            ]
        ],
        'after'  => [
            'toolbar',
            //'honeypot'
        ],
    ];
    // Works on all of a particular HTTP method
    // (GET, POST, etc) as BEFORE filters only
    //     like: 'post' => ['CSRF', 'throttle'],
    public $methods = [];
    // List filter aliases and any before/after uri patterns
    // that they should run on, like:
    //    'isLoggedIn' => ['before' => ['account/*', 'profiles/*']],
    public $filters = [];

}