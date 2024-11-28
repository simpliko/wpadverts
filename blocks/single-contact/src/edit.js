import { Component, useState } from '@wordpress/element';
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
        this.props.setAttributes( { post_type: post_type, form_scheme: "" } );
    }

    onSelectFormScheme = ( form_scheme ) => {
        this.props.setAttributes( { form_scheme } );
    }

    setContactsStacked = ( contacts_stacked ) => {
        this.props.setAttributes( { contacts_stacked } );
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

    onChangeContactOptions = ( contact, contact_order ) => {
        //contact[1] = contact[1] + "X"
        this.props.setAttributes( { 
            contact: [...contact], 
            contact_order: [ ...contact_order ] 
        } ); 
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

    getAvailableContactForms = ( post_type ) => {
        var pt = this.getCurrentPostType();
        return [{label: "", value: ""}].concat( pt["form_schemes"]["contact"] );
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

    contactMethodChecked( cm ) {
        //console.log(cm);
        //console.log(this.props.attributes.contact);
        return this.props.attributes.contact.includes(cm.name);
    }

    onCustomContactToggle = ( custom_contact ) => {
        this.props.setAttributes( { custom_contact } );
    }

    setFormExpand = ( form_expand ) => {
        this.props.setAttributes( { form_expand } );
    }

    setFormButtonHide = ( form_button_hide ) => {
        this.props.setAttributes( { form_button_hide } );
    }

    setFormTitle = ( form_title ) => {
        this.props.setAttributes( { form_title } );
    }

    setFormStyle = ( form_style ) => {
        this.props.setAttributes( { form_style } );
    }

    setFormScheme = ( form_scheme ) => {
        this.props.setAttributes( { form_scheme } );
    }

    setFormCondensed = ( form_condensed ) => {
        this.props.setAttributes( { form_condensed } );
    }

    setFormPx = ( form_px ) => {
        this.props.setAttributes( { form_px } );
    }

    setFormPy = ( form_py ) => {
        this.props.setAttributes( { form_py } );
    }

    setFormBg = ( form_bg ) => {
        this.props.setAttributes( { form_bg } );
    }

    setRequiresLogin = ( requires_login ) => {
        this.props.setAttributes( { requires_login } );
    }

    setRequiresRegister = ( requires_register ) => {
        this.props.setAttributes( { requires_register } );
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

    getPxMarks() {
        return [
            "atw-px-0",
            "atw-px-0.5",
            "atw-px-1",
            "atw-px-1.5",
            "atw-px-2",
            "atw-px-2.5",
            "atw-px-3",
            "atw-px-3.5",
            "atw-px-4",
            "atw-px-4.5",
            "atw-px-5",
            "atw-px-5.5",
            "atw-px-6",
            "atw-px-6.5",
            "atw-px-7",
            "atw-px-7.5",
            "atw-px-8",
            "atw-px-8.5",
            "atw-px-9",
            "atw-px-9.5",
            "atw-px-10",
            "atw-px-10.5"
        ];
    }

    getPyMarks() {
        return [
            "atw-py-0",
            "atw-py-0.5",
            "atw-py-1",
            "atw-py-1.5",
            "atw-py-2",
            "atw-py-2.5",
            "atw-py-3",
            "atw-py-3.5",
            "atw-py-4",
            "atw-py-4.5",
            "atw-py-5",
            "atw-py-5.5",
            "atw-py-6",
            "atw-py-6.5",
            "atw-py-7",
            "atw-py-7.5",
            "atw-py-8",
            "atw-py-8.5",
            "atw-py-9",
            "atw-py-9.5",
            "atw-py-10",
            "atw-py-10.5"
        ];
    }

    getPxMark(index) {
        return this.getPxMarks()[index];
    }

    getPyMark(index) {
        return this.getPyMarks()[index];
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
            post_type,
            contacts_stacked,
            custom_contact,
            contact,
            contact_order,
            form_expand,
            form_button_hide,
            form_title,
            form_style,
            form_scheme,
            form_condensed,
            form_px,
            form_py,
            form_bg,
            requires,
            requires_error,
            requires_login,
            requires_register
        } = attributes;

        var stl = {};
        var cls = [

        ].join( " " );

        const { show_instructions } = this.state;

        let _flex_dir = "";
        let _flex_dir2 = "";
        if(!contacts_stacked) {
            _flex_dir = "atw-flex-row";
            _flex_dir2 = "atw-mx-1";
        } else {
            _flex_dir = "atw-flex-col";
        }

        let _form_labels = ["", "", ""];
        let _form_values = ["", "", ""];
        if(form_condensed) {
            _form_values = ["Your Name", "Your Email", "Subject", "Message"];
        } else {
            _form_labels = ["Your Name", "Your Email", "Subject", "Message"];
        }

        var opts = [];
        var ignore_empty_list = false;
        const regex = /(<([^>]+)>)/gi;
        if(this.state.initiated === true) {

            if(custom_contact === false) {
                this.getCurrentPostType().contact.map((c) => {
                    if(c.is_active === true) {
                        opts.push(c);
                    }
                });
            } else {
                let contact_list = [];
                for(let i=0; i<contact_order.length; i++) {
                    if(contact.includes(contact_order[i])) {
                        contact_list.push(contact_order[i]);
                    }
                } 

                if(form_button_hide && contact_list.includes("contact-form")) {
                    contact_list.splice(contact_list.indexOf("contact-form"), 1);
                    ignore_empty_list = true;
                }

                contact_list.map((name) => {
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
                            label="Show contacts button stacked"
                            checked={ contacts_stacked }
                            onChange={this.setContactsStacked}
                        />

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
                                    checked={contact}
                                    order={contact_order}
                                /> 
                                
                            </>
                        }

   

                    </PanelBody>

                    { contact.includes("contact-form") &&
                        <PanelBody title="Contact Form">
                            <ToggleControl 
                                label="Expand form by default."
                                checked={ form_expand }
                                onChange={this.setFormExpand}
                            />

                            <ToggleControl 
                                label="Hide 'contact form' button."
                                checked={ form_button_hide }
                                onChange={this.setFormButtonHide}
                                help="When the button is hidden users will not be able to hide the contact form."
                            />

                            <TextControl
                                label="Form Title"
                                value={ form_title }
                                onChange={this.setFormTitle}
                            />

                            <SelectControl
                                label="Form Style"
                                value={ form_style }
                                options={ [
                                    { label: 'Default', value: '' },
                                    { label: 'None', value: 'wpa-unstyled' },
                                    { label: 'Flat', value: 'wpa-flat' },
                                    { label: 'Solid', value: 'wpa-solid' },
                                    { label: 'Border Bottom', value: 'wpa-border-bottom' }
                                ] }
                                onChange={this.setFormStyle}
                            /> 

                            <SelectControl
                                label="Form Scheme"
                                value={ form_scheme }
                                options={this.getAvailableContactForms(post_type)}
                                onChange={this.setFormScheme}
                            /> 

                            <ToggleControl 
                                label="Condense Form."
                                checked={ form_condensed }
                                onChange={this.setFormCondensed}
                                help="Use form labels as input placeholders."
                            />

                            <BaseControl label="Form Margins">
                                <RangeControl
                                    __nextHasNoMarginBottom
                                    beforeIcon={<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 2 24 24" width="24" height="24" aria-hidden="true" focusable="false"><path d="m7.5 6h9v-1.5h-9zm0 13.5h9v-1.5h-9zm-3-3h1.5v-9h-1.5zm13.5-9v9h1.5v-9z" style={{opacity: "0.25"}}></path><path d="m4.5 7.5v9h1.5v-9z"></path><path d="m18 7.5v9h1.5v-9z"></path></svg>}
                                    label=""
                                    value={ form_px }
                                    onChange={ this.setFormPx }
                                    min={ 0 }
                                    max={ this.getPxMarks().length }
                                    withInputField={false}
                                />

                                <RangeControl
                                    __nextHasNoMarginBottom
                                    beforeIcon={<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 2 24 24" width="24" height="24" aria-hidden="true" focusable="false"><path d="m7.5 6h9v-1.5h-9zm0 13.5h9v-1.5h-9zm-3-3h1.5v-9h-1.5zm13.5-9v9h1.5v-9z" style={{opacity: "0.25"}}></path><path d="m7.5 6h9v-1.5h-9z"></path><path d="m7.5 19.5h9v-1.5h-9z"></path></svg>}
                                    label=""
                                    value={ form_py }
                                    onChange={ this.setFormPy }
                                    min={ 0 }
                                    max={ this.getPyMarks().length }
                                    withInputField={false}
                                />
                            </BaseControl>

                            <BaseControl label="Form Background Color">
                                <AdvertsColorPicker
                                    value={form_bg}
                                    onChange={this.setFormBg}
                                />
                            </BaseControl>

                        </PanelBody>

                        
                    }

                    <PanelBody title="Access Control">

                        <TextControl
                            label="Required Capability"
                            value={requires}
                            onChange={this.onRequiresChange}
                            help="Capability required to see contact options. By default anyone can see it."
                        />
        
                        { requires !== "" && 
                            <>
                                <TextareaControl
                                    label="Error Message"
                                    help="Cusom message for users who are not allowed to contact options (leave blank otherwise)."
                                    value={requires_error}
                                    onChange={this.onRequiresErrorChange}
                                /> 

                                <TextControl
                                    label="Login URL"
                                    value={requires_login}
                                    onChange={this.setRequiresLogin}
                                />

                                <TextControl
                                    label="Registration URL"
                                    value={requires_register}
                                    onChange={this.setRequiresRegister}
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


                    <div class="wpa-cpt-contact-details atw-my-6 ">

                        {(opts.length===0 && !contact.includes('contact-form')) &&
                            <div class="atw-block atw-bg-gray-100 atw-px-4 atw-py-2 atw-rounded-lg atw-text-sm atw-text-center">
                                <span class="fas fa-warning atw-not-italic atw-text-gray-400 atw-mr-2"></span>
                                <span class="atw-text-xs atw-text-gray-600">No contact options selected.</span>
                            </div>
                        }
                        <div class="atw-relative atw-flex atw-flex-col md:atw-flex-row">

                            <div className={"atw-relative atw-box-border atw-flex atw-flex-1 atw--mx-1 atw-flex-row " + _flex_dir }>
                                {opts.map((o,index) => (
                                    
                                <div className={"atw-flex-auto atw-box-border atw-mb-3 atw-px-1 atw-relative atw-w-full 11"}>
                                    
                                    <Disabled>
                                    <button name="" value="" type="button" class="wpa-btn-primary wpadverts-show-contact-form  atw-flex hover:atw-bg-none atw-bg-none atw-w-full atw-text-base atw-outline-none atw-border-solid atw-font-semibold atw-px-6 atw-py-2 atw-rounded atw-border atw-leading-loose">
                                        <div class="atw-flex-grow">
                                            <span class="atw-inline md:atw-inline atw-px-0.5"><span class="atw-text-base md:atw-text-base"><span class={o.button.icon}></span></span></span> 
                                            <span class="atw-inline md:atw-inline atw-px-0.5">{o.button.text.replace(regex, "")}</span>
                                        </div>
                                        {/*<span  class="atw-flex-0 atw-pl-6 atw-border-l atw-border-gray-800/20 atw-border-solid"><span class="fas fa-angle-down atw-text-2xl -atw-leading-tight atw-align-middle"></span></span>*/}
                                    </button>
                                    </Disabled>


                                </div>
                                ) ) }
                            </div>
        
                        </div>

                        {(contact.includes('contact-form') && form_expand) &&
                            <div className={this.getPxMark(form_px) + " " + this.getPyMark(form_py)} style={{backgroundColor: form_bg}}>
                                { form_title &&
                                    <div className="atw-py-3 atw-text-lg">{form_title}</div>
                                }

                                <div>
                                    <form>
                                        <div>
                                            <Disabled>
                                                <TextControl label={_form_labels[0]} value={_form_values[0]} onChange={e => alert("")} />
                                            </Disabled>
                                        </div>

                                        <div>
                                            <Disabled>
                                                <TextControl label={_form_labels[1]} value={_form_values[1]} onChange={e => alert("")} />
                                            </Disabled>
                                        </div>
                                        <div>
                                            <Disabled>
                                                <TextControl label={_form_labels[2]} value={_form_values[2]} onChange={e => alert("")} />
                                            </Disabled>
                                        </div>
                                        <div>
                                            <Disabled>
                                                <TextareaControl value="" onChange={e => alert("")} />
                                            </Disabled>
                                        </div>
                                        <div>
                                            <Disabled>
                                            <button name="" value="" type="button" class="wpa-btn-primary atw-flex hover:atw-bg-none atw-bg-none atw-w-full atw-text-base atw-outline-none atw-border-solid atw-font-semibold atw-px-6 atw-py-2 atw-rounded atw-border atw-leading-loose">
                                                <div class="atw-flex-grow">
                                                    <span class="atw-inline md:atw-inline atw-px-0.5">Send Message</span>
                                                </div>
                                            </button>
                                            </Disabled>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        
                        }
                        


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