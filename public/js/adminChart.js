$(document).ready(function(){
    
    // *******************
    // * Dashboard Chart *
    // *******************
    if (typeof adminChartInit !== 'undefined') {

        // CHART 1
        var ctx = document.getElementById('myChart1').getContext('2d');
        var chart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: labelsOfBookingPerMonth,
                datasets: [{
                    label: 'Numero di appuntamenti',
                    backgroundColor: 'rgb(255, 99, 132)',
                    borderColor: 'rgb(255, 99, 132)',
                    data: dataOfBookingPerMonth,
                }]
            },
            options: {}
        });

        // CHART 2
        var ctx2 = document.getElementById('myChart2').getContext('2d');
        var chart2 = new Chart(ctx2, {
            type: 'line',
            data: {
                labels: labelsOfBookingPerService,
                datasets: [{
                    label: 'Numero di appuntamenti',
                    backgroundColor: 'rgb(99, 255, 208)',
                    borderColor: 'rgb(99, 255, 208)',
                    data: dataOfBookingPerService,
                }]
            },
            options: {}
        });

        // CHART 3
        var ctx3 = document.getElementById('myChart3').getContext('2d');
        var chart3 = new Chart(ctx3, {
            type: 'line',
            data: {
                labels: labelsOfBookingPerDepartment,
                datasets: [{
                    label: 'Numero di appuntamenti',
                    backgroundColor: 'rgb(255, 153, 99)',
                    borderColor: 'rgb(255, 153, 99)',
                    data: dataOfBookingPerDepartment,
                }]
            },
            options: {}
        });


    }


    // **************
    // * User Chart *
    // **************
    if (typeof adminUserChartInit !== 'undefined') {

        // CHART 1
        var ctx = document.getElementById('myChart1').getContext('2d');
        var chart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: labelsOfUserPerMonth,
                datasets: [{
                    label: 'Numero di appuntamenti',
                    backgroundColor: 'rgb(255, 99, 132)',
                    borderColor: 'rgb(255, 99, 132)',
                    data: dataOfUserPerMonth,
                }]
            },
            options: {}
        });

        // CHART 2
        var ctx2 = document.getElementById('myChart2').getContext('2d');
        var chart2 = new Chart(ctx2, {
            type: 'line',
            data: {
                labels: labelsOfUserPerService,
                datasets: [{
                    label: 'Numero di appuntamenti',
                    backgroundColor: 'rgb(99, 255, 208)',
                    borderColor: 'rgb(99, 255, 208)',
                    data: dataOfUserPerService,
                }]
            },
            options: {}
        });

        // CHART 3
        var ctx3 = document.getElementById('myChart3').getContext('2d');
        var chart3 = new Chart(ctx3, {
            type: 'line',
            data: {
                labels: labelsOfUserPerDepartment,
                datasets: [{
                    label: 'Numero di appuntamenti',
                    backgroundColor: 'rgb(255, 153, 99)',
                    borderColor: 'rgb(255, 153, 99)',
                    data: dataOfUserPerDepartment,
                }]
            },
            options: {}
        });
    }

});