$(document).ready(function() {

    // Show password field if user send query to auth
    $('input#LoginFormEmail').on('focus', function() {
        $('input#tLoginFormPassword').show('450');
    });

    // Show name field if user forgot password
    $('a#forgotEmail').on('click', function() {
        if ($('input#LoginFormEmail').css('display') === 'none') {
            $('input#LoginFormEmail').show();
            $('input#LoginFormName').hide();
            $('a#forgotEmail').html('Войти по имени?');
        } else {
            $('input#LoginFormEmail').hide();
            $('input#LoginFormName').show();
            $('a#forgotEmail').html('Войти по email?&nbsp;&nbsp;');
        }
    });

    /*/////////////////////--------------------
                    TestDB scripts
    --------------------/////////////////////*/

    let role, action, entityId;

    // Delete testEntity by clicking on cross at last row column
    // $('th.deleteThisRow > i.fa-times').on('click', function() {
    //     entityId = parseInt($(this).parent('th').parent('tr').attr('id'));
    // });

    // Choose row in table
    $('table#entitiesTable>tbody>tr').on('click', function() {

        if ( ( entityId !== undefined ) && ( entityId !== $(this).attr('id') )  ) {
            $('tr#'+entityId).removeClass('default-color')
        }

        if ( $(this).hasClass('default-color') ) {
            $(this).removeClass('default-color');
            entityId = undefined;
            $('h4#displayEntityId').text('');
        } else {
            $(this).addClass('default-color');
            entityId = $(this).attr('id');
            $('h4#displayEntityId').text(entityId);
        }

    });

    // Choose role
    $('select#roleSelect > option').on('click', function() {
        role = $(this).text();
    });

    // An example of AJAX JSON query to application
    $('div#actionSelect > button').on('click', function() {

        console.log(role, action, entityId);

        if ( ( role === 'Choose role' ) || ( role === undefined ) ) {
            alert('Please, choose DB role!');
            return false;
        } else if ( entityId === undefined ) {
            alert('Please, choose an entity!');
            return false;
        }

        role = $("select#roleSelect option:selected").text();
        action = $(this).attr('action');

        console.log(role, action, entityId);

        var query = JSON.parse('{"role":"'+role+'","action":"'+action+'","entityId":"'+entityId+'"}');

        $.ajax({
            url: "/test_db",
            method: "POST",
            dataType: "json",
            data: query
        }).done(function( msg ) {
            console.log('Success');
            alert( msg['data'] );
        }).fail(function( msg ) {
            console.log('Fail');
            alert( msg['data'] );
        });

    });

});
