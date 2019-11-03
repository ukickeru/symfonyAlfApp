"use strict";

$(document).ready(function() {

    /*!
     * toggleAttr() jQuery plugin
     * @link http://github.com/mathiasbynens/toggleAttr-jQuery-Plugin
     * @description Used to toggle selected="selected", disabled="disabled", checked="checked" etc…
     * @author Mathias Bynens <http://mathiasbynens.be/>
     */
    jQuery.fn.toggleAttr = function(attr) {
        return this.each(function() {
            var $this = $(this);
            $this.attr(attr) ? $this.removeAttr(attr) : $this.attr(attr, attr);
        });
    };

    /*/////////////////////--------------------
            Message modal & warning popup
    --------------------/////////////////////*/

    // Showing message
    window.showMessage = function(title, body, type) {
        if ( type === 'modal' ) {
            alertModal(title, body);
        } else if ( type === 'warning' ) {
            alertWarning(title, body);
        } else {
            alert(title, body);
        }
    };

    // Alert modal or popup
    window.alertModal = function(title, body) {
        if ( $('div#messageModal').length > 0 ) {
            $('h4#messageTitle').html(title);
            $('h5#messageBody').html(body);
            $('div#messageModal').modal('show');
        } else {
            alert(title+'\n\n'+body);
        }
    };

    // Modal button
    $('button#messageButton').on('click', function() {
        $('div#messageModal').modal('hide');
    });

    // Playing sound
    window.PlaySound = function(soundObj) {
        let sound = document.getElementById(soundObj);
        sound.volume = 0.1;
        if ( sound ) {
            sound.play();
        }
    }

    // Warning popup
    window.alertWarning = function(title, body) {
        if ( $('div#popupWaning').length > 0 ) {
            $('h4#warningTitle').html(title);
            $('h5#warningBody').html(body);
            $('div#popupWaning').show(250).delay(3000).hide(250);
            PlaySound('warningSound');
        } else {
            alert(title+'\n\n'+body);
        }
    };
    
});
