/*
 * ghz.me url shortener
 * when a long url hz.
 *
 * (c) 2014 Sam Thompson <contact@samt.us>
 * License: MIT
 */

$(function () {
  var $inputbox = $('input[name="url"]'),
    $form = $inputbox.parent().parent(),
    $success = $('.success'),
    $error = $('.error');

  $form.on('submit', function (e) {
    e.preventDefault();
    fetchUrl( $inputbox.val() );
    return false;
  });

  function fetchUrl(url) {
    $success.hide();
    $error.hide();

    $.post(document.URL, { url: encodeURI(url) }, function (data) {
      if (!data.success) {
        return $error.show();
      }

      $success.show();
      $inputbox.val(data.url);
      $inputbox.select();

    }, 'json');
  }

});
