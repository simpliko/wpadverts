import { __ } from '@wordpress/i18n';
import { registerBlockType } from '@wordpress/blocks';

import { useBlockProps, RichText, BlockControls, AlignmentToolbar, InspectorControls , PanelColorSettings } from '@wordpress/block-editor';
import { PanelBody, PanelRow, FormToggle, RadioControl, Toolbar, ToolbarDropdownMenu, ColorPalette} from '@wordpress/components';
import ServerSideRender from '@wordpress/server-side-render';

import Edit from './edit';

registerBlockType( 'wpadverts/list', {
    title: __( 'Classifieds List', 'wpadverts' ),
    icon: 'universal-access-alt',
    category: 'design',
    example: {},
    attributes: {
        content: {
            type: "string"
        },
        color: {
            type: "string"
        },
        form_style: {
            type: "string",
            default: "wpa-solid"
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

/*

const blockStyle = {
	backgroundColor: '#900',
	color: '#fff',
	padding: '20px',
};


registerBlockType( 'wpadverts/list', {
    title: __( 'Classifieds List', 'wpadverts' ),
    icon: 'universal-access-alt',
    category: 'design',
    example: {},
    edit: ( props ) => {
        const blockProps = useBlockProps(  );
 
        const onChangeContent = ( newContent ) => {
            props.setAttributes( { content: newContent } );
        };
 
        const onChangeAlignment = ( newAlignment ) => {
            props.setAttributes( { alignment: newAlignment === undefined ? 'none' : newAlignment } );
        };
 
        const option = "list";
 
        return (
            <div {...blockProps}>
                {
                    <InspectorControls>
                    
                        <PanelBody title="Display Settings">
                            <PanelRow>
                                <RadioControl
                                label="List Type"
                                help=""
                                selected={ option }
                                options={ [
                                    { label: 'List', value: 'list' },
                                    { label: 'Grid', value: 'grid' },
                                ] }
                                onChange={ ( option ) => {
                                    onChangeContent();
                                } }
                                />
                            </PanelRow>
                        </PanelBody>
                    
                        <PanelBody title="Test">
                           <PanelRow>
                                    <FormToggle
                                     id="high-contrast-form-toggle"
                                     label={ __( 'High Contrast', 'wpadverts' ) }

                                 />
                           </PanelRow>
                        </PanelBody>
                        
                    </InspectorControls>
                }
                <ServerSideRender
                    block="wpadverts/list"
                    attributes={ props.attributes }
                />
            </div>
        );
 
    },
    save() {
        const blockProps = useBlockProps.save(  );
 
        return <div { ...blockProps }>Hello World, step 1 (from the frontend).</div>;
    },
} );
 * 
 * 
 */