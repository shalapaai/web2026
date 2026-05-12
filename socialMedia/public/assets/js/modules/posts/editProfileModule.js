// modules/editProfileModule.js
import { Api } from '../api.js'; 

export class EditProfileModule {
    constructor(container, config, path) {
        this.container = container;
        this.config = config;
        this.path = path;
        this.api = new Api(config);
        
        // DOM-элементы
        this.form = this.container?.querySelector('form');
        this.nameInput = this.container?.querySelector('#name');
        this.contentInput = this.container?.querySelector('#content');
        this.fileInput = this.container?.querySelector('.add-photo-container__field input[type="file"]');
        this.btnMain = this.container?.querySelector('#add-new-photo');
        
        // Ищем превью аватарки
        this.avatarPreview = this.container?.querySelector('.user-avatar');

        // Состояние для аватарки
        this.avatarFile = null;          
        this.avatarDataUrl = null;        
        this.avatarExistingPath = config?.currentAvatarUrl || null;  // Текущая аватарка из конфига

        this.MAX_WIDTH = 400;
        this.MAX_HEIGHT = 400;
    }

    async init() {
        // Сначала показываем текущую аватарку
        await this._loadCurrentAvatar();
        // Потом вешаем обработчики
        this._bindEvents();
        this._initFormSubmit();
    }

    async _loadCurrentAvatar() {
        if (!this.avatarExistingPath) {
            this._renderDefaultAvatar();
            return;
        }
        const avatarUrl = `${window.location.origin}/uploads/avatars${this.avatarExistingPath}`;
        // Если элемент превью уже есть — просто обновляем src
        if (this.avatarPreview) {
            this.avatarPreview.src = avatarUrl;
            this.avatarPreview.alt = 'Текущая аватарка';
            this.avatarPreview.classList.remove('avatar-preview');  // Убираем маркер "новая"
            return;
        }
        // Если элемента нет — создаём его
        this._createAvatarPreview(avatarUrl, false); 
    }

    // Показ дефолтной аватарки, если нет своей
    _renderDefaultAvatar() {
        const defaultUrl = '/assets/images/default-avatar.png';
        if (this.avatarPreview) {
            this.avatarPreview.src = defaultUrl;
            this.avatarPreview.alt = 'Аватарка';
            return;
        }
        this._createAvatarPreview(defaultUrl, false);
    }

    _bindEvents() {
        this.form?.addEventListener('submit', e => e.preventDefault());

        const openFilePicker = (e) => {
            e.preventDefault();
            this.fileInput?.click();
        };
        this.btnMain?.addEventListener('click', openFilePicker);

        this.fileInput?.addEventListener('change', async (e) => {
            const file = e.target.files?.[0];
            if (file) {
                await this._processAvatar(file);
                this.fileInput.value = '';
            }
        });
    }

    async _processAvatar(file) {
        const allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
        if (!allowedTypes.includes(file.type)) {
            return;
        }
        const processedBlob = await this._resizeIfNeeded(file);
        const dataUrl = await this._toDataURL(processedBlob);
        this.avatarFile = processedBlob;
        this.avatarDataUrl = dataUrl;
        this.avatarExistingPath = null;  
        this._renderAvatarPreview();
    }

    _resizeIfNeeded(file) {
        return new Promise(resolve => {
            const img = new Image();
            img.onload = () => {
                if (img.width <= this.MAX_WIDTH && img.height <= this.MAX_HEIGHT) {
                    URL.revokeObjectURL(img.src);
                    resolve(file);
                    return;
                }
                
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
                
                canvas.toBlob(
                    (blob) => {
                        URL.revokeObjectURL(img.src);
                        resolve(blob || file);
                    },
                    'image/jpeg'
                );
            };
            img.onerror = () => {
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

    _renderAvatarPreview() {
        console.log('_renderAvatarPreview', { 
            hasPreviewEl: !!this.avatarPreview, 
            hasDataUrl: !!this.avatarDataUrl 
        });
        
        if (!this.avatarDataUrl) return;
        
        if (this.avatarPreview) {
            this.avatarPreview.src = this.avatarDataUrl;
            this.avatarPreview.alt = 'Новая аватарка';
            this.avatarPreview.classList.add('avatar-preview'); 
            this.avatarPreview.style.opacity = '1';
            return;
        }
        this._createAvatarPreview(this.avatarDataUrl, true);
    }

    _createAvatarPreview(src, isNew = false) {
        const avatarContainer = this.container?.querySelector('.about-user') 
                             || this.container?.querySelector('.user-avatar-container')
                             || this.container;
        
        if (!avatarContainer) {
            console.warn('EditProfileModule: не найден контейнер для аватарки');
            return;
        }
        let previewImg = avatarContainer.querySelector('img.avatar-preview, img.user-avatar');
        if (!previewImg) {
            previewImg = document.createElement('img');
            previewImg.className = 'user-avatar';
            previewImg.alt = 'Аватарка';
            const insertBefore = avatarContainer.querySelector('#add-new-photo') 
                              || avatarContainer.querySelector('button');
            if (insertBefore?.parentNode === avatarContainer) {
                avatarContainer.insertBefore(previewImg, insertBefore);
            } else {
                avatarContainer.prepend(previewImg);
            }
        }
        
        previewImg.src = src;        
        this.avatarPreview = previewImg;
    }

    _initFormSubmit() {
        const form = this.form || this.container;
        if (!form) return;
        
        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            
            const name = this.nameInput?.value.trim();
            const content = this.contentInput?.value.trim();

            if (name.length > 255 || content?.length > 255) {
                this._showError('🤓 Макс. число символов - 255');
                return;
            }
            
            try {
                const currUser = await this.api.getSignedUser();
                const userId = currUser?.data?.id;
                
                if (!userId) {
                    this._showError('🤥 Пользователь не авторизован');
                    return;
                }
                const profileData = {
                    name: name,
                    profileStatus: content
                };
                
                if (this.avatarFile instanceof Blob) {
                    profileData.avatar = this.avatarFile;
                }
                // Если аватарка yt менялась — отправляем путь к существующей
                else if (this.avatarExistingPath) {
                    profileData.existingAvatarPath = this.avatarExistingPath;
                }
                const result = await this.api.editProfile(userId, profileData);
                if (result?.success) {
                    console.log('Профиль обновлён!');
                    window.location.href = '/home/';
                }

            } catch (err) {
                console.error('Edit profile error:', err);
            }
        });
    }
}