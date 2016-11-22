<?php
    script("files_version_cleaner", "script");
    style("files_version_cleaner", "style");
?>
<div class="section" id="files_version_cleaner_section">
    <h2><?php p($l->t('Version control')); ?></h2>
    <div>
        <label for="files_version_cleaner_personal_input"><?php p($l->t( 'Max number of files versions' )); ?> </label>
        <select id="files_version_cleaner_personal_input" data-key="versionNumber">
            <?php for ($i = 1; $i <= $_["maxVersionNum"]; $i++) { ?>
                <option value="<?php echo $i; ?>" <?php if($i == $_["userVersionNum"]) echo "selected"; ?>><?php echo $i; ?></option>
            <?php } ?>
        </select>
        <div class="loading-files_version_cleaner" id="files_version_cleaner_loader" style="display: none;"></div>
        <span id="files_version_cleaner_msg_success" class="files_version_cleaner-msg files_version_cleaner-green" hidden><?php p($l->t("Change successful!"))?></span>
        <span id="files_version_cleaner_msg_fail" class="files_version_cleaner-msg files_version_cleaner-red" hidden><?php p($l->t("Change unsuccessful!"))?></span>
    </div>
    <div>
        <label for="files_version_cleaner_personal_input_historic"><?php p($l->t( 'Max number of files historic versions' )); ?> </label>
        <select id="files_version_cleaner_personal_input_historic" data-key="historicVersionNumber">
            <?php for ($i = 1; $i <= $_["maxHistoricVersionNum"]; $i++) { ?> <option value="<?php echo $i; ?>" <?php if($i == $_["userHistoricVersionNum"]) echo "selected"; ?>><?php echo $i; ?></option> <?php } ?>
        </select>
        <div class="loading-files_version_cleaner" id="files_version_cleaner_loader_historic" style="display: none;"></div>
        <span id="files_version_cleaner_msg_success_historic" class="files_version_cleaner-msg files_version_cleaner-green" hidden><?php p($l->t("Change successful!"))?></span>
        <span id="files_version_cleaner_msg_fail_historic" class="files_version_cleaner-msg files_version_cleaner-red" hidden><?php p($l->t("Change unsuccessful!"))?></span>
    </div>
    <div>
        <label for="files_version_cleaner_personal_input_historic"><?php p($l->t( 'Interval of per historic version (Day)' )); ?> </label>
        <input id="files_version_cleaner_personal_interval_input" type="number" min="1" max="30" value="<?php echo $_["interval"] ?>">
        <div id="files_version_cleaner_personal_interval_button" class="inlineblock button"><?php p($l->t("Submit")); ?></div>
        <div class="loading-files_version_cleaner" id="files_version_cleaner_loader_interval" style="display: none;"></div>
        <span id="files_version_cleaner_msg_success_interval" class="files_version_cleaner-msg files_version_cleaner-green" hidden><?php p($l->t("Change successful!"))?></span>
        <span id="files_version_cleaner_msg_fail_interval" class="files_version_cleaner-msg files_version_cleaner-red" hidden><?php p($l->t("Change unsuccessful!"))?></span>
    </div>
</div>
