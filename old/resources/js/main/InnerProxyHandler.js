/**
 * Internal proxy for ZenonHub.
 *
 * This handler wraps the ZenonHub instance that is passed to the constructor of plugin instances.
 * It prevents access to the following methods:
 *  - `attachAbstracts`: No need to attach abstracts again.
 *  - `loadUtilties`: No need to load utilities again.
 *  - `initialise`: ZenonHub is already initialised.
 *  - `initialiseSingletons`: Singletons are already initialised.
 */
export default {
    get(target, prop, receiver) {
        if (typeof prop === 'string') {
            const propLower = prop.toLowerCase();

            if (['attachAbstracts', 'loadUtilities', 'initialise', 'initialiseSingletons'].includes(prop)) {
                throw new Error(`You cannot use the "${prop}" ZenonHub method within a plugin.`);
            }

            if (target.hasPlugin(propLower)) {
                return (...params) => Reflect.get(target, 'plugins')[propLower].getInstance(...params);
            }
        }

        return Reflect.get(target, prop, receiver);
    },

    has(target, prop) {
        if (typeof prop === 'string') {
            const propLower = prop.toLowerCase();

            if (['attachAbstracts', 'loadUtilities', 'initialise', 'initialiseSingletons'].includes(prop)) {
                return false;
            }

            if (target.hasPlugin(propLower)) {
                return true;
            }
        }

        return Reflect.has(target, prop);
    },
};
