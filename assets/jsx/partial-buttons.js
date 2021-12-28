import { Component } from '@wordpress/element';
import { __ } from '@wordpress/i18n';

import {
    Panel, 
    PanelBody,
    Flex, 
    Button, 
    ButtonGroup,
    BaseControl,
    TextControl,
    ToggleControl,
    ColorPalette,
    Popover,
    SelectControl,
    RangeControl
} from '@wordpress/components';

import { useInstanceId } from '@wordpress/compose';
import { useState, forwardRef } from '@wordpress/element';

import AdvertsColorPicker from './adverts-color-picker';

class PartialButtons extends Component {

	constructor( props ) {
        
		super(props);
        this.data = {
            text: this.props.placeholder,
            desktop: "",
            mobile: "",
            border_radius: "",
            border_width: "",
            color_text: "",
            color_bg: "",
            color_border: "",
            color_text_h: "",
            color_bg_h: "",
            color_border_h: "",
            customize: 0,
            ...props.data
        };

        this.state = {
            mode: "normal",
            popover_mode: "desktop",
            popover_is_visible: false
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
        console.log(desktop);
        this.data.desktop = desktop;
        this.props.onChange( this.data );
    }

    onMobileClick = ( mobile ) => {
        this.data.mobile = mobile;
        this.props.onChange( this.data );
    }

    onColorTextChange = ( color_text ) => {
        this.data.color_text = color_text;
        this.props.onChange( this.data );
    }

    onColorBgChange = ( color_bg ) => {
        this.data.color_bg = color_bg;
        this.props.onChange( this.data );
    }

    onColorBorderChange = ( color_border ) => {
        this.data.color_border = color_border;
        this.props.onChange( this.data );
    }

    onColorTextHoverChange = ( color_text_h ) => {
        this.data.color_text_h = color_text_h;
        this.props.onChange( this.data );
    }

    onColorBgHoverChange = ( color_bg_h ) => {
        this.data.color_bg_h = color_bg_h;
        this.props.onChange( this.data );
    }

    onColorBorderHoverChange = ( color_border_h ) => {
        this.data.color_border_h = color_border_h;
        this.props.onChange( this.data );
    }

    onModeToggleClick = ( mode ) => {
        this.setState( { mode } );
    }

    togglePopover = () => {
        this.setState( { popover_is_visible: ! this.state.popover_is_visible } );
    }

    setPopoverMode = ( popover_mode ) => {
        this.setState( { popover_mode, popover_is_visible: false } );
    }

    onBorderRadiusChange = ( border_radius ) => {
        this.data.border_radius = border_radius;
        this.props.onChange( this.data );
    }

    onBorderWidthChange = ( border_width ) => {
        this.data.border_width = border_width;
        this.props.onChange( this.data );
    }

    onCustomizeChange = ( customize ) => {
        this.data.customize = (customize === true ? 1 : 0);
        this.props.onChange( this.data );
    }

    render( ) {

        const {
            desktop,
            mobile,
            text,
            border_radius,
            border_width,
            color_text,
            color_bg,
            color_border,
            color_text_h,
            color_bg_h,
            color_border_h,
            customize
        } = this.data;

        const {
            mode, 
            popover_is_visible,
            popover_mode
        } = this.state;

        const color_palette =  [
            { name: 'white', color: '#FFFFFF' },
            { name: 'black', color: '#000000' },
            { name: 'red', color: '#B91C1C' },
            { name: 'yellow', color: '#B45309' },
            { name: 'green', color: '#047857' },
            { name: 'blue', color: '#1D4ED8' },
            { name: 'indigo', color: '#4338CA' },
            { name: 'purple', color: '#6D28D9' }
            
        ];

        const color_palette_hover =  [
            { name: 'white', color: '#FFFFFF' },
            { name: 'black', color: '#000000' },
            { name: 'red', color: '#B91C1C' },
            { name: 'yellow', color: '#B45309' },
            { name: 'green', color: '#047857' },
            { name: 'blue', color: '#1D4ED8' },
            { name: 'indigo', color: '#4338CA' },
            { name: 'purple', color: '#fff000' }
            
        ];

        return (
            <>

                <Panel>

                    <PanelBody title={this.props.title} initialOpen={this.props.initialOpen}>


                        <TextControl
                            label="Button Text"
                            placeholder={this.props.placeholder}
                            value={ text }
                            onChange={this.onTextChange}
                        />

                        <BaseControl>
                                Button Display

                                
                                <Button variant="tertiary" isSmall={true} onClick={this.togglePopover}>
                                <span className={"fas fa-"+popover_mode}></span>
                                
                                { popover_is_visible && 
                                    <Popover position="bottom center">
                                        <Button onClick={(e) => this.setPopoverMode('desktop')} style={{display:"inline-block", width:"100%"}} variant="secondary" isSmall={true}><span className="fas fa-desktop"></span></Button>
                                        <Button onClick={(e) => this.setPopoverMode('mobile')} style={{display:"inline-block", width:"100%"}} variant="secondary" isSmall={true}><span className="fas fa-mobile-alt"></span></Button>
                                    </Popover> 
                                }

                                </Button>

                                { popover_mode == 'desktop' && 
                                    <SelectControl
                                        value={ desktop }
                                        options={ [
                                            { label: 'Text', value: 'text' },
                                            { label: 'Icon', value: 'icon' },
                                            { label: 'Text and Icon', value: 'text-and-icon' }
                                        ] }
                                        onChange={this.onDesktopClick}
                                    /> 
                                }

                                { popover_mode === 'mobile' && 
                                    <SelectControl
                                        value={ mobile }
                                        options={ [
                                            { label: 'Text', value: 'text' },
                                            { label: 'Icon', value: 'icon' },
                                            { label: 'Text and Icon', value: 'text-and-icon' }
                                        ] }
                                        onChange={this.onMobileClick}
                                    /> 
                                }
                        </BaseControl>

                        <ToggleControl
                            label="Customize the default form styling"
                            checked={ customize }
                            onChange={this.onCustomizeChange}
                        />

                        <div className="wpa-block-editor-common-tip">
                            The default button styling (applied to all WPAdverts buttons) you can change in the <a href="http://localhost/wpadverts/wp-admin/edit.php?post_type=advert&amp;page=adverts-extensions&amp;module=styling" target="_blank">Styling Settings</a>.
                        </div>

                        { customize === 1 && 

                            <>

                                <BaseControl label="Border Radius">
                                    <RangeControl
                                        min="0"
                                        max="6"
                                        step="1"
                                        xmarks={[
                                            {value:0, label:"None"},
                                            {value:1, label:"XS"},
                                            {value:2, label:"S"},
                                            {value:3, label:"M"},
                                            {value:4, label:"L"},
                                            {value:5, label:"XL"},
                                            {value:6, label:"Full"}
                                        ]}
                                        showTooltip={false}
                                        withInputField={false}
                                        value={border_radius}
                                        onChange={this.onBorderRadiusChange}
                                    >
                                    </RangeControl>
                                </BaseControl>                        
                                
                                <BaseControl label="Border Width">
                                    <RangeControl
                                        min="0"
                                        max="3"
                                        step="1"
                                        xmarks={[
                                            {value:0, label:"None"},
                                            {value:1, label:"Thin"},
                                            {value:2, label:"Thick"},
                                            {value:3, label:"Extra Thick"}
                                        ]}
                                        showTooltip={false}
                                        withInputField={false}
                                        value={border_width}
                                        onChange={this.onBorderWidthChange}
                                    >

                                    </RangeControl>
                                </BaseControl>


                                <ButtonGroup style={{width:"100%"}}>
                                    <Flex className="wpa-space-bottom"  justify="space-between">
                                        <Button style={{width:"100%", justifyContent:"center"}} variant="secondary" isPressed={'normal'===mode ? true : false}  onClick={(e) => this.onModeToggleClick( 'normal' ) } >Normal</Button>
                                        <Button style={{width:"100%", justifyContent:"center"}} variant="secondary" isPressed={'hover'===mode ? true : false}  onClick={(e) => this.onModeToggleClick( 'hover' ) } >Hover</Button>
                                    </Flex>
                                </ButtonGroup>

                                { mode == "normal" && 
                                <div>
                                    <BaseControl label="Text Color">
                                        <AdvertsColorPicker
                                            value={color_text}
                                            onChange={this.onColorTextChange}
                                        />
                                    </BaseControl>

                                    <BaseControl label="Background Color">
                                        <AdvertsColorPicker
                                            colors={color_palette}
                                            value={color_bg}
                                            onChange={this.onColorBgChange}
                                        />
                                    </BaseControl>

                                    <BaseControl label="Border Color">
                                        <AdvertsColorPicker
                                            colors={color_palette}
                                            value={color_border}
                                            onChange={this.onColorBorderChange}
                                        />
                                    </BaseControl>
                                </div>
                                }

                                { mode == "hover" && 
                                <div>
                                    <BaseControl label="Text Color">
                                        <AdvertsColorPicker
                                            colors={color_palette_hover}
                                            value={color_text_h}
                                            onChange={this.onColorTextHoverChange}
                                        />
                                    </BaseControl>

                                    <BaseControl label="Background Color">
                                        <AdvertsColorPicker
                                            colors={color_palette_hover}
                                            value={color_bg_h}
                                            onChange={this.onColorBgHoverChange}
                                        />
                                    </BaseControl>

                                    <BaseControl label="Border Color">
                                        <AdvertsColorPicker
                                            colors={color_palette_hover}
                                            value={color_border_h}
                                            onChange={this.onColorBorderHoverChange}
                                        />
                                    </BaseControl>
                                </div>
                                }

                            </>
                        }

                    </PanelBody>  
                </Panel>
                
            </>
        )
    }
}

export default PartialButtons;