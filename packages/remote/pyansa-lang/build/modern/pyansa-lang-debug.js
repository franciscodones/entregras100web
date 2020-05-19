var $jscomp = $jscomp || {};
$jscomp.scope = {};
$jscomp.ASSUME_ES5 = false;
$jscomp.ASSUME_NO_NATIVE_MAP = false;
$jscomp.ASSUME_NO_NATIVE_SET = false;
$jscomp.defineProperty = $jscomp.ASSUME_ES5 || typeof Object.defineProperties == 'function' ? Object.defineProperty : function(target, property, descriptor) {
  descriptor = descriptor;
  if (target == Array.prototype || target == Object.prototype) {
    return;
  }
  target[property] = descriptor.value;
};
$jscomp.getGlobal = function(maybeGlobal) {
  return typeof window != 'undefined' && window === maybeGlobal ? maybeGlobal : typeof global != 'undefined' && global != null ? global : maybeGlobal;
};
$jscomp.global = $jscomp.getGlobal(this);
$jscomp.polyfill = function(target, polyfill, fromLang, toLang) {
  if (!polyfill) {
    return;
  }
  var obj = $jscomp.global;
  var split = target.split('.');
  for (var i = 0; i < split.length - 1; i++) {
    var key = split[i];
    if (!(key in obj)) {
      obj[key] = {};
    }
    obj = obj[key];
  }
  var property = split[split.length - 1];
  var orig = obj[property];
  var impl = polyfill(orig);
  if (impl == orig || impl == null) {
    return;
  }
  $jscomp.defineProperty(obj, property, {configurable:true, writable:true, value:impl});
};
$jscomp.polyfill('Array.prototype.copyWithin', function(orig) {
  if (orig) {
    return orig;
  }
  var polyfill = function(target, start, opt_end) {
    var len = this.length;
    target = Number(target);
    start = Number(start);
    opt_end = Number(opt_end != null ? opt_end : len);
    if (target < start) {
      opt_end = Math.min(opt_end, len);
      while (start < opt_end) {
        if (start in this) {
          this[target++] = this[start++];
        } else {
          delete this[target++];
          start++;
        }
      }
    } else {
      opt_end = Math.min(opt_end, len + start - target);
      target += opt_end - start;
      while (opt_end > start) {
        if (--opt_end in this) {
          this[--target] = this[opt_end];
        } else {
          delete this[target];
        }
      }
    }
    return this;
  };
  return polyfill;
}, 'es6', 'es3');
$jscomp.SYMBOL_PREFIX = 'jscomp_symbol_';
$jscomp.initSymbol = function() {
  $jscomp.initSymbol = function() {
  };
  if (!$jscomp.global['Symbol']) {
    $jscomp.global['Symbol'] = $jscomp.Symbol;
  }
};
$jscomp.Symbol = function() {
  var counter = 0;
  function Symbol(opt_description) {
    return $jscomp.SYMBOL_PREFIX + (opt_description || '') + counter++;
  }
  return Symbol;
}();
$jscomp.initSymbolIterator = function() {
  $jscomp.initSymbol();
  var symbolIterator = $jscomp.global['Symbol'].iterator;
  if (!symbolIterator) {
    symbolIterator = $jscomp.global['Symbol'].iterator = $jscomp.global['Symbol']('iterator');
  }
  if (typeof Array.prototype[symbolIterator] != 'function') {
    $jscomp.defineProperty(Array.prototype, symbolIterator, {configurable:true, writable:true, value:function() {
      return $jscomp.arrayIterator(this);
    }});
  }
  $jscomp.initSymbolIterator = function() {
  };
};
$jscomp.arrayIterator = function(array) {
  var index = 0;
  return $jscomp.iteratorPrototype(function() {
    if (index < array.length) {
      return {done:false, value:array[index++]};
    } else {
      return {done:true};
    }
  });
};
$jscomp.iteratorPrototype = function(next) {
  $jscomp.initSymbolIterator();
  var iterator = {next:next};
  iterator[$jscomp.global['Symbol'].iterator] = function() {
    return this;
  };
  return iterator;
};
$jscomp.iteratorFromArray = function(array, transform) {
  $jscomp.initSymbolIterator();
  if (array instanceof String) {
    array = array + '';
  }
  var i = 0;
  var iter = {next:function() {
    if (i < array.length) {
      var index = i++;
      return {value:transform(index, array[index]), done:false};
    }
    iter.next = function() {
      return {done:true, value:void 0};
    };
    return iter.next();
  }};
  iter[Symbol.iterator] = function() {
    return iter;
  };
  return iter;
};
$jscomp.polyfill('Array.prototype.entries', function(orig) {
  if (orig) {
    return orig;
  }
  var polyfill = function() {
    return $jscomp.iteratorFromArray(this, function(i, v) {
      return [i, v];
    });
  };
  return polyfill;
}, 'es6', 'es3');
$jscomp.polyfill('Array.prototype.fill', function(orig) {
  if (orig) {
    return orig;
  }
  var polyfill = function(value, opt_start, opt_end) {
    var length = this.length || 0;
    if (opt_start < 0) {
      opt_start = Math.max(0, length + opt_start);
    }
    if (opt_end == null || opt_end > length) {
      opt_end = length;
    }
    opt_end = Number(opt_end);
    if (opt_end < 0) {
      opt_end = Math.max(0, length + opt_end);
    }
    for (var i = Number(opt_start || 0); i < opt_end; i++) {
      this[i] = value;
    }
    return this;
  };
  return polyfill;
}, 'es6', 'es3');
$jscomp.findInternal = function(array, callback, thisArg) {
  if (array instanceof String) {
    array = String(array);
  }
  var len = array.length;
  for (var i = 0; i < len; i++) {
    var value = array[i];
    if (callback.call(thisArg, value, i, array)) {
      return {i:i, v:value};
    }
  }
  return {i:-1, v:void 0};
};
$jscomp.polyfill('Array.prototype.find', function(orig) {
  if (orig) {
    return orig;
  }
  var polyfill = function(callback, opt_thisArg) {
    return $jscomp.findInternal(this, callback, opt_thisArg).v;
  };
  return polyfill;
}, 'es6', 'es3');
$jscomp.polyfill('Array.prototype.findIndex', function(orig) {
  if (orig) {
    return orig;
  }
  var polyfill = function(callback, opt_thisArg) {
    return $jscomp.findInternal(this, callback, opt_thisArg).i;
  };
  return polyfill;
}, 'es6', 'es3');
$jscomp.polyfill('Array.from', function(orig) {
  if (orig) {
    return orig;
  }
  var polyfill = function(arrayLike, opt_mapFn, opt_thisArg) {
    $jscomp.initSymbolIterator();
    opt_mapFn = opt_mapFn != null ? opt_mapFn : function(x) {
      return x;
    };
    var result = [];
    var iteratorFunction = arrayLike[Symbol.iterator];
    if (typeof iteratorFunction == 'function') {
      arrayLike = iteratorFunction.call(arrayLike);
      var next;
      while (!(next = arrayLike.next()).done) {
        result.push(opt_mapFn.call(opt_thisArg, next.value));
      }
    } else {
      var len = arrayLike.length;
      for (var i = 0; i < len; i++) {
        result.push(opt_mapFn.call(opt_thisArg, arrayLike[i]));
      }
    }
    return result;
  };
  return polyfill;
}, 'es6', 'es3');
$jscomp.polyfill('Object.is', function(orig) {
  if (orig) {
    return orig;
  }
  var polyfill = function(left, right) {
    if (left === right) {
      return left !== 0 || 1 / left === 1 / right;
    } else {
      return left !== left && right !== right;
    }
  };
  return polyfill;
}, 'es6', 'es3');
$jscomp.polyfill('Array.prototype.includes', function(orig) {
  if (orig) {
    return orig;
  }
  var includes = function(searchElement, opt_fromIndex) {
    var array = this;
    if (array instanceof String) {
      array = String(array);
    }
    var len = array.length;
    for (var i = opt_fromIndex || 0; i < len; i++) {
      if (array[i] == searchElement || Object.is(array[i], searchElement)) {
        return true;
      }
    }
    return false;
  };
  return includes;
}, 'es7', 'es3');
$jscomp.polyfill('Array.prototype.keys', function(orig) {
  if (orig) {
    return orig;
  }
  var polyfill = function() {
    return $jscomp.iteratorFromArray(this, function(i) {
      return i;
    });
  };
  return polyfill;
}, 'es6', 'es3');
$jscomp.polyfill('Array.of', function(orig) {
  if (orig) {
    return orig;
  }
  var polyfill = function(var_args) {
    return Array.from(arguments);
  };
  return polyfill;
}, 'es6', 'es3');
$jscomp.polyfill('Array.prototype.values', function(orig) {
  if (orig) {
    return orig;
  }
  var polyfill = function() {
    return $jscomp.iteratorFromArray(this, function(k, v) {
      return v;
    });
  };
  return polyfill;
}, 'es6', 'es3');
$jscomp.makeIterator = function(iterable) {
  $jscomp.initSymbolIterator();
  var iteratorFunction = iterable[Symbol.iterator];
  return iteratorFunction ? iteratorFunction.call(iterable) : $jscomp.arrayIterator(iterable);
};
$jscomp.FORCE_POLYFILL_PROMISE = false;
$jscomp.polyfill('Promise', function(NativePromise) {
  if (NativePromise && !$jscomp.FORCE_POLYFILL_PROMISE) {
    return NativePromise;
  }
  function AsyncExecutor() {
    this.batch_ = null;
  }
  AsyncExecutor.prototype.asyncExecute = function(f) {
    if (this.batch_ == null) {
      this.batch_ = [];
      this.asyncExecuteBatch_();
    }
    this.batch_.push(f);
    return this;
  };
  AsyncExecutor.prototype.asyncExecuteBatch_ = function() {
    var self = this;
    this.asyncExecuteFunction(function() {
      self.executeBatch_();
    });
  };
  var nativeSetTimeout = $jscomp.global['setTimeout'];
  AsyncExecutor.prototype.asyncExecuteFunction = function(f) {
    nativeSetTimeout(f, 0);
  };
  AsyncExecutor.prototype.executeBatch_ = function() {
    while (this.batch_ && this.batch_.length) {
      var executingBatch = this.batch_;
      this.batch_ = [];
      for (var i = 0; i < executingBatch.length; ++i) {
        var f = executingBatch[i];
        delete executingBatch[i];
        try {
          f();
        } catch (error) {
          this.asyncThrow_(error);
        }
      }
    }
    this.batch_ = null;
  };
  AsyncExecutor.prototype.asyncThrow_ = function(exception) {
    this.asyncExecuteFunction(function() {
      throw exception;
    });
  };
  var PromiseState = {PENDING:0, FULFILLED:1, REJECTED:2};
  var PolyfillPromise = function(executor) {
    this.state_ = PromiseState.PENDING;
    this.result_ = undefined;
    this.onSettledCallbacks_ = [];
    var resolveAndReject = this.createResolveAndReject_();
    try {
      executor(resolveAndReject.resolve, resolveAndReject.reject);
    } catch (e) {
      resolveAndReject.reject(e);
    }
  };
  PolyfillPromise.prototype.createResolveAndReject_ = function() {
    var thisPromise = this;
    var alreadyCalled = false;
    function firstCallWins(method) {
      return function(x) {
        if (!alreadyCalled) {
          alreadyCalled = true;
          method.call(thisPromise, x);
        }
      };
    }
    return {resolve:firstCallWins(this.resolveTo_), reject:firstCallWins(this.reject_)};
  };
  PolyfillPromise.prototype.resolveTo_ = function(value) {
    if (value === this) {
      this.reject_(new TypeError('A Promise cannot resolve to itself'));
    } else {
      if (value instanceof PolyfillPromise) {
        this.settleSameAsPromise_(value);
      } else {
        if (isObject(value)) {
          this.resolveToNonPromiseObj_(value);
        } else {
          this.fulfill_(value);
        }
      }
    }
  };
  PolyfillPromise.prototype.resolveToNonPromiseObj_ = function(obj) {
    var thenMethod = undefined;
    try {
      thenMethod = obj.then;
    } catch (error) {
      this.reject_(error);
      return;
    }
    if (typeof thenMethod == 'function') {
      this.settleSameAsThenable_(thenMethod, obj);
    } else {
      this.fulfill_(obj);
    }
  };
  function isObject(value) {
    switch(typeof value) {
      case 'object':
        return value != null;
      case 'function':
        return true;
      default:
        return false;
    }
  }
  PolyfillPromise.prototype.reject_ = function(reason) {
    this.settle_(PromiseState.REJECTED, reason);
  };
  PolyfillPromise.prototype.fulfill_ = function(value) {
    this.settle_(PromiseState.FULFILLED, value);
  };
  PolyfillPromise.prototype.settle_ = function(settledState, valueOrReason) {
    if (this.state_ != PromiseState.PENDING) {
      throw new Error('Cannot settle(' + settledState + ', ' + valueOrReason | '): Promise already settled in state' + this.state_);
    }
    this.state_ = settledState;
    this.result_ = valueOrReason;
    this.executeOnSettledCallbacks_();
  };
  PolyfillPromise.prototype.executeOnSettledCallbacks_ = function() {
    if (this.onSettledCallbacks_ != null) {
      var callbacks = this.onSettledCallbacks_;
      for (var i = 0; i < callbacks.length; ++i) {
        callbacks[i].call();
        callbacks[i] = null;
      }
      this.onSettledCallbacks_ = null;
    }
  };
  var asyncExecutor = new AsyncExecutor;
  PolyfillPromise.prototype.settleSameAsPromise_ = function(promise) {
    var methods = this.createResolveAndReject_();
    promise.callWhenSettled_(methods.resolve, methods.reject);
  };
  PolyfillPromise.prototype.settleSameAsThenable_ = function(thenMethod, thenable) {
    var methods = this.createResolveAndReject_();
    try {
      thenMethod.call(thenable, methods.resolve, methods.reject);
    } catch (error) {
      methods.reject(error);
    }
  };
  PolyfillPromise.prototype.then = function(onFulfilled, onRejected) {
    var resolveChild;
    var rejectChild;
    var childPromise = new PolyfillPromise(function(resolve, reject) {
      resolveChild = resolve;
      rejectChild = reject;
    });
    function createCallback(paramF, defaultF) {
      if (typeof paramF == 'function') {
        return function(x) {
          try {
            resolveChild(paramF(x));
          } catch (error) {
            rejectChild(error);
          }
        };
      } else {
        return defaultF;
      }
    }
    this.callWhenSettled_(createCallback(onFulfilled, resolveChild), createCallback(onRejected, rejectChild));
    return childPromise;
  };
  PolyfillPromise.prototype['catch'] = function(onRejected) {
    return this.then(undefined, onRejected);
  };
  PolyfillPromise.prototype.callWhenSettled_ = function(onFulfilled, onRejected) {
    var thisPromise = this;
    function callback() {
      switch(thisPromise.state_) {
        case PromiseState.FULFILLED:
          onFulfilled(thisPromise.result_);
          break;
        case PromiseState.REJECTED:
          onRejected(thisPromise.result_);
          break;
        default:
          throw new Error('Unexpected state: ' + thisPromise.state_);
      }
    }
    if (this.onSettledCallbacks_ == null) {
      asyncExecutor.asyncExecute(callback);
    } else {
      this.onSettledCallbacks_.push(function() {
        asyncExecutor.asyncExecute(callback);
      });
    }
  };
  function resolvingPromise(opt_value) {
    if (opt_value instanceof PolyfillPromise) {
      return opt_value;
    } else {
      return new PolyfillPromise(function(resolve, reject) {
        resolve(opt_value);
      });
    }
  }
  PolyfillPromise['resolve'] = resolvingPromise;
  PolyfillPromise['reject'] = function(opt_reason) {
    return new PolyfillPromise(function(resolve, reject) {
      reject(opt_reason);
    });
  };
  PolyfillPromise['race'] = function(thenablesOrValues) {
    return new PolyfillPromise(function(resolve, reject) {
      var iterator = $jscomp.makeIterator(thenablesOrValues);
      for (var iterRec = iterator.next(); !iterRec.done; iterRec = iterator.next()) {
        resolvingPromise(iterRec.value).callWhenSettled_(resolve, reject);
      }
    });
  };
  PolyfillPromise['all'] = function(thenablesOrValues) {
    var iterator = $jscomp.makeIterator(thenablesOrValues);
    var iterRec = iterator.next();
    if (iterRec.done) {
      return resolvingPromise([]);
    } else {
      return new PolyfillPromise(function(resolveAll, rejectAll) {
        var resultsArray = [];
        var unresolvedCount = 0;
        function onFulfilled(i) {
          return function(ithResult) {
            resultsArray[i] = ithResult;
            unresolvedCount--;
            if (unresolvedCount == 0) {
              resolveAll(resultsArray);
            }
          };
        }
        do {
          resultsArray.push(undefined);
          unresolvedCount++;
          resolvingPromise(iterRec.value).callWhenSettled_(onFulfilled(resultsArray.length - 1), rejectAll);
          iterRec = iterator.next();
        } while (!iterRec.done);
      });
    }
  };
  return PolyfillPromise;
}, 'es6', 'es3');
$jscomp.executeAsyncGenerator = function(generator) {
  function passValueToGenerator(value) {
    return generator.next(value);
  }
  function passErrorToGenerator(error) {
    return generator['throw'](error);
  }
  return new Promise(function(resolve, reject) {
    function handleGeneratorRecord(genRec) {
      if (genRec.done) {
        resolve(genRec.value);
      } else {
        Promise.resolve(genRec.value).then(passValueToGenerator, passErrorToGenerator).then(handleGeneratorRecord, reject);
      }
    }
    handleGeneratorRecord(generator.next());
  });
};
$jscomp.owns = function(obj, prop) {
  return Object.prototype.hasOwnProperty.call(obj, prop);
};
$jscomp.polyfill('WeakMap', function(NativeWeakMap) {
  function isConformant() {
    if (!NativeWeakMap || !Object.seal) {
      return false;
    }
    try {
      var x = Object.seal({});
      var y = Object.seal({});
      var map = new NativeWeakMap([[x, 2], [y, 3]]);
      if (map.get(x) != 2 || map.get(y) != 3) {
        return false;
      }
      map['delete'](x);
      map.set(y, 4);
      return !map.has(x) && map.get(y) == 4;
    } catch (err) {
      return false;
    }
  }
  if (isConformant()) {
    return NativeWeakMap;
  }
  var prop = '$jscomp_hidden_' + Math.random().toString().substring(2);
  function insert(target) {
    if (!$jscomp.owns(target, prop)) {
      var obj = {};
      $jscomp.defineProperty(target, prop, {value:obj});
    }
  }
  function patch(name) {
    var prev = Object[name];
    if (prev) {
      Object[name] = function(target) {
        insert(target);
        return prev(target);
      };
    }
  }
  patch('freeze');
  patch('preventExtensions');
  patch('seal');
  var index = 0;
  var PolyfillWeakMap = function(opt_iterable) {
    this.id_ = (index += Math.random() + 1).toString();
    if (opt_iterable) {
      $jscomp.initSymbol();
      $jscomp.initSymbolIterator();
      var iter = $jscomp.makeIterator(opt_iterable);
      var entry;
      while (!(entry = iter.next()).done) {
        var item = entry.value;
        this.set(item[0], item[1]);
      }
    }
  };
  PolyfillWeakMap.prototype.set = function(key, value) {
    insert(key);
    if (!$jscomp.owns(key, prop)) {
      throw new Error('WeakMap key fail: ' + key);
    }
    key[prop][this.id_] = value;
    return this;
  };
  PolyfillWeakMap.prototype.get = function(key) {
    return $jscomp.owns(key, prop) ? key[prop][this.id_] : undefined;
  };
  PolyfillWeakMap.prototype.has = function(key) {
    return $jscomp.owns(key, prop) && $jscomp.owns(key[prop], this.id_);
  };
  PolyfillWeakMap.prototype['delete'] = function(key) {
    if (!$jscomp.owns(key, prop) || !$jscomp.owns(key[prop], this.id_)) {
      return false;
    }
    return delete key[prop][this.id_];
  };
  return PolyfillWeakMap;
}, 'es6', 'es3');
$jscomp.MapEntry = function() {
  this.previous;
  this.next;
  this.head;
  this.key;
  this.value;
};
$jscomp.polyfill('Map', function(NativeMap) {
  var isConformant = !$jscomp.ASSUME_NO_NATIVE_MAP && function() {
    if (!NativeMap || !NativeMap.prototype.entries || typeof Object.seal != 'function') {
      return false;
    }
    try {
      NativeMap = NativeMap;
      var key = Object.seal({x:4});
      var map = new NativeMap($jscomp.makeIterator([[key, 's']]));
      if (map.get(key) != 's' || map.size != 1 || map.get({x:4}) || map.set({x:4}, 't') != map || map.size != 2) {
        return false;
      }
      var iter = map.entries();
      var item = iter.next();
      if (item.done || item.value[0] != key || item.value[1] != 's') {
        return false;
      }
      item = iter.next();
      if (item.done || item.value[0].x != 4 || item.value[1] != 't' || !iter.next().done) {
        return false;
      }
      return true;
    } catch (err) {
      return false;
    }
  }();
  if (isConformant) {
    return NativeMap;
  }
  $jscomp.initSymbol();
  $jscomp.initSymbolIterator();
  var idMap = new WeakMap;
  var PolyfillMap = function(opt_iterable) {
    this.data_ = {};
    this.head_ = createHead();
    this.size = 0;
    if (opt_iterable) {
      var iter = $jscomp.makeIterator(opt_iterable);
      var entry;
      while (!(entry = iter.next()).done) {
        var item = entry.value;
        this.set(item[0], item[1]);
      }
    }
  };
  PolyfillMap.prototype.set = function(key, value) {
    var r = maybeGetEntry(this, key);
    if (!r.list) {
      r.list = this.data_[r.id] = [];
    }
    if (!r.entry) {
      r.entry = {next:this.head_, previous:this.head_.previous, head:this.head_, key:key, value:value};
      r.list.push(r.entry);
      this.head_.previous.next = r.entry;
      this.head_.previous = r.entry;
      this.size++;
    } else {
      r.entry.value = value;
    }
    return this;
  };
  PolyfillMap.prototype['delete'] = function(key) {
    var r = maybeGetEntry(this, key);
    if (r.entry && r.list) {
      r.list.splice(r.index, 1);
      if (!r.list.length) {
        delete this.data_[r.id];
      }
      r.entry.previous.next = r.entry.next;
      r.entry.next.previous = r.entry.previous;
      r.entry.head = null;
      this.size--;
      return true;
    }
    return false;
  };
  PolyfillMap.prototype.clear = function() {
    this.data_ = {};
    this.head_ = this.head_.previous = createHead();
    this.size = 0;
  };
  PolyfillMap.prototype.has = function(key) {
    return !!maybeGetEntry(this, key).entry;
  };
  PolyfillMap.prototype.get = function(key) {
    var entry = maybeGetEntry(this, key).entry;
    return entry && entry.value;
  };
  PolyfillMap.prototype.entries = function() {
    return makeIterator(this, function(entry) {
      return [entry.key, entry.value];
    });
  };
  PolyfillMap.prototype.keys = function() {
    return makeIterator(this, function(entry) {
      return entry.key;
    });
  };
  PolyfillMap.prototype.values = function() {
    return makeIterator(this, function(entry) {
      return entry.value;
    });
  };
  PolyfillMap.prototype.forEach = function(callback, opt_thisArg) {
    var iter = this.entries();
    var item;
    while (!(item = iter.next()).done) {
      var entry = item.value;
      callback.call(opt_thisArg, entry[1], entry[0], this);
    }
  };
  PolyfillMap.prototype[Symbol.iterator] = PolyfillMap.prototype.entries;
  var maybeGetEntry = function(map, key) {
    var id = getId(key);
    var list = map.data_[id];
    if (list && $jscomp.owns(map.data_, id)) {
      for (var index = 0; index < list.length; index++) {
        var entry = list[index];
        if (key !== key && entry.key !== entry.key || key === entry.key) {
          return {id:id, list:list, index:index, entry:entry};
        }
      }
    }
    return {id:id, list:list, index:-1, entry:undefined};
  };
  var makeIterator = function(map, func) {
    var entry = map.head_;
    return $jscomp.iteratorPrototype(function() {
      if (entry) {
        while (entry.head != map.head_) {
          entry = entry.previous;
        }
        while (entry.next != entry.head) {
          entry = entry.next;
          return {done:false, value:func(entry)};
        }
        entry = null;
      }
      return {done:true, value:void 0};
    });
  };
  var createHead = function() {
    var head = {};
    head.previous = head.next = head.head = head;
    return head;
  };
  var mapIndex = 0;
  var getId = function(obj) {
    var type = obj && typeof obj;
    if (type == 'object' || type == 'function') {
      obj = obj;
      if (!idMap.has(obj)) {
        var id = '' + ++mapIndex;
        idMap.set(obj, id);
        return id;
      }
      return idMap.get(obj);
    }
    return 'p_' + obj;
  };
  return PolyfillMap;
}, 'es6', 'es3');
$jscomp.polyfill('Math.acosh', function(orig) {
  if (orig) {
    return orig;
  }
  var polyfill = function(x) {
    x = Number(x);
    return Math.log(x + Math.sqrt(x * x - 1));
  };
  return polyfill;
}, 'es6', 'es3');
$jscomp.polyfill('Math.asinh', function(orig) {
  if (orig) {
    return orig;
  }
  var polyfill = function(x) {
    x = Number(x);
    if (x === 0) {
      return x;
    }
    var y = Math.log(Math.abs(x) + Math.sqrt(x * x + 1));
    return x < 0 ? -y : y;
  };
  return polyfill;
}, 'es6', 'es3');
$jscomp.polyfill('Math.log1p', function(orig) {
  if (orig) {
    return orig;
  }
  var polyfill = function(x) {
    x = Number(x);
    if (x < 0.25 && x > -0.25) {
      var y = x;
      var d = 1;
      var z = x;
      var zPrev = 0;
      var s = 1;
      while (zPrev != z) {
        y *= x;
        s *= -1;
        z = (zPrev = z) + s * y / ++d;
      }
      return z;
    }
    return Math.log(1 + x);
  };
  return polyfill;
}, 'es6', 'es3');
$jscomp.polyfill('Math.atanh', function(orig) {
  if (orig) {
    return orig;
  }
  var log1p = Math.log1p;
  var polyfill = function(x) {
    x = Number(x);
    return (log1p(x) - log1p(-x)) / 2;
  };
  return polyfill;
}, 'es6', 'es3');
$jscomp.polyfill('Math.cbrt', function(orig) {
  if (orig) {
    return orig;
  }
  var polyfill = function(x) {
    if (x === 0) {
      return x;
    }
    x = Number(x);
    var y = Math.pow(Math.abs(x), 1 / 3);
    return x < 0 ? -y : y;
  };
  return polyfill;
}, 'es6', 'es3');
$jscomp.polyfill('Math.clz32', function(orig) {
  if (orig) {
    return orig;
  }
  var polyfill = function(x) {
    x = Number(x) >>> 0;
    if (x === 0) {
      return 32;
    }
    var result = 0;
    if ((x & 4294901760) === 0) {
      x <<= 16;
      result += 16;
    }
    if ((x & 4278190080) === 0) {
      x <<= 8;
      result += 8;
    }
    if ((x & 4026531840) === 0) {
      x <<= 4;
      result += 4;
    }
    if ((x & 3221225472) === 0) {
      x <<= 2;
      result += 2;
    }
    if ((x & 2147483648) === 0) {
      result++;
    }
    return result;
  };
  return polyfill;
}, 'es6', 'es3');
$jscomp.polyfill('Math.cosh', function(orig) {
  if (orig) {
    return orig;
  }
  var exp = Math.exp;
  var polyfill = function(x) {
    x = Number(x);
    return (exp(x) + exp(-x)) / 2;
  };
  return polyfill;
}, 'es6', 'es3');
$jscomp.polyfill('Math.expm1', function(orig) {
  if (orig) {
    return orig;
  }
  var polyfill = function(x) {
    x = Number(x);
    if (x < .25 && x > -.25) {
      var y = x;
      var d = 1;
      var z = x;
      var zPrev = 0;
      while (zPrev != z) {
        y *= x / ++d;
        z = (zPrev = z) + y;
      }
      return z;
    }
    return Math.exp(x) - 1;
  };
  return polyfill;
}, 'es6', 'es3');
$jscomp.polyfill('Math.hypot', function(orig) {
  if (orig) {
    return orig;
  }
  var polyfill = function(x, y, var_args) {
    x = Number(x);
    y = Number(y);
    var i, z, sum;
    var max = Math.max(Math.abs(x), Math.abs(y));
    for (i = 2; i < arguments.length; i++) {
      max = Math.max(max, Math.abs(arguments[i]));
    }
    if (max > 1e100 || max < 1e-100) {
      x = x / max;
      y = y / max;
      sum = x * x + y * y;
      for (i = 2; i < arguments.length; i++) {
        z = Number(arguments[i]) / max;
        sum += z * z;
      }
      return Math.sqrt(sum) * max;
    } else {
      sum = x * x + y * y;
      for (i = 2; i < arguments.length; i++) {
        z = Number(arguments[i]);
        sum += z * z;
      }
      return Math.sqrt(sum);
    }
  };
  return polyfill;
}, 'es6', 'es3');
$jscomp.polyfill('Math.imul', function(orig) {
  if (orig) {
    return orig;
  }
  var polyfill = function(a, b) {
    a = Number(a);
    b = Number(b);
    var ah = a >>> 16 & 65535;
    var al = a & 65535;
    var bh = b >>> 16 & 65535;
    var bl = b & 65535;
    var lh = ah * bl + al * bh << 16 >>> 0;
    return al * bl + lh | 0;
  };
  return polyfill;
}, 'es6', 'es3');
$jscomp.polyfill('Math.log10', function(orig) {
  if (orig) {
    return orig;
  }
  var polyfill = function(x) {
    return Math.log(x) / Math.LN10;
  };
  return polyfill;
}, 'es6', 'es3');
$jscomp.polyfill('Math.log2', function(orig) {
  if (orig) {
    return orig;
  }
  var polyfill = function(x) {
    return Math.log(x) / Math.LN2;
  };
  return polyfill;
}, 'es6', 'es3');
$jscomp.polyfill('Math.sign', function(orig) {
  if (orig) {
    return orig;
  }
  var polyfill = function(x) {
    x = Number(x);
    return x === 0 || isNaN(x) ? x : x > 0 ? 1 : -1;
  };
  return polyfill;
}, 'es6', 'es3');
$jscomp.polyfill('Math.sinh', function(orig) {
  if (orig) {
    return orig;
  }
  var exp = Math.exp;
  var polyfill = function(x) {
    x = Number(x);
    if (x === 0) {
      return x;
    }
    return (exp(x) - exp(-x)) / 2;
  };
  return polyfill;
}, 'es6', 'es3');
$jscomp.polyfill('Math.tanh', function(orig) {
  if (orig) {
    return orig;
  }
  var polyfill = function(x) {
    x = Number(x);
    if (x === 0) {
      return x;
    }
    var y = Math.exp(-2 * Math.abs(x));
    var z = (1 - y) / (1 + y);
    return x < 0 ? -z : z;
  };
  return polyfill;
}, 'es6', 'es3');
$jscomp.polyfill('Math.trunc', function(orig) {
  if (orig) {
    return orig;
  }
  var polyfill = function(x) {
    x = Number(x);
    if (isNaN(x) || x === Infinity || x === -Infinity || x === 0) {
      return x;
    }
    var y = Math.floor(Math.abs(x));
    return x < 0 ? -y : y;
  };
  return polyfill;
}, 'es6', 'es3');
$jscomp.polyfill('Number.EPSILON', function(orig) {
  return Math.pow(2, -52);
}, 'es6', 'es3');
$jscomp.polyfill('Number.MAX_SAFE_INTEGER', function() {
  return 9007199254740991;
}, 'es6', 'es3');
$jscomp.polyfill('Number.MIN_SAFE_INTEGER', function() {
  return -9007199254740991;
}, 'es6', 'es3');
$jscomp.polyfill('Number.isFinite', function(orig) {
  if (orig) {
    return orig;
  }
  var polyfill = function(x) {
    if (typeof x !== 'number') {
      return false;
    }
    return !isNaN(x) && x !== Infinity && x !== -Infinity;
  };
  return polyfill;
}, 'es6', 'es3');
$jscomp.polyfill('Number.isInteger', function(orig) {
  if (orig) {
    return orig;
  }
  var polyfill = function(x) {
    if (!Number.isFinite(x)) {
      return false;
    }
    return x === Math.floor(x);
  };
  return polyfill;
}, 'es6', 'es3');
$jscomp.polyfill('Number.isNaN', function(orig) {
  if (orig) {
    return orig;
  }
  var polyfill = function(x) {
    return typeof x === 'number' && isNaN(x);
  };
  return polyfill;
}, 'es6', 'es3');
$jscomp.polyfill('Number.isSafeInteger', function(orig) {
  if (orig) {
    return orig;
  }
  var polyfill = function(x) {
    return Number.isInteger(x) && Math.abs(x) <= Number.MAX_SAFE_INTEGER;
  };
  return polyfill;
}, 'es6', 'es3');
$jscomp.polyfill('Object.assign', function(orig) {
  if (orig) {
    return orig;
  }
  var polyfill = function(target, var_args) {
    for (var i = 1; i < arguments.length; i++) {
      var source = arguments[i];
      if (!source) {
        continue;
      }
      for (var key in source) {
        if ($jscomp.owns(source, key)) {
          target[key] = source[key];
        }
      }
    }
    return target;
  };
  return polyfill;
}, 'es6', 'es3');
$jscomp.polyfill('Object.entries', function(orig) {
  if (orig) {
    return orig;
  }
  var entries = function(obj) {
    var result = [];
    for (var key in obj) {
      if ($jscomp.owns(obj, key)) {
        result.push([key, obj[key]]);
      }
    }
    return result;
  };
  return entries;
}, 'es8', 'es3');
$jscomp.polyfill('Object.getOwnPropertySymbols', function(orig) {
  if (orig) {
    return orig;
  }
  return function() {
    return [];
  };
}, 'es6', 'es5');
$jscomp.polyfill('Reflect.ownKeys', function(orig) {
  if (orig) {
    return orig;
  }
  var symbolPrefix = 'jscomp_symbol_';
  function isSymbol(key) {
    return key.substring(0, symbolPrefix.length) == symbolPrefix;
  }
  var polyfill = function(target) {
    var keys = [];
    var names = Object.getOwnPropertyNames(target);
    var symbols = Object.getOwnPropertySymbols(target);
    for (var i = 0; i < names.length; i++) {
      (isSymbol(names[i]) ? symbols : keys).push(names[i]);
    }
    return keys.concat(symbols);
  };
  return polyfill;
}, 'es6', 'es5');
$jscomp.polyfill('Object.getOwnPropertyDescriptors', function(orig) {
  if (orig) {
    return orig;
  }
  var getOwnPropertyDescriptors = function(obj) {
    var result = {};
    var keys = Reflect.ownKeys(obj);
    for (var i = 0; i < keys.length; i++) {
      result[keys[i]] = Object.getOwnPropertyDescriptor(obj, keys[i]);
    }
    return result;
  };
  return getOwnPropertyDescriptors;
}, 'es8', 'es5');
$jscomp.underscoreProtoCanBeSet = function() {
  var x = {a:true};
  var y = {};
  try {
    y.__proto__ = x;
    return y.a;
  } catch (e) {
  }
  return false;
};
$jscomp.setPrototypeOf = typeof Object.setPrototypeOf == 'function' ? Object.setPrototypeOf : $jscomp.underscoreProtoCanBeSet() ? function(target, proto) {
  target.__proto__ = proto;
  if (target.__proto__ !== proto) {
    throw new TypeError(target + ' is not extensible');
  }
  return target;
} : null;
$jscomp.polyfill('Object.setPrototypeOf', function(orig) {
  return orig || $jscomp.setPrototypeOf;
}, 'es6', 'es5');
$jscomp.polyfill('Object.values', function(orig) {
  if (orig) {
    return orig;
  }
  var values = function(obj) {
    var result = [];
    for (var key in obj) {
      if ($jscomp.owns(obj, key)) {
        result.push(obj[key]);
      }
    }
    return result;
  };
  return values;
}, 'es8', 'es3');
$jscomp.polyfill('Reflect.apply', function(orig) {
  if (orig) {
    return orig;
  }
  var apply = Function.prototype.apply;
  var polyfill = function(target, thisArg, argList) {
    return apply.call(target, thisArg, argList);
  };
  return polyfill;
}, 'es6', 'es3');
$jscomp.objectCreate = $jscomp.ASSUME_ES5 || typeof Object.create == 'function' ? Object.create : function(prototype) {
  var ctor = function() {
  };
  ctor.prototype = prototype;
  return new ctor;
};
$jscomp.construct = function() {
  function reflectConstructWorks() {
    function Base() {
    }
    function Derived() {
    }
    new Base;
    Reflect.construct(Base, [], Derived);
    return new Base instanceof Base;
  }
  if (typeof Reflect != 'undefined' && Reflect.construct) {
    if (reflectConstructWorks()) {
      return Reflect.construct;
    }
    var brokenConstruct = Reflect.construct;
    var patchedConstruct = function(target, argList, opt_newTarget) {
      var out = brokenConstruct(target, argList);
      if (opt_newTarget) {
        Reflect.setPrototypeOf(out, opt_newTarget.prototype);
      }
      return out;
    };
    return patchedConstruct;
  }
  function construct(target, argList, opt_newTarget) {
    if (opt_newTarget === undefined) {
      opt_newTarget = target;
    }
    var proto = opt_newTarget.prototype || Object.prototype;
    var obj = $jscomp.objectCreate(proto);
    var apply = Function.prototype.apply;
    var out = apply.call(target, obj, argList);
    return out || obj;
  }
  return construct;
}();
$jscomp.polyfill('Reflect.construct', function(orig) {
  return $jscomp.construct;
}, 'es6', 'es3');
$jscomp.polyfill('Reflect.defineProperty', function(orig) {
  if (orig) {
    return orig;
  }
  var polyfill = function(target, propertyKey, attributes) {
    try {
      Object.defineProperty(target, propertyKey, attributes);
      var desc = Object.getOwnPropertyDescriptor(target, propertyKey);
      if (!desc) {
        return false;
      }
      return desc.configurable === (attributes.configurable || false) && desc.enumerable === (attributes.enumerable || false) && ('value' in desc ? desc.value === attributes.value && desc.writable === (attributes.writable || false) : desc.get === attributes.get && desc.set === attributes.set);
    } catch (err) {
      return false;
    }
  };
  return polyfill;
}, 'es6', 'es5');
$jscomp.polyfill('Reflect.deleteProperty', function(orig) {
  if (orig) {
    return orig;
  }
  var polyfill = function(target, propertyKey) {
    if (!$jscomp.owns(target, propertyKey)) {
      return true;
    }
    try {
      return delete target[propertyKey];
    } catch (err) {
      return false;
    }
  };
  return polyfill;
}, 'es6', 'es3');
$jscomp.polyfill('Reflect.getOwnPropertyDescriptor', function(orig) {
  return orig || Object.getOwnPropertyDescriptor;
}, 'es6', 'es5');
$jscomp.polyfill('Reflect.getPrototypeOf', function(orig) {
  return orig || Object.getPrototypeOf;
}, 'es6', 'es5');
$jscomp.findDescriptor = function(target, propertyKey) {
  var obj = target;
  while (obj) {
    var property = Reflect.getOwnPropertyDescriptor(obj, propertyKey);
    if (property) {
      return property;
    }
    obj = Reflect.getPrototypeOf(obj);
  }
  return undefined;
};
$jscomp.polyfill('Reflect.get', function(orig) {
  if (orig) {
    return orig;
  }
  var polyfill = function(target, propertyKey, opt_receiver) {
    if (arguments.length <= 2) {
      return target[propertyKey];
    }
    var property = $jscomp.findDescriptor(target, propertyKey);
    if (property) {
      return property.get ? property.get.call(opt_receiver) : property.value;
    }
    return undefined;
  };
  return polyfill;
}, 'es6', 'es5');
$jscomp.polyfill('Reflect.has', function(orig) {
  if (orig) {
    return orig;
  }
  var polyfill = function(target, propertyKey) {
    return propertyKey in target;
  };
  return polyfill;
}, 'es6', 'es3');
$jscomp.polyfill('Reflect.isExtensible', function(orig) {
  if (orig) {
    return orig;
  }
  if ($jscomp.ASSUME_ES5 || typeof Object.isExtensible == 'function') {
    return Object.isExtensible;
  }
  return function() {
    return true;
  };
}, 'es6', 'es3');
$jscomp.polyfill('Reflect.preventExtensions', function(orig) {
  if (orig) {
    return orig;
  }
  if (!($jscomp.ASSUME_ES5 || typeof Object.preventExtensions == 'function')) {
    return function() {
      return false;
    };
  }
  var polyfill = function(target) {
    Object.preventExtensions(target);
    return !Object.isExtensible(target);
  };
  return polyfill;
}, 'es6', 'es3');
$jscomp.polyfill('Reflect.set', function(orig) {
  if (orig) {
    return orig;
  }
  var polyfill = function(target, propertyKey, value, opt_receiver) {
    var property = $jscomp.findDescriptor(target, propertyKey);
    if (!property) {
      if (Reflect.isExtensible(target)) {
        target[propertyKey] = value;
        return true;
      }
      return false;
    }
    if (property.set) {
      property.set.call(arguments.length > 3 ? opt_receiver : target, value);
      return true;
    } else {
      if (property.writable && !Object.isFrozen(target)) {
        target[propertyKey] = value;
        return true;
      }
    }
    return false;
  };
  return polyfill;
}, 'es6', 'es5');
$jscomp.polyfill('Reflect.setPrototypeOf', function(orig) {
  if (orig) {
    return orig;
  } else {
    if ($jscomp.setPrototypeOf) {
      var setPrototypeOf = $jscomp.setPrototypeOf;
      var polyfill = function(target, proto) {
        try {
          setPrototypeOf(target, proto);
          return true;
        } catch (e) {
          return false;
        }
      };
      return polyfill;
    } else {
      return null;
    }
  }
}, 'es6', 'es5');
$jscomp.polyfill('Set', function(NativeSet) {
  var isConformant = !$jscomp.ASSUME_NO_NATIVE_SET && function() {
    if (!NativeSet || !NativeSet.prototype.entries || typeof Object.seal != 'function') {
      return false;
    }
    try {
      NativeSet = NativeSet;
      var value = Object.seal({x:4});
      var set = new NativeSet($jscomp.makeIterator([value]));
      if (!set.has(value) || set.size != 1 || set.add(value) != set || set.size != 1 || set.add({x:4}) != set || set.size != 2) {
        return false;
      }
      var iter = set.entries();
      var item = iter.next();
      if (item.done || item.value[0] != value || item.value[1] != value) {
        return false;
      }
      item = iter.next();
      if (item.done || item.value[0] == value || item.value[0].x != 4 || item.value[1] != item.value[0]) {
        return false;
      }
      return iter.next().done;
    } catch (err) {
      return false;
    }
  }();
  if (isConformant) {
    return NativeSet;
  }
  $jscomp.initSymbol();
  $jscomp.initSymbolIterator();
  var PolyfillSet = function(opt_iterable) {
    this.map_ = new Map;
    if (opt_iterable) {
      var iter = $jscomp.makeIterator(opt_iterable);
      var entry;
      while (!(entry = iter.next()).done) {
        var item = entry.value;
        this.add(item);
      }
    }
    this.size = this.map_.size;
  };
  PolyfillSet.prototype.add = function(value) {
    this.map_.set(value, value);
    this.size = this.map_.size;
    return this;
  };
  PolyfillSet.prototype['delete'] = function(value) {
    var result = this.map_['delete'](value);
    this.size = this.map_.size;
    return result;
  };
  PolyfillSet.prototype.clear = function() {
    this.map_.clear();
    this.size = 0;
  };
  PolyfillSet.prototype.has = function(value) {
    return this.map_.has(value);
  };
  PolyfillSet.prototype.entries = function() {
    return this.map_.entries();
  };
  PolyfillSet.prototype.values = function() {
    return this.map_.values();
  };
  PolyfillSet.prototype.keys = PolyfillSet.prototype.values;
  PolyfillSet.prototype[Symbol.iterator] = PolyfillSet.prototype.values;
  PolyfillSet.prototype.forEach = function(callback, opt_thisArg) {
    var set = this;
    this.map_.forEach(function(value) {
      return callback.call(opt_thisArg, value, value, set);
    });
  };
  return PolyfillSet;
}, 'es6', 'es3');
$jscomp.checkStringArgs = function(thisArg, arg, func) {
  if (thisArg == null) {
    throw new TypeError("The 'this' value for String.prototype." + func + ' must not be null or undefined');
  }
  if (arg instanceof RegExp) {
    throw new TypeError('First argument to String.prototype.' + func + ' must not be a regular expression');
  }
  return thisArg + '';
};
$jscomp.polyfill('String.prototype.codePointAt', function(orig) {
  if (orig) {
    return orig;
  }
  var polyfill = function(position) {
    var string = $jscomp.checkStringArgs(this, null, 'codePointAt');
    var size = string.length;
    position = Number(position) || 0;
    if (!(position >= 0 && position < size)) {
      return void 0;
    }
    position = position | 0;
    var first = string.charCodeAt(position);
    if (first < 55296 || first > 56319 || position + 1 === size) {
      return first;
    }
    var second = string.charCodeAt(position + 1);
    if (second < 56320 || second > 57343) {
      return first;
    }
    return (first - 55296) * 1024 + second + 9216;
  };
  return polyfill;
}, 'es6', 'es3');
$jscomp.polyfill('String.prototype.endsWith', function(orig) {
  if (orig) {
    return orig;
  }
  var polyfill = function(searchString, opt_position) {
    var string = $jscomp.checkStringArgs(this, searchString, 'endsWith');
    searchString = searchString + '';
    if (opt_position === void 0) {
      opt_position = string.length;
    }
    var i = Math.max(0, Math.min(opt_position | 0, string.length));
    var j = searchString.length;
    while (j > 0 && i > 0) {
      if (string[--i] != searchString[--j]) {
        return false;
      }
    }
    return j <= 0;
  };
  return polyfill;
}, 'es6', 'es3');
$jscomp.polyfill('String.fromCodePoint', function(orig) {
  if (orig) {
    return orig;
  }
  var polyfill = function(var_args) {
    var result = '';
    for (var i = 0; i < arguments.length; i++) {
      var code = Number(arguments[i]);
      if (code < 0 || code > 1114111 || code !== Math.floor(code)) {
        throw new RangeError('invalid_code_point ' + code);
      }
      if (code <= 65535) {
        result += String.fromCharCode(code);
      } else {
        code -= 65536;
        result += String.fromCharCode(code >>> 10 & 1023 | 55296);
        result += String.fromCharCode(code & 1023 | 56320);
      }
    }
    return result;
  };
  return polyfill;
}, 'es6', 'es3');
$jscomp.polyfill('String.prototype.includes', function(orig) {
  if (orig) {
    return orig;
  }
  var polyfill = function(searchString, opt_position) {
    var string = $jscomp.checkStringArgs(this, searchString, 'includes');
    return string.indexOf(searchString, opt_position || 0) !== -1;
  };
  return polyfill;
}, 'es6', 'es3');
$jscomp.polyfill('String.prototype.repeat', function(orig) {
  if (orig) {
    return orig;
  }
  var polyfill = function(copies) {
    var string = $jscomp.checkStringArgs(this, null, 'repeat');
    if (copies < 0 || copies > 1342177279) {
      throw new RangeError('Invalid count value');
    }
    copies = copies | 0;
    var result = '';
    while (copies) {
      if (copies & 1) {
        result += string;
      }
      if (copies >>>= 1) {
        string += string;
      }
    }
    return result;
  };
  return polyfill;
}, 'es6', 'es3');
$jscomp.stringPadding = function(padString, padLength) {
  var padding = padString !== undefined ? String(padString) : ' ';
  if (!(padLength > 0) || !padding) {
    return '';
  }
  var repeats = Math.ceil(padLength / padding.length);
  return padding.repeat(repeats).substring(0, padLength);
};
$jscomp.polyfill('String.prototype.padEnd', function(orig) {
  if (orig) {
    return orig;
  }
  var padEnd = function(targetLength, opt_padString) {
    var string = $jscomp.checkStringArgs(this, null, 'padStart');
    var padLength = targetLength - string.length;
    return string + $jscomp.stringPadding(opt_padString, padLength);
  };
  return padEnd;
}, 'es8', 'es3');
$jscomp.polyfill('String.prototype.padStart', function(orig) {
  if (orig) {
    return orig;
  }
  var padStart = function(targetLength, opt_padString) {
    var string = $jscomp.checkStringArgs(this, null, 'padStart');
    var padLength = targetLength - string.length;
    return $jscomp.stringPadding(opt_padString, padLength) + string;
  };
  return padStart;
}, 'es8', 'es3');
$jscomp.polyfill('String.prototype.startsWith', function(orig) {
  if (orig) {
    return orig;
  }
  var polyfill = function(searchString, opt_position) {
    var string = $jscomp.checkStringArgs(this, searchString, 'startsWith');
    searchString = searchString + '';
    var strLen = string.length;
    var searchLen = searchString.length;
    var i = Math.max(0, Math.min(opt_position | 0, string.length));
    var j = 0;
    while (j < searchLen && i < strLen) {
      if (string[i++] != searchString[j++]) {
        return false;
      }
    }
    return j >= searchLen;
  };
  return polyfill;
}, 'es6', 'es3');
$jscomp.arrayFromIterator = function(iterator) {
  var i;
  var arr = [];
  while (!(i = iterator.next()).done) {
    arr.push(i.value);
  }
  return arr;
};
$jscomp.arrayFromIterable = function(iterable) {
  if (iterable instanceof Array) {
    return iterable;
  } else {
    return $jscomp.arrayFromIterator($jscomp.makeIterator(iterable));
  }
};
$jscomp.inherits = function(childCtor, parentCtor) {
  childCtor.prototype = $jscomp.objectCreate(parentCtor.prototype);
  childCtor.prototype.constructor = childCtor;
  if ($jscomp.setPrototypeOf) {
    var setPrototypeOf = $jscomp.setPrototypeOf;
    setPrototypeOf(childCtor, parentCtor);
  } else {
    for (var p in parentCtor) {
      if (p == 'prototype') {
        continue;
      }
      if (Object.defineProperties) {
        var descriptor = Object.getOwnPropertyDescriptor(parentCtor, p);
        if (descriptor) {
          Object.defineProperty(childCtor, p, descriptor);
        }
      } else {
        childCtor[p] = parentCtor[p];
      }
    }
  }
  childCtor.superClass_ = parentCtor.prototype;
};
$jscomp.polyfill('WeakSet', function(NativeWeakSet) {
  function isConformant() {
    if (!NativeWeakSet || !Object.seal) {
      return false;
    }
    try {
      var x = Object.seal({});
      var y = Object.seal({});
      var set = new NativeWeakSet([x]);
      if (!set.has(x) || set.has(y)) {
        return false;
      }
      set['delete'](x);
      set.add(y);
      return !set.has(x) && set.has(y);
    } catch (err) {
      return false;
    }
  }
  if (isConformant()) {
    return NativeWeakSet;
  }
  var PolyfillWeakSet = function(opt_iterable) {
    this.map_ = new WeakMap;
    if (opt_iterable) {
      $jscomp.initSymbol();
      $jscomp.initSymbolIterator();
      var iter = $jscomp.makeIterator(opt_iterable);
      var entry;
      while (!(entry = iter.next()).done) {
        var item = entry.value;
        this.add(item);
      }
    }
  };
  PolyfillWeakSet.prototype.add = function(elem) {
    this.map_.set(elem, true);
    return this;
  };
  PolyfillWeakSet.prototype.has = function(elem) {
    return this.map_.has(elem);
  };
  PolyfillWeakSet.prototype['delete'] = function(elem) {
    return this.map_['delete'](elem);
  };
  return PolyfillWeakSet;
}, 'es6', 'es3');
try {
  if (Array.prototype.values.toString().indexOf('[native code]') == -1) {
    delete Array.prototype.values;
  }
} catch (e) {
}
Ext.define('Pyansa.String', {}, function() {
  Pyansa.String = {rightPad:function(value, size, character) {
    var result = String(value);
    character = character || ' ';
    while (result.length < size) {
      result = result + character;
    }
    return result;
  }, random:function(length, word) {
    var str = '', i;
    word = word || 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    for (i = 0; i < length; i++) {
      str += word.charAt(Ext.Number.randomInt(0, word.length - 1));
    }
    return str;
  }, toCamelCase:function(value, capitalize) {
    var words = value.split(/[_.\- ]+/), camelString = '', i;
    capitalize = capitalize || false;
    for (i = 0; i < words.length; i++) {
      if (i == 0) {
        if (capitalize) {
          camelString += words[i].charAt(0).toUpperCase();
          camelString += words[i].substr(1);
        } else {
          camelString += words[i];
        }
      } else {
        camelString += words[i].charAt(0).toUpperCase();
        camelString += words[i].substr(1);
      }
    }
    return camelString;
  }, chunk:function(value, length, reverse) {
    var pieces = [], substr = '';
    length = length || 1;
    reverse = reverse || false;
    while (value != '') {
      if (reverse) {
        substr = value.substr(-length);
        value = value.substring(0, value.length - length);
        pieces.unshift(substr);
      } else {
        substr = value.substr(0, length);
        value = value.substr(length);
        pieces.push(substr);
      }
    }
    return pieces;
  }, splitParagraphs:function(value, length, word) {
    var paragraphs = [], p = '', words;
    word = word || true;
    if (!word) {
      return this.chunk(value.trim(), length);
    }
    words = Ext.String.splitWords(value);
    while (words.length > 0) {
      if (p.length + words[0].length + 1 > length) {
        paragraphs.push(p);
        p = '';
      }
      if (p == '') {
        p = words[0];
      } else {
        if (p != '') {
          p = p + ' ' + words[0];
        }
      }
      words = words.slice(1);
      if (words.length == 0) {
        paragraphs.push(p);
      }
    }
    return paragraphs;
  }};
});
Ext.define('Pyansa.overrides.String', {override:'Ext.String', requires:['Pyansa.String']}, function() {
  var prop;
  for (prop in Pyansa.String) {
    Ext.String[prop] = Pyansa.String[prop];
  }
});
Ext.define('Pyansa.Math', {}, function() {
  var prop;
  Pyansa.Math = {trunc:Math.trunc || function(x) {
    return x < 0 ? Math.ceil(x) : Math.floor(x);
  }};
  for (prop in Pyansa.Math) {
    Math[prop] = Pyansa.Math[prop];
  }
});
Ext.define('Pyansa.Number', {requires:['Pyansa.Math']}, function() {
  Pyansa.Number = {decimalAdjust:function(value, n, type) {
    n = n || 0;
    type = type || 'round';
    value = +value;
    value = Ext.Number.correctFloat(value);
    n = +n;
    if (isNaN(value) || !(typeof n === 'number' && n % 1 === 0 && n >= 0)) {
      return NaN;
    }
    value = value.toString().split('e');
    value = Math[type](+(value[0] + 'e' + ((value[1] ? +value[1] : 0) + n)));
    value = value.toString().split('e');
    return +(value[0] + 'e' + ((value[1] ? +value[1] : 0) - n));
  }, trunc:function(value, n) {
    return this.decimalAdjust(value, n, 'trunc');
  }, round:function(value, n) {
    return this.decimalAdjust(value, n, 'round');
  }, ceil:function(value, n) {
    return this.decimalAdjust(value, n, 'ceil');
  }, floor:function(value, n) {
    return this.decimalAdjust(value, n, 'floor');
  }};
});
Ext.define('Pyansa.overrides.Number', {override:'Ext.Number', requires:['Pyansa.Number']}, function() {
  var prop;
  for (prop in Pyansa.Number) {
    Ext.Number[prop] = Pyansa.Number[prop];
  }
});
Ext.define('Pyansa.overrides.data.request.Ajax', {override:'Ext.data.request.Ajax'}, function(Ajax) {
  Ajax.$$parseStatus = Ajax.parseStatus;
  Ajax.parseStatus = function(status, response) {
    var ret = Ajax.$$parseStatus(status, response), type = response.responseType;
    if (status === 0 && (type === 'json' || type === 'document') && !response.responseURL) {
      ret.success = false;
    }
    return ret;
  };
});
Ext.define('Pyansa.overrides.data.Connection', {override:'Ext.data.Connection', config:{sendTimeoutAsHeader:false}, setOptions:function(options, scope) {
  var me = this, sendTimeoutAsHeader = options.sendTimeoutAsHeader || me.getSendTimeoutAsHeader(), timeout = options.timeout || me.getTimeout();
  if (sendTimeoutAsHeader) {
    options.headers = options.headers || {};
    options.headers['X-Request-Timeout'] = timeout;
  }
  return me.callParent(arguments);
}});
Ext.define('Pyansa.overrides.Ajax', {override:'Ext.Ajax', requires:['Pyansa.overrides.data.Connection']}, function() {
  var me = this;
  me.setConfig('sendTimeoutAsHeader', me.config.sendTimeoutAsHeader);
});
Ext.define('Pyansa.util.Format', {requires:['Ext.util.Format', 'Pyansa.Number', 'Pyansa.String']}, function() {
  Pyansa.util.Format = {numberName:function(num) {
    var UNIDADES = {0:'', 1:'un', 2:'dos', 3:'tres', 4:'cuatro', 5:'cinco', 6:'seis', 7:'siete', 8:'ocho', 9:'nueve'}, DECENAS = {1:'', 2:'', 3:'treinta', 4:'cuarenta', 5:'cincuenta', 6:'sesenta', 7:'setenta', 8:'ochenta', 9:'noventa'}, CENTENAS = {1:'', 2:'doscientos', 3:'trescientos', 4:'cuatrocientos', 5:'quinientos', 6:'seiscientos', 7:'setecientos', 8:'ochocientos', 9:'novecientos'}, name = '', clases, numStr, i, claseNum, millaresMillones, millones, millares, unidades;
    num = Pyansa.Number.trunc(num);
    if (num < 0 || num > 9.99999999999E11) {
      throw new Error('El numero a convertir debe entrar en el rango de 0 a 999999999999');
    }
    if (num == 0) {
      return 'cero';
    }
    numStr = Ext.String.leftPad(num.toString(), 12, '0');
    clases = Ext.String.chunk(numStr, 3);
    millaresMillones = getMillares(Ext.String.chunk(clases[0], 1).map(function(i) {
      return parseInt(i);
    }));
    millones = getCentenas(Ext.String.chunk(clases[1], 1).map(function(i) {
      return parseInt(i);
    }));
    millares = getMillares(Ext.String.chunk(clases[2], 1).map(function(i) {
      return parseInt(i);
    }));
    unidades = getCentenas(Ext.String.chunk(clases[3], 1).map(function(i) {
      return parseInt(i);
    }));
    millones = (millaresMillones + ' ' + millones).trim() == '' ? '' : (millaresMillones + ' ' + millones).trim() + ' millones';
    unidades = (millares + ' ' + unidades).trim();
    name = (millones + ' ' + unidades).trim();
    return name;
    function getUnidades(digito) {
      return UNIDADES[digito];
    }
    function getDecenas(digitos) {
      var decena = digitos[0], unidad = digitos[1], str;
      switch(decena) {
        case 0:
          str = getUnidades(unidad);
          break;
        case 1:
          switch(unidad) {
            case 0:
              str = 'diez';
              break;
            case 1:
              str = 'once';
              break;
            case 2:
              str = 'doce';
              break;
            case 3:
              str = 'trece';
              break;
            case 4:
              str = 'catorce';
              break;
            case 5:
              str = 'quince';
              break;
            default:
              str = 'dieci' + getUnidades(unidad);
              break;
          }break;
        case 2:
          switch(unidad) {
            case 0:
              str = 'veinte';
              break;
            default:
              str = 'veinti' + getUnidades(unidad);
              break;
          }break;
        default:
          switch(unidad) {
            case 0:
              str = DECENAS[decena];
              break;
            default:
              str = DECENAS[decena] + ' y ' + getUnidades(unidad);
              break;
          }break;
      }
      return str;
    }
    function getCentenas(digitos) {
      var centena = digitos[0], decena = digitos[1], unidad = digitos[2], str;
      switch(centena) {
        case 0:
          str = getDecenas([decena, unidad]);
          break;
        case 1:
          switch(decena) {
            case 0:
              switch(unidad) {
                case 0:
                  str = 'cien';
                  break;
                default:
                  str = 'ciento ' + getUnidades(unidad);
                  break;
              }break;
            default:
              str = 'ciento ' + getDecenas([decena, unidad]);
              break;
          }break;
        default:
          switch(decena) {
            case 0:
              switch(unidad) {
                case 0:
                  str = CENTENAS[centena];
                  break;
                default:
                  str = CENTENAS[centena] + ' ' + getUnidades(unidad);
                  break;
              }break;
            default:
              str = CENTENAS[centena] + ' ' + getDecenas([decena, unidad]);
              break;
          }break;
      }
      return str;
    }
    function getMillares(digitos) {
      var centena = digitos[0], decena = digitos[1], unidad = digitos[2], str;
      str = getCentenas([centena, decena, unidad]);
      if (str == '') {
        str = '';
      } else {
        if (str == 'un') {
          str = 'mil';
        } else {
          str = str + ' mil';
        }
      }
      return str;
    }
  }};
});
Ext.define('Pyansa.overrides.util.Format', {override:'Ext.util.Format', requires:['Pyansa.util.Format']}, function() {
  var prop;
  for (prop in Pyansa.util.Format) {
    Ext.util.Format[prop] = Pyansa.util.Format[prop];
  }
});
Ext.define('Pyansa.overrides.data.AbstractStore', {override:'Ext.data.AbstractStore', requires:['Ext.data.identifier.Sequential'], constructor:function(config) {
  var me = this;
  config = me.initProperties(config);
  me.callParent([config]);
}, initProperties:function(config) {
  var me = this, identifier = me.self.identifier;
  storeId = me.getStoreId();
  if (!storeId && (config && config.storeId)) {
    me.setStoreId(storeId = config.storeId);
  }
  if (!storeId && (config && config.id)) {
    me.setStoreId(storeId = config.id);
  }
  if (!identifier && storeId) {
    identifier = me.initIdentifier(storeId);
  }
  if (Ext.data.StoreManager.get(storeId)) {
    me.setStoreId(storeId = identifier.generate());
    if (config && config.storeId) {
      config.storeId = storeId;
    }
    if (config && config.id) {
      config.id = storeId;
    }
  }
  return config;
}, initIdentifier:function(storeId) {
  var identifier = new Ext.data.identifier.Sequential({prefix:storeId + '-'});
  this.self.identifier = identifier;
  return identifier;
}});
Ext.define('Pyansa.overrides.data.Model', {override:'Ext.data.Model', requires:['Ext.Object'], clientIdProperty:'clientId', getFirstError:function() {
  var me = this, validation = me.getValidation(), errors = validation.getData(), field, value, msg;
  if (me.isValid()) {
    return null;
  }
  for (field in errors) {
    value = errors[field];
    if (value != true) {
      return '"' + field + '" ' + value.toLowerCase();
    }
  }
}});
Ext.define('Pyansa.overrides.data.proxy.Ajax', {override:'Ext.data.proxy.Ajax', constructor:function(config) {
  var me = this;
  config = me.initProperties(config);
  me.callParent([config]);
}, initProperties:function(config) {
  var me;
  config = config || {};
  config = me.prepareConfig(config);
  return config;
}, prepareConfig:function(config) {
  var headers = config.headers, reader = config.reader, writer = config.writer;
  if (reader && reader.type == 'json') {
    config.headers = me.prepareAcceptHeader(headers);
    config.reader = me.prepareJsonReaderConfig(reader);
  }
  if (writer && writer.type == 'json') {
    config.headers = me.prepareAcceptHeader(headers);
    config.reader = me.prepareJsonWriterConfig(reader);
  }
  return config;
}, prepareAcceptHeader:function(headers) {
  var acceptHeader;
  headers = headers || {};
  if (headers['Accept']) {
    acceptHeader = headers['Accept'];
    if (acceptHeader.indexOf('application/json') == -1) {
      acceptHeader = 'application/json, ' + acceptHeader;
    }
  } else {
    headers['Accept'] = 'application/json, */*';
  }
  return headers;
}, prepareJsonReaderConfig:function(reader) {
  reader.messageProperty = reader.messageProperty || 'message';
  reader.rootProperty = reader.rootProperty || 'records';
  return reader;
}, prepareJsonWriterConfig:function(writer) {
  writer.rootProperty = writer.rootProperty || 'records';
  return reader;
}});
Ext.define('Pyansa.overrides.data.Store', {override:'Ext.data.Store', rejectOnExceptions:false, onBatchComplete:function(batch, operation) {
  var me = this, proxy = me.getProxy(), batchExceptions = batch.getExceptions(), operationException;
  if (me.rejectOnExceptions) {
    if (batchExceptions.length > 0) {
      me.rejectChanges();
    }
  }
  me.callParent(arguments);
}, isDirty:function() {
  var me = this;
  return me.getNewRecords().length > 0 || me.getUpdatedRecords().length > 0 || me.getRemovedRecords().length > 0;
}, clone:function() {
  var me = this, newStore, records, filters, sorters, config;
  config = me.getCurrentConfig();
  delete config.data;
  delete config.filters;
  delete config.sorters;
  delete config.grouper;
  delete config.fields;
  delete config.listeners;
  delete config.proxy;
  delete config.storeId;
  records = me.getDataSource().getRange().map(function(record) {
    return record.copy();
  });
  filters = me.getFilters().clone().getRange();
  sorters = me.getSorters().clone().getRange();
  config.filters = filters;
  config.sorters = sorters;
  newStore = new me.self(config);
  newStore.add(records);
  newStore.loadCount = me.loadCount;
  newStore.totalCount = me.totalCount;
  newStore.complete = me.complete;
  newStore.currentPage = me.currentPage;
  return newStore;
}, getInvalidRecords:function() {
  var me = this;
  return me.filterDataSource(function(item) {
    return !item.isValid();
  });
}});
Ext.define('Pyansa.overrides.data.PageMap', {override:'Ext.data.PageMap', hasRange:function(start, end) {
  var pageNumber = this.getPageFromRecordIndex(start), endPageNumber = this.getPageFromRecordIndex(end);
  for (; pageNumber <= endPageNumber; pageNumber++) {
    if (!this.hasPage(pageNumber)) {
      return false;
    }
  }
  return true;
}});
Ext.define('Pyansa.overrides.data.TreeStore', {override:'Ext.data.TreeStore', getNodesCount:function() {
  var count = 0;
  this.getRoot().cascade({before:function(node) {
    if (!node.isRoot() || node.getId() != 'root') {
      count++;
    }
  }});
  return count;
}, getNodes:function() {
  var nodes = [];
  this.getRoot().cascade({before:function(node) {
    if (!node.isRoot() || node.getId() != 'root') {
      nodes.push(node);
    }
  }});
  return nodes;
}, eachNode:function(fn, scope) {
  var i = 0;
  this.getRoot().cascade(function(node) {
    if (!node.isRoot() || node.getId() != 'root') {
      return fn.call(scope || node, node, i++);
    }
  });
}});
Ext.define('Pyansa.data.proxy.Sql', {extend:'Ext.data.proxy.Client', alias:'pyansa.data.proxy.sql', requires:['Ext.Object', 'Ext.XTemplate'], config:{reader:null, writer:null, defaultDateFormat:'Y-m-d H:i:s'}, connection:null, table:null, selectStatementTpl:['SELECT', "\x3ctpl if\x3d'columns'\x3e", ' {[ values.columns.join(", ") ]}', '\x3ctpl else\x3e', ' *', '\x3c/tpl\x3e', ' FROM {table}'], insertStatementTpl:['INSERT INTO `{table}` (', '{[ values.columns.join(", ") ]}', ') VALUES (', '{[ Ext.String.repeat("?", values.columns.length, ", ") ]}', 
')'], updateStatementTpl:['UPDATE `{table}` SET ', '{[', 'values.columns.map(function(column) {', 'return column + " \x3d ?";', '}).join(", ")', ']}', ' WHERE {idProperty} \x3d ?'], deleteStatementTpl:['DELETE FROM `{table}`', ' WHERE {idProperty} \x3d ?'], constructor:function(config) {
  var me = this;
  config = me.initProperties(config);
  this.callParent([config]);
}, initProperties:function(config) {
  var me = this;
  config = config || {};
  config.connection = config.connection || me.connection;
  config.table = config.table || me.table;
  return config;
}, create:function(operation) {
  var me = this, connection = me.connection, records = operation.getRecords(), modelClass = me.getModel(), clientIdProperty = (new modelClass).clientIdProperty || 'clientId', resultSet = new Ext.data.ResultSet, insertedRecords = [], i, ln, data, record;
  operation.setStarted();
  connection.transaction(function(transaction) {
    me.insertRecords(transaction, records, function(rows, error) {
      if (error) {
        operation.setException(error);
        return;
      }
      for (i = 0, ln = rows.length; i < ln; i++) {
        data = rows[i];
        record = {id:data.id};
        record[clientIdProperty] = data[clientIdProperty];
        insertedRecords.push(record);
      }
      resultSet.setRecords(insertedRecords);
      resultSet.setTotal(ln);
      resultSet.setCount(ln);
      resultSet.setSuccess(true);
      operation.process(resultSet);
    });
  });
}, read:function(operation) {
  var me = this, connection = me.connection, params = operation.getParams() || {}, recordCreator = operation.getRecordCreator(), modelClass = me.getModel(), idProperty = (new modelClass).getIdProperty(), resultSet = new Ext.data.ResultSet, records = [], record, i, ln, data;
  operation.setStarted();
  connection.transaction(function(transaction) {
    me.selectRecords(transaction, params, function(rows, error) {
      if (error) {
        operation.setException(error);
        return;
      }
      for (i = 0, ln = rows.length; i < ln; i++) {
        data = rows[i];
        record = recordCreator ? recordCreator(data, modelClass) : new modelClass(data);
        records.push(record);
      }
      resultSet.setRecords(records);
      resultSet.setTotal(ln);
      resultSet.setCount(ln);
      resultSet.setSuccess(true);
      operation.process(resultSet);
    });
  });
}, update:function(operation) {
  var me = this, connection = me.connection, records = operation.getRecords(), resultSet = new Ext.data.ResultSet, updatedRecords = [], i, ln, data, record;
  operation.setStarted();
  connection.transaction(function(transaction) {
    me.updateRecords(transaction, records, function(rows, error) {
      if (error) {
        operation.setException(error);
        return;
      }
      resultSet.setRecords(rows);
      resultSet.setTotal(ln);
      resultSet.setCount(ln);
      resultSet.setSuccess(true);
      operation.process(resultSet);
    });
  });
}, erase:function(operation) {
  var me = this, connection = me.connection, records = operation.getRecords(), resultSet = new Ext.data.ResultSet, updatedRecords = [], i, ln, data, record;
  operation.setStarted();
  connection.transaction(function(transaction) {
    me.deleteRecords(transaction, records, function(rows, error) {
      if (error) {
        operation.setException(error);
        return;
      }
      resultSet.setRecords(rows);
      resultSet.setTotal(ln);
      resultSet.setCount(ln);
      resultSet.setSuccess(true);
      operation.process(resultSet);
    });
  });
}, selectRecords:function(transaction, params, callback) {
  var me = this, table = me.table, records = [], query, rows, i, ln, data;
  query = (new Ext.XTemplate(me.selectStatementTpl)).apply({table:table.name});
  transaction.executeSql(query, [], function(transaction, resultSet) {
    rows = resultSet.rows;
    for (i = 0, ln = rows.length; i < ln; i++) {
      data = rows.item(i);
      records.push(data);
    }
    if (typeof callback == 'function') {
      callback.call(me, records);
    }
  }, function(transaction, error) {
    if (typeof callback == 'function') {
      callback.call(me, null, error);
    }
  });
}, insertRecords:function(transaction, records, callback) {
  var me = this, table = me.table, columns = table.getColumns().collect('name'), insertedRecords = [], errors = [], totalRecords = records.length, executed = 0, record = records[0], idProperty = record.getIdProperty(), modelIdentifierPrefix = record.self.identifier.getPrefix(), queryWithIdProperty, queryWithoutIdProperty, columnsWithoutIdProperty;
  columnsWithoutIdProperty = columns.filter(function(column) {
    return column != idProperty;
  });
  queryWithIdProperty = (new Ext.XTemplate(me.insertStatementTpl)).apply({table:table.name, columns:columns});
  queryWithoutIdProperty = (new Ext.XTemplate(me.insertStatementTpl)).apply({table:table.name, columns:columnsWithoutIdProperty});
  records.sort(function(a, b) {
    var aIsAutogenerated = Ext.String.startsWith(a.getId(), modelIdentifierPrefix), bIsAutogenerated = Ext.String.startsWith(b.getId(), modelIdentifierPrefix);
    if (aIsAutogenerated && !bIsAutogenerated) {
      return 1;
    } else {
      if (!aIsAutogenerated && bIsAutogenerated) {
        return -1;
      } else {
        return 0;
      }
    }
  });
  Ext.Array.each(records, function(record) {
    var id = record.getId(), data = me.getRecordData(record), clientIdProperty = record.clientIdProperty || 'clientId', query, values;
    if (Ext.String.startsWith(id, modelIdentifierPrefix)) {
      query = queryWithoutIdProperty;
      values = me.getColumnValues(columnsWithoutIdProperty, data);
    } else {
      query = queryWithIdProperty;
      values = me.getColumnValues(columns, data);
    }
    transaction.executeSql(query, values, function(transaction, resultSet) {
      executed++;
      record = {id:resultSet.insertId};
      record[clientIdProperty] = id;
      insertedRecords.push(record);
      if (executed === totalRecords && typeof callback === 'function') {
        callback.call(me, insertedRecords, errors.length > 0 ? errors : null);
      }
    }, function(transaction, error) {
      executed++;
      record = {id:id, error:error};
      record[clientIdProperty] = id;
      errors.push(record);
      if (executed === totalRecords && typeof callback === 'function') {
        callback.call(me, insertedRecords, errors);
      }
    });
  });
}, updateRecords:function(transaction, records, callback) {
  var me = this, table = me.table, columns = table.getColumns().collect('name'), updatedRecords = [], errors = [], totalRecords = records.length, idProperty = records[0].getIdProperty(), executed = 0;
  query = (new Ext.XTemplate(me.updateStatementTpl)).apply({table:table.name, columns:columns, idProperty:idProperty});
  Ext.Array.each(records, function(record) {
    var id = record.getId(), data = me.getRecordData(record), values = me.getColumnValues(columns, data), clientIdProperty = record.clientIdProperty || 'clientId';
    transaction.executeSql(query, values.concat(id), function(transaction, resultSet) {
      executed++;
      record = {id:id};
      record[clientIdProperty] = id;
      updatedRecords.push(record);
      if (executed === totalRecords && typeof callback === 'function') {
        callback.call(me, updatedRecords, errors.length > 0 ? errors : null);
      }
    }, function(transaction, error) {
      executed++;
      record = {error:error};
      record[clientIdProperty] = id;
      errors.push(record);
      if (executed === totalRecords && typeof callback === 'function') {
        callback.call(me, updatedRecords, errors);
      }
    });
  });
}, deleteRecords:function(transaction, records, callback) {
  var me = this, table = me.table, columns = table.getColumns().collect('name'), deletedRecords = [], errors = [], totalRecords = records.length, idProperty = records[0].getIdProperty(), executed = 0;
  query = (new Ext.XTemplate(me.deleteStatementTpl)).apply({table:table.name, idProperty:idProperty});
  Ext.Array.each(records, function(record) {
    var id = record.getId(), clientIdProperty = record.clientIdProperty || 'clientId';
    transaction.executeSql(query, [id], function(transaction, resultSet) {
      executed++;
      record = {id:id};
      record[clientIdProperty] = id;
      deletedRecords.push(record);
      if (executed === totalRecords && typeof callback === 'function') {
        callback.call(me, deletedRecords, errors.length > 0 ? errors : null);
      }
    }, function(transaction, error) {
      executed++;
      record = {error:error};
      record[clientIdProperty] = id;
      errors.push(record);
      if (executed === totalRecords && typeof callback === 'function') {
        callback.call(me, deletedRecords, errors);
      }
    });
  });
}, getRecordData:function(record) {
  var me = this, fields = record.getFields(), data = {}, name, value, i, ln, field;
  for (i = 0, ln = fields.length; i < ln; i++) {
    field = fields[i];
    if (field.persist) {
      name = field.name;
      value = record.get(name);
      if (field.isDateField) {
        value = me.parseDate(field, value);
      } else {
        if (field.isBooleanField) {
          value = me.parseBoolean(field, value);
        }
      }
      data[name] = value;
    }
  }
  return data;
}, parseDate:function(field, date) {
  if (Ext.isEmpty(date)) {
    return null;
  }
  var dateFormat = field.getDateFormat() || this.getDefaultDateFormat();
  switch(dateFormat) {
    case 'timestamp':
      return date.getTime() / 1000;
    case 'time':
      return date.getTime();
    default:
      return Ext.Date.format(date, dateFormat);
  }
}, parseBoolean:function(field, value) {
  return value == null ? null : !!value ? 1 : 0;
}, getColumnValues:function(columns, data) {
  var ln = columns.length, values = [], i, column, value;
  for (i = 0; i < ln; i++) {
    column = columns[i];
    value = data[column];
    if (value !== undefined) {
      values.push(value);
    } else {
      values.push(null);
    }
  }
  return values;
}});
Ext.define('Pyansa.database.sqlite.Column', {alias:'pyansa.database.sqlite.column', requires:['Ext.XTemplate'], statementTpl:['`{name}` ', '{[values.type.toUpperCase()]} ', "\x3ctpl if\x3d'!acceptsNull'\x3eNOT \x3c/tpl\x3e", 'NULL', "\x3ctpl if\x3d'defaultValue !\x3d\x3d null \x26\x26 defaultValue !\x3d\x3d undefined'\x3e", ' DEFAULT ', "\x3ctpl switch\x3d'typeof values.defaultValue'\x3e", "\x3ctpl case\x3d'number'\x3e", '{defaultValue}', '\x3ctpl default\x3e', '"{defaultValue}"', '\x3c/tpl\x3e', 
'\x3c/tpl\x3e'], isColumn:true, name:null, type:null, isPrimaryKey:false, acceptsNull:false, defaultValue:null, constructor:function(config) {
  var me = this;
  config = me.initProperties(config);
  this.initConfig(config);
}, initProperties:function(config) {
  var me = this;
  config = config || {};
  config.name = config.name || me.name;
  config.type = config.type || me.type;
  config.isPrimaryKey = config.isPrimaryKey || me.isPrimaryKey;
  config.acceptsNull = config.acceptsNull || me.acceptsNull;
  config.defaultValue = config.defaultValue || me.defaultValue;
  return config;
}, buildStatement:function(tpl) {
  var me = this;
  tpl = tpl || me.statementTpl;
  if (!tpl) {
    Ext.raise('No existe un template para generar la sentencia');
  }
  if (Ext.isArray(tpl)) {
    tpl = tpl.join('');
  }
  if (!tpl.isXTemplate) {
    tpl = new Ext.XTemplate(tpl);
  }
  return tpl.apply(me);
}});
Ext.define('Pyansa.database.sqlite.Connection', {alias:'pyansa.database.sqlite.connection', isConnection:true, name:null, version:'1.0', description:'WebSQL Database', size:0, connection:null, constructor:function(config) {
  var me = this;
  config = me.initProperties(config);
  me.initConfig(config);
  if (me.size <= 0) {
    Ext.raise('El tamaño de la base de datos debe ser mayor a 0');
  }
  me.connection = me.databaseObject = openDatabase(me.name, me.version, me.description, me.size);
}, initProperties:function(config) {
  var me = this;
  config = config || {};
  config.name = config.name || me.name;
  config.version = config.version || me.version;
  config.description = config.description || me.description;
  config.size = config.size || me.size;
  config.connection = config.connection || me.connection;
  return config;
}, transaction:function() {
  var me = this;
  me.connection.transaction.apply(me.connection, arguments);
}});
Ext.define('Pyansa.database.sqlite.Table', {alias:'pyansa.database.sqlite.table', requires:['Pyansa.database.sqlite.Column', 'Ext.XTemplate', 'Ext.util.Collection', 'Ext.Deferred'], createStatementTpl:['CREATE', "\x3ctpl if\x3d'isTemporary'\x3e", ' TEMPORARY', '\x3c/tpl\x3e', ' TABLE', "\x3ctpl if\x3d'checkExistence'\x3e", ' IF NOT EXISTS', '\x3c/tpl\x3e', ' `{name}` (', '{[', 'values.columns.items.map(function(item) { ', 'return item.buildStatement();', '}).join(", ")', ']}', '\x3ctpl if\x3d\'columns.findIndex("isPrimaryKey", true) !\x3d -1\'\x3e', 
', PRIMARY KEY (', '{[', 'values.columns.items.filter(function(item) {', 'return item.isPrimaryKey;', '}).map(function(item) { ', 'return "`" + item.name + "`";', '}).join(", ")', ']}', ')', '\x3c/tpl\x3e', ')'], dropStatementTpl:['DROP', "\x3ctpl if\x3d'isTemporary'\x3e", ' TEMPORARY', '\x3c/tpl\x3e', ' TABLE', "\x3ctpl if\x3d'checkExistence'\x3e", ' IF EXISTS', '\x3c/tpl\x3e', ' `{name}`'], isTable:true, name:null, isTemporary:false, checkExistence:false, columns:null, constructor:function(config) {
  var me = this, columns;
  config = me.initProperties(config);
  me.initConfig(config);
  columns = me.columns;
  me.columns = new Ext.util.Collection({keyFn:function(item) {
    return item.name;
  }, decoder:me.createColumn});
  me.setColumns(columns);
}, initProperties:function(config) {
  var me = this;
  config = config || {};
  config.name = config.name || me.name;
  config.isTemporary = config.isTemporary || me.isTemporary;
  config.checkExistence = config.checkExistence || me.checkExistence;
  config.columns = config.columns || me.columns;
  return config;
}, getColumns:function() {
  return this.columns;
}, setColumns:function(columns) {
  var me = this, columnsCollection = me.columns, i;
  columns = columns || [];
  columnsCollection.clear();
  for (i = 0; i < columns.length; i++) {
    columnsCollection.add(columns[i]);
  }
}, createColumn:function(column) {
  if (!column.isColumn) {
    column = Ext.create('pyansa.database.sqlite.column', column);
  }
  return column;
}, buildStatement:function(tpl) {
  var me = this;
  if (!tpl) {
    Ext.raise('No existe un template para generar la sentencia');
  }
  if (Ext.isArray(tpl)) {
    tpl = tpl.join('');
  }
  if (!tpl.isXTemplate) {
    tpl = new Ext.XTemplate(tpl);
  }
  return tpl.apply(me);
}, create:function() {
  var me = this;
  return me.schema.query(me.buildStatement(me.createStatementTpl));
}, drop:function() {
  var me = this;
  return me.schema.query(me.buildStatement(me.dropStatementTpl));
}, truncate:function() {
  var me = this, deferred = new Ext.Deferred;
  me.schema.transaction(function(tx) {
    tx.executeSql(me.buildStatement(me.dropStatementTpl));
    tx.executeSql(me.buildStatement(me.createStatementTpl));
  }, function(sqlError) {
    deferred.reject(sqlError);
  }, function() {
    deferred.resolve();
  });
  return deferred.promise;
}});
Ext.define('Pyansa.database.sqlite.Schema', {alias:'pyansa.database.sqlite.schema.', requires:['Pyansa.database.sqlite.Connection', 'Pyansa.database.sqlite.Table', 'Ext.Deferred'], isSchema:true, name:null, version:'1.0', description:'WebSQL Database', size:0, connection:null, tables:null, constructor:function(config) {
  var me = this, tables, defaults;
  config = me.initProperties(config);
  me.initConfig(config);
  me.connection = new Pyansa.database.sqlite.Connection({name:me.name, version:me.version, description:me.description, size:me.size});
  tables = me.tables;
  me.tables = new Ext.util.Collection({keyFn:function(item) {
    return item.name;
  }, decoder:me.createTable.bind(me)});
  me.setTables(tables);
}, initProperties:function(config) {
  var me = this;
  config = config || {};
  config.name = config.name || me.name;
  config.version = config.version || me.version;
  config.description = config.description || me.description;
  config.size = config.size || me.size;
  config.connection = config.connection || me.connection;
  config.tables = config.tables || me.tables;
  return config;
}, getTables:function() {
  return this.tables;
}, setTables:function(tables) {
  var me = this, tableCollection = me.tables, i, table;
  tables = tables || [];
  tableCollection.clear();
  for (i = 0; i < tables.length; i++) {
    table = tables[i];
    tableCollection.add(tables[i]);
  }
}, createTable:function(table) {
  var me = this;
  if (typeof table === 'string') {
    table = {type:table};
  }
  if (!table.isTable) {
    table = Ext.create(table.type || 'Pyansa.database.sqlite.Table', table);
    table.schema = me;
  }
  return table;
}, query:function(query, params) {
  var me = this, deferred = new Ext.Deferred;
  if (!me.connection) {
    Ext.raise('No existe una conexion a la base de datos');
  }
  me.connection.transaction(function(tx) {
    tx.executeSql(query, params);
  }, function(sqlError) {
    deferred.reject(sqlError);
  }, function() {
    deferred.resolve();
  });
  return deferred.promise;
}, transaction:function() {
  var me = this;
  me.connection.transaction.apply(me.connection, arguments);
}, create:function() {
  var me = this, deferred = new Ext.Deferred;
  me.connection.transaction(function(tx) {
    me.tables.each(function(item) {
      tx.executeSql(item.buildStatement(item.createStatementTpl));
    });
  }, function(sqlError) {
    deferred.reject(sqlError);
  }, function() {
    deferred.resolve();
  });
  return deferred.promise;
}, drop:function() {
  var me = this, deferred = new Ext.Deferred;
  me.transaction(function(tx) {
    me.tables.each(function(item) {
      tx.executeSql(item.buildStatement(item.dropStatementTpl));
    });
  }, function(sqlError) {
    deferred.reject(sqlError);
  }, function() {
    deferred.resolve();
  });
  return deferred.promise;
}});
Ext.define('Ext.state.Provider', {mixins:{observable:'Ext.util.Observable'}, prefix:'ext-', constructor:function(config) {
  var me = this;
  Ext.apply(me, config);
  me.state = {};
  me.mixins.observable.constructor.call(me);
}, get:function(name, defaultValue) {
  var ret = this.state[name];
  return ret === undefined ? defaultValue : ret;
}, clear:function(name) {
  var me = this;
  delete me.state[name];
  me.fireEvent('statechange', me, name, null);
}, set:function(name, value) {
  var me = this;
  me.state[name] = value;
  me.fireEvent('statechange', me, name, value);
}, decodeValue:function(value) {
  var me = this, re = /^(a|n|d|b|s|o|e)\:(.*)$/, matches = re.exec(unescape(value)), all, type, keyValue, values, vLen, v;
  if (!matches || !matches[1]) {
    return;
  }
  type = matches[1];
  value = matches[2];
  switch(type) {
    case 'e':
      return null;
    case 'n':
      return parseFloat(value);
    case 'd':
      return new Date(Date.parse(value));
    case 'b':
      return value === '1';
    case 'a':
      all = [];
      if (value) {
        values = value.split('^');
        vLen = values.length;
        for (v = 0; v < vLen; v++) {
          value = values[v];
          all.push(me.decodeValue(value));
        }
      }
      return all;
    case 'o':
      all = {};
      if (value) {
        values = value.split('^');
        vLen = values.length;
        for (v = 0; v < vLen; v++) {
          value = values[v];
          keyValue = value.split('\x3d');
          all[keyValue[0]] = me.decodeValue(keyValue[1]);
        }
      }
      return all;
    default:
      return value;
  }
}, encodeValue:function(value) {
  var flat = '', i = 0, enc, len, key;
  if (value == null) {
    return 'e:1';
  } else {
    if (typeof value === 'number') {
      enc = 'n:' + value;
    } else {
      if (typeof value === 'boolean') {
        enc = 'b:' + (value ? '1' : '0');
      } else {
        if (Ext.isDate(value)) {
          enc = 'd:' + value.toUTCString();
        } else {
          if (Ext.isArray(value)) {
            for (len = value.length; i < len; i++) {
              flat += this.encodeValue(value[i]);
              if (i !== len - 1) {
                flat += '^';
              }
            }
            enc = 'a:' + flat;
          } else {
            if (typeof value === 'object') {
              for (key in value) {
                if (typeof value[key] !== 'function' && value[key] !== undefined) {
                  flat += key + '\x3d' + this.encodeValue(value[key]) + '^';
                }
              }
              enc = 'o:' + flat.substring(0, flat.length - 1);
            } else {
              enc = 's:' + value;
            }
          }
        }
      }
    }
  }
  return escape(enc);
}});
Ext.define('Ext.state.CookieProvider', {extend:'Ext.state.Provider', constructor:function(config) {
  var me = this;
  me.path = '/';
  me.expires = new Date(Ext.Date.now() + 1000 * 60 * 60 * 24 * 7);
  me.domain = null;
  me.secure = false;
  me.callParent(arguments);
  me.state = me.readCookies();
}, set:function(name, value) {
  var me = this;
  if (typeof value === 'undefined' || value === null) {
    me.clear(name);
    return;
  }
  me.setCookie(name, value);
  me.callParent(arguments);
}, clear:function(name) {
  this.clearCookie(name);
  this.callParent(arguments);
}, readCookies:function() {
  var cookies = {}, c = document.cookie + ';', re = /\s?(.*?)=(.*?);/g, prefix = this.prefix, len = prefix.length, matches, name, value;
  while ((matches = re.exec(c)) != null) {
    name = matches[1];
    value = matches[2];
    if (name && name.substring(0, len) === prefix) {
      cookies[name.substr(len)] = this.decodeValue(value);
    }
  }
  return cookies;
}, setCookie:function(name, value) {
  var me = this;
  document.cookie = me.prefix + name + '\x3d' + me.encodeValue(value) + (me.expires == null ? '' : '; expires\x3d' + me.expires.toUTCString()) + (me.path == null ? '' : '; path\x3d' + me.path) + (me.domain == null ? '' : '; domain\x3d' + me.domain) + (me.secure ? '; secure' : '');
}, clearCookie:function(name) {
  var me = this;
  document.cookie = me.prefix + name + '\x3dnull; expires\x3dThu, 01-Jan-1970 00:00:01 GMT' + (me.path == null ? '' : '; path\x3d' + me.path) + (me.domain == null ? '' : '; domain\x3d' + me.domain) + (me.secure ? '; secure' : '');
}});
Ext.define('Ext.state.LocalStorageProvider', {extend:'Ext.state.Provider', requires:['Ext.util.LocalStorage'], alias:'state.localstorage', constructor:function() {
  var me = this;
  me.callParent(arguments);
  me.store = me.getStorageObject();
  if (me.store) {
    me.state = me.readLocalStorage();
  } else {
    me.state = {};
  }
}, readLocalStorage:function() {
  var store = this.store, data = {}, keys = store.getKeys(), i = keys.length, key;
  while (i--) {
    key = keys[i];
    data[key] = this.decodeValue(store.getItem(key));
  }
  return data;
}, set:function(name, value) {
  var me = this;
  me.clear(name);
  if (value != null) {
    me.store.setItem(name, me.encodeValue(value));
    me.callParent(arguments);
  }
}, clear:function(name) {
  this.store.removeItem(name);
  this.callParent(arguments);
}, getStorageObject:function() {
  var prefix = this.prefix, id = prefix, n = id.length - 1;
  if (id.charAt(n) === '-') {
    id = id.substring(0, n);
  }
  return new Ext.util.LocalStorage({id:id, prefix:prefix});
}});
Ext.define('Ext.state.Manager', {singleton:true, requires:['Ext.state.Provider'], constructor:function() {
  this.provider = new Ext.state.Provider;
}, setProvider:function(stateProvider) {
  this.provider = stateProvider;
}, get:function(key, defaultValue) {
  return this.provider.get(key, defaultValue);
}, set:function(key, value) {
  this.provider.set(key, value);
}, clear:function(key) {
  this.provider.clear(key);
}, getProvider:function() {
  return this.provider;
}});
Ext.define('Ext.state.Stateful', {mixinId:'state', requires:['Ext.state.Manager', 'Ext.util.TaskRunner'], config:{stateful:null}, saveDelay:100, constructor:function() {
  var me = this;
  if (!me.stateEvents) {
    me.stateEvents = [];
  }
  if (me.stateful) {
    me.addStateEvents(me.stateEvents);
    me.initState();
  }
}, addStateEvents:function(events) {
  var me = this, i, event, stateEventsByName, eventArray;
  if (me.stateful && me.getStateId()) {
    eventArray = typeof events === 'string' ? arguments : events;
    stateEventsByName = me.stateEventsByName || (me.stateEventsByName = {});
    for (i = eventArray.length; i--;) {
      event = eventArray[i];
      if (event && !stateEventsByName[event]) {
        stateEventsByName[event] = 1;
        me.on(event, me.onStateChange, me);
      }
    }
  }
}, onStateChange:function() {
  var me = this, delay = me.saveDelay, statics, runner;
  if (!me.stateful) {
    return;
  }
  if (delay) {
    if (!me.stateTask) {
      statics = Ext.state.Stateful;
      runner = statics.runner || (statics.runner = new Ext.util.TaskRunner);
      me.stateTask = runner.newTask({run:me.saveState, scope:me, interval:delay, repeat:1, fireIdleEvent:false});
    }
    me.stateTask.start();
  } else {
    me.saveState();
  }
}, saveState:function() {
  var me = this, stateful = me.getStateful(), id = stateful && me.getStateId(), hasListeners = me.hasListeners, cfg, configs, plugins, plugin, i, len, state, pluginState;
  if (id) {
    state = me.getState() || {};
    if (Ext.isObject(stateful)) {
      configs = me.self.getConfigurator();
      configs = configs.configs;
      for (i in stateful) {
        if (stateful[i]) {
          if (!(i in state)) {
            cfg = configs[i];
            state[i] = cfg ? me[cfg.get]() : me[i];
          }
        } else {
          delete state[i];
        }
      }
    }
    plugins = me.getPlugins() || [];
    for (i = 0, len = plugins.length; i < len; i++) {
      plugin = plugins[i];
      if (plugin && plugin.getState) {
        pluginState = plugin.getState(state);
        if (pluginState && !state[plugin.ptype]) {
          state[plugin.ptype] = pluginState;
        }
      }
    }
    if (!hasListeners.beforestatesave || me.fireEvent('beforestatesave', me, state) !== false) {
      Ext.state.Manager.set(id, state);
      if (hasListeners.statesave) {
        me.fireEvent('statesave', me, state);
      }
    }
  }
}, getState:function() {
  return null;
}, applyState:function(state) {
  if (state) {
    Ext.apply(this, state);
  }
}, getStateId:function() {
  var me = this;
  return me.stateId || (me.autoGenId ? null : me.id);
}, initState:function() {
  var me = this, id = me.stateful && me.getStateId(), hasListeners = me.hasListeners, state, combinedState, i, len, plugins, plugin, pluginType;
  if (id) {
    combinedState = Ext.state.Manager.get(id);
    if (combinedState) {
      state = Ext.apply({}, combinedState);
      if (!hasListeners.beforestaterestore || me.fireEvent('beforestaterestore', me, combinedState) !== false) {
        plugins = me.getPlugins() || [];
        for (i = 0, len = plugins.length; i < len; i++) {
          plugin = plugins[i];
          if (plugin) {
            pluginType = plugin.ptype;
            if (plugin.applyState) {
              plugin.applyState(state[pluginType], combinedState);
            }
            delete state[pluginType];
          }
        }
        me.applyState(state);
        if (hasListeners.staterestore) {
          me.fireEvent('staterestore', me, combinedState);
        }
      }
    }
  }
}, savePropToState:function(propName, state, stateName) {
  var me = this, value = me[propName], config = me.initialConfig;
  if (me.hasOwnProperty(propName)) {
    if (!config || config[propName] !== value) {
      if (state) {
        state[stateName || propName] = value;
      }
      return true;
    }
  }
  return false;
}, savePropsToState:function(propNames, state) {
  var me = this, i, n;
  if (typeof propNames === 'string') {
    me.savePropToState(propNames, state);
  } else {
    for (i = 0, n = propNames.length; i < n; ++i) {
      me.savePropToState(propNames[i], state);
    }
  }
  return state;
}, destroy:function() {
  var task = this.stateTask;
  if (task) {
    task.destroy();
    this.stateTask = null;
  }
}});
Ext.define('Pyansa.state.StatefulStorage', {alias:'pyansa.state.statefulstorage', mixins:['Ext.state.Stateful', 'Ext.mixin.Observable'], config:{stateful:true}, data:null, stateful:true, constructor:function(config) {
  var me = this;
  me.data = {};
  me.initConfig(config);
  me.mixins.observable.constructor.call(me);
  me.mixins.state.constructor.call(me);
}, getState:function() {
  var me = this, state = {}, prop;
  for (prop in me.data) {
    if (me.data.hasOwnProperty(prop)) {
      state[prop] = me.data[prop];
    }
  }
  return state;
}, applyState:function(state) {
  var me = this;
  if (state) {
    Ext.apply(me.data, state);
  }
}, set:function(key, value) {
  var me = this;
  me.data[key] = value;
  me.saveState();
}, get:function(key) {
  var me = this;
  return me.data[key];
}, 'delete':function(key) {
  var me = this;
  delete me.data[key];
  me.saveState();
}, getPlugins:function() {
  return null;
}});
