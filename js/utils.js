function isMobile() {
    const match = window.matchMedia('(max-width: 767px)');

    if(match.matches) {
        return true;
    } else {
        return false;
    }
}

function debounce(func, delay) {
    let timeoutId;
    return function(...args) {
        clearTimeout(timeoutId);
        timeoutId = setTimeout(() => func.apply(this, args), delay);
    };
}

isMobile()

function lazyLoad(video) {
    const srcDesc = video.dataset.srcHorizontal;
    const srcMob = video.dataset.srcVertical;

    if(isMobile()) {
        video.src = srcMob;
        // source.removeAttribute('data-src')
    } else {
        video.src = srcDesc;
    }

    video.load()
}

function randomChoice(a, b) {
    return Math.random() < 0.5 ? a() : b();
}