// waits for the html document to load
jQuery(document).ready(function ($) {

    /* HELPER FUNCTIONS */
    function replaceUrlParam(url, paramName, paramValue) {
        if (paramValue == null) {
            paramValue = '';
        }
        let pattern = new RegExp('\\b(' + paramName + '=).*?(&|#|$)');
        if (url.search(pattern) >= 0) {
            return url.replace(pattern, '$1' + paramValue + '$2');
        }
        url = url.replace(/[?#]$/, '');
        return url + (url.indexOf('?') > 0 ? '&' : '?') + paramName + '=' + paramValue;
    }

    function isOnMobile() {
        if (window.innerWidth <= 700) {
            return true;
        }
        return false;
    }

    // loads options from wp-admin
    const imageParam = scriptParams.image;
    const captionParam = scriptParams.caption;
    const maxImageIndex = scriptParams.maxImageIndex;
    const pcNumberOfImages = scriptParams.pcNumberOfImages;
    const mobileNumberOfImages = scriptParams.mobileNumberOfImages;
    const backgroundColor = scriptParams.backgroundColor;
    const arrowsTopMargin = scriptParams.arrowsTopMargin;
    const arrowsColor = scriptParams.arrowsColor;
    const arrowsFontSize = scriptParams.arrowsFontSize;
    const imageIndexColor = scriptParams.imageIndexColor;
    const captionFontSize = scriptParams.captionFontSize;
    const closeButtonColor = scriptParams.closeButtonColor;
    const imageIndexFontSize = scriptParams.imageIndexFontSize;
    const closeButtonFontSize = scriptParams.closeButtonFontSize;
    const logoImage = scriptParams.logoImage;
    const logoWidth = scriptParams.logoWidth;
    const logoHeight = scriptParams.logoHeight;

    // gets params from url
    const urlSearchParams = new URLSearchParams(window.location.search);
    const urlParams = Object.fromEntries(urlSearchParams.entries());

    let imageNumber = 1;
    if (urlParams.imagenumber != null) {
        imageNumber = urlParams.imagenumber;
    }
    if (imageNumber > parseInt(maxImageIndex)) {
        imageNumber = maxImageIndex;
    }

    /* SETS ALL OPTIONS TO THE CORRECT ELEMENTS */
    let container = document.querySelector('#lightbox-container');
    container.style.backgroundColor = backgroundColor;

    let logo = document.querySelector('#logo');
    logo.src = logoImage;
    logo.style.width = `${logoWidth}px`
    logo.style.height = `${logoHeight}px`
    logo.addEventListener('click', () => {
        window.location.href = window.location.origin;
    })

    let imageIndex = document.querySelector('#image-index');
    imageIndex.innerHTML = imageNumber;

    let imageIndexContainer = document.querySelector('#image-index-container');
    imageIndexContainer.style.fontSize = `${imageIndexFontSize}px`

    let imageIndexMax = document.querySelector('#image-index-max');
    imageIndexMax.innerHTML = maxImageIndex;

    let leftArrow = document.querySelector('#left-arrow');
    leftArrow.style.fontSize = `${arrowsFontSize}px`
    leftArrow.style.color = arrowsColor;
    leftArrow.addEventListener('click', () => {
        moveImageToLeft();
    })

    let rightArrow = document.querySelector('#right-arrow');
    rightArrow.style.fontSize = `${arrowsFontSize}px`
    rightArrow.style.color = arrowsColor;
    rightArrow.addEventListener('click', () => {
        moveImageToRight();
    });

    let imageIndexWithArrowsContainer = document.querySelector('#image-index-with-arrows-container');
    imageIndexWithArrowsContainer.style.color = imageIndexColor;

    let closeButton = document.querySelector('#close-btn');
    closeButton.style.color = closeButtonColor;
    closeButton.style.fontSize = `${closeButtonFontSize}px`;
    closeButton.addEventListener('click', () => {
        window.location.href = `${window.location.origin}/${urlParams.article}?scrollpos=${urlParams.scrollpos}`;
    });

    image.src = imageParam;
    caption.innerHTML = captionParam;
    caption.style.fontSize = `${captionFontSize}px`;

    function moveImageToRight() {
        let nextImageIndex = parseInt(imageNumber) + 1;

        if (nextImageIndex > parseInt(maxImageIndex)) {
            nextImageIndex = 1;
        }

        window.location.href = replaceUrlParam(window.location.href, 'imagenumber', nextImageIndex);
    }

    function moveImageToLeft() {
        let nextImageIndex = parseInt(imageNumber) - 1;

        if (nextImageIndex < 1) {
            nextImageIndex = parseInt(maxImageIndex);
        }

        window.location.href = replaceUrlParam(window.location.href, 'imagenumber', nextImageIndex);
    }

    /* HANDLE SWIPING */
    document.addEventListener('touchstart', handleTouchStart, false);
    document.addEventListener('touchmove', handleTouchMove, false);
    let xDown = null;
    let yDown = null;

    function getTouches(evt) {
        return evt.touches ||
            evt.originalEvent.touches;
    }

    function handleTouchStart(evt) {
        const firstTouch = getTouches(evt)[0];
        xDown = firstTouch.clientX;
        yDown = firstTouch.clientY;
    };

    function handleTouchMove(evt) {
        if (!xDown || !yDown) {
            return;
        }

        let xUp = evt.touches[0].clientX;
        let yUp = evt.touches[0].clientY;
        let xDiff = xDown - xUp;
        let yDiff = yDown - yUp;

        if (Math.abs(xDiff) > Math.abs(yDiff)) {
            if (xDiff > 0) {
                moveImageToRight();
            } else {
                moveImageToLeft();
            }
        }
        xDown = null;
        yDown = null;
    };

    repositionArrows();
    window.addEventListener('resize', function (event) {
        repositionArrows();
    }, true);

    function repositionArrows() {
        let imageIndexContainer = document.querySelector('#image-index-with-arrows-container');
        if (isOnMobile()) {
            imageIndexContainer.classList.add('mobile-image-index');
            imageIndexContainer.style.top = `${parseInt(logoHeight) + parseInt(arrowsTopMargin)}px`;
        } else {
            imageIndexContainer.classList.remove('mobile-image-index');
        }
    }
});