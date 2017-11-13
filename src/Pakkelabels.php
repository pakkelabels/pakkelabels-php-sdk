<?php
require_once('PakkelabelsException.php');
class Pakkelabels {
    const API_ENDPOINT = 'https://app.pakkelabels.dk/api/public/v3';
    const VERSION = '3.0';

    private $_api_user;
    private $_api_key;

    public function __construct($api_user, $api_key){
        $this->_api_user = $api_user;
        $this->_api_key = $api_key;
    }

    public function balance(){
        $result = $this->_make_api_call('/account/balance');
        return $result;
    }
    
    private function _make_api_call($method, $doPost = false,$params = array()){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_USERPWD, $this->_api_user . ":" . $this->_api_key);
        $params['user_agent'] = 'pdk_php_library v' . self::VERSION;
        if ($doPost){
            $query = json_encode($params);
            curl_setopt($ch, CURLOPT_URL, self::API_ENDPOINT . '/' . $method);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $query);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'Content-Length: ' . strlen($query))
            );
        } else {
            $query = http_build_query($params);
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
