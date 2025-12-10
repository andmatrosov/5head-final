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