<?php

global $espresso_premium;
if ($espresso_premium != true)
    return;
global $current_user;
global $user_email;
wp_get_current_user();

$userid = $current_user->ID;
$um_fname = $current_user->first_name;
$um_lname = $current_user->last_name;
$um_email = $user_email;

//Event Espresso user meta
$um_address = esc_attr(get_user_meta($userid, 'event_espresso_address', true));
$um_address2 = esc_attr(get_user_meta($userid, 'event_espresso_address2', true));
$um_city = esc_attr(get_user_meta($userid, 'event_espresso_city', true));
$um_state = esc_attr(get_user_meta($userid, 'event_espresso_state', true));
$um_zip = esc_attr(get_user_meta($userid, 'event_espresso_zip', true));
$um_country = esc_attr(get_user_meta($userid, 'event_espresso_country', true));
$um_phone = esc_attr(get_user_meta($userid, 'event_espresso_phone', true));

//WP user meta from other plugins
$wpum_address = esc_attr(get_user_meta($userid, 'address', true));
$wpum_city = esc_attr(get_user_meta($userid, 'city', true));
$wpum_state = esc_attr(get_user_meta($userid, 'state', true));
$wpum_zip = esc_attr(get_user_meta($userid, 'zip', true));
$wpum_country = esc_attr(get_user_meta($userid, 'country', true));
$wpum_phone = esc_attr(get_user_meta($userid, 'phone', true));
$wpum_company = esc_attr(get_user_meta($userid, 'company', true));
$wpum_jobtitle = esc_attr(get_user_meta($userid, 'jobtitle', true));
$wpum_gender = esc_attr(get_user_meta($userid, 'gender', true));
$wpum_timezone = esc_attr(get_user_meta($userid, 'timezone', true));
$wpum_twitter = esc_attr(get_user_meta($userid, 'twitter', true));
$wpum_linkedin = esc_attr(get_user_meta($userid, 'linkedin', true));
$wpum_newsletter = esc_attr(get_user_meta($userid, 'newsletter', true));
$wpum_terms = esc_attr(get_user_meta($userid, 'terms', true));

//Get the options
$member_options = get_option('events_member_settings');
$login_page = empty($member_options['login_page']) ? '' : $member_options['login_page'];
$register_page = empty($member_options['register_page']) ? '' : $member_options['register_page'];
if (isset($member_options['member_only_all']) && $member_options['member_only_all'] == 'Y') {
    $member_only = 'Y';
}
