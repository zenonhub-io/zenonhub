import Singleton from '../abstracts/Singleton';

export default class LoadingScreen extends Singleton {

    listens() {
        return {
            ready: 'ready',
        };
    }

    ready() {
        const preloader = document.querySelector('.page-loading');
        preloader.classList.remove('active');
        setTimeout(function () {
            preloader.remove();
        }, 1000);
    }
}
