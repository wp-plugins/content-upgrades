<?php

/*
  Plugin Name: Content Upgrades
  Plugin URI: http://contentupgradespro.com
  Description: A free plugin that lets you create a single "content upgrade", but it can be customised for each page where you use it.
  Version: 1.1
  Author: Tim Soulo
  Author URI: http://bloggerjet.com
 */
require('coupg_admin.php');
require('coupg_init.php');

// Grab lists from API
function coupg_grab_lists()
{

    if (preg_match('#[a-zA-Z0-9]{32}-[a-zA-Z0-9]{3}#', $_POST['mcapikey']))
    {
        $apikey=$_POST['mcapikey'];
    }
    else if (preg_match('#[a-zA-Z0-9]{32}-[a-zA-Z0-9]{3}#', $_POST['coupg_mcapikey_list']))
    {
        $apikey=$_POST['coupg_mcapikey_list'];
    }
    else
    {
        $response=json_encode(array('status'=>-1)); // Invalid API key
        echo $response;
        die();
    }

    $mcapi=new coupg_mc_api($apikey);
    $result=$mcapi->call('lists/list');
    $listnames='';
    if ($result!==false&&key_exists('data', $result))
    {
        if ($result['total']>0)
        {
            foreach ($result['data'] as $list)
            {
                $lists[$list['id']]=array('name'=>$list['name'], 'subscribe_url_long'=>$list['subscribe_url_long']);
                $listnames.=$list['name']."\n";
            }
            $lists=json_encode($lists);
            $response=json_encode(array('status'=>1, 'listnum'=>$result['total'], 'listnames'=>$listnames)); // Got lists 
        }
        else
        {
            $lists='';
        }
        update_option('coupg_mcapikey', $apikey);
        update_option('coupg_maillists', $lists);
    }
    else
    {
        $response=json_encode(array('status'=>0, 'error'=>$result['name'])); // Failure   
    }



    if (isset($_POST['action']))
    {
        echo $response;
        die();
    }
}

// Minimalistic MailChimp API wrapper by Drew McLellan
class coupg_mc_api
{

    private $api_key;
    private $api_endpoint='https://<dc>.api.mailchimp.com/2.0';
    private $verify_ssl=false;

    function __construct($api_key)
    {
        $this->api_key=$api_key;
        list(, $datacentre)=explode('-', $this->api_key);
        $this->api_endpoint=str_replace('<dc>', $datacentre, $this->api_endpoint);
    }

    public function call($method, $args=array(), $timeout=10)
    {
        return $this->makeRequest($method, $args, $timeout);
    }

    private function makeRequest($method, $args=array(), $timeout=10)
    {
        $args['apikey']=$this->api_key;
        $url=$this->api_endpoint.'/'.$method.'.json';
        $json_data=json_encode($args);
        if (function_exists('curl_init')&&function_exists('curl_setopt'))
        {
            $ch=curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
            curl_setopt($ch, CURLOPT_USERAGENT, 'CoUpg_mc_integration');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $this->verify_ssl);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $json_data);
            $result=curl_exec($ch);
            curl_close($ch);
        }
        else
        {
            $result=file_get_contents($url, null, stream_context_create(array(
                'http'=>array(
                    'protocol_version'=>1.1,
                    'user_agent'=>'CoUpg_mc_integration',
                    'method'=>'POST',
                    'header'=>"Content-type: application/json\r\n".
                    "Connection: close\r\n".
                    "Content-length: ".strlen($json_data)."\r\n",
                    'content'=>$json_data,
                ),
            )));
        }
        return $result ? json_decode($result, true) : false;
    }

}

function coupg_activation()
{
    global $wpdb;
    $query="select * from `".$wpdb->prefix."posts` where `post_type`='content-upgrades' and `post_name`='default-upgrade';";
    $default=$wpdb->get_results($query, ARRAY_A);
    if (count($default)==0)
    {
        $post=array(
            'post_content'=>'',
            'post_name'=>'default-upgrade',
            'post_title'=>'Default Upgrade',
            'post_status'=>'publish',
            'post_type'=>'content-upgrades',
            'post_author'=>'1',
            'ping_status'=>'closed',
            'post_date'=>date('Y-m-d H:i:s'),
            'post_date_gmt'=>date('Y-m-d H:i:s'),
            'comment_status'=>'closed'
        );
        $id=wp_insert_post($post, $wp_error);
        if (is_numeric($id)&&$id!=0)
        {
            update_option('coupg_default_upgrade', $id);
            $header=get_post_meta($id, 'coupg_header', true);
            $subheader=get_post_meta($id, 'coupg_description', true);
            $button=get_post_meta($id, 'coupg_button_text', true);
            $email=get_post_meta($id, 'coupg_default_email_text', true);
            $privacy=get_post_meta($id, 'coupg_privacy_statement', true);
            if (empty($header)||$header='')
            {
                update_post_meta($id, 'coupg_header', "DOWNLOAD: 15 Amazing tools\r\nTo Get The Most Of Your Blog");
            }
            if (empty($subheader)||$subheader='')
            {
                update_post_meta($id, 'coupg_description', "Leave your email below\r\nto download my free ebook:");
            }
            if (empty($button)||$button='')
            {
                update_post_meta($id, 'coupg_button_text', "GET IT NOW");
            }
            if (empty($email)||$email='')
            {
                update_post_meta($id, 'coupg_default_email_text', "Your Email...");
            }
            if (empty($privacy)||$privacy='')
            {
                update_post_meta($id, 'coupg_privacy_statement', "We guarantee 100% privacy. Your email address is safe with us.");
            }
        }
    }
}

register_activation_hook(__FILE__, 'coupg_activation');

function coupg_shortcode_handler($atts, $content=null)
{
    global $wpdb, $post;
    $default=get_option('coupg_default_upgrade', -1);
    $upid=$default;
    wp_enqueue_style('coupg_style', plugins_url('/res/coupg_styles.css', __FILE__));
    wp_enqueue_script('coupg_script', plugins_url('/res/coupg_js.js', __FILE__), array('jquery'), NULL);
    wp_localize_script('coupg_script', 'coupg_ajax_object', array('ajax_url'=>admin_url('admin-ajax.php')));

    if (is_numeric($upid)&&$upid!=-1&&$upid!=0)
    {

        $query="select `ID` from `".$wpdb->prefix."posts` where `post_type`='content-upgrades' and `post_status`='publish' and `ID`='".$upid."';";
        $upid=$wpdb->get_results($query, ARRAY_A);
        if ($content==null)
        {
            $content='Example anchor text';
        }
        if (key_exists('ID', $upid[0])&&isset($upid[0])&&count($upid)>0)
        {
            $upid=$upid[0]['ID'];
            $header=get_post_meta($post->ID, 'coupg_header', true);
            $description=get_post_meta($post->ID, 'coupg_description', true);
            $email=get_post_meta($upid, 'coupg_default_email_text', true);
            $button=get_post_meta($upid, 'coupg_button_text', true);
            $privacy=get_post_meta($upid, 'coupg_privacy_statement', true);
            $listid=get_post_meta($upid, 'coupg_list', true);
            if ($header=='')
            {
                $header=get_post_meta($upid, 'coupg_header', true);
            }
            if ($description=='')
            {
                $description=get_post_meta($upid, 'coupg_description', true);
            }
            $lists=get_option('coupg_maillists');

            if ($lists&&$lists!='')
            {
                $lists=json_decode($lists, true);
                if (key_exists($listid, $lists))
                {
                    $theme='deafault';
                    $poid=$post->ID;
                    global $coupg_boxes, $coupg_boxes_index;
                    if ($coupg_boxes_index==null)
                    {
                        $coupg_boxes_index=array();
                    }
                    if (!key_exists($poid, $coupg_boxes_index))
                    {
                        $coupg_boxes_index[$poid]=1;
                    }
                    else
                    {
                        $coupg_boxes_index[$poid] ++;
                    }
                    if (has_action('wp_footer'))
                    {
                        $coupg_boxes[]=array('header'=>$header, 'description'=>$description, 'pid'=>$poid, 'upid'=>$upid, 'index'=>$coupg_boxes_index[$poid], 'theme'=>$theme, 'emailtext'=>$email, 'button'=>$button, 'privacy'=>$privacy);
                        return "<span id=\"coupg_link_container_".$poid."_".$coupg_boxes_index[$poid]."\" class=\"coupg_link_container\"><a href=\"#\">".$content."</a></span>";
                    }
                    else
                    {
                        return '<span class="coupg_error_message">[content_upgrade]Content Upgrade Error: Your theme is missing wp_footer hook. Refer to the FAQ for possible solutions.[/content_upgrade]</span>';
                    }
                }
                else
                {
                    return '<span class="coupg_error_message">[content_upgrade]Content Upgrade Error: Bad list ID. Make sure to select list from dropdown on Content Upgrade editing page.[/content_upgrade]</span>';
                }
            }
            else
            {
                return '<span class="coupg_error_message">[content_upgrade]Content Upgrade Error: You don\'t have any lists.[/content_upgrade]</span>';
            }
        }
        else
        {
            return '<span class="coupg_error_message">[content_upgrade]Content Upgrade Error: Bad upgrade ID.[/content_upgrade]</span>';
        }
    }
    else
    {
        return '<span class="coupg_error_message">[content_upgrade]Content Upgrade Error: Bad upgrade ID.[/content_upgrade]</span>';
    }
}

function coupg_subscribe()
{
    $lockerid=preg_replace('#[^\d]#', '', $_POST['lid']);

    $list=get_post_meta($lockerid, 'coupg_list', true);
    $apikey=get_option('coupg_mcapikey');
    $mcapi=new coupg_mc_api($apikey);
    $result=$mcapi->call('lists/subscribe', array('id'=>$list, 'email'=>array('email'=>$_POST['email'])));
    if ($result!==false&&key_exists('email', $result))
    {
        $link=get_post_meta($lockerid, 'coupg_em_cofirm_page', true);
        if ($link!='-1')
        {
            $link=get_permalink($link);
        } // confirm
        else
        {
            $link=get_site_url();
        }
        $response=json_encode(array('status'=>0, 'link'=>$link)); // already subbed   
    }
    else
    if ($result['name']=='List_AlreadySubscribed')
    {
        $link=get_post_meta($lockerid, 'coupg_upg_location_page', true);
        if ($link!='-1')
        {
            $link=get_permalink($link); //content location
        }
        else
        {
            $link=get_site_url();
        }
        $response=json_encode(array('status'=>1, 'link'=>$link)); // already subbed   
    }
    else
    {
// some weird error
        $response=json_encode(array('status'=>-1, 'error'=>$result['name'])); // Failure   
    }
    echo $response;
    if (isset($_POST['action']))
    {
        die();
    }
}

function coupg_process_footer()
{
    global $coupg_boxes;
    if(count($coupg_boxes)>0){
    $boxes="";
    foreach ($coupg_boxes as $item)
    {
         $boxes.='<div id="coupg_upgrade_box_'.$item['pid'].'_'.$item['index'].'" class="coupg_popup coupg_popup_default">
                <div class="coupg_popup_wrapper">
				<div class="coupg_artcl">
				<div class="coupg_popup_content">
				<div id="coupg_close_button_'.$item['pid'].'_'.$item['index'].'" class="coupg_popup_close"></div>
				<div class="coupg_popup_content_default">					
				<div class="coupg_popup_top_default">
				<span id="coupg_hidden_'.$item['pid'].'_'.$item['index'].'" class="coupg_hidden">'.$item['upid'].'</span>						
                <div class="coupg_title">'.nl2br($item['header'], false).'</div>
                <div class="coupg_descr">'.nl2br($item['description'], false).'</div>
				</div>
				<div class="coupg_subscribe_form_box coupg_clearfix">
				<input id="coupg_email_'.$item['pid'].'_'.$item['index'].'" class="coupg_left" type="text" placeholder="'.$item['emailtext'].'" required />
				<button id="coupg_submit_button_'.$item['pid'].'_'.$item['index'].'" class="coupg_sbt_button coupg_submit_button_default coupg_right" type="button">'.$item['button'].'</button>
				</div>
				<span class="coupg_popup_bottom_default">'.$item['privacy'].'</span>				
				</div>
                <div class="coupg_popup_pwdb">Powered by <a href="http://contentupgradespro.com/?utm_source=free_plugin&utm_medium=frontend&utm_campaign=free_plugin" title="Content Upgrades Pro">Content Upgrades Pro</a></div>
        		</div>
           		</div>
                </div>
                </div>';
    }
    echo $boxes;}
}

function fancybox_shortcode_handler($atts, $content = null)
{
    wp_enqueue_style('fancy_style', plugins_url('/res/fancy_styles.css', __FILE__));
    return '<div class="fb-bonusblock_1">'  . do_shortcode($content) . '</div>';
}

