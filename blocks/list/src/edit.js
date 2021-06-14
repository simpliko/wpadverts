import { Component } from '@wordpress/element';
import { __ } from '@wordpress/i18n';

import { RichText, BlockControls, InspectorControls , PanelColorSettings } from '@wordpress/block-editor';
import { Disabled, PanelBody, Toolbar, ToolbarDropdownMenu, ColorPalette, DropdownMenu, SelectControl } from '@wordpress/components';

import ServerSideRender from '@wordpress/server-side-render';


class Edit extends Component {

    onChangeColor = ( color ) => {
        this.props.setAttributes( { color } );
    }

    onFormStyleChange = ( form_style ) => {
        this.props.setAttributes( { form_style } );
    }

    render() {

        const { className, attributes } = this.props;
        const { content, color, form_style } = attributes;

        return (
            <>
                <InspectorControls>
                    <PanelBody>

              
                    <SelectControl
                        label="Size"
                        labelPosition="side"
                        value={ form_style }
                        options={ [
                            { label: 'Unstyled', value: '' },
                            { label: 'Solid', value: 'wpa-solid' },
                            { label: 'Simple', value: 'wpa-simple' },
                        ] }
                        onChange={this.onFormStyleChange}
                    />
                    </PanelBody>

                    <PanelColorSettings
                        colorSettings={[
                            {
                                value: color,
                                onChange: this.onChangeColor,
                                label: __( "Price Color", "wpadverts" )
                            }
                        ]}
                    />

                    <PanelBody>
                        <ColorPalette 
                            onChange={ (v) => console.log( v ) }
                        />
                    </PanelBody>
                </InspectorControls>

                <BlockControls>
                    <Toolbar 
                        controls={[
                            {
                                icon: "wordpress",
                                title: "test",
                                onClick: (v) => alert(true) 
                            }
                        ]}
                    />
                    <Toolbar>
                        <ToolbarDropdownMenu 
                            controls={[
                                {
                                    icon: "wordpress",
                                    title: "test",
                                    onClick: (v) => alert(true) 
                                }
                            ]}
                        />
                    </Toolbar>
                </BlockControls>

                <Disabled>
                    <ServerSideRender
                        block="wpadverts/list"
                        attributes={ this.props.attributes }
                    />
                </Disabled>
            </>
        ) ;
    }
}

export default Edit;