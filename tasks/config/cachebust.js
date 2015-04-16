module.exports = function (grunt) {
  grunt.config.set('cacheBust', {
    options: {
      encoding: 'utf8',
      algorithm: 'md5',
      baseDir: 'public/',
      length: 8,
      rename: false
    },
    assets: {
      files: {
        src: ['public/index.html'],
      }
    }
  });

  grunt.loadNpmTasks('grunt-cache-bust');
};
