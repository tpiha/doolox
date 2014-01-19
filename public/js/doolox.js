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