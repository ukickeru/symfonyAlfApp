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
    window.showMessage = function(title, body, type, warningType) {
        if ( type === 'modal' ) {
            alertModal(title, body);
        } else if ( type === 'warning' ) {
            alertWarning(title, body, warningType);
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
    };

    // Warning popup
    // This function clone warningPopup DOM element, fill it with text
    // show, hide and then remove it
    window.alertWarning = function(title, body, warningType) {

        let popupWarning = $('div.popupWarning').first();

        if ( popupWarning.length > 0 ) {

            let warningStyle = '';

            switch (warningType) {
              case 'success':
                warningStyle = 'success-color';
                break;
              case 'warning':
                warningStyle = 'warning-color';
                break;
              case 'danger':
                warningStyle = 'danger-color';
                break;
              default:
                warningStyle = 'info-color';
            }

            popupWarning.clone().appendTo('div#popupWarningWrapper');
            popupWarning = $('div.popupWarning').last();
            popupWarning.addClass(warningStyle);
            popupWarning.children('div.center').children('h5#warningBody').html(body);

            popupWarning.fadeIn(250).delay(3000).fadeOut(250);
            setTimeout(function() {
                popupWarning.remove();
            }, 4000);
            PlaySound('warningSound');

        } else {
            alert(title+'\n\n'+body);
        }
    };

});
