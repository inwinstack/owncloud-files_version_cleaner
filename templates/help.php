<?php

?>
<!DOCTYPE html>
<style type="text/css">
h1 {
  padding: 20px 0px;
  font-size: 32px;
}

h3 {

  font-size: 28px;
  padding: 20px 0px;
}
.file_version_cleaner_help {
  font-size: 20px;
  padding: 20px;
}
.img {
  max-width: 100%;
  max-height: 100%;
  display:table-cell;
  vertical-align:middle;
  margin:auto;
  padding: 20px;

}
</style>
<div>
    <h1>我要怎麼開啟版本控制功能?</h1>
    <h3>開啟資料夾的版本控制功能後，更新這個資料夾底下的檔案時，檔案會自動產生版本唷！</h3>
    <p class="file_version_cleaner_help">
      1.點擊資料夾右邊的<img src="/core/img/actions/more.svg"></img>圖示後，點選 <strong>[詳細資料]</strong>。
    </p>
    <img class="img" src="<?php print_unescaped(image_path('files_version_cleaner', 'step1.png')); ?>">
    <p class="file_version_cleaner_help">
      2.點選詳細資料中的 <strong>[版本]</strong>，再勾起啟用版本控制就可以開啟版本功能囉！
    </p>
    <img class="img" src="<?php print_unescaped(image_path('files_version_cleaner', 'step2.png')); ?>">
    <h5>
    <strong>＊ 保留版本係每異動一次檔案會自動產生一個新版本。當保留版本數量達設定上限時，會將最舊之保留版本移至>歷史版本區。</strong>
    </h5>
    <h5>
    <strong>＊ 歷史版本則每日至多保存一個版本，歷史版本數量達設定上限時，會將最舊之歷史版本移除。</strong>
    </h5>
</div>
