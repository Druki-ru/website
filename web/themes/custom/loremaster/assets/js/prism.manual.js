/**
 * @file
 * The Prism.js initial file.
 *
 * This file disable auto-highlighting so we can lazy-highlight the code block.
 * Prism support two ways of doing it. We doing this via js, because attribute
 * on library wil split up js files and insted of 1 bundled JS Drupal will
 * generate 3 of them (at least). This is not good for such small thing.
 *
 * @see https://prismjs.com/#basic-usage
 */
window.Prism = window.Prism || {};
window.Prism.manual = true;
