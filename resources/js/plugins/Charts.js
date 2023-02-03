import ApexCharts from "apexcharts";

export default class Charts extends window.zenonHub.Singleton {

    /**
     * Construct
     */
    construct() {
        this.charts = {};
    }

    renderChart(element, options, id = false) {
        let chart = new ApexCharts(element, options);
        chart.render();

        if (id) {
            this.charts[id] = chart;
        } else {
            return chart;
        }
    }

    destroyChart(id) {
        if (typeof this.charts[id] !== "undefined") {
            this.charts[id].destroy();
        }
    }
}
