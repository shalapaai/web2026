export class Api {
    constructor(config) {
        this.baseURL = config?.apiBase;
    }

    async request(endpoint, options = {}) {
        const fullUrl = `${this.baseURL}${endpoint}`;
        const headers = {
            'Accept': 'application/json',
            ...options.headers  
        };
        if (!(options.body instanceof FormData)) {
            headers['Content-Type'] = 'application/json';
        }
        console.log('potions: ', options);
        const response = await fetch(fullUrl, {
            ...options,
            headers
        });
        const rawText = await response.text();
        if (!response.ok) {
            try {
                const error = JSON.parse(rawText);
                throw new Error(error.message || error.error || `HTTP ${response.status}`);
            } catch {
                console.error('Server error response:', rawText.substring(0, 200));
                throw new Error(rawText || `HTTP ${response.status}: ${response.statusText}`);
            }
        }
        if (!rawText.trim()) return {};
        try {
            return JSON.parse(rawText);
        } catch (e) {
            console.error('Failed to parse JSON:', rawText.substring(0, 200));
            throw new Error('Invalid JSON response from server');
        }
    }

    login(email, password) {
        return this.request('/login', {
            method: 'POST',
            body: JSON.stringify({ email, password }) 
        });
    }

    register(email, password) {
        return this.request('/register', {
            method: 'POST',
            body: JSON.stringify({ email, password }) 
        });
    }

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
        const formData = new FormData();
        formData.append('content', data.content);
        data.images.forEach((blob, index) => {
            const fileName = `post_${Date.now()}_${index}.jpg`;
            formData.append('images[]', blob, fileName);
        });
        return this.request('/create', { 
            method: 'POST', 
            body: formData,
            headers: {
                'Accept': 'application/json'
            }
        });
    }

    updatePost(postId, data) {
        const formData = new FormData();
        formData.append('content', data.content || '');
        if (data.newImages?.length) {
            data.newImages.forEach((blob, index) => {
                if (blob instanceof Blob) {
                    formData.append('images[]', blob, `post_${Date.now()}_${index}.jpg`);
                }
            });
        }
        if (data.existingImagePaths?.length) {
            formData.append('existing_images', JSON.stringify(data.existingImagePaths));
        }
        if (data.removedImagePaths?.length) {
            formData.append('removed_images', JSON.stringify(data.removedImagePaths));
        }
        return this.request(`/edit?postId=${postId}`, { 
            method: 'POST',
            body: formData,
            headers: { 'Accept': 'application/json' }
        });
    }

    toggleLike(postId) {
        return this.request(`/post/like?postId=${postId}`, {
            method: 'POST',
            credentials: 'include'  
        });
    }

    checkUserLike(postId) {
        try {
            const response = this.request(`/post/like/status?postId=${postId}`, {
                method: 'GET',
                credentials: 'include' 
            });
            console.log('res:', response);
            return response;
        } catch {
            return false; 
        }
    }

    getAllUsers() {
        return this.request(`/users`);
    }

    getUserById(id) {
        return this.request(`/user?id=${id}`);
    }

    getSignedUser() {
        return this.request(`/signed/user`);
    }

    editProfile(userId, data) {
        const formData = new FormData();
        formData.append('name', data.name || '');
        formData.append('profileStatus', data.profileStatus || '');
        console.log(data.profileStatus);
        if (data.avatar instanceof Blob) {
            formData.append('avatar', data.avatar, 'avatar.jpg');
        }
        if (data.existingAvatarPath) {
            formData.append('existingAvatarPath', data.existingAvatarPath);
        }

        console.log(data.avatar);
        
        return this.request(`/edit/profile?id=${userId}`, { 
            method: 'POST',  
            body: formData,
            headers: { 'Accept': 'application/json' }
        });
    }
}