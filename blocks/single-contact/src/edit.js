import { Component } from '@wordpress/element';
import { __ } from '@wordpress/i18n';

import { RichText, BlockControls, InspectorControls , PanelColorSettings } from '@wordpress/block-editor';
import { 
    Button,
    Dashicon,
    Draggable,
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
    CheckboxControl,
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
import AdvertsListCheckbox from '../../../assets/jsx/adverts-list-checkbox';
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

        for(var i=0; i<pt.form_schemes[type].length; i++) {

            Object.entries(pt.form_schemes[type][i].data).map(([key, value]) => {
                data.push(value);
            })
            data = this.getUnique(data, "name");
            data.sort((a, b) => a.label > b.label ? 1 : -1)
            //data.push(pt.form_schemes[type][i].data);
        }

        return data;
    }

    getContactOptions = () => {
        let options = this.getCurrentPostType().contact;
        //console.log(options);

        return options;
    }

    onChangeContactOptions = ( options ) => {
        this.props.setAttributes( { contact: [ ...options ] } ); 
    }

    onChangeContactOptionsOrder = ( order ) => {
        this.props.setAttributes( { order: [ ...order ] } );
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

    setChecked = ( checked, name ) => {
        var c = [];//this.props.attributes.contact;

        this.props.attributes.contact.map((method) => {
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
        this.props.setAttributes( { contact: c } );
    }

    contactMethodChecked( cm ) {
        //console.log(cm);
        //console.log(this.props.attributes.contact);
        return this.props.attributes.contact.includes(cm.name);
    }

    onCustomContactToggle = ( custom_contact ) => {
        this.props.setAttributes( { custom_contact } );
    }

    getContactMethodByName( name ) {

        var m = null;
        this.getCurrentPostType().contact.map((c) => {
            if(c.name == name) {
                m = c;
            }
        });

        return m;
    }

    onRequiresChange = ( requires ) => {
        this.props.setAttributes( { requires } );
    }

    onRequiresErrorChange = ( requires_error ) => {
        this.props.setAttributes( { requires_error } );
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

    Move = (direction, item) => {
        var contact = this.props.attributes.contact.slice(0);

        //console.log(direction);
        //console.log(item);
        //console.log(contact);

        var old_index = contact.indexOf(item);
        var new_index = old_index;

        if(direction == "left" && old_index > 0) {
            new_index--;
        }

        if(direction == "right" && old_index < contact.length-1) {
            new_index++;
        }

        contact = this.arrayMove(contact, old_index, new_index);
        //console.log(contact);
        this.props.setAttributes( { contact: contact } );
    }

    arrayMove(arr, old_index, new_index) {
        if (new_index >= arr.length) {
            var k = new_index - arr.length + 1;
            while (k--) {
                arr.push(undefined);
            }
        }
        arr.splice(new_index, 0, arr.splice(old_index, 1)[0]);
        return arr; // for testing
    }

    renderInit() {

        const { post_type } = this.props.attributes;
        const { show_instructions } = this.state;

        return (
            <>
                <Placeholder 
                    icon={ megaphone } 
                    label="Classifieds Single Contact" 
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
            custom_contact,
            contact,
            contact_order,
            requires,
            requires_error
        } = attributes;

        var stl = {};
        var cls = [

        ].join( " " );

        const { show_instructions } = this.state;

        var opts = [];
        const regex = /(<([^>]+)>)/gi;
        if(this.state.initiated === true) {

            if(custom_contact === false) {
                this.getCurrentPostType().contact.map((c) => {
                    if(c.is_active === true) {
                        opts.push(c);
                    }
                });
            } else {
                contact.map((name) => {
                    var data = this.getContactMethodByName(name);
                    if(data) {
                        opts.push(data);
                    }
                    
                });
            }
        }

        return (
            <>
                { this.state.initiated === true ?

                <>
                <InspectorControls>

                    <PanelBody title="Contact Methods">

                        <ToggleControl 
                            label="I want to select available contact options"
                            checked={ custom_contact }
                            onChange={this.onCustomContactToggle}
                        />

                        {custom_contact === true && 
                            <>
                                <AdvertsListCheckbox 
                                    label="Contact Methods"
                                    options={this.getContactOptions()}
                                    onChange={this.onChangeContactOptions}
                                    onChangeOrder={this.onChangeContactOptionsOrder}
                                    checked={contact}
                                    order={contact_order}
                                /> 
                                
                            </>
                        }

   

                    </PanelBody>

                    { wp.hooks.doAction( 'hook_name' ) }

                    <PanelBody title="Access Control">

                        <TextControl
                            label="Required Capability"
                            value={requires}
                            onChange={this.onRequiresChange}
                            help="Capability required to see contact options. By default anyone can see it."
                        />
        
                        { requires !== "" && <TextareaControl
                            label="Error Message"
                            help="Cusom message for users who are not allowed to contact options (leave blank otherwise)."
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


                    <div class="wpa-cpt-contact-details atw-my-6 atw--mx-1">

                        {opts.length===0 &&
                            <div class="atw-block atw-bg-gray-100 atw-px-4 atw-py-2 atw-rounded-lg atw-text-sm atw-text-center">
                                <span class="fas fa-warning atw-not-italic atw-text-gray-400 atw-mr-2"></span>
                                <span class="atw-text-xs atw-text-gray-600">No contact options selected.</span>
                            </div>
                        }
                        <div class="atw-relative atw-flex atw-flex-col md:atw-flex-row --atw--mx-1">

                            <div class="atw-relative atw-flex atw-flex-1 atw-flex-col md:atw-flex-row md:atw-flex-none">
                                {opts.map((o,index) => (
                                <div class="atw-flex-auto atw-mx-1 atw-mb-3 atw-relative">
                                    
                                    <Disabled>
                                    <button name="" value="" type="button" class="wpa-btn-primary wpadverts-show-contact-form  atw-flex hover:atw-bg-none atw-bg-none atw-w-full atw-text-base atw-outline-none atw-border-solid atw-font-semibold atw-px-6 atw-py-2 atw-rounded atw-border atw-leading-loose">
                                        <div class="atw-flex-grow">
                                            <span class="atw-inline md:atw-inline atw-px-0.5"><span class="atw-text-base md:atw-text-base"><span class={o.button.icon}></span></span></span> 
                                            <span class="atw-inline md:atw-inline atw-px-0.5">{o.button.text.replace(regex, "")}</span>
                                        </div>
                                        {/*<span  class="atw-flex-0 atw-pl-6 atw-border-l atw-border-gray-800/20 atw-border-solid"><span class="fas fa-angle-down atw-text-2xl -atw-leading-tight atw-align-middle"></span></span>*/}
                                    </button>
                                    </Disabled>

                                    {custom_contact===true && index>0 &&
                                    <span title="Move Left" onClick={(e) => this.Move("left",o.name)} class="atw-absolute atw-flex atw-items-center atw-cursor-pointer atw-px-2 atw-inset-y-0 atw-top-0 atw-left-0 hover:atw-bg-gray-50">
                                        <span class="atw-not-italic fa-solid fa-chevron-left"></span>
                                    </span>                                    
                                    }

                                    {custom_contact===true && index<opts.length-1 &&
                                    <span title="Move Right" onClick={(e) => this.Move("right",o.name)} class="atw-absolute atw-flex atw-items-center atw-cursor-pointer atw-px-2 atw-inset-y-0 atw-top-0 atw-right-0 hover:atw-bg-gray-50">
                                        <span class="atw-not-italic fa-solid fa-chevron-right"></span>
                                    </span>
                                    }

                                </div>
                                ) ) }
                            </div>
        
                        </div>
                        


                    </div>

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