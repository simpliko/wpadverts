const path = require( 'path' );
const defaultConfig = require( '@wordpress/scripts/config/webpack.config' );

module.exports = {
    ...defaultConfig,
    
    entry: {
        'block/list': path.resolve( process.cwd(), 'blocks/list/src/index.js' )
    },
    
    output: {
        filename: 'index.js',
        path: path.resolve( process.cwd(), 'blocks/list/build' ),
    },
    
    module: {
        ...defaultConfig.module,
        rules: [
            ...defaultConfig.module.rules,
        ]
    }
};
