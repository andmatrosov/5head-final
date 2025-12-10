class Quiz {
    constructor() {
        this.slider = null;
        this.sliderElement = null;
        this.timerInterval = null;
        this.startScreen = document.querySelector('.quiz__screen-start');
        this.finishedScreen = document.querySelector('.quiz__screen-finish');
        this.quizScreen = document.querySelector('.quiz__test-slider');
        this.quizSteps = Array.from(document.querySelectorAll('.quiz__steps-item')).reverse();
        this.secElement = document.getElementById('sec');
        this.msecElement = document.getElementById('msec');
        this.currentStep = 0;
        this.mobQuizNextStepDuration = 2000;
        this.isAnswerSelected = false;
        this.hintHalfUsed = false;
        this.hintCallUsed = false;

        // Инициализация звуков
        this.sounds = window.sounds;

        this.init();
    }

    init() {
        this.sliderElement = document.querySelector('.quiz__test .swiper');

        if (!this.sliderElement) return;

        document.querySelector('.quiz__test').scrollIntoView({ behavior: "smooth", block: "end", inline: "nearest" });

        this.sounds.fadeOut('start', 500);

        this.startQuiz();
        this.attachEventListeners();
    }

    initSlider() {
        const self = this;

        this.slider = new Swiper(this.sliderElement, {
            slidesPerView: 1,
            effect: 'coverflow',
            allowTouchMove: false,
            on: {
                init(swiper) {
                    self.getCurrentSlideData(swiper);
                },
                slideChangeTransitionEnd(swiper) {
                    self.getCurrentSlideData(swiper);
                    self.resetTimer();
                }
            }
        });
    }

    attachEventListeners() {
        const quizSlider = document.querySelector('.quiz__test-slider');

        if (!quizSlider) return;

        quizSlider.addEventListener('click', (e) => this.handleAnswerClick(e), true);
    }

    handleAnswerClick(e) {
        const target = e.target;

        if (!target.matches('.quiz__answers-item') && !target.closest('.quiz__answers-item')) {
            return;
        }

        const answerEl = target.closest('.quiz__answers-item');
        const questionEl = target.closest('.quiz__question');
        const questionId = Number(questionEl.dataset.question);
        const answerOption = answerEl.dataset.option;
        const questionVideo = target.closest('.quiz__test-item').querySelectorAll('video');


        const questionAnswers = window.quiz.questions[questionId].answers;

        // Блокируем все кнопки после выбора ответа
        this.disableInteractionsAfterSelect(answerEl);

        if (questionAnswers[answerOption].correct) {
            this.handleCorrectAnswer(answerEl, questionId);
        } else {
            this.handleWrongAnswer(answerEl, target, questionAnswers, questionVideo);
        }
    }

    startQuiz() {
        this.startScreen.setAttribute('hidden', '')
        this.quizScreen.removeAttribute('hidden');

        this.initSlider();
        this.showCurrentStep(0)
        this.currentStep = 1
    }

    handleCorrectAnswer(answerEl, idx) {
        answerEl.classList.add('-selected');
        this.stopTimer();
        this.isAnswerSelected = true;

        this.sounds.play('selected', {
            volume: 0.5
        });

        const timerSelectAnswer = setTimeout(() => {
            answerEl.classList.remove('-selected');
            answerEl.classList.add('-accepted');
            this.sounds.stop('selected');
            this.sounds.play('correct', {
                volume: 0.5
            });

            const timerChangeQuestion = setTimeout(() => {

                this.slider.slides[idx].querySelectorAll('video').forEach(v => {
                    v.pause();
                    v.currentTime = 0;
                });
                if(this.slider.slides[idx + 1]) {

                    if(isMobile()) {
                        this.showNextStepMobile(() => {
                            this.sounds.stop('correct');
                            this.sounds.play('next');
                            this.showCurrentStep(idx + 1);
                            this.slider.slideNext(400);
                        });
                    } else {
                        this.showCurrentStep(idx + 1);
                        this.sounds.play('next');
                        const timerShowNext = setTimeout(() => {
                            this.slider.slideNext(400);
                            clearTimeout(timerShowNext);
                        }, 400)
                    }
                    this.isAnswerSelected = false;
                    localStorage.setItem('scores', this.currentStep);
                    this.currentStep++;
                } else {
                    this.end('finish')
                }

                clearTimeout(timerChangeQuestion);
            }, 2000);

            clearTimeout(timerSelectAnswer);
        }, 2000);
    }

    handleWrongAnswer(answerEl, target, questionAnswers, video) {
        const correctOption = Object.entries(questionAnswers).find(([k, v]) => v.correct);
        const [option] = correctOption;

        const answersWrapper = target.closest('.quiz__answers');
        const correctEl = Array.from(answersWrapper.querySelectorAll('.quiz__answers-item'))
            .find(i => i.dataset.option === option);

        answerEl.classList.add('-selected');
        this.isAnswerSelected = true;
        this.stopTimer();

        setTimeout(() => {
            correctEl.classList.add('-accepted');
            answersWrapper.querySelectorAll('.quiz__answers-item')
                .forEach(i => i.setAttribute('disabled', ''));

            this.end('wrong');
            if(this.currentStep >= 7) {
                this.sounds.play('wrongHard');
            } else {
                this.sounds.play('wrong');
            }

            if(isMobile()) {
                video[1].pause();
                video[1].currentTime = 0;
            } else {
                video[0].pause();
                video[0].currentTime = 0;
            }
        }, 2000);
    }

    getCurrentSlideData(swiper) {
        const index = swiper.activeIndex;
        const slide = swiper.slides[index];
        const video = slide.querySelectorAll('video');
        const questionWrapp = slide.querySelector('.quiz__question');
        const hintHalf = questionWrapp.querySelector('.quiz__hints-half');
        const hintCall = questionWrapp.querySelector('.quiz__hints-call');
        const answers = slide.querySelectorAll('.quiz__answers-item');
        const timestamps = window.quiz.questions[index].timestamps;
        const isFirstQuestion = index === 0;

        new Promise(resolve => {
            this.sounds.fadeOut('next', 2500)
            setTimeout(() => {
                resolve();
            }, 2500)
        });

        this.showAnswers(video, answers, timestamps, isFirstQuestion, questionWrapp, hintHalf, hintCall);
    }

    showAnswers(video, elements, timestamps, isFirst = false, questionElement, hintHalf, hintCall) {
        if(isMobile()) {
            if(isFirst) {
                video[1].play();
            } else {
                setTimeout(() => {
                    video[1].play();
                }, this.mobQuizNextStepDuration)
            }
        } else {
            video[0].play();
        }

        const triggered = new Set();
        const tolerance = 0.3;

        hintHalf.addEventListener('click', () => {
            this.hideWrongAnswers(questionElement);
        });

        hintCall.addEventListener('click', () => {
            this.showHintDialog(questionElement);
        });

        const timeUpdateHandler = () => {
            const currentTime = isMobile() ? video[1].currentTime : video[0].currentTime;

            timestamps.forEach((timestamp, index) => {

                if (Math.abs(currentTime - timestamp) <= tolerance && !triggered.has(index)) {
                    elements[index].querySelector('.quiz__answers-text').classList.remove('hidden');
                    triggered.add(index);
                }
            });
        };

        const endedHandler = () => {
            elements.forEach(el => el.removeAttribute('disabled'));

            hintHalf.removeAttribute('disabled');
            hintCall.removeAttribute('disabled');

            if(!this.isAnswerSelected) {
                this.startTimer(50, () => {
                    if(isMobile()) {
                        video[1].pause();
                        video[1].currentTime = 0;
                    } else {
                        video[0].pause();
                        video[0].currentTime = 0;
                    }
                    this.end('timeleft');
                });
            }
        };

        video.forEach(v => {
            v.addEventListener('timeupdate', timeUpdateHandler);
            v.addEventListener('ended', endedHandler);
        })
    }

    showCurrentStep(step) {
        this.quizSteps.forEach(i => i.classList.remove('active'));

        if(this.quizSteps[step]) {
            this.quizSteps[step].classList.add('active');
        }
    }

    showNextStepMobile(cb) {
        window.quizeWrapperSlider.slideNext(600);

        const timeout = setTimeout(() => {

            cb();

            const timeout2 = setTimeout(() => {
                window.quizeWrapperSlider.slidePrev(600);
                clearTimeout(timeout2);
            }, this.mobQuizNextStepDuration)
            clearTimeout(timeout);
        }, this.mobQuizNextStepDuration / 2)
    }

    hideWrongAnswers(questionElement) {
        const questionId = Number(questionElement.dataset.question);
        const questionAnswers = window.quiz.questions[questionId].answers;

        // Находим правильный ответ
        const correctOption = Object.entries(questionAnswers).find(([k, v]) => v.correct);
        const [correctOptionKey] = correctOption;

        // Получаем все варианты ответов
        const answerElements = Array.from(questionElement.querySelectorAll('.quiz__answers-item'));

        // Фильтруем неправильные ответы
        const wrongAnswers = answerElements.filter(el => el.dataset.option !== correctOptionKey);

        // Перемешиваем и берем первые 2
        const answersToHide = wrongAnswers
            .sort(() => Math.random() - 0.5)
            .slice(0, 2);

        // Скрываем выбранные неправильные ответы
        answersToHide.forEach(answer => {
            answer.setAttribute('disabled', true);
            answer.querySelector('.quiz__answers-text').classList.add('hidden');
        });

        this.hintHalfUsed = true;

        document.querySelectorAll('.quiz__hints-half').forEach(hint => {
            hint.classList.add('-used');
        })

        // Воспроизводим звук подсказки (если есть)
        if (this.sounds && this.sounds.play) {
            this.sounds.play('hintHalf', { volume: 0.5 });
        }
    }

    // Метод открытия диалога с подсказкой
    showHintDialog(questionElement) {
        const questionId = Number(questionElement.dataset.question);

        // Находим главный диалог с подсказками
        const hintsDialog = document.getElementById('hints');

        if (!hintsDialog) {
            console.warn('Диалог с подсказками не найден');
            return;
        }

        // Скрываем все подсказки
        const allHints = hintsDialog.querySelectorAll('.popups__hints-content');
        allHints.forEach(hint => hint.style.display = 'none');

        // Показываем нужную подсказку
        const currentHint = hintsDialog.querySelector(`[data-hint-id="${questionId}"]`);

        if (!currentHint) {
            console.warn(`Подсказка с data-hint-id="${questionId}" не найдена`);
            return;
        }

        currentHint.style.display = 'flex'; // или 'block', в зависимости от твоих стилей

        // Открываем диалог
        hintsDialog.showModal();

        this.hintCallUsed = true;

        document.querySelectorAll('.quiz__hints-call').forEach(hint => {
            hint.classList.add('-used');
        })

        // Воспроизводим звук подсказки
        if (this.sounds && this.sounds.play) {
            this.sounds.play('hint', { volume: 0.5 });
        }

        // Воспроизводим видео в подсказке
        const hintVideo = currentHint.querySelector('video');
        if (hintVideo) {
            hintVideo.currentTime = 0;
            hintVideo.play();
        }

        // Обработчик закрытия диалога
        const closeButton = hintsDialog.querySelector('.mobile-overlay__close-button');
        if (closeButton) {
            closeButton.addEventListener('click', () => {
                if (hintVideo) {
                    hintVideo.pause();
                    hintVideo.currentTime = 0;
                }
            }, { once: true });
        }
    }

    disableInteractionsAfterSelect(answerEl) {
        const questionEl = answerEl.closest('.quiz__question');
        const buttons = questionEl.querySelectorAll('button');

        buttons.forEach(b => b.setAttribute('disabled', ''));
    }

    startTimer(seconds, onComplete) {
        if (!this.secElement || !this.msecElement) return;

        let totalMs = seconds * 100;

        // Очищаем предыдущий интервал
        this.stopTimer();
        if(this.currentStep >= 7) {
            this.sounds.play('timerHard')
        } else {
            this.sounds.play('timer')
        }

        const updateDisplay = () => {
            const currentSeconds = Math.floor(totalMs / 100);
            const currentMs = totalMs % 100;

            this.secElement.textContent = String(currentSeconds).padStart(2, '0');
            this.msecElement.textContent = String(currentMs).padStart(2, '0');
        };

        updateDisplay();

        this.timerInterval = setInterval(() => {
            totalMs--;

            if (totalMs <= 0) {
                totalMs = 0;
                updateDisplay();
                this.stopTimer(true);

                if (typeof onComplete === 'function') {
                    onComplete();
                }

                return;
            }

            updateDisplay();
        }, 10);
    }

    stopTimer(timerEnd = false) {
        if (this.timerInterval) {
            clearInterval(this.timerInterval);
            this.timerInterval = null;
            // this.sounds.stop('timer');
            if(this.currentStep >= 7) {
                this.sounds.stop('timerHard')
            } else {
                this.sounds.stop('timer')
            }
            if(timerEnd) {
                this.sounds.play('timerLeft');
            }
        }
    }

    resetTimer() {
        if (!this.secElement || !this.msecElement) return;

        this.secElement.innerText = '30';
        this.msecElement.innerText = '00';
    }

    async saveResults() {
        const nickname = localStorage.getItem('nickname');
        const email = localStorage.getItem('email');

        console.log(`Сохраняем данные: \n nickname: ${nickname}; \n email: ${email}; \n scores: ${this.currentStep}`);

        await fetch('/backend/api/update-score.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                participant_id: localStorage.getItem('userId'),
                quiz_score: this.currentStep - 1,
            })
        })
    }

    end(trigger) {
        this.quizScreen.setAttribute('hidden', '');
        this.finishedScreen.removeAttribute('hidden');
        localStorage.setItem('isFinished', true);
        this.quizSteps.forEach(s => s.classList.remove('active'));

        const popupGameOver = document.getElementById('gameOver');
        const timeOverPopup = document.getElementById('timeOver');
        const coolResPopup = document.getElementById('coolRes');
        const finishPopup = document.getElementById('finish');
        const likeInsaniPopup = document.getElementById('likeInsani');

        document.cookie = "finished=true";
        this.saveResults();

        if(trigger === 'timeleft') {
            timeOverPopup.showModal();
        } else if(trigger === 'wrong') {
            if(this.currentStep <= 7) {
                popupGameOver.showModal();
            } else if(this.currentStep === 8) {
                coolResPopup.showModal();
            } else if(this.currentStep === 9){
                likeInsaniPopup.showModal();
            } else if(this.currentStep === 10){
                coolResPopup.showModal();
            }
        } else if(trigger === 'finish') {
            finishPopup.showModal();
        }
    }
}