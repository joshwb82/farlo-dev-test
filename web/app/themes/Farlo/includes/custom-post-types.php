<?php

declare(strict_types=1);

// Farlo Shows
add_action('init', 'farlo_shows');

function farlo_shows()
{
    $labels = [
        'name' => _x('Shows', 'Post Type General Name', 'text_domain'),
        'singular_name' => _x('Show', 'Post Type Singular Name', 'text_domain'),
        'menu_name' => __('Shows', 'text_domain'),
        'name_admin_bar' => __('Shows', 'text_domain'),
        'parent_item_colon' => __('Shows:', 'text_domain'),
        'all_items' => __('All Shows', 'text_domain'),
        'add_new_item' => __('Add New Show', 'text_domain'),
        'add_new' => __('Add New', 'text_domain'),
        'new_item' => __('New Show', 'text_domain'),
        'edit_item' => __('Edit Show', 'text_domain'),
        'update_item' => __('Update Show', 'text_domain'),
        'view_item' => __('View Show', 'text_domain'),
        'search_items' => __('Search Shows', 'text_domain'),
        'not_found' => __('Not found', 'text_domain'),
        'not_found_in_trash' => __('Not found in Trash', 'text_domain'),
        'items_list' => __('Show list', 'text_domain'),
        'items_list_navigation' => __('Show list navigation', 'text_domain'),
        'filter_items_list' => __('Filter Shows', 'text_domain'),
    ];
    $args = [
        'label' => __('Shows', 'text_domain'),
        'description' => __('Shows Custom Post', 'text_domain'),
        'labels' => $labels,
        'supports' => ['title', 'editor'],
        'taxonomies' => [],
        'hierarchical' => false,
        'public' => false,
        'show_ui' => true,
        'show_in_menu' => true,
        'menu_position' => 20,
        'menu_icon' => 'dashicons-editor-video',
        'show_in_admin_bar' => true,
        'show_in_nav_menus' => true,
        'can_export' => true,
        'has_archive' => false,
        'show_in_rest' => true,
        'exclude_from_search' => false,
        'publicly_queryable' => true,
        'capability_type' => 'page',
    ];
    register_post_type('Shows', $args);
}
