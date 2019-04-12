<?php

require_once('PakkelabelsException.php');
class Pakkelabels {
  const API_ENDPOINT = 'https://app.pakkelabels.dk/api/public/v3';

  const VERSION = '3.0';

  private $_api_user;

  private $_api_key;

  public function __construct($api_user, $api_key, $api_base_path=self::API_ENDPOINT){
    $this->_api_user = $api_user;
    $this->_api_key = $api_key;
    $this->_api_base_path = $api_base_path;
  }

  public function account_balance(){
    $result = $this->_make_api_call('/account/balance');

    return $result;
  }

  public function account_payment_requests($params){
    $result = $this->_make_api_call('/account/payment_requests', 'GET', $params);

    return $result;
  }

  public function products($params){
    $result = $this->_make_api_call('/products', 'GET', $params);

    return $result;
  }

  public function pickup_points($params){
    $result = $this->_make_api_call('/pickup_points', 'GET', $params);

    return $result;
  }

  public function shipment_monitor_statuses($params){
    $result = $this->_make_api_call('/shipment_monitor_statuses', 'GET', $params);

    return $result;
  }  

  public function return_portals($params){
    $result = $this->_make_api_call('/return_portals', 'GET', $params);

    return $result;
  }  
  
  public function return_portal($id){
    $result = $this->_make_api_call('/return_portals/' . $id);

    return $result;
  }

  public function return_portal_shipments($return_portal_id, $params){
    $result = $this->_make_api_call('/return_portals/' . $return_portal_id . '/shipments');

    return $result;
  }

  public function shipments($params){
    $result = $this->_make_api_call('/shipments', 'GET', $params);

    return $result;
  }

  public function shipment($id){
    $result = $this->_make_api_call('/shipments/' . $id);

    return $result;
  }

  public function shipment_labels($id, $params){
    $result = $this->_make_api_call('/shipments/' . $id . '/labels', 'GET', $params);

    return $result;
  }

  public function create_shipment($params){
    $result = $this->_make_api_call('/shipments', 'POST', $params);

    return $result;
  }

  public function print_queue_entries($params){
    $result = $this->_make_api_call('/print_queue_entries', 'GET', $params);

    return $result;
  }  

  public function imported_shipments($params){
    $result = $this->_make_api_call('/imported_shipments', 'GET', $params);

    return $result;
  }

  public function imported_shipment($id){
    $result = $this->_make_api_call('/imported_shipments/' . $id);

    return $result;
  }

  public function create_imported_shipment($params){
    $result = $this->_make_api_call('/imported_shipments', 'POST', $params);

    return $result;
  }

  public function update_imported_shipment($id, $params){
    $result = $this->_make_api_call('/imported_shipments/'. $id, 'PUT', $params);

    return $result;
  }

  public function delete_imported_shipment($id){
    $result = $this->_make_api_call('/imported_shipments/'. $id, 'DELETE');

    return $result;
  }

  public function labels($params){
    $result = $this->_make_api_call('/labels/', 'GET', $params);

    return $result;
  }
  
  private function _make_api_call($path, $method = 'GET',$params = array()){
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_USERPWD, $this->_api_user . ":" . $this->_api_key);
    $params['user_agent'] = 'pdk_php_library v' . self::VERSION;

    switch ($method) {
    case 'GET':
      $query = http_build_query($params);
      curl_setopt($ch, CURLOPT_URL, $this->_api_base_path . '/' . $path . '?' . $query);
      break;
    case 'POST':
      $query = json_encode($params);
      curl_setopt($ch, CURLOPT_URL, $this->_api_base_path . '/' . $path);
      curl_setopt($ch, CURLOPT_POST, 1);
      curl_setopt($ch, CURLOPT_POSTFIELDS, $query);
      curl_setopt($ch, CURLOPT_HTTPHEADER, array(
          'Content-Type: application/json',
          'Content-Length: ' . strlen($query))
      );
      break;
    case 'PUT':
      $query = json_encode($params);
      curl_setopt($ch, CURLOPT_URL, $this->_api_base_path . '/' . $path);
      curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
      curl_setopt($ch, CURLOPT_POSTFIELDS, $query);
      curl_setopt($ch, CURLOPT_HTTPHEADER, array(
          'Content-Type: application/json',
          'Content-Length: ' . strlen($query))
      );
      break;
    case 'DELETE':
      $query = http_build_query($params);
      curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE'); 
      curl_setopt($ch, CURLOPT_URL, $this->_api_base_path . '/' . $path . '?' . $query);
      break;
    }

    $headers = [];
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    // this function is called by curl for each header received
    curl_setopt($ch, CURLOPT_HEADERFUNCTION,
      function($curl, $header) use (&$headers)
      {
        $len = strlen($header);
        $header = explode(':', $header, 2);
        if (count($header) < 2) // ignore invalid headers
          return $len;

        $name = strtolower(trim($header[0]));
        if (!array_key_exists($name, $headers))
          $headers[$name] = [trim($header[1])];
        else
          $headers[$name][] = trim($header[1]);

        return $len;
      }
    );

    $output = curl_exec($ch);
    $http_code = curl_getinfo( $ch, CURLINFO_HTTP_CODE);
    $output = json_decode($output, true);
    
     curl_close($ch);
    
    if ($http_code != 200){
    throw new PakkelabelsException($output['error']);
    }

    $pagination = $this->_extract_pagination($headers);

    $output = array(
      'output' => $output,
      'pagination' => $pagination
    );

    return $output;
  }

  private function _extract_pagination($headers) {
    $arr = array('x-per-page', 'x-current-page', 'x-total-count', 'x-total-pages');
    $pagination = array();
    foreach ($arr as &$key) {
      if (array_key_exists($key, $headers)) {
        $pagination[$key] = $headers[$key][0];
      } else {
        return $pagination;
      }
    }

    return $pagination;
  }
}
?>