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
    TextareaControl,
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

    toggleModeration = ( moderate ) => {
        this.props.setAttributes( { moderate } );
    }

    toggleSkipPreview = ( skip_preview ) => {
        this.props.setAttributes( { skip_preview } );
    }

    togglePreselectCategory = ( preselect_category ) => {
        this.props.setAttributes( { preselect_category } );
    }

    onRequiresChange = ( requires ) => {
        this.props.setAttributes( { requires } );
    }

    onRequiresErrorChange = ( requires_error ) => {
        this.props.setAttributes( { requires_error } );
    }

    onPartialPrimaryButtonChange = ( primary_button ) => {
        this.props.setAttributes( { primary_button: { ...primary_button } } ); 
    }      
    
    onPartialSecondaryButtonChange = ( secondary_button ) => {
        this.props.setAttributes( { secondary_button: { ...secondary_button } } ); 
    }    
    
    onPartialFormChange = ( form ) => {
        this.props.setAttributes( { form: { ...form } } );
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

        const { post_type, form_scheme } = this.props.attributes;
        const { show_instructions } = this.state;

        return (
            <>
                <Placeholder 
                    icon={ megaphone } 
                    label="Classifieds Publish" 
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

                            { post_type !== "" && <SelectControl
                                label="Search Form Scheme"
                                labelPosition="top"
                                value={ form_scheme }
                                options={this.getAvailableSearchForms(post_type)}
                                onChange={this.onSelectFormScheme}
                                style={{lineHeight:'1rem'}}
                                help="You can create multiple form schemes using Custom Fields extension."
                            /> }

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
            requires,
            requires_error,
            moderate,
            skip_preview,
            preselect_category,
            preselect_category_type,
            primary_button,
            secondary_button,
            form

        } = attributes;

        const { show_instructions } = this.state;

        return (
            <>
                { this.state.initiated === true ?

                <>
                <InspectorControls>
                    <PanelBody title="Options">

                    <ToggleControl
                        label="Moderate Ads"
                        checked={moderate}
                        onChange={this.toggleModeration}
                    />   

                    <ToggleControl
                        label="Skip Preview"
                        checked={skip_preview}
                        onChange={this.toggleSkipPreview}
                    />                       
                    
                    <ToggleControl
                        label="Preselect Category"
                        checked={preselect_category}
                        onChange={this.togglePreselectCategory}
                    />   

                    <div className="wpa-block-editor-common-tip" style={{display:"flex", alignItems:"center", lineHeight:"18px"}}>
                        <Icon icon="warning" />
                        <span style={{paddingLeft: "12px"}}>Category preselection requires <a href="">Custom Fields</a> extension.</span>
                    </div>

                    <TextControl
                        label="Capability required to publish"
                        value={requires}
                        onChange={this.onRequiresChange}
                        help="Capability required to see the publish form. By default anyone can see it."
                    />

                    { requires !== "" && <TextareaControl
                        label="Error Message"
                        help="Cusom message for users who are not allowed to post ads (leave blank otherwise)."
                        value={requires_error}
                        onChange={this.onRequiresErrorChange}
                    /> }

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
                        block="wpadverts/publish"
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