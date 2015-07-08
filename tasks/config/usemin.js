module.exports = function (grunt) {
  grunt.config.set('useminPrepare', {
    html: 'public/pre-build.html',
    options: {
      dest: 'public'
    }
  });

  grunt.config.set('usemin', {
    html: 'public/index.html',
    options: {
      assetDirs: ['public']
    }
  });

  grunt.loadNpmTasks('grunt-usemin');
};