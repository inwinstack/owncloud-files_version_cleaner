(function ($, OC) {

$(document).ready(function() {
  FilesVersionCleaner = {};

  FilesVersionCleaner.view= {
    input: $('#files_version_cleaner_personal_input'),
    loading: $('#files_version_cleaner_loader'),
    msgSuccess: $('#files_version_cleaner_msg_success'),
    msgFail: $('#files_version_cleaner_msg_fail'),
  };

  FilesVersionCleaner.setVersionNumber = function(){
    $.ajax({
      url: OC.generateUrl('apps/files_version_cleaner/set_number'),
      method: 'POST',
      data: {"versionNumber" : FilesVersionCleaner.view.input.val()},
    })
    .then(function(data) {
      FilesVersionCleaner.view.loading.show();

      msg = data.success ? FilesVersionCleaner.view.msgSuccess : FilesVersionCleaner.view.msgFail;

      msg.show();
      msg.delay(2000).fadeOut(1000);
    })
    .done(function(){
      FilesVersionCleaner.view.loading.hide();
    });
  };

  FilesVersionCleaner.view.input.change(FilesVersionCleaner.setVersionNumber);
});

})(jQuery, OC);
