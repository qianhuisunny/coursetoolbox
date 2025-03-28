<?php

namespace Hostinger\AffiliatePlugin\Amplitude;

use Hostinger\AffiliatePlugin\Admin\PluginSettings;
use Hostinger\Amplitude\AmplitudeManager;

if ( ! defined( 'ABSPATH' ) ) {
    die;
}

class Events {
    private const AMPLITUDE_ENDPOINT = '/v3/wordpress/plugin/trigger-event';

    private PluginSettings $plugin_settings;
    private AmplitudeManager $amplitude_manager;

    public function __construct( PluginSettings $plugin_settings, AmplitudeManager $amplitude_manager ) {
        $this->plugin_settings   = $plugin_settings;
        $this->amplitude_manager = $amplitude_manager;
    }

    public function init() {
        add_action( 'transition_post_status', array( $this, 'track_published_post' ), 10, 3 );
    }

    public function affiliate_created( string $layout = '' ) {
        if ( empty( $layout ) ) {
            return;
        }

        $endpoint = self::AMPLITUDE_ENDPOINT;

        $params = array(
            'action'      => Actions::AFFILIATE_CREATE,
            'layout_type' => $layout,
        );

        $this->send_request( $endpoint, $params );
    }

    public function affiliate_content_published( string $post_type, int $post_id ): void {
        $endpoint = self::AMPLITUDE_ENDPOINT;
        $params   = array(
            'action' => Actions::AFFILIATE_PUBLISHED,
        );

        $this->send_request( $endpoint, $params );
    }

    public function track_published_post( string $new_status, string $old_status, \WP_Post $post ): void {
        $post_id = $post->ID;

        static $is_action_executed = array();
        if ( isset( $is_action_executed[ $post_id ] ) ) {
            return;
        }

        if ( ( 'draft' === $old_status || 'auto-draft' === $old_status ) && $new_status === 'publish' ) {
            if ( ( has_block( 'hostinger-affiliate-plugin/block', $post )
                   || has_shortcode( $post->post_content, 'hostinger-affiliate-table' ) )
                 && ! wp_is_post_revision( $post_id ) ) {
                $post_type = get_post_type( $post_id );
                $this->affiliate_content_published( $post_type, $post_id );

                add_option( 'hostinger_affiliate_links_created', true, false );

                $is_action_executed[ $post_id ] = true;
            }
        }
    }

    private function send_request( string $endpoint, array $params = array() ): array {
        $scraper_param = array(
            'scraping' => empty( $this->plugin_settings->get_plugin_settings()->amazon->use_amazon_api() ),
        );

        $combined_params = array_merge( $params, $scraper_param );

        return $this->amplitude_manager->sendRequest( $endpoint, $combined_params );
    }
}
