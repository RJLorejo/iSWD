import './bootstrap';

import Alpine from 'alpinejs';

import ApexCharts from 'apexcharts';

window.Alpine = Alpine;

Alpine.start();



window.ApexCharts = ApexCharts;


document.addEventListener('DOMContentLoaded', function () {

    /*
    |--------------------------------------------------------------------------
    | MAINTENANCE CASE STATUS CHART
    |--------------------------------------------------------------------------
    */

    const caseStatusElement = document.querySelector(
        '#maintenanceCaseStatusChart'
    );

    if (caseStatusElement) {

        const caseStatusChart = new ApexCharts(
            caseStatusElement,
            {

                chart: {
                    type: 'bar',
                    height: 340,
                    toolbar: {
                        show: false
                    },
                    fontFamily: 'inherit'
                },

                series: [
                    {
data: [
    window.maintenanceDashboard.verified,
    window.maintenanceDashboard.unassigned,
    window.maintenanceDashboard.assigned,
    window.maintenanceDashboard.inProgress,
    window.maintenanceDashboard.review,
    window.maintenanceDashboard.completed
]
                    }
                ],

                xaxis: {
                    categories: [
                        'Verified',
                        'Unassigned',
                        'Assigned',
                        'In Progress',
                        'For Review',
                        'Completed'
                    ],

                    labels: {
                        style: {
                            fontSize: '11px'
                        }
                    }
                },

                yaxis: {

                    min: 0,

                    forceNiceScale: true,

                    labels: {
                        style: {
                            fontSize: '11px'
                        }
                    }

                },

                plotOptions: {

                    bar: {

                        borderRadius: 6,

                        columnWidth: '50%',

                        distributed: true

                    }

                },

                dataLabels: {
                    enabled: false
                },

                grid: {

                    borderColor: '#f1f5f9',

                    strokeDashArray: 4

                },

                legend: {
                    show: false
                },

                tooltip: {

                    y: {

                        formatter: function (value) {

                            return value + ' cases';

                        }

                    }

                }

            }
        );

        caseStatusChart.render();

    }


    /*
    |--------------------------------------------------------------------------
    | WORKFLOW DOUGHNUT CHART
    |--------------------------------------------------------------------------
    */

    const workflowElement = document.querySelector(
        '#maintenanceWorkflowChart'
    );

    if (workflowElement) {

        const workflowChart = new ApexCharts(
            workflowElement,
            {

                chart: {

                    type: 'donut',

                    height: 340,

                    fontFamily: 'inherit'

                },

series: [
    window.maintenanceDashboard.verified,
    window.maintenanceDashboard.unassigned,
    window.maintenanceDashboard.assigned,
    window.maintenanceDashboard.inProgress,
    window.maintenanceDashboard.review,
    window.maintenanceDashboard.completed
],

                labels: [
                    'Verified',
                    'Unassigned',
                    'Assigned',
                    'In Progress',
                    'For Review',
                    'Completed'
                ],

                legend: {

                    position: 'bottom',

                    fontSize: '12px',

                    markers: {

                        width: 8,

                        height: 8,

                        radius: 8

                    },

                    itemMargin: {

                        horizontal: 8,

                        vertical: 5

                    }

                },

                plotOptions: {

                    pie: {

                        donut: {

                            size: '68%',

                            labels: {

                                show: true,

                                total: {

                                    show: true,

                                    label: 'Total Cases',

                                    formatter: function () {

                                        return 28;

                                    }

                                }

                            }

                        }

                    }

                },

                dataLabels: {

                    enabled: false

                },

                stroke: {

                    width: 3,

                    colors: ['#ffffff']

                },

                tooltip: {

                    y: {

                        formatter: function (value) {

                            return value + ' cases';

                        }

                    }

                }

            }
        );

        workflowChart.render();

    }

});
