// ui/ModalWindow.js
import { Slider } from './Slider.js';

export class ModalWindow {
    constructor(options = {}) {
        this.images = options.images || [];
        this.currentIndex = options.startIndex || 0;
        
        this._modal = null;
        this._slider = null;
        
        this._onKeydown = this._onKeydown.bind(this);
    }

    open() {
        if (this.images.length === 0) return;
        this._render();
        this._initSlider();
        this._bindEvents();
    }

    close() {
        if (!this._modal) return;
        
        if (this._slider) {
            this._slider.destroy();
            this._slider = null;
        }
        
        this._unbindEvents();
        this._modal.remove();
        this._modal = null;
        
        if (typeof this.onClose === 'function') {
            this.onClose();
        }
    }

    _render() {
        this._modal = document.createElement('div');
        this._modal.className = 'modal-window';
        
        this._modal.innerHTML = `
            <div class="modal-window-content">
                <button class="modal-window-content__close-button">
                    <img src="/assets/icons/close.svg" alt="Закрыть">
                </button>
                
                <div class="post-content">
                    <img class="post-content__image modal-window-content__image" src="" alt="Картинка поста ${this.currentIndex}">
                    ${this.images.length > 1 ? `
                    <button class="post-content__arrow arrow_left" type="button">
                        <img src="/assets/icons/arrow-left.svg" alt="<-" width="10" height="10">
                    </button>
                    <button class="post-content__arrow arrow_right" type="button">
                        <img src="/assets/icons/arrow-right.svg" alt="->" width="10" height="10">
                    </button>
                    ` : ''}
                </div>
                ${this.images.length > 1 ? `
                <span class="modal-window-content__counter"></span>
                ` : ''}
            </div>
        `;
        
        document.body.appendChild(this._modal);
    }

    _initSlider() {
        if (this.images.length <= 1) {
            const img = this._modal?.querySelector('.modal-window-content__image');
            if (img && this.images[0]) {
                img.src = this.images[0];
            }
            return;
        }
        
        this._slider = new Slider(null, {
            images: this.images,
            startIndex: this.currentIndex,
            isModal: true
        });
        
        this._slider.attachElements({
            img: this._modal.querySelector('.modal-window-content__image'),
            counter: this._modal.querySelector('.modal-window-content__counter'),
            leftArrow: this._modal.querySelector('.arrow_left'),
            rightArrow: this._modal.querySelector('.arrow_right')
        });
    }

    _bindEvents() {
        this._modal?.querySelector('.modal-window-content__close-button')
            ?.addEventListener('click', () => this.close());
        document.addEventListener('keydown', this._onKeydown);
    }

    _unbindEvents() {
        document.removeEventListener('keydown', this._onKeydown);
    }

    _onKeydown(e) {
        if (e.key === 'Escape') {
            e.preventDefault();
            this.close();
        } else if (this.images.length > 1 && this._slider) {
            if (e.key === 'ArrowLeft') {
                e.preventDefault();
                this._slider.prev();
            } else if (e.key === 'ArrowRight') {
                e.preventDefault();
                this._slider.next();
            }
        }
    }
}