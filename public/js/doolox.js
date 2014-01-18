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