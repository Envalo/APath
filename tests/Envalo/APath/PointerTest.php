<?php
require_once('DataSources.php');
class Envalo_APath_PointerTest extends PHPUnit_Framework_TestCase
{
    public function testPointerChangesOriginal()
    {
        $data = DataSources::getOrderData();
        $engine = DataSources::getEngine();
        $results = $engine->extract($data, '/order/items/1');
        $this->assertEquals(1, count($results));
        $result =& $results[0];
        $pointer = new Envalo_APath_Pointer($result, 'some_part', '/orders/items/1', null);
        $pointer->_pointer['new_key'] = 'new_value';
        $this->assertArrayHasKey('new_key', $result, 'Pointer was not able to modify original result...');
        $this->assertArrayHasKey('new_key', $data['order']['items'][1], 'Original Data was not altered...');
    }

    public function testHasChild()
    {
        $data = DataSources::getOrderData();
        $engine = DataSources::getEngine();
        $results = $engine->extract($data, '/order/items/1');
        $this->assertEquals(1, count($results));
        $result =& $results[0];
        $pointer = new Envalo_APath_Pointer($result, 'some_part', '/orders/items/1', null);
        $this->assertTrue($pointer->hasChild('sku'));
    }
}