#angular-growl

> growl like notifications for angularJS projects, using bootstrap alert classes

##Features

![Standard bootstrap 2.x styles](doc/screenshot.jpg)

* growl like notifications like in MacOS X
* using standard bootstrap classes (alert, alert-info, alert-error, alert-success)
* global or per message configuration of a timeout when message will be automatically closed
* automatic translation of messages if [angular-translate](https://github.com/PascalPrecht/angular-translate) filter is
present, you only have to provide keys as messages, angular-translate will translate them
* pre-defined $http-Interceptor to automatically handle $http responses for server-sent messages
* automatic CSS animations when adding/closing notifications (only when using >= angularJS 1.2)
* < 1 kB after GZIP

##Changelog

**0.4.0** - 19th Nov 2013

* updated dependency to angularJS 1.2.x, angular-growl does not work with 1.0.x anymore (BREAKING CHANGE)
* new option: only display unique messages, which is the new default, disable to allow same message more than once (BREAKING CHANGE)
* new option: allow html tags in messages, default is off  you need to

**0.3.1** - 1st Oct 2013

* bugfix: translating of messages works again
* change: also set alert css classes introduced by bootstrap 3

**0.3.0** - 26th Sept 2013

* adding css animations support via ngAnimate (for angularJS >= 1.2)
* ability to configure server message keys

**0.2.0** - 22nd Sept 2013

* reworking, bugfixing and documenting handling of server sent messages/notifications
* externalizing css styles of growl class
* provide minified versions of js and css files in build folder

**0.1.3**  - 20th Sept 2013

* introducing ttl config option, fixes #2

##Installation

You can install angular-growl with bower:

> bower install angular-growl

Alternatively you can download the files in the [build folder](build/) manually and include them in your project.

````html
<html>
    <head>
        <link href="bootstrap.min.css" rel="stylesheet">
        <script src="angular.min.js"></script>
        <script src="angular-sanitize.min.js"></script>

        <link href="angular-growl.css" rel="stylesheet">
        <script src="angular-growl.js"></script>
    </head>
</html>
````

As angular-growl is based on its own angularJS module, you have to alter your dependency list when creating your application
module:

````javascript
var app = angular.module('myApp', ['angular-growl']);
````

Finally, you have to include the directive somewhere in your HTML like this:

````html
<body>
    <div growl></div>
</body>
````

##Usage

Just let angular inject the growl Factory into your code and call the 4 functions that the factory provides accordingly:

````javascript
app.controller("demoCtrl", ['$scope', 'growl', function($scope, growl) {
    $scope.addSpecialWarnMessage = function() {
        growl.addWarnMessage("This adds a warn message");
        growl.addInfoMessage("This adds a info message");
        growl.addSuccessMessage("This adds a success message");
        growl.addErrorMessage("This adds a error message");
    }
}]);
````

If [angular-translate](https://github.com/PascalPrecht/angular-translate) is present, its filter is automatically called for translating of messages, so you have to provide
only the key:

````javascript
app.controller("demoCtrl", ['$scope', 'growl', function($scope, growl) {
    $scope.addSpecialWarnMessage = function() {
        growl.addSuccessMessage("SAVE_SUCCESS_MESSAGE");
        growl.addErrorMessage("VALIDATION_ERROR");
    }
}]);
````

##Configuration

###Only unique messages

* Default: true

Accept only unique messages as a new message. If a message is already displayed (text and severity are the same) then this
message will not be added to the displayed message list. Set to false, to always display all messages regardless if they
are already displayed or not:

````javascript
var app = angular.module('myApp', ['angular-growl']);

app.config(['growlProvider', function(growlProvider) {
    growlProvider.onlyUniqueMessages(false);
}]);
````

###Automatic closing of notifications (timeout, ttl)

* Default: none (all messages need to be closed manually by the user.)

However, you can configure a global timeout (TTL) after which notifications should be automatically closed.  To do
this, you have to configure this during config phase of angular bootstrap like this:

````javascript
var app = angular.module('myApp', ['angular-growl']);

app.config(['growlProvider', function(growlProvider) {
    growlProvider.globalTimeToLive(5000);
}]);
````

This sets a global timeout of 5 seconds after which every notification will be closed.

You can override TTL generally for every single message if you want:

````javascript
app.controller("demoCtrl", ['$scope', 'growl', function($scope, growl) {
    $scope.addSpecialWarnMessage = function() {
        growl.addWarnMessage("Override global ttl setting", {ttl: 10000});
    }
}]);
````

This sets a 10 second timeout, after which the notification will be automatically closed.

If you have set a global TTL, you can disable automatic closing of single notifications by setting their ttl to -1:

````javascript
app.controller("demoCtrl", ['$scope', 'growl', function($scope, growl) {
    $scope.addSpecialWarnMessage = function() {
        growl.addWarnMessage("this will not be closed automatically even when a global ttl is set", {ttl: -1});
    }
}]);
````

###Allow HTML in messages

* Default: false

Turn this on to be able to display html tags in messages, default behaviour is to NOT display HTML.

For this to work, you have to declare a dependency to "ngSanitize" (and load the extra javascript) in your own application
module!

````javascript
var app = angular.module('myApp', ['angular-growl', 'ngSanitize']);

app.config(['growlProvider', function(growlProvider) {
    growlProvider.globalEnableHtml(true);
}]);
````

You can override the global option and allow HTML tags in single messages too:

````javascript
app.controller("demoCtrl", ['$scope', 'growl', function($scope, growl) {
    $scope.addSpecialWarnMessage = function() {
        growl.addWarnMessage("<strong>This is a HTML message</strong>", {enableHtml: true});
    }
}]);
````

###Animations

Beginning with angularJS 1.2 growl messages can be automatically animated with CSS animations when adding and/or closing
them. All you have to do is load the angular-animate.js provided by angularJS and add **ngAnimate** to your applications
dependency list:

````html
<html>
    <head>
        <link href="bootstrap.min.css" rel="stylesheet">
        <script src="angular.min.js"></script>
        <script src="angular-animate.min.js"></script>

        <link href="angular-growl.css" rel="stylesheet">
        <script src="angular-growl.js"></script>
    </head>
</html>
````

````javascript
var app = angular.module('myApp', ['angular-growl', 'ngAnimate']);
````

That's it. The angular-growl.css comes with a pre-defined animation of 0.5s to opacity.

To configure the animations, just change the _growl-item.*_ classes in the css file to your preference. F.i. to change length
of animation from 0.5s to 1s do this:

````css
.growl-item.ng-enter,
.growl-item.ng-leave {
    -webkit-transition:1s linear all;
    -moz-transition:1s linear all;
    -o-transition:1s linear all;
    transition:1s linear all;
}
````

Basically you can style your animations just as you like if ngAnimate can pick it up automatically. See the [ngAnimate
docs](http://docs.angularjs.org/api/ngAnimate) for more info.

###Handling of server sent notifications

When doing $http requests, you can configure angular-growl to look automatically for messages in $http responses, so your
business logic on the server is able to send messages/notifications to the client and you can display them automagically:

````javascript
var app = angular.module('myApp', ['angular-growl']);

app.config(['growlProvider', '$httpProvider', function(growlProvider, $httpProvider) {
    $httpProvider.responseInterceptors.push(growlProvider.serverMessagesInterceptor);
}]);
````

This adds a pre-defined angularJS HTTP interceptor that is called on every HTTP request and looks if response contains
messages. Interceptor looks in response for a "messages" array of objects with "text" and "severity" key. This is an example
response which results in 3 growl messages:

````json
{
    "someOtherData": {...},
	"messages": [
		{"text":"this is a server message", "severity": "warn"},
		{"text":"this is another server message", "severity": "info"},
		{"text":"and another", "severity": "error"}
	]
}
````

You can configure the keys, the interceptor is looking for like this:

````javascript
var app = angular.module("demo", ["angular-growl"]);

app.config(["growlProvider", "$httpProvider", function(growlProvider, $httpProvider) {
	growlProvider.messagesKey("my-messages");
	growlProvider.messageTextKey("messagetext");
	growlProvider.messageSeverityKey("severity-level");
	$httpProvider.responseInterceptors.push(growlProvider.serverMessagesInterceptor);
}]);
````

Server messages will be created with default TTL.
