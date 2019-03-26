/**
 * @file
 * Simple throttle implementation for Drupal namespace.
 */

/**
 * Limits the invocations of a function in a given time frame.
 *
 * Creates a throttled function that only invokes func at most once per every
 * wait milliseconds. The throttled function comes with a cancel method to
 * cancel delayed func invocations and a flush method to immediately invoke
 * them. Provide options to indicate whether func should be invoked on the
 * leading and/or trailing edge of the wait timeout. The func is invoked with
 * the last arguments provided to the throttled function. Subsequent calls to
 * the throttled function return the result of the last func invocation.
 *
 * @see https://lodash.com/docs/4.17.11#throttle
 * @see https://css-tricks.com/debouncing-throttling-explained-examples/
 *
 * @param {function} func
 *   The function to be invoked.
 * @param {number} wait
 *   The time period within which the callback function should only be
 *   invoked once.
 *
 * @return {function}
 *   The throttle function.
 */
Drupal.throttle = function(func, wait = 100) {
  let timer = null;

  return function(...args) {
    if (timer === null) {
      timer = setTimeout(() => {
        func.apply(this, args);
        timer = null;
      }, wait);
    }
  };
};
