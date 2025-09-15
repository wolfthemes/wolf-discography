<?php
/**
 * Post Output Factory
 *
 * @package WolfDiscography
 * @subpackage Frontend
 * @since 2.0.0
 */

namespace WolfDiscography\Frontend;

defined( 'ABSPATH' ) || exit;

class PostOutputFactory {
	public static function create( $format, $processed_atts ) {
        switch ( $format ) {
            case 'html': return new HTMLRenderer( $processed_atts );
            case 'json': return new JSONRenderer( $processed_atts );
            case 'data': return new DataRenderer( $processed_atts );
        }
    }
}