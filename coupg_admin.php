<?php

//Add subpages subpage
function coupg_admin_inits()
{
    add_submenu_page(
            'edit.php?post_type=content-upgrades', 'New Upgrade', 'New Upgrade', 'manage_options', 'coupg_free_notice', 'coupg_show_free_notice_page');
    add_submenu_page(
        'edit.php?post_type=content-upgrades', 'Fancy Boxes', 'Fancy Boxes', 'manage_options', 'fancyboxes', 'fancybox_page');
    add_submenu_page(
            'edit.php?post_type=content-upgrades', 'Statistics', 'Statistics', 'manage_options', 'coupg_stats_free_notice', 'coupg_show_stats_free_notice_page');
    add_submenu_page(
            'edit.php?post_type=content-upgrades', 'Settings', 'Settings', 'manage_options', 'coupg_settings', 'coupg_show_settings_page');
    add_meta_box('coupg_options_metabox', 'Options', 'coupg_show_options_metabox', 'content-upgrades', 'normal', 'high');
    add_meta_box('coupg_override_metabox_post', 'Content Upgrade Options', 'coupg_show_override_metabox_post', 'post', 'normal', 'high');
    add_meta_box('coupg_override_metabox_page', 'Content Upgrade Options', 'coupg_show_override_metabox_page', 'page', 'normal', 'high');
    add_meta_box('coupg_preview_metabox', 'Live Preview', 'coupg_show_preview_metabox', 'content-upgrades', 'normal', 'high');
}

//Settings page callback
function coupg_show_settings_page()
{
    require('settings.php');
}

function fancybox_page()
{
    if (isset($_GET['id'])) {
        wp_enqueue_style('fb_admin_style', plugins_url('/res/fancy_styles.css', __FILE__));
        require('res/fancybox.php');
    } else {
        wp_enqueue_style('fb_admin_style', plugins_url('/res/coupg_acss.css', __FILE__));
        require('fancyboxes.php');
    }
}
//How to use page callback
function coupg_show_free_notice_page()
{
    require('coupg_free_notice.php');
}

function coupg_show_stats_free_notice_page()
{
    require('coupg_stats_notice.php');
}

// Remove edit from bluk actions
function coupg_bulk_actions($actions)
{
    unset($actions['edit']);
    return $actions;
}

//Rename Title column and add new ones
function coupg_custom_columns($columns)
{

    $columns['title']='Upgrade title';
    $columns=array_merge(array_slice($columns, 0, 2, true), array('shortcode'=>'Shortcode'), array_slice($columns, 2, null, true));
    $columns=array_merge(array_slice($columns, 0, 3, true), array('theme'=>'Theme'), array_slice($columns, 3, null, true));
    return $columns;
}

// Fill in values for custom columns 
function coupg_custom_columns_data($column, $post_id)
{
    switch ($column)
    {
        case "theme":
            {

                echo 'Deafault';
                break;
            }
        case "shortcode":
            {
                $default=get_option('coupg_default_upgrade');
                if ($post_id==$default)
                {
                    echo '[content_upgrade]Anchor text[/content_upgrade]';
                }
                else
                {
                    echo '[content_upgrade id='.$post_id.']Anchor text[/content_upgrade]';
                }
                break;
            }
    }
}

//Add ability to sort by values in columns
function coupg_sortable_columns()
{
    return array(
        'title'=>'title',
        'date'=>'date',
        'theme'=>'theme',
        'shortcode'=>'shortcode'
    );
}

// Show options meta box callback
function coupg_show_options_metabox()
{
    global $post;
    wp_enqueue_style('coupg_admin_style', plugins_url('/res/coupg_acss.css', __FILE__));
    wp_enqueue_style('coupg_style', plugins_url('/res/coupg_styles.css', __FILE__));
    wp_enqueue_script('coupg_admin_script', plugins_url('/res/coupg_as.js', __FILE__));
    // Use nonce for verification
    wp_nonce_field('coupg_options_metabox', 'coupg_options_metabox_nonce');
    $default=get_option('coupg_default_upgrade');
    if ($post->ID==$default)
    {
        $shortcode='[content_upgrade]Insert your anchort text here[/content_upgrade]';
    }
    else
    {
        $shortcode='[content_upgrade id='.$post->ID.']Insert your anchort text here[/content_upgrade]';
    }
    $table='<table class="form-table">'
            .'<tr>'
            .'<th style="width:20%"><label for="coupg_shortcode">Shortcode</label></th>'
            .'<td><input type="text" disabled=\'disabled\' name="coupg_shortcode" id="coupg_shortcode" value="'.$shortcode.'" size="30" style="width:97%" /><br />Use this shortcode to create a link to the content upgrade.</td>'
            .'</tr>'
            .'<tr>'
            .'<th style="width:20%"><label for="coupg_header">Headline</label></th>'
            .'<td><textarea name="coupg_header" id="coupg_header" cols="60" rows="2" style="width:97%">'.get_post_meta($post->ID, 'coupg_header', true).'</textarea>'
            .'<a class="button button-primary" id="coupg_ab">+ A/B Headline</a><span id="coupg_abheadline_warning"></span></td>'
            .'</tr>'
            .'<tr><th style="width:20%"><label for="coupg_description">Subhead</label></th>'
            .'<td><textarea name="coupg_description" id="coupg_description" cols="60" rows="2" style="width:97%">'.get_post_meta($post->ID, 'coupg_description', true).'</textarea></td>'
            .'</tr>'
            .'<tr>'
            .'<th style="width:20%"><label for="coupg_default_email_text">Email hint</label></th>'
            .'<td><input type="text"  name="coupg_default_email_text" id="coupg_default_email_text" value="'.get_post_meta($post->ID, 'coupg_default_email_text', true).'" size="30" style="width:97%" /></td>'
            .'</tr>'
            .'<tr>'
            .'<th style="width:20%"><label for="coupg_button_text">Button</label></th>'
            .'<td><input type="text"  name="coupg_button_text" id="coupg_button_text" value="'.get_post_meta($post->ID, 'coupg_button_text', true).'" size="30" style="width:97%" /></td>'
            .'</tr>'
            .'<tr>'
            .'<th style="width:20%"><label for="coupg_privacy_statement">Privacy statement</label></th>'
            .'<td><input type="text"  name="coupg_privacy_statement" id="coupg_privacy_statement" value="'.get_post_meta($post->ID, 'coupg_privacy_statement', true).'" size="30" style="width:97%" /></td>'
            .'</tr>'
            .'<tr>'
            .'<th style="width:20%"><label for="coupg_themes">Theme</label></th>'
            .'<td><select name="coupg_themes" id="coupg_themes">'
            .'<option value="default">Default</option>'
            .'<option value="-">Get more themes</option>'
            .'</select><span id="coupg_get_more_themes_notice">Get the <a href="http://contentupgradespro.com/?utm_source=free_plugin&utm_medium=backend&utm_campaign=free_plugin" target="_blank">PRO version</a>, if you need more themes.</span></td>'
            .'</tr>'
            .'<tr>'
            .'<th style="width:20%"><label for="coupg_lists">Connect to email list</label></th>'
            .'<td>'.coupg_form_list_dropdown(get_post_meta($post->ID, 'coupg_list', true)).'</td>'
            .'</tr>'
            .'<tr>'
            .'<th style="width:20%"></th>'
            .'<td>If the person is not in your list, forward him to this page: (usually this would be "please go to your inbox and confirm subscription" page)</td>'
            .'</tr>'
            .'<tr><th style="width:20%"><label for="coupg_pages">"Please confirm subscription" page</label></th>'
            .'<td>'.coupg_form_pages_dropdown(get_post_meta($post->ID, 'coupg_em_cofirm_page', true), 'coupg_em_cofirm_page').'<span id="custom_url_redir1">This feature is only available in PRO version</span></td>'
            .'</tr>'
            .'<tr>'
            .'<th style="width:20%"></th>'
            .'<td>If person is already in your list, forward him to the page with bonuses:</td>'
            .'</tr>'
            .'<tr><th style="width:20%"><label for="coupg_pages">"Thanks for subscribing" page</label></th>'
            .'<td>'.coupg_form_pages_dropdown(get_post_meta($post->ID, 'coupg_upg_location_page', true), 'coupg_upg_location_page').'<span id="custom_url_redir2">This feature is only available in PRO version</span><br/>Put the bonus materials on this page</td>'
            .'</tr>'
            .'</table>';
    echo $table;
}

// Handle data saving
function coupg_save_options_metabox($post_id)
{

    if (!isset(
                    $_POST['coupg_options_metabox_nonce']))
    {
        return;
    }

    if (!wp_verify_nonce($_POST['coupg_options_metabox_nonce'], 'coupg_options_metabox'))
    {
        return;
    }

    if (defined('DOING_AUTOSAVE')&&DOING_AUTOSAVE)
    {
        return;
    }
    if (isset($_POST['post_type'])&&'content-upgrades'==$_POST['post_type'])
    {
        if (!current_user_can('edit_post', $post_id))
        {
            return;
        }
    }

    //Header
    if (isset($_POST['coupg_header']))
    {
        $header=implode("\n", array_map('sanitize_text_field', explode("\n", $_POST['coupg_header'])));
    }
    else
    {
        $header='';
    }
    update_post_meta($post_id, 'coupg_header', $header);

    //Description
    if (isset($_POST['coupg_description']))
    {
        $desc=implode("\n", array_map('sanitize_text_field', explode("\n", $_POST['coupg_description'])));
    }
    else
    {
        $desc='';
    }
    update_post_meta($post_id, 'coupg_description', $desc);
    //Button text
    if (isset($_POST['coupg_button_text']))
    {
        $button_text=sanitize_text_field($_POST['coupg_button_text']);
    }
    else
    {
        $button_text='Submit';
    }
    update_post_meta($post_id, 'coupg_button_text', $button_text);
    //privacy statement
    if (isset($_POST['coupg_privacy_statement']))
    {
        $privacy=sanitize_text_field($_POST['coupg_privacy_statement']);
    }
    else
    {
        $privacy='';
    }
    update_post_meta($post_id, 'coupg_privacy_statement', $privacy);
    //privacy statement
    if (isset($_POST['coupg_default_email_text']))
    {
        $etext=sanitize_text_field($_POST['coupg_default_email_text']);
    }
    else
    {
        $etext='';
    }
    update_post_meta($post_id, 'coupg_default_email_text', $etext);
    // List
    if (isset($_POST['coupg_lists_dropdown']))
    {
        update_post_meta($post_id, 'coupg_list', $_POST['coupg_lists_dropdown']);
    }
    //Theme
    if (isset($_POST['coupg_themes']))
    {
        //$themes=get_option('coupg_themes'); -> Will hold list of themes in future
        $themes=array('default');
        if ($themes&&$themes!='')
        {
            if (in_array($_POST['coupg_themes'], $themes))
            {
                update_post_meta($post_id, 'coupg_theme', $_POST['coupg_themes']);
            }
        }
    }
    // Page
    if (isset($_POST['coupg_em_cofirm_page']))
    {
        if ($_POST['coupg_em_cofirm_page']!=-1&&$_POST['coupg_em_cofirm_page']!=-2)
        {
            update_post_meta($post_id, 'coupg_em_cofirm_page', $_POST['coupg_em_cofirm_page']);
        }
    }
    if (isset($_POST['coupg_upg_location_page']))
    {
        if ($_POST['coupg_upg_location_page']!=-1&&$_POST['coupg_upg_location_page']!=-2)
        {
            update_post_meta($post_id, 'coupg_upg_location_page', $_POST['coupg_upg_location_page']);
        }
    }
}

// Hides most of publish metabox and changes title
function coupg_hide_publishing_actions()
{
    global $post, $wp_meta_boxes;
    $wp_meta_boxes['content-upgrades'
            ]['side']['core']['submitdiv']['title']='Save';
    if ($post->post_type=='content-upgrades')
    {
        echo
        '
                <style type="text/css">
         
                           #misc-publishing-actions,
                    #minor-publishing-actions{
                        display:none;
                    }
                </style>
            ';
    }
}

// Remove inline view, quick edit links
function coupg_remove_row_actions($actions)
{
    if (get_post_type()==='content-upgrades')
    {
        unset($actions['quick edit']);
        unset($actions['view']);
        unset($actions['inline hide-if-no-js']);
    }
    return $actions;
}

// Very dirty way to replace text of Publish/Update button to Save
function coupg_change_publish_button($translation, $text)
{
    if
    ('content-upgrades'==get_post_type())
        if ($text=='Publish'||$text=='Update')
            return 'Save';

    return $translation;
}

// Form list dropdown
function coupg_form_list_dropdown($selected)
{
    $lists=get_option('coupg_maillists');
    if ($lists&&$lists!='')
    {
        $result='<select name="coupg_lists_dropdown" id="coupg_lists_dropdown">';
        $lists=json_decode($lists, true);
        foreach ($lists as $key=> $list_item)
        {
            $result.='<option '.selected($selected, $key, false).' value="'.$key.'">'.$list_item['name'].'</option>';
        }
        $result.='</select>';
        return $result;
    }
    else
    {
        return '<span class="coupg_inline_error">Go to plugin settings and connect your email service to make this work</span>';
    }
}

// Form pages dropdown
function coupg_form_pages_dropdown($selected, $name)
{
    global $wpdb;
    $query="select `id`, `post_title` from `".$wpdb->prefix."posts` where `post_type`='page' and `post_status` = 'publish' order by `"
            .$wpdb->prefix."posts`.`post_title` ASC";
    $pages=$wpdb->get_results($query, ARRAY_A);
    if (count($pages)>0)
    {
        $result='<select id="'.$name.'" name="'.$name.'"><option value="-1">Please pick a page</option>';
        foreach ($pages as $page_item)
        {

            $result.='<option '.selected($selected, $page_item['id'], false).' value="'.$page_item['id'].'">'.$page_item['post_title'].'</option>';
        }
        $result.='<option value="-2">Custom URL</option></select>';
        return $result;
    }
    else
    {
        return'There are no pages available.';
    }
}

// Remove All/Published/Draft/Trash/Pending
function coupg_remove_views($views)
{
    unset($views['draft']);
    unset($views['pending']);
    unset($views['all']);
    return $views;
}

// Save settings
function coupg_save_settings()
{
    if (isset($_POST['coupg_mcapikey_list'])&&preg_match('#[a-zA-Z0-9]{32}-[a-zA-Z0-9]{3}#', $_POST['coupg_mcapikey_list']))
    {
        $currentkey=get_option('coupg_mcapikey');
        if (!$currentkey||$currentkey!=$_POST['coupg_mcapikey_list'])
        {
            update_option('coupg_mcapikey', $_POST['coupg_mcapikey_list']);
            coupg_grab_lists();
            echo "<div class='updated'><p>Settings were updated.</p></div>";
        }
    }
}

// Prevent draft and pending
function coupg_force_published($post)
{
    if ($post['post_type']=='content-upgrades')
    {
        if ('trash'!==$post['post_status'])
        {
            $post['post_status']='publish';
            $post['post_name']=sanitize_title($post['post_title']);
        }
    }
    return $post;
}

// Preview metabox
function coupg_show_preview_metabox()
{
    global $post;
    ?>
    <div id="coupg_preview_container"><div id="coupg_upgrade_box_0_0" class="coupg_popup coupg_popup_default" style="display:block;position: relative ;background:none;overflow-y: visible;">
            <div class="coupg_popup_wrapper">
                <div class="coupg_artcl">
                    <div class="coupg_popup_content">
                        <div id="coupg_close_button_0_0" class="coupg_popup_close"></div>
                        <div class="coupg_popup_content_default">					
                            <div class="coupg_popup_top_default">
                                <span id="coupg_hidden_0_0" class="coupg_hidden">0</span>						
                                <div class="coupg_title" id="coupg_header_preview"><?php echo nl2br(get_post_meta($post->ID, 'coupg_header', true), false) ?></div>
                                <div class="coupg_descr" id="coupg_description_preview"><?php echo nl2br(get_post_meta($post->ID, 'coupg_description', true), false) ?></div>
                            </div>
                            <div class="coupg_subscribe_form_box coupg_clearfix">
                                <input id="coupg_email_0_0" class="coupg_left" type="text" placeholder="<?php echo get_post_meta($post->ID, 'coupg_default_email_text', true) ?>">
                                <button id="coupg_submit_button_0_0" class="coupg_sbt_button coupg_submit_button_default coupg_right" type="button"><?php echo get_post_meta($post->ID, 'coupg_button_text', true) ?></button>
                            </div>
                            <span class="coupg_popup_bottom_default" id="coupg_privacy_preview"><?php echo get_post_meta($post->ID, 'coupg_privacy_statement', true) ?></span>				
                        </div>
                    </div>
                </div>
            </div>
        </div></div>    
    <?php
}

function coupg_updated_messages($messages)
{
    global $post;
    $messages['content-upgrades']=array(
        0=>'',
        1=>'Upgrade updated.',
        2=>'Custom field updated.',
        3=>'Custom field deleted.',
        4=>'Upgrade updated.',
        5=>isset($_GET['revision']) ? sprintf('Upgrade restored to revision from %s', wp_post_revision_title((int) $_GET['revision'], false)) : false,
        6=>'Upgrade published.',
        7=>'Upgrade saved.',
        8=>'Upgrade submitted.',
        9=>sprintf(
                'Upgrade scheduled for: <strong>%1$s</strong>.', date_i18n('M j, Y @ G:i', strtotime($post->post_date))
        ),
        10=>'Upgrade draft updated.'
    );


    return $messages;
}

// post override
function coupg_show_override_metabox_post()
{
    global $post;
    // Use nonce for verification
    wp_nonce_field('coupg_override_metabox_post', 'coupg_override_metabox_post_nonce');
    $table='<table class="form-table">'
            .'<tr>'
            .'<th style="width:20%"><label for="coupg_header">Headline</label></th>'
            .'<td><textarea name="coupg_header" id="coupg_header" cols="60" rows="2" style="width:97%">'.get_post_meta($post->ID, 'coupg_header', true).'</textarea></td>'
            .'</tr>'
            .'<tr><th style="width:20%"><label for="coupg_description">Subhead</label></th>'
            .'<td><textarea name="coupg_description" id="coupg_description" cols="60" rows="2" style="width:97%">'.get_post_meta($post->ID, 'coupg_description', true).'</textarea></td>'
            .'</tr>'
            .'</table>';
    echo $table;
}

// page override
function coupg_show_override_metabox_page()
{
    global $post;
    // Use nonce for verification
    wp_nonce_field('coupg_override_metabox_page', 'coupg_override_metabox_page_nonce');
    $table='<table class="form-table">'
            .'<tr>'
            .'<th style="width:20%"><label for="coupg_header">Headline</label></th>'
            .'<td><textarea name="coupg_header" id="coupg_header" cols="60" rows="2" style="width:97%">'.get_post_meta($post->ID, 'coupg_header', true).'</textarea></td>'
            .'</tr>'
            .'<tr><th style="width:20%"><label for="coupg_description">Subhead</label></th>'
            .'<td><textarea name="coupg_description" id="coupg_description" cols="60" rows="2" style="width:97%">'.get_post_meta($post->ID, 'coupg_description', true).'</textarea></td>'
            .'</tr>'
            .'</table>';
    echo $table;
}

// Handle data override saving
function coupg_save_override_metabox($post_id)
{

    if (!isset($_POST['coupg_override_metabox_page_nonce'])&&!isset($_POST['coupg_override_metabox_post_nonce']))
    {
        return;
    }

    if (!wp_verify_nonce($_POST['coupg_override_metabox_page_nonce'], 'coupg_override_metabox_page')&&!wp_verify_nonce($_POST['coupg_override_metabox_post_nonce'], 'coupg_override_metabox_post'))
    {
        return;
    }

    if (defined('DOING_AUTOSAVE')&&DOING_AUTOSAVE)
    {
        return;
    }
    if (isset($_POST['post_type'])&&('post'==$_POST['post_type']||'page'==$_POST['post_type']))
    {
        if (!current_user_can('edit_post', $post_id))
        {
            return;
        }
    }
    //Header
    if (isset($_POST['coupg_header']))
    {
        $header=sanitize_text_field($_POST['coupg_header']);
    }
    else
    {
        $header='';
    }
    update_post_meta($post_id, 'coupg_header', $header);

    //Description
    if (isset($_POST['coupg_description']))
    {
        $desc=sanitize_text_field($_POST['coupg_description']);
    }
    else
    {
        $desc='';
    }
    update_post_meta($post_id, 'coupg_description', $desc);
}
