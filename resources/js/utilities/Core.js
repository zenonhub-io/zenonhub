import Singleton from '../abstracts/Singleton';
import {Tab, Tooltip, Popover} from 'bootstrap';
import ClipboardJS from "clipboard";

export default class Core extends Singleton {

    copiersList = null;
    tooltipList = null;

    listens() {
        return {
            ready: 'ready',
        };
    }

    ready() {
        //this.reopenTab();
        this.attachHandlers();
    }

    attachHandlers() {

        this.onPageGlobals();

        Livewire.hook('commit', ({ component, commit, respond, succeed, fail }) => {
            succeed(({ snapshot, effect }) => {
                this.offPageGlobals();

                setTimeout(() => {
                    this.onPageGlobals();
                }, 0);

                // queueMicrotask(() => {
                //     this.onPageGlobals();
                // })
            })
        });

        document.addEventListener('livewire:navigating', () => {
            this.offPageGlobals();
        })

        document.addEventListener('livewire:navigated', () => {
            this.onPageGlobals();
        })

        // window.livewire.on('refreshPage', refresh => {
        //     window.location.reload();
        // });
    }

    onPageGlobals() {
        this.onFormHandler();
        this.onScrollers();
        this.onTabs();
        this.onCopiers();
        this.onTooltips();
        this.onPopovers();
        this.onSyntaxHighlight();
    }

    offPageGlobals() {
        this.offFormHandler();
        this.offScrollers();
        this.offTabs();
        this.offCopiers();
        this.offTooltips();
        this.offPopovers();
    }

    reopenTab() {
        if (location.hash !== '') {
            let node = document.querySelector('[data-bs-toggle="tab"][href="' + location.hash + '"]');
            if (node) {
                let tab = Tab.getOrCreateInstance(node);
                tab.show();
            }
        }
    }

    onFormHandler() {
        const forms = document.querySelectorAll('.needs-validation')
        Array.from(forms).map(form => {
            form.addEventListener('submit', (event) => this.formHandler(form, event))
        });
    }

    offFormHandler() {
        const forms = document.querySelectorAll('.needs-validation')
        Array.from(forms).map(form => {
            form.removeEventListener('submit', (event) => this.formHandler(form, event), false)
        });
    }

    onScrollers() {
        const scrollers = document.querySelectorAll('.js-scroll');
        Array.from(scrollers).map(element => {
            element.addEventListener('click', (event) => this.scrollHandler());
        });
    }

    offScrollers() {
        const scrollers = document.querySelectorAll('.js-scroll');
        Array.from(scrollers).map(element => {
            element.removeEventListener('click', (event) => this.scrollHandler());
        });
    }

    onTabs() {
        const tabs = document.querySelectorAll('[data-bs-toggle="tab"]')
        Array.from(tabs).map(tab => {
            tab.addEventListener('shown.bs.tab', (event) => this.tabClickHandler(event))
        });
    }

    offTabs() {
        const tabs = document.querySelectorAll('[data-bs-toggle="tab"]')
        Array.from(tabs).map(tab => {
            tab.removeEventListener('shown.bs.tab', (event) => this.tabClickHandler(event))
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
                highlightEl.innerHTML = window.zenonHub.helpers().syntaxHighlight(highlightEl.innerHTML);
                highlightEl.classList.add('highlighted');
            }
        });
    }

    formHandler(form, event) {
        if (!form.checkValidity()) {
            event.preventDefault()
            event.stopPropagation()
        }
        form.classList.add('was-validated')
    }

    scrollHandler() {
        window.zenonHub.helpers().scrollToTop();
    }

    tabClickHandler(event) {
        // Replace url with tab hash
        let stateObject = { url: event.target.hash };
        window.history.replaceState(
            stateObject,
            document.title,
            window.location.pathname + window.location.search + event.target.hash
        );
    }
}
