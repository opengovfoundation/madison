module.exports = function (grunt) {
  grunt.config.set('clean', {
    build: ['public/build/*.{js,css}', 'public/index.html']
  });

  grunt.loadNpmTasks('grunt-contrib-clean');
};