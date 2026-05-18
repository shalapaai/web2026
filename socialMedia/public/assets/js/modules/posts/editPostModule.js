import { CreatePostModule } from './createPostModule.js';
import { Api } from '../api.js';

export class EditPostModule extends CreatePostModule {
    constructor(container, config) {
        super(container, config);
        this.api = new Api(config);
        this.postId = config?.postId;
        this.initialContent = config?.content || '';
        this.initialImages = config?.images || [];
        this.isEditMode = true;
        this.shareBtn = this.container.querySelector('.save');
        this.removedImagePaths = [];
    }

    async init() {
        super.init();
        await this._loadPostData();
        this._updateUIForEdit();
        this._updateShareState();
    }

    async _loadPostData() {
        this.caption = this.initialContent;
        if (this.captionInput) {
            this.captionInput.value = this.caption;
        }
        
        if (this.initialImages?.length) {
            for (const imgPath of this.initialImages) {
                console.log('Loading image path:', imgPath);
                let normalizedPath = imgPath
                    .replace(/^\.+\//, '')      // убираем ./ или ../ в начале
                    .replace(/^\/+/, '/');       // нормализуем слэши
                
                // Если путь не начинается с /uploads — добавляем префикс
                if (!normalizedPath.startsWith('/uploads/')) {
                    normalizedPath = `/uploads/posts/${normalizedPath.replace(/^\/+/, '')}`;
                }
                
                this.images.push({
                    originalFile: null,           
                    processedBlob: null,          
                    dataUrl: normalizedPath,      // Используем URL напрямую для <img src>
                    existingPath: normalizedPath, // Помечаем как существующую для бэкенда
                    isExisting: true              // Флаг - это старая картинка
                });
            }
            // Перерисовываем превью с новыми путями
            if (this.images.length > 0) {
                this._renderPreview();
            }
        }
    }

    _updateUIForEdit() {
        const titleEl = document.querySelector('.header__name');
        if (titleEl) titleEl.textContent = 'Редактировать пост';
        if (this.shareBtn) {
            this.shareBtn.textContent = 'Сохранить изменения';
            this.shareBtn.title = 'Сохранить изменения';
        }
    }

    async _handleSubmit() {
        if (!this.postId) {
            this._showError('🤥 Ошибка: не указан ID поста');
            return;
        }
        const existingImages = this.images.filter(i => i.isExisting);
        const newImages = this.images.filter(i => !i.isExisting);
        
        const postData = {
            content: this.caption,
            newImages: newImages.map(i => i.processedBlob).filter(b => b !== null),
            existingImagePaths: existingImages.map(i => i.existingPath),
            // Отправляем пути к удалённым картинкам
            removedImagePaths: this.removedImagePaths
        };

        try {
            await this.api.updatePost(this.postId, postData);
            console.log('Пост обновлён!');
            this._resetForm();
            window.location.href = '/home/';
            return;
        } catch (err) {
            console.error('Failed to update post:', err);
            this._showError('🤥 Не удалось сохранить изменения');
        }
    }

    _resetForm() {
        this.caption = this.initialContent;
        if (this.captionInput) {
            this.captionInput.value = this.initialContent;
        }
        this._updateShareState();
    }

    _deleteImageAtIndex(index) {
        console.log(`_deleteImageAtIndex: ${index}`);
        
        const deleted = this.images[index];
        if (deleted?.isExisting && deleted.existingPath) {
            this.removedImagePaths.push(deleted.existingPath);
        }
        this.images.splice(index, 1);
        if (this.images.length === 1) {
            this.sliderInstance = null;
        }
        this._renderPreview();
        this._updateShareState();
    }
}