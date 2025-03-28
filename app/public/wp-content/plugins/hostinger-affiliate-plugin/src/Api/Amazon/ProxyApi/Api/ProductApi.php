<?php
namespace Hostinger\AffiliatePlugin\Api\Amazon\ProxyApi\Api;

use Hostinger\AffiliatePlugin\Api\Amazon\AmazonApi\Request\GetProductDataRequest;
use Hostinger\AffiliatePlugin\Api\Amazon\ProxyApi\Client;
use Hostinger\AffiliatePlugin\Repositories\ProductRepository;
use WP_Error;

if ( ! defined( 'ABSPATH' ) ) {
    die;
}

class ProductApi {
    private const GET_ITEM_ENDPOINT = '/v3/wordpress/plugin/amazon/product';
    private Client $client;

    private ProductRepository $product_repository;

    public function __construct( Client $client, ProductRepository $product_repository ) {
        $this->client             = $client;
        $this->product_repository = $product_repository;
    }

    public function product_data( GetProductDataRequest $request ): array|WP_Error {
        $products = array();

        foreach ( $request->get_item_ids() as $asin ) {
            $product = $this->get_product( $asin );

            if ( ! is_wp_error( $product ) ) {
                $products[] = $product;
            }
        }

        return $products;
    }

    public function get_product( string $asin ): array|WP_Error {
        $params = array(
            'asin' => $asin,
        );

        $request = $this->client->get( self::GET_ITEM_ENDPOINT, $params );

        if ( is_wp_error( $request ) ) {
            $error_data = $request->get_error_data();

            if ( is_array( $error_data ) && ! empty( $error_data['status'] ) && $error_data['status'] === 404 ) {
                $this->disable_orphaned_product( $asin );
            }
        }

        if ( is_array( $request ) && isset( $request['data']['name'] ) ) {
            if ( empty( $request['data']['asin'] ) ) {
                $request['data']['asin'] = $asin;
            }

            return $request['data'];
        }

        return $request;
    }

    private function disable_orphaned_product( string $asin ): bool {
        $data  = array(
            'status' => 'inactive',
        );
        $where = array(
            'asin' => $asin,
        );

        return $this->product_repository->update( $data, $where );
    }
}
