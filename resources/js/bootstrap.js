/**
 * The axios HTTP library is used by a variety of first-party Laravel packages
 * like Inertia in order to make requests to the Laravel backend. This will
 * automatically handle sending the CSRF token via a header based on the
 * value of the "XSRF" token cookie sent with previous HTTP responses.
 */
import axios from 'axios';
import * as bootstrap from 'bootstrap';

import '../../vendor/rappasoft/laravel-livewire-tables/resources/imports/laravel-livewire-tables.js';
//import './vendor/livewire-charts/app.js';

// import ApexCharts from 'apexcharts'
// import areaChart from "./vendor/livewire-charts/areaChart"
// import columnChart from "./vendor/livewire-charts/columnChart"
// import multiColumnChart from "./vendor/livewire-charts/multiColumnChart"
// import lineChart from "./vendor/livewire-charts/lineChart"
// import multiLineChart from "./vendor/livewire-charts/multiLineChart"
// import pieChart from "./vendor/livewire-charts/pieChart"
// import radarChart from "./vendor/livewire-charts/radarChart"
// import treeMapChart from "./vendor/livewire-charts/treeMapChart"
// import radialChart from "./vendor/livewire-charts/radialChart"

window.axios = axios;
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
window.bootstrap = bootstrap;


