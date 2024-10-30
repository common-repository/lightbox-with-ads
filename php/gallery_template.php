<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Galerie</title>
</head>
<body>
<div id="lightbox-container">
    <div id="lightbox-header">
        <img id="logo" onerror="this.style.visibility='hidden'">
        <div id="image-index-with-arrows-container">
            <div id="left-arrow" class="material-icons">chevron_left</div>
            <div id="image-index-container">
                <div id="image-index">0</div>
                <span>/</span>
                <div id="image-index-max">0</div>
            </div>
            <div id="right-arrow" class="material-icons">chevron_right</div>
        </div>
        <div id="close-btn" class="material-icons">close</div>
    </div>
    <div id="lightbox-body">
        <figure id="image-caption-container">
            <img id="image">
            <figcaption id="caption"></figcaption>
        </figure>
        <div id="ad-container" class="ad-container-inactive">
        </div>
    </div>
    <div id="lightbox-footer">
    </div>
</div>
</body>
</html>