/**
 * BSA.Utility - обьект утилит
 *
 * В набор утилит входят:
 *  - Функции для работы с объектами
 *  - Функции для работы с массивами
 *  - Функции для работы с функциями
 *  - Работать с частями адреса URL
 *
 * JavaScript
 *
 * Copyright (c) 2011 Бескоровайный Сергей
 *
 * @author     Бескоровайный Сергей <bs261257@gmail.com>
 * @copyright  2011 Бескоровайный Сергей
 * @license    BSD
 * @version    1.00.00
 * @link       http://my-site.com/web
 */

(function () {
    // encapsulated variables
    var UNDEFINED,
    doc = document,
    win = window,
    math = Math,
    mathRound = math.round,
    mathFloor = math.floor,
    mathCeil = math.ceil,
    mathMax = math.max,
    mathMin = math.min,
    mathAbs = math.abs,
    mathCos = math.cos,
    mathSin = math.sin,
    mathPI = math.PI,
    deg2rad = mathPI * 2 / 360,

    // some variables
    userAgent = navigator.userAgent,
    isOpera = win.opera,
    isIE = /msie/i.test(userAgent) && !isOpera,
    isWebKit = /AppleWebKit/.test(userAgent),
    isFirefox = /Firefox/.test(userAgent),
    isTouchDevice = /(Mobile|Android|Windows Phone)/.test(userAgent),
    defaultOptions = {
        lang: {
            decimalPoint: '.',
            months: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
            shortMonths: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
            thousandsSep: ',',
            weekdays: ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"]
        }
    },
timeUnits,

NONE = 'none',
MILLISECOND = 'millisecond',
SECOND = 'second',
MINUTE = 'minute',
HOUR = 'hour',
DAY = 'day',
WEEK = 'week',
MONTH = 'month',
YEAR = 'year';

// The Highcharts namespace
win.BSA.Utility = win.BSA.Utility ? error(16, true) : {};
    
    /**
    * Provide error messages for debugging, with links to online explanation 
    */
    function error(code, stop) {
        var msg = 'Highcharts error #' + code + ': www.highcharts.com/errors/' + code;
        if (stop) {
            throw msg;
        } else if (win.console) {
            console.log(msg);
        }
    }
    
    /**
    * Merge the default options with custom options and return the new options structure
    * @param {Object} options The new custom options
    */
    var setOptions = function (options) {
        // Merge in the default options
        defaultOptions = merge(defaultOptions, options);
	
        return defaultOptions;
    }

    /**
    * Get the updated default options. Merely exposing defaultOptions for outside modules
    * isn't enough because the setOptions method creates a new object.
    */
    var getOptions = function () {
        return defaultOptions;
    }

    //-------- Работа с обьектами ---------//
    
    // Возвращает массив, содержащий имена перечислимых свойств объекта "o"
    var getPropertyNames = function(/* объект */o) {
        var r = [];
        for(name in o) r.push(name);
        return r;
    }
    
    // Копирует перечислимые свойства объекта "from" в объект "to",
    // но только те, которые еще не определены в объекте "to".
    // Это может оказаться необходимым, например, когда объект "from" содержит
    // значения по умолчанию, которые необходимо скопировать в свойства,
    // если они еще не были определены в объекте "to".
    var copyUndefinedProperties = function (/* объект */ from, /* объект */ to) {
        for(p in from) {
            if (!p in to) to[p] = from[p];
        }
    }
    
    // Определение типа объекта с помощью метода Object.toString()
    // ф-ия реализует расширенные возможности по выяснению типа. Как уже отмечалось ранее, 
    // метод toString() не работает с пользовательскими классами, в этом случае 
    // показанная далее функция проверяет строковое значение свойства classname и 
    // возвращает его значение, если оно определено
    var getType = function (x) {
        // Если значение x равно null, возвращается "null"
        if (x == null) return "null";
        // Попробовать определить тип с помощью оператора typeof
        var t = typeof x;
        // Если получен непонятный результат, вернуть его
        if (t != "object") return t;
        // В противном случае, x – это объект. Вызвать метод toString()
        // по умолчанию и извлечь подстроку с именем класса.
        var c = Object.prototype.toString.apply(x); // В формате "[object class]"
        c = c.substring(8, c.length-1); // Удалить "[object" и "]"
        // Если имя класса  не Object, вернуть его.
        if (c != "Object") return c;
        // Если получен тип "Object", проверить, может быть x
        // действительно принадлежит этому классу.
        if (x.constructor == Object) return c; // Тип действительно "Object"
        // Для пользовательских классов извлечь строковое значение свойства
        // classname, которое наследуется от объектапрототипа
        if ("classname" in x.constructor.prototype && // наследуемое имя класса
            typeof x.constructor.prototype.classname == "string") // это строка
            return x.constructor.prototype.classname;
        // Если определить тип так и не удалось, так и скажем об этом.
        return "<unknown type>";
    }
    
    /**
    * Extend an object with the members of another
    * @param {Object} a The object to be extended
    * @param {Object} b The object to add to the first one
    */
    var extend = function(a, b) {
        var n;
        if (!a) {
            a = {};
        }
        for (n in b) {
            a[n] = b[n];
        }
        return a;
    }
    
    /**
    * Deep merge two or more objects and return a third object.
    * Previously this function redirected to jQuery.extend(true), but this had two limitations.
    * First, it deep merged arrays, which lead to workarounds in Highcharts. Second,
    * it copied properties from extended prototypes. 
    */
    var merge = function () {
        var i,
        len = arguments.length,
        ret = {},
        doCopy = function (copy, original) {
            var value, key;

            for (key in original) {
                if (original.hasOwnProperty(key)) {
                    value = original[key];

                    // An object is replacing a primitive
                    if (typeof copy !== 'object') {
                        copy = {};
                    }
						
                    // Copy the contents of objects, but not arrays or DOM nodes
                    if (value && typeof value === 'object' && Object.prototype.toString.call(value) !== '[object Array]'
                        && typeof value.nodeType !== 'number') {
                        copy[key] = doCopy(copy[key] || {}, value);
				
                    // Primitives and arrays are copied over directly
                    } else {
                        copy[key] = original[key];
                    }
                }
            }
            return copy;
        };

        // For each argument, extend the return
        for (i = 0; i < len; i++) {
            ret = doCopy(ret, arguments[i]);
        }

        return ret;
    }

    /**
    * Take an array and turn into a hash with even number arguments as keys and odd numbers as
    * values. Allows creating constants for commonly used style properties, attributes etc.
    * Avoid it in performance critical situations like looping
    */
    var hash = function () {
        var i = 0,
        args = arguments,
        length = args.length,
        obj = {};
        for (; i < length; i++) {
            obj[args[i++]] = args[i];
        }
        return obj;
    }
    
    /**
    * Returns true if the object is not null or undefined. Like MooTools' $.defined.
    * @param {Object} obj
    */
    var defined = function (obj) {
        return obj !== UNDEFINED && obj !== null;
    }
    
    /**
    * Extend a prototyped class by new members
    * @param {Object} parent
    * @param {Object} members
    */
    var extendClass = function (parent, members) {
        var object = function () {};
        object.prototype = new parent();
        extend(object.prototype, members);
        return object;
    }
    
    /**
    * Wrap a method with extended functionality, preserving the original function
    * @param {Object} obj The context object that the method belongs to 
    * @param {String} method The name of the method to extend
    * @param {Function} func A wrapper function callback. This function is called with the same arguments
    * as the original function, except that the original function is unshifted and passed as the first 
    * argument. 
    */
    var wrap = function (obj, method, func) {
        var proceed = obj[method];
        obj[method] = function () {
            var args = Array.prototype.slice.call(arguments);
            args.unshift(proceed);
            return func.apply(this, args);
        };
    }
    
    /**
    * Utility method that destroys any SVGElement or VMLElement that are properties on the given object.
    * It loops all properties and invokes destroy if there is a destroy method. The property is
    * then delete'ed.
    * @param {Object} The object to destroy properties on
    * @param {Object} Exception, do not destroy this property, only delete it.
    */
    var destroyObjectProperties = function (obj, except) {
        var n;
        for (n in obj) {
            // If the object is non-null and destroy is defined
            if (obj[n] && obj[n] !== except && obj[n].destroy) {
                // Invoke the destroy
                obj[n].destroy();
            }

            // Delete the property from the object.
            delete obj[n];
        }
    }
    
    //-------- Работа с функциями ---------//
    
    /**
    * Return the first value that is defined. Like MooTools' $.pick.
    */
    var pick = function () {
        var args = arguments,
        i,
        arg,
        length = args.length;
        for (i = 0; i < length; i++) {
            arg = args[i];
            if (typeof arg !== 'undefined' && arg !== null) {
                return arg;
            }
        }
    }
    
    // Возвращает самостоятельную функцию, которая в свою очередь вызывает
    // функцию "f" как метод объекта "o". Эта функция может использоваться,
    // когда возникает необходимость передать в функцию метод.
    // Если не связать метод с объектом, ассоциация будет утрачена, и метод,
    // переданный функции, будет вызван как обычная функция.
    var bindMethod = function(/* объект */ o, /* функция */ f) {
        return function() {
            return f.apply(o, arguments)
        }
    }
    
    // Возвращает самостоятельную функцию, которая в свою очередь вызывает
    // функцию "f" с заданными аргументами и добавляет дополнительные
    // аргументы, передаваемые возвращаемой функции.
    // (Этот прием иногда называется "currying".)
    var bindArguments = function (/* функция */ f /*, начальные аргументы... */) {
        var boundArgs = arguments;
        return function() {
            // Создать массив аргументов. Он будет начинаться с аргументов,
            // определенных ранее, и заканчиваться аргументами, переданными сейчас
            var args = [];
            for(var i = 1; i < boundArgs.length; i++) args.push(boundArgs[i]);
            for(var i = 0; i < arguments.length; i++) args.push(arguments[i]);
            // Теперь вызвать функцию с новым списком аргументов
            return f.apply(this, args);
        }
    }
    
    //-------- Ф-ии кодирования/декодирования ---------//
    
    var base64_encode = function(data) {
        // http://kevin.vanzonneveld.net
        // +   original by: Tyler Akins (http://rumkin.com)
        // +   improved by: Bayron Guevara
        // +   improved by: Thunder.m
        // +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
        // +   bugfixed by: Pellentesque Malesuada
        // +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
        // +   improved by: Rafał Kukawski (http://kukawski.pl)
        // *     example 1: base64_encode('Kevin van Zonneveld');
        // *     returns 1: 'S2V2aW4gdmFuIFpvbm5ldmVsZA=='
        // mozilla has this native
        // - but breaks in 2.0.0.12!
        //if (typeof this.window['btoa'] == 'function') {
        //    return btoa(data);
        //}
        var b64 = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=";
        var o1, o2, o3, h1, h2, h3, h4, bits, i = 0,
        ac = 0,
        enc = "",
        tmp_arr = [];

        if (!data) {
            return data;
        }

        do { // pack three octets into four hexets
            o1 = data.charCodeAt(i++);
            o2 = data.charCodeAt(i++);
            o3 = data.charCodeAt(i++);

            bits = o1 << 16 | o2 << 8 | o3;

            h1 = bits >> 18 & 0x3f;
            h2 = bits >> 12 & 0x3f;
            h3 = bits >> 6 & 0x3f;
            h4 = bits & 0x3f;

            // use hexets to index into b64, and append result to encoded string
            tmp_arr[ac++] = b64.charAt(h1) + b64.charAt(h2) + b64.charAt(h3) + b64.charAt(h4);
        } while (i < data.length);

        enc = tmp_arr.join('');

        var r = data.length % 3;

        return (r ? enc.slice(0, r - 3) : enc) + '==='.slice(r || 3);

    }
    
    var base64_decode = function(data) {
        // http://kevin.vanzonneveld.net
        // +   original by: Tyler Akins (http://rumkin.com)
        // +   improved by: Thunder.m
        // +      input by: Aman Gupta
        // +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
        // +   bugfixed by: Onno Marsman
        // +   bugfixed by: Pellentesque Malesuada
        // +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
        // +      input by: Brett Zamir (http://brett-zamir.me)
        // +   bugfixed by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
        // *     example 1: base64_decode('S2V2aW4gdmFuIFpvbm5ldmVsZA==');
        // *     returns 1: 'Kevin van Zonneveld'
        // mozilla has this native
        // - but breaks in 2.0.0.12!
        //if (typeof this.window['atob'] == 'function') {
        //    return atob(data);
        //}
        var b64 = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=";
        var o1, o2, o3, h1, h2, h3, h4, bits, i = 0,
        ac = 0,
        dec = "",
        tmp_arr = [];

        if (!data) {
            return data;
        }

        data += '';

        do { // unpack four hexets into three octets using index points in b64
            h1 = b64.indexOf(data.charAt(i++));
            h2 = b64.indexOf(data.charAt(i++));
            h3 = b64.indexOf(data.charAt(i++));
            h4 = b64.indexOf(data.charAt(i++));

            bits = h1 << 18 | h2 << 12 | h3 << 6 | h4;

            o1 = bits >> 16 & 0xff;
            o2 = bits >> 8 & 0xff;
            o3 = bits & 0xff;

            if (h3 == 64) {
                tmp_arr[ac++] = String.fromCharCode(o1);
            } else if (h4 == 64) {
                tmp_arr[ac++] = String.fromCharCode(o1, o2);
            } else {
                tmp_arr[ac++] = String.fromCharCode(o1, o2, o3);
            }
        } while (i < data.length);

        dec = tmp_arr.join('');

        return dec;
    }

    //-------- Математические ф-ии ---------//

    /**
    * Shortcut for parseInt
    * @param {Object} s
    * @param {Number} mag Magnitude
    */
    var pInt = function (s, mag) {
        return parseInt(s, mag || 10);
    }
    
    var log2lin = function (num) {
        return math.log(num) / math.LN10;
    }
    var lin2log = function (num) {
        return math.pow(10, num);
    }
    
    /**
    * How many decimals are there in a number
    */
    var getDecimals = function getDecimals(number) {
	
        number = (number || 0).toString();
	
        return number.indexOf('.') > -1 ? 
        number.split('.')[1].length :
        0;
    }
    
    /**
    * Utility method that sorts an object array and keeping the order of equal items.
    * ECMA script standard does not specify the behaviour when items are equal.
    */
    var stableSort = function (arr, sortFunction) {
        var length = arr.length,
        sortValue,
        i;

        // Add index to each item
        for (i = 0; i < length; i++) {
            arr[i].ss_i = i; // stable sort index
        }

        arr.sort(function (a, b) {
            sortValue = sortFunction(a, b);
            return sortValue === 0 ? a.ss_i - b.ss_i : sortValue;
        });

        // Remove index from items
        for (i = 0; i < length; i++) {
            delete arr[i].ss_i; // stable sort index
        }
    }
    
    /**
    * Fix JS round off float errors
    * @param {Number} num
    */
    var correctFloat = function (num) {
        return parseFloat(
            num.toPrecision(14)
            );
    }
    
    /**
    * The time unit lookup
    */
    /*jslint white: true*/
    timeUnits = hash(
        MILLISECOND, 1,
        SECOND, 1000,
        MINUTE, 60000,
        HOUR, 3600000,
        DAY, 24 * 3600000,
        WEEK, 7 * 24 * 3600000,
        MONTH, 31 * 24 * 3600000,
        YEAR, 31556952000
        );

    //-------- Ф-ии определения типа ---------//

    /**
    * Check for string
    * @param {Object} s
    */
    var isString = function (s) {
        return typeof s === 'string';
    }

    /**
    * Check for object
    * @param {Object} obj
    */
    var isObject = function (obj) {
        return typeof obj === 'object';
    }

    /**
    * Check for array
    * @param {Object} obj
    */
    var isArray = function (obj) {
        return Object.prototype.toString.call(obj) === '[object Array]';
    }

    /**
    * Check for number
    * @param {Object} n
    */
    var isNumber = function (n) {
        return typeof n === 'number';
    }

    //-------- Работа с массивами ---------//

    /**
    * Remove last occurence of an item from an array
    * @param {Array} arr
    * @param {Mixed} item
    */
    var erase = function (arr, item) {
        var i = arr.length;
        while (i--) {
            if (arr[i] === item) {
                arr.splice(i, 1);
                break;
            }
        }
    //return arr;
    }
    
    /**
    * Check if an element is an array, and if not, make it into an array. Like
    * MooTools' $.splat.
    */
    var splat = function (obj) {
        return isArray(obj) ? obj : [obj];
    }
    
    /**
    * Non-recursive method to find the lowest member of an array. Math.min raises a maximum
    * call stack size exceeded error in Chrome when trying to apply more than 150.000 points. This
    * method is slightly slower, but safe.
    */
    var arrayMin =function (data) {
        var i = data.length,
        min = data[0];

        while (i--) {
            if (data[i] < min) {
                min = data[i];
            }
        }
        return min;
    }

    /**
    * Non-recursive method to find the lowest member of an array. Math.min raises a maximum
    * call stack size exceeded error in Chrome when trying to apply more than 150.000 points. This
    * method is slightly slower, but safe.
    */
    var arrayMax = function (data) {
        var i = data.length,
        max = data[0];

        while (i--) {
            if (data[i] > max) {
                max = data[i];
            }
        }
        return max;
    }
    
    /**
    * Map an array
    * @param {Array} arr
    * @param {Function} fn
    */
    var map = function (arr, fn) {
        //return jQuery.map(arr, fn);
        var results = [],
        i = 0,
        len = arr.length;
        for (; i < len; i++) {
            results[i] = fn.call(arr[i], arr[i], i, arr);
        }
        return results;
	
    }
    
    // Возвращает массив значений, которые являются результатом передачи
    // каждого элемента массива функции "f"
    var mapArray = function (/* массив */a, /* функция */ f) {
        var r = []; // результаты
        var length = a.length; // На случай, если f изменит свойство length!
        for(var i = 0; i < length; i++) r[i] = f(a[i]);
        return r;
    }
    
    // Передать каждый элемент массива "a" заданной функции проверки.
    // Вернуть массив, хранящий только те элементы, для которых
    // функция проверки вернула значение true
    var filterArray = function filterArray(/* массив */ a, /* функция проверки */ predicate) {
        var results = [];
        var length = a.length; // На случай, если функция проверки изменит свойство length!
        for(var i = 0; i < length; i++) {
            var element = a[i];
            if (predicate(element)) results.push(element);
        }
        return results;
    }
    
    

    //-------- Работа с DOM деревом ---------//

    /**
    * Set or get an attribute or an object of attributes. Can't use jQuery attr because
    * it attempts to set expando properties on the SVG element, which is not allowed.
    *
    * @param {Object} elem The DOM element to receive the attribute(s)
    * @param {String|Object} prop The property or an abject of key-value pairs
    * @param {String} value The value if a single property is set
    */
    var attr = function (elem, prop, value) {
        var key,
        setAttribute = 'setAttribute',
        ret;

        // if the prop is a string
        if (isString(prop)) {
            // set the value
            if (defined(value)) {

                elem[setAttribute](prop, value);

            // get the value
            } else if (elem && elem.getAttribute) { // elem not defined when printing pie demo...
                ret = elem.getAttribute(prop);
            }

        // else if prop is defined, it is a hash of key/value pairs
        } else if (defined(prop) && isObject(prop)) {
            for (key in prop) {
                elem[setAttribute](key, prop[key]);
            }
        }
        return ret;
    }
    

    /**
    * Set CSS on a given element
    * @param {Object} el
    * @param {Object} styles Style object with camel case property names
    */
    var css = function (el, styles) {
        if (isIE) {
            if (styles && styles.opacity !== UNDEFINED) {
                styles.filter = 'alpha(opacity=' + (styles.opacity * 100) + ')';
            }
        }
        extend(el.style, styles);
    }

    /**
    * Utility function to create element with attributes and styles
    * @param {Object} tag
    * @param {Object} attribs
    * @param {Object} styles
    * @param {Object} parent
    * @param {Object} nopad
    */
    var createElement = function (tag, attribs, styles, parent, nopad) {
        var el = doc.createElement(tag);
        if (attribs) {
            extend(el, attribs);
        }
        if (nopad) {
            css(el, {
                padding: 0, 
                border: NONE, 
                margin: 0
            });
        }
        if (styles) {
            css(el, styles);
        }
        if (parent) {
            parent.appendChild(el);
        }
        return el;
    }

    
    //-------- Ф-ии форматирования ---------//
    

    /**
    * Format a number and return a string based on input settings
    * @param {Number} number The input number to format
    * @param {Number} decimals The amount of decimals
    * @param {String} decPoint The decimal point, defaults to the one given in the lang options
    * @param {String} thousandsSep The thousands separator, defaults to the one given in the lang options
    */
    var numberFormat = function (number, decimals, decPoint, thousandsSep) {
        var lang = defaultOptions.lang,
        // http://kevin.vanzonneveld.net/techblog/article/javascript_equivalent_for_phps_number_format/
        n = number,
        c = decimals === -1 ?
        getDecimals(number) :
        (isNaN(decimals = mathAbs(decimals)) ? 2 : decimals),
        d = decPoint === undefined ? lang.decimalPoint : decPoint,
        t = thousandsSep === undefined ? lang.thousandsSep : thousandsSep,
        s = n < 0 ? "-" : "",
        i = String(pInt(n = mathAbs(+n || 0).toFixed(c))),
        j = i.length > 3 ? i.length % 3 : 0;

        return s + (j ? i.substr(0, j) + t : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t) +
        (c ? d + mathAbs(n - i).toFixed(c).slice(2) : "");
    }

    /**
    * Pad a string to a given length by adding 0 to the beginning
    * @param {Number} number
    * @param {Number} length
    */
    var pad = function (number, length) {
        // Create an array of the remaining length +1 and join it with 0's
        return new Array((length || 2) + 1 - String(number).length).join(0) + number;
    }

    /**
    * Based on http://www.php.net/manual/en/function.strftime.php
    * @param {String} format
    * @param {Number} timestamp
    * @param {Boolean} capitalize
    */
    var dateFormat = function (format, timestamp, capitalize) {
        if (!defined(timestamp) || isNaN(timestamp)) {
            return 'Invalid date';
        }
        format = pick(format, '%Y-%m-%d %H:%M:%S');

        var date = new Date(timestamp);
        var key, // used in for constuct below
        // get the basic time values
        hours = date.getHours(),
        day = date.getDay(),
        dayOfMonth = date.getDate(),
        month = date.getMonth(),
        fullYear = date.getFullYear(),
        lang = defaultOptions.lang,
        langWeekdays = lang.weekdays,

        // List all format keys. Custom formats can be added from the outside. 
        // See http://jsfiddle.net/highcharts/7PB5N/ // docs
        replacements = {

            // Day
            'a': langWeekdays[day].substr(0, 3), // Short weekday, like 'Mon'
            'A': langWeekdays[day], // Long weekday, like 'Monday'
            'd': pad(dayOfMonth), // Two digit day of the month, 01 to 31
            'e': dayOfMonth, // Day of the month, 1 through 31

            // Week (none implemented)
            //'W': weekNumber(),

            // Month
            'b': lang.shortMonths[month], // Short month, like 'Jan'
            'B': lang.months[month], // Long month, like 'January'
            'm': pad(month + 1), // Two digit month number, 01 through 12

            // Year
            'y': fullYear.toString().substr(2, 2), // Two digits year, like 09 for 2009
            'Y': fullYear, // Four digits year, like 2009

            // Time
            'H': pad(hours), // Two digits hours in 24h format, 00 through 23
            'I': pad((hours % 12) || 12), // Two digits hours in 12h format, 00 through 11
            'l': (hours % 12) || 12, // Hours in 12h format, 1 through 12
            'M': pad(date.getMinutes()), // Two digits minutes, 00 through 59
            'p': hours < 12 ? 'AM' : 'PM', // Upper case AM or PM
            'P': hours < 12 ? 'am' : 'pm', // Lower case AM or PM
            'S': pad(date.getSeconds()), // Two digits seconds, 00 through  59
            'L': pad(mathRound(timestamp % 1000), 3) // Milliseconds (naming from Ruby)
        };


        // do the replaces
        for (key in replacements) {
            while (format.indexOf('%' + key) !== -1) { // regex would do it in one line, but this is faster
                format = format.replace('%' + key, typeof replacements[key] === 'function' ? replacements[key](timestamp) : replacements[key]);
            }
        }

        // Optionally capitalize the string and return
        return capitalize ? format.substr(0, 1).toUpperCase() + format.substr(1) : format;
    };

    /** 
    * Format a single variable. Similar to sprintf, without the % prefix.
    */
    var formatSingle = function (format, val) {
        var floatRegex = /f$/,
        decRegex = /\.([0-9])/,
        lang = defaultOptions.lang,
        decimals;

        if (floatRegex.test(format)) { // float
            decimals = format.match(decRegex);
            decimals = decimals ? decimals[1] : 6;
            val = numberFormat(
                val,
                decimals,
                lang.decimalPoint,
                format.indexOf(',') > -1 ? lang.thousandsSep : ''
                );
        } else {
            val = dateFormat(format, val);
        }
        return val;
    }

    /**
    * Format a string according to a subset of the rules of Python's String.format method.
    */
    var format = function (str, ctx) {
        var splitter = '{',
        isInside = false,
        segment,
        valueAndFormat,
        path,
        i,
        len,
        ret = [],
        val,
        index;
	
        while ((index = str.indexOf(splitter)) !== -1) {
		
            segment = str.slice(0, index);
            if (isInside) { // we're on the closing bracket looking back
			
                valueAndFormat = segment.split(':');
                path = valueAndFormat.shift().split('.'); // get first and leave format
                len = path.length;
                val = ctx;

                // Assign deeper paths
                for (i = 0; i < len; i++) {
                    val = val[path[i]];
                }

                // Format the replacement
                if (valueAndFormat.length) {
                    val = formatSingle(valueAndFormat.join(':'), val);
                }

                // Push the result and advance the cursor
                ret.push(val);
			
            } else {
                ret.push(segment);
			
            }
            str = str.slice(index + 1); // the rest
            isInside = !isInside; // toggle
            splitter = isInside ? '}' : '{'; // now look for next matching bracket
        }
        ret.push(str);
        return ret.join('');
    }

    
    

    /*****************************************************************************
    * End ordinal axis logic                                                     *
    *****************************************************************************/
    // global variables
    extend(BSA.Utility, {
        
        // Получить/Установить опции
        getOptions: getOptions,
        setOptions: setOptions,
        
        // Обьекты
        getPropertyNames: getPropertyNames,
        copyUndefinedProperties: copyUndefinedProperties,
        getType: getType,
        extend: extend,
        map: map,
        merge: merge,
        defined: defined,
        extendClass: extendClass,
        wrap: wrap,
        destroyObjectProperties: destroyObjectProperties,
        
        // Ф-ии
        pick: pick,
        bindMethod: bindMethod,
        bindArguments: bindArguments,
        
        //Ф-ии кодирования/декодирования 
        base64_encode: base64_encode,
        base64_decode: base64_decode,
        
        // Математика
        pInt: pInt,
        log2lin: log2lin,
        lin2log: lin2log,
        getDecimals: getDecimals,
        stableSort: stableSort,
        correctFloat: correctFloat,
        timeUnits: timeUnits,
        
        // Определение переменных
        isObject: isObject,
        isString: isString,
        isArray: isArray,
        isNumber: isNumber,
        
        // Массивы
        arrayMin: arrayMin,
        arrayMax: arrayMax,
        erase: erase,
        splat: splat,
        map: map,
        mapArray: mapArray,
        filterArray: filterArray,
        
        // Форматирование
        pad: pad,
        dateFormat: dateFormat,
        numberFormat: numberFormat,
        format: format,
        formatSingle: formatSingle,
        
        // Агенты
        userAgent: userAgent,
        isOpera: isOpera,
        isIE: isIE,
        isWebKit: isWebKit,
        isFirefox: isFirefox,
        isTouchDevice: isTouchDevice,
        
        // DOM
        createElement: createElement,
        attr: attr,
        css: css
        
        
        
        
        
        
    });
}());


