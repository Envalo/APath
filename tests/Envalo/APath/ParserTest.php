<?php
require_once('DataSources.php');
class Envalo_APath_ParserTest extends PHPUnit_Framework_TestCase
{
    public function testAlreadyParsed()
    {
        $parser = DataSources::getParser();
        $a_path = '/some/path/with[filter!=conditions]/#/./iterators/too';
        $parsed = $parser->parse($a_path);
        $parsed_again = $parser->parse($parsed);
        $this->assertEquals($parsed, $parsed_again);
    }
}