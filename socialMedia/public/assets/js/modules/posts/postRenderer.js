import { Slider } from '../../ui/Slider.js';
import { Pluralize } from '../../utils/pluralize.js';
import { ModalWindow } from '../../ui/ModalWindow.js';
import { MoreButton } from '../../ui/MoreButton.js';
import { Api } from '../api.js';

export class PostRenderer {
    constructor(container, config, posts = []) {
        this.container = container;
        this.pluralize = new Pluralize;
        this.posts = new Map(posts.map(p => [p.id, p]));
        this.container.addEventListener('click', (e) => this._onContainerClick(e));
        this.api = new Api(config);
    }

    _onContainerClick(e) {
        const postDiv = e.target.closest('.post');
        if (!postDiv) return;
        const postId = postDiv.dataset.postId;
        const likeBtn = e.target.closest('.likes');
        if (likeBtn && postId) {
            this._toggleLike(postDiv, postId);
            return;
        }
        const img = e.target.closest('.post-content__image');
        if (!img) return;
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

    async renderPost(currUserId, post, author) {
        const isOwner = author.id === currUserId;
        const postDiv = document.createElement('div');
        postDiv.className = 'post';
        postDiv.dataset.postId = post.id;
        postDiv._postImages = post.images || [];
        const hasLongContent = post.content?.length > 200;
        const isLiked = (await this.api.checkUserLike(post.id)).data.isLiked;
        console.log('isLiked: ', isLiked);
        
        postDiv.innerHTML = `
            <div class="header">
                <a class="header__user" href="/profile?id=${author?.id}" title="Профиль">
                    <img class="header__avatar" 
                            src="/uploads/avatars${author?.avatar}" 
                            alt="Аватар" 
                            width="32" height="32"">
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
                
                <button class="likes ${isLiked ? 'liked' : ''}" title="Лайкнуть">
                    <img class="likes__image" src="/assets/icons/like.png" alt="Лайк" width="16" height="16">
                    <span class="likes__counter">${post.likes ?? 0}</span>
                </button>
                
                <p class="post-text">${post.content || ''}</p>
                
                ${hasLongContent ? `
                <button class="read-more" title="Показать ещё">ещё</button>
                ` : ''}
                
                <p class="posted-at">
                    ${this.pluralize.formatRelativeTime(post.createdAt)}
                </p>
            </div>
        `;
        this.container.appendChild(postDiv);
        if (hasLongContent) {
            const textEl = postDiv.querySelector('.post-text');
            const moreBtn = postDiv.querySelector('.read-more');
            if (textEl && moreBtn) {
                new MoreButton({
                    textElement: textEl,
                    button: moreBtn
                });
            }
        }

        if (post.images?.length > 1) {
            const sliderContainer = postDiv.querySelector('.post__slider-container');
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
        return `<div class="post__slider-container"></div>`;
    }

    async _toggleLike(postDiv, postId) {
        const likeBtn = postDiv.querySelector('.likes');
        const counterEl = postDiv.querySelector('.likes__counter');
        if (!likeBtn || !counterEl) return;

        const wasLiked = likeBtn.classList.contains('liked');
        const currentCount = parseInt(counterEl.textContent) || 0;

        likeBtn.classList.toggle('liked');
        counterEl.textContent = wasLiked ? currentCount - 1 : currentCount + 1;
        likeBtn.disabled = true;
        try {
            const result = await this.api.toggleLike(postId);
            if (!result?.success) {
                likeBtn.classList.toggle('liked');
                counterEl.textContent = currentCount;
            }
        } catch (err) {
            console.error('Failed to toggle like:', err);
            likeBtn.classList.toggle('liked');
            counterEl.textContent = currentCount;
        } finally {
           likeBtn.disabled = false;
        }
    }
}