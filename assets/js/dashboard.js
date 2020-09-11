
// CIRCLE CHART
$(document).ready(function() {
    var options = {
        chart: {
            height: 280,
            type: 'radialBar',
        },
        colors: ['#0e83dd'],
        plotOptions: {
            radialBar: {
                hollow: {
                    size: '70%',
                }
            },
        },
        labels: ['Complete'],
        series: [70],
    }
    var chart = new ApexCharts(
        document.querySelector("#apex-circle-chart"),
        options
    );

    chart.render();
});