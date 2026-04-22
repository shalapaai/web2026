// Предварительно создайте modules/api.js для централизации запросов
// modules/api.js
const API_BASE = 'http://localhost:80/api';

export async function request(endpoint, options = {}) {
    const headers = {
        'Content-Type': 'application/json',
        ...options.headers
    };
    const response = await fetch(`${API_BASE}${endpoint}`, {
        ...options,
        headers
    });
    
    if (!response.ok) {
        const error = await response.json().catch(() => ({}));
        throw new Error(error.message || `HTTP ${response.status}: ${response.statusText}`);
    }

    const rawText = await response.text();
    console.log('Raw response:', rawText); 
    return JSON.parse(rawText);
}

export const api = {
    posts: {
        getAllPosts: () => request(`/posts`),
        getPostById: (id) => request(`/post?id=${id}`),
        getPostsByAuthorId: (authorId) => request(`/posts?userId=${authorId}`),
        create: (data) => request('/posts', { method: 'POST', body: JSON.stringify(data) })
    },
    users: {
        getAllUsers: () => request(`/users`),
        getUserById: (id) => request(`/user?id=${id}`),
    }
};