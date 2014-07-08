module.exports = function(grunt) {
	
	grunt.initConfig({
		pkg: grunt.file.readJSON('package.json'),
		
		sass: {
			dist: {
				files: {
					'ext/css/build/main.css': 'ext/css/main.scss'
				}
			}
		},
		
		autoprefixer: {
			options: {
				browsers: ['> 0.5%', 'last 2 versions', 'Firefox ESR', 'Opera 12.1', 'Android 4', 'BlackBerry 10']
			},
			main: {
				src: 'ext/css/build/main.css',
				dest: 'ext/css/build/main.css'
			},
			fonts: {
				src: 'ext/css/fonts.css',
				dest: 'ext/css/build/fonts.css'
			}
		},
		
		cssmin: {
			combine: {
				files: {
					'ext/css/build/style.min.css': ['ext/css/build/fonts.css', 'ext/css/build/main.css']
				}
			}
		},
		
		jshint: {
			all: ['Gruntfile.js', 'ext/jsc/*.js']
		},
		
		uglify: {
			main: {
				files: {
					'ext/jsc/build/main.min.js': 'ext/jsc/main.js'
				}
			},
			pages: {
				files: [{
					expand: true,
					cwd: 'ext/jsc',
					src: ['*.page.js', '*/*.page.js'],
					dest: 'ext/jsc/build',
					ext: '.page.min.js'
				}]
			}
		},
		
		clean: [
			'ext/css/build/fonts.css',
			'ext/css/build/main.css'
		],
		
		watch: {
			styles: {
				files: ['ext/css/*.css', 'ext/css/*.scss'],
				tasks: ['sass', 'autoprefixer', 'cssmin', 'clean'],
			},
			scripts: {
				files: ['ext/jsc/*.js'],
				tasks: ['jshint', 'uglify']
			}
		}
	});
	
	grunt.loadNpmTasks('grunt-contrib-sass');
	grunt.loadNpmTasks('grunt-autoprefixer');
	grunt.loadNpmTasks('grunt-contrib-cssmin');
	
	grunt.loadNpmTasks('grunt-contrib-jshint');
	grunt.loadNpmTasks('grunt-contrib-uglify');
	
	grunt.loadNpmTasks('grunt-contrib-clean');
	grunt.loadNpmTasks('grunt-contrib-watch');
	
	grunt.registerTask('default', ['sass', 'autoprefixer', 'cssmin', 'jshint', 'uglify', 'clean']);
	
};

