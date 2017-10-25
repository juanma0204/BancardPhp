<?php
class BancardComponent extends Component{
  /*
  * funcion para genera el procces para realizar el pago de la transaccion
    $v_amount decimal monto de la transacion
    $v_descripcion varchar descripcion dl pago
    $v_shop_process_id varchar numero identificardor enviado a bancard
  */
  public function bancarVposGenerarProccess($v_amount,$v_description,$v_shop_process_id){
    $host = "https://vpos.infonet.com.py";
    $url = $host."/vpos/api/0.3/single_buy";
    $v_currency = "PYG";
    $v_additional_data = json_encode(array());
    $v_return_url = "$url_success";
    $v_cancel_url = "$url_cancel";
    $v_token = PRIVATEKEY . $v_shop_process_id . $v_amount . $v_currency;
    $v_token = md5($v_token);
    $_SESSION['shop_proccess_id'] = $v_shop_process_id;
    $data = json_encode(array(
      "public_key" => PUBLICKEY,
      "operation"  => array(
        "token" => "$v_token",
        "shop_process_id" => "$v_shop_process_id",
        "amount" => "$v_amount",
        "currency" => "$v_currency",
        'additional_data' => "$v_additional_data",
        "description" => "$v_description",
        "return_url" => "$v_return_url",
        "cancel_url" => "$v_cancel_url"
      ),
    ));

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $result = curl_exec($ch);

    $result = json_decode($result, false);
    $result = json_decode(json_encode($result), true);

    if($result['status'] == "success"){
      $urlRetorno = $host . "/payment/single_buy?process_id=" . $result['process_id'];
    }

    curl_close($ch);
    return $urlRetorno;
  }

  /*
  funcion para solicitar el estado de la transaccion realizada por bancard
  */

  public function bancarVposSolicitarProccessGenerado($v_shop_process_id){
    $host = "https://vpos.infonet.com.py";
    $url = $host."/vpos/api/0.3/single_buy/confirmations";

    $v_token = md5( PRIVATEKEY . $v_shop_process_id ."get_confirmation" );
    $data = json_encode(array(
      "public_key" => PUBLICKEY,
      "operation"  => array(
        "token" => "$v_token",
        "shop_process_id" => "$v_shop_process_id"
      ),
    ));

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $result = curl_exec($ch);


    $result = json_decode($result, false);
    $result = json_decode(json_encode($result), true);
    curl_close($ch);
    return $result;
  }

  /*
  funcion que se encarga de revertir algun pago realizado por bancard
  */
  public function bancarRoolBack($v_shop_process_id){
    $host = "https://vpos.infonet.com.py";
    $url = $host."/vpos/api/0.3/single_buy/rollback";

    $v_token = md5( PRIVATEKEY . $v_shop_process_id . "rollback" . "0.00"  );
    $data = json_encode(array(
      "public_key" => PUBLICKEY,
      "operation"  => array(
        "token" => "$v_token",
        "shop_process_id" => "$v_shop_process_id"
      ),
    ));


    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $result = curl_exec($ch);


    $result = json_decode($result, false);
    $result = json_decode(json_encode($result), true);

    curl_close($ch);
    return $result;
  }
}
?>