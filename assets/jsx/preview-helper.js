import { Component } from '@wordpress/element';
import { __ } from '@wordpress/i18n';

import {
    BaseControl,
} from '@wordpress/components';

class PreviewHelper extends Component {

    render( ) {
        return(
            <>
                <div class="atw-flex atw-px-2 atw-py-4 atw-mb-4 atw-bg-gray-100 atw-rounded-lg">
                    <span class="fas fa-circle-info atw-mr-2 atw-py-2"></span>
                    <div>
                        <span class="atw-inline-block atw-pb-2 atw-mx-0">
                            In this ection you can enter here data that will be used in the preview, 
                        </span>
                        <span class="atw-inline-block atw-pt-2 atw-mx-0">
                            This will allow you to get a better idea of how the page will look like in the frontend.
                        </span>
                    </div>
                </div>    
            </>
        );
    }
}

export default PreviewHelper;