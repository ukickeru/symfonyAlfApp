$(document).ready(function() {

    // Show password field if user send query to auth
    $('input#LoginFormEmail').on('focus', function() {
        $('input#tLoginFormPassword').show('450');
    });

    // Show name field if user forgot password
    $('a#forgotEmail').on('click', function() {
        if ( $('input#LoginFormEmail').css('display') === 'none' ) {
            $('input#LoginFormEmail').show();
            $('input#LoginFormName').hide();
            $('a#forgotEmail').html('Войти по имени?');
        } else {
            $('input#LoginFormEmail').hide();
            $('input#LoginFormName').show();
            $('a#forgotEmail').html('Войти по email?&nbsp;&nbsp;');
        }
    });

});
