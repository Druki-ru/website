/**
 * @file
 * Mobile navigation behaviors.
 */
(function(Drupal) {

  let DELAY = 500;

  Drupal.behaviors.autumnMobileNavigation = {
    attach: function(context, settings) {
      let toggler = context.querySelector('.mobile-toggle');
      let mobileNavigation = context.querySelector('.mobile-navigation');

      if (toggler && mobileNavigation) {
        let listener = e => {
          this.activate(e, mobileNavigation);
        };

        toggler.removeEventListener('click', listener);
        toggler.addEventListener('click', listener);
      }
    },

    activate: function(e, mobileNavigation) {
      mobileNavigation.classList.add('mobile-navigation--active');

      let sidebar = mobileNavigation.querySelector('.mobile-navigation__sidebar');
      let listener = e => {
        if (!sidebar.contains(e.target)) {
          mobileNavigation.classList.add('mobile-navigation--hiding');
          setTimeout(() => {
            mobileNavigation.classList.remove('mobile-navigation--active');
            mobileNavigation.classList.remove('mobile-navigation--hiding');
          }, DELAY);
        }
      };

      mobileNavigation.removeEventListener('click', listener);
      mobileNavigation.addEventListener('click', listener);
    },
  };

})(Drupal);
