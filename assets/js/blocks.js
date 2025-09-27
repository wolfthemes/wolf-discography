/**
 * Wolf Discography Gutenberg Blocks
 */

const { registerBlockType } = wp.blocks;
const { InspectorControls } = wp.blockEditor;
const { PanelBody, RangeControl, SelectControl, ToggleControl, TextControl } = wp.components;
const { Fragment } = wp.element;
const { __ } = wp.i18n;

/**
 * Register Releases Block
 */
registerBlockType('wolf-discography/releases', {
    title: __('Discography Releases', 'wolf-discography'),
    description: __('Display a list of music releases.', 'wolf-discography'),
    icon: 'album',
    category: 'widgets',
    keywords: [
        __('discography', 'wolf-discography'),
        __('releases', 'wolf-discography'),
        __('music', 'wolf-discography'),
    ],

    attributes: {
        postsPerPage: {
            type: 'number',
            default: 12,
        },
        columns: {
            type: 'number',
            default: 3,
        },
        display: {
            type: 'string',
            default: 'grid',
        },
        orderby: {
            type: 'string',
            default: 'date',
        },
        order: {
            type: 'string',
            default: 'DESC',
        },
        band: {
            type: 'string',
            default: '',
        },
        label: {
            type: 'string',
            default: '',
        },
        genre: {
            type: 'string',
            default: '',
        },
    },

    edit: function(props) {
        const { attributes, setAttributes } = props;
        const {
            postsPerPage,
            columns,
            display,
            orderby,
            order,
            band,
            label,
            genre,
        } = attributes;

        return (
            <Fragment>
                <InspectorControls>
                    <PanelBody title={__('Display Settings', 'wolf-discography')} initialOpen={true}>
                        <RangeControl
                            label={__('Posts per Page', 'wolf-discography')}
                            value={postsPerPage}
                            onChange={(value) => setAttributes({ postsPerPage: value })}
                            min={1}
                            max={50}
                        />

                        <RangeControl
                            label={__('Columns', 'wolf-discography')}
                            value={columns}
                            onChange={(value) => setAttributes({ columns: value })}
                            min={1}
                            max={6}
                        />

                        <SelectControl
                            label={__('Display Type', 'wolf-discography')}
                            value={display}
                            options={[
                                { label: __('Grid', 'wolf-discography'), value: 'grid' },
                                { label: __('List', 'wolf-discography'), value: 'list' },
                                { label: __('Carousel', 'wolf-discography'), value: 'carousel' },
                            ]}
                            onChange={(value) => setAttributes({ display: value })}
                        />
                    </PanelBody>

                    <PanelBody title={__('Sorting', 'wolf-discography')} initialOpen={false}>
                        <SelectControl
                            label={__('Order By', 'wolf-discography')}
                            value={orderby}
                            options={[
                                { label: __('Date', 'wolf-discography'), value: 'date' },
                                { label: __('Title', 'wolf-discography'), value: 'title' },
                                { label: __('Menu Order', 'wolf-discography'), value: 'menu_order' },
                                { label: __('Random', 'wolf-discography'), value: 'rand' },
                            ]}
                            onChange={(value) => setAttributes({ orderby: value })}
                        />

                        <SelectControl
                            label={__('Order', 'wolf-discography')}
                            value={order}
                            options={[
                                { label: __('Descending', 'wolf-discography'), value: 'DESC' },
                                { label: __('Ascending', 'wolf-discography'), value: 'ASC' },
                            ]}
                            onChange={(value) => setAttributes({ order: value })}
                        />
                    </PanelBody>

                    <PanelBody title={__('Filters', 'wolf-discography')} initialOpen={false}>
                        <TextControl
                            label={__('Band (slug)', 'wolf-discography')}
                            value={band}
                            onChange={(value) => setAttributes({ band: value })}
                            help={__('Filter by band slug. Use "current" for current taxonomy page.', 'wolf-discography')}
                        />

                        <TextControl
                            label={__('Label (slug)', 'wolf-discography')}
                            value={label}
                            onChange={(value) => setAttributes({ label: value })}
                            help={__('Filter by label slug. Use "current" for current taxonomy page.', 'wolf-discography')}
                        />

                        <TextControl
                            label={__('Genre (slug)', 'wolf-discography')}
                            value={genre}
                            onChange={(value) => setAttributes({ genre: value })}
                            help={__('Filter by genre slug. Use "current" for current taxonomy page.', 'wolf-discography')}
                        />
                    </PanelBody>
                </InspectorControls>

                <div className={`wp-block-wolf-discography-releases preview`}>
                    <div className="block-preview-placeholder">
                        <div className="block-icon">♫</div>
                        <h3>{__('Wolf Discography Releases', 'wolf-discography')}</h3>
                        <p>{__('Displaying', 'wolf-discography')} {postsPerPage} {__('releases in', 'wolf-discography')} {columns} {__('columns', 'wolf-discography')}</p>
                        {band && <p>{__('Band:', 'wolf-discography')} {band}</p>}
                        {label && <p>{__('Label:', 'wolf-discography')} {label}</p>}
                        {genre && <p>{__('Genre:', 'wolf-discography')} {genre}</p>}
                    </div>
                </div>
            </Fragment>
        );
    },

    save: function() {
        // Return null since this is a dynamic block
        return null;
    },
});

/**
 * Register Release Info Block
 */
registerBlockType('wolf-discography/release-info', {
    title: __('Release Info', 'wolf-discography'),
    description: __('Display release metadata like bands, labels, genres, and release date.', 'wolf-discography'),
    icon: 'info',
    category: 'widgets',
    keywords: [
        __('release', 'wolf-discography'),
        __('info', 'wolf-discography'),
        __('metadata', 'wolf-discography'),
    ],

    attributes: {
        postId: {
            type: 'number',
            default: 0,
        },
        showBands: {
            type: 'boolean',
            default: true,
        },
        showLabels: {
            type: 'boolean',
            default: true,
        },
        showGenres: {
            type: 'boolean',
            default: true,
        },
        showReleaseDate: {
            type: 'boolean',
            default: true,
        },
    },

    edit: function(props) {
        const { attributes, setAttributes } = props;
        const { showBands, showLabels, showGenres, showReleaseDate } = attributes;

        return (
            <Fragment>
                <InspectorControls>
                    <PanelBody title={__('Display Settings', 'wolf-discography')} initialOpen={true}>
                        <ToggleControl
                            label={__('Show Release Date', 'wolf-discography')}
                            checked={showReleaseDate}
                            onChange={(value) => setAttributes({ showReleaseDate: value })}
                        />

                        <ToggleControl
                            label={__('Show Bands', 'wolf-discography')}
                            checked={showBands}
                            onChange={(value) => setAttributes({ showBands: value })}
                        />

                        <ToggleControl
                            label={__('Show Labels', 'wolf-discography')}
                            checked={showLabels}
                            onChange={(value) => setAttributes({ showLabels: value })}
                        />

                        <ToggleControl
                            label={__('Show Genres', 'wolf-discography')}
                            checked={showGenres}
                            onChange={(value) => setAttributes({ showGenres: value })}
                        />
                    </PanelBody>
                </InspectorControls>

                <div className="wp-block-wolf-discography-release-info preview">
                    <div className="block-preview-placeholder">
                        <div className="block-icon">ℹ</div>
                        <h3>{__('Release Information', 'wolf-discography')}</h3>
                        <div className="preview-items">
                            {showReleaseDate && <div>{__('✓ Release Date', 'wolf-discography')}</div>}
                            {showBands && <div>{__('✓ Bands', 'wolf-discography')}</div>}
                            {showLabels && <div>{__('✓ Labels', 'wolf-discography')}</div>}
                            {showGenres && <div>{__('✓ Genres', 'wolf-discography')}</div>}
                        </div>
                    </div>
                </div>
            </Fragment>
        );
    },

    save: function() {
        return null;
    },
});

/**
 * Register Buy Links Block
 */
registerBlockType('wolf-discography/buy-links', {
    title: __('Buy Links', 'wolf-discography'),
    description: __('Display purchase links for a release.', 'wolf-discography'),
    icon: 'cart',
	category: 'wolf-discography', // instead of 'widgets'
    keywords: [
        __('buy', 'wolf-discography'),
        __('purchase', 'wolf-discography'),
        __('links', 'wolf-discography'),
    ],

    attributes: {
        postId: {
            type: 'number',
            default: 0,
        },
    },

    edit: function(props) {
        return (
            <div className="wp-block-wolf-discography-buy-links preview">
                <div className="block-preview-placeholder">
                    <div className="block-icon">🛒</div>
                    <h3>{__('Buy Links', 'wolf-discography')}</h3>
                    <p>{__('Purchase links will be displayed here.', 'wolf-discography')}</p>
                </div>
            </div>
        );
    },

    save: function() {
        return null;
    },
});