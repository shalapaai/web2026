export class Pluralize {
    _pluralize(count, one, few, many) {
        const lastDigit = count % 10;
        const lastTwo = count % 100;
        
        // 11-14 всегда "many"
        if (lastTwo >= 11 && lastTwo <= 14) {
            return many;
        }
        // 1 → "one"
        if (lastDigit === 1) {
            return one;
        }
        // 2-4 → "few"
        if (lastDigit >= 2 && lastDigit <= 4) {
            return few;
        }
        // 5-9, 0 → "many"
        return many;
    }

    formatRelativeTime(timestamp) {
        let timeMs;
        if (timestamp instanceof Date) {
            timeMs = timestamp.getTime();
        } else if (typeof timestamp === 'string') {
            timeMs = new Date(timestamp).getTime();
        } else {
            timeMs = Number(timestamp) * 1000;
        }
        
        if (isNaN(timeMs)) {
            return '';
        }
        
        const now = Date.now();
        const diff = Math.floor((now - timeMs) / 1000); // разница в секундах
        
        // Менее минуты
        if (diff < 60) {
            return 'только что';
        }
        // Менее часа
        if (diff < 3600) {
            const minutes = Math.floor(diff / 60);
            return `${minutes} ${this._pluralize(minutes, 'минуту', 'минуты', 'минут')} назад`;
        }
        // Менее дня
        if (diff < 86400) {
            const hours = Math.floor(diff / 3600);
            return `${hours} ${this._pluralize(hours, 'час', 'часа', 'часов')} назад`;
        }
        // Менее недели
        if (diff < 604800) {
            const days = Math.floor(diff / 86400);
            return `${days} ${this._pluralize(days, 'день', 'дня', 'дней')} назад`;
        }
        // Менее месяца (~4 недели)
        if (diff < 2592000) {
            const weeks = Math.floor(diff / 604800);
            return `${weeks} ${this._pluralize(weeks, 'неделю', 'недели', 'недель')} назад`;
        }
        // Менее года
        if (diff < 31536000) {
            const months = Math.floor(diff / 2592000);
            return `${months} ${this._pluralize(months, 'месяц', 'месяца', 'месяцев')} назад`;
        }
        // Год и более
        const years = Math.floor(diff / 31536000);
        return `${years} ${this._pluralize(years, 'год', 'года', 'лет')} назад`;
    }
}
