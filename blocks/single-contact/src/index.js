import { __ } from '@wordpress/i18n';
import { registerBlockType } from '@wordpress/blocks';

import Edit from './edit';
import Metadata from './block.json';
import { PanelBody } from '@wordpress/components';

registerBlockType( Metadata, {
    edit: Edit,
    save: ( { attributes } ) => {
        return null;
    }
});
