function toggle_dropdown(id) {
    $('#' + id).toggle();
    if ($('#' + id).is(':visible')) {
        $.cookie('dropdown1', 1, { path: '/' });
    }
    else {
        $.cookie('dropdown1', 0, { path: '/' });
    }
}

if ($.cookie('dropdown1') && $.cookie('dropdown1') == '1') {
    document.write('<style type="text/css">#dropdown1 { display: block; }</style>');
}
else {
    document.write('<style type="text/css">#dropdown1 { display: none; }</style>');
}


/*
 * RC4 symmetric cipher encryption/decryption
 *
 * @license Public Domain
 * @param string key - secret key for encryption/decryption
 * @param string str - string to be encrypted/decrypted
 * @return string
 */
function rc4(key, pt) {
    var hex = pt
    var bytes = [];
    for(var i=0; i< hex.length-1; i+=2){
        bytes.push(parseInt(hex.substr(i, 2), 16));
    }
    var str = String.fromCharCode.apply(String, bytes);
    pt = str;

    s = new Array();
    for (var i=0; i<256; i++) {
        s[i] = i;
    }
    var j = 0;
    var x;
    for (i=0; i<256; i++) {
        j = (j + s[i] + key.charCodeAt(i % key.length)) % 256;
        x = s[i];
        s[i] = s[j];
        s[j] = x;
    }
    i = 0;
    j = 0;
    var ct = '';
    for (var y=0; y<pt.length; y++) {
        i = (i + 1) % 256;
        j = (j + s[i]) % 256;
        x = s[i];
        s[i] = s[j];
        s[j] = x;
        ct += String.fromCharCode(pt.charCodeAt(y) ^ s[(s[i] + s[j]) % 256]);
    }
    return ct;
}

function wplogin(site_id) {
    var username = rc4(window.name, $('#username-' + site_id).val());
    var password = rc4(window.name, $('#password-' + site_id).val());
    var url = $('#url-' + site_id).val();

    $('#log').val(username);
    $('#pwd').val(password);
    $('#wploginform').attr('action', url);
    $('#wploginform').submit();
    $('#log').val('');
    $('#pwd').val('');
}

$(document).ready(function() {
    if (!window.name.length) {
        $.ajax({
            url: base_url + 'getk',
            type: 'get',
            success: function(response) {
                window.name = response.key;
            }
        });
    }
});


function update_caret() {
    var ds = $('#url').val().split('.');
    if (ds.length == 3) {
        var l = ds[0].length;
        if ($('#url').caret() > l) {
            $('#url').caret(l, l);
        }
        sd = __domain.split('.')
        if (ds.length < 3 || ds[1] != sd[0] || ds[2] != sd[1]) {
            $('#url').val($('#url').val() + '.' + __domain);
        }
    }
    else {
        $('#url').val('.' + __domain);
        $('#url').caret(0, 0);
    }
}


function check_domain(callback, paypal) {
    $('#ajax-loader').fadeIn();
    var domain = $('#url').val();
    // if (!domain) domain = $('#id_domain_full').val();
    $.ajax({
        url: base_url + 'check-domain/' + domain,
        type: 'get',
        success: function(data) {
            if (!paypal || (!data.free && !$('#id_owner').is(":checked")) || data.status == 3) $('#ajax-loader').fadeOut();
            $('#domain-required').fadeOut();
            $('#domain-invalid').fadeOut();
            $('.errorlist').fadeOut();
            if (data.free) {
                $('#domain-taken').fadeOut(function() { $('#domain-free').fadeIn(); });
                $('#domain-doolox').fadeOut();
                if (callback) callback();
                $('#owner-parent').fadeOut();
            }
            else {
                if (data.status == 1) {
                    $('#domain-free').fadeOut(function() { $('#domain-invalid').fadeIn(); });
                    $('#domain-taken').fadeOut();
                    $('#domain-doolox').fadeOut();
                    $('#owner-parent').fadeOut();
                }
                else if (data.status == 2) {
                    $('#domain-free').fadeOut(function() { $('#domain-taken').fadeIn(); $('#owner-parent').fadeIn(); });
                    $('#domain-doolox').fadeOut();
                    if ($('#id_owner').is(":checked")) callback();
                }
                else {
                    $('#domain-free').fadeOut(function() { $('#domain-doolox').fadeIn(); });
                    $('#domain-taken').fadeOut();
                    $('#owner-parent').fadeOut();
                    $('#id_owner').attr('checked', false);
                }
            }
        }
    });
}

function update_tld() {
    var tld = $('#domain :selected').val();
    __domain = tld;
    $('#url').val('.' + tld);
}