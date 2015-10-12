<?php
/**********************************************************
* Copyright (C) 2014, Envalo, Inc - All Rights Reserved
* (www.envalo.com/licensing)
*
* @file Pointer.php
* @date 10/12/2015
*/
class Envalo_APath_Pointer
{
    protected $_parent = null;
    protected $_part = null;
    protected $_path = null;
    public $_pointer = null;
    public function __construct(&$pointer, $part, $path, $parent)
    {
        $this->_pointer = &$pointer;
        $this->_part = $part;
        $this->_path = $path;
        $this->_parent = $parent;
    }

    public function hasParent()
    {
        return is_null($this->_parent);
    }

    /**
     * @return Envalo_APath_Pointer|null
     */
    public function getParent()
    {
        return $this->_parent;
    }

    public function getPart()
    {
        return $this->_part;
    }
    public function getPath()
    {
        return $this->_path;
    }

    /**
     * @deprecated Do not use. Just access $_pointer directly.
     * @return null
     */
    public function getPointer()
    {
        $temp =& $this->_pointer;
        return $temp;
    }

    public function hasChild($part)
    {
        $temp =& $this->_pointer;
        return isset($temp[$part]);
    }

    /**
     * @deprecated Using this will likely cause an array copy which we need to avoid.
     * @param $part
     * @return mixed
     */
    public function getChild($part)
    {
        $pointer =& $this->_pointer[$part];
        return $pointer;
    }
}