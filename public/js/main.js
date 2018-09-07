// ***************************************************
// ------------------- Page --------------------------
// ***************************************************
/**
 * -----------------------
 * - Service Page Script -
 * -----------------------
 */
$(document).ready(function() {
    // Check if schedule and service variables are setted
    if (typeof schedule !== 'undefined' && typeof service !== 'undefined') {

        // Create the opening hours table
        var parsed = JSON.parse(schedule).schedule;
        var closingDay = [];
        for (var i = 0; i < parsed.length; i++) {
            var day = parsed[i].day;
            var morOpening = (parsed[i].m_opening !== null) ? '<i class="fas fa-door-open"></i> ' + parsed[i].m_opening : '<i class="fas fa-door-closed"></i> chiuso';
            var morClosing = (parsed[i].m_closing !== null) ? ' - ' + parsed[i].m_closing : '';
            var aftOpening = (parsed[i].a_opening !== null) ? '<i class="fas fa-door-open"></i> ' + parsed[i].a_opening : '<i class="fas fa-door-closed"></i> chiuso';
            var aftClosing = (parsed[i].a_closing !== null) ? ' - ' + parsed[i].a_closing : '';
            $('.schedule .table tbody').append('<tr><td><em>' + day + '</em></td><td>' + morOpening + morClosing + '</td><td>' + aftOpening + aftClosing + '</td></tr>')

            // Check if the hospital is closed all the day and push element
            // into the array that will be used in the datetimepicker
            if (parsed[i].m_opening === null && parsed[i].a_opening === null) {
                closingDay.push(i)
            }
        }

        // Booking calendar
        var picker = $('#datetimepicker');
        var btnBook = $('#book');
        var d = new Date();
        var dateToday = new Date(d.getFullYear(), d.getMonth(), d.getDate(), d.getHours() + 1);
        var choosedDate = dateToday;
        picker.datetimepicker({
            minDate: dateToday,
            locale: 'it',
            inline: true,
            stepping: 60,
            format: 'dd/MM/YYYY H',
            icons: {
                time: 'far fa-clock',
                date: 'far fa-calendar-alt',
                up: 'fas fa-arrow-up',
                down: 'fas fa-arrow-down',
                next: 'fas fa-caret-right',
                previous: 'fas fa-caret-left'
            },
            sideBySide: true,
            daysOfWeekDisabled: closingDay
        });
        picker.on('dp.change', function(val) {
            choosedDate = val.date._d;
        });
        btnBook.click(function() {
            var booking_date = moment(choosedDate).format('YYYY-MM-DD H:00');
            var service_id = service;
            var user_id = userId;

            if (userLogged == 0) {
                bootoast({
                    message: '<strong>INFO:</strong> registrati o esegui il login per poterti prenotare!',
                    type: 'warning',
                    timeout: 6,
                    position: 'bottom-right'
                });
            } else {
                // ****************************************
                // ------------- SEND AJAX ----------------
                // ****************************************

                $.ajax({
                    type : 'POST',
                    url : urlAjax,
                    data : {
                        userId: user_id,
                        seriviceId: service_id,
                        date: booking_date
                    },
                    dataType : 'JSON',
                    success : function(res){
                        if (res.result == 1) {
                            var type = 'success';
                        } else {
                            var type = 'danger';
                        }
                        bootoast({
                            message: res.message,
                            type: type,
                            timeout: 6,
                            position: 'bottom-right'
                        });
                    }
                });
            }
        });
    }
});

/**
 * ----------------------
 * - Search Page Script -
 * ----------------------
 */
$(document).ready(function() {
    // Check if search variable is setted
    if (typeof search !== 'undefined') {
        for (var i = 0; i < search.length; i++) {
            $('#departmentSelect').append('<option value="' + search[i].department_id + '">' + search[i].name + '</option>')
        }

        $('#departmentSelect').change(function() {
            $('#chooseService').fadeIn(); // fadeIn the select
            var departmentId = $(this).val(); // read the id of selected department
            $('#serviceSelect').find('option').remove(); // remove eventually options

            for (var j = 0; j < search.length; j++) {
                if (search[j].department_id == departmentId) {
                    $('#serviceSelect').append('<option>Scegli...</option>')
                    for (var a = 0; a < search[j].service.length; a++) {
                        $('#serviceSelect').append('<option value="' + search[j].service[a].service_id + '">' + search[j].service[a].name + '</option>')
                    }
                }
            }
        });

        $('#serviceSelect').change(function() {
            $('#viewService').fadeIn();
            var serviceId = $(this).val(); // read the id of selected department
            $('#viewService a').attr('href', 'service?id=' + serviceId);
        });

        // Search into JSON by text
        $('#searchByName').keyup(function(){
            searchIntoJSON(search)
        });
    }
});

/**
 * ------------------------
 * - Register Page Script -
 * ------------------------
 */
$(document).ready(function() {
    // Check if validationForm variable is setted
    if (typeof validationForm !== 'undefined') {
      // Fetch the form we want to apply custom Bootstrap validation styles to
      window.addEventListener('load', function() {
        var forms = $('.need-validation');
        // Loop over them and prevent submission
        var validation = Array.prototype.filter.call(forms, function(form) {
          form.addEventListener('submit', function(event) {
            if (form.checkValidity() === false) {
              event.preventDefault();
              event.stopPropagation();
            }
            form.classList.add('was-validated');
          }, false);
        });
      }, false);
    }
});

/**
 * ----------------------------------
 * - Reserved User Area Page Script -
 * ----------------------------------
 */
$(document).ready(function() {
    // Check if reservedUserArea variable is setted
    if (typeof reservedUserArea !== 'undefined') {
        $('.bookingTables .deleteBooking').on('click', function(){
            var booking_id = $(this).attr('data-booking');

            var result = confirm('Sicuro di voler annullare la prenotazione?');
            if (result) {
                // ****************************************
                // ------------- SEND AJAX ----------------
                // ****************************************
                $.ajax({
                    type : 'POST',
                    url : urlAjax,
                    data : {
                        bookingId: booking_id,
                    },
                    dataType : 'JSON',
                    success : function(res){
                        if (res.result == 1) {
                            var type = 'success';
                            $('#row'+booking_id).fadeOut();
                        } else {
                            var type = 'danger';
                        }
                        bootoast({
                            message: res.message,
                            type: type,
                            timeout: 6,
                            position: 'bottom-right'
                        });
                    }
                });
            }
        })
    }
});

/**
 * -------------------------
 * - Chat User Page Script -
 * -------------------------
 */
$(document).ready(function() {

    // Moment Europe TIMEZONE
    moment.tz.add(["Europe/Rome|CET CEST|-10 -20|0101010101010101010101010101010101010101010101010101010101010101010101010101010101010101010101010101010101010101010101010101010101010101010101010101010101010101010101010|-2as10 M00 1cM0 1cM0 14o0 1o00 WM0 1qM0 17c0 1cM0 M3A0 5M20 WM0 1fA0 1cM0 16K0 1iO0 16m0 1de0 1lc0 14m0 1lc0 WO0 1qM0 GTW0 On0 1C10 Lz0 1C10 Lz0 1EN0 Lz0 1C10 Lz0 1zd0 Oo0 1C00 On0 1C10 Lz0 1zd0 On0 1C10 LA0 1C00 LA0 1zc0 Oo0 1C00 Oo0 1zc0 Oo0 1fC0 1a00 1cM0 1cM0 1cM0 1cM0 1cM0 1cM0 1cM0 1fA0 1cM0 1cM0 1cM0 1cM0 1cM0 1cM0 1cM0 1cM0 1cM0 1cM0 1cM0 1fA0 1cM0 1cM0 1cM0 1cM0 1cM0 1cM0 1cM0 1cM0 1cM0 1cM0 1fA0 1o00 11A0 1o00 11A0 1o00 11A0 1qM0 WM0 1qM0 WM0 1qM0 11A0 1o00 11A0 1o00 11A0 1qM0 WM0 1qM0 WM0 1qM0 WM0 1qM0 11A0 1o00 11A0 1o00 11A0 1qM0 WM0 1qM0 WM0 1qM0 11A0 1o00 11A0 1o00 11A0 1o00 11A0 1qM0 WM0 1qM0 WM0 1qM0 11A0 1o00 11A0 1o00 11A0 1qM0 WM0 1qM0 WM0 1qM0 11A0 1o00 11A0 1o00 11A0 1o00 11A0 1qM0 WM0 1qM0 WM0 1qM0 11A0 1o00 11A0 1o00 11A0 1qM0 WM0 1qM0 WM0 1qM0 WM0 1qM0 11A0 1o00 11A0 1o00|39e5"]);

    // Check if chatPage variable is setted
    if (typeof chatPage !== 'undefined') {
        // Scroll down the chat
        $('.msg_history').scrollTop($('.msg_history')[0].scrollHeight);

        // Send a new message
        $('.msg_send_btn').on('click', function(){
            var text = $('.write_msg');
            var message = text.val();
            var userId = user_id;
            // ****************************************
            // ------------- SEND AJAX ----------------
            // ****************************************
            if (message != '') {
                $.ajax({
                    type : 'POST',
                    url : urlAjaxSend,
                    data : {
                        message: message,
                        userId: userId
                    },
                    dataType : 'JSON',
                    success : function(res){
                        if (res.result == 1) {
                            text.val('');
                        }
                    }
                });
            }
        });

        // Refresh the chat
        setInterval(function(){
            // ****************************************
            // ------------- SEND AJAX ----------------
            // ****************************************
            $.ajax({
                type : 'GET',
                url : urlAjaxGet,
                data : {
                    lastData: lastRefresh
                },
                dataType : 'JSON',
                success : function(res){
                    if (res.messages.length > 0) {
                        for (var i = 0; i < res.messages.length; i++) {
                            var timeMsg = moment(res.messages[i].time).format('HH:mm:ss');
                            if (res.messages[i].user == user_id) {
                                $('.msg_history').append('<div class="outgoing_msg"><div class="sent_msg"><p>'+res.messages[i].message+'</p><span class="time_date"><strong>'+res.messages[i].name+' '+res.messages[i].surname+'</strong> - '+timeMsg+'</span></div></div>');
                            } else {
                                $('.msg_history').append('<div class="incoming_msg"><div class="incoming_msg_img"><img src="'+imgUrl+'/img/chat_doctor.png" alt="doctor"></div><div class="received_msg"><div class="received_withd_msg"><p>'+res.messages[i].message+'</p><span class="time_date"><strong>'+res.messages[i].name+' '+res.messages[i].surname+'</strong> - '+timeMsg+'</span></div></div></div>');
                            }
                            $('.msg_history').scrollTop($('.msg_history')[0].scrollHeight);
                        }
                    }
                    lastRefresh = moment(new Date()).tz('Europe/Rome').format('YYYY-MM-DD HH:mm:ss');
                }
            });
        }, timeRefresh);
    }
});


// ***************************************************************
// ------------------- Helpers Function --------------------------
// ***************************************************************

/**
 * --------------------------
 * - Bootstrap Toast Notify -
 * --------------------------
 */
(function($, window, document, undefined) {
    "use strict";
    var pluginName = 'bootoast';

    function BootstrapNotify(options) {
        if (options !== undefined) {
            this.settings = $.extend({}, this.defaults);
            if (typeof options !== 'string') {
                $.extend(this.settings, options);
            } else {
                this.settings.message = options;
            }
            this.content = this.settings.content || this.settings.text || this.settings.message;
            if (this.positionSupported[this.settings.position] === undefined) {
                var positionCamel = $.camelCase(this.settings.position);
                if (this.positionSinonym[positionCamel] !== undefined) {
                    this.settings.position = this.positionSinonym[positionCamel] || 'bottom-center';
                }
            }
            var position = this.settings.position.split('-'),
                positionSelector = '.' + position.join('.'),
                positionClass = position.join(' ');
            this.putTo = position[0] == 'bottom' ? 'appendTo' : 'prependTo';
            this.settings.icon = this.settings.icon || this.icons[this.settings.type];
            var containerClass = pluginName + '-container';
            if ($('body > .' + containerClass + positionSelector).length === 0) {
                $('<div class="' + containerClass + ' ' + positionClass + '"></div>').appendTo('body');
            }
            this.$el = $('<div class="alert alert-' + this.settings.type + ' ' + pluginName + '"><span class="fas fa-' + this.settings.icon + '"></span><span class="bootoast-alert-container"><span class="bootoast-alert-content">' + this.content + '</span></span></div>')[this.putTo]('.' + containerClass + positionSelector);
            if (this.settings.dismissable === true) {
                this.$el
                    .addClass('alert-dismissable')
                    .prepend('<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>');
            }
            this.$el.animate({
                opacity: 1,
            }, this.settings.animationDuration);
            if (this.settings.timeout !== false) {
                var secondsTimeout = parseInt(this.settings.timeout * 1000),
                    timer = this.hide(secondsTimeout),
                    plugin = this;
                this.$el.hover(
                    clearTimeout.bind(window, timer),
                    function() {
                        timer = plugin.hide(secondsTimeout);
                    });
            }
        }
    };
    $.extend(BootstrapNotify.prototype, {
        defaults: {
            message: 'Helo!',
            type: 'info',
            position: 'bottom-center',
            icon: undefined,
            timeout: false,
            animationDuration: 300,
            dismissable: true
        },
        icons: {
            warning: 'exclamation',
            success: 'check-circle',
            danger: 'exclamation-triangle',
            info: 'info-circle'
        },
        positionSinonym: {
            bottom: 'bottom-center',
            leftBottom: 'bottom-left',
            rightBottom: 'bottom-right',
            top: 'top-center',
            rightTop: 'top-right',
            leftTop: 'top-left'
        },
        positionSupported: [
            'top-left',
            'top-center',
            'top-right',
            'bottom-left',
            'bottom-right'
        ],
        hide: function(timeout) {
            var plugin = this;
            return setTimeout(function() {
                plugin.$el.animate({
                    opacity: 0,
                }, plugin.settings.animationDuration, function() {
                    plugin.$el.remove();
                });
            }, timeout || 0);
        }
    });
    window[pluginName] = function(options) {
        return new BootstrapNotify(options);
    };
})(window.jQuery || false, window, document);


/**
 * -------------------------------
 * - Search into JSON of service -
 * -------------------------------
 */
function searchIntoJSON(searchJSON) {
    
    var input, filter, table, tr, td, i;
    input = $('#searchByName').val();
    filter = input.toUpperCase();

    $('#searchResult').find('.card').remove();
    for (i = 0; i < searchJSON.length; i++) {
        if (searchJSON[i].service.length > 0) {
            for (j = 0; j < searchJSON[i].service.length; j++) {
                if (searchJSON[i].service[j].name.toUpperCase().indexOf(filter) > -1) {
                    var btnService = '<a href="service/index/id/'+searchJSON[i].service[j].service_id+'" class="btn btn-sm btn-info float-right">Vedi dettagli</a>'
                    $('#searchResult').append('<div class="card"><div class="card-body"><em>'+searchJSON[i].service[j].name+'</em>'+btnService+'</div></div>')
                }
            }
        }
    }
}