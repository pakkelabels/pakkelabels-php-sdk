<?php
require_once dirname(__FILE__) . '/../src/Pakkelabels.php';

class PakkelabelsTest extends \PHPUnit_Framework_TestCase
{
    protected function getClient($user, $api_key)
    {
        return new Pakkelabels($user, $api_key);
    }

    public function testConstructor()
    {
        $invalid_api_key = 'invalid';
        $invalid_user = 'invalid';
        try {
            $client = $this->getClient($invalid_user, $invalid_api_key);
        } catch (PakkelabelsException $expected) {
            $this->assertEquals($expected->getMessage(), 'Incorrect API Key or API User');
            return;
        }

        $this->fail('An expected exception has not been raised.');
    }
}
