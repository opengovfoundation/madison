/** @license ES6/DOM4 polyfill | @version 0.8.8 | MIT License | github.com/termi */

// ==ClosureCompiler==
// @compilation_level ADVANCED_OPTIMIZATIONS
// @warning_level VERBOSE
// @jscomp_warning missingProperties
// @output_file_name a.js
// @check_types
// ==/ClosureCompiler==

/**
 * TODO::
 * 0. eng comments
 * 1. HTMLCanvasElement.toBlob (https://developer.mozilla.org/en/DOM/HTMLCanvasElement | http://stackoverflow.com/questions/4998908/convert-data-uri-to-file-then-append-to-formdata#answer-5100158)
 * 2. dateTime prop for IE < 8
 * 3. offset[Top/Left/Width/Height] for IE from https://raw.github.com/yui/yui3/master/src/dom/js/dom-style-ie.js
 * 4. MutationObserver http://hacks.mozilla.org/2012/05/dom-mutationobserver-reacting-to-dom-changes-without-killing-browser-performance/
 *                     http://updates.html5rocks.com/2012/02/Detect-DOM-changes-with-Mutation-Observers
 * 5. Web Animation API https://dvcs.w3.org/hg/FXTF/raw-file/tip/web-anim/index.html
 * 6. window.innerWidth for IE < 9 https://developer.mozilla.org/en/DOM/window.innerWidth
 * 7. http://dev.w3.org/csswg/selectors4/ querySelector[All] shim
 * 8. http://www.w3.org/TR/DOM-Level-3-Events/#event-type-mouseenter and http://www.w3.org/TR/DOM-Level-3-Events/#event-type-mouseleave for standards-compliant browsers:
 *   i) https://gist.github.com/3153964
 *   ii) http://blog.stchur.com/2007/03/15/mouseenter-and-mouseleave-events-for-firefox-and-other-non-ie-browsers/
 *   iii) https://developer.mozilla.org/en-US/docs/DOM/DOM_event_reference/mouseleave | https://developer.mozilla.org/en-US/docs/DOM/DOM_event_reference/mouseenter
 * 9. https://bugzilla.mozilla.org/show_bug.cgi?id=486002 (Node.compareDocumentPosition returns spurious preceding|following bits for disconnected nodes) :: https://bugzilla.mozilla.org/attachment.cgi?id=671404&action=diff
 */








// [[[|||---=== GCC DEFINES START ===---|||]]]
/*
How to calculate bitmap:

Object.keys(this).filter(function(a) {
  return a.indexOf("__GCC__") == 0 }
).reduce(function(value, name, index){
  var group
    , intValue
  ;

  if( this[name] == true ) {
    group = ~~(index / 31);
    value = value.split("!");
    if( !value[group] ) {
      value[group] = "0";
    }
    intValue = value[group];
    intValue |= Math.pow(2, index - group * 31);
    value[group] = intValue;
    value = value.join("!");
  }

  return value;
}.bind(this), "0");
*/

/** @define {boolean} */
var __GCC__IS_DEBUG__ = false;
//IF __GCC__IS_DEBUG__ == true [
//0. Some errors in console
//1. Fix console From https://github.com/theshock/console-cap/blob/master/console.js
//]

/** @define {boolean} */
var __GCC__UNSTABLE_FUNCTIONS__ = false;
//IF __GCC__UNSTABLE_FUNCTIONS__ == true [
//]

/** @define {boolean} */
var __GCC__INCLUDE_EXTRAS__ = true;
//IF __GCC__INCLUDE_EXTRAS__ == true [
//Exporting these objects to global (window)
  /** 1. browser @define {boolean} */
  var __GCC__INCLUDE_EXTRAS__BROWSER__ = false;
  /** 2. Utils.Dom.DOMStringCollection @define {boolean} */
  var __GCC__INCLUDE_EXTRAS__DOMSTRINGCOLLECTION__ = true;
//Extending objects
  /** 1. Object.append(object, donor, [donor2, ...]) @define {boolean} */
  var __GCC__INCLUDE_EXTRAS__OBJECT_APPEND__ = true;
  /** 2. Object.extend(object, donor, [donor2, ...]) (Object.append with overwrite exists properties) @define {boolean} */
  var __GCC__INCLUDE_EXTRAS__OBJECT_EXTEND__ = true;
  /** 3. Object.inherit(Child, Parent) @define {boolean} */
  var __GCC__INCLUDE_EXTRAS__OBJECT_INHERITS__ = true;
  /** 4. Array.prototype.unique() @define {boolean} */
  var __GCC__INCLUDE_EXTRAS__ARRAY_PROTOTYPE_UNIQUE__ = false;
  /** 5. String.random(length) @define {boolean} */
  var __GCC__INCLUDE_EXTRAS__STRING_RANDOM__ = false;
//]

/** @define {boolean} */
var __GCC__ECMA_SCRIPT5__ = true;
  /** @define {boolean} */
  var __GCC__ECMA_SCRIPT5_GENERIC_ARRAY_METHODS__= true;

/** @define {boolean} */
var __GCC__ECMA_SCRIPT6__ = true;
    /** @define {boolean} */
    var __GCC__ECMA_SCRIPT6_MATH__ = true;

/** @define {boolean} */
var __GCC__SCRIPT_BUGFIXING__ = true;
//IF __GCC__SCRIPT_BUGFIXING__ == true [
  /** 1. Array.prototype.splice: IE < 9 bug: [1,2].splice(0).join("") == "" but should be "12" @define {boolean} */
  var __GCC__SCRIPT_BUGFIXING_ARRAY_PROTOTYPE_SPLICE__ = true;
  /** 2. String.prototype.trim @define {boolean} */
  var __GCC__SCRIPT_BUGFIXING_STRING_PROTOTYPE_TRIM__ = true;
  /** 3. String.prototype.split @define {boolean} */
  var __GCC__SCRIPT_BUGFIXING_STRING_PROTOTYPE_SPLIT__ = true;
  /** 4. Date: parse and toJSON fixes @define {boolean} */
  var __GCC__SCRIPT_BUGFIXING_DATE__ = true;
//]

/** @define {boolean} */
var __GCC__DOM_API_POLYFILL__ = true;
//IF __GCC__DOM_API_POLYFILL__ == true [
  var __GCC__DOM_API_POLYFILL_DOM_EVENTS_LVL3__ = true;
  /**'reversed' for <ol> with DOM API @define {boolean} */
  var __GCC__DOM_API_POLYFILL__REVERSE_POLYFILL__ = true;
    /**Auto init 'reversed' for <ol> in DOMContentLoaded @define {boolean} */
    var __GCC__DOM_API_POLYFILL__REVERSE_POLYFILL__AUTO_INIT__ = true;
  /**
  * HTML*Element.labels
  * HTMLLabelElement.control
  * @define {boolean}
  */
  var __GCC__DOM_API_POLYFILL__LABELS_AND_CONTROL_POLYFILL__ = true;
    /** @define {boolean} */
    var __GCC__DOM_API_POLYFILL_CLASSLIST__ = true;
  /** @define {boolean} */
  var __GCC__DOM_API_POLYFILL_CLASSLIST_FIX__ = true;
  /** @define {boolean} */
  var __GCC__DOM_API_POLYFILL_DOM3_API__ = true;
    /** @define {boolean} */
    var __GCC__DOM_API_POLYFILL_DOM3_API_BUGFIXING__ = true;
  /** @define {boolean} */
  var __GCC__DOM_API_POLYFILL_DOM4_API__ = true;
    /** @define {boolean} */
    var __GCC__DOM_API_POLYFILL_DOM4_API_NODELIST_INHERIT_FROM_ARRAY__ = true;
    /** @define {boolean} */
    var __GCC__DOM_API_POLYFILL_DOM4_API_RADIONODELIST__ = true;
    /** @define {boolean} */
    var __GCC__DOM_API_POLYFILL_DOM4_API_FIND__ = true;

//TODO::
//]

var __GCC__LEGACY_BROWSERS_SUPPORT__ = true;
//IF __GCC__LEGACY_BROWSERS_SUPPORT__ == true [
  /** @define {boolean} */
  var __GCC__LEGACY_BROWSERS_SUPPORT__IELT9__ = true;
  /** @define {boolean} */
  var __GCC__LEGACY_BROWSERS_SUPPORT__OPERA_LT_12_10__ = true;
//]

var __GCC__STRING_LEGACY_DELETE__ = false;
// [[[|||---=== GCC DEFINES END ===---|||]]]







/**
 * @type {Window}
 * @const */
void function() {

"use strict";

var global = this;

/** @const @type {boolean} */
var DEBUG = __GCC__IS_DEBUG__;

var _ = __GCC__LEGACY_BROWSERS_SUPPORT__ && __GCC__LEGACY_BROWSERS_SUPPORT__IELT9__ && global["_"];

var /** @const */
  _Object_prototype = Object.prototype

  , _String_prototype = String.prototype

  , _Array_prototype_ = Array.prototype

    , /** @const */
  _Function_apply_ = __GCC__LEGACY_BROWSERS_SUPPORT__ && __GCC__LEGACY_BROWSERS_SUPPORT__IELT9__ && _ && _["apply"]
        || Function.prototype.apply

    , /** @const */
  _Array_slice_ = _Array_prototype_.slice

    , /** @const */
  _Array_splice_ = _Array_prototype_.splice

    , /** @type {number} */
  _browser_msie

  , _String_contains_

    , /** @const */
  _String_split_ = _String_prototype.split

  , _tmp_ = Function.prototype.bind

    ,
    /** Use native or unsafe but fast 'bind' for service and performance needs
   * Set <native Function#bind> for IE, Opera, FireFox and Safari but for V8 set it to shim function
   *  Due in V8 `Function#bind` is slower except when partially applied. An idea from github.com/bestiejs/lodash
   * @const
   * @param {Object} object
   * @param {...} var_args
   * @return {Function} */
    _fastUnsafe_Function_bind_ =
    ( // Detect V8 js engine
      global["opera"] // Opera
      || global["attachEvent"] // IE & Opera
      || /\n/.test(_tmp_) // FireFox & Safari
    )
    && _tmp_
    || function(object, var_args) {
      var __method = this
        , args
      ;

      if( arguments.length > 1 ) {
        args = _Array_slice_.call(arguments, 1);
        return function () {
          return _Function_apply_.call(__method, object, args.concat(_Array_slice_.call(arguments)));
        };
      }

      return function () {
        return _Function_apply_.call(__method, object, arguments);
      };
    }

    , /** @const */
    _hasOwnProperty = _fastUnsafe_Function_bind_.call(Function.prototype.call, _Object_prototype.hasOwnProperty)

    ,
    /**
   * Call _function
   * @const
   * @param {Function} _function function to call
   * @param {*} context
   * @param {...} var_args
   * @return {*} mixed
   * @version 2
   */
    _call_function = function(_function, context, var_args) {
    // If no callback function or if callback is not a callable function
    // it will throw TypeError
        return _Function_apply_.call(_function, context, _Array_slice_.call(arguments, 2))
  }

    , _append = function(obj, ravArgs) {
    for(var i = 1; i < arguments.length; i++) {
      var extension = arguments[i];
      for(var key in extension)
        if(_hasOwnProperty(extension, key) &&
           (!_hasOwnProperty(obj, key))
          )obj[key] = extension[key];
    }

    return obj;
  }
    ,
  /**
   * @const
   * @param {Object} obj
   * @param {boolean=} _allowNull
   */
    _toObject =
        __GCC__LEGACY_BROWSERS_SUPPORT__ && __GCC__LEGACY_BROWSERS_SUPPORT__IELT9__ && (function(strObj) {
            // Check failure of by-index access of string characters (IE < 9)
            // and failure of `0 in strObj` (Rhino)
            return strObj[0] !== "a" || !(0 in strObj);
        })(Object("a"))
        ?
        function(obj, _allowNull) {
            if( obj == null && !_allowNull ) { // this matches both null and undefined
                throwTypeError("invalid object");
            }

            //Fixed `toObject` to work for strings in IE8 and Rhino
            //https://github.com/kriskowal/es5-shim/pull/94
            // If the implementation doesn't support by-index access of
            // string characters (ex. IE < 9), split the string
      // ( can't using typeof here! )
            if( obj && ( _toString_.call(obj) === "[object String]" ) ) {
                return _String_split_.call(obj, "");
            }

            return Object(obj);
        }
        :
        function(obj, _allowNull) {
            if( obj == null && !_allowNull ) { // this matches both null and undefined
                throwTypeError("invalid object");
            }

            return Object(obj);
        }

    , /** @const */
    _toString_ = _Object_prototype.toString

    , /** @const */
    _throwDOMException = function(errStr) {
    var ex = Object.create(DOMException.prototype);
    ex.code = DOMException[errStr];
    ex.message = errStr +': DOM Exception ' + ex.code;
    throw ex;
  }

    , /** @const @constructor */
  emptyFunction = function() {}

    , /** @type {(Function|undefined)} */
    functionReturnFalse = function() { return false }

    , functionReturnFirstParam = function(param) { return param }

  //Take Element.prototype or silently take a fake object
  // IE < 8 support in a.ielt8.js and a.ielt8.htc
    , _Element_prototype = global["Element"] && global["Element"].prototype || {}

  , _Node_prototype = global["Node"] && global["Node"].prototype || {}

    ,
    S_ELEMENT_CACHED_CLASSLIST_NAME

    , _document_createElement = _fastUnsafe_Function_bind_.call(
    __GCC__LEGACY_BROWSERS_SUPPORT__ && __GCC__LEGACY_BROWSERS_SUPPORT__IELT9__ && document["__orig__createElement__"] ||
      document.createElement,
    document
  )

    , _testElement = _document_createElement('p')

    , dom4_mutationMacro

    , /** @type {number} some unique identifire. must inc after use */
    UUID = 1

    , /** @const @type{string} UUID property name */
    UUID_PROP_NAME = "__UUID__"

    , _tmp_function

  // ------------------------------ ==================  querySelector[All], match, find[All]  ================== ------------------------------
    , /** @type {RegExp} @const */
    RE__selector__easySelector = /^(\w+)?((?:\.(?:[\w\-]+))+)?$|^#([\w\-]+$)/
    , /** @type {RegExp} @const */
    RE__matchSelector__doubleSpaces = /\s*([,>+~ ])\s*/g//Note: Use with "$1"
    , /** @type {RegExp} */
    RE_document_find_scopedReplacer

  // ------------------------------ ==================  Events  ================== ------------------------------

    , _Event

    , _CustomEvent

    , _Event_prototype

    , _Custom_Event_prototype

    , implementation_stopImmediatePropagation

    , _native_preventDefault

  // ------------------------------ ==================  Utils.Dom  ================== ------------------------------
    , DOMStringCollection

    , DOMStringCollection_init

    , DOMStringCollection_getNodeClassName

    , DOMStringCollection_setNodeClassName

  , _classList_toggle = function(token, forse) {
    token += "";
    var thisObj = this
      , result = thisObj.contains(token)
      , method = result ?
        forse !== true && "remove"
        :
        forse !== false && "add"
      ;

    if(method)this[method](token);

    return result;
  }

  // ------------------------------ ==================  es5-shim  ================== ------------------------------
    , _Array_map_

    , _Array_from

    , _Array_forEach_

  , array_some_or_every

    , array_find_or_findIndex

    , _String_trim_

    , _String_trim_whitespace = "\x09\x0A\x0B\x0C\x0D\x20\xA0\u1680\u180E\u2000\u2001\u2002\u2003\u2004\u2005\u2006\u2007\u2008\u2009\u200A\u202F\u205F\u3000\u2028\u2029\uFEFF"

    , _String_trim_left

    , _String_trim_right

    , definePropertyFallback

    , definePropertiesFallback

    , getOwnPropertyDescriptorFallback

  // ------------------------------ ==================  Date  ================== ------------------------------
    , /** @const */
    _Native_Date = global["Date"]

    , _Shimed_Date

    , _Shimed_Date_isoDateExpression

    , _Shimed_Date_monthes

    , _Shimed_Date_dayFromMonth

  // ------------------------------ ==================  __GCC__INCLUDE_EXTRAS__  ================== ------------------------------
    , browser

    , _nodesRecursivelyWalk

    , /** @type {string} Space separator list of labelable element names */
    _labelable_elements

    , OL_reversed_Shim

    , OL_reversed_autoInitFunction

  , throwTypeError = function(msg) {
    //silence
  }

  , _Object_isPlainObject = __GCC__LEGACY_BROWSERS_SUPPORT__ && __GCC__LEGACY_BROWSERS_SUPPORT__IELT9__ && _ && _["isPlainObject"] || function(object) {
    return object && _toString_.call(object) === "[object Object]";// test with Object.prototype.toString
  }
;


//Browser sniffing :) START
if(__GCC__INCLUDE_EXTRAS__ && __GCC__INCLUDE_EXTRAS__BROWSER__) {
  browser = {};
  /** @type {Array}
   * @const */
  browser["names"] = (browser["agent"] = global.navigator.userAgent.toLowerCase()).match(/(mozilla|compatible|chrome|webkit|safari|opera|msie|iphone|ipod|ipad)/gi);

  _tmp_ = browser["names"] && browser["names"].length || 0;
  while(_tmp_-- > 0)browser[browser["names"][_tmp_]] = true;

  browser["mozilla"] = browser["mozilla"] && !browser["compatible"] && !browser["webkit"];
  browser["safari"] = browser["safari"] && !browser["chrome"];
  browser["msie"] = browser["msie"] && !browser["opera"];

  if(__GCC__LEGACY_BROWSERS_SUPPORT__ && __GCC__LEGACY_BROWSERS_SUPPORT__IELT9__) {
    _browser_msie = browser["msie"] || null;
  }

  global["browser"] = browser;//Export
}//if(__GCC__INCLUDE_EXTRAS__)
else if(__GCC__LEGACY_BROWSERS_SUPPORT__ && __GCC__LEGACY_BROWSERS_SUPPORT__IELT9__) {
  _browser_msie = // paranoiac mode
    "attachEvent" in document
    && "all" in document
    && "uniqueId" in document.documentElement
    && +((/msie (\d+)/i.exec(navigator.userAgent) || [])[1] || 0)
    || null
  ;
}
//Browser sniffing :) END




if( !global["HTMLDocument"] ) {
  global["HTMLDocument"] = global["Document"];
}//For IE9
if( __GCC__LEGACY_BROWSERS_SUPPORT__ && __GCC__LEGACY_BROWSERS_SUPPORT__IELT9__ ) {
  if( !global["Document"] ) {//For IE8
    global["Document"] = global["HTMLDocument"];
  }
    if( !global["DocumentFragment"] ) {
    global["DocumentFragment"] = global["HTMLDocument"];
  }
}
//TODO:: for IE < 8 :: if(!global["Document"] && !global["HTMLDocument"])global["Document"] = global["HTMLDocument"] = ??;//for IE < 8

if( !document["head"] ) {
  document["head"] = document.getElementsByTagName("HEAD")[0];
}


if(__GCC__INCLUDE_EXTRAS__) {
/*  =======================================================================================  */
/*  ======================================  Object extras  <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<  */

if(__GCC__INCLUDE_EXTRAS__OBJECT_APPEND__) {
/**
 * Object.append
 * Merge the contents of two or more objects together into the first object.
 * This function does not overwrite existing properties
 * @param {Object} obj Object to extend
 * @param {...} ravArgs extentions
 * @return {Object} the same object as `obj`
 */
Object["append"] = _append;
}

if(__GCC__INCLUDE_EXTRAS__OBJECT_EXTEND__) {
/**
 * Object.extend
 * Merge the contents of two or more objects together into the first object.
 * This function overwrite existing properties
 * @param {Object} obj Object to extend
 * @param {...} ravArgs extentions
 * @return {Object} the same object as `obj`
 */
if(!Object["extend"])Object["extend"] = function(obj, ravArgs) {
  var i = 1
    , l = arguments.length
    , extension
  ;
  for( ; i < l; i++) {
    if(extension = arguments[i]) {
      for(var key in extension) {
        if(_hasOwnProperty(extension, key)) {
          obj[key] = extension[key];
        }
      }
    }
  }

  return obj;
};
}

if(__GCC__INCLUDE_EXTRAS__OBJECT_INHERITS__) {

/**
 * Object.inherits
 * Inherits one Child 'class' (function) from Parent 'class' (function). Note: you need to apply Parent constructor in Child constructor manualy (<class>.superclass.constructor.apply(this, <arguments>))
 * @requires Object.create, Object.getOwnPropertyDescriptors
 * @param {Function} Child
 * @param {Function} Parent
 *
 * Example:
 *  function A() { this.message = "World!"; this.subject = "Hello" };A.prototype.say = function() { alert(this.subject + " " + this.message) }
 *  function B() { B.superclass.call(this); this.message = "Classical inheritance!" }
 *  Object["inherits"](B, A);
 *  test = new B;
 *  test.say();
 */
Object["inherits"] = function(Child, Parent) {
  Child.prototype = Object.create(
    (Child["superclass"] = Parent).prototype
    , Child.prototype && Object["getOwnPropertyDescriptors"](Child.prototype) || void 0
  );
};
}

/**
 * Object.isPlainObject
 * need this method in Function.prototype.bind polyfill
 * Non-standart
 * */
if(!Object["isPlainObject"])Object["isPlainObject"] = _Object_isPlainObject;

/*  >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>  Object extras  ======================================  */
/*  =======================================================================================  */
}//if(__GCC__INCLUDE_EXTRAS__)









//  =======================================================================================
//  =======================================================================================
//  =======================================================================================
//  =======================================================================================
//  =======================================================================================
//  =======================================================================================
// --------------- ================ es5 shim ================ ---------------
//  =======================================================================================
//  =======================================================================================
//  =======================================================================================
//  =======================================================================================
//  =======================================================================================
//  =======================================================================================
// Based on https://github.com/kriskowal/es5-shim/blob/master/es5-shim.js



/*  ======================================================================================  */
/*  ==================================  Function prototype  <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<  */

if(__GCC__ECMA_SCRIPT5__) {
/**
 * Wraps the function in another, locking its execution scope to an object specified by thisObj.
 * http://es5.github.com/#x15.3.4.5
 * http://code.google.com/p/js-examples/source/browse/trunk/bind_emulation.js
 * @param {Object} object
 * @param {...} var_args
 * @return {Function}
 * @version 3
 */
if(!Function.prototype.bind)Function.prototype.bind = function (object, var_args) {
  //If IsCallable(Target) is false, throw a TypeError exception.
  if ( typeof this != "function" ) {
    if( _browser_msie ? !(this && "apply" in this) : _String_trim_.call(this + "").indexOf("function") !== 0 ) {
      throwTypeError("Function.prototype.bind called on incompatible " + this);
    }
  }

  var __method = this
    , args
    , _result
  ;

  if( arguments.length > 1 ) {
    args = _Array_slice_.call(arguments, 1);
    _result = function () {
      return _Function_apply_.call(
        __method,
        (// this created by 'new' operator
          _Object_isPlainObject(this)//We coudn't apply instanceof for DOM-objects in IE<8
          && this instanceof _result
        ) ?
          this//The `object` value is ignored if the bound function is constructed using the new operator.
          :
          object
        ,
        args.concat(_Array_slice_.call(arguments))
      );
    }
  }
  else {
    _result = function () {
      return _Function_apply_.call(
        __method,
        (// this created by 'new' operator
          _Object_isPlainObject(this)//We coudn't apply instanceof for DOM-objects in IE<8
          && this instanceof _result
        ) ?
          this//The `object` value is ignored if the bound function is constructed using the new operator.
          :
          object
        ,
        arguments
      );
    }
  }

  if( __method.prototype ) {
    _result.prototype = Object.create(__method.prototype);
    //TODO:: Function objects created using Function.prototype.bind do not have a prototype property or the [[Code]], [[FormalParameters]], and [[Scope]] internal properties.
    //_result.constructor = __method;
  }
  return _result;
};
}//if __GCC__ECMA_SCRIPT5__
/*  >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>  Function prototype  ==================================  */
/*  =======================================================================================  */


/*  =======================================================================================  */
/*  =================================  Object prototype  <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<  */

if(__GCC__ECMA_SCRIPT5__) {
_append(Object, /** @lends {Object} */{
  /**
   * Object.keys
   * ES5 15.2.3.14
   * http://es5.github.com/#x15.2.3.14
   * https://developer.mozilla.org/en/JavaScript/Reference/global_Objects/Object/keys
   * Returns an array of all own enumerable properties found upon a given object, in the same order as that provided by a for-in loop (the difference being that a for-in loop enumerates properties in the prototype chain as well).
   *
   * Implementation from http://whattheheadsaid.com/2010/10/a-safer-object-keys-compatibility-implementation
   *
   * @param obj The object whose enumerable own properties are to be returned.
   * @return {Array} object keys
   */
  keys: (function() {
      var DontEnums
            , DontEnumsLength
            , hasDontEnumBug = !{"toString":null}.propertyIsEnumerable('toString')
        ;

        if( hasDontEnumBug ) {
            DontEnums = [
                'toString',
                'toLocaleString',
                'valueOf',
                'hasOwnProperty',
                'isPrototypeOf',
                'propertyIsEnumerable',
                'constructor'
            ];
            DontEnumsLength = DontEnums.length;
        }

      return function (obj) {
          if( typeof obj != "object"
                  && typeof obj != "function"
                || obj === null
            ) {
                throwTypeError("Object.keys called on a non-object");
            }

          var result = [];
          for( var name in obj ) {
              if( _hasOwnProperty(obj, name) ) {
                    result.push(name);
                }
          }

          if( hasDontEnumBug ) {
              for( var i = 0; i < DontEnumsLength; i++ ) {
                  if( _hasOwnProperty(obj, DontEnums[i]) ) {
                        result.push(DontEnums[i]);
                    }
              }
          }

          return result;
      };
  })()

    ,
  /**
   * Object.getOwnPropertyNames
   * ES5 15.2.3.4
   * http://es5.github.com/#x15.2.3.4
   * Returns an array of all properties (enumerable or not) found upon a given object.
   * @param obj The object whose enumerable own properties are to be returned.
   * @return {Array} object keys
   */
    getOwnPropertyNames : function(obj) {
    return Object.keys(obj);
  }

    ,
  /**
   * Object.seal
   * ES5 15.2.3.8
   * http://es5.github.com/#x15.2.3.8
   * this is misleading and breaks feature-detection, but
   * allows "securable" code to "gracefully" degrade to working
   * but insecure code.
   * @param {!Object} object
   * @return {Object} the same object
   */
    seal : functionReturnFirstParam

    ,
  /**
   * Object.freeze
   * ES5 15.2.3.9
   * http://es5.github.com/#x15.2.3.9
   * this is misleading and breaks feature-detection, but
   * allows "securable" code to "gracefully" degrade to working
   * but insecure code.
   * @param {!Object} object
   * @return {Object} the same object
   */
    freeze : functionReturnFirstParam

    ,
  /**
   * Object.preventExtensions
   * ES5 15.2.3.10
   * http://es5.github.com/#x15.2.3.10
   * this is misleading and breaks feature-detection, but
   * allows "securable" code to "gracefully" degrade to working
   * but insecure code.
   * @param {!Object} object
   * @return {Object} the same object
   */
    preventExtensions : functionReturnFirstParam

    ,
  /**
   * Object.isSealed
   * ES5 15.2.3.11
   * http://es5.github.com/#x15.2.3.11
   * @param {!Object} object
   * @param {boolean} is sealed?
   */
    isSealed : functionReturnFalse

    ,
  /**
   * Object.isFrozen
   * ES5 15.2.3.12
   * http://es5.github.com/#x15.2.3.12
   * @param {!Object} object
   * @param {boolean} is frozen?
   */
    isFrozen : functionReturnFalse

    ,
  /**
   * Object.isExtensible
   * ES5 15.2.3.13
   * http://es5.github.com/#x15.2.3.13
   * @param {!Object} object
   * @param {boolean} is extensible?
   */
    isExtensible : function(object) {
        // 1. If Type(O) is not Object throw a TypeError exception.
        if( Object(object) !== object ) {
      throwTypeError("invalid object");
        }

        // 2. Return the Boolean value of the [[Extensible]] internal property of O.
        var name = ''
            , returnValue
        ;

        while( _hasOwnProperty(object, name) ) {
            name += '?';
        }
        object[name] = true;

        returnValue = _hasOwnProperty(object, name);

        delete object[name];

        return returnValue;
    }

    ,
    /**
   * Object.getPrototypeOf
   * WARNING!!!: This implementation works not as native
   *  For examples:
   *  Object.getPrototypeOf(({}).constructor.prototype) == null
   *  ({}).constructor.prototype.constructor.prototype == ({}).constructor.prototype
   * ES5 15.2.3.2
   * http://es5.github.com/#x15.2.3.2
   * https://github.com/kriskowal/es5-shim/issues#issue/2
   * http://ejohn.org/blog/objectgetprototypeof/
   * recommended by fschaefer on github
   * @param {!Object} object
   * @return {Object} prototype of given object
   */
    getPrototypeOf : function(object) {
    return object.__proto__ || (
      object.constructor ?
      object.constructor.prototype :
      _Object_prototype
    );
  }

    ,
  /**
   * Object.create
   * https://developer.mozilla.org/en/JavaScript/Reference/global_Objects/Object/create
   * JavaScript 1.8.5
   * ES5 15.2.3.5
   * http://es5.github.com/#x15.2.3.5
   * Creates a new object with the specified prototype object and properties.
   * @param {Object} _prototype The object which should be the prototype of the newly-created object.
   * @param {Object=} properties If specified and not undefined, an object whose enumerable own properties (that is, those properties defined upon itself and not enumerable properties along its prototype chain) specify property descriptors to be added to the newly-created object, with the corresponding property names.
   * @return {!Object}
   */
    "create": function(_prototype, properties) {
    var _object;
    if( _prototype === null ) {
      _object = { "__proto__": null };
    }
    else {
      if( typeof _prototype != "object" ) {
        throwTypeError("typeof prototype[" + (typeof _prototype) + "] != 'object'");
      }

      emptyFunction.prototype = _prototype;
      _object = new emptyFunction();
      // Clean up dangling references.
      emptyFunction.prototype = null;
      // IE has no built-in implementation of `Object.getPrototypeOf`
      // neither `__proto__`, but this manually setting `__proto__` will
      // guarantee that `Object.getPrototypeOf` will work as expected with
      // objects created using `Object.create`
      _object.__proto__ = _prototype;
    }

    if( properties ) {
      Object.defineProperties(_object, properties);
    }

    return _object;
  }

  /**
   * Object.isObject
   * need this method in Function.prototype.bind polyfill
   * Non-standart
   * */
  , "isObject": function(object) {
    var type;
    return object
      && (
        (type = typeof object) === "object"
        || type === "function"
      )
    ;
  }
});
}//if __GCC__ECMA_SCRIPT5__


if(__GCC__ECMA_SCRIPT5__ || __GCC__ECMA_SCRIPT6__) {
// ES5 15.2.3.3
// http://es5.github.com/#x15.2.3.3
// FF bug:
//  https://bugzilla.mozilla.org/show_bug.cgi?id=520882
// check whether getOwnPropertyDescriptor works if it's given. Otherwise,
// shim partially.
if( Object.getOwnPropertyDescriptor ) {
    // doesGetOwnPropertyDescriptorWork
    _tmp_ = function(object) {
        try {
            object["a"] = 0;
            return Object.getOwnPropertyDescriptor(
                object,
                "a"
            ).value === 0;
        }
        catch (exception) {
            return false;
        }
    };
    if( !_tmp_({})// does getOwnPropertyDescriptor work on objects
        || !_tmp_(_testElement)// does getOwnPropertyDescriptor work on DOM elements
        || (
            !document.__proto__
            || !function() {
                //FireFox failed this test
                try {
                    Object.getOwnPropertyDescriptor(
                        document.__proto__,
                        "firstChild"
                    );
                    return true;
                } catch (exception) {
                    return false;
                }
            }()
        )
    ) {
        getOwnPropertyDescriptorFallback = Object.getOwnPropertyDescriptor;
    }
}
}//if(__GCC__ECMA_SCRIPT5__ || __GCC__ECMA_SCRIPT6__)


if(__GCC__ECMA_SCRIPT5__) {

// ES5 15.2.3.6
// http://es5.github.com/#x15.2.3.6

// Patch for WebKit and IE8 standard mode
// Designed by hax <hax.github.com>
// related issue: https://github.com/kriskowal/es5-shim/issues#issue/5
// IE8 Reference:
//     http://msdn.microsoft.com/en-us/library/dd282900.aspx
//     http://msdn.microsoft.com/en-us/library/dd229916.aspx
// WebKit Bugs:
//     https://bugs.webkit.org/show_bug.cgi?id=36423

// check whether defineProperty works if it's given. Otherwise,
// shim partially.
if( Object.defineProperty ) {
    // doesDefinePropertyWork
  _tmp_ = function(object) {
      try {
          Object.defineProperty(object, "sentinel", {});
          return "sentinel" in object;
      } catch (exception) {
      return false;
      }
  };

    if( !_tmp_({})// does defineProperty work on objects
        || !_tmp_(_testElement)// does defineProperty work on DOM elements
    ) {
        definePropertyFallback = Object.defineProperty;
    definePropertiesFallback = Object.defineProperties;
    }
}

if( !Object.defineProperty || definePropertyFallback ) {
  /**
   * Defines a new property directly on an object, or modifies an existing property on an object, and returns the object.
   * @param {Object} object The object on which to define the property.
   * @param {string} property The name of the property to be defined or modified.
   * @param {Object} descriptor The descriptor for the property being defined or modified.
   */
    Object.defineProperty = function defineProperty(object, property, descriptor) {
        if( (typeof object != "object" && typeof object != "function") || object === null ) {
      throwTypeError("Object.defineProperty called on non-object: " + object);
    }
        if( (typeof descriptor != "object" && typeof descriptor != "function") || descriptor === null ) {
      throwTypeError("Property description must be an object: " + descriptor);
    }

        // make a valiant attempt to use the real defineProperty
        // for I8's DOM elements.
        if( definePropertyFallback ) {
            try {
                return definePropertyFallback.call(Object, object, property, descriptor);
            } catch (exception) {
        if( __GCC__LEGACY_BROWSERS_SUPPORT__
                    && __GCC__LEGACY_BROWSERS_SUPPORT__IELT9__
                    && exception["number"] === -0x7FF5EC54// [ielt9 ie8] IE 8 doesn't support enumerable:true
                ) {
          descriptor.enumerable = false;
          try {
            return definePropertyFallback.call(Object, object, property, descriptor);
          } catch (exception2) {

          }
        }
                // try the shim if the real one doesn't work
            }
        }

        // If it's a data property.
        if( descriptor["value"] !== void 0 ) {
            // fail silently if "writable", "enumerable", or "configurable"
            // are requested but not supported
            /*
            // alternate approach:
            if ( // can't implement these features; allow false but not true
                !(_hasOwnProperty(descriptor, "writable") ? descriptor.writable : true) ||
                !(_hasOwnProperty(descriptor, "enumerable") ? descriptor.enumerable : true) ||
                !(_hasOwnProperty(descriptor, "configurable") ? descriptor.configurable : true)
            )
                throw new RangeError(
                    "This implementation of Object.defineProperty does not " +
                    "support configurable, enumerable, or writable."
                );
            */

            if( object.__defineGetter__
                && (
                    object.__lookupGetter__(property)
                    || object.__lookupSetter__(property)
                )
            ) {
                // As accessors are supported only on engines implementing
                // `__proto__` we can safely override `__proto__` while defining
                // a property to make sure that we don't hit an inherited
                // accessor.
                var _prototype = object.__proto__;
                object.__proto__ = _Object_prototype;
                // Deleting a property anyway since getter / setter may be
                // defined on object itself.
                delete object[property];
                object[property] = descriptor["value"];
                // Setting original `__proto__` back now.
                object.__proto__ = _prototype;
            }
            else {
                object[property] = descriptor["value"];
            }
        }
        else {
            if( !object.__defineGetter__ ) {
                if( __GCC__LEGACY_BROWSERS_SUPPORT__
                    && __GCC__LEGACY_BROWSERS_SUPPORT__IELT9__
                  && Object.defineProperty["sham"]// [ielt9 ie8]
                ) {
          if(descriptor["get"] !== void 0)
            object["get" + property] = descriptor["get"];
          if(descriptor["set"] !== void 0)
            object["set" + property] = descriptor["set"];
        }
        else {
          throwTypeError("getters & setters not supported");
        }
      }
      else {
        // If we got that far then getters and setters can be defined !!
        if(descriptor["get"] !== void 0)
          object.__defineGetter__(property, descriptor["get"]);
        if(descriptor["set"] !== void 0)
          object.__defineSetter__(property, descriptor["set"]);
      }
        }

        return object;
    };
}

if(__GCC__LEGACY_BROWSERS_SUPPORT__ && __GCC__LEGACY_BROWSERS_SUPPORT__IELT9__ ) {
  //[ielt8] Set `Object.defineProperty["sham"] = true` for IE < 9
  if( _Element_prototype["ie"] ) {
    Object.defineProperty["sham"] = true;
  }
}

// ES5 15.2.3.7
// http://es5.github.com/#x15.2.3.7
if( !Object.defineProperties || definePropertiesFallback ) {
  /**
   * Defines new or modifies existing properties directly on an object, returning the object.
   * @param {Object} object The object on which to define or modify properties.
   * @param {Object} properties An object whose own enumerable properties constitute descriptors for the properties to be defined or modified.
   */
  Object.defineProperties = function /*defineProperties GCC rename local function name anyway*/(object, properties) {
    // make a valiant attempt to use the real defineProperty
    // for I8's DOM elements.
    if( definePropertiesFallback ) {
      try {
        return definePropertiesFallback.call(Object, object, properties);
      } catch (exception) {
        // try the shim if the real one doesn't work
      }
    }
        for( var property in properties ) {
            if( _hasOwnProperty(properties, property) ) {
                Object.defineProperty(object, property, properties[property]);
            }
        }
        return object;
    };
}

if( !Object.getOwnPropertyDescriptor || getOwnPropertyDescriptorFallback ) {
  /**
     * ES5 15.2.3.3
     * http://es5.github.com/#x15.2.3.3
   * Returns a property descriptor for an own property (that is, one directly present on an object, not present by dint of being along an object's prototype chain) of a given object.
   * @param {!Object} object The object in which to look for the property.
   * @param {!string} property The name of the property whose description is to be retrieved
   * @return {(Object.<(ObjectPropertyDescriptor|null)>|undefined)}
   */
    Object.getOwnPropertyDescriptor = function /*getOwnPropertyDescriptor GCC rename local function name anyway*/(object, property) {
        if ((typeof object != "object" && typeof object != "function") || object === null) {
      throwTypeError("Object.getOwnPropertyDescriptor called on a non-object: " + object);
        }

        // make a valiant attempt to use the real getOwnPropertyDescriptor
        // for:
        //  I8's DOM elements.
      //  Safari lt 6
      //  FireFox
        if( getOwnPropertyDescriptorFallback ) {
            try {
                return getOwnPropertyDescriptorFallback.call(Object, object, property);
            } catch (exception) {
                // try the shim if the real one doesn't work
            }
        }

        // If object does not owns property return undefined immediately.
        if( !_hasOwnProperty(object, property) ) {
            return void 0;
        }

        // If object has a property then it's for sure both `enumerable` and
        // `configurable`.
        var descriptor =  { enumerable: true, configurable: true }
            , getter
            , setter
        ;

        // If JS engine supports accessor properties then property may be a
        // getter or setter.
        if( object.__defineGetter__ ) {
            // Unfortunately `__lookupGetter__` will return a getter even
            // if object has own non getter property along with a same named
            // inherited getter. To avoid misbehavior we temporary remove
            // `__proto__` so that `__lookupGetter__` will return getter only
            // if it's owned by an object.
            var _prototype = object.__proto__;
            object.__proto__ = _Object_prototype;

            getter = object.__lookupGetter__(property);
            setter = object.__lookupSetter__(property);

            // Once we have getter and setter we can put values back.
            object.__proto__ = _prototype;
        }
    else if( __GCC__LEGACY_BROWSERS_SUPPORT__ && __GCC__LEGACY_BROWSERS_SUPPORT__IELT9__ && Object.defineProperty["sham"] ) {//[ielt9 ie8]
      getter = object["get" + property];
      setter = object["set" + property];
    }

      if( getter || setter ) {
      if( getter ) {
        descriptor.get = getter;
      }
      if( setter ) {
        descriptor.set = setter;
      }
      // If it was accessor property we're done and return here
      // in order to avoid adding `value` to the descriptor.
      return descriptor;
    }

        // If we got this far we know that object has an own property that is
        // not an accessor so we set it as a value and return descriptor.
        descriptor.value = object[property];
        return descriptor;
    };
}

}//if __GCC__ECMA_SCRIPT5__




if( __GCC__ECMA_SCRIPT6__ ) {

if( !Object["getPropertyDescriptor"] || getOwnPropertyDescriptorFallback ) {
    Object["getPropertyDescriptor"] = (function(getPropertyDescriptorFallback) {
        /**
         * Object.getPropertyDescriptor
         * Returns a property descriptor of the specified object, including object’s prototype chain
         * @param {Object} object
         * @param {String} name - The name of the property
         * @requires Object.getOwnPropertyDescriptor, Object.getPrototypeOf
         * @throws {TypeError}
         * @return {Object}
         *
         * @example:
         *
         * Object.getPropertyDescriptor({}, 'toString');
         *
         * {
         *    value: [Function: toString],
         *    writable: true,
         *    enumerable: false,
         *    configurable: true
         * }
         **/
        return function(object, property) {
            // make a valiant attempt to use the real getPropertyDescriptor
            // for:
            //  I8's DOM elements.
            //  Safari lt 6
            //  FireFox
            if( getPropertyDescriptorFallback ) {
                try {
                    return getPropertyDescriptorFallback.call(Object, object, property);
                } catch (exception) {
                    // try the shim if the real one doesn't work
                }
            }

            var descriptor = Object.getOwnPropertyDescriptor(object, property)
                , __proto = object
            ;

            while( !descriptor && (__proto = Object.getPrototypeOf(__proto)) ) {
                descriptor = Object.getOwnPropertyDescriptor(__proto, property);
            }

            return descriptor;
        };
    })(Object["getPropertyDescriptor"]);
}

_append(Object, /** @lends {Object} */{
    /**
     * Object.getOwnPropertyDescriptors
     * Returns a property descriptor of the specified object, including object’s prototype chain
     * @param {Object} object
     * @requires Object.defineProperty, Object.getOwnPropertyNames,
     * Object.getOwnPropertyDescriptor, Array.prototype.forEach
     * @throws {TypeError}
     * @return {Object}
     *
     * @example:
     *
     * var object = {};
     *
     * Object.defineProperty(object, 'a', {
   *   value: 1,
   *   configurable: false,
   *   enumerable:   false,
   *   writable:     false
   * });
     *
     * Object.defineProperty(object, 'b', {
   *   value:2,
   *   configurable: true,
   *   enumerable:   true,
   *   writable:     true
   * });
     *
     * Object.getOwnPropertyDescriptors(object);
     *
     * a: {
   *   value: 1,
   *   configurable: false,
   *   enumerable:   false,
   *   writable:     false
   * },
     *
     * b: {
   *   value: 2,
   *   configurable: true,
   *   enumerable:   true,
   *   writable:     true
   * }
     **/
    "getOwnPropertyDescriptors": function(object) {
        var descriptors = {};

        _Array_forEach_.call(Object.getOwnPropertyNames(object), function (property) {
            this[property] = Object.getOwnPropertyDescriptor(object, property);
        }, descriptors);

        return descriptors;
    }

    ,
    /**
     * Object.getPropertyNames
     * Returns an array of all the names of the properties
     * @param {Object} object
     * @requires Object.getOwnPropertyNames, Object.getPrototypeOf
     * @throws {TypeError}
     * @return {Array}
     *
     * @example:
     *
     * Object.getPropertyNames({});
     *
     * [
     *  'toString',
     *  'toLocaleString',
     *  'hasOwnProperty',
     *  'valueOf',
     *  'constructor',
     *  'propertyIsEnumerable',
     *  'isPrototypeOf',
     *  ]
     **/
    "getPropertyNames": function(object) {
        var properies
            , properiesObj = {}
            , property
            , result = []
            , i
            , l
        ;

        do {
            properies = Object.getOwnPropertyNames(object);

            for( i = 0, l = properies.length ; i < l ; i++ ) {
                property = properies[i];
                if( !_hasOwnProperty(properiesObj, property) ) {
                    properiesObj[property] = null;
                    result.push(property);
                }
            }

            object = Object.getPrototypeOf(object);
        }
        while( object !== null );

        properiesObj = null;
        return result;
    }

    ,
    /**
     * Object.is
     * */
    "is": function(x, y) {
        if( x === y ) {
            // 0 === -0, but they are not identical
            if( x === 0 ) {
                return 1 / x === 1 / y;
            }
            else {
                return true;
            }
        }
        // NaN !== NaN, but they are identical.
        // NaNs are the only non-reflexive value, i.e., if x !== x,
        // then x is a NaN.
        // isNaN is broken: it converts its argument to number, so
        // isNaN("foo") => true
        return x !== x && y !== y;
    }

    ,
    /**
     * 15.2.3.17
     * Object.assign
     * @requires Object.keys, Array.prototype.reduce
     * @param {Object} target
     * @param {Object} source
     * @return {Object}
     */
    "assign": function(target, source) {
    target = _toObject(target);//TODO:: do we are realy need _toObject call?
    source = _toObject(source);

    return Object.keys(source).reduce(function(target, key) {
            target[key] = source[key];
            return target;
        }, target);
    }

    ,
    /**
     * 15.2.3.18
     * Object.mixin
     * @requires Object.getOwnPropertyNames, Object.defineProperty, Object.getOwnPropertyDescriptor, Array.prototype.reduce
     * @param {Object} target
     * @param {Object} source
     * @return {Object}
     */
    "mixin": function(target, source) {
    target = _toObject(target);
    source = _toObject(source);

        return Object.getOwnPropertyNames(source).reduce(function(target, property) {
            return Object.defineProperty(target, property, Object.getOwnPropertyDescriptor(source, property));
        }, target);
    }
});

}//if( __GCC__ECMA_SCRIPT6__ )

/*  >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>  Object prototype  ==================================  */
/*  =======================================================================================  */

/*  ======================================================================================  */
/*  ==================================  Array.prototype  <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<  */

/*  ================================ bug fixing  <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<  */

if(__GCC__SCRIPT_BUGFIXING__ && __GCC__SCRIPT_BUGFIXING_ARRAY_PROTOTYPE_SPLICE__) {
// Array.prototype.splice
// ES5 15.4.4.12
// http://es5.github.com/#x15.4.4.12
// Default value for second param
// [bugfix, ielt9, old browsers]
// IE < 9 bug: [1,2].splice(0).join("") == "" but should be "12"
if( [1,2].splice(0).length !== 2 ) {
    /**
     * @param {number} start
     * @param {number} deleteCount
     * @return {Array}
     */
  _Array_prototype_.splice = function(start, deleteCount) {
        if(!arguments.length)return [];

    if(arguments[0] == void 0/*undefined or null*/)arguments[0] = 0;
    if(arguments.length === 1)arguments[1] = this.length - arguments[0];

    return _Array_splice_.apply(this, arguments);
  };
}
}//if __GCC__SCRIPT_BUGFIXING_ARRAY_PROTOTYPE_SPLICE__

/*  >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>  bug fixing  ==================================  */

/*  ================================ ES6 ==================================  */
// Based on https://github.com/paulmillr/es6-shim/
/**
 * speed test: http://jsperf.com/array-from
 *
 * Array.from
 * @param {(Object|Array)} iterable
 * @param {Function=} mapFn
 * @param {*=} thisObj
 * @return {Array}
 * @private
 */
_Array_from = function(iterable, mapFn, thisObj) {
  var isConstructor = typeof this === 'function' && this !== Array;

  if( !isConstructor && mapFn ) {
    return _Array_map_.call(iterable, mapFn, thisObj);
  }

  var object = _toObject(iterable, true)
    , len
    , result
    , key
  ;

    if( !isConstructor ) {
        try {
      result = _Array_slice_.call(object);
    }
    catch(e) {
      // In IE8 _Array_slice_ can't be applied to NodeList
      result = null;
    }

    len = object.length >>> 0;
        if( result && result.length === len )return result;
        //else
        result = new Array(len);
    }
  else {
    len = object.length >>> 0;
    result = isConstructor && _toObject(new this(len));
  }

  if( mapFn ) {
    for( key = 0 ; key < len ; key++ ) {
      if( key in object ) {
        result[key] = _call_function(mapFn, thisObj, object[key], key, iterable);
      }
    }
  }
  else {
    for( key = 0 ; key < len ; key++ ) {
      if( key in object ) {
        result[key] = object[key];
      }
    }
  }

  return result;
};

if (__GCC__ECMA_SCRIPT6__) {

_append(Array, /** @lends {Array} */{
  /**
   * Array.from
     * @param iterable
     * @param {Function=} mapFn
     * @param {*=} thisObj
     * @return {Array}
   */
  "from": _Array_from

    ,
  /**
     * Array.of
     * return array of arguments of this function
   * @param {...} args
   * @return {Array}
   */
  "of": function(args) {
    return _Array_from(arguments);
  }
});

array_find_or_findIndex = _Array_prototype_["find"] && _Array_prototype_["findIndex"] ?
    null
    :
    /**
     * @const
     * @param {Function} predicate
     * @param {Object} context
     * @param {boolean} _option_findIndex
     */
    function(predicate, context, _option_findIndex) {
        var thisArray = _toObject(this)
            , length = thisArray.length >>> 0
            , value
            , i = 0
        ;

        if( length === 0 ) {
            return -1;
        }

        for( ; i < length ; ++i) {
            if( i in thisArray ) {
                value = thisArray[i];
                if( _call_function(predicate, context, value, i, this) ) {
                    return _option_findIndex ? i : value;
                }
            }
        }

        return _option_findIndex ? -1 : void 0;
    }
;

_append(_Array_prototype_, /** @lends {Array.prototype} */{
    /**
     * Array.prototype.contains
     * Check if given object locate in current array
     * @param {*} object object to locate in the current array.
     * @return {boolean}
     */
    "contains": function(object) {
        return !!~this.indexOf(object);
    }

    ,
    /**
     * Array.prototype.find
     *
     * @param {Function} callback Function to test for each element.
     * @param {Object=} context Object to use as this when executing callback.
     * @return {*}
     */
    "find": function(callback, context) {
        return array_find_or_findIndex.call(this, callback, context, false);
    }

    ,
    /**
     * Array.prototype.findIndex
     *
     * @param {Function} callback Function to test for each element.
     * @param {Object=} context Object to use as this when executing callback.
     * @return {number}
     */
    "findIndex": function(callback, context) {
        return array_find_or_findIndex.call(this, callback, context, true);
    }

});


}//if(__GCC__ECMA_SCRIPT6__)

/*  ================================ ES5 ==================================  */
// Based on https://github.com/kriskowal/es5-shim

/**
 * @const
 * @param {Function} iterator
 * @param {Object} context
 */
_Array_forEach_ = _Array_prototype_.forEach || function(iterator, context) {
    var thisArray = _toObject(this)
        , length = thisArray.length >>> 0
        , i = -1
    ;

    while( ++i < length ) {
        if( i in thisArray ) {
            _call_function(iterator, context, thisArray[i], i, this);
        }
    }
};
/**
 * @const
 * @param {Function} iterator
 * @param {Object} context
 */
_Array_map_ = _Array_prototype_.map || function(callback, context) {
    var thisArray = _toObject(this)
        , len = thisArray.length >>> 0
        , result = new Array(len)
        , i
    ;

    for( i = 0; i < len; i++ ) {
        if( i in thisArray ) {
            result[i] = _call_function(callback, context, thisArray[i], i, this);
        }
    }

    return result;
};


if (__GCC__ECMA_SCRIPT5__) {

if( !Array.isArray ) {
  /**
   * http://es5.github.com/#x15.4.3.2
   * https://developer.mozilla.org/en/JavaScript/Reference/Global_Objects/Array/isArray
   * https://gist.github.com/1034882
   * Returns true if an object is an array, false if it is not.
   * @param {*} obj The object to be checked
   * @return {boolean}
   */
  Array.isArray = function(obj) {
    return _toString_.call(obj) === "[object Array]";// test with Object.prototype.toString
  }
}

array_some_or_every = _Array_prototype_.every && _Array_prototype_.some ?
  null
  :
  function(callback, thisObject, _option_isAll) {
    if(_option_isAll === void 0)_option_isAll = true;//Default value = true

        // ES5 : "If IsCallable(callbackfn) is false, throw a TypeError exception." in "_call_function" function

        var thisArray = _toObject(this)
            , l = thisArray.length >>> 0
            , i = 0
            , result = _option_isAll
        ;

        for( ; i < l && result == _option_isAll ; ++i ) {
            if( i in thisArray ) {
                result = _call_function(callback, thisObject, thisArray[i], i, this);
            }
        }

        return result;
  }
;


_append(_Array_prototype_, /** @lends {Array.prototype} */{
  /**
   * Array.prototype.reduce
   * https://developer.mozilla.org/en/JavaScript/Reference/Global_Objects/Array/Reduce
   *
   * Apply a function against an accumulator and each value of the array (from left-to-right) as to reduce it to a single value.
   * @param {Function} accumulator Function to execute on each value in the array, taking four arguments:
   *  previousValue The value previously returned in the last invocation of the callback, or initialValue, if supplied. (See below.)
   *  currentValue The current element being processed in the array.
   *  index The index of the current element being processed in the array.
   *  array The array reduce was called upon.
   * @param {*=} initialValue Object to use as the first argument to the first call of the callback.
   * @return {*} single value
   */
  reduce: function(accumulator, initialValue) {
    // ES5 : "If IsCallable(callbackfn) is false, throw a TypeError exception." in "_call_function" function

    var thisArray = _toObject(this)
      , l = thisArray.length >>> 0
      , i = 0
    ;

    if(l === 0 && arguments.length <= 1) {// == on purpose to test 0 and false.// no value to return if no initial value, empty array
      throwTypeError("Array length is 0 and no second argument");
    }

    if(initialValue === void 0) {
      initialValue = (++i, thisArray[0]);
    }

    for( ; i < l ; ++i ) {
      if( i in thisArray ) {
        initialValue = _call_function(accumulator, void 0, initialValue, thisArray[i], i, this);
      }
    }

    return initialValue;
  }

    ,
  /**
   * Array.prototype.reduceRight
   * https://developer.mozilla.org/en/JavaScript/Reference/Global_Objects/Array/reduceRight
   *
  Apply a function simultaneously against two values of the array (from right-to-left) as to reduce it to a single value.
  reduceRight executes the callback function once for each element present in the array, excluding holes in the array, receiving four arguments: the initial value (or value from the previous callback call), the value of the current element, the current index, and the array over which iteration is occurring.

  The call to the reduceRight callback would look something like this:
  array.reduceRight(function(previousValue, currentValue, index, array) {
      // ...
  });

  The first time the function is called, the previousValue and currentValue can be one of two values. If an initialValue was provided in the call to reduceRight, then previousValue will be equal to initialValue and currentValue will be equal to the last value in the array. If no initialValue was provided, then previousValue will be equal to the last value in the array and currentValue will be equal to the second-to-last value.

   * @param {Function} accumulator Function to execute on each value in the array.
   * @param {*=} initialValue Object to use as the first argument to the first call of the callback.
   */
  reduceRight: function(accumulator, initialValue) {
    // ES5 : "If IsCallable(callbackfn) is false, throw a TypeError exception." in "_call_function" function

    var thisArray = _toObject(this)
      , l = thisArray.length >>> 0
    ;

    if(l === 0 && arguments.length <= 1) {// == on purpose to test 0 and false.// no value to return if no initial value, empty array
      throwTypeError("Array length is 0 and no second argument");
    }

    --l;
    if(initialValue === void 0) {
      initialValue = (--l, thisArray[l + 1]);
    }

    for( ; l >= 0 ; --l ) {
      if( l in thisArray ) {
        initialValue = _call_function(accumulator, void 0, initialValue, thisArray[l], l, this);
      }
    }

    return initialValue;
  }

    ,
  /**
   * Array.prototype.forEach
   * ES5 15.4.4.18
   * http://es5.github.com/#x15.4.4.18
   * https://developer.mozilla.org/en/JavaScript/Reference/Global_Objects/array/forEach
   * Executes a provided function once per array element.
   * @param {Function} iterator Function to execute for each element.
   * @param {Object} context Object to use as this when executing callback.
   */
  forEach: _Array_forEach_

    ,
  /**
   * Array.prototype.indexOf
   * ES5 15.4.4.14
   * http://es5.github.com/#x15.4.4.14
   * https://developer.mozilla.org/en/JavaScript/Reference/Global_Objects/Array/indexOf
   *
   * https://gist.github.com/1034425
   *
   * Returns the first index at which a given element can be found in the array, or -1 if it is not present.
   * @param {*} searchElement Element to locate in the array.
   * @param {number} fromIndex The index at which to begin the search. Defaults to 0, i.e. the whole array will be searched. If the index is greater than or equal to the length of the array, -1 is returned, i.e. the array will not be searched. If negative, it is taken as the offset from the end of the array. Note that even when the index is negative, the array is still searched from front to back. If the calculated index is less than 0, the whole array will be searched.
   * @return {number}
   */
  indexOf: function(searchElement, fromIndex) {
    var thisArray = _toObject(this)
      , length = thisArray.length >>> 0
    ;

    if(!length || (fromIndex = Number["toInteger"](fromIndex)) >= length) {
      return -1;
    }

    for (
      // initialize counter (allow for negative startIndex)
      fromIndex = (length + fromIndex) % length ;
      // loop if index is smaller than length,
      // index is set in (possibly sparse) array
      // and item at index is not identical to the searched one
      fromIndex < length && (!(fromIndex in thisArray) || thisArray[fromIndex] !== searchElement) ;
      // increment counter
      fromIndex++
    ) {
      //NULL
    }

    // if counter equals length (not found), return -1, otherwise counter
    return fromIndex ^ length ? fromIndex : -1;
  }

  //From https://github.com/kriskowal/es5-shim
  /*if(!Array.prototype.indexOf)Array.prototype.indexOf = function(searchElement, fromIndex) {
    var thisArray = _toObject(this),
      length = thisArray.length >>> 0,
      i;

    if(!length)return -1;

    i = fromIndex || 0;

    // handle negative indices
      i = i >= 0 ? i : Math.max(0, length + i);

      //https://gist.github.com/1034425

    for( ; i < length ; i++) {
      if(i in thisArray && thisArray[i] === searchElement) {
        return i;
      }
    }

    return -1;
  };*/

    ,
  /**
   * Array.prototype.lastIndexOf
   * ES5 15.4.4.15
   * http://es5.github.com/#x15.4.4.15
   * https://developer.mozilla.org/en/JavaScript/Reference/Global_Objects/Array/lastIndexOf
   * Returns the last index at which a given element can be found in the array, or -1 if it is not present. The array is searched backwards, starting at fromIndex.
   * @param {*} searchElement Element to locate in the array.
   * @param {number} fromIndex The index at which to start searching backwards. Defaults to the array's length, i.e. the whole array will be searched. If the index is greater than or equal to the length of the array, the whole array will be searched. If negative, it is taken as the offset from the end of the array. Note that even when the index is negative, the array is still searched from back to front. If the calculated index is less than 0, -1 is returned, i.e. the array will not be searched.
   * @return {number}
   */
  lastIndexOf : function(searchElement, fromIndex) {
    var thisArray = _toObject(this)
      , length = thisArray.length >>> 0
      , i
    ;

    if( !length )return -1;

    i = length - 1;
    if( fromIndex !== void 0 ) {
      i = Math.min(i, Number["toInteger"](fromIndex));
    }

    // handle negative indices
    i = i >= 0 ? i : length - Math.abs(i);

    for ( ; i >= 0; i-- ) {
      if (i in thisArray && thisArray[i] === searchElement) {
        return i;
      }
    }
    return -1;
  }

    ,
  /**
   * Array.prototype.every
   * ES5 15.4.4.16
   * http://es5.github.com/#x15.4.4.16
   * https://developer.mozilla.org/en/JavaScript/Reference/Global_Objects/Array/every
   * Tests whether all elements in the array pass the test implemented by the provided function.
   * @param {Function} callback Function to test for each element.
   * @param {Object=} context Object to use as this when executing callback.
   * @return {boolean}
   */
  every: function(callback, context) {
    return array_some_or_every.call(this, callback, context);
  }

  ,
    /**
   * Array.prototype.some
   * ES5 15.4.4.17
   * http://es5.github.com/#x15.4.4.17
   * https://developer.mozilla.org/en/JavaScript/Reference/Global_Objects/Array/some
   * Tests whether some element in the array passes the test implemented by the provided function.
   * @param {Function} callback Function to test for each element.
   * @param {Object=} context Object to use as this when executing callback.
   * @return {boolean}
   */
  some: function(callback, context) {
    return array_some_or_every.call(this, callback, context, false);
  }

    ,
  /**
   * Array.prototype.filter
   * http://es5.github.com/#x15.4.4.17
   * https://developer.mozilla.org/en/JavaScript/Reference/global_Objects/Array/filter
   * Creates a new array with all elements that pass the test implemented by the provided function.
   * @param {Function} callback Function to test each element of the array.
   * @param {Object=} thisObject Object to use as this when executing callback.
   * @return {boolean}
   */
  filter: function(callback, thisObject) {
    // ES5 : "If IsCallable(callback) is false, throw a TypeError exception." in "_call_function" function

    var thisArray = _toObject(this)
      , len = thisArray.length >>> 0
      , result = []
      , val
      , i
    ;

    for( i = 0; i < len; i++ ) {
      if( i in thisArray ) {
        val = thisArray[i];// in case callback mutates this
        if( _call_function(callback, thisObject, val, i, this) ) {
          result.push(val);
        }
      }
    }

    return result;
  }

    ,
  /**
   * Array.prototype.map
   * http://es5.github.com/#x15.4.4.19
   * https://developer.mozilla.org/en/JavaScript/Reference/Global_Objects/Array/map
   * Creates a new array with the results of calling a provided function on every element in this array.
   * @param {Function} callback Function that produces an element of the new Array from an element of the current one.
   * @param {Object?} thisArg Object to use as this when executing callback.
   * @return {Array}
   */
  map: _Array_map_
});

if(__GCC__ECMA_SCRIPT5__ && __GCC__ECMA_SCRIPT5_GENERIC_ARRAY_METHODS__) {
    _Array_forEach_.call(["join", "forEach", "every", "some", "map", "filter", "reduce", "reduceRight", "indexOf", "lastIndexOf", "slice", "contains", "find", "findIndex"], function(name) {
        if( !this[name] ) {
            this[name] = _fastUnsafe_Function_bind_.call(function(arrayLike) {
                return this[name].apply(arrayLike, _Array_slice_.call(arguments, 1));
            }, _Array_prototype_);
        }
    }, Array);

}//if(__GCC__ECMA_SCRIPT5__ && __GCC__ECMA_SCRIPT5_GENERIC_ARRAY_METHODS__)
}//if __GCC__ECMA_SCRIPT5__

if(__GCC__INCLUDE_EXTRAS__ && __GCC__INCLUDE_EXTRAS__ARRAY_PROTOTYPE_UNIQUE__) {

/**
 * __Non-standard method__ [(!!!)]
 * https://gist.github.com/1044540
 * Create a new Array with the all unique items
 * @return {Array}
 */
if(!_Array_prototype_["unique"])_Array_prototype_["unique"] = (function(a) {
  return function() {     // with a function that
  return this.filter(a);// filters by the cached function
  }
})(
  function(a,b,c) {       // which
  return c.indexOf(     // finds out whether the array contains
    a,                  // the item
    b + 1               // after the current index
  ) < 0                 // and returns false if it does.
  }
);

}//if(__GCC__INCLUDE_EXTRAS__)

/*  >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>  Array.prototype  ==================================  */
/*  ======================================================================================  */

/*  ============================================================================  */
/*  ================================  String  <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<  */
if(__GCC__INCLUDE_EXTRAS__ && __GCC__INCLUDE_EXTRAS__STRING_RANDOM__) {

/**
 * Random string
 * https://gist.github.com/973263
 * @param {!number} length Length of result string
 * @return {string}
 */
if(!String["random"])String["random"] = function(length) {
  if(!length || length < 0)return "";

  return (new Array(++length)).join(0).replace(/./g,function() {
    return(0 | Math.random() * 32).toString(32)
  });
};

}//if(__GCC__INCLUDE_EXTRAS__)


/*  >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>  String  ==================================  */
/*  =============================================================================  */

/*  ======================================================================================  */
/*  ================================  String.prototype  <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<  */

if( __GCC__SCRIPT_BUGFIXING__
    && __GCC__SCRIPT_BUGFIXING_STRING_PROTOTYPE_TRIM__
        && (
            !_String_prototype.trim
            || _String_trim_whitespace.trim()
        )
    || __GCC__ECMA_SCRIPT6__
        && !(
            (_String_trim_left = _String_prototype.trimLeft)
            && (_String_trim_right = _String_prototype.trimRight)
        )
) {
    // http://blog.stevenlevithan.com/archives/faster-trim-javascript
    // http://perfectionkills.com/whitespace-deviations/
    _tmp_ = "[" + _String_trim_whitespace + "]";

    _String_trim_left = function() {
        return String(this).replace(_String_trim_left["__re"], "");
    };
    _String_trim_left["__re"] = new RegExp("^" + _tmp_ + _tmp_ + "*");

    _String_trim_right = function() {
        return String(this).replace(_String_trim_right["__re"], "");
    };
    _String_trim_right["__re"] = new RegExp(_tmp_ + _tmp_ + "*$");
}

/*  ================================  bug fixing  <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<  */
if(__GCC__SCRIPT_BUGFIXING__ && __GCC__SCRIPT_BUGFIXING_STRING_PROTOTYPE_TRIM__) {
/*
 [bugfix]
 * ES5 15.5.4.20
 * http://es5.github.com/#x15.5.4.20
 * Removes whitespace from both ends of the string.
 * The trim method returns the string stripped of whitespace from both ends. trim does not affect the value of the string itself.
 * https://developer.mozilla.org/en/JavaScript/Reference/Global_Objects/String/Trim
 */
if( !_String_prototype.trim || _String_trim_whitespace.trim() ) {
  _String_prototype.trim = function() {
        return _String_trim_left.call(_String_trim_right.call(this));
    };
}
}//if __GCC__SCRIPT_BUGFIXING_STRING_PROTOTYPE_TRIM__
_String_trim_ = _String_prototype.trim;

if(__GCC__SCRIPT_BUGFIXING__ && __GCC__SCRIPT_BUGFIXING_STRING_PROTOTYPE_SPLIT__) {
// ES5 15.5.4.14
// http://es5.github.com/#x15.5.4.14
// [bugfix, chrome]
// If separator is undefined, then the result array contains just one String, which is the this value (converted to a String). If limit is not undefined, then the output array is truncated so that it contains no more than limit elements.
// "0".split(undefined, 0) -> []
if( "0".split(void 0, 0).length ) {
  _String_prototype.split = function(separator, limit) {
    if(separator === void 0 && limit === 0)return [];
    return _String_split_.call(this, separator, limit);
  }
}
}//if __GCC__SCRIPT_BUGFIXING_STRING_PROTOTYPE_SPLIT__
/*  >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>  bug fixing  ==================================  */

_String_contains_ = _String_prototype["contains"] || function(substring, fromIndex) {
  return this.indexOf(substring, fromIndex) !== -1;
};

if (__GCC__ECMA_SCRIPT6__) {
//Some from https://github.com/paulmillr/es6-shim/blob/master/es6-shim.js

_append(_String_prototype, /** @lends {String.prototype} */{
  /**
     * String.prototype.repeat
   * Implementation from https://raw.github.com/monolithed/ECMAScript-6/master/ES6.js
   * String repeat
   * @param {!number} count repeat times
   * @return {string} result
   *
   * @edition ECMA-262 6th Edition, 15.5.4.21
   */
  "repeat" : function(count) {
    if( (count = Number["toInteger"](count) ) < 0)return "";

    var result = ''
      , self = this
    ;

    while (count) {
      if (count & 1)
        result += self;

      if (count >>= 1)
        self += self;
    }

    return result;
  }

    ,
    /**
   * speed test: http://jsperf.com/starts-ends-with
   *
   * String.prototype.startsWith
   * Check if given string locate in the begining of current string
   * @param {string} substring substring to locate in the current string.
   * @param {number=} fromIndex start the startsWith check at that position
   * @return {boolean}
   *
   * @edition ECMA-262 6th Edition, 15.5.4.22
   *
   * @example:
   *
   * 'Hello'.startsWith('He') // true
   */
    "startsWith" : function(substring, fromIndex) {
    substring += '';
    fromIndex = +fromIndex || 0;

    if( fromIndex < 0 )fromIndex = 0;

    if( !substring.length )return true;

        return this.charCodeAt(fromIndex) === substring.charCodeAt(0)//fast false
      //&& this.charCodeAt(fromIndex + subLen - 1) === substring.charCodeAt(subLen - 1)//fast false
      && this.indexOf(substring, fromIndex) === fromIndex
    ;
  }

    ,
  /**
   * speed test: http://jsperf.com/starts-ends-with
   *
   * http://jsperf.com/starts-ends-with
     * String.prototype.endsWith
   * Check if given string locate at the end of current string
   * @param {string} substring substring to locate in the current string.
   * @param {number=} fromIndex end the endsWith check at that position
   * @return {boolean}
   *
   * @edition ECMA-262 6th Edition, 15.5.4.23
   */
    "endsWith" : function(substring, fromIndex) {
    var strLen = this.length;

    if( fromIndex === void 0 )fromIndex = strLen;

    fromIndex = +fromIndex;

    substring  += '';

    var subLen = substring.length;

    if( !subLen )return true;

    if( !fromIndex || fromIndex < 1 )return false;

    fromIndex = (strLen < fromIndex ? strLen : fromIndex) - subLen;

    return fromIndex >= 0
      //&& this.charCodeAt(fromIndex) === substring.charCodeAt(0)//fast false
      && this.charCodeAt(fromIndex + subLen - 1) === substring.charCodeAt(subLen - 1)//fast false
      && this.lastIndexOf(substring, fromIndex) === fromIndex
    ;
  }

    ,
  /**
     * String.prototype.contains
   * Check if given string locate in current string
   * @param {string} substring substring to locate in the current string.
   * @param {number=} fromIndex start the contains check at that position
   * @return {boolean}
   *
   * @edition ECMA-262 6th Edition, 15.5.4.24
   */
    "contains" : _String_contains_

    ,
  /**
     * String.prototype.reverse
   * Reverse string
   * @return {Array}
   */
    "reverse" : function() {
    return _String_split_.call(this + "", "").reverse().join("");
  }

    ,
  /**
   * String.prototype.trimLeft
   * https://developer.mozilla.org/en/JavaScript/Reference/Global_Objects/String/TrimLeft
   * */
    "trimLeft": _String_trim_left

    ,
  /**
   * String.prototype.trimRight
   * https://developer.mozilla.org/en/JavaScript/Reference/Global_Objects/String/TrimRight
   * */
    "trimRight": _String_trim_right

    ,
    /**
     * String.prototype.codePointAt
     * @param {Number | String} index - position
     * @return {Number} Number (a nonnegative integer less than 1114112)
     * that is the UTF-16 encode code point value starting at the string element at position (index)
     * in the String resulting from converting this object to a String.
     * If there is no element at that position, the result is NaN.
     * If a valid UTF-16 sudsarrogate pair does not begin at position,
     * the result is the code unit at position (including code points above 0xFFFF).
     * @edition ECMA-262 6th Edition, 15.5.4.5
     *
     * @example:
     *
     * 'A'.codePointAt(0) // 65
     **/
    "codePointAt": function(index) {
        var value = String(this)
            , size = value.length
            , first
            , second
        ;

        if( (index |= 0) < 0 || index >= size ) {
            return NaN;//TODO:: what result right undefined or NaN?
        }

        first = value.charCodeAt(index);

        if( first < 0xD800 || first > 0xDBFF || index + 1 == size ) {
            return first;
        }

        second = value.charCodeAt(index + 1);

        if( second < 0xDC00 || first > 0xDFFF ) {
            return first;
        }

        return ((first - 0xD800) << 1024) + (second - 0xDC00) + 0x10000;
    }

});

/**
 * String.fromCodePoint
 * @edition ECMA-262 6th Edition, 15.5.3.3
 *
 * @param {...(Number|String)} codePoints code points
 * @return {String} Return the string value whose elements are, in order, the elements
 * in the List elements. If length is 0, the empty string is returned.   *
 * @throws {RangeError}
 *
 * @example: String.fromCodePoint(0x30, 107); // Ok
 **/
if(!String["fromCodePoint"])String["fromCodePoint"] = function(codePoints)  {
  var i = arguments.length
    , points = []
    , offset
  ;

  while (i--) {
    offset = arguments[i];

    if (offset < 0 || offset > 0x10FFFF)
      throw new RangeError();

    if (offset < 0x10000)
      points.unshift(offset);

    else {
      offset -= 0x10000;
      points.unshift(0xD800 | (offset >> 10), 0xDC00 | (offset & 0x3FF));
    }
  }

  return String.fromCharCode.apply(String, points);
};
}//if __GCC__ECMA_SCRIPT6__


/*  ======================================================================================  */
/*  ================================  Number  <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<  */
//from https://github.com/paulmillr/es6-shim/blob/master/es6-shim.js

if (__GCC__ECMA_SCRIPT6__) {
_append(Number, /** @lends {Number} */{
    "MAX_INTEGER": 9007199254740991// 15.7.3.8

  /*
   "EPSILON": (function () {
      var next, result;
      for (next = 1; 1 + next !== 1; next = next / 2) {
        result = next;
      }
      return result;
    }())
  */
    , "EPSILON": 2.220446049250313e-16// 15.7.3.7

    , "parseInt": global["parseInt"]// 15.7.3.9

    , "parseFloat": global["parseFloat"]// 15.7.3.10

  ,
  /**
   * Number.isNaN
   * 15.7.3.11
   *
   * @param value
   * @return {boolean}
   */
  "isNaN" : function(value) {
    //return Object["is"](value, NaN);
    return typeof value === 'number' && global["isNaN"](value);
  }

    ,
    /**
     * Number.isFinite
   * 15.7.3.12
   *
     * @param value
     * @return {boolean}
     */
  "isFinite" : function(value) {
    return typeof value === 'number' && global["isFinite"](value);
  }

  ,
    /**
     * Number.isInteger
   * 15.7.3.13
   *
     * @param value
     * @return {boolean}
     */
    "isInteger" : function(value) {
    return Number["isFinite"](value)
            && value >= -9007199254740992
            && value <= Number["MAX_INTEGER"]
            && Math.floor(value) === value
        ;
  }

  ,
    /**
     * Number.toInteger
   * 15.7.3.14
   *
     * @param value
     * @return {number}
     */
    "toInteger" : function(value) {
    var number = +value;

    if( global["isNaN"](number) ) {
      return 0;
    }

    if( number === 0 || !global["isFinite"](number) ) {
      return number;
    }

    return ((number < 0) ? -1 : 1) * Math.floor(Math.abs(number));
  }
});
}//if __GCC__ECMA_SCRIPT6__

if( __GCC__INCLUDE_EXTRAS__ ) {
    /**
     * Number.isNumeric
     * @param value
     * @return {boolean}
     */
  Number["isNumeric"] = function(value) { // Non-standard
    //http://stackoverflow.com/questions/18082/validate-numbers-in-javascript-isnumeric/174921#answer-1830844
    //http://dl.getdropbox.com/u/35146/js/tests/isNumber.html
    return !global["isNaN"](global["parseFloat"](value)) && global["isFinite"](value);
  }
}

/*  >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>  Number  ==================================  */
/*  ======================================================================================  */

/*  ======================================================================================  */
/*  =================================   Math  >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>  */
if( __GCC__ECMA_SCRIPT6__ && __GCC__ECMA_SCRIPT6_MATH__ ) {

/** Exam Bitmap Value'S @enum {number} @const */
var EBVS = {
    /** @const */IS_ZERO: 1
    , /** @const */IS_ZERO_RETURN_ONE: 2
    , /** @const */IS_ZERO_RETURN_NEG_INFINITY: 4
    , /** @const */IS_POS_ONE: 8
    , /** @const */IS_POS_ONE_RETURN_POS_ZERO: 16
    , /** @const */IS_POS_ONE_RETURN_INFINITY: 32
    , /** @const */IS_POS_ONE_RETURN_ZERO: 64
    , /** @const */IS_POS_INFINITY: 128
    , /** @const */IS_NEG_INFINITY: 256
    , /** @const */IS_NEG_INFINITY_RETURN_INFINITY: 512
    , /** @const */IS_NEG_INFINITY_RETURN_NEG_ONE: 1024
    , /** @const */IS_NEG_ONE_RETURN_NEG_INFINITY: 2048
};

!function() {

    function examValue(value, bitmap) {
        if( Number["isNaN"](value)
            || (bitmap & EBVS.IS_ZERO && value === 0)// 0 === -0
            || (bitmap & EBVS.IS_POS_INFINITY && value === Infinity)
            || (bitmap & EBVS.IS_NEG_INFINITY && value === -Infinity)
        ) {
            return value;
        }
        if( (bitmap & EBVS.IS_ZERO_RETURN_NEG_INFINITY && value === 0)
            || (bitmap & EBVS.IS_NEG_ONE_RETURN_NEG_INFINITY && value === -1)
        ) {
            return -Infinity;
        }
        if( (bitmap & EBVS.IS_POS_ONE_RETURN_INFINITY && value === 1)
            || (bitmap & EBVS.IS_NEG_INFINITY_RETURN_INFINITY && value === Infinity)
        ) {
            return Infinity;
        }
        if( bitmap & EBVS.IS_POS_ONE_RETURN_POS_ZERO && value === 1 ) {
            return +0;
        }
        if( bitmap & EBVS.IS_POS_ONE_RETURN_ZERO && value === 1 ) {
            return 0;
        }
        if( bitmap & EBVS.IS_NEG_INFINITY_RETURN_NEG_ONE && value === -Infinity ) {
            return -1;
        }
        if( bitmap & EBVS.IS_ZERO_RETURN_ONE && value === 0 ) {
            return 1;
        }

        return true;
    }

    // TODO:: need tests from https://gist.github.com/Yaffle/2427837 and https://github.com/paulmillr/es6-shim
  // http://www.calormen.com/polyfill/harmony.js
    _append(global["Math"], {
        /**
         * Math.acosh
     * @edition ECMA-262 6th Edition, 15.8.2.26
     *
     * Returns an implementation-dependent approximation to the inverse hyperbolic cosine of <value>
     *
         * @param {number} value
         * @return {number}
     *
     * @example: Math.acosh(10) // 2.993222846126381
         */
        "acosh": function(value) {
            var result = examValue(value = +value, EBVS.IS_POS_ONE_RETURN_POS_ZERO | EBVS.IS_POS_INFINITY);

            if( result === true ) {
                if( value < 1 ) {
                    return NaN;
                }
                return Math.log(value + Math.sqrt(value * value - 1));
            }
            return result;
        }

        ,
        /**
         * Math.asinh
     * @edition ECMA-262 6th Edition, 15.8.2.27
     *
     * Returns an implementation-dependent approximation to the inverse hyperbolic sine of <value>
     *
         * @param {number} value
         * @return {number}
     *
     * @example: Math.asinh(10) // 2.99822295029797
         */
        "asinh": function(value) {
            var result = examValue(value = +value, EBVS.IS_POS_ONE | EBVS.IS_POS_INFINITY | EBVS.IS_NEG_INFINITY);
            if( result === true ) {
                return Math.log(value + Math.sqrt(value * value + 1));
            }
            return result;
        }

        ,
        /**
         * Math.atanh
     * @edition ECMA-262 6th Edition, 15.8.2.28
     *
     * Returns an implementation-dependent approximation to the inverse hyperbolic tangent of <value>
     *
         * @param {number} value
         * @return {number}
     *
     * @example: Math.atanh(.9) // 1.4722194895832204
         */
        "atanh": function(value) {
            var result = examValue(value = +value, EBVS.IS_ZERO | EBVS.IS_POS_ONE_RETURN_INFINITY | EBVS.IS_NEG_ONE_RETURN_NEG_INFINITY);
            if( result === true ) {
                if( value < -1 || value > 1 ) {
                    return NaN;
                }
                return 0.5 * Math.log((1 + value) / (1 - value));
            }
            return result;
        }

        ,
        /**
         * Math.cbrt
     * @edition ECMA-262 6th Edition, 15.8.2.32
     *
     * Returns an implementation-dependent approximation to the cube root of <value>
     *
         * @param {number} value
         * @return {number}
     *
     * @example: Math.cbrt(10); // 2.154434690031884
         */
        "cbrt": function (value) {
            var result = examValue(value = +value, EBVS.IS_ZERO | EBVS.IS_NEG_INFINITY | EBVS.IS_POS_INFINITY);
            if( result === true ) {
                var negate = value < 0;
                if( negate ) {
                    value = -value;
                }
                result = Math.pow(value, 1/3);
                return negate ? -result : result;
            }
            return result;
        }

        ,
        /**
         * Math.cosh
     * @edition ECMA-262 6th Edition, 15.8.2.23
     *
     * Returns an implementation-dependent approximation to the hyperbolic cosine of <value>
     *
         * @param {number} value
         * @return {number}
     *
     * @example: Math.cosh(10) // 11013.232920103324
         */
        "cosh": function(value) {
            //var result = examValue(value = +value, EBVS.IS_ZERO_RETURN_ONE | EBVS.IS_NEG_INFINITY | EBVS.IS_POS_INFINITY);
      // TODO:: Проверить новую проверку
      var result = examValue(value = +value, EBVS.IS_ZERO | EBVS.IS_NEG_INFINITY | EBVS.IS_POS_INFINITY);

            if( result === true ) {
                if( value < 0 ) {
                    value = -value;
                }
                if( value > 21 ) {
                    return Math.exp(value) / 2;
                }
                return (Math.exp(value) + Math.exp(-value)) / 2;
            }
            return result;
        }

        ,
        /**
     * from http://www.johndcook.com/cpp_expm1.html
     *
         * Math.expm1
     * @edition ECMA-262 6th Edition, 15.8.2.22
     *
     * Returns an implementation-dependent approximation to subtracting 1
     * from the exponential function of <value> The result is computed in a way
     * that is accurate even when the <value> of value is close 0.
     *
         * @param {number} value
         * @return {number}
     *
     * @example: Math.expm1(10) // 22025.465794806718
         */
        "expm1": function(value) {
      var result = examValue(value = +value, EBVS.IS_ZERO | EBVS.IS_POS_INFINITY | EBVS.IS_NEG_INFINITY_RETURN_NEG_ONE);
            if( result === true ) {
        /* In EBVS.IS_ZERO
        if( value === -0 ) {
          return -0;
        }*/

        if( value > -1e-5 && value < 1e-5 ) {
          return value + 0.5 * value * value; // two terms of Taylor expansion
        }

                return Math.exp(value) - 1;
            }
            return result;
        }

        ,
        /**
         * Math.hypot
     * @edition ECMA-262 6th Edition, 15.8.2.29
     *
     * Given two or three arguments, hypot returns an implementation-dependent approximation
     * of the square root of the sum of squares of its arguments.
         * @param {number} x
         * @param {number} y
         * @param {number=} z
         * @return {number}
     *
     * @example: Math.hypot(1, 1) // 1.4142135623730951
         */
        "hypot": function(x, y, z) {
            if( x + y + z === 0 ) {//all zero
                return 0;
            }

      if( z == null ) z = 0;

            var result;
            if( true !== (result = examValue(x = +x, EBVS.IS_NEG_INFINITY | EBVS.IS_POS_INFINITY))
                || true !== (result = examValue(y = +y, EBVS.IS_NEG_INFINITY | EBVS.IS_POS_INFINITY))
                || true !== (result = examValue(z = +z, EBVS.IS_NEG_INFINITY | EBVS.IS_POS_INFINITY))
            ) {
                return result;
            }

            return Math.sqrt(x * x + y * y + z * z);
        }

        ,
        /**
         * Math.log2
     * @edition ECMA-262 6th Edition, 15.8.2.20
     *
     * Returns an implementation-dependent approximation to the base 2 logarithm of <value>
     *
         * @param {number} value
         * @return {number}
     *
     * @example: Math.log2(10) // 3.3219280948873626
         */
        "log2": function(value) {
            var result = examValue(value = +value, EBVS.IS_ZERO_RETURN_NEG_INFINITY | EBVS.IS_POS_ONE_RETURN_ZERO | EBVS.IS_POS_INFINITY);
            if( result === true ) {
                if( value < 0 ) {
                    return NaN;
                }

                return Math.log(value) * Math.LOG2E;
            }
            return result;
        }

        ,
        /**
         * Math.log10
     * @edition ECMA-262 6th Edition, 15.8.2.19
     *
     * Returns an implementation-dependent approximation to the base 10 logarithm of <value>
     *
         * @param {number} value
         * @return {number}
     *
     * @example: Math.log10(10) // 0.9999999999999999
         */
        "log10": function(value) {
            var result = examValue(value = +value, EBVS.IS_ZERO_RETURN_NEG_INFINITY | EBVS.IS_POS_ONE_RETURN_ZERO | EBVS.IS_POS_INFINITY);
            if( result === true ) {
                if( value < 0 ) {
                    return NaN;
                }

                return Math.log(value) * Math.LOG10E;//Note: Math.LOG10E != 1 / Math.LN10 ( 1 / Math.LN10 - Math.LOG10E = -5.551115123125783e-17 in Google Chrome)
            }
            return result;
        }

        ,
        /**
         * from http://www.johndcook.com/cpp_log_one_plus_x.html
     *
     * Math.log1p
     * @edition ECMA-262 6th Edition, 15.8.2.21
     *
     * Returns an implementation-dependent approximation to the natural logarithm of 1 + <value>.
     * The result is computed in a way that is accurate even when the value of <value> is close to zero.
     *
         * @param {number} value
         * @return {number}
     *
     * @example: Math.log1p(10) // 2.3978952727983707
         */
        "log1p": function(value) {
            var result = examValue(value = +value, EBVS.IS_ZERO | EBVS.IS_NEG_ONE_RETURN_NEG_INFINITY | EBVS.IS_POS_INFINITY);
            if( result === true ) {
                if( value < -1 ) {
                    return NaN;
                }
        /* In EBVS.IS_ZERO
        if( value === -0 ) {
          return -0;
        }*/

        if( value > -1e-4 && value < 1e-4 ) {
          return (-0.5 * value + 1) * value;
        }

        return Math.log(1 + value);
            }
            return result;
        }

        ,
        /**
         * Math.sign
     * @edition ECMA-262 6th Edition, 15.8.2.31
     *
     * Returns the sign of the <value>, indicating whether <value> is positive, negative or zero
     *
         * @param {number} value
         * @return {number}
     *
     * @example: Math.sign(12); // 1
         */
        "sign": function(value) {
            value = +value;

      return value < 0 ?
        -1
        :
        value > 0 ?
          1
          :
          value
      ;
        }

        ,
        /**
         * Math.sinh
     * @edition ECMA-262 6th Edition, 15.8.2.24
     *
     * Returns an implementation-dependent approximation to the hyperbolic sine of <value>
     *
         * @param {number} value
         * @return {number}
     *
     * @example: Math.sinh(10) // 11013.232874703393
         */
        "sinh": function(value) {
            var result = examValue(value = +value, EBVS.IS_ZERO | EBVS.IS_POS_INFINITY | EBVS.IS_NEG_INFINITY);
            if( result === true ) {
                return (Math.exp(value) - Math.exp(-value)) / 2;
            }
            return result;
        }

        ,
        /**
         * Math.tanh
     * @edition ECMA-262 6th Edition, 15.8.2.25
     *
     * Returns an implementation-dependent approximation to the hyperbolic tangent of <value>
     *
         * @param {number} value
         * @return {number}
     *
     * @example: Math.tanh(10) // 0.9999999958776926
         */
        "tanh": function(value) {
            var result = examValue(value = +value, EBVS.IS_ZERO | EBVS.IS_NEG_INFINITY_RETURN_NEG_ONE)
        , p1
        , p2
      ;
            if( result === true ) {
                if( value === Infinity ) {
                    return 1;
                }

        p1 = Math.exp(value);
        p2 = Math.exp(-value);

                return (p1 - p2) / (p1 + p2);
            }
            return result;
        }

        ,
        /**
         * Math.trunc
     * @edition ECMA-262 6th Edition, 15.8.2.30
     *
     * Returns the integral part of the number <value>, removing any fractional digits.
     * If <value> is already an integer, the result is <value>
     *
         * @param {number} value
         * @return {number}
     *
     * @example: Math.trunc(1.1) // 1
         */
        "trunc": function(value) {
            var result = examValue(value = +value, EBVS.IS_ZERO | EBVS.IS_POS_INFINITY | EBVS.IS_NEG_INFINITY);
            if( result === true ) {
                //return ~~value;//~~ operator doesn't work with big numbers
        //return value < 0 ? Math.ceil(value) : Math.floor(value);
                return value - value % 1;
            }
            return result;
        }

    ,

    /**
     * https://developer.mozilla.org/en-US/docs/JavaScript/Reference/Global_Objects/Math/imul
     * Non-standard
     *
     * This operations returns the result of the C-like 32-bit multiplication of the two parameters.
     * Math.imul allows for fast 32-bit integer multiplication with C-like semantics. This feature is useful for projects like Emscripten.
     * @param {number} a First number.
     * @param {number} b Second number.
     * @return {number}
     */
    "imul": function(a, b) {
      var ah  = (a >>> 16) & 0xffff;
      var al = a & 0xffff;
      var bh  = (b >>> 16) & 0xffff;
      var bl = b & 0xffff;
      // the shift by 0 fixes the sign on the high part
      // the final |0 converts the unsigned value into a signed value
      return ((al * bl) + (((ah * bl + al * bh) << 16) >>> 0)|0);
    }
    });

}();
}//if( __GCC__ECMA_SCRIPT6__ && __GCC__ECMA_SCRIPT6_MATH__ )
/*  >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>  Math  ========================================  */
/*  ======================================================================================  */

/*  ======================================================================================  */
/*  ======================================  Events  <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<  */

if(__GCC__DOM_API_POLYFILL__ && __GCC__DOM_API_POLYFILL_DOM_EVENTS_LVL3__) {
  // new Event(...) and new CustomEvent(...) from github.com/Raynos/DOM-shim/ with my fixing
  // Chrome throws error if using Error
  // IE9 says Event is an object and not a function -.-
  // IE8 doesn't like it and gives a different error messsage!
  // Firefox also says no
  // Safari says me too, me too!
  // Opera throws a DOM exception instead ¬_¬
  /**
   * @constructor
   * @param {string} type
   * @param {Object=} dict
   */
  _Event = function (type, dict) {// Event constructor
    var e = document.createEvent("Events");

    dict = dict || {};
    e.initEvent(type, dict.bubbles || false, dict.cancelable || false);
    if(!("isTrusted" in e))e.isTrusted = false;

    return e;
  };

  try {
    _Event_prototype = Event.prototype;
    new Event("click");
  } catch (e) {
    global["Event"] = _Event;

    if(_Event_prototype) {
      _Event.prototype = _Event_prototype;
    }
    else if(__GCC__LEGACY_BROWSERS_SUPPORT__ && __GCC__LEGACY_BROWSERS_SUPPORT__IELT9__) {
      //IE < 8 has no Event.prototype
      _Event_prototype = _Event.prototype;
    }
  }

  // Chrome calling .initEvent on a CustomEvent object is a no-no
  // IE9 doesn't like it either
  // IE8 says no in its own special way.
  // Firefox agrees this cannot be done
  // Safari says lul wut?
  // Opera says have another DOM exception!
  /**
   * @constructor
   * @param {string} type
   * @param {Object=} dict
   */
  _CustomEvent = function (type, dict) {// CustomEvent constructor
    var e
      , _detail
    ;
    try {
      e = document.createEvent("CustomEvent");
    }
    catch(err) {//FF 3.6 cant create "CustomEvent"
      e = document.createEvent("Event");
    }

    dict = dict || {};
    _detail = dict.detail !== void 0 ? dict.detail : null;
    (e.initCustomEvent || (e.detail = _detail, e.initEvent)).call
      (e, type, dict.bubbles || false, dict.cancelable || false, _detail);
    if(!("isTrusted" in e))e.isTrusted = false;

    return e;
  };

  try {
    _Custom_Event_prototype = (global["CustomEvent"] || Event).prototype;//use global to prevent Exception if the is not CustomEvent || CustomEvent.prototype
    new CustomEvent("magic");
  } catch (e) {
    global["CustomEvent"] = _CustomEvent;

    if(_Custom_Event_prototype || _Event_prototype)_CustomEvent.prototype = _Custom_Event_prototype || _Event_prototype;//The is no CustomEvent.prototype in IE < 8
  }
}//if(__GCC__DOM_API_POLYFILL__ && __GCC__DOM_API_POLYFILL_DOM_EVENTS_LVL3__)
else {
  _Event_prototype = Event.prototype;
}

if( __GCC__LEGACY_BROWSERS_SUPPORT__ ) {

//create temporary event for feature detection
try {
  _tmp_ = document.createEvent("Event")
}
catch(e) {
  _tmp_ = {};
}

//Browser not implement Event.prototype.defaultPrevented
if( !("defaultPrevented" in _tmp_) ) {
  Object.defineProperty(_Event_prototype, "defaultPrevented", {"value" : false});
  _native_preventDefault = _Event_prototype.preventDefault;
  _Event_prototype.preventDefault = function() {
    this["defaultPrevented"] = true;
    _native_preventDefault.apply(this, arguments);
  };
}

}//if( __GCC__LEGACY_BROWSERS_SUPPORT__ )

//Browser not implement Event.prototype.stopImmediatePropagation
if(__GCC__LEGACY_BROWSERS_SUPPORT__ && __GCC__LEGACY_BROWSERS_SUPPORT__OPERA_LT_12_10__ && !("stopImmediatePropagation" in _tmp_)) {
  implementation_stopImmediatePropagation = function(e) {
    var listener = this._listener,
      thisObj = this._this;

    if( typeof listener !== "function" ) {
      if( listener && "handleEvent" in listener ) {
        thisObj = listener;
        listener = listener.handleEvent;
      }
      else return;
    }

    if( e.timeStamp && e["__stopNow"] === e.timeStamp ) {
      e.stopPropagation();
    }
    else return listener.apply(thisObj, arguments);
  };

  _Event_prototype["stopImmediatePropagation"] = function() {
    this["__stopNow"] = (this.timeStamp || (this.timeStamp = (new Date).getTime()));
  }
}

//fix [add|remove]EventListener for all browsers that support it natively
if(__GCC__LEGACY_BROWSERS_SUPPORT__ && __GCC__LEGACY_BROWSERS_SUPPORT__OPERA_LT_12_10__ && "addEventListener" in _testElement &&
  !_testElement.addEventListener["__shim__"]//Indicator that this is not native implementation
  ) {
    // FF fails when you "forgot" the optional parameter for addEventListener and removeEventListener
  // Arg!!! FF 3.6 Unable to override addEventListener https://bugzilla.mozilla.org/show_bug.cgi?id=428229
  // Opera didn't do anything without optional parameter
  // Opera do not discarded multiple identical EventListeners are registered on the same EventTarget with the same parameters
  _tmp_ = 0;

  try {
    _tmp_function = function () {
      _tmp_++
    };
    _testElement.addEventListener("click", _tmp_function);
    _testElement.addEventListener("click", _tmp_function);
    if( _testElement.click ) {// NO: Opera 10.10
      _testElement.click();//testing
    }
    else {
      _testElement.dispatchEvent(new _Event("click"));
    }
  } catch (e) { }

  if(_tmp_ == 0 || _tmp_ == 2 || implementation_stopImmediatePropagation) {//fixEventListenerAll
    (function() {//closure
      var _addEventListener_dublicate_bug = _tmp_ == 2
          /** @const @type {string} */
        , _event_eventsUUID = "_e_8vj"
      ;

      _Array_forEach_.call(
        [global["HTMLDocument"] && global["HTMLDocument"].prototype || global["document"],
         global["Window"] && global["Window"].prototype || global,
         _Node_prototype],
        function (elementToFix) {
          if(elementToFix) {
            var old_addEventListener = elementToFix.addEventListener
              , old_removeEventListener = elementToFix.removeEventListener
            ;

            if(old_addEventListener) {
              elementToFix.addEventListener = function(type, listener, useCapture) {
                var _,
                  _eventsUUID;

                useCapture = useCapture || false;

                if(_addEventListener_dublicate_bug || implementation_stopImmediatePropagation) {
                  _eventsUUID = _event_eventsUUID + (useCapture ? "-" : "") + (listener[UUID_PROP_NAME] || (listener[UUID_PROP_NAME] = ++UUID)) + type

                  if(!(_ = this["_"]))_ = this["_"] = {};
                  //If multiple identical EventListeners are registered on the same EventTarget with the same parameters, the duplicate instances are discarded. They do not cause the EventListener to be called twice, and since the duplicates are discarded, they do not need to be removed manually with the removeEventListener method.
                  if(_eventsUUID in _)return;

                  listener = implementation_stopImmediatePropagation ? (
                    _[_eventsUUID] = _fastUnsafe_Function_bind_.call(implementation_stopImmediatePropagation, {_listener : listener, _this : this})
                  ) : (_[_eventsUUID] = void 0), listener;
                }

                return old_addEventListener.call(this, type, listener, useCapture);
              };

              //elementToFix.addEventListener.__shim__ = true;
              if(old_removeEventListener)elementToFix.removeEventListener = function(type, listener, useCapture) {
                var _,
                  _eventsUUID;

                useCapture = useCapture || false;

                if(_addEventListener_dublicate_bug || implementation_stopImmediatePropagation) {
                  _ = this["_"];
                  if(_ && _[_eventsUUID = _event_eventsUUID + (useCapture ? "-" : "") + listener[UUID_PROP_NAME] + type]) {
                    listener = _[_eventsUUID];
                    delete _[_eventsUUID];
                  }
                }

                return old_removeEventListener.call(this, type, listener, useCapture);
              };
              //elementToFix.removeEventListener.__shim__ = true;
            }
          }
        }
      );
        })();
  }
}

if(DEBUG && !document.addEventListener) {
  console.error("[add|remove]EventListener not supported")
}

/*  >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>  Events  ======================================  */
/*  ======================================================================================  */



/*  =======================================================================================  */
/*  =================================  Utils.Dom  <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<  */

if(__GCC__DOM_API_POLYFILL__ && (__GCC__DOM_API_POLYFILL_CLASSLIST__ || __GCC__INCLUDE_EXTRAS__DOMSTRINGCOLLECTION__)) {
/**
 * DOMStringCollection
 * DOMSettableTokenList like object
 * http://www.w3.org/TR/html5/common-dom-interfaces.html#domsettabletokenlist-0
 * @param {Function} getter callback for onchange event
 * @param {Function} setter callback for onchange event
 * @param {Object} object_this context of onchange function
 * @constructor
 */
DOMStringCollection = function(getter, setter, object_this) {
  /**
   * Event fired when any change apply to the object
   */
  this["__getter__"] = _fastUnsafe_Function_bind_.call(getter, object_this);
  this["__setter__"] = _fastUnsafe_Function_bind_.call(setter, object_this);
  this["length"] = 0;
  this["value"] = "";

  this.DOMStringCollection_check_currentValue();
};
/**
 * @param {DOMStringCollection} thisObj
 * @param {string} _string
 */
DOMStringCollection_init = function(thisObj, _string) {
  var string = _string || ""//default value
    , isChange = !!thisObj.length
  ;

  if(isChange) {
    while(thisObj.length > 0) {
      delete thisObj[--thisObj.length];
    }

    thisObj["value"] = "";
  }

  if(string) {
    if(string = _String_trim_.call(string)) {
      _String_split_.call(string, /\s+/g).forEach(DOMStringCollection_init.add, thisObj);
    }
    thisObj["value"] = _string;//empty value should stringify to contain the attribute's whitespace
  }

  if(isChange && thisObj["__setter__"])thisObj["__setter__"](thisObj["value"]);
};
/**
 * @param {string} token
 * @this {DOMStringCollection}
 */
DOMStringCollection_init.add = function(token) {
  this[this.length++] = token;
};

_append(DOMStringCollection.prototype, /** @lends {DOMStringCollection.prototype} */{
  DOMStringCollection_check_currentValue : function() {
    var string = this["__getter__"]();
    if(string !== this["value"]) {
      DOMStringCollection_init(this, string);
    }
  },
  DOMStringCollection_check_Token_and_argumentsCount : function(token, argumentsCount) {
    if(argumentsCount === 0) {
      _throwDOMException("WRONG_ARGUMENTS_ERR");
    }

    if(token === "")_throwDOMException("SYNTAX_ERR");
    if(/\s+/g.test(token))_throwDOMException("INVALID_CHARACTER_ERR");
  },
  "add": function() {
    var tokens = arguments
      , i = 0
      , l = tokens.length
      , token
      , thisObj = this
      , currentValue
      , prevValue
      , updated = false
    ;

    this.DOMStringCollection_check_currentValue();
    this.DOMStringCollection_check_Token_and_argumentsCount(null, l);

    currentValue = thisObj["value"];
    prevValue = " " + currentValue + " ";

    do {
      token = tokens[i] + "";

      this.DOMStringCollection_check_Token_and_argumentsCount(token);

      if( !_String_contains_.call(prevValue, " " + token + " ") ) { // not contains
        currentValue += ((i > 0 || currentValue && !currentValue.match(/\s+$/g) ? " " : "") + token);

        this[this.length++] = token;

        updated = true;
      }
    }
    while(++i < l);

    if( updated ) {
      thisObj["value"] = currentValue;
      if(thisObj["__setter__"])thisObj["__setter__"](thisObj["value"]);
    }
  },
  "remove": function() {
    var tokens = arguments
      , i = 0
      , l = tokens.length
      , token
      , thisObj = this
      , currentValue
      , currentValueLength
      , itemsArray
      , newItemsArray = []
      , filterObject = {}
    ;

    this.DOMStringCollection_check_currentValue();
    this.DOMStringCollection_check_Token_and_argumentsCount(null, l);

    currentValue = thisObj["value"];
    currentValueLength = currentValue.length;

    do {
      token = tokens[i] + "";

      this.DOMStringCollection_check_Token_and_argumentsCount(token);

      filterObject[token] = null;
    }
    while(++i < l);

    itemsArray = _String_split_.call(currentValue, " ");
    currentValue = "";
    for(i = 0, l = itemsArray.length ; i < l ; ++i) {
      if(!((token = itemsArray[i]) in filterObject)) {
        newItemsArray.push(token);
        currentValue += ((i ? " " : "") + token);
      }
    }

    if( currentValueLength !== currentValue.length ) {
      for(i = thisObj.length - 1 ; i >= 0 ; --i) {
        if(!(thisObj[i] = newItemsArray[i])) {
          thisObj.length--;
          delete thisObj[i];
        }
      }

      thisObj["value"] = currentValue;
      if(thisObj["__setter__"])thisObj["__setter__"](thisObj["value"]);
    }
  },
  "contains": function(token) {
    this.DOMStringCollection_check_Token_and_argumentsCount(token, arguments.length);
    this.DOMStringCollection_check_currentValue();

    return _String_contains_.call(" " + this["value"] + " ", " " + token + " ");
  },
  "item": function(index) {
    this.DOMStringCollection_check_currentValue();

    return this[index] || null;
  },
  "toggle": _classList_toggle
});

DOMStringCollection.prototype.toString = function() {//_append function do not overwrite Object.prototype.toString
  this.DOMStringCollection_check_currentValue();

  return this["value"] || ""
};
}//if(__GCC__DOM_API_POLYFILL__ && (__GCC__DOM_API_POLYFILL_CLASSLIST__ || __GCC__INCLUDE_EXTRAS__DOMSTRINGCOLLECTION__))

if(__GCC__INCLUDE_EXTRAS__ && __GCC__INCLUDE_EXTRAS__DOMSTRINGCOLLECTION__) {//Export DOMStringCollection
  global["DOMStringCollection"] = DOMStringCollection;
}


/*  >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>  Utils.Dom  ==================================  */
/*  =======================================================================================  */


/*  ======================================================================================  */
/*  ========================================  DOM  <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<  */

if(__GCC__LEGACY_BROWSERS_SUPPORT__) {

//[Opera lt 12]
if(__GCC__LEGACY_BROWSERS_SUPPORT__OPERA_LT_12_10__) {
    _tmp_ = "AT_TARGET";
  if( !_Event_prototype[_tmp_] || Event[_tmp_] ) {
        _tmp_ = {
            "CAPTURING_PHASE": 1
            , "AT_TARGET": 2
            , "BUBBLING_PHASE": 3
        };
        _append(_Event_prototype, _tmp_);
        _append(Event, _tmp_);
  }
}//if(__GCC__LEGACY_BROWSERS_SUPPORT__OPERA_LT_12_50__)


// window.getComputedStyle fix
//FF say that pseudoElt param is required
if( global.getComputedStyle ) {
  try {
    global.getComputedStyle(_testElement)
  }
  catch(e) {
    global.getComputedStyle = _fastUnsafe_Function_bind_.call(function(obj, pseudoElt) {
      return this.call(global, obj, pseudoElt || null)
    }, global.getComputedStyle);
  }
}
}//if(__GCC__LEGACY_BROWSERS_SUPPORT__)
/*  ======================================================================================  */
/*  >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>  DOM  =======================================  */

/*  ======================================================================================  */
/*  ================================  Element.prototype  <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<  */


/*  Some of from https://github.com/Raynos/DOM-shim/  */
if(__GCC__DOM_API_POLYFILL__ && (__GCC__DOM_API_POLYFILL_CLASSLIST__ || __GCC__DOM_API_POLYFILL_CLASSLIST_FIX__)) {

_tmp_ = !("classList" in _testElement) ?
  void 0//doesn't support classList
  :
  (_testElement["classList"]["add"](1, 2), _testElement["classList"]["contains"](2)) && true || false//false - support old version of classList
;

//https://developer.mozilla.org/en/DOM/Element.classList
//Add JS 1.8 Element property classList
// IE < 8 support in a.ielt8.js and a.ielt8.htc
if( !_tmp_ ) {
  if( __GCC__DOM_API_POLYFILL_CLASSLIST__
        && _tmp_ === void 0
    ) {
        S_ELEMENT_CACHED_CLASSLIST_NAME = "_ccl_";

    DOMStringCollection_setNodeClassName = function(newValue) {
      this.className = newValue;
    };
    DOMStringCollection_getNodeClassName = function() {
      return this.className;
    };

    Object.defineProperty(_Element_prototype, "classList", {
      "get": function() {
        if(!this.tagName)return void 0;

        var thisObj = this,
          cont = thisObj["_"] || (thisObj["_"] = {});//Put S_ELEMENT_CACHED_CLASSLIST_NAME in container "_";

        if(!cont[S_ELEMENT_CACHED_CLASSLIST_NAME]) {
          cont[S_ELEMENT_CACHED_CLASSLIST_NAME] =
            new DOMStringCollection(DOMStringCollection_getNodeClassName, DOMStringCollection_setNodeClassName, thisObj);
        }

        return cont[S_ELEMENT_CACHED_CLASSLIST_NAME];
      }
    });
  }
  else if( __GCC__DOM_API_POLYFILL_CLASSLIST_FIX__
        && _tmp_ === false
        && (_tmp_ = global["DOMTokenList"])
        && (_tmp_ = _tmp_.prototype)
    ) {//Polyfill for nevest DOM[Settable]TokenList standart
    //see http://dom.spec.whatwg.org/#dom-domtokenlist-toggle
    // Polyfill:
    //  DOMTokenList.prototype.add(tokens...)
    //  DOMTokenList.prototype.remove(tokens...)
    //  DOMTokenList.prototype.toggle(token, force)
    //  [bugfix, opera] Old Opera (11.50 at least) not supported null as class name in add/remove/...etc functions
    !function(_old_add, _old_remove, class_helper) {
      _tmp_["add"] = function() {
        _Array_forEach_.call(_Array_map_.call(arguments, class_helper), _old_add, this);
      };
      _tmp_["remove"] = function() {
        _Array_forEach_.call(_Array_map_.call(arguments, class_helper), _old_remove, this);
      };
      _tmp_["toggle"] = _classList_toggle;
    }.call(null, _tmp_["add"], _tmp_["remove"], function(a){ return a + "" });
  }
}
}//if(__GCC__DOM_API_POLYFILL__ && __GCC__DOM_API_POLYFILL_CLASSLIST__)

if(__GCC__DOM_API_POLYFILL__ && __GCC__DOM_API_POLYFILL_DOM3_API__) {
//Node.prototype.parentElement
//https://developer.mozilla.org/en/DOM/Node.parentElement
//[FF lt 9]
if( !("parentElement" in _testElement) ) {
  Object.defineProperty(_Element_prototype, "parentElement", {"get" : function() {
    var parent = this.parentNode;

      if(parent && parent.nodeType === 1)return parent;

      return null;
  }});
}

//https://developer.mozilla.org/En/DOM/Node.contains
//[FF lt 9]
if( !("contains" in _testElement) ) {
    /**
     * Node.prototype.contains
     * @param {Node} node
     * @return {boolean}
     */
  global["Node"].prototype.contains = function (node) {
    return !!(this.compareDocumentPosition(node) & 16)
  };
}

if( !("insertAdjacentHTML" in _testElement) ) {
/**
 * HTMLElement.prototype.insertAdjacentHTML
 * https://developer.mozilla.org/En/DOM/Element.insertAdjacentHTML, http://html5.org/specs/dom-parsing.html#insertadjacenthtml(), https://gist.github.com/1276030 by https://gist.github.com/eligrey
 * insertAdjacentHTML() parses the specified text as HTML or XML and inserts the resulting nodes into the DOM tree at a specified position. It does not reparse the element it is being used on and thus it does not corrupt the existing elements inside the element. This, and avoiding the extra step of serialization make it much faster than direct innerHTML manipulation.
 * @requires Element.prototype.before, Element.prototype.prepend, Element.prototype.append, Element.prototype.after
 * @param {string} position is the position relative to the element, and must be one of the following strings:
 *  'beforebegin' - Before the element itself.
 *  'afterbegin' - Just inside the element, before its first child.
 *  'beforeend' - Just inside the element, after its last child.
 *  'afterend' - After the element itself.
 *  Note: The beforebegin and afterend positions work only if the node is in a tree and has an element parent.
 * @param {string} html is the string to be parsed as HTML or XML and inserted into the tree.
 */
  global["HTMLElement"].prototype.insertAdjacentHTML = function(position, html) {
    var ref = this
            , container = ref.ownerDocument.createElement("_")
            , nodes
            , translate = {
        "beforebegin" : "before",
        "afterbegin" : "prepend",
        "beforeend" : "append",
        "afterend" : "after"
      }
            , func
        ;

    container.innerHTML = html;
    nodes = container.childNodes;

    if( nodes
            && nodes.length
            && ( func = ref[translate[position]])
        ) {
      func.apply(this, nodes)
    }

    nodes = container = null;
  };
}


// Emuleted HTMLTimeElement
// TODO:: need more work
/*
if(!(global["HTMLTimeElement"] && global["HTMLTimeElement"].prototype))
Object.defineProperty((global["HTMLUnknownElement"] && global["HTMLUnknownElement"].prototype) || _Element_prototype,
  "dateTime", {
  "get" : function() {
    var thisObj = this,
      elTag = thisObj.tagName;

    return thisObj.tagName.toUpperCase() == "TIME" ? (thisObj.getAttribute("datetime") || "") : void 0;
  },
  "set" : function(val) {
    var thisObj = this,
      elTag = thisObj.tagName;

    if(thisObj.tagName.toUpperCase() == "TIME") {
      thisObj.setAttribute("datetime", val);
      return val;
    }

    return null;
  }
});
*/

if( __GCC__DOM_API_POLYFILL_DOM3_API_BUGFIXING__ ) {
// document.importNode
// IE9 thinks the argument is not optional
// FF thinks the argument is not optional
// Opera agress that its not optional
// IE < 9 has javascript implimentation marked as `shim`
// FROM https://github.com/Raynos/DOM-shim/blob/master/src/all/bugs.js
if(document.importNode && !document.importNode["shim"])
try {
  document.importNode(_testElement);
} catch (e) {
  var importNode = document.importNode;
  delete document.importNode;
  document.importNode = function (node, bool) {
    if (bool === void 0) {
      bool = true;
    }
    return importNode.call(this, node, bool);
  }
}

// Node.prototype.cloneNode
// Firefox fails on .cloneNode thinking argument is not optional
// Opera agress that its not optional.
// FROM https://github.com/Raynos/DOM-shim/blob/master/src/all/bugs.js
try {
  _testElement.cloneNode();
} catch (e) {
  [
    _Node_prototype,
    Comment && Comment.prototype,
    _Element_prototype,
    ProcessingInstruction && ProcessingInstruction.prototype,
    Document.prototype,
    DocumentType && DocumentType.prototype,
    DocumentFragment.prototype
  ].forEach(function(proto) {
      if(proto) {
        var cloneNode = proto.cloneNode;
        delete proto.cloneNode;
        proto.cloneNode = function _cloneNode(bool) {
          if (bool === void 0) {
            bool = true;
          }
          return cloneNode.call(this, bool);
        };
      }
    });
}
}//if( __GCC__DOM_API_POLYFILL_DOM3_API_BUGFIXING__ )

//Element.prototype.matchesSelector
if(!_Element_prototype.matchesSelector) {
  _Element_prototype.matchesSelector =
    _Element_prototype["webkitMatchesSelector"]
    || _Element_prototype["mozMatchesSelector"]
    || _Element_prototype["msMatchesSelector"]
    || _Element_prototype["oMatchesSelector"]
    || __GCC__LEGACY_BROWSERS_SUPPORT__ && function(selector, refNodes) {
      if(!selector)return false;
      if(selector === "*")return true;

      var thisObj,
        parent,
        i,
        k = 0,
        str,
        rules,
        tmp,
        match;

      if(refNodes) {
        //fast and unsafe isIterable. Note: <form> have 'length' prop
        if("length" in refNodes && !("nodeType" in refNodes)) {
          thisObj = refNodes[0];
        }
        else {
          thisObj = refNodes;
          refNodes = void 0;
        }
      }
      else thisObj = this;

      do {
        match = false;
        if(thisObj === document.documentElement)match = selector === ":root";
        else if(thisObj === document.body)match = selector.toUpperCase() === "BODY";

        if(!match) {
          selector = _String_trim_.call(selector.replace(RE__matchSelector__doubleSpaces, "$1"));

          if(rules = selector.match(RE__selector__easySelector)) {
            switch (selector.charAt(0)) {
              case '#':
                match = thisObj.id === selector.slice(1);
                break;
              default:
                match = !rules[1] || (!("tagName" in thisObj) || thisObj.tagName.toUpperCase() === rules[1].toUpperCase());
                if(match && rules[2]) {
                  i = -1;
                  tmp = rules[2].slice(1).split(".");
                  str = " " + thisObj.className + " ";
                  while(tmp[++i] && match) {
                    match = _String_contains_.call(str, " " + tmp[i] + " ");
                  }
                }
            }
          }
          else {
            if(!/([,>+~ ])/.test(selector) && (parent = thisObj.parentNode) && parent.querySelector) {
              match = parent.querySelector(selector) === thisObj;
            }

            if(!match && (parent = thisObj.ownerDocument)) {
              tmp = parent.querySelectorAll(selector);
              i = -1;
              while(!match && tmp[++i]) {
                match = tmp[i] === thisObj;
              }
            }
          }
        }
      }
      while(match && refNodes && (thisObj = refNodes[++k]));

      return refNodes && "length" in refNodes ? match && refNodes.length == k : match;
    }
}
if(!document.documentElement.matchesSelector)document.documentElement.matchesSelector = _Element_prototype.matchesSelector;

}//if(__GCC__DOM_API_POLYFILL__ && __GCC__DOM_API_POLYFILL_DOM3_API__)

//New DOM4 API
if(__GCC__DOM_API_POLYFILL__ && __GCC__DOM_API_POLYFILL_DOM4_API__) {

//Element.prototype.matches
if(!("matches" in _Element_prototype))_Element_prototype["matches"] = document.documentElement["matches"] =
  _Element_prototype.matchesSelector
  || !__GCC__DOM_API_POLYFILL_DOM3_API__
    && (
      _Element_prototype["webkitMatchesSelector"]
      || _Element_prototype["mozMatchesSelector"]
      || _Element_prototype["msMatchesSelector"]
      || _Element_prototype["oMatchesSelector"]
  )
;

if( !_testElement["prepend"] ) {
  dom4_mutationMacro = function(nodes) {
    var resultNode = null
      , i = 0
      , maxLength = nodes.length
    ;

    nodes = _Array_map_.call(nodes, dom4_mutationMacro.replaceStringWithTextNode);

    if (maxLength === 1) {
      return nodes[0];
    } else {
      resultNode = document.createDocumentFragment();

            //nodes can be a live NodeList so we can't iterate NodeList directly
            nodes = _Array_from(nodes);

      for(i = 0 ; i < maxLength ; ++i) {
        resultNode.appendChild(nodes[i]);
      }
    }

    return resultNode;
  };
    dom4_mutationMacro.replaceStringWithTextNode = function(string) {
        return typeof string === "string" ?
            document.createTextNode(string)
            :
            string
        ;
    };

    _append(_Element_prototype, /** @lends {Element.prototype} */{
        //Element.prototype.after
        "after": function () {
            this.parentNode && this.parentNode.insertBefore(dom4_mutationMacro(arguments), this.nextSibling);
        }

        ,
        //Element.prototype.before
        "before": function () {
            this.parentNode && this.parentNode.insertBefore(dom4_mutationMacro(arguments), this);
        }

        ,
        //Element.prototype.append
        "append": function () {
            this.appendChild(dom4_mutationMacro(arguments));
        }

        ,
        //Element.prototype.prepend
        "prepend": function () {
            this.insertBefore(dom4_mutationMacro(arguments), this.firstChild);
        }

        ,
        //Element.prototype.replace
        "replace": function () {
            this.parentNode && this.parentNode.replaceChild(dom4_mutationMacro(arguments), this);
        }

        ,
        //Node.prototype.remove
        //Google Chrome has Node.prototype.remove from version 24 (or 23)
        "remove": function () {
            this.parentNode && this.parentNode.removeChild(this);
        }
    });

  //Interface CharacterData
  if( _tmp_ = (global["CharacterData"] || global["Text"]) ) {
    _tmp_ = _tmp_.prototype;
  }
  if( !_tmp_ ) {
    _tmp_ = _Node_prototype;//Fallback for very old browsers
  }
  if( _tmp_ ) {
    //CharacterData.prototype.after
    if(!_tmp_["after"])_tmp_["after"] = _Element_prototype["after"];
    //CharacterData.prototype.before
    if(!_tmp_["before"])_tmp_["before"] = _Element_prototype["before"];
    //CharacterData.prototype.replace
    if(!_tmp_["replace"])_tmp_["replace"] = _Element_prototype["replace"];
    //CharacterData.prototype.remove
    if(!_tmp_["remove"])_tmp_["remove"] = _Element_prototype["remove"];
  }

  //Document.prototype.prepend
  //DocumentFragment.prototype.prepend
  //document.prepend
  //Document.prototype.append
  //DocumentFragment.prototype.append
  //document.append
  if( !("prepend" in document) && (_tmp_ = global["Document"]) && (_tmp_ = _tmp_.prototype) ) {
    document["prepend"] = _tmp_["prepend"] = function() {
      _Element_prototype["prepend"].apply(this.documentElement, arguments);
    };
    document["append"] = _tmp_["append"] = function() {
      _Element_prototype["append"].apply(this.documentElement, arguments);
    };
    global["DocumentFragment"].prototype["prepend"] = _Element_prototype["prepend"];
    global["DocumentFragment"].prototype["append"] = _Element_prototype["append"];
  }
}

//The remove() method from Element Interface is overwritten in HTMLSelectElement Interface
//https://www.w3.org/Bugs/Public/show_bug.cgi?id=20720
//HTMLSelectElement.prototype.remove
_tmp_ = document.createElement("select");
_tmp_.innerHTML = "<option>0</option>";
try {
  _tmp_.remove();
}
catch(e) {
  _tmp_ = false
}
if( _tmp_ === false || !_tmp_.childNodes.length ) {
  if((_tmp_ = global["HTMLSelectElement"]) && (_tmp_ = _tmp_.prototype) && ("remove" in _tmp_)) {
    !function(_HTMLSelectElement_prototype, _HTMLSelectElement_remove) {
            Object.defineProperty(_HTMLSelectElement_prototype, "remove", {"value": function(index) {
                if(arguments.length)_HTMLSelectElement_remove.apply(this, arguments);
                else Element.prototype["remove"].call(this);
            }});
    }.call(null, _tmp_, _tmp_["remove"]);
  }
}

if(__GCC__DOM_API_POLYFILL_DOM4_API_FIND__){

//Document.prototype.find
//DocumentFragment.prototype.find
//document.find
//Document.prototype.findAll
//DocumentFragment.prototype.findAll
//document.findAll
if( !("find" in document) && "querySelector" in _testElement ) {
  RE_document_find_scopedReplacer = /(\:scope)(?=[ >~+])/gi;

  (global["Document"].prototype || document)["find"] = global["DocumentFragment"].prototype["find"] = function(selector, refNodes) {
    refNodes && (("length" in refNodes && !("nodeType" in refNodes)) || (refNodes = [refNodes])) || (refNodes = [this]);

    var result
      , i = 0
      , l = refNodes.length
      , node
    ;

    do {
      node = refNodes[i];
      selector = selector.replace(
                RE_document_find_scopedReplacer, node.nodeType == 9/*Node.DOCUMENT_NODE*/ ?
                    ":root"
                    :
                    function() {
                        return "#" + (node.id || (node.id = "find" + UUID_PROP_NAME + ++UUID));
                    }
                )
            ;

      result = node.querySelector(selector);
    }
    while( !result && ++i < l );

    return result || null;
  };

  (global["Document"].prototype || document)["findAll"] = global["DocumentFragment"].prototype["findAll"] = function(selector, refNodes) {
    refNodes && (("length" in refNodes && !("nodeType" in refNodes)) || (refNodes = [refNodes])) || (refNodes = [this]);

    var result = []
      , i = 0
      , l = refNodes.length
      , node
      , tmpResult
      , node2
      , k
      , n
      , uuid
      , uniqueMap = {}
      , forsed
    ;

    do {
      node = refNodes[i];
      selector = selector.replace(
                RE_document_find_scopedReplacer, node.nodeType == 9/*Node.DOCUMENT_NODE*/ ?
                    ":root"
                    :
                    function() {
                        return "#" + (node.id || (node.id = "find" + UUID_PROP_NAME + ++UUID));
                    }
          )
            ;

      tmpResult = node.querySelectorAll(selector);
      if( l > 1 ) {
        for( k = 0, n = tmpResult.length ; k < n ; ++k ) {
          node2 = tmpResult[k];
          forsed = false;
          uuid = node2.getAttribute("data-" + UUID_PROP_NAME)
            || (
            (forsed = true),
              node2.setAttribute("data-" + UUID_PROP_NAME, uuid = ++UUID),
              uuid
            )
          ;

          if( forsed || !(uuid in uniqueMap) ) {
            uniqueMap[uuid] = null;

            result.push(tmpResult[k]);
          }
        }
      }
      else {
        result = _Array_from(tmpResult);
      }
    }
    while(++i < l);

    return result;
  };
}
//Element.prototype.find
//Element.prototype.findAll
if( !("find" in _Element_prototype) ) {
  //TODO:: add ':scope' support
  // http://lists.w3.org/Archives/Public/public-webapps/2011OctDec/0316.html (Re: QSA, the problem with ":scope", and naming)
  _Element_prototype["find"] = _Element_prototype.querySelector;
  _Element_prototype["findAll"] = _Element_prototype.querySelectorAll;
}

}//if(__GCC__DOM_API_POLYFILL_DOM4_API_FIND__)

}//if(__GCC__DOM_API_POLYFILL__ && __GCC__DOM_API_POLYFILL_DOM4_API__)
/*  >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>  Element.prototype  ==================================  */
/*  ======================================================================================  */

/*  ======================================================================================  */
/*  ================================  HTMLInputElement.prototype  <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<  */
/*  ================================  HTMLButtonElement.prototype  <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<  */
/*  ================================  HTMLKeygenElement.prototype  <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<  */
/*  ================================  HTMLMeterElement.prototype  <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<  */
/*  ================================  HTMLOutputElement.prototype  <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<  */
/*  ================================  HTMLProgressElement.prototype  <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<  */
/*  ================================  HTMLTextAreaElement.prototype  <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<  */
/*  ================================  HTMLSelectElement.prototype  <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<  */

//HTMLInputElement.prototype.labels
//HTMLButtonElement.prototype.labels
//HTMLKeygenElement.prototype.labels
//HTMLMeterElement.prototype.labels
//HTMLOutputElement.prototype.labels
//HTMLProgressElement.prototype.labels
//HTMLTextAreaElement.prototype.labels
//HTMLSelectElement.prototype.labels
//all that methods via Element.prototype.labels
if( __GCC__DOM_API_POLYFILL__ && __GCC__DOM_API_POLYFILL__LABELS_AND_CONTROL_POLYFILL__ ) {
  _labelable_elements = " INPUT BUTTON KEYGEN METER OUTPUT PROGRESS TEXTAREA SELECT ";
  /*
  Implement HTML*Element.labels
  https://developer.mozilla.org/en/DOM/HTMLInputElement
  http://www.w3.org/TR/html5/forms.html#dom-lfe-labels
  */
  if( !("labels" in _document_createElement("input")) )
    Object.defineProperty(_Element_prototype, "labels", {
      enumerable: true,
      "get": function() {
        if( !(_String_contains_.call(_labelable_elements, (" " + this.nodeName + " ").toUpperCase())) ) {
                    return void 0;
                }

        var node = this
                    ,
          /**
           * represents the list of label elements, in [!]tree order[!]
           * @type {Array}
           */
          result = this.id ?
            _Array_from( document.querySelectorAll("label[for='" + this.id + "']") )
                        :
            []

                    , _lastInTreeOrder_index = result.length - 1
                ;

        while( (node = node.parentNode) && (!node["control"] || node["control"] === this) ) {
                    if( node.nodeName.toUpperCase() === "LABEL" ) {
                        while (
                            result[_lastInTreeOrder_index]
                            && result[_lastInTreeOrder_index].compareDocumentPosition(node) & 2//DOCUMENT_POSITION_PRECEDING
                        ) {
                            _lastInTreeOrder_index--;
                        }
                        _Array_splice_.call(result, _lastInTreeOrder_index + 1, 0, node)
                    }
                }

        return result;
      }
    });
}//if(__GCC__DOM_API_POLYFILL__ && __GCC__DOM_API_POLYFILL__LABELS_AND_CONTROL_POLYFILL__)

/*  ======================================================================================  */

/*  ======================================================================================  */
/*  ================================  HTMLLabelElement.prototype  <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<  */

if(__GCC__DOM_API_POLYFILL__ && __GCC__DOM_API_POLYFILL__LABELS_AND_CONTROL_POLYFILL__) {
  /*
  Implement HTMLLabelElement.prototype.control
  https://developer.mozilla.org/en/DOM/HTMLLabelElement
  http://www.w3.org/TR/html5/forms.html#dom-label-control
  */
  if( !("control" in _document_createElement("label")) ) {
    _nodesRecursivelyWalk = function(nodes, cb) {
      for (var i = 0, len = nodes.length; i < len; i++) {
        var node = nodes[i],
          ret = cb(node);
        if (ret) {
          return ret;
        }
        if (node.childNodes && node.childNodes.length > 0) {
          ret = _nodesRecursivelyWalk(node.childNodes, cb);
          if (ret) {
            return ret;
          }
        }
      }
            return null;
    };
        _nodesRecursivelyWalk.labelHelper = function(el) {
            if (_String_contains_.call(_labelable_elements, " " + el.nodeName.toUpperCase() + " ") ) {
                return el
            }
            return null;
        };

    Object.defineProperty(( _tmp_ = global["HTMLLabelElement"]) && _tmp_.prototype || _Element_prototype, "control", {
      enumerable: true,
      "get" : function() {
        if(this.nodeName.toUpperCase() !== "LABEL")
          return void 0;

        if(this.hasAttribute("for"))
          return document.getElementById(this.htmlFor);

        return _nodesRecursivelyWalk(this.childNodes, _nodesRecursivelyWalk.labelHelper);
      }
    });
  }
}//if(__GCC__DOM_API_POLYFILL__ && __GCC__DOM_API_POLYFILL__LABELS_AND_CONTROL_POLYFILL__)

/*  >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>  HTMLLabelElement.prototype  ==================================  */
/*  ======================================================================================  */

/*  ======================================================================================  */
/*  ================================  HTMLSelectElement.prototype  <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<  */

/*
 * HTMLSelectElement.prototype.reversed
 * Reverse Ordered Lists in HTML5
 * a polyfill for the ordered-list reversed attribute
 * Based on https://gist.github.com/1671548
 *
 * http://www.whatwg.org/specs/web-apps/current-work/multipage/grouping-content.html#the-ol-element
 * http://www.whatwg.org/specs/web-apps/current-work/multipage/grouping-content.html#dom-li-value
 * http://www.impressivewebs.com/reverse-ordered-lists-html5/
 * http://html5doctor.com/ol-element-attributes/
 * TODO::
 *  1. Equivalent list-style-type:
    type="1"  decimal (default style)
    type="a"  lower-alpha
    type="A"  upper-alpha
    type="i"  lower-roman
    type="I"  upper-roman
 */
//In strict mode code, functions can only be declared at top level or immediately within another function
if(__GCC__DOM_API_POLYFILL__ && __GCC__DOM_API_POLYFILL__REVERSE_POLYFILL__ && !('reversed' in _document_createElement("ol"))) {
  OL_reversed_Shim = function(list) {
    var reversed = list.getAttribute('reversed')
        , dataReversed = list.getAttribute("data-reversed-p" + UUID_PROP_NAME)
        , children
        , count
    ;

    if( (dataReversed === "on") === (reversed !== null) )return;

    children = list.children;
    count = list.getAttribute('start');

    //check to see if a start attribute is provided
    if( count !== null ) {
      count = +count;

      if(isNaN(count))count = null;
    }

    if(reversed !== null) {
            list.setAttribute("data-reversed-p" + UUID_PROP_NAME, "on");

      //no, this isn't duplication - start will be set to null
      // in the previous if statement if an invalid start attribute
      // is provided
      if( count === null ) {
                count = children.length;
            }

      _Array_forEach_.call(children, function(child) {
        child["value"] = count--;
      });
    }
    else {
            list.removeAttribute("data-reversed-p" + UUID_PROP_NAME);

      if( children[0] ) {
        children[0]["value"] = count || 0;
      }

      _Array_forEach_.call(children, function(child) {
        child.removeAttribute("value");
      });
    }
  };

  Object.defineProperty( (_tmp_ = global["HTMLOListElement"]) && _tmp_.prototype || _Element_prototype, "reversed", {
    "get": function () {
      var thisObj = this;

      if((thisObj.nodeName || "").toUpperCase() !== "OL")return void 0;

      return thisObj.getAttribute('reversed') !== null;
    }
        ,
    /** @param {boolean} value */
    "set": function (value) {
      var thisObj = this;

      if((thisObj.nodeName || "").toUpperCase() !== "OL")return void 0;

      thisObj[(value ? "set" : "remove") + "Attribute"]('reversed', "");

            OL_reversed_Shim(thisObj); //Run shim

      return value;
    }
  });

  //Auto init
  if( __GCC__DOM_API_POLYFILL__REVERSE_POLYFILL__AUTO_INIT__ && document.addEventListener ) {
    OL_reversed_autoInitFunction = function() {
      document.removeEventListener('DOMContentLoaded', OL_reversed_autoInitFunction, false);
      OL_reversed_autoInitFunction = null;
      _Array_forEach_.call(document.getElementsByTagName("ol"), OL_reversed_Shim);
    };
    if(document.readyState == 'complete') {
      OL_reversed_autoInitFunction();
    }
    else {
      document.addEventListener('DOMContentLoaded', OL_reversed_autoInitFunction, false);
    }
  }
}//if(__GCC__DOM_API_POLYFILL__ && __GCC__DOM_API_POLYFILL__REVERSE_POLYFILL__)
/*  >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>  HTMLLabelElement.prototype  ==================================  */
/*  ======================================================================================  */

/*  =======================================================================================  */
/*  ================================  NodeList.prototype  <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<  */

if(__GCC__LEGACY_BROWSERS_SUPPORT__ && __GCC__LEGACY_BROWSERS_SUPPORT__OPERA_LT_12_10__) {
  //TODO::
  // In Opera < 12.10 (document.querySelectorAll("div").constructor == NodeList) == false
}

if(__GCC__DOM_API_POLYFILL__ && __GCC__DOM_API_POLYFILL_DOM4_API__ && __GCC__DOM_API_POLYFILL_DOM4_API_NODELIST_INHERIT_FROM_ARRAY__) {
  //Inherit NodeList from Array
  _Array_forEach_.call([
    // prototypes
    (_tmp_ = global["HTMLAllCollection"]) && _tmp_.prototype
    , (_tmp_ = global["HTMLCollection"]) && _tmp_.prototype
    , (_tmp_ = global["NodeList"]) && _tmp_.prototype
    // instances
    , document.getElementsByClassName && document.getElementsByClassName("")
    , document.querySelectorAll && document.querySelectorAll("#z")
    , document.documentElement.children
  ], function(instance, index) {
      var proto;

      if( index < 3 || (instance && !("map" in instance) && !Array.isArray(instance)) ) {
        if( index < 3 ) {
          proto = instance;
        }
        else {
          //in old FF nodeList_proto.__proto__ != nodeList_proto.constructor.prototype
          proto = instance.__proto__ || instance.constructor && instance.constructor.prototype;
        }

        if( proto && proto !== _Array_prototype_ && proto !== _Object_prototype ) {// Paranoiac mode
          this(proto);
        }
      }
    }, _Array_forEach_.bind(
        [
          "join", "forEach", "every", "some", "map", "filter", "reduce", "reduceRight", "indexOf", "lastIndexOf", "slice", "contains", "find", "findIndex"
          //index: 14
          , "splice", "concat", "reverse", "push", "pop", "shift", "unshift", "sort"
        ]
        //Unsafe:: "splice", "concat", "reverse", "push", "pop", "shift", "unshift", "sort"
        , function(key, index) {
          var value;
          if( !(key in this) ) {
            value = {
              "configurable": true
              , "enumerable": false
              , "writable": true
            };
            if(index < 15) {
              value["value"] = _Array_prototype_[key];
            }
            else {
              value["value"] = function() {
                _throwDOMException("NO_MODIFICATION_ALLOWED_ERR");
              }
            }
            Object.defineProperty(this, key, value);
          }
        }
      )
  );

}//if(__GCC__DOM_API_POLYFILL__ && __GCC__DOM_API_POLYFILL_DOM4_API__ && __GCC__DOM_API_POLYFILL_DOM4_API_NODELIST_INHERIT_FROM_ARRAY__)

//Implements RadioNodeList :- http://www.whatwg.org/specs/web-apps/current-work/multipage/common-dom-interfaces.html#radionodelist
if(__GCC__DOM_API_POLYFILL__ && __GCC__DOM_API_POLYFILL_DOM4_API__ && __GCC__DOM_API_POLYFILL_DOM4_API_RADIONODELIST__) {
    // using _tmp_ variable as only variable we need

    (
        _tmp_ = _document_createElement("form")
    ).innerHTML = "<input type=radio name=t value=1><input type=radio checked name=t value=2>";

    if( _tmp_["t"] && _tmp_["t"]["value"] !== 2 ) {// Browser do not support RadioNodeList
        _tmp_ = (_tmp_ = _tmp_["t"]) && (_tmp_ = _tmp_.constructor) && _tmp_.prototype || (_tmp_ = global["NodeList"]) && _tmp_.prototype;

        if( _tmp_
            && _tmp_ !== Object.prototype//Safari 4 return Collection as a result of form["inputname"], and the prototype of Collection is Object.prototype :(
        ) {
            Object.defineProperty(_tmp_, "value", {
                get: function() {
                    var k = this.length
                        , el
                    ;

                    if( k
                        && (el = this[0])
                        && (el.nodeName + "").toUpperCase() === "INPUT"
                        && ("form" in el)
                    ) {
                        while( el = this[--k] ) {
                            if( el
                                && (el.type + "").toUpperCase() === "RADIO"
                                && el.checked
                            ) {
                                return el.value;
                            }
                        }
                    }

                    return void 0;
                },
                set: function(value) {
          var k = this.length
                        , el
                    ;

                    if( k
                        && (el = this[0])
                        && (el.nodeName + "").toUpperCase() === "INPUT"
                        && ("form" in el)
                    ) {
                        while( el = this[--k] ) {
                            if( el
                                && (el.type + "").toUpperCase() === "RADIO"
                                && el.checked
                            ) {
                                el.value = value;
                                return el.value;
                            }
                        }
                    }

                    return void 0;
                },
                configurable: true
            });
        }
    }
}//if(__GCC__DOM_API_POLYFILL__ && __GCC__DOM_API_POLYFILL_DOM4_API__ && __GCC__DOM_API_POLYFILL_DOM4_API_RADIONODELIST__)
/*  >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>  NodeList.prototype  ==================================  */
/*  ======================================================================================  */


/*  =======================================================================================  */
/*  ========================================  Date  <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<  */
//
// Date
// ====
//

if(__GCC__SCRIPT_BUGFIXING__ && __GCC__SCRIPT_BUGFIXING_DATE__) {
// ES5 15.9.5.43
// http://es5.github.com/#x15.9.5.43
// This function returns a String value represent the instance in time
// represented by this Date object. The format of the String is the Date Time
// string format defined in 15.9.1.15. All fields are present in the String.
// The time zone is always UTC, denoted by the suffix Z. If the time value of
// this object is not a finite Number a RangeError exception is thrown.
if( !_Native_Date.prototype.toISOString
   || (new _Native_Date(-1).toISOString() !== '1969-12-31T23:59:59.999Z')
   || (_String_contains_.call(new _Native_Date(-62198755200000).toISOString(), '-000001'))
) {
    _Native_Date.prototype.toISOString = function() {
        var result,
          length,
          value,
          year,
          month;

        if (!global["isFinite"](this)) {
            throw new RangeError("Date.prototype.toISOString called on non-finite value.");
        }

        year = this.getUTCFullYear();

        month = this.getUTCMonth();
        // see https://github.com/kriskowal/es5-shim/issues/111
        year += ~~(month / 12);
        month = (month % 12 + 12) % 12;

        // the date time string format is specified in 15.9.1.15.
        result = [month + 1, this.getUTCDate(),
            this.getUTCHours(), this.getUTCMinutes(), this.getUTCSeconds()];
        year = (year < 0 ? '-' : (year > 9999 ? '+' : '')) + ('00000' + Math.abs(year)).slice(0 <= year && year <= 9999 ? -4 : -6);

        length = result.length;
        while (length--) {
            value = result[length];
            // pad months, days, hours, minutes, and seconds to have two digits.
            if (value < 10) {
                result[length] = "0" + value;
            }
        }
        // pad milliseconds to have three digits.
        return year + "-" + result.slice(0, 2).join("-") + "T" + result.slice(2).join(":") + "." +
            ("000" + this.getUTCMilliseconds()).slice(-3) + "Z";
    };
}

// ES5 15.9.4.4
// http://es5.github.com/#x15.9.4.4
if( !_Native_Date.now ) {
    _Native_Date.now = function() {
        return new _Native_Date().getTime();
    };
}

// ES5 15.9.5.44
// http://es5.github.com/#x15.9.5.44
// This function provides a String representation of a Date object for use by
// JSON.stringify (15.12.3).Object.create(f.prototype)
if( !_Native_Date.prototype.toJSON
    || _String_contains_.call((new _Native_Date(-62198755200000)).toJSON(), '-000001')
    || ~(function() {
            // is Date.prototype.toJSON non-generic?
            try {
                return _Native_Date.prototype.toJSON.call({toISOString:function(){return -1;}});
            } catch (err) {}
        }())
) {
    _Native_Date.prototype.toJSON = function(key) {
        // When the toJSON method is called with argument key, the following
        // steps are taken:

        // 1.  Let O be the result of calling ToObject, giving it the this
        // value as its argument.
        // 2. Let tv be ToPrimitive(O, hint Number).
        // 3. If tv is a Number and is not finite, return null.
        // XXX
        // 4. Let toISO be the result of calling the [[Get]] internal method of
        // O with argument "toISOString".
        // 5. If IsCallable(toISO) is false, throw a TypeError exception.
        // In "_call_function"
        // 6. Return the result of calling the [[Call]] internal method of
        //  toISO with O as the this value and an empty argument list.
        return _call_function(this.toISOString, this);

        // NOTE 1 The argument is ignored.

        // NOTE 2 The toJSON function is intentionally generic; it does not
        // require that its this value be a Date object. Therefore, it can be
        // transferred to other kinds of objects for use as a method. However,
        // it does require that any such object have a toISOString method. An
        // object is free to use the argument key to filter its
        // stringification.
    };
}

// ES5 15.9.4.2
// http://es5.github.com/#x15.9.4.2
// based on work shared by Daniel Friesen (dantman)
// http://gist.github.com/303249
/*
OR
https://github.com/csnover/js-iso8601
https://raw.github.com/csnover/js-iso8601/master/iso8601.min.js
?
*/
if( !_Native_Date.parse || "Date.parse is buggy" ) {
    // Date.length === 7
    _Shimed_Date = function(Y, M, D, h, m, s, ms) {
        var length = arguments.length;
        if (this instanceof _Native_Date) {
            var date = length == 1 && String(Y) === Y ? // isString(Y)
                // We explicitly pass it through parse:
                new _Native_Date(Date.parse(Y)) :
                // We have to manually make calls depending on argument
                // length here
                length >= 7 ? new _Native_Date(Y, M, D, h, m, s, ms) :
                length >= 6 ? new _Native_Date(Y, M, D, h, m, s) :
                length >= 5 ? new _Native_Date(Y, M, D, h, m) :
                length >= 4 ? new _Native_Date(Y, M, D, h) :
                length >= 3 ? new _Native_Date(Y, M, D) :
                length >= 2 ? new _Native_Date(Y, M) :
                length >= 1 ? new _Native_Date(Y) :
                              new _Native_Date();
            // Prevent mixups with unfixed Date object
            date.constructor = _Shimed_Date;
            return date;
        }
        return _Native_Date.apply(this, arguments);
    };

    // 15.9.1.15 Date Time String Format.
    _Shimed_Date_isoDateExpression = new RegExp("^" +
        "(\\d{4}|[\\+\\-]\\d{6})" + // four-digit year capture or sign + 6-digit extended year
        "(?:-(\\d{2})" + // optional month capture
        "(?:-(\\d{2})" + // optional day capture
        "(?:" + // capture hours:minutes:seconds.milliseconds
            "T(\\d{2})" + // hours capture
            ":(\\d{2})" + // minutes capture
            "(?:" + // optional :seconds.milliseconds
                ":(\\d{2})" + // seconds capture
                "(?:\\.(\\d{3}))?" + // milliseconds capture
            ")?" +
        "(" + // capture UTC offset component
            "Z|" + // UTC capture
            "(?:" + // offset specifier +/-hours:minutes
                "([-+])" + // sign capture
                "(\\d{2})" + // hours offset capture
                ":(\\d{2})" + // minutes offset capture
            ")" +
        ")?)?)?)?" +
    "$");

  _Shimed_Date_monthes = [0, 31, 59, 90, 120, 151, 181, 212, 243, 273, 304, 334, 365];

  _Shimed_Date_dayFromMonth = function(year, month) {
      var t = month > 1 ? 1 : 0;
            return (
        _Shimed_Date_monthes[month] +
                Math.floor((year - 1969 + t) / 4) -
                Math.floor((year - 1901 + t) / 100) +
                Math.floor((year - 1601 + t) / 400) +
                365 * (year - 1970)
            );
  };

  // Copy any custom methods a 3rd party library may have added
  for (_tmp_ in _Native_Date) {
      _Shimed_Date[_tmp_] = _Native_Date[_tmp_];
  }

  // Copy "native" methods explicitly; they may be non-enumerable
  _Shimed_Date.now = _Native_Date.now;
  _Shimed_Date.UTC = _Native_Date.UTC;
  _Shimed_Date.prototype = _Native_Date.prototype;
  _Shimed_Date.prototype.constructor = _Shimed_Date;

  // Upgrade Date.parse to handle simplified ISO 8601 strings
  _Shimed_Date.parse = function parse(string) {
      var match = _Shimed_Date_isoDateExpression.exec(string);
      if (match) {
          // parse months, days, hours, minutes, seconds, and milliseconds
          // provide default values if necessary
          // parse the UTC offset component
          var year = Number(match[1]),
                month = Number(match[2] || 1) - 1,
                day = Number(match[3] || 1) - 1,
              hour = Number(match[4] || 0),
              minute = Number(match[5] || 0),
              second = Number(match[6] || 0),
              millisecond = Number(match[7] || 0),
        // When time zone is missed, local offset should be used
        // (ES 5.1 bug)
              // see https://bugs.ecmascript.org/show_bug.cgi?id=112
                offset = !match[4] || match[8] ?
                        0 : Number(new _Native_Date(1970, 0)),
              signOffset = match[9] === "-" ? 1 : -1,
              hourOffset = Number(match[10] || 0),
              minuteOffset = Number(match[11] || 0),
              result;
                if (
                    hour < (
                        minute > 0 || second > 0 || millisecond > 0 ?
                        24 : 25
                    ) &&
                    minute < 60 && second < 60 && millisecond < 1000 &&
                    month > -1 && month < 12 && hourOffset < 24 &&
                    minuteOffset < 60 && // detect invalid offsets
                    day > -1 &&
                    day < (
            _Shimed_Date_dayFromMonth(year, month + 1) -
              _Shimed_Date_dayFromMonth(year, month)
                    )
                ) {
                    result = (
                        (_Shimed_Date_dayFromMonth(year, month) + day) * 24 +
                        hour +
                        hourOffset * signOffset
                    ) * 60;
                    result = (
                        (result + minute + minuteOffset * signOffset) * 60 +
                        second
                    ) * 1000 + millisecond + offset;
                if (-8.64e15 <= result && result <= 8.64e15) {
                    return result;
                }
          }
          return NaN;
      }
      return _Native_Date.parse.apply(this, arguments);
  };

    global["Date"] = _Shimed_Date;
}
}//if __GCC__SCRIPT_BUGFIXING__
/*  ======================================================================================  */
/*  >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>  Date  =====================================  */


/*  =======================================================================================  */
/*  ========================================  DEBUG  <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<  */

if(DEBUG) {
// friendly console
// http://habrahabr.ru/blogs/javascript/116852/
// https://github.com/theshock/console-cap/blob/master/console.js
// 21.02.2012 Update with CHANGES!!!
(function (console) {

var i,
  methods = ['assert','count','debug','dir','dirxml','error','group','groupCollapsed','groupEnd','info','log','markTimeline','profile','profileEnd','table','time','timeEnd','trace','warn'],
  empty   = {},
  timeCounters,
    functionReturnFirstParam = function(param) { return param }
    ;

for (i = methods.length; i--;) empty[methods[i]] = functionReturnFirstParam;

if (console) {

  if (!console.time) {
    timeCounters = {};

    console.time = function(name, reset){
      if (name) {
        var time = +new Date, key = "KEY" + name.toString();
        if (reset || !timeCounters[key]) timeCounters[key] = time;
      }
    };

    console.timeEnd = function(name){
      var diff,
        time = +new Date,
        key = "KEY" + name.toString(),
        timeCounter = timeCounters[key];

      if (timeCounter) {
        diff  = time - timeCounter;
        console.info( name + ": " + diff + "ms" );
        delete timeCounters[key];
      }
      return diff;
    };
  }

  for (i = methods.length; i--;) {
    console[methods[i]] = methods[i] in console ?
      _fastUnsafe_Function_bind_.call(console[methods[i]], console) : functionReturnFirstParam;
  }
  console.disable = function () { global.console = empty;   };
    empty.enable  = function () { global.console = console; };

  empty.disable = console.enable = functionReturnFirstParam;

} else {
  console = global.console = empty;
  console.disable = console.enable = functionReturnFirstParam;
}

methods = void 0;

})( typeof console === 'undefined' ? null : console );

}//if(DEBUG)

/*  ======================================================================================  */
/*  >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>  DEBUG  =====================================  */

if(__GCC__LEGACY_BROWSERS_SUPPORT__ && __GCC__LEGACY_BROWSERS_SUPPORT__IELT9__) {
//apply IE lt 9 shims
if( _ && _["ielt9shims"] ) {
    _Array_forEach_.call(_["ielt9shims"], _call_function);
  //Restore original "_" or set "_" to undefined
  if( _["orig_"] ) {
    global["_"] = _["orig_"];
  }
    else {
        try {
      delete global["_"];
    }
    catch(e){}
    }
}
}//if(__GCC__LEGACY_BROWSERS_SUPPORT__ && __GCC__LEGACY_BROWSERS_SUPPORT__IELT9__)

/*  =======================================================================================  */
/*  ========================================  Delete section  <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<  */

if(__GCC__STRING_LEGACY_DELETE__) {
  _Array_forEach_.call(["anchor", "big", "blink", "bold", "fixed", "fontcolor", "fontsize", "italics", "link", "small", "strike", "sub", "sup"], function(name) {
    delete this[name];
  }, _String_prototype);
}

/*  ======================================================================================  */
/*  >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>  Delete section  =====================================  */

if(__GCC__LEGACY_BROWSERS_SUPPORT__ && __GCC__LEGACY_BROWSERS_SUPPORT__IELT9__) {
  _ = null;
}

// cleanup, no need this any more
_append = _tmp_ = _testElement = _document_createElement = _Array_prototype_ = _String_prototype =
    _Event = _CustomEvent = _Event_prototype = _Custom_Event_prototype = _tmp_function = _classList_toggle =
  _Element_prototype = _Shimed_Date = functionReturnFalse = functionReturnFirstParam = null;

throwTypeError = function(msg) {
  throw new TypeError(msg)
};

}.call(window);