import { Component } from '@wordpress/element';
import { __ } from '@wordpress/i18n';

import { RichText, BlockControls, InspectorControls , PanelColorSettings } from '@wordpress/block-editor';
import { 
    ButtonGroup, 
    Button, 
    Disabled, 
    Flex,
    FlexItem,
    FlexBlock,
    Panel, 
    PanelBody, 
    Text,
    TextControl, 
    Toolbar, 
    ToolbarDropdownMenu, 
    ColorPicker,
    ColorPalette, 
    DropdownMenu,
    SelectControl, 
    Tooltip 
} from '@wordpress/components';

//import ServerSideRender from '@wordpress/server-side-render';
import ServerSideRenderX from './server-side-render-x';

import PartialButtons from './partial-buttons';


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
        console.log( "Corners Change" )
        this.props.setAttributes( { form_input_corners } );
    }    
    
    onFormInputFocusChange = ( form_input_focus ) => {
        this.props.setAttributes( { form_input_focus } );
    }

    onPartialPrimaryButtonChange = ( primary_button ) => {
        this.props.setAttributes( { primary_button: { ...primary_button } } ); 
    }

    render() {

        const { className, attributes } = this.props;

        const { 
            content, 
            color, 
            form_style, 
            form_input_padding, 
            form_input_corners,
            form_input_focus,
            primary_button,
            dataText
        } = attributes;

        //console.log("Render Again...");
        //console.log(this.props.attributes);

        return (
            <>
                <InspectorControls className="wpa-admin">
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

                    <PartialButtons 
                        title="Primary Button"
                        onChange={ this.onPartialPrimaryButtonChange }
                        data={ primary_button }
                    />

                    <PanelBody title="Buttons">

                        <Flex justify="space-between">
                            <FlexItem>Buttons Position</FlexItem>
                            
                            <FlexBlock className="wpa-content-right">
                                <ButtonGroup className="wpa-text-right">
                                    <Button variant="primary" isSmall={true} >Right</Button>
                                    <Button variant="secondary" isSmall={true} >Bottom</Button>
                                </ButtonGroup>
                            </FlexBlock>
                        </Flex>


                        <Panel title="Button Secondary">
                            <h4>Button Secondary</h4>
                        </Panel>
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
                        method="get"
                    />
                </Disabled>
            </>
        ) ;
    }
}

export default Edit;