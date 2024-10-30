<?php
/**
 * Plugin Name:       Lightbox With Ads
 * Description:       Lightbox With Ads is a flexible and responsive lightbox that allows you to present images in a post as one gallery in a different url. You can purchase Premium that allows you to add ads from Google AdSense, or test the feature for free for 14 days.
 * Version:           1.0
 * Requires at least: 5.5
 * Requires PHP:      7
 * Author:            World News Media s.r.o.
 * Author URI:        https://www.worldnewsmedia.cz
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 */

// for security
if (!defined('ABSPATH')) {
    die;
}

const LIGHTBOX_WITH_ADS_PLUGIN_VERSION = '1.0';
const LIGHTBOX_WITH_ADS_LICENSE_SERVER = 'https://lightboxwithads.com/wp-json/lmfwc/v2/licenses';
const LIGHTBOX_WITH_ADS_CONSUMER_KEY = 'ck_636b9977e3d923e73112e9723360e3a35b5d69d9';
const LIGHTBOX_WITH_ADS_CONSUMER_SECRET = 'cs_bf5f330cb4927a03e208837c7f57cb53816d29bf';

// activation
register_activation_hook(__FILE__, 'lightbox_with_ads_activation');
function lightbox_with_ads_activation()
{
    lightbox_with_ads_activation_output();
    flush_rewrite_rules();
}

// adds a new permalink entry
add_action('init', 'lightbox_with_ads_activation_output');
function lightbox_with_ads_activation_output()
{
    add_rewrite_tag('%galerie%', '([^&]+)');
    add_rewrite_rule('^galerie/?', 'index.php?galerie=galerie', 'top');
    add_rewrite_endpoint('galerie', EP_PERMALINK | EP_PAGES);
}

// adds a param to gallery entry
add_filter('query_vars', 'lightbox_with_ads_add_article_query_var');
function lightbox_with_ads_add_article_query_var($vars)
{
    $vars[] = "article";
    return $vars;
}

// adds a param to article entry
add_filter('query_vars', 'lightbox_with_ads_add_image_query_var');
function lightbox_with_ads_add_image_query_var($vars)
{
    $vars[] = "image-number";
    return $vars;
}

// redirects to the gallery template
add_action('template_redirect', 'lightbox_with_ads_display');
function lightbox_with_ads_display()
{
    $url = explode('/', sanitize_url($_SERVER['REQUEST_URI']));
    $article = get_query_var('article');

    if ($url[1] == 'galerie' && $article) {
        include('php/gallery_template.php');
        wp_footer();
        wp_head();
        exit;
    }
}

// adds settings link in plugin page
add_filter('plugin_action_links_' . plugin_basename(__FILE__), 'lightbox_with_ads_add_settings_link');
function lightbox_with_ads_add_settings_link($links)
{
    // build and escape the URL
    $url = esc_url(add_query_arg(
        'page',
        'lightbox_with_ads',
        get_admin_url() . 'options-general.php'
    ));

    // create the link
    $settings_link = "<a href='$url'>" . __('Settings') . '</a>';

    // adds the link to the end of the array.
    $links[] = $settings_link;

    return $links;
}

// initialization
add_action('init', 'lightbox_with_ads_general_init');
function lightbox_with_ads_general_init()
{
    global $pagenow;
    // setups all files for frontend (!is_admin() returns false, if not in the wp-admin page)
    if (!is_admin()) {
        wp_enqueue_script('jquery');
        wp_enqueue_style('material-icons-styles.css', 'https://fonts.googleapis.com/icon?family=Material+Icons');

        if (!preg_match('/^\/galerie\//', sanitize_url($_SERVER['REQUEST_URI']))) {
            wp_enqueue_script('client-article.js', plugins_url('/js/client-article.js', __FILE__), [], LIGHTBOX_WITH_ADS_PLUGIN_VERSION);
        } else {
            wp_enqueue_style('client-gallery-styles.css', plugins_url('/css/client-gallery-styles.css', __FILE__), [], LIGHTBOX_WITH_ADS_PLUGIN_VERSION);

            // if the user has a premium account, enqueues the premium js
            if (get_option('lightbox_with_ads_option_plugin_type') == 'Premium' || get_option('lightbox_with_ads_option_plugin_type') == 'Premium trial') {
                $scriptPath = 'js/premium/client-gallery' . '-ver=' . LIGHTBOX_WITH_ADS_PLUGIN_VERSION . '.js';

                if(get_option('lightbox_with_ads_option_plugin_type') == 'Premium trial') {
                    $license_key = '780Y-SA47-N72A-F2AS-9UOL'; // License key for trial
                } else {
                    $license_key = get_option('lightbox_with_ads_option_license_key');
                }

                // if the premium script with current version is not downloaded, download it and save it to js/premium/
                if(!file_exists(plugin_dir_path(__FILE__) . $scriptPath)) {
                    $response = wp_remote_get(LIGHTBOX_WITH_ADS_LICENSE_SERVER . '/?plugin_license_key=' . $license_key, ['timeout' => 20]);
                    $response = json_decode(wp_remote_retrieve_body($response));

                    // if the response is success, save the premium script
                    if($response->success == 'true') {
                        // if the premium folder does not exist, create it
                        if (!file_exists(plugin_dir_path(__FILE__) . 'js/premium')) {
                            mkdir(plugin_dir_path(__FILE__) . 'js/premium', 0777, true);
                        }

                        file_put_contents(plugin_dir_path(__FILE__) . $scriptPath, $response->body);
                    }
                    else { // if it's not success, set the script path to free version
                        $scriptPath = '/js/client-gallery.js.';
                    }

                }

                wp_enqueue_script('client-gallery.js', plugins_url($scriptPath, __FILE__), [], LIGHTBOX_WITH_ADS_PLUGIN_VERSION);
            } else {
                wp_enqueue_script('client-gallery.js', plugins_url('/js/client-gallery.js', __FILE__), [], LIGHTBOX_WITH_ADS_PLUGIN_VERSION);
            }
            wp_dequeue_script('style.css');

            // Gets the image number from url parameter
            preg_match("~imagenumber=\s*([^\n\r]*)~", sanitize_url($_SERVER['REQUEST_URI']), $pregImageNumber);
            $image_number = $pregImageNumber[1];

            // Gets the article slug from url parameter
            $article = [];
            preg_match("~article=\s*([^\n\r]*)~", sanitize_url($_SERVER['REQUEST_URI']), $article);
            preg_match("~.+?(?=&)~", $article[1], $article);

            $post_id = url_to_postid(site_url($article[0]));
            $content = get_post_field('post_content', $post_id);

            $doc = new DOMDocument();
            @$doc->loadHTML('<?xml encoding="utf-8" ?>' . $content);
            $xpath = new DOMXPath($doc);

            $images_html = $xpath->evaluate("//figure[not(.//figure)]//img/@src");
            $captions_html = $xpath->evaluate("//figure[not(.//figure) and .//img]");

            $images = [];
            $captions = [];

            foreach ($images_html as $image) {
                $images[] = $image->nodeValue;
            }

            foreach ($captions_html as $caption) {
                $captions[] = $caption->nodeValue;
            }

            // gets settings to pass to the client js
            $script_params = array(
                'image' => $images[$image_number - 1],
                'caption' => $captions[$image_number - 1],
                'maxImageIndex' => count($images),
                'pcNumberOfImages' => get_option('lightbox_with_ads_option_pc_number_of_images'),
                'mobileNumberOfImages' => get_option('lightbox_with_ads_option_mobile_number_of_images'),
                'backgroundColor' => get_option('lightbox_with_ads_option_background_color'),
                'arrowsTopMargin' => get_option('lightbox_with_ads_option_arrows_top_margin'),
                'arrowsColor' => get_option('lightbox_with_ads_option_arrows_color'),
                'arrowsFontSize' => get_option('lightbox_with_ads_option_arrows_font_size'),
                'imageIndexColor' => get_option('lightbox_with_ads_option_current_img_index_color'),
                'closeButtonColor' => get_option('lightbox_with_ads_option_close_button_color'),
                'captionFontSize' => get_option('lightbox_with_ads_option_caption_font_size'),
                'imageIndexFontSize' => get_option('lightbox_with_ads_option_image_index_font_size'),
                'closeButtonFontSize' => get_option('lightbox_with_ads_option_close_button_font_size'),
                'pcAdScript' => get_option('lightbox_with_ads_option_pc_ad_script'),
                'mobileAdScript' => get_option('lightbox_with_ads_option_mobile_ad_script'),
                'logoWidth' => get_option('lightbox_with_ads_option_logo_width'),
                'logoHeight' => get_option('lightbox_with_ads_option_logo_height'),
                'logoImage' => wp_get_attachment_url(get_option('lightbox_with_ads_option_logo_image'))
            );
            wp_localize_script('client-gallery.js', 'scriptParams', $script_params);
        }
    }

    if((get_option('lightbox_with_ads_option_plugin_type') == 'Premium' && time() >= get_option('lightbox_with_ads_option_license_expire_date') ||
        get_option('lightbox_with_ads_option_plugin_type') == 'Premium trial' && time() >= get_option('lightbox_with_ads_option_plugin_trial_end_date'))) {
        update_option('lightbox_with_ads_option_plugin_type', 'Free');
        update_option('lightbox_with_ads_option_license_status', 'Expired');
    }

    update_option('lightbox_with_ads_option_license_last_checked', time());
}

// enqueues admin scripts
add_action('admin_enqueue_scripts', 'lightbox_with_ads_enqueue_admin_scripts');
function lightbox_with_ads_enqueue_admin_scripts() {
    wp_enqueue_media();
    wp_enqueue_script('jquery');
    wp_enqueue_style('admin-panel-settings-styles.css', plugins_url('/css/admin-panel-settings-styles.css', __FILE__));
    wp_enqueue_script('admin-panel-settings.js', plugins_url('/js/admin-panel-settings.js', __FILE__));
}

add_action('admin_init', 'lightbox_with_ads_register_settings');
function lightbox_with_ads_register_settings()
{
    // registers settings
    register_setting('lightbox_with_ads_options_group', 'lightbox_with_ads_option_plugin_type');
    register_setting('lightbox_with_ads_options_group', 'lightbox_with_ads_option_plugin_trial_end_date');
    register_setting('lightbox_with_ads_options_group', 'lightbox_with_ads_option_license_key');
    register_setting('lightbox_with_ads_options_group', 'lightbox_with_ads_option_license_type');
    register_setting('lightbox_with_ads_options_group', 'lightbox_with_ads_option_license_status');
    register_setting('lightbox_with_ads_options_group', 'lightbox_with_ads_option_license_expire_date');
    register_setting('lightbox_with_ads_options_group', 'lightbox_with_ads_option_license_last_checked');
    register_setting('lightbox_with_ads_options_group', 'lightbox_with_ads_option_pc_number_of_images');
    register_setting('lightbox_with_ads_options_group', 'lightbox_with_ads_option_mobile_number_of_images');
    register_setting('lightbox_with_ads_options_group', 'lightbox_with_ads_option_background_color');
    register_setting('lightbox_with_ads_options_group', 'lightbox_with_ads_option_arrows_top_margin');
    register_setting('lightbox_with_ads_options_group', 'lightbox_with_ads_option_arrows_color');
    register_setting('lightbox_with_ads_options_group', 'lightbox_with_ads_option_current_img_index_color');
    register_setting('lightbox_with_ads_options_group', 'lightbox_with_ads_option_close_button_color');
    register_setting('lightbox_with_ads_options_group', 'lightbox_with_ads_option_caption_font_size');
    register_setting('lightbox_with_ads_options_group', 'lightbox_with_ads_option_image_index_font_size');
    register_setting('lightbox_with_ads_options_group', 'lightbox_with_ads_option_close_button_font_size');
    register_setting('lightbox_with_ads_options_group', 'lightbox_with_ads_option_arrows_font_size');
    register_setting('lightbox_with_ads_options_group', 'lightbox_with_ads_option_pc_ad_script');
    register_setting('lightbox_with_ads_options_group', 'lightbox_with_ads_option_mobile_ad_script');
    register_setting('lightbox_with_ads_options_group', 'lightbox_with_ads_option_logo_image');
    register_setting('lightbox_with_ads_options_group', 'lightbox_with_ads_option_logo_width');
    register_setting('lightbox_with_ads_options_group', 'lightbox_with_ads_option_logo_height');

    // sets default values if they are null
    if (!get_option('lightbox_with_ads_option_plugin_type'))
        update_option('lightbox_with_ads_option_plugin_type', 'Premium trial');

    if (!get_option('lightbox_with_ads_option_plugin_trial_end_date'))
        update_option('lightbox_with_ads_option_plugin_trial_end_date', strtotime('+ 14 days'));

    if (!get_option('lightbox_with_ads_option_license_type'))
        update_option('lightbox_with_ads_option_license_type', 'Free');

    if (!get_option('lightbox_with_ads_option_license_status'))
        update_option('lightbox_with_ads_option_license_status', 'Active');

    if (!get_option('lightbox_with_ads_option_license_expire_date'))
        update_option('lightbox_with_ads_option_license_expire_date', 'Never');

    if (!get_option('lightbox_with_ads_option_pc_number_of_images'))
        update_option('lightbox_with_ads_option_pc_number_of_images', '3');

    if (!get_option('lightbox_with_ads_option_mobile_number_of_images'))
        update_option('lightbox_with_ads_option_mobile_number_of_images', '3');

    if (!get_option('lightbox_with_ads_option_background_color'))
        update_option('lightbox_with_ads_option_background_color', '#000000');

    if (!get_option('lightbox_with_ads_option_arrows_color'))
        update_option('lightbox_with_ads_option_arrows_color', '#FFFFFF');

    if (!get_option('lightbox_with_ads_option_arrows_font_size'))
        update_option('lightbox_with_ads_option_arrows_font_size', '64');

    if (!get_option('lightbox_with_ads_option_arrows_top_margin'))
        update_option('lightbox_with_ads_option_arrows_top_margin', '50');

    if (!get_option('lightbox_with_ads_option_current_img_index_color'))
        update_option('lightbox_with_ads_option_current_img_index_color', '#FFFFFF');

    if (!get_option('lightbox_with_ads_option_image_index_font_size'))
        update_option('lightbox_with_ads_option_image_index_font_size', '25');

    if (!get_option('lightbox_with_ads_option_close_button_color'))
        update_option('lightbox_with_ads_option_close_button_color', '#FFFFFF');

    if (!get_option('lightbox_with_ads_option_close_button_font_size'))
        update_option('lightbox_with_ads_option_close_button_font_size', '48');

    if (!get_option('lightbox_with_ads_option_caption_font_size'))
        update_option('lightbox_with_ads_option_caption_font_size', '18');

    if (!get_option('lightbox_with_ads_option_logo_width'))
        update_option('lightbox_with_ads_option_logo_width', '150');

    if (!get_option('lightbox_with_ads_option_logo_height'))
        update_option('lightbox_with_ads_option_logo_height', '50');

}

function lightbox_with_ads_get_between($string, $start = "", $end = "")
{
    if (strpos($string, $start)) {
        $startCharCount = strpos($string, $start) + strlen($start);
        $firstSubStr = substr($string, $startCharCount, strlen($string));
        $endCharCount = strpos($firstSubStr, $end);
        if ($endCharCount == 0) {
            $endCharCount = strlen($firstSubStr);
        }
        return substr($firstSubStr, 0, $endCharCount);
    } else {
        return '';
    }
}

add_action('admin_menu', 'lightbox_with_ads_register_options_page');
function lightbox_with_ads_register_options_page()
{
    add_options_page('Basic Settings', 'Lightbox With Ads', 'manage_options', 'lightbox_with_ads', function () {

        if (isset($_POST['lightbox_with_ads_option_license_key'])) {
            $license_key = sanitize_text_field($_POST['lightbox_with_ads_option_license_key']);

            $headers = [
                'Authorization' => 'Basic ' . base64_encode(LIGHTBOX_WITH_ADS_CONSUMER_KEY . ':' . LIGHTBOX_WITH_ADS_CONSUMER_SECRET)
            ];
            $args = ['timeout' => 20, 'headers' => $headers];

            if (!isset($_POST['deactivate-license-button'])) {
                $result = lightbox_with_ads_activate_license($license_key, $args);
            } else {
                $result = lightbox_with_ads_deactivate_license($license_key, $args);
            }

            if ($result['success']) {
                $license_success = $result['message'];
            } else {
                $license_error = $result['message'];
            }
        }

        if (isset($_POST['submit'])) {
            update_option('lightbox_with_ads_option_pc_number_of_images', sanitize_text_field($_POST['lightbox_with_ads_option_pc_number_of_images']));
            update_option('lightbox_with_ads_option_mobile_number_of_images', sanitize_text_field($_POST['lightbox_with_ads_option_mobile_number_of_images']));
            update_option('lightbox_with_ads_option_background_color', sanitize_hex_color($_POST['lightbox_with_ads_option_background_color']));
            update_option('lightbox_with_ads_option_arrows_color', sanitize_hex_color($_POST['lightbox_with_ads_option_arrows_color']));
            update_option('lightbox_with_ads_option_arrows_font_size', sanitize_text_field($_POST['lightbox_with_ads_option_arrows_font_size']));
            update_option('lightbox_with_ads_option_arrows_top_margin', sanitize_text_field($_POST['lightbox_with_ads_option_arrows_top_margin']));
            update_option('lightbox_with_ads_option_current_img_index_color', sanitize_text_field($_POST['lightbox_with_ads_option_current_img_index_color']));
            update_option('lightbox_with_ads_option_image_index_font_size', sanitize_text_field($_POST['lightbox_with_ads_option_image_index_font_size']));
            update_option('lightbox_with_ads_option_close_button_color', sanitize_hex_color($_POST['lightbox_with_ads_option_close_button_color']));
            update_option('lightbox_with_ads_option_close_button_font_size', sanitize_text_field($_POST['lightbox_with_ads_option_close_button_font_size']));
            update_option('lightbox_with_ads_option_caption_font_size', sanitize_text_field($_POST['lightbox_with_ads_option_caption_font_size']));
            update_option('lightbox_with_ads_option_logo_image', sanitize_text_field($_POST['lightbox_with_ads_option_logo_image']));
            update_option('lightbox_with_ads_option_logo_width', sanitize_text_field($_POST['lightbox_with_ads_option_logo_width']));
            update_option('lightbox_with_ads_option_logo_height', sanitize_text_field($_POST['lightbox_with_ads_option_logo_height']));

            // this deletes original scripts elements and replaces them with classic AdSense scripts
            $pc_script = stripslashes(wp_filter_post_kses($_POST['lightbox_with_ads_option_pc_ad_script']));
            $mobile_script = stripslashes(wp_filter_post_kses($_POST['lightbox_with_ads_option_mobile_ad_script']));

            if($pc_script != '') {
                $pc_script = trim(str_replace('     (adsbygoogle = window.adsbygoogle || []).push({});','', $pc_script));
                $pc_pub_id = lightbox_with_ads_get_between($pc_script, 'ca-pub-', '"');
                $pc_script = '<script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-' . $pc_pub_id . '"' .'
     crossorigin="anonymous"></script>' . "\n" . stripslashes($pc_script) . "\n" . '<script>
     (adsbygoogle = window.adsbygoogle || []).push({});
</script>';
            }

            if($mobile_script != '') {
                $mobile_script = trim(str_replace('     (adsbygoogle = window.adsbygoogle || []).push({});','', $mobile_script));
                $mobile_pub_id = lightbox_with_ads_get_between($mobile_script, 'ca-pub-', '"');
                $mobile_script = '<script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-' . $mobile_pub_id . '"' .'
     crossorigin="anonymous"></script>' . "\n" . stripslashes($mobile_script) . "\n" . '<script>
     (adsbygoogle = window.adsbygoogle || []).push({});
</script>';
            }

            update_option('lightbox_with_ads_option_pc_ad_script', $pc_script);
            update_option('lightbox_with_ads_option_mobile_ad_script', $mobile_script);
        }

        require_once 'php/admin_panel_settings.php';
    });
}

function lightbox_with_ads_activate_license($license_key, $args)
{
    $response = wp_remote_get(LIGHTBOX_WITH_ADS_LICENSE_SERVER . '/activate/' . $license_key, $args);

    if (is_wp_error($response) || wp_remote_retrieve_response_code($response) != 200) {
        return ['success' => false, 'message' => json_decode(wp_remote_retrieve_body($response))->message];
    }

    $license_data = json_decode(wp_remote_retrieve_body($response));

    update_option('lightbox_with_ads_option_plugin_type', 'Premium');
    update_option('lightbox_with_ads_option_license_type', 'Premium');
    update_option('lightbox_with_ads_option_license_status', 'Active');
    update_option('lightbox_with_ads_option_license_key', $license_key);
    update_option('lightbox_with_ads_option_license_expire_date', strtotime($license_data->data->expiresAt));
    update_option('lightbox_with_ads_option_license_last_checked', time());

    return ['success' => true, 'message' => 'License key successfully activated'];
}

function lightbox_with_ads_deactivate_license($license_key, $args)
{
    $response = wp_remote_get(LIGHTBOX_WITH_ADS_LICENSE_SERVER . '/deactivate/' . $license_key, $args);
    $message = json_decode(wp_remote_retrieve_body($response))->message;

    if (is_wp_error($response) || wp_remote_retrieve_response_code($response) != 200 && $message != 'License Key: ' . $license_key . ' has not been activated yet.') {
        return ['success' => false, 'message' => json_decode(wp_remote_retrieve_body($response))->message];
    }

    update_option('lightbox_with_ads_option_plugin_type', 'Free');
    update_option('lightbox_with_ads_option_license_type', 'Free');
    update_option('lightbox_with_ads_option_license_status', 'Active');
    update_option('lightbox_with_ads_option_license_key', '');
    update_option('lightbox_with_ads_option_license_expire_date', 'Never');
    update_option('lightbox_with_ads_option_license_last_checked', time());

    return ['success' => true, 'message' => 'License key successfully deactivated'];
}