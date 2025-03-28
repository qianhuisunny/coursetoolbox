<?php
/**
 * PluginSettings
 *
 * @package HostingerAffiliatePlugin
 */

namespace Hostinger\AffiliatePlugin\Admin\Options;

/**
 * Avoid possibility to get file accessed directly
 */
if ( ! defined( 'ABSPATH' ) ) {
    die;
}

/**
 * Class for handling Settings
 */
class PluginOptions {
    const STATUS_CONNECTED    = 'connected';
    const STATUS_DISCONNECTED = 'disconnected';

    /**
     * @var bool
     */
    private bool $is_first_time = true;

    /**
     * @var bool
     */
    private string $connection_status = '';

    /**
     * @var AmazonOptions
     */
    public AmazonOptions $amazon;

    /**
     * @param array $settings plugin settings array.
     */
    public function __construct( array $settings = array() ) {
        $this->is_first_time     = ! isset( $settings['is_first_time'] ) ? true : $settings['is_first_time'];
        $this->connection_status = empty( $settings['connection_status'] ) ? self::STATUS_DISCONNECTED : $settings['connection_status'];

        $this->amazon = new AmazonOptions( ! empty( $settings['amazon'] ) ? $settings['amazon'] : array() );
    }

    /**
     * @return bool
     */
    public function get_is_first_time(): bool {
        return $this->is_first_time;
    }

    /**
     * @param bool $is_first_time is it first time plugin is used.
     */
    public function set_is_first_time( bool $is_first_time ): void {
        $this->is_first_time = $is_first_time;
    }

    /**
     * @return string
     */
    public function get_connection_status(): string {
        return $this->connection_status;
    }

    /**
     * @param string $connection_status connection status.
     */
    public function set_connection_status( string $connection_status ): void {
        $this->connection_status = $connection_status;
    }

    public function get_amazon_options(): AmazonOptions {
        return $this->amazon;
    }

    /**
     * @return array
     */
    public function to_array(): array {
        return array(
            'is_first_time'     => $this->get_is_first_time(),
            'connection_status' => $this->get_connection_status(),
            'amazon'            => $this->amazon->to_array(),
        );
    }
}
