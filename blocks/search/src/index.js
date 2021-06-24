import { __ } from '@wordpress/i18n';
import { registerBlockType } from '@wordpress/blocks';

import { useBlockProps, RichText, BlockControls, AlignmentToolbar, InspectorControls , PanelColorSettings } from '@wordpress/block-editor';
import { PanelBody, PanelRow, FormToggle, RadioControl, Toolbar, ToolbarDropdownMenu, ColorPalette} from '@wordpress/components';
import ServerSideRender from '@wordpress/server-side-render';

import Edit from './edit';

registerBlockType( 'wpadverts/search', {
    title: __( 'Classifieds Search', 'wpadverts' ),
    icon: 'universal-access-alt',
    category: 'design',
    example: {},
    attributes: {
        content: {
            type: "string"
        },
        form_style: {
            type: "string",
            default: "wpa-solid"
        },
        form_input_padding: {
            type: "string",
            default: "wpa-padding-sm"
        },        
        form_input_corners: {
            type: "string",
            default: "wpa-mood-simple"
        },        
        form_input_focus: {
            type: "string",
            default: "wpa-focus-simple"
        },
        primary_button: {
            type: "object",
            default: {
                desktop: "icon",
                text: ""
            }
        },
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
