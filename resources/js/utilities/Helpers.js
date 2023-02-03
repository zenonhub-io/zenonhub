import Singleton from "../abstracts/Singleton";

export default class Helpers extends Singleton {

    scrollToTop() {
        let element = document.querySelector('.js-scroll-to');

        if(element) {
            let bodyRect = document.body.getBoundingClientRect(),
                elemRect = element.getBoundingClientRect(),
                offset   = (elemRect.top - bodyRect.top) - 40;

            window.scroll({
                top: offset,
                left: 0,
                behavior: 'smooth'
            });
        }
    }

    scrollToBottom(element) {
        element.scrollIntoView(false);
    }
}
