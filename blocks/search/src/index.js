import { __ } from '@wordpress/i18n';
import { registerBlockType } from '@wordpress/blocks';

import { useBlockProps, RichText, BlockControls, AlignmentToolbar, InspectorControls , PanelColorSettings } from '@wordpress/block-editor';
import { PanelBody, PanelRow, FormToggle, RadioControl, Toolbar, ToolbarDropdownMenu, ColorPalette} from '@wordpress/components';
import ServerSideRender from '@wordpress/server-side-render';

import Edit from './edit';

registerBlockType( 'wpadverts/search', {
    title: __( 'Classifieds Search', 'wpadverts' ),
    icon: 'megaphone',
    category: 'design',
    example: {},
    attributes: {
        content: {
            type: "string"
        },
        post_type: {
            type: "string",
            default: ""  
        },
        form_scheme: {
            type: "string",
            default: ""
        },
        form: {
            type: "object",
            default: {

            }
        },
        primary_button: {
            type: "object",
            default: {
                desktop: "icon",
                text: ""
            }
        },
        secondary_button: {
            type: "object",
            default: {

            }
        },
        buttons_pos: {
            type: "string",
            default: "wpa-flex-row"
        },
        redirect_to: {
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

