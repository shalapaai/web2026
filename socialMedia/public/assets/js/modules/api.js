export class Api {
    constructor(config) {
        this.baseURL = config?.apiBase;
    }

    async request(endpoint, options = {}) {
        const headers = {
            'Content-Type': 'application/json',
            ...options.headers
        };

        const response = await fetch(`${this.baseURL}${endpoint}`, {
            ...options,
            headers
        });
        
        if (!response.ok) {
            const error = await response.json().catch(() => ({}));
            throw new Error(error.message || `HTTP ${response.status}: ${response.statusText}`);
        }

        const rawText = await response.text();
        return rawText ? JSON.parse(rawText) : {};
    }

    // 👇 Posts — просто методы, возвращающие промисы
    getAllPosts() {
        return this.request(`/posts`);
    }

    getPostById(id) {
        return this.request(`/post?id=${id}`);
    }

    getPostsByAuthorId(authorId) {
        return this.request(`/posts?userId=${authorId}`);
    }

    createPost(data) {
        return this.request('/posts', { 
            method: 'POST', 
            body: JSON.stringify(data) 
        });
    }

    // 👇 Users
    getAllUsers() {
        return this.request(`/users`);
    }

    getUserById(id) {
        return this.request(`/user?id=${id}`);
    }
}