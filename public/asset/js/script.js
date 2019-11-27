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
    window.showMessage = function(title, body, messageType, notifyType) {
        if ( messageType === 'modal' ) {
            alertModal(title, body);
        } else if ( messageType === 'notify' ) {
            alertNotify(title, body, notifyType);
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
    let notifySound = document.getElementById('notifySound');

    window.PlaySound = function() {
        // Because of track duration
        let tid = setTimeout(function(){
            audio.pause();
        }, 400);

        clearInterval(tid);

        notifySound.volume = 0.1;
        notifySound.pause();
        notifySound.currentTime = 0;
        notifySound.play();
    };

    // Warning popup

    let timerId = 0;                            // Using for counting timers
    let notifyProgressTimerArray = new Array(); // Using for saving timers ID's

    // This function clone warningPopup DOM element, fill it with text
    // show, hide and then remove it
    window.alertNotify = function(title, body, notifyType) {

        let popupWarning = $('div.popupWarning').first();

        if ( popupWarning.length > 0 ) {

            let warningStyle = '';

            switch (notifyType) {
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
            popupWarning.attr('notifyId', timerId);
            popupWarning.children('div.center').children('h5#warningBody').html(body);
            // Draggable property for notify closing
            popupWarning.draggable({
                axis: "x",
                drag: function (e) {
                    let left = parseInt($(this).css('left'));
                    // console.log(left);
                    if ( left <= -5 ) {
                        e.preventDefault();
                        $(this).css('left', '0px');
                        left = 0;
                    } else if ( left >= 105 ) {
                        $(this).hide('slow');
                        $(this).remove();
                    }
                }
            });

            popupWarning.draggable();

            let showTime = 5000;
            let timeLeft = 0;
            popupWarning.fadeIn(250);

            notifyProgressTimerArray[timerId] = setInterval(function() {
                timeLeft += 500;
                if ( timeLeft <= showTime ) {
                    popupWarning.children('div.progress').children('div.progress-bar').css('width', (timeLeft/showTime)*100+'%');
                } else {
                    setTimeout(function() {
                        popupWarning.fadeOut(250);
                    }, 250);
                    clearInterval(notifyProgressTimerArray[timerId]);
                    setTimeout(function() {
                        popupWarning.remove();
                    }, 550);
                }
                // console.log('Notify ID'+timerId+' timer tick: '+timeLeft);
            }, 500, popupWarning, timeLeft, showTime);

            // Timer counter for next timer's definition by id
            timerId += 1;

            // If sound object exists
            if (notifySound) {
                PlaySound();
            }

        } else {
            alert(title+'\n\n'+body);
        }
    };

    // Closing notifications by clicking on "X" button
    // handler binding happens dynamically using page's 'body' element
    window.$('body').on('click', 'div#popupWarningWrapper > div.popupWarning > div.closeButton > a.closeNotify', function(e) {
        e.preventDefault();
        let currentNotifyTimerId = parseInt($(this).parent('div.closeButton').parent('div.popupWarning').attr('notifyId'));
        $(this).parent('div.closeButton').parent('div.popupWarning').remove();
        clearTimeout(notifyProgressTimerArray[currentNotifyTimerId]);
    });

});
