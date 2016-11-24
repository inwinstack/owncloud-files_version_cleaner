(function ($, OC) {

$(document).ready(function() {
  FilesVersionCleaner = {};

  FilesVersionCleaner.view= {
    input: $('#files_version_cleaner_personal_input'),
    input_button: $('#files_version_cleaner_personal_button'),
    loading: $('#files_version_cleaner_loader'),
    msg: {"success": $('#files_version_cleaner_msg_success'), "fail": $('#files_version_cleaner_msg_fail')},
    input_historic: $('#files_version_cleaner_personal_input_historic'),
    input_historic_button: $('#files_version_cleaner_personal_historic_button'),
    loading_historic: $('#files_version_cleaner_loader_historic'),
    msg_historic: {"success": $('#files_version_cleaner_msg_success_historic'), "fail": $('#files_version_cleaner_msg_fail_historic')},
    input_interval: $('#files_version_cleaner_personal_interval_input'),
    input_interval_button: $('#files_version_cleaner_personal_interval_button'),
    loading_interval: $('#files_version_cleaner_loader_interval'),
    msg_interval: {"success": $('#files_version_cleaner_msg_success_interval'), "fail": $('#files_version_cleaner_msg_fail_interval')},
  };

  FilesVersionCleaner.setVersionNumber = function(){
    var self = $(this);
    var key = self.data('key');

    if(key === "versionNumber") {
      var loader = FilesVersionCleaner.view.loading.show();
      var msg = FilesVersionCleaner.view.msg;
      var val = FilesVersionCleaner.view.input.val();
    }
    else {
      var loader = FilesVersionCleaner.view.loading_historic.show();
      var msg = FilesVersionCleaner.view.msg_historic;
      var val = FilesVersionCleaner.view.input_historic.val();
    }

    $.ajax({
      url: OC.generateUrl('apps/files_version_cleaner/set_number'),
      method: 'POST',
      data: {"versionNumber" : val, "key" : key},
    })
    .then(function(data) {
      msg = data.success ? msg.success : msg.fail;
      msg.show();
      msg.delay(2000).fadeOut(1000);
    })
    .done(function(){
      loader.hide();
    });
  };

  FilesVersionCleaner.setIntervalNumber = function(){
    var self = $(this);

    var loader = FilesVersionCleaner.view.loading_interval.show();
    var msg = FilesVersionCleaner.view.msg_interval;
    console.dir(this.value);

    $.ajax({
      url: OC.generateUrl('apps/files_version_cleaner/setInterval'),
      method: 'POST',
      data: {"interval" : FilesVersionCleaner.view.input_interval.val()},
    })
    .then(function(data) {
      msg = data.success ? msg.success : msg.fail;
      msg.show();
      msg.delay(2000).fadeOut(1000);
    })
    .done(function(){
      loader.hide();
    });
  };

  FilesVersionCleaner.view.input_button.click(FilesVersionCleaner.setVersionNumber);
  FilesVersionCleaner.view.input_historic_button.click(FilesVersionCleaner.setVersionNumber);
  FilesVersionCleaner.view.input_interval_button.click(FilesVersionCleaner.setIntervalNumber);
});
})(jQuery, OC);
