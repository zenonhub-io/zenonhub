import Charts from "../plugins/Charts";
import Globe from "../plugins/Globe";

if (window.zenonHub === undefined) {
    throw new Error('ZenonHub must be loaded in order to use the node statistics.');
}

export default class NodeStats extends window.zenonHub.Singleton {

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
        this.attachHandlers();
    }

    /**
     * Attaches the necessary handlers for all request interactions.
     */
    attachHandlers() {

        window.livewire.hook('message.received', (message, component) => {
            this.offMap();
            this.offCountries();
            this.offCities();
            this.offNetworks();
            this.offVersions();
        });

        Livewire.on('stats.nodes.mapDataLoaded', data => {
            this.onMap(data);
        });

        Livewire.on('stats.nodes.countriesDataLoaded', data => {
            this.onCountries(data);
        });

        Livewire.on('stats.nodes.citiesDataLoaded', data => {
            this.onCities(data);
        });

        Livewire.on('stats.nodes.networksDataLoaded', data => {
            this.onNetworks(data);
        });

        Livewire.on('stats.nodes.versionsDataLoaded', data => {
            this.onVersions(data);
        });
    }

    onMap(data) {
        window.zenonHub.globe().init('js-node-map', data);
    }

    offMap() {
        window.zenonHub.globe().destroyGlobe();
    }

    onCountries(data) {
        window.zenonHub.charts().renderChart(
            document.getElementById("chart-node-countries"),
            {
                series: data['data'],
                labels: data['labels'],
                colors: window.zenonHub.charts().getColourGradient(data['data'].length),
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

    onCities(data) {
        window.zenonHub.charts().renderChart(
            document.getElementById("chart-node-cities"),
            {
                series: [{
                    name: "Nodes",
                    data: data['data']
                }],
                labels: data['labels'],
                colors: window.zenonHub.charts().getColourGradient(data['data'].length),
                chart: {
                    type: 'bar',
                    foreColor: 'rgba(255, 255, 255, .7)',
                    height: '800px',
                    toolbar: {
                        show: false,
                    }
                },
                xaxis: {
                    categories: data['labels'],
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

    onNetworks(data) {
        window.zenonHub.charts().renderChart(
            document.getElementById("chart-node-networks"),
            {
                series: data['data'],
                labels: data['labels'],
                colors: window.zenonHub.charts().getColourGradient(data['data'].length),
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

    onVersions(data) {
        window.zenonHub.charts().renderChart(
            document.getElementById("chart-node-versions"),
            {
                series: data['data'],
                labels: data['labels'],
                colors: ['rgba(34, 197, 94, 0.94)', 'rgba(66, 119, 255, 0.94)', 'rgba(141, 44, 44, 0.94)'],
                chart: {
                    type: 'donut',
                    foreColor: 'rgba(255, 255, 255, .7)'
                },
                legend: {
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
                            height: 650
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
            'nodeVersions',
        );
    }

    offVersions() {
        window.zenonHub.charts().destroyChart('nodeVersions');
    }
}

((ZenonHub) => {
    ZenonHub.addPlugin('charts', Charts);
    ZenonHub.addPlugin('globe', Globe);
    ZenonHub.addPlugin('nodeStats', NodeStats);
})(window.zenonHub);
