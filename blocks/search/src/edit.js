import { Component } from '@wordpress/element';
import { __ } from '@wordpress/i18n';

import { BlockControls, InspectorControls } from '@wordpress/block-editor';
import { 
    BaseControl,
    Button,
    Card,
    CardBody,
    CardHeader,
    Disabled, 
    Panel, 
    PanelRow,
    PanelBody, 
    Toolbar, 
    ToolbarDropdownMenu, 
    SelectControl, 
    Spinner,
    Placeholder,
    Text
} from '@wordpress/components';

import { megaphone } from '@wordpress/icons';

import ServerSideRenderX    from '../../../assets/jsx/server-side-render-x';
import PartialButtons       from '../../../assets/jsx/partial-buttons';
import PartialForm          from '../../../assets/jsx/partial-form';

class Edit extends Component {

	constructor(props) {
        super(props);
        
		this.state = {
            initiated: false,
			post_types: [],
			loading: true
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
            console.log(this.state);
		});
    }

    onFormSchemeChange = ( form_scheme ) => {
        this.props.setAttributes( { form_scheme } );
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

    onButtonsPositionChange = ( buttons_pos ) => {
        console.log(buttons_pos);
        this.props.setAttributes( { buttons_pos } ); 
    }

    onSelectPostType = ( post_type ) => {
        this.props.setAttributes( { post_type, form_scheme: "" } );
    }

    onSelectFormScheme = ( form_scheme ) => {
        this.props.setAttributes( { form_scheme } );
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

    getAvailableSearchForms = ( post_type ) => {
        return [{label: "", value: ""}].concat( this.state.post_types[0]["form_schemes"]["search"] );
    }

    initVisuals = () => {
        const { post_type, form_scheme } = this.props.attributes;

        if( post_type === "" ) {
            return;
        }

        this.setState( { initiated: true } );
    }

    resetVisuals = () => {
        this.props.setAttributes( { post_type: "", form_scheme: "" } );
        this.setState( { initiated: false, loading: true } );
        this.runApiFetchForms();
    }

    render() {

        const { className, attributes } = this.props;

        const { 
            post_type,
            form_scheme,
            form, 
            primary_button,
            secondary_button,
            buttons_pos
        } = attributes;

        //console.log("Render Again...");
        //console.log(this.props.attributes);


        return (
            <>
                { this.state.initiated === true ? 
                    <>
                    <InspectorControls className="wpa-admin">

                        <PanelBody title="Options">

                            <BaseControl label="Custom Post Type" help="" style={{paddingBottom:"12px"}}>
                                
                            <div><code>{post_type}</code></div>

                            </BaseControl>

                            <BaseControl label="Seach Form Scheme" help="" style={{paddingBottom:"12px"}}>
                            
                                <SelectControl
                                    value={ form_scheme }
                                    options={this.getAvailableSearchForms()}
                                    onChange={this.onSelectFormScheme}
                                    
                                />

                                <p style={{fontSize:"12px", color:"rgb(117,117,117)", marginTop:"-12px"}}>
                                    To use custom search form you need the <a href="#">Custom Fields</a> extension.
                                </p>

                            </BaseControl>



                            <div>

                                <SelectControl
                                    label="Search and Filter Buttons Position"
                                    labelPosition="top"
                                    value={ buttons_pos }
                                    options={ [
                                        { label: 'On the right side', value: 'atw-flex-row' },
                                        { label: 'Below the search form', value: 'atw-flex-col' }
                                    ] }
                                    onChange={this.onButtonsPositionChange}
                                />

                            </div>
                        </PanelBody>

                        <PartialForm                        
                            title="Styling / Form"
                            onChange={ this.onPartialFormChange }
                            data={ form }
                            initialOpen={ false }
                        />

                        <PartialButtons 
                            title="Styling / Primary Button"
                            placeholder="Search"
                            onChange={ this.onPartialPrimaryButtonChange }
                            data={ primary_button }
                            initialOpen={ false }
                        />                    
                        
                        <PartialButtons 
                            title="Styling / Secondary Button"
                            placeholder="Filter"
                            onChange={ this.onPartialSecondaryButtonChange }
                            data={ secondary_button }
                            initialOpen={ false }
                        />


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
                        <ServerSideRenderX
                            block="wpadverts/search"
                            attributes={ this.props.attributes }
                            spinnerLocation={{right: 0, top: 10, unit: 'px'}}
                            method="get"
                        />
                    </Disabled>
                </>

                :

                <Placeholder 
                    icon={ megaphone } 
                    label="Classifieds Search Form" 
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

            }
            </>
        ) ;
    }
}

export default Edit;