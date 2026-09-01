/**
 * @file
 * Applies this site's configured Coffee shortcut. Coffee's own file is
 * never modified, so this keeps working across Coffee updates.
 */

(function (Drupal, once, drupalSettings) {

  'use strict';

  Drupal.behaviors.coffeeShortcut = {
    attach: function (context) {
      once('coffee-shortcut', 'html', context).forEach(function () {
        var settings = drupalSettings.coffeeShortcut || {};
        var custom = settings.custom || {};
        var customKeyCode = custom.key ? custom.key.toUpperCase().charCodeAt(0) : null;

        document.addEventListener('keydown', function (event) {
          // 68/206 = d/D, the keys Coffee binds to alt + D. Only block the
          // plain combination, so a custom shortcut that adds another
          // modifier, such as alt + shift + D, still reaches the check below.
          if (settings.blockDefault
            && event.altKey && !event.shiftKey && !event.ctrlKey && !event.metaKey
            && (event.keyCode === 68 || event.keyCode === 206)) {
            event.stopImmediatePropagation();
            return;
          }

          if (customKeyCode
            && event.keyCode === customKeyCode
            && event.altKey === !!custom.alt
            && event.shiftKey === !!custom.shift
            && event.ctrlKey === !!custom.ctrl
            && event.metaKey === !!custom.meta
            && window.DrupalCoffee) {
            // Coffee's own listener does not check for shift, ctrl or meta,
            // so it would otherwise also react to a custom combination that
            // reuses the D or K key. Stop it here so only this shortcut
            // handles the keypress.
            event.preventDefault();
            event.stopImmediatePropagation();
            window.DrupalCoffee.coffee_show();
          }
        }, true);
      });
    }
  };

})(Drupal, once, drupalSettings);
