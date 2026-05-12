import { Api } from '../api.js'; 

export class LoginModule {
    constructor(container, config, path) {
        this.container = container;
        this.config = config;
        this.path = path;
        this.api = new Api(config);
        
        // Элементы формы
        this.passwordInput = null;
        this.toggleButton = null;
        this.toggleIcon = null;
        
        // Состояние
        this.isPasswordVisible = false;
        
        // Иконки
        this.icons = {
            hidden: '/assets/icons/eye-off.svg',
            visible: '/assets/icons/eye-open.svg'
        };
        
        // Тексты для доступности
        this.altText = {
            hidden: 'Показать пароль',
            visible: 'Скрыть пароль'
        };
    }

    init() {
        // Находим элементы
        this.passwordInput = this.container?.querySelector('#password');
        this.toggleButton = this.container?.querySelector('.login-form__show-password-image');
        
        // Если элементов нет — выходим
        if (!this.passwordInput || !this.toggleButton) {
            console.warn('LoginModule: password input or toggle button not found');
            return;
        }
        
        // Инициализируем переключатель
        this._initPasswordToggle();
        
        // Инициализируем отправку формы
        this._initFormSubmit();
    }

    _initPasswordToggle() {
        this.toggleIcon = this.toggleButton;
        this.toggleIcon.addEventListener('click', (e) => {
            e.preventDefault();
            this._togglePasswordVisibility();
        });
    }

    _togglePasswordVisibility() {
        this.isPasswordVisible = !this.isPasswordVisible;
        this.passwordInput.type = this.isPasswordVisible ? 'text' : 'password';
        this.toggleIcon.src = this.isPasswordVisible 
            ? this.icons.visible 
            : this.icons.hidden;
        this.toggleIcon.alt = this.isPasswordVisible 
            ? this.altText.visible 
            : this.altText.hidden;
    }

    _initFormSubmit() {
        const form = this.container;
        if (!form) return;
        
        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            
            const email = this.container?.querySelector('#email')?.value.trim();
            const password = this.passwordInput?.value;
            
            if (!email || !password) {
                this._showError('🤓 Поля обязательные');
                return;
            }
            
            if (!this._isValidEmail(email)) {
                this._showError('🤥 Неверный формат электропочты');
                return;
            }

            if (password.length < 8 && this.path === '/register') {
                this._showError('🤓 Мин. число символов для пароля - 8');
                return;
            }
            
            try {
                let result;
                (this.path === '/register') 
                ? result = await this.api.register(email, password)
                : result = await this.api.login(email, password);
                
                
                if (result.success) {
                    window.location.href = '/home/';
                } else if (result.error === 'invalid_credentials') {
                    this._showError('🤥 Неверный логин или пароль');
                } else if (result.error === 'user_exists') {
                    this._showError('🤥 Пользователь уже существует');
                } else {
                    this._showError('🤥 Ошибка');
                }

                
            } catch (err) {
                console.error('Login error:', err);
                this._showError('🤥 Ошибка соединения с сервером');
            }
        });
    }

    _isValidEmail(email) {
        return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
    }

    _showError(message) {
        // Удаляем старые ошибки
        const oldError = this.container?.querySelector('.login-form__error');
        oldError?.remove();
        
        // Создаём новый элемент ошибки
        const errorEl = document.createElement('div');
        errorEl.className = 'login-form__error';
        errorEl.textContent = message;

        this.container.prepend(errorEl);
        
        setTimeout(() => errorEl?.remove(), 5000);
    }
}