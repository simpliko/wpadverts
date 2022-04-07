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

    onChangeColorPrice = ( color_price ) => {
        this.props.setAttributes( { color_price } );
    }
    
    onChangeColorTitle = ( color_title ) => {
        this.props.setAttributes( { color_title } );
    }
    
    onChangeColorBgFeatured = ( color_bg_featured ) => {
        this.props.setAttributes( { color_bg_featured } );
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
            taxonomy
        } = attributes;

        return (
            <>
                { this.state.initiated === true ?

                <>

                <InspectorControls>

                    <PanelBody title="Display Options">

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