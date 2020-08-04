<?php

class Notifi
{


  private function switchNotDesc($type)
  {
    $desc = [
        // old type_notifications
        'new'=>Yii::t('notify','new'),
        'complete'=>Yii::t('notify','complete'),

        // new type_notifications
        'token' => Yii::t('notify','Token'),
        'ether' => Yii::t('notify','Ether'),
    ];

    // echo '<pre>'.print_r($desc,true).'</pre>';
    // echo '<pre>'.print_r($type,true).'</pre>';
    // exit;

    return $desc[$type];
  }

    public function description($status,$type){
        switch (strtolower(trim($status))){
            //ricordati i case tutti minuscoli
			case 'new':
                $notifi__title = Yii::t('notify', 'You have sent a new {alias} transaction.',
                    array('{alias}'=>self::switchNotDesc($type))
                );
			    break;

          case 'complete':
              if ($type <> 'invoice'){
                  $notifi__title = Yii::t('notify', 'A token transaction has been completed.');
              }else{
                  $notifi__title = Yii::t('notify', 'A transaction has been completed.');
              }
      break;

          case 'expired':
              if ($type <> 'invoice'){
                  $notifi__title = Yii::t('notify', 'A token transaction has expired.');
              }else{
                  $notifi__title = Yii::t('notify', 'A transaction has expired.');
              }

        break;

            case 'help':
            case 'followed': // telegram contact added in address book
            case 'unfollowed': // telegram contact added in address book
                $notifi__title = '';
                break;


            default:
                $notifi__title = Yii::t('notify','New message');
		}
        return $notifi__title;
    }


  public function Icon($type_notification,$type_transaction=null){
      switch (strtolower(trim($type_notification))){
          //ricordati i case tutti minuscoli
          case 'help':
              $zmdicon = 'fa fa-question';
              break;

              case 'alarm':
                  $zmdicon = 'fa fa-exclamation';
                  break;


          // case 'fattura':
          //     $zmdicon = 'zmdi zmdi-collection-pdf';
          //     break;
          case 'invoice':
              $zmdicon = 'fab fa-btc';
              break;

          case 'token':
              $zmdicon = 'fa fa-star';
              break;

              case 'ether':
                  $zmdicon = 'fab fa-ethereum';
                  break;

          // case 'withdraw':
          //     $zmdicon = 'zmdi zmdi-balance';
          //     break;

          // case 'deposit':
          //     $zmdicon = 'fa fa-eur';
          //     break;

          case  'contact':
            $zmdicon = 'fa fa-users';
            break;


          case 'new':
          case 'complete':
          // case 'paid':
          // case 'confirmed':
          // case 'invoice':
            $zmdicon = 'fa fa-star';
              // $zmdicon = 'fab fa-btc';
              break;


          // case 'expired':
          // case 'invalid':
          // case 'failed':
          //     $zmdicon = 'fa fa-exclamation';
          //     break;

          default:
              $zmdicon = 'fa fa-info';
      }
      return $zmdicon;
  }
  public function Color($status){
      switch (strtolower(trim($status))){
          //ricordati i case tutti minuscoli
          case 'alarm':
              $color = 'bg-danger';
              break;

          case 'help':
              $color = 'bg-success';
              break;

          case 'fattura':
          case 'sending':
          case 'new':
              $color = 'bg-dark';
              break;

          case 'failed':
          case 'invalid':
              $color = 'bg-danger';
              break;

          case 'expired':
          case 'paidpartial':
              $color = 'bg-warning';
              break;

          case 'complete':
          case 'paid':
          case 'confirmed':
          case 'sent':
              $color = 'bg-success';
              break;

          case 'paidover':
              $color = 'bg-primary';
              break;

        case 'followed':
            $color = "bg-success";
            break;

        case 'unfollowed':
                $color = "bg-warning";
                break;

          default:
              $color = 'bg-secondary';
      }
      return $color;
  }

}
