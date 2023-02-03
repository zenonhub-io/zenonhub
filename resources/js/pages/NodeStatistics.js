import Charts from "../plugins/Charts";
import Globe from "../plugins/Globe";
import Gradient from "javascript-color-gradient";

if (window.zenonHub === undefined) {
    throw new Error('ZenonHub must be loaded in order to use the node statistics.');
}

export default class NodeStatistics extends window.zenonHub.Singleton {

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
            this.offMap();
            this.offCountries();
            this.offCities();
            this.offNetworks();
        });

        window.livewire.hook('message.processed', (message, component) => {
            if(this.activeTab === 'map') {
                this.onMap();
            } else if(this.activeTab === 'countries') {
                this.onCountries();
            } else if(this.activeTab === 'cities') {
                this.onCities();
            } else if(this.activeTab === 'networks') {
                this.onNetworks();
            }
        });
    }

    initTab() {
        this.activeTab = window.zenonHub.getData('initialTab');

        if(this.activeTab === 'map') {
            this.onMap();
        }

        if(this.activeTab === 'countries') {
            this.onCountries();
        }

        if(this.activeTab === 'cities') {
            this.onCities();
        }

        if(this.activeTab === 'networks') {
            this.onNetworks();
        }
    }

    onMap() {
        window.zenonHub.globe().init(window.zenonHub.getData('nodeMapCanvasId'), window.zenonHub.getData('nodeMapMarkers'));
    }

    offMap() {
        window.zenonHub.globe().destroyGlobe();
    }

    onCountries() {
        const gradientArray = new Gradient()
            .setColorGradient('#6FF34D', '#0061EB', '#F91690')
            .setMidpoint(window.zenonHub.getData('nodeCountriesSeries').length)
            .getColors();

        window.zenonHub.charts().renderChart(
            document.getElementById("chart-node-countries"),
            {
                series: window.zenonHub.getData('nodeCountriesSeries'),
                labels: window.zenonHub.getData('nodeCountriesLabels'),
                colors: gradientArray,
                chart: {
                    type: 'donut',
                    foreColor: 'rgba(255, 255, 255, .7)'
                },
                legend: {
                    position: 'right',
                    labels: {
                        useSeriesColors: false
                    },
                    itemMargin: {
                        horizontal: 4,
                        vertical: 4
                    },
                    markers: {
                        width: 16,
                        height: 16,
                        radius: 2,
                        offsetY: 3
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
                            height: 450
                        },
                        legend: {
                            position: 'bottom',
                            itemMargin: {
                                horizontal: 8,
                                vertical: 8
                            },
                            markers: {
                                offsetY: 0
                            },
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
                                        return w.globals.seriesTotals.length
                                    }
                                },
                            }
                        }
                    }
                }
            },
            'nodeCountries',
        );
    }

    offCountries() {
        window.zenonHub.charts().destroyChart('nodeCountries');
    }

    onCities() {
        const gradientArray = new Gradient()
            .setColorGradient('#6FF34D', '#0061EB', '#F91690')
            .setMidpoint(window.zenonHub.getData('nodeCitiesSeries').length)
            .getColors();

        window.zenonHub.charts().renderChart(
            document.getElementById("chart-node-cities"),
            {
                series: [{
                    name: "Nodes",
                    data: window.zenonHub.getData('nodeCitiesSeries')
                }],
                labels: window.zenonHub.getData('nodeCitiesLabels'),
                chart: {
                    type: 'bar',
                    foreColor: 'rgba(255, 255, 255, .7)',
                    height: '800px',
                    toolbar: {
                        show: false,
                    }
                },
                colors: gradientArray,
                xaxis: {
                    categories: window.zenonHub.getData('nodeCitiesLabels'),
                    labels: {
                        show: false
                    },
                    axisTicks: {
                        show: false,
                    },
                },
                yaxis: {
                    labels: {
                        show: true
                    },
                },
                legend: {
                    show: false,
                    position: 'bottom',
                    labels: {
                        useSeriesColors: false
                    },
                    itemMargin: {
                        horizontal: 4,
                        vertical: 4
                    },
                    markers: {
                        width: 16,
                        height: 16,
                        radius: 2,
                        offsetY: 0
                    },
                },
                tooltip: {
                    theme: "dark",
                    y: {
                        show: false,
                        formatter: function(value, { seriesIndex, dataPointIndex, w }) {
                            return value;
                        }
                    },
                    x: {
                        formatter: function(value, { seriesIndex, dataPointIndex, w }) {
                            return value;
                        }
                    }
                },
                grid: {
                    show: false,
                    borderColor: 'rgba(255, 255, 255, 0.14)',
                },
                plotOptions: {
                    bar: {
                        horizontal: true,
                        distributed: true,
                        barHeight: '80%',
                        borderRadius: 2,
                    }
                },
            },
            'nodeCities',
        );
    }

    offCities() {
        window.zenonHub.charts().destroyChart('nodeCities');
    }

    onNetworks() {
        const gradientArray = new Gradient()
            .setColorGradient('#6FF34D', '#0061EB', '#F91690')
            .setMidpoint(window.zenonHub.getData('nodeNetworkSeries').length)
        .getColors();

        window.zenonHub.charts().renderChart(
            document.getElementById("chart-node-networks"),
            {
                series: window.zenonHub.getData('nodeNetworkSeries'),
                labels: window.zenonHub.getData('nodeNetworkLabels'),
                colors: gradientArray,
                chart: {
                    type: 'donut',
                    foreColor: 'rgba(255, 255, 255, .7)'
                },
                legend: {
                    position: 'right',
                    labels: {
                        useSeriesColors: false
                    },
                    itemMargin: {
                        horizontal: 4,
                        vertical: 4
                    },
                    markers: {
                        width: 16,
                        height: 16,
                        radius: 2,
                        offsetY: 3
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
                            height: 650
                        },
                        legend: {
                            position: 'bottom',
                            itemMargin: {
                                horizontal: 8,
                                vertical: 8
                            },
                            markers: {
                                offsetY: 0
                            },
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
                                        return w.globals.seriesTotals.length
                                    }
                                },
                            }
                        }
                    }
                }
            },
            'nodeNetworks',
        );
    }

    offNetworks() {
        window.zenonHub.charts().destroyChart('nodeNetworks');
    }
}

((ZenonHub) => {
    ZenonHub.addPlugin('charts', Charts);
    ZenonHub.addPlugin('globe', Globe);
    ZenonHub.addPlugin('nodeStatistics', NodeStatistics);
})(window.zenonHub);
