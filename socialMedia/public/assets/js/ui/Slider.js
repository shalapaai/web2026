export class Slider {
    constructor(container, options = {}) {
        this.container = typeof container === 'string' 
            ? document.querySelector(container) 
            : container;
        
        this.images = options.images;
        this.currentIndex = 0;
        
        if (this.container && this.images.length > 1) {
            this._init();
        }
    }

    _init() {
        this._render();
        this._bindEvents();
    }

    _render() {
        this.container.innerHTML = `
            <img class="post-content__image" 
                 src="${this.images[0]}" 
                 alt="Post image" 
                 width="474" height="474"
                 loading="lazy"
                 onerror="this.style.display='none'">
            <span class="post-content__counter">1/${this.images.length}</span>
            <div class="post-content__arrow arrow_left">
                <img src="/assets/icons/arrow-left.svg" alt="←" width="10" height="10">
            </div>
            <div class="post-content__arrow arrow_right">
                <img src="/assets/icons/arrow-right.svg" alt="→" width="10" height="10">
            </div>
        `;
        
        // Сохраняем ссылки на элементы
        this._img = this.container.querySelector('.post-content__image');
        this._counter = this.container.querySelector('.post-content__counter');
    }

    _bindEvents() {
        const leftArrow = this.container.querySelector('.arrow_left');
        leftArrow?.addEventListener('click', (e) => {
            e.stopPropagation();
            this._prev();
        });
        
        const rightArrow = this.container.querySelector('.arrow_right');
        rightArrow?.addEventListener('click', (e) => {
            e.stopPropagation();
            this._next();
        });
    }

    _prev() {
        this.currentIndex = (this.currentIndex - 1 + this.images.length) % this.images.length;
        this._update();
    }

    _next() {
        this.currentIndex = (this.currentIndex + 1) % this.images.length;
        this._update();
    }

    _update() {
        this._img.src = this.images[this.currentIndex];
        this._counter.textContent = `${this.currentIndex + 1}/${this.images.length}`;
    }
}