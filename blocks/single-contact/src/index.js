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


function addCoverAttribute(settings, name) {
    if (typeof settings.attributes !== 'undefined') {
        if (name == 'wpadverts/single-contact') {
            settings.attributes = Object.assign(settings.attributes, {
                hideOnMobile: {
                    type: 'boolean',
                }
            });
        }
    }
    return settings;
}
 
wp.hooks.addFilter(
    'blocks.registerBlockType',
    'wpadverts/single-contact',
    addCoverAttribute
);

const coverAdvancedControls = wp.compose.createHigherOrderComponent((BlockEdit) => {
    return (props) => {
        const { Fragment } = wp.element;
        const { ToggleControl, PanelBody } = wp.components;
        const { InspectorControls } = wp.blockEditor;
        const { attributes, setAttributes, isSelected } = props;
        return (
            <Fragment>
                <BlockEdit {...props} />
                {isSelected && (props.name == 'wpadverts/single-contact') && 
                    <InspectorControls>
                        <PanelBody title="Apply Online">
                            <ToggleControl
                                label={wp.i18n.__('Hide on mobile', 'awp')}
                                checked={!!attributes.hideOnMobile}
                                onChange={(newval) => setAttributes({ hideOnMobile: !attributes.hideOnMobile })}
                            />
                        </PanelBody>
                    </InspectorControls>
                }
            </Fragment>
        );
    };
}, 'coverAdvancedControls');
 
wp.hooks.addFilter(
    'editor.BlockEdit',
    'wpadverts/single-contact',
    coverAdvancedControls
);