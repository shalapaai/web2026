export class Config {
    constructor() {
        this.config = null;
    }
    async loadConfig() {
        if (this.config) return this.config;
        
        const response = await fetch('../config.json');
        this.config = await response.json();
        return this.config;
    }

    getConfig() {
        if (!this.config) {
            throw new Error('Config not loaded');
        }
        return this.config;
    }
}