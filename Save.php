<?php
/**
 * Class Settings
 * @author Sergio Casizzone
 * @class Classe che carica e salva i settings della WebApp
 */



class Save extends SaveModels
{

  // salva le transazioni
  public function Transaction($array,$action){
    return self::saveTransaction($array,$action);
  }

  // salva le informazioni dettagliate sulle transazioni
  public function CryptoInfo($idTransaction, $cryptoInfo){
    return self::saveCryptoInfo($idTransaction,$cryptoInfo);
  }

  // salva le informazioni dettagliate sulle invoice pos data
  public function PosData($idTransaction,$posData){
    return self::savePosData($idTransaction,$posData);
  }

  // salva le notifiche
  public function Notification($array,$admin=false){
    return self::saveNotification($array,$admin);
  }

  // salva le transazioni token
  public function Token($array){
    return self::saveETHTransaction($array);
  }

  // salva i messaggi delle transazioni token
  public function Memo($array){
    return self::saveMemo($array);
  }

  // salva le transazioni token
  public function PosInvoices($array){
    return self::savePosInvoices($array);
  }

  // salva i messaggi delle transazioni token
  public function PosMemo($array){
    return self::savePosMemo($array);
  }

  // salva i log dell'applicazione
  public function WriteLog($app, $controller, $action, $description, $die=false){
    return self::saveLog($app, $controller, $action, $description, $die);
  }

}
?>
