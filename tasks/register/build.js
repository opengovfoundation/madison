module.exports = function (grunt) {
  grunt.registerTask('build', [
    'clean',
    'jshint',
    'sass_globbing',
    'compass',
    'cssmin',
    'bless:dev',
    // useminPrepare alters the file paths of concat, uglify, and cssmin,
    // so prepare after we've blessed and minified.
    // This will leave the dev, blessed assets in /public/css/ intact for
    // dev builds.
    'useminPrepare',
    'concat',
    'uglify',
    'copy',
    'usemin',
    // Bless a second time for the prod assets in /public/build/
    'bless:prod',
    'cacheBust'
  ]);
};
