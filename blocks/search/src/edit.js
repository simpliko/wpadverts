import { Component } from '@wordpress/element';
import { __ } from '@wordpress/i18n';

import { RichText, BlockControls, InspectorControls , PanelColorSettings } from '@wordpress/block-editor';
import { Disabled, PanelBody, Toolbar, ToolbarDropdownMenu, ColorPalette, DropdownMenu, SelectControl, Tooltip } from '@wordpress/components';

import ServerSideRender from '@wordpress/server-side-render';
import ServerSideRenderX from './server-side-render-x';


class Edit extends Component {

    onChangeColor = ( color ) => {
        this.props.setAttributes( { color } );
    }

    onFormStyleChange = ( form_style ) => {
        this.props.setAttributes( { form_style } );
    }

    onFormInputPaddingChange = ( form_input_padding ) => {
        this.props.setAttributes( { form_input_padding } );
    }    
    
    onFormInputCornersChange = ( form_input_corners ) => {
        this.props.setAttributes( { form_input_corners } );
    }    
    
    onFormInputFocusChange = ( form_input_focus ) => {
        this.props.setAttributes( { form_input_focus } );
    }

    render() {

        const { className, attributes } = this.props;

        const { 
            content, 
            color, 
            form_style, 
            form_input_padding, 
            form_input_corners,
            form_input_focus
        } = attributes;

        return (
            <>
                <InspectorControls>
                    <PanelBody title="Form Styling">

              
                    <SelectControl
                        label="Form Style"
                        labelPosition="side"
                        value={ form_style }
                        options={ [
                            { label: 'Unstyled', value: '' },
                            { label: 'Solid', value: 'wpa-solid' },
                            { label: 'Simple', value: 'wpa-simple' },
                        ] }
                        onChange={this.onFormStyleChange}
                    />                    
                    
                    <SelectControl
                        label="Input Padding"
                        labelPosition="side"
                        value={ form_input_padding }
                        options={ [
                            { label: 'Default', value: '' },
                            { label: 'Small', value: 'wpa-padding-sm' },
                            { label: 'Medium', value: 'wpa-padding-md' },
                        ] }
                        onChange={this.onFormInputPaddingChange}
                    />                    
                    
                    <SelectControl
                        label="Input Corners"
                        labelPosition="side"
                        value={ form_input_corners }
                        options={ [
                            { label: 'Default', value: '' },
                            { label: 'Simple', value: 'wpa-mood-simple' },
                            { label: 'Playful', value: 'wpa-mood-playful' },
                            { label: 'Elegant', value: 'wpa-mood-elegant' },
                        ] }
                        onChange={this.onFormInputCornersChange}
                    />                    
                    
                    <SelectControl
                        label="Input Focus"
                        labelPosition="side"
                        value={ form_input_focus }
                        options={ [
                            { label: 'Default', value: '' },
                            { label: 'Simple', value: 'wpa-focus-simple' }
                        ] }
                        onChange={this.onFormInputFocusChange}
                    />

                    </PanelBody>

                    <PanelBody title="Search Scheme">

                        <SelectControl

                            value={ form_input_focus }
                            options={ [
                                { label: 'Default', value: '' },
                                { label: 'Simple', value: 'wpa-focus-simple' }
                            ] }
                            onChange={this.onFormInputFocusChange}
                        />


                        <div>To use custom search form you need the <a href="#">Custom Fields</a> extension.</div>
    
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
                    <ServerSideRenderX
                        block="wpadverts/search"
                        attributes={ this.props.attributes }
                        spinnerLocation={{right: 0, top: 10, unit: 'px'}}
                    />
                </Disabled>
            </>
        ) ;
    }
}

export default Edit;