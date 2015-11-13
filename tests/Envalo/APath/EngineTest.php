<?php
require_once('DataSources.php');
class Envalo_APath_EngineTest extends PHPUnit_Framework_TestCase
{

    public function testSingleFilteredIterators()
    {
        $engine = DataSources::getEngine();
        $data = DataSources::getOrderData();
        $results = $engine->extract($data, '/order/items/#/.[warehouse_id==3]');
        $this->_testItemResults($results, 2);

    }
    public function testMultiFilteredIterator()
    {
        $engine = DataSources::getEngine();
        $data = DataSources::getOrderData();
        $results = $engine->extract($data, '/order/items/#/.[warehouse_id==3][sku=="SKU1"][price>=10]');
        $this->_testItemResults($results, 1);
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