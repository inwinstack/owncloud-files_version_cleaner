(function() {
  OCA.VersionCleaner = OCA.VersionCleaner || {};

  var VersionCleanerModel = OC.Backbone.Model.extend({
    url: OC.generateUrl('/apps/files_version_cleaner/folderStatus'),

    defaults: {
      folderName: undefined,
      value: undefined,
    },

    sync: function(method, model, options) {
      var folderName = model.attributes.folderName;
      switch (method) {
        case 'read':
          Backbone.ajax({
            method: 'GET',
            url: model.url,
            data: {folderName: folderName},
            async: false,
          })
          .done(function(data){
            model.set({value: data.value});
          });
          break;
        case 'create':
          $.post(model.url, {folderName: folderName, value: model.attributes.value});
          break;
      }
    },
  });

  OCA.VersionCleaner.VersionCleanerModel = VersionCleanerModel;
})();
