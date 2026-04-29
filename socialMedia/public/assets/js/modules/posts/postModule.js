import { Api } from "../api.js";
import { PostRenderer } from "./postRenderer.js";

export class PostModule {
    constructor(container, config) {
        this.container = container;
        this.sliders = new Map();
        this.PostRenderer = new PostRenderer(this.container);
        this.api = new Api(config);
    }

    async init() {
        const params = new URLSearchParams(window.location.search);
        const postId = params.get('postId');
        let posts;
        let users;
        if (postId) {
            posts = [(await this.fetchPost(postId)).data]; 
            // users = [(await this.fetchUser(posts[0].userId)).data]; 
            users = (await this.fetchUsers()).data; 
        } else {
            posts = (await this.fetchPosts()).data; 
            users = (await this.fetchUsers()).data; 
        }
        
        posts.forEach(post => {
            const author = users.find(u => u.id === post.authorId);
            this.PostRenderer.renderPost(post, author);
        });
    }

    async fetchPosts() {
        return await this.api.getAllPosts();
    }

    async fetchPost(id) {
        return await this.api.getPostById(id);
    }

    async fetchUsers() {
        return await this.api.getAllUsers();
    }

    async fetchUser(id) {
        return await this.api.getUserById(id);
    } 
}