import { __ } from '@wordpress/i18n';
import { registerBlockType } from '@wordpress/blocks';

import { useBlockProps, RichText, BlockControls, AlignmentToolbar, InspectorControls , PanelColorSettings } from '@wordpress/block-editor';
import { PanelBody, PanelRow, FormToggle, RadioControl, Toolbar, ToolbarDropdownMenu, ColorPalette} from '@wordpress/components';
import ServerSideRender from '@wordpress/server-side-render';

import Edit from './edit';
import { stringify } from 'postcss';

registerBlockType( 'wpadverts/list', {
    title: __( 'Classifieds List', 'wpadverts' ),
    icon: 'megaphone',
    category: 'design',
    example: {},
    attributes: {
        post_type: {
            type: "string",
            default: ""
        },
        form_scheme: {
            type: "string"
        },
        form_style: {
            type: "string",
            default: "wpa-solid"
        },
        query: {
            type: "object",
            default: {}
        },
        show_results_counter: {
            type: "boolean",
            default: true
        },
        switch_views: {
            type: "boolean",
            default: true
        },
        allow_sorting:  {
            type: "boolean",
            default: true
        },
        show_pagination:  {
            type: "boolean",
            default: true
        },
        posts_per_page:  {
            type: "integer",
            default: 20
        },
        display:  {
            type: "string",
            default: "grid"
        },
        order_by:  {
            type: "string",
            default: "date-desc"
        },
        order_by_featured: {
            type: "boolean",
            default: true
        },
        list_type: {
            type: "string",
            default: "all"
        },
        list_img_width: {
            type: "integer",
            default: 1
        },
        list_img_height: {
            type: "integer",
            default: 1
        },
        list_img_fit: {
            type: "string",
            default: "contain"
        },
        list_img_source: {
            type: "string",
            default: "adverts-list"
        },
        grid_columns: {
            type: "string",
            default: '2'
        },        
        grid_columns_mobile: {
            type: "string",
            default: '2'
        },
        grid_img_height: {
            type: "integer",
            default: 2
        },
        grid_img_fit: {
            type: "string",
            default: "contain"
        },
        grid_img_source: {
            type: "string",
            default: "adverts-list"
        },
        data: {
            type: "array",
            default: [
                { name: "meta__adverts_location" },
                { name: "pattern__post_date" }
            ]
        },
        default_image_url: {
            type: 'string',
            default: ''
        },
        show_image_column: {
            type: "boolean",
            default: true
        },
        show_price_column: {
            type: "boolean",
            default: true
        },
        title_source: {
            type: "string",
            default: "default__post_title"
        },
        alt_source: {
            type: "string",
            default: "pattern__price"
        },
        color_price: {
            type: "string",
            default: "#b91c1c"
        },
        color_title: {
            type: "string",
            default: ""
        },
        color_bg_featured: {
            type: "string",
            default: ""
        }
        
    },
    edit: Edit,
    save: ( { attributes } ) => {
        return null;

        const { content } = attributes;
        return <RichText.Content
            tagName="p"
            value={ attributes.content }
        />
    }
});
