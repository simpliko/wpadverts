import { Component } from '@wordpress/element';
import { __ } from '@wordpress/i18n';

import {
    Panel, 
    PanelBody,
    BaseControl,
    TextControl,
    ToggleControl,
    SelectControl,
    RangeControl
} from '@wordpress/components';

class PartialForm extends Component {

	constructor( props ) {

        super(props);
        
        this.data = {
            style: "wpa-solid",
            shadow: "wpa-none",
            palette: "cool-gray",
            rounded: "3",
            px: "",
            py: "",
            border: "1",
            customize: 0,
            ...props.data
        };

        this.state = {

        };
    }

    onPaletteChange = ( palette ) => {
        this.data.palette = palette;
        this.props.onChange( this.data )
    }

    onStyleChange = ( style ) => {
        this.data.style = style;
        this.props.onChange( this.data )
    }

    onShadowChange = ( shadow ) => {
        this.data.shadow = shadow;
        this.props.onChange( this.data )
    }

    onRoundedChange = ( rounded ) => {
        this.data.rounded = rounded;
        this.props.onChange( this.data )
    }

    onBorderChange = ( border ) => {
        this.data.border = border;
        this.props.onChange( this.data )
    }

    onPXChange = ( px ) => {
        this.data.px = px;
        this.props.onChange( this.data )
    }
    onPYChange = ( py ) => {
        this.data.py = py;
        this.props.onChange( this.data )
    }

    onCustomizeChange = ( customize ) => {
        this.data.customize = (customize === true ? 1 : 0);
        this.props.onChange( this.data );
    }

    void = ( param ) => {
        this.props.onChange( this.data );
    }

    render( ) {
        
        const {
            style,
            shadow,
            palette,
            rounded,
            px,
            py,
            border,
            customize
        } = this.data;

        const {

        } = this.state;



        return (
            <>
                <Panel>

                    <PanelBody title={this.props.title} initialOpen={this.props.initialOpen}>

                        <ToggleControl
                            label="Customize the default form styling"
                            checked={ customize }
                            onChange={this.onCustomizeChange}
                        />

                        <div className="wpa-block-editor-common-tip">
                            The default form styling (applied to all WPAdverts form by default) you can change in the <a href="http://localhost/wpadverts/wp-admin/edit.php?post_type=advert&amp;page=adverts-extensions&amp;module=styling" target="_blank">Styling Settings</a>.
                        </div>

                        { customize === 1 && 
                            <>
                                <SelectControl
                                    label="Color Palette"
                                    labelPosition="top"
                                    value={ palette }
                                    options={ [
                                        { label: 'Blue Gray', value: 'blue-gray' },
                                        { label: 'Cool Gray', value: 'cool-gray' },
                                        { label: 'Gray', value: 'gray' },
                                        { label: 'True Gray', value: 'true-gray' },
                                        { label: 'Warm Gray', value: 'warm-gray' }
                                    ] }
                                    onChange={this.onPaletteChange}
                                />  

                                <SelectControl
                                    label="Style"
                                    labelPosition="top"
                                    value={ style }
                                    options={ [
                                        { label: 'None (default styling)', value: 'wpa-unstyled' },
                                        { label: 'Flat', value: 'wpa-flat' },
                                        { label: 'Solid', value: 'wpa-solid' },
                                        { label: 'Bottom Border', value: 'wpa-bottom-border' },
                                    ] }
                                    onChange={this.onStyleChange}
                                />  

                                <SelectControl
                                    label="Shadow"
                                    labelPosition="top"
                                    value={ shadow }
                                    options={ [
                                        { label: 'None (default styling)', value: 'wpa-shadow-none' },
                                        { label: 'Small', value: 'wpa-shadow-sm' },
                                        { label: 'Medium', value: 'wpa-shadow-md' },
                                        { label: 'Inside', value: 'wpa-shadow-inside' },
                                    ] }
                                    onChange={this.onShadowChange}
                                />  

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
                                        value={border}
                                        onChange={this.onBorderChange}
                                    >

                                    </RangeControl>
                                </BaseControl>

                                <BaseControl label="Round Corners">
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
                                        value={rounded}
                                        onChange={this.onRoundedChange}
                                    >
                                    </RangeControl>
                                </BaseControl>  

                                <TextControl
                                    label="Padding - Horizontal"
                                    type="number"
                                    value={px}
                                    onChange={this.onPXChange}
                                />
                                
                                <TextControl
                                    label="Padding - Vertical"
                                    type="number"
                                    value={py}
                                    onChange={this.onPYChange}
                                />

                            </>

                        }


                    </PanelBody>

                </Panel>
            </>
        );
    }

}

export default PartialForm;