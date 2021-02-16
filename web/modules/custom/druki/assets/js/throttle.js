Drupal.throttle=function(l,t=100){let n=null;return function(...u){null===n&&(n=setTimeout((()=>{l.apply(this,u),n=null}),t))}};
//# sourceMappingURL=throttle.js.map
