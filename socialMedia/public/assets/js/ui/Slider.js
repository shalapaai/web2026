export class Slider {
    constructor(options = {}) {
        this.images = options.images || [];
        this.currentIndex = options.startIndex || 0;
        
        this._img = null;
        this._counter = null;
        this._leftArrow = null;
        this._rightArrow = null;

        this._onLeftClick = (e) => {
            this.prev();
        };

        this._onRightClick = (e) => {
            this.next();
        };
    }

    attachElements({ img, counter, leftArrow, rightArrow }) {
        this._img = img;
        this._counter = counter;
        this._leftArrow = leftArrow;
        this._rightArrow = rightArrow;

        this._bindEvents();
        this.update();

        return this;
    }

    _bindEvents() {
        this._leftArrow?.addEventListener('click', this._onLeftClick);
        this._rightArrow?.addEventListener('click', this._onRightClick);
    }

    prev() {
        this.currentIndex = (this.currentIndex - 1 + this.images.length) % this.images.length;
        this.update();
    }

    next() {
        this.currentIndex = (this.currentIndex + 1) % this.images.length;
        this.update();
    }

    update() {
        if (!this._img) return;
        this._img.src = this.images[this.currentIndex];
        if (this._counter) this._counter.textContent = `${this.currentIndex + 1}/${this.images.length}`;
    }

    destroy() {
        this._leftArrow?.removeEventListener('click', this._onLeftClick);
        this._rightArrow?.removeEventListener('click', this._onRightClick);
    }
}