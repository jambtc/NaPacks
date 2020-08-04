<?php

class SaveModels
{
  /**
   * Salva la transazione sul DB e restituisce un object
   * @param: $array: è l'array contenente gli attributes da salvare
   * @param: $action ('new/update')
   *
   * @return: restituisce l'object degli attributes della transazione
   */
public function saveTransaction($array,$action){
      #echo "<pre>$action: ".print_r($array,true).'</pre>';
  if ($action == 'new'){
    $transaction = new Transactions;
          $transaction->attributes = $array;
      $transaction->insert();
  }else{
    $transaction = Transactions::model()->findByPk($array['id_transaction']);
          $transaction->attributes = $array;
      $transaction->update();
  }


  #echo '<pre>'.print_r($transaction,true).'</pre>';
  #exit;
  $return  = (object) $transaction->attributes;
  return $return;
}

/**
 * Salva le info delle transazioni sul DB
 * @param: $idTransaction: è l'id della transazione da aggiornare
 * @param: $cryptoInfo: è l'array contenente gli attributes da salvare
 *
 * @return: restituisce l'object degli attributes della transazione
 */

public function saveCryptoInfo($idTransaction, $cryptoInfo){
    if ($cryptoInfo){
        foreach($cryptoInfo as $index => $value){
            $txInfo = TransactionsInfo::model()->findByAttributes(['id_transaction'=>$idTransaction,'cryptoCode'=>$value['cryptoCode']]);
            if (null === $txInfo){
                // non trovo la transazione
                $txInfo = new TransactionsInfo;
                $txInfo->id_transaction = $idTransaction;
                $txInfo->cryptoCode = $value['cryptoCode'];
                $txInfo->paymentType = $value['paymentType'];
                $txInfo->rate = $value['rate'];
                $txInfo->paid = $value['paid'];
                $txInfo->price = $value['price'];
                $txInfo->due = $value['due'];
                $txInfo->address = $value['address'];
                $txInfo->txCount = $value['txCount'];
                if (!empty($value['payments'])){
                    $txInfo->txId = $value['payments'][0]['id'];
                    $date = new DateTime($value['payments'][0]['receivedDate']);
                    $txInfo->received = $date->getTimestamp();
                    $txInfo->value = $value['payments'][0]['value']*1;
                    $txInfo->destination = $value['payments'][0]['destination'];
                }
                $txInfo->insert();
            }else{
                // trovo la transazione
                $txInfo->cryptoCode = $value['cryptoCode'];
                $txInfo->paymentType = $value['paymentType'];
                $txInfo->rate = $value['rate'];
                $txInfo->paid = $value['paid'];
                $txInfo->price = $value['price'];
                $txInfo->due = $value['due'];
                $txInfo->address = $value['address'];
                $txInfo->txCount = $value['txCount'];
                if (!empty($value['payments'])){
                    $txInfo->txId = $value['payments'][0]['id'];
                    $date = new DateTime($value['payments'][0]['receivedDate']);
                    $txInfo->received = $date->getTimestamp();
                    $txInfo->value = $value['payments'][0]['value']*1;
                    $txInfo->destination = $value['payments'][0]['destination'];
                }
                $txInfo->update();
            }
            $return[]  = (object) $txInfo->attributes;
        }
    }else{
        return false;
    }
    #echo '<pre>'.print_r($transaction,true).'</pre>';
    #exit;
    return $return;
}
/**
 * Salva le info delle transazioni posData sul DB
 * @param: $idTransaction: è l'id della transazione da aggiornare
 * @param: $posData: è l'array contenente i prodotti da salvare
 *
 * @return: restituisce l'object degli attributes della transazione
 */

public function savePosData($idTransaction, $posData){
    if ($posData){
        $value = CJSON::decode($posData);
        $txInfo = TransactionsData::model()->findByAttributes(['id_transaction'=>$idTransaction]);
        if (null === $txInfo){
            // non trovo la transazione
            $txInfo = new TransactionsData;
            $txInfo->id_transaction = $idTransaction;
            $txInfo->cart = CJSON::encode($value['cart']);
            $txInfo->customAmount = $value['customAmount'];
            $txInfo->discountPercentage = $value['discountPercentage'];
            $txInfo->subTotal = $value['subTotal'];
            $txInfo->discountAmount = $value['discountAmount'];
            $txInfo->tip = $value['tip'];
            $txInfo->total = $value['total'];
            $txInfo->insert();
        }else{
            // trovo la transazione
            $txInfo->cart = CJSON::encode($value['cart']);
            $txInfo->customAmount = $value['customAmount'];
            $txInfo->discountPercentage = $value['discountPercentage'];
            $txInfo->subTotal = $value['subTotal'];
            $txInfo->discountAmount = $value['discountAmount'];
            $txInfo->tip = $value['tip'];
            $txInfo->total = $value['total'];
            $txInfo->update();
        }
        $return[]  = (object) $txInfo->attributes;
    }else{
        return false;
    }

    return $return;
}

    /**
     * SALVA LE NOTIFICHE PER GLI UTENTI E GIL ADMIN
     * Se $admin == true salva anche per gli admin
     */
    public function saveNotification( $array, $admin=false ){
        $model = new Notifications;

        $model->attributes = $array;
        $model->id_user = $array['id_user'];
        $model->insert();

        //Aggiorna le notifiche da leggere per l'utente
        $this->saveReader($model->id_user,$model->id_notification);

        if ($admin){
            // Carico tutti gli admin
            $criteria=new CDbCriteria();
            $criteria->compare('id_users_type',3,false);
            $adminUsers = Users::model()->findAll($criteria);

            $adminList = CHtml::listData($adminUsers,'id_user' , 'id_user');
            foreach ($adminList as $id)
                $this->saveReader($id,$model->id_notification);
        }

        return (object) $model->attributes;
    }

    private function saveReader($id_user,$id_notification){
        $readers = new Notifications_readers;
        $readers->id_user = $id_user;
        $readers->id_notification = $id_notification;
        $readers->alreadyread = 0;
        $readers->insert();

        return true;
    }


    // salva le transazioni token
    public function saveETHTransaction($array){
        $model = new Tokens;
        $model->attributes = $array;

        if (!$model->save()){
            echo CJSON::encode(array("error"=>'Error: Cannot save transaction.'));
            exit;
        }
        return (object) $model->attributes;
    }

    // salva il messaggio delle transazioni token
    public function saveMemo($array){
        $model = new TokensMemo;
        $model->attributes = $array;

        if (!$model->save()){
          echo CJSON::encode(array("error"=>'Error: Cannot save memo transaction.'));
          exit;
        }
        return (object) $model->attributes;
    }


    // salva le invoice token
    public function savePosInvoices($array){
        $model = new PosInvoices;
        $model->attributes = $array;

        if (!$model->save()){
            echo CJSON::encode(array("error"=>'Error: Cannot save invoice transaction.'));
            exit;
        }
        return (object) $model->attributes;
    }

    // salva il messaggio delle transazioni token
    public function savePosMemo($array){
        $model = new PosInvoicesMemo;
        $model->attributes = $array;

        if (!$model->save()){
          echo CJSON::encode(array("error"=>'Error: Cannot save memo invoice transaction.'));
          exit;
        }
        return (object) $model->attributes;
    }

    /**
     * Salva il log dell'applicazione
     * @param string $app L'applicazione che richiama il log
     * @param string $controller Il Controller
     * @param string $action Azione del controller
     * @param string $description Descrizione operazione
     * @param string $die true/false Impone l'arresto dell'applicazione
    */
    public function saveLog($app, $controller, $action, $description, $die=false)
    {
        $timestamp = time();
        $id_user = 1; // 1st administrator
        $remoteAddress = self::get_client_ip_server();
        $browser = 'localhost';

        if (isset(Yii::app()->user->objUser) && isset(Yii::app()->user->objUser['id_user']))
            $id_user = Yii::app()->user->objUser['id_user'];

        if (isset($_SERVER['HTTP_USER_AGENT']))
            $browser = $_SERVER['HTTP_USER_AGENT'];


        $model = new Log;
        $model->timestamp = $timestamp;
        $model->id_user = $id_user;
        $model->remote_address = $remoteAddress;
        $model->browser = $browser;
        $model->app = $app;
        $model->controller = $controller;
        $model->action = $action;
        $model->description = $description;
        $model->die = ($die === true ? 1 : 0);

        if (!$model->save()){
            echo CJSON::encode(array(
                "error"=>'Error: Cannot save log.',
                'attributes' => CJSON::encode($model->attributes)."</pre>"
            ));
            die();
        }

        if ($die){
            echo CJSON::encode(["error"=>$description]);
        	die();
        }

        return (object) $model->attributes;
    }

    // Function to get the client ip address
    private function get_client_ip_server() {
        $ipaddress = '';
        if (array_key_exists('HTTP_CLIENT_IP', $_SERVER))
            $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
        else if(array_key_exists('HTTP_X_FORWARDED_FOR', $_SERVER))
            $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
        else if(array_key_exists('HTTP_X_FORWARDED', $_SERVER))
            $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
        else if(array_key_exists('HTTP_FORWARDED_FOR', $_SERVER))
            $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
        else if(array_key_exists('HTTP_FORWARDED', $_SERVER))
            $ipaddress = $_SERVER['HTTP_FORWARDED'];
        else if(array_key_exists('REMOTE_ADDR', $_SERVER))
            $ipaddress = $_SERVER['REMOTE_ADDR'];
        else
            $ipaddress = 'UNKNOWN';

        return $ipaddress;
    }


}
