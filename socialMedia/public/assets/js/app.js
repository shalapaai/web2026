import {HomePage} from './HomePage.js'

document.addEventListener('DOMContentLoaded', () => {
    const path = window.location.pathname;
    initPage(path);
    // initGlobalEvents();
});

function initPage(path) {
    const container = document.querySelector('#app') || document.querySelector('.content');
    
    if (!container) {
        console.warn('Container not found');
        return;
    }
    
    // Главная страница
    if (path === '/' || path === '/home' || path === '/home/') {
        const homePage = new HomePage(container);
        homePage.init();
    }
    // // Страница профиля
    // else if (path === '/profile') {
    //     const profilePage = new ProfilePage(container);
    //     profilePage.init();
    // }
    // // Страница создания поста
    // else if (path === '/create') {
    //     initCreatePostForm();
    // }
    // // Страница входа
    // else if (path === '/login') {
    //     initLoginForm();
    // }
}