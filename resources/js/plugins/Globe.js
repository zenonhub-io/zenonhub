import createGlobe from "cobe";

export default class Globe extends window.zenonHub.Singleton {

    init(canvasId, markers) {
        this.canvasId = canvasId;
        this.markers = markers;
        this.active = false;
        this.renderGlobe();
    }

    renderGlobe() {

        let canvas = document.getElementById(this.canvasId);

        if (! canvas) {
            return;
        }

        let pointerInteracting = null;
        let pointerInteractionMovement = 0;
        let phi = 0;
        const { width, height } = canvas.getBoundingClientRect();

        this.globe = createGlobe(canvas, {
            devicePixelRatio: 2,
            phi: 0,
            theta: 0.15,
            dark: 1,
            diffuse: 1,
            mapSamples: 18000,
            mapBrightness: 1,
            baseColor: [1, 1, 1],
            markerColor: [1 / 255, 213 / 255, 87 / 255],
            glowColor: [150 / 255, 150 / 255, 150 / 255],
            width: height * 2,
            height: height * 2,
            markers: this.markers,
            onRender: (state) => {
                // This prevents rotation while dragging
                if (! pointerInteracting) {
                    phi += 0.002
                }
                state.phi = phi + pointerInteractionMovement / 200;

                const { width, height } = canvas.getBoundingClientRect();
                state.width = height * 2;
                state.height = height * 2;
            },
        });

        this.active = true;

        canvas.onpointerdown = (e) => {
            pointerInteracting = e.clientX - pointerInteractionMovement;
            canvas.style.cursor = 'grabbing';
        };

        canvas.onpointerup = () => {
            pointerInteracting = null;
            canvas.style.cursor = 'grab';
        };

        canvas.onpointerout = () => {
            pointerInteracting = null;
            canvas.style.cursor = 'grab';
        };

        canvas.onmousemove = (e) => {
            if (pointerInteracting !== null) {
                pointerInteractionMovement = e.clientX - pointerInteracting;
            }
        };

        canvas.ontouchmove = (e) => {
            if (pointerInteracting !== null && e.touches[0]) {
                pointerInteractionMovement = e.touches[0].clientX - pointerInteracting;
            }
        };
    }

    destroyGlobe() {
        if (this.active) {
            this.globe.destroy();
            this.active = false;
        }
    }
}
