
module.exports = function(grunt) {

    grunt.initConfig({
        pkg: grunt.file.readJSON('package.json'),
        makepot: {
            deploy: {
                options: {
                    cwd: '<%= pkg.svn %>/trunk/',                          // Directory of files to internationalize.
                    domainPath: '/languages/',                   // Where to save the POT file.
                    exclude: [],                      // List of files or directories to ignore.
                    include: [],                      // List of files or directories to include.
                    mainFile: 'wpadverts.php',                     // Main project file.
                    potComments: '',                  // The copyright at the beginning of the POT file.
                    potFilename: 'wpadverts.pot',                  // Name of the POT file.
                    potHeaders: {
                        poedit: true,                 // Includes common Poedit headers.
                        'x-poedit-keywordslist': true // Include a list of all possible gettext functions.
                    },                                // Headers to add to the generated POT file.
                    processPot: null,                 // A callback function for manipulating the POT file.
                    type: 'wp-plugin',                // Type of project (wp-plugin or wp-theme).
                    updateTimestamp: true,            // Whether the POT-Creation-Date should be updated without other changes.
                    updatePoFiles: false              // Whether to update PO files in the same directory as the POT file.
                }
            },
            build: {
                options: {
                    cwd: '<%= pkg.release_path %>',                          // Directory of files to internationalize.
                    domainPath: '/languages/',                   // Where to save the POT file.
                    exclude: [],                      // List of files or directories to ignore.
                    include: [],                      // List of files or directories to include.
                    mainFile: 'wpadverts.php',                     // Main project file.
                    potComments: '',                  // The copyright at the beginning of the POT file.
                    potFilename: 'wpadverts.pot',                  // Name of the POT file.
                    potHeaders: {
                        poedit: true,                 // Includes common Poedit headers.
                        'x-poedit-keywordslist': true // Include a list of all possible gettext functions.
                    },                                // Headers to add to the generated POT file.
                    processPot: null,                 // A callback function for manipulating the POT file.
                    type: 'wp-plugin',                // Type of project (wp-plugin or wp-theme).
                    updateTimestamp: true,            // Whether the POT-Creation-Date should be updated without other changes.
                    updatePoFiles: false              // Whether to update PO files in the same directory as the POT file.
                }
            },
        },
        copy: {
            deploy: {
               files: [
                    {
                        expand: true,
                        cwd:    './',
                        src:    [ 
                            '**', '!node_modules/**', '!Gruntfile.js', '!package.json', '!.gitignore', '!nbproject/**', 
                            '!tests/**', '!bin/**', '!.phpcs.xml.dist', '!.travis.yml', '!phpunit.xml.dist',
                            '!blocks.webpack.js', '!package-lock.json', '!postcss.config.js', '!tailwind.config.js',
                            '!blocks/*/src/*.js', '!blocks/*/build/index.js.map', '!assets/jsx/**', '!assets/css/tailwind.css'
                        ],
                        dest:   "<%= pkg.svn %>/trunk/"
                    }
               ] 
            },
            build: {
                files: [
                    {
                        expand: true,
                        cwd:    './',
                        src:    [ 
                            '**', '!node_modules/**', '!Gruntfile.js', '!package.json', '!.gitignore', '!nbproject/**', 
                            '!tests/**', '!bin/**', '!.phpcs.xml.dist', '!.travis.yml', '!phpunit.xml.dist',
                            '!blocks.webpack.js', '!package-lock.json', '!postcss.config.js', '!tailwind.config.js',
                            '!blocks/*/src/*.js'
                        ],
                        dest:   "<%= pkg.release_path %>"
                    }
               ]  
            }
        },
        checktextdomain: {
            deploy: {
                options:{
                    text_domain: 'wpadverts',
                    report_missing: false,
                    keywords: [ 
                       '__:1,2d',
                       '_e:1,2d',
                       '_x:1,2c,3d',
                       'esc_html__:1,2d',
                       'esc_html_e:1,2d',
                       'esc_html_x:1,2c,3d',
                       'esc_attr__:1,2d', 
                       'esc_attr_e:1,2d', 
                       'esc_attr_x:1,2c,3d', 
                       '_ex:1,2c,3d',
                       '_n:1,2,4d', 
                       '_nx:1,2,4c,5d',
                       '_n_noop:1,2,3d',
                       '_nx_noop:1,2,3c,4d'
                   ]
               },
               files: [{
                   src: ['**/*.php', '!node_modules/**'], //all php  
                   expand: true
               }]
            }
        },
        compress: {
            build: {
                options: {
                    archive: '<%= pkg.release_path %>/../wpadverts.zip',
                    mode: 'zip'
                },
                files: [
                    { 
                        src: './wpadverts/**',
                        cwd: '<%= pkg.release_path %>/../',
                        expand: true
                    }
                ]
            }
        },
        clean: {
            options: {
                force: true
            },
            build: [ '<%= pkg.release_path %>' ]
        }
    });

    grunt.loadNpmTasks('grunt-wp-i18n');
    grunt.loadNpmTasks('grunt-contrib-copy');
    grunt.loadNpmTasks('grunt-checktextdomain');
    grunt.loadNpmTasks('grunt-contrib-compress');
    grunt.loadNpmTasks('grunt-contrib-clean');

    grunt.registerTask('test', ['checktextdomain']);
    grunt.registerTask('deploy', ['checktextdomain', 'copy', 'makepot']);
    grunt.registerTask('build', ['checktextdomain', 'clean:build', 'copy:build', 'makepot:build', 'compress:build']);
};
