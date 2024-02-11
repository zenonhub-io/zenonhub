export default class Wallet extends window.zenonHub.Singleton {

    /**
     * Construct
     */
    construct() {}

    /**
     * Listeners.
     *
     * @returns {Object}
     */
    listens() {
        return {
            ready: 'ready',
        };
    }

    /**
     * Ready event callback.
     *
     * Attaches handlers to the window to listen for all request interactions.
     */
    ready() {
        this.attachHandlers();
        this.zenonHub.log('wallet plugin loaded');
    }

    /**
     * Attaches the necessary handlers for all request interactions.
     */
    attachHandlers() {

        // window.addEventListener("message", (event) => {
        //     try{
        //         const parsedData = event.data;
        //
        //         console.log(parsedData);
        //
        //         if (parsedData.method){
        //             switch(parsedData.method){
        //                 case "znn.grantedWalletRead":{
        //                     const result = JSON.stringify(parsedData.data);
        //                     break;
        //                 }
        //                 case "znn.signedTransaction": {
        //                     const result = JSON.stringify(parsedData.data);
        //                     break;
        //                 }
        //                 case "znn.znn.accountBlockSent": {
        //                     const result = JSON.stringify(parsedData.data);
        //                     break;
        //                 }
        //             }
        //         }
        //     }
        //     catch(err){
        //         console.error(err);
        //     }
        // });

        // document.getElementById("connect-wallet")
        //     .addEventListener("click", function () {
        //         window.postMessage({
        //             method: "znn.requestWalletAccess",
        //             params: {}
        //         }, "*");
        //     });

    }
}
