import ApexCharts from "apexcharts";
import Gradient from "javascript-color-gradient";

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

    getColourGradient(length) {
        return new Gradient()
            .setColorGradient('#6FF34D', '#0061EB', '#F91690')
            .setMidpoint(length)
            .getColors();
    }

    destroyChart(id) {
        if (typeof this.charts[id] !== "undefined") {
            this.charts[id].destroy();
        }
    }
}
