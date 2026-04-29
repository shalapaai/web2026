export class MoreButton {
    constructor(options = {}) {
        this.textElement = options.textElement; 
        this.button = options.button; 
        this.limit = 200;
        this.expandedText = 'свернуть';
        this.collapsedText = 'ещё';
        
        this._isExpanded = false;
        this._fullText = '';
        this._truncatedText = '';
        
        if (this.textElement && this.button) {
            this._init();
        }
    }

    _init() {
        this._fullText = this.textElement.textContent;
        
        if (this._fullText.length <= this.limit) {
            this.button.style.display = 'none';
            return;
        }
        
        this._truncatedText = this._truncateText(this._fullText, this.limit);
        this.textElement.textContent = this._truncatedText;
        this.button.addEventListener('click', (e) => {
            e.preventDefault();
            this.toggle();
        });
    }

    _truncateText(text, limit) {
        if (text.length <= limit) return text;
        let truncated = text.slice(0, limit);
        const lastSpace = truncated.lastIndexOf(' ');
        if (lastSpace > limit * 0.8) { // если пробел недалеко от конца
            truncated = truncated.slice(0, lastSpace);
        }
        return truncated + '…';
    }

    toggle() {
        if (!this.textElement) return;
        this._isExpanded = !this._isExpanded;
        
        if (this._isExpanded) {
            this.textElement.textContent = this._fullText;
            this.button.textContent = this.expandedText;
        } else {
            this.textElement.textContent = this._truncatedText;
            this.button.textContent = this.collapsedText;
        }
    }
}