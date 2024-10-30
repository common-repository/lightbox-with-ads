<div id="lightbox-with-ads-settings-page">
    <div class="main-title">
        <img class="" src="<?php echo(esc_attr(plugin_dir_url(__DIR__) . 'assets/icon-256x256.png')); ?>">
        <div class="header">
            <div class="plugin-logo">Lightbox With Ads</div>
            <div class="settings-text">Settings</div>
        </div>
    </div>
    <form method="post">
        <section>
            <?php
            $plugin_type = get_option('lightbox_with_ads_option_plugin_type');
            $trial_end_date = get_option('lightbox_with_ads_option_plugin_trial_end_date');
            $license_key = get_option('lightbox_with_ads_option_license_key');
            $license_type = get_option('lightbox_with_ads_option_license_type');
            $license_status = get_option('lightbox_with_ads_option_license_status');

            $license_expire_date_epoch = get_option('lightbox_with_ads_option_license_expire_date');
            if ($license_expire_date_epoch != 'Never') {
                $license_expire_date_formatted = new DateTime();
                $license_expire_date_formatted->setTimestamp($license_expire_date_epoch);
                $license_expire_date_formatted = $license_expire_date_formatted->format('d. m. Y H:i:s');
            }

            $license_last_checked_epoch = get_option('lightbox_with_ads_option_license_last_checked');
            $license_last_checked_formatted = new DateTime();
            $license_last_checked_formatted->setTimestamp($license_last_checked_epoch);
            $license_last_checked_formatted = $license_last_checked_formatted->format('d. m. Y H:i:s');

            $trial_days_end = round(($trial_end_date - time()) / (60 * 60 * 24));
            $trial_ends_message = $trial_days_end . ' days';

            if ($trial_days_end == 0) {
                $trial_ends_message = 'Ends today';
            }

            ?>
            <h1>Account Type -
                <?php if ($plugin_type == 'Premium trial'): ?>
                    <span class="success">PREMIUM TRIAL - <?php echo(esc_html($trial_ends_message)); ?> - <a
                                href="https://lightboxwithads.com" target="_blank">Buy license</a></span>
                <?php elseif ($plugin_type == 'Premium'): ?>
                    <span class="success"><?php echo(strtoupper(esc_html($plugin_type))); ?></span>
                <?php else: ?>
                    <span class="warning"><?php echo(strtoupper(esc_html($plugin_type))); ?> - <a
                                href="https://lightboxwithads.com" target="_blank">Buy license</a></span>
                <?php endif; ?>
            </h1>
            <div class="options-container account-type-option">
                <div class="option">
                    <label for="lightbox_with_ads_option_pc_number_of_images">Your license key</label>
                    <div>
                        <input style="width: 300px" required type="text" id="lightbox_with_ads_option_license_key"
                               name="lightbox_with_ads_option_license_key"
                               class="lightbox_with_ads_option"
                            <?php if ($license_key != ''): ?> readonly <?php endif; ?>
                               value="<?php echo(esc_attr($license_key)); ?>"/>
                    </div>
                    <div class="license-key-buttons">
                        <button type="submit" id="activate-license-button"
                            <?php if ($license_key != ''): ?> class="disabled-button" disabled <?php endif; ?>>
                            Activate license
                        </button>
                        <button type="submit" id="deactivate-license-button" name="deactivate-license-button"
                            <?php if (get_option('lightbox_with_ads_option_license_key') == ''): ?>
                                class="disabled-button" disabled
                            <?php endif; ?>>Deactivate license
                        </button>
                        <?php if ($license_error != ''): ?>
                            <div class="license-error">
                                <?php echo(esc_html($license_error)); ?>
                                <br>
                                If you think this is not right, please contact us at info@wn24.cz
                            </div>
                        <?php endif; ?>
                        <?php if ($license_success != ''): ?>
                            <div class="license-success">
                                <?php echo(esc_html($license_success)); ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="license-info">
                    <?php if (get_option('lightbox_with_ads_option_license_key') != ''): ?>
                        <div class="account-type">License type:
                            <span class="success"><?php echo(esc_html($license_type)); ?></span>
                        </div>
                        <div class="license-status">License status:
                            <span class="<?php if (get_option('lightbox_with_ads_option_license_status') == 'Active') {
                                echo('success');
                            } else {
                                echo('warning');
                            } ?>"><?php echo(esc_html($license_status)); ?></span>
                        </div>
                        <div class="license-expire-type">License expire date:
                            <span class="success"><?php echo(esc_html($license_expire_date_formatted)); ?></span>
                        </div>
                        <div class="license-expire-type">Last checked:
                            <span class="success"><?php echo(esc_html($license_last_checked_formatted)); ?></span></div>
                    <?php endif; ?>
                </div>
            </div>
        </section>
    </form>
    <form method="post">
        <section>
            <h1>Customization</h1>
            <div class="options-container">
                <div class="option">
                    <label for="lightbox_with_ads_option_background_color">Background color</label>
                    <input required type="color" id="lightbox_with_ads_option_background_color"
                           name="lightbox_with_ads_option_background_color"
                           class="lightbox_with_ads_option"
                           value="<?php echo(esc_attr(get_option('lightbox_with_ads_option_background_color'))); ?>"/>
                </div>
                <div class="option">
                    <label for="lightbox_with_ads_option_arrows_color">Arrows color</label>
                    <input required type="color" id="lightbox_with_ads_option_arrows_color"
                           name="lightbox_with_ads_option_arrows_color"
                           class="lightbox_with_ads_option"
                           value="<?php echo(esc_attr(get_option('lightbox_with_ads_option_arrows_color'))); ?>"/>
                </div>
                <div class="option option-with-unit">
                    <label for="lightbox_with_ads_option_arrows_font_size">Arrows size</label>
                    <div>
                        <input required type="number" id="lightbox_with_ads_option_arrows_font_size"
                               name="lightbox_with_ads_option_arrows_font_size"
                               class="lightbox_with_ads_option"
                               value="<?php echo(esc_attr(get_option('lightbox_with_ads_option_arrows_font_size'))); ?>"/>
                        <span class="units">px</span>
                    </div>
                </div>
                <div class="option option-with-unit">
                    <label for="lightbox_with_ads_option_arrows_top_margin">Margin from header (only on mobile)</label>
                    <div>
                        <input required type="number" id="lightbox_with_ads_option_arrows_top_margin"
                               name="lightbox_with_ads_option_arrows_top_margin"
                               class="lightbox_with_ads_option"
                               value="<?php echo(esc_attr(get_option('lightbox_with_ads_option_arrows_top_margin'))); ?>"/>
                        <span class="units">px</span>
                    </div>
                </div>
                <div class="option">
                    <label for="lightbox_with_ads_option_current_img_index_color">Image index color</label>
                    <div>
                        <input required type="color" id="lightbox_with_ads_option_current_img_index_color"
                               name="lightbox_with_ads_option_current_img_index_color"
                               class="lightbox_with_ads_option"
                               value="<?php echo(esc_attr(get_option('lightbox_with_ads_option_current_img_index_color'))); ?>"/>
                    </div>
                </div>
                <div class="option option-with-unit">
                    <label for="lightbox_with_ads_option_image_index_font_size">Image index font size</label>
                    <div>
                        <input required type="number" id="lightbox_with_ads_option_image_index_font_size"
                               name="lightbox_with_ads_option_image_index_font_size"
                               class="lightbox_with_ads_option"
                               value="<?php echo(esc_attr(get_option('lightbox_with_ads_option_image_index_font_size'))); ?>"/>
                        <span class="units">px</span>
                    </div>
                </div>
                <div class="option">
                    <label for="lightbox_with_ads_option_close_button_color">Close button color</label>
                    <div>
                        <input required type="color" id="lightbox_with_ads_option_close_button_color"
                               name="lightbox_with_ads_option_close_button_color"
                               class="lightbox_with_ads_option"
                               value="<?php echo(esc_attr(get_option('lightbox_with_ads_option_close_button_color'))); ?>"/>
                    </div>
                </div>
                <div class="option option-with-unit">
                    <label for="lightbox_with_ads_option_close_button_font_size">Close button size</label>
                    <div>
                        <input required type="number" id="lightbox_with_ads_option_close_button_font_size"
                               name="lightbox_with_ads_option_close_button_font_size"
                               class="lightbox_with_ads_option"
                               value="<?php echo(esc_attr(get_option('lightbox_with_ads_option_close_button_font_size'))); ?>"/>
                        <span class="units">px</span>
                    </div>
                </div>
                <div class="option option-with-unit">
                    <label for="lightbox_with_ads_option_caption_font_size">Image caption font size</label>
                    <div>
                        <input required type="number" id="lightbox_with_ads_option_caption_font_size"
                               name="lightbox_with_ads_option_caption_font_size"
                               class="lightbox_with_ads_option"
                               value="<?php echo(esc_attr(get_option('lightbox_with_ads_option_caption_font_size'))); ?>"/>
                        <span class="units">px</span>
                    </div>
                </div>
            </div>
        </section>
        <section>
            <div class="ad-scripts-header">
                <h1>Google AdSense Scripts</h1>
                <?php if ($plugin_type == 'Free'): ?>
                    <a href="https://lightboxwithads.com" target="_blank" class="premium-warning">BUY PREMIUM TO ACCESS
                        ADS</a>
                <?php else: ?>
                    <a class="premium-warning" style="background-color: rgb(229,97,166);">Premium feature</a>
                <?php endif; ?>
            </div>
            <div class="options-container">
                <div class="option">
                    <label>Desktop</label>
                    <textarea <?php if ($plugin_type == 'Free') {
                        echo('disabled');
                    } ?> id="lightbox_with_ads_option_pc_ad_script"
                         name="lightbox_with_ads_option_pc_ad_script"
                         class="lightbox_with_ads_option"><?php echo(get_option(wp_kses_post('lightbox_with_ads_option_pc_ad_script'))); ?></textarea>
                </div>
                <div class="option">
                    <label>Mobile</label>
                    <textarea <?php if ($plugin_type == 'Free') {
                        echo('disabled');
                    } ?> id="lightbox_with_ads_option_mobile_ad_script"
                         name="lightbox_with_ads_option_mobile_ad_script"
                         class="lightbox_with_ads_option"><?php echo(get_option(wp_kses_post('lightbox_with_ads_option_mobile_ad_script'))); ?></textarea>
                </div>
            </div>
        </section>
        <section>
            <div class="ad-scripts-header">
                <h1>Ad options</h1>
                <?php if ($plugin_type == 'Free'): ?>
                    <a href="https://lightboxwithads.com" target="_blank" class="premium-warning">BUY PREMIUM TO ACCESS
                        ADS</a>
                <?php else: ?>
                    <a class="premium-warning" style="background-color: rgb(229,97,166);">Premium feature</a>
                <?php endif; ?>
            </div>
            <div class="options-container">
                <div class="option">
                    <label for="lightbox_with_ads_option_pc_number_of_images">After how many images seen should ads be
                        refreshed (Desktop)</label>
                    <div>
                        <input <?php if ($plugin_type == 'Free') {
                            echo('disabled');
                        } ?>
                                required type="number" id="lightbox_with_ads_option_pc_number_of_images"
                                name="lightbox_with_ads_option_pc_number_of_images"
                                class="lightbox_with_ads_option"
                                value="<?php echo(esc_attr(get_option('lightbox_with_ads_option_pc_number_of_images'))); ?>"/>
                    </div>
                    <label for="lightbox_with_ads_option_mobile_number_of_images">After how many images seen should ads
                        be shown (Mobile)</label>
                    <div>
                        <input <?php if ($plugin_type == 'Free') {
                            echo('disabled');
                        } ?>
                                required type="number" id="lightbox_with_ads_option_mobile_number_of_images"
                                name="lightbox_with_ads_option_mobile_number_of_images"
                                class="lightbox_with_ads_option"
                                value="<?php echo(esc_attr(get_option('lightbox_with_ads_option_mobile_number_of_images'))); ?>"/>
                    </div>
                </div>
            </div>
        </section>
        <section>
            <h1>Logo in upper left corner</h1>
            <div class="options-container">
                <div class="option">
                    <label for="lightbox_with_ads_option_logo_image">Image</label>
                    <div>
                        <img id="logo-image-preview"
                             src="<?php echo(esc_attr(wp_get_attachment_url(get_option('lightbox_with_ads_option_logo_image')))); ?>"
                             width="<?php echo(esc_attr(get_option('lightbox_with_ads_option_logo_width'))); ?>"
                             height="<?php echo(esc_attr(get_option('lightbox_with_ads_option_logo_height'))); ?>"/>
                        <div>
                            <input required type="hidden" name="lightbox_with_ads_option_logo_image"
                                   id="lightbox_with_ads_option_logo_image"
                                   value="<?php echo(esc_attr(get_option('lightbox_with_ads_option_logo_image'))); ?>"/>
                            <button type="submit" id="upload-logo-image-button">Pick from media gallery</button>
                        </div>
                    </div>
                </div>
                <div class="option option-with-unit">
                    <label for="lightbox_with_ads_option_logo_width">Width</label>
                    <div>
                        <input required type="number" id="lightbox_with_ads_option_logo_width"
                               name="lightbox_with_ads_option_logo_width"
                               class="lightbox_with_ads_option"
                               value="<?php echo(esc_attr(get_option('lightbox_with_ads_option_logo_width'))); ?>"/>
                        <span class="units">px</span>
                    </div>
                </div>
                <div class="option option-with-unit">
                    <label for="lightbox_with_ads_option_logo_height">Height</label>
                    <div>
                        <input required type="number" id="lightbox_with_ads_option_logo_height"
                               name="lightbox_with_ads_option_logo_height"
                               class="lightbox_with_ads_option"
                               value="<?php echo(esc_attr(get_option('lightbox_with_ads_option_logo_height'))); ?>"/>
                        <span class="units">px</span>
                    </div>
                </div>
            </div>
        </section>
        <section>
            <div class="buttons-container">
                <button type="submit" name="submit" id="submit">Save Changes</button>
                <button type="button" id="reset-changes" class="settings-btn">Reset Changes</button>
            </div>
        </section>
    </form>
</div>