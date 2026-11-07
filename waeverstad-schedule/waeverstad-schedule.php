<?php
/**
 * Plugin Name:       Waeverstad Schedule
 * Description:       Het carnavalsprogramma van Waeverstad Feest! als tijdlijn voor telefoon en desktop. Plaats de shortcode [waeverstad_schedule] op de programmapagina.
 * Version:           0.1.0
 * Requires at least: 6.0
 * Requires PHP:      8.0
 * Author:            Bas van Dinther
 * License:           GPL-2.0-or-later
 * Text Domain:       waeverstad-schedule
 */

defined('ABSPATH') || exit;

require_once __DIR__ . '/includes/class-renderer.php';

const WAEVERSTAD_SCHEDULE_SHORTCODE = 'waeverstad_schedule';

/**
 * Loads the schedule. The file path can be overridden with the
 * 'waeverstad_schedule_data_path' filter, e.g. to a file in uploads.
 */
function waeverstad_schedule_data(): ?array
{
    $path = apply_filters('waeverstad_schedule_data_path', __DIR__ . '/data/schedule.json');
    if (!is_readable($path)) {
        return null;
    }
    $data = json_decode((string) file_get_contents($path), true);

    return is_array($data) ? $data : null;
}

add_action('wp_enqueue_scripts', function (): void {
    $url = plugin_dir_url(__FILE__) . 'assets/';
    $dir = __DIR__ . '/assets/';

    wp_register_style('waeverstad-schedule', $url . 'schedule.css', [], (string) filemtime($dir . 'schedule.css'));
    wp_register_script('waeverstad-schedule', $url . 'schedule.js', [], (string) filemtime($dir . 'schedule.js'), ['in_footer' => true, 'strategy' => 'defer']);

    // Print the stylesheet in <head> when the shortcode is on this page (Divi modules included).
    $post = get_post();
    if ($post instanceof WP_Post && str_contains((string) $post->post_content, '[' . WAEVERSTAD_SCHEDULE_SHORTCODE)) {
        wp_enqueue_style('waeverstad-schedule');
    }
});

add_shortcode(WAEVERSTAD_SCHEDULE_SHORTCODE, function ($atts): string {
    $atts = shortcode_atts(['title' => 'Programma'], (array) $atts, WAEVERSTAD_SCHEDULE_SHORTCODE);

    $data = waeverstad_schedule_data();
    if ($data === null) {
        return current_user_can('edit_posts')
            ? '<p><strong>Het programma kon niet worden geladen.</strong> Controleer data/schedule.json in de plugin Waeverstad Schedule.</p>'
            : '';
    }

    wp_enqueue_style('waeverstad-schedule');
    wp_enqueue_script('waeverstad-schedule');

    return (new \Waeverstad\Schedule\Renderer($data, ['title' => (string) $atts['title']]))->render();
});
