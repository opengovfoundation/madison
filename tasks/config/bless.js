module.exports = function (grunt) {
  grunt.config.set('bless', {
    // This happens early, for dev builds.
    dev: {
      files: {
        // destination : source
        'public/css/style.css': 'public/css/style.css'
      },
      options: {
        pathType: 'relative',
        force: true
      }
    },
    // This happens after the final assets are compiled.
    prod: {
      files: {
        // destination : source
        'public/build/app.css': 'public/build/app.css'
      },
      options: {
        pathType: 'relative',
        force: true
      }
    }
  });

  grunt.loadNpmTasks('grunt-bless');
};
