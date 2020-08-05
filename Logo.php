<?php
class Logo
{
  public function footer(){
      $versionfilename = Yii::app()->basePath."/../version.txt";
      if(file_exists($versionfilename)){
          $version = file_get_contents($versionfilename);
          $time = filemtime($versionfilename);
      }else{
          $version = "test";
          $time = time();
      }
      $footer = '';
      $footer .= '<div class="row">&nbsp;</div>';
      $footer .= '<center>';
      $footer .= '<div class="copyright">';
      $footer .= '<p>';
      $footer .= 'Made with ❤️ by ';
      $footer .= '<a href="' . Yii::app()->params['website'] . '" target="_blank">' . Yii::app()->params['adminName'] . '</a>';
      $footer .= '<br>';
      $footer .= 'Release n. '.substr($version,0,7) .' ' .date('d/m/Y',$time);
      $footer .= '</p>';
      $footer .= '</div>';
      $footer .= '</center>';
      $footer .= '</div>';
      $footer .= '<div class="row">&nbsp;</div>';

      return $footer;
  }

  public function login(){
    $logo = '
    <a href="'. Yii::app()->createUrl('site/index') .'">
        <div style="padding: 0px;" >
            <img alt="logo" style="display: block; margin-left: auto;	margin-right: auto; max-width: 150px; " src="'. Yii::app()->request->baseUrl.Yii::app()->params['logoApplicazione'].'">
        </div>
    </a>
     ';
     echo $logo;
 }
 public function header(){
    $logo = '
    <a href="'. Yii::app()->createUrl('site/index') .'">

        <img alt="logo" style="display: block; height:57px; margin-top:5px;" src="'. Yii::app()->request->baseUrl.Yii::app()->params['logoApplicazione'].'">

    </a>
    ';
    // <h1 class="logo-name">'.Yii::t('lang','BOLT').' <small class="logo-descri">'.Yii::t('lang','TTS wallet').'</small></h1>
    echo $logo;
}
}