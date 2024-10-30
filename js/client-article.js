// waits for the html document to load
jQuery(document).ready(function ($) {

    // scrolls to the previous position after closing the gallery
    const urlSearchParams = new URLSearchParams(window.location.search);
    const urlParams = Object.fromEntries(urlSearchParams.entries());
    if(urlParams.scrollpos != 0) {
        window.scrollTo(0, urlParams.scrollpos);
    }

    // selects all images
    let images = document.querySelectorAll('figure img');
    images.forEach((image) => { image.style.cursor = 'pointer' })

    images.forEach((image, i) => {
        image.onclick = () => {
            window.location.href = `${window.location.origin}/galerie?article=${window.location.pathname.replaceAll('/', '')}&imagenumber=${i + 1}&scrollpos=${window.pageYOffset}`;
            return false;
        }
    })
});