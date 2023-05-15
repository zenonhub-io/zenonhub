import Charts from "../plugins/Charts";

if (window.zenonHub === undefined) {
    throw new Error('ZenonHub must be loaded in order to use the node statistics.');
}

export default class AcceleratorStats extends window.zenonHub.Singleton {

    activeTab;

    /**
     * Listeners.
     *
     * @returns {Object}
     */
    listens() {
        return {
            ready: 'ready',
        };
    }

    /**
     * Ready event callback.
     *
     * Attaches handlers to the window to listen for all request interactions.
     */
    ready() {
        this.initTab();
        this.attachHandlers();
    }

    /**
     * Attaches the necessary handlers for all request interactions.
     */
    attachHandlers() {

        window.livewire.on('tabChange', (tab) => {
            this.activeTab = tab
        });

        window.livewire.hook('message.received', (message, component) => {
            this.offFunding();
            this.offProjects();
            this.offEngagement();
        });

        window.livewire.hook('message.processed', (message, component) => {
            if(this.activeTab === 'funding') {
                this.onFunding();
            } else if(this.activeTab === 'projects') {
                this.onProjects();
            } else if(this.activeTab === 'engagement') {
                this.onEngagement();
            }
        });
    }

    initTab() {
        this.activeTab = window.zenonHub.getData('initialTab');

        if(this.activeTab === 'funding') {
            this.onFunding();
        }

        if(this.activeTab === 'projects') {
            this.onProjects();
        }

        if(this.activeTab === 'engagement') {
            this.onEngagement();
        }
    }

    onFunding() {

        window.zenonHub.charts().renderChart(
            document.getElementById("chart-az-funding-znn"),
            {
                series: window.zenonHub.getData('azFundingZnn'),
                labels: window.zenonHub.getData('azFundingZnnLabels'),
                colors: ['rgba(111, 243, 77, 0.8)', 'rgba(255, 255, 255, 0.14)'],
                chart: {
                    type: 'donut',
                    foreColor: 'rgba(255, 255, 255, .7)'
                },
                tooltip: {
                    y: {
                        formatter: function(value) {
                            return (value).toLocaleString(undefined, {
                                minimumFractionDigits: 0
                            });
                        },
                    },
                },
                legend: {
                    show: false,
                    position: 'bottom',
                    labels: {
                        useSeriesColors: false
                    },
                    itemMargin: {
                        horizontal: 8,
                        vertical: 8
                    },
                    markers: {
                        width: 16,
                        height: 16,
                        radius: 2,
                        offsetY: 0
                    },
                },
                stroke:{
                    show: false,
                },
                states: {
                    hover: {
                        filter: {
                            type: 'none',
                        }
                    },
                },
                dataLabels: {
                    style: {
                        colors: ['rgba(255, 255, 255, .8)'],
                    },
                    dropShadow: {
                        enabled: false,
                    }
                },
                responsive: [{
                    breakpoint: 576,
                    options: {
                        chart: {
                            height: 280
                        },
                        stroke:{
                            width: 2,
                            colors:['#191818'],
                        },
                    }
                }],
                plotOptions: {
                    pie: {
                        expandOnClick: false,
                        donut: {
                            size: '45%',
                            labels: {
                                show: false,
                                total: {
                                    show: false,
                                },
                            }
                        }
                    }
                }
            },
            'azFundingZnn',
        );

        window.zenonHub.charts().renderChart(
            document.getElementById("chart-az-funding-qsr"),
            {
                series: window.zenonHub.getData('azFundingQsr'),
                labels: window.zenonHub.getData('azFundingQsrLabels'),
                colors: ['rgba(0, 97, 235, 0.8)', 'rgba(255, 255, 255, 0.14)'],
                chart: {
                    type: 'donut',
                    foreColor: 'rgba(255, 255, 255, .7)'
                },
                tooltip: {
                    y: {
                        formatter: function(value) {
                            return (value).toLocaleString(undefined, {
                                minimumFractionDigits: 0
                            });
                        },
                    },
                },
                legend: {
                    show: false,
                    position: 'bottom',
                    labels: {
                        useSeriesColors: false
                    },
                    itemMargin: {
                        horizontal: 8,
                        vertical: 8
                    },
                    markers: {
                        width: 16,
                        height: 16,
                        radius: 2,
                        offsetY: 0
                    },
                },
                stroke:{
                    show: false,
                },
                states: {
                    hover: {
                        filter: {
                            type: 'none',
                        }
                    },
                },
                dataLabels: {
                    style: {
                        colors: ['rgba(255, 255, 255, .8)'],
                    },
                    dropShadow: {
                        enabled: false,
                    }
                },
                responsive: [{
                    breakpoint: 576,
                    options: {
                        chart: {
                            height: 280
                        },
                        stroke:{
                            width: 2,
                            colors:['#191818'],
                        },
                    }
                }],
                plotOptions: {
                    pie: {
                        expandOnClick: false,
                        donut: {
                            size: '45%',
                            labels: {
                                show: false,
                                total: {
                                    show: false,
                                },
                            }
                        }
                    }
                }
            },
            'azFundingQsr',
        );

    }

    offFunding() {
        window.zenonHub.charts().destroyChart('azFundingZnn');
        window.zenonHub.charts().destroyChart('azFundingQsr');
    }

    onProjects() {
        window.zenonHub.charts().renderChart(
            document.getElementById("chart-az-project-totals"),
            {
                series: window.zenonHub.getData('azProjectTotals'),
                labels: window.zenonHub.getData('azProjectTotalLabels'),
                colors: ['rgba(255, 255, 255, 0.8)', 'rgba(66, 119, 255, 0.8)', 'rgba(34, 197, 94, 0.8)', 'rgba(141, 44, 44, 0.8)'],
                chart: {
                    type: 'donut',
                    foreColor: 'rgba(255, 255, 255, .7)'
                },
                legend: {
                    show: false,
                    position: 'top',
                    labels: {
                        useSeriesColors: false
                    },
                    itemMargin: {
                        horizontal: 8,
                        vertical: 8
                    },
                    markers: {
                        width: 16,
                        height: 16,
                        radius: 2,
                        offsetY: 0
                    },
                },
                stroke:{
                    width: 4,
                    colors:['#191818'],
                },
                states: {
                    hover: {
                        filter: {
                            type: 'none',
                        }
                    },
                },
                dataLabels: {
                    style: {
                        colors: ['rgba(255, 255, 255, .8)'],
                    },
                    dropShadow: {
                        enabled: false,
                    }
                },
                responsive: [{
                    breakpoint: 576,
                    options: {
                        chart: {
                            height: 280
                        },
                        stroke:{
                            width: 2,
                            colors:['#191818'],
                        },
                    }
                }],
                plotOptions: {
                    pie: {
                        expandOnClick: false,
                        donut: {
                            size: '45%',
                            labels: {
                                show: true,
                                name: {
                                    show: true,
                                    color: 'rgba(255, 255, 255, .7)',
                                },
                                total: {
                                    show: true,
                                    color: 'rgba(255, 255, 255, .7)',
                                    formatter: function (w) {
                                        return w.globals.seriesTotals.reduce(function(previousValue, currentValue){
                                            return currentValue + previousValue;
                                        });
                                    }
                                },
                            }
                        }
                    }
                }
            },
            'azProjectTotals',
        );
    }

    offProjects() {
        window.zenonHub.charts().destroyChart('azProjectTotals');
    }

    onEngagement() {
        console.log('ready');
    }

    offEngagement() {
        window.zenonHub.charts().destroyChart('nodeCountries');
    }
}

((ZenonHub) => {
    ZenonHub.addPlugin('charts', Charts);
    ZenonHub.addPlugin('acceleratorStats', AcceleratorStats);
})(window.zenonHub);
