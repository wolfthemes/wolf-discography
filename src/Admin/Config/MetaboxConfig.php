<?php
/**
 * Metabox Configuration
 *
 * Defines all metabox configurations for the plugin
 *
 * @package WolfDiscography
 * @subpackage Admin\Config
 * @since 2.0.0
 */

namespace WolfDiscography\Admin\Config;

defined('ABSPATH') || exit;

/**
 * Metabox Configuration Class
 */
class MetaboxConfig {

    /**
     * Get all metabox configurations
     *
     * @return array
     */
    public static function getConfig(): array {
        return [
            'release_details' => [
                'title'    => __('Release Details', 'wolf-discography'),
                'screen'   => 'release',
                'context'  => 'normal',
                'priority' => 'high',
                'fields'   => self::getReleaseDetailFields(),
            ],
        ];
    }

    /**
     * Get release detail fields configuration
     *
     * @return array
     */
    private static function getReleaseDetailFields(): array {
        return [
            [
                'label' => __('Title', 'wolf-discography'),
                'id'    => '_wolf_release_title',
                'type'  => 'text',
            ],
            [
                'label' => __('Release date', 'wolf-discography'),
                'id'    => '_wolf_release_date',
                'type'  => 'datepicker',
            ],
            [
                'label' => __('Catalog Number', 'wolf-discography'),
                'id'    => '_wolf_release_catalog_number',
                'type'  => 'text',
            ],
            [
                'label'   => __('Type', 'wolf-discography'),
                'id'      => '_wolf_release_type',
                'desc'    => __('You can choose to not display the format in the plugin setting.', 'wolf-discography'),
                'type'    => 'select',
                'choices' => [
                    'cd'       => __('CD', 'wolf-discography'),
                    'digital'  => __('Digital Download', 'wolf-discography'),
                    'dvd'      => __('DVD', 'wolf-discography'),
                    'vinyl'    => __('Vinyl', 'wolf-discography'),
                    'tape'     => __('Tape', 'wolf-discography'),
                ],
            ],
            [
                'label' => __('Amazon', 'wolf-discography'),
                'id'    => '_wolf_release_amazon',
                'type'  => 'url',
            ],
            [
                'label' => __('Apple', 'wolf-discography'),
                'id'    => '_wolf_release_apple',
                'type'  => 'url',
            ],
            [
                'label' => __('Bandcamp', 'wolf-discography'),
                'id'    => '_wolf_release_bandcamp',
                'type'  => 'url',
            ],
            [
                'label' => __('Deezer', 'wolf-discography'),
                'id'    => '_wolf_release_deezer',
                'type'  => 'url',
            ],
            [
                'label' => __('iTunes', 'wolf-discography'),
                'id'    => '_wolf_release_itunes',
                'type'  => 'url',
            ],
            [
                'label' => __('Spotify', 'wolf-discography'),
                'id'    => '_wolf_release_spotify',
                'type'  => 'url',
            ],
            [
                'label' => __('Tidal', 'wolf-discography'),
                'id'    => '_wolf_release_tidal',
                'type'  => 'url',
            ],
            [
                'label' => __('YouTube Music', 'wolf-discography'),
                'id'    => '_wolf_release_google_play',
                'type'  => 'url',
            ],
            [
                'label' => __('Buy (any link where the release can be purchased)', 'wolf-discography'),
                'id'    => '_wolf_release_buy',
                'type'  => 'url',
            ],
            [
                'label' => __('Free Download link', 'wolf-discography'),
                'id'    => '_wolf_release_free',
                'type'  => 'url',
            ],
            [
                'label' => __('Tracklist', 'wolf-discography'),
                'id'    => '_wolf_release_tracklist',
                'type'  => 'repeatable',
            ],
        ];
    }

    /**
     * Get field types that require URL validation
     *
     * @return array
     */
    public static function getUrlFields(): array {
        return [
            '_wolf_release_amazon',
            '_wolf_release_apple',
            '_wolf_release_bandcamp',
            '_wolf_release_deezer',
            '_wolf_release_itunes',
            '_wolf_release_spotify',
            '_wolf_release_tidal',
            '_wolf_release_google_play',
            '_wolf_release_buy',
            '_wolf_release_free',
        ];
    }

    /**
     * Get field types that are repeatable
     *
     * @return array
     */
    public static function getRepeatableFields(): array {
        return [
            '_wolf_release_tracklist',
        ];
    }
}