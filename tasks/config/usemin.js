module.exports = function (grunt) {
  grunt.config.set('useminPrepare', {
    html: 'public/index.html',
    options: {
      dest: 'public/build'
    }
  });

  grunt.config.set('usemin', {
    html: 'public/build/index.html',
    options: {
      assetDirs: ['public/build']
    }
  });

  grunt.loadNpmTasks('grunt-usemin');
};