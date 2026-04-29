import { PostModule } from './modules/posts/postModule.js';
import { LoginModule } from './modules/auth/loginModule.js';
import { Config } from './utils/config.js';

export class App {
    constructor() {
        this.config = new Config();
    }

    async init() {
        await this.config.loadConfig();
        this.initPage(window.location.pathname);
    }

    initPage(path) {
        if (path === '/' || path === '/home' || path === '/home/') {
            const container = document.querySelector('.content');
            const postModule = new PostModule(container, this.config.getConfig());
            postModule.init();
        }
        if (path === '/login' || path === '/login/') {
            const container = document.querySelector('.login-form');
            const loginModule = new LoginModule(container, this.config.getConfig());
            loginModule.init();
        }
    }
}

document.addEventListener('DOMContentLoaded', async () => {
    const app = new App();
    await app.init();
});