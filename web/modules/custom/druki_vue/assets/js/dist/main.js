(function (Drupal, Vue$2) {
            'use strict';

            Drupal = Drupal && Drupal.hasOwnProperty('default') ? Drupal['default'] : Drupal;
            Vue$2 = Vue$2 && Vue$2.hasOwnProperty('default') ? Vue$2['default'] : Vue$2;

            var global$1 = (typeof global !== "undefined" ? global :
                        typeof self !== "undefined" ? self :
                        typeof window !== "undefined" ? window : {});

            if (typeof global$1.setTimeout === 'function') ;
            if (typeof global$1.clearTimeout === 'function') ;

            // from https://github.com/kumavis/browser-process-hrtime/blob/master/index.js
            var performance = global$1.performance || {};
            var performanceNow =
              performance.now        ||
              performance.mozNow     ||
              performance.msNow      ||
              performance.oNow       ||
              performance.webkitNow  ||
              function(){ return (new Date()).getTime() };

            /**
             * vuex v3.1.0
             * (c) 2019 Evan You
             * @license MIT
             */
            function applyMixin (Vue) {
              var version = Number(Vue.version.split('.')[0]);

              if (version >= 2) {
                Vue.mixin({ beforeCreate: vuexInit });
              } else {
                // override init and inject vuex init procedure
                // for 1.x backwards compatibility.
                var _init = Vue.prototype._init;
                Vue.prototype._init = function (options) {
                  if ( options === void 0 ) options = {};

                  options.init = options.init
                    ? [vuexInit].concat(options.init)
                    : vuexInit;
                  _init.call(this, options);
                };
              }

              /**
               * Vuex init hook, injected into each instances init hooks list.
               */

              function vuexInit () {
                var options = this.$options;
                // store injection
                if (options.store) {
                  this.$store = typeof options.store === 'function'
                    ? options.store()
                    : options.store;
                } else if (options.parent && options.parent.$store) {
                  this.$store = options.parent.$store;
                }
              }
            }

            var devtoolHook =
              typeof window !== 'undefined' &&
              window.__VUE_DEVTOOLS_GLOBAL_HOOK__;

            function devtoolPlugin (store) {
              if (!devtoolHook) { return }

              store._devtoolHook = devtoolHook;

              devtoolHook.emit('vuex:init', store);

              devtoolHook.on('vuex:travel-to-state', function (targetState) {
                store.replaceState(targetState);
              });

              store.subscribe(function (mutation, state) {
                devtoolHook.emit('vuex:mutation', mutation, state);
              });
            }

            /**
             * Get the first item that pass the test
             * by second argument function
             *
             * @param {Array} list
             * @param {Function} f
             * @return {*}
             */

            /**
             * forEach for object
             */
            function forEachValue (obj, fn) {
              Object.keys(obj).forEach(function (key) { return fn(obj[key], key); });
            }

            function isObject (obj) {
              return obj !== null && typeof obj === 'object'
            }

            function isPromise (val) {
              return val && typeof val.then === 'function'
            }

            function assert (condition, msg) {
              if (!condition) { throw new Error(("[vuex] " + msg)) }
            }

            // Base data struct for store's module, package with some attribute and method
            var Module = function Module (rawModule, runtime) {
              this.runtime = runtime;
              // Store some children item
              this._children = Object.create(null);
              // Store the origin module object which passed by programmer
              this._rawModule = rawModule;
              var rawState = rawModule.state;

              // Store the origin module's state
              this.state = (typeof rawState === 'function' ? rawState() : rawState) || {};
            };

            var prototypeAccessors = { namespaced: { configurable: true } };

            prototypeAccessors.namespaced.get = function () {
              return !!this._rawModule.namespaced
            };

            Module.prototype.addChild = function addChild (key, module) {
              this._children[key] = module;
            };

            Module.prototype.removeChild = function removeChild (key) {
              delete this._children[key];
            };

            Module.prototype.getChild = function getChild (key) {
              return this._children[key]
            };

            Module.prototype.update = function update (rawModule) {
              this._rawModule.namespaced = rawModule.namespaced;
              if (rawModule.actions) {
                this._rawModule.actions = rawModule.actions;
              }
              if (rawModule.mutations) {
                this._rawModule.mutations = rawModule.mutations;
              }
              if (rawModule.getters) {
                this._rawModule.getters = rawModule.getters;
              }
            };

            Module.prototype.forEachChild = function forEachChild (fn) {
              forEachValue(this._children, fn);
            };

            Module.prototype.forEachGetter = function forEachGetter (fn) {
              if (this._rawModule.getters) {
                forEachValue(this._rawModule.getters, fn);
              }
            };

            Module.prototype.forEachAction = function forEachAction (fn) {
              if (this._rawModule.actions) {
                forEachValue(this._rawModule.actions, fn);
              }
            };

            Module.prototype.forEachMutation = function forEachMutation (fn) {
              if (this._rawModule.mutations) {
                forEachValue(this._rawModule.mutations, fn);
              }
            };

            Object.defineProperties( Module.prototype, prototypeAccessors );

            var ModuleCollection = function ModuleCollection (rawRootModule) {
              // register root module (Vuex.Store options)
              this.register([], rawRootModule, false);
            };

            ModuleCollection.prototype.get = function get (path) {
              return path.reduce(function (module, key) {
                return module.getChild(key)
              }, this.root)
            };

            ModuleCollection.prototype.getNamespace = function getNamespace (path) {
              var module = this.root;
              return path.reduce(function (namespace, key) {
                module = module.getChild(key);
                return namespace + (module.namespaced ? key + '/' : '')
              }, '')
            };

            ModuleCollection.prototype.update = function update$1 (rawRootModule) {
              update([], this.root, rawRootModule);
            };

            ModuleCollection.prototype.register = function register (path, rawModule, runtime) {
                var this$1 = this;
                if ( runtime === void 0 ) runtime = true;

              {
                assertRawModule(path, rawModule);
              }

              var newModule = new Module(rawModule, runtime);
              if (path.length === 0) {
                this.root = newModule;
              } else {
                var parent = this.get(path.slice(0, -1));
                parent.addChild(path[path.length - 1], newModule);
              }

              // register nested modules
              if (rawModule.modules) {
                forEachValue(rawModule.modules, function (rawChildModule, key) {
                  this$1.register(path.concat(key), rawChildModule, runtime);
                });
              }
            };

            ModuleCollection.prototype.unregister = function unregister (path) {
              var parent = this.get(path.slice(0, -1));
              var key = path[path.length - 1];
              if (!parent.getChild(key).runtime) { return }

              parent.removeChild(key);
            };

            function update (path, targetModule, newModule) {
              {
                assertRawModule(path, newModule);
              }

              // update target module
              targetModule.update(newModule);

              // update nested modules
              if (newModule.modules) {
                for (var key in newModule.modules) {
                  if (!targetModule.getChild(key)) {
                    {
                      console.warn(
                        "[vuex] trying to add a new module '" + key + "' on hot reloading, " +
                        'manual reload is needed'
                      );
                    }
                    return
                  }
                  update(
                    path.concat(key),
                    targetModule.getChild(key),
                    newModule.modules[key]
                  );
                }
              }
            }

            var functionAssert = {
              assert: function (value) { return typeof value === 'function'; },
              expected: 'function'
            };

            var objectAssert = {
              assert: function (value) { return typeof value === 'function' ||
                (typeof value === 'object' && typeof value.handler === 'function'); },
              expected: 'function or object with "handler" function'
            };

            var assertTypes = {
              getters: functionAssert,
              mutations: functionAssert,
              actions: objectAssert
            };

            function assertRawModule (path, rawModule) {
              Object.keys(assertTypes).forEach(function (key) {
                if (!rawModule[key]) { return }

                var assertOptions = assertTypes[key];

                forEachValue(rawModule[key], function (value, type) {
                  assert(
                    assertOptions.assert(value),
                    makeAssertionMessage(path, key, type, value, assertOptions.expected)
                  );
                });
              });
            }

            function makeAssertionMessage (path, key, type, value, expected) {
              var buf = key + " should be " + expected + " but \"" + key + "." + type + "\"";
              if (path.length > 0) {
                buf += " in module \"" + (path.join('.')) + "\"";
              }
              buf += " is " + (JSON.stringify(value)) + ".";
              return buf
            }

            var Vue$1; // bind on install

            var Store = function Store (options) {
              var this$1 = this;
              if ( options === void 0 ) options = {};

              // Auto install if it is not done yet and `window` has `Vue`.
              // To allow users to avoid auto-installation in some cases,
              // this code should be placed here. See #731
              if (!Vue$1 && typeof window !== 'undefined' && window.Vue) {
                install(window.Vue);
              }

              {
                assert(Vue$1, "must call Vue.use(Vuex) before creating a store instance.");
                assert(typeof Promise !== 'undefined', "vuex requires a Promise polyfill in this browser.");
                assert(this instanceof Store, "store must be called with the new operator.");
              }

              var plugins = options.plugins; if ( plugins === void 0 ) plugins = [];
              var strict = options.strict; if ( strict === void 0 ) strict = false;

              // store internal state
              this._committing = false;
              this._actions = Object.create(null);
              this._actionSubscribers = [];
              this._mutations = Object.create(null);
              this._wrappedGetters = Object.create(null);
              this._modules = new ModuleCollection(options);
              this._modulesNamespaceMap = Object.create(null);
              this._subscribers = [];
              this._watcherVM = new Vue$1();

              // bind commit and dispatch to self
              var store = this;
              var ref = this;
              var dispatch = ref.dispatch;
              var commit = ref.commit;
              this.dispatch = function boundDispatch (type, payload) {
                return dispatch.call(store, type, payload)
              };
              this.commit = function boundCommit (type, payload, options) {
                return commit.call(store, type, payload, options)
              };

              // strict mode
              this.strict = strict;

              var state = this._modules.root.state;

              // init root module.
              // this also recursively registers all sub-modules
              // and collects all module getters inside this._wrappedGetters
              installModule(this, state, [], this._modules.root);

              // initialize the store vm, which is responsible for the reactivity
              // (also registers _wrappedGetters as computed properties)
              resetStoreVM(this, state);

              // apply plugins
              plugins.forEach(function (plugin) { return plugin(this$1); });

              var useDevtools = options.devtools !== undefined ? options.devtools : Vue$1.config.devtools;
              if (useDevtools) {
                devtoolPlugin(this);
              }
            };

            var prototypeAccessors$1 = { state: { configurable: true } };

            prototypeAccessors$1.state.get = function () {
              return this._vm._data.$$state
            };

            prototypeAccessors$1.state.set = function (v) {
              {
                assert(false, "use store.replaceState() to explicit replace store state.");
              }
            };

            Store.prototype.commit = function commit (_type, _payload, _options) {
                var this$1 = this;

              // check object-style commit
              var ref = unifyObjectStyle(_type, _payload, _options);
                var type = ref.type;
                var payload = ref.payload;
                var options = ref.options;

              var mutation = { type: type, payload: payload };
              var entry = this._mutations[type];
              if (!entry) {
                {
                  console.error(("[vuex] unknown mutation type: " + type));
                }
                return
              }
              this._withCommit(function () {
                entry.forEach(function commitIterator (handler) {
                  handler(payload);
                });
              });
              this._subscribers.forEach(function (sub) { return sub(mutation, this$1.state); });

              if (
                options && options.silent
              ) {
                console.warn(
                  "[vuex] mutation type: " + type + ". Silent option has been removed. " +
                  'Use the filter functionality in the vue-devtools'
                );
              }
            };

            Store.prototype.dispatch = function dispatch (_type, _payload) {
                var this$1 = this;

              // check object-style dispatch
              var ref = unifyObjectStyle(_type, _payload);
                var type = ref.type;
                var payload = ref.payload;

              var action = { type: type, payload: payload };
              var entry = this._actions[type];
              if (!entry) {
                {
                  console.error(("[vuex] unknown action type: " + type));
                }
                return
              }

              try {
                this._actionSubscribers
                  .filter(function (sub) { return sub.before; })
                  .forEach(function (sub) { return sub.before(action, this$1.state); });
              } catch (e) {
                {
                  console.warn("[vuex] error in before action subscribers: ");
                  console.error(e);
                }
              }

              var result = entry.length > 1
                ? Promise.all(entry.map(function (handler) { return handler(payload); }))
                : entry[0](payload);

              return result.then(function (res) {
                try {
                  this$1._actionSubscribers
                    .filter(function (sub) { return sub.after; })
                    .forEach(function (sub) { return sub.after(action, this$1.state); });
                } catch (e) {
                  {
                    console.warn("[vuex] error in after action subscribers: ");
                    console.error(e);
                  }
                }
                return res
              })
            };

            Store.prototype.subscribe = function subscribe (fn) {
              return genericSubscribe(fn, this._subscribers)
            };

            Store.prototype.subscribeAction = function subscribeAction (fn) {
              var subs = typeof fn === 'function' ? { before: fn } : fn;
              return genericSubscribe(subs, this._actionSubscribers)
            };

            Store.prototype.watch = function watch (getter, cb, options) {
                var this$1 = this;

              {
                assert(typeof getter === 'function', "store.watch only accepts a function.");
              }
              return this._watcherVM.$watch(function () { return getter(this$1.state, this$1.getters); }, cb, options)
            };

            Store.prototype.replaceState = function replaceState (state) {
                var this$1 = this;

              this._withCommit(function () {
                this$1._vm._data.$$state = state;
              });
            };

            Store.prototype.registerModule = function registerModule (path, rawModule, options) {
                if ( options === void 0 ) options = {};

              if (typeof path === 'string') { path = [path]; }

              {
                assert(Array.isArray(path), "module path must be a string or an Array.");
                assert(path.length > 0, 'cannot register the root module by using registerModule.');
              }

              this._modules.register(path, rawModule);
              installModule(this, this.state, path, this._modules.get(path), options.preserveState);
              // reset store to update getters...
              resetStoreVM(this, this.state);
            };

            Store.prototype.unregisterModule = function unregisterModule (path) {
                var this$1 = this;

              if (typeof path === 'string') { path = [path]; }

              {
                assert(Array.isArray(path), "module path must be a string or an Array.");
              }

              this._modules.unregister(path);
              this._withCommit(function () {
                var parentState = getNestedState(this$1.state, path.slice(0, -1));
                Vue$1.delete(parentState, path[path.length - 1]);
              });
              resetStore(this);
            };

            Store.prototype.hotUpdate = function hotUpdate (newOptions) {
              this._modules.update(newOptions);
              resetStore(this, true);
            };

            Store.prototype._withCommit = function _withCommit (fn) {
              var committing = this._committing;
              this._committing = true;
              fn();
              this._committing = committing;
            };

            Object.defineProperties( Store.prototype, prototypeAccessors$1 );

            function genericSubscribe (fn, subs) {
              if (subs.indexOf(fn) < 0) {
                subs.push(fn);
              }
              return function () {
                var i = subs.indexOf(fn);
                if (i > -1) {
                  subs.splice(i, 1);
                }
              }
            }

            function resetStore (store, hot) {
              store._actions = Object.create(null);
              store._mutations = Object.create(null);
              store._wrappedGetters = Object.create(null);
              store._modulesNamespaceMap = Object.create(null);
              var state = store.state;
              // init all modules
              installModule(store, state, [], store._modules.root, true);
              // reset vm
              resetStoreVM(store, state, hot);
            }

            function resetStoreVM (store, state, hot) {
              var oldVm = store._vm;

              // bind store public getters
              store.getters = {};
              var wrappedGetters = store._wrappedGetters;
              var computed = {};
              forEachValue(wrappedGetters, function (fn, key) {
                // use computed to leverage its lazy-caching mechanism
                computed[key] = function () { return fn(store); };
                Object.defineProperty(store.getters, key, {
                  get: function () { return store._vm[key]; },
                  enumerable: true // for local getters
                });
              });

              // use a Vue instance to store the state tree
              // suppress warnings just in case the user has added
              // some funky global mixins
              var silent = Vue$1.config.silent;
              Vue$1.config.silent = true;
              store._vm = new Vue$1({
                data: {
                  $$state: state
                },
                computed: computed
              });
              Vue$1.config.silent = silent;

              // enable strict mode for new vm
              if (store.strict) {
                enableStrictMode(store);
              }

              if (oldVm) {
                if (hot) {
                  // dispatch changes in all subscribed watchers
                  // to force getter re-evaluation for hot reloading.
                  store._withCommit(function () {
                    oldVm._data.$$state = null;
                  });
                }
                Vue$1.nextTick(function () { return oldVm.$destroy(); });
              }
            }

            function installModule (store, rootState, path, module, hot) {
              var isRoot = !path.length;
              var namespace = store._modules.getNamespace(path);

              // register in namespace map
              if (module.namespaced) {
                store._modulesNamespaceMap[namespace] = module;
              }

              // set state
              if (!isRoot && !hot) {
                var parentState = getNestedState(rootState, path.slice(0, -1));
                var moduleName = path[path.length - 1];
                store._withCommit(function () {
                  Vue$1.set(parentState, moduleName, module.state);
                });
              }

              var local = module.context = makeLocalContext(store, namespace, path);

              module.forEachMutation(function (mutation, key) {
                var namespacedType = namespace + key;
                registerMutation(store, namespacedType, mutation, local);
              });

              module.forEachAction(function (action, key) {
                var type = action.root ? key : namespace + key;
                var handler = action.handler || action;
                registerAction(store, type, handler, local);
              });

              module.forEachGetter(function (getter, key) {
                var namespacedType = namespace + key;
                registerGetter(store, namespacedType, getter, local);
              });

              module.forEachChild(function (child, key) {
                installModule(store, rootState, path.concat(key), child, hot);
              });
            }

            /**
             * make localized dispatch, commit, getters and state
             * if there is no namespace, just use root ones
             */
            function makeLocalContext (store, namespace, path) {
              var noNamespace = namespace === '';

              var local = {
                dispatch: noNamespace ? store.dispatch : function (_type, _payload, _options) {
                  var args = unifyObjectStyle(_type, _payload, _options);
                  var payload = args.payload;
                  var options = args.options;
                  var type = args.type;

                  if (!options || !options.root) {
                    type = namespace + type;
                    if (!store._actions[type]) {
                      console.error(("[vuex] unknown local action type: " + (args.type) + ", global type: " + type));
                      return
                    }
                  }

                  return store.dispatch(type, payload)
                },

                commit: noNamespace ? store.commit : function (_type, _payload, _options) {
                  var args = unifyObjectStyle(_type, _payload, _options);
                  var payload = args.payload;
                  var options = args.options;
                  var type = args.type;

                  if (!options || !options.root) {
                    type = namespace + type;
                    if (!store._mutations[type]) {
                      console.error(("[vuex] unknown local mutation type: " + (args.type) + ", global type: " + type));
                      return
                    }
                  }

                  store.commit(type, payload, options);
                }
              };

              // getters and state object must be gotten lazily
              // because they will be changed by vm update
              Object.defineProperties(local, {
                getters: {
                  get: noNamespace
                    ? function () { return store.getters; }
                    : function () { return makeLocalGetters(store, namespace); }
                },
                state: {
                  get: function () { return getNestedState(store.state, path); }
                }
              });

              return local
            }

            function makeLocalGetters (store, namespace) {
              var gettersProxy = {};

              var splitPos = namespace.length;
              Object.keys(store.getters).forEach(function (type) {
                // skip if the target getter is not match this namespace
                if (type.slice(0, splitPos) !== namespace) { return }

                // extract local getter type
                var localType = type.slice(splitPos);

                // Add a port to the getters proxy.
                // Define as getter property because
                // we do not want to evaluate the getters in this time.
                Object.defineProperty(gettersProxy, localType, {
                  get: function () { return store.getters[type]; },
                  enumerable: true
                });
              });

              return gettersProxy
            }

            function registerMutation (store, type, handler, local) {
              var entry = store._mutations[type] || (store._mutations[type] = []);
              entry.push(function wrappedMutationHandler (payload) {
                handler.call(store, local.state, payload);
              });
            }

            function registerAction (store, type, handler, local) {
              var entry = store._actions[type] || (store._actions[type] = []);
              entry.push(function wrappedActionHandler (payload, cb) {
                var res = handler.call(store, {
                  dispatch: local.dispatch,
                  commit: local.commit,
                  getters: local.getters,
                  state: local.state,
                  rootGetters: store.getters,
                  rootState: store.state
                }, payload, cb);
                if (!isPromise(res)) {
                  res = Promise.resolve(res);
                }
                if (store._devtoolHook) {
                  return res.catch(function (err) {
                    store._devtoolHook.emit('vuex:error', err);
                    throw err
                  })
                } else {
                  return res
                }
              });
            }

            function registerGetter (store, type, rawGetter, local) {
              if (store._wrappedGetters[type]) {
                {
                  console.error(("[vuex] duplicate getter key: " + type));
                }
                return
              }
              store._wrappedGetters[type] = function wrappedGetter (store) {
                return rawGetter(
                  local.state, // local state
                  local.getters, // local getters
                  store.state, // root state
                  store.getters // root getters
                )
              };
            }

            function enableStrictMode (store) {
              store._vm.$watch(function () { return this._data.$$state }, function () {
                {
                  assert(store._committing, "do not mutate vuex store state outside mutation handlers.");
                }
              }, { deep: true, sync: true });
            }

            function getNestedState (state, path) {
              return path.length
                ? path.reduce(function (state, key) { return state[key]; }, state)
                : state
            }

            function unifyObjectStyle (type, payload, options) {
              if (isObject(type) && type.type) {
                options = payload;
                payload = type;
                type = type.type;
              }

              {
                assert(typeof type === 'string', ("expects string as the type, but found " + (typeof type) + "."));
              }

              return { type: type, payload: payload, options: options }
            }

            function install (_Vue) {
              if (Vue$1 && _Vue === Vue$1) {
                {
                  console.error(
                    '[vuex] already installed. Vue.use(Vuex) should be called only once.'
                  );
                }
                return
              }
              Vue$1 = _Vue;
              applyMixin(Vue$1);
            }

            /**
             * Reduce the code which written in Vue.js for getting the state.
             * @param {String} [namespace] - Module's namespace
             * @param {Object|Array} states # Object's item can be a function which accept state and getters for param, you can do something for state and getters in it.
             * @param {Object}
             */
            var mapState = normalizeNamespace(function (namespace, states) {
              var res = {};
              normalizeMap(states).forEach(function (ref) {
                var key = ref.key;
                var val = ref.val;

                res[key] = function mappedState () {
                  var state = this.$store.state;
                  var getters = this.$store.getters;
                  if (namespace) {
                    var module = getModuleByNamespace(this.$store, 'mapState', namespace);
                    if (!module) {
                      return
                    }
                    state = module.context.state;
                    getters = module.context.getters;
                  }
                  return typeof val === 'function'
                    ? val.call(this, state, getters)
                    : state[val]
                };
                // mark vuex getter for devtools
                res[key].vuex = true;
              });
              return res
            });

            /**
             * Reduce the code which written in Vue.js for committing the mutation
             * @param {String} [namespace] - Module's namespace
             * @param {Object|Array} mutations # Object's item can be a function which accept `commit` function as the first param, it can accept anthor params. You can commit mutation and do any other things in this function. specially, You need to pass anthor params from the mapped function.
             * @return {Object}
             */
            var mapMutations = normalizeNamespace(function (namespace, mutations) {
              var res = {};
              normalizeMap(mutations).forEach(function (ref) {
                var key = ref.key;
                var val = ref.val;

                res[key] = function mappedMutation () {
                  var args = [], len = arguments.length;
                  while ( len-- ) args[ len ] = arguments[ len ];

                  // Get the commit method from store
                  var commit = this.$store.commit;
                  if (namespace) {
                    var module = getModuleByNamespace(this.$store, 'mapMutations', namespace);
                    if (!module) {
                      return
                    }
                    commit = module.context.commit;
                  }
                  return typeof val === 'function'
                    ? val.apply(this, [commit].concat(args))
                    : commit.apply(this.$store, [val].concat(args))
                };
              });
              return res
            });

            /**
             * Reduce the code which written in Vue.js for getting the getters
             * @param {String} [namespace] - Module's namespace
             * @param {Object|Array} getters
             * @return {Object}
             */
            var mapGetters = normalizeNamespace(function (namespace, getters) {
              var res = {};
              normalizeMap(getters).forEach(function (ref) {
                var key = ref.key;
                var val = ref.val;

                // The namespace has been mutated by normalizeNamespace
                val = namespace + val;
                res[key] = function mappedGetter () {
                  if (namespace && !getModuleByNamespace(this.$store, 'mapGetters', namespace)) {
                    return
                  }
                  if (!(val in this.$store.getters)) {
                    console.error(("[vuex] unknown getter: " + val));
                    return
                  }
                  return this.$store.getters[val]
                };
                // mark vuex getter for devtools
                res[key].vuex = true;
              });
              return res
            });

            /**
             * Reduce the code which written in Vue.js for dispatch the action
             * @param {String} [namespace] - Module's namespace
             * @param {Object|Array} actions # Object's item can be a function which accept `dispatch` function as the first param, it can accept anthor params. You can dispatch action and do any other things in this function. specially, You need to pass anthor params from the mapped function.
             * @return {Object}
             */
            var mapActions = normalizeNamespace(function (namespace, actions) {
              var res = {};
              normalizeMap(actions).forEach(function (ref) {
                var key = ref.key;
                var val = ref.val;

                res[key] = function mappedAction () {
                  var args = [], len = arguments.length;
                  while ( len-- ) args[ len ] = arguments[ len ];

                  // get dispatch function from store
                  var dispatch = this.$store.dispatch;
                  if (namespace) {
                    var module = getModuleByNamespace(this.$store, 'mapActions', namespace);
                    if (!module) {
                      return
                    }
                    dispatch = module.context.dispatch;
                  }
                  return typeof val === 'function'
                    ? val.apply(this, [dispatch].concat(args))
                    : dispatch.apply(this.$store, [val].concat(args))
                };
              });
              return res
            });

            /**
             * Rebinding namespace param for mapXXX function in special scoped, and return them by simple object
             * @param {String} namespace
             * @return {Object}
             */
            var createNamespacedHelpers = function (namespace) { return ({
              mapState: mapState.bind(null, namespace),
              mapGetters: mapGetters.bind(null, namespace),
              mapMutations: mapMutations.bind(null, namespace),
              mapActions: mapActions.bind(null, namespace)
            }); };

            /**
             * Normalize the map
             * normalizeMap([1, 2, 3]) => [ { key: 1, val: 1 }, { key: 2, val: 2 }, { key: 3, val: 3 } ]
             * normalizeMap({a: 1, b: 2, c: 3}) => [ { key: 'a', val: 1 }, { key: 'b', val: 2 }, { key: 'c', val: 3 } ]
             * @param {Array|Object} map
             * @return {Object}
             */
            function normalizeMap (map) {
              return Array.isArray(map)
                ? map.map(function (key) { return ({ key: key, val: key }); })
                : Object.keys(map).map(function (key) { return ({ key: key, val: map[key] }); })
            }

            /**
             * Return a function expect two param contains namespace and map. it will normalize the namespace and then the param's function will handle the new namespace and the map.
             * @param {Function} fn
             * @return {Function}
             */
            function normalizeNamespace (fn) {
              return function (namespace, map) {
                if (typeof namespace !== 'string') {
                  map = namespace;
                  namespace = '';
                } else if (namespace.charAt(namespace.length - 1) !== '/') {
                  namespace += '/';
                }
                return fn(namespace, map)
              }
            }

            /**
             * Search a special module from store by namespace. if module not exist, print error message.
             * @param {Object} store
             * @param {String} helper
             * @param {String} namespace
             * @return {Object}
             */
            function getModuleByNamespace (store, helper, namespace) {
              var module = store._modulesNamespaceMap[namespace];
              if (!module) {
                console.error(("[vuex] module namespace not found in " + helper + "(): " + namespace));
              }
              return module
            }

            var index_esm = {
              Store: Store,
              install: install,
              version: '3.1.0',
              mapState: mapState,
              mapMutations: mapMutations,
              mapGetters: mapGetters,
              mapActions: mapActions,
              createNamespacedHelpers: createNamespacedHelpers
            };

            var has = Object.prototype.hasOwnProperty;
            var isArray = Array.isArray;

            var hexTable = (function () {
                var array = [];
                for (var i = 0; i < 256; ++i) {
                    array.push('%' + ((i < 16 ? '0' : '') + i.toString(16)).toUpperCase());
                }

                return array;
            }());

            var compactQueue = function compactQueue(queue) {
                while (queue.length > 1) {
                    var item = queue.pop();
                    var obj = item.obj[item.prop];

                    if (isArray(obj)) {
                        var compacted = [];

                        for (var j = 0; j < obj.length; ++j) {
                            if (typeof obj[j] !== 'undefined') {
                                compacted.push(obj[j]);
                            }
                        }

                        item.obj[item.prop] = compacted;
                    }
                }
            };

            var arrayToObject = function arrayToObject(source, options) {
                var obj = options && options.plainObjects ? Object.create(null) : {};
                for (var i = 0; i < source.length; ++i) {
                    if (typeof source[i] !== 'undefined') {
                        obj[i] = source[i];
                    }
                }

                return obj;
            };

            var merge = function merge(target, source, options) {
                if (!source) {
                    return target;
                }

                if (typeof source !== 'object') {
                    if (isArray(target)) {
                        target.push(source);
                    } else if (target && typeof target === 'object') {
                        if ((options && (options.plainObjects || options.allowPrototypes)) || !has.call(Object.prototype, source)) {
                            target[source] = true;
                        }
                    } else {
                        return [target, source];
                    }

                    return target;
                }

                if (!target || typeof target !== 'object') {
                    return [target].concat(source);
                }

                var mergeTarget = target;
                if (isArray(target) && !isArray(source)) {
                    mergeTarget = arrayToObject(target, options);
                }

                if (isArray(target) && isArray(source)) {
                    source.forEach(function (item, i) {
                        if (has.call(target, i)) {
                            var targetItem = target[i];
                            if (targetItem && typeof targetItem === 'object' && item && typeof item === 'object') {
                                target[i] = merge(targetItem, item, options);
                            } else {
                                target.push(item);
                            }
                        } else {
                            target[i] = item;
                        }
                    });
                    return target;
                }

                return Object.keys(source).reduce(function (acc, key) {
                    var value = source[key];

                    if (has.call(acc, key)) {
                        acc[key] = merge(acc[key], value, options);
                    } else {
                        acc[key] = value;
                    }
                    return acc;
                }, mergeTarget);
            };

            var assign = function assignSingleSource(target, source) {
                return Object.keys(source).reduce(function (acc, key) {
                    acc[key] = source[key];
                    return acc;
                }, target);
            };

            var decode = function (str, decoder, charset) {
                var strWithoutPlus = str.replace(/\+/g, ' ');
                if (charset === 'iso-8859-1') {
                    // unescape never throws, no try...catch needed:
                    return strWithoutPlus.replace(/%[0-9a-f]{2}/gi, unescape);
                }
                // utf-8
                try {
                    return decodeURIComponent(strWithoutPlus);
                } catch (e) {
                    return strWithoutPlus;
                }
            };

            var encode = function encode(str, defaultEncoder, charset) {
                // This code was originally written by Brian White (mscdex) for the io.js core querystring library.
                // It has been adapted here for stricter adherence to RFC 3986
                if (str.length === 0) {
                    return str;
                }

                var string = typeof str === 'string' ? str : String(str);

                if (charset === 'iso-8859-1') {
                    return escape(string).replace(/%u[0-9a-f]{4}/gi, function ($0) {
                        return '%26%23' + parseInt($0.slice(2), 16) + '%3B';
                    });
                }

                var out = '';
                for (var i = 0; i < string.length; ++i) {
                    var c = string.charCodeAt(i);

                    if (
                        c === 0x2D // -
                        || c === 0x2E // .
                        || c === 0x5F // _
                        || c === 0x7E // ~
                        || (c >= 0x30 && c <= 0x39) // 0-9
                        || (c >= 0x41 && c <= 0x5A) // a-z
                        || (c >= 0x61 && c <= 0x7A) // A-Z
                    ) {
                        out += string.charAt(i);
                        continue;
                    }

                    if (c < 0x80) {
                        out = out + hexTable[c];
                        continue;
                    }

                    if (c < 0x800) {
                        out = out + (hexTable[0xC0 | (c >> 6)] + hexTable[0x80 | (c & 0x3F)]);
                        continue;
                    }

                    if (c < 0xD800 || c >= 0xE000) {
                        out = out + (hexTable[0xE0 | (c >> 12)] + hexTable[0x80 | ((c >> 6) & 0x3F)] + hexTable[0x80 | (c & 0x3F)]);
                        continue;
                    }

                    i += 1;
                    c = 0x10000 + (((c & 0x3FF) << 10) | (string.charCodeAt(i) & 0x3FF));
                    out += hexTable[0xF0 | (c >> 18)]
                        + hexTable[0x80 | ((c >> 12) & 0x3F)]
                        + hexTable[0x80 | ((c >> 6) & 0x3F)]
                        + hexTable[0x80 | (c & 0x3F)];
                }

                return out;
            };

            var compact = function compact(value) {
                var queue = [{ obj: { o: value }, prop: 'o' }];
                var refs = [];

                for (var i = 0; i < queue.length; ++i) {
                    var item = queue[i];
                    var obj = item.obj[item.prop];

                    var keys = Object.keys(obj);
                    for (var j = 0; j < keys.length; ++j) {
                        var key = keys[j];
                        var val = obj[key];
                        if (typeof val === 'object' && val !== null && refs.indexOf(val) === -1) {
                            queue.push({ obj: obj, prop: key });
                            refs.push(val);
                        }
                    }
                }

                compactQueue(queue);

                return value;
            };

            var isRegExp = function isRegExp(obj) {
                return Object.prototype.toString.call(obj) === '[object RegExp]';
            };

            var isBuffer = function isBuffer(obj) {
                if (!obj || typeof obj !== 'object') {
                    return false;
                }

                return !!(obj.constructor && obj.constructor.isBuffer && obj.constructor.isBuffer(obj));
            };

            var combine = function combine(a, b) {
                return [].concat(a, b);
            };

            var utils = {
                arrayToObject: arrayToObject,
                assign: assign,
                combine: combine,
                compact: compact,
                decode: decode,
                encode: encode,
                isBuffer: isBuffer,
                isRegExp: isRegExp,
                merge: merge
            };

            var replace = String.prototype.replace;
            var percentTwenties = /%20/g;

            var formats = {
                'default': 'RFC3986',
                formatters: {
                    RFC1738: function (value) {
                        return replace.call(value, percentTwenties, '+');
                    },
                    RFC3986: function (value) {
                        return value;
                    }
                },
                RFC1738: 'RFC1738',
                RFC3986: 'RFC3986'
            };

            var has$1 = Object.prototype.hasOwnProperty;

            var arrayPrefixGenerators = {
                brackets: function brackets(prefix) { // eslint-disable-line func-name-matching
                    return prefix + '[]';
                },
                comma: 'comma',
                indices: function indices(prefix, key) { // eslint-disable-line func-name-matching
                    return prefix + '[' + key + ']';
                },
                repeat: function repeat(prefix) { // eslint-disable-line func-name-matching
                    return prefix;
                }
            };

            var isArray$1 = Array.isArray;
            var push = Array.prototype.push;
            var pushToArray = function (arr, valueOrArray) {
                push.apply(arr, isArray$1(valueOrArray) ? valueOrArray : [valueOrArray]);
            };

            var toISO = Date.prototype.toISOString;

            var defaults = {
                addQueryPrefix: false,
                allowDots: false,
                charset: 'utf-8',
                charsetSentinel: false,
                delimiter: '&',
                encode: true,
                encoder: utils.encode,
                encodeValuesOnly: false,
                formatter: formats.formatters[formats['default']],
                // deprecated
                indices: false,
                serializeDate: function serializeDate(date) { // eslint-disable-line func-name-matching
                    return toISO.call(date);
                },
                skipNulls: false,
                strictNullHandling: false
            };

            var stringify = function stringify( // eslint-disable-line func-name-matching
                object,
                prefix,
                generateArrayPrefix,
                strictNullHandling,
                skipNulls,
                encoder,
                filter,
                sort,
                allowDots,
                serializeDate,
                formatter,
                encodeValuesOnly,
                charset
            ) {
                var obj = object;
                if (typeof filter === 'function') {
                    obj = filter(prefix, obj);
                } else if (obj instanceof Date) {
                    obj = serializeDate(obj);
                } else if (generateArrayPrefix === 'comma' && isArray$1(obj)) {
                    obj = obj.join(',');
                }

                if (obj === null) {
                    if (strictNullHandling) {
                        return encoder && !encodeValuesOnly ? encoder(prefix, defaults.encoder, charset) : prefix;
                    }

                    obj = '';
                }

                if (typeof obj === 'string' || typeof obj === 'number' || typeof obj === 'boolean' || utils.isBuffer(obj)) {
                    if (encoder) {
                        var keyValue = encodeValuesOnly ? prefix : encoder(prefix, defaults.encoder, charset);
                        return [formatter(keyValue) + '=' + formatter(encoder(obj, defaults.encoder, charset))];
                    }
                    return [formatter(prefix) + '=' + formatter(String(obj))];
                }

                var values = [];

                if (typeof obj === 'undefined') {
                    return values;
                }

                var objKeys;
                if (isArray$1(filter)) {
                    objKeys = filter;
                } else {
                    var keys = Object.keys(obj);
                    objKeys = sort ? keys.sort(sort) : keys;
                }

                for (var i = 0; i < objKeys.length; ++i) {
                    var key = objKeys[i];

                    if (skipNulls && obj[key] === null) {
                        continue;
                    }

                    if (isArray$1(obj)) {
                        pushToArray(values, stringify(
                            obj[key],
                            typeof generateArrayPrefix === 'function' ? generateArrayPrefix(prefix, key) : prefix,
                            generateArrayPrefix,
                            strictNullHandling,
                            skipNulls,
                            encoder,
                            filter,
                            sort,
                            allowDots,
                            serializeDate,
                            formatter,
                            encodeValuesOnly,
                            charset
                        ));
                    } else {
                        pushToArray(values, stringify(
                            obj[key],
                            prefix + (allowDots ? '.' + key : '[' + key + ']'),
                            generateArrayPrefix,
                            strictNullHandling,
                            skipNulls,
                            encoder,
                            filter,
                            sort,
                            allowDots,
                            serializeDate,
                            formatter,
                            encodeValuesOnly,
                            charset
                        ));
                    }
                }

                return values;
            };

            var normalizeStringifyOptions = function normalizeStringifyOptions(opts) {
                if (!opts) {
                    return defaults;
                }

                if (opts.encoder !== null && opts.encoder !== undefined && typeof opts.encoder !== 'function') {
                    throw new TypeError('Encoder has to be a function.');
                }

                var charset = opts.charset || defaults.charset;
                if (typeof opts.charset !== 'undefined' && opts.charset !== 'utf-8' && opts.charset !== 'iso-8859-1') {
                    throw new TypeError('The charset option must be either utf-8, iso-8859-1, or undefined');
                }

                var format = formats['default'];
                if (typeof opts.format !== 'undefined') {
                    if (!has$1.call(formats.formatters, opts.format)) {
                        throw new TypeError('Unknown format option provided.');
                    }
                    format = opts.format;
                }
                var formatter = formats.formatters[format];

                var filter = defaults.filter;
                if (typeof opts.filter === 'function' || isArray$1(opts.filter)) {
                    filter = opts.filter;
                }

                return {
                    addQueryPrefix: typeof opts.addQueryPrefix === 'boolean' ? opts.addQueryPrefix : defaults.addQueryPrefix,
                    allowDots: typeof opts.allowDots === 'undefined' ? defaults.allowDots : !!opts.allowDots,
                    charset: charset,
                    charsetSentinel: typeof opts.charsetSentinel === 'boolean' ? opts.charsetSentinel : defaults.charsetSentinel,
                    delimiter: typeof opts.delimiter === 'undefined' ? defaults.delimiter : opts.delimiter,
                    encode: typeof opts.encode === 'boolean' ? opts.encode : defaults.encode,
                    encoder: typeof opts.encoder === 'function' ? opts.encoder : defaults.encoder,
                    encodeValuesOnly: typeof opts.encodeValuesOnly === 'boolean' ? opts.encodeValuesOnly : defaults.encodeValuesOnly,
                    filter: filter,
                    formatter: formatter,
                    serializeDate: typeof opts.serializeDate === 'function' ? opts.serializeDate : defaults.serializeDate,
                    skipNulls: typeof opts.skipNulls === 'boolean' ? opts.skipNulls : defaults.skipNulls,
                    sort: typeof opts.sort === 'function' ? opts.sort : null,
                    strictNullHandling: typeof opts.strictNullHandling === 'boolean' ? opts.strictNullHandling : defaults.strictNullHandling
                };
            };

            var stringify_1 = function (object, opts) {
                var obj = object;
                var options = normalizeStringifyOptions(opts);

                var objKeys;
                var filter;

                if (typeof options.filter === 'function') {
                    filter = options.filter;
                    obj = filter('', obj);
                } else if (isArray$1(options.filter)) {
                    filter = options.filter;
                    objKeys = filter;
                }

                var keys = [];

                if (typeof obj !== 'object' || obj === null) {
                    return '';
                }

                var arrayFormat;
                if (opts && opts.arrayFormat in arrayPrefixGenerators) {
                    arrayFormat = opts.arrayFormat;
                } else if (opts && 'indices' in opts) {
                    arrayFormat = opts.indices ? 'indices' : 'repeat';
                } else {
                    arrayFormat = 'indices';
                }

                var generateArrayPrefix = arrayPrefixGenerators[arrayFormat];

                if (!objKeys) {
                    objKeys = Object.keys(obj);
                }

                if (options.sort) {
                    objKeys.sort(options.sort);
                }

                for (var i = 0; i < objKeys.length; ++i) {
                    var key = objKeys[i];

                    if (options.skipNulls && obj[key] === null) {
                        continue;
                    }
                    pushToArray(keys, stringify(
                        obj[key],
                        key,
                        generateArrayPrefix,
                        options.strictNullHandling,
                        options.skipNulls,
                        options.encode ? options.encoder : null,
                        options.filter,
                        options.sort,
                        options.allowDots,
                        options.serializeDate,
                        options.formatter,
                        options.encodeValuesOnly,
                        options.charset
                    ));
                }

                var joined = keys.join(options.delimiter);
                var prefix = options.addQueryPrefix === true ? '?' : '';

                if (options.charsetSentinel) {
                    if (options.charset === 'iso-8859-1') {
                        // encodeURIComponent('&#10003;'), the "numeric entity" representation of a checkmark
                        prefix += 'utf8=%26%2310003%3B&';
                    } else {
                        // encodeURIComponent('✓')
                        prefix += 'utf8=%E2%9C%93&';
                    }
                }

                return joined.length > 0 ? prefix + joined : '';
            };

            var has$2 = Object.prototype.hasOwnProperty;

            var defaults$1 = {
                allowDots: false,
                allowPrototypes: false,
                arrayLimit: 20,
                charset: 'utf-8',
                charsetSentinel: false,
                comma: false,
                decoder: utils.decode,
                delimiter: '&',
                depth: 5,
                ignoreQueryPrefix: false,
                interpretNumericEntities: false,
                parameterLimit: 1000,
                parseArrays: true,
                plainObjects: false,
                strictNullHandling: false
            };

            var interpretNumericEntities = function (str) {
                return str.replace(/&#(\d+);/g, function ($0, numberStr) {
                    return String.fromCharCode(parseInt(numberStr, 10));
                });
            };

            // This is what browsers will submit when the ✓ character occurs in an
            // application/x-www-form-urlencoded body and the encoding of the page containing
            // the form is iso-8859-1, or when the submitted form has an accept-charset
            // attribute of iso-8859-1. Presumably also with other charsets that do not contain
            // the ✓ character, such as us-ascii.
            var isoSentinel = 'utf8=%26%2310003%3B'; // encodeURIComponent('&#10003;')

            // These are the percent-encoded utf-8 octets representing a checkmark, indicating that the request actually is utf-8 encoded.
            var charsetSentinel = 'utf8=%E2%9C%93'; // encodeURIComponent('✓')

            var parseValues = function parseQueryStringValues(str, options) {
                var obj = {};
                var cleanStr = options.ignoreQueryPrefix ? str.replace(/^\?/, '') : str;
                var limit = options.parameterLimit === Infinity ? undefined : options.parameterLimit;
                var parts = cleanStr.split(options.delimiter, limit);
                var skipIndex = -1; // Keep track of where the utf8 sentinel was found
                var i;

                var charset = options.charset;
                if (options.charsetSentinel) {
                    for (i = 0; i < parts.length; ++i) {
                        if (parts[i].indexOf('utf8=') === 0) {
                            if (parts[i] === charsetSentinel) {
                                charset = 'utf-8';
                            } else if (parts[i] === isoSentinel) {
                                charset = 'iso-8859-1';
                            }
                            skipIndex = i;
                            i = parts.length; // The eslint settings do not allow break;
                        }
                    }
                }

                for (i = 0; i < parts.length; ++i) {
                    if (i === skipIndex) {
                        continue;
                    }
                    var part = parts[i];

                    var bracketEqualsPos = part.indexOf(']=');
                    var pos = bracketEqualsPos === -1 ? part.indexOf('=') : bracketEqualsPos + 1;

                    var key, val;
                    if (pos === -1) {
                        key = options.decoder(part, defaults$1.decoder, charset);
                        val = options.strictNullHandling ? null : '';
                    } else {
                        key = options.decoder(part.slice(0, pos), defaults$1.decoder, charset);
                        val = options.decoder(part.slice(pos + 1), defaults$1.decoder, charset);
                    }

                    if (val && options.interpretNumericEntities && charset === 'iso-8859-1') {
                        val = interpretNumericEntities(val);
                    }

                    if (val && options.comma && val.indexOf(',') > -1) {
                        val = val.split(',');
                    }

                    if (has$2.call(obj, key)) {
                        obj[key] = utils.combine(obj[key], val);
                    } else {
                        obj[key] = val;
                    }
                }

                return obj;
            };

            var parseObject = function (chain, val, options) {
                var leaf = val;

                for (var i = chain.length - 1; i >= 0; --i) {
                    var obj;
                    var root = chain[i];

                    if (root === '[]' && options.parseArrays) {
                        obj = [].concat(leaf);
                    } else {
                        obj = options.plainObjects ? Object.create(null) : {};
                        var cleanRoot = root.charAt(0) === '[' && root.charAt(root.length - 1) === ']' ? root.slice(1, -1) : root;
                        var index = parseInt(cleanRoot, 10);
                        if (!options.parseArrays && cleanRoot === '') {
                            obj = { 0: leaf };
                        } else if (
                            !isNaN(index)
                            && root !== cleanRoot
                            && String(index) === cleanRoot
                            && index >= 0
                            && (options.parseArrays && index <= options.arrayLimit)
                        ) {
                            obj = [];
                            obj[index] = leaf;
                        } else {
                            obj[cleanRoot] = leaf;
                        }
                    }

                    leaf = obj;
                }

                return leaf;
            };

            var parseKeys = function parseQueryStringKeys(givenKey, val, options) {
                if (!givenKey) {
                    return;
                }

                // Transform dot notation to bracket notation
                var key = options.allowDots ? givenKey.replace(/\.([^.[]+)/g, '[$1]') : givenKey;

                // The regex chunks

                var brackets = /(\[[^[\]]*])/;
                var child = /(\[[^[\]]*])/g;

                // Get the parent

                var segment = brackets.exec(key);
                var parent = segment ? key.slice(0, segment.index) : key;

                // Stash the parent if it exists

                var keys = [];
                if (parent) {
                    // If we aren't using plain objects, optionally prefix keys that would overwrite object prototype properties
                    if (!options.plainObjects && has$2.call(Object.prototype, parent)) {
                        if (!options.allowPrototypes) {
                            return;
                        }
                    }

                    keys.push(parent);
                }

                // Loop through children appending to the array until we hit depth

                var i = 0;
                while ((segment = child.exec(key)) !== null && i < options.depth) {
                    i += 1;
                    if (!options.plainObjects && has$2.call(Object.prototype, segment[1].slice(1, -1))) {
                        if (!options.allowPrototypes) {
                            return;
                        }
                    }
                    keys.push(segment[1]);
                }

                // If there's a remainder, just add whatever is left

                if (segment) {
                    keys.push('[' + key.slice(segment.index) + ']');
                }

                return parseObject(keys, val, options);
            };

            var normalizeParseOptions = function normalizeParseOptions(opts) {
                if (!opts) {
                    return defaults$1;
                }

                if (opts.decoder !== null && opts.decoder !== undefined && typeof opts.decoder !== 'function') {
                    throw new TypeError('Decoder has to be a function.');
                }

                if (typeof opts.charset !== 'undefined' && opts.charset !== 'utf-8' && opts.charset !== 'iso-8859-1') {
                    throw new Error('The charset option must be either utf-8, iso-8859-1, or undefined');
                }
                var charset = typeof opts.charset === 'undefined' ? defaults$1.charset : opts.charset;

                return {
                    allowDots: typeof opts.allowDots === 'undefined' ? defaults$1.allowDots : !!opts.allowDots,
                    allowPrototypes: typeof opts.allowPrototypes === 'boolean' ? opts.allowPrototypes : defaults$1.allowPrototypes,
                    arrayLimit: typeof opts.arrayLimit === 'number' ? opts.arrayLimit : defaults$1.arrayLimit,
                    charset: charset,
                    charsetSentinel: typeof opts.charsetSentinel === 'boolean' ? opts.charsetSentinel : defaults$1.charsetSentinel,
                    comma: typeof opts.comma === 'boolean' ? opts.comma : defaults$1.comma,
                    decoder: typeof opts.decoder === 'function' ? opts.decoder : defaults$1.decoder,
                    delimiter: typeof opts.delimiter === 'string' || utils.isRegExp(opts.delimiter) ? opts.delimiter : defaults$1.delimiter,
                    depth: typeof opts.depth === 'number' ? opts.depth : defaults$1.depth,
                    ignoreQueryPrefix: opts.ignoreQueryPrefix === true,
                    interpretNumericEntities: typeof opts.interpretNumericEntities === 'boolean' ? opts.interpretNumericEntities : defaults$1.interpretNumericEntities,
                    parameterLimit: typeof opts.parameterLimit === 'number' ? opts.parameterLimit : defaults$1.parameterLimit,
                    parseArrays: opts.parseArrays !== false,
                    plainObjects: typeof opts.plainObjects === 'boolean' ? opts.plainObjects : defaults$1.plainObjects,
                    strictNullHandling: typeof opts.strictNullHandling === 'boolean' ? opts.strictNullHandling : defaults$1.strictNullHandling
                };
            };

            var parse = function (str, opts) {
                var options = normalizeParseOptions(opts);

                if (str === '' || str === null || typeof str === 'undefined') {
                    return options.plainObjects ? Object.create(null) : {};
                }

                var tempObj = typeof str === 'string' ? parseValues(str, options) : str;
                var obj = options.plainObjects ? Object.create(null) : {};

                // Iterate over the keys and setup the new object

                var keys = Object.keys(tempObj);
                for (var i = 0; i < keys.length; ++i) {
                    var key = keys[i];
                    var newObj = parseKeys(key, tempObj[key], options);
                    obj = utils.merge(obj, newObj, options);
                }

                return utils.compact(obj);
            };

            var lib = {
                formats: formats,
                parse: parse,
                stringify: stringify_1
            };

            const resource = '/api/search';
            const headers = {
              'Content-Type': 'application/json'
            };
            var SearchRepository = {
              doGlobalSearch(params) {
                let query = lib.stringify(params);
                console.log(query);
                return fetch(`${resource}/global?${query}`, {
                  'method': 'GET',
                  headers
                });
              }

            };

            const state = {
              result: [],
              status: ''
            };
            const getters = {
              searchResult: state => state.result,
              searchStatus: state => state.status
            };
            const actions = {
              SEARCH_REQUEST: ({
                commit
              }, params) => {
                return new Promise((resolve, reject) => {
                  commit('SEARCH_REQUEST');
                  SearchRepository.doGlobalSearch(params).then(result => result.json()).then(result => {
                    commit('SEARCH_SUCCESS', result);
                    resolve(result);
                  }).catch(error => {
                    commit('SEARCH_ERROR', error);
                    reject(error);
                  });
                });
              }
            };
            const mutations = {
              SEARCH_REQUEST: state => {
                state.status = 'loading';
              },
              SEARCH_SUCCESS: (state, result) => {
                state.status = 'success';
                state.result = result;
              },
              SEARCH_ERROR: (state, error) => {
                state.status = 'error';
                console.log('Search Error: ' + error);
              }
            };
            var search = {
              state,
              actions,
              getters,
              mutations
            };

            Vue$2.use(index_esm);
            var store = new index_esm.Store({
              modules: {
                search
              }
            });

            //
            var script = {
              name: 'Search',
              data: () => ({
                text: '',
                filterDifficulty: [],
                filterCore: []
              }),
              computed: {
                /**
                 * Gets results for search.
                 */
                result: function () {
                  return this.$store.getters['searchResult'];
                },

                /**
                 * Gets current status of the search.
                 */
                status: function () {
                  return this.$store.getters['searchStatus'];
                }
              },
              watch: {
                text: function () {
                  this.doSearch();
                },
                filterDifficulty: function () {
                  this.doSearch();
                },
                filterCore: function () {
                  this.doSearch();
                }
              },
              methods: {
                onFocus() {
                  this.focused = true;
                },

                onBlur() {
                  this.focused = false;
                },

                doSearch: Drupal.debounce(function () {
                  if (this.text.length >= 3 || this.filterDifficulty.length || this.filterCore.length) {
                    let params = {};

                    if (this.text.length >= 3) {
                      params = Object.assign(params, {
                        text: this.text
                      });
                    }

                    if (this.filterDifficulty.length) {
                      params = Object.assign(params, {
                        difficulty: this.filterDifficulty
                      });
                    }

                    if (this.filterCore.length) {
                      params = Object.assign(params, {
                        core: this.filterCore
                      });
                    }

                    this.$store.dispatch('SEARCH_REQUEST', params);
                    this.updateCurrentUrl();
                  }
                }, 300),

                updateCurrentUrl() {
                  const params = new URLSearchParams(location.search);

                  if (this.text.length >= 3) {
                    params.set('text', this.text);
                  } else {
                    params.delete('text');
                  }

                  if (this.filterDifficulty.length) {
                    params.set('difficulty', this.filterDifficulty);
                  } else {
                    params.delete('difficulty');
                  }

                  if (this.filterCore.length) {
                    params.set('core', this.filterCore);
                  } else {
                    params.delete('core');
                  }

                  window.history.replaceState({}, '', `${location.pathname}?${params}`);
                }

              },

              created() {
                const params = new URLSearchParams(location.search);

                if (Array.from(params).length) {
                  if (params.has('text') && params.get('text').length) {
                    this.text = params.get('text');
                  }

                  if (params.has('core') && params.get('core').length) {
                    this.filterCore = params.get('core').split(',');
                  }

                  if (params.has('difficulty') && params.get('difficulty').length) {
                    this.filterDifficulty = params.get('difficulty').split(',');
                  }

                  this.doSearch();
                }
              }

            };

            function normalizeComponent(template, style, script, scopeId, isFunctionalTemplate, moduleIdentifier
            /* server only */
            , shadowMode, createInjector, createInjectorSSR, createInjectorShadow) {
              if (typeof shadowMode !== 'boolean') {
                createInjectorSSR = createInjector;
                createInjector = shadowMode;
                shadowMode = false;
              } // Vue.extend constructor export interop.


              var options = typeof script === 'function' ? script.options : script; // render functions

              if (template && template.render) {
                options.render = template.render;
                options.staticRenderFns = template.staticRenderFns;
                options._compiled = true; // functional template

                if (isFunctionalTemplate) {
                  options.functional = true;
                }
              } // scopedId


              if (scopeId) {
                options._scopeId = scopeId;
              }

              var hook;

              if (moduleIdentifier) {
                // server build
                hook = function hook(context) {
                  // 2.3 injection
                  context = context || // cached call
                  this.$vnode && this.$vnode.ssrContext || // stateful
                  this.parent && this.parent.$vnode && this.parent.$vnode.ssrContext; // functional
                  // 2.2 with runInNewContext: true

                  if (!context && typeof __VUE_SSR_CONTEXT__ !== 'undefined') {
                    context = __VUE_SSR_CONTEXT__;
                  } // inject component styles


                  if (style) {
                    style.call(this, createInjectorSSR(context));
                  } // register component module identifier for async chunk inference


                  if (context && context._registeredComponents) {
                    context._registeredComponents.add(moduleIdentifier);
                  }
                }; // used by ssr in case component is cached and beforeCreate
                // never gets called


                options._ssrRegister = hook;
              } else if (style) {
                hook = shadowMode ? function () {
                  style.call(this, createInjectorShadow(this.$root.$options.shadowRoot));
                } : function (context) {
                  style.call(this, createInjector(context));
                };
              }

              if (hook) {
                if (options.functional) {
                  // register for functional component in vue file
                  var originalRender = options.render;

                  options.render = function renderWithStyleInjection(h, context) {
                    hook.call(context);
                    return originalRender(h, context);
                  };
                } else {
                  // inject component registration as beforeCreate hook
                  var existing = options.beforeCreate;
                  options.beforeCreate = existing ? [].concat(existing, hook) : [hook];
                }
              }

              return script;
            }

            var normalizeComponent_1 = normalizeComponent;

            /* script */
            const __vue_script__ = script;

            /* template */
            var __vue_render__ = function() {
              var _vm = this;
              var _h = _vm.$createElement;
              var _c = _vm._self._c || _h;
              return _c("div", { staticClass: "search" }, [
                _c("div", { staticClass: "search__input-pane" }, [
                  _c("input", {
                    directives: [
                      {
                        name: "model",
                        rawName: "v-model",
                        value: _vm.text,
                        expression: "text"
                      }
                    ],
                    staticClass: "form-control search__input",
                    attrs: {
                      type: "text",
                      placeholder: "Давайте попробуем что-нибудь найти",
                      name: "text",
                      autocomplete: "off"
                    },
                    domProps: { value: _vm.text },
                    on: {
                      focus: _vm.onFocus,
                      blur: _vm.onBlur,
                      input: function($event) {
                        if ($event.target.composing) {
                          return
                        }
                        _vm.text = $event.target.value;
                      }
                    }
                  }),
                  _vm._v(" "),
                  _c("span", { staticClass: "search__input-icon" })
                ]),
                _vm._v(" "),
                _c("div", { staticClass: "search__results-pane" }, [
                  _c("div", { staticClass: "search__results-content" }, [
                    _c(
                      "div",
                      { staticClass: "search__results" },
                      [
                        _c(
                          "div",
                          {
                            directives: [
                              {
                                name: "show",
                                rawName: "v-show",
                                value: !_vm.result.items,
                                expression: "!result.items"
                              }
                            ]
                          },
                          [_vm._v("\n          Ничего не найдено\n        ")]
                        ),
                        _vm._v(" "),
                        _vm._l(_vm.result.items, function(item) {
                          return _c("div", { staticClass: "search__result" }, [
                            _c(
                              "a",
                              {
                                staticClass: "search__result-link",
                                attrs: { href: item.url }
                              },
                              [
                                _c("h2", { staticClass: "search__result-title" }, [
                                  _vm._v(_vm._s(item.label))
                                ]),
                                _vm._v(" "),
                                _c("div", { staticClass: "search__result-url" }, [
                                  _vm._v(_vm._s(item.url))
                                ])
                              ]
                            ),
                            _vm._v(" "),
                            item.core
                              ? _c("div", { staticClass: "search__result-core" }, [
                                  _vm._v(
                                    "\n            Drupal " +
                                      _vm._s(item.core) +
                                      "\n          "
                                  )
                                ])
                              : _vm._e()
                          ])
                        })
                      ],
                      2
                    ),
                    _vm._v(" "),
                    _c("div", { staticClass: "search__filters" }, [
                      _c("div", { staticClass: "search__filter" }, [
                        _c("div", { staticClass: "search__filter-label" }, [
                          _vm._v("Сложность")
                        ]),
                        _vm._v(" "),
                        _c("div", { staticClass: "search__filter-item" }, [
                          _c("input", {
                            directives: [
                              {
                                name: "model",
                                rawName: "v-model",
                                value: _vm.filterDifficulty,
                                expression: "filterDifficulty"
                              }
                            ],
                            attrs: {
                              id: "search-difficulty-1",
                              type: "checkbox",
                              name: "difficulty",
                              value: "none"
                            },
                            domProps: {
                              checked: Array.isArray(_vm.filterDifficulty)
                                ? _vm._i(_vm.filterDifficulty, "none") > -1
                                : _vm.filterDifficulty
                            },
                            on: {
                              change: function($event) {
                                var $$a = _vm.filterDifficulty,
                                  $$el = $event.target,
                                  $$c = $$el.checked ? true : false;
                                if (Array.isArray($$a)) {
                                  var $$v = "none",
                                    $$i = _vm._i($$a, $$v);
                                  if ($$el.checked) {
                                    $$i < 0 && (_vm.filterDifficulty = $$a.concat([$$v]));
                                  } else {
                                    $$i > -1 &&
                                      (_vm.filterDifficulty = $$a
                                        .slice(0, $$i)
                                        .concat($$a.slice($$i + 1)));
                                  }
                                } else {
                                  _vm.filterDifficulty = $$c;
                                }
                              }
                            }
                          }),
                          _vm._v(" "),
                          _vm._m(0)
                        ]),
                        _vm._v(" "),
                        _c("div", { staticClass: "search__filter-item" }, [
                          _c("input", {
                            directives: [
                              {
                                name: "model",
                                rawName: "v-model",
                                value: _vm.filterDifficulty,
                                expression: "filterDifficulty"
                              }
                            ],
                            attrs: {
                              id: "search-difficulty-2",
                              type: "checkbox",
                              name: "difficulty",
                              value: "basic"
                            },
                            domProps: {
                              checked: Array.isArray(_vm.filterDifficulty)
                                ? _vm._i(_vm.filterDifficulty, "basic") > -1
                                : _vm.filterDifficulty
                            },
                            on: {
                              change: function($event) {
                                var $$a = _vm.filterDifficulty,
                                  $$el = $event.target,
                                  $$c = $$el.checked ? true : false;
                                if (Array.isArray($$a)) {
                                  var $$v = "basic",
                                    $$i = _vm._i($$a, $$v);
                                  if ($$el.checked) {
                                    $$i < 0 && (_vm.filterDifficulty = $$a.concat([$$v]));
                                  } else {
                                    $$i > -1 &&
                                      (_vm.filterDifficulty = $$a
                                        .slice(0, $$i)
                                        .concat($$a.slice($$i + 1)));
                                  }
                                } else {
                                  _vm.filterDifficulty = $$c;
                                }
                              }
                            }
                          }),
                          _vm._v(" "),
                          _vm._m(1)
                        ]),
                        _vm._v(" "),
                        _c("div", { staticClass: "search__filter-item" }, [
                          _c("input", {
                            directives: [
                              {
                                name: "model",
                                rawName: "v-model",
                                value: _vm.filterDifficulty,
                                expression: "filterDifficulty"
                              }
                            ],
                            attrs: {
                              id: "search-difficulty-3",
                              type: "checkbox",
                              name: "difficulty",
                              value: "medium"
                            },
                            domProps: {
                              checked: Array.isArray(_vm.filterDifficulty)
                                ? _vm._i(_vm.filterDifficulty, "medium") > -1
                                : _vm.filterDifficulty
                            },
                            on: {
                              change: function($event) {
                                var $$a = _vm.filterDifficulty,
                                  $$el = $event.target,
                                  $$c = $$el.checked ? true : false;
                                if (Array.isArray($$a)) {
                                  var $$v = "medium",
                                    $$i = _vm._i($$a, $$v);
                                  if ($$el.checked) {
                                    $$i < 0 && (_vm.filterDifficulty = $$a.concat([$$v]));
                                  } else {
                                    $$i > -1 &&
                                      (_vm.filterDifficulty = $$a
                                        .slice(0, $$i)
                                        .concat($$a.slice($$i + 1)));
                                  }
                                } else {
                                  _vm.filterDifficulty = $$c;
                                }
                              }
                            }
                          }),
                          _vm._v(" "),
                          _vm._m(2)
                        ]),
                        _vm._v(" "),
                        _c("div", { staticClass: "search__filter-item" }, [
                          _c("input", {
                            directives: [
                              {
                                name: "model",
                                rawName: "v-model",
                                value: _vm.filterDifficulty,
                                expression: "filterDifficulty"
                              }
                            ],
                            attrs: {
                              id: "search-difficulty-4",
                              type: "checkbox",
                              name: "difficulty",
                              value: "advanced"
                            },
                            domProps: {
                              checked: Array.isArray(_vm.filterDifficulty)
                                ? _vm._i(_vm.filterDifficulty, "advanced") > -1
                                : _vm.filterDifficulty
                            },
                            on: {
                              change: function($event) {
                                var $$a = _vm.filterDifficulty,
                                  $$el = $event.target,
                                  $$c = $$el.checked ? true : false;
                                if (Array.isArray($$a)) {
                                  var $$v = "advanced",
                                    $$i = _vm._i($$a, $$v);
                                  if ($$el.checked) {
                                    $$i < 0 && (_vm.filterDifficulty = $$a.concat([$$v]));
                                  } else {
                                    $$i > -1 &&
                                      (_vm.filterDifficulty = $$a
                                        .slice(0, $$i)
                                        .concat($$a.slice($$i + 1)));
                                  }
                                } else {
                                  _vm.filterDifficulty = $$c;
                                }
                              }
                            }
                          }),
                          _vm._v(" "),
                          _vm._m(3)
                        ])
                      ]),
                      _vm._v(" "),
                      _c("div", { staticClass: "search__filter" }, [
                        _c("div", { staticClass: "search__filter-label" }, [
                          _vm._v("Версия ядра")
                        ]),
                        _vm._v(" "),
                        _c("div", { staticClass: "search__filter-item" }, [
                          _c("input", {
                            directives: [
                              {
                                name: "model",
                                rawName: "v-model",
                                value: _vm.filterCore,
                                expression: "filterCore"
                              }
                            ],
                            attrs: {
                              id: "search-core-1",
                              type: "checkbox",
                              name: "core",
                              value: "none"
                            },
                            domProps: {
                              checked: Array.isArray(_vm.filterCore)
                                ? _vm._i(_vm.filterCore, "none") > -1
                                : _vm.filterCore
                            },
                            on: {
                              change: function($event) {
                                var $$a = _vm.filterCore,
                                  $$el = $event.target,
                                  $$c = $$el.checked ? true : false;
                                if (Array.isArray($$a)) {
                                  var $$v = "none",
                                    $$i = _vm._i($$a, $$v);
                                  if ($$el.checked) {
                                    $$i < 0 && (_vm.filterCore = $$a.concat([$$v]));
                                  } else {
                                    $$i > -1 &&
                                      (_vm.filterCore = $$a
                                        .slice(0, $$i)
                                        .concat($$a.slice($$i + 1)));
                                  }
                                } else {
                                  _vm.filterCore = $$c;
                                }
                              }
                            }
                          }),
                          _vm._v(" "),
                          _vm._m(4)
                        ]),
                        _vm._v(" "),
                        _c("div", { staticClass: "search__filter-item" }, [
                          _c("input", {
                            directives: [
                              {
                                name: "model",
                                rawName: "v-model",
                                value: _vm.filterCore,
                                expression: "filterCore"
                              }
                            ],
                            attrs: {
                              id: "search-core-2",
                              type: "checkbox",
                              name: "core",
                              value: "8"
                            },
                            domProps: {
                              checked: Array.isArray(_vm.filterCore)
                                ? _vm._i(_vm.filterCore, "8") > -1
                                : _vm.filterCore
                            },
                            on: {
                              change: function($event) {
                                var $$a = _vm.filterCore,
                                  $$el = $event.target,
                                  $$c = $$el.checked ? true : false;
                                if (Array.isArray($$a)) {
                                  var $$v = "8",
                                    $$i = _vm._i($$a, $$v);
                                  if ($$el.checked) {
                                    $$i < 0 && (_vm.filterCore = $$a.concat([$$v]));
                                  } else {
                                    $$i > -1 &&
                                      (_vm.filterCore = $$a
                                        .slice(0, $$i)
                                        .concat($$a.slice($$i + 1)));
                                  }
                                } else {
                                  _vm.filterCore = $$c;
                                }
                              }
                            }
                          }),
                          _vm._v(" "),
                          _vm._m(5)
                        ])
                      ])
                    ])
                  ]),
                  _vm._v(" "),
                  _c(
                    "div",
                    {
                      directives: [
                        {
                          name: "show",
                          rawName: "v-show",
                          value: _vm.status === "loading",
                          expression: "status === 'loading'"
                        }
                      ],
                      staticClass: "search__loading"
                    },
                    [
                      _c(
                        "svg",
                        { attrs: { width: "48", height: "48", viewBox: "0 0 680 666" } },
                        [_c("use", { attrs: { "xlink:href": "#druki-loading-svg" } })]
                      )
                    ]
                  )
                ])
              ])
            };
            var __vue_staticRenderFns__ = [
              function() {
                var _vm = this;
                var _h = _vm.$createElement;
                var _c = _vm._self._c || _h;
                return _c("div", { staticClass: "search__filter-item-label" }, [
                  _c("label", { attrs: { for: "search-difficulty-1" } }, [
                    _vm._v("Не указана")
                  ])
                ])
              },
              function() {
                var _vm = this;
                var _h = _vm.$createElement;
                var _c = _vm._self._c || _h;
                return _c("div", { staticClass: "search__filter-item-label" }, [
                  _c("label", { attrs: { for: "search-difficulty-2" } }, [
                    _vm._v("Базовая")
                  ]),
                  _vm._v(" "),
                  _c("div", { staticClass: "search__filter-item-description" }, [
                    _vm._v(
                      "\n                Подойдет новичкам без каких-либо навыков.\n              "
                    )
                  ])
                ])
              },
              function() {
                var _vm = this;
                var _h = _vm.$createElement;
                var _c = _vm._self._c || _h;
                return _c("div", { staticClass: "search__filter-item-label" }, [
                  _c("label", { attrs: { for: "search-difficulty-3" } }, [
                    _vm._v("Средняя")
                  ]),
                  _vm._v(" "),
                  _c("div", { staticClass: "search__filter-item-description" }, [
                    _vm._v(
                      "\n                Потребуются базовые знания и знакомство с административным\n                интерфейсом.\n              "
                    )
                  ])
                ])
              },
              function() {
                var _vm = this;
                var _h = _vm.$createElement;
                var _c = _vm._self._c || _h;
                return _c("div", { staticClass: "search__filter-item-label" }, [
                  _c("label", { attrs: { for: "search-difficulty-4" } }, [
                    _vm._v("Продвинутая")
                  ]),
                  _vm._v(" "),
                  _c("div", { staticClass: "search__filter-item-description" }, [
                    _vm._v("\n                Потребуется написание кода.\n              ")
                  ])
                ])
              },
              function() {
                var _vm = this;
                var _h = _vm.$createElement;
                var _c = _vm._self._c || _h;
                return _c("div", { staticClass: "search__filter-item-label" }, [
                  _c("label", { attrs: { for: "search-core-1" } }, [_vm._v("Не указана")])
                ])
              },
              function() {
                var _vm = this;
                var _h = _vm.$createElement;
                var _c = _vm._self._c || _h;
                return _c("div", { staticClass: "search__filter-item-label" }, [
                  _c("label", { attrs: { for: "search-core-2" } }, [_vm._v("Drupal 8")])
                ])
              }
            ];
            __vue_render__._withStripped = true;

              /* style */
              const __vue_inject_styles__ = undefined;
              /* scoped */
              const __vue_scope_id__ = undefined;
              /* module identifier */
              const __vue_module_identifier__ = undefined;
              /* functional template */
              const __vue_is_functional_template__ = false;
              /* style inject */
              
              /* style inject SSR */
              

              
              var Search = normalizeComponent_1(
                { render: __vue_render__, staticRenderFns: __vue_staticRenderFns__ },
                __vue_inject_styles__,
                __vue_script__,
                __vue_scope_id__,
                __vue_is_functional_template__,
                __vue_module_identifier__,
                undefined,
                undefined
              );

            /**
             * Some help links:
             * - https://medium.com/vuetify/productivity-in-vue-part-3-697d6407498e
             */
            /**
             * Behavior for attaching all vue components when page is updates.
             */

            Drupal.behaviors.drukiVueInit = {
              attach: function (context) {
                this.attachSearch(context);
              },

              /**
               * Attaches header search component.
               */
              attachSearch: function (context) {
                let searchElements = context.querySelectorAll('.search-init');

                if (searchElements.length) {
                  searchElements.forEach(element => {
                    new Vue({
                      render: h => h(Search),
                      store
                    }).$mount(element);
                  });
                }
              }
            };

}(Drupal, Vue));
