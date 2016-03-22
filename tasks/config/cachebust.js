module.exports = function (grunt) {
  grunt.config.set('cacheBust', {
    options: {
      encoding: 'utf8',
      algorithm: 'md5',
      baseDir: 'public/build/',
      length: 8,
      rename: false,
      assets: ['*.js', '*.css']
    },
    assets: {
      src: ['public/index.html', 'public/build/*.css']
    }
  });

  grunt.loadNpmTasks('grunt-cache-bust');
};
