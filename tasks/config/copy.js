module.exports = function (grunt) {
  grunt.config.set('copy', {
    release: {
      files: [
        {
          expand: true,
          cwd: 'public',
          src: [
            'pre-build.html'
          ],
          dest: 'public/',
          rename: function (dest, src) {
            return dest + 'index.html';
          }
        }
      ]
    }
  });

  grunt.loadNpmTasks('grunt-contrib-copy');
};