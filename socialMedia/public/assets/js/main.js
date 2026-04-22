import { PostModule } from './modules/postModule.js';

document.addEventListener('DOMContentLoaded', () => {
    const path = window.location.pathname;
    initPage(path);
});

function initPage(path) {
    if (path === '/' || path === '/home' || path === '/home/') {
        const container = document.querySelector('.content');
        const postModule = new PostModule(container);
        postModule.init();
    }
}