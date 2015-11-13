<?php
require_once('DataSources.php');
class Envalo_APath_NodeTest extends PHPUnit_Framework_TestCase
{
    public function testMethodChaining()
    {
        $node = DataSources::getNode();
        $node->setPart('some_part')
            ->addFilter('some_filter')
            ->setCondition('some_condition')
            ->addFilter('some_other_filter')
            ->setPart('some_other_part');
        $this->assertEquals('some_other_part', $node->getPart());
        $this->assertEquals('some_condition', $node->getCondition());
        $this->assertEquals(array('some_filter', 'some_other_filter'), $node->getFilters());

    }


}