(function() {
  OCA.VersionCleaner = OCA.VersionCleaner || {};

  var TEMPLATE = '<%= enableLabel %> <input id="versionCleanerCheckbox" type="checkbox" <% if (checked){ %> checked <% } %> >';
  var TEMPLATE2 = '<input id="versionCleanerText" style="width: 94%;  margin-top: 19px;"" type="text" placeholder="<%= enableLabel %>" disabled = "disabled">';
  var VersionCleanerView = OC.Backbone.View.extend({
    model: function(option) {
      return new OCA.VersionCleaner.VersionCleanerModel(option);
    },

    fileInfo: undefined,

    folderName: undefined,

    template: _.template(TEMPLATE),
    template2: _.template(TEMPLATE2),

    events: {
      'change #versionCleanerCheckbox': 'onChangeCheckbox'
    },

    initialize: function(option) {
      this.fileInfo = option.fileInfo;
      if(this.fileInfo.attributes.path === '/') {
        this.folderName = this.fileInfo.attributes.path + this.fileInfo.attributes.name;
      }
      else {
        this.folderName = this.fileInfo.attributes.path + '/' + this.fileInfo.attributes.name;
      }

      this.model = new OCA.VersionCleaner.VersionCleanerModel({folderName: this.folderName});

      this.model.fetch();
    },

    onChangeCheckbox: function(e) {
      var value = $(e.target).attr('checked') ? true : false;
      self = this;

      if (!value) {
        OC.dialogs.confirmN(
          t('files_version_cleaner', 'Are you sure to cancel version coltrol on this folder?'),
          t('files_version_cleaner', 'Cancel version control'),
          function(dialogValue) {
            if(dialogValue) {
              OC.dialogs.confirmN(
                t('files_version_cleaner', 'Are you really sure to cancel?'),
                t('files_version_cleaner', 'Cancel version control'),
                function(dialogValue) {
                  if(dialogValue) {
                    self.model.set({value: false});
                    self.model.save();
                    $image = OC.getRootPath() + '/core/img/filetypes/folder.svg';
                    $cssUrl = 'url("' + $image + '")';
                    $('.thumbnailContainer').find('a').css('background-image', $cssUrl);
                    $('#fileList').find('tr[data-file="' + self.fileInfo.get('name') + '"]').find('.thumbnail').css('background-image', $cssUrl)
                  }
                  else {
                    $(e.target).attr('checked', true);
                  }
                }
              );
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
        if(value == true) {
          $image = OC.getRootPath() + '/themes/MOE/core/img/filetypes/folder-version.svg';
          $cssUrl = 'url("' + $image + '")';
          $('.thumbnailContainer').find('a').css('background-image', $cssUrl);
          $('#fileList').find('tr[data-file="' + self.fileInfo.get('name') + '"]').find('.thumbnail').css('background-image', $cssUrl)
        }
        else{
          $image = OC.getRootPath() + '/core/img/filetypes/folder.svg';
          $cssUrl = 'url("' + $image + '")';
          $('.thumbnailContainer').find('a').css('background-image', $cssUrl);
          $('#fileList').find('tr[data-file="' + self.fileInfo.get('name') + '"]').find('.thumbnail').css('background-image', $cssUrl)
        }
      }

    },

    formatData: function(fileInfo) {
      return {
        enableLabel: t('files_version_cleaner', 'Enable version control'),
        checked: this.model.attributes.value,
      }
    },

    notSupportFormatData: function(fileInfo) {
	      return {
	        enableLabel: t('files_version_cleaner', 'External storage not support files version.'),
	        checked: this.model.attributes.value,
	      }
	    },
    
    render: function() {
	var data = document.getElementsByClassName('highlighted');
	if (data.length == 1){
	    var isExternal = data[0].hasAttribute('data-mounttype');
	    if (isExternal){
		this.$el.html(this.template2(this.notSupportFormatData()));
		return this;
	    }
	}
      this.$el.html(this.template(this.formatData()));
      return this;
    },
  });

  OCA.VersionCleaner.VersionCleanerView = VersionCleanerView;
})();
