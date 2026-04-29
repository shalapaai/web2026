// ui/Slider.js
export class Slider {
    constructor(container, options = {}) {
        this.container = typeof container === 'string' 
            ? document.querySelector(container) 
            : container;
        
        this.images = options.images || [];
        this.currentIndex = options.startIndex || 0;
        this.isModal = options.isModal ?? false;
        
        this._img = null;
        this._counter = null;
        this._leftArrow = null;
        this._rightArrow = null;
        
        this._onLeftClick = (e) => {
            e.stopPropagation();
            this.prev();
        };
        this._onRightClick = (e) => {
            e.stopPropagation();
            this.next();
        };
        
        if (!this.isModal && this.container && this.images.length > 1) {
            this._init();
        }
    }

    attachElements({ img, counter, leftArrow, rightArrow }) {
        if (!img) return this;
        
        this._img = img;
        this._counter = counter || null;
        this._leftArrow = leftArrow || null;
        this._rightArrow = rightArrow || null;
        
        if (this.images.length > 1 && this._leftArrow && this._rightArrow) {
            this._bindEvents();
        }
        this.update();
        return this;
    }

    _init() {
        this._render();
        this._bindEvents();
    }

    _render() {
        if (this.isModal) return;
        
        this.container.innerHTML = `
            <img class="post-content__image" 
                 src="${this.images[this.currentIndex]}" 
                 alt="Картика поста ${this.currentIndex}" 
                 width="474" height="474">
            <span class="post-content__counter">${this.currentIndex + 1}/${this.images.length}</span>
            <button class="post-content__arrow arrow_left" type="button">
                <img src="/assets/icons/arrow-left.svg" alt="<-" width="10" height="10">
            </button>
            <button class="post-content__arrow arrow_right" type="button">
                <img src="/assets/icons/arrow-right.svg" alt="->" width="10" height="10">
            </button>
        `;
        
        this._img = this.container.querySelector('.post-content__image');
        this._counter = this.container.querySelector('.post-content__counter');
        this._leftArrow = this.container.querySelector('.arrow_left');
        this._rightArrow = this.container.querySelector('.arrow_right');
    }

    _bindEvents() {
        this._leftArrow?.removeEventListener('click', this._onLeftClick);
        this._rightArrow?.removeEventListener('click', this._onRightClick);
        
        this._leftArrow?.addEventListener('click', this._onLeftClick);
        this._rightArrow?.addEventListener('click', this._onRightClick);
    }

    prev() {
        if (this.images.length <= 1) return;
        this.currentIndex = (this.currentIndex - 1 + this.images.length) % this.images.length;
        this.update();
    }

    next() {
        if (this.images.length <= 1) return;
        this.currentIndex = (this.currentIndex + 1) % this.images.length;
        this.update();
    }

    update() {
        if (!this._img) return;
        this._img.src = this.images[this.currentIndex];
        if (this._counter) {
            (this.isModal)
            ? this._counter.textContent = `${this.currentIndex + 1} из ${this.images.length}`
            : this._counter.textContent = `${this.currentIndex + 1}/${this.images.length}`
        }
    }

    destroy() {
        this._leftArrow?.removeEventListener('click', this._onLeftClick);
        this._rightArrow?.removeEventListener('click', this._onRightClick);
    }
}