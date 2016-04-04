<?php
require_once('DataSources.php');
class Envalo_APath_ConditionTest extends PHPUnit_Framework_TestCase
{
    public function testEquals()
    {

    }
    public function testSubstringMatch()
    {
        $engine = DataSources::getEngine();
        $data = DataSources::getOrderData();
        $results = $engine->extract($data, '/order/items/#/.[sku=~KU1]');
        $this->_testItemResults($results, 1);
        $results = $engine->extract($data, '/order/items/#/.[sku=@"SKUYYY,SKU1,SKU2,SKUXXX"]');
        $this->_testItemResults($results, 2);
        $results = $engine->extract($data, '/order/items/#/.[sku!@"SKUYYY,SKU1,SKU2,SKU3"]');
        $this->_testItemResults($results, 1);
        $results = $engine->extract($data, '/order/items/#/.[sku=~SKU]');
        $this->_testItemResults($results, 4);
        $results = $engine->extract($data, '/order/items/#/.[sku!~KU1]');
        $this->_testItemResults($results, 3);
        $results = $engine->extract($data, '/order/items/#/.[sku!~SKU]');
        $this->_testItemResults($results, 0);
        $results = $engine->extract($data, '/order/items/#/.[sku!~KU5]');
        $this->_testItemResults($results, 4);


    }
    protected function _testItemResults($results, $expected_qty)
    {
        $this->assertEquals($expected_qty, count($results));
        $keys_to_check = array('sku', 'warehouse_id', 'price', 'qty');
        foreach ($results as $result)
        {
            foreach ($keys_to_check as $key)
            {
                $this->assertArrayHasKey($key, $result, 'Relevant Result: ' . print_r($result, true));
            }
        }
    }
}