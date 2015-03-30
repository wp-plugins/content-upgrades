<?php
coupg_save_settings();
wp_enqueue_style('coupg_admin_style', plugins_url('/res/coupg_acss.css', __FILE__));
wp_enqueue_script('coupg_admin_script', plugins_url('/res/coupg_as.js', __FILE__));
$apikey=get_option('coupg_mcapikey');
wp_localize_script('coupg_admin_script', 'coupg_admin_ajax_object', array('ajax_url'=>admin_url('admin-ajax.php'), 'rog'=>(!empty($apikey)&&$apikey) ? 0 : 1));
?>
<h2>Settings</h2>
<form name="form" method="POST" style="margin-top:15px;">
    <label for="coupg_client">Please select a mail client</label>
    <select id="coupg_client" name="coupg_client">
        <option value="mc" selected>MailChimp</option>
        <option value="more">More options</option>
    </select>
    <div id="coupg_more_clients_notice">Content Upgrades PRO integrates with:
        <br/><br/>- MailChimp;
        <br>- Aweber;
        <br>- GetResponse.
        <br/><br/>There's also an option to store subscribers locally and export them as .csv file.
        <br/><br/>Please upgrade to PRO version to get all these features.
    </div>
    <table class="form-table">
        <tr>
            <th><label for="coupg_mcapikey_list" class="coupg_admin_label" style="display:inline-block;">MailChimp API Key</label></th>
            <td>
                <input type="text" name="coupg_mcapikey_list" id="coupg_mcapikey_list" value="<?php echo $apikey ?>" class="coupg_admin_input"><button type="button" class="button button-secondary button-small" id="coupg_admin_refresh_mc_lists"><?php echo (!empty($apikey)&&$apikey) ? 'Refresh lists' : 'Grab lists' ?></button>
            </td>
        </tr>
        <tr>
            <th ><label  for="coupg_double_optin_mc" class="coupg_admin_label">Disable "double opt-in"<br/>(if you activate this option, people will be added to your email list without having to confirm their email address)</label></th>
            <td><input type="checkbox" name="coupg_double_optin_mc" id="coupg_double_optin_mc" value="0" disabled="disabled"><br/>Only available in <a href="http://contentupgradespro.com/?utm_source=free_plugin&utm_medium=backend&utm_campaign=free_plugin">PRO version</a></td>
        </tr>
        <tr>
            <th ><label  for="coupg_send_email" class="coupg_admin_label">Send a custom email to everyone who will opt-in<br/>(each content upgrade will have its own custom email that you need to configure)</label></th>
            <td><input type="checkbox" name="coupg_send_email" id="coupg_send_email" value="0" disabled="disabled"><br/>Only available in <a href="http://contentupgradespro.com/?utm_source=free_plugin&utm_medium=backend&utm_campaign=free_plugin">PRO version</a></td>
        </tr>
    </table>
    <input type="submit" style="margin-top:10px;" <?php echo (!empty($apikey)&&$apikey) ? '' : "disabled='disabled'" ?> class='button button-primary button-large' id="coupg_save_button" value="Save">
</form>