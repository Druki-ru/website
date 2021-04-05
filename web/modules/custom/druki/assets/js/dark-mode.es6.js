/**
 * @file
 * Dark mode switcher.
 */
(function () {

  'use strict';

  function DarkMode() {
    this.store = localStorage;
    // The key to store value of active scheme.
    this.name = 'dark-mode-toggle';
    this.eventHandlers = {
      'onUpdate': [],
    };
    this.init();
  }

  DarkMode.prototype = {

    /**
     * Initialize script.
     */
    init: function () {
      // Update theme on script initialization.
      this.update();

      // Add listener to color scheme changes.
      const darkModeMediaQuery = window.matchMedia('(prefers-color-scheme: dark)');
      darkModeMediaQuery.addEventListener('change', () => {
        this.update();
      });
    },

    /**
     * Sets needed color scheme.
     */
    setColorScheme: function (scheme) {
      if (['auto', 'light', 'dark'].includes(scheme)) {
        if (scheme ===  'auto') {
          // By removing value we fallback to 'system detection' mode.
          this.store.removeItem(this.name);
        }
        else {
          this.store.setItem(this.name, scheme);
        }
      }
      else {
        // If provided not valid scheme, reset it to 'auto'.
        this.store.removeItem(this.name);
      }
      this.update();
    },

    /**
     * Get currently active color scheme.
     */
    getColorScheme: function () {
      let currentScheme = this.store.getItem(this.name);
      // If no value is set, let it be handled bu user's system.
      if (!currentScheme) {
        currentScheme = this.getColorSchemeFromSystem();
      }
      return currentScheme;
    },

    /**
     * Gets current color scheme from system settings.
     */
    getColorSchemeFromSystem: function () {
      if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
        return 'dark';
      }
      else {
        return 'light';
      }
    },

    /**
     * Checks if current color scheme from system or specified.
     */
    isSchemeFromSystem: function () {
      return this.store.getItem(this.name) === null;
    },

    /**
     * Sets correct attributes to update theme visuals.
     */
    update: function () {
      this.dispatchEvent('onUpdate', this.getColorScheme(), this.isSchemeFromSystem());
      document.documentElement.setAttribute('data-theme', this.getColorScheme());
    },

    /**
     * Adds handler for 'onUpdate' event.
     */
    onUpdate: function (handler) {
      this.eventHandlers.onUpdate.push(handler);
    },

    /**
     * Dispatch custom event.
     */
    dispatchEvent: function (eventName, ...args) {
      this.eventHandlers[eventName].forEach(handler => {
        handler.call(this, ...args);
      });
    },

  }

  // The DarkMode object is internal. Pass only instance into global scope.
  window['DarkMode'] = new DarkMode();

})();
