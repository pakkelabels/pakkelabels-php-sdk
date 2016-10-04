<?php
require_once('PakkelabelsException.php');
class Pakkelabels {
    const API_ENDPOINT = 'https://app.pakkelabels.dk/api/public/v2';
    const VERSION = '1.2';

    private $_api_user;
    private $_api_key;
    private $_token;

    public function __construct($api_user, $api_key){
        $this->_api_user = $api_user;
        $this->_api_key = $api_key;
        $this->login();
    }

    private function login(){
        $result = $this->_make_api_call('users/login', true, array('api_user' => $this->_api_user, 'api_key' => $this->_api_key));
        $this->_token = $result['token'];
    }

    public function balance(){
        $result = $this->_make_api_call('users/balance');
        return $result['balance'];
    }

    public function pdf($id){
        $result = $this->_make_api_call('shipments/pdf', false, array('id' => $id));
        return $result['base64'];
    }

    public function zpl($id){
        $result = $this->_make_api_call('shipments/zpl', false, array('id' => $id));
        return $result['base64'];
    }
    
    public function shipments($params = array()){
        $result = $this->_make_api_call('shipments/shipments', false, $params);
        return $result;
    }
    
    public function imported_shipments($params = array()){
        $result = $this->_make_api_call('shipments/imported_shipments', false, $params);
        return $result;
    }

    public function create_imported_shipment($params){
        $result = $this->_make_api_call('shipments/imported_shipment', true, $params);
        return $result;
    }
    
    public function create_shipment($params){
        $result = $this->_make_api_call('shipments/shipment', true, $params);
        return $result;
    }

    public function create_shipment_own_customer_number($params){
        $result = $this->_make_api_call('shipments/shipment_own_customer_number', true, $params);
        return $result;
    }

    public function freight_rates(){
        $result = $this->_make_api_call('shipments/freight_rates');
        return $result;
    }

    public function payment_requests(){
        $result = $this->_make_api_call('users/payment_requests');
        return $result;
    }

    public function gls_droppoints($params){
        $result = $this->_make_api_call('shipments/gls_droppoints', false, $params);
        return $result;
    }

    public function pdk_droppoints($params){
        $result = $this->_make_api_call('shipments/pdk_droppoints', false, $params);
        return $result;
    }

    public function dao_droppoints($params){
        $result = $this->_make_api_call('shipments/dao_droppoints', false, $params);
        return $result;
    }

    public function getToken(){
        return $this->_token;
    }

    public function add_to_print_queue($shipments){
        $result = $this->_make_api_call('shipments/add_to_print_queue', true, array('ids' => implode(',', $shipments)));
        return $result;    
    }

    public function pdf_multiple($shipments){
        $result = $this->_make_api_call('shipments/pdf_multiple', false, array('ids' => implode(',', $shipments)));
        return $result;
    }
    
    private function _make_api_call($method, $doPost = false,$params = array()){
        $ch = curl_init();
        $params['token'] = $this->_token;
        $params['user_agent'] = 'pdk_php_library v' . self::VERSION;

        $query = http_build_query($params);    
        if ($doPost){
            curl_setopt($ch, CURLOPT_URL, self::API_ENDPOINT . '/' . $method);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $query);
        } else {
            curl_setopt($ch, CURLOPT_URL, self::API_ENDPOINT . '/' . $method . '?' . $query);
        }
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $output = curl_exec($ch);
        $http_code = curl_getinfo( $ch, CURLINFO_HTTP_CODE);
        curl_close ($ch);
        $output = json_decode($output, true);

        if ($http_code != 200){
                        if(is_array($output['message'])){
				throw new PakkelabelsException(print_r($output['message'], true));
                        }else{
				throw new PakkelabelsException($output['message']);
                        }
                }
        return $output;
    }
}
?>
