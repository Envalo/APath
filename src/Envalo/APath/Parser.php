<?php
/**********************************************************
* Copyright (C) 2014, Envalo, Inc - All Rights Reserved
* (www.envalo.com/licensing)
*
* @file Parser.php
* @date 10/12/2015
*/
require_once "Node.php";
require_once "Condition.php";
/**
 * Class Envalo_APath_Parser
 * Parses APath expressions into a structured format that can be applied with Envalo_APath_Engine
 *
 * This parser is quite dumb right now. Needs to be improved. Doesn't really validate the path is correct.
 *
 */
class Envalo_APath_Parser
{
    protected static $_parse_cache = array();
    protected $_a_path = null;
    protected $_filters = array();
    /* @var $_current_filter null|Envalo_APath_Node */
    protected $_current_node = null;
    public function parse($a_path)
    {
        if(isset(self::$_parse_cache[$a_path]))
        {
            return self::$_parse_cache[$a_path];
        }
        //Reset protected variables.
        $this->_filters = array();
        $this->_current_node = null;
        $result = array();
        $extract_filter_regex = '/\[([^\]]+)\]/';
        $process_filter_regex = '/\$\$\$(\d+)\%\%\%/';
        $process_condition_regex = '/^(.+)(==|!=|<=|>=|<(?!=)|>(?!=))(.+)$/';
        $a_path_cleaned = preg_replace_callback($extract_filter_regex, array($this, '_extractFilters'), $a_path);
        $parts = explode('/', $a_path_cleaned);

        foreach($parts as $part)
        {
            $node = new Envalo_APath_Node();
            $this->_current_node = $node;
            $cleaned_part = preg_replace_callback($process_filter_regex, array($this, '_processFilters'), $part);
            $final_part = preg_replace_callback($process_condition_regex, array($this, '_processConditions'), $cleaned_part);
            $this->_current_node->setPart($final_part);

            $result[] = $this->_current_node;
        }
        self::$_parse_cache[$a_path] = $result;
        return $result;
    }
    protected function _processConditions($matches)
    {
        $path_part = $matches[1];
        $operator = $matches[2];
        $argument = $matches[3];
        $this->_current_node->setCondition(new Envalo_APath_Condition($operator, $argument));
        return $path_part;
    }
    /**
     * This is used as a callback for preg_replace_callback
     * The regex that triggers it looks for $$$(some number)%%% in the string.
     * We return an empty string because we want to remove that, but we also instantiate
     * a new parser to parse what was originally in that location.
     * @param $matches array
     * @return string
     */
    protected function _processFilters($matches)
    {
        $filter_index = (int)$matches[1];
        $filter = $this->_filters[$filter_index];
        $parser = new Envalo_APath_Parser();
        $this->_current_node->addFilter($parser->parse($filter));
        return '';
    }

    /**
     * This is used as a callback for preg_replace_callback
     * This replaces sub-paths with a token so we can later
     * link that sub-path up to the parsed path part it is part of
     * Example:
     * /foo/bar[baz/pill]/guff
     * Will call this passing $matches = array("[baz/pill]", "baz/pill");
     * And return "$$$0%%%"
     * @param $matches array
     * @return string
     */
    protected function _extractFilters($matches)
    {
        $this->_filters[] = $matches[1];
        return '$$$' . (count($this->_filters) - 1) . '%%%';
    }
}