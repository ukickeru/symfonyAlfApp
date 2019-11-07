"use strict";

$(document).ready(function() {

    /*/////////////////////--------------------
                        AJAX
    --------------------/////////////////////*/

    // Test DB button
    $('button#testDBConn').on('click', function() {
        testDBConn();
    });

    // Test DB connection function
    function testDBConn() {
        var query = JSON.parse('{"type":"testDBConn"}');
        $.ajax({
            url: "/user",
            method: "POST",
            dataType: "json",
            data: query
        }).done(function( msg ) {
            showMessage( msg['title'], msg['body'], msg['type'] );
        }).fail(function( msg ) {
            showMessage( msg['title'], msg['body'], msg['type'] );
        });
    }

    // Logout btn click
    $('button#logout').on('click', function() {
        logout();
    });

    // Logout function
    function logout() {
        $.ajax({
            url: "/logout",
            method: "POST",
            dataType: "json"
        }).done(function() {
            location.reload();
        });
    }

});
