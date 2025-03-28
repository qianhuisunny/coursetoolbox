<?php
/**
 * Assets class
 *
 * @package HostingerAffiliatePlugin
 */

namespace Hostinger\AffiliatePlugin\Setup;

use Hostinger\AffiliatePlugin\Admin\Options\PluginOptions;
use Hostinger\AffiliatePlugin\Admin\PluginSettings;
use Hostinger\AffiliatePlugin\Functions;
use Hostinger\AffiliatePlugin\Localization\AdminTranslations;

/**
 * Avoid possibility to get file accessed directly
 */
if ( ! defined( 'ABSPATH' ) ) {
    die;
}

/**
 * Assets class
 */
class Assets {
    /**
     * @var Functions
     */
    private Functions $functions;

    private PluginSettings $plugin_settings;

    /**
     * @param Functions $functions
     */
    public function __construct( Functions $functions, PluginSettings $plugin_settings ) {
        $this->functions       = $functions;
        $this->plugin_settings = $plugin_settings;
    }

    /**
     * Run actions or/and hooks
     */
    public function init(): void {
        if ( $this->functions->need_to_load_affiliate_assets() ) {
            add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
        }
    }

    /**
     * Enqueue admin assets
     *
     * @return void
     */
    public function admin_enqueue_scripts(): void {
        wp_register_style(
            'hostinger-affiliate-plugin-backend',
            HOSTINGER_AFFILIATE_PLUGIN_URL . 'assets/dist/backend.css',
            false,
            filemtime( HOSTINGER_AFFILIATE_PLUGIN_DIR . 'assets/dist/backend.js' )
        );

        wp_enqueue_style(
            'hostinger-affiliate-plugin-backend'
        );

        wp_enqueue_script(
            'hostinger-affiliate-plugin-backend',
            HOSTINGER_AFFILIATE_PLUGIN_URL . 'assets/dist/backend.js',
            array(),
            filemtime( HOSTINGER_AFFILIATE_PLUGIN_DIR . 'assets/dist/backend.js' ),
            true
        );

        $plugin_settings = $this->plugin_settings->get_plugin_settings()->to_array();

        $user = wp_get_current_user();

        $table_args = array(
            'post_type'   => 'hst_affiliate_table',
            'post_status' => 'publish',
            'numberposts' => -1,
        );

        $tables = get_posts( $table_args );

        wp_localize_script(
            'hostinger-affiliate-plugin-backend',
            'hst_affiliate_data',
            array(
                'site_url'                   => get_site_url(),
                'ajax_url'                   => admin_url( 'admin-ajax.php' ),
                'plugin_url'                 => HOSTINGER_AFFILIATE_PLUGIN_URL,
                'rest_base_url'              => esc_url_raw( rest_url() ),
                'nonce'                      => wp_create_nonce( 'wp_rest' ),
                'block'                      => array(
                    'user_display_name' => $user->data->display_name,
                    'site_url'          => get_site_url(),
                    'status_constants'  => array(
                        'connected'    => PluginOptions::STATUS_CONNECTED,
                        'disconnected' => PluginOptions::STATUS_DISCONNECTED,
                    ),
                    'connection_status' => ( ! empty( $plugin_settings['connection_status'] ) ? $plugin_settings['connection_status'] : PluginOptions::STATUS_DISCONNECTED ),
                    'tracking_id'       => ( ! empty( $plugin_settings['amazon']['tracking_id'] ) ) ? $plugin_settings['amazon']['tracking_id'] : '',
                    'tables'            => $tables,
                    'domain_settings'   => array(
                        'domain' => $this->functions->get_host_info(),
                    ),
                    'amazon_api_used'   => $this->plugin_settings->get_plugin_settings()->amazon->use_amazon_api(),
                ),
                'amazon_dashboard_page_slug' => Functions::AMAZON_AFFILIATE_DASHBOARD_PAGE_SLUG,
                'amazon_api_keys_page_slug'  => Functions::AMAZON_AFFILIATE_API_KEYS_PAGE_SLUG,
                'amazon_domain_locales'      => Functions::AMAZON_AFFILIATE_DOMAIN_LOCALES,
                'amazon_dashboard_url'       => $this->functions->get_amazon_affiliate_dashboard_url(),
                'user_locale'                => get_user_locale(),
                'translations'               => AdminTranslations::get_values(),
            )
        );
    }
}
