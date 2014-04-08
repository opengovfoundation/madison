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
		browserify: {
			js: {
				//Single entry file for frontend app
				src: 'public/js/app.js',
				//Compiles to a single script
				dest: 'public/build/app.js'
			}
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
			scripts: {
				files: ['public/js/*.js'],
				tasks: ['browserify']
			}
			// sass: {
			// 	files: './public/sass/**/*.scss',
			// 	tasks: ['compass']
			// },
			// livereload: {
			// 	files: ['./public/css/*.css', './public/img/*', './public/js/**/*.js'],
			// 	options: {
			// 		livereload: true
			// 	}
			// }
		}
	});

	// Plugin loading
	grunt.loadNpmTasks('grunt-contrib-compass');
	grunt.loadNpmTasks('grunt-contrib-watch');
	grunt.loadNpmTasks('grunt-contrib-jshint');
	grunt.loadNpmTasks('grunt-contrib-concat');
	grunt.loadNpmTasks('grunt-contrib-uglify');
	grunt.loadNpmTasks('grunt-browserify');

	// Task definition
	grunt.registerTask('default', ['browserify', 'watch']);
};