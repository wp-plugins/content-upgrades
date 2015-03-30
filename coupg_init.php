<?php

// Init custom post type
function coupg_init()
{
    register_post_type('content-upgrades', array(
        'label'=>"Content Upgrades",
        'labels'=>array('all_items'=>'All Upgrades', 'add_new'=>'New Upgrade', 'new_item'=>'New Upgrade', 'edit_item'=>'Edit Upgrade', 'add_new_item'=>'Add New Upgrade', 'view_item'=>'View Upgrade', 'search_items'=>'Search Upgrades'),
        'supports'=>array('title'),
        'public'=>false,
        'show_ui'=>true,
        'show_in_menu'=>true,
        'rewrite'=>false,
        'menu_icon'=> plugin_dir_url( __FILE__ ).'res/menu_icon.png',
        'query_var'=>false,
        'publicly_queryable'=>false,
        'menu_position'=>80,
        'capabilities'=>array(
            'create_posts'=>'do_not_allow',
        ),        
        'map_meta_cap'=>true,
        'exclude_from_search'=>true
    ));
}

add_action('init', 'coupg_init');
add_action('admin_menu', 'coupg_admin_inits');
add_filter('bulk_actions-edit-content-upgrades', 'coupg_bulk_actions');
add_filter('manage_content-upgrades_posts_columns', 'coupg_custom_columns');
add_action("manage_content-upgrades_posts_custom_column", "coupg_custom_columns_data", 10, 2);
add_filter("manage_edit-content-upgrades_sortable_columns", "coupg_sortable_columns");
add_filter('post_row_actions', 'coupg_remove_row_actions', 10, 1);
add_action('admin_head-post.php', 'coupg_hide_publishing_actions');
add_action('admin_head-post-new.php', 'coupg_hide_publishing_actions');
add_filter('gettext', 'coupg_change_publish_button', 10, 2);
add_action('views_edit-content-upgrades', 'coupg_remove_views');
add_filter('wp_insert_post_data', 'coupg_force_published');
add_action('wp_ajax_copug_grab_mc_lists', 'coupg_grab_lists');
add_action('save_post', 'coupg_save_options_metabox');
add_action('save_post', 'coupg_save_override_metabox');
add_shortcode( 'content_upgrade', 'coupg_shortcode_handler' );
add_action('wp_ajax_coupg_subscribe', 'coupg_subscribe');
add_action('wp_ajax_nopriv_coupg_subscribe', 'coupg_subscribe');
add_filter('post_updated_messages', 'coupg_updated_messages');
add_shortcode('fancy_box', 'fancybox_shortcode_handler');
if (has_action('wp_footer'))
{
    add_action('wp_footer', 'coupg_process_footer');
} 