import { Component } from '@wordpress/element';
import { __ } from '@wordpress/i18n';

import {
    Panel, PanelBody,
    Flex, FlexItem, FlexBlock,
    Button, ButtonGroup,
    BaseControl,
    TextControl,
    ColorPalette,
    Popover,
    SelectControl,
    RangeControl,
    TabPanel,
    Heading
} from '@wordpress/components';

import AdvertsListData from './adverts-list-data';

import { useInstanceId } from '@wordpress/compose';
import { useState, forwardRef } from '@wordpress/element';
import { CheckboxControl } from '@wordpress/components';

class AdvertsListCheckbox extends Component {


	constructor( props ) {
        
        super(props);
        
        this.data = {
            options: [],
            order: []
        };

        this.data.options = [...this.props.options];

        this.props.checked = [...this.props.checked];
        this.props.order = [...this.props.order];

        this.state = {
            mode: "normal"
        };

        this.compileOrder();
        
    }

    compileOrder() {
        
        for(let i=0; i<this.props.options.length; i++) {
            if(!this.props.order.includes(this.props.options[i].name)) {
                this.props.order.push(this.props.options[i].name);
            }
        }

        //console.log(this.props.options, this.props.order);
    }

    isChecked(name) {
        for(let i=0; i<this.props.checked.length; i++) {
            if(this.props.checked[i] == name) {
                return true;
            }
        }
        return false;
    }

    getObjectByName(name) {
        //console.log(name, this.props.options);
        for(let i=0; i<this.props.options.length; i++) {
            if(this.props.options[i].name === name) {
                //console.log(this.props.options[i]);
                return this.props.options[i];
            }
        }
    }

    shouldComponentUpdate(nextProps) {
        return true;
    }

    onChange = ( name, is_checked ) => {
        let index = this.props.checked.indexOf(name);
        if(is_checked) {
            this.props.checked.push(name);
        } else if(!is_checked && index !== -1) {
            this.props.checked.splice(index, 1);
        }
        
        this.props.onChange( this.props.checked, this.props.order );
    }

    onChangeSingle = ( e ) => {
        if(e.target.value == "-1") {
            this.data.options = [];
            return;
        }

        this.data.options = [ e.target.value ];
        this.props.onChange( this.data.options );
    }

    maybeSelected = ( e ) => {
        if(this.props.mode === "multi") {
            return false;
        }

        if(this.data.options.includes(e)) {
            return "selected";
        } else {
            return false;
        }
    }

    addOption( option ) {
        this.data.options.push( {
            name: option
        });
    }

    onCustomizeQuery = ( e ) => {

    }

    onMove = ( old_index, new_index ) => {
        this.props.order = this.arrayMove( this.props.order, old_index, new_index );
        console.log(this.props.order);
        this.props.onChange( this.props.checked, this.props.order );
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

    render( ) {

        const {
            options,
        } = this.data;

        if(this.props.mode === 'multi') {
            var callback = this.onChange;
            var mode = "multi";
        } else {
            var callback = this.onChangeSingle;
            var mode = "single";
        }

        if(typeof this.props.label === 'undefined') {
            var label = "List Data";
        } else {
            var label = this.props.label;
        }

        if(typeof this.props.placeholder === 'undefined') {
            var placeholder = "";
        } else {
            var placeholder = this.props.placeholder;
        }

        let objects = [];
        for(let i=0; i<this.props.order.length; i++) {
            let object = this.getObjectByName(this.props.order[i]);
            if(object !== undefined) {
                objects.push(object);
            }
        }

        return(
            <>
                <BaseControl
                    label={label}
                    labelPosition="top"
                >

                
                    <>
                        {objects.map((object, i) => {
                            return (
                                
                                <Flex 
                                    key={i} 
                                >

                                    <FlexBlock title={object.label} style={{textOverflow:"ellipsis", overflow:"hidden", whiteSpace:"nowrap", padding:"4px 0 0 4px"}}>
                                        <CheckboxControl 
                                            label={object.label}
                                            value={object.value}
                                            checked={this.isChecked(object.name)}
                                            onChange={e => this.onChange(object.name, !this.isChecked(object.name))}
                                        />
                                    </FlexBlock>
                                    
                                    <FlexItem>

                                        <Button 
                                            label=""
                                            variant="trynitary"
                                            icon="arrow-down-alt2"
                                            value={object.name}
                                            isSmall={true}
                                            onClick={e => this.onMove(i, i+1, object)}
                                            disabled={(i+1>=options.length)}
                                        />

                                        <Button 
                                            label=""
                                            variant="trynitary"
                                            icon="arrow-up-alt2"
                                            isSmall={true}
                                            onClick={e => this.onMove(i, i-1, object)}
                                            disabled={(i<=0)}
                                        />

                                    </FlexItem>
                                </Flex>
                            );
                        })}
                    </>
    
                </BaseControl>
            </>
        );
    }

}

export default AdvertsListCheckbox;