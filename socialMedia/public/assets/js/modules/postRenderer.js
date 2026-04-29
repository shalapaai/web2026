import { Slider } from '../ui/Slider.js';
import { Pluralize } from '../utils/pluralize.js';
import { ModalWindow } from '../ui/ModalWindow.js';

export class PostRenderer {
    constructor(container, posts = []) {
        this.container = container;
        this.pluralize = new Pluralize;
        this.posts = new Map(posts.map(p => [p.id, p]));
        this.container.addEventListener('click', (e) => this._onContainerClick(e));
    }

    _onContainerClick(e) {
        console.log('Clicked');
        const postDiv = e.target.closest('.post');
        if (!postDiv) return;
        
        const img = e.target.closest('.post-content__image');
        if (!img) return;
        
        if (e.target.closest('.post-content__arrow')) return;
        
        // Собираем данные для модалки
        const postId = postDiv.dataset.postId;
        console.log(postId);

        const postImages = postDiv._postImages;
        if (!postImages) {
            console.log('no images');
            return;
        }
        const fullImages = postImages.map(p => `/uploads/posts${p}`);
    
        const currentSrc = img.src;
        const startIndex = postImages.findIndex(src => 
            currentSrc.includes(src.split('/').pop())
        );
        
        const modal = new ModalWindow({
            images: fullImages,
            startIndex: startIndex >= 0 ? startIndex : 0
        });
        modal.open();
    }

    renderPost(post, author) {
        const isOwner = true;
        
        const postDiv = document.createElement('div');
        postDiv.className = 'post';
        postDiv.dataset.postId = post.id;
        postDiv._postImages = post.images || [];
        
        postDiv.innerHTML = `
            <div class="header">
                <a class="header__user" href="/profile?id=${author?.id}" title="Профиль">
                    <img class="header__avatar" 
                            src="/uploads/avatars${author?.avatar}" 
                            alt="Аватар" 
                            width="32" height="32"
                            onerror="this.src='/assets/images/default-avatar.png'">
                    <span class="header__user-name">${author?.name || 'Аноним'}</span>
                </a>
                ${isOwner ? `
                <a href="/edit?postId=${post.id}" title="Редактировать пост" class="header__edit">
                    <img src="/assets/icons/edit.svg" alt="Редактировать" width="24" height="24">
                </a>` : ''}
            </div>
            
            <div class="post-content">
                ${this._renderMedia(post.images)}
            </div>
            
            <div class="about-post">
                <button class="likes" title="Лайкнуть" data-action="like">
                    <img class="likes__image" src="/assets/icons/like.png" alt="Лайк" width="16" height="16">
                    <span class="likes__counter">${post.likes ?? 0}</span>
                </button>
                
                <p class="post-text">${post.content || ''}</p>
                
                ${post.content?.length > 200 ? `
                <button class="read-more" title="Показать ещё" data-action="toggleContent">ещё</button>
                ` : ''}
                
                <p class="posted-at">
                    ${this.pluralize.formatRelativeTime(post.createdAt)}
                </p>
            </div>
        `;
        this.container.appendChild(postDiv);

        if (post.images?.length > 1) {
            const sliderContainer = postDiv.querySelector('[data-slider]');
            
            if (sliderContainer) {
                new Slider(sliderContainer, {
                    images: post.images.map(img => `/uploads/posts${img}`),
                    loop: true,
                });
            }
        }
    }

    _renderMedia(images) {
        if (!images || images.length === 0) {
            return '';
        }

        if (images.length === 1) {
            return `
                <img class="post-content__image" src="/uploads/posts${images[0]}" alt="Картинка поста ${this.currentIndex}">
            `;
        } 
        return `<div class="post__slider-container" data-slider></div>`;
    }
}