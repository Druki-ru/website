/**
 * @file
 * Provides custom initialization for Quicklink.
 *
 * @see https://github.com/GoogleChromeLabs/quicklink#api
 */
window.addEventListener('load', () =>{
  quicklink.listen({
    ignores: [
      // Ignore user routes.
      /\/user.?/,
      // Ignore admin routes.
      /\/admin.?/,
      // Ignore anchors.
      uri => uri.includes('#'),
      (uri, elem) => elem.hasAttribute('noprefetch'),
    ]
  });
});
