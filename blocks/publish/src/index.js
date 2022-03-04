import { __ } from '@wordpress/i18n';
import { registerBlockType } from '@wordpress/blocks';

import { useBlockProps, RichText, BlockControls, AlignmentToolbar, InspectorControls , PanelColorSettings } from '@wordpress/block-editor';
import { PanelBody, PanelRow, FormToggle, RadioControl, Toolbar, ToolbarDropdownMenu, ColorPalette} from '@wordpress/components';
import ServerSideRender from '@wordpress/server-side-render';

import Edit from './edit';
import { stringify } from 'postcss';

registerBlockType( 'wpadverts/publish', {
    title: __( 'Classifieds Publish', 'wpadverts' ),
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
