module.exports = function (grunt) {
  grunt.config.set('concat', {
    options: {
      sourceMap: true,
      stripBanners: {
        options: {
          block: true,
          line: true
        }
      }
    },
    dist: {}
  });

  grunt.loadNpmTasks('grunt-contrib-concat');
};