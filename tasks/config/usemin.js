module.exports = function (grunt) {
  grunt.config.set('useminPrepare', {
    html: 'public/index.html',
    options: {
      dest: 'public/'
    }
  });

  grunt.config.set('usemin', {
    html: 'public/build/index.html',
    css: 'public/build/app.css',
    js: 'public/build/app.*.js',
  });

  grunt.loadNpmTasks('grunt-usemin');
};