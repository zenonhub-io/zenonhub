import { Livewire } from '../../vendor/livewire/livewire/dist/livewire.esm'
import ClipboardJS from "clipboard";
import {Popover, Tooltip} from "bootstrap";
import {formValidationHandler, syntaxHighlight} from "./helpers.js";

export default class Core {
    copiersList= null;
    tooltipList = null;
    popoverList = null;
    listeners = [];

    init() {
        this.initListeners();
        this.attachEvents();
        this.initPageControls();
    }

    initListeners() {
        document.addEventListener('livewire:navigating', () => {
            this.destroyPageControls();
        })

        document.addEventListener('livewire:navigated', () => {
            this.initPageControls();
        });
    }

    attachEvents() {
        Livewire.hook('commit', ({ component, commit, respond, succeed, fail }) => {
            succeed(({ snapshot, effect }) => {
                this.destroyPageControls();

                setTimeout(() => {
                    this.initPageControls();
                }, 1);
            })
        });
    }

    initPageControls() {
        this.onFormHandler();
        this.onCopiers();
        this.onTooltips();
        this.onPopovers();
        this.onSyntaxHighlight();
    }

    destroyPageControls() {
        this.offFormHandler();
        this.offCopiers();
        this.offTooltips();
        this.offPopovers();
    }

    onFormHandler() {
        const forms = document.querySelectorAll('.needs-validation')
        Array.from(forms).map(form => {
            form.addEventListener('submit', (event) => formValidationHandler(form, event))
        });
    }

    offFormHandler() {
        const forms = document.querySelectorAll('.needs-validation')
        Array.from(forms).map(form => {
            form.removeEventListener('submit', (event) => formValidationHandler(form, event), false)
        });
    }

    onCopiers() {
        const copiersTriggerList = document.querySelectorAll('.js-copy');
        this.copiersList = [...copiersTriggerList].map(copyTriggerEl => new ClipboardJS(copyTriggerEl));
    }

    offCopiers() {

        if (! this.copiersList) {
            return;
        }

        this.copiersList.forEach(copier => {
            copier.destroy();
        });
    }

    onTooltips() {
        const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
        this.tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new Tooltip(tooltipTriggerEl));
    }

    offTooltips() {

        if (! this.tooltipList) {
            return;
        }

        this.tooltipList.forEach(tooltip => {
            if (tooltip._element) {
                tooltip.dispose();
            }
        });
    }

    onPopovers() {
        const popoverTriggerList = document.querySelectorAll('[data-bs-toggle="popover"]');
        this.popoverList = [...popoverTriggerList].map(popoverTriggerEl => new Popover(popoverTriggerEl));
    }

    offPopovers() {

        if (! this.popoverList) {
            return;
        }

        this.popoverList.forEach(popover => {
            if (popover._element) {
                popover.dispose();
            }
        });
    }

    onSyntaxHighlight() {
        const highlight = document.querySelectorAll('pre > code.lang-json');
        [...highlight].map(function (highlightEl) {
            if (! highlightEl.classList.contains('highlighted')) {
                highlightEl.innerHTML = syntaxHighlight(highlightEl.innerHTML);
                highlightEl.classList.add('highlighted');
            }
        });
    }
}
