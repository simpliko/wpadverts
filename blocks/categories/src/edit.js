import { Component } from '@wordpress/element';
import { __ } from '@wordpress/i18n';

import { 
    BlockControls, 
    InspectorControls, 
} from '@wordpress/block-editor';

import { 
    Button,
    Placeholder,
    Spinner,
    Disabled, 
    PanelBody, 
    Toolbar, 
    SelectControl,
    TextControl,
    NumberControl,
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
                initiated: ( this.props.attributes.taxonomy !== "" )
            });
		});
    }

    onSelectTaxonomy = ( taxonomy ) => {
        this.props.setAttributes( { taxonomy } );
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
        if( typeof this.props.attributes.query[param] === 'undefined') {
            return "";
        } else {
            return this.props.attributes.query[param]
        }
    }

    getAvailableTaxonomies = () => {
        var types = [{
            label: "", value: ""
        }];

        this.state.post_types.forEach(function(item, index) {
            item.taxonomies.forEach(function(taxonomy, j) {
                types.push({
                    label: item.label + " - " + taxonomy.label,
                    value: taxonomy.name
                });
            });
        });

        return types;
    }
    getAvailableItemDisplays = () => {
        return [
            { label: "Stacked", value: "wpa-item-stacked" },
            { label: "Aligned", value: "wpa-item-aligned" }
        ];
    }
    initVisuals = () => {
        const { taxonomy } = this.props.attributes;

        if( taxonomy === "" ) {
            return;
        }

        this.setState( { initiated: true } );
    }

    resetVisuals = () => {
        this.props.setAttributes( { taxonomy: "" } );
        this.setState( { initiated: false, loading: true } );
        this.runApiFetchForms();
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

    toggleShowImageColumn = ( show_image_column ) => {
        this.props.setAttributes( { show_image_column } );
    }

    onChangeOrderBy = ( order_by ) => {
        this.props.setAttributes( { order_by } );
    }    
    
    onChangeOrderByFeatured = ( order_by_featured ) => {
        this.props.setAttributes( { order_by_featured } );
    }

    onChangeListType = ( list_type ) => {
        this.props.setAttributes( { list_type } );
    }

    onListDataChange = ( data ) => {
        console.log(data);
        this.props.setAttributes( { data: [ ...data ] } ); 
    } 

    onChangeTitleSource = ( title_source ) => {
        this.props.setAttributes( { title_source } );
    }

    onChangeAltSource = ( alt_source ) => {
        this.props.setAttributes( { alt_source } );
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

    onChangeColorIcon = ( color_icon ) => {
        this.props.setAttributes( { color_icon } );
    }
    
    onChangeColorText = ( color_text ) => {
        this.props.setAttributes( { color_text } );
    }
    
    onChangeColorBg = ( color_bg ) => {
        this.props.setAttributes( { color_bg } );
    }    
    
    onChangeColorBorder = ( color_border ) => {
        this.props.setAttributes( { color_border } );
    }

    toggleShowCount = ( show_count ) => {
        this.props.setAttributes( { show_count });
    }

    toggleShowIcons = ( show_icons ) => {
        this.props.setAttributes( { show_icons });
    }

    onChangeColumns = ( columns ) => {
        this.props.setAttributes( { columns: parseInt(columns) } );
    }

    onChangeColumnsMobile = ( columns_mobile ) => {
        this.props.setAttributes( { columns_mobile: parseInt(columns_mobile) } );
    }

    onChangeDefaultIcon = ( default_icon ) => {
        this.props.setAttributes( { default_icon } );
    }

    onChangeIconSize = ( icon_size ) => {
        this.props.setAttributes( { icon_size } );
    }

    onSelectItemDisplay = ( item_display ) => {
        this.props.setAttributes( { item_display } );
    }

    onChangeMargin = ( margin ) => {
        this.props.setAttributes( { margin } );
    }

    onChangeItemPadding = ( item_padding ) => {
        this.props.setAttributes( { item_padding } );
    }

    toggleTaxonomyDetect = ( taxonomy_detect ) => {
        this.props.setAttributes( { taxonomy_detect } );
    }

    toggleNoCategoriesText = ( no_categories_text ) => {
        this.props.setAttributes( { no_categories_text } );
    }

    renderInit() {

        const { taxonomy} = this.props.attributes;

        return (
            <>
                <Placeholder 
                    icon={ megaphone } 
                    label={ __("Classifieds Categories", "wpadverts" ) }
                    instructions="Select taxonomy to continue."
                    isColumnLayout="true"
                >
                    
                    { this.state.loading === true ? 
                    
                        <Spinner/>

                    :

                        <>
                            <SelectControl
                                label="Taxonomy"
                                labelPosition="top"
                                value={ taxonomy }
                                options={ this.getAvailableTaxonomies() }
                                onChange={this.onSelectTaxonomy}
                                style={{lineHeight:'1rem'}}
                            />

                            <div>
                                <Button 
                                    variant="primary"
                                    disabled={ ( taxonomy === "" ) }
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
            taxonomy,
            item_display,
            item_padding,
            columns,
            columns_mobile,
            default_icon,
            icon_size,
            show_icons,
            show_count,
            color_icon,
            color_text,
            color_bg,
            color_border,
            margin,
            taxonomy_detect,
            no_categories_text
        } = attributes;

        console.log("cats...");
        console.log(columns);

        return (
            <>
                { this.state.initiated === true ?

                <>

                <InspectorControls>

                    <PanelBody title="Display Options">

                        <SelectControl
                            label="Item Display"
                            labelPosition="top"
                            value={ item_display }
                            options={ this.getAvailableItemDisplays() }
                            onChange={this.onSelectItemDisplay}
                            style={{lineHeight:'1rem'}}
                        />

                        <ToggleControl
                            label="Show items count"
                            checked={show_count}
                            onChange={this.toggleShowCount}
                        />     
                        


                        <TextControl
                            label="Columns"
                            value={columns}
                            onChange={this.onChangeColumns}
                            type="number"
                            min={ 1 }
                            max={ 10 }
                            step={ 1 }
                        />  

           

                        <TextControl
                            label="Columns (mobile)"
                            value={columns_mobile}
                            onChange={this.onChangeColumnsMobile}
                            type="number"
                            min={ 1 }
                            max={ 2 }
                            step={ 1 }
                        />

                        <RangeControl
                            label="Margin between items"
                            value={margin}
                            onChange={this.onChangeMargin}
                            min={ 0 }
                            max={ 6 }
                            withInputField={false}
                        />  

                        <RangeControl
                            label="Item padding"
                            value={item_padding}
                            onChange={this.onChangeItemPadding}
                            min={ 0 }
                            max={ 6 }
                            withInputField={false}
                        />  

                    </PanelBody>

                    <PanelBody title="Filters" initialOpen={false}>

                        <ToggleControl
                            label="Autodetect taxonomy"
                            checked={taxonomy_detect}
                            onChange={this.toggleTaxonomyDetect}
                        />  

                        {taxonomy_detect &&
                            <ToggleControl
                                label="Show 'no categories found' text"
                                checked={no_categories_text}
                                onChange={this.toggleNoCategoriesText}
                            />
                        }

                    </PanelBody>

                    <PanelBody title="Icons" initialOpen={false}>

                        <ToggleControl
                            label="Show icons"
                            checked={show_icons}
                            onChange={this.toggleShowIcons}
                        />  

                        <TextControl
                            label="Default icon"
                            value={default_icon}
                            onChange={this.onChangeDefaultIcon}
                        />    

                        <RangeControl
                            label="Icon Size"
                            value={icon_size}
                            onChange={this.onChangeIconSize}
                            min={ 0 }
                            max={ 12 }
                            withInputField={false}
                        />  
                    </PanelBody>

                    <PanelBody title="Colors" initialOpen={false}>

                        <AdvertsColorPicker
                            label="Icon Color"
                            labelPosition="top"
                            value={color_icon}
                            onChange={this.onChangeColorIcon}
                        />

                        <AdvertsColorPicker
                            label="Text Color"
                            labelPosition="top"
                            value={color_text}
                            onChange={this.onChangeColorText}
                        />

                        <AdvertsColorPicker
                            label="Background Color"
                            labelPosition="top"
                            value={color_bg}
                            onChange={this.onChangeColorBg}
                        />

                        <AdvertsColorPicker
                            label="Border Color"
                            labelPosition="top"
                            value={color_border}
                            onChange={this.onChangeColorBorder}
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
                        block="wpadverts/categories"
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