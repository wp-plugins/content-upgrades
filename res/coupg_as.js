var $j = jQuery;
$j('#coupg_admin_refresh_mc_lists').on('click', function () {
    var request = {rog: coupg_admin_ajax_object.rog, action: "copug_grab_mc_lists", mcapikey: $j('#coupg_mcapikey_list').val()}
    $j.ajax({type: "post", dataType: "json", url: coupg_admin_ajax_object.ajax_url, data: request, success: function (e) {
            if (e.status == 1) {
                alert('Successfully grabbed ' + e.listnum + ' ' + (e.listnum == 1 ? 'list' : 'lists') + '\nList Names:\n' + e.listnames);
                $j('#coupg_save_button').prop("disabled", false);
            }
            else if (e.status == 0) {
                alert('Something went wrong. Here is what MailChimp returned:\n' + e.error);
            }
            else if (e.status == -1) {
                alert('Supplied API key is invalid');
            }
        }})
});

$j('#coupg_themes').change(function () {
    var request = {action: "copug_change_theme_preview", coupg_preview_header: $j('#coupg_header').val(), coupg_preview_description: $j('#coupg_description').val(), coupg_preview_theme: $j("#coupg_themes :selected").attr('value'), coupg_preview_emailtext: $j('#coupg_default_email_text').val(), coupg_preview_privacy: $j('#coupg_privacy_statement').val(), coupg_preview_button: $j('#coupg_button_text').val()}
    $j.ajax({type: "post", dataType: "json", url: coupg_admin_ajax_object.ajax_url, data: request, success: function (e) {
            $j('#coupg_preview_container').html(e.boxhtml)
        }})
});
//Live preview
$j('#coupg_description').keyup(function () {
    $j('#coupg_description_preview').html(coupg_nl2br($j('#coupg_description').val()));
});
$j('#coupg_header').keyup(function () {
    $j('#coupg_header_preview').html(coupg_nl2br($j('#coupg_header').val()));
});

$j('#coupg_privacy_statement').keyup(function () {
    $j('#coupg_privacy_preview').html($j('#coupg_privacy_statement').val());
});
$j('#coupg_button_text').keyup(function () {
    $j('#coupg_submit_button_0_0').html($j('#coupg_button_text').val());
});
$j('#coupg_default_email_text').keyup(function () {
    $j('#coupg_email_0_0').attr('placeholder', $j('#coupg_default_email_text').val());
});
function coupg_nl2br(str) {
    return (str + '').replace(/([^>\r\n]?)(\r\n|\n\r|\r|\n)/g, '$1' + '<br />' + '$2');
}