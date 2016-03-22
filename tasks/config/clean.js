module.exports = function (grunt) {
  grunt.config.set('clean', {
    build: ['public/build/*.{js,css,map}', 'public/css/*.css', 'public/index.html', '.tmp/*']
  });

  grunt.loadNpmTasks('grunt-contrib-clean');
};
