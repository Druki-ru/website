!function(e){e.behaviors.loremasterCodeHighlight={attach:function(e,t){if(!window.IntersectionObserver)return;let r;r=window.requestIdleCallback?e=>{requestIdleCallback(e)}:e=>{e()},r((()=>{const e=new IntersectionObserver((function(t){t.forEach((function(t){if(t.isIntersecting){let r=t.target;Prism.highlightElement(r),e.unobserve(r)}}))}));[].slice.call(document.querySelectorAll("pre code")).forEach((function(t){t.processed||(t.processed=!0,e.observe(t))}))}))}}}(Drupal);
//# sourceMappingURL=code-highlight.js.map
