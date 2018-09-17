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
            var booking_date = moment(choosedDate).format('YYYY-MM-DD HH:00');
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
                    type: 'POST',
                    url: urlAjax,
                    data: {
                        userId: user_id,
                        seriviceId: service_id,
                        date: booking_date
                    },
                    dataType: 'JSON',
                    success: function(res) {
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
        $('#searchByName').keyup(function() {
            searchIntoJSON(search)
        });
    }
});

/**
 * ------------------------------------
 * - Form Validation | Multiple Pages -
 * ------------------------------------
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
        $('.bookingTables .deleteBooking').on('click', function() {
            var booking_id = $(this).attr('data-booking');

            var result = confirm('Sicuro di voler annullare la prenotazione?');
            if (result) {
                // ****************************************
                // ------------- SEND AJAX ----------------
                // ****************************************
                $.ajax({
                    type: 'POST',
                    url: urlAjax,
                    data: {
                        bookingId: booking_id,
                    },
                    dataType: 'JSON',
                    success: function(res) {
                        if (res.result == 1) {
                            var type = 'success';
                            $('#row' + booking_id).fadeOut();
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
        $('.msg_send_btn').on('click', function() {
            var text = $('.write_msg');
            var message = text.val();
            var userId = user_id;

            if (typeof userChatId !== 'undefined') {
                var sendData = {
                    message: message,
                    userId: userId,
                    userChatId: userChatId
                };
            } else {
                var sendData = {
                    message: message,
                    userId: userId
                };
            }
            // ****************************************
            // ------------- SEND AJAX ----------------
            // ****************************************
            if (message != '') {
                $.ajax({
                    type: 'POST',
                    url: urlAjaxSend,
                    data: sendData,
                    dataType: 'JSON',
                    success: function(res) {
                        if (res.result == 1) {
                            text.val('');
                        }
                    }
                });
            }
        });

        // Refresh the chat
        setInterval(function() {
            if (typeof userChatId !== 'undefined') {
                var sendData = {
                    lastData: lastRefresh,
                    userChatId: userChatId
                };
                var image = 'chat_user';
            } else {
                var sendData = {
                    lastData: lastRefresh
                };
                var image = 'chat_doctor';
            }
            // ****************************************
            // ------------- SEND AJAX ----------------
            // ****************************************
            $.ajax({
                type: 'GET',
                url: urlAjaxGet,
                data: sendData,
                dataType: 'JSON',
                success: function(res) {
                    if (res.messages.length > 0) {
                        for (var i = 0; i < res.messages.length; i++) {
                            var timeMsg = moment(res.messages[i].time).format('HH:mm:ss');
                            if (res.messages[i].user == user_id) {
                                $('.msg_history').append('<div class="outgoing_msg"><div class="sent_msg"><p>' + res.messages[i].message + '</p><span class="time_date"><strong>' + res.messages[i].name + ' ' + res.messages[i].surname + '</strong> - ' + timeMsg + '</span></div></div>');
                            } else {
                                $('.msg_history').append('<div class="incoming_msg"><div class="incoming_msg_img"><img src="' + imgUrl + '/img/' + image + '.png" alt="doctor"></div><div class="received_msg"><div class="received_withd_msg"><p>' + res.messages[i].message + '</p><span class="time_date"><strong>' + res.messages[i].name + ' ' + res.messages[i].surname + '</strong> - ' + timeMsg + '</span></div></div></div>');
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
/**
 * ----------------------------
 * - Staff Agenda Page Script -
 * ----------------------------
 */
$(document).ready(function() {
    // Check if schedule and service variables are setted
    if (typeof staffAgenda !== 'undefined') {

        $('[data-toggle="tooltip"]').tooltip();

        var d = new Date();
        var dateToday = new Date(d.getFullYear(), d.getMonth(), d.getDate(), d.getHours() + 1);

        // Agenda calendar
        var picker = $('#datetimepicker');
        picker.datetimepicker({
            minDate: dateToday,
            locale: 'it',
            inline: true,
            stepping: 60,
            format: 'DD/MM/YYYY',
            icons: {
                time: 'far fa-clock',
                date: 'far fa-calendar-alt',
                up: 'fas fa-arrow-up',
                down: 'fas fa-arrow-down',
                next: 'fas fa-caret-right',
                previous: 'fas fa-caret-left'
            }
        });
        picker.on('dp.change', function(val) {
            var newDate = val.date._d;
            $('.dateChoosed').html(moment(newDate).format('YYYY-MM-DD'));
            $.ajax({
                type: 'GET',
                url: ajaxUrlAgenda,
                data: {
                    data: moment(newDate).format('YYYY-MM-DD')
                },
                dataType: 'JSON',
                success: function(res) {
                    $('.appointmentsTable tbody').find('tr').remove();
                    if (res.result == 1) {
                        for (var i = 0; i < res.message.length; i++) {
                            var columns = '<td>' + moment(res.message[i].date).format('HH:mm') + '</td>';
                            columns += '<td>' + res.message[i].name + '</td>';
                            columns += '<td>' + res.message[i].patient_name + ' ' + res.message[i].patient_surname + '</td>';
                            columns += '<td style="width: 100px; text-align: center;">';
                            columns += '<a class="text-dark deleteBooking" data-toggle="tooltip" data-placement="top" title="Cancella prenotazione" data-booking="' + res.message[i].booking_id + '"><i class="fas fa-trash-alt"></i></a>';
                            columns += '&nbsp;&nbsp;';
                            columns += '<a class="text-dark updateBookingNew" data-toggle="tooltip" data-placement="top" title="Modifica prenotazione" data-booking="' + res.message[i].booking_id + '" data-toggle="modal" data-target="#updateModal"><i class="fas fa-edit"></i></a>';
                            columns += '&nbsp;&nbsp;';
                            columns += '<a class="text-dark" data-toggle="tooltip" data-placement="top" title="Apri chat" href="' + chatLink + '/id/' + res.message[i].user_id + '"><i class="fas fa-comment"></i></a>';
                            columns += '</td>';
                            $('.appointmentsTable tbody').append('<tr id="row' + res.message[i].booking_id + '">' + columns + '</tr>');
                            $('[data-toggle="tooltip"]').tooltip();
                            $('.deleteBooking').on('click', function() {
                                var booking_id = $(this).attr('data-booking');
                                var result = confirm('Sicuro di voler cancellare la prenotazione?');
                                if (result) {
                                    deleteElement(booking_id);
                                }
                            });
                            $('.updateBookingNew').on('click', function() {
                                $('#updateModal').modal();
                                var booking_id = $(this).attr('data-booking');
                                updateElement(booking_id);
                            });
                        }
                        appointmentsToday = res.message;
                    } else {
                        $('.appointmentsTable tbody').append('<tr><td colspan="4" class="table-light text-dark text-center">Nessun appuntamento previsto!</td></tr>');
                    }
                }
            });
        });
        $('.updateBooking').on('click', function() {
            $('#updateModal').modal();
            var booking_id = $(this).attr('data-booking');
            updateElement(booking_id);
        });
        $('.deleteBooking').on('click', function() {
            var booking_id = $(this).attr('data-booking');
            var result = confirm('Sicuro di voler cancellare la prenotazione?');
            if (result) {
                deleteElement(booking_id);
            }
        });

        function deleteElement(booking_id) {
            // ****************************************
            // ------------- SEND AJAX ----------------
            // ****************************************
            $.ajax({
                type: 'POST',
                url: ajaxUrlDelete,
                data: {
                    bookingId: booking_id,
                },
                dataType: 'JSON',
                success: function(res) {
                    if (res.result == 1) {
                        var type = 'success';
                        $('#row' + booking_id).fadeOut();
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

        var choosedDateUp;
        var service_idUp;
        var bookingIdUp;
        // Function to update booking
        function updateElement(booking_id) {
            var i;
            var d = new Date();
            var dateToday = new Date(d.getFullYear(), d.getMonth(), d.getDate(), d.getHours() + 1);

            for (i = 0; i < appointmentsToday.length; i++) {
                if (appointmentsToday[i].booking_id == booking_id) {

                    service_idUp = appointmentsToday[i].service_id;
                    bookingIdUp = appointmentsToday[i].booking_id;

                    var picker2 = $('#calendarModify');
                    picker2.datetimepicker({
                        defaultDate: moment(appointmentsToday[i].date).format('YYYY-MM-DD HH:00'),
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
                        sideBySide: true
                    });
                    picker2.on('dp.change', function(val) {
                        choosedDateUp = val.date._d;
                    });

                }
            }
        }

        $('#sendUpdateBook').click(function() {
            console.log('dentro')
            var booking_date = moment(choosedDateUp).format('YYYY-MM-DD HH:00');
            // ****************************************
            // ------------- SEND AJAX ----------------
            // ****************************************
            $.ajax({
                type: 'POST',
                url: ajaxUrlUpdate,
                data: {
                    bookingId: bookingIdUp,
                    seriviceId: service_idUp,
                    date: booking_date
                },
                dataType: 'JSON',
                success: function(res) {
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
        });
    }
});

/**
 * -----------------------
 * - Admin Delete Record -
 * -----------------------
 */
$(document).ready(function() {
    // Check if typeOfRecord variable is setted
    if (typeof typeOfRecord !== 'undefined') {
        $('.deleteAction').on('click', function() {
            var primaryKey = $(this).attr('data-primary');
            var type = typeOfRecord;

            var result = confirm('Sicuro di voler eliminare il record dalla tabella?');
            if (result) {
                // ****************************************
                // ------------- SEND AJAX ----------------
                // ****************************************
                $.ajax({
                    type: 'POST',
                    url: ajaxUrlDel,
                    data: {
                        type: type,
                        id: primaryKey
                    },
                    dataType: 'JSON',
                    success: function(res) {
                        if (res.result == 1) {
                            var type = 'success';
                            $('#row' + primaryKey).fadeOut();
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
 * -----------------------------------
 * - Admin DataTables Initialization -
 * -----------------------------------
 */
$(document).ready(function() {
    // Check if dataTableInit variable is setted
    if (typeof dataTableInit !== 'undefined') {
        // Open Update modal
        $('.updateAction').on('click', function() {
            $('#updateModal').modal();
            var primaryKey = $(this).attr('data-primary');
            var type = typeOfRecord;

            $('.modalInput-key').val(primaryKey);
            // ****************************************
            // ------------- SEND AJAX ----------------
            // ****************************************
            $.ajax({
                type: 'POST',
                url: ajaxUrlGet,
                data: {
                    type: type,
                    id: primaryKey
                },
                dataType: 'JSON',
                success: function(res) {
                    if (res.result == 1) {
                        if (res.record == 'faq') {
                            $('.modalInput-question').val(res.message[0].question);
                            $('.modalInput-answer').val(res.message[0].answer);
                        } else if (res.record == 'service') {
                            $('.modalInput-name').val(res.message[0].name);
                            $('.modalInput-description').val(res.message[0].description);
                            $('.modalInput-prescriptions').val(res.message[0].prescriptions);
                            $('.modalInput-department').find('option').remove().end();
                            $('.modalInput-doctor').find('option').remove().end();
                            for (var i = 0; i < res.message.department_list.length; i++) {
                                var selected = (res.message.department_list[i].department_id == res.message[0].department) ? 'selected' : '';
                                $('.modalInput-department').append('<option value="' + res.message.department_list[i].department_id + '" ' + selected + '>' + res.message.department_list[i].name + '</option>');
                            }
                            for (var a = 0; a < res.message.staff_list.length; a++) {
                                var selected = (res.message.staff_list[a].user_id == res.message[0].staff) ? 'selected' : '';
                                $('.modalInput-doctor').append('<option value="' + res.message.staff_list[a].user_id + '" ' + selected + '>' + res.message.staff_list[a].name + ' ' + res.message.staff_list[a].surname + '</option>');
                            }
                        } else if (res.record == 'booking') {
                            $('.modalInput-user').val(res.message[0].user);
                            $('.modalInput-service').val(res.message[0].service);
                        } else if (res.record == 'department') {
                            $('.modalInput-place').val(res.message[0].place);
                            $('.modalInput-name').val(res.message[0].name);
                            $('.modalInput-description').val(res.message[0].description);
                        } else if (res.record == 'staff') {
                            $('.modalInput-username').val(res.message[0].username);
                            $('.modalInput-name').val(res.message[0].name);
                            $('.modalInput-surname').val(res.message[0].surname);
                            $('.modalInput-code').val(res.message[0].code);
                            $('.modalInput-email').val(res.message[0].email);
                        } else if (res.record == 'user') {
                            $('.modalInput-username').val(res.message[0].username);
                            $('.modalInput-name').val(res.message[0].name);
                            $('.modalInput-surname').val(res.message[0].surname);
                            $('.modalInput-code').val(res.message[0].code);
                            $('.modalInput-email').val(res.message[0].email);
                        }
                    }
                }
            });
        });
        $('[data-toggle="tooltip"]').tooltip();

        // Open the schedule
        $('.openScheduleModal').on('click', function() {
            $('.schedule .table tbody').find('tr').remove();
            var dataSchedule = $(this).attr('data-schedule');
            var schedule = JSON.parse(dataSchedule).schedule;

            for (var i = 0; i < schedule.length; i++) {
                var day = schedule[i].day;
                var morOpening = (schedule[i].m_opening !== null) ? '<i class="fas fa-door-open"></i> ' + schedule[i].m_opening : '<i class="fas fa-door-closed"></i> chiuso';
                var morClosing = (schedule[i].m_closing !== null) ? ' - ' + schedule[i].m_closing : '';
                var aftOpening = (schedule[i].a_opening !== null) ? '<i class="fas fa-door-open"></i> ' + schedule[i].a_opening : '<i class="fas fa-door-closed"></i> chiuso';
                var aftClosing = (schedule[i].a_closing !== null) ? ' - ' + schedule[i].a_closing : '';
                $('.schedule .table tbody').append('<tr><td><em>' + day + '</em></td><td>' + morOpening + morClosing + '</td><td>' + aftOpening + aftClosing + '</td></tr>')
            }

            $('#hoursModal').modal();
        });

        // View the dataTable
        $('#dataTable').DataTable({
            responsive: true,
            order: []
        });
    }
});

/**
 * -----------------------
 * - Admin Inser Booking -
 * -----------------------
 */
$(document).ready(function() {
    // Check if dataTableInit variable is setted
    if (typeof adminInsertBooking !== 'undefined') {
        // Booking calendar
        var picker = $('#datetimepicker');
        var d = new Date();
        var dateToday = new Date(d.getFullYear(), d.getMonth(), d.getDate(), d.getHours() + 1);
        var choosedDate = dateToday;
        var userId;
        var serviceId;
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
            sideBySide: true
        });
        picker.on('dp.change', function(val) {
            choosedDate = val.date._d;
            userId = $('#InputUser').val();
            serviceId = $('#InputService').val();

            $('#InputDate').val(moment(choosedDate).format('YYYY-MM-DD HH:00'));

            if (userId != '' && serviceId != '') {
                // ****************************************
                // ------------- SEND AJAX ----------------
                // ****************************************
                $.ajax({
                    type: 'POST',
                    url: urlAjaxBookingCheck,
                    data: {
                        userId: userId,
                        seriviceId: serviceId,
                        date: moment(choosedDate).format('YYYY-MM-DD HH:00')
                    },
                    dataType: 'JSON',
                    success: function(res) {
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
            } else {
                bootoast({
                    message: 'Prima di scegliere la data devi selezionare un\'utente ed un servizio!',
                    type: 'warning',
                    timeout: 6,
                    position: 'bottom-right'
                });
            }
        });
    }
});

/**
 * -----------------------
 * - Admin Update Record -
 * -----------------------
 */
$(document).ready(function() {
    // Check if typeOfRecord variable is setted
    if (typeof typeOfRecord !== 'undefined') {
        $('#sendUpdateBook').on('click', function() {
            var formData = JSON.parse(JSON.stringify($('#updateModal form').serializeArray()));
            var data, type, updatedId;

            for (var i = 0; i < formData.length; i++) {
                if (formData[i].name == 'typeOfRecord' && formData[i].value == 'faq') {
                    // Update FAQ
                    type = 'faq';
                    updatedId = formData[3].value;
                    data = {
                        question: formData[0].value,
                        answer: formData[1].value
                    }
                } else if (formData[i].name == 'typeOfRecord' && formData[i].value == 'booking') {
                    // Update BOOKING
                    type = 'booking';
                    updatedId = formData[7].value;
                    data = {
                        user: formData[4].value,
                        service: formData[5].value,
                        date: formData[2].value + '-' + formData[0].value + '-' + formData[1].value + ' ' + formData[3].value
                    }
                } else if (formData[i].name == 'typeOfRecord' && formData[i].value == 'service') {
                    // Update SERVICE
                    type = 'service';
                    updatedId = formData[34].value;
                    data = {
                        name: formData[0].value,
                        description: formData[2].value,
                        department: formData[1].value,
                        staff: formData[3].value,
                        prescriptions: formData[32].value
                    }
                } else if (formData[i].name == 'typeOfRecord' && formData[i].value == 'department') {
                    // Update DEPARTMENT
                    type = 'department';
                    updatedId = formData[4].value;
                    data = {
                        name: formData[0].value,
                        description: formData[1].value,
                        place: formData[2].value
                    }
                } else if (formData[i].name == 'typeOfRecord' && formData[i].value == 'staff') {
                    // Update STAFF
                    type = 'staff';
                    updatedId = formData[6].value;
                    data = {
                        username: formData[0].value,
                        name: formData[1].value,
                        surname: formData[2].value,
                        code: formData[3].value,
                        email: formData[4].value
                    }
                } else if (formData[i].name == 'typeOfRecord' && formData[i].value == 'user') {
                    // Update USER
                    type = 'user';
                    updatedId = formData[6].value;
                    data = {
                        username: formData[0].value,
                        name: formData[1].value,
                        surname: formData[2].value,
                        code: formData[3].value,
                        email: formData[4].value
                    }
                }
            }

            if (formData) {
                // ****************************************
                // ------------- SEND AJAX ----------------
                // ****************************************
                $.ajax({
                    type: 'POST',
                    url: ajaxUrlUpd,
                    data: {
                        jsonData: data,
                        type: type,
                        id: updatedId
                    },
                    dataType: 'JSON',
                    success: function(res) {
                        if (res.result == 1) {
                            var type = 'success';
                            $('#updateModal').modal('toggle');
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

// ***************************************************************
// ------------------- Helpers Functions -------------------------
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
            message: 'Hello!',
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
                    var btnService = '<a href="service/index/id/' + searchJSON[i].service[j].service_id + '" class="btn btn-sm btn-info float-right">Vedi dettagli</a>'
                    $('#searchResult').append('<div class="card"><div class="card-body"><em>' + searchJSON[i].service[j].name + '</em>' + btnService + '</div></div>')
                }
            }
        }
    }
}