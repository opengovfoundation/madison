//Gruntfile
module.exports = function (grunt) {

	//Initializing the configuration object
	grunt.initConfig({
		pkg: grunt.file.readJSON('package.json'),

		// Task configuration
		compass: {
			dist: {
				options: {
					config: './public/config.rb',
					environment: 'production'
				}
			},
			dev: {
				options: {
					config: './public/config.rb',
				}
			}
		},
		jshint: {
			options: {
				force: true
			}
		},
		concat: {
			components: {
				src: [
					'./public/components/jquery/dist/jquery.min.js',
					'./public/components/bootstrap/dist/js/bootstrap.min.js',
					'./public/components/modernizr/modernizr.js',
					'./public/components/underscore/underscore.js',
					'./public/components/pagedown/Markdown.Converter.js',
					'./public/components/angular/angular.min.js',
					'./public/components/angular-md5/angular-md5.min.js',
					'./public/components/angular-ui/angular-ui.min.js',
					'./public/components/angular-animate/angular-animate.min.js',
					'./public/components/angular-ui-utils/ui-utils.js',
					'./public/components/angular-ui-utils/ui-utils-ieshiv.min.js',
					'./public/components/select2/select2.min.js', // may need css
					'./public/components/angular-ui-select2/src/select2.js',
					'./public/components/moment/min/moment.min.js',
					'./public/components/angular-bootstrap-datetimepicker/src/js/datetimepicker.js', // needs css
					'./public/components/angular-ui-bootstrap/src/collapse/collapse.js',
					'./public/components/angular-ui-bootstrap/src/transition/transition.js',
				],
				dest: './public/components.js'
			},
		},
		uglify: {
			options: {
				mangle: false
			},
			components: {
				files: {
					'./public/components.js': './public/components.js'
				}
			},
			main: {
				files: {
					'./public/main.js': './public/main.js'
				}
			}
		},
		watch: {
			sass: {
				files: './public/sass/**/*.scss',
				tasks: ['compass']
			},
			livereload: {
				files: ['./public/css/*.css', './public/img/*', './public/js/**/*.js'],
				options: {
					livereload: true
				}
			}
		}
	});

	// Plugin loading
	grunt.loadNpmTasks('grunt-contrib-compass');
	grunt.loadNpmTasks('grunt-contrib-watch');
	grunt.loadNpmTasks('grunt-contrib-jshint');
	grunt.loadNpmTasks('grunt-contrib-concat');
	grunt.loadNpmTasks('grunt-contrib-uglify');

	// Task definition
	grunt.registerTask('default', ['jshint', 'compass', 'watch']);
};