# grunt-modernizr

[![Build Status](https://travis-ci.org/Modernizr/grunt-modernizr.png?branch=master,develop)](https://travis-ci.org/Modernizr/grunt-modernizr)

##### *tl;dr:* `grunt-modernizr` sifts through your project files, gathers up your references to Modernizr tests and outputs a lean, mean Modernizr machine.

`grunt-modernizr` is a Modernizr builder for your project. It is based on the Modernizr team's [Modulizr](https://github.com/Modernizr/modernizr.com/blob/gh-pages/i/js/modulizr.js) tool.

This highly configurable task allows you to configure and export a custom Modernizr build. Use Modernizr's [annotated source](http://modernizr.com/downloads/modernizr-latest.js) for development, and let this tool worry about optimization.

When you're ready to build, `grunt-modernizr` will crawl your project for Modernizr test references and save out a minified, uglified, customized version using only the tests you've used in your JavaScript or (S)CSS.

## Getting Started
Install this grunt plugin next to your project's [grunt.js gruntfile][getting_started] with:

```bash
npm install grunt-modernizr --save-dev
```

Then add this line to your project's `grunt.js` gruntfile:

```javascript
grunt.loadNpmTasks("grunt-modernizr");
```

[grunt]: https://github.com/cowboy/grunt
[getting_started]: https://github.com/cowboy/grunt/blob/master/docs/getting_started.md

## Documentation

### Command Line

Run the task with `grunt modernizr`.

#### Cache Busting

Bust the cache of locally downloaded files by running `grunt modernizr:bust`

### Config Options

Add a `modernizr` config object to your Gruntfile.js file. The task supports multiple targets:

```javascript
modernizr: {

	dist: {
		// [REQUIRED] Path to the build you're using for development.
		"devFile" : "lib/modernizr-dev.js",

		// [REQUIRED] Path to save out the built file.
		"outputFile" : "build/modernizr-custom.js",

		// Based on default settings on http://modernizr.com/download/
		"extra" : {
			"shiv" : true,
			"printshiv" : false,
			"load" : true,
			"mq" : false,
			"cssclasses" : true
		},

		// Based on default settings on http://modernizr.com/download/
		"extensibility" : {
			"addtest" : false,
			"prefixed" : false,
			"teststyles" : false,
			"testprops" : false,
			"testallprops" : false,
			"hasevents" : false,
			"prefixes" : false,
			"domprefixes" : false
		},

		// By default, source is uglified before saving
		"uglify" : true,

		// Define any tests you want to implicitly include.
		"tests" : [],

		// By default, this task will crawl your project for references to Modernizr tests.
		// Set to false to disable.
		"parseFiles" : true,

		// When parseFiles = true, this task will crawl all *.js, *.css, *.scss files, except files that are in node_modules/.
		// You can override this by defining a "files" array below.
		// "files" : {
			// "src": []
		// },

		// When parseFiles = true, matchCommunityTests = true will attempt to
		// match user-contributed tests.
		"matchCommunityTests" : false,

		// Have custom Modernizr tests? Add paths to their location here.
		"customTests" : []
	}

}
```

#### Required

###### **`devFile`** (String)
Path to the local build file you're using for development. This parameter is needed so `grunt-modernizr` can skip your dev file when traversing your project to avoid triggering false positives. If you're using a remote file for development, set this option to `remote`.

#### Optional

###### **`outputFile`** (String)
Path to save the customized Modernizr build. It defaults to `lib/modernizr-custom.js`.

###### **`extra`** (Object)
An object of extra configuration options. Check the extra section on [modernizr.com/download](http://modernizr.com/download/) for complete options. Defaults are as they appear on the official site.

###### **`extensibility`** (Object)
An object of extensibility options. Check the section on [modernizr.com/download](http://modernizr.com/download/) for complete options. Defaults are as they appear on the official site.

###### **`uglify`** (Boolean)
By default, the source is uglified before save. Set to false to disable.

###### **`tests`** (Array)
Define any tests you want to implicitly include. Test names are lowercased, separated by underscores (if needed). Check out the full set of test options [here](https://github.com/Modernizr/modernizr.com/blob/gh-pages/i/js/modulizr.js#L15-157).

###### **`parseFiles`** (Boolean)
By default, this task will crawl your project for references to Modernizr tests. Set to false to disable.

###### **`files.src`** (Array)
When `parseFiles` = `true`, this task will crawl all `*.js`, `*.css`, `*.scss` files. You can override this by defining a custom `files.src` array. The object supports all [minimatch](https://github.com/isaacs/minimatch) options.

###### **`matchCommunityTests`** (Boolean)
When `parseFiles` = `true`, setting this boolean to true will attempt to match user-contributed tests. Check out the full set of community tests [here](https://github.com/Modernizr/grunt-modernizr/blob/master/lib/customappr.js#L2-111)

###### **`customTests`** (Array)
Have custom Modernizr tests? Add paths to their location here. The object supports all [minimatch](https://github.com/isaacs/minimatch) options.

###### **`excludeFiles`** (Array)
Files added here will be excluded when looking for Modernizr refs. The object supports all [minimatch](https://github.com/isaacs/minimatch) options.

## License
Copyright (c) 2012 Richard Herrera
Licensed under the MIT license.
