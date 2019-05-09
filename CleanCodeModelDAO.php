<?php
require_once 'CleanCodeModel.php';

class CleanCodeModelDAO extends CleanCodeModel
{
    protected static $table = '(mytable)';
    
    protected $data = array();
    
    public static function getTable($alias = '')
    {
        return $alias? static::$table . ' ' . $alias : static::$table;
    }
    
    protected function get_column($name, $default = '')
    {
        return self::searchPos($this->data, $name, $default);
    }
    
    protected function set_column($name, $value, $regex, $min = 1, $max = '')
    {
        if($this->validateData($value, $regex, $min, $max))
        {
            $this->data[$name] = $this->formatValue($value, $regex);
        }
        else if($this->checkErrors())
        {
            $this->setErrorByField($name);
        }
    }
    
    protected function loadData($bdData)
    {
        $this->data = is_array($bdData)? $bdData : array();
    }
    
    public function isLoaded()
    {
        return $this->data && count($this->data);
    }
    
    public function toArray()
    {
        return $this->data;
    }
    
    protected static function sql($alias = '')
    {
        return new CleanCodeSQL(self::getTable($alias));
    }
}
?>