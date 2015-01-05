module.exports = function (grunt) {
  grunt.config.set('copy', {
    release: {
      files: [
        {
          expand: true,
          cwd: 'public',
          src: [
            'index.html'
          ],
          dest: 'public/build'
        }
      ]
    }
  });

  grunt.loadNpmTasks('grunt-contrib-copy');
};