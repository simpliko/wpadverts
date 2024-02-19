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
                    label="Classifieds Manage" 
                    instructions="Select custom post type to continue."
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

    toggleShowResultsCounter = ( show_results_counter ) => {
        this.props.setAttributes( { show_results_counter });
    }

    toggleSwitchViews = ( switch_views ) => {
        this.props.setAttributes( { switch_views });
    }

    toggleAllowSorting = ( allow_sorting ) => {
        this.props.setAttributes( { allow_sorting });
    }

    toggleShowPagination = ( show_pagination ) => {
        this.props.setAttributes( { show_pagination });
    }

    onChangePostsPerPage = ( posts_per_page ) => {
        this.props.setAttributes( { posts_per_page });
    }

    onChangeDisplay = ( display ) => {
        this.props.setAttributes( { display });
    }

    onChangeDefaultImageUrl = ( default_image_url ) => {
        this.props.setAttributes( { default_image_url });
    }

    toggleShowPriceColumn = ( show_price_column ) => {
        this.props.setAttributes( { show_price_column } );
    }

    onListDataChange = ( data ) => {
        console.log(data);
        this.props.setAttributes( { data: [ ...data ] } ); 
    } 

    onChangeTitleSource = ( title_source ) => {
        this.props.setAttributes( { title_source } );
    }

    toggleShowImageColumn = ( show_image_column ) => {
        this.props.setAttributes( { show_image_column } );
    }

    onChangeListImageWidth = ( list_img_width ) => {
        this.props.setAttributes( { list_img_width } );
    }

    onChangeListImageHeight = ( list_img_height ) => {
        this.props.setAttributes( { list_img_height } );
    }

    onChangeListImageFit = ( list_img_fit ) => {
        this.props.setAttributes( { list_img_fit } );
    }

    onChangeListImageSource = ( list_img_source ) => {
        this.props.setAttributes( { list_img_source } );
    }

    onChangeGridColumns = ( grid_columns ) => {
        this.props.setAttributes( { grid_columns } );
    }

    onChangeGridColumnsMobile = ( grid_columns_mobile ) => {
        this.props.setAttributes( { grid_columns_mobile } );
    }

    onChangeGridImgHeight = ( grid_img_height ) => {
        this.props.setAttributes( { grid_img_height } );
    }

    onChangeGridImgFit = ( grid_img_fit ) => {
        this.props.setAttributes( { grid_img_fit } );
    }

    onChangeGridImgSource = ( grid_img_source ) => {
        this.props.setAttributes( { grid_img_source } );
    }
    
    onChangeOrderBy = ( order_by ) => {
        this.props.setAttributes( { order_by } );
    }    

    render() {

        const { 
            className, 
            attributes 
        } = this.props;

        const { 
            show_results_counter, 
            switch_views, 
            allow_sorting,
            show_pagination,
            posts_per_page,
            data,
            display,
            default_image_url,
            order_by,
            order_by_featured,
            list_type,
            list_img_width,
            list_img_height,
            list_img_fit,
            list_img_source,
            grid_columns,
            grid_columns_mobile,
            grid_img_height,
            grid_img_fit,
            grid_img_source,
            show_price_column,
            show_image_column,
            title_source,
            alt_source,
            color_price,
            color_title,
            color_bg_featured
        } = attributes;

        const { show_instructions } = this.state;

        return (
            <>
                { this.state.initiated === true ?

                <>

                    <InspectorControls>

                        <PanelBody title="Display Options" initialOpen={ true }>
         
                            <ToggleControl
                                label="Show number of found results."
                                checked={show_results_counter}
                                onChange={this.toggleShowResultsCounter}
                            />                    

                            <ToggleControl
                                label="Allow switching views."
                                checked={switch_views}
                                onChange={this.toggleSwitchViews}
                            />

                            <ToggleControl
                                label="Allow sorting."
                                checked={allow_sorting}
                                onChange={this.toggleAllowSorting}
                            />                    

                            {/* Show pagination input here */}

                            <TextControl
                                label="Results Per Page."
                                value={posts_per_page}
                                onChange={this.onChangePostsPerPage}
                                type="number"
                                min="1"
                                max="100"
                                step="1"
                            />

                            <SelectControl
                                label="Default View"
                                labelPosition="top"
                                value={display}
                                options={ [
                                    { label: 'List', value: 'list' },
                                    { label: 'Grid', value: 'grid' },
                                    { label: 'Map (requires MAL extension)', value: 'map' }
                                ] }
                                onChange={this.onChangeDisplay}
                            />

                            <TextControl
                                label="Default Image URL"
                                value={default_image_url}
                                onChange={this.onChangeDefaultImageUrl}
                            />

                        </PanelBody>

                        <PanelBody title="Display Information" initialOpen={ false }>

                            <ToggleControl
                                label="Show image column/row."
                                checked={show_image_column}
                                onChange={this.toggleShowImageColumn}
                            />                        

                            {/* Show price column/row */} 

                            <AdvertsListData 
                                data={this.getAdvertsListData()}
                                onChange={this.onListDataChange}
                                value={data}
                                mode="multi"
                            />

                            <AdvertsSelect
                                label="Title Text"
                                labelPosition="top"
                                value={title_source}
                                options={this.getDataOptions()}
                                onChange={this.onChangeTitleSource}
                            />

                            {/* Third column */} 

                        </PanelBody>

                        <PanelBody title="List View Options" initialOpen={ false }>

                            <RangeControl
                                label="Image Width"
                                value={list_img_width}
                                onChange={this.onChangeListImageWidth}
                                min={ 0 }
                                max={ 10 }
                                withInputField={false}
                            />                        
                            
                            <RangeControl
                                label="Image Height"
                                value={list_img_height}
                                onChange={this.onChangeListImageHeight}
                                min={ 0 }
                                max={ 10 }
                                withInputField={false}
                            />

                            <SelectControl 
                                label="Image Fit"
                                labelPosition="top"
                                value={list_img_fit}
                                onChange={this.onChangeListImageFit}
                                options={[
                                    { value: "none", label: "Default" },
                                    { value: "contain", label: "Contain" },
                                    { value: "cover", label: "Cover" },
                                    { value: "scale-down", label: "Scale Down" }
                                ]}
                            />

                            <SelectControl 
                                label="Use Image"
                                labelPosition="top"
                                value={list_img_source}
                                onChange={this.onChangeListImageSource}
                                options={[
                                    { value: "adverts-upload-thumbnail", label: "Adverts - Upload Thumbnail" },
                                    { value: "adverts-list", label: "Adverts - List" },
                                    { value: "adverts-gallery", label: "Adverts - Gallery" },
                                    { value: "small", label: "Small" },
                                    { value: "medium", label: "Medium" },
                                    { value: "large", label: "Large" },
                                    { value: "full", label: "Full Size" }
                                ]}
                            />

                        </PanelBody>    

                        <PanelBody title="Grid View Options" initialOpen={ false }>

                            <TextControl
                                label="Columns in the Grid view."
                                value={grid_columns}
                                onChange={this.onChangeGridColumns}
                                type="number"
                                min="1"
                                max="6"
                                step="1"
                            />                        

                            <TextControl
                                label="Columns in the mobile Grid view."
                                value={grid_columns_mobile}
                                onChange={this.onChangeGridColumnsMobile}
                                type="number"
                                min="1"
                                max="2"
                                step="1"
                            />

                            <RangeControl
                                label="Image Height"
                                value={grid_img_height}
                                onChange={this.onChangeGridImgHeight}
                                min={ 0 }
                                max={ 15 }
                                withInputField={false}
                            />

                            <SelectControl 
                                label="Image Fit"
                                labelPosition="top"
                                value={grid_img_fit}
                                onChange={this.onChangeGridImgFit}
                                options={[
                                    { value: "none", label: "Default" },
                                    { value: "contain", label: "Contain" },
                                    { value: "cover", label: "Cover" },
                                    { value: "scale-down", label: "Scale Down" }
                                ]}
                            />

                            <SelectControl 
                                label="Use Image"
                                labelPosition="top"
                                value={grid_img_source}
                                onChange={this.onChangeGridImgSource}
                                options={[
                                    { value: "adverts-upload-thumbnail", label: "Adverts - Upload Thumbnail" },
                                    { value: "adverts-list", label: "Adverts - List" },
                                    { value: "adverts-gallery", label: "Adverts - Gallery" },
                                    { value: "small", label: "Small" },
                                    { value: "medium", label: "Medium" },
                                    { value: "large", label: "Large" },
                                    { value: "full", label: "Full Size" }
                                ]}
                            />
                        </PanelBody>

                        <PanelBody title="Filters / Basic" initialOpen={ false }>

                            <SelectControl
                                label="Default Sorting and Order"
                                labelPosition="top"
                                value={order_by}
                                options={ [
                                    { label: 'Newest First', value: 'date-desc' },
                                    { label: 'Oldest First', value: 'date-asc' },
                                    { label: 'Most Expensive First', value: 'price-desc' },
                                    { label: 'Cheapest First', value: 'price-asc' },
                                    { label: 'From A to Z', value: 'title-asc' },
                                    { label: 'From Z to A', value: 'title-desc' }
                                ] }
                                onChange={this.onChangeOrderBy}
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
                    <ServerSideRender
                        block="wpadverts/manage"
                        attributes={ this.props.attributes }
                    />
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