import { PostModule } from './modules/posts/postModule.js';
import { LoginModule } from './modules/auth/loginModule.js';
import { CreatePostModule } from './modules/posts/createPostModule.js';
import { Config } from './utils/config.js';
import { EditPostModule } from './modules/posts/editPostModule.js';
import { EditProfileModule } from './modules/posts/editProfileModule.js';
import { Api } from './modules/api.js';

export class App {
    constructor() {
        this.config = new Config();
        this.api;
        this.currUser; 
    }

    async init() {
        await this.config.loadConfig();
        this.api = new Api(this.config.getConfig());
        try {
            this.currUser = (await this.api.getSignedUser()).data; 
        } catch {
            console.log('Not authtorizes');
        }
        this.initPage(window.location.pathname);
    }

    async initPage(path) {
        if (path === '/' || path === '/home' || path === '/home/') {
            const container = document.querySelector('.content');
            const postModule = new PostModule(container, this.config.getConfig());
            postModule.init();
        }
        if (path === '/login' || path === '/login/' || path === '/register' || path === '/register/') {
            const container = document.querySelector('.login-form');
            const loginModule = new LoginModule(container, this.config.getConfig(), path);
            loginModule.init();
        }
        if (path === '/create' || path === '/create/') {
            const container = document.querySelector('.input-field');
            const createPostModule = new CreatePostModule(container, this.config.getConfig());
            createPostModule.init();
        }
        if (path === '/edit/profile' || path === '/edit/profile/') {
            const container = document.querySelector('.login-form');
            const params = new URLSearchParams(window.location.search);
            const userId = params.get('id');
            if (this.currUser.id !== userId) {
                window.location.href = '/home/';
                return;
            }
            const response = await this.api.getUserById(userId);
            if (!response?.success || !response?.data) {
                throw new Error('Failed to load user data');
            }
            const user = response.data;
            const config = {
                ...this.config.getConfig(),
                currentAvatarUrl: user.avatar || null 
            };
            const editProfileModule = new EditProfileModule(container, config, path);
            await editProfileModule.init();
        }
        if (path === '/edit' || path === '/edit/') {
            const container = document.querySelector('.input-field');
            const params = new URLSearchParams(window.location.search);
            const postId = params.get('postId');
            if (!postId) {
                console.error('EditPostModule: postId not found in URL');
                window.location.href = '/home/';
                return;
            }
            try {
                const api = new Api(this.config.getConfig());
                const response = await api.getPostById(postId);
                if (!response?.success || !response?.data) {
                    throw new Error('Failed to load post data');
                }
                const post = response.data;
                if (post.authorId !== this.currUser.id) {
                    window.location.href = '/home/';
                    return;
                }
                const editConfig = {
                    ...this.config.getConfig(),
                    postId: post.id,
                    content: post.content,
                    images: post.images || []  
                };
                const editPostModule = new EditPostModule(container, editConfig);
                await editPostModule.init();  
            } catch (err) {
                console.error('Failed to initialize EditPostModule:', err);
            }
        }
    }
}

document.addEventListener('DOMContentLoaded', async () => {
    const app = new App();
    await app.init();
});