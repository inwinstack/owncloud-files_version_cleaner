$(document).ready(function() {
    $(".files_version_cleaner_info").click(function(){
        $.colorbox({
                opacity:0.4,
                transition:"elastic",
                speed:100,
                width:"70%",
                height:"70%",
                href: OC.filePath('files_version_cleaner', '', 'help.php'),
        });
    });
});
