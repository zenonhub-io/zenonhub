export default class AcceleratorStats {

    init() {
        this.initListeners();
        this.attachEvents();
    }

    initListeners() {}

    attachEvents() {}

    // attachHandlers() {
    //
    //     window.livewire.hook('message.received', (message, component) => {
    //         this.offFunding();
    //         this.offProjects();
    //     });
    //
    //     Livewire.on('stats.az.fundingDataLoaded', data => {
    //         this.onFunding(data);
    //     });
    //
    //     Livewire.on('stats.az.projectDataLoaded', data => {
    //         this.onProjects(data);
    //     });
    // }
    //
    // onFunding(data) {
    //
    //     window.zenonHub.charts().renderChart(
    //         document.getElementById("chart-az-funding-znn"),
    //         {
    //             series: data['znn']['data'],
    //             labels: data['znn']['labels'],
    //             colors: ['rgba(111, 243, 77, 0.8)', 'rgba(255, 255, 255, 0.14)'],
    //             chart: {
    //                 type: 'donut',
    //                 foreColor: 'rgba(255, 255, 255, .7)'
    //             },
    //             tooltip: {
    //                 y: {
    //                     formatter: function(value) {
    //                         return (value).toLocaleString(undefined, {
    //                             minimumFractionDigits: 0
    //                         });
    //                     },
    //                 },
    //             },
    //             legend: {
    //                 show: false,
    //                 position: 'bottom',
    //                 labels: {
    //                     useSeriesColors: false
    //                 },
    //                 itemMargin: {
    //                     horizontal: 8,
    //                     vertical: 8
    //                 },
    //                 markers: {
    //                     width: 16,
    //                     height: 16,
    //                     radius: 2,
    //                     offsetY: 0
    //                 },
    //             },
    //             stroke:{
    //                 show: false,
    //             },
    //             states: {
    //                 hover: {
    //                     filter: {
    //                         type: 'none',
    //                     }
    //                 },
    //             },
    //             dataLabels: {
    //                 style: {
    //                     colors: ['rgba(255, 255, 255, .8)'],
    //                 },
    //                 dropShadow: {
    //                     enabled: false,
    //                 }
    //             },
    //             responsive: [{
    //                 breakpoint: 576,
    //                 options: {
    //                     chart: {
    //                         height: 280
    //                     },
    //                     stroke:{
    //                         width: 2,
    //                         colors:['#191818'],
    //                     },
    //                 }
    //             }],
    //             plotOptions: {
    //                 pie: {
    //                     expandOnClick: false,
    //                     donut: {
    //                         size: '45%',
    //                         labels: {
    //                             show: false,
    //                             total: {
    //                                 show: false,
    //                             },
    //                         }
    //                     }
    //                 }
    //             }
    //         },
    //         'azFundingZnn',
    //     );
    //
    //     window.zenonHub.charts().renderChart(
    //         document.getElementById("chart-az-funding-qsr"),
    //         {
    //             series: data['qsr']['data'],
    //             labels: data['qsr']['labels'],
    //             colors: ['rgba(0, 97, 235, 0.8)', 'rgba(255, 255, 255, 0.14)'],
    //             chart: {
    //                 type: 'donut',
    //                 foreColor: 'rgba(255, 255, 255, .7)'
    //             },
    //             tooltip: {
    //                 y: {
    //                     formatter: function(value) {
    //                         return (value).toLocaleString(undefined, {
    //                             minimumFractionDigits: 0
    //                         });
    //                     },
    //                 },
    //             },
    //             legend: {
    //                 show: false,
    //                 position: 'bottom',
    //                 labels: {
    //                     useSeriesColors: false
    //                 },
    //                 itemMargin: {
    //                     horizontal: 8,
    //                     vertical: 8
    //                 },
    //                 markers: {
    //                     width: 16,
    //                     height: 16,
    //                     radius: 2,
    //                     offsetY: 0
    //                 },
    //             },
    //             stroke:{
    //                 show: false,
    //             },
    //             states: {
    //                 hover: {
    //                     filter: {
    //                         type: 'none',
    //                     }
    //                 },
    //             },
    //             dataLabels: {
    //                 style: {
    //                     colors: ['rgba(255, 255, 255, .8)'],
    //                 },
    //                 dropShadow: {
    //                     enabled: false,
    //                 }
    //             },
    //             responsive: [{
    //                 breakpoint: 576,
    //                 options: {
    //                     chart: {
    //                         height: 280
    //                     },
    //                     stroke:{
    //                         width: 2,
    //                         colors:['#191818'],
    //                     },
    //                 }
    //             }],
    //             plotOptions: {
    //                 pie: {
    //                     expandOnClick: false,
    //                     donut: {
    //                         size: '45%',
    //                         labels: {
    //                             show: false,
    //                             total: {
    //                                 show: false,
    //                             },
    //                         }
    //                     }
    //                 }
    //             }
    //         },
    //         'azFundingQsr',
    //     );
    // }
    //
    // offFunding() {
    //     window.zenonHub.charts().destroyChart('azFundingZnn');
    //     window.zenonHub.charts().destroyChart('azFundingQsr');
    // }
    //
    // onProjects(data) {
    //     window.zenonHub.charts().renderChart(
    //         document.getElementById("chart-az-project-totals"),
    //         {
    //             series: data['data'],
    //             labels: data['labels'],
    //             colors: ['rgba(255, 255, 255, 0.8)', 'rgba(66, 119, 255, 0.8)', 'rgba(34, 197, 94, 0.8)', 'rgba(141, 44, 44, 0.8)'],
    //             chart: {
    //                 type: 'donut',
    //                 foreColor: 'rgba(255, 255, 255, .7)'
    //             },
    //             legend: {
    //                 show: false,
    //                 position: 'top',
    //                 labels: {
    //                     useSeriesColors: false
    //                 },
    //                 itemMargin: {
    //                     horizontal: 8,
    //                     vertical: 8
    //                 },
    //                 markers: {
    //                     width: 16,
    //                     height: 16,
    //                     radius: 2,
    //                     offsetY: 0
    //                 },
    //             },
    //             stroke:{
    //                 width: 4,
    //                 colors:['#191818'],
    //             },
    //             states: {
    //                 hover: {
    //                     filter: {
    //                         type: 'none',
    //                     }
    //                 },
    //             },
    //             dataLabels: {
    //                 style: {
    //                     colors: ['rgba(255, 255, 255, .8)'],
    //                 },
    //                 dropShadow: {
    //                     enabled: false,
    //                 }
    //             },
    //             responsive: [{
    //                 breakpoint: 576,
    //                 options: {
    //                     chart: {
    //                         height: 280
    //                     },
    //                     stroke:{
    //                         width: 2,
    //                         colors:['#191818'],
    //                     },
    //                 }
    //             }],
    //             plotOptions: {
    //                 pie: {
    //                     expandOnClick: false,
    //                     donut: {
    //                         size: '45%',
    //                         labels: {
    //                             show: true,
    //                             name: {
    //                                 show: true,
    //                                 color: 'rgba(255, 255, 255, .7)',
    //                             },
    //                             total: {
    //                                 show: true,
    //                                 color: 'rgba(255, 255, 255, .7)',
    //                                 formatter: function (w) {
    //                                     return w.globals.seriesTotals.reduce(function(previousValue, currentValue){
    //                                         return currentValue + previousValue;
    //                                     });
    //                                 }
    //                             },
    //                         }
    //                     }
    //                 }
    //             }
    //         },
    //         'azProjectTotals',
    //     );
    // }
    //
    // offProjects() {
    //     window.zenonHub.charts().destroyChart('azProjectTotals');
    // }
}
