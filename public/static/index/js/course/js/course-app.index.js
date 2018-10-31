webpackJsonp([0],[
/* 0 */,
/* 1 */,
/* 2 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var bind = __webpack_require__(72);
var isBuffer = __webpack_require__(191);

/*global toString:true*/

// utils is a library of generic helper functions non-specific to axios

var toString = Object.prototype.toString;

/**
 * Determine if a value is an Array
 *
 * @param {Object} val The value to test
 * @returns {boolean} True if value is an Array, otherwise false
 */
function isArray(val) {
  return toString.call(val) === '[object Array]';
}

/**
 * Determine if a value is an ArrayBuffer
 *
 * @param {Object} val The value to test
 * @returns {boolean} True if value is an ArrayBuffer, otherwise false
 */
function isArrayBuffer(val) {
  return toString.call(val) === '[object ArrayBuffer]';
}

/**
 * Determine if a value is a FormData
 *
 * @param {Object} val The value to test
 * @returns {boolean} True if value is an FormData, otherwise false
 */
function isFormData(val) {
  return (typeof FormData !== 'undefined') && (val instanceof FormData);
}

/**
 * Determine if a value is a view on an ArrayBuffer
 *
 * @param {Object} val The value to test
 * @returns {boolean} True if value is a view on an ArrayBuffer, otherwise false
 */
function isArrayBufferView(val) {
  var result;
  if ((typeof ArrayBuffer !== 'undefined') && (ArrayBuffer.isView)) {
    result = ArrayBuffer.isView(val);
  } else {
    result = (val) && (val.buffer) && (val.buffer instanceof ArrayBuffer);
  }
  return result;
}

/**
 * Determine if a value is a String
 *
 * @param {Object} val The value to test
 * @returns {boolean} True if value is a String, otherwise false
 */
function isString(val) {
  return typeof val === 'string';
}

/**
 * Determine if a value is a Number
 *
 * @param {Object} val The value to test
 * @returns {boolean} True if value is a Number, otherwise false
 */
function isNumber(val) {
  return typeof val === 'number';
}

/**
 * Determine if a value is undefined
 *
 * @param {Object} val The value to test
 * @returns {boolean} True if the value is undefined, otherwise false
 */
function isUndefined(val) {
  return typeof val === 'undefined';
}

/**
 * Determine if a value is an Object
 *
 * @param {Object} val The value to test
 * @returns {boolean} True if value is an Object, otherwise false
 */
function isObject(val) {
  return val !== null && typeof val === 'object';
}

/**
 * Determine if a value is a Date
 *
 * @param {Object} val The value to test
 * @returns {boolean} True if value is a Date, otherwise false
 */
function isDate(val) {
  return toString.call(val) === '[object Date]';
}

/**
 * Determine if a value is a File
 *
 * @param {Object} val The value to test
 * @returns {boolean} True if value is a File, otherwise false
 */
function isFile(val) {
  return toString.call(val) === '[object File]';
}

/**
 * Determine if a value is a Blob
 *
 * @param {Object} val The value to test
 * @returns {boolean} True if value is a Blob, otherwise false
 */
function isBlob(val) {
  return toString.call(val) === '[object Blob]';
}

/**
 * Determine if a value is a Function
 *
 * @param {Object} val The value to test
 * @returns {boolean} True if value is a Function, otherwise false
 */
function isFunction(val) {
  return toString.call(val) === '[object Function]';
}

/**
 * Determine if a value is a Stream
 *
 * @param {Object} val The value to test
 * @returns {boolean} True if value is a Stream, otherwise false
 */
function isStream(val) {
  return isObject(val) && isFunction(val.pipe);
}

/**
 * Determine if a value is a URLSearchParams object
 *
 * @param {Object} val The value to test
 * @returns {boolean} True if value is a URLSearchParams object, otherwise false
 */
function isURLSearchParams(val) {
  return typeof URLSearchParams !== 'undefined' && val instanceof URLSearchParams;
}

/**
 * Trim excess whitespace off the beginning and end of a string
 *
 * @param {String} str The String to trim
 * @returns {String} The String freed of excess whitespace
 */
function trim(str) {
  return str.replace(/^\s*/, '').replace(/\s*$/, '');
}

/**
 * Determine if we're running in a standard browser environment
 *
 * This allows axios to run in a web worker, and react-native.
 * Both environments support XMLHttpRequest, but not fully standard globals.
 *
 * web workers:
 *  typeof window -> undefined
 *  typeof document -> undefined
 *
 * react-native:
 *  navigator.product -> 'ReactNative'
 */
function isStandardBrowserEnv() {
  if (typeof navigator !== 'undefined' && navigator.product === 'ReactNative') {
    return false;
  }
  return (
    typeof window !== 'undefined' &&
    typeof document !== 'undefined'
  );
}

/**
 * Iterate over an Array or an Object invoking a function for each item.
 *
 * If `obj` is an Array callback will be called passing
 * the value, index, and complete array for each item.
 *
 * If 'obj' is an Object callback will be called passing
 * the value, key, and complete object for each property.
 *
 * @param {Object|Array} obj The object to iterate
 * @param {Function} fn The callback to invoke for each item
 */
function forEach(obj, fn) {
  // Don't bother if no value provided
  if (obj === null || typeof obj === 'undefined') {
    return;
  }

  // Force an array if not already something iterable
  if (typeof obj !== 'object') {
    /*eslint no-param-reassign:0*/
    obj = [obj];
  }

  if (isArray(obj)) {
    // Iterate over array values
    for (var i = 0, l = obj.length; i < l; i++) {
      fn.call(null, obj[i], i, obj);
    }
  } else {
    // Iterate over object keys
    for (var key in obj) {
      if (Object.prototype.hasOwnProperty.call(obj, key)) {
        fn.call(null, obj[key], key, obj);
      }
    }
  }
}

/**
 * Accepts varargs expecting each argument to be an object, then
 * immutably merges the properties of each object and returns result.
 *
 * When multiple objects contain the same key the later object in
 * the arguments list will take precedence.
 *
 * Example:
 *
 * ```js
 * var result = merge({foo: 123}, {foo: 456});
 * console.log(result.foo); // outputs 456
 * ```
 *
 * @param {Object} obj1 Object to merge
 * @returns {Object} Result of all merge properties
 */
function merge(/* obj1, obj2, obj3, ... */) {
  var result = {};
  function assignValue(val, key) {
    if (typeof result[key] === 'object' && typeof val === 'object') {
      result[key] = merge(result[key], val);
    } else {
      result[key] = val;
    }
  }

  for (var i = 0, l = arguments.length; i < l; i++) {
    forEach(arguments[i], assignValue);
  }
  return result;
}

/**
 * Extends object a by mutably adding to it the properties of object b.
 *
 * @param {Object} a The object to be extended
 * @param {Object} b The object to copy properties from
 * @param {Object} thisArg The object to bind function to
 * @return {Object} The resulting value of object a
 */
function extend(a, b, thisArg) {
  forEach(b, function assignValue(val, key) {
    if (thisArg && typeof val === 'function') {
      a[key] = bind(val, thisArg);
    } else {
      a[key] = val;
    }
  });
  return a;
}

module.exports = {
  isArray: isArray,
  isArrayBuffer: isArrayBuffer,
  isBuffer: isBuffer,
  isFormData: isFormData,
  isArrayBufferView: isArrayBufferView,
  isString: isString,
  isNumber: isNumber,
  isObject: isObject,
  isUndefined: isUndefined,
  isDate: isDate,
  isFile: isFile,
  isBlob: isBlob,
  isFunction: isFunction,
  isStream: isStream,
  isURLSearchParams: isURLSearchParams,
  isStandardBrowserEnv: isStandardBrowserEnv,
  forEach: forEach,
  merge: merge,
  extend: extend,
  trim: trim
};


/***/ }),
/* 3 */
/***/ (function(module, exports) {

module.exports = function normalizeComponent (
  rawScriptExports,
  compiledTemplate,
  scopeId,
  cssModules
) {
  var esModule
  var scriptExports = rawScriptExports = rawScriptExports || {}

  // ES6 modules interop
  var type = typeof rawScriptExports.default
  if (type === 'object' || type === 'function') {
    esModule = rawScriptExports
    scriptExports = rawScriptExports.default
  }

  // Vue.extend constructor export interop
  var options = typeof scriptExports === 'function'
    ? scriptExports.options
    : scriptExports

  // render functions
  if (compiledTemplate) {
    options.render = compiledTemplate.render
    options.staticRenderFns = compiledTemplate.staticRenderFns
  }

  // scopedId
  if (scopeId) {
    options._scopeId = scopeId
  }

  // inject cssModules
  if (cssModules) {
    var computed = options.computed || (options.computed = {})
    Object.keys(cssModules).forEach(function (key) {
      var module = cssModules[key]
      computed[key] = function () { return module }
    })
  }

  return {
    esModule: esModule,
    exports: scriptExports,
    options: options
  }
}


/***/ }),
/* 4 */,
/* 5 */,
/* 6 */
/***/ (function(module, exports, __webpack_require__) {

/*
  MIT License http://www.opensource.org/licenses/mit-license.php
  Author Tobias Koppers @sokra
  Modified by Evan You @yyx990803
*/

var hasDocument = typeof document !== 'undefined'

if (typeof DEBUG !== 'undefined' && DEBUG) {
  if (!hasDocument) {
    throw new Error(
    'vue-style-loader cannot be used in a non-browser environment. ' +
    "Use { target: 'node' } in your Webpack config to indicate a server-rendering environment."
  ) }
}

var listToStyles = __webpack_require__(85)

/*
type StyleObject = {
  id: number;
  parts: Array<StyleObjectPart>
}

type StyleObjectPart = {
  css: string;
  media: string;
  sourceMap: ?string
}
*/

var stylesInDom = {/*
  [id: number]: {
    id: number,
    refs: number,
    parts: Array<(obj?: StyleObjectPart) => void>
  }
*/}

var head = hasDocument && (document.head || document.getElementsByTagName('head')[0])
var singletonElement = null
var singletonCounter = 0
var isProduction = false
var noop = function () {}

// Force single-tag solution on IE6-9, which has a hard limit on the # of <style>
// tags it will allow on a page
var isOldIE = typeof navigator !== 'undefined' && /msie [6-9]\b/.test(navigator.userAgent.toLowerCase())

module.exports = function (parentId, list, _isProduction) {
  isProduction = _isProduction

  var styles = listToStyles(parentId, list)
  addStylesToDom(styles)

  return function update (newList) {
    var mayRemove = []
    for (var i = 0; i < styles.length; i++) {
      var item = styles[i]
      var domStyle = stylesInDom[item.id]
      domStyle.refs--
      mayRemove.push(domStyle)
    }
    if (newList) {
      styles = listToStyles(parentId, newList)
      addStylesToDom(styles)
    } else {
      styles = []
    }
    for (var i = 0; i < mayRemove.length; i++) {
      var domStyle = mayRemove[i]
      if (domStyle.refs === 0) {
        for (var j = 0; j < domStyle.parts.length; j++) {
          domStyle.parts[j]()
        }
        delete stylesInDom[domStyle.id]
      }
    }
  }
}

function addStylesToDom (styles /* Array<StyleObject> */) {
  for (var i = 0; i < styles.length; i++) {
    var item = styles[i]
    var domStyle = stylesInDom[item.id]
    if (domStyle) {
      domStyle.refs++
      for (var j = 0; j < domStyle.parts.length; j++) {
        domStyle.parts[j](item.parts[j])
      }
      for (; j < item.parts.length; j++) {
        domStyle.parts.push(addStyle(item.parts[j]))
      }
      if (domStyle.parts.length > item.parts.length) {
        domStyle.parts.length = item.parts.length
      }
    } else {
      var parts = []
      for (var j = 0; j < item.parts.length; j++) {
        parts.push(addStyle(item.parts[j]))
      }
      stylesInDom[item.id] = { id: item.id, refs: 1, parts: parts }
    }
  }
}

function createStyleElement () {
  var styleElement = document.createElement('style')
  styleElement.type = 'text/css'
  head.appendChild(styleElement)
  return styleElement
}

function addStyle (obj /* StyleObjectPart */) {
  var update, remove
  var styleElement = document.querySelector('style[data-vue-ssr-id~="' + obj.id + '"]')

  if (styleElement) {
    if (isProduction) {
      // has SSR styles and in production mode.
      // simply do nothing.
      return noop
    } else {
      // has SSR styles but in dev mode.
      // for some reason Chrome can't handle source map in server-rendered
      // style tags - source maps in <style> only works if the style tag is
      // created and inserted dynamically. So we remove the server rendered
      // styles and inject new ones.
      styleElement.parentNode.removeChild(styleElement)
    }
  }

  if (isOldIE) {
    // use singleton mode for IE9.
    var styleIndex = singletonCounter++
    styleElement = singletonElement || (singletonElement = createStyleElement())
    update = applyToSingletonTag.bind(null, styleElement, styleIndex, false)
    remove = applyToSingletonTag.bind(null, styleElement, styleIndex, true)
  } else {
    // use multi-style-tag mode in all other cases
    styleElement = createStyleElement()
    update = applyToTag.bind(null, styleElement)
    remove = function () {
      styleElement.parentNode.removeChild(styleElement)
    }
  }

  update(obj)

  return function updateStyle (newObj /* StyleObjectPart */) {
    if (newObj) {
      if (newObj.css === obj.css &&
          newObj.media === obj.media &&
          newObj.sourceMap === obj.sourceMap) {
        return
      }
      update(obj = newObj)
    } else {
      remove()
    }
  }
}

var replaceText = (function () {
  var textStore = []

  return function (index, replacement) {
    textStore[index] = replacement
    return textStore.filter(Boolean).join('\n')
  }
})()

function applyToSingletonTag (styleElement, index, remove, obj) {
  var css = remove ? '' : obj.css

  if (styleElement.styleSheet) {
    styleElement.styleSheet.cssText = replaceText(index, css)
  } else {
    var cssNode = document.createTextNode(css)
    var childNodes = styleElement.childNodes
    if (childNodes[index]) styleElement.removeChild(childNodes[index])
    if (childNodes.length) {
      styleElement.insertBefore(cssNode, childNodes[index])
    } else {
      styleElement.appendChild(cssNode)
    }
  }
}

function applyToTag (styleElement, obj) {
  var css = obj.css
  var media = obj.media
  var sourceMap = obj.sourceMap

  if (media) {
    styleElement.setAttribute('media', media)
  }

  if (sourceMap) {
    // https://developer.chrome.com/devtools/docs/javascript-debugging
    // this makes source maps inside style tags work properly in Chrome
    css += '\n/*# sourceURL=' + sourceMap.sources[0] + ' */'
    // http://stackoverflow.com/a/26603875
    css += '\n/*# sourceMappingURL=data:application/json;base64,' + btoa(unescape(encodeURIComponent(JSON.stringify(sourceMap)))) + ' */'
  }

  if (styleElement.styleSheet) {
    styleElement.styleSheet.cssText = css
  } else {
    while (styleElement.firstChild) {
      styleElement.removeChild(styleElement.firstChild)
    }
    styleElement.appendChild(document.createTextNode(css))
  }
}


/***/ }),
/* 7 */,
/* 8 */,
/* 9 */,
/* 10 */,
/* 11 */,
/* 12 */,
/* 13 */,
/* 14 */,
/* 15 */,
/* 16 */,
/* 17 */,
/* 18 */,
/* 19 */,
/* 20 */,
/* 21 */,
/* 22 */,
/* 23 */,
/* 24 */,
/* 25 */,
/* 26 */,
/* 27 */,
/* 28 */,
/* 29 */,
/* 30 */,
/* 31 */,
/* 32 */,
/* 33 */,
/* 34 */,
/* 35 */,
/* 36 */,
/* 37 */,
/* 38 */,
/* 39 */,
/* 40 */,
/* 41 */,
/* 42 */,
/* 43 */,
/* 44 */,
/* 45 */,
/* 46 */,
/* 47 */,
/* 48 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";
/* WEBPACK VAR INJECTION */(function(process) {

var utils = __webpack_require__(2);
var normalizeHeaderName = __webpack_require__(193);

var DEFAULT_CONTENT_TYPE = {
  'Content-Type': 'application/x-www-form-urlencoded'
};

function setContentTypeIfUnset(headers, value) {
  if (!utils.isUndefined(headers) && utils.isUndefined(headers['Content-Type'])) {
    headers['Content-Type'] = value;
  }
}

function getDefaultAdapter() {
  var adapter;
  if (typeof XMLHttpRequest !== 'undefined') {
    // For browsers use XHR adapter
    adapter = __webpack_require__(73);
  } else if (typeof process !== 'undefined') {
    // For node use HTTP adapter
    adapter = __webpack_require__(73);
  }
  return adapter;
}

var defaults = {
  adapter: getDefaultAdapter(),

  transformRequest: [function transformRequest(data, headers) {
    normalizeHeaderName(headers, 'Content-Type');
    if (utils.isFormData(data) ||
      utils.isArrayBuffer(data) ||
      utils.isBuffer(data) ||
      utils.isStream(data) ||
      utils.isFile(data) ||
      utils.isBlob(data)
    ) {
      return data;
    }
    if (utils.isArrayBufferView(data)) {
      return data.buffer;
    }
    if (utils.isURLSearchParams(data)) {
      setContentTypeIfUnset(headers, 'application/x-www-form-urlencoded;charset=utf-8');
      return data.toString();
    }
    if (utils.isObject(data)) {
      setContentTypeIfUnset(headers, 'application/json;charset=utf-8');
      return JSON.stringify(data);
    }
    return data;
  }],

  transformResponse: [function transformResponse(data) {
    /*eslint no-param-reassign:0*/
    if (typeof data === 'string') {
      try {
        data = JSON.parse(data);
      } catch (e) { /* Ignore */ }
    }
    return data;
  }],

  /**
   * A timeout in milliseconds to abort a request. If set to 0 (default) a
   * timeout is not created.
   */
  timeout: 0,

  xsrfCookieName: 'XSRF-TOKEN',
  xsrfHeaderName: 'X-XSRF-TOKEN',

  maxContentLength: -1,

  validateStatus: function validateStatus(status) {
    return status >= 200 && status < 300;
  }
};

defaults.headers = {
  common: {
    'Accept': 'application/json, text/plain, */*'
  }
};

utils.forEach(['delete', 'get', 'head'], function forEachMethodNoData(method) {
  defaults.headers[method] = {};
});

utils.forEach(['post', 'put', 'patch'], function forEachMethodWithData(method) {
  defaults.headers[method] = utils.merge(DEFAULT_CONTENT_TYPE);
});

module.exports = defaults;

/* WEBPACK VAR INJECTION */}.call(exports, __webpack_require__(10)))

/***/ }),
/* 49 */,
/* 50 */,
/* 51 */,
/* 52 */,
/* 53 */,
/* 54 */,
/* 55 */,
/* 56 */,
/* 57 */,
/* 58 */,
/* 59 */,
/* 60 */,
/* 61 */,
/* 62 */,
/* 63 */,
/* 64 */,
/* 65 */,
/* 66 */,
/* 67 */,
/* 68 */,
/* 69 */,
/* 70 */,
/* 71 */,
/* 72 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


module.exports = function bind(fn, thisArg) {
  return function wrap() {
    var args = new Array(arguments.length);
    for (var i = 0; i < args.length; i++) {
      args[i] = arguments[i];
    }
    return fn.apply(thisArg, args);
  };
};


/***/ }),
/* 73 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";
/* WEBPACK VAR INJECTION */(function(process) {

var utils = __webpack_require__(2);
var settle = __webpack_require__(194);
var buildURL = __webpack_require__(196);
var parseHeaders = __webpack_require__(197);
var isURLSameOrigin = __webpack_require__(198);
var createError = __webpack_require__(74);
var btoa = (typeof window !== 'undefined' && window.btoa && window.btoa.bind(window)) || __webpack_require__(199);

module.exports = function xhrAdapter(config) {
  return new Promise(function dispatchXhrRequest(resolve, reject) {
    var requestData = config.data;
    var requestHeaders = config.headers;

    if (utils.isFormData(requestData)) {
      delete requestHeaders['Content-Type']; // Let the browser set it
    }

    var request = new XMLHttpRequest();
    var loadEvent = 'onreadystatechange';
    var xDomain = false;

    // For IE 8/9 CORS support
    // Only supports POST and GET calls and doesn't returns the response headers.
    // DON'T do this for testing b/c XMLHttpRequest is mocked, not XDomainRequest.
    if (process.env.NODE_ENV !== 'test' &&
        typeof window !== 'undefined' &&
        window.XDomainRequest && !('withCredentials' in request) &&
        !isURLSameOrigin(config.url)) {
      request = new window.XDomainRequest();
      loadEvent = 'onload';
      xDomain = true;
      request.onprogress = function handleProgress() {};
      request.ontimeout = function handleTimeout() {};
    }

    // HTTP basic authentication
    if (config.auth) {
      var username = config.auth.username || '';
      var password = config.auth.password || '';
      requestHeaders.Authorization = 'Basic ' + btoa(username + ':' + password);
    }

    request.open(config.method.toUpperCase(), buildURL(config.url, config.params, config.paramsSerializer), true);

    // Set the request timeout in MS
    request.timeout = config.timeout;

    // Listen for ready state
    request[loadEvent] = function handleLoad() {
      if (!request || (request.readyState !== 4 && !xDomain)) {
        return;
      }

      // The request errored out and we didn't get a response, this will be
      // handled by onerror instead
      // With one exception: request that using file: protocol, most browsers
      // will return status as 0 even though it's a successful request
      if (request.status === 0 && !(request.responseURL && request.responseURL.indexOf('file:') === 0)) {
        return;
      }

      // Prepare the response
      var responseHeaders = 'getAllResponseHeaders' in request ? parseHeaders(request.getAllResponseHeaders()) : null;
      var responseData = !config.responseType || config.responseType === 'text' ? request.responseText : request.response;
      var response = {
        data: responseData,
        // IE sends 1223 instead of 204 (https://github.com/axios/axios/issues/201)
        status: request.status === 1223 ? 204 : request.status,
        statusText: request.status === 1223 ? 'No Content' : request.statusText,
        headers: responseHeaders,
        config: config,
        request: request
      };

      settle(resolve, reject, response);

      // Clean up request
      request = null;
    };

    // Handle low level network errors
    request.onerror = function handleError() {
      // Real errors are hidden from us by the browser
      // onerror should only fire if it's a network error
      reject(createError('Network Error', config, null, request));

      // Clean up request
      request = null;
    };

    // Handle timeout
    request.ontimeout = function handleTimeout() {
      reject(createError('timeout of ' + config.timeout + 'ms exceeded', config, 'ECONNABORTED',
        request));

      // Clean up request
      request = null;
    };

    // Add xsrf header
    // This is only done if running in a standard browser environment.
    // Specifically not if we're in a web worker, or react-native.
    if (utils.isStandardBrowserEnv()) {
      var cookies = __webpack_require__(200);

      // Add xsrf header
      var xsrfValue = (config.withCredentials || isURLSameOrigin(config.url)) && config.xsrfCookieName ?
          cookies.read(config.xsrfCookieName) :
          undefined;

      if (xsrfValue) {
        requestHeaders[config.xsrfHeaderName] = xsrfValue;
      }
    }

    // Add headers to the request
    if ('setRequestHeader' in request) {
      utils.forEach(requestHeaders, function setRequestHeader(val, key) {
        if (typeof requestData === 'undefined' && key.toLowerCase() === 'content-type') {
          // Remove Content-Type if data is undefined
          delete requestHeaders[key];
        } else {
          // Otherwise add header to the request
          request.setRequestHeader(key, val);
        }
      });
    }

    // Add withCredentials to request if needed
    if (config.withCredentials) {
      request.withCredentials = true;
    }

    // Add responseType to request if needed
    if (config.responseType) {
      try {
        request.responseType = config.responseType;
      } catch (e) {
        // Expected DOMException thrown by browsers not compatible XMLHttpRequest Level 2.
        // But, this can be suppressed for 'json' type as it can be parsed by default 'transformResponse' function.
        if (config.responseType !== 'json') {
          throw e;
        }
      }
    }

    // Handle progress if needed
    if (typeof config.onDownloadProgress === 'function') {
      request.addEventListener('progress', config.onDownloadProgress);
    }

    // Not all browsers support upload events
    if (typeof config.onUploadProgress === 'function' && request.upload) {
      request.upload.addEventListener('progress', config.onUploadProgress);
    }

    if (config.cancelToken) {
      // Handle cancellation
      config.cancelToken.promise.then(function onCanceled(cancel) {
        if (!request) {
          return;
        }

        request.abort();
        reject(cancel);
        // Clean up request
        request = null;
      });
    }

    if (requestData === undefined) {
      requestData = null;
    }

    // Send the request
    request.send(requestData);
  });
};

/* WEBPACK VAR INJECTION */}.call(exports, __webpack_require__(10)))

/***/ }),
/* 74 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var enhanceError = __webpack_require__(195);

/**
 * Create an Error with the specified message, config, error code, request and response.
 *
 * @param {string} message The error message.
 * @param {Object} config The config.
 * @param {string} [code] The error code (for example, 'ECONNABORTED').
 * @param {Object} [request] The request.
 * @param {Object} [response] The response.
 * @returns {Error} The created error.
 */
module.exports = function createError(message, config, code, request, response) {
  var error = new Error(message);
  return enhanceError(error, config, code, request, response);
};


/***/ }),
/* 75 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


module.exports = function isCancel(value) {
  return !!(value && value.__CANCEL__);
};


/***/ }),
/* 76 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


/**
 * A `Cancel` is an object that is thrown when an operation is canceled.
 *
 * @class
 * @param {string=} message The message.
 */
function Cancel(message) {
  this.message = message;
}

Cancel.prototype.toString = function toString() {
  return 'Cancel' + (this.message ? ': ' + this.message : '');
};

Cancel.prototype.__CANCEL__ = true;

module.exports = Cancel;


/***/ }),
/* 77 */,
/* 78 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var _vue = __webpack_require__(1);

var _vue2 = _interopRequireDefault(_vue);

var _routers = __webpack_require__(79);

var _routers2 = _interopRequireDefault(_routers);

var _elementUi = __webpack_require__(51);

var _elementUi2 = _interopRequireDefault(_elementUi);

var _axios = __webpack_require__(189);

var _axios2 = _interopRequireDefault(_axios);

__webpack_require__(77);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

_vue2.default.use(_elementUi2.default);
_vue2.default.prototype.$ajax = _axios2.default;
_vue2.default.prototype.Const = {
    userMsg: function () {
        if ($.cookie('userMsg')) {
            return JSON.parse($.cookie('userMsg'));
        } else {
            window.location.href = API_ENV.API_ProjectRoot + 'index/index/login';
        }
    }()
};

var appCourse = new _vue2.default({
    el: '#app',
    data: {
        userMsg: '',
        userPrivilege: '',
        Bus: new _vue2.default()
    },
    methods: {
        checked: function checked(Privilege) {
            return this.userPrivilege.indexOf(Privilege) < 0 ? false : true;
        }
    },
    router: _routers2.default,
    computed: {
        key: function key() {
            return this.$route.name !== undefined ? this.$route.name + new Date() : this.$route + new Date();
        }
    },
    mounted: function mounted() {
        this.$nextTick(function () {
            if ($.cookie('userMsg') && JSON.parse($.cookie('userMsg')) !== null && window.localStorage.getItem('userPrivilege')) {
                this.userMsg = JSON.parse($.cookie('userMsg'));
                this.userPrivilege = window.localStorage.getItem('userPrivilege').split(',');

                // 更新cookie时间
                var date = new Date();
                date.setTime(date.getTime() + 1 * 1 * 60 * 30 * 1000);
                $.cookie('userMsg', $.cookie('userMsg'), { expires: date, path: '/' });
            } else {
                window.location.href = API_ENV.API_ProjectRoot + 'index/index/login';
            }
        });
    }

});

/***/ }),
/* 79 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
    value: true
});

var _vue = __webpack_require__(1);

var _vue2 = _interopRequireDefault(_vue);

var _vueRouter = __webpack_require__(50);

var _vueRouter2 = _interopRequireDefault(_vueRouter);

var _coursesMain = __webpack_require__(80);

var _coursesMain2 = _interopRequireDefault(_coursesMain);

var _courseDetails = __webpack_require__(89);

var _courseDetails2 = _interopRequireDefault(_courseDetails);

var _coursedetailslist = __webpack_require__(94);

var _coursedetailslist2 = _interopRequireDefault(_coursedetailslist);

var _tasklist = __webpack_require__(97);

var _tasklist2 = _interopRequireDefault(_tasklist);

var _addTask = __webpack_require__(100);

var _addTask2 = _interopRequireDefault(_addTask);

var _members = __webpack_require__(105);

var _members2 = _interopRequireDefault(_members);

var _addcourses = __webpack_require__(110);

var _addcourses2 = _interopRequireDefault(_addcourses);

var _editchapter = __webpack_require__(115);

var _editchapter2 = _interopRequireDefault(_editchapter);

var _readtasks = __webpack_require__(120);

var _readtasks2 = _interopRequireDefault(_readtasks);

var _subtask = __webpack_require__(125);

var _subtask2 = _interopRequireDefault(_subtask);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

_vue2.default.use(_vueRouter2.default);

exports.default = new _vueRouter2.default({
    routes: [{
        path: '/', component: _coursesMain2.default
    }, {
        path: '/course', component: _courseDetails2.default,
        children: [{
            path: '', component: _coursedetailslist2.default
        }, {
            path: 'tasks', component: _tasklist2.default
        }, {
            path: 'members', component: _members2.default /*,
                                                          {
                                                             path: 'addtasks', component: addtasks
                                                          }*/ }]
    }, {
        path: '/course/addtasks', component: _addTask2.default
    }, {
        path: '/course/readtasks', component: _readtasks2.default
    }, {
        path: '/course/subtask', component: _subtask2.default
    }, {
        path: '/course/addcourses', component: _addcourses2.default
    }, {
        path: '/course/editchapter', component: _editchapter2.default
    }]
});

/***/ }),
/* 80 */
/***/ (function(module, exports, __webpack_require__) {

var Component = __webpack_require__(3)(
  /* script */
  __webpack_require__(81),
  /* template */
  __webpack_require__(88),
  /* scopeId */
  null,
  /* cssModules */
  null
)
Component.options.__file = "D:\\wampserver\\wamp\\www\\vue\\course-vue\\src\\components\\coursesMain.vue"
if (Component.esModule && Object.keys(Component.esModule).some(function (key) {return key !== "default" && key !== "__esModule"})) {console.error("named exports are not supported in *.vue files.")}
if (Component.options.functional) {console.error("[vue-loader] coursesMain.vue: functional components are not supported with templates, they should use render functions.")}

/* hot reload */
if (false) {(function () {
  var hotAPI = require("vue-hot-reload-api")
  hotAPI.install(require("vue"), false)
  if (!hotAPI.compatible) return
  module.hot.accept()
  if (!module.hot.data) {
    hotAPI.createRecord("data-v-3492e1c0", Component.options)
  } else {
    hotAPI.reload("data-v-3492e1c0", Component.options)
  }
})()}

module.exports = Component.exports


/***/ }),
/* 81 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
    value: true
});

var _paging = __webpack_require__(82);

var _paging2 = _interopRequireDefault(_paging);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

exports.default = {
    data: function data() {
        return {
            courses: userDatas[1].courses,
            // currrentCourses: userDatas[1].courses.teachCourse
            currrentCourses: null
        };
    },
    components: {
        paging: _paging2.default
    },
    mounted: function mounted() {
        var thisVue = this;
        /* 请求课堂数据 */
        $(function () {
            var data = {
                'module': 'service',
                'controller': 'Classroom_Con',
                'action': 'userClassroom',
                'page': 1,
                'list_rows': 6
            };
            $.ajax({
                url: API_ENV.API_URL,
                type: 'POST',
                data: data,
                dataType: 'json',
                success: function success(data) {
                    console.log('请求成功！', data);
                },
                error: function error(err) {
                    console.log(err, '请求报错了！');
                }
            });
        });

        /* 课堂鼠标移动效果 */
        $(function () {
            var classItem = $('#index-content .content-list .item');
            classItem.hover(function () {
                var classMsg = $(this).find('.class-msg');
                classMsg.stop().animate({ height: '120px' }, 200);
            }, function () {
                var classMsg = $(this).find('.class-msg');
                classMsg.stop().animate({ height: '80px' }, 200);
            });
        });

        window.onload = function () {
            thisVue.getCourseByTerm();
            mainTitleCut();
        };
    },
    methods: {
        getCourseList: function getCourseList(type) {
            switch (type) {
                case 'teach':
                    this.currrentCourses = this.courses.teachCourse || '';
                    break;
                case 'study':
                    this.currrentCourses = this.courses.studyCourse || '';
                    break;
            }
        },
        clickdiv: function clickdiv() {
            // dom结构改变后，重新注册dom事件
            $(function () {
                var classItem = $('#index-content .content-list .item');
                classItem.hover(function () {
                    var classMsg = $(this).find('.class-msg');
                    classMsg.stop().animate({ height: '120px' }, 200);
                }, function () {
                    var classMsg = $(this).find('.class-msg');
                    classMsg.stop().animate({ height: '80px' }, 200);
                });
            });
        },
        getCourseByTerm: function getCourseByTerm() {
            /* 学期选择 */
            layui.use('form', function () {
                var form = layui.form;
                form.on('select(term)', function (data) {
                    console.log('学期选择的值', data.value);
                });
            });
        }
    },
    watch: {
        "currrentCourses": function currrentCourses() {
            console.log('currrentCourses数据列表发生变化');
        }
    }
}; //
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//

/***/ }),
/* 82 */
/***/ (function(module, exports, __webpack_require__) {


/* styles */
__webpack_require__(83)

var Component = __webpack_require__(3)(
  /* script */
  __webpack_require__(86),
  /* template */
  __webpack_require__(87),
  /* scopeId */
  null,
  /* cssModules */
  null
)
Component.options.__file = "D:\\wampserver\\wamp\\www\\vue\\course-vue\\src\\components\\paging.vue"
if (Component.esModule && Object.keys(Component.esModule).some(function (key) {return key !== "default" && key !== "__esModule"})) {console.error("named exports are not supported in *.vue files.")}
if (Component.options.functional) {console.error("[vue-loader] paging.vue: functional components are not supported with templates, they should use render functions.")}

/* hot reload */
if (false) {(function () {
  var hotAPI = require("vue-hot-reload-api")
  hotAPI.install(require("vue"), false)
  if (!hotAPI.compatible) return
  module.hot.accept()
  if (!module.hot.data) {
    hotAPI.createRecord("data-v-16c6eb66", Component.options)
  } else {
    hotAPI.reload("data-v-16c6eb66", Component.options)
  }
})()}

module.exports = Component.exports


/***/ }),
/* 83 */
/***/ (function(module, exports, __webpack_require__) {

// style-loader: Adds some css to the DOM by adding a <style> tag

// load the styles
var content = __webpack_require__(84);
if(typeof content === 'string') content = [[module.i, content, '']];
if(content.locals) module.exports = content.locals;
// add the styles to the DOM
var update = __webpack_require__(6)("1db64412", content, false);
// Hot Module Replacement
if(false) {
 // When the styles change, update the <style> tags
 if(!content.locals) {
   module.hot.accept("!!../../node_modules/css-loader/index.js!../../node_modules/vue-loader/lib/style-rewriter.js?id=data-v-16c6eb66!../../node_modules/vue-loader/lib/selector.js?type=styles&index=0!./paging.vue", function() {
     var newContent = require("!!../../node_modules/css-loader/index.js!../../node_modules/vue-loader/lib/style-rewriter.js?id=data-v-16c6eb66!../../node_modules/vue-loader/lib/selector.js?type=styles&index=0!./paging.vue");
     if(typeof newContent === 'string') newContent = [[module.id, newContent, '']];
     update(newContent);
   });
 }
 // When the module is disposed, remove the <style> tags
 module.hot.dispose(function() { update(); });
}

/***/ }),
/* 84 */
/***/ (function(module, exports, __webpack_require__) {

exports = module.exports = __webpack_require__(5)();
// imports


// module
exports.push([module.i, "\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n", ""]);

// exports


/***/ }),
/* 85 */
/***/ (function(module, exports) {

/**
 * Translates the list format produced by css-loader into something
 * easier to manipulate.
 */
module.exports = function listToStyles (parentId, list) {
  var styles = []
  var newStyles = {}
  for (var i = 0; i < list.length; i++) {
    var item = list[i]
    var id = item[0]
    var css = item[1]
    var media = item[2]
    var sourceMap = item[3]
    var part = {
      id: parentId + ':' + i,
      css: css,
      media: media,
      sourceMap: sourceMap
    }
    if (!newStyles[id]) {
      styles.push(newStyles[id] = { id: id, parts: [part] })
    } else {
      newStyles[id].parts.push(part)
    }
  }
  return styles
}


/***/ }),
/* 86 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
    value: true
});
//
//
//
//
//
//
//
//
//
//
//
//

exports.default = {
    data: function data() {
        return {
            // 分页数据
        };
    }
};

/***/ }),
/* 87 */
/***/ (function(module, exports, __webpack_require__) {

module.exports={render:function (){var _vm=this;var _h=_vm.$createElement;var _c=_vm._self._c||_h;
  return _vm._m(0)
},staticRenderFns: [function (){var _vm=this;var _h=_vm.$createElement;var _c=_vm._self._c||_h;
  return _c('div', {
    staticClass: "paging course-paging clearfix"
  }, [_c('ul', {
    staticClass: "clearfix"
  }, [_c('li', [_c('a', {
    attrs: {
      "href": ""
    }
  }, [_vm._v("上一页")])]), _vm._v(" "), _c('li', [_c('a', {
    staticClass: "hover",
    attrs: {
      "href": ""
    }
  }, [_vm._v("1")])]), _vm._v(" "), _c('li', [_c('a', {
    attrs: {
      "href": ""
    }
  }, [_vm._v("2")])]), _vm._v(" "), _c('li', [_c('a', {
    attrs: {
      "href": ""
    }
  }, [_vm._v("...")])]), _vm._v(" "), _c('li', [_c('a', {
    attrs: {
      "href": ""
    }
  }, [_vm._v("下一页")])])])])
}]}
module.exports.render._withStripped = true
if (false) {
  module.hot.accept()
  if (module.hot.data) {
     require("vue-hot-reload-api").rerender("data-v-16c6eb66", module.exports)
  }
}

/***/ }),
/* 88 */
/***/ (function(module, exports, __webpack_require__) {

module.exports={render:function (){var _vm=this;var _h=_vm.$createElement;var _c=_vm._self._c||_h;
  return _c('div', {
    staticClass: "wrap-course content"
  }, [_c('div', {
    staticClass: "class-wrap"
  }, [_c('div', {
    staticClass: "classify clearfix",
    on: {
      "click": _vm.clickdiv
    }
  }, [_c('ul', {
    staticClass: "fl"
  }, [(this.$parent.checked('INDEX/MAIN/TEACH')) ? _c('li', {
    on: {
      "click": function($event) {
        _vm.getCourseList('teach')
      }
    }
  }, [_c('i', {
    staticClass: "before",
    staticStyle: {
      "left": "0"
    }
  }), _vm._v(" "), _c('a', {
    staticClass: "click",
    attrs: {
      "href": "javascript:void(0);"
    }
  }, [_vm._v("我教的课程")])]) : _vm._e(), _vm._v(" "), (this.$parent.checked('INDEX/MAIN/STUDY')) ? _c('li', {
    on: {
      "click": function($event) {
        _vm.getCourseList('study')
      }
    }
  }, [_c('a', {
    attrs: {
      "href": "javascript:void(0);"
    }
  }, [_vm._v("我学的课程")])]) : _vm._e()]), _vm._v(" "), _vm._m(0)]), _vm._v(" "), _c('div', {
    staticClass: "content-list clearfix"
  }, [(_vm.currrentCourses === null) ? _c('p', {
    staticStyle: {
      "display": "block",
      "padding": "10px 0",
      "text-align": "center"
    }
  }, [_vm._v("目前还没有您的课堂数据！")]) : _vm._l((_vm.currrentCourses), function(item) {
    return _c('div', {
      staticClass: "item"
    }, [_c('router-link', {
      staticClass: "class-pic",
      attrs: {
        "to": "/course"
      }
    }, [_c('img', {
      attrs: {
        "src": item.courseImg,
        "title": "",
        "alt": ""
      }
    })]), _vm._v(" "), _c('div', {
      staticClass: "class-msg"
    }, [_c('h2', {
      staticClass: "class-title",
      attrs: {
        "data-courseid": item.courseId
      }
    }, [_vm._v(_vm._s(item.courseName))]), _vm._v(" "), _c('p', {
      staticClass: "deal-msg"
    }, [_c('span', {
      staticClass: "dealing"
    }, [_vm._v("待办(" + _vm._s(item.gtasks) + ")")]), _vm._v(" "), _c('span', {
      staticClass: "dealed"
    }, [_vm._v("已读(" + _vm._s(item.readed) + ")")])]), _vm._v(" "), _c('div', {
      staticClass: "readin"
    }, [_c('router-link', {
      attrs: {
        "to": "/course"
      }
    }, [_vm._v("进入课程")])], 1)])], 1)
  }), _vm._v(" "), _c('div', {
    staticClass: "item add-course"
  }, [_c('router-link', {
    staticClass: "course-pic",
    attrs: {
      "to": "/course/addcourses",
      "title": "添加课堂"
    }
  }, [_c('i', {
    staticClass: "icon"
  })])], 1)], 2), _vm._v(" "), _c('paging')], 1)])
},staticRenderFns: [function (){var _vm=this;var _h=_vm.$createElement;var _c=_vm._self._c||_h;
  return _c('form', {
    staticClass: "layui-form layui-form-pane term fr",
    attrs: {
      "action": ""
    }
  }, [_c('div', {
    staticClass: "layui-form-item"
  }, [_c('label', {
    staticClass: "layui-form-label"
  }, [_vm._v("学期")]), _vm._v(" "), _c('div', {
    staticClass: "layui-input-block"
  }, [_c('select', {
    attrs: {
      "name": "interest",
      "lay-filter": "term"
    }
  }, [_c('option', {
    attrs: {
      "value": ""
    }
  }), _vm._v(" "), _c('option', {
    attrs: {
      "value": "0"
    }
  }, [_vm._v("写作")]), _vm._v(" "), _c('option', {
    attrs: {
      "value": "1"
    }
  }, [_vm._v("阅读")]), _vm._v(" "), _c('option', {
    attrs: {
      "value": "2"
    }
  }, [_vm._v("游戏")]), _vm._v(" "), _c('option', {
    attrs: {
      "value": "3"
    }
  }, [_vm._v("音乐")]), _vm._v(" "), _c('option', {
    attrs: {
      "value": "4"
    }
  }, [_vm._v("旅行")])])])])])
}]}
module.exports.render._withStripped = true
if (false) {
  module.hot.accept()
  if (module.hot.data) {
     require("vue-hot-reload-api").rerender("data-v-3492e1c0", module.exports)
  }
}

/***/ }),
/* 89 */
/***/ (function(module, exports, __webpack_require__) {


/* styles */
__webpack_require__(90)

var Component = __webpack_require__(3)(
  /* script */
  __webpack_require__(92),
  /* template */
  __webpack_require__(93),
  /* scopeId */
  null,
  /* cssModules */
  null
)
Component.options.__file = "D:\\wampserver\\wamp\\www\\vue\\course-vue\\src\\components\\courseDetails.vue"
if (Component.esModule && Object.keys(Component.esModule).some(function (key) {return key !== "default" && key !== "__esModule"})) {console.error("named exports are not supported in *.vue files.")}
if (Component.options.functional) {console.error("[vue-loader] courseDetails.vue: functional components are not supported with templates, they should use render functions.")}

/* hot reload */
if (false) {(function () {
  var hotAPI = require("vue-hot-reload-api")
  hotAPI.install(require("vue"), false)
  if (!hotAPI.compatible) return
  module.hot.accept()
  if (!module.hot.data) {
    hotAPI.createRecord("data-v-0642f676", Component.options)
  } else {
    hotAPI.reload("data-v-0642f676", Component.options)
  }
})()}

module.exports = Component.exports


/***/ }),
/* 90 */
/***/ (function(module, exports, __webpack_require__) {

// style-loader: Adds some css to the DOM by adding a <style> tag

// load the styles
var content = __webpack_require__(91);
if(typeof content === 'string') content = [[module.i, content, '']];
if(content.locals) module.exports = content.locals;
// add the styles to the DOM
var update = __webpack_require__(6)("2a26f79e", content, false);
// Hot Module Replacement
if(false) {
 // When the styles change, update the <style> tags
 if(!content.locals) {
   module.hot.accept("!!../../node_modules/css-loader/index.js!../../node_modules/vue-loader/lib/style-rewriter.js?id=data-v-0642f676!../../node_modules/vue-loader/lib/selector.js?type=styles&index=0!./courseDetails.vue", function() {
     var newContent = require("!!../../node_modules/css-loader/index.js!../../node_modules/vue-loader/lib/style-rewriter.js?id=data-v-0642f676!../../node_modules/vue-loader/lib/selector.js?type=styles&index=0!./courseDetails.vue");
     if(typeof newContent === 'string') newContent = [[module.id, newContent, '']];
     update(newContent);
   });
 }
 // When the module is disposed, remove the <style> tags
 module.hot.dispose(function() { update(); });
}

/***/ }),
/* 91 */
/***/ (function(module, exports, __webpack_require__) {

exports = module.exports = __webpack_require__(5)();
// imports


// module
exports.push([module.i, "\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n", ""]);

// exports


/***/ }),
/* 92 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
    value: true
});
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//

exports.default = {
    data: function data() {
        return {
            // 课堂目录数据
            courseid: '12312312341231' // 课堂id
        };
    },
    mounted: function mounted() {

        mainTitleCut(1);
    }
};

/***/ }),
/* 93 */
/***/ (function(module, exports, __webpack_require__) {

module.exports={render:function (){var _vm=this;var _h=_vm.$createElement;var _c=_vm._self._c||_h;
  return _c('div', {
    staticClass: "class-wrap content",
    attrs: {
      "id": "course"
    }
  }, [_vm._m(0), _vm._v(" "), _c('div', {
    staticClass: "classify-wrap"
  }, [_c('div', {
    staticClass: "classify clearfix"
  }, [_c('ul', {
    staticClass: "fl"
  }, [_c('li', {
    staticClass: "check"
  }, [_c('i', {
    staticClass: "before",
    staticStyle: {
      "left": "0"
    }
  }), _vm._v(" "), _c('router-link', {
    staticClass: "click",
    attrs: {
      "to": "/course/"
    }
  }, [_vm._v("章节")])], 1), _vm._v(" "), _c('li', {}, [_c('i', {
    staticClass: "before",
    staticStyle: {
      "left": "0"
    }
  }), _vm._v(" "), _c('router-link', {
    attrs: {
      "to": "/course/tasks"
    }
  }, [_vm._v("作业")])], 1), _vm._v(" "), _vm._m(1), _vm._v(" "), _vm._m(2), _vm._v(" "), _c('li', {}, [_c('i', {
    staticClass: "before",
    staticStyle: {
      "left": "0"
    }
  }), _vm._v(" "), _c('router-link', {
    attrs: {
      "to": "/course/members"
    }
  }, [_vm._v("成员")])], 1)]), _vm._v(" "), _c('button', {}, [_vm._v("编辑")])]), _vm._v(" "), _c('router-view', {
    attrs: {
      "courseid": _vm.courseid
    }
  })], 1)])
},staticRenderFns: [function (){var _vm=this;var _h=_vm.$createElement;var _c=_vm._self._c||_h;
  return _c('aside', {
    staticClass: "top-nav"
  }, [_c('div', {
    staticClass: "cont fr"
  }, [_c('span', {
    staticClass: "cont-big"
  }, [_vm._v("课程名称")]), _vm._v(" "), _c('span', [_vm._v("专业基础课")]), _vm._v(" "), _c('div', {
    staticClass: "course-data"
  }, [_c('div', {
    staticClass: "study study-range"
  }, [_c('span', [_vm._v("适用范围")]), _vm._v(" "), _c('span', [_vm._v("移动互联方向")])]), _vm._v(" "), _c('div', {
    staticClass: "study study-class"
  }, [_c('span', [_vm._v("课程数")]), _vm._v(" "), _c('span', [_vm._v("48节")])]), _vm._v(" "), _c('div', {
    staticClass: "study study-num"
  }, [_c('span', [_vm._v("学习人数")]), _vm._v(" "), _c('span', [_vm._v("31")])])])])])
},function (){var _vm=this;var _h=_vm.$createElement;var _c=_vm._self._c||_h;
  return _c('li', {}, [_c('i', {
    staticClass: "before",
    staticStyle: {
      "left": "0"
    }
  }), _vm._v(" "), _c('a', {
    attrs: {
      "href": "javascript:void(0);"
    }
  }, [_vm._v("实验")])])
},function (){var _vm=this;var _h=_vm.$createElement;var _c=_vm._self._c||_h;
  return _c('li', {}, [_c('i', {
    staticClass: "before",
    staticStyle: {
      "left": "0"
    }
  }), _vm._v(" "), _c('a', {
    attrs: {
      "href": "javascript:void(0);"
    }
  }, [_vm._v("分组")])])
}]}
module.exports.render._withStripped = true
if (false) {
  module.hot.accept()
  if (module.hot.data) {
     require("vue-hot-reload-api").rerender("data-v-0642f676", module.exports)
  }
}

/***/ }),
/* 94 */
/***/ (function(module, exports, __webpack_require__) {

var Component = __webpack_require__(3)(
  /* script */
  __webpack_require__(95),
  /* template */
  __webpack_require__(96),
  /* scopeId */
  null,
  /* cssModules */
  null
)
Component.options.__file = "D:\\wampserver\\wamp\\www\\vue\\course-vue\\src\\components\\coursedetailslist.vue"
if (Component.esModule && Object.keys(Component.esModule).some(function (key) {return key !== "default" && key !== "__esModule"})) {console.error("named exports are not supported in *.vue files.")}
if (Component.options.functional) {console.error("[vue-loader] coursedetailslist.vue: functional components are not supported with templates, they should use render functions.")}

/* hot reload */
if (false) {(function () {
  var hotAPI = require("vue-hot-reload-api")
  hotAPI.install(require("vue"), false)
  if (!hotAPI.compatible) return
  module.hot.accept()
  if (!module.hot.data) {
    hotAPI.createRecord("data-v-581c5134", Component.options)
  } else {
    hotAPI.reload("data-v-581c5134", Component.options)
  }
})()}

module.exports = Component.exports


/***/ }),
/* 95 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
    value: true
});

function _defineProperty(obj, key, value) { if (key in obj) { Object.defineProperty(obj, key, { value: value, enumerable: true, configurable: true, writable: true }); } else { obj[key] = value; } return obj; }

//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//

exports.default = {
    data: function data() {
        var thisVue = this;
        return {
            coursedata: [],
            knobbledata: [],
            curCourse: {
                id: thisVue.courseid
            }, // ,
            errorMsgChapter: '没有当前课程的章节信息！',
            errorMsgKnobble: '该章还没有小节信息'
        };
    },
    props: {
        courseid: {
            type: String,
            required: true
        }
    },
    created: function created() {
        var _this = this;

        this.$root.Bus.$on('sendCourseToChapter', function (course) {
            _this.repuestData(_this, course);
        });
    },

    activated: function activated() {
        // 组件每次加载都会执行
        var thisVue = this;
        thisVue.repuestData(thisVue);
    },

    mounted: function mounted() {
        var bool = 1; //其中bool=1表示下拉状态，bool=0表示收缩状态
        /* 翻页 */
        $(function () {
            var allpage = 10;
            var maxPage = 5; // 最多显示5条页码

            $(".chapterpage ul").append(pageNum(allpage, maxPage));
            $(".chapterpage ul li.num").eq(0).addClass("hover");
            $(".chapterpage ul li.prev").hide();
            var index = 1; // 初始页码

            $(".chapterpage ul li.num").click(function () {
                index = Number($(this).find("a").text());
                showHide(index, $("ul li"), allpage);
                $(this).addClass("hover").siblings().removeClass("hover");
                console.log(index);
            });
            $(".chapterpage ul li.prev, .chapterpage ul li.next").click(function () {
                var other = $(this).index();
                index = prevNext(other, index, allpage);
                showHide(index, $(".chapterpage ul li"), allpage);
                togClass(index, allpage);
                console.log(index);
            });

            function prevNext(other, index, allPage) {
                if (!other) {
                    // 上一页
                    index--;
                    if (index <= 1) {
                        return index = 1;
                    }
                } else {
                    index++;
                    if (index >= allPage) {
                        return index = allPage;
                    }
                }
                return index;
            };

            function togClass(index, allPage) {
                switch (index) {
                    case 1:
                        addHover(0);
                        break;
                    case 2:
                        addHover(1);
                        break;
                    case 3:
                        addHover(2);
                        break;
                    case 4:
                        addHover(3);
                        break;
                    case allPage:
                        addHover($(".chapterpage ul li.num").length - 1);
                        break;
                    default:
                        $(".chapterpage ul li.more").eq(0).addClass("hover").siblings('li').removeClass("hover");
                }
            };

            function addHover(index) {
                $(".chapterpage ul li.num").eq(index).addClass("hover").siblings().removeClass("hover");
            };

            function showHide(index, li, allPage) {
                if (index === 1) {
                    li.eq(0).fadeOut(100);
                    li.eq(li.length - 1).fadeIn(300);
                } else if (index === allPage) {
                    li.eq(li.length - 1).fadeOut(100);
                    li.eq(0).fadeIn(300);
                } else {
                    li.fadeIn(300);
                }
            };

            function pageNum(allPage, maxPage) {
                var html = '';
                if (allPage <= 0) {
                    return html = "";
                } else if (allPage > maxPage) {
                    for (var i = 0; i < maxPage - 1; i++) {
                        html += "<li class='num'><a href='#'>" + (i + 1) + "</a></li>";
                    }
                    html = "<li class='prev'><a href='JavaScript:void(0)'>上一页</a></li>" + html + "<li class='more'><a href='#'>...</a></li><li class='num'><a href='#'>" + allPage + "</a></li><li class='next'><a href='JavaScript:void(0)'>下一页</a></li>";
                } else {
                    for (var i = 0; i < allPage; i++) {
                        html += "<li class='num'><a href='#'>" + (i + 1) + "</a></li>";
                    }
                    html = "<li class='prev'><a href='JavaScript:void(0)'>上一页</a></li>" + html + "<li class='next'><a href='JavaScript:void(0)'>下一页</a></li>";
                }
                return html;
            };
        });
    },
    methods: {
        //请求数据库
        startRequestCourse: function startRequestCourse() {
            // 首页第一次请求课程数据
            var thisVue = this;
            // 请求课程库数据
            var data = {
                'module': 'service',
                'controller': 'Data_Request',
                'action': 'courseRequest'
            };
            $.ajax({
                url: API_ENV.API_URL,
                type: 'POST',
                data: data,
                success: function success(data) {
                    console.log('课程库请求成功！', JSON.parse(data));
                    var data = JSON.parse(data);
                    window.sessionStorage.setItem('currentCourse', JSON.stringify(course));
                },
                error: function error(err) {
                    console.log('课程库请求失败！', err);
                }
            });
        },
        //请求数据
        repuestData: function repuestData(thisVue, course) {
            // this.curCourse = course || JSON.parse(window.sessionStorage.getItem('currentCourse'));
            console.log(this.courseid);
            var datalist = {
                module: 'service',
                controller: 'Course_Sections_Con',
                action: 'queSections',
                courseid: this.courseid
            };
            console.log(this.courseid);
            $.ajax({
                url: API_ENV.API_URL,
                type: "POST",
                // dataType: 'json',
                data: datalist,
                success: function success(response) {
                    var response = JSON.parse(response);
                    window.utiltool.checkLoginStatus(response.status);
                    if (!response.status && window.utiltool.isArray(response.data)) {
                        for (var i = response.data.length - 1; i >= 0; i--) {
                            if (!response.data[i].child) {
                                response.data[i].child = [];
                            }
                        }
                        thisVue.coursedata = response.data;
                        thisVue.errorMsg = '';
                        console.log('课程详细数据', response);
                    } else {
                        thisVue.errorMsg = '没有该课程的章节信息！';
                    }
                },
                error: function error(err) {
                    console.log('课程详细数据请求失败！', err);
                }
            });
        },
        //章节下拉
        dropchapters: function dropchapters(event) {
            var thiUl = $(event.path[0]).parent().siblings('ul');
            var liL = thiUl.children('li').length; //整个小节
            var thisI = $(event.path[0]).siblings('i');
            if (parseInt(thiUl.css('height'))) {
                thisI.css('background-position', '-78px -63px');
                thiUl.stop().animate({
                    height: 0
                }, 300, function () {
                    thiUl.find('.revamp').css("height", "0");
                });
            } else {
                thisI.css('background-position', '-114px -63px');
                thiUl.stop().animate({
                    height: liL * 50 + 'px'
                }, 300);
            }
        },
        //添加章节
        addchapter: function addchapter(courseIdNumber) {
            console.log(courseIdNumber);
            var thisVue = this;
            var pageChapter = $('.chapter');
            var sortArr = [];
            for (var i = pageChapter.length - 1; i >= 0; i--) {
                sortArr.push(Number(pageChapter.eq(i).data('sort')));
            }
            var maxSort = pageChapter.length ? Math.max.apply(null, sortArr) : 0;
            console.log(maxSort);
            var html = '<div id="add-layer"><div class="add-course1"><div class="course-lib"><div class="add-course-form"><div id="" class="add-form1"><form id="" class="addcourselib"><div class="wrp"><div id="add1"><p class="title"> <span><i>*</i>章节全名</span><input class="con h-name" name="fullname" id="coursetitle" type="text" autofocus placeholder="不能为空" /></p><p class="info"><span><i>*</i>章节简称</span><input class="con h-summary" name="shortname" id="coursecall" type="text" placeholder="不能为空" /></p><p class="period"><span><i>*</i>章节课时</span><input class="con h-time" name="numsections" id="courseperiod" type="number" placeholder="由正整数组成" /></p></div></div><div class="addcon"><p class="continue-add"><span class="con-add">继续添加</span></p></div></form></div></div></div></div></div>';
            //var htmlcontent = $('#add-course1').show(300);
            //弹出框
            layer.open({
                type: 1,
                title: '添加章节',
                content: html,
                area: ['700px', '400px'],
                btn: ['ok', '关闭'],
                anim: 4,
                tipsMore: true,
                yes: function yes(index, layero) {
                    var chapters = [];
                    var inputN = $('#add-layer .add-course1 p input.h-name');
                    var inputS = $('#add-layer .add-course1 p input.h-summary');
                    var inputT = $('#add-layer .add-course1 p input.h-time');
                    for (var i = inputN.length - 1; i >= 0; i--) {
                        var currentNaVal = inputN.eq(i).val().trim();
                        var currentSuVal = inputS.eq(i).val().trim();
                        var currentTaVal = inputT.eq(i).val().trim();
                        if (currentNaVal == '' || currentTaVal == '') {
                            chapters = [];
                            break;
                        } else {
                            console.log(currentNaVal);
                            chapters.push({ //添加数据
                                name: currentNaVal,
                                summary: currentSuVal,
                                section: currentTaVal,
                                sort: ''
                            });
                        }
                    }
                    if (!chapters.length) {
                        layer.msg('亲，您的课程章节数据不正常！', {
                            icon: 2,
                            time: 2000
                        });
                    } else {
                        var reChapters = chapters.reverse();
                        var reChaptersLen = reChapters.length;
                        for (var i = 0; i < reChaptersLen; i++) {
                            reChapters[i]['sort'] = maxSort + i + 1;
                        }
                        var data = {
                            'module': 'service',
                            'controller': 'Course_Sections_Con',
                            'action': 'addSections',
                            'pid': '',
                            'courseid': courseIdNumber,
                            //'courseid': '',
                            'type': '0',
                            'dataList': reChapters
                        };
                        console.log(data);
                        // console.log(chapters)

                        $.ajax({
                            url: API_ENV.API_URL,
                            type: 'POST',
                            dataType: 'json',
                            data: data,
                            success: function success(data) {
                                window.utiltool.checkLoginStatus(data.status);
                                if (data.status === 0) {
                                    var addChapter = {};
                                    for (i = 0; i < data.data.length; i++) {
                                        var _addChapter;

                                        var addChapter = (_addChapter = {
                                            id: data.data[i].courseid,
                                            name: data.data[i].name
                                        }, _defineProperty(_addChapter, 'id', data.data[i].id), _defineProperty(_addChapter, 'child', []), _defineProperty(_addChapter, 'sort', data.data[i].sort), _defineProperty(_addChapter, 'summary', data.data[i].summary), _addChapter);
                                    }
                                    console.log(addChapter);
                                    thisVue.coursedata.push(addChapter);

                                    layer.msg('添加章节成功！', {
                                        icon: 1,
                                        time: 1000
                                    }, function () {
                                        layer.closeAll();
                                    });
                                } else {
                                    layer.msg(data.data, {
                                        icon: 2,
                                        time: 2000
                                    });
                                }
                            },
                            error: function error(err) {
                                console.log('提交失败!', err);
                            }
                        });
                    }
                    layer.close(index);
                },
                btn2: function btn2(index, layero) {
                    layer.close(index);
                },
                cancel: function cancel(index, layero) {
                    var _index = index;
                    layer.confirm('亲，离开后数据不会保存，确定离开吗？', {
                        icon: 3,
                        title: '温馨提示'
                    }, function (index) {
                        layer.close(index);
                        layer.close(_index);
                    });
                    return false;
                },
                success: function success() {
                    $(".add-course1 .add-form1 .continue-add .con-add").click(function () {
                        var inputHtml = $('<div id="add2"><p class="title"><span><i>*</i>章节全名</span><input class="con" name="fullname" id="" type="text" autofocus placeholder="不能为空" /></p><p class="info"><span><i>*</i>章节简称</span><input class="con" name="shortname" id="" type="text" placeholder="不能为空" /></p><p class="period"><span><i>*</i>章节课时</span><input class="con" name="numsections" id="" type="number" placeholder="由正整数组成" /></p><i class="remove" title="删除该小节">×</i></div>');
                        inputHtml.appendTo($(".add-course1 .add-form1 .addcourselib .wrp")).animate({
                            marginTop: '0',
                            opacity: 1
                        }, 300, 'swing').find('.remove').on('click', function () {
                            var _this2 = this;

                            /* 删除弹窗继续添加章节 */
                            //var edi = document.getElementById('add1');
                            //var con = document.getElementById('add2');
                            // con.style.cssText = "margin-top: -160px; opacity: 0";
                            $(this).parent().animate({
                                marginTop: '-160px',
                                opacity: 0
                            }, 300, 'swing', function () {
                                $(_this2).parent().remove();
                            });
                        });
                    });
                }
            });
        },
        //删除章节
        deletechapter: function deletechapter(event, chapterId) {
            var thisVue = this;
            var data = {
                module: 'service',
                controller: 'Course_Sections_Con',
                action: 'delSections',
                type: '0',
                id: chapterId
            };
            console.log('删除章', data);
            var thisbtn = $(event.path[0]);
            layer.confirm('该章及其下的所有小节都会被删除，确定删除吗?', {
                icon: 3,
                title: '提示',
                btn: ['确定', '取消'],
                anim: 0,
                tipsMore: true,
                yes: function yes(index, layero) {
                    $.ajax({
                        url: API_ENV.API_URL,
                        type: 'POST',
                        data: data,
                        success: function success(data) {
                            console.log('删除章提交成功！', data);
                            var response = JSON.parse(data);
                            if (response.status == '0') {
                                layer.msg(response.data, {
                                    icon: 1,
                                    time: 1500
                                }, function () {
                                    for (var i = thisVue.coursedata.length - 1; i >= 0; i--) {
                                        if (thisVue.coursedata[i].id === chapterId) {
                                            thisVue.coursedata.removeByValue(thisVue.coursedata[i]);
                                        }
                                    }
                                });
                            } else {
                                layer.msg(response.data, {
                                    icon: 2,
                                    time: 2000
                                });
                            }
                        },
                        error: function error(err) {
                            console.log('删除章提交失败！', err);
                        }
                    });
                    layer.close(index);
                    $('.chaptermess').hide(300);
                    layer.msg('成功', {
                        icon: 1,
                        time: 3000
                    });
                },
                btn2: function btn2(index, layero) {
                    layer.close(index);
                },
                cancel: function cancel(index, layero) {
                    layer.closeAll();
                }
            });
        },
        //添加小节
        addchapterlib: function addchapterlib(event, ChapterID, courseId) {
            var thisVue = this; // vue对象
            var eventVue = event;
            var nobbleLi = $('.chapter ul li[chapterid=' + ChapterID + ']').siblings('li');
            var sortArr = [];
            for (var i = 0; i < nobbleLi.length; i++) {
                sortArr.push(Number(nobbleLi.eq(i).data('sort')));
            }
            console.log(sortArr);
            var maxSort = nobbleLi.length ? Math.max.apply(null, sortArr) : 0;
            var html = '<div id="add-layerlib"><div class="add-course1lib"><div class="course-lib"><div class="add-course-form"><div id="" class="add-form1"><form id="" class="addcourselib"><div class="wrp"><div id="libadd1"><p class="title"><span><i>*</i>小节名称</span><input class="con c-name" name="fullname" id="" type="text" autofocus placeholder="不能为空" /></p><p class="info"><span><i>*</i>小节概要</span><input class="con c-summary" name="shortname" id="" type="text" placeholder="不能为空" /></p></div></div><div class="addcon"><p class="continue-addlib"><span class="con-addlib">继续添加</span></p></div></form></div></div></div></div></div>';
            //弹出框
            layer.open({
                type: 1,
                title: '添加小节',
                content: html, //$('#add-layerlib').html() && $('.add-course1lib').show(300),
                area: ['700px', '400px'],
                btn: ['ok', '关闭'],
                anim: 4,
                tipsMore: true,
                yes: function yes(index, layero) {
                    var knobbles = []; // 保存小节信息的数组
                    var inputN = $('#add-layerlib .add-course1lib p input.c-name');
                    var inputS = $('#add-layerlib .add-course1lib p input.c-summary');
                    for (var i = inputN.length - 1; i >= 0; i--) {
                        var currentNaVal = inputN.eq(i).val().trim();
                        var currentSuVal = inputS.eq(i).val().trim();
                        if (currentNaVal == '') {
                            knobbles = [];
                            break;
                        } else {
                            knobbles.push({
                                'name': currentNaVal,
                                'summary': currentSuVal,
                                'sort': ''
                            });
                        }
                    }
                    if (!knobbles.length) {
                        layer.msg('亲，您的课程小节数据不正常！', {
                            icon: 3,
                            time: 3000
                        });
                    } else {
                        var reKnobbles = knobbles.reverse();
                        var reKnobblesLens = reKnobbles.length;
                        for (var _i = 0; _i < reKnobblesLens; _i++) {
                            reKnobbles[_i]['sort'] = maxSort + _i + 1;
                        }
                        var data = {
                            'module': 'service',
                            'controller': 'Course_Sections_Con',
                            'action': 'addSections',
                            'pid': ChapterID,
                            'courseid': courseId,
                            'type': '1',
                            'dataList': reKnobbles
                        };
                        console.log(data);
                        //console.log(knobbles)

                        $.ajax({
                            url: API_ENV.API_URL,
                            type: 'POST',
                            data: data,
                            dataType: 'json',
                            success: function success(data) {
                                console.log('提交成功！', data);
                                window.utiltool.checkLoginStatus(data.status);
                                var addknobbles = {};
                                if (data.status === 0) {
                                    for (var i = 0; i < data.data.length; i++) {
                                        for (var j = 0; j < thisVue.coursedata.length; j++) {
                                            if (thisVue.coursedata[j].id === ChapterID) {
                                                thisVue.coursedata[j].child.push(data.data[i]);
                                                break;
                                            }
                                        }
                                    }

                                    /*
                                                                            for (var i = 0; i < data.data.length; i++) {
                                                                                var addknobbles = {
                                                                                    id: '',
                                                                                    name: knobbles[i].name,
                                                                                    sort: '',
                                                                                    summary: knobbles[i].summary
                                                                                };
                                                                            }
                                                                            console.log(addknobbles)
                                                                            for (var j = 0; j < thisVue.coursedata.length; j++) {
                                                                                if (thisVue.coursedata[j].id === ChapterID) {
                                                                                    console.log(thisVue.coursedata[j]);
                                                                                    thisVue.coursedata[j].child.push(
                                                                                        addknobbles);
                                                                                    console.log(thisVue.coursedata[j].child)
                                                                                    break;
                                                                                }
                                                                             }*/
                                    //thisVue.coursedata.child.push(addknobbles);
                                    layer.msg('添加小节成功！', {
                                        icon: 1,
                                        time: 1000
                                    }, function () {
                                        layer.closeAll();
                                    });
                                } else {
                                    layer.msg(data.data, {
                                        icon: 2,
                                        time: 1500
                                    });
                                }
                            },
                            error: function error(err) {
                                console.log('提交失败!', err);
                            }
                        });
                    }
                    layer.close(index);
                },
                btn2: function btn2(index, layero) {
                    layer.close(index);
                },
                cancel: function cancel(index, layero) {
                    var _index = index;
                    layer.confirm('亲，离开后数据不会保存，确定离开吗？', {
                        icon: 3,
                        title: '温馨提示'
                    }, function (index) {
                        layer.close(index);
                        layer.close(_index);
                    });
                    return false;
                },
                success: function success() {
                    $(".add-course1lib .add-form1 .continue-addlib .con-addlib").click(function () {
                        var inputHtml = $(' <div id="libadd2"><p class="title"><span>小节名称</span><input class="con" name="fullname" id="" type="text" autofocus placeholder="不能为空" /></p><p class="info"><span><i>*</i>小节概要</span><input class="con" name="shortname" id="" type="text" placeholder="不能为空" /></p><i class="removelib" title="删除该小节">×</i></div>');
                        inputHtml.appendTo($(".add-course1lib .add-form1 .addcourselib .wrp")).animate({
                            marginTop: '0px',
                            opacity: 1
                        }, 300, 'swing').find('.removelib').on('click', function () {
                            var _this3 = this;

                            /* 删除弹窗继续添加小节 */
                            //var edi = document.getElementById('libadd1');
                            //var con = document.getElementById('libadd2');
                            //con.style.cssText = "margin-top: -120px; opacity: 0";
                            $(this).parent().animate({
                                marginTop: '-120px',
                                opacity: 0
                            }, 300, 'swing', function () {
                                $(_this3).parent().remove();
                            });
                        });
                    });
                }
            });
        },
        //删除小节
        deletechapterlib: function deletechapterlib(event, chapterid, LibID) {
            var thisVue = this;
            var data = {
                module: 'service',
                controller: 'Course_Sections_Con',
                action: 'delSections',
                type: '1',
                id: LibID
            };
            console.log('删除小节的数据', data);
            var thislibbtn = $(event.path[0]);
            layer.confirm('该章及其下的所有小节都会被删除，确定删除吗?', {
                icon: 3,
                title: '提示',
                btn: ['确定', '取消'],
                anim: 0,
                tipsMore: true,
                yes: function yes(index, layero) {
                    //thislibbtn.parent().parent().parent().parent().remove();
                    $.ajax({
                        url: API_ENV.API_URL,
                        type: 'POST',
                        data: data,
                        success: function success(data) {
                            var response = JSON.parse(data);
                            window.utiltool.checkLoginStatus(response.status);
                            if (!Number(response.status)) {
                                for (var i = 0; i < thisVue.coursedata.length; i++) {
                                    var currentVal = thisVue.coursedata[i];
                                    if (currentVal.id === chapterid) {
                                        for (var j = 0; j < currentVal.child.length; j++) {
                                            if (currentVal.child[j].id === LibID) {
                                                currentVal.child.removeByValue(currentVal.child[j]);
                                                break;
                                            }
                                        }
                                        break;
                                    }
                                }
                            } else {
                                layer.msg(response.data, {
                                    icon: 2,
                                    time: 2000
                                });
                            }
                        },
                        error: function error(err) {
                            console.log('删除小节提交失败！', err);
                        }
                    });
                    layer.close(index);
                    $('.chaptermess').hide(300);
                    layer.msg('成功', {
                        icon: 1,
                        time: 3000
                    });
                },
                btn2: function btn2(index, layero) {
                    layer.close(index);
                },
                cancel: function cancel(index, layero) {
                    layer.closeAll();
                }
            });
        },
        //小节编辑
        editchapterlib: function editchapterlib(event) {
            var libedit = $(event.path[0]).parent().parent().parent().siblings('div');
            var NewValue = $(event.path[0]).parent().parent().parent().parent('li');
            var thisEvent = event;
            var AllChapter = $(event.path[0]).parent().parent().parent().parent().parent('ul');
            var liL = AllChapter.children('li').length; //整个小节
            var libeditcancel = $(event.path[0]).parent().parent().parent().parent();
            AllChapter.stop().animate({
                height: liL * 50 + 155 + 'px'
            }, 300);
            libedit.stop().animate({
                height: '155px'
            }, 300);
            var submit = $(event.path[0]).parents(".knobble-list").find(".revamp li.operation p span.submit");
            var cancel = $(event.path[0]).parents(".knobble-list").find(".revamp li.operation p span.cancel");
            //小节编辑取消
            cancel.on("click", function () {

                libedit.stop().animate({
                    height: 0
                }, 300);
                AllChapter.stop().animate({
                    height: liL * 50 + 'px'
                }, 300);
            });
            //小节更新
            submit.on("click", function () {
                var knobbleId = $(this).parents('.knobble-list').find('.knobble-title small').text();
                var reName = $(this).parents('ul.clearfix').find('p.name input').val().trim();
                var reSummary = $(this).parents('ul.clearfix').find('p.summary textarea').val().trim();
                if (!reName) {
                    layer.msg('不能修改小节名称为空！', {
                        icon: 2,
                        time: 2000
                    });
                } else {
                    var data = {
                        module: 'service',
                        controller: 'Course_Sections_Con',
                        action: 'upSections',
                        courseid: $('.mod-chapters .add-chapter .chapter-title').attr('courseId'),
                        id: knobbleId,
                        type: '1',
                        name: reName,
                        summary: reSummary
                    };
                    console.log(data);
                    $.ajax({
                        url: API_ENV.API_URL,
                        type: 'POST',
                        data: data,
                        success: function success(data) {
                            var response = JSON.parse(data);
                            window.utiltool.checkLoginStatus(response.status);
                            if (!Number(response.status)) {
                                layer.msg(response.data, {
                                    icon: 1,
                                    time: 1500
                                }, function () {
                                    NewValue.find('.knobble-title span.move').text(reName);
                                    NewValue.find('.revamp p input').val(reName);
                                    NewValue.find('.revamp p.summary textarea').val(reSummary);
                                });
                            } else {
                                layer.msg(response.data, {
                                    icon: 2,
                                    time: 2000
                                });
                            }
                            // 解除点击事件
                            submit.off('click');
                            //实现cancel的动作
                            libedit.stop().animate({
                                height: 0
                            }, 300);
                            AllChapter.stop().animate({
                                height: liL * 50 + 'px'
                            }, 300);
                        },
                        error: function error(err) {
                            console.log('小节修改提交失败！', err);
                        }
                    });
                }
            });
        },
        //传递参数
        sendChapterToEdit: function sendChapterToEdit(chapter) {
            this.$root.Bus.$emit("sendChapter", chapter);
        },
        //监听
        watch: {
            "coursedata": {
                handler: function handler(val, oldval) {
                    console.log('章节数据变化监测');
                    console.log(val, oldval);
                },
                deep: true
            }
        },
        beforeDestroy: function beforeDestroy() {
            this.$root.Bus.$off('sendCourseToChapter');
            // window.sessionStorage.removeItem('currentCourseId');
        }

    }
};

/***/ }),
/* 96 */
/***/ (function(module, exports, __webpack_require__) {

module.exports={render:function (){var _vm=this;var _h=_vm.$createElement;var _c=_vm._self._c||_h;
  return _c('div', [_c('div', {
    staticClass: "course-con"
  }, [_c('div', {
    staticClass: "container"
  }, [_c('div', {
    staticClass: "course-list"
  }, [_c('div', {
    staticClass: "mod-chapters"
  }, [(_vm.coursedata.length > 0) ? _vm._l((_vm.coursedata), function(chapter) {
    return _c('div', {
      staticClass: "chapter",
      attrs: {
        "data-sort": chapter.sort
      }
    }, [_c('h3', [_c('i', {
      staticClass: "icon-c"
    }), _vm._v(" "), _c('strong', {
      staticClass: "chapter-titles",
      on: {
        "click": function($event) {
          _vm.dropchapters($event)
        }
      }
    }, [_vm._v(_vm._s(chapter.name))]), _vm._v(" "), _c('p', {
      staticClass: "fr"
    }, [_c('span', {
      staticClass: "chapter-edit",
      on: {
        "click": function($event) {
          _vm.sendChapterToEdit(chapter)
        }
      }
    }, [_c('router-link', {
      staticClass: "chapteredit",
      attrs: {
        "to": "/course/editchapter"
      }
    }, [_vm._v("编辑")])], 1), _vm._v(" "), _c('span', {
      staticClass: "chapter-delete"
    }, [_c('a', {
      staticClass: "chapterdelete",
      attrs: {
        "href": "javascript:void(0)"
      },
      on: {
        "click": function($event) {
          _vm.deletechapter($event, chapter.id)
        }
      }
    }, [_vm._v("删除")])])])]), _vm._v(" "), _c('ul', {
      staticClass: "sortable chapter-lists"
    }, [(chapter.child.length < 1) ? _c('p', {
      staticStyle: {
        "text-align": "center"
      },
      domProps: {
        "textContent": _vm._s(_vm.errorMsgKnobble)
      }
    }) : _vm._l((chapter.child), function(item) {
      return _c('li', {
        staticClass: "knobble-list drag",
        attrs: {
          "data-sort": "1",
          "data-sort": item.sort
        }
      }, [_c('div', {
        staticClass: "knobble-title"
      }, [_c('small', {
        staticStyle: {
          "display": "none"
        }
      }, [_vm._v(_vm._s(item.id))]), _c('span', {
        staticClass: "move"
      }, [_vm._v(_vm._s(item.name))]), _vm._v(" "), _c('div', {
        staticClass: "btn-operation fr"
      }, [_c('span', {
        staticClass: "btn"
      }, [_c('a', {
        staticClass: "chapterlibedit",
        attrs: {
          "href": "javascript:void(0)"
        },
        on: {
          "click": function($event) {
            _vm.editchapterlib($event)
          }
        }
      }, [_vm._v("修改")])]), _vm._v(" "), _c('span', {
        staticClass: "btn libdelete"
      }, [_c('a', {
        staticClass: "chapterlibdelete",
        attrs: {
          "href": "javascript:void(0)"
        },
        on: {
          "click": function($event) {
            _vm.deletechapterlib($event, chapter.id, item.id)
          }
        }
      }, [_vm._v("删除")])])])]), _vm._v(" "), _c('div', {
        staticClass: "revamp"
      }, [_c('ul', {
        staticClass: "clearfix"
      }, [_c('li', [_c('h4', [_vm._v("快速编辑")]), _vm._v(" "), _c('p', {
        staticClass: "name"
      }, [_c('span', [_vm._v("名称")]), _vm._v(" "), _c('input', {
        attrs: {
          "type": "text"
        },
        domProps: {
          "value": item.name
        }
      })])]), _vm._v(" "), _c('li', [_c('h5', [_vm._v("摘要")]), _vm._v(" "), _c('p', {
        staticClass: "summary"
      }, [_c('textarea', {
        attrs: {
          "name": "",
          "id": "",
          "cols": "",
          "rows": ""
        }
      }, [_vm._v(_vm._s(item.summary))])])]), _vm._v(" "), _c('li', {
        staticClass: "operation"
      }, [_c('p', {
        staticClass: "deal"
      }, [_c('span', {
        staticClass: "cancel"
      }, [_vm._v("取消")]), _vm._v(" "), _c('span', {
        staticClass: "submit"
      }, [_vm._v("更新")])])])])])])
    }), _vm._v(" "), _c('li', {
      staticClass: "other btn-addlib",
      attrs: {
        "chapterid": chapter.id,
        "id": ""
      },
      on: {
        "click": function($event) {
          _vm.addchapterlib($event, chapter.id, _vm.curCourse.id)
        }
      }
    }, [_vm._v("添加小节")])], 2)])
  }) : _c('p', {
    staticStyle: {
      "text-align": "center"
    },
    domProps: {
      "textContent": _vm._s(_vm.errorMsgChapter)
    }
  }), _vm._v(" "), _c('div', {
    staticClass: "add-chapter"
  }, [_c('h3', [_c('strong', {
    staticClass: "chapter-title",
    attrs: {
      "courseId": _vm.curCourse.id,
      "id": "add-chapterlib"
    },
    on: {
      "click": function($event) {
        _vm.addchapter(_vm.curCourse.id)
      }
    }
  }, [_c('a', {
    attrs: {
      "href": "javascript:void(0);"
    }
  }, [_vm._v("添加章节")])])])])], 2)])])]), _vm._v(" "), _vm._m(0)])
},staticRenderFns: [function (){var _vm=this;var _h=_vm.$createElement;var _c=_vm._self._c||_h;
  return _c('div', {
    staticClass: "chapterpage"
  }, [_c('ul')])
}]}
module.exports.render._withStripped = true
if (false) {
  module.hot.accept()
  if (module.hot.data) {
     require("vue-hot-reload-api").rerender("data-v-581c5134", module.exports)
  }
}

/***/ }),
/* 97 */
/***/ (function(module, exports, __webpack_require__) {

var Component = __webpack_require__(3)(
  /* script */
  __webpack_require__(98),
  /* template */
  __webpack_require__(99),
  /* scopeId */
  null,
  /* cssModules */
  null
)
Component.options.__file = "D:\\wampserver\\wamp\\www\\vue\\course-vue\\src\\components\\tasklist.vue"
if (Component.esModule && Object.keys(Component.esModule).some(function (key) {return key !== "default" && key !== "__esModule"})) {console.error("named exports are not supported in *.vue files.")}
if (Component.options.functional) {console.error("[vue-loader] tasklist.vue: functional components are not supported with templates, they should use render functions.")}

/* hot reload */
if (false) {(function () {
  var hotAPI = require("vue-hot-reload-api")
  hotAPI.install(require("vue"), false)
  if (!hotAPI.compatible) return
  module.hot.accept()
  if (!module.hot.data) {
    hotAPI.createRecord("data-v-09f044a4", Component.options)
  } else {
    hotAPI.reload("data-v-09f044a4", Component.options)
  }
})()}

module.exports = Component.exports


/***/ }),
/* 98 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
    value: true
});
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//

exports.default = {
    data: function data() {
        var thisVue = this;
        return {
            // courseid: thisVue.$route.query.td,// 课程id
            homeworklist: { // 课程 作业 数据
                taskdata: new Array()
            },
            selitem: new Array(), // 保存选中的数据
            page: 1, // 当前页码，默认第一页
            allpage: null, // 总页码,
            rows: 4,
            currentOrg: '', // 当前机构的id 用于翻页的请求
            max_page: 5 //最大数字显示
        };
    },
    props: {
        courseid: {
            type: String,
            required: true
        }
    },
    mounted: function mounted() {
        console.log(this.courseid);
        this.requestHomework(this.page, this.rows);
    },
    activated: function activated() {
        this.$nextTick(function () {
            layui.use(['form'], function () {
                var form = layui.form;
                form.render();
            });
        });
    },
    filters: {
        fnIssue: function fnIssue(value) {
            if (value == '0') {
                return '未发布';
            } else {
                return '已发布';
            }
        },
        fnannounceAnswer: function fnannounceAnswer(value) {
            if (value == '0') {
                return '未公布';
            } else {
                return '已公布';
            }
        }
    },
    methods: {
        fnIssueLayer: function fnIssueLayer(sectionid, homewid) {
            // 发布作业
            fnReqIssueLayer(homewid, this.homeworklist.courseid, sectionid);
        },
        requestHomework: function requestHomework(page, rows) {
            // 请求课程数据 请求作业数据
            var thisVue = this;
            if (this.courseid) {
                var data = {
                    id: thisVue.courseid,
                    section: thisVue.$route.query.sd || '', // 章节id
                    row: rows,
                    page: page
                };
                console.log('请求课程和作业列表提交的数据', data);
                // TODO 请求课程和作业列表数据
                this.allpage = 10;
                this.homeworklist = {
                    courseid: thisVue.courseid,
                    coursename: '课堂全名',
                    total: '数据总条数',
                    taskdata: [{
                        id: '123123',
                        name: '作业名称',
                        type: '作业类型',
                        innumber: '作业编号',
                        issue: '0',
                        announce_answer: '0',
                        sectionid: '1231231231',
                        sectionname: '章节1'
                    }, {
                        id: '12315467',
                        name: '作业名称',
                        type: '作业类型',
                        innumber: '作业编号',
                        issue: '0',
                        announce_answer: '0',
                        sectionid: '1231231231',
                        sectionname: '章节2'
                    }, {
                        id: '1231233',
                        name: '作业名称',
                        type: '作业类型',
                        innumber: '作业编号',
                        issue: '0',
                        announce_answer: '0',
                        sectionid: '1231231231',
                        sectionname: '章节3'
                    }, {
                        id: '123193',
                        name: '作业名称',
                        type: '作业类型',
                        innumber: '作业编号',
                        issue: '0',
                        announce_answer: '0',
                        sectionid: '1231231231',
                        sectionname: '章节4'
                    }]
                };
            } else {
                this.$router.go(-1);
            }
        },
        fnSelOne: function fnSelOne(event, task) {
            // 选择一条数据
            $('.mod-chapters .chapter span.selitem').removeClass('checked');
            $(event.path[0]).find('.selitem').addClass('checked');
            $(event.path[0]).addClass('hover').siblings().removeClass('hover');
            this.selitem = new Array();
            this.selitem.push(task);
        },
        fnSelOnes: function fnSelOnes(event) {
            // 复选框选中多个
            $(event.path[0]).toggleClass('checked');
            var li = $(event.path[1]);
            if ($(event.path[0]).hasClass('checked')) {
                li.addClass('hover');
            } else {
                li.removeClass('hover');
            }
            this.fnGetAllSel();
        },
        fnSelAll: function fnSelAll(event) {
            // 全选或者全不选
            var li = $(".mod-chapters .chapter .chapter-list li");
            $(event.path[0]).toggleClass('checked');
            if ($(event.path[0]).hasClass('checked')) {
                li.addClass('hover');
                li.find('.selitem').addClass('checked');
            } else {
                li.removeClass('hover');
                li.find('.selitem').removeClass('checked');
            }
            this.fnGetAllSel();
        },
        fnGetAllSel: function fnGetAllSel() {
            // 提取选中的数据
            var thisVue = this;
            var li = $(".mod-chapters .chapter .chapter-list li");
            var arr = [];
            for (var i = li.length - 1; i >= 0; i--) {
                if (li.eq(i).hasClass('hover')) {
                    var id = li.eq(i).data('id');
                    for (var j = 0, item; item = thisVue.homeworklist.taskdata[j]; j++) {
                        if (item.id == id) {
                            arr.push(item);
                            break;
                        }
                    }
                }
            }
            thisVue.selitem = arr;
        },
        getPageHtml: function getPageHtml() {
            // 生成翻页html结构 最多显示max_page个页码
            // $(".course-lib .course-paging ul").html('');// 清除已有内容
            var thisVue = this;
            liAppendDom(thisVue.allpage, thisVue.max_page, $(".course-paging ul.clearfix"), $(".course-paging ul.pagesign span.num[name = all]"), function () {
                $(".course-paging ul.clearfix").on('click', '.num, .prev, .next, .first_page, .last_page', function () {
                    var parent = $(this).parent();
                    var li = parent.find("li");
                    var linum = parent.find("li.num");
                    var limore = parent.find("li.more");
                    var liprev = parent.find("li.prev");
                    if ($(this).hasClass('num')) {
                        var index = Number($(this).find("a").text()); //点击的数字
                        if (index !== thisVue.page) {
                            thisVue.page = index;
                        } else {
                            return;
                        }
                    } else if ($(this).hasClass("prev") || $(this).hasClass("next") || $(this).hasClass("first_page") || $(this).hasClass("last_page")) {
                        var classname = $(this).attr('class');
                        var index = 1;
                        if ($(this).hasClass("first_page")) {
                            index = 1;
                        } else if ($(this).hasClass("last_page")) {
                            index = thisVue.allpage;
                        } else {
                            index = prevNext(classname, thisVue.page, thisVue.allpage);
                        }
                        if (index !== thisVue.page) {
                            thisVue.page = index;
                        } else {
                            return;
                        }
                    }
                    $(this).addClass("hover").siblings().removeClass("hover");
                    alterPage(index, thisVue.allpage, thisVue.max_page, liprev, linum, limore);
                    addHover(parent, index);
                    $(".course-paging ul.pagesign span.num[name = current]").text(" " + index + " ");
                    showHide(thisVue.page, li, thisVue.allpage);
                    thisVue.requestHomework(thisVue.page, thisVue.rows);
                });
            });
        },
        fnDelhomework: function fnDelhomework() {
            // 删除作业
            var thisVue = this;
            var data = function () {
                var arr = [];
                for (var i = 0, item; item = thisVue.selitem[i]; i++) {
                    arr.push({
                        id: item.id,
                        course: thisVue.courseid,
                        section: item.sectionid
                    });
                }
                return arr;
            }();
            console.log('删除的数据', data);
        },
        fnBack: function fnBack() {
            // 返回
            this.$router.go(-1);
        }
    },
    watch: {
        allpage: function allpage() {
            // 页码发生变化时，从新生成页码
            var ThisVue = this;
            $(".course-paging ul.clearfix").off('click', '.num, .prev, .next, .first_page, .last_page').find('li').remove();
            ThisVue.$nextTick(function () {
                ThisVue.getPageHtml();
            });
        }
    }
};

/***/ }),
/* 99 */
/***/ (function(module, exports, __webpack_require__) {

module.exports={render:function (){var _vm=this;var _h=_vm.$createElement;var _c=_vm._self._c||_h;
  return _c('div', {
    attrs: {
      "id": "room-taskes"
    }
  }, [_c('div', {
    staticClass: "task-con"
  }, [_c('div', {
    staticClass: "container"
  }, [_c('div', {
    staticClass: "course-top"
  }, [_c('div', {
    staticClass: "operation-bar",
    staticStyle: {
      "border-top": "none"
    }
  }, [_c('div', {
    staticClass: "btn-arr"
  }, [_c('a', {
    staticClass: "btn-item back",
    attrs: {
      "href": "javascript: void(0);"
    },
    on: {
      "click": function($event) {
        _vm.fnBack()
      }
    }
  }, [_vm._v("返回")]), _vm._v(" "), _c('router-link', {
    staticClass: "btn-item add",
    attrs: {
      "to": {
        path: '/course/addtasks',
        query: {
          cd: _vm.courseid
        }
      }
    }
  }, [_vm._v("添加")]), _vm._v(" "), (_vm.selitem.length >= 1) ? _c('a', {
    staticClass: "btn-item del",
    attrs: {
      "href": "javascript: void(0);"
    },
    on: {
      "click": function($event) {
        _vm.fnDelhomework()
      }
    }
  }, [_vm._v("删除")]) : _vm._e()], 1), _vm._v(" "), _vm._m(0)])]), _vm._v(" "), _c('div', {
    staticClass: "course-list"
  }, [_c('div', {
    staticClass: "mod-chapters"
  }, [_c('div', {
    staticClass: "chapter"
  }, [_c('h3', [_c('span', {
    staticClass: "selitem",
    on: {
      "click": function($event) {
        _vm.fnSelAll($event)
      }
    }
  }), _vm._v(" "), _c('strong', {
    staticClass: "chapter-title"
  }, [_vm._v("作业名称")]), _vm._v(" "), _c('strong', {
    staticClass: "chapter-other1"
  }, [_vm._v("章节")]), _vm._v(" "), _c('strong', {
    staticClass: "chapter"
  }, [_vm._v("作业编号")]), _vm._v(" "), _c('strong', {
    staticClass: "chapter"
  }, [_vm._v("发布状态")]), _vm._v(" "), _c('strong', {
    staticClass: "chapter"
  }, [_vm._v("公布答案")]), _vm._v(" "), _c('strong', {
    staticClass: "chapter details"
  }, [_vm._v("详情")])]), _vm._v(" "), (_vm.homeworklist.taskdata.length > 0) ? [_c('ul', {
    staticClass: "chapter-list fx"
  }, _vm._l((_vm.homeworklist.taskdata), function(item) {
    return _c('li', {
      attrs: {
        "data-id": item.id
      },
      on: {
        "click": function($event) {
          _vm.fnSelOne($event, item)
        }
      }
    }, [_c('span', {
      staticClass: "selitem",
      on: {
        "click": function($event) {
          $event.stopPropagation();
          _vm.fnSelOnes($event)
        }
      }
    }), _vm._v(" "), _c('span', {
      staticClass: "task-title"
    }, [_vm._v(_vm._s(item.name))]), _vm._v(" "), _c('span', {
      staticClass: "task other"
    }, [_vm._v(_vm._s(item.sectionname))]), _vm._v(" "), _c('span', {
      staticClass: "task-other"
    }, [_vm._v(_vm._s(item.innumber))]), _vm._v(" "), _c('span', {
      staticClass: "task-other",
      on: {
        "click": function($event) {
          _vm.fnIssueLayer(item.sectionid, item.id)
        }
      }
    }, [_vm._v(_vm._s(_vm._f("fnIssue")(item.issue)))]), _vm._v(" "), _c('span', {
      staticClass: "task-other"
    }, [_vm._v(_vm._s(_vm._f("fnannounceAnswer")(item.announce_answer)))]), _vm._v(" "), _c('router-link', {
      attrs: {
        "tag": "span",
        "to": {
          path: 'subtask',
          query: {
            cd: _vm.courseid,
            sd: item.sectionid,
            td: item.id
          }
        }
      }
    }, [_vm._v("提交作业")]), _vm._v(" "), _c('router-link', {
      attrs: {
        "tag": "span",
        "to": {
          path: 'readtasks',
          query: {
            cd: _vm.courseid,
            sd: item.sectionid,
            td: item.id,
            tp: 'edit'
          }
        }
      }
    }, [_vm._v("编辑")]), _vm._v(" "), _c('router-link', {
      staticClass: "btn-operation",
      attrs: {
        "tag": "span",
        "to": {
          path: 'readtasks',
          query: {
            cd: _vm.courseid,
            sd: item.sectionid,
            td: item.id,
            tp: 'show'
          }
        }
      }
    }, [_vm._v("查看内容")])], 1)
  }))] : _c('p', {
    staticStyle: {
      "text-align": "center"
    }
  }, [_vm._v("该课程目前还没有作业！")])], 2)])])])]), _vm._v(" "), _vm._m(1)])
},staticRenderFns: [function (){var _vm=this;var _h=_vm.$createElement;var _c=_vm._self._c||_h;
  return _c('div', {
    staticClass: "search-user type-search fr clearfix"
  }, [_c('div', {
    staticClass: "input"
  }, [_c('input', {
    attrs: {
      "type": "search",
      "placeholder": "请输入关键词搜索"
    }
  }), _vm._v(" "), _c('div', {
    staticClass: "sel-type"
  }, [_c('form', {
    staticClass: "layui-form",
    attrs: {
      "action": ""
    }
  }, [_c('div', {
    staticClass: "layui-form-item"
  }, [_c('div', {
    staticClass: "layui-input-inline"
  }, [_c('select', {
    attrs: {
      "name": "interest",
      "lay-filter": "search-type"
    }
  }, [_c('option', {
    attrs: {
      "value": "0"
    }
  }, [_vm._v("作业名")]), _vm._v(" "), _c('option', {
    attrs: {
      "value": "1",
      "selected": ""
    }
  }, [_vm._v("阅读")]), _vm._v(" "), _c('option', {
    attrs: {
      "value": "2"
    }
  }, [_vm._v("游戏")]), _vm._v(" "), _c('option', {
    attrs: {
      "value": "3"
    }
  }, [_vm._v("音乐")]), _vm._v(" "), _c('option', {
    attrs: {
      "value": "4"
    }
  }, [_vm._v("旅行")])])])])])])]), _vm._v(" "), _c('p', {
    staticClass: "search-btn fr"
  }, [_vm._v("搜索")])])
},function (){var _vm=this;var _h=_vm.$createElement;var _c=_vm._self._c||_h;
  return _c('div', {
    staticClass: "paging course-paging clearfix"
  }, [_c('ul', {
    staticClass: "pagesign"
  }, [_c('li', {
    staticClass: "sign"
  }, [_vm._v("当前第"), _c('span', {
    staticClass: "num",
    attrs: {
      "name": "current"
    }
  }, [_vm._v(" 1 ")]), _vm._v("页，共"), _c('span', {
    staticClass: "num",
    attrs: {
      "name": "all"
    }
  }, [_vm._v(" 1 ")]), _vm._v("页")])]), _vm._v(" "), _c('ul', {
    staticClass: "clearfix"
  })])
}]}
module.exports.render._withStripped = true
if (false) {
  module.hot.accept()
  if (module.hot.data) {
     require("vue-hot-reload-api").rerender("data-v-09f044a4", module.exports)
  }
}

/***/ }),
/* 100 */
/***/ (function(module, exports, __webpack_require__) {


/* styles */
__webpack_require__(101)

var Component = __webpack_require__(3)(
  /* script */
  __webpack_require__(103),
  /* template */
  __webpack_require__(104),
  /* scopeId */
  null,
  /* cssModules */
  null
)
Component.options.__file = "D:\\wampserver\\wamp\\www\\vue\\course-vue\\src\\components\\addTask.vue"
if (Component.esModule && Object.keys(Component.esModule).some(function (key) {return key !== "default" && key !== "__esModule"})) {console.error("named exports are not supported in *.vue files.")}
if (Component.options.functional) {console.error("[vue-loader] addTask.vue: functional components are not supported with templates, they should use render functions.")}

/* hot reload */
if (false) {(function () {
  var hotAPI = require("vue-hot-reload-api")
  hotAPI.install(require("vue"), false)
  if (!hotAPI.compatible) return
  module.hot.accept()
  if (!module.hot.data) {
    hotAPI.createRecord("data-v-2faeaf56", Component.options)
  } else {
    hotAPI.reload("data-v-2faeaf56", Component.options)
  }
})()}

module.exports = Component.exports


/***/ }),
/* 101 */
/***/ (function(module, exports, __webpack_require__) {

// style-loader: Adds some css to the DOM by adding a <style> tag

// load the styles
var content = __webpack_require__(102);
if(typeof content === 'string') content = [[module.i, content, '']];
if(content.locals) module.exports = content.locals;
// add the styles to the DOM
var update = __webpack_require__(6)("54d482a7", content, false);
// Hot Module Replacement
if(false) {
 // When the styles change, update the <style> tags
 if(!content.locals) {
   module.hot.accept("!!../../node_modules/css-loader/index.js!../../node_modules/vue-loader/lib/style-rewriter.js?id=data-v-2faeaf56!../../node_modules/vue-loader/lib/selector.js?type=styles&index=0!./addTask.vue", function() {
     var newContent = require("!!../../node_modules/css-loader/index.js!../../node_modules/vue-loader/lib/style-rewriter.js?id=data-v-2faeaf56!../../node_modules/vue-loader/lib/selector.js?type=styles&index=0!./addTask.vue");
     if(typeof newContent === 'string') newContent = [[module.id, newContent, '']];
     update(newContent);
   });
 }
 // When the module is disposed, remove the <style> tags
 module.hot.dispose(function() { update(); });
}

/***/ }),
/* 102 */
/***/ (function(module, exports, __webpack_require__) {

exports = module.exports = __webpack_require__(5)();
// imports


// module
exports.push([module.i, "\n.layui-form{\n    margin: 20px auto;\n}\n.layui-field-box{\n    padding: 10px 0;\n}\n", ""]);

// exports


/***/ }),
/* 103 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
    value: true
});
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//

exports.default = {
    data: function data() {
        return {
            course: {
                id: '1231231',
                name: '课程名'
            },
            chapter: [{
                id: '1231123123',
                name: '第一章'
            }, {
                id: '12312153123',
                name: '第二章'
            }, {
                id: '12316733123',
                name: '第三章'
            }, {
                id: '1231212323',
                name: '第四章'
            }, {
                id: '123167123',
                name: '第五章'
            }],
            knob: [{
                id: '1231',
                name: '第一节'
            }, {
                id: '342',
                name: '第二节'
            }, {
                id: '12316733234123',
                name: '第三节'
            }, {
                id: '123121235356323',
                name: '第四节'
            }, {
                id: '1231',
                name: '第五节'
            }],
            homeworkupfile: {
                listdata: new Array(), // 列表循环显示的数据
                files: new Array(), // 文件列表
                msglist: new Array() // 文件描述列表
            }, // 保存上传的文件
            answerupfile: {
                listdata: new Array(), // 列表循环显示的数据
                files: new Array(), // 文件列表
                msglist: new Array() // 文件描述列表
            },
            answernum: 1 // 答案的个数
        };
    },
    activated: function activated() {
        var thisVue = this;
        layui.use('form', function () {
            var form = layui.form;

            form.on('select(section)', function (data) {
                console.log('章节的下拉选项数据', data);
            });
            form.on('submit(submit)', function (data) {
                console.log(data);
                thisVue.fnSubmit();
                return false;
            });
            form.render();
        });
    },
    mounted: function mounted() {
        layui.use('form', function () {
            var form = layui.form;
            form.render();
        });
        this.fnGetEdit("#editor-homework-bar", "#editor-homework-con");
        this.fnGetEdit("#editor-answer-bar1", "#editor-answer-con1");
    },
    computed: {},
    filters: {},
    methods: {
        fnBack: function fnBack() {
            this.$router.go(-1);
        },
        fnGetEdit: function fnGetEdit(bar, con) {
            var Editor = window.wangEditor;
            var editor = new Editor(bar, con);
            // 通过 url 参数配置 debug 模式。url 中带有 wangeditor_debug_mode=1 才会开启 debug 模式
            editor.customConfig.debug = location.href.indexOf('wangeditor_debug_mode=1') > 0;
            editor.customConfig.uploadImgShowBase64 = true;
            editor.customConfig.zIndex = 100;
            editor.create();
        },
        fnListenerUpFile: function fnListenerUpFile(event, upfile, sign) {
            // 监听文件上传之添加文件
            var thisVue = this;
            var filelist = event.target.files;
            var filtered = window.utiltool.dealFileFilter({
                filtes: filelist,
                sign: sign,
                msglist: upfile.msglist
            });
            for (var i = 0; i < filtered.length; i++) {
                upfile.files.push(filtered[i].file);
                upfile.listdata.push({
                    localurl: window.utiltool.funGetUrlByType(filtered[i].file, filtered[i].type),
                    name: filtered[i].file.name,
                    size: filtered[i].formatFileSize
                });
            }
        },
        fnDelFile: function fnDelFile(event, index, upfile) {
            // 移除添加的文件
            console.log(event, index);
            upfile.listdata.removeByValue(upfile.listdata[index]);
            upfile.files.removeByValue(upfile.files[index]);
            upfile.msglist.removeByValue(upfile.msglist[index]);
        },
        fnAddSummaryToFile: function fnAddSummaryToFile(event, index, upfile) {
            // 给上传的文件添加描述
            upfile.msglist[index].summary = $(event.path[0]).val();
        },
        fnSubmit: function fnSubmit() {
            // 提交添加的作业
            var thisVue = this;
            console.log('thisVue.$refs', thisVue.$refs);
            var homeworkcon = thisVue.$refs.homeworkcon.children[0].innerHTML;
            // 作业内容不能为空
            if (new RegExp(regexEnum.notempty).test(thisVue.$refs.homeworkcon.children[0].innerText)) {
                var data = {
                    courseid: thisVue.course.id,
                    homework: {
                        name: thisVue.$refs.name.value,
                        sectionid: thisVue.$refs.knob.value ? thisVue.$refs.knob.value : thisVue.$refs.chapter.value,
                        type: thisVue.$refs.type.value,
                        explain: thisVue.$refs.homeworkcon.children[0].innerHTML
                    },
                    answer: function () {
                        var arr = new Array();
                        for (var i = 0; i < thisVue.answernum; i++) {
                            var answercon = 'answercon' + (i + 1);
                            var attention = 'attention' + (i + 1);
                            arr.push({
                                answer: thisVue.$refs[answercon][0].children[0].innerHTML,
                                attention: thisVue.$refs[attention][0].value
                            });
                        }
                        return arr;
                    }(),
                    files: thisVue.homeworkupfile.files.concat(thisVue.answerupfile.files),
                    msglist: thisVue.homeworkupfile.msglist.concat(thisVue.answerupfile.msglist)
                };
                console.log(data);
            } else {
                layer.msg('作业内容不能为空', { icon: 5, time: 2000 }, function () {
                    // thisVue.$refs.homeworkcon.children[0].onfocus();
                });
            }
        },
        fnAddAnswer: function fnAddAnswer() {
            // 继续添加作业答案
            var thisVue = this;
            thisVue.answernum++;
            this.$nextTick(function () {
                thisVue.fnGetEdit("#editor-answer-bar" + thisVue.answernum, "#editor-answer-con" + thisVue.answernum);
            });
        },
        fnRemoveAnswer: function fnRemoveAnswer() {
            // 移去作业答案答案
            this.answernum--;
            if (this.answernum <= 1) {
                this.answernum = 1;
            }
        }

    }
};

/***/ }),
/* 104 */
/***/ (function(module, exports, __webpack_require__) {

module.exports={render:function (){var _vm=this;var _h=_vm.$createElement;var _c=_vm._self._c||_h;
  return _c('div', {
    staticClass: "class-wrap content operation",
    attrs: {
      "id": "course"
    }
  }, [_c('div', {
    staticClass: "classify clearfix"
  }, [_vm._m(0), _vm._v(" "), (_vm.course) ? _c('div', {
    staticClass: "fr"
  }, [_c('span', [_vm._v(_vm._s(_vm.course.name))])]) : _vm._e()]), _vm._v(" "), _c('div', {
    staticClass: "classify-wrap"
  }, [_c('div', {
    staticClass: "layui-form"
  }, [_c('fieldset', {
    staticClass: "layui-elem-field"
  }, [_c('legend', [_vm._v("作业题目")]), _vm._v(" "), _c('div', {
    staticClass: "layui-field-box"
  }, [_c('div', {
    staticClass: "layui-form-item"
  }, [_c('label', {
    staticClass: "layui-form-label"
  }, [_vm._v("作业位置")]), _vm._v(" "), _c('div', {
    staticClass: "layui-input-inline"
  }, [_c('select', {
    ref: "chapter",
    attrs: {
      "name": "chapter",
      "lay-verify": "required",
      "lay-filter": "chapter"
    }
  }, [_c('option', {
    attrs: {
      "value": "",
      "selected": ""
    }
  }, [_vm._v("选择章节")]), _vm._v(" "), _vm._l((_vm.chapter), function(item) {
    return _c('option', {
      domProps: {
        "value": item.id
      }
    }, [_vm._v(_vm._s(item.name))])
  })], 2)]), _vm._v(" "), _c('div', {
    staticClass: "layui-input-inline"
  }, [_c('select', {
    ref: "knob",
    attrs: {
      "name": "knob",
      "lay-filter": "knob"
    }
  }, [_c('option', {
    attrs: {
      "value": "",
      "selected": ""
    }
  }, [_vm._v("选择小节")]), _vm._v(" "), _vm._l((_vm.knob), function(item) {
    return _c('option', {
      domProps: {
        "value": item.id
      }
    }, [_vm._v(_vm._s(item.name))])
  })], 2)])]), _vm._v(" "), _c('div', {
    staticClass: "layui-form-item"
  }, [_c('label', {
    staticClass: "layui-form-label"
  }, [_vm._v("作业类型")]), _vm._v(" "), _c('div', {
    staticClass: "layui-input-inline"
  }, [_c('select', {
    ref: "type",
    attrs: {
      "name": "type",
      "lay-verify": "required"
    }
  }, [_c('option', {
    attrs: {
      "value": ""
    }
  }, [_vm._v("请选择类型")]), _vm._v(" "), _c('option', {
    attrs: {
      "value": "singlechoice",
      "disabled": ""
    }
  }, [_vm._v("单选题")]), _vm._v(" "), _c('option', {
    attrs: {
      "value": "multiplechoice",
      "disabled": ""
    }
  }, [_vm._v("多选题")]), _vm._v(" "), _c('option', {
    attrs: {
      "value": "shortanswer",
      "disabled": ""
    }
  }, [_vm._v("填空题")]), _vm._v(" "), _c('option', {
    attrs: {
      "value": "truefalse",
      "disabled": ""
    }
  }, [_vm._v("判断题")]), _vm._v(" "), _c('option', {
    attrs: {
      "value": "match",
      "disabled": ""
    }
  }, [_vm._v("匹配题")]), _vm._v(" "), _c('option', {
    attrs: {
      "value": "essay",
      "selected": ""
    }
  }, [_vm._v("简答题")]), _vm._v(" "), _c('option', {
    attrs: {
      "value": "comprehensive",
      "disabled": ""
    }
  }, [_vm._v("综合题")]), _vm._v(" "), _c('option', {
    attrs: {
      "value": "readingcomprehension",
      "disabled": ""
    }
  }, [_vm._v("阅读理解")])])])]), _vm._v(" "), _c('div', {
    staticClass: "layui-form-item"
  }, [_c('label', {
    staticClass: "layui-form-label"
  }, [_vm._v("作业名称")]), _vm._v(" "), _c('div', {
    staticClass: "layui-input-block"
  }, [_c('input', {
    ref: "name",
    staticClass: "layui-input",
    attrs: {
      "type": "text",
      "name": "name",
      "lay-verify": "required",
      "autocomplete": "off",
      "placeholder": "请输入名称"
    }
  })])]), _vm._v(" "), _c('div', {
    staticClass: "layui-form-item layui-form-text"
  }, [_c('label', {
    staticClass: "layui-form-label"
  }, [_vm._v("作业内容")]), _vm._v(" "), _c('div', {
    staticClass: "editor",
    attrs: {
      "id": "editor-homework"
    }
  }, [_c('div', {
    staticClass: "editor-bar",
    attrs: {
      "id": "editor-homework-bar"
    }
  }), _vm._v(" "), _c('div', {
    staticClass: "editor-file clearfix",
    attrs: {
      "id": "editor-homework-file"
    }
  }, [_c('div', {
    staticClass: "file-btn"
  }, [_c('input', {
    attrs: {
      "type": "file",
      "multiple": "",
      "name": "files-homework"
    },
    on: {
      "change": function($event) {
        _vm.fnListenerUpFile($event, _vm.homeworkupfile, 'homeworkupfile')
      }
    }
  }), _vm._v(" "), _vm._m(1)]), _vm._v(" "), _c('div', {
    staticClass: "file-list"
  }, [(_vm.homeworkupfile.listdata.length > 0) ? _c('table', {
    staticClass: "table"
  }, _vm._l((_vm.homeworkupfile.listdata), function(item, index) {
    return _c('tr', [_c('td', [_c('div', {
      attrs: {
        "id": "preview"
      }
    }, [_c('img', {
      attrs: {
        "src": item.localurl,
        "alt": item.name
      }
    })])]), _vm._v(" "), _c('td', [_c('span', [_vm._v(_vm._s(item.name))])]), _vm._v(" "), _c('td', [_c('span', [_vm._v(_vm._s(item.size))])]), _vm._v(" "), _c('td', [_c('textarea', {
      staticClass: "textarea",
      attrs: {
        "plcaeholder": "文件描述"
      },
      on: {
        "blur": function($event) {
          _vm.fnAddSummaryToFile($event, index, _vm.homeworkupfile)
        }
      }
    })]), _vm._v(" "), _c('td', [_c('p', {
      staticClass: "btn delete cancel",
      attrs: {
        "data-index": index
      },
      on: {
        "click": function($event) {
          _vm.fnDelFile($event, index, _vm.homeworkupfile)
        }
      }
    }, [_vm._v("移除")])])])
  })) : _vm._e()])]), _vm._v(" "), _c('div', {
    ref: "homeworkcon",
    staticClass: "editor-con",
    attrs: {
      "id": "editor-homework-con"
    }
  })])])])]), _vm._v(" "), _c('fieldset', {
    staticClass: "layui-elem-field"
  }, [_c('legend', [_vm._v("作业答案")]), _vm._v(" "), _c('div', {
    staticClass: "layui-field-box"
  }, [_c('div', {
    ref: "answerwrap",
    staticClass: "wrap"
  }, [_c('div', {
    staticClass: "layui-form-item"
  }, [_c('label', {
    staticClass: "layui-form-label"
  }, [_vm._v("答案附件")]), _vm._v(" "), _c('div', {
    staticClass: "layui-input-block",
    staticStyle: {
      "width": "80%"
    }
  }, [_c('div', {
    staticClass: "editor-file clearfix",
    attrs: {
      "id": "editor-answer-file"
    }
  }, [_c('div', {
    staticClass: "file-btn"
  }, [_c('input', {
    attrs: {
      "type": "file",
      "multiple": "",
      "name": "files-answer"
    },
    on: {
      "change": function($event) {
        _vm.fnListenerUpFile($event, _vm.answerupfile, 'answerupfile')
      }
    }
  }), _vm._v(" "), _vm._m(2)]), _vm._v(" "), _c('div', {
    staticClass: "file-list"
  }, [(_vm.answerupfile.listdata.length > 0) ? _c('table', {
    staticClass: "table"
  }, _vm._l((_vm.answerupfile.listdata), function(item, index) {
    return _c('tr', [_c('td', [_c('div', {
      attrs: {
        "id": "preview"
      }
    }, [_c('img', {
      attrs: {
        "src": item.localurl,
        "alt": item.name
      }
    })])]), _vm._v(" "), _c('td', [_c('span', [_vm._v(_vm._s(item.name))])]), _vm._v(" "), _c('td', [_c('span', [_vm._v(_vm._s(item.size))])]), _vm._v(" "), _c('td', [_c('textarea', {
      staticClass: "textarea",
      attrs: {
        "plcaeholder": "文件描述"
      },
      on: {
        "blur": function($event) {
          _vm.fnAddSummaryToFile($event, index, _vm.answerupfile)
        }
      }
    })]), _vm._v(" "), _c('td', [_c('p', {
      staticClass: "btn delete cancel",
      attrs: {
        "data-index": index
      },
      on: {
        "click": function($event) {
          _vm.fnDelFile($event, index, _vm.answerupfile)
        }
      }
    }, [_vm._v("移除")])])])
  })) : _vm._e()])])])]), _vm._v(" "), _vm._l((_vm.answernum), function(item) {
    return _c('div', {
      staticClass: "item"
    }, [_c('div', {
      staticClass: "layui-form-item"
    }, [_c('label', {
      staticClass: "layui-form-label"
    }, [_vm._v("答案备注")]), _vm._v(" "), _c('div', {
      staticClass: "layui-input-block"
    }, [_c('input', {
      ref: ['attention' + item],
      refInFor: true,
      staticClass: "layui-input",
      attrs: {
        "type": "text",
        "autocomplete": "off",
        "placeholder": "作业答案特别说明"
      }
    })])]), _vm._v(" "), _c('div', {
      staticClass: "layui-form-item layui-form-text"
    }, [_c('label', {
      staticClass: "layui-form-label"
    }, [_vm._v("作业答案")]), _vm._v(" "), _c('div', {
      staticClass: "editor",
      attrs: {
        "id": ['editor-answer' + item]
      }
    }, [_c('div', {
      staticClass: "editor-bar",
      attrs: {
        "id": ['editor-answer-bar' + item]
      }
    }), _vm._v(" "), _c('div', {
      ref: ['answercon' + item],
      refInFor: true,
      staticClass: "editor-con",
      attrs: {
        "id": ['editor-answer-con' + item]
      }
    })])])])
  })], 2), _vm._v(" "), _c('div', {
    staticClass: "layui-form-item"
  }, [_c('div', {
    staticClass: "layui-input-block"
  }, [_c('button', {
    staticClass: "layui-btn layui-btn-primary",
    on: {
      "click": function($event) {
        _vm.fnAddAnswer()
      }
    }
  }, [_vm._v("添加答案")]), _vm._v(" "), (_vm.answernum > 1) ? _c('button', {
    staticClass: "layui-btn layui-btn-primary",
    on: {
      "click": function($event) {
        _vm.fnRemoveAnswer()
      }
    }
  }, [_vm._v("移除答案")]) : _vm._e()])])])]), _vm._v(" "), _c('fieldset', {
    staticClass: "layui-elem-field"
  }, [_c('legend', [_vm._v("添加作业")]), _vm._v(" "), _c('div', {
    staticClass: "layui-field-box"
  }, [_c('div', {
    staticClass: "layui-form-item"
  }, [_c('div', {
    staticClass: "layui-input-block"
  }, [_c('button', {
    staticClass: "layui-btn",
    attrs: {
      "lay-submit": "",
      "lay-filter": "submit"
    }
  }, [_vm._v("立即提交")]), _vm._v(" "), _c('button', {
    staticClass: "layui-btn",
    attrs: {
      "lay-filter": "back"
    },
    on: {
      "click": function($event) {
        _vm.fnBack()
      }
    }
  }, [_vm._v("返回")]), _vm._v(" "), _c('button', {
    staticClass: "layui-btn layui-btn-primary"
  }, [_vm._v("重置")])])])])])])])])
},staticRenderFns: [function (){var _vm=this;var _h=_vm.$createElement;var _c=_vm._self._c||_h;
  return _c('ul', {
    staticClass: "fl"
  }, [_c('li', {
    staticClass: "check"
  }, [_c('i', {
    staticClass: "before",
    staticStyle: {
      "left": "0"
    }
  }), _vm._v(" "), _c('a', {
    staticClass: "click",
    attrs: {
      "href": "javascript:void(0);"
    }
  }, [_vm._v("添加课堂作业")])])])
},function (){var _vm=this;var _h=_vm.$createElement;var _c=_vm._self._c||_h;
  return _c('p', {
    staticClass: "mark"
  }, [_c('i'), _vm._v("添加附件")])
},function (){var _vm=this;var _h=_vm.$createElement;var _c=_vm._self._c||_h;
  return _c('p', {
    staticClass: "mark"
  }, [_c('i'), _vm._v("添加附件")])
}]}
module.exports.render._withStripped = true
if (false) {
  module.hot.accept()
  if (module.hot.data) {
     require("vue-hot-reload-api").rerender("data-v-2faeaf56", module.exports)
  }
}

/***/ }),
/* 105 */
/***/ (function(module, exports, __webpack_require__) {


/* styles */
__webpack_require__(106)

var Component = __webpack_require__(3)(
  /* script */
  __webpack_require__(108),
  /* template */
  __webpack_require__(109),
  /* scopeId */
  null,
  /* cssModules */
  null
)
Component.options.__file = "D:\\wampserver\\wamp\\www\\vue\\course-vue\\src\\components\\members.vue"
if (Component.esModule && Object.keys(Component.esModule).some(function (key) {return key !== "default" && key !== "__esModule"})) {console.error("named exports are not supported in *.vue files.")}
if (Component.options.functional) {console.error("[vue-loader] members.vue: functional components are not supported with templates, they should use render functions.")}

/* hot reload */
if (false) {(function () {
  var hotAPI = require("vue-hot-reload-api")
  hotAPI.install(require("vue"), false)
  if (!hotAPI.compatible) return
  module.hot.accept()
  if (!module.hot.data) {
    hotAPI.createRecord("data-v-e390b3b0", Component.options)
  } else {
    hotAPI.reload("data-v-e390b3b0", Component.options)
  }
})()}

module.exports = Component.exports


/***/ }),
/* 106 */
/***/ (function(module, exports, __webpack_require__) {

// style-loader: Adds some css to the DOM by adding a <style> tag

// load the styles
var content = __webpack_require__(107);
if(typeof content === 'string') content = [[module.i, content, '']];
if(content.locals) module.exports = content.locals;
// add the styles to the DOM
var update = __webpack_require__(6)("4e37ddae", content, false);
// Hot Module Replacement
if(false) {
 // When the styles change, update the <style> tags
 if(!content.locals) {
   module.hot.accept("!!../../node_modules/css-loader/index.js!../../node_modules/vue-loader/lib/style-rewriter.js?id=data-v-e390b3b0!../../node_modules/vue-loader/lib/selector.js?type=styles&index=0!./members.vue", function() {
     var newContent = require("!!../../node_modules/css-loader/index.js!../../node_modules/vue-loader/lib/style-rewriter.js?id=data-v-e390b3b0!../../node_modules/vue-loader/lib/selector.js?type=styles&index=0!./members.vue");
     if(typeof newContent === 'string') newContent = [[module.id, newContent, '']];
     update(newContent);
   });
 }
 // When the module is disposed, remove the <style> tags
 module.hot.dispose(function() { update(); });
}

/***/ }),
/* 107 */
/***/ (function(module, exports, __webpack_require__) {

exports = module.exports = __webpack_require__(5)();
// imports


// module
exports.push([module.i, "\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n", ""]);

// exports


/***/ }),
/* 108 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
    value: true
});
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//

exports.default = {
    data: function data() {
        return {
            //
        };
    },
    activated: function activated() {},
    mounted: function mounted() {},
    methods: {}
};

/***/ }),
/* 109 */
/***/ (function(module, exports, __webpack_require__) {

module.exports={render:function (){var _vm=this;var _h=_vm.$createElement;var _c=_vm._self._c||_h;
  return _vm._m(0)
},staticRenderFns: [function (){var _vm=this;var _h=_vm.$createElement;var _c=_vm._self._c||_h;
  return _c('section', {
    staticClass: "show-table"
  }, [_c('div', {
    staticClass: "show-track clearfix"
  }, [_c('div', {
    staticClass: "table-list"
  }, [_c('div', {
    staticClass: "operation"
  }, [_c('div', {
    attrs: {
      "id": "table-btn-list"
    }
  }, [_c('div', {
    staticClass: "select btnarr"
  }, [_c('div', {
    staticClass: "dt-buttons"
  }, [_c('span', {
    staticClass: "dt-button student active"
  }, [_vm._v("学生")]), _vm._v(" "), _c('span', {
    staticClass: "dt-button teacher"
  }, [_vm._v("老师")])])]), _vm._v(" "), _c('ul', {
    staticClass: "clearfix"
  })])]), _vm._v(" "), _c('div', {
    staticClass: "table-con"
  })]), _vm._v(" "), _c('div', {
    staticClass: "detalis-form"
  })])])
}]}
module.exports.render._withStripped = true
if (false) {
  module.hot.accept()
  if (module.hot.data) {
     require("vue-hot-reload-api").rerender("data-v-e390b3b0", module.exports)
  }
}

/***/ }),
/* 110 */
/***/ (function(module, exports, __webpack_require__) {


/* styles */
__webpack_require__(111)

var Component = __webpack_require__(3)(
  /* script */
  __webpack_require__(113),
  /* template */
  __webpack_require__(114),
  /* scopeId */
  null,
  /* cssModules */
  null
)
Component.options.__file = "D:\\wampserver\\wamp\\www\\vue\\course-vue\\src\\components\\addcourses.vue"
if (Component.esModule && Object.keys(Component.esModule).some(function (key) {return key !== "default" && key !== "__esModule"})) {console.error("named exports are not supported in *.vue files.")}
if (Component.options.functional) {console.error("[vue-loader] addcourses.vue: functional components are not supported with templates, they should use render functions.")}

/* hot reload */
if (false) {(function () {
  var hotAPI = require("vue-hot-reload-api")
  hotAPI.install(require("vue"), false)
  if (!hotAPI.compatible) return
  module.hot.accept()
  if (!module.hot.data) {
    hotAPI.createRecord("data-v-655e2690", Component.options)
  } else {
    hotAPI.reload("data-v-655e2690", Component.options)
  }
})()}

module.exports = Component.exports


/***/ }),
/* 111 */
/***/ (function(module, exports, __webpack_require__) {

// style-loader: Adds some css to the DOM by adding a <style> tag

// load the styles
var content = __webpack_require__(112);
if(typeof content === 'string') content = [[module.i, content, '']];
if(content.locals) module.exports = content.locals;
// add the styles to the DOM
var update = __webpack_require__(6)("3a89597c", content, false);
// Hot Module Replacement
if(false) {
 // When the styles change, update the <style> tags
 if(!content.locals) {
   module.hot.accept("!!../../node_modules/css-loader/index.js!../../node_modules/vue-loader/lib/style-rewriter.js?id=data-v-655e2690!../../node_modules/vue-loader/lib/selector.js?type=styles&index=0!./addcourses.vue", function() {
     var newContent = require("!!../../node_modules/css-loader/index.js!../../node_modules/vue-loader/lib/style-rewriter.js?id=data-v-655e2690!../../node_modules/vue-loader/lib/selector.js?type=styles&index=0!./addcourses.vue");
     if(typeof newContent === 'string') newContent = [[module.id, newContent, '']];
     update(newContent);
   });
 }
 // When the module is disposed, remove the <style> tags
 module.hot.dispose(function() { update(); });
}

/***/ }),
/* 112 */
/***/ (function(module, exports, __webpack_require__) {

exports = module.exports = __webpack_require__(5)();
// imports


// module
exports.push([module.i, "\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n", ""]);

// exports


/***/ }),
/* 113 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
    value: true
});

var _typeof = typeof Symbol === "function" && typeof Symbol.iterator === "symbol" ? function (obj) { return typeof obj; } : function (obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; };

//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//

exports.default = {
    data: function data() {
        var thisVue = this;
        return {
            ishandle: false, // 是否手动添加，还是模板导入
            ruleForm: {
                course_id: '',
                department: function () {
                    var arr = [];
                    arr.push(thisVue.Const.userMsg.pid);
                    return arr;
                }(),
                departmentName: '',
                fullname: '',
                shortname: '',
                coursenum: 'Y100',
                idnumber: '',
                category: '',
                numsections: 24,
                summary: '',
                pic_path: '',
                files: null
            },
            imgUrl: '',
            rules: {
                fullname: [{ required: true, message: '请输入课堂完整名称', trigger: 'blur' }],
                shortname: [{ required: true, message: '请输入课堂简称', trigger: 'blur' }],
                idnumber: [{ required: true, message: '请输入课堂编号', trigger: 'blur' }],
                category: [{ required: true, message: '请选择课堂类型', trigger: 'change' }],
                numsections: [{ required: true, message: '请输入课堂课时', trigger: 'blur' }],
                summary: [{ required: true, message: '请输入课堂概要', trigger: 'blur' }]
            },
            categories: [],
            organizationList: [],
            props: {
                value: 'id',
                label: 'name',
                children: 'children'
            },
            dialogTableVisible: false, // 拉取课程的弹窗
            courseGridData: [{
                cate_id: "0A96392421B669AF3EE4A8D7218C9ECE",
                cate_name: "专业选修课",
                fullname: "课程全名测试11",
                id: "2EF9421FADD98946FA21E8BD50441BDF",
                idnumber: "ces11",
                numsections: 111,
                org_id: "B608AA535E0EC9C5DDBC8548AF39D4CB",
                org_name: "电子科技大学成都学院",
                pic_path: null,
                shortname: "课程简称测试11",
                summary: "11111"
            }],
            courseTablePage: {
                currentPage: 1,
                pageSize: 10,
                total: 100
            }
        };
    },
    updated: function updated() {
        // 当这个钩子被调用是，DOM已经更新
        console.log('12312312312312');
    },
    created: function created() {},
    activated: function activated() {},
    mounted: function mounted() {
        // 首次请求默认的课程类型
        this.fnReqCatByOrgid(this.Const.userMsg.pid);
        this.getOrganization();
    },
    methods: {
        getOrganization: function getOrganization() {
            // 请求全部机构数据
            window.utiltool.checkCookie();
            var thisVue = this;
            // 请求机构信息和课堂类型信息
            var param = {
                'module': 'service',
                'controller': 'Organization_Con',
                'action': 'getUserOrgAll'
            };
            $.ajax({
                url: API_ENV.API_URL,
                type: 'POST',
                data: param,
                success: function success(data) {
                    var data = JSON.parse(data);
                    window.utiltool.checkLoginStatus(data.status);
                    if (!data.status && _typeof(data.data) === 'object') {
                        thisVue.organizationList = function () {
                            var arr = [];
                            arr.push(data.data);
                            return arr;
                        }();
                    } else {
                        thisVue.$message({ type: 'error', message: data.data ? data.data : '获取机构信息出错了！' });
                    }
                },
                error: function error(err) {
                    thisVue.$message({ type: 'error', message: '获取机构信息出错了！' });
                }
            });
        },
        fnReqCatByOrgid: function fnReqCatByOrgid(orgid) {
            var thisVue = this;
            $.ajax({
                url: API_ENV.API_URL,
                type: 'POST',
                dataType: 'json',
                data: {
                    module: 'Service',
                    controller: 'Course_Categories_Con',
                    action: 'getCate',
                    organization: orgid
                },
                success: function success(response) {
                    if (!response.status && window.utiltool.isArray(response.data)) {
                        thisVue.categories = response.data;
                    } else {
                        window.utiltool.dealErr({
                            level: 2,
                            msg: response.data
                        });
                    }
                },
                error: function error(err) {
                    window.utiltool.dealErr({
                        level: 1,
                        msg: '按机构请求类型数据报错'
                    });
                }
            });
        },
        requestCourse: function requestCourse() {
            var thisVue = this;
            var param = {
                'module': 'service',
                'controller': 'Course_Con',
                'action': 'queCourseFc',
                'page': this.courseTablePage.currentPage,
                'rows': this.courseTablePage.pageSize
            };
            var loading = this.$loading({
                lock: true,
                text: '正在请求数据，请稍后...',
                spinner: 'el-icon-loading',
                background: 'rgba(0, 0, 0, 0.7)'
            });
            this.$ajax.post(API_ENV.API_URL, param).then(function (res) {
                loading.close();
                var resData = res.data;
                console.log('123123132', res);
                window.utiltool.checkLoginStatus(resData.status);
                if (!resData.status && typeof resData.data !== 'string') {
                    thisVue.courseTablePage.currentPage = resData.data.current_page;
                    thisVue.courseTablePage.total = resData.data.total;
                    thisVue.courseGridData = resData.data.data;
                } else {
                    thisVue.$message({ type: 'info', message: resData.data ? resData.data : '获取课程数据失败！' });
                }
            }).catch(function (error) {
                loading.close();
                thisVue.$message.error('请求错误！');
            });
        },
        // 图片上传
        handleCoverChange: function handleCoverChange(file, filelist) {
            console.log('图片上传', file);
            var picTypeList = ['image/jpeg', 'image/png'];
            var isPic = new RegExp(',' + file.raw.type + ',').test(',' + picTypeList.join() + ',');
            var isSize = file.size < 2 * 1024 * 1024;
            if (isPic && isSize) {
                this.ruleForm.files = file.raw;
                this.imgUrl = URL.createObjectURL(file.raw);
            } else {
                this.$message({ type: 'info', message: '只允许上传小于 2MB 的 jpg|png 图片' });
            }
        },
        // 添加课堂-提交
        submitFormAdd: function submitFormAdd(refform) {
            var thisVue = this;
            this.$refs[refform].validate(function (valid) {
                if (valid) {
                    console.log(thisVue.ruleForm);
                } else {
                    return false;
                }
            });
        },
        // 从课程库拉取课程
        selCourseFromLib: function selCourseFromLib() {
            this.dialogTableVisible = true;
            this.requestCourse();
        },
        // 触发表格的单选
        handleCurrentTableChange: function handleCurrentTableChange(currentRow) {
            console.log('currentRow', currentRow);
            this.ruleForm.course_id = currentRow.id;
            this.ruleForm.department = function () {
                var arr = [];
                arr.push(currentRow.org_id);
                return arr;
            }();
            this.ruleForm.fullname = currentRow.fullname;
            this.ruleForm.shortname = currentRow.shortname;
            this.ruleForm.coursenum = currentRow.idnumber;
            this.ruleForm.departmentName = currentRow.org_name;
            this.ruleForm.idnumber = '';
            this.ruleForm.category = currentRow.cate_id;
            this.ruleForm.numsections = currentRow.numsections;
            this.ruleForm.summary = currentRow.summary;
            this.ruleForm.pic_path = currentRow.pic_path;
        },
        // 确认选取的课程
        getTableCourseData: function getTableCourseData() {
            this.dialogTableVisible = false;
        },
        // 表格页码发生变化
        handleCurrentChangeTable: function handleCurrentChangeTable(currentpage) {
            console.log('handleCurrentChangeTable', currentpage);
            this.courseTablePage.currentPage = currentpage;
            this.requestCourse();
        }
    },
    watch: {}
};

/***/ }),
/* 114 */
/***/ (function(module, exports, __webpack_require__) {

module.exports={render:function (){var _vm=this;var _h=_vm.$createElement;var _c=_vm._self._c||_h;
  return _c('div', {
    staticClass: "wrap-course content",
    attrs: {
      "id": "addcourse"
    }
  }, [_c('div', {
    staticClass: "class-wrap"
  }, [_c('div', {
    staticClass: "classify clearfix"
  }, [_vm._m(0), _vm._v(" "), _c('div', {
    staticClass: "fr"
  }, [_c('el-button-group', [(!_vm.ishandle) ? _c('el-button', {
    attrs: {
      "title": "手动添加",
      "type": "primary",
      "icon": "el-icon-edit"
    },
    on: {
      "click": function($event) {
        _vm.ishandle = true
      }
    }
  }, [_vm._v("手动")]) : _c('el-button', {
    attrs: {
      "title": "模板导入",
      "type": "primary",
      "icon": "el-icon-sold-out"
    },
    on: {
      "click": function($event) {
        _vm.ishandle = false
      }
    }
  }, [_vm._v("模板")]), _vm._v(" "), _c('el-button', {
    attrs: {
      "type": "primary",
      "icon": "el-icon-back"
    },
    on: {
      "click": function($event) {
        _vm.$router.push({
          path: '/'
        })
      }
    }
  }, [_vm._v("返回")])], 1)], 1)]), _vm._v(" "), _c('div', {
    staticClass: "add-course-form"
  }, [_c('el-form', {
    ref: "ruleFormAddRoom",
    staticClass: "demo-ruleForm",
    attrs: {
      "model": _vm.ruleForm,
      "status-icon": "",
      "rules": _vm.rules,
      "label-width": "100px"
    }
  }, [(!_vm.ishandle) ? _c('el-form-item', {
    attrs: {
      "label": "选择课程"
    }
  }, [_c('el-button', {
    attrs: {
      "type": "primary",
      "plain": ""
    },
    on: {
      "click": _vm.selCourseFromLib
    }
  }, [_c('i', {
    staticClass: "el-icon-plus"
  })])], 1) : _vm._e(), _vm._v(" "), _c('el-form-item', {
    attrs: {
      "label": "所属机构",
      "prop": "department"
    }
  }, [(!_vm.ishandle) ? _c('el-input', {
    attrs: {
      "readonly": true
    },
    model: {
      value: (_vm.ruleForm.departmentName),
      callback: function($$v) {
        _vm.ruleForm.departmentName = $$v
      },
      expression: "ruleForm.departmentName"
    }
  }) : _c('el-cascader', {
    attrs: {
      "expand-trigger": "hover",
      "change-on-select": true,
      "props": _vm.props,
      "options": _vm.organizationList
    },
    model: {
      value: (_vm.ruleForm.department),
      callback: function($$v) {
        _vm.ruleForm.department = $$v
      },
      expression: "ruleForm.department"
    }
  })], 1), _vm._v(" "), _c('el-form-item', {
    attrs: {
      "label": "课堂全名",
      "prop": "fullname"
    }
  }, [_c('el-input', {
    model: {
      value: (_vm.ruleForm.fullname),
      callback: function($$v) {
        _vm.ruleForm.fullname = $$v
      },
      expression: "ruleForm.fullname"
    }
  })], 1), _vm._v(" "), _c('el-form-item', {
    attrs: {
      "label": "课堂简称",
      "prop": "shortname"
    }
  }, [_c('el-input', {
    model: {
      value: (_vm.ruleForm.shortname),
      callback: function($$v) {
        _vm.ruleForm.shortname = $$v
      },
      expression: "ruleForm.shortname"
    }
  })], 1), _vm._v(" "), _c('el-form-item', {
    attrs: {
      "label": "课程编号"
    }
  }, [_c('el-input', {
    attrs: {
      "readonly": true
    },
    model: {
      value: (_vm.ruleForm.coursenum),
      callback: function($$v) {
        _vm.ruleForm.coursenum = $$v
      },
      expression: "ruleForm.coursenum"
    }
  })], 1), _vm._v(" "), _c('el-form-item', {
    attrs: {
      "label": "课堂编号",
      "prop": "idnumber"
    }
  }, [_c('el-input', {
    model: {
      value: (_vm.ruleForm.idnumber),
      callback: function($$v) {
        _vm.ruleForm.idnumber = $$v
      },
      expression: "ruleForm.idnumber"
    }
  })], 1), _vm._v(" "), _c('el-form-item', {
    attrs: {
      "label": "课堂类型",
      "prop": "category"
    }
  }, [_c('el-select', {
    attrs: {
      "placeholder": "请选择课堂类型"
    },
    model: {
      value: (_vm.ruleForm.category),
      callback: function($$v) {
        _vm.ruleForm.category = $$v
      },
      expression: "ruleForm.category"
    }
  }, _vm._l((_vm.categories), function(item, index) {
    return _c('el-option', {
      key: index,
      attrs: {
        "label": item.name,
        "value": item.id
      }
    })
  }))], 1), _vm._v(" "), _c('el-form-item', {
    attrs: {
      "label": "课堂课时",
      "prop": "numsections"
    }
  }, [_c('el-input', {
    attrs: {
      "placeholder": "请输入课堂课时",
      "type": "number"
    },
    model: {
      value: (_vm.ruleForm.numsections),
      callback: function($$v) {
        _vm.ruleForm.numsections = $$v
      },
      expression: "ruleForm.numsections"
    }
  })], 1), _vm._v(" "), _c('el-form-item', {
    attrs: {
      "label": "课堂概要",
      "prop": "summary"
    }
  }, [_c('el-input', {
    attrs: {
      "placeholder": "请输入课堂概要",
      "type": "textarea"
    },
    model: {
      value: (_vm.ruleForm.summary),
      callback: function($$v) {
        _vm.ruleForm.summary = $$v
      },
      expression: "ruleForm.summary"
    }
  })], 1), _vm._v(" "), _c('el-form-item', {
    attrs: {
      "label": "课程封面"
    }
  }, [(_vm.ruleForm.pic_path) ? [_c('a', {
    staticStyle: {
      "width": "100px",
      "height": "100px",
      "display": "block"
    },
    attrs: {
      "href": _vm.ruleForm.pic_path,
      "target": "_blank"
    }
  }, [_c('img', {
    attrs: {
      "src": _vm.ruleForm.pic_path,
      "alt": ""
    }
  })])] : _c('el-upload', {
    staticClass: "cover-class-uploader",
    attrs: {
      "action": "",
      "auto-upload": false,
      "show-file-list": false,
      "on-change": _vm.handleCoverChange
    }
  }, [(_vm.imgUrl) ? _c('img', {
    staticClass: "cover-class",
    attrs: {
      "src": _vm.imgUrl
    }
  }) : _c('i', {
    staticClass: "el-icon-plus avatar-uploader-icon"
  })])], 2), _vm._v(" "), _c('el-form-item', [_c('el-button', {
    attrs: {
      "type": "primary"
    },
    on: {
      "click": function($event) {
        _vm.submitFormAdd('ruleFormAddRoom')
      }
    }
  }, [_vm._v("立即创建")]), _vm._v(" "), _c('el-button', {
    on: {
      "click": function($event) {
        _vm.$router.push({
          path: '/'
        })
      }
    }
  }, [_vm._v("取消")])], 1)], 1)], 1)]), _vm._v(" "), _c('section', [_c('el-dialog', {
    attrs: {
      "show-close": false,
      "title": "选择课程模板",
      "width": "80%",
      "visible": _vm.dialogTableVisible
    }
  }, [_c('el-table', {
    attrs: {
      "highlight-current-row": "",
      "height": "300",
      "stripe": true,
      "data": _vm.courseGridData
    },
    on: {
      "current-change": _vm.handleCurrentTableChange
    }
  }, [_c('el-table-column', {
    attrs: {
      "type": "index",
      "width": "50"
    }
  }), _vm._v(" "), _c('el-table-column', {
    attrs: {
      "prop": "fullname",
      "label": "课程全称"
    }
  }), _vm._v(" "), _c('el-table-column', {
    attrs: {
      "prop": "cate_name",
      "label": "课程类型",
      "width": "200"
    }
  }), _vm._v(" "), _c('el-table-column', {
    attrs: {
      "prop": "org_name",
      "label": "所属机构",
      "width": "300"
    }
  }), _vm._v(" "), _c('el-table-column', {
    attrs: {
      "prop": "idnumber",
      "label": "课程编号",
      "width": "150"
    }
  }), _vm._v(" "), _c('el-table-column', {
    attrs: {
      "prop": "numsections",
      "label": "课程课时",
      "width": "80"
    }
  })], 1), _vm._v(" "), _c('el-pagination', {
    attrs: {
      "background": "",
      "current-page": _vm.courseTablePage.currentPage,
      "page-size": _vm.courseTablePage.pageSize,
      "layout": "total, prev, pager, next",
      "total": _vm.courseTablePage.total
    },
    on: {
      "current-change": _vm.handleCurrentChangeTable
    }
  }), _vm._v(" "), _c('div', {
    staticClass: "dialog-footer",
    slot: "footer"
  }, [_c('el-button', {
    on: {
      "click": function($event) {
        _vm.dialogTableVisible = false
      }
    }
  }, [_vm._v("取 消")]), _vm._v(" "), _c('el-button', {
    attrs: {
      "type": "primary"
    },
    on: {
      "click": _vm.getTableCourseData
    }
  }, [_vm._v("确 定")])], 1)], 1)], 1)])
},staticRenderFns: [function (){var _vm=this;var _h=_vm.$createElement;var _c=_vm._self._c||_h;
  return _c('ul', {
    staticClass: "fl"
  }, [_c('li', [_c('i', {
    staticClass: "before",
    staticStyle: {
      "left": "0"
    }
  }), _vm._v(" "), _c('a', {
    staticClass: "click",
    attrs: {
      "href": "javascript:void(0);"
    }
  }, [_vm._v("添加新课堂")])])])
}]}
module.exports.render._withStripped = true
if (false) {
  module.hot.accept()
  if (module.hot.data) {
     require("vue-hot-reload-api").rerender("data-v-655e2690", module.exports)
  }
}

/***/ }),
/* 115 */
/***/ (function(module, exports, __webpack_require__) {


/* styles */
__webpack_require__(116)

var Component = __webpack_require__(3)(
  /* script */
  __webpack_require__(118),
  /* template */
  __webpack_require__(119),
  /* scopeId */
  null,
  /* cssModules */
  null
)
Component.options.__file = "D:\\wampserver\\wamp\\www\\vue\\course-vue\\src\\components\\editchapter.vue"
if (Component.esModule && Object.keys(Component.esModule).some(function (key) {return key !== "default" && key !== "__esModule"})) {console.error("named exports are not supported in *.vue files.")}
if (Component.options.functional) {console.error("[vue-loader] editchapter.vue: functional components are not supported with templates, they should use render functions.")}

/* hot reload */
if (false) {(function () {
  var hotAPI = require("vue-hot-reload-api")
  hotAPI.install(require("vue"), false)
  if (!hotAPI.compatible) return
  module.hot.accept()
  if (!module.hot.data) {
    hotAPI.createRecord("data-v-022a9392", Component.options)
  } else {
    hotAPI.reload("data-v-022a9392", Component.options)
  }
})()}

module.exports = Component.exports


/***/ }),
/* 116 */
/***/ (function(module, exports, __webpack_require__) {

// style-loader: Adds some css to the DOM by adding a <style> tag

// load the styles
var content = __webpack_require__(117);
if(typeof content === 'string') content = [[module.i, content, '']];
if(content.locals) module.exports = content.locals;
// add the styles to the DOM
var update = __webpack_require__(6)("62a45478", content, false);
// Hot Module Replacement
if(false) {
 // When the styles change, update the <style> tags
 if(!content.locals) {
   module.hot.accept("!!../../node_modules/css-loader/index.js!../../node_modules/vue-loader/lib/style-rewriter.js?id=data-v-022a9392!../../node_modules/vue-loader/lib/selector.js?type=styles&index=0!./editchapter.vue", function() {
     var newContent = require("!!../../node_modules/css-loader/index.js!../../node_modules/vue-loader/lib/style-rewriter.js?id=data-v-022a9392!../../node_modules/vue-loader/lib/selector.js?type=styles&index=0!./editchapter.vue");
     if(typeof newContent === 'string') newContent = [[module.id, newContent, '']];
     update(newContent);
   });
 }
 // When the module is disposed, remove the <style> tags
 module.hot.dispose(function() { update(); });
}

/***/ }),
/* 117 */
/***/ (function(module, exports, __webpack_require__) {

exports = module.exports = __webpack_require__(5)();
// imports


// module
exports.push([module.i, "\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n", ""]);

// exports


/***/ }),
/* 118 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
    value: true
});
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//

exports.default = {
    data: function data() {
        return {
            // 课程库编辑课程章节
            currentchapter: null
        };
    },
    created: function created() {
        var _this = this;

        this.$root.Bus.$on("sendChapter", function (chapter) {
            window.sessionStorage.setItem('currentChapter', JSON.stringify(chapter));
            _this.read(chapter);
        });
    },
    activated: function activated() {
        if (window.sessionStorage.getItem("currentChapter")) {
            this.read();
        } else {
            window.history.back(-1);
        }
    },
    methods: {
        save: function save() {
            var thisVue = this;
            $.formValidator.initConfig({
                formID: "editchapterlib",
                errorFocus: true,
                alertMessage: false,
                onError: function onError(err) {
                    layer.msg(err, {
                        icon: 2,
                        time: 2000
                    });
                }
            });
            $("#editchaptertitle").formValidator({
                empty: false
            }).regexValidator({
                regExp: "notempty",
                dataType: "enum",
                onError: "课程名称不能为空"
            });
            $("#editchapterperiod").formValidator({
                empty: false
            }).regexValidator({
                regExp: "intege1",
                dataType: "enum",
                onError: "课程课时只能是正整数"
            });
            if ($.formValidator.pageIsValid('1')) {
                var title = $("#editchaptertitle").val().trim();
                var section = $("#editchapterperiod").val().trim();
                var summary = $("#editchapterdes").val().trim();
                var data = {
                    'module': 'service',
                    'controller': 'Course_Sections_Con',
                    'action': 'upSections',
                    'type': 0,
                    'courseid': thisVue.currentchapter.courseid,
                    'id': thisVue.currentchapter.id,
                    'name': title,
                    'section': section,
                    'summary': summary
                };
                $.ajax({
                    url: API_ENV.API_URL,
                    dataType: 'json',
                    type: 'POST',
                    data: data,
                    success: function success(response) {
                        window.utiltool.checkLoginStatus(response.status);
                        if (!response.status) {
                            //history.back(-1);

                            layer.msg(response.data, {
                                icon: 1,
                                time: 1500
                            }, function () {
                                history.back(-1);
                            });
                        } else {
                            layer.msg(response.data, {
                                icon: 2,
                                time: 1500
                            }, function () {
                                window.location.reload();
                            });
                        }
                    },
                    error: function error(err) {
                        console.log('章修改提交失败！', err);
                    }
                });
            }
        },
        read: function read(item) {
            var item = item || JSON.parse(window.sessionStorage.getItem("currentChapter"));
            this.currentchapter = item;
        },
        back: function back() {
            window.history.back(-1);
        }
    },
    mounted: function mounted() {},
    beforeDestroy: function beforeDestroy() {
        this.$root.Bus.$off('sendCourse');
    }

};

/***/ }),
/* 119 */
/***/ (function(module, exports, __webpack_require__) {

module.exports={render:function (){var _vm=this;var _h=_vm.$createElement;var _c=_vm._self._c||_h;
  return _c('div', {
    staticClass: "content",
    attrs: {
      "id": "add-course"
    }
  }, [_c('div', {
    staticClass: "course-lib"
  }, [_vm._m(0), _vm._v(" "), _c('div', {
    staticClass: "add-course-form"
  }, [(_vm.currentchapter) ? [_c('div', {
    attrs: {
      "id": "add-form"
    }
  }, [_c('form', {
    attrs: {
      "id": "editchapterlib"
    }
  }, [_c('p', {
    staticClass: "title"
  }, [_vm._m(1), _vm._v(" "), _c('input', {
    staticClass: "con",
    attrs: {
      "name": "fullname",
      "id": "editchaptertitle",
      "type": "text",
      "autofocus": ""
    },
    domProps: {
      "value": _vm.currentchapter.name
    }
  })]), _vm._v(" "), _c('p', {
    staticClass: "period"
  }, [_vm._m(2), _vm._v(" "), _c('input', {
    staticClass: "con",
    attrs: {
      "name": "numsections",
      "id": "editchapterperiod",
      "type": "number"
    },
    domProps: {
      "value": _vm.currentchapter.section
    }
  })]), _vm._v(" "), _c('p', {
    staticClass: "des clearfix"
  }, [_vm._m(3), _vm._v(" "), _c('textarea', {
    staticClass: "con",
    attrs: {
      "name": "summary",
      "id": "editchapterdes"
    }
  }, [_vm._v(_vm._s(_vm.currentchapter.summary))])])])]), _vm._v(" "), _c('div', {
    staticClass: "submit clearfix"
  }, [_c('p', {
    staticClass: "fr"
  }, [_c('span', {
    staticClass: "back",
    on: {
      "click": _vm.back
    }
  }, [_vm._v("返回")]), _vm._v(" "), _c('span', {
    staticClass: "save",
    on: {
      "click": _vm.save
    }
  }, [_vm._v("更新")])])])] : _vm._e()], 2)])])
},staticRenderFns: [function (){var _vm=this;var _h=_vm.$createElement;var _c=_vm._self._c||_h;
  return _c('div', {
    staticClass: "classify clearfix"
  }, [_c('ul', {
    staticClass: "fl"
  }, [_c('li', [_c('i', {
    staticClass: "before",
    staticStyle: {
      "left": "0px"
    }
  }), _vm._v(" "), _c('a', {
    staticClass: "clicks",
    attrs: {
      "href": "javascript:void(0);"
    }
  }, [_vm._v("编辑章节")])])])])
},function (){var _vm=this;var _h=_vm.$createElement;var _c=_vm._self._c||_h;
  return _c('span', [_c('i', [_vm._v("*")]), _vm._v("章节标题")])
},function (){var _vm=this;var _h=_vm.$createElement;var _c=_vm._self._c||_h;
  return _c('span', [_c('i', [_vm._v("*")]), _vm._v("章节课时")])
},function (){var _vm=this;var _h=_vm.$createElement;var _c=_vm._self._c||_h;
  return _c('span', [_c('i', [_vm._v("*")]), _vm._v("章节概要")])
}]}
module.exports.render._withStripped = true
if (false) {
  module.hot.accept()
  if (module.hot.data) {
     require("vue-hot-reload-api").rerender("data-v-022a9392", module.exports)
  }
}

/***/ }),
/* 120 */
/***/ (function(module, exports, __webpack_require__) {


/* styles */
__webpack_require__(121)

var Component = __webpack_require__(3)(
  /* script */
  __webpack_require__(123),
  /* template */
  __webpack_require__(124),
  /* scopeId */
  null,
  /* cssModules */
  null
)
Component.options.__file = "D:\\wampserver\\wamp\\www\\vue\\course-vue\\src\\components\\readtasks.vue"
if (Component.esModule && Object.keys(Component.esModule).some(function (key) {return key !== "default" && key !== "__esModule"})) {console.error("named exports are not supported in *.vue files.")}
if (Component.options.functional) {console.error("[vue-loader] readtasks.vue: functional components are not supported with templates, they should use render functions.")}

/* hot reload */
if (false) {(function () {
  var hotAPI = require("vue-hot-reload-api")
  hotAPI.install(require("vue"), false)
  if (!hotAPI.compatible) return
  module.hot.accept()
  if (!module.hot.data) {
    hotAPI.createRecord("data-v-b1a778f2", Component.options)
  } else {
    hotAPI.reload("data-v-b1a778f2", Component.options)
  }
})()}

module.exports = Component.exports


/***/ }),
/* 121 */
/***/ (function(module, exports, __webpack_require__) {

// style-loader: Adds some css to the DOM by adding a <style> tag

// load the styles
var content = __webpack_require__(122);
if(typeof content === 'string') content = [[module.i, content, '']];
if(content.locals) module.exports = content.locals;
// add the styles to the DOM
var update = __webpack_require__(6)("729c00e1", content, false);
// Hot Module Replacement
if(false) {
 // When the styles change, update the <style> tags
 if(!content.locals) {
   module.hot.accept("!!../../node_modules/css-loader/index.js!../../node_modules/vue-loader/lib/style-rewriter.js?id=data-v-b1a778f2!../../node_modules/vue-loader/lib/selector.js?type=styles&index=0!./readtasks.vue", function() {
     var newContent = require("!!../../node_modules/css-loader/index.js!../../node_modules/vue-loader/lib/style-rewriter.js?id=data-v-b1a778f2!../../node_modules/vue-loader/lib/selector.js?type=styles&index=0!./readtasks.vue");
     if(typeof newContent === 'string') newContent = [[module.id, newContent, '']];
     update(newContent);
   });
 }
 // When the module is disposed, remove the <style> tags
 module.hot.dispose(function() { update(); });
}

/***/ }),
/* 122 */
/***/ (function(module, exports, __webpack_require__) {

exports = module.exports = __webpack_require__(5)();
// imports


// module
exports.push([module.i, "\n.layui-form{\n    margin: 20px auto;\n}\n.layui-field-box{\n    padding: 10px 0;\n}\n", ""]);

// exports


/***/ }),
/* 123 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
    value: true
});
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//

exports.default = {
    data: function data() {
        var thisVue = this;
        return {
            course: {
                id: '1231231',
                name: '课程名'
            },
            task: {
                courseid: '1231231',
                coursename: '课程全名',
                sectionid: '章或节id',
                sectionname: '章或节名',
                homework: {
                    id: '123123',
                    number: '作业序号',
                    name: '作业名称',
                    explain: '作业描述',
                    type: '作业类型',
                    idnumber: '作业编号',
                    author: 'admin',
                    createtime: '2017-06-30',
                    modifiedtime: '修改时间',
                    files: [{
                        id: '1231231',
                        name: '文件名',
                        summary: '文件描述',
                        type: '文件类型',
                        attachments: '地址id'
                    }, {
                        id: '1231231',
                        name: '文件名',
                        summary: '文件描述',
                        type: '文件类型',
                        attachments: '地址id'
                    }],
                    issue: 1,
                    announce_answer: 1,
                    issuemessage: {
                        dosubmit: 2,
                        attention: '布置作业特别说明',
                        score: 10,
                        starttime: '2017-12-12 12:12:12',
                        closetime: '2018-12-12 12:12:12'
                    }
                },
                answer: {
                    answer: [{
                        id: '答案1id',
                        answer: '答案1内容',
                        attention: '答案1特别说明'
                    }, {
                        id: '答案2id',
                        answer: '答案2内容',
                        attention: '答案2特别说明'
                    }],
                    files: [{
                        id: '答案文件id',
                        name: '答案文件名',
                        summary: '答案文件描述',
                        type: '答案文件类型',
                        attachments: '答案地址id'
                    }, {
                        id: '答案文件id',
                        name: '答案文件名',
                        summary: '答案文件描述',
                        type: '答案文件类型',
                        attachments: '答案地址id'
                    }]
                }
            },
            homeworkupfile: {
                listdata: new Array(), // 列表循环显示的数据
                files: new Array(), // 文件列表
                msglist: new Array() // 文件描述列表
            }, // 保存上传的文件
            answerupfile: {
                listdata: new Array(), // 列表循环显示的数据
                files: new Array(), // 文件列表
                msglist: new Array() // 文件描述列表
            },
            delfile: { // 删除的文件id
                homework: [],
                answer: []
            },
            delanswer: [], // 删除答案
            answernum: 0 // 新增答案的个数
        };
    },
    activated: function activated() {
        var thisVue = this;
        layui.use('form', function () {
            var form = layui.form;
            form.on('submit(submit)', function (data) {
                console.log(data);
                thisVue.fnSubmit();
                return false;
            });
            form.render();
        });
    },
    mounted: function mounted() {
        layui.use('form', function () {
            var form = layui.form;
            form.render();
        });
        this.fnGetEdit("#editor-homework-bar", "#editor-homework-con", this.task.homework.explain);
        var answer = this.task.answer.answer;
        for (var i = 0; i < answer.length; i++) {
            this.fnGetEdit("#editor-answer-bar" + (i + 1), "#editor-answer-con" + (i + 1), answer[i].answer);
        }
    },
    computed: {},
    filters: {},
    methods: {
        fnGetEdit: function fnGetEdit(bar, con, html) {
            var Editor = window.wangEditor;
            var editor = new Editor(bar, con);
            // 通过 url 参数配置 debug 模式。url 中带有 wangeditor_debug_mode=1 才会开启 debug 模式
            editor.customConfig.debug = location.href.indexOf('wangeditor_debug_mode=1') > 0;
            editor.customConfig.uploadImgShowBase64 = true;
            editor.customConfig.zIndex = 100;
            editor.create();
            var html = html || '';
            editor.txt.html(html);
        },
        fnListenerUpFile: function fnListenerUpFile(event, upfile, sign) {
            // 监听文件上传之添加文件
            var thisVue = this;
            var filelist = event.target.files;
            var filtered = window.utiltool.dealFileFilter({
                filtes: filelist,
                sign: sign,
                msglist: upfile.msglist
            });
            for (var i = 0; i < filtered.length; i++) {
                upfile.files.push(filtered[i].file);
                upfile.listdata.push({
                    localurl: window.utiltool.funGetUrlByType(filtered[i].file, filtered[i].type),
                    name: filtered[i].file.name,
                    size: filtered[i].formatFileSize
                });
            }
        },
        fnDelFile: function fnDelFile(event, index, upfile) {
            // 移除添加的文件
            console.log(event, index);
            upfile.listdata.removeByValue(upfile.listdata[index]);
            upfile.files.removeByValue(upfile.files[index]);
            upfile.msglist.removeByValue(upfile.msglist[index]);
        },
        fnDelUpFile: function fnDelUpFile(file, type) {
            // 删除已经上传的文件
            // 删除已经上传的文件
            var thisVue = this;
            if (type === 'homework') {
                thisVue.delfile.homework.push(file.id);
                thisVue.task.homework.files.removeByValue(file);
            } else {
                thisVue.delfile.answer.push(file.id);
                thisVue.task.answer.files.removeByValue(file);
            }
        },
        fnAddSummaryToFile: function fnAddSummaryToFile(event, index, upfile) {
            // 给上传的文件添加描述
            upfile.msglist[index].summary = $(event.path[0]).val();
        },
        fnSubmit: function fnSubmit() {
            // 提交添加的作业
            var thisVue = this;
            console.log('thisVue.$refs', thisVue.$refs);
            var homeworkcon = thisVue.$refs.homeworkcon.children[0].innerHTML;
            // 作业内容不能为空
            if (new RegExp(regexEnum.notempty).test(thisVue.$refs.homeworkcon.children[0].innerText)) {
                var data = {
                    courseid: thisVue.course.id,
                    sectionid: thisVue.task.sectionid,
                    homework: {
                        name: thisVue.$refs.name.value,
                        type: thisVue.task.homework.type,
                        explain: thisVue.$refs.homeworkcon.children[0].innerHTML,
                        id: thisVue.task.homework.id,
                        files: function () {
                            var arr = [];
                            var homeworkfiles = thisVue.task.homework.files;
                            for (var i = 0; i < homeworkfiles.length; i++) {
                                var hfilesindex = 'hfiles' + i;
                                arr.push({
                                    id: homeworkfiles[i].id,
                                    summary: thisVue.$refs[hfilesindex][0].value
                                });
                            }
                            return arr;
                        }(),
                        delfile: thisVue.delfile.homework
                    },
                    answer: {
                        answer: function () {
                            var arr = new Array();
                            var answerlen = thisVue.task.answer.answer.length;
                            for (var i = 0; i < answerlen; i++) {
                                var answercon = 'answercon' + (i + 1);
                                var attention = 'attention' + (i + 1);
                                arr.push({
                                    id: thisVue.task.answer.answer[i].id,
                                    answer: thisVue.$refs[answercon][0].children[0].innerHTML,
                                    attention: thisVue.$refs[attention][0].value
                                });
                            }
                            return arr;
                        }(),
                        delanswer: thisVue.delanswer,
                        addanswer: function () {
                            var arr = new Array();
                            var answerlen = thisVue.task.answer.answer.length;
                            for (var i = 0; i < thisVue.answernum; i++) {
                                var answercon = 'answercon' + (i + 1 + answerlen);
                                var attention = 'attention' + (i + 1 + answerlen);
                                arr.push({
                                    answer: thisVue.$refs[answercon][0].children[0].innerHTML,
                                    attention: thisVue.$refs[attention][0].value
                                });
                            }
                            return arr;
                        }(),
                        files: function () {
                            var arr = [];
                            var answerfiles = thisVue.task.answer.files;
                            for (var i = 0; i < answerfiles.length; i++) {
                                var afilesindex = 'afiles' + i;
                                arr.push({
                                    id: answerfiles[i].id,
                                    summary: thisVue.$refs[afilesindex][0].value
                                });
                            }
                            return arr;
                        }(),
                        delfile: thisVue.delfile.answer
                    },
                    files: thisVue.homeworkupfile.files.concat(thisVue.answerupfile.files),
                    msglist: thisVue.homeworkupfile.msglist.concat(thisVue.answerupfile.msglist)
                };
                console.log(data);
            } else {
                layer.msg('作业内容不能为空', { icon: 5, time: 2000 }, function () {
                    // thisVue.$refs.homeworkcon.children[0].onfocus();
                });
            }
        },
        fnAddAnswer: function fnAddAnswer() {
            // 继续添加作业答案
            var thisVue = this;
            thisVue.answernum++;
            var index = thisVue.task.answer.answer.length;
            this.$nextTick(function () {
                thisVue.fnGetEdit("#editor-answer-bar" + (thisVue.answernum + index), "#editor-answer-con" + (thisVue.answernum + index));
            });
        },
        fnRemoveAnswer: function fnRemoveAnswer() {
            // 移去作业答案答案
            this.answernum--;
            if (this.answernum <= 0) {
                this.answernum = 0;
            }
        },
        fnRemoveUpAnswer: function fnRemoveUpAnswer(item) {
            // 删除已经存在的答案
            this.delanswer.push(item.id);
            this.task.answer.answer.removeByValue(item);
        }

    },
    watch: {
        answernum: function answernum() {
            var thisVue = this;
        }
    }
};

/***/ }),
/* 124 */
/***/ (function(module, exports, __webpack_require__) {

module.exports={render:function (){var _vm=this;var _h=_vm.$createElement;var _c=_vm._self._c||_h;
  return _c('div', {
    staticClass: "class-wrap content operation",
    attrs: {
      "id": "course"
    }
  }, [_c('div', {
    staticClass: "classify clearfix"
  }, [_c('ul', {
    staticClass: "fl"
  }, [_c('li', {
    staticClass: "check"
  }, [_c('i', {
    staticClass: "before",
    staticStyle: {
      "left": "0"
    }
  }), _vm._v(" "), (_vm.$route.query.tp === 'edit') ? _c('a', {
    staticClass: "click",
    attrs: {
      "href": "javascript:void(0);"
    }
  }, [_vm._v("修改课堂作业")]) : _vm._e(), _vm._v(" "), (_vm.$route.query.tp === 'show') ? _c('a', {
    staticClass: "click",
    attrs: {
      "href": "javascript:void(0);"
    }
  }, [_vm._v("查看课堂作业")]) : _vm._e()])]), _vm._v(" "), (_vm.course) ? _c('div', {
    staticClass: "fr"
  }, [_c('span', [_vm._v(_vm._s(_vm.course.name))])]) : _vm._e()]), _vm._v(" "), _c('div', {
    staticClass: "classify-wrap"
  }, [_c('div', {
    staticClass: "layui-form"
  }, [_c('fieldset', {
    staticClass: "layui-elem-field"
  }, [_c('legend', [_vm._v("作业题目")]), _vm._v(" "), _c('div', {
    staticClass: "layui-field-box"
  }, [_c('div', {
    staticClass: "layui-form-item"
  }, [_c('label', {
    staticClass: "layui-form-label"
  }, [_vm._v("作业章节")]), _vm._v(" "), _c('div', {
    staticClass: "layui-input-inline"
  }, [_c('input', {
    staticClass: "layui-input",
    attrs: {
      "type": "text",
      "name": "name",
      "autocomplete": "off",
      "disabled": ""
    },
    domProps: {
      "value": _vm.task.sectionname
    }
  })])]), _vm._v(" "), _c('div', {
    staticClass: "layui-form-item"
  }, [_c('label', {
    staticClass: "layui-form-label"
  }, [_vm._v("作业类型")]), _vm._v(" "), _c('div', {
    staticClass: "layui-input-inline"
  }, [_c('input', {
    staticClass: "layui-input",
    attrs: {
      "type": "text",
      "name": "name",
      "autocomplete": "off",
      "disabled": ""
    },
    domProps: {
      "value": _vm.task.homework.type
    }
  })])]), _vm._v(" "), _c('div', {
    staticClass: "layui-form-item"
  }, [_c('label', {
    staticClass: "layui-form-label"
  }, [_vm._v("作业序号")]), _vm._v(" "), _c('div', {
    staticClass: "layui-input-inline"
  }, [_c('input', {
    staticClass: "layui-input",
    attrs: {
      "type": "text",
      "name": "name",
      "autocomplete": "off",
      "disabled": ""
    },
    domProps: {
      "value": _vm.task.homework.number
    }
  })])]), _vm._v(" "), _c('div', {
    staticClass: "layui-form-item"
  }, [_c('label', {
    staticClass: "layui-form-label"
  }, [_vm._v("作业编号")]), _vm._v(" "), _c('div', {
    staticClass: "layui-input-inline"
  }, [_c('input', {
    staticClass: "layui-input",
    attrs: {
      "type": "text",
      "name": "name",
      "autocomplete": "off",
      "disabled": ""
    },
    domProps: {
      "value": _vm.task.homework.idnumber
    }
  })])]), _vm._v(" "), _c('div', {
    staticClass: "layui-form-item"
  }, [_c('label', {
    staticClass: "layui-form-label"
  }, [_vm._v("作业编者")]), _vm._v(" "), _c('div', {
    staticClass: "layui-input-inline"
  }, [_c('input', {
    staticClass: "layui-input",
    attrs: {
      "type": "text",
      "name": "name",
      "autocomplete": "off",
      "disabled": ""
    },
    domProps: {
      "value": _vm.task.homework.author
    }
  })])]), _vm._v(" "), _c('div', {
    staticClass: "layui-form-item"
  }, [_c('label', {
    staticClass: "layui-form-label"
  }, [_vm._v("创建时间")]), _vm._v(" "), _c('div', {
    staticClass: "layui-input-inline"
  }, [_c('input', {
    staticClass: "layui-input",
    attrs: {
      "type": "text",
      "name": "name",
      "autocomplete": "off",
      "disabled": ""
    },
    domProps: {
      "value": _vm.task.homework.createtime
    }
  })])]), _vm._v(" "), _c('div', {
    staticClass: "layui-form-item"
  }, [_c('label', {
    staticClass: "layui-form-label"
  }, [_vm._v("修改时间")]), _vm._v(" "), _c('div', {
    staticClass: "layui-input-inline"
  }, [_c('input', {
    staticClass: "layui-input",
    attrs: {
      "type": "text",
      "name": "name",
      "autocomplete": "off",
      "disabled": ""
    },
    domProps: {
      "value": _vm.task.homework.modifiedtime
    }
  })])]), _vm._v(" "), _c('div', {
    staticClass: "layui-form-item"
  }, [_c('label', {
    staticClass: "layui-form-label"
  }, [_vm._v("作业名称")]), _vm._v(" "), _c('div', {
    staticClass: "layui-input-block"
  }, [_c('input', {
    ref: "name",
    staticClass: "layui-input",
    attrs: {
      "disabled": _vm.$route.query.tp === 'show',
      "type": "text",
      "name": "name",
      "lay-verify": "required",
      "autocomplete": "off",
      "placeholder": "请输入名称"
    },
    domProps: {
      "value": _vm.task.homework.name
    }
  })])]), _vm._v(" "), _c('div', {
    staticClass: "layui-form-item layui-form-text"
  }, [_c('label', {
    staticClass: "layui-form-label"
  }, [_vm._v("作业内容")]), _vm._v(" "), _c('div', {
    staticClass: "editor",
    attrs: {
      "id": "editor-homework"
    }
  }, [_c('div', {
    staticClass: "editor-bar",
    attrs: {
      "id": "editor-homework-bar"
    }
  }), _vm._v(" "), _c('div', {
    staticClass: "editor-file clearfix",
    attrs: {
      "id": "editor-homework-file"
    }
  }, [(_vm.$route.query.tp === 'edit') ? _c('div', {
    staticClass: "file-btn"
  }, [_c('input', {
    attrs: {
      "type": "file",
      "multiple": "",
      "name": "files-homework"
    },
    on: {
      "change": function($event) {
        _vm.fnListenerUpFile($event, _vm.homeworkupfile, 'homeworkupfile')
      }
    }
  }), _vm._v(" "), _vm._m(0)]) : _vm._e(), _vm._v(" "), _c('div', {
    staticClass: "file-list"
  }, [(_vm.task.homework.files.length > 0) ? _c('table', {
    staticClass: "table"
  }, [_vm._l((_vm.task.homework.files), function(item, index) {
    return _c('tr', [_c('td', [_c('div', {
      attrs: {
        "id": "preview"
      }
    }, [_c('img', {
      attrs: {
        "src": item.attachments,
        "alt": item.name
      }
    })])]), _vm._v(" "), _c('td', [_c('span', [_vm._v(_vm._s(item.name))])]), _vm._v(" "), _c('td', [_c('span', [_vm._v(_vm._s(item.type))])]), _vm._v(" "), _c('td', [_c('textarea', {
      ref: ['hfiles' + index],
      refInFor: true,
      staticClass: "textarea",
      attrs: {
        "disabled": _vm.$route.query.tp === 'show',
        "plcaeholder": "文件描述"
      }
    }, [_vm._v(_vm._s(item.summary))])]), _vm._v(" "), _c('td', [(_vm.$route.query.tp === 'edit') ? _c('p', {
      staticClass: "btn delete cancel",
      on: {
        "click": function($event) {
          _vm.fnDelUpFile(item, 'homework')
        }
      }
    }, [_vm._v("移除")]) : _vm._e(), _vm._v(" "), (_vm.$route.query.tp === 'show') ? _c('p', {
      staticClass: "btn download cancel"
    }, [_vm._v("下载")]) : _vm._e()])])
  }), _vm._v(" "), _vm._l((_vm.homeworkupfile.listdata), function(item, index) {
    return _c('tr', [_c('td', [_c('div', {
      attrs: {
        "id": "preview"
      }
    }, [_c('img', {
      attrs: {
        "src": item.localurl,
        "alt": item.name
      }
    })])]), _vm._v(" "), _c('td', [_c('span', [_vm._v(_vm._s(item.name))])]), _vm._v(" "), _c('td', [_c('span', [_vm._v(_vm._s(item.size))])]), _vm._v(" "), _c('td', [_c('textarea', {
      staticClass: "textarea",
      attrs: {
        "plcaeholder": "文件描述"
      },
      on: {
        "blur": function($event) {
          _vm.fnAddSummaryToFile($event, index, _vm.homeworkupfile)
        }
      }
    })]), _vm._v(" "), _c('td', [_c('p', {
      staticClass: "btn delete cancel",
      attrs: {
        "data-index": index
      },
      on: {
        "click": function($event) {
          _vm.fnDelFile($event, index, _vm.homeworkupfile)
        }
      }
    }, [_vm._v("移除")])])])
  })], 2) : _vm._e()])]), _vm._v(" "), _c('div', {
    ref: "homeworkcon",
    staticClass: "editor-con",
    attrs: {
      "id": "editor-homework-con"
    }
  })])])])]), _vm._v(" "), (_vm.$route.query.tp === 'show') ? _c('fieldset', {
    staticClass: "layui-elem-field"
  }, [_c('legend', [_vm._v("作业状态")]), _vm._v(" "), _c('div', {
    staticClass: "layui-field-box"
  }, [_c('div', {
    staticClass: "wrap"
  }, [_c('div', {
    staticClass: "item"
  }, [_c('div', {
    staticClass: "layui-form-item"
  }, [_c('label', {
    staticClass: "layui-form-label"
  }, [_vm._v("作业发布")]), _vm._v(" "), _c('div', {
    staticClass: "layui-input-block"
  }, [_c('input', {
    attrs: {
      "lay-filter": "issue",
      "type": "checkbox",
      "lay-skin": "switch",
      "lay-text": "ON|OFF",
      "disabled": ""
    },
    domProps: {
      "checked": _vm.task.homework.issue == '1'
    }
  })])])]), _vm._v(" "), (_vm.task.homework.issue == '1') ? _c('div', {
    staticClass: "item"
  }, [_c('div', {
    staticClass: "layui-form-item"
  }, [_c('label', {
    staticClass: "layui-form-label"
  }, [_vm._v("提交次数")]), _vm._v(" "), _c('div', {
    staticClass: "layui-input-block"
  }, [_c('input', {
    staticClass: "layui-input",
    attrs: {
      "type": "text",
      "autocomplete": "off",
      "placeholder": "作业答案提交次数",
      "readonly": ""
    },
    domProps: {
      "value": _vm.task.homework.issuemessage.dosubmit
    }
  })])]), _vm._v(" "), _c('div', {
    staticClass: "layui-form-item"
  }, [_c('label', {
    staticClass: "layui-form-label"
  }, [_vm._v("提交时间")]), _vm._v(" "), _c('div', {
    staticClass: "layui-input-block",
    staticStyle: {
      "width": "400px"
    }
  }, [_c('input', {
    staticClass: "layui-input",
    attrs: {
      "type": "text",
      "autocomplete": "off",
      "placeholder": "作业答案提交时间段",
      "readonly": ""
    },
    domProps: {
      "value": [_vm.task.homework.issuemessage.starttime + ' - ' + _vm.task.homework.issuemessage.closetime]
    }
  })])]), _vm._v(" "), _c('div', {
    staticClass: "layui-form-item"
  }, [_c('label', {
    staticClass: "layui-form-label"
  }, [_vm._v("作业满分值")]), _vm._v(" "), _c('div', {
    staticClass: "layui-input-block"
  }, [_c('input', {
    staticClass: "layui-input",
    attrs: {
      "type": "text",
      "autocomplete": "off",
      "placeholder": "作业答案作业满分值",
      "readonly": ""
    },
    domProps: {
      "value": _vm.task.homework.issuemessage.score
    }
  })])]), _vm._v(" "), _c('div', {
    staticClass: "layui-form-item"
  }, [_c('label', {
    staticClass: "layui-form-label"
  }, [_vm._v("作业说明")]), _vm._v(" "), _c('div', {
    staticClass: "layui-input-block"
  }, [_c('textarea', {
    staticClass: "layui-textarea",
    attrs: {
      "readonly": ""
    }
  }, [_vm._v(_vm._s(_vm.task.homework.issuemessage.attention))])])])]) : _vm._e()])])]) : _vm._e(), _vm._v(" "), _c('fieldset', {
    staticClass: "layui-elem-field"
  }, [_c('legend', [_vm._v("作业答案")]), _vm._v(" "), _c('div', {
    staticClass: "layui-field-box"
  }, [_c('div', {
    ref: "answerwrap",
    staticClass: "wrap"
  }, [_c('div', {
    staticClass: "layui-form-item"
  }, [_c('label', {
    staticClass: "layui-form-label"
  }, [_vm._v("答案附件")]), _vm._v(" "), _c('div', {
    staticClass: "layui-input-block",
    staticStyle: {
      "width": "80%"
    }
  }, [_c('div', {
    staticClass: "editor-file clearfix",
    attrs: {
      "id": "editor-answer-file"
    }
  }, [(_vm.$route.query.tp === 'edit') ? _c('div', {
    staticClass: "file-btn"
  }, [_c('input', {
    attrs: {
      "type": "file",
      "multiple": "",
      "name": "files-answer"
    },
    on: {
      "change": function($event) {
        _vm.fnListenerUpFile($event, _vm.answerupfile, 'answerupfile')
      }
    }
  }), _vm._v(" "), _vm._m(1)]) : _vm._e(), _vm._v(" "), _c('div', {
    staticClass: "file-list"
  }, [(_vm.task.answer.files.length > 0) ? _c('table', {
    staticClass: "table"
  }, [_vm._l((_vm.task.answer.files), function(item, index) {
    return _c('tr', [_c('td', [_c('div', {
      attrs: {
        "id": "preview"
      }
    }, [_c('img', {
      attrs: {
        "src": item.attachments,
        "alt": item.name
      }
    })])]), _vm._v(" "), _c('td', [_c('span', [_vm._v(_vm._s(item.name))])]), _vm._v(" "), _c('td', [_c('span', [_vm._v(_vm._s(item.type))])]), _vm._v(" "), _c('td', [_c('textarea', {
      ref: ['afiles' + index],
      refInFor: true,
      staticClass: "textarea",
      attrs: {
        "disabled": _vm.$route.query.tp === 'show',
        "plcaeholder": "文件描述"
      }
    }, [_vm._v(_vm._s(item.summary))])]), _vm._v(" "), _c('td', [(_vm.$route.query.tp === 'edit') ? _c('p', {
      staticClass: "btn delete cancel",
      on: {
        "click": function($event) {
          _vm.fnDelUpFile(item, 'answer')
        }
      }
    }, [_vm._v("移除")]) : _vm._e(), _vm._v(" "), (_vm.$route.query.tp === 'show') ? _c('p', {
      staticClass: "btn download cancel"
    }, [_vm._v("下载")]) : _vm._e()])])
  }), _vm._v(" "), _vm._l((_vm.answerupfile.listdata), function(item, index) {
    return _c('tr', [_c('td', [_c('div', {
      attrs: {
        "id": "preview"
      }
    }, [_c('img', {
      attrs: {
        "src": item.localurl,
        "alt": item.name
      }
    })])]), _vm._v(" "), _c('td', [_c('span', [_vm._v(_vm._s(item.name))])]), _vm._v(" "), _c('td', [_c('span', [_vm._v(_vm._s(item.size))])]), _vm._v(" "), _c('td', [_c('textarea', {
      staticClass: "textarea",
      attrs: {
        "plcaeholder": "文件描述"
      },
      on: {
        "blur": function($event) {
          _vm.fnAddSummaryToFile($event, index, _vm.answerupfile)
        }
      }
    })]), _vm._v(" "), _c('td', [_c('p', {
      staticClass: "btn delete cancel",
      attrs: {
        "data-index": index
      },
      on: {
        "click": function($event) {
          _vm.fnDelFile($event, index, _vm.answerupfile)
        }
      }
    }, [_vm._v("移除")])])])
  })], 2) : _vm._e()])])])]), _vm._v(" "), _vm._l((_vm.task.answer.answer), function(item, index) {
    return _c('div', {
      staticClass: "item"
    }, [_c('div', {
      staticClass: "layui-form-item"
    }, [_c('label', {
      staticClass: "layui-form-label"
    }, [_vm._v("答案备注")]), _vm._v(" "), _c('div', {
      staticClass: "layui-input-block"
    }, [_c('input', {
      ref: ['attention' + (index + 1)],
      refInFor: true,
      staticClass: "layui-input",
      attrs: {
        "type": "text",
        "autocomplete": "off",
        "placeholder": "作业答案特别说明"
      },
      domProps: {
        "value": item.summary
      }
    })])]), _vm._v(" "), _c('div', {
      staticClass: "layui-form-item layui-form-text"
    }, [_c('label', {
      staticClass: "layui-form-label"
    }, [_vm._v("作业答案")]), _vm._v(" "), _c('div', {
      staticClass: "editor",
      attrs: {
        "id": ['editor-answer' + (index + 1)]
      }
    }, [_c('div', {
      staticClass: "editor-bar",
      attrs: {
        "id": ['editor-answer-bar' + (index + 1)]
      }
    }), _vm._v(" "), _c('div', {
      ref: ['answercon' + (index + 1)],
      refInFor: true,
      staticClass: "editor-con",
      attrs: {
        "id": ['editor-answer-con' + (index + 1)]
      }
    })])]), _vm._v(" "), (_vm.$route.query.tp === 'edit') ? _c('div', {
      staticClass: "layui-form-item"
    }, [_c('div', {
      staticClass: "layui-input-block"
    }, [(_vm.task.answer.answer.length > 0) ? _c('button', {
      staticClass: "layui-btn layui-btn-primary",
      on: {
        "click": function($event) {
          _vm.fnRemoveUpAnswer(item)
        }
      }
    }, [_vm._v("删除答案")]) : _vm._e()])]) : _vm._e()])
  }), _vm._v(" "), _vm._l((_vm.answernum), function(item) {
    return _c('div', {
      staticClass: "item"
    }, [_c('div', {
      staticClass: "layui-form-item"
    }, [_c('label', {
      staticClass: "layui-form-label"
    }, [_vm._v("答案备注")]), _vm._v(" "), _c('div', {
      staticClass: "layui-input-block"
    }, [_c('input', {
      ref: ['attention' + (item + _vm.task.answer.answer.length)],
      refInFor: true,
      staticClass: "layui-input",
      attrs: {
        "type": "text",
        "autocomplete": "off",
        "placeholder": "作业答案特别说明"
      }
    })])]), _vm._v(" "), _c('div', {
      staticClass: "layui-form-item layui-form-text"
    }, [_c('label', {
      staticClass: "layui-form-label"
    }, [_vm._v("作业答案")]), _vm._v(" "), _c('div', {
      staticClass: "editor",
      attrs: {
        "id": ['editor-answer' + (item + _vm.task.answer.answer.length)]
      }
    }, [_c('div', {
      staticClass: "editor-bar",
      attrs: {
        "id": ['editor-answer-bar' + (item + _vm.task.answer.answer.length)]
      }
    }), _vm._v(" "), _c('div', {
      ref: ['answercon' + (item + _vm.task.answer.answer.length)],
      refInFor: true,
      staticClass: "editor-con",
      attrs: {
        "id": ['editor-answer-con' + (item + _vm.task.answer.answer.length)]
      }
    })])])])
  })], 2), _vm._v(" "), (_vm.$route.query.tp === 'edit') ? _c('div', {
    staticClass: "layui-form-item"
  }, [_c('div', {
    staticClass: "layui-input-block"
  }, [_c('button', {
    staticClass: "layui-btn layui-btn-primary",
    on: {
      "click": function($event) {
        _vm.fnAddAnswer()
      }
    }
  }, [_vm._v("添加答案")]), _vm._v(" "), (_vm.answernum > 0) ? _c('button', {
    staticClass: "layui-btn layui-btn-primary",
    on: {
      "click": function($event) {
        _vm.fnRemoveAnswer()
      }
    }
  }, [_vm._v("移除答案")]) : _vm._e()])]) : _vm._e(), _vm._v(" "), (_vm.$route.query.tp === 'show') ? _c('div', [_c('div', {
    staticClass: "layui-form-item"
  }, [_c('label', {
    staticClass: "layui-form-label"
  }, [_vm._v("答案公布")]), _vm._v(" "), _c('div', {
    staticClass: "layui-input-inline"
  }, [_c('input', {
    attrs: {
      "lay-filter": "announceanswer",
      "type": "checkbox",
      "name": "open",
      "lay-skin": "switch",
      "lay-text": "ON|OFF",
      "disabled": ""
    },
    domProps: {
      "checked": _vm.task.homework.announce_answer == '1'
    }
  })])])]) : _vm._e()])]), _vm._v(" "), _c('fieldset', {
    staticClass: "layui-elem-field"
  }, [_c('legend', [_vm._v("添加作业")]), _vm._v(" "), _c('div', {
    staticClass: "layui-field-box"
  }, [_c('div', {
    staticClass: "layui-form-item"
  }, [_c('div', {
    staticClass: "layui-input-block",
    staticStyle: {
      "width": "700px"
    }
  }, [(_vm.$route.query.tp === 'edit') ? _c('button', {
    staticClass: "layui-btn",
    attrs: {
      "lay-submit": "",
      "lay-filter": "submit"
    }
  }, [_vm._v("立即提交")]) : _vm._e(), _vm._v(" "), _c('router-link', {
    staticClass: "layui-btn layui-btn-primary",
    attrs: {
      "tag": "button",
      "to": "/course/tasks"
    }
  }, [_vm._v("返回")]), _vm._v(" "), (_vm.$route.query.tp === 'edit') ? _c('button', {
    staticClass: "layui-btn layui-btn-primary"
  }, [_vm._v("重置")]) : _vm._e(), _vm._v(" "), (_vm.$route.query.tp === 'show') ? _c('router-link', {
    staticClass: "layui-btn layui-btn-primary",
    attrs: {
      "tag": "button",
      "to": {
        path: '',
        query: {
          cd: _vm.$route.query.cd,
          sd: _vm.$route.query.sd,
          td: _vm.$route.query.td,
          tp: 'edit'
        }
      }
    }
  }, [_vm._v("启用编辑")]) : _vm._e(), _vm._v(" "), (_vm.$route.query.tp === 'edit') ? _c('router-link', {
    staticClass: "layui-btn layui-btn-primary",
    attrs: {
      "tag": "button",
      "to": {
        path: '',
        query: {
          cd: _vm.$route.query.cd,
          sd: _vm.$route.query.sd,
          td: _vm.$route.query.td,
          tp: 'show'
        }
      }
    }
  }, [_vm._v("停用编辑")]) : _vm._e()], 1)])])])])])])
},staticRenderFns: [function (){var _vm=this;var _h=_vm.$createElement;var _c=_vm._self._c||_h;
  return _c('p', {
    staticClass: "mark"
  }, [_c('i'), _vm._v("添加附件")])
},function (){var _vm=this;var _h=_vm.$createElement;var _c=_vm._self._c||_h;
  return _c('p', {
    staticClass: "mark"
  }, [_c('i'), _vm._v("添加附件")])
}]}
module.exports.render._withStripped = true
if (false) {
  module.hot.accept()
  if (module.hot.data) {
     require("vue-hot-reload-api").rerender("data-v-b1a778f2", module.exports)
  }
}

/***/ }),
/* 125 */
/***/ (function(module, exports, __webpack_require__) {


/* styles */
__webpack_require__(126)

var Component = __webpack_require__(3)(
  /* script */
  __webpack_require__(128),
  /* template */
  __webpack_require__(129),
  /* scopeId */
  null,
  /* cssModules */
  null
)
Component.options.__file = "D:\\wampserver\\wamp\\www\\vue\\course-vue\\src\\components\\subtask.vue"
if (Component.esModule && Object.keys(Component.esModule).some(function (key) {return key !== "default" && key !== "__esModule"})) {console.error("named exports are not supported in *.vue files.")}
if (Component.options.functional) {console.error("[vue-loader] subtask.vue: functional components are not supported with templates, they should use render functions.")}

/* hot reload */
if (false) {(function () {
  var hotAPI = require("vue-hot-reload-api")
  hotAPI.install(require("vue"), false)
  if (!hotAPI.compatible) return
  module.hot.accept()
  if (!module.hot.data) {
    hotAPI.createRecord("data-v-6cba7ad4", Component.options)
  } else {
    hotAPI.reload("data-v-6cba7ad4", Component.options)
  }
})()}

module.exports = Component.exports


/***/ }),
/* 126 */
/***/ (function(module, exports, __webpack_require__) {

// style-loader: Adds some css to the DOM by adding a <style> tag

// load the styles
var content = __webpack_require__(127);
if(typeof content === 'string') content = [[module.i, content, '']];
if(content.locals) module.exports = content.locals;
// add the styles to the DOM
var update = __webpack_require__(6)("05202d60", content, false);
// Hot Module Replacement
if(false) {
 // When the styles change, update the <style> tags
 if(!content.locals) {
   module.hot.accept("!!../../node_modules/css-loader/index.js!../../node_modules/vue-loader/lib/style-rewriter.js?id=data-v-6cba7ad4!../../node_modules/vue-loader/lib/selector.js?type=styles&index=0!./subtask.vue", function() {
     var newContent = require("!!../../node_modules/css-loader/index.js!../../node_modules/vue-loader/lib/style-rewriter.js?id=data-v-6cba7ad4!../../node_modules/vue-loader/lib/selector.js?type=styles&index=0!./subtask.vue");
     if(typeof newContent === 'string') newContent = [[module.id, newContent, '']];
     update(newContent);
   });
 }
 // When the module is disposed, remove the <style> tags
 module.hot.dispose(function() { update(); });
}

/***/ }),
/* 127 */
/***/ (function(module, exports, __webpack_require__) {

exports = module.exports = __webpack_require__(5)();
// imports


// module
exports.push([module.i, "\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n", ""]);

// exports


/***/ }),
/* 128 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
    value: true
});
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//

exports.default = {
    data: function data() {
        return {
            //
        };
    },
    mounted: function mounted() {
        layui.use('form', function () {
            var form = layui.form;
            form.render();
        });
        this.fnGetEdit("#editor-answer-bar", "#editor-answer-con");
    },
    methods: {
        fnGetEdit: function fnGetEdit(bar, con, html) {
            var html = html || '';
            var Editor = window.wangEditor;
            var editor = new Editor(bar, con);
            editor.customConfig.menus = ['head', // 标题
            'bold', // 粗体
            'italic', // 斜体
            'underline', // 下划线
            'strikeThrough', // 删除线
            'foreColor', // 文字颜色
            'backColor', // 背景颜色
            'link', // 插入链接
            'list', // 列表
            'justify', // 对齐方式
            'quote', // 引用
            'emoticon', // 表情
            'table', // 表格
            'code', // 插入代码
            'undo', // 撤销
            'redo' // 重复
            ];
            // 通过 url 参数配置 debug 模式。url 中带有 wangeditor_debug_mode=1 才会开启 debug 模式
            editor.customConfig.debug = location.href.indexOf('wangeditor_debug_mode=1') > 0;
            editor.customConfig.uploadImgShowBase64 = true;
            editor.customConfig.zIndex = 100;
            editor.create();
            editor.txt.html(html);
        }
    }
};

/***/ }),
/* 129 */
/***/ (function(module, exports, __webpack_require__) {

module.exports={render:function (){var _vm=this;var _h=_vm.$createElement;var _c=_vm._self._c||_h;
  return _vm._m(0)
},staticRenderFns: [function (){var _vm=this;var _h=_vm.$createElement;var _c=_vm._self._c||_h;
  return _c('div', {
    attrs: {
      "id": "subtask"
    }
  }, [_c('div', {
    staticClass: "layui-form"
  }, [_c('fieldset', {
    staticClass: "layui-elem-field"
  }, [_c('legend', [_vm._v("作业题目")]), _vm._v(" "), _c('div', {
    staticClass: "layui-field-box"
  }, [_c('div', {
    staticClass: "layui-form-item"
  }, [_c('label', {
    staticClass: "layui-form-label"
  }, [_vm._v("课堂")]), _vm._v(" "), _c('div', {
    staticClass: "layui-input-inline"
  }, [_c('input', {
    staticClass: "layui-input",
    attrs: {
      "type": "text",
      "name": "courseroom",
      "lay-verify": "title",
      "autocomplete": "off",
      "placeholder": "请输入名称"
    }
  })])]), _vm._v(" "), _c('div', {
    staticClass: "layui-form-item"
  }, [_c('label', {
    staticClass: "layui-form-label"
  }, [_vm._v("章节")]), _vm._v(" "), _c('div', {
    staticClass: "layui-input-inline"
  }, [_c('input', {
    staticClass: "layui-input",
    attrs: {
      "type": "text",
      "name": "section",
      "lay-verify": "title",
      "autocomplete": "off",
      "placeholder": "请输入名称"
    }
  })])]), _vm._v(" "), _c('div', {
    staticClass: "layui-form-item"
  }, [_c('label', {
    staticClass: "layui-form-label"
  }, [_vm._v("作业名称")]), _vm._v(" "), _c('div', {
    staticClass: "layui-input-block"
  }, [_c('input', {
    staticClass: "layui-input",
    attrs: {
      "type": "text",
      "name": "title",
      "lay-verify": "title",
      "autocomplete": "off",
      "placeholder": "请输入名称"
    }
  })])]), _vm._v(" "), _c('div', {
    staticClass: "layui-form-item layui-form-text"
  }, [_c('label', {
    staticClass: "layui-form-label"
  }, [_vm._v("作业内容")]), _vm._v(" "), _c('div', {
    staticClass: "layui-input-block"
  }, [_c('textarea', {
    staticClass: "layui-textarea",
    attrs: {
      "placeholder": "请输入内容"
    }
  })])])])]), _vm._v(" "), _c('fieldset', {
    staticClass: "layui-elem-field"
  }, [_c('legend', [_vm._v("提交作业")]), _vm._v(" "), _c('div', {
    staticClass: "layui-field-box"
  }, [_c('div', {
    staticClass: "layui-form-item"
  }, [_c('label', {
    staticClass: "layui-form-label"
  }, [_vm._v("答案备注")]), _vm._v(" "), _c('div', {
    staticClass: "layui-input-block"
  }, [_c('input', {
    staticClass: "layui-input",
    attrs: {
      "type": "text",
      "name": "title",
      "lay-verify": "title",
      "autocomplete": "off",
      "placeholder": "作业答案特别说明"
    }
  })])]), _vm._v(" "), _c('div', {
    staticClass: "layui-form-item layui-form-text"
  }, [_c('label', {
    staticClass: "layui-form-label"
  }, [_vm._v("作业答案")]), _vm._v(" "), _c('div', {
    staticClass: "editor",
    attrs: {
      "id": "editor-answer"
    }
  }, [_c('div', {
    staticClass: "editor-bar",
    attrs: {
      "id": "editor-answer-bar"
    }
  }), _vm._v(" "), _c('div', {
    staticClass: "editor-file clearfix",
    attrs: {
      "id": "editor-answer-file"
    }
  }, [_c('div', {
    staticClass: "file-btn"
  }, [_c('input', {
    attrs: {
      "type": "file",
      "name": "files-answer"
    }
  }), _vm._v(" "), _c('p', {
    staticClass: "mark"
  }, [_c('i'), _vm._v("添加附件")])]), _vm._v(" "), _c('div', {
    staticClass: "file-list"
  })]), _vm._v(" "), _c('div', {
    staticClass: "editor-con",
    attrs: {
      "id": "editor-answer-con"
    }
  })])])]), _vm._v(" "), _c('div', {
    staticClass: "layui-field-box"
  }, [_c('div', {
    staticClass: "layui-form-item"
  }, [_c('div', {
    staticClass: "layui-input-block"
  }, [_c('button', {
    staticClass: "layui-btn",
    attrs: {
      "lay-submit": "",
      "lay-filter": "demo1"
    }
  }, [_vm._v("立即提交")]), _vm._v(" "), _c('button', {
    staticClass: "layui-btn",
    attrs: {
      "lay-submit": "",
      "lay-filter": "demo2"
    }
  }, [_vm._v("返回")]), _vm._v(" "), _c('button', {
    staticClass: "layui-btn layui-btn-primary",
    attrs: {
      "type": "reset"
    }
  }, [_vm._v("重置")])])])])])])])
}]}
module.exports.render._withStripped = true
if (false) {
  module.hot.accept()
  if (module.hot.data) {
     require("vue-hot-reload-api").rerender("data-v-6cba7ad4", module.exports)
  }
}

/***/ }),
/* 130 */,
/* 131 */,
/* 132 */,
/* 133 */,
/* 134 */,
/* 135 */,
/* 136 */,
/* 137 */,
/* 138 */,
/* 139 */,
/* 140 */,
/* 141 */,
/* 142 */,
/* 143 */,
/* 144 */,
/* 145 */,
/* 146 */,
/* 147 */,
/* 148 */,
/* 149 */,
/* 150 */,
/* 151 */,
/* 152 */,
/* 153 */,
/* 154 */,
/* 155 */,
/* 156 */,
/* 157 */,
/* 158 */,
/* 159 */,
/* 160 */,
/* 161 */,
/* 162 */,
/* 163 */,
/* 164 */,
/* 165 */,
/* 166 */,
/* 167 */,
/* 168 */,
/* 169 */,
/* 170 */,
/* 171 */,
/* 172 */,
/* 173 */,
/* 174 */,
/* 175 */,
/* 176 */,
/* 177 */,
/* 178 */,
/* 179 */,
/* 180 */,
/* 181 */,
/* 182 */,
/* 183 */,
/* 184 */,
/* 185 */,
/* 186 */,
/* 187 */,
/* 188 */,
/* 189 */
/***/ (function(module, exports, __webpack_require__) {

module.exports = __webpack_require__(190);

/***/ }),
/* 190 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var utils = __webpack_require__(2);
var bind = __webpack_require__(72);
var Axios = __webpack_require__(192);
var defaults = __webpack_require__(48);

/**
 * Create an instance of Axios
 *
 * @param {Object} defaultConfig The default config for the instance
 * @return {Axios} A new instance of Axios
 */
function createInstance(defaultConfig) {
  var context = new Axios(defaultConfig);
  var instance = bind(Axios.prototype.request, context);

  // Copy axios.prototype to instance
  utils.extend(instance, Axios.prototype, context);

  // Copy context to instance
  utils.extend(instance, context);

  return instance;
}

// Create the default instance to be exported
var axios = createInstance(defaults);

// Expose Axios class to allow class inheritance
axios.Axios = Axios;

// Factory for creating new instances
axios.create = function create(instanceConfig) {
  return createInstance(utils.merge(defaults, instanceConfig));
};

// Expose Cancel & CancelToken
axios.Cancel = __webpack_require__(76);
axios.CancelToken = __webpack_require__(206);
axios.isCancel = __webpack_require__(75);

// Expose all/spread
axios.all = function all(promises) {
  return Promise.all(promises);
};
axios.spread = __webpack_require__(207);

module.exports = axios;

// Allow use of default import syntax in TypeScript
module.exports.default = axios;


/***/ }),
/* 191 */
/***/ (function(module, exports) {

/*!
 * Determine if an object is a Buffer
 *
 * @author   Feross Aboukhadijeh <https://feross.org>
 * @license  MIT
 */

// The _isBuffer check is for Safari 5-7 support, because it's missing
// Object.prototype.constructor. Remove this eventually
module.exports = function (obj) {
  return obj != null && (isBuffer(obj) || isSlowBuffer(obj) || !!obj._isBuffer)
}

function isBuffer (obj) {
  return !!obj.constructor && typeof obj.constructor.isBuffer === 'function' && obj.constructor.isBuffer(obj)
}

// For Node v0.10 support. Remove this eventually.
function isSlowBuffer (obj) {
  return typeof obj.readFloatLE === 'function' && typeof obj.slice === 'function' && isBuffer(obj.slice(0, 0))
}


/***/ }),
/* 192 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var defaults = __webpack_require__(48);
var utils = __webpack_require__(2);
var InterceptorManager = __webpack_require__(201);
var dispatchRequest = __webpack_require__(202);

/**
 * Create a new instance of Axios
 *
 * @param {Object} instanceConfig The default config for the instance
 */
function Axios(instanceConfig) {
  this.defaults = instanceConfig;
  this.interceptors = {
    request: new InterceptorManager(),
    response: new InterceptorManager()
  };
}

/**
 * Dispatch a request
 *
 * @param {Object} config The config specific for this request (merged with this.defaults)
 */
Axios.prototype.request = function request(config) {
  /*eslint no-param-reassign:0*/
  // Allow for axios('example/url'[, config]) a la fetch API
  if (typeof config === 'string') {
    config = utils.merge({
      url: arguments[0]
    }, arguments[1]);
  }

  config = utils.merge(defaults, {method: 'get'}, this.defaults, config);
  config.method = config.method.toLowerCase();

  // Hook up interceptors middleware
  var chain = [dispatchRequest, undefined];
  var promise = Promise.resolve(config);

  this.interceptors.request.forEach(function unshiftRequestInterceptors(interceptor) {
    chain.unshift(interceptor.fulfilled, interceptor.rejected);
  });

  this.interceptors.response.forEach(function pushResponseInterceptors(interceptor) {
    chain.push(interceptor.fulfilled, interceptor.rejected);
  });

  while (chain.length) {
    promise = promise.then(chain.shift(), chain.shift());
  }

  return promise;
};

// Provide aliases for supported request methods
utils.forEach(['delete', 'get', 'head', 'options'], function forEachMethodNoData(method) {
  /*eslint func-names:0*/
  Axios.prototype[method] = function(url, config) {
    return this.request(utils.merge(config || {}, {
      method: method,
      url: url
    }));
  };
});

utils.forEach(['post', 'put', 'patch'], function forEachMethodWithData(method) {
  /*eslint func-names:0*/
  Axios.prototype[method] = function(url, data, config) {
    return this.request(utils.merge(config || {}, {
      method: method,
      url: url,
      data: data
    }));
  };
});

module.exports = Axios;


/***/ }),
/* 193 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var utils = __webpack_require__(2);

module.exports = function normalizeHeaderName(headers, normalizedName) {
  utils.forEach(headers, function processHeader(value, name) {
    if (name !== normalizedName && name.toUpperCase() === normalizedName.toUpperCase()) {
      headers[normalizedName] = value;
      delete headers[name];
    }
  });
};


/***/ }),
/* 194 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var createError = __webpack_require__(74);

/**
 * Resolve or reject a Promise based on response status.
 *
 * @param {Function} resolve A function that resolves the promise.
 * @param {Function} reject A function that rejects the promise.
 * @param {object} response The response.
 */
module.exports = function settle(resolve, reject, response) {
  var validateStatus = response.config.validateStatus;
  // Note: status is not exposed by XDomainRequest
  if (!response.status || !validateStatus || validateStatus(response.status)) {
    resolve(response);
  } else {
    reject(createError(
      'Request failed with status code ' + response.status,
      response.config,
      null,
      response.request,
      response
    ));
  }
};


/***/ }),
/* 195 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


/**
 * Update an Error with the specified config, error code, and response.
 *
 * @param {Error} error The error to update.
 * @param {Object} config The config.
 * @param {string} [code] The error code (for example, 'ECONNABORTED').
 * @param {Object} [request] The request.
 * @param {Object} [response] The response.
 * @returns {Error} The error.
 */
module.exports = function enhanceError(error, config, code, request, response) {
  error.config = config;
  if (code) {
    error.code = code;
  }
  error.request = request;
  error.response = response;
  return error;
};


/***/ }),
/* 196 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var utils = __webpack_require__(2);

function encode(val) {
  return encodeURIComponent(val).
    replace(/%40/gi, '@').
    replace(/%3A/gi, ':').
    replace(/%24/g, '$').
    replace(/%2C/gi, ',').
    replace(/%20/g, '+').
    replace(/%5B/gi, '[').
    replace(/%5D/gi, ']');
}

/**
 * Build a URL by appending params to the end
 *
 * @param {string} url The base of the url (e.g., http://www.google.com)
 * @param {object} [params] The params to be appended
 * @returns {string} The formatted url
 */
module.exports = function buildURL(url, params, paramsSerializer) {
  /*eslint no-param-reassign:0*/
  if (!params) {
    return url;
  }

  var serializedParams;
  if (paramsSerializer) {
    serializedParams = paramsSerializer(params);
  } else if (utils.isURLSearchParams(params)) {
    serializedParams = params.toString();
  } else {
    var parts = [];

    utils.forEach(params, function serialize(val, key) {
      if (val === null || typeof val === 'undefined') {
        return;
      }

      if (utils.isArray(val)) {
        key = key + '[]';
      } else {
        val = [val];
      }

      utils.forEach(val, function parseValue(v) {
        if (utils.isDate(v)) {
          v = v.toISOString();
        } else if (utils.isObject(v)) {
          v = JSON.stringify(v);
        }
        parts.push(encode(key) + '=' + encode(v));
      });
    });

    serializedParams = parts.join('&');
  }

  if (serializedParams) {
    url += (url.indexOf('?') === -1 ? '?' : '&') + serializedParams;
  }

  return url;
};


/***/ }),
/* 197 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var utils = __webpack_require__(2);

// Headers whose duplicates are ignored by node
// c.f. https://nodejs.org/api/http.html#http_message_headers
var ignoreDuplicateOf = [
  'age', 'authorization', 'content-length', 'content-type', 'etag',
  'expires', 'from', 'host', 'if-modified-since', 'if-unmodified-since',
  'last-modified', 'location', 'max-forwards', 'proxy-authorization',
  'referer', 'retry-after', 'user-agent'
];

/**
 * Parse headers into an object
 *
 * ```
 * Date: Wed, 27 Aug 2014 08:58:49 GMT
 * Content-Type: application/json
 * Connection: keep-alive
 * Transfer-Encoding: chunked
 * ```
 *
 * @param {String} headers Headers needing to be parsed
 * @returns {Object} Headers parsed into an object
 */
module.exports = function parseHeaders(headers) {
  var parsed = {};
  var key;
  var val;
  var i;

  if (!headers) { return parsed; }

  utils.forEach(headers.split('\n'), function parser(line) {
    i = line.indexOf(':');
    key = utils.trim(line.substr(0, i)).toLowerCase();
    val = utils.trim(line.substr(i + 1));

    if (key) {
      if (parsed[key] && ignoreDuplicateOf.indexOf(key) >= 0) {
        return;
      }
      if (key === 'set-cookie') {
        parsed[key] = (parsed[key] ? parsed[key] : []).concat([val]);
      } else {
        parsed[key] = parsed[key] ? parsed[key] + ', ' + val : val;
      }
    }
  });

  return parsed;
};


/***/ }),
/* 198 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var utils = __webpack_require__(2);

module.exports = (
  utils.isStandardBrowserEnv() ?

  // Standard browser envs have full support of the APIs needed to test
  // whether the request URL is of the same origin as current location.
  (function standardBrowserEnv() {
    var msie = /(msie|trident)/i.test(navigator.userAgent);
    var urlParsingNode = document.createElement('a');
    var originURL;

    /**
    * Parse a URL to discover it's components
    *
    * @param {String} url The URL to be parsed
    * @returns {Object}
    */
    function resolveURL(url) {
      var href = url;

      if (msie) {
        // IE needs attribute set twice to normalize properties
        urlParsingNode.setAttribute('href', href);
        href = urlParsingNode.href;
      }

      urlParsingNode.setAttribute('href', href);

      // urlParsingNode provides the UrlUtils interface - http://url.spec.whatwg.org/#urlutils
      return {
        href: urlParsingNode.href,
        protocol: urlParsingNode.protocol ? urlParsingNode.protocol.replace(/:$/, '') : '',
        host: urlParsingNode.host,
        search: urlParsingNode.search ? urlParsingNode.search.replace(/^\?/, '') : '',
        hash: urlParsingNode.hash ? urlParsingNode.hash.replace(/^#/, '') : '',
        hostname: urlParsingNode.hostname,
        port: urlParsingNode.port,
        pathname: (urlParsingNode.pathname.charAt(0) === '/') ?
                  urlParsingNode.pathname :
                  '/' + urlParsingNode.pathname
      };
    }

    originURL = resolveURL(window.location.href);

    /**
    * Determine if a URL shares the same origin as the current location
    *
    * @param {String} requestURL The URL to test
    * @returns {boolean} True if URL shares the same origin, otherwise false
    */
    return function isURLSameOrigin(requestURL) {
      var parsed = (utils.isString(requestURL)) ? resolveURL(requestURL) : requestURL;
      return (parsed.protocol === originURL.protocol &&
            parsed.host === originURL.host);
    };
  })() :

  // Non standard browser envs (web workers, react-native) lack needed support.
  (function nonStandardBrowserEnv() {
    return function isURLSameOrigin() {
      return true;
    };
  })()
);


/***/ }),
/* 199 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


// btoa polyfill for IE<10 courtesy https://github.com/davidchambers/Base64.js

var chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=';

function E() {
  this.message = 'String contains an invalid character';
}
E.prototype = new Error;
E.prototype.code = 5;
E.prototype.name = 'InvalidCharacterError';

function btoa(input) {
  var str = String(input);
  var output = '';
  for (
    // initialize result and counter
    var block, charCode, idx = 0, map = chars;
    // if the next str index does not exist:
    //   change the mapping table to "="
    //   check if d has no fractional digits
    str.charAt(idx | 0) || (map = '=', idx % 1);
    // "8 - idx % 1 * 8" generates the sequence 2, 4, 6, 8
    output += map.charAt(63 & block >> 8 - idx % 1 * 8)
  ) {
    charCode = str.charCodeAt(idx += 3 / 4);
    if (charCode > 0xFF) {
      throw new E();
    }
    block = block << 8 | charCode;
  }
  return output;
}

module.exports = btoa;


/***/ }),
/* 200 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var utils = __webpack_require__(2);

module.exports = (
  utils.isStandardBrowserEnv() ?

  // Standard browser envs support document.cookie
  (function standardBrowserEnv() {
    return {
      write: function write(name, value, expires, path, domain, secure) {
        var cookie = [];
        cookie.push(name + '=' + encodeURIComponent(value));

        if (utils.isNumber(expires)) {
          cookie.push('expires=' + new Date(expires).toGMTString());
        }

        if (utils.isString(path)) {
          cookie.push('path=' + path);
        }

        if (utils.isString(domain)) {
          cookie.push('domain=' + domain);
        }

        if (secure === true) {
          cookie.push('secure');
        }

        document.cookie = cookie.join('; ');
      },

      read: function read(name) {
        var match = document.cookie.match(new RegExp('(^|;\\s*)(' + name + ')=([^;]*)'));
        return (match ? decodeURIComponent(match[3]) : null);
      },

      remove: function remove(name) {
        this.write(name, '', Date.now() - 86400000);
      }
    };
  })() :

  // Non standard browser env (web workers, react-native) lack needed support.
  (function nonStandardBrowserEnv() {
    return {
      write: function write() {},
      read: function read() { return null; },
      remove: function remove() {}
    };
  })()
);


/***/ }),
/* 201 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var utils = __webpack_require__(2);

function InterceptorManager() {
  this.handlers = [];
}

/**
 * Add a new interceptor to the stack
 *
 * @param {Function} fulfilled The function to handle `then` for a `Promise`
 * @param {Function} rejected The function to handle `reject` for a `Promise`
 *
 * @return {Number} An ID used to remove interceptor later
 */
InterceptorManager.prototype.use = function use(fulfilled, rejected) {
  this.handlers.push({
    fulfilled: fulfilled,
    rejected: rejected
  });
  return this.handlers.length - 1;
};

/**
 * Remove an interceptor from the stack
 *
 * @param {Number} id The ID that was returned by `use`
 */
InterceptorManager.prototype.eject = function eject(id) {
  if (this.handlers[id]) {
    this.handlers[id] = null;
  }
};

/**
 * Iterate over all the registered interceptors
 *
 * This method is particularly useful for skipping over any
 * interceptors that may have become `null` calling `eject`.
 *
 * @param {Function} fn The function to call for each interceptor
 */
InterceptorManager.prototype.forEach = function forEach(fn) {
  utils.forEach(this.handlers, function forEachHandler(h) {
    if (h !== null) {
      fn(h);
    }
  });
};

module.exports = InterceptorManager;


/***/ }),
/* 202 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var utils = __webpack_require__(2);
var transformData = __webpack_require__(203);
var isCancel = __webpack_require__(75);
var defaults = __webpack_require__(48);
var isAbsoluteURL = __webpack_require__(204);
var combineURLs = __webpack_require__(205);

/**
 * Throws a `Cancel` if cancellation has been requested.
 */
function throwIfCancellationRequested(config) {
  if (config.cancelToken) {
    config.cancelToken.throwIfRequested();
  }
}

/**
 * Dispatch a request to the server using the configured adapter.
 *
 * @param {object} config The config that is to be used for the request
 * @returns {Promise} The Promise to be fulfilled
 */
module.exports = function dispatchRequest(config) {
  throwIfCancellationRequested(config);

  // Support baseURL config
  if (config.baseURL && !isAbsoluteURL(config.url)) {
    config.url = combineURLs(config.baseURL, config.url);
  }

  // Ensure headers exist
  config.headers = config.headers || {};

  // Transform request data
  config.data = transformData(
    config.data,
    config.headers,
    config.transformRequest
  );

  // Flatten headers
  config.headers = utils.merge(
    config.headers.common || {},
    config.headers[config.method] || {},
    config.headers || {}
  );

  utils.forEach(
    ['delete', 'get', 'head', 'post', 'put', 'patch', 'common'],
    function cleanHeaderConfig(method) {
      delete config.headers[method];
    }
  );

  var adapter = config.adapter || defaults.adapter;

  return adapter(config).then(function onAdapterResolution(response) {
    throwIfCancellationRequested(config);

    // Transform response data
    response.data = transformData(
      response.data,
      response.headers,
      config.transformResponse
    );

    return response;
  }, function onAdapterRejection(reason) {
    if (!isCancel(reason)) {
      throwIfCancellationRequested(config);

      // Transform response data
      if (reason && reason.response) {
        reason.response.data = transformData(
          reason.response.data,
          reason.response.headers,
          config.transformResponse
        );
      }
    }

    return Promise.reject(reason);
  });
};


/***/ }),
/* 203 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var utils = __webpack_require__(2);

/**
 * Transform the data for a request or a response
 *
 * @param {Object|String} data The data to be transformed
 * @param {Array} headers The headers for the request or response
 * @param {Array|Function} fns A single function or Array of functions
 * @returns {*} The resulting transformed data
 */
module.exports = function transformData(data, headers, fns) {
  /*eslint no-param-reassign:0*/
  utils.forEach(fns, function transform(fn) {
    data = fn(data, headers);
  });

  return data;
};


/***/ }),
/* 204 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


/**
 * Determines whether the specified URL is absolute
 *
 * @param {string} url The URL to test
 * @returns {boolean} True if the specified URL is absolute, otherwise false
 */
module.exports = function isAbsoluteURL(url) {
  // A URL is considered absolute if it begins with "<scheme>://" or "//" (protocol-relative URL).
  // RFC 3986 defines scheme name as a sequence of characters beginning with a letter and followed
  // by any combination of letters, digits, plus, period, or hyphen.
  return /^([a-z][a-z\d\+\-\.]*:)?\/\//i.test(url);
};


/***/ }),
/* 205 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


/**
 * Creates a new URL by combining the specified URLs
 *
 * @param {string} baseURL The base URL
 * @param {string} relativeURL The relative URL
 * @returns {string} The combined URL
 */
module.exports = function combineURLs(baseURL, relativeURL) {
  return relativeURL
    ? baseURL.replace(/\/+$/, '') + '/' + relativeURL.replace(/^\/+/, '')
    : baseURL;
};


/***/ }),
/* 206 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var Cancel = __webpack_require__(76);

/**
 * A `CancelToken` is an object that can be used to request cancellation of an operation.
 *
 * @class
 * @param {Function} executor The executor function.
 */
function CancelToken(executor) {
  if (typeof executor !== 'function') {
    throw new TypeError('executor must be a function.');
  }

  var resolvePromise;
  this.promise = new Promise(function promiseExecutor(resolve) {
    resolvePromise = resolve;
  });

  var token = this;
  executor(function cancel(message) {
    if (token.reason) {
      // Cancellation has already been requested
      return;
    }

    token.reason = new Cancel(message);
    resolvePromise(token.reason);
  });
}

/**
 * Throws a `Cancel` if cancellation has been requested.
 */
CancelToken.prototype.throwIfRequested = function throwIfRequested() {
  if (this.reason) {
    throw this.reason;
  }
};

/**
 * Returns an object that contains a new `CancelToken` and a function that, when called,
 * cancels the `CancelToken`.
 */
CancelToken.source = function source() {
  var cancel;
  var token = new CancelToken(function executor(c) {
    cancel = c;
  });
  return {
    token: token,
    cancel: cancel
  };
};

module.exports = CancelToken;


/***/ }),
/* 207 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


/**
 * Syntactic sugar for invoking a function and expanding an array for arguments.
 *
 * Common use case would be to use `Function.prototype.apply`.
 *
 *  ```js
 *  function f(x, y, z) {}
 *  var args = [1, 2, 3];
 *  f.apply(null, args);
 *  ```
 *
 * With `spread` this example can be re-written.
 *
 *  ```js
 *  spread(function(x, y, z) {})([1, 2, 3]);
 *  ```
 *
 * @param {Function} callback
 * @returns {Function}
 */
module.exports = function spread(callback) {
  return function wrap(arr) {
    return callback.apply(null, arr);
  };
};


/***/ })
],[78]);