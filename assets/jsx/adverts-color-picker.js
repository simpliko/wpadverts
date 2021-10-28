import { Component } from '@wordpress/element';
import { __ } from '@wordpress/i18n';

import {
    Button, 
    BaseControl,
    ColorPicker,
    Popover,
} from '@wordpress/components';


class AdvertsColorPicker extends Component {


	constructor( props ) {
        
        super(props);
        
        this.state = {
            popover: false,
            color: null
        };

        this.value = this.props.value;
    }

    togglePopover = ( ) => {
        this.setState( { popover: !this.state.popover } );
    }

    setColor = ( color ) => {
        console.log(color);
        this.setState( { color: color.hex } );
    }

    applyColor = () => {
        this.value = this.state.color;
        this.setState( { popover: false, color: null } );
        this.props.onChange(this.value);
    }

    cancelColor = () => {
        this.setState( { popover: false, color: null } );
    }

    resetColor = () => {
        this.value = "";
        this.setState( { popover: false, color: null } );
        this.props.onChange(this.value);
    }

    render( ) {

        const {
            value,
            label,
            labelPosition,
        } = this.props;


        return(
            <>
                <BaseControl
                    label={label}
                    labelPosition="top"
                >
                    <div className="wpa-color-indicator-wrap">

                        <span className="wpa-color-indicator" style={{background: this.value}} />

                        <Button 
                            variant="secondary"
                            isSmall="true"
                            onClick={this.togglePopover}
                        >
                                Select Color
                        </Button>
                        
                        <Button 
                            variant="link"
                            onClick={this.resetColor}
                        >
                            Reset
                        </Button>

                    </div>

                    { this.state.popover === true && 
                        <Popover>
                            <ColorPicker
                                style={{padding:"5px"}}
                                color={this.value}
                                onChangeComplete={this.setColor}
                                enableAlpha
                                defaultValue="#000"
                            />
                            <div style={{padding:"0px 16px 12px 16px"}}>
                                <Button
                                    onClick={this.applyColor}
                                    variant="primary"
                                >
                                    Apply
                                </Button>
                                &nbsp;
                                <Button
                                    onClick={this.cancelColor}
                                    variant="secondary"
                                >
                                    Cancel
                                </Button>
                            </div>

                        </Popover>
                    }

                </BaseControl>
            </>
        );
    }
}

export default AdvertsColorPicker;
