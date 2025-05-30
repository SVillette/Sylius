/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

import ApexCharts from 'apexcharts';

let chart = null;
function renderChart() {
    // eslint-disable-next-line no-undef
    const statisticsChart = document.querySelector('#statistics-chart');

    if (!statisticsChart) {
        return;
    }

    const options = {
        colors: ['#32be9f', '#066fd1'],
        fill: {
            colors: ['#32be9f']
        },
        series: [{
            name: 'Sales',
            data: JSON.parse(statisticsChart.dataset.sales)
        }, {
            name: 'Paid orders count',
            data: JSON.parse(statisticsChart.dataset.paidOrdersCount)
        }],
        chart: {
            toolbar: {
                show: false
            },
            height: 350,
            type: 'line'
        },
        plotOptions: {
            bar: {
                borderRadius: 4,
                dataLabels: {
                    position: 'top' // top, center, bottom
                }
            }
        },
        xaxis: {
            categories: JSON.parse(statisticsChart.dataset.intervals),
            position: 'top',
            axisBorder: {
                show: false
            },
            axisTicks: {
                show: false
            },
            crosshairs: {
                fill: {
                    type: 'gradient',
                    gradient: {
                        colorFrom: '#32be9f',
                        colorTo: '#2a9f83',
                        stops: [0, 100],
                        opacityFrom: 0.4,
                        opacityTo: 0.5
                    }
                }
            },
            tooltip: {
                enabled: true
            }
        },
        yaxis: [{
            axisBorder: {
                show: false
            },
            axisTicks: {
                show: false
            },
            labels: {
                formatter(val) {
                    const { currency } = statisticsChart.dataset;
                    return `${currency}${val}`;
                }
            }

        }, {
            opposite: true,
            labels: {
                formatter(val) {
                    return val;
                }
            }
        }],
        title: {
            floating: true,
            offsetY: 330,
            align: 'center',
            style: {
                color: '#444'
            }
        }
    };

    chart = new ApexCharts(statisticsChart, options);
    chart.render();
}

renderChart();

const element = document.querySelector('#statistics-chart');

if (element) {
    const observer = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            if (mutation.attributeName === 'data-sales' || mutation.attributeName === 'data-intervals') {
                chart.destroy();
                renderChart();
            }
        });
    });

    observer.observe(element, {
        attributes: true
    });
}
