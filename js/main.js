document.addEventListener('DOMContentLoaded', () => {
    // font size
    document.addEventListener('DOMContentLoaded', (event) => {
        function setFontSize() {
            if (window.innerWidth <= 1920) {
                document.body.style.fontSize = window.innerWidth / 1728 + "rem";
            } else {
                if (document.body.style.removeProperty) {
                    document.body.style.removeProperty("font-size");
                } else {
                    document.body.style.removeAttribute("font-size");
                }
            }
        }
        window.addEventListener("resize", function () { setFontSize(); });
        setFontSize();
    });


    function initSelect(element) {
        const select = new UxSelect(element, {
            optionStyle: 'checkbox',
            hideOnSelect: true,
        });

        element.addEventListener('change', function() {
            const selectedValue = this.value;

            // Получаем текущий URL
            const currentUrl = new URL(window.location.href);

            // Устанавливаем параметр lang
            currentUrl.searchParams.set('lang', selectedValue);

            // Редирект на новый URL
            window.location.href = currentUrl.toString();
        });
    }

    const selectElemnts = document.querySelectorAll('.select');

    if(selectElemnts.length) {
        selectElemnts.forEach(select => initSelect(select))
    }



    // Prizes hover effect
    const prizeCards = document.querySelectorAll(".prizes__item:not(.cloned) [data-effect]");
    const match = window.matchMedia("(min-width: 768px)");
    const tiltSetting = {
        glare: true,
        "max-glare": 0.5,
    };

    let tiltDestroyed = false;

    const init = () => VanillaTilt.init(prizeCards, tiltSetting);
    init();

    function toggleVanillaTilt(reload = true) {
        if (reload) {
            document.location.reload();
        }
        if(match.matches) {
            init();
        } else {
            // Уничтожаем только инициализированные элементы
            prizeCards.forEach(card => {
                if(card && card.vanillaTilt) {
                    // card.vanillaTilt.destroy();
                    card.vanillaTilt.reset();
                    tiltDestroyed = true;
                    // card.querySelector('.js-tilt-glare').remove();
                }
            });
        }
    }

    match.addEventListener('change', toggleVanillaTilt);

    toggleVanillaTilt(false);



    // Winners

    const winnersBlocks = document.querySelectorAll(".winners");

    if(winnersBlocks.length) {
        winnersBlocks.forEach(el => {
            const winnersSliderEl = el.querySelector('.swiper');

            const winnersSlider = new Swiper(winnersSliderEl, {
                sliderPerView: 1,
                navigation: {
                    prevEl: el.querySelector('.winners-button-prev') || null,
                    nextEl: el.querySelector('.winners-button-next') || null
                },
                effect: 'coverflow'
            });
        })
    }
})

window.addEventListener('load', e => {
    // Divider scroll
    const match = window.matchMedia("(min-width: 768px)");
    let iconScrollers = {};

    function initiateLogoScroller(container = document) {
        const rows = container.querySelectorAll(".divider__wrapper");

        rows.forEach(row => {
            // Kill any existing animation for this row
            if (iconScrollers[row]) {
                iconScrollers[row].kill();
                delete iconScrollers[row];
            }

            const rowItems = Array.from(row.children);
            let rowWidth = row.scrollWidth;
            const containerWidth = window.innerWidth;

            // Prevent duplicate cloning
            if (!row.dataset.cloned) {
                let totalWidth = rowWidth;

                // Clone logos until the total width is at least twice the viewport width
                while (totalWidth < containerWidth * 2) {
                    rowItems.forEach(item => {
                        const clone = item.cloneNode(true);
                        row.appendChild(clone);
                        totalWidth += item.offsetWidth;
                    });
                }

                rowWidth = row.scrollWidth; // Update rowWidth after cloning
                row.dataset.cloned = "true";
            }

            // Reset row position to start
            gsap.set(row, { x: 0 });

            // Calculate speed dynamically to maintain consistency
            let baseSpeed = 20;
            let duration = (rowWidth / containerWidth) * baseSpeed;

            if (window.innerWidth < 768) {
                duration *= 0.75; // Slow it down for mobile
            }

            // GSAP Infinite Scrolling
            const tl = gsap.timeline({ repeat: -1, ease: "none" });

            tl.to(row, {
                x: `-${rowWidth / 2}px`, // Move by half the total width
                duration: duration,
                ease: "none",
                onComplete: function () {
                    gsap.set(row, { x: 0 }); // Reset position to loop seamlessly
                }
            });

            // Store the timeline for future cleanup
            iconScrollers[row] = tl;

        });
    }

    // Initialize on page load
    initiateLogoScroller(document.querySelector('.divider'));



    // Prizes scroll
    let prizeScrollers = {};

    function initiatePrizeScroller(container = document) {
        const rows = container.querySelectorAll(".prizes__row");

        rows.forEach(row => {
            // Kill any existing animation for this row
            if (prizeScrollers[row]) {
                prizeScrollers[row].kill();
                delete prizeScrollers[row];
            }

            const rowItems = Array.from(row.children);
            const clonedHTML = rowItems.map(item => {
                const clone = item.cloneNode(true);
                clone.classList.add('cloned');
                return clone.outerHTML;
            }).join('');
            row.innerHTML += clonedHTML;
            let rowWidth = row.scrollWidth;
            const containerWidth = window.innerWidth;

            // Prevent duplicate cloning
            if (!row.dataset.cloned) {
                let totalWidth = rowWidth;

                // Clone logos until the total width is at least twice the viewport width
                while (totalWidth < containerWidth * 2) {
                    rowItems.forEach(item => {
                        const clone = item.cloneNode(true);
                        row.appendChild(clone);
                        totalWidth += item.offsetWidth;
                    });
                }

                rowWidth = row.scrollWidth; // Update rowWidth after cloning
                row.dataset.cloned = "true";
            }

            // Reset row position to start
            gsap.set(row, { x: 0 });

            // Calculate speed dynamically to maintain consistency
            let baseSpeed = 20;
            let duration = (rowWidth / containerWidth) * baseSpeed;

            if (window.innerWidth < 768) {
                duration *= 0.75; // Slow it down for mobile
            }

            // GSAP Infinite Scrolling
            const tl = gsap.timeline({ repeat: -1, ease: "none" });

            tl.to(row, {
                x: `-${rowWidth / 2}px`, // Move by half the total width
                duration: duration,
                ease: "none",
                onComplete: function () {
                    gsap.set(row, { x: 0 }); // Reset position to loop seamlessly
                }
            });

            // Store the timeline for future cleanup
            prizeScrollers[row] = tl;
        });
    }

    function togglePrizeScroll() {
        const rows = document.querySelectorAll(".prizes__row");

        if(!match.matches) {
            initiatePrizeScroller(document.querySelector('.prizes__items'));
        } else {
            // Убиваем все анимации и очищаем стили
            rows.forEach(row => {
                if(prizeScrollers[row]) {
                    prizeScrollers[row].kill();
                    delete prizeScrollers[row];
                }
                gsap.set(row, { clearProps: "all" }); // Полностью очищаем GSAP стили
                row.removeAttribute('style');

                row.removeAttribute('data-cloned')

                row.querySelectorAll('.cloned').forEach(i => i.remove());
            });
        }
    }


    togglePrizeScroll();

    // initSoundManager

    window.sounds = new SoundManager({
        start: 'sounds/start.mp3',
        selected: 'sounds/selected.mp3',
        correct: 'sounds/correct.mp3',
        wrong: 'sounds/wrong.mp3',
        wrongHard: 'sounds/wrongHard.mp3',
        next: {
            src: ['sounds/next.mp3'],
            volume: 0.4
        },
        hintHalf: 'sounds/50-50.mp3',
        hintCall: 'sounds/50-50.mp3',
        call: 'sounds/call.mp3',
        pickUp: 'sounds/pickUp.mp3',
        timer: 'sounds/timer.mp3',
        timerHard: 'sounds/timerHard.mp3',
        timerLeft: 'sounds/timerLeft.mp3',
        bgMusic: {
            src: ['sounds/background.mp3'],
            loop: true,
            volume: 0.3
        }
    });


    // startQuiz
    const startBtns = document.querySelectorAll('.js-start');

    startBtns.forEach(startBtn => {
        startBtn.addEventListener('click', startBtnHandler)
    });

    function startBtnHandler(e) {
        if(window.isFinished) return;

        if(localStorage.getItem('userId')) {
            window.quizInstance = new Quiz();
            return
        }

        window.sounds.playMusic('start');
        const registerPopup = document.getElementById('register');
        registerPopup.showModal();
    }
    const matchMediaQuizWrapp = window.matchMedia('(max-width: 767px)');

    function initSliderWrapper() {
        const quizWrapperSliderEl = document.querySelector('.js-quiz-wrapper-slider');

        if(matchMediaQuizWrapp.matches) {
            window.quizeWrapperSlider = new Swiper(quizWrapperSliderEl, {
                slidesPerView: 1,
                spaceBetween: 20,
                on: {
                    resize(swiper) {
                        debounce(() => {
                            swiper.update();
                        }, 500)
                    }
                }
            })
        } else {
            if(window.quizeWrapperSlider) {
                window.quizeWrapperSlider.destroy();
                window.quizeWrapperSlider = null
            }
        }
    }

    initSliderWrapper();

    matchMediaQuizWrapp.addEventListener('change', initSliderWrapper);
})