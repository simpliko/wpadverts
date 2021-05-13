import { __ } from '@wordpress/i18n';
import { registerBlockType } from '@wordpress/blocks';
import { useBlockProps, RichText, BlockControls, AlignmentToolbar, InspectorControls } from '@wordpress/block-editor';
import { PanelBody, PanelRow, FormToggle } from '@wordpress/components';
import ServerSideRender from '@wordpress/server-side-render';

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
 
        return (
            <div {...blockProps}>
                {
                    <InspectorControls>
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