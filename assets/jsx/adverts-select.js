import { Component } from '@wordpress/element';
import { __ } from '@wordpress/i18n';

import {
    BaseControl,
} from '@wordpress/components';

class AdvertsSelect extends Component {


	constructor( props ) {
        
		super(props);

        this.value = this.props.value;

        this.state = {
            mode: "normal"
        };
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

    onChange = ( e ) => {
        
        this.value = e.target.value;
        this.props.onChange( this.value );
    }

    render( ) {

        const {
            label,
            labelPosition,
        } = this.props;

        return(
            <>
                <BaseControl
                    label={label}
                    labelPosition={labelPosition}
                >
                    <select
                        className="components-select-control__input"
                        onChange={this.onChange}
                        value={this.value}
                    >
                        <option key="-1" value="-1"></option>
                        {this.props.options.map((object,i,value) => {
                            if( object.options.length === 0) {
                                return null;
                            }
                            return(
                                <optgroup key={i} label={object.label}>
                                {object.options.map((option,j) => {
                                    return(
                                        <option key={j} value={option.value}>{option.label}</option> 
                                    );
                                })}  
                                </optgroup>
                            );
                                
                        })}

                    </select>

                </BaseControl>
            </>
        );
    }

}

export default AdvertsSelect;