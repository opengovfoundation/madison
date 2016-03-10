module.exports = function (grunt) {
  grunt.config.set('clean', {
    build: ['public/build/*.{js,css,map}', 'public/index.html']
  });

  grunt.loadNpmTasks('grunt-contrib-clean');
};