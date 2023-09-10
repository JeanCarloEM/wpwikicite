(function ($, w) {
  $(function () {
    $("sup.reference a").tipsy({
      fade: true,
      opacity: 0.98,
      html: true,
      title: (function () {
        var el = $(this);
        if (el.attr('href').indexOf('#') === 0) {
          return $(el.attr('href') + ' span.reference-text').html();
        }
      })
    });

    $('sup.reference a').mousedown(function () {
      $('#reference_list').collapse("show");
    });
  });
}(jQuery, window));