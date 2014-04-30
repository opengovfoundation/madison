# Project Notes

## Code Structure

### Back-end

Madison utilizes Laravel and Composer for managing the back-end code.  Installed packages can be found in composer.json.

## Front-end

We use AngularJS as our front-end framework along with [Annotator](http://annotatorjs.org) as our Annotation library.

The other front-end libraries can be found in bower.json and package.json.  Currently we use NPM and Bower as our front-end package managers.
We use NPM as the primary source and only fall back to Bower if the package cannot be found.

## Build Process

Grunt is used as our build task manager.  We also use Compass, JSHint, and Browserify as tasks in our Gruntfile.
Compass compiles our .scss files, JSHint lints our custom JS files, and Browserify concatenates all our JS assets into one file: public/build/app.js.

There are a few outliers to this process at the moment.  The Pagedown libraries are included directly in app/views/layouts/assets.blade.php as well as any 3rd party css files.
A few libraries are also included directly on the document pages in app/views/doc/reader/index.blade.php