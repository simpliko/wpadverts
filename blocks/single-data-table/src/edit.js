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

    getFieldTypes() {
 

        var scheme = {
            builtin: {
                label: "Builtin",
                data: [
                    {
                        name: "__builtin",
                        label: "Builtin Fields"
                    }
                ]
            },
            meta: {
                label: "Fields",
                data: [

                    {
                        name: "adverts_field_text",
                        label: "Short Text"
                    },                    
                    {
                        name: "adverts_field_textarea",
                        label: "Text Area"
                    },                    
                    {
                        name: "adverts_field_select",
                        label: "Dropdown"
                    },                    
                    {
                        name: "adverts_field_checkbox",
                        label: "Checkbox"
                    },                    
                    {
                        name: "adverts_field_radio",
                        label: "Radio"
                    },                    
                    {
                        name: "adverts_field_autocomplete",
                        label: "Autocomplete"
                    }
                ]
            },
            taxonomies: {
                label: "Other",
                data: [                    
                    {
                        name: "adverts_cf_field_date",
                        label: "Date and Time"
                    }
                ]
            }
        };

        return scheme;
    }

    onChangeLayout = ( layout ) => {
        this.props.setAttributes( { layout } );
    }

    onChangeColumns = ( columns ) => {
        this.props.setAttributes( { columns: parseInt(columns) } );
    }

    onChangeClosedTop = ( closed_top ) => {
        this.props.setAttributes( { closed_top } );
    }

    onChangeIncludeFields = ( include_fields ) => {
        this.props.setAttributes( { include_fields: [ ...include_fields ] } ); 
    }    
    
    onChangeExcludeFields = ( exclude_fields ) => {
        this.props.setAttributes( { exclude_fields: [ ...exclude_fields ] } ); 
    }

    onChangeIncludeFieldTypes = ( include_types ) => {
        this.props.setAttributes( { include_types: [ ...include_types ] } );
    }

    onChangeExcludeFieldTypes = ( exclude_types ) => {
        this.props.setAttributes( { exclude_types: [ ...exclude_types ] } );
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

    previewOnly = ( ) => {
        return (
            <div class="atw-block atw-bg-gray-100 atw-px-4 atw-py-2 atw-rounded-t-lg atw-text-sm atw-text-center">
                <span class="fas fa-warning atw-not-italic atw-text-gray-400 atw-mr-2"></span>
                <span class="atw-text-xs atw-text-gray-600">This is a preview only only, the number of rows on an actual page might be different.</span>
            </div>
        );
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
            columns,
            closed_top,
            include_fields,
            exclude_fields,
            include_types,
            exclude_types
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

                        { layout == "list" && 
                            <ToggleControl
                                label="Closed Top"
                                checked={closed_top}
                                onChange={this.onChangeClosedTop}
                            />   
                        }

                        { layout == "grid" && 
                            <TextControl
                                label="Columns"
                                value={columns}
                                onChange={this.onChangeColumns}
                                type="number"
                                min={ 1 }
                                max={ 10 }
                                step={ 1 }
                            />  
                        }

                    </PanelBody>

                    <PanelBody title="Filters">

                        <AdvertsListData 
                            label="Include Field Types"
                            data={this.getFieldTypes()}
                            onChange={this.onChangeIncludeFieldTypes}
                            value={include_types}
                            mode="multi"
                            placeholder="All"
                        />                        
                        
                        <AdvertsListData 
                            label="Exclude Field Types"
                            data={this.getFieldTypes()}
                            onChange={this.onChangeExcludeFieldTypes}
                            value={exclude_types}
                            mode="multi"
                            placeholder="None"
                        />

                        <AdvertsListData 
                            label="Include Fields"
                            data={this.getAdvertsListData()}
                            onChange={this.onChangeIncludeFields}
                            value={include_fields}
                            mode="multi"
                            placeholder="All"
                        />

                        <AdvertsListData 
                            label="Exclude Fields"
                            data={this.getAdvertsListData()}
                            onChange={this.onChangeExcludeFields}
                            value={exclude_fields}
                            mode="multi"
                            placeholder="None"
                        />

                    </PanelBody>

                    <PanelBody title="Preview">
                        <PreviewHelper />
                        
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


                    {/* layout == "list" && 
                    <div class="atw-grid atw-grid-cols-1 md:atw-grid-cols-1 atw-mt-6 atw-border-t atw-border-solid atw-border-gray-100">
                        {[0,1,2].map(index => (
                        <div class="atw-border-b atw-border-solid atw-border-gray-100 atw-pb-2">
                            <div class="atw-flex atw-pt-3 atw-pb-1 atw-mx-0">
                                <div class="atw-hidden md:atw-flex atw-justify-center atw-items-center atw-bg-gray-200 atw-w-10 atw-h-10 atw-rounded-full atw-mr-3">
                                    <div class=" ">
                                        <i class="fas fa-icon-wordpress atw-text-gray-400 atw-text-lg"></i>
                                    </div>
                                </div>
                                <div class="atw-flex atw-flex-col md:atw-flex-row atw-grow">
                                    <div class="atw-flex atw-flex-none atw-items-center atw-w-1/3 atw-h-10 atw-text-gray-700 atw-text-base atw-mb-1 md:atw-mb-0">
                                        <span class="atw-inline-block atw-font-bold md:atw-font-normal">Label</span>
                                    </div>
                                    <div class="atw-flex atw-grow atw-items-center atw-text-gray-800">
                                        <span class="atw-inline-block">Lorem Ipsum</span>
                                    </div>
                                </div>
                            </div>
                            
                        </div>
                        ) ) }
                    </div>
                    */}
                    { layout == "list" && 
                    <>
                    {this.previewOnly()}
                    <div class="atw-border atw-border-solid atw-border-gray-100 atw-px-3">
                    <div class="atw-grid atw-grid-cols-1 md:atw-grid-cols-1 --atw-mt-6 atw-border-x-0 atw-border-b-0 atw-border-t-0 atw-border-solid atw-border-gray-100">
                        {[...Array(3)].map(index => (
                        <div class="atw-border-x-0 atw-border-t-0 atw-border-b atw-border-solid atw-border-gray-100 atw-pb-2">
                            <div class="atw-flex atw-pt-3 atw-pb-1 atw-mx-0">
                                <div class="atw-hidden md:atw-flex atw-justify-center atw-items-center atw-bg-gray-200 atw-w-10 atw-h-10 atw-rounded-full atw-mr-3">
                                    <div class=" ">
                                        <i class="--fas --fa-icon-wordpress atw-text-gray-400 atw-text-lg"></i>
                                    </div>
                                </div>
                                <div class="atw-flex atw-flex-col md:atw-flex-row atw-grow">
                                    <div class="atw-flex atw-flex-none atw-items-center atw-w-1/3 atw-h-10 atw-text-gray-700 atw-text-base atw-mb-1 md:atw-mb-0">
                                        <span class="atw-inline-block atw-font-bold md:atw-font-normal --- atw-bg-gray-200 atw-w-1/2">&nbsp;</span>
                                    </div>
                                    <div class="atw-flex atw-grow atw-items-center atw-text-gray-800  --- atw-h-10 atw-rounded atw-text-base">
                                        <span class="atw-inline-block --- atw-rounded atw-bg-gray-200 atw-w-3/4 ">&nbsp;</span>
                                    </div>
                                </div>
                            </div>
                            
                        </div>
                        ) ) }
                        
                    </div>
                    </div>
                    
                    </>
                    }

                    { layout == "grid" && 
                    <>
                        {this.previewOnly()}
                        <div class="atw-block atw-border atw-border-solid atw-border-gray-100 atw-px-3">
                            <div class={ ("atw-grid atw-mt-6 atw-grid-cols-1 md:atw-grid-cols-") + columns.toString() }>
                                {[...Array(columns)].map(index => (
                                <div class="atw-border-0 atw-pb-2">
                                    <div class="atw-flex atw-items-center atw-py-3 atw-mx-0">
                                        <div class="atw-flex atw-justify-center atw-items-center atw-flex-none atw-bg-gray-200 atw-w-10 atw-h-10 atw-rounded-full atw-mr-3">
                                            <div class=" ">
                                                <i class="atw-text-gray-300 atw-text-3xl"></i>
                                            </div>
                                        </div>
                                        <div class="atw-flex atw-flex-col atw-grow">
                                            <div class="atw-flex atw-flex-none atw-items-center atw-text-gray-600 atw-text-base">
                                                <span class="atw-inline-block atw-font-normal --- atw-bg-gray-200 atw-w-1/2 atw-rounded-lg">&nbsp;</span>
                                            </div>
                                            <div class="atw-flex atw-grow atw-items-center atw-text-gray-800 --- atw-h-10 atw-rounded-lg atw-text-base">
                                                <span class="atw-inline-block --- atw-rounded atw-bg-gray-200 atw-w-3/4 ">&nbsp;</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                ) ) }
                            </div>   
                        </div>
                    </>
                    }                    
                    
                    { layout == "text" && 

                    <div class="atw-mb-6">
                        {this.previewOnly()}
                        <div class="atw-border atw-border-solid atw-border-gray-100 atw-px-3 atw-pb-3">
                        {[0,1].map(index => (
                        <div class="atw-mt-3">
                            <div>
                                <span class="atw-inline-block atw-text-gray-700 atw-text-xl atw-font-bold atw-py-3 atw-w-full">
                                    <span class="atw-inline-block atw-bg-gray-300 atw-w-1/2 atw-text-xl atw-rounded-lg">&nbsp;</span>
                                </span>
                            </div>
                            <div>
                                {[0,1,2,3,4].map(j => (
                                    <div class="atw-bg-gray-200 atw-w-full atw-rounded atw-text-sm atw-my-1">&nbsp;</div>
                                ) ) }

                                    <div class="atw-bg-gray-200 atw-w-3/4 atw-rounded atw-text-sm atw-my-1">&nbsp;</div>
                            </div>
                        </div>
                        ) ) }
                        </div>
                    </div>


                    }

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