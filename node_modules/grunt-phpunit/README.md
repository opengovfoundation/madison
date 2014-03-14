# grunt-phpunit

> Grunt plugin for running phpunit.

##Getting Started

This plugin requires Grunt `0.4.0`.

If you haven't used [Grunt](http://gruntjs.com/) before, be sure to check out the [Getting Started](http://gruntjs.com/getting-started) guide, as it explains how to create a [Gruntfile](http://gruntjs.com/sample-gruntfile) as well as install and use Grunt plugins.

1. Install this grunt plugin with the following command:

	```shell
	npm install grunt-phpunit --save-dev
	```


2. [Install phpunit](https://github.com/sebastianbergmann/phpunit/#installation) (preferably with [composer](https://github.com/composer/composer))

	```shell
	composer install
	```


3. Once the plugin has been installed, it may be enabled inside your Gruntfile with this line of JavaScript:

	```js
	grunt.loadNpmTasks('grunt-phpunit');
	```


##PHPUnit task

_Run this task with the `grunt phpunit` command._

###Usage Example

```js
phpunit: {
	classes: {
		dir: 'tests/php/'
	},
	options: {
		bin: 'vendor/bin/phpunit',
		bootstrap: 'tests/php/phpunit.php',
		colors: true
	}
}
```

###Target Properties
####dir
Type: `String`

The directory where phpunit should be run, i.e. where the test classes and the bootstrap are located in.

###Options
####bin
Type: `String`  Default: `'phpunit'`

The executable binary.

####logJunit
Type: `String` Default: `false`

Log test execution in JUnit XML format to file.

####logTap
Type: `String` Default: `false`

Log test execution in TAP format to file.

####logJson
Type: `String` Default: `false`

Log test execution in JSON format.

####coverageClover
Type: `String` Default: `false`

Generate code coverage report in Clover XML format.

####coverageHtml
Type: `String` Default: `false`

Generate code coverage report in HTML format.

####coveragePhp
Type: `String` Default: `false`

Serialize PHP_CodeCoverage object to file.

####coverage (or coverageText)
Type: `Boolean` Default: `false`

Generate code coverage report in text format. Default to writing to the standard output. This option can also be set by running the task with `--coverage`.

####testdoxHtml
Type: `String` Default: `false`

Write agile documentation in HTML format to file.

####testdoxText
Type: `String` Default: `false`

Write agile documentation in Text format to file.

####filter
Type: `String` Default: `false`

Filter which tests to run.

####group
Type: `String` Default: `false`

Only runs tests from the specified group(s).

####excludeGroup
Type: `String` Default: `false`

Exclude tests from the specified group(s).

####listGroups
Type: `Boolean` Default: `false`

List available test groups. This option can also be set by running the task with `--list-groups`.

####loader
Type: `String` Default: `false`

TestSuiteLoader implementation to use.

####printer
Type: `String` Default: `false`

TestSuiteListener implementation to use.

####repeat
Type: `String` Default: `false`

Runs the test(s) repeatedly.

####tap
Type: `Boolean` Default: `false`

Report test execution progress in TAP format. This option can also be set by running the task with `--tap`.

####testdox
Type: `Boolean` Default: `false`

Report test execution progress in TestDox format. This option can also be set by running the task with `--testdox`.

####colors
Type: `Boolean` Default: `false`

Use colors in output. This option can also be set by running the task with `--colors`.

####stderr
Type: `Boolean` Default: `false`

Write to STDERR instead of STDOUT. This option can also be set by running the task with `--stderr`.

####stopOnError
Type: `Boolean` Default: `false`

Stop execution upon first error. This option can also be set by running the task with `--stop-on-error`.

####stopOnFailure
Type: `Boolean` Default: `false`

Stop execution upon first error or failure. This option can also be set by running the task with `--stop-on-failure`.

####stopOnSkipped
Type: `Boolean` Default: `false`

Stop execution upon first skipped test. This option can also be set by running the task with `--stop-on-skipped`.

####stopOnIncomplete
Type: `Boolean` Default: `false`

Stop execution upon first incomplete test. This option can also be set by running the task with `--stop-on-incomplete`.

####strict
Type: `Boolean` Default: `false`

Run tests in strict mode. This option can also be set by running the task with `--strict`.

####verbose
Type: `Boolean` Default: `false`

Output more verbose information. This option can also be set by running the task with `--verbose`.

####debug
Type: `Boolean` Default: `false`

Display debbuging information during test execution. This option can also be set by running the task with `--debug`.

####processIsolation
Type: `Boolean` Default: `false`

Run each test in a separate PHP process. This option can also be set by running the task with `--process-isolation`.

####noGlobalsBackup
Type: `Boolean` Default: `false`

Do not backup and restore $GLOBALS for each test. This option can also be set by running the task with `--no-globals-backup`.

####staticBackup
Type: `Boolean` Default: `false`

Backup and restore static attributes for each test. This option can also be set by running the task with `--static-backup`.

####bootstrap
Type: `String` Default: `false`

A "bootstrap" PHP file that is run before the tests.

####configuration
Type: `String` Default: `false`

Read configuration from XML file.

####noConfiguration
Type: `Boolean` Default: `false`

Ignore default configuration file (phpunit.xml). This option can also be set by running the task with `--no-configuration`.

####includePath
Type: `String` Default: `false`

Prepend PHP's include_path with given path(s).

####d
Type: `String` Default: `false`

Sets a php.ini value.

####followOutput
Type: `Boolean` Default: `false`

Prints the output to the console immediately, instead of displaying the whole ouput at the end.
This can be useful -combining with debug: true- when there are many long running tests.