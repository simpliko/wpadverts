import { Component } from '@wordpress/element';
import { __ } from '@wordpress/i18n';

import {
    Panel, PanelBody,
    Flex, FlexItem, FlexBlock,
    Button, ButtonGroup,
    TextControl,
    ColorPalette
} from '@wordpress/components';

import { useInstanceId } from '@wordpress/compose';
import { useState, forwardRef } from '@wordpress/element';

class PartialButtons extends Component {

	constructor( props ) {
		super(props);
        this.data = {
            text: "",
            desktop: "",
            mobile: "",
            color_primary: "",
            color_secondary: "",
            ...props.data
        };
    }
    
    shouldComponentUpdate(nextProps) {
        return true;
    }

    onTextChange = ( text ) => {
        this.data.text = text;
        this.props.onChange( this.data );
    }

    onDesktopClick = ( desktop ) => {
        //this.setState( { desktop } );
        this.data.desktop = desktop;
        this.props.onChange( this.data );
    }

    onMobileClick = ( mobile ) => {
        this.data.mobile = mobile;
        this.props.onChange( this.data );
    }

    onPrimaryColorChange = ( color_primary ) => {
        this.data.color_primary = color_primary;
        this.props.onChange( this.data );
    }

    onSecondaryColorChange = ( color_secondary ) => {
        this.data.color_secondary = color_secondary;
        this.props.onChange( this.data );
    }

    render( ) {
        console.log(this.props);
        const {
            desktop,
            mobile,
            text,
            color_primary,
            color_secondary
            
        } = this.data;

        return (
            <>
                <PanelBody title="Primary Button">
                    <Panel>

                        <Flex className="wpa-space-bottom" justify="space-between">
                            <FlexItem>Desktop</FlexItem>
                            
                            <FlexBlock className="wpa-content-right">
                                <ButtonGroup>
                                    <Button variant="secondary" isPressed={'icon'===desktop ? true : false} isSmall={true} onClick={(e) => this.onDesktopClick( 'icon' ) } >Icon</Button>
                                    <Button variant="secondary" isPressed={'text'===desktop ? true : false} isSmall={true} onClick={(e) => this.onDesktopClick( 'text' )} >Text</Button>
                                    <Button variant="secondary" isPressed={'icon-and-text'===desktop ? true : false} isSmall={true} onClick={(e) => this.onDesktopClick( 'icon-and-text' )} >Icon and Text</Button>
                                </ButtonGroup> 
                            </FlexBlock>
                        </Flex>

                        <Flex className="wpa-space-bottom"  justify="space-between">
                            <FlexItem>Mobile</FlexItem>
                            
                            <FlexBlock className="wpa-content-right">
                                <ButtonGroup>
                                    <Button variant="secondary" isPressed={'icon'===mobile ? true : false} isSmall={true} onClick={(e) => this.onMobileClick( 'icon' ) } >Icon</Button>
                                    <Button variant="secondary" isPressed={'text'===mobile ? true : false} isSmall={true} onClick={(e) => this.onMobileClick( 'text' ) } >Text</Button>
                                    <Button variant="secondary" isPressed={'icon-and-text'===mobile ? true : false} isSmall={true} onClick={(e) => this.onMobileClick( 'icon-and-text' ) } >Icon and Text</Button>
                                </ButtonGroup>
                            </FlexBlock>
                        </Flex>


                        <TextControl
                            label="Button Text"
                            placeholder="Search"
                            value={ text }
                            onChange={this.onTextChange}
                        />


                        <p>Text Color</p>

                        <ColorPalette
                            disableCustomColors="false"
                            colors={ [
                                { name: 'red', color: '#f00' },
                                { name: 'white', color: '#fff' },
                                { name: 'blue', color: '#00f' },
                                { name: 'yellow', color: '#0ff' }
                            ] }
                            value={color_primary}
                            onChange={this.onPrimaryColorChange}
                        />

                        <p>Background Color</p>

                        <ColorPalette
                            colors={ [
                                { name: 'red', color: '#f00' },
                                { name: 'white', color: '#fff' },
                                { name: 'blue', color: '#00f' },
                            ] }
                            value={color_secondary}
                            onChange={this.onSecondaryColorChange}
                        />
                        

                    </Panel>
                </PanelBody>
            </>
        )
    }
}

export default PartialButtons;