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

    syntaxHighlight(json) {
        if (typeof json != 'string') {
            json = JSON.stringify(json, undefined, 2);
        }
        json = json.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
        return json.replace(/("(\\u[a-zA-Z0-9]{4}|\\[^u]|[^\\"])*"(\s*:)?|\b(true|false|null)\b|-?\d+(?:\.\d*)?(?:[eE][+\-]?\d+)?)/g, function (match) {
            let cls = 'number';
            if (/^"/.test(match)) {
                if (/:$/.test(match)) {
                    cls = 'key';
                } else {
                    cls = 'string';
                }
            } else if (/true|false/.test(match)) {
                cls = 'boolean';
            } else if (/null/.test(match)) {
                cls = 'null';
            }
            return '<span class="json-' + cls + '">' + match + '</span>';
        });
    }
}
