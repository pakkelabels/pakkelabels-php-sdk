# Pakkelabels.dk's official PHP library

This SDK supports Pakkelabels.dk API v3.

Specification: https://app.pakkelabels.dk/api/public/v3/specification

The previous SDK supporting Pakkelabels.dk API v2 can be found under the branch 'for_api_v2'

## Getting started

Below is a simple PHP script which illustrate the minimum amount of code needed to getting started.

```php5
<?php
  try {
	  $client = new Pakkelabels('api_user', 'api_key');
  } catch (PakkelabelsException $e) {
    echo $e->getMessage();
  }
?>
```

Once the $client object is created, you can begin to use the API.
Following are some examples for how to utilize the SDK.

### Get current balance:

```php5
<?php
  echo $client->account_balance();
?>
```

Get outstanding payment requests:

```php5
<?php
  $params = array(
    'created_at_min' => '2017-06-19',
    'page' => 1
  );
  echo $client->account_payment_requests($params);
?>
```

Get available products (pagination is supported):

```php5
<?php
  $params = array(
    'country_code' => 'DK',
    'carrier_code' => 'gls',
    'page' => 1
  );
  echo $client->products($params);
?>
```

Get available / nearest pickup points:

```php5
<?php
  $params = array(
    'country_code' => 'DK',
    'carrier_code' => 'gls',
    'zipcode' => '5000'
  );
  echo $client->pickup_points($params);
?>
```


Get shipments (pagination is supported):

```php5
<?php
  $params = array(
    'page' => 1,
    'carrier_code' => 'dao'
  );
  echo $client->shipments($params);
?>
```

Get shipment by id:

```php5
<?php
  $id = 5545625;
  echo $client->shipment($id);  
?>
```

Get label(s) for shipment:

```php5
<?php
  $shipment_id = 5545625;
  $params = array(
    'label_format' => '10x19_pdf'
  );
  echo $client->shipment_labels($shipment_id, $params);  
?>
```

Create shipment: 

```php5
<?php
  $params = array(
    "test_mode" => true,
    "own_agreement"=> true,
    "label_format"=> "a4_pdf",
    "product_code"=> "GLSDK_HD",
    "service_codes"=> "EMAIL_NT,SMS_NT",
    "sender" => array(
      "name"=> "Pakkelabels.dk ApS",
      "address1"=> "Strandvejen 6",
      "address2"=> null,
      "country_code"=> "DK",
      "zipcode"=> "5240",
      "city"=> "Odense NØ",
      "attention" => null,
      "email"=> "firma@email.dk",
      "telephone"=> "70400407",
      "mobile"=> "70400407"       
    ),
    "receiver" => array(
      "name"=> "Lene Jensen",
      "address1"=> "Vindegade 112",
      "address2"=> null,
      "country_code"=> "DK",
      "zipcode"=> "5000",
      "city"=> "Odense C",
      "attention"=> null,
      "email"=> "lene@email.dk",
      "telephone"=> "50607080",
      "mobile"=> "50607080",
      "instruction"=> null
    ),
    "parcels" => array(
      array(
      "weight" => 1000
      )
    ),
  );
  echo $client->create_shipment($params);
?>
```

Get shipment monitor statuses:

```php5
<?php
  $params = array(
    'ids' => '5546689,5546696',
    'page' => 1
  );
  echo $client->shipment_monitor_statuses($params);  
?>
```

Get return portals:

```php5
<?php
  $params = array(
    'page' => 1
  );
  echo $client->return_portals($params);  
?>
```

Get return portal by id:

```php5
<?php
  $id = 4766;
  echo $client->return_portal($id);  
?>
```

Get return shipments for return portal (pagination is supported):

```php5
<?php
  $return_portal_id = 4766;
  $params = array(
    'page' => 1
  );
  echo $client->return_portal_shipments($return_portal_id, $params);  
?>
```

