/**
 * @file
 * loading-indicator.js behaviors.
 */

(function(Drupal) {

  Drupal.theme.ajaxProgressThrobber = function() {
    return '<div class="druki-loading-indicator-pane">\n  <svg width="128" height="128" viewBox="0 0 680 666">\n    <use xlink:href="#druki-loading-svg" />\n  </svg>\n</div>';
  };

  Drupal.theme.ajaxProgressIndicatorFullscreen = function() {
    return '<div class="druki-loading-indicator-pane druki-loading-indicator-pane--fullscreen">\n  <svg width="64" height="64" viewBox="0 0 680 666">\n    <use xlink:href="#druki-loading-svg" />\n  </svg>\n</div>';
  };

})(Drupal);
