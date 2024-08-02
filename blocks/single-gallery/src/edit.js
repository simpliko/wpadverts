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

        const { 
            post_type,
            gallery_img_height,
            gallery_img_size,
            gallery_fit,
            gallery_bg,
            slider_is_lazy,
            thumb_show,
            thumb_width,
            thumb_height,
            thumb_img_size,
            thumb_fit,
            thumb_bg,
            nav_show,
            nav_position
        } = this.props.attributes;
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

    getOptionsHeight() {
        return [
            { label: '~128px', value: 'atw-h-32' },
            { label: '~144px', value: 'atw-h-36' },
            { label: '~160px', value: 'atw-h-40' },
            { label: '~176px', value: 'atw-h-44' },
            { label: '~192px', value: 'atw-h-48' },
            { label: '~208px', value: 'atw-h-52' },
            { label: '~224px', value: 'atw-h-56' },
            { label: '~240px', value: 'atw-h-60' },
            { label: '~256px', value: 'atw-h-64' },
            { label: '~288px', value: 'atw-h-72' },
            { label: '~320px', value: 'atw-h-80' },
            { label: '~384px', value: 'atw-h-96' }
        ];
    }

    getOptionsThumbHeight() {
        return [
            { label: '~16px', value: 'atw-h-4' },
            { label: '~20px', value: 'atw-h-5' },
            { label: '~24px', value: 'atw-h-6' },
            { label: '~28px', value: 'atw-h-7' },
            { label: '~32px', value: 'atw-h-8' },
            { label: '~36px', value: 'atw-h-9' },
            { label: '~40px', value: 'atw-h-10' },
            { label: '~44px', value: 'atw-h-11' },
            { label: '~48px', value: 'atw-h-12' },
            { label: '~56px', value: 'atw-h-14' },
            { label: '~64px', value: 'atw-h-16' },
            { label: '~80px', value: 'atw-h-20' },
            { label: '~96px', value: 'atw-h-24' },
            { label: '~112px', value: 'atw-h-28' },
            { label: '~128px', value: 'atw-h-32' }
        ];
    }

    getOptionsThumbWidth() {
        return [
            { label: '~16px', value: 'atw-w-4' },
            { label: '~20px', value: 'atw-w-5' },
            { label: '~24px', value: 'atw-w-6' },
            { label: '~28px', value: 'atw-w-7' },
            { label: '~32px', value: 'atw-w-8' },
            { label: '~36px', value: 'atw-w-9' },
            { label: '~40px', value: 'atw-w-10' },
            { label: '~44px', value: 'atw-w-11' },
            { label: '~48px', value: 'atw-w-12' },
            { label: '~56px', value: 'atw-w-14' },
            { label: '~64px', value: 'atw-w-16' },
            { label: '~80px', value: 'atw-w-20' },
            { label: '~96px', value: 'atw-w-24' },
            { label: '~112px', value: 'atw-w-28' },
            { label: '~128px', value: 'atw-w-32' }
        ];
    }

    getOptionsSize() {
        return [
            { value: "adverts-upload-thumbnail", label: "Adverts - Upload Thumbnail" },
            { value: "adverts-list", label: "Adverts - List" },
            { value: "adverts-gallery", label: "Adverts - Gallery" },
            { value: "small", label: "Small" },
            { value: "medium", label: "Medium" },
            { value: "large", label: "Large" },
            { value: "full", label: "Full Size" }
        ];
    }

    getOptionsFit() {
        return [
            { value: "none", label: "Default" },
            { value: "contain", label: "Contain" },
            { value: "cover", label: "Cover" },
            { value: "fill", label: "Fill" },
            { value: "scale-down", label: "Scale Down" }
        ];
    }

    getOptionsNav() {
        return [
            { value: "top-left", label: "Top Left" },
            { value: "top-right", label: "Top Right" },
            { value: "bottom-left", label: "Bottom Left" },
            { value: "bottom-right", label: "Bottom Right" }
        ];
    }

    onChangeGalleryHeight = ( gallery_img_height ) => {
        const options = this.getOptionsHeight();
        this.props.setAttributes( { gallery_img_height: options[gallery_img_height].value } );
    }

    onChangeGalleryImgSize = ( gallery_img_size ) => {
        this.props.setAttributes( { gallery_img_size } );
    }

    onChangeGalleryFit = ( gallery_fit ) => {
        this.props.setAttributes( { gallery_fit } );
    }

    onChangeGalleryBg = ( gallery_bg ) => {
        this.props.setAttributes( { gallery_bg } );
    }
    
    onToggleSliderIsLazy = ( slider_is_lazy ) => {
        this.props.setAttributes( { slider_is_lazy } );
    }

    onToggleNavShow = ( nav_show ) => {
        this.props.setAttributes( { nav_show } );
    }

    onChangeNavPosition = ( nav_position ) => {
        this.props.setAttributes( { nav_position } );
    }

    onToggleThumbShow = ( thumb_show ) => {
        this.props.setAttributes( { thumb_show } );
    }

    onChangeThumbHeight  = ( thumb_height ) => {
        const options = this.getOptionsThumbHeight();
        this.props.setAttributes( { thumb_height: options[thumb_height].value } );
    }

    onChangeThumbWidth  = ( thumb_width ) => {
        const options = this.getOptionsThumbWidth();
        this.props.setAttributes( { thumb_width: options[thumb_width].value } );
    }

    onChangeThumbImgSize  = ( thumb_img_size ) => {
        this.props.setAttributes( { thumb_img_size } );
    }

    onChangeThumbFit = ( thumb_fit ) => {
        this.props.setAttributes( { thumb_fit } );
    }

    onChangeThumbBg = ( thumb_bg ) => {
        this.props.setAttributes( { thumb_bg } );
    }

    getIndex = (value, options) => {
        for(let i=0; i<options.length; i++) {
            if(value === options[i].value) {
                return i;
            }
        }
    }

    printNavPos(nav_position) {
        const positions = {
            "top-left": "atw-top-0 atw-left-0",
            "top-right": "atw-top-0 atw-right-0",
            "bottom-left": "atw-bottom-0 atw-left-0",
            "bottom-right": "atw-bottom-0 atw-right-0"
        };
        return positions[nav_position];
    }

    printBackground(bg) {
        if(bg.length > 0) {
            return {backgroundColor: bg};
        } else {
            return {};
        }
    }

    render() {

        const { 
            className, 
            attributes 
        } = this.props;

        const { 
            gallery_img_height,
            gallery_img_size,
            gallery_fit,
            gallery_bg,
            slider_is_lazy,
            thumb_show,
            thumb_width,
            thumb_height,
            thumb_img_size,
            thumb_fit,
            thumb_bg,
            nav_show,
            nav_position
        } = attributes;

        const { show_instructions } = this.state;

        return (
            <>
                { this.state.initiated === true ?

                <>
                <InspectorControls>

                    <PanelBody title="Gallery">

                        <AdvertsColorPicker
                            label="Gallery Background"
                            labelPosition="top"
                            value={gallery_bg}
                            onChange={this.onChangeGalleryBg}
                        />

                        <RangeControl
                            label="Image Height"
                            value={this.getIndex(gallery_img_height, this.getOptionsHeight())}
                            onChange={this.onChangeGalleryHeight}
                            min={ 0 }
                            max={ this.getOptionsHeight().length-1 }
                            withInputField={false}
                        />  

                        <SelectControl
                            label="Use Image Size"
                            value={ gallery_img_size }
                            options={ this.getOptionsSize() }
                            onChange={this.onChangeGalleryImgSize}
                        />

                        <SelectControl
                            label="Image Fit"
                            value={ gallery_fit }
                            options={ this.getOptionsFit() }
                            onChange={this.onChangeGalleryFit}
                        />

                        <ToggleControl
                            label="Lazy load gallery images"
                            checked={slider_is_lazy}
                            onChange={this.onToggleSliderIsLazy}
                        />

                        <ToggleControl
                            label="Show navigation"
                            checked={nav_show}
                            onChange={this.onToggleNavShow}
                        />

                        {nav_show && 
                            <SelectControl
                                label="Navigation position"
                                value={ nav_position }
                                options={this.getOptionsNav()}
                                onChange={this.onChangeNavPosition}
                            />
                        }

                    </PanelBody>

                    <PanelBody title="Thumbnails">

                        <ToggleControl
                            label="Show thumbnails below gallery"
                            checked={thumb_show}
                            onChange={this.onToggleThumbShow}
                        />

                        { thumb_show &&
                            <>

                                <AdvertsColorPicker
                                    label="Thumbnail Background"
                                    labelPosition="top"
                                    value={thumb_bg}
                                    onChange={this.onChangeThumbBg}
                                />

                                <RangeControl
                                    label="Thumbnail Height"
                                    value={this.getIndex(thumb_height, this.getOptionsThumbHeight())}
                                    onChange={this.onChangeThumbHeight}
                                    min={ 0 }
                                    max={ this.getOptionsThumbHeight().length-1 }
                                    withInputField={false}
                                />  

                                <RangeControl
                                    label="Thumbnail Width"
                                    value={this.getIndex(thumb_width, this.getOptionsThumbWidth())}
                                    onChange={this.onChangeThumbWidth}
                                    min={ 0 }
                                    max={ this.getOptionsThumbWidth().length-1 }
                                    withInputField={false}
                                /> 

                                <SelectControl
                                    label="Use Image Size"
                                    value={ thumb_img_size }
                                    options={ this.getOptionsSize() }
                                    onChange={this.onChangeThumbImgSize}
                                />

                                <SelectControl
                                    label="Thumbnail Fit"
                                    value={ thumb_fit }
                                    options={ this.getOptionsFit() }
                                    onChange={this.onChangeThumbFit}
                                />


                            </>
                        }

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
                    <div className="atw-relative" style={{textAlign:"center"}}>
                        <div className="atw-bg-gray-100 atw-px-4 atw-pt-4">
                            <div className={"atw-relative atw-flex atw-items-center atw-justify-center atw-border atw-border-solid atw-px-4 atw-border-gray-300 " + gallery_img_height } style={this.printBackground(gallery_bg)}>
                                <i className="fa-solid fa-images atw-flex  atw-text-gray-400 atw-text-9xl atw-py-10" xstyle={{fontSize:"128px",paddingTop:"16px",fontStyle:"normal",marginTop:"16px"}}></i>
                                {nav_show && 
                                <div className={this.printNavPos(nav_position) + " atw-absolute atw-text-sm atw-px-3 atw-py-2 atw-mx-3 atw-my-3 atw-border atw-border-solid atw-bg-gray-100 atw-border-gray-300 atw-rounded"}>
                                    <span className="fas fa-camera"></span>
                                    <span className="atw-pl-2">1 / 4</span>
                                </div>
                                }
                            </div>

                            {thumb_show && 
                                <div className="atw-flex atw-flex-row atw-mt-4">
                                {[0,1,2,3].map(index => (
                                    <div className={"atw-flex atw-items-center atw-justify-center atw-border atw-border-solid atw-mr-2 atw-border-gray-300 " + thumb_width + " " + thumb_height }  style={this.printBackground(thumb_bg)}>
                                        <i className="atw-flex fa-solid fa-image atw-text-gray-400 atw-text-3xl"></i>
                                    </div>
                                )) }
                                </div>
                            }
                        </div>
                        <div class="atw-block atw-bg-gray-100 atw-py-2 atw-rounded-b-lg atw-text-sm atw-text-center">
                            <span class="fas fa-warning atw-not-italic atw-text-gray-400 atw-mr-2"></span>
                            <span class="atw-text-xs atw-text-gray-600">This is a preview only, in the frontend you will see a gallery here.</span>
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