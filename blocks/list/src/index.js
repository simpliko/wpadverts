import { __ } from '@wordpress/i18n';
import { registerBlockType } from '@wordpress/blocks';

import { useBlockProps, RichText, BlockControls, AlignmentToolbar, InspectorControls , PanelColorSettings } from '@wordpress/block-editor';
import { PanelBody, PanelRow, FormToggle, RadioControl, Toolbar, ToolbarDropdownMenu, ColorPalette} from '@wordpress/components';
import ServerSideRender from '@wordpress/server-side-render';

import Edit from './edit';
import { stringify } from 'postcss';

import Metadata from './block.json';

registerBlockType( Metadata, {
    edit: Edit,
    save: ( { attributes } ) => {
        return null;
    }
});
