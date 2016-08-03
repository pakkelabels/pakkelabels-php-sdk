# Pakkelabels.dk's official PHP library

## Getting started

Below is a simple PHP script which illustrate the minimum amount of code needed to getting started.

```php5
<?php
    try {
		$label = new Pakkelabels('api_user', 'api_key');
    } catch (PakkelabelsException $e) {
      echo $e->getMessage();
    }
?>
```

Once the $label object is created, you can begin to use the API.

To see the current balance:

```php5
<?php
    echo $label->balance();
?>
```

To list all Post Danmark shipments sent to to Denmark:

```php5
<?php
    $labels = $label->shipments(array('shipping_agent' => 'pdk', 'receiver_country' => 'DK'));
    print_r($labels);
?>
```

To display the PDF for the shipment ID with 42 inline in the browser:

```php5
<?php
    $base64 = $label->pdf(42);
    $pdf = base64_decode($base64);
    header('Content-type: application/pdf');
    header('Content-Disposition: inline; filename="label.pdf"');
    echo $pdf;
?>
```

To create a test shipment with Post Danmark, and then output the Track&Trace number of the newly created shipment:

```php5
<?php
    $data = array(
      'shipping_agent' => 'pdk',
      'weight' => '1000',
      'receiver_name' => 'John Doe',
      'receiver_address1' => 'Some Street 42',
      'receiver_zipcode' => '5230',
      'receiver_city' => 'Odense M',
      'receiver_country' => 'DK',
      'sender_name' => 'John Wayne',
      'sender_address1' => 'The Batcave 1',
      'sender_zipcode' => '5000',
      'sender_city' => 'Odense C',
      'sender_country' => 'DK',
      'shipping_product_id' => '51',
      'services' => '11,12',
      'receiver_mobile' => '004560708090',
      'receiver_email' => 'john@doe.com',
      'test' => 'true' // Change to false when going live
    );

    $shipment = $label->create_shipment($data);
    echo 'Track&Trace: ' . $shipment['pkg_no'];
?>
```

You’ll notice that we supply a shipping_product_id and a list of services. In this case, 51 is the ID of “Privatpakke u. omdeling” (without delivery)
The service with ID 11 is email notification, and 12 is sms notification.

To find the IDs of the products and services:

```php5
<?php
    print_r($label->freight_rates());
?>
```
