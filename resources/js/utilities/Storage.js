import Singleton from '../abstracts/Singleton';

export default class Storage extends Singleton {

    get(name) {
        const value = window.sessionStorage.getItem(name);
        return this.#parseValue(value)
    }

    put(name, value) {
        window.sessionStorage.setItem(name, this.#stringifyValue(value));
    }

    push(name, key, value) {
        let data = this.get(name);
        if (typeof data === 'object') {
            if (Array.isArray(data)) {
                data.push(key, this.#stringifyValue(value))
            } else {
                data[key] = this.#stringifyValue(value);
            }
        }
    }

    pull(name) {
        const item = this.get(name);
        this.remove(name);
        return item;
    }

    remove(name) {
        window.sessionStorage.removeItem(name);
    }

    clear() {
        window.sessionStorage.clear();
    }

    #stringifyValue(value) {
        if (typeof value === 'object') {
            value = JSON.stringify(value);
        }

        return value;
    }

    #parseValue(value) {

        const stringValue = String(value);

        if (stringValue === 'null') {
            return null;
        }

        if (stringValue === 'undefined') {
            return undefined;
        }

        if (stringValue.startsWith('base64:')) {
            const base64str = stringValue.replace(/^base64:/, '');
            const decoded = atob(base64str);
            return this.#parseValue(decoded);
        }

        // Boolean value
        if (['true', 'yes'].includes(stringValue.toLowerCase())) {
            return true;
        }
        if (['false', 'no'].includes(stringValue.toLowerCase())) {
            return false;
        }

        // Numeric value
        if (/^[-+]?[0-9]+(\.[0-9]+)?$/.test(stringValue)) {
            return Number(stringValue);
        }

        try {
            return JSON.parse(stringValue);
        } catch (e) {
            return (stringValue === '') ? true : stringValue;
        }
    }
}
