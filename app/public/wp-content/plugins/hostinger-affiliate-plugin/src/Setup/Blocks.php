<?php
/**
 * Blocks class
 *
 * @package HostingerAffiliatePlugin
 */

namespace Hostinger\AffiliatePlugin\Setup;

use Hostinger\AffiliatePlugin\Amplitude\Actions as AmplitudeActions;
use Hostinger\AffiliatePlugin\Api\AmazonFetch;
use Hostinger\AffiliatePlugin\Amplitude\Events as AmplitudeEvents;
use Hostinger\AffiliatePlugin\Admin\PluginSettings;
use Hostinger\AffiliatePlugin\Repositories\ListRepository;
use Hostinger\AffiliatePlugin\Repositories\ProductRepository;
use Hostinger\AffiliatePlugin\Repositories\TableRepository;
use Hostinger\AffiliatePlugin\Shortcodes\ShortcodeManager;

/**
 * Avoid possibility to get file accessed directly
 */
if ( ! defined( 'ABSPATH' ) ) {
    die;
}

/**
 * Blocks class
 */
class Blocks {
    private PluginSettings $plugin_settings;

    private ShortcodeManager $shortcode_manager;

    public function __construct( PluginSettings $plugin_settings, ShortcodeManager $shortcode_manager ) {
        $this->plugin_settings   = $plugin_settings;
        $this->shortcode_manager = $shortcode_manager;
    }

    /**
     * Run actions or/and hooks
     */
    public function init(): void {
        if ( ! function_exists( 'register_block_type' ) ) {
            return;
        }

        add_action( 'init', array( $this, 'register_block' ) );
        add_action( 'enqueue_block_editor_assets', array( $this, 'enqueue_blocks' ) );

        add_action( 'admin_footer', array( $this, 'render_search_modal' ) );

        add_shortcode( 'hostinger-affiliate-table', array( $this, 'render_affiliate_table_shortcode' ) );
    }

    /**
     * @return void
     */
    public function register_block(): void {
        wp_register_style(
            'hostinger-affiliate-plugin-block-frontend',
            HOSTINGER_AFFILIATE_PLUGIN_URL . 'assets/dist/frontend.css',
            array(),
            filemtime( HOSTINGER_AFFILIATE_PLUGIN_DIR . 'assets/dist/frontend.css' )
        );

        register_block_type(
            'hostinger-affiliate-plugin/block',
            array(
                'attributes'      => array(
                    'display_type'                  => array(
                        'type' => 'string',
                    ),
                    'product_selector'              => array(
                        'type' => 'string',
                    ),
                    'product_list_type'             => array(
                        'type' => 'string',
                    ),
                    'list_navigation'               => array(
                        'type' => 'string',
                    ),
                    'list_layout_selected'          => array(
                        'type' => 'boolean',
                    ),
                    'list_items_count'              => array(
                        'type'    => 'integer',
                        'default' => 3,
                    ),
                    'list_layout'                   => array(
                        'type' => 'string',
                    ),
                    'asin'                          => array(
                        'type' => 'string',
                    ),
                    'asin_manual'                   => array(
                        'type' => 'string',
                    ),
                    'items'                         => array(
                        'type' => 'object',
                    ),
                    'keywords'                      => array(
                        'type' => 'string',
                    ),
                    'title_overwrite_enabled'       => array(
                        'type' => 'boolean',
                    ),
                    'title_overwrite'               => array(
                        'type' => 'string',
                    ),
                    'title_length'                  => array(
                        'type'    => 'integer',
                        'default' => 65,
                    ),
                    'description_overwrite'         => array(
                        'type' => 'string',
                    ),
                    'description_enabled'           => array(
                        'type' => 'boolean',
                    ),
                    'description_overwrite_enabled' => array(
                        'type' => 'boolean',
                    ),
                    'description_forced'            => array(
                        'type' => 'boolean',
                    ),
                    'description_items'             => array(
                        'type'    => 'integer',
                        'default' => 3,
                    ),
                    'description_length'            => array(
                        'type'    => 'integer',
                        'default' => 120,
                    ),
                    'ready'                         => array(
                        'type' => 'boolean',
                    ),
                    'table_id'                      => array(
                        'type'    => 'integer',
                        'default' => 0,
                    ),
                    'products_selected_open'        => array(
                        'type'    => 'boolean',
                        'default' => true,
                    ),
                    'bestseller_label_enabled'      => array(
                        'type'    => 'boolean',
                        'default' => true,
                    ),
                    'buy_button_overwrite_enabled'  => array(
                        'type'    => 'boolean',
                        'default' => false,
                    ),
                    'buy_button_overwrite'          => array(
                        'type'    => 'string',
                        'default' => '',
                    ),
                ),
                'render_callback' => array(
                    $this,
                    'render_block',
                ),
                'style'           => 'hostinger-affiliate-plugin-block-frontend',
                'editor_style'    => 'hostinger-affiliate-plugin-block-editor',
                'editor_script'   => 'hostinger-affiliate-plugin-block',
            )
        );
    }

    /**
     * @return void
     */
    public function enqueue_blocks(): void {
        wp_enqueue_script(
            'hostinger-affiliate-plugin-block',
            HOSTINGER_AFFILIATE_PLUGIN_URL . 'gutenberg-block/dist/index.js',
            array( 'wp-blocks', 'wp-i18n', 'wp-element', 'wp-editor' ),
            filemtime( HOSTINGER_AFFILIATE_PLUGIN_DIR . 'gutenberg-block/dist/index.js' )
        );

        wp_set_script_translations( 'hostinger-affiliate-plugin-block', 'hostinger-affiliate-plugin', HOSTINGER_AFFILIATE_PLUGIN_DIR . 'languages' );

        wp_enqueue_style(
            'hostinger-affiliate-plugin-block-editor',
            HOSTINGER_AFFILIATE_PLUGIN_URL . 'gutenberg-block/dist/index.css',
            array( 'wp-edit-blocks' ),
            filemtime( HOSTINGER_AFFILIATE_PLUGIN_DIR . 'gutenberg-block/dist/index.css' )
        );
    }

    /**
     * @param array $atts block attributes.
     *
     * @return string
     */
    public function render_block( array $atts ): string {
        $this->shortcode_manager->set_atts( $atts );

        return $this->shortcode_manager->render_shortcode();
    }

    /**
     * @return void
     */
    public function render_search_modal(): void {
        $plugin_settings = $this->plugin_settings->get_plugin_settings();

        ?>
        <div class="hostinger-affiliate-search-modal" id="hostinger-affiliate-search-modal" style="display: none;" data-type="single" data-operation="add">
            <div class="product-search-modal product-search-modal--found">
                <div class="product-search-modal__container-box">
                    <div class="product-search-modal__search-items-input">
                        <input type="text" name="hostinger-affiliate-product-keyword-search" value="" placeholder="<?php echo __( 'Search product name ...', 'hostinger-affiliate-plugin' ); ?>">
                        <div class="product-search-modal__search-items-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                <path d="M9.45 15.775C7.6473 15.775 6.12163 15.15 4.87298 13.9C3.62433 12.65 3 11.1416 3 9.37498C3 7.60831 3.625 6.09998 4.875 4.84998C6.125 3.59998 7.6375 2.97498 9.4125 2.97498C11.1875 2.97498 12.6958 3.59998 13.9375 4.84998C15.1792 6.09998 15.8 7.60956 15.8 9.37873C15.8 10.0929 15.6833 10.7833 15.45 11.45C15.2167 12.1166 14.8667 12.7416 14.4 13.325L20.45 19.325C20.6 19.4676 20.675 19.6491 20.675 19.8694C20.675 20.0898 20.6 20.275 20.45 20.425C20.3 20.575 20.1148 20.65 19.8945 20.65C19.6741 20.65 19.4926 20.575 19.35 20.425L13.325 14.4C12.825 14.8333 12.242 15.1708 11.576 15.4125C10.91 15.6541 10.2014 15.775 9.45 15.775ZM9.425 14.275C10.7792 14.275 11.9302 13.7958 12.8781 12.8375C13.826 11.8791 14.3 10.725 14.3 9.37498C14.3 8.02498 13.826 6.87081 12.8781 5.91248C11.9302 4.95414 10.7792 4.47498 9.425 4.47498C8.05695 4.47498 6.8941 4.95414 5.93645 5.91248C4.97882 6.87081 4.5 8.02498 4.5 9.37498C4.5 10.725 4.97882 11.8791 5.93645 12.8375C6.8941 13.7958 8.05695 14.275 9.425 14.275Z" fill="currentColor"/>
                            </svg>
                        </div>
                    </div>
                    <div class="product-search-modal__search-notifications" style="display: none;">
                        <div class="product-search-modal__snackbar">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                <path d="M12 2C6.477 2 2 6.477 2 12C2 17.523 6.477 22 12 22C17.523 22 22 17.523 22 12C22 6.477 17.523 2 12 2ZM13 17H11V11H13V17ZM13 9H11V7H13V9Z" fill="#727586"/>
                            </svg>
                            <?php /* translators: %s: Amazon store region */ ?>
                            <?php echo sprintf( __( 'Products that shown in here is from your store region (%1$s). You can change store region in <a href="%2$s">Settings page</a>.', 'hostinger-affiliate-plugin' ), $plugin_settings->amazon->get_domain(), '/wp-admin/admin.php?page=hostinger#amazon-affiliate' ); ?>
                        </div>
                    </div>
                    <div class="product-search-modal__content">
                        <div class="product-search-modal__item-results" style="display: none;">

                        </div>
                        <div class="product-search-modal__item-selected" style="display: none">

                        </div>
                        <div class="product-search-modal__search-placeholder product-search-modal__search-placeholder--no-results" style="display: none;">
                            <div class="product-search-modal__search-placeholder-image">
                                <img src="<?php echo HOSTINGER_AFFILIATE_PLUGIN_URL . 'assets/img/products-not-found.svg'; ?>" alt="<?php echo __( 'Search icon', 'hostinger-affiliate-plugin' ); ?>">
                            </div>
                            <div class="product-search-modal__search-placeholder-title">
                                <?php echo __( 'No products found', 'hostinger-affiliate-plugin' ); ?>
                            </div>
                            <div class="product-search-modal__search-placeholder-description">
                                <?php echo __( 'Check your search term and try again', 'hostinger-affiliate-plugin' ); ?><br>
                            </div>
                        </div>
                        <div class="product-search-modal__search-placeholder product-search-modal__search-placeholder--default">
                            <div class="product-search-modal__search-placeholder-image">
                                <img src="<?php echo HOSTINGER_AFFILIATE_PLUGIN_URL . 'assets/img/search-placeholder-icon.svg'; ?>" title="<?php echo __( 'Search icon', 'hostinger-affiliate-plugin' ); ?>">
                            </div>
                            <div class="product-search-modal__search-placeholder-title">
                                <?php echo __( 'Searched products will appear here', 'hostinger-affiliate-plugin' ); ?>
                            </div>
                            <div class="product-search-modal__search-placeholder-description">
                                <?php echo __( 'Search for product name that you want to add to your blog post.', 'hostinger-affiliate-plugin' ); ?><br>
                                <?php

                                if ( ! empty( $plugin_settings->amazon->get_domain() ) ) {
                                    /* translators: %s: Amazon store region */
                                    echo sprintf( __( 'Here you will see products from your store region on <b>%s</b>.', 'hostinger-affiliate-plugin' ), $plugin_settings->amazon->get_domain() );
                                } else {
                                    echo __( 'Here you will see products from your store region.', 'hostinger-affiliate-plugin' );
                                }

                                ?>
                            </div>
                        </div>
                    </div>
                    <div class="product-search-modal__search-actions">
                        <div class="product-search-modal__search-selected-products" style="display: none;">
                            <span class="product-search-modal__search-selected-products-validation-message">
                                <?php echo __( 'Select at least 2 products to continue', 'hostinger-affiliate-plugin' ); ?>
                            </span>
                            <span
                                class="product-search-modal__search-selected-products-count"
                                data-singular="<?php echo __( 'product', 'hostinger-affiliate-plugin' ); ?>"
                                data-plural="<?php echo __( 'products', 'hostinger-affiliate-plugin' ); ?>"
                                data-selected="<?php echo __( 'selected', 'hostinger-affiliate-plugin' ); ?>"
                            >

                            </span>
                        </div>
                        <button
                            class="hostinger-block-button hostinger-block-button--is-normal hostinger-block-button--is-primary-transparent product-search-modal__cancel-button"
                            type="button">
                            <?php echo __( 'Cancel', 'hostinger-affiliate-plugin' ); ?>
                        </button>
                        <button
                            class="hostinger-block-button hostinger-block-button--is-normal hostinger-block-button--is-primary product-search-modal__confirm-button"
                            disabled
                            type="button" style="display: none;">
                            <?php echo __( 'Confirm selection', 'hostinger-affiliate-plugin' ); ?>
                        </button>
                    </div>
                </div>
                <input name="hostinger-affiliate-product-keyword-search-selected-asins" type="hidden" value="">
            </div>
        </div>
        <?php
    }

    /**
     * @param array $atts
     *
     * @return string
     */
    public function render_affiliate_table_shortcode( array $atts ): string {
        $atts['display_type'] = AmplitudeActions::AFFILIATE_TABLE_LAYOUT;
        $atts['table_id']     = $atts['id'];

        return $this->render_block( $atts );
    }
}
