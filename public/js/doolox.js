function toggle_dropdown(id) {
    $('#' + id).toggle();
    if ($('#' + id).is(':visible')) {
        $.cookie(id, 1, { path: '/' });
    }
    else {
        $.cookie(id, 0, { path: '/' });
    }
}

if ($.cookie('dropdown1') && $.cookie('dropdown1') == '1') {
    document.write('<style type="text/css">#dropdown1 { display: block; }</style>');
}
else {
    document.write('<style type="text/css">#dropdown1 { display: none; }</style>');
}

if ($.cookie('dropdown2') && $.cookie('dropdown2') == '1') {
    document.write('<style type="text/css">#dropdown2 { display: block; }</style>');
}
else {
    document.write('<style type="text/css">#dropdown2 { display: none; }</style>');
}

function wpconnect(site_id, site_url) {
    bootbox.prompt("WordPress website username:<br /><span style=\"font-size: 12px; color: #888;\">(only first time and it's not stored in our database)</span>", function(result) {                
        if (result !== null) {
            $.ajax({
                url: base_url + 'wpcipher-connect/' + site_id + '/' + result,
                type: 'get',
                async: false,
                success: function(data) {
                    $('#wploginform').attr('action', site_url + 'wp-login.php');
                    $('#ciphertext').val(data.cipher);
                    $('#wploginform').submit();
                }
            });
        }
    });    
}

function wplogin(site_id, site_url) {
    $.ajax({
        url: base_url + 'wpcipher-login/' + site_id,
        type: 'get',
        async: false,
        success: function(data) {
            $('#wploginform').attr('action', site_url + 'wp-login.php');
            $('#ciphertext').val(data.cipher);
            $('#wploginform').submit();
        }
    });
}

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
    var domain = $('#url').val();
    if (domain.length) {
        $('#ajax-loader').fadeIn();
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
    else {
        $('#domain-free').fadeOut(function() { $('#domain-invalid').fadeIn(); });
        $('#domain-taken').fadeOut();
        $('#domain-doolox').fadeOut();
        $('#owner-parent').fadeOut();
    }
}

function check_subdomain(callback, paypal) {
    var domain = $('#url').val();
    $('#ajax-loader').fadeIn();
    $.ajax({
        url: base_url + 'check-subdomain/' + domain,
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