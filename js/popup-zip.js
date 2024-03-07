(function ($, Drupal) {
  'use strict';

  Drupal.behaviors.myModal = {
    attach: function(context) {
      $(context).find('body')
        .once('zip-modal')
        .each(function () {
          var ajaxSettings = {
            url: '/popup_zip/popup_zip_form'
          };
          var myAjaxObject = Drupal.ajax(ajaxSettings);
          myAjaxObject.execute();
        });
    }
  };

})(jQuery, Drupal);
