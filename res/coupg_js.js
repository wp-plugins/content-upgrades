var $j = jQuery;
$j('.coupg_popup_close').on('click', function (e) {
    e.preventDefault();
    $j('#coupg_upgrade_box_'+extract_id($j(this).attr('id'))).css("display", "none");
    $j('body').removeClass("open_pop");
});
$j('.coupg_popup_content').on('click', function (e) {
    e.stopPropagation();
});
$j('.coupg_popup').on('click', function (e) {
    $j(this).css("display", "none");
    $j('body').removeClass("open_pop");
});
$j('.coupg_link_container > a').on('click', function (e) {
    e.preventDefault();
    $j('#coupg_upgrade_box_'+extract_id($j(this).parent().attr('id'))).css("display", "block");
    $j('body').addClass("open_pop");
});

$j('.coupg_sbt_button').on('click', function (e) {
    if (!isEmail($j('#coupg_email_'+extract_id($j(this).attr('id'))).val())) {
        alert('Invalid E-mail')
    } else {
        $j(this).addClass('coupg_act');
        $j(this).attr('disabled','disabled');
        var request = {lid: $j('#coupg_hidden_'+extract_id($j(this).attr('id'))).text(), action: "coupg_subscribe", email: $j('#coupg_email_'+extract_id($j(this).attr('id'))).val()}
        $j.ajax({type: "post", dataType: "json", url: coupg_ajax_object.ajax_url, data: request, success: function (e) {
                if (e.status == 1) {
                    window.location = e.link;
                }
                else if (e.status == 0) {
                    window.location = e.link;
                }
                else if (e.status == -1) {
                    alert('Unknown error has occured. Please notify administrator as soon as possible.\nError code: ' + e.error);
                }
                 $j(this).removeAttr('disabled');
                 $j(this).removeClass('coupg_act');
            }})
    }
});

function isEmail(email) {
    var regex = /^^[a-z0-9!#$%&'*+/=?^_`{|}~-]+(?:\.[a-z0-9!#$%&'*+/=?^_`{|}~-]+)*@(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\.)+[a-z0-9](?:[a-z0-9-]*[a-z0-9])?$/;
    return regex.test(email);
}

function extract_id(id)
{
    return id.replace(/[^\d]+_/g, "");
}