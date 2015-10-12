<?php
/**********************************************************
* Copyright (C) 2014, Envalo, Inc - All Rights Reserved
* (www.envalo.com/licensing)
*
* @file Node.php
* @date 10/12/2015
*/

/**
 * Class Envalo_APath_Node
 * This represents one parsed 'step' in a path.
 * Produced by Envalo_APath_Parser
 * Consumed by Envalo_APath_Engine
 */
class Envalo_APath_Node {
    protected $_part = null;
    protected $_filters = array();
    protected $_condition = null;
    public function setPart($part)
    {
        $this->_part = $part;
        return $this;
    }
    public function setCondition($condition)
    {
        $this->_condition = $condition;
        return $this;
    }
    public function addFilter($filter)
    {
        $this->_filters[] = $filter;
        return $this;
    }

    public function getPart()
    {
        return $this->_part;
    }

    /**
     * @return Envalo_APath_Condition|null
     */
    public function getCondition()
    {
        return $this->_condition;
    }

    /**
     * @return array
     */
    public function getFilters()
    {
        return $this->_filters;
    }

}