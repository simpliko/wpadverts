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
            type: "string",
            default: ""
        },
        list_img_height: {
            type: "string",
            default: ""
        },
        list_img_fit: {
            type: "string",
            default: "contain"
        },
        list_img_source: {
            type: "string",
            default: "adverts-list"
        },
        list_img_height: {
            type: "string",
            default: ""
        },
        grid_columns: {
            type: "integer",
            default: 2
        },
        grid_img_height: {
            type: "string",
            default: ""
        },
        grid_img_fit: {
            type: "string",
            default: "contain"
        },
        grid_img_source: {
            type: "string",
            default: "adverts-list"
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
