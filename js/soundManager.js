class SoundManager {
    constructor(sounds = {}) {
        this.sounds = {};
        this.currentMusic = null;
        this.isMuted = false;
        this.masterVolume = 1.0;
        
        // Загружаем звуки при создании
        if (Object.keys(sounds).length > 0) {
            this.loadSounds(sounds);
        }
    }

    /**
     * Загрузить звуки
     * @param {Object} sounds - Объект со звуками { название: путь или настройки }
     * @example
     * soundManager.loadSounds({
     *   click: '/sounds/click.mp3',
     *   music: { src: '/sounds/music.mp3', loop: true, volume: 0.5 }
     * });
     */
    loadSounds(sounds) {
        Object.entries(sounds).forEach(([name, config]) => {
            // Если передан просто путь
            if (typeof config === 'string') {
                this.sounds[name] = new Howl({
                    src: [config],
                    volume: this.masterVolume
                });
            } 
            // Если передан объект настроек
            else {
                this.sounds[name] = new Howl({
                    ...config,
                    volume: (config.volume || 1) * this.masterVolume
                });
            }
        });
    }

    /**
     * Добавить один звук
     * @param {string} name - Название звука
     * @param {string|Object} config - Путь к файлу или объект настроек
     */
    addSound(name, config) {
        if (typeof config === 'string') {
            this.sounds[name] = new Howl({
                src: [config],
                volume: this.masterVolume
            });
        } else {
            this.sounds[name] = new Howl({
                ...config,
                volume: (config.volume || 1) * this.masterVolume
            });
        }
    }

    /**
     * Воспроизвести звук
     * @param {string} name - Название звука
     * @param {Object} options - Дополнительные опции
     * @returns {number|null} ID звука или null
     */
    play(name, options = {}) {
        const sound = this.sounds[name];
        
        if (!sound) {
            console.warn(`Sound "${name}" not found`);
            return null;
        }

        // Применяем опции если есть
        if (options.volume !== undefined) {
            sound.volume(options.volume * this.masterVolume);
        }
        
        if (options.rate !== undefined) {
            sound.rate(options.rate);
        }

        const id = sound.play();
        
        // Callback при окончании
        if (options.onEnd) {
            sound.once('end', options.onEnd, id);
        }

        return id;
    }

    /**
     * Остановить звук
     * @param {string} name - Название звука
     * @param {number} id - ID конкретного экземпляра (опционально)
     */
    stop(name, id) {
        const sound = this.sounds[name];
        
        if (!sound) {
            console.warn(`Sound "${name}" not found`);
            return;
        }

        sound.stop(id);
    }

    /**
     * Поставить на паузу
     * @param {string} name - Название звука
     * @param {number} id - ID конкретного экземпляра (опционально)
     */
    pause(name, id) {
        const sound = this.sounds[name];
        
        if (!sound) {
            console.warn(`Sound "${name}" not found`);
            return;
        }

        sound.pause(id);
    }

    /**
     * Воспроизвести музыку (только одна музыка может играть)
     * @param {string} name - Название музыки
     * @param {Object} options - Опции
     */
    playMusic(name, options = {}) {
        // Останавливаем текущую музыку
        if (this.currentMusic && this.currentMusic !== name) {
            this.fadeOut(this.currentMusic, options.fadeOutDuration || 1000);
        }

        this.currentMusic = name;
        const sound = this.sounds[name];
        
        if (!sound) {
            console.warn(`Music "${name}" not found`);
            return null;
        }

        // Фейд-ин если задан
        if (options.fadeInDuration) {
            sound.volume(0);
            sound.play();
            sound.fade(0, options.volume || sound.volume(), options.fadeInDuration);
        } else {
            sound.play();
        }

        return sound;
    }

    /**
     * Остановить текущую музыку
     * @param {number} fadeDuration - Длительность затухания в мс
     */
    stopMusic(fadeDuration = 0) {
        if (!this.currentMusic) return;

        if (fadeDuration > 0) {
            this.fadeOut(this.currentMusic, fadeDuration, () => {
                this.currentMusic = null;
            });
        } else {
            this.stop(this.currentMusic);
            this.currentMusic = null;
        }
    }

    /**
     * Плавное затухание звука
     * @param {string} name - Название звука
     * @param {number} duration - Длительность в мс
     * @param {Function} onComplete - Callback по окончании
     */
    fadeOut(name, duration = 1000, onComplete) {
        const sound = this.sounds[name];
        
        if (!sound) return;

        const currentVolume = sound.volume();
        
        sound.fade(currentVolume, 0, duration);
        
        setTimeout(() => {
            sound.stop();
            sound.volume(currentVolume); // Восстанавливаем громкость
            if (onComplete) onComplete();
        }, duration);
    }

    /**
     * Плавное появление звука
     * @param {string} name - Название звука
     * @param {number} duration - Длительность в мс
     * @param {number} targetVolume - Целевая громкость
     */
    fadeIn(name, duration = 1000, targetVolume = 1.0) {
        const sound = this.sounds[name];
        
        if (!sound) return;

        sound.volume(0);
        const id = sound.play();
        sound.fade(0, targetVolume * this.masterVolume, duration, id);
    }

    /**
     * Установить громкость конкретного звука
     * @param {string} name - Название звука
     * @param {number} volume - Громкость (0.0 - 1.0)
     */
    setVolume(name, volume) {
        const sound = this.sounds[name];
        
        if (!sound) return;

        sound.volume(volume * this.masterVolume);
    }

    /**
     * Установить общую громкость всех звуков
     * @param {number} volume - Громкость (0.0 - 1.0)
     */
    setMasterVolume(volume) {
        this.masterVolume = Math.max(0, Math.min(1, volume));
        
        Object.values(this.sounds).forEach(sound => {
            const currentVol = sound.volume();
            sound.volume(currentVol * this.masterVolume);
        });
    }

    /**
     * Отключить/включить все звуки
     * @param {boolean} mute - true для отключения
     */
    mute(mute = true) {
        this.isMuted = mute;
        Howler.mute(mute);
    }

    /**
     * Переключить mute
     */
    toggleMute() {
        this.isMuted = !this.isMuted;
        Howler.mute(this.isMuted);
        return this.isMuted;
    }

    /**
     * Проверить, играет ли звук
     * @param {string} name - Название звука
     * @returns {boolean}
     */
    isPlaying(name) {
        const sound = this.sounds[name];
        return sound ? sound.playing() : false;
    }

    /**
     * Получить длительность звука
     * @param {string} name - Название звука
     * @returns {number} Длительность в секундах
     */
    getDuration(name) {
        const sound = this.sounds[name];
        return sound ? sound.duration() : 0;
    }

    /**
     * Остановить все звуки
     */
    stopAll() {
        Object.values(this.sounds).forEach(sound => sound.stop());
        this.currentMusic = null;
    }

    /**
     * Удалить звук из памяти
     * @param {string} name - Название звука
     */
    unload(name) {
        const sound = this.sounds[name];
        
        if (sound) {
            sound.unload();
            delete this.sounds[name];
        }

        if (this.currentMusic === name) {
            this.currentMusic = null;
        }
    }

    /**
     * Очистить все звуки
     */
    unloadAll() {
        Object.values(this.sounds).forEach(sound => sound.unload());
        this.sounds = {};
        this.currentMusic = null;
    }
}