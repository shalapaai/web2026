import { Slider } from '../../ui/Slider.js';
import { Api } from '../api.js';

export class CreatePostModule {
    constructor(container, config) {
        this.container = container;
        this.config = config;
        this.api = new Api(config);
        
        // Состояние
        this.images = []; 
        this.caption = '';
        this.sliderInstance = null;

        // DOM-элементы
        this.form = this.container.querySelector('form');
        this.fileInput = this.container.querySelector('.add-photo-container__field input[type="file"]');
        this.btnMain = this.container.querySelector('.add-photo-container__post-image-main-button');
        this.btnSecondary = this.container.querySelector('.post-image-button');
        this.imageArea = this.container.querySelector('.add-photo-container');
        this.captionInput = this.container.querySelector('.add-info');
        this.shareBtn = this.container.querySelector('.share');

        // Лимиты для ресайза
        this.MAX_WIDTH = 1280;
        this.MAX_HEIGHT = 720;
    }

    init() {
        console.log('init');
        this._bindEvents();
        this._updateShareState();
    }

    _bindEvents() {
        // Блокируем стандартную отправку формы
        this.form?.addEventListener('submit', e => e.preventDefault());
        const openFilePicker = (e) => {
            e.preventDefault();
            this.fileInput.click();
        };
        this.btnMain?.addEventListener('click', openFilePicker);
        this.btnSecondary?.addEventListener('click', openFilePicker);
        this.fileInput?.addEventListener('change', async (e) => {
            const files = Array.from(e.target.files);
            await this._processFiles(files);
            this.fileInput.value = ''; // Сброс, чтобы можно было выбрать те же файлы повторно
        });

        // Отслеживание текста
        this.captionInput?.addEventListener('input', (e) => {
            this.caption = e.target.value.trim();
            this._updateShareState();
        });

        // Клик по "Поделиться"
        this.shareBtn?.addEventListener('click', () => {
            if (!this.shareBtn.disabled) this._handleSubmit();
        });
    }

    async _processFiles(files) {        
        const MAX_FILES = 10;
        const availableSlots = MAX_FILES - this.images.length;
        
        if (availableSlots <= 0) {
            return;
        }
        if (files.length > availableSlots) {
            files = files.slice(0, availableSlots);
        }
        const allowedTypes = ['image/jpeg', 'image/png'];
        for (const file of files) {
            if (!allowedTypes.includes(file.type)) continue;
            const processedBlob = await this._resizeIfNeeded(file);
            const dataUrl = await this._toDataURL(processedBlob);
            this.images.push({
                originalFile: file,
                processedBlob: processedBlob,
                dataUrl: dataUrl
            });
        }
        this._renderPreview();
        this._updateShareState();
    }

    _resizeIfNeeded(file) {
        return new Promise(resolve => {
            const img = new Image();
            img.onload = () => {
                // Если картинка вписывается в лимиты — отдаём как есть
                if (img.width <= this.MAX_WIDTH && img.height <= this.MAX_HEIGHT) {
                    URL.revokeObjectURL(img.src); // чистим память
                    resolve(file);
                    return;
                }
                
                // Вычисляем новые размеры с сохранением пропорций
                const scale = Math.min(
                    this.MAX_WIDTH / img.width, 
                    this.MAX_HEIGHT / img.height
                );
                const newWidth = Math.round(img.width * scale);
                const newHeight = Math.round(img.height * scale);
                const canvas = document.createElement('canvas');
                canvas.width = newWidth;
                canvas.height = newHeight;
                const ctx = canvas.getContext('2d');
                ctx.drawImage(img, 0, 0, newWidth, newHeight);
                
                // Конвертируем в Blob
                canvas.toBlob(
                    (blob) => {
                        URL.revokeObjectURL(img.src); // чистим память
                        resolve(blob || file); // если ошибка — фолбэк на оригинал
                    },
                    'image/jpeg',
                    0.9
                );
            };
            
            img.onerror = () => {
                console.warn(`[Resize] Failed to load: ${file.name}`);
                URL.revokeObjectURL(img.src);
                resolve(file);
            };
            img.src = URL.createObjectURL(file);
        });
    }

    _toDataURL(file) {
        return new Promise(resolve => {
            const reader = new FileReader();
            reader.onload = e => resolve(e.target.result);
            reader.readAsDataURL(file);
        });
    }

    _renderPreview() {
        this.imageArea.innerHTML = '';
        if (this.images.length === 0) {
            this._renderInitialState();
            return;
        }

        const wrapper = document.createElement('div');
        const deleteBtn = document.createElement('button');
        deleteBtn.type = 'button';
        deleteBtn.className = 'post-preview__delete-btn';
        deleteBtn.innerHTML = '✕';
        deleteBtn.className = 'post-preview__delete-btn';
        
        deleteBtn.addEventListener('click', () => {
            if (this.images.length === 1) {
                this._clearAllImages();
            } else {
                const currentIndex = this.sliderInstance?.currentIndex ?? 0;
                this._deleteImageAtIndex(currentIndex);
            }
        });
        wrapper.appendChild(deleteBtn);

        if (this.images.length === 1) {
            const img = document.createElement('img');
            img.src = this.images[0].dataUrl;
            img.className = 'post-content__image';
            wrapper.appendChild(img);
        } else {
            const sliderContainer = document.createElement('div');
            sliderContainer.className = 'post-content';
            sliderContainer.innerHTML = `
                <img class="post-content__image">

                <span class="post-content__counter"></span>

                <button class="post-content__arrow arrow_left" type="button">
                    <img src="/assets/icons/arrow-left.svg" alt="<-" width="10" height="10">
                </button>

                <button class="post-content__arrow arrow_right" type="button">
                    <img src="/assets/icons/arrow-right.svg" alt="->" width="10" height="10">
                </button>
            `;
            wrapper.appendChild(sliderContainer);
            this.sliderInstance = new Slider({
                images: this.images.map(i => i.dataUrl)
            });
            this.sliderInstance.attachElements({
                img: sliderContainer.querySelector('.post-content__image'),
                counter: sliderContainer.querySelector('.post-content__counter'),
                leftArrow: sliderContainer.querySelector('.arrow_left'),
                rightArrow: sliderContainer.querySelector('.arrow_right')
            });
        }
        this.imageArea.appendChild(wrapper);
    }

    _renderInitialState() {
        const field = document.createElement('div');
        field.className = 'add-photo-container__field';
        field.innerHTML = `
            <img class="add-photo-container__picture" src="assets/icons/picture.png" alt="Ввод картинки" width="81px" height="81px">
            <button class="add-photo-container__post-image-main-button" title="Добавить фото">Добавить фото</button>
            <input style="display: none;" type="file" accept="image/jpeg, image/png" multiple>
        `;
        this.imageArea.appendChild(field);
        
        // Восстанавливаем обработчик для кнопки
        const btnMain = field.querySelector('.add-photo-container__post-image-main-button');
        const fileInput = field.querySelector('input[type="file"]');
        btnMain?.addEventListener('click', (e) => {
            e.preventDefault();
            fileInput?.click();
        });
        fileInput?.addEventListener('change', async (e) => {
            const files = Array.from(e.target.files);
            await this._processFiles(files);
            fileInput.value = '';
        });
        // Обновляем ссылки на DOM-элементы
        this.fileInput = fileInput;
        this.btnMain = btnMain;
    }

    _deleteImageAtIndex(index) {
        this.images.splice(index, 1);
        if (this.images.length === 1) {
            this.sliderInstance = null;
        }
        this._renderPreview();
        this._updateShareState();
    }

    _clearAllImages() {
        this.images = [];
        this.sliderInstance = null;
        this._renderPreview();
        this._updateShareState();
    }

    _updateShareState() {
        const isValid = this.images.length > 0 && this.caption.length > 0;
        this.shareBtn.disabled = !isValid;
    }

    async _handleSubmit() {
        console.log('_handleSubmit');
        const postData = {
            content: this.caption,
            images: this.images.map(i => i.processedBlob)
        };

        try {
            await this.api.createPost(postData);
            console.log('Пост создан!');
            this._resetForm();  
            window.location.href = '/home/';
            return;
        } catch (err) {
            console.error('Failed to create post:', err);
        }
    }

    _resetForm() {
        console.log('_resetForm');
        this.images = [];
        this.caption = '';
        this.sliderInstance = null;
        if (this.captionInput) {
            this.captionInput.value = '';
        }
        this._renderPreview();
        this._updateShareState();
    }
}