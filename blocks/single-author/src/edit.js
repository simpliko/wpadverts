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
    BaseControl,
    CheckboxControl

} from '@wordpress/components';


import { megaphone } from '@wordpress/icons';

import ServerSideRender from '@wordpress/server-side-render';
import AdvertsSelect from '../../../assets/jsx/adverts-select';
import AdvertsListData from '../../../assets/jsx/adverts-list-data';
import AdvertsColorPicker from '../../../assets/jsx/adverts-color-picker';


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

    onChangeLayout = ( layout ) => {
        this.props.setAttributes( { layout } );
    }

    onToggleShowAvatar = ( show_avatar ) => {
        this.props.setAttributes( { show_avatar } );
    }

    onChangeAvatarSize = ( avatar_size ) => {
        this.props.setAttributes( { avatar_size } );
    }

    onChangeAvatarRadius = ( avatar_radius ) => {
        this.props.setAttributes( { avatar_radius } );
    }

    setDataSecondaryChecked = ( checked, name ) => {
        var c = [];//this.props.attributes.contact;

        this.props.attributes.data_secondary.map((method) => {
            c.push(method);
        });

        var index = c.indexOf(name);

        if(checked && index === -1) {
            c.push(name);
        } else if(!checked && index >= 0) {
            c.splice(index, 1);
        }
        //console.log(data);
        //console.log(c);
        this.props.setAttributes( { data_secondary: c } );
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
                    label="Classifieds Single Gallery" 
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
            layout,
            show_avatar,
            avatar_size,
            avatar_radius,
            data_main,
            data_secondary
        } = attributes;

        var stl = {};
        var cls = [

        ].join( " " );

        const { show_instructions } = this.state;

        return (
            <>
                { this.state.initiated === true ?

                <>
                <InspectorControls>

                    <PanelBody title="Display Options">

                        <SelectControl
                            label="Layout"
                            value={ layout }
                            options={ [
                                { label: 'List', value: 'list' },
                                { label: 'Grid', value: 'grid' },
                                { label: 'Text', value: 'text' }
                            ] }
                            onChange={this.onChangeLayout}
                        />

                    </PanelBody>

                    <PanelBody title="Avatar Options">

                        <ToggleControl
                            label="Show Avatar."
                            checked={show_avatar}
                            onChange={this.onToggleShowAvatar}
                        />

                        <SelectControl
                            label="Avatar Size"
                            value={ avatar_size }
                            options={ [
                                { label: '16px', value: 16 },
                                { label: '32px', value: 32 },
                                { label: '64px', value: 64 }
                            ] }
                            onChange={this.onChangeAvatarSize}
                        />

                        <SelectControl
                            label="Border Radius"
                            labelPosition="top"
                            value={avatar_radius}
                            options={ [
                                { label: 'None', value: 'atw-rounded-none' },
                                { label: 'Extra Small', value: 'atw-rounded-sm' },
                                { label: 'Small', value: 'atw-rounded' },
                                { label: 'Medium', value: 'atw-rounded-md' },
                                { label: 'Large', value: 'atw-rounded-lg' },
                                { label: 'Extra Large', value: 'atw-rounded-xl' },
                                { label: 'Full', value: 'atw-rounded-full' },
                            ] }
                            onChange={this.onChangeAvatarRadius}
                        />

                    </PanelBody>

                    <PanelBody title="Data - Main Row">
                        <CheckboxControl
                            label="Name"
                            value="name"
                            readOnly="readOnly"
                            checked={ true }
                            onChange={() => alert("Cannot disable showing a name") }
                        />

                    </PanelBody>

                    <PanelBody title="Data - Second Row">
                        <CheckboxControl
                            label="Published"
                            value="published"
                            checked={data_secondary.includes('published')}
                            onChange={(is_checked) => this.setDataSecondaryChecked(is_checked, "published")}
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


                <div class="atw-flex atw-grow ">
                    {show_avatar && 
                    <div class="atw-flex-none atw-m-0 atw-pr-4 atw-justify-center -adverts-single-author-avatar">
                        <img alt="" src="http://1.gravatar.com/avatar/496c0741682ce4dc7c7f73ca4fe8dc5e?s=64&amp;d=mm&amp;r=g" class={ "avatar avatar-64 photo atw-m-0 atw-p-0 atw-block " + avatar_radius } loading="lazy" decoding="async" width={avatar_size} height={avatar_size} />
                    </div>
                    }
                    <div class="atw-flex atw-flex-col atw-grow atw-justify-center -adverts-single-author-name">
                        <div class="atw-block">
                            <span class="atw-font-bold atw-text-gray-700 atw-text-xl">John Doe</span>
                        </div>
                        <div class="atw-block">
                            {data_secondary.includes('published') &&
                                <span class="atw-text-gray-500 atw-text-base">
                                Published 17/07/2023 - 2 hours ago
                                </span>
                            }
                        </div>
                            
                    </div>
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