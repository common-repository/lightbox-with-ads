=== Lightbox With Ads ===
Contributors: wn24cz
Tags: lightbox, gallery, ads, ad, images
Tested up to: 6.1.1
Requires at least: 5.5
Stable tag: 1.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Version: 1.0

== Description ==

Lightbox With Ads is a flexible and responsive lightbox that allows you to present images from post as one gallery in a different url.
You can purchase Premium that allows you to add ads from Google AdSense, or test the feature for free for 14 days.

== Screenshots ==

1. Installation 1)
2. Installation 2)
3. Configuration 1)
4. Configuration 2)
5. Inside gallery on mobile (placeholder will be ad if you use premium)
6. Inside gallery on desktop (placeholder will be ad if you use premium)

== Instalation ==

Prerequisites:
You need to have permalinks set to "post name" in Settings -> Permalinks -> Common Settings

1. Download the plugin to your Wordpress site
2. Install the plugin
3. Activate the plugin
4. Customize the interface and appearance in Settings
5. Paste ad code from Google Adsense (recommended size is 300x300 for mobile and 300x600 for desktop)
   Beacuse direct pasting of scripts is not allowed by WordPress, the pasted code has to be the exact code you copied from your Google AdSense account.

How the code should look
(the <ins> element will vary, but it HAS to have "data-ad-client" property, that should have the same ca-pub number as the script above it):
<script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-1234567891234567"
     crossorigin="anonymous"></script>
<!-- Ad unit name -->
<ins class="adsbygoogle"
     style="display:inline-block;width:300px;height:300px"
     data-ad-client="ca-pub-1234567891234567"
     data-ad-slot="123456789"></ins>
<script>
     (adsbygoogle = window.adsbygoogle || []).push({});
</script>


== Frequently Asked Questions ==

= Is Lightbox with ads free? =

The whole plugin that you download is free. It also comes with a option to buy Premium, that allows you to add ads to the gallery.
You can test this feature for free for 14 days.