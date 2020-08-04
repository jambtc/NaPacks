<?php
/**
 * @author Sergio Casizzone
 * @class Classe che raccoglie funzioni in comune per i diversi controller
 *
 *  -                               NaPay   POS     Wallet-tts  Bolt
 *     1. WebApp::dateLN               x                           x
 *     2. WebApp::translateMsg         x       x       x           x
 *     3. WebApp::typeTransaction      x       x       x           x
 *     4. WebApp::isMobileDevice       x       x       x           x
 *     5. WebApp::data_it              x
 *     6. WebApp::data_eng             x       x       x
 *     7. WebApp::walletStatus         x       x       x           x
 *     8. WebApp::showPasswordButton   x
 *     9. WebApp::checkYesOrNot        x
 *     10. WebApp::statusList           x       x
 *     11. WebApp::isEthAddress         x               x          x
 *     12. WebApp::ContaSollecitiPagamenti x
 *     13. WebApp::StatoPagamenti       x       x       x
 *     14. WebApp::timeToLocalDate     ?        x       ?          x
 *     15. WebApp::showMessageRows              x       x          x
 *     16. WebApp::typePrice                    x       x          x
 *     17. WebApp::isBtcAddress                 x
 *     18. WebApp::isConfirmedLock                      x          x
 *     19. WebApp::addButton                                       x
 */

Yii::app()->language = ( isset($_COOKIE['lang']) ? $_COOKIE['lang'] : 'it' );
Yii::app()->sourceLanguage = ( isset($_COOKIE['langSource']) ? $_COOKIE['langSource'] : 'it_it' );

class WebApp {
    /**
     * converte la risposta degli status dei diversi exchange in stati comuni
     * @param: $idExchange: è l'id dell'exchange
     * @param: $status: è il codice dello stato
     *
     * @return: restituisce lo stato
     * - new (richiesta creata)
     * - failed (richiesta fallita)
     * - invalid (richiesta annullata o respinta)
     * - complete (richiesta completata)
     *
     * bitstamp 0 (open), 1 (in process), 2 (finished), 3 (canceled) or 4 (failed).
     * binance 0(0:Email Sent,1:Cancelled 2:Awaiting Approval 3:Rejected 4:Processing 5:Failure 6Completed)
     *
     * In comune hanno:
     * - 0,1 -> 0,4,2 : Processing 	(NEW)
     * - 4 -> 5 : Failure		(FAILED)
     * - 3 -> 1,3 : Canceled	(CANCELED)
     * - 2 -> 6 : Completed  	(COMPLETED)
     */

  /**
 	 * @return data e time su 2 righe
 	 * Per le transazioni token
 	 */
 	 public function dateLN($timestamp,$id=null){
 		$date = date("d M `y",$timestamp);
 		$time = date("H:i",$timestamp);
 		$return = '';
 		if ($id){
 			$return = '<p class="mobile-not-show" style="margin-bottom:0px;">'.substr(crypt::encrypt($id),0,20).'...</p>';
 		}
    $return .= '<div class="desktop-is-hidden"><div style="font-size:0.85em;">'.$date.' - '.$time.'</div></div>';
    $return .= '<div class="mobile-is-hidden"><div style="font-size:0.85em;">'.$date.'</div><div style="font-size:0.75em; text-align:center;" >'.$time.'</div></div>';
 		return $return;
 	}

  /**
 	 *
 	 * @return data e time su 2 righe
 	 * Per le transazioni token
 	 */
 	 public function dateView($timestamp){
 		$date = date("d M Y",$timestamp);
 		$time = date("H:i:s",$timestamp);
 		$return = $date.' - '.$time;

 		return $return;
 	}

    /**
	 * Traduci i messaggi nella view Messages
	 */
	public function translateMsg($msg)
	{
		$findParam = explode('{',$msg);

		if (!isset($findParam[1])){
			$messaggio = $findParam[0];
		}else{
			$findEnd = explode('}',$findParam[1]);
			$param[0] = $findEnd[0];
			$messaggio = $findEnd[1];
		}
		if (isset($param[0])){
			// PHP YIIC MESSAGE TRADUZIONE! QUESTA RIGA MANDA IN ERRORE IL SOFTWARE Yii
			// in caso di aggiunta traduzioni, eliminare, eseguire il comando e riaggiungere!
			// TODO: fix questo bug di Yii
			$response =	Yii::t('notify','{param0} '. trim($messaggio),array('{param0}'=>$findEnd[0]));
		}else{
			$response =	Yii::t('notify',$messaggio);
		}
		return $response;
	}

    /**
	* Questa funziona mostra a video il tipo di transazione e se è ether o token
	*/
	public function typeTransaction($type){
		if ($type == 'ether')
			return '<i class="fab fa-ethereum"></i>';
		else
			return '<i class="zmdi zmdi-star-outline"></i>';
	}


    /**
	 * check if is mobile device
	 * @return boolean true / false
	 */
	public function isMobileDevice(){
		$useragent[0]=$_SERVER['HTTP_USER_AGENT'];
		$useragent[1]=substr($useragent[0],0,4);

		$mobileUA[0] = '/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino/i';
		$mobileUA[1] = '/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i';

		$result = false;
		for ($x=0; $x<2;$x++){
			if ( preg_match ($mobileUA[$x], $useragent[$x]) ){
				$result = true;
			}
		}
		return $result;
    }

    /**
     *
     * @return data_it
	 * restituisce la data in formato italiano
     */
	 public function data_it($data){
		 if ($data === null)
		 	return true;
		//verifico che non ci siano '/'
		$data = str_replace("/","-",$data);

		// Creo una array dividendo la data YYYY-MM-DD sulla base del trattino
		$array = explode("-", $data);

		if (count($array) != 3)
  	  		return false;

		// Riorganizzo gli elementi in stile DD/MM/YYYY
		$data_it = $array[2]."/".$array[1]."/".$array[0];

		// Restituisco il valore della data in formato italiano
		return $data_it;
	}

    /**
     *
     * @return data_eng
	 * restituisce la data in formato inglese
     */
	 public function data_eng($data){
		 if ($data === null)
		 	return true;

		 //verifico che non ci siano '/'
 		$data = str_replace("/","-",$data);

	  // Creo una array dividendo la data dd/mm/yyyy sulla base dello slash
	  $array = explode("-", $data);
	  if (count($array) != 3)
	  	return false;

	  // Riorganizzo gli elementi in stile yyyy/mm/dd
	  $data = $array[2]."-".$array[1]."-".$array[0];

	  // Restituisco il valore della data nel nuovo formato
	  return $data;
	}

    public function convertExchangeStatus($idExchange,$status)
    {
        // 1: Bitstamp
        // 2: Binance
        $ExchangeStatus = [
            1 => [
                0 => 'new',
                1 => 'new',
                2 => 'paid',
                3 => 'invalid',
                4 => 'failed'
            ],
            2 => [
                0 => 'new',
                1 => 'invalid',
                2 => 'new',
                3 => 'invalid',
                4 => 'new',
                5 => 'failed',
                6 => 'paid'
            ]
        ];
        return $ExchangeStatus[$idExchange][$status];
    }

    /**
     * Questa funzione VERIFICA se ci sono informazioni posData sulla transazione
     * @param float $id è l'id della transazione
    */
    public function issetPosData($id)
    {
        $criteria=new CDbCriteria;
        $criteria->compare('id_transaction',$id,false);

        $txInfo = new CActiveDataProvider('TransactionsData', array(
			'criteria'=>$criteria,
		));

        if (null === $txInfo)
            return false;
        else
            return true;

    }

    /**
     * Questa funzione MOSTRA A VIDEO le informazioni posData sulla transazione
     * @param float $id è l'id della transazione
    */
    public function showPosData($id)
    {
        $criteria=new CDbCriteria;
        $criteria->compare('id_transaction',$id,false);

        $txInfo = new CActiveDataProvider('TransactionsData', array(
			'criteria'=>$criteria,
		));

        if (null === $txInfo)
            return false;

            $show = '<table class="table table-borderless table-striped">';
            $show .= '<tbody>';

            $iterator = new CDataProviderIterator($txInfo);
            $x=0;
            foreach($iterator as $model) {
                // echo '<pre>'.print_r($model,true).'</pre>';
                // exit;
                $colorClass = ($x % 2 == 0) ? 'even' : 'odd';

                $cart = CJSON::decode($model->cart);
                // echo '<pre>'.print_r($cart,true).'</pre>';
                // exit;

                foreach ($cart as $id => $product){
                    $show .= '
                    <tr class="'.$colorClass.'">
                        <th style="text-align:left; width:1%; min-width:130px;">'
                        .CHtml::image($product['image'],$product['title'],array("class"=>"image img-square img-120")) .
                        '</th>

                        <th style="text-align:left; width:99%;">
                            <table class="table table-striped text-dark table-data4 table-wallet" style="width: 100%;">
                            <tbody>
                                <tr><th align=left style="width:1%; min-width:60px;">Titolo</th><td align=left>'.$product['title'].'</td></tr>
                                <tr><th align=left>Prezzo</th><td align=left>'.$product['price']['formatted'].'</td></tr>
                            </tbody>
                            </table>
                        </th>
                    </tr>
                    ';
                }

                $show .= '
                    <tr><th>Importo Manuale</th><td>'.$model->customAmount.' €</td></tr>
                    <tr><th>Sub totale</th><td>'.$model->subTotal.' €</td></tr>
                    <tr><th>Sconto</th><td>'.$model->discountAmount.' €</td></tr>
                    <tr><th>Mancia</th><td>'.$model->tip.' €</td></tr>
                    <tr><th>Totale</th><td>'.$model->total.' €</td></tr>
                </tbody>
                </table>
                ';

                $x++;
            }
            $show .= '</tbody></table>';
            return $show;

    }






    /**
     * Questa funzione MOSTRA A VIDEO LE informazioni di una transazione
     * @param float $id è l'id della transazione
    */
    public function showCryptoInfo($id)
    {
        $criteria=new CDbCriteria;
        $criteria->compare('id_transaction',$id,false);

        $txInfo = new CActiveDataProvider('TransactionsInfo', array(
			'criteria'=>$criteria,
		));

        // echo '<pre>'.print_r($txInfo,true).'</pre>';
        // exit;

        if (null === $txInfo)
             return false;

        $show = '<table class="table table-borderless table-striped" style="ma x-width:100px;">';
        $show .= '<tbody>';

        $iterator = new CDataProviderIterator($txInfo);
        $x=0;
        foreach($iterator as $model) {
            // echo '<pre>'.print_r($model,true).'</pre>';
            // exit;
            $colorClass = ($x % 2 == 0) ? 'even' : 'odd';

            $show .= '<tr class="'.$colorClass.'">
                <th style="text-align:left; width:10px; padding:0px;" class="text-primary"><h4>'.$model->cryptoCode.'</h4></th>
                <th style="text-align:left;">
                    <table class="table table-borderless table-striped ">
                    <tbody>
                        <tr><th style="max-width:100px;">Tipo&nbsp;Pagamento</th><td>'.$model->paymentType.'</td></tr>
                        <tr><th>Tasso</th><td>'.$model->rate.'</td></tr>
                        <tr class="text-info"><th>Pagato</th><td>'.$model->paid.'</td></tr>
                        <tr><th>Prezzo</th><td>'.$model->price.'</td></tr>
                        <tr><th>Dovuto</th><td>'.$model->due.'</td></tr>
                        ';
            if ($model->txCount >0){
                $show .= '
                <tr><th>Id Transazione</th><td><a href="'.Yii::app()->params["blockchainCheck"].substr($model->txId,0,-2).'" target="_blank">'.substr($model->txId,0,-2).'</a></td></tr>
                <tr><th>Data</th><td>'.date("d/m/Y H:i:s",$model->received).'</td></tr>
                <tr><th>Valore</th><td>'.$model->value.'</td></tr>
                <tr><th>Indirizzo</th><td><a href="'.Yii::app()->params["blockchainCheck"].$model->destination.'" target="_blank">'.$model->destination.'</a></td></tr>
                ';
            }else{
                $show .= '<tr><th>Indirizzo</th><td><a href="'.Yii::app()->params["blockchainCheck"].$model->address.'" target="_blank">'.$model->address.'</a></td></tr>';
            }
            $show .= '</tbody></table>
                </th>
            </tr>';

            $x++;
        }
        $show .= '</tbody></table>';
        return $show;

    }


    /**
     * Questa funzione MOSTRA A VIDEO LE COIN Abilitate
     * @param JSON $json è il contenuto delle coin con l'importo max spendibile
    */
    public function coinsEnabledViewMerchants($json)
    {
        $array = CJSON::decode($json);
        if (!(is_array($array)))
          return false;

        $show = '<table class="table table-stripe" style="max-width:100px;">';
        $show .= '<thead><tr class="text-primary"><th>Coin</th></tr></thead><tbody>';
        foreach ($array as $id => $value) {

            $show .= '<tr>
                <th align=center>'.$value.'</th>
            </tr>';
        }
        $show .= '</tbody></table>';
        return $show;
    }
    /**
     * Questa funzione MOSTRA A VIDEO LE COIN Abilitate
     * @param JSON $json è il contenuto delle coin con l'importo max spendibile
     * VERIFICA SE SERVE ANCORA !!!!!
     * DA ELIMINARE !!!!!!!!!!!!!!!
    */
    public function coinsEnabledView($json)
    {
        $array = CJSON::decode($json);
        if (!(is_array($array)))
          return false;

          // echo '<pre>invoice'.print_r($array,true).'</pre>';
      		// exit;

        $show = '<table class="table table-stripe" style="max-width:400px;">';
        $show .= '<thead>
            <tr class="text-primary">
                <th>Coin</th>
                <th>Massimo erogabile</th>
                  <th>Minimo erogabile</th>
                <th><center>Fee</center></th>
                <th><center>Conferme</center></th>
                <th><center>Monitoraggio</center></th>
            </tr></thead><tbody>';
        foreach ($array as $coin => $value) {

            $show .= '<tr>
                <th align=center>'.$coin.'</th>
                <td align=center>'.$value["maxBuy"].'</td>
                <td align=center>'.$value["minBuy"].'</td>
                <td align=center>'.$value['percFee'].'</td>
                <td align=center>'.$value['speedPolicy'].'</td>
                <td align=center>'.$value['monitoringExpiration'].'</td>
            </tr>';
        }
        $show .= '</tbody></table>';
        return $show;
    }

    /**
     * Questa funzione MOSTRA A VIDEO gli address delle mpk
     * @param JSON $json è il contenuto delle coin con l'importo max spendibile
     * VERIFICA SE SERVE ANCORA !!!!!
     * DA ELIMINARE !!!!!!!!!!!!!!!
    */
    public function mpkAddressesView($json)
    {
        $array = CJSON::decode($json);
        if (!(is_array($array)))
          return false;

        $show = '<table class="table table-stripe" style="max-width:100px;">';
        $show .= '<thead><tr class="text-primary"><th></th><th>Indirizzi</th></tr></thead><tbody>';
        foreach ($array as $id => $value) {

            $show .= '<tr>
                <th align=center>'.($id+1).'</th>
                <th align=center>'.$value.'</th>
            </tr>';
        }
        $show .= '</tbody></table>';
        return $show;
    }




    // Questa funzione recupera il FIAT RATE in EURO
    // per il token non passo nulla e il rapporto è 1 <=> 1
    // se $type == null restituisco tutti
  	public function getFiatRate($type=null){
        $url = 'https://www.bitstamp.net/api/v2/ticker/btceur';
        $result = json_decode(BTCPayWebRequest::request($url,array(),'GET'),true);

        switch(strtolower($type)){
            // case 'eth':
            //     $value = $result['symbols']['ETHEUR']['last'];
            //     break;

            case 'btc':
                $value = $result['data']['last'];
                break;

            case 'token':
                $value = 1;
                break;

            default:
                $value = $result['data']['last'];
        }

  		return $value;
  	}



    /**
	 * Questa funzione recupera la lista delle coin da gestire
	 * @return ARRAY
	*/
	public function getPreferredCoinList()
	{
		$listaCoin = CHtml::listData(Assets::model()->findAll(), 'ticket', function($row) {
			return $row->ticket.' ('.$row->denomination.')';
		});
		return $listaCoin;
	}

    /**
     *
     * @return walletStatus
	 * restituisce un valore in base al valore $status dell'invoice (sent, complete,expired, ecc...)
     */
	 public function walletStatus($value){
		 switch (strtolower($value)){
  			case 'complete':
  				return '<span class="btn btn-outline-success"style="padding: 1px 5px 1px 5px;">'.ucfirst(self::translateMsg($value)).'</span>';
  			case 'failed':
  			case 'invalid':
  				return '<span class="btn btn-outline-danger" style="padding: 1px 5px 1px 5px;">'.ucfirst(self::translateMsg($value)).'</span>';
 			case 'canceled':
 				return '<span class="btn btn-outline-danger" style="padding: 1px 5px 1px 5px;">'.ucfirst(self::translateMsg($value)).'</span>';
  			case 'sending':
  			case 'new':
  				return '<span class="btn btn-secondary" style="padding: 1px 5px 1px 5px;">'.ucfirst(self::translateMsg($value)).'</span>';
  			case 'sent':
  				return '<span class="btn btn-outline-success" style="padding: 1px 5px 1px 5px;">'.ucfirst(self::translateMsg($value)).'</span>';
  			case 'expired':
  				return '<span class="btn btn-outline-warning" style="padding: 1px 5px 1px 5px;">'.ucfirst(self::translateMsg($value)).'</span>';
  			case 'paidpartial':
  				return '<span class="btn btn-outline-warning" style="padding: 1px 5px 1px 5px;">'.ucfirst(self::translateMsg($value)).'</span>';
  			case 'paidover':
  				return '<span class="btn btn-outline-primary" style="padding: 1px 5px 1px 5px;">'.ucfirst(self::translateMsg($value)).'</span>';
  			case 'paid'	:
  				return '<span class="btn btn-outline-success" style="padding: 1px 5px 1px 5px;">'.ucfirst(self::translateMsg($value)).'</span>';
  			case 'confirmed'	:
  				return '<span class="btn btn-outline-success" style="padding: 1px 5px 1px 5px;">'.ucfirst(self::translateMsg($value)).'</span>';
			case 'followed'	:
				return '<span class="btn btn-outline-success" style="padding: 1px 5px 1px 5px;">'.ucfirst(self::translateMsg($value)).'</span>';
			case 'unfollowed'	:
				return '<span class="btn btn-outline-warning" style="padding: 1px 5px 1px 5px;">'.ucfirst(self::translateMsg($value)).'</span>';
			case 'help'	:
	 			return '<span class="btn btn-outline-success" style="padding: 1px 5px 1px 5px;">'.ucfirst(self::translateMsg($value)).'</span>';

			default:
  				return $value;
  		}
	}

    /**
     *
     * @return showPassword
	 * restituisce l'immagine con la funzione e il valore selezionato
     */
	public function showPasswordButton($id_merchant){
		$BpsUsers = BpsUsers::model()->findByAttributes(array("id_merchant"=>$id_merchant));

		if (null === $BpsUsers)
			$ret = '';
		else if (!isset($BpsUsers->bps_auth))
			$ret = '';
		else
			$ret = "<a href='#' onclick='showPassword(".$id_merchant.");'>
				<button type='button' data-toggle='modal' data-target='#passwordModal' title='Mostra password'>
						<i class='fa fa-eye text-primary'></i>
					</button>
					</a>";

		return $ret;
	}

    /**
     *
     * @return checkYesOrNot
	 * restituisce un valore 'Si' o 'No' in base a quanto ricevuto (0,1)
     */
	public function checkYesOrNot($value){
		$ret = 'No';
		if ($value == 1)
			$ret = 'Si';
		// Restituisco il valore
		return $ret;
	}

    /**
     *
     * @return statusList
	 * restituisce un array per btc o per token
     */
	 public function statusList($page){
		 switch ($page){
			 case 'pagamenti':
				 return [
					 'complete' => 'Completato',
					 'failed' => 'Fallito',
					 'invalid' => 'Non valido',
					 'canceled' => 'Cancellato',
				 ];
			 	break;

			default:
				return [
					'complete' => 'Completata',
					'failed' => 'Fallita',
					'invalid' => 'Non valida',
					'new' => 'In corso...',
					'expired' => 'Scaduta',
					'paid' => 'Pagata',
					'confirmed' => 'Confermata',
				];
				break;
		 }
 	}

    public function ContaSollecitiPagamenti($id_user){
		$userSettings = Settings::loadUser($id_user);
		if (!isset($userSettings->SollecitiPagamenti))
			return 0;
		else
			return $userSettings->SollecitiPagamenti;
	}

    public function StatoPagamenti($id_user,$timelapse=false,$scadenza=false){
		$timestamp = time();
		$criteria = new CDbCriteria();
		$criteria->compare('id_user',$id_user, false);
		$criteria->addCondition('progressivo != 0');

		//$provider = Pagamenti::model()->OrderByDateDesc()->findAll($criteria);
		$provider = Pagamenti::model()->Paid()->OrderByIDDesc()->findAll($criteria);
		if ($provider === null)
			$expiration_membership = $timestamp;
		else{
			$provider = (array) $provider;
			if (count($provider) == 0)
				$expiration_membership = 1;
			else
				$expiration_membership = strtotime($provider[0]->data_scadenza);
		}

		$data1 = new DateTime(date('Y-m-d', $expiration_membership));
		$data2 = new DateTime(date('Y-m-d', $timestamp ));
		$interval = $data1->diff($data2);

		if ($timestamp < $expiration_membership){
			if ($timelapse)
				return -$interval->format('%a');
			else
				if ($interval->format('%a') > 0 && $interval->format('%a') < 45)
					$text = $interval->format('<span class="status--warning">Scade tra <b>%a</b> giorni</span>');
				else
					$text = $interval->format('Valida');
		}else{
			if ($interval->format('%a')>17000)
				$text = '<span class="status--denied">Nessun pagamento</span>';
			else if ($interval->format('%a')>46 && $interval->format('%a')<=17000)
				$text = $interval->format('<span class="status--denied">Scaduta da %a giorni</span>');
			else if ($interval->format('%a') == 45)
				$text = $interval->format('<span class="status--denied">Ultimo giorno di validità iscrizione (%a gg)</span>');
			else{
				if ($scadenza)
					$text = $interval->format('<span class="status--denied">Scaduta il '.date("d/m/Y",$expiration_membership).'</span>');
				else
					$text = $interval->format('<span class="status--denied">In ritardo di %a giorni</span>');
			}
		}
		if ($timelapse)
			return $interval->format('%a');
		else
			return $text;
	}

    /**
    * FUNZIONI POS
    *
    * -------------------------------------------------------------------------
    */

    /**
	* Questa funzione dovrebbe tradurre la data nel linguaggio impostato in LOCALE
	*/
	public function timeToLocalDate($timestamp)
	{
		/* TODO: Creare funzione che imposti data in LOCALE */
		// setlocale(LC_TIME, Yii::app()->language);
		//
		// $d = date("d",$timestamp).chr(32);
		// $M = date("M",$timestamp).chr(32);


		// $date = strftime('%A %d %B %Y');
		// echo "<pre>".print_r($month_name,true)."</pre>";
		// exit;
		// ('d M Y - H:i:s',$notify->timestamp)
		// <span class="date text-primary">'.strftime('%d %M %Y - %H:%i:%s',
		// 	mktime(
		// 		date("d",$notify->timestamp),
		// 		date("M",$notify->timestamp),
		// 	)
		// ).'</span>
		//
		// }

		// $localdate = $d.$M;
		// $localdate = strftime('%d %M %Y - %H:%i:%s',mktime(0,0,0,$d,$M,0));
		$localdate = date('d M Y - H:i:s',$timestamp);

		return $localdate;
	}


    /*
	* Mostra singole righe delle notifiche
	* in uso in POS, Wallet-tts
	*/
    public function showMessageRows($timestamp,$id_tocheck,$status,$url,$type,$description,$price)
	{
        $notifi__icon = Notifi::Icon(
            (strpos($description,'token') !== false ? 'token' : $type )
        );
		$notifi__color = Notifi::Color($status);

		$table = '
			<table width=100% class="table text-light bg-messages">
				<tr>
					<!-- <td rowspan=1 style="vertical-align:middle;">
					 	<div class="notifi__item notifi__item-list"><div class="'.$notifi__color.' img-cir img-40"><i class="'.$notifi__icon.'"></i></div></div>
					</td> -->
					<td>'.self::dateLN($timestamp).'</td>
					<td>'.self::walletStatus($status).'</td>
					<td>'.($type == "help" ||  $type == "contact" ? "" : $price).'</td>
					<td></td>
				</tr>
				<tr>
					<td colspan=4>'.self::translateMsg($description).'</td>
					<td></td>
				</tr>
			</table>
		';

		$return = CHtml::link(
			$table,
			// Yii::app()->createUrl("tokens/view",["id"=>crypt::Encrypt($id_tocheck)]),
			$url,
			array('style' => 'width:100%;')
		);
		return $return;
	}

    /**
	* Questa funziona mostra a video il tipo di transazione e se è stata inviata o ricevuta
	*/
	public function typePrice($price,$sent){
		return ($sent == 'sent' ? '<h5 class="text-warning">-' : '<h5 class="text-success">+') . $price . '</h5>';
	}

    public function isEthAddress($value){
		if (strlen($value) == 42) // è un indirizzo
			return '/account/'.$value;
		else
			return '/tx/'.$value;
	}

    /**
     *
     * @return checkYesOrNot
	 * restituisce un valore 'Si' o 'No' in base a quanto ricevuto (0,1)
     */
	public function isBtcAddress($value){
		if (substr($value,0,4) == 'http')
			return $value;
		else
			return Yii::app()->params["blockchainCheck"].CHtml::encode($value);
	}

    public function isConfirmedLock($actualBlock,$walletBlock){
		$confirms = $actualBlock - $walletBlock;
		if ($confirms <0) $confirms = 0;

		if ($confirms < 6)
			return "<i class='fa fa-unlock' style='color:red;'></i><span style='font-size:0.8em;'>".$confirms."</span>";
		else
			return "<i class='fa fa-lock' style='color:green;'></i><span style='font-size:0.8em;'>6+</span>";
	}

    /**
    * FUNZIONI BOLT
    *
    * -------------------------------------------------------------------------
    */

    /**
	 * Questa funzionae cerca l'utente a cui appartiene l'indirizzo tra:
	 * - socialUser: utenti di bolt
	 * - np_user: utenti di token-tts
	 */
	public function fromUser($address)
	{
		$BoltWallets = Wallets::model()->findByAttributes(['wallet_address'=>$address]);
		if (null === $BoltWallets){
			$NapayWallets = WalletsNapay::model()->findByAttributes(['wallet_address'=>$address]);
			if (null === $NapayWallets){
				return $address; // CHtml::encode($address);
			}else{
				//cerca utente napay
				$NapayUser = UsersNapay::model()->findByPk($NapayWallets->id_user);
				if ($NapayUser->corporate == 1){
					return '<b class="text-success">'. $NapayUser->denomination. '</b> '.$address;
				}else{
					return '<b class="text-success">'. ucfirst($NapayUser->name).' '.ucfirst($NapayUser->surname). '</b> '.$address;
				}
			}
		}else{
			$socialUser = Socialusers::model()->findByAttributes(['id_user'=>$BoltWallets->id_user]);
			if (null === $socialUser){
				$BoltUser = Users::model()->findByPk($BoltWallets->id_user);
				return '<b class="text-success">'. $BoltUser->email. '</b> '.$address;
			}else{
				$return = '<b class="text-success">';
				$return .= (!empty($socialUser->first_name)) ? ucfirst($socialUser->first_name).' '.ucfirst($socialUser->last_name) : $socialUser->email;
				$return .= '</b> '.$address;
				// return '<b class="text-success">'. ucfirst($socialUser->first_name).' '.ucfirst($socialUser->last_name). '</b> '.$address;
				return $return;
			}
			//echo '<div class="account-item clearfix">'. CHtml::image($socialUser->picture, "img", array("class"=>"image img-cir img-120")) ."</div>" . CHtml::image("css/images/".$socialUser->oauth_provider.".svg", "img", array("class"=>"imgProvider image img-cir img-40"))  ;
			// echo CHtml::image($socialUser->picture, "img", array("class"=>"image img-cir img-120")) ;
		}


	}

    /**
	 * show button to add contact
	 */
	public function addButton($id_social)
	{
		$contact = Contacts::model()->findByAttributes(['id_social'=>$id_social,'id_user'=>Yii::app()->user->objUser['id_user']]);
		if (null === $contact)
			// echo '<a href="#" onclick="addcontact('.$id_social.')"><button id="add_button_'.$id_social.'" class="addContact btn alert-success text-light img-cir" style="padding:2.5px; width:30px; height:30px;"><i class="fa fa-plus  "></i></button></a>';
			echo '<a href="#" onclick="addContact('.$id_social.');" id="add_contact_'.$id_social.'"><button class="btn alert-success text-light img-cir" style="padding:2.5px; width:30px; height:30px;"><i class="fa fa-plus  "></i></button></a>';
		else
			echo '<button class="btn alert-secondary text-light img-cir" style="padding:2.5px; width:30px; height:30px;"><i class="fa fa-check  "></i></button>';
	}

    /**
     * Recupera casualmente il primo nodo POA disponibile
     * @return nodeurl
     */
    public function getPoaNode()
    {
        $nodelist = CHtml::listData(Nodes::model()->findAll(), 'id_node', function($item) {
        	return $item->url.':'.$item->port;
        });

        $isdown = true;
        shuffle($nodelist);
        do {
            $node = array_shift($nodelist);
            if (empty($node)){
                return false;
            }

            if( webRequest::url_test( $node ) ) {
                $isdown = false;
            }
        } while ($isdown);

        return $node;
    }

    public function CoingateInitialize($api_auth_token)
  	{
  		\CoinGate\CoinGate::config(
  			array(
  				'auth_token'    => $api_auth_token,
  				'environment'   => 'live',
  				//'environment'   => 'sandbox',
  				'user_agent'    => ('CoinGate - myPlugin v. 1.0'),
  			)
  		);
  	}

}
?>
