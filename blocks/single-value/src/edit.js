import { Component } from '@wordpress/element';
import { __ } from '@wordpress/i18n';

import { RichText, BlockControls, InspectorControls , PanelColorSettings } from '@wordpress/block-editor';
import { 
    Button,
    Dashicon,
    Placeholder,
    Icon,
    Spinner,
    Disabled, 
    PanelBody, 
    Toolbar, 
    ToolbarDropdownMenu, 
    ColorIndicator,
    ColorPalette, 
    ColorPicker,
    DropdownMenu, 
    SelectControl,
    TextControl,
    ToggleControl,
    RangeControl,
    Modal,
    BaseControl

} from '@wordpress/components';


import { megaphone } from '@wordpress/icons';

import ServerSideRender from '@wordpress/server-side-render';
import AdvertsSelect from '../../../assets/jsx/adverts-select';
import AdvertsListData from '../../../assets/jsx/adverts-list-data';
import AdvertsColorPicker from '../../../assets/jsx/adverts-color-picker';
import PreviewHelper from '../../../assets/jsx/preview-helper';


class Edit extends Component {

	constructor(props) {
        super(props);
        
		this.state = {
            initiated: false,
			post_types: [],
            loading: true,
            show_instructions: false
        }
        
        this.initVisuals();
	}

    componentDidMount() {
        this.runApiFetchForms();
    }

    runApiFetchForms() {
		wp.apiFetch({
			path: 'wpadverts/v1/classifieds-types',
		}).then(data => {
			this.setState({
				post_types: data.data,
                loading: false,
                initiated: ( this.props.attributes.post_type !== "" )
            });
            //console.log(this.state);
		});
    }

    onChangeColor = ( color ) => {
        this.props.setAttributes( { color } );
    }

    onFormStyleChange = ( form_style ) => {
        this.props.setAttributes( { form_style } );
    }

    onSelectPostType = ( post_type ) => {
        this.props.setAttributes( { post_type, form_scheme: "" } );
    }

    onSelectFormScheme = ( form_scheme ) => {
        this.props.setAttributes( { form_scheme } );
    }

    onCustomizeQuery = ( param, value ) => {
        var query = {...this.props.attributes.query};

        if( value.length === 0 ) {
            delete query[param];
        } else {
            query[param] = value;
        }
        
        this.props.setAttributes( { query } );
    }

    getQueryParam = ( param ) => {
        
        //console.log(this.props.attributes.query);
        if( typeof this.props.attributes.query[param] === 'undefined') {
            return "";
        } else {
            return this.props.attributes.query[param]
        }
    }

    getAvailablePostTypes = () => {
        var types = [{
            label: "", value: ""
        }];

        this.state.post_types.forEach(function(item, index) {
            types.push({ 
                label:item.label, 
                value:item.post_type
            });
        });

        return types;
    }

    getCurrentPostType = () => {
        return this.state.post_types[0];
    }

    getSelectedFormScheme = ( type ) => {

        if(this.props.attributes.form_scheme === "") {
            return null;
        }

        var pt = this.getCurrentPostType();

        for(var i=0; i<pt.form_schemes[type].length; i++) {
            if(pt.form_schemes[type][i].name === this.props.attributes.form_scheme) {
                return pt.form_schemes[type][i];
            }
        }

        return null;
    }

    getSelectedFormSchemeData = ( type ) => {
        var fs = this.getSelectedFormScheme(type);

        if(fs === null) {
            return [];
        } else {
            return fs.data;
        }
    }

    getAvailableSearchForms = ( post_type ) => {
        var pt = this.getCurrentPostType();
        return [{label: "", value: ""}].concat( pt["form_schemes"]["search"] );
    }

    getCurrentPostType = () => {
        return this.state.post_types[0];
    }

    getSelectedFormScheme = ( type ) => {

        if(this.props.attributes.form_scheme === "") {
            return null;
        }

        var pt = this.getCurrentPostType();

        for(var i=0; i<pt.form_schemes[type].length; i++) {
            if(pt.form_schemes[type][i].name === this.props.attributes.form_scheme) {
                return pt.form_schemes[type][i];
            }
        }

        return null;
    }

    getSelectedFormSchemeData = ( type ) => {

        var pt = this.getCurrentPostType();
        var data = [];

        //console.log(pt);
        for(var i=0; i<pt.form_schemes[type].length; i++) {

            Object.entries(pt.form_schemes[type][i].data).map(([key, value]) => {
                data.push(value);
            })
            data = this.getUnique(data, "name");
            data.sort((a, b) => a.label > b.label ? 1 : -1)
            //data.push(pt.form_schemes[type][i].data);
        }
        //console.log(data);
        return data;
    }

    getUnique = (array, key) => {
        if (typeof key !== 'function') {
          const property = key;
          key = function(item) { return item[property]; };
        }
        return Array.from(array.reduce(function(map, item) {
          const k = key(item);
          if (!map.has(k)) map.set(k, item);
          return map;
        }, new Map()).values());
    }

    getAvailableSearchForms = ( post_type ) => {
        var pt = this.getCurrentPostType();
        return [{label: "", value: ""}].concat( pt["form_schemes"]["search"] );
    }

    getAdvertsListData = () => {

        var pt = this.getCurrentPostType();

        var scheme = {
            builtin: {
                label: "Builtin",
                data: pt.form_schemes_default.publish
            },
            meta: {
                label: "Custom Fields",
                data: this.getSelectedFormSchemeData( "publish" )
            },
            taxonomies: {
                label: "Taxonomies",
                data: []
            }
        };

        for(var i=0; i<pt.taxonomies.length; i++) {
            scheme.taxonomies.data.push({
                name: "taxonomy__" + pt.taxonomies[i].name,
                label: pt.taxonomies[i].label
            });
        }
        
        //console.log(this.props.attributes.form_scheme);
        //console.log(scheme);

        return scheme;
    }

    getDataOptions() {
        var options = [];
        var data = this.getAdvertsListData();
        var i = 0;
        var x = null;

        var optgroup = [
            {
                label: "Builtin",
                options: []
            },
            {
                label: "Patterns",
                options: []
            },
            {
                label: "Custom Fields",
                options: []
            },
            {
                label: "Taxonomies",
                options: []
            }
        ];

        for(i=0;i<data.builtin.data.length; i++) {
            if( data.builtin.data[i].name.startsWith("pattern__")) {
                x = 1;
            } else {
                x = 0;
            }
            optgroup[x].options.push({
                value: data.builtin.data[i].name,
                label: data.builtin.data[i].label
            })
        }

        for(i=0;i<data.meta.data.length; i++) {
            optgroup[2].options.push({
                value: data.meta.data[i].name,
                label: data.meta.data[i].label
            })
        }

        for(i=0;i<data.taxonomies.data.length; i++) {
            optgroup[3].options.push({
                value: data.taxonomies.data[i].name,
                label: data.taxonomies.data[i].label
            })
        }




        return optgroup;
    }

    onListDataChange = ( data ) => {
        this.props.setAttributes( { data: [ ...data ] } ); 
    }

    onChangeEmptyValue = ( empty_value ) => {
        this.props.setAttributes( { empty_value } );
    }

    onChangeRenderAs = ( render_as ) => {
        this.props.setAttributes( { render_as } );
    }

    onChangeTextSize = ( text_size ) => {
        this.props.setAttributes( { text_size } );
    }

    onChangeFontWeight = ( font_weight ) => {
        this.props.setAttributes( { font_weight } );
    }

    onChangeType = ( type ) => {
        this.props.setAttributes( { type } );
    }

    onChangeColor = ( color ) => {
        this.props.setAttributes( { color } );
    }

    onChangeColorBg = ( color_bg ) => {
        this.props.setAttributes( { color_bg } );
    }

    onChangeMarginX = ( margin_x ) => {
        this.props.setAttributes( { margin_x } );
    }    
    
    onChangeMarginY = ( margin_y ) => {
        this.props.setAttributes( { margin_y } );
    }

    onChangePaddingX = ( padding_x ) => {
        this.props.setAttributes( { padding_x } );
    }    
    
    onChangePaddingY = ( padding_y ) => {
        this.props.setAttributes( { padding_y } );
    }

    onChangeBorderRadius = ( border_radius ) => {
        this.props.setAttributes( { border_radius } );
    }

    onPreviewValueChange = ( p_value ) => {
        this.props.setAttributes( { p_value } );
    }

    initVisuals = () => {
        const { post_type } = this.props.attributes;

        if( post_type === "" ) {
            return;
        }

        this.setState( { initiated: true } );
    }

    resetVisuals = () => {
        this.props.setAttributes( { post_type: "" } );
        this.setState( { initiated: false, loading: true } );
        this.runApiFetchForms();
    }

    renderInit() {

        const { post_type } = this.props.attributes;
        const { show_instructions } = this.state;

        return (
            <>
                <Placeholder 
                    icon={ megaphone } 
                    label="Classifieds Single Value" 
                    instructions="Select custom post type and search form scheme to continue."
                    isColumnLayout="true"
                >
                    
                    { this.state.loading === true ? 
                    
                        <Spinner/>

                    :

                        <>
                            <SelectControl
                                label="Custom Post Type"
                                labelPosition="top"
                                value={ post_type }
                                options={ this.getAvailablePostTypes() }
                                onChange={this.onSelectPostType}
                                style={{lineHeight:'1rem'}}
                            />

                            <div>
                                <Button 
                                    variant="primary"
                                    disabled={ ( post_type === "" ) }
                                    onClick={this.initVisuals}
                                >
                                    Apply
                                </Button>

                            </div>

                        </>
                    }

                </Placeholder>
            </>
        )
    }



    render() {

        const { 
            className, 
            attributes 
        } = this.props;

        const { 
            data,
            empty_value,
            render_as,
            text_size,
            font_weight,
            type,
            color,
            color_bg,
            margin_x,
            margin_y,
            padding_x,
            padding_y,
            border_radius,
            p_value

        } = attributes;

        var stl = {
            color: color,
            backgroundColor: color_bg
        };

        var cls = [
            text_size,
            font_weight,
            type,
            margin_x,
            margin_y,
            padding_x,
            padding_y,
            border_radius
        ].join( " " );

        const { show_instructions } = this.state;

        return (
            <>
                { this.state.initiated === true ?

                <>
                <InspectorControls>

                    <PanelBody title="Display Options">

                        <AdvertsListData 
                            label="Property"
                            data={this.getAdvertsListData()}
                            onChange={this.onListDataChange}
                            value={data}
                            mode="single"
                        />

                        <TextControl
                            label="Empty Value"
                            value={empty_value}
                            onChange={this.onChangeEmptyValue}
                            type="text"
                        />

                        <SelectControl
                            label="Render As"
                            labelPosition="top"
                            value={render_as}
                            options={ [
                                { label: 'Text', value: 'text' },
                                { label: 'HTML', value: 'html' },
                            ] }
                            onChange={this.onChangeRenderAs}
                        />

                    </PanelBody>

                    { render_as === 'html' &&
                    <PanelBody title="Formatting">

                        <SelectControl
                            label="Text Size"
                            labelPosition="top"
                            value={text_size}
                            options={ [
                                { label: 'Extra Small', value: 'atw-text-xs' },
                                { label: 'Small', value: 'atw-text-sm' },
                                { label: 'Normal', value: 'atw-text-base' },
                                { label: 'Large', value: 'atw-text-lg' },
                                { label: 'Extra Large', value: 'atw-text-xl' },
                                { label: 'Extra Large x2', value: 'atw-text-2xl' },
                                { label: 'Extra Large x3', value: 'atw-text-3xl' },
                                { label: 'Extra Large x4', value: 'atw-text-4xl' },
                                { label: 'Extra Large x5', value: 'atw-text-5xl' },
                                { label: 'Extra Large x6', value: 'atw-text-6xl' },
                                { label: 'Extra Large x7', value: 'atw-text-7xl' },
                                { label: 'Extra Large x8', value: 'atw-text-8xl' },
                                { label: 'Extra Large x9', value: 'atw-text-9xl' },
                            ] }
                            onChange={this.onChangeTextSize}
                        />

                        <SelectControl
                            label="Font Weight"
                            labelPosition="top"
                            value={font_weight}
                            options={ [
                                { label: 'Thin', value: 'atw-font-thin' },
                                { label: 'Normal', value: 'atw-font-normal' },
                                { label: 'Semi-Bold', value: 'atw-font-semibold' },
                                { label: 'Bold', value: 'atw-font-bold' },
                            ] }
                            onChange={this.onChangeFontWeight}
                        />

                        <SelectControl
                            label="Type"
                            labelPosition="top"
                            value={type}
                            options={ [
                                { label: 'Inline', value: 'atw-inline' },
                                { label: 'Inline Block', value: 'atw-inline-block' },
                                { label: 'Block', value: 'atw-block' },
                            ] }
                            onChange={this.onChangeType}
                        />

                        <AdvertsColorPicker
                            label="Text Color"
                            labelPosition="top"
                            value={color}
                            onChange={this.onChangeColor}
                        />                        
                        
                        <AdvertsColorPicker
                            label="Background Color"
                            labelPosition="top"
                            value={color_bg}
                            onChange={this.onChangeColorBg}
                        />

                        <SelectControl
                            label="Border Radius"
                            labelPosition="top"
                            value={border_radius}
                            options={ [
                                { label: 'None', value: 'atw-rounded-none' },
                                { label: 'Extra Small', value: 'atw-rounded-sm' },
                                { label: 'Small', value: 'atw-rounded' },
                                { label: 'Medium', value: 'atw-rounded-md' },
                                { label: 'Large', value: 'atw-rounded-lg' },
                                { label: 'Extra Large', value: 'atw-rounded-xl' },
                                { label: 'Full', value: 'atw-rounded-full' },
                            ] }
                            onChange={this.onChangeBorderRadius}
                        />

                        <div style={{display:"flex"}}>
                            <SelectControl
                                label="Margin (width)"
                                labelPosition="top"
                                value={margin_x}
                                options={ [
                                    { label: 'None', value: '' },
                                    { label: '~2px', value: 'atw-mx-0.5' },
                                    { label: '~4px', value: 'atw-mx-1' },
                                    { label: '~6px', value: 'atw-mx-1.5' },
                                    { label: '~8px', value: 'atw-mx-2' },
                                    { label: '~10px', value: 'atw-mx-2.5' },
                                    { label: '~12px', value: 'atw-mx-3' },
                                    { label: '~14px', value: 'atw-mx-3.5' },
                                    { label: '~16px', value: 'atw-mx-4' },
                                    { label: '~18px', value: 'atw-mx-4.5' },
                                    { label: '~20px', value: 'atw-mx-5' },
                                    { label: '~24px', value: 'atw-mx-6' },
                                    { label: '~28px', value: 'atw-mx-7' },
                                    { label: '~32px', value: 'atw-mx-8' },
                                    { label: '~36px', value: 'atw-mx-9' },
                                    { label: '~40px', value: 'atw-mx-10' },
                                ] }
                                onChange={this.onChangeMarginX}
                            />      
                            <span> </span>                  
                            <SelectControl
                                label="Margin (height)"
                                labelPosition="top"
                                value={margin_y}
                                options={ [
                                    { label: 'None', value: '' },
                                    { label: '~2px', value: 'atw-my-0.5' },
                                    { label: '~4px', value: 'atw-my-1' },
                                    { label: '~6px', value: 'atw-my-1.5' },
                                    { label: '~8px', value: 'atw-my-2' },
                                    { label: '~10px', value: 'atw-my-2.5' },
                                    { label: '~12px', value: 'atw-my-3' },
                                    { label: '~14px', value: 'atw-my-3.5' },
                                    { label: '~16px', value: 'atw-my-4' },
                                    { label: '~18px', value: 'atw-my-4.5' },
                                    { label: '~20px', value: 'atw-my-5' },
                                    { label: '~24px', value: 'atw-my-6' },
                                    { label: '~28px', value: 'atw-my-7' },
                                    { label: '~32px', value: 'atw-my-8' },
                                    { label: '~36px', value: 'atw-my-9' },
                                    { label: '~40px', value: 'atw-my-10' },
                                ] }
                                onChange={this.onChangeMarginY}
                            />
                        </div>

                        <div style={{display:"flex"}}>
                            <SelectControl
                                label="Padding (width)"
                                labelPosition="top"
                                value={padding_x}
                                options={ [
                                    { label: 'None', value: '' },
                                    { label: '~2px', value: 'atw-px-0.5' },
                                    { label: '~4px', value: 'atw-px-1' },
                                    { label: '~6px', value: 'atw-px-1.5' },
                                    { label: '~8px', value: 'atw-px-2' },
                                    { label: '~10px', value: 'atw-px-2.5' },
                                    { label: '~12px', value: 'atw-px-3' },
                                    { label: '~14px', value: 'atw-px-3.5' },
                                    { label: '~16px', value: 'atw-px-4' },
                                    { label: '~18px', value: 'atw-px-4.5' },
                                    { label: '~20px', value: 'atw-px-5' },
                                    { label: '~24px', value: 'atw-px-6' },
                                    { label: '~28px', value: 'atw-px-7' },
                                    { label: '~32px', value: 'atw-px-8' },
                                    { label: '~36px', value: 'atw-px-9' },
                                    { label: '~40px', value: 'atw-px-10' },
                                ] }
                                onChange={this.onChangePaddingX}
                            />      
                            <span> </span>                  
                            <SelectControl
                                label="Padding (height)"
                                labelPosition="top"
                                value={padding_y}
                                options={ [
                                    { label: 'None', value: '' },
                                    { label: '~2px', value: 'atw-py-0.5' },
                                    { label: '~4px', value: 'atw-py-1' },
                                    { label: '~6px', value: 'atw-py-1.5' },
                                    { label: '~8px', value: 'atw-py-2' },
                                    { label: '~10px', value: 'atw-py-2.5' },
                                    { label: '~12px', value: 'atw-py-3' },
                                    { label: '~14px', value: 'atw-py-3.5' },
                                    { label: '~16px', value: 'atw-py-4' },
                                    { label: '~18px', value: 'atw-py-4.5' },
                                    { label: '~20px', value: 'atw-py-5' },
                                    { label: '~24px', value: 'atw-py-6' },
                                    { label: '~28px', value: 'atw-py-7' },
                                    { label: '~32px', value: 'atw-py-8' },
                                    { label: '~36px', value: 'atw-py-9' },
                                    { label: '~40px', value: 'atw-py-10' },
                                ] }
                                onChange={this.onChangePaddingY}
                            />
                        </div>

                    </PanelBody>
                    }

                    <PanelBody title="Preview">
                        <PreviewHelper />

                        <TextControl
                            label="Preview Value"
                            value={p_value}
                            onChange={this.onPreviewValueChange}
                            type="text"
                        />
                    </PanelBody>

                </InspectorControls>

                <BlockControls>
                    <Toolbar 
                        controls={[
                            {
                                icon: "controls-repeat",
                                title: "Reset post type and form scheme",
                                onClick:this.resetVisuals
                            }
                        ]}
                    />
                </BlockControls>

                <Disabled>
                    {/*
                    <ServerSideRender
                        block="wpadverts/details"
                        attributes={ this.props.attributes }
                    />
                    */}
                    <div className="-wpa-block-editor-common-tip">
                        { render_as === 'html' &&
                        <span class={cls} style={stl}>
                            <span>{p_value}</span>
                        </span>
                        }

                        { render_as === 'text' && 
                            <span>{p_value}</span>
                        }
                    </div>
                </Disabled>

                </>

                : 

                <>
                    {this.renderInit()}
                </>

                }
            </>
        ) ;
    }
}

export default Edit;