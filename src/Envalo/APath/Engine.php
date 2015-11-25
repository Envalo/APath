<?php
/**********************************************************
* Copyright (C) 2014, Envalo, Inc - All Rights Reserved
* (www.envalo.com/licensing)
*
* @file Engine.php
* @date 10/12/2015
*/
require_once 'Pointer.php';
require_once 'Parser.php';
class Envalo_APath_Engine {

    /**
     * This will extract values found with the parsed path in $nodes
     * @param $data array
     * @param $path string
     * @return array
     */
    public function extract(&$data, $path)
    {
        $nodes = $this->_parse($path);
        $extracted = $this->_walk($data, $nodes);
        $results = array();
        if(is_array($extracted))
        {
            /* @var $pointer Envalo_APath_Pointer */
            foreach($extracted as $pointer)
            {
                $results[] =& $pointer->_pointer;
            }
        }
        return $results;
    }

    /**
     * @param $data array
     * @param $path string
     * @param $values
     * @param $append boolean An append action will add a new child instead of overwriting existing values
     * @param $create_path boolean Should we create new elements in the array if they don't exist
     * @return bool
     */
    public function insert(&$data, $path, $values, $append = false, $create_path = true)
    {
        $nodes = $this->_parse($path);
        $extracted = $this->_walk($data, $nodes, $create_path);
        if(is_array($extracted) && count($extracted))
        {
            /* @var $pointer Envalo_APath_Pointer */
            foreach($extracted as $pointer)
            {
                if($append)
                {
                    $pointer->_pointer[] = $values;
                }
                else
                {
                    $pointer->_pointer = $values;
                }
            }
            return true;
        }
        return false;
    }
    /**
     * Removes elements from the passed in array $data that match the path
     * defined by $nodes. Returns true if matches were found and removed.
     * False otherwise.
     * @param $data array
     * @param $path string
     * @return bool
     */
    public function remove(&$data, $path)
    {
        $nodes = $this->_parse($path);
        $extracted = $this->_walk($data, $nodes);
        if(is_array($extracted) && count($extracted))
        {
            /* @var $pointer Envalo_APath_Pointer */
            foreach($extracted as $pointer)
            {
                $part = $pointer->getPart();
                unset($pointer->getParent()->_pointer[$part]);
            }
            return true;
        }
        return false;
    }

    /**
     * @param $data array
     * @param $nodes Envalo_APath_Node[]
     * @param $pointer Envalo_APath_Pointer|null
     * @param $create_path boolean
     * @return Envalo_APath_Pointer[]|null
     */
    protected function _walk(&$data, &$nodes, $create_path = false, &$pointer = null)
    {
        $result = array();
        $current_pointer = null;
        $current_path = '';
        $value = null;
        //ROOT POINTER
        if($pointer == null)
        {
            $ptr = &$data;
            $current_pointer = new Envalo_APath_Pointer($ptr, null, $current_path, null);
        }
        else if($pointer instanceof Envalo_APath_Pointer)
        {
            $current_pointer =& $pointer;
        }
        $use_current = null;
        /* $nodes is an array of Envalo_APath_Node objects. Each node object
         * represents one step in the path we are traversing.
         * A node can contain zero or more filters, which are sub-paths off of
         * the current node used to validate or invalidate the node. A node may also
         * have zero or one condition checks. These compare the value of the node to
         * some static field.
        */
        /* @var $node Envalo_APath_Node */
        foreach($nodes as $index => $node)
        {
            $part = $node->getPart();
            $use_current = false;
            if($part == '..')
            {
                $new_nodes = array_slice($nodes, $index + 1);
                $parent_pointer = $current_pointer->getParent();
                if(!$parent_pointer)
                {
                    //Either we are at the root node or something has gone wrong.
                    return null;
                }
                $parent_ptr =& $parent_pointer->_pointer;
                return $this->_walk($parent_ptr, $new_nodes, $create_path, $parent_pointer);
            }
            else if($part == '.') /* This indicates we are working on the current node */
            {
                $use_current = true;
            }
            else if($part == '#') /* This indicates we are iterating over an array of arrays */
            {
                $sub_nodes = array_slice($nodes, $index + 1);
                $sub_ptr =& $current_pointer->_pointer;
                $sub_path = $current_pointer->getPath() . '/' . $part;
                $sub_results = array();
                foreach($sub_ptr as $sub_index => &$sub_array)
                {
                    if(is_int($sub_index))
                    {
                        $sub_pointer = new Envalo_APath_Pointer($sub_array, $sub_index, $sub_path, $current_pointer);
                        $sub_result = $this->_walk($sub_array, $sub_nodes, $create_path, $sub_pointer);
                        if($sub_result)
                        {
                            $sub_results = array_merge($sub_results, $sub_result);
                        }
                    }

                }
                return $sub_results;
            }
            else if($part == '*') /* This indicates we are iterating over an array of arrays, and including text-based keys */
            {
                $sub_nodes = array_slice($nodes, $index + 1);
                $sub_ptr =& $current_pointer->_pointer;
                $sub_path = $current_pointer->getPath() . '/' . $part;
                $sub_results = array();
                foreach($sub_ptr as $sub_index => &$sub_array)
                {

                    $sub_pointer = new Envalo_APath_Pointer($sub_array, $sub_index, $sub_path, $current_pointer);
                    $sub_result = $this->_walk($sub_array, $sub_nodes, $create_path, $sub_pointer);
                    if($sub_result)
                    {
                        $sub_results = array_merge($sub_results, $sub_result);
                    }


                }
                return $sub_results;
            }
            else if(!$current_pointer->hasChild($part) && !$create_path)
            {
                //Return...
                return null;
            }
            if(!$use_current)
            {
                $ptr =& $current_pointer->_pointer;
                if(!is_array($ptr))
                {
                    // IF we are trying to walk into an array but we aren't using an array, bad things could happen.
                    return null;
                }
                if(!isset($ptr[$part]) && $create_path)
                {
                    $ptr[$part] = array();

                }
                $ptr =& $ptr[$part];
                $new_path = $current_pointer->getPath() . '/' . $part;
                $current_pointer = new Envalo_APath_Pointer($ptr, $part, $new_path, $current_pointer);
            }
            if($node->getCondition())
            {
                $condition = $node->getCondition();
                if(!$condition->process($current_pointer->_pointer))
                {
                    return null;
                }
            }
            if(is_array($node->getFilters()) && count($node->getFilters()))
            {
                foreach($node->getFilters() as $filter)
                {
                    $filter_ptr =& $current_pointer->_pointer;
                    $filter_pointer = new Envalo_APath_Pointer($filter_ptr, $current_pointer->getPart(), $current_pointer->getPath(), $current_pointer->getParent());
                    if(is_null($this->_walk($filter_pointer->_pointer, $filter, $create_path, $filter_pointer)))
                    {
                        return null;
                    }
                }
            }
        }
        $result[] = $current_pointer;
        return $result;
    }

    /**
     * Parse an APath type path into a set of Envalo_APath_Node objects
     * @param $path
     * @return array
     */
    protected function _parse($path)
    {
        $parser = new Envalo_APath_Parser();
        return $parser->parse($path);
    }

}