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
		this.state = {
            desktop: props.data.desktop,
            text: null
        };
        this.data = {...props.data};
    }
    
    shouldComponentUpdate(nextProps) {
        return true;
    }

    onTextChange = ( text ) => {
        this.data.text = text;
        this.props.onChange( this.props.data );
    }

    onDesktopClick = ( desktop ) => {
        //this.setState( { desktop } );
        this.data.desktop = desktop;
        this.props.onChange( this.data );
    }

    render( ) {

        const {
            desktop,
            text
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
                                    <Button variant="secondary" isSmall={true} >Icon</Button>
                                    <Button variant="secondary" isSmall={true} >Text</Button>
                                    <Button variant="primary" isSmall={true} >Icon and Text</Button>
                                </ButtonGroup>
                            </FlexBlock>
                        </Flex>


                        <TextControl
                            label="Button Text"
                            placeholder="Search"
                            
                            onChange={this.onTextChange}
                        />


                        <p>Text Color</p>

                        <ColorPalette
                            colors={ [
                                { name: 'red', color: '#f00' },
                                { name: 'white', color: '#fff' },
                                { name: 'blue', color: '#00f' },
                            ] }
                            value="#ccc"
                            onChange={ ( color ) => alert( color ) }
                        />

                        <p>Background Color</p>

                        <ColorPalette
                            colors={ [
                                { name: 'red', color: '#f00' },
                                { name: 'white', color: '#fff' },
                                { name: 'blue', color: '#00f' },
                            ] }
                            value="#ccc"
                            onChange={ ( color ) => alert( color ) }
                        />
                        

                    </Panel>
                </PanelBody>
            </>
        )
    }
}

export default PartialButtons;