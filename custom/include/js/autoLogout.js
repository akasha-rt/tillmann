$(window).bind('unload', function() {
    $.ajax({
        url: 'index.php?module=la_LoginAudit&action=autoLogoutOnBrowserClose',
        type: 'REQUEST',
        async: false,
        success: function(result) {
        }
    });
});