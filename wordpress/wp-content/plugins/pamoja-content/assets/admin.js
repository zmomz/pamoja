/* global wp, jQuery, pamojaAdmin */
(function ($) {
  'use strict';

  // ----- Conditional fields (data-show-when="pamoja_status" data-show-value="handed-on")
  function bindConditionals(scope) {
    $(scope).find('[data-show-when]').each(function () {
      var $field = $(this);
      var $dep = $('[name="' + $field.data('showWhen') + '"]');
      function update() {
        $field.prop('hidden', String($dep.val()) !== String($field.data('showValue')));
      }
      $dep.on('change', update);
      update();
    });
  }

  // ----- Gallery picker
  function galleryItem(att) {
    var thumb = (att.sizes && (att.sizes.thumbnail || att.sizes.medium || att.sizes.full)) || { url: att.url };
    var ok = !!att.pamojaOk;
    return $('<li>')
      .attr('data-id', att.id)
      .addClass(ok ? 'is-ok' : 'is-blocked')
      .append($('<img>').attr({ src: thumb.url, alt: att.alt || '' }))
      .append($('<span class="pamoja-gallery-status">').text(att.pamojaStatus || ''))
      .append(
        $('<span class="pamoja-gallery-actions">')
          .append($('<a target="_blank" rel="noopener">').attr('href', att.pamojaEditUrl || '#').text(pamojaAdmin.edit))
          .append($('<button type="button" class="button-link-delete pamoja-gallery-remove">').attr('aria-label', pamojaAdmin.remove).html('&times;'))
      );
  }

  function bindGalleries(scope) {
    $(scope).find('.pamoja-gallery').each(function () {
      var $wrap = $(this);
      var $ids = $wrap.find('.pamoja-gallery-ids');
      var $list = $wrap.find('.pamoja-gallery-list');
      var frame;

      function sync() {
        var ids = $list.children('li').map(function () { return $(this).data('id'); }).get();
        $ids.val(ids.join(','));
      }

      $list.sortable({ placeholder: 'pamoja-gallery-placeholder', update: sync });

      $wrap.on('click', '.pamoja-gallery-remove', function () {
        $(this).closest('li').remove();
        sync();
      });

      $wrap.on('click', '.pamoja-gallery-add', function (e) {
        e.preventDefault();
        if (!frame) {
          frame = wp.media({
            title: pamojaAdmin.addPhotos,
            button: { text: pamojaAdmin.useThese },
            library: { type: 'image' },
            multiple: 'add'
          });
          frame.on('select', function () {
            var existing = $list.children('li').map(function () { return String($(this).data('id')); }).get();
            frame.state().get('selection').each(function (model) {
              var att = model.toJSON();
              if (existing.indexOf(String(att.id)) === -1) {
                $list.append(galleryItem(att));
              }
            });
            sync();
          });
        }
        frame.open();
      });
    });
  }

  // ----- Single image picker (settings pages)
  function bindImages(scope) {
    $(scope).find('.pamoja-image-field').each(function () {
      var $wrap = $(this);
      var $id = $wrap.find('.pamoja-image-id');
      var $preview = $wrap.find('.pamoja-image-preview');
      var $clear = $wrap.find('.pamoja-image-clear');
      var frame;
      $wrap.on('click', '.pamoja-image-pick', function (e) {
        e.preventDefault();
        if (!frame) {
          frame = wp.media({ title: pamojaAdmin.chooseImage, library: { type: 'image' }, multiple: false });
          frame.on('select', function () {
            var att = frame.state().get('selection').first().toJSON();
            var src = (att.sizes && att.sizes.medium ? att.sizes.medium.url : att.url);
            $id.val(att.id);
            $preview.html($('<img>').attr({ src: src, alt: att.alt || '' }));
            $clear.prop('hidden', false);
          });
        }
        frame.open();
      });
      $clear.on('click', function (e) {
        e.preventDefault();
        $id.val('');
        $preview.empty();
        $clear.prop('hidden', true);
      });
    });
  }

  $(function () {
    bindConditionals(document);
    bindGalleries(document);
    bindImages(document);
  });
})(jQuery);
