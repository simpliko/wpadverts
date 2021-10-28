import { __ } from '@wordpress/i18n';
import { registerBlockType } from '@wordpress/blocks';
import { RichText } from '@wordpress/block-editor';

import Edit from './edit';
import { stringify } from 'postcss';

registerBlockType( 'wpadverts/details', {
    title: __( 'Classifieds Details', 'wpadverts' ),
    icon: 'megaphone',
    category: 'design',
    example: {},
    attributes: {
        post_type: {
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
