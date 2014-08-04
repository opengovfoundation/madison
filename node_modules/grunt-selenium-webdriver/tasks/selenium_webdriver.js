/*
 * grunt-selenium-webdriver
 * https://github.com/connectid/grunt-selenium-webdriver
 *
 * Copyright (c) 2014 ConnectiD
 * Licensed under the MIT license.
 */
/* 
 * starts and stops selenium in webdriver grid mode as standard
 * but in single hub mode for phantom. This is to ensure compatibility
 * with versions provided on platforms like codeship and circlci
 * requires java runtime installed
 */

"use strict";
var spawn = require('child_process').spawn,
    starting = false, 
    started = false,
    os = require('os'),
    selOptions = [ '-jar' ],
    phantomLoc = __dirname,
    seleniumServerProcess = null,
    phantomProcess = null,
    fs = require('fs' ),
    os = require('os' ),
    JAR_NAME = 'selenium-server-standalone-2.42.2.jar';

function getEnv () {
    return 'Environment: [' + os.type() + ',' +
        os.platform() + ',' +
        os.arch() + ',' +
        os.release() + ']';
}
//console.log ('Current location: [' + __dirname + ']. ' + getEnv() );

// paths vary depending of whether installed as module or locally
// and on different local and CI environments, if you are having problems enable the console and
// let is know the details / submit a pull request for your env
if ( fs.existsSync('jar/' + JAR_NAME) ) {
//    console.log ('branch 1a');
    // mac plugin test - ie a test of this respository
    selOptions.push ( 'jar/' + JAR_NAME );
// this fixes a bug with ubuntu builds https://github.com/levexis/grunt-selenium-webdriver/issues/2
} else if ( fs.existsSync( 'node_modules/grunt-selenium-webdriver/jar/' + JAR_NAME ) ) {
//    console.log ('branch 2a');
    // mac as module
    selOptions.push ( 'node_modules/grunt-selenium-webdriver/jar/' + JAR_NAME );
} else if ( fs.existsSync('../node_modules/grunt-selenium-webdriver/jar/' + JAR_NAME ) ) {
//    console.log ('branch 3a');
    // required for some unix environments?
    selOptions.push ( '../node_modules/grunt-selenium-webdriver/jar/' + JAR_NAME );
} else {
    // if adding new cases please identify environment so that changes can be maintained
    throw new Error('Unable to find path to selenium, please run npm install and find the relative path for your system. Current location: [' + __dirname + ']. '  + getEnv() );
}
// installed as module or locally?
if ( fs.existsSync( phantomLoc + "/../node_modules/phantomjs/bin/phantomjs") ) {
//    console.log ('branch 1b');
    // mac as grunt plugin test
    phantomLoc += "/../node_modules/phantomjs/bin";
} else if (fs.existsSync(phantomLoc + '/../../phantomjs/bin/phantomjs')) {
    // mac, module use
//    console.log ('branch 2b');
    phantomLoc += "/../../phantomjs/bin";
} else {
    // if adding new cases please identify environment so that changes can be maintained
    throw new Error('Unable to find path to phantomjs, please run npm install and find the relative path for your system. Current location: [' + __dirname + ']' +
        'Environment: [' + os.type() + ',' +
        os.platform() + ',' +
        os.arch() + ',' +
        os.release() + ']');
}


/*
 * starts phantom, called after grid has been established
 * @private
 */
function startPhantom ( next, options ) {
    
    phantomProcess = spawn( phantomLoc +'/phantomjs' , [ '--webdriver', '8080', '--webdriver-selenium-grid-hub=http://' + options.host+':' + options.port ]);

    phantomProcess.stderr.setEncoding('utf8');
    phantomProcess.stderr.on('data', function(data) {
        data = data.trim();
    });
    phantomProcess.stdout.setEncoding('utf8');
    // wait for client ready message before proceeding
    phantomProcess.stdout.on('data', function( msg ) {
        // look for msg that indicates it's ready and then stop logging messages
        if ( !started && msg.indexOf( 'Registered with grid' ) > -1) {
            console.log ('registered phantomjs with hub running on ' + options.host + ':' + options.port);
            started = true;
            starting = false;
            if (typeof next === 'function') { 
                return next();
            }
        }
    });
}

/**
 * starts a selenium server with access to default browsers
 * @param next callback function
 * @param isHeadless will start bundled phantomjs single client with selenium in hub mode
 * @param options GruntJS Options object
 * @private
 */
function start( next, isHeadless, options ) {

    if ( started) { 
        return next(console.log('already started')); 
    }
    
    if ( isHeadless ) {    
        selOptions.push ( '-role');
        selOptions.push ( 'hub');
    }

    selOptions.push ( '-host' );
    selOptions.push ( options.host );

    selOptions.push ( '-port' );
    selOptions.push ( options.port );

    selOptions.push ( '-timeout' );
    selOptions.push ( options.timeout );

    selOptions.push ( '-maxSession' );
    selOptions.push ( options.maxSession );


    seleniumServerProcess = spawn( 'java', selOptions );
    // selenium webdriver has a port prober in it which could be factored in.
    seleniumServerProcess.on('uncaughtException', function(err) {
        if(err.errno === 'EADDRINUSE' ){
            console.log ('PORT already IN USE, assume selenium running');
            next(); 
        } else {
            console.trace(err);
            process.exit(1);
        }
    });

    seleniumServerProcess.stderr.setEncoding('utf8');
    // parse procee output until server is actually ready, otherwise next task will break
    seleniumServerProcess.stderr.on('data', function(data) {
        var errMsg;
        data = data.trim();
        if ( isHeadless) {
            // check for grid started, which is outputted to standard error
            if ( data.indexOf( 'Started SocketConnector' ) > -1) {
//                console.log ('selenium hub ready');
                return startPhantom(next, options);
            } else if ( data.indexOf ('Address already in use') > -1 ) {
                // throw error if already started
                 errMsg = 'FATAL ERROR starting selenium: ' + data + ' maybe try killall -9 java';
                throw errMsg;                
            }
        } else if ( data && 
             // throw error if something unexpected happens
             data.indexOf('org.openqa.grid.selenium.GridLauncher main') === -1 &&
             data.indexOf('Setting system property') === -1 &&
             data.indexOf('INFO') === -1 &&
             data.indexOf('WARNING') === -1 &&
             !started
              ) {
            errMsg = 'FATAL ERROR starting selenium: ' + data+ ' is java runtime installed?';
            throw errMsg;
        }
    });
    seleniumServerProcess.stdout.setEncoding('utf8');
    seleniumServerProcess.stdout.on('data', function( msg ) {
        // monitor process output for ready message
        if ( !started && ( msg.indexOf( 'Started org.openqa.jetty.jetty.servlet.ServletHandler' ) > -1 ) ) {
            console.log ('seleniumrc webdriver ready on ' + options.host + ':' + options.port);
            started = true;
            starting = false;
            if (typeof next === 'function') {
                return next();
            }
        }
    });
}

    
/**
 * Stop the servers
 * 
 * @param function optional callback
 * @private
 */
function stop(next) {
    if (phantomProcess) { 
        seleniumServerProcess.on('close', function (code, signal) {
            // this should really resolve both callbacks rather than guessing phantom wrapper will terminate instantly
            if (typeof next === 'function' && !seleniumServerProcess ) {
                next();
            }
        });
        // SIGTERM should ensure processes end cleanly, can do killall -9 java if getting startup errors
        phantomProcess.kill('SIGTERM');
        started = false;
        starting = false;
    }
    if (seleniumServerProcess) { 
        seleniumServerProcess.on('close', function (code, signal) {
            if (typeof next === 'function' ) { 
                // need to stub out the other callback
                next();
            }
        });
        seleniumServerProcess.kill('SIGTERM');        
        started = false;
        starting = false;
    }
}

/*
 * stop the child processes if this process exits
 * @private
 */
process.on('exit', function onProcessExit() {
    if (started) {
        stop();
    }
});

/**
 * Exports 3 tasks
 * selenium_start - will start selenium local server on http://127.0.0.1:4444/wd/hub with all browsers in PATH available
 * selenium_phantom_hub - will start selenium grid hub and attachphantomjs to it
 * stop_selenium - stops whichever server was started
 * @public
 */
module.exports= function ( grunt) {
    grunt.registerTask( 'selenium_start' , 'Starts and stops webdriver in grid or hub mode for use with 3rd party CI platforms' , function () {
        var options = this.options({
          timeout: 30,
          host: '127.0.0.1',
          port: 4444,
          maxSession: 5
        });
        var done = this.async();
        return start ( done , false, options );
    });    
    grunt.registerTask( 'selenium_phantom_hub' , 'Starts selenium in hub mode and attaches a single phantonjs to it for headless env', function() {
        var options = this.options({
          timeout: 30,
          host: '127.0.0.1',
          port: 4444,
          maxSession: 5
        });
        var done = this.async();
        return start ( done , true, options );
    });
    grunt.registerTask( 'selenium_stop', 'Stops webdriver in grid or hub mode for use with 3rd party CI platforms', function() {
        var done = this.async();
        return stop ( done );
    });
};




