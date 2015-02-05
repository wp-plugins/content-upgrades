<?php
coupg_save_settings();
wp_enqueue_style('coupg_admin_style', plugins_url('/res/coupg_acss.css', __FILE__));
wp_enqueue_script('coupg_admin_script', plugins_url('/res/coupg_as.js', __FILE__));
$apikey=get_option('coupg_mcapikey');
wp_localize_script('coupg_admin_script', 'coupg_admin_ajax_object', array('ajax_url'=>admin_url('admin-ajax.php'),'rog'=>(!empty($apikey)&&$apikey) ? 0 : 1));
?>
<h2>Settings</h2>
<form name="form" method="POST" style="margin-top:15px;">
    <table class="form-table">
        <tr>
            <th><label for="coupg_mcapikey_list" class="coupg_admin_label" style="display:inline-block;">MailChimp API Key</label></th>
            <td>
                <input type="text" name="coupg_mcapikey_list" id="coupg_mcapikey_list" value="<?php echo $apikey ?>" class="coupg_admin_input"><button type="button" class="button button-secondary button-small" id="coupg_admin_refresh_mc_lists"><?php echo (!empty($apikey)&&$apikey) ? 'Refresh lists' : 'Grab lists' ?></button>
            </td>
        </tr>
    </table>
    <input type="submit" style="margin-top:10px;" <?php echo (!empty($apikey)&&$apikey) ? '' : "disabled='disabled'" ?> class='button button-primary button-large' id="coupg_save_button" value="Save">
</form>