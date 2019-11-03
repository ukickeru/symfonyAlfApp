"use strict";

$(document).ready(function() {

    // Variables
    let role = $('#LoginFormName')[0].value;
    let pin;

    // Choose role
    $('select#LoginFormName > option').on('click', function() {
        role = $(this).text();
    });

    // Cancel (change) login
    $('button#changeLogin').on('click', function() {
        $('select#LoginFormName').toggleAttr('disabled');
        $('input#LoginFormPassword').hide('350');
        $('button#changeLogin').hide('350');
        $('button#formSubmit').text('Получить пин');
    });

    // Login form
    $('form#loginForm').on('submit', function() {

        if ( role === 'Выберите логин' ) {
            showMessage('Ошибка!','Пожалуйста, выберите логин!', 'modal');
        } else {
            if ( $('button#formSubmit').text() === 'Получить пин' ) {
                $('button#formSubmit').text('Войти');
                $('select#LoginFormName').toggleAttr('disabled');
                $('input#LoginFormPassword').show('350');
                $('button#changeLogin').show('350');
                getAuth('sendPin', role, '');
            } else if ( $('button#formSubmit').text() === 'Войти' ) {
                pin = $('input#LoginFormPassword').val();
                getAuth('login', role, pin);
            }
        }

    });

    // AJAX request
    function getAuth(action, role, pin) {

        var query = JSON.parse('{"type":"auth","action":"'+action+'","role":"'+role+'","pin":"'+pin+'"}');

        $.ajax({
            url: "/",
            method: "POST",
            dataType: "json",
            data: query
        }).done(function( msg ) {
            showMessage( msg['title'], msg['body'], msg['type'] );
        }).fail(function( msg ) {
            showMessage( msg['title'], msg['body'], msg['type'] );
        });

    };

});
