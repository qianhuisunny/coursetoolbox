<?php
namespace Hostinger\AffiliatePlugin\Transformers;

use Hostinger\AffiliatePlugin\Containers\Container;

class ProductTransformerFactory {
    protected Container $container;

    public function __construct( Container $container ) {
        $this->container = $container;
    }

    public function get_transformer( bool $use_amazon_api ): ProductTransformer {
        if ( $use_amazon_api ) {
            return $this->container->get( AmazonItemTransformer::class );
        }

        return $this->container->get( ProxyItemTransformer::class );
    }
}
