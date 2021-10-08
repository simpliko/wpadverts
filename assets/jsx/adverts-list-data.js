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

import { useInstanceId } from '@wordpress/compose';
import { useState, forwardRef } from '@wordpress/element';

class AdvertsListData extends Component {


	constructor( props ) {
        
        super(props);
        
        this.data = {
            text: this.props.placeholder,
            options: [],
            ...props.data
        };

        this.data.options = this.props.value;

        this.state = {
            mode: "normal"
        };

    }

    shouldComponentUpdate(nextProps) {
        return true;
    }

    onChange = ( e ) => {
        
        if(e.target.value == "-1") {
            return;
        }
        this.addOption( e.target.value);
        this.props.onChange( this.data.options );
    }

    addOption( option ) {
        this.data.options.push( {
            name: option
        });
    }

    onCustomizeQuery = ( e ) => {

    }

    onMove = ( old_index, new_index ) => {
        this.data.options = this.arrayMove( this.data.options, old_index, new_index );
        this.props.onChange( this.data );
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

    onTrashClick = ( remove_index ) => {
        this.data.options.splice( remove_index, 1 );
        this.props.onChange( this.data );
    }

    getOptionLabel = ( name ) => {

        for(var j=0; j<this.props.data.builtin.data.length; j++) {
            if(this.props.data.builtin.data[j].name === name ) {
                return this.props.data.builtin.data[j].label;
            }
        }


        for(var i=0; i<this.props.data.length; i++) {
            for(var j=0; j<this.props.data[i].data.length; j++) {
                if(this.props.data[i].data[j].name === name ) {
                    return this.props.data[i].data[j].label;
                }
            }
        }



        return name;
    }

    render( ) {

        const {
            options
        } = this.data;

        return(
            <>
                <BaseControl
                    label="List Data"
                    labelPosition="top"
                >
                    <select
                        className="components-select-control__input"
                        onChange={this.onChange}
                    >
                        <option key="-1" value="-1"></option>

                        <optgroup label={this.props.data.builtin.label}>
                            {this.props.data.builtin.data.map((object,i) => {
                                return(
                                    <option key={i} value={object.name}>{object.label}</option> 
                                );
                                
                            })}
                        </optgroup>

                        { this.props.data.meta.data.length > 0 &&         
                            <optgroup label={this.props.data.meta.label}>
                            {this.props.data.meta.data.map((object,i) => {
                                return(
                                    <option key={i} value={object.name}>{object.label}</option> 
                                );
                                
                            })}
                            </optgroup>
                        }

                        { this.props.data.taxonomies.data.length > 0 &&         
                            <optgroup label={this.props.data.taxonomies.label}>
                            {this.props.data.taxonomies.data.map((object,i) => {
                                return(
                                    <option key={i} value={object.name}>{object.label}</option> 
                                );
                                
                            })}
                            </optgroup>
                        }

                    </select>

                { options.length > 0 &&
                    <>
                        {options.map((object, i) => {
                            return (
                                <Flex 
                                    key={i} 
                                >

                                    <FlexBlock title={this.getOptionLabel(object.name)} style={{textOverflow:"ellipsis", overflow:"hidden", whiteSpace:"nowrap"}}>
                                        {this.getOptionLabel(object.name)}
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


                                        <Button 
                                            label=""
                                            variant="trynitary"
                                            icon="edit"
                                            isSmall={true}
                                            onClick={this.toggleInstructions}
                                        />

                                        <Button 
                                            label=""
                                            variant="trynitary"
                                            icon="trash"
                                            isSmall={true}
                                            onClick={e => this.onTrashClick(i)}
                                        />
                                    </FlexItem>
                                </Flex>
                            );
                        })}
                    </>
                }
                </BaseControl>
            </>
        );
    }

}

export default AdvertsListData;