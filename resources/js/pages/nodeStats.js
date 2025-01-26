import jsVectorMap from 'jsvectormap'
import 'jsvectormap/dist/maps/world'
import {docReady} from "../helpers.js";

export default class NodeStats {

    map = null;

    init() {
        this.initListeners();
        this.attachEvents();
        this.loadNodeMap();
    }

    initListeners() {

        document.addEventListener('livewire:navigating', () => {
            this.destroyNodeMap();
        })

        document.addEventListener('livewire:navigated', () => {
            this.loadNodeMap();
        });
    }

    attachEvents() {
        window.addEventListener('resize', () => {
            this.map.updateSize()
        })
    }

    loadNodeMap() {
        const mapElement = document.getElementById('js-node-map');
        const mapMarkers = mapElement.getAttribute('data-markers');
        const colours = JSON.parse(document.querySelector('meta[name="colours"]').content)

        this.map = new jsVectorMap({
            selector: "#js-node-map",
            map: "world",
            //showTooltip: false,
            zoomOnScroll: false,
            markers: JSON.parse(mapMarkers),
            markerStyle: {
                initial: {
                    fill: colours['zenon-green'],
                },
                hover: {
                    fill: colours['zenon-green'],
                },
            },
        });
    }

    destroyNodeMap() {
        if (this.map) {
            this.map.destroy()
        }
    }
}

(function() {
    docReady(function () {
        (new NodeStats).init();
    });
})();
