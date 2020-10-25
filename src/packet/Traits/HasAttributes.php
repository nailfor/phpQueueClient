<?php

namespace nailfor\queue\packet\Traits;

trait HasAttributes 
{
    protected $fillable = [];
    protected $attributes = [];

    /**
     * fill attributes
     * @param array $data
     */
    protected function fill($data)
    {
        if (!is_array($data)) {
            $data = $data->toArray();
        }
        
        $fillable = $this->fillable ?? [];
        foreach ($fillable as $key) {
            $val = $data[$key] ?? null;
            if ($val !== null) {
                $this->attributes[$key] = $val;
            }
        }
    }
    
    /**
     * getters
     * @param string $name
     * @return type value
     */
    public function __get(string $name) 
    {
        if (property_exists($this, $name)) {
            return $this->{$name};
        }
        
        $getter = "get{$name}Attribute";
        if (method_exists($this, $getter)) {
            return $this->$getter();
        }
        
        return $this->attributes[$name] ?? null;
    }
    
    /**
     * setters
     * @param string $name
     * @param type $val
     */
    public function __set(string $name, $val) 
    {
        if (property_exists($this, $name)) {
            $this->{$name} = $val;
            return;
        }
        
        $this->attributes[$name] = $val;
    }
}