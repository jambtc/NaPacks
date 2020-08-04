<?php
require_once dirname(__FILE__) . '/../web-push-php/vendor/autoload.php';
use Minishlink\WebPush\WebPush;
use Minishlink\WebPush\Subscription;


class Push
{
    /**
    * Funzione che restituisce il titolo del messaggio push in base al type_notification
    **/
    private function appTitle($type_notification,$app){
        switch ($type_notification) {
            case 'invoice':
            case 'help':
            case 'fattura':
                $return = 'Napay';
                break;

            case 'token':
            case 'contact':
                if ($app == 'wallet')
                    $return = 'Wallet';
                else
                    $return = 'Bolt';

                break;

            default:
                $return = 'Napay';
                break;
        }

        return $return;

    }
  /**
   * FUNZIONE CHE INVIA UN MESSAGGIO PUSH
   *
   * @param $array (array contenente la notifica)
   * @param $app (applicazione che riceverà la notifica) di default è Napay
   */
  public function send($array, $app='dashboard')
  {
      // echo '<pre>'.print_r($array,true).'</pre>';
      if (gethostname() == 'CGF6135T'){
          return true;
      }
      #echo '<pre>'.print_r($array,true).'</pre>';
      // exit;

      //Carico i parametri della webapp
      $settings=Settings::load();

      $criteria=new CDbCriteria();
      $criteria->compare('id_user',$array->id_user,false);
      $criteria->compare('type',$app,false);

      #echo '<pre>'.print_r($criteria,true).'</pre>';

      $subscribe_array = CHtml::listData(PushSubscriptions::model()->findAll($criteria), 'id_subscription', function($tabella) {
          $object['endpoint'] =  $tabella->endpoint;
          $object['auth'] =  $tabella->auth;
          $object['p256dh'] =  $tabella->p256dh;
          return $object;
      });

      if (isset($subscribe_array)){
          // Crea autorizzazioni VAPID
          $auth = array(
              'VAPID' => array(
                  'subject' => WebApp::translateMsg($array->description),
                  'publicKey' => $settings->VapidPublic, // don't forget that your public key also lives in app.js
                  'privateKey' => $settings->VapidSecret, // in the real world, this would be in a secret file
              ),
          );
          #echo '<pre>'.print_r($auth,true).'</pre>';

          // impostazioni di default
          $defaultOptions = [
              'TTL' => 64800, //4 settimane
          ];

          // il contenuto del messaggio
          $content = array(
              'title' => Yii::t('lang','['.self::appTitle($array->type_notification,$app).'] - New message'), //'$array->type_notification,
              'body' => WebApp::translateMsg($array->description),
              'icon' => 'src/images/icons/app-icon-96x96.png',
              'badge' => 'src/images/icons/app-icon-96x96.png',
              //'image' => $imagePath.'banner.png',
              //'sound' => $soundPath.'notification-sound.mp3',
              'vibrate' => [200, 100, 200, 100, 200],
              'tag' => 'notify',
              'renotify' => true,
              'data' => [
                  'priority'=>'high',
                  'openUrl' => $array->url,
              ],
              'actions' => [
                ['action'=> 'openUrl', 'title'=> Yii::t('lang','Yes'), 'icon'=> 'css/images/chk_on.png'],
                ['action'=> 'close', 'title'=> Yii::t('lang','No'), 'icon'=> 'css/images/chk_off.png'],
                  // ['action' => ['action'=>'open_url', 'action_url' => $array->url],
                  // 'title' => 'Apri link',
                  // ]
              ],

          );
          #echo '<pre>'.print_r($content,true).'</pre>';
          // trasformo il payload in json
          $pay_load = CJSON::encode($content);
          #echo '<pre>'.print_r($pay_load,true).'</pre>';

          foreach ($subscribe_array as $id => $array){
              $subscription = Subscription::create([
                  "endpoint" => $array['endpoint'],
                  "keys" => [
                      "p256dh" => $array['p256dh'],
                      "auth" => $array['auth']
                  ],
              ]);
              #echo '<pre>'.print_r($array,true).'</pre>';
              #echo '<pre>'.print_r($subscription,true).'</pre>';

              // inizializzo la classe
              $webPush = new WebPush($auth, $defaultOptions);
              $webPush->setDefaultOptions($defaultOptions);

              // invio il messaggio push
              $result = $webPush->sendNotification(
                  $subscription,
                  $pay_load,
                  true
              );
          }

          /**
           * Check sent results
           * @var MessageSentReport $report
           */
            if (isset($webPush)) {
                $save = new Save;
                foreach ($webPush->flush() as $report) {
                    $endpoint = $report->getRequest()->getUri()->__toString();

                    if ($report->isSuccess()) {
                        $save->WriteLog('libs','push','send',"Message sent successfully for subscription {$endpoint}.");
                    } else {
                        $save->WriteLog('libs','push','send',"[x] Message failed to sent for subscription {$endpoint}: {$report->getReason()}");
                    }
                }
            }
        }
    }
}
