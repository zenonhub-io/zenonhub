/**
 * Plugin base abstract.
 *
 * This class provides the base functionality for all plugins.
 */
export default class PluginBase {
    /**
     * Constructor.
     *
     * The constructor is provided the ZenonHub framework instance, and should not be overwritten
     * unless you absolutely know what you're doing.
     *
     * @param {ZenonHub} zenonHub
     */
    constructor(zenonHub) {
        this.zenonHub = zenonHub;
    }

    /**
     * Plugin constructor.
     *
     * This method should be treated as the true constructor of a plugin, and can be overwritten.
     * It will be called straight after construction.
     */
    construct() {
    }

    /**
     * Defines the required plugins for this specific module to work.
     *
     * @returns {string[]} An array of plugins required for this module to work, as strings.
     */
    dependencies() {
        return [];
    }

    /**
     * Defines the listener methods for global events.
     *
     * @returns {Object}
     */
    listens() {
        return {};
    }

    /**
     * Plugin destructor.
     *
     * Fired when this plugin is removed. Can be manually called if you have another scenario for
     * destruction, ie. the element attached to the plugin is removed or changed.
     */
    destruct() {
        this.detach();
        delete this.zenonHub;
    }

    /**
     * Plugin destructor (old method name).
     *
     * Allows previous usage of the "destructor" method to still work.
     */
    destructor() {
        this.destruct();
    }
}
