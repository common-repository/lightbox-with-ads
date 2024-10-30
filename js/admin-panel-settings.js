jQuery(document).ready(function ($) {

    // backs up options for reset button
    const optionsBackup = [];
    document.querySelectorAll('.lightbox_with_ads_option').forEach((input) => {
        optionsBackup.push(input.value);
    })

    // form buttons
    const resetButton = document.querySelector('#reset-changes');

    // resets any changes
    resetButton.addEventListener('click', () => {
        if (confirm("Are you sure you want to reset all the changes you have just made?")) {
            document.querySelectorAll('.lightbox_with_ads_option').forEach((input, i) => {
                input.value = optionsBackup[i];
            })
        }
    })

// The "Upload" button
    $('#upload-logo-image-button').click(function() {
        var send_attachment_bkp = wp.media.editor.send.attachment;
        var button = $(this);
        wp.media.editor.send.attachment = function(props, attachment) {
            $(button).parent().prev().attr('src', attachment.url);
            $(button).prev().val(attachment.id);
            wp.media.editor.send.attachment = send_attachment_bkp;
        }
        wp.media.editor.open(button);
        return false;
    });
});