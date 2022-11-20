(function (CKEDITOR) {
  function findElementByName(element, name) {
    if (element.name === name) {
      return element;
    }

    var found = null;
    element.forEach(function (el) {
      if (el.name === name) {
        found = el;
        return false;
      }
    }, CKEDITOR.NODE_ELEMENT);
    return found;
  }

  CKEDITOR.plugins.add('sis_image_style', {
    requires: 'drupalimage',
    beforeInit: function beforeInit(editor) {
      editor.on('widgetDefinition', function (event) {
        var widgetDefinition = event.data;

        if (widgetDefinition.name !== 'image') {
          return;
        }

        console.log(widgetDefinition.features);

        CKEDITOR.tools.extend(widgetDefinition.features, {
          imageStyle: {
            requiredContent: 'img[data-image-size]'
          }
        }, true);
        var requiredContent = widgetDefinition.requiredContent.getDefinition();
        requiredContent.attributes['data-image-size'] = '';
        widgetDefinition.requiredContent = new CKEDITOR.style(requiredContent);
        widgetDefinition.allowedContent.img.attributes['!data-image-size'] = true;

        var originalDowncast = widgetDefinition.downcast;

        widgetDefinition.downcast = function (element) {
          var img = findElementByName(element, 'img');
          originalDowncast.call(this, img);
          var attrs = img.attributes;
          attrs['data-image-size'] = this.data['data-image-size'];
          return img;
        };

        var originalUpcast = widgetDefinition.upcast;

        widgetDefinition.upcast = function (element, data) {
          if (element.name !== 'img' || !element.attributes['data-entity-type'] || !element.attributes['data-entity-uuid']) {
            return;
          }

          if (element.attributes['data-cke-realelement']) {
            return;
          }

          element = originalUpcast.call(this, element, data);
          var attrs = element.attributes;

          data['data-image-size'] = attrs['data-image-size'];
          delete attrs['data-image-size'];

          return element;
        };

        if (widgetDefinition._mapDataToDialog) {
          CKEDITOR.tools.extend(widgetDefinition._mapDataToDialog, {
            "data-image-size": "data-image-size"
          });
        }
      }, null, null, 20);
    }
  });
})(CKEDITOR);
