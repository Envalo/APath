<?php
/**********************************************************
* Copyright (C) 2014, Envalo, Inc - All Rights Reserved
* (www.envalo.com/licensing)
*
* @file Condition.php
* @date 10/12/2015
*/
class Envalo_APath_Condition
{
    protected $_operator = null;
    protected $_argument = null;
    public function __construct($operator, $argument)
    {
        $this->_operator = $operator;
        $this->_argument = $argument;
    }

    public function process(&$value)
    {
        $arg = $this->_getCleanedArgument();
        switch($this->_operator)
        {
            case '==':
                return $value == $arg;
                break;
            case '!=':
                return $value != $arg;
                break;
            case '<=':
                return $value <= $arg;
                break;
            case '>=':
                return $value >= $arg;
                break;
            case '<':
                return $value < $arg;
                break;
            case '>':
                return $value > $arg;
                break;
            default: return false;
        }
    }

    protected function _getCleanedArgument()
    {
        //TODO: More cleanup of things passed in. My check for quoted strings is quite 'dumb' right now.
        if($this->_argument[0] == "'" || $this->_argument[0] == '"')
        {
            return substr($this->_argument, 1, strlen($this->_argument)-2);
        }
        return $this->_argument;
    }
}