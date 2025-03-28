<?php

if ( ! defined( 'ABSPATH' ) ) {
    die;
}

foreach ( array_slice( $products, 0, $list_items_count ) as $product ) {
    ?>
    <div class="hostinger-affiliate-block-list-with-desc">
        <?php

        if ( ! empty( $atts['bestseller_label_enabled'] ) ) {
            ?>
            <div class="hostinger-affiliate-block-list-with-desc__bestseller-label">
                <?php /* translators: %d: bestseller place */ ?>
                <?php echo sprintf( __( 'Bestseller #%d', 'hostinger-affiliate-plugin' ), $product_index ); ?>
            </div>
            <?php
        }
        ?>
        <div class="hostinger-affiliate-block-list-with-desc__inner-wrap">
            <?php

            $product_title = $this->render_product_title( $product );

            if ( ! empty( $product->get_image_url() ) ) {
                ?>
                <div class="hostinger-affiliate-block-list-with-desc__image">
                    <a href="<?php echo $product->get_url(); ?>" target="_blank" rel="nofollow noopener noreferrer">
                        <img src="<?php echo $product->get_image_url(); ?>" alt="<?php echo esc_attr( $product_title ); ?>">
                    </a>
                </div>
                <?php
            }

            ?>
            <div class="hostinger-affiliate-block-list-with-desc__product-data">
                <div class="hostinger-affiliate-block-list-with-desc__product-title">
                    <a href="<?php echo $product->get_url(); ?>" target="_blank" rel="nofollow noopener noreferrer">
                        <h3>
                            <?php echo $product_title; ?>
                        </h3>
                    </a>
                </div>
                <?php

                if ( ! empty( $atts['description_enabled'] ) ) {
                    ?>
                    <div class="hostinger-affiliate-block-list-with-desc__product-description">
                        <?php echo $this->render_product_description( $product ); ?>
                    </div>
                    <?php
                }

                ?>
                <div class="hostinger-affiliate-block-list-with-desc__product-actions">
                    <?php

                    $price = $product->price_available();

                    if ( ! empty( $price ) ) {
                        ?>
                        <div class="hostinger-affiliate-block-list-with-desc__product-price">
                            <?php echo $this->shortcode_manager->render_price( $product ); ?>
                        </div>
                        <?php
                    }

                    if ( ! empty( $product->get_is_prime() ) ) {
                        ?>
                        <div class="hostinger-affiliate-block-list-with-desc__product-prime">
                            <img src="<?php echo HOSTINGER_AFFILIATE_PLUGIN_URL . 'assets/img/prime.png'; ?>" alt="<?php echo __( 'Is prime', 'hostinger-affiliate-plugin' ); ?>">
                        </div>
                        <?php
                    }

                    ?>
                    <div class="hostinger-affiliate-block-list-with-desc__product-button-wrap">
                        <a href="<?php echo $product->get_url(); ?>" class="hostinger-affiliate-block-list-with-desc__product-amazon-button" target="_blank" rel="nofollow noopener noreferrer">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
                                <g clip-path="url(#clip0_2104_11808)">
                                    <path d="M17.2188 16.147C9.12286 20 4.09841 16.7763 0.882107 14.8183C0.683083 14.6949 0.344812 14.8472 0.638308 15.1843C1.70982 16.4835 5.22139 19.615 9.80502 19.615C14.3918 19.615 17.1205 17.1123 17.4619 16.6757C17.8009 16.2428 17.5614 16.004 17.2187 16.147H17.2188ZM19.4926 14.8913C19.2751 14.6082 18.1706 14.5554 17.4754 14.6408C16.7792 14.7238 15.7341 15.1493 15.825 15.4048C15.8716 15.5005 15.9668 15.4575 16.445 15.4145C16.9246 15.3667 18.2682 15.1971 18.5481 15.5631C18.8294 15.9316 18.1196 17.687 17.99 17.9701C17.8647 18.2533 18.0378 18.3263 18.2731 18.1377C18.5051 17.9492 18.9252 17.4611 19.2071 16.7703C19.487 16.0758 19.6578 15.1069 19.4926 14.8913Z" fill="#FF9900"/>
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M11.7752 8.28499C11.7752 9.29595 11.8007 10.1391 11.2898 11.0369C10.8773 11.7669 10.224 12.2158 9.49408 12.2158C8.49766 12.2158 7.91736 11.4566 7.91736 10.3362C7.91736 8.12435 9.89913 7.72292 11.7752 7.72292V8.28499ZM14.3921 14.61C14.2205 14.7633 13.9723 14.7743 13.7789 14.6721C12.9176 13.9567 12.7642 13.6246 12.2898 12.9421C10.8664 14.3947 9.85905 14.829 8.01229 14.829C5.82972 14.829 4.12891 13.4822 4.12891 10.7851C4.12891 8.67919 5.27135 7.24479 6.89539 6.54409C8.30425 5.92355 10.2715 5.81408 11.7752 5.6426V5.30679C11.7752 4.68994 11.8226 3.96001 11.4613 3.42718C11.1437 2.94904 10.5379 2.75194 10.005 2.75194C9.01595 2.75194 8.13269 3.25923 7.91736 4.31036C7.87351 4.544 7.70202 4.77395 7.46846 4.78488L4.95008 4.51485C4.73844 4.4673 4.50487 4.29582 4.56327 3.97094C5.14357 0.919802 7.89905 0 10.3663 0C11.6291 0 13.2788 0.335809 14.2752 1.29208C15.538 2.47091 15.4176 4.04394 15.4176 5.75569V9.79963C15.4176 11.015 15.9212 11.5478 16.3957 12.2048C16.5635 12.4384 16.6001 12.7195 16.3884 12.8946C15.8591 13.3362 14.9175 14.1575 14.3993 14.6174L14.392 14.61" fill="black"/>
                                    <path d="M17.2188 16.147C9.12286 20 4.09841 16.7763 0.882107 14.8183C0.683083 14.6949 0.344812 14.8472 0.638308 15.1843C1.70982 16.4835 5.22139 19.615 9.80502 19.615C14.3918 19.615 17.1205 17.1123 17.4619 16.6757C17.8009 16.2428 17.5614 16.004 17.2187 16.147H17.2188ZM19.4926 14.8913C19.2751 14.6082 18.1706 14.5554 17.4754 14.6408C16.7792 14.7238 15.7341 15.1493 15.825 15.4048C15.8716 15.5005 15.9668 15.4575 16.445 15.4145C16.9246 15.3667 18.2682 15.1971 18.5481 15.5631C18.8294 15.9316 18.1196 17.687 17.99 17.9701C17.8647 18.2533 18.0378 18.3263 18.2731 18.1377C18.5051 17.9492 18.9252 17.4611 19.2071 16.7703C19.487 16.0758 19.6578 15.1069 19.4926 14.8913Z" fill="#FF9900"/>
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M11.7752 8.28499C11.7752 9.29595 11.8007 10.1391 11.2898 11.0369C10.8773 11.7669 10.224 12.2158 9.49408 12.2158C8.49766 12.2158 7.91736 11.4566 7.91736 10.3362C7.91736 8.12435 9.89913 7.72292 11.7752 7.72292V8.28499ZM14.3921 14.61C14.2205 14.7633 13.9723 14.7743 13.7789 14.6721C12.9176 13.9567 12.7642 13.6246 12.2898 12.9421C10.8664 14.3947 9.85905 14.829 8.01229 14.829C5.82972 14.829 4.12891 13.4822 4.12891 10.7851C4.12891 8.67919 5.27135 7.24479 6.89539 6.54409C8.30425 5.92355 10.2715 5.81408 11.7752 5.6426V5.30679C11.7752 4.68994 11.8226 3.96001 11.4613 3.42718C11.1437 2.94904 10.5379 2.75194 10.005 2.75194C9.01595 2.75194 8.13269 3.25923 7.91736 4.31036C7.87351 4.544 7.70202 4.77395 7.46846 4.78488L4.95008 4.51485C4.73844 4.4673 4.50487 4.29582 4.56327 3.97094C5.14357 0.919802 7.89905 0 10.3663 0C11.6291 0 13.2788 0.335809 14.2752 1.29208C15.538 2.47091 15.4176 4.04394 15.4176 5.75569V9.79963C15.4176 11.015 15.9212 11.5478 16.3957 12.2048C16.5635 12.4384 16.6001 12.7195 16.3884 12.8946C15.8591 13.3362 14.9175 14.1575 14.3993 14.6174L14.392 14.61" fill="black"/>
                                </g>
                                <defs>
                                    <clipPath id="clip0_2104_11808">
                                        <rect width="20" height="20" fill="white"/>
                                    </clipPath>
                                </defs>
                            </svg>

                            <?php echo $this->shortcode_manager->render_buy_now_button_label( $product ); ?>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php
    ++$product_index;
}
?>
