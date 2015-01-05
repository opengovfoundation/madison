module.exports = function (grunt) {
  grunt.config.set('filerev', {
    options: {
      encoding: 'utf8',
      algorithm: 'md5',
      length: 8
    },
    assets: {
      files: [{
        src: [
          'public/build/*.{js,css}'
        ]
      }]
    }
  });

  grunt.loadNpmTasks('grunt-filerev');
};