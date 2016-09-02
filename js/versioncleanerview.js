(function() {
  OCA.VersionCleaner = OCA.VersionCleaner || {};

  var TEMPLATE = '<%= enableLabel %> <input id="versionCleanerCheckbox" type="checkbox">';
  var VersionCleanerView = OC.Backbone.View.extend({
    model: undefined,

    fileInfo: undefined,

    template: _.template(TEMPLATE),

    events: {
      'change #versionCleanerCheckbox': 'onChangeCheckbox'
    },

    initialize: function(option) {
      this.render();
    },

    onChangeCheckbox: function(e) {
      var test = $(e.target).attr('checked') ? true : false;
      console.dir(this.fileInfo);
    },

    formatData: function(fileInfo) {
      return {
        enableLabel: 'Enable version control',
      }
    },

    render: function() {
      this.$el.empty();
      this.$el.append(this.template(this.formatData()));
    },
  });

  OCA.VersionCleaner.VersionCleanerView = VersionCleanerView;
})();
