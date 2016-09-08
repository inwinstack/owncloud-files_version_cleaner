(function() {
  OCA.VersionCleaner = OCA.VersionCleaner || {};

  var TEMPLATE = '<%= enableLabel %> <input id="versionCleanerCheckbox" type="checkbox" <% if (checked){ %> checked <% } %> >';
  var VersionCleanerView = OC.Backbone.View.extend({
    model: function(option) {
      return new OCA.VersionCleaner.VersionCleanerModel(option);
    },

    fileInfo: undefined,

    folderName: undefined,

    template: _.template(TEMPLATE),

    events: {
      'change #versionCleanerCheckbox': 'onChangeCheckbox'
    },

    initialize: function(option) {
      this.fileInfo = option.fileInfo;
      this.folderName = this.fileInfo.attributes.path + this.fileInfo.attributes.name;

      this.model =  new OCA.VersionCleaner.VersionCleanerModel({folderName: this.folderName});

      this.model.fetch();
    },

    onChangeCheckbox: function(e) {
      var value = $(e.target).attr('checked') ? true : false;
      self = this;

      if (!value) {
        OC.dialogs.confirm(
          t('files_version_cleaner', 'Are you sure to cancel version coltrol on this folder?'),
          t('files_version_cleaner', 'Version control'),
          function(dialogValue) {
            if(dialogValue) {
              self.model.set({value: false});
              self.model.save();
              console.dir(1234);
            }
            else {
              $(e.target).attr('checked', true);
            }
          }
        );
      }
      else {
        this.model.set({value: value});
        this.model.save();
      }
    },

    formatData: function(fileInfo) {
      return {
        enableLabel: t('files_version_cleaner', 'Enable version control'),
        checked: this.model.attributes.value,
      }
    },

    render: function() {
      this.$el.html(this.template(this.formatData()));
      return this;
    },
  });

  OCA.VersionCleaner.VersionCleanerView = VersionCleanerView;
})();
