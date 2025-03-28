<?php

namespace Hostinger\AffiliatePlugin\Services;

use Hostinger\AffiliatePlugin\Admin\Options\PluginOptions;
use Hostinger\AffiliatePlugin\Admin\PluginSettings;
use Hostinger\AffiliatePlugin\Repositories\ProductRepository;

if ( ! defined( 'ABSPATH' ) ) {
    die;
}

class ProductUpdateService {
    private ProductRepository $product_repository;
    private ProductFetchService $product_fetch_service;
    private PluginSettings $plugin_settings;

    public function __construct( ProductRepository $product_repository, ProductFetchService $product_fetch_service, PluginSettings $plugin_settings ) {
        $this->product_repository    = $product_repository;
        $this->product_fetch_service = $product_fetch_service;
        $this->plugin_settings       = $plugin_settings;
    }

    public function init(): void {
        add_action( 'hostinger_affiliate_product_update', array( $this, 'handle_product_update' ) );
    }

    public function handle_product_update(): bool {
        $settings = $this->plugin_settings->get_plugin_settings();

        $connection_status = $settings->get_connection_status();
        if ( $connection_status !== PluginOptions::STATUS_CONNECTED ) {
            return false;
        }

        // Getting outdated products.
        $past_seven_days = wp_date( 'Y-m-d H:i:s', strtotime( '-7 days' ) );
        $products        = $this->product_repository->get_by_updated_at( $past_seven_days, 10 );

        if ( empty( $products ) ) {
            return false;
        }

        // Prepare outdated product ASINs.
        $asins = array();
        foreach ( $products as $product ) {
            $asin = $product->get_asin();

            if ( empty( $asin ) || empty( $product->get_title() ) ) {
                $this->disable_orphaned_product( $product->get_id() );
                continue;
            }

            $asins[] = $asin;
        }

        add_filter( 'hostinger_proxy_api_params', array( $this, 'set_request_source' ) );

        // Pull outdated products from Amazon API.
        try {
            $products = $this->product_fetch_service->fetch_products_from_api( $asins );
        } catch ( \Exception $e ) {
            error_log( 'Hostinger Amazon Affiliate Connector: Error syncing products from Amazon API - ' . $e->getMessage() );
        }

        if ( empty( $products ) ) {
            return false;
        }

        // Update outdated products in DB.
        foreach ( $products as $product ) {
            $product_data = $product->to_array();
            $where        = array(
                'asin' => $product_data['asin'],
            );
            $this->product_repository->update( $product_data, $where );
        }

        return true;
    }

    public function set_request_source( array $params ): array {
        $params['request_source'] = 'cron';

        return $params;
    }

    private function disable_orphaned_product( int $id ): bool {
        $data  = array(
            'status' => 'inactive',
        );
        $where = array(
            'id' => $id,
        );

        return $this->product_repository->update( $data, $where );
    }
}
