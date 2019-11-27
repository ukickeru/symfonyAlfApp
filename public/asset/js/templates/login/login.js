"use strict";

$(document).ready(function() {

    // Variables
    let role = $('#LoginFormName')[0].value;
    let pin;

    // Choose role
    $('select#LoginFormName').on('change', function() {
        role = $(this)[0].value;
    });

    // Cancel (change) login
    $('button#changeLogin').on('click', function() {
        $('select#LoginFormName').toggleAttr('disabled');
        $('input#LoginFormPassword').hide('350');
        $('button#changeLogin').hide('350');
        $('button#formSubmit').text('Получить пин');
    });

    // Login form
    $('form#loginForm').on('submit', function(event) {

        event.preventDefault();

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
            url: "/login",
            method: "POST",
            dataType: "json",
            data: query
        }).done(function( msg ) {
            if ( typeof( msg['signin'] ) !== "undefined" && msg['signin'] !== null ) {
                location.reload();
            } else {
                showMessage( msg['title'], msg['body'], msg['messageType'], msg['notifyType'] );
            }
        }).fail(function( msg ) {
            showMessage( msg['title'], msg['body'], msg['messageType'], msg['notifyType'] );
        });

    };

    // If user don't have access to system
    $('a#noAccess').on('click', function(e) {
        e.preventDefault();
        showMessage('Нет доступа к системе?', 'Если Вы не нашли свой логин с списке или при попытке входа возникает ошибка, пожалуйста, обратитесь к штатному программисту:<br><a href="tel:+7(999)999-99-99">+7(999)999-99-99</a><br>Спасибо за понимание.', 'modal');
    });

});
