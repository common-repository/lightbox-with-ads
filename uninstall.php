<?php
if (!defined('WP_UNINSTALL_PLUGIN')) {
    die;
}

delete_option('lightbox_with_ads_option_pc_number_of_images');
delete_option('lightbox_with_ads_option_mobile_number_of_images');
delete_option('lightbox_with_ads_option_background_color');
delete_option('lightbox_with_ads_option_arrows_top_margin');
delete_option('lightbox_with_ads_option_arrows_color');
delete_option('lightbox_with_ads_option_current_img_index_color');
delete_option('lightbox_with_ads_option_close_button_color');
delete_option('lightbox_with_ads_option_caption_font_size');
delete_option('lightbox_with_ads_option_image_index_font_size');
delete_option('lightbox_with_ads_option_close_button_font_size');
delete_option('lightbox_with_ads_option_arrows_font_size');
delete_option('lightbox_with_ads_option_pc_ad_script');
delete_option('lightbox_with_ads_option_mobile_ad_script');
delete_option('lightbox_with_ads_option_logo_image');
delete_option('lightbox_with_ads_option_logo_width');
delete_option('lightbox_with_ads_option_logo_height');