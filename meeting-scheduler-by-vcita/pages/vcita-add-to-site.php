<?php
wp_enqueue_script('vcita-colorpicker', WPSHD_VCITA_ASSETS_PATH.'/assets/extra/colorpicker/js/colorpicker.js');
wp_enqueue_script('vcita-add-to-site-script', WPSHD_VCITA_ASSETS_PATH.'assets/js/add_to_site_v.js');

wp_localize_script(
	'vcita-add-to-site-script',
	'vcitaSchedulerData',
	[
		'ajaxUrl' => admin_url( 'admin-ajax.php' ),
		'nonce'   => wp_create_nonce( 'wpshd_vcita_nonce_action' ),
	]
);

wp_enqueue_script('jquery-ui-tooltip');

wp_enqueue_style('vcita-add-to-site-style', WPSHD_VCITA_ASSETS_PATH.'assets/style/add_to_site_v.css');
wp_enqueue_style('vcita-colorpicker', WPSHD_VCITA_ASSETS_PATH.'/assets/extra/colorpicker/css/colorpicker.css');
wp_enqueue_style('jquery-ui-tooltip');

$wpshd_vcita_widget = (array)get_option(WPSHD_VCITA_WIDGET_KEY);

$wpshd_vcita_widget_config = create_default_settings_data($wpshd_vcita_widget);
foreach ($wpshd_vcita_widget_config as $key => $val) {
    $wpshd_vcita_widget[$key] = $val;
}

echo '<style>:root { --vcita-schedule-button-button-color: ' . $wpshd_vcita_widget['btn_color'] . ';
        --vcita-schedule-button-text-color: ' . $wpshd_vcita_widget['txt_color'] . ';
        --vcita-schedule-button-hover-color: ' . $wpshd_vcita_widget['hover_color'] . '}</style>';

$needs_reconnect = wpshd_vcita_check_need_to_reconnect($wpshd_vcita_widget);

add_filter('show_admin_bar', '__return_false');

if (WPSHD_VCITA_ANOTHER_PLUGIN) {
    $wpshd_vcita_widget['show_on_site'] = 0;
    $wpshd_vcita_widget['contact_page_active'] = 0;
    $wpshd_vcita_widget['calendar_page_active'] = 0;
}

$av_plugin_list = wp_cache_get('WPSHD_VCITA_ANOTHER_PLUGIN_LIST');
?>
<div class="vcita-wrap" dir="ltr">
    <?php require_once WP_PLUGIN_DIR . "/" . WPSHD_VCITA_WIDGET_UNIQUE_ID . '/php_assets/admin_header.php' ?>
    <!--
    <?php if ($wpshd_vcita_widget['uid']) { ?>
        <div class="vcita-wrap-left-banner">
            <img onclick="vcita_show_av_plans(event,'wp_sched_upgrade_banner_clicked')" src="<?php echo WPSHD_VCITA_ASSETS_PATH ?>/images/upgrade_banner.png"/>
        </div>
    <?php } ?>
    -->
    <div class="vcita-wrap-inner">
        <?php if (WPSHD_VCITA_ANOTHER_PLUGIN) { ?>
            <?php if ($av_plugin_list) {
                $plugin_str = '';

                foreach ($av_plugin_list as $vcita_plugin_data) {
                    $plugin_str .= ' or ' . $vcita_plugin_data["Name"];
                }

                $plugin_str = substr($plugin_str, 4);
            ?>
                <div class="vcita__page__error-container">
                    As you already use <b><?php echo $plugin_str ?></b> by vcita, you can edit the scheduling widget there.
                    Alternatively you can deactivate all other vcita plugins installed on your site and configure your scheduling
                    widget using this plugin. Set up widget using
                    <a href="<?php echo get_admin_url('', '', 'admin') . 'admin.php?page=' . urlencode($vcita_plugin_data['url']) ?>">
                        <?php echo $plugin_str ?>
                    </a>
                    or
                    <a href="javascript:void(0)" onclick="jQuery.ajax({'url':`${window.$_ajaxurl}?action=vcita_deactivate_others&rescan=1`,'method':'GET','success':(resp)=>{try{if(resp=='success'){window.location.reload()}else{VcitaUI.hidePopup()}}catch(err){console.log(err)}}})">
                        deactivate all other vcita plugins
                    </a>
                </div>
            <?php } ?>
        <?php } else { ?>
            <div class="vcita__page__inner__section <?php echo $wpshd_vcita_widget['show_on_site'] && (($wpshd_vcita_widget['uid'] || !$wpshd_vcita_widget['vcita_design'])) ? 'no-margin' : '' ?>">
                <h3>
                    <?php echo $wpshd_vcita_widget['migrated'] ? __('Show widget on site', 'meeting-scheduler-by-vcita') : __('Show on site', 'meeting-scheduler-by-vcita') ?>
                    <div class="vcita__toggle__checkbox-container" style="float: right">
                        <input type="checkbox"
                            <?php echo WPSHD_VCITA_ANOTHER_PLUGIN ? 'disabled' : '' ?>
                            name="show_on_site" id="show_on_site" class="vcita__toggle__checkbox"
                            <?php echo $wpshd_vcita_widget['show_on_site'] ? 'checked' : '' ?>
                            value="<?php echo $wpshd_vcita_widget['show_on_site'] ?>"
                            onchange="vcita_widget_show_toggle(this,this.checked)">
                        <label class="input__toggle" for="show_on_site"><span></span></label>
                    </div>
                    <?php if ($wpshd_vcita_widget['uid'] && !$needs_reconnect) { ?>
                        <small class="vcita__toggle__checkbox-show-text<?php echo($wpshd_vcita_widget['show_on_site'] ? '' : ' hidden') ?>">
                            <?php echo __('Hooray! Your clients can book with you right from your site', 'meeting-scheduler-by-vcita') ?>
                        </small>
                    <?php } else { ?>
                        <small class="vcita__toggle__checkbox-show-text<?php echo($wpshd_vcita_widget['show_on_site'] ? '' : ' hidden') ?>">
                            <?php echo __('The widget will be added to your site once you connect to vcita', 'meeting-scheduler-by-vcita') ?>
                            <p><button class="vcita__btn__blue start-signup-add clickme">
                                <?php echo __('Connect to vcita', 'meeting-scheduler-by-vcita') ?>
                            </button></p>
                        </small>
                    <?php } ?>
                    <small class="vcita__toggle__checkbox-hide-text<?php echo(!$wpshd_vcita_widget['show_on_site'] ? '' : ' hidden') ?>">
                        <?php if (WPSHD_VCITA_ANOTHER_PLUGIN) { ?>
                            <?php echo __('The scheduling widget is already shown on your site by another vcita plugin you use. Hence, we disabled the option to turn this switch on.', 'meeting-scheduler-by-vcita') ?>
                        <?php } else { ?>
                            <?php echo __('Turn the switch on to add vcita`s scheduling widget to your site!', 'meeting-scheduler-by-vcita') ?>
                            <?php if (!$wpshd_vcita_widget['uid'] || $needs_reconnect) { ?>
                                <p><button class="vcita__btn__blue start-signup-add clickme">
                                    <?php echo __('Connect to vcita', 'meeting-scheduler-by-vcita') ?>
                                </button></p>
                            <?php } ?>
                        <?php } ?>
                    </small>
                </h3>
            </div>
            <?php if ($wpshd_vcita_widget['uid'] || !$wpshd_vcita_widget['vcita_design']) { ?>
                <div class="vcita__page__inner__section <?php echo !$wpshd_vcita_widget['show_on_site'] ? 'hidden' : '' ?>">
                    <form class="vcita__page__inner__section-banner <?php echo !$wpshd_vcita_widget['vcita_design'] ? 'content-stretched' : '' ?>">
                        <div class="vcita__page__inner__section-banner-img <?php echo $wpshd_vcita_widget['vcita_design'] ? 'hidden' : '' ?>">
                            <header>
                                <?php echo __('Mobile preview', 'meeting-scheduler-by-vcita') ?>
                                <div><?php echo __('(also supported on desktop)', 'meeting-scheduler-by-vcita') ?></div>
                            </header>
                            <div class="vcita__widget__preview__container">
                                <div class="vcita__widget__preview__container-inner <?php echo !$wpshd_vcita_widget['widget_show'] ? 'no-background' : '' ?>">
                                    <div class="vcita__widget__preview__container-inner-powered <?php echo !$wpshd_vcita_widget['widget_show'] ? 'hidden_from_view' : '' ?>">
                                        <!--Powered by vcita-->
                                    </div>
                                    <div class="vcita__widget__preview__container-inner-header flex vcentered <?php echo !$wpshd_vcita_widget['widget_show'] ? 'hidden_from_view' : '' ?>">
                                        <?php if (!empty($wpshd_vcita_widget['widget_img'])) {
                                            echo wp_get_attachment_image($wpshd_vcita_widget['widget_img']);
                                        } ?>
                                        <span>
                                            <?php echo !empty($wpshd_vcita_widget['widget_title']) ? stripslashes($wpshd_vcita_widget['widget_title']) : __('Let\'s talk!', 'meeting-scheduler-by-vcita') ?>
                                        </span>
                                    </div>
                                    <div class="vcita__widget__preview__container-inner-text <?php echo !$wpshd_vcita_widget['widget_show'] ? 'hidden_from_view' : '' ?>">
                                        <?php echo !empty($wpshd_vcita_widget['widget_text']) ? stripslashes($wpshd_vcita_widget['widget_text']) : __('Thanks for stopping by! We’re here to help…', 'meeting-scheduler-by-vcita') ?>
                                    </div>
                                    <div class="vcita__widget__preview__container-inner-button">
                                        <button onclick="event.preventDefault();event.stopPropagation();" class="vcita_schedule_button<?php echo !$wpshd_vcita_widget['widget_show'] ? ' big' : '' ?>">
                                            <?php echo !empty($wpshd_vcita_widget['btn_text']) ? stripslashes($wpshd_vcita_widget['btn_text']) : __('Schedule Now', 'meeting-scheduler-by-vcita') ?>
                                        </button>
                                        <!--<span class="vcita_burger_btn"><span></span></span>-->
                                    </div>
                                </div>
                                <iframe id="preview_iframe" src="//<?php echo $_SERVER['SERVER_NAME'] ?>?WPSCHD_DISABLE_BUTTON=1" width="153" height="335"></iframe>
                                <script type="text/javascript">
                                    document.querySelector('#preview_iframe').addEventListener('load', function () {
                                        this.contentWindow.document.head.insertAdjacentHTML('afterbegin', '<style>html{zoom:0.4}#wpadminbar{display:none!important}</style>');
                                    })
                                </script>
                                <img src="<?php echo WPSHD_VCITA_ASSETS_PATH ?>/images/assets/phone-mockup-2.svg" alt="phone mockup"/>
                            </div>
                        </div>
                        <?php if ($wpshd_vcita_widget['uid']) { ?>
                            <section>
                                <h3 class="no-margin">
                                    <?php echo __('Take design from', 'meeting-scheduler-by-vcita') ?>&nbsp;
                                    <i class="vcita_info" title="<?php echo __('You can either set the widget design here, or on vcita website if you wish to utilize more advanced features', 'meeting-scheduler-by-vcita') ?>">i</i>
                                </h3>
                                <div class="vcita__input__select txt_input">
                                    <select name="design" onchange="vcita_toggle_design_view(this,this.value)">
                                        <option value="0" <?php echo $wpshd_vcita_widget['vcita_design'] ? '' : 'selected' ?>>
                                            <?php echo __('Plugin', 'meeting-scheduler-by-vcita') ?>
                                        </option>
                                        <option value="1" <?php echo $wpshd_vcita_widget['vcita_design'] ? 'selected' : '' ?>>
                                            vcita
                                        </option>
                                    </select>
                                    <div class="vcita__input__select-selected">
                                        <?php echo __('Plugin', 'meeting-scheduler-by-vcita') ?>
                                    </div>
                                </div>
                                <a href="javascript:void(0)" onclick="vcita_show_edit_livesite(event)">
                                    <?php echo __('Edit on vcita', 'meeting-scheduler-by-vcita') ?>
                                </a>
                            </section>
                        <?php } ?>
                        <section <?php echo $wpshd_vcita_widget['vcita_design'] ? 'class="hidden"' : '' ?>>
                            <h3 <?php echo !($wpshd_vcita_widget['migrated'] && $wpshd_vcita_widget['uid']) ? 'class="no-margin"' : '' ?>>
                                <?php echo __('Design', 'meeting-scheduler-by-vcita') ?>
                            </h3>
                        </section>
                        <section <?php echo $wpshd_vcita_widget['vcita_design'] ? 'class="hidden"' : '' ?>>
                            <h4><?php echo __('Button text', 'meeting-scheduler-by-vcita') ?></h4>
                            <input type="text" placeholder="<?php echo __('Schedule Now', 'meeting-scheduler-by-vcita') ?>" style="width: 100%;" class="txt_input" name="btn_text" oninput="vcita_design_change(this)" value="<?php echo stripslashes($wpshd_vcita_widget['btn_text']) ?>">
                        </section>
                        <section class="divided flex flex-wrap hspaced <?php echo $wpshd_vcita_widget['vcita_design'] ? 'hidden' : '' ?>">
                            <div>
                                <h4><?php echo __('Button color', 'meeting-scheduler-by-vcita') ?></h4>
                                <span class="color_input" style="--selected-color: <?php echo $wpshd_vcita_widget['btn_color'] ?>" selected-color="<?php echo $wpshd_vcita_widget['btn_color'] ?>" style="width: 100%;">
                                    <input type="text" placeholder="#01DCF7" class="txt_input" name="btn_color" readonly value="<?php echo $wpshd_vcita_widget['btn_color'] ?>">
                                </span>
                            </div>
                            <div>
                                <h4><?php echo __('Text color', 'meeting-scheduler-by-vcita') ?></h4>
                                <span class="color_input" style="--selected-color: <?php echo $wpshd_vcita_widget['txt_color'] ?>" selected-color="<?php echo $wpshd_vcita_widget['txt_color'] ?>" style="width: 100%;">
                                    <input type="text" placeholder="#FFFFFF" class="txt_input" name="txt_color" readonly value="<?php echo $wpshd_vcita_widget['txt_color'] ?>">
                                </span>
                            </div>
                            <div>
                                <h4><?php echo __('Hover color', 'meeting-scheduler-by-vcita') ?></h4>
                                <span class="color_input" style="--selected-color: <?php echo $wpshd_vcita_widget['hover_color'] ?>" selected-color="<?php echo $wpshd_vcita_widget['hover_color'] ?>" style="width: 100%;">
                                    <input type="text" placeholder="#01DCF7" class="txt_input" name="hover_color" readonly value="<?php echo $wpshd_vcita_widget['hover_color'] ?>">
                                </span>
                            </div>
                        </section>
                        <section <?php echo $wpshd_vcita_widget['vcita_design'] ? 'class="hidden"' : '' ?>>
                            <h3>
                                <?php echo __('Pop up after 5 seconds', 'meeting-scheduler-by-vcita') ?>
                                <div class="vcita__toggle__checkbox-container" style="float: right">
                                    <input type="checkbox" id="widget_show" class="vcita__toggle__checkbox" name="widget_show" onchange="vcita_widget_popup_toggle(this.checked)" <?php echo $wpshd_vcita_widget['widget_show'] ? 'checked' : '' ?>>
                                    <label class="input__toggle" for="widget_show"><span></span></label>
                                </div>
                            </h3>
                        </section>
                        <section class="vcita_section_toggle-view <?php echo !$wpshd_vcita_widget['widget_show'] || $wpshd_vcita_widget['vcita_design'] ? 'hidden_from_view' : '' ?>">
                            <h4><?php echo __('Widget title', 'meeting-scheduler-by-vcita') ?></h4>
                            <input type="text" placeholder="<?php echo __('Let\'s Talk!', 'meeting-scheduler-by-vcita') ?>" style="width: 100%;" oninput="vcita_design_change(this)" class="txt_input" name="widget_title" value="<?php echo stripslashes($wpshd_vcita_widget['widget_title']) ?>">
                        </section>
                        <section class="vcita_section_toggle-view <?php echo !$wpshd_vcita_widget['widget_show'] || $wpshd_vcita_widget['vcita_design'] ? 'hidden_from_view' : '' ?>">
                            <h4><?php echo __('Widget text', 'meeting-scheduler-by-vcita') ?></h4>
                            <input type="text" placeholder="<?php echo __('Thanks for stopping by! We’re here to help…', 'meeting-scheduler-by-vcita') ?>" style="width: 100%;" oninput="vcita_design_change(this)" class="txt_input" name="widget_text" value="<?php echo stripslashes($wpshd_vcita_widget['widget_text']) ?>">
                        </section>
                        <section class="vcita_section_toggle-view vcita_image_attach_container <?php echo !$wpshd_vcita_widget['widget_show'] || $wpshd_vcita_widget['vcita_design'] ? 'hidden_from_view' : '' ?>">
                            <h4><?php echo __('Widget image (Optional)', 'meeting-scheduler-by-vcita') ?></h4>
                            <input type="file" name="widget_img" accept="image/*" onchange="vcita_onFileInputChange(this)">
                            <input type="text" placeholder="Upload image" style="width: 100%;" class="file_input" readonly onclick="this.previousElementSibling.click()">
                            <a href="javascript:void(0)" onclick="vcita_clear_fileinput(event,this)">
                                <?php echo __('Clear', 'meeting-scheduler-by-vcita') ?>
                            </a>
                            <div class="vcita_selected_image_wrapper">
                                <?php if (!empty($wpshd_vcita_widget['widget_img'])) { ?>
                                    <?php echo wp_get_attachment_image($wpshd_vcita_widget['widget_img']); ?>
                                    <span class="delete_button vcita__btn__close" onclick="vcita_del_attachment(this)">
                                        <input type="checkbox" class="onchange_hidden" id="widget_img_clear" name="widget_img_clear" onchange="this.value=this.checked?1:0">
                                        <label for="widget_img_clear" class="onchange_hidden"></label>
                                    </span>
                                <?php } ?>
                            </div>
                        </section>
                        <section <?php echo $wpshd_vcita_widget['vcita_design'] ? 'class="hidden"' : '' ?>>
                            <button class="vcita__btn__blue" onclick="event.preventDefault();event.stopPropagation();vcita_save_design(this,this.closest('form'));">
                                <?php echo __('Save', 'meeting-scheduler-by-vcita') ?>
                            </button>
                        </section>
                        <section <?php echo $wpshd_vcita_widget['vcita_design'] ? 'class="hidden"' : '' ?>>
                            <h4>
                                <?php echo __('Not satisfied with the result?', 'meeting-scheduler-by-vcita') ?>
                                &nbsp;&nbsp;
                                <a href="javascript:void(0)" onclick="vcita_reset_design(this)">
                                    <?php echo __('Reset to default', 'meeting-scheduler-by-vcita') ?>
                                </a>
                            </h4>
                        </section>
                    </form>
                </div>
            <?php } ?>
            <?php if ($wpshd_vcita_widget['uid'] || $needs_reconnect) { ?>
                <div class="vcita__page__inner__section no-hide">
                    <h3>
                        <?php echo __('Contact Form', 'meeting-scheduler-by-vcita') ?>
                        <div style="float:right;text-align:right">
                            <div class="vcita__toggle__checkbox-container">
                                <input type="checkbox" name="contact_page_active"
                                    id="contact_page_active" <?php echo WPSHD_VCITA_ANOTHER_PLUGIN ? 'disabled' : '' ?>
                                    class="vcita__toggle__checkbox" <?php echo $wpshd_vcita_widget['contact_page_active'] ? 'checked' : '' ?>
                                    value="<?php echo $wpshd_vcita_widget['contact_page_active'] ?>"
                                    onchange="vcita_switch_contact_page(event,this)">
                                <label class="input__toggle" for="contact_page_active"><span></span></label>
                            </div>
                            <div class="<?php echo $wpshd_vcita_widget['contact_page_active'] ? '' : 'hidden' ?>">
                                <a href="<?php echo wpshd_vcita_get_contact_url($wpshd_vcita_widget);?>" target="_blank">
                                    <?php echo __('Preview', 'meeting-scheduler-by-vcita') ?>
                                </a>
                            </div>
                        </div>
                        <?php if (!WPSHD_VCITA_ANOTHER_PLUGIN) { ?>
                            <small class="vcita__toggle__checkbox-hide-text<?php echo(!$wpshd_vcita_widget['contact_page_active'] ? '' : ' hidden') ?>">
                                <?php echo __('Turn switch on to add your vcita contact form on your website', 'meeting-scheduler-by-vcita') ?>
                            </small>
                            <small class="vcita__toggle__checkbox-hide-text<?php echo($wpshd_vcita_widget['contact_page_active'] ? '' : ' hidden') ?>">
                                <?php echo __('Your vcita contact form is active on your site.', 'meeting-scheduler-by-vcita') ?>
                                <a href="https://app.<?php echo WPSHD_VCITA_SERVER_BASE ?>/app/my-livesite?section=website-widgets" target="_blank">
                                    <?php echo __('Edit form', 'meeting-scheduler-by-vcita') ?>
                                </a>
                            </small>
                        <?php } else { ?>
                            <small class="vcita__toggle__checkbox-hide-text">
                                <?php echo __('The scheduling widget is already presented on your site. We block the option to turn contact page on here as well.', 'meeting-scheduler-by-vcita') ?>
                            </small>
                        <?php } ?>
                    </h3>
                </div>
                <div class="vcita__page__inner__section no-hide">
                    <h3>
                        <?php echo __('Scheduling Page', 'meeting-scheduler-by-vcita') ?>
                        <div style="float:right;text-align:right">
                            <div class="vcita__toggle__checkbox-container">
                                <input type="checkbox" name="calendar_page_active"
                                    id="calendar_page_active" <?php echo WPSHD_VCITA_ANOTHER_PLUGIN ? 'disabled' : '' ?>
                                    class="vcita__toggle__checkbox" <?php echo $wpshd_vcita_widget['calendar_page_active'] ? 'checked' : '' ?>
                                    value="<?php echo $wpshd_vcita_widget['calendar_page_active'] ?>"
                                    onchange="vcita_switch_calendar_page(event,this)">
                                <label class="input__toggle" for="calendar_page_active"><span></span></label>
                            </div>
                            <div class="<?php echo $wpshd_vcita_widget['calendar_page_active'] ? '' : 'hidden' ?>">
                                <a href="<?php echo wpshd_vcita_get_schedule_url($wpshd_vcita_widget);?>" target="_blank">
                                    <?php echo __('Preview', 'meeting-scheduler-by-vcita') ?>
                                </a>
                            </div>
                        </div>
                        <?php if (!WPSHD_VCITA_ANOTHER_PLUGIN) { ?>
                            <small class="vcita__toggle__checkbox-hide-text<?php echo(!$wpshd_vcita_widget['calendar_page_active'] ? '' : ' hidden') ?>">
                                <?php echo __('Turn switch on to add a scheduling page to your website', 'meeting-scheduler-by-vcita') ?>
                            </small>
                            <small class="vcita__toggle__checkbox-hide-text<?php echo($wpshd_vcita_widget['calendar_page_active'] ? '' : ' hidden') ?>">
                                <?php echo __('Your scheduling page is active on your site', 'meeting-scheduler-by-vcita') ?>
                            </small>
                        <?php } else { ?>
                            <small class="vcita__toggle__checkbox-hide-text">
                                <?php echo __('The scheduling widget is already presented on your site. We block the option to turn scheduling page on here as well.', 'meeting-scheduler-by-vcita') ?>
                            </small>
                        <?php } ?>
                    </h3>
                </div>
            <?php } ?>
            <?php if (($wpshd_vcita_widget['uid'] || $needs_reconnect) && !WPSHD_VCITA_ANOTHER_PLUGIN) { ?>
                <div class="vcita__page__inner__section no-hide">
                    <h3>
                        <?php echo __('More options', 'meeting-scheduler-by-vcita') ?>
                        <small>
                            <?php echo __('Enable clients to book meetings with you right from your Facebook / Google page', 'meeting-scheduler-by-vcita') ?>
                        </small>
                    </h3>
                    <div class="vcita__page__inner__section-no-padding flex flex-wrap hspaced">
                        <div class="vcita__page__add-to-site__more-options">
                            <img src="<?php echo WPSHD_VCITA_ASSETS_PATH ?>/images/assets/white-copy.png"/>
                            <div>
                                <img src="<?php echo WPSHD_VCITA_ASSETS_PATH ?>/images/assets/google-2015-logo.png"/>
                                <article>
                                    <?php echo __('Add a book button to your Google page', 'meeting-scheduler-by-vcita') ?>
                                </article>
                                <a href="javascript:void(0)" onclick="VcitaUI.openSyncGoogleWin()">
                                    <?php echo __('Add Google Reserve', 'meeting-scheduler-by-vcita') ?>
                                </a>
                            </div>
                        </div>
                        <div class="vcita__page__add-to-site__more-options">
                            <img src="<?php echo WPSHD_VCITA_ASSETS_PATH ?>/images/assets/white.png"/>
                            <div>
                                <img style="margin-bottom:5px" src="<?php echo WPSHD_VCITA_ASSETS_PATH ?>/images/assets/facebook-logo-2019.png"/>
                                <article>
                                    <?php echo __('Add a book button to your Facebook page', 'meeting-scheduler-by-vcita') ?>
                                </article>
                                <a href="javascript:void(0)" onclick="VcitaUI.openSyncFacebookWin()">
                                    <?php echo __('Add on Facebook', 'meeting-scheduler-by-vcita') ?>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php } ?>
        <?php } ?>
        <?php require_once WP_PLUGIN_DIR.'/'.WPSHD_VCITA_WIDGET_UNIQUE_ID.'/php_assets/admin_footer.php'; ?>
    </div>
</div>
