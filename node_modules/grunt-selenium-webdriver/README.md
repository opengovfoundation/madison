# grunt-selenium-webdriver

> Starts and stops slenium in webdriver or hub mode, works with 3rd party CI platforms

We wrote this after extensive problems getting selenium webdriver tests to run in all local and 3rd party environments. If you are using circleci and want to run headless tests then the selenium_phantom_hub task will do the trick.

Note that we have found probems with selenium and bundled chromedriver but at the current time we do not have an option to start chromedriver in the same way as phantomjs. Our advice is use firefox and phantom for CI testing and saucelabs / local for the rest.

## Getting Started
This plugin requires Grunt `~0.4.2`

If you haven't used [Grunt](http://gruntjs.com/) before, be sure to check out the [Getting Started](http://gruntjs.com/getting-started) guide, as it explains how to create a [Gruntfile](http://gruntjs.com/sample-gruntfile) as well as install and use Grunt plugins. Once you're familiar with that process, you may install this plugin with this command:

```shell
npm install grunt-selenium-webdriver --save-dev
```

Once the plugin has been installed, it may be enabled inside your Gruntfile with this line of JavaScript:

```js
grunt.loadNpmTasks('grunt-selenium-webdriver');
```

## The "selenium_webdriver" tasks

Three tasks are added (selenium_start, selenium_stop and selenium_phantom_hub). The tasks do not require or set any options

### Overview

```js
    grunt.loadNpmTasks('grunt-selenium-webdriver');
    grunt.registerTask('e2e', [
        'selenium_phantom_hub',
        'mocha_e2e',
        'selenium_stop'    
    ]);

```

### Usage Examples

This task is designed to be used in conjunction with your test runner of preference. The server is destroyed when grunt exists so grunt start_selenium will only make a server available to other grunt tasks.
If you need a task that persists the selenium server then try grunt-selenium-standalone.

#### selenium_start
Starts a selenium local server on http://127.0.0.1:4444/wd/hub with all browsers in PATH available, including phantom. However, if you get errors with test run using default phantomjs try the task below.
#### selenium_phantom_hub
starts a selenium grid hub and attaches phantomjs to it for headless testing, only one client is available in this configuration
#### selenium_stop
stops whichever server was started, if you need to change from headless to normal mode then you will need to stop first

## Contributing
In lieu of a formal styleguide, take care to maintain the existing coding style. Add unit tests for any new or changed functionality. Lint and test your code using [Grunt](http://gruntjs.com/).

## Release History
0.2.391 inital release using selenium-server-standalone-2.39.0.jar
0.2.392 will have theses documentation updates