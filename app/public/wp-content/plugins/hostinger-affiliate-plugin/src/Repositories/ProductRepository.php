<?php

namespace Hostinger\AffiliatePlugin\Repositories;

use Hostinger\AffiliatePlugin\Admin\PluginSettings;
use Hostinger\AffiliatePlugin\Models\Product as ProductModel;
use Hostinger\AffiliatePlugin\Api\AmazonFetch;
use Hostinger\AffiliatePlugin\Amplitude\Events as AmplitudeEvents;
use Exception;
use WP_Error;
use wpdb;

if ( ! defined( 'ABSPATH' ) ) {
    die;
}

class ProductRepository implements RepositoryInterface {
    private wpdb $db;
    private AmplitudeEvents $amplitude_events;
    private string $table_name;
    private PluginSettings $plugin_settings;

    public function __construct( wpdb $wpdb, AmplitudeEvents $amplitude_events, PluginSettings $plugin_settings ) {
        $this->db               = $wpdb;
        $this->amplitude_events = $amplitude_events;
        $this->table_name       = $this->db->prefix . 'hostinger_affiliate_products';
        $this->plugin_settings  = $plugin_settings;
    }

    public function get_by_asins( array $asins ): array {
        $sql = 'SELECT * FROM `' . $this->table_name . '` WHERE `asin` IN (' . implode(
            ', ',
            array_fill( 0, count( $asins ), '%s' )
        ) . ')';

        $query = call_user_func_array( array( $this->db, 'prepare' ), array_merge( array( $sql ), $asins ) );

        $results = $this->db->get_results( $query, ARRAY_A );

        if ( empty( $results ) ) {
            return array();
        }

        return array_map(
            function ( $item ) {
                return new ProductModel( $item );
            },
            $results
        );
    }

    public function insert( array $fields ): bool {
        $exists = $this->db->get_var( 'SELECT COUNT(*) FROM ' . $this->table_name . ' WHERE asin = "' . $fields['asin'] . '"' );

        if ( $exists ) {
            return false;
        }

        return ! empty( $this->db->insert( $this->table_name, $fields ) );
    }

    public function clean_asin( string $asin ): array {
        $asin = trim( $asin );

        $asin = str_replace( ' ', '', $asin );

        return array_filter( explode( ',', $asin ) );
    }

    public function validate_asins( array $asins ): bool {
        if ( empty( $asins ) ) {
            return false;
        }

        $valid = true;

        foreach ( $asins as $asin ) {
            if ( ! preg_match( '/^[A-Z0-9]{10}$/', $asin ) ) {
                $valid = false;
            }
        }

        return $valid;
    }

    public function format_asins( array $products ) {
        $formatted_asins = array();

        if ( empty( $products ) ) {
            return $formatted_asins;
        }

        foreach ( $products as $product ) {
            $formatted_asins[ $product->get_asin() ] = array_intersect_key(
                $product->to_array(),
                array_flip( array( 'asin', 'title', 'image_url' ) )
            );
        }

        return $formatted_asins;
    }

    public function create_from_api( ProductModel $product ): ProductModel {
        $product->set_updated_at( $product->get_created_at() );

        $this->insert( $product->to_array() );

        return $product;
    }

    public function update( $data, $where ): bool {
        $data = array_diff_key( $data, array_flip( array( 'id', 'created_at' ) ) );

        if ( empty( $data ) ) {
            return false;
        }

        return $this->db->update(
            $this->table_name,
            $data,
            $where
        );
    }

    public function delete( $where ): bool {
        if ( empty( $where ) ) {
            return false;
        }

        return $this->db->delete(
            $this->table_name,
            $where
        );
    }

    public function get_by_updated_at( string $date, int $limit = 10 ): array {
        $sql = 'SELECT * FROM `'
               . $this->table_name
               . '` WHERE `updated_at` < %s AND `status` = %s ORDER BY `updated_at` ASC LIMIT 0, %d';

        $query = $this->db->prepare( $sql, $date, 'active', $limit );

        $results = $this->db->get_results( $query, ARRAY_A );

        if ( empty( $results ) ) {
            return array();
        }

        return array_map(
            function ( $item ) {
                return new ProductModel( $item );
            },
            $results
        );
    }
}
