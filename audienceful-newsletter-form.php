<?php
/**
 * Plugin Name: Audienceful Newsletter Form
 * Description: Simple AJAX newsletter signup form and direct Audienceful API integration.
 * Version: 1.0.0
 * Author: Muhammad Zain
 * License: GPL2+
 * Text Domain: audienceful-form
 * Requires at least: 5.0
 * Requires PHP: 7.4
 */

if (!defined('ABSPATH')) exit;

class Audienceful_Form {
    
    public function __construct() {
        // Load translations
        add_action('plugins_loaded', [$this, 'load_textdomain']);

        // Register settings
        add_action('admin_init', [$this, 'register_settings']);
        add_action('admin_menu', [$this, 'add_settings_page']);

        // Shortcode
        add_shortcode('audienceful_form', [$this, 'render_form']);

        // Enqueue scripts/styles
        add_action('wp_enqueue_scripts', [$this, 'enqueue_assets']);

        // AJAX
        add_action('wp_ajax_audienceful_submit', [$this, 'handle_submission']);
        add_action('wp_ajax_nopriv_audienceful_submit', [$this, 'handle_submission']);

        // Settings link on Plugins page
        add_filter('plugin_action_links_' . plugin_basename(__FILE__), [$this, 'settings_link']);

    }

    public function load_textdomain() {
        load_plugin_textdomain('audienceful-form', false, dirname(plugin_basename(__FILE__)) . '/languages');
    }

    public function register_settings() {
        register_setting('audienceful_options', 'audienceful_api_key', 'sanitize_text_field');
        register_setting('audienceful_options', 'audienceful_success', 'sanitize_text_field');
        register_setting('audienceful_options', 'audienceful_error', 'sanitize_text_field');
    }
    // Add "Settings" link under plugin name on Plugins page
    public function settings_link($links) {
        $settings_link = '<a href="' . esc_url(admin_url('options-general.php?page=audienceful-form')) . '">' 
            . __('Settings', 'audienceful-form') . '</a>';
        array_unshift($links, $settings_link);
        return $links;
    }


    public function add_settings_page() {
        add_options_page(
            __('Audienceful Form Settings', 'audienceful-form'),
            __('Audienceful Form', 'audienceful-form'),
            'manage_options',
            'audienceful-form',
            [$this, 'render_settings_page']
        );
    }

    public function render_settings_page() { ?>
        <div class="wrap">
            <h1><?php _e('Audienceful Newsletter Form Settings', 'audienceful-form'); ?></h1>
            <form method="post" action="options.php">
                <?php settings_fields('audienceful_options'); ?>
                <?php do_settings_sections('audienceful_options'); ?>

                <table class="form-table">
                    <tr>
                        <th><label for="audienceful_api_key"><?php _e('API Key', 'audienceful-form'); ?></label></th>
                        <td><input type="password" name="audienceful_api_key" value="<?php echo esc_attr(get_option('audienceful_api_key')); ?>" class="regular-text"></td>
                    </tr>
                    <tr>
                        <th><label for="audienceful_success"><?php _e('Success Message', 'audienceful-form'); ?></label></th>
                        <td><input type="text" name="audienceful_success" value="<?php echo esc_attr(get_option('audienceful_success', __('Thank you for subscribing!', 'audienceful-form'))); ?>" class="regular-text"></td>
                    </tr>
                    <tr>
                        <th><label for="audienceful_error"><?php _e('Error Message', 'audienceful-form'); ?></label></th>
                        <td><input type="text" name="audienceful_error" value="<?php echo esc_attr(get_option('audienceful_error', __('Something went wrong. Please try again.', 'audienceful-form'))); ?>" class="regular-text"></td>
                    </tr>
                </table>

                <?php submit_button(); ?>
            </form>
        </div>
    <?php }

    public function enqueue_assets() {
        wp_enqueue_style('audienceful-form', plugin_dir_url(__FILE__) . 'assets/css/audienceful-form.css', [], '1.0');
        wp_enqueue_script('audienceful-form', plugin_dir_url(__FILE__) . 'assets/js/audienceful-form.js', ['jquery'], '1.0', true);

        wp_localize_script('audienceful-form', 'audienceful_ajax', [
            'ajax_url' => admin_url('admin-ajax.php'),
            'success_msg' => get_option('audienceful_success', __('Thank you for subscribing!', 'audienceful-form')),
            'error_msg' => get_option('audienceful_error', __('Something went wrong. Please try again.', 'audienceful-form')),
        ]);
    }

    public function render_form() {
        ob_start(); ?>
        <div class="audienceful-wrapper">
            <form class="audienceful-form">
                <input type="email" class="audienceful-email" name="email" placeholder="<?php esc_attr_e('Enter your email', 'audienceful-form'); ?>" required />
                <input type="text" class="audienceful-website" name="website" autocomplete="off" />
                <?php wp_nonce_field('audienceful_form_action', 'audienceful_form_nonce'); ?>
                <button type="submit" class="audienceful-submit">
                    <span class="arrow">➔</span>
                    <span class="spinner"></span>
                </button>
            </form>
            <div class="audienceful-message"></div>
        </div>
        <?php
        return ob_get_clean();
    }

    public function handle_submission() {
        $email = sanitize_email($_POST['email'] ?? '');
        $nonce = $_POST['audienceful_form_nonce'] ?? '';

        if (!wp_verify_nonce($nonce, 'audienceful_form_action')) {
            wp_send_json_error(['message' => __('Security check failed.', 'audienceful-form')]);
        }
        if (!is_email($email)) {
            wp_send_json_error(['message' => __('Invalid email address.', 'audienceful-form')]);
        }

        $api_key = get_option('audienceful_api_key');
        
        $success_msg = get_option('audienceful_success', __('Thank you for subscribing!', 'audienceful-form'));
        $error_msg = get_option('audienceful_error', __('Something went wrong. Please try again.', 'audienceful-form'));

        $response = wp_remote_post('https://app.audienceful.com/api/people/', [
            'headers' => [
                'Content-Type' => 'application/json',
                'X-Api-Key' => $api_key,
            ],
            'body' => json_encode(['email' => $email]),
        ]);

        if (is_wp_error($response)) {
            wp_send_json_error(['message' => $error_msg]);
        }

        $code = wp_remote_retrieve_response_code($response);
        if ($code == 200 || $code == 201) {
            wp_send_json_success(['message' => $success_msg]);
        } else {
            wp_send_json_error(['message' => $error_msg]);
        }
    }
}

new Audienceful_Form();
