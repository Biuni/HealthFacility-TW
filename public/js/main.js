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
            daysOfWeekDisabled: closingDay,
            disabledHours: [0, 1, 2, 3, 4, 5, 6, 19, 20, 21, 22, 23]
        });
        picker.on('dp.change', function(val) {
            choosedDate = val.date._d;
        });
        btnBook.click(function() {
            var bookingDate = moment(choosedDate).format('YYYY-MM-DD H:00');
            var serviceId = service;
            // ****************************************
            // ------------- SEND AJAX ----------------
            // ****************************************
            bootoast({
                message: 'Prenotazione confermata!<br>Data: <strong>' + bookingDate + '</strong>',
                type: 'success',
                position: 'bottom-right'
            });
            bootoast({
                message: '<strong>ATTENZIONE!</strong> Data <u>non disponibile</u>.<br>Prova a modificare ora o giorno.',
                type: 'danger',
                position: 'bottom-right'
            });
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
                    var btnService = '<a href="service?id='+searchJSON[i].service[j].service_id+'" class="btn btn-sm btn-info float-right">Vedi dettagli</a>'
                    $('#searchResult').append('<div class="card"><div class="card-body"><em>'+searchJSON[i].service[j].name+'</em>'+btnService+'</div></div>')
                }
            }
        }
    }
}