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
        
	}

    onChangeType = ( type ) => {
        this.props.setAttributes( { type } );
    } 

    render() {

        const { 
            className, 
            attributes 
        } = this.props;

        const { 
            type
        } = attributes;

        return (
            <>

                
                <InspectorControls>
                    <PanelBody title="Options">
                        <SelectControl
                            label="Notifications Block Poition"
                            labelPosition="top"
                            value={type}
                            options={ [
                                { label: 'Top', value: 'top' },
                                { label: 'Bottom', value: 'bottom' }
                            ] }
                            onChange={this.onChangeType}
                        />
                    </PanelBody>
                </InspectorControls>

                <BlockControls>
                </BlockControls>

                <Disabled>


                <div class="atw-block atw-bg-gray-100 atw-px-4 atw-py-2 atw-rounded-lg atw-text-sm atw-text-center">
                    <span class="fas fa-warning atw-not-italic atw-text-gray-400 atw-mr-2"></span>
                    <span class="atw-text-xs atw-text-gray-600">Notifications (if any) will show here.</span>
                </div>

                </Disabled>

                

      
            </>
        ) ;
    }
}

export default Edit;