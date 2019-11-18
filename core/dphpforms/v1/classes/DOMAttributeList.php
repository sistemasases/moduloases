<?php

abstract class AbstractDOMAttributeList {

    abstract public function addBoolAttribute(string $attr, bool $val): void;

    abstract public function addIntAttribute(string $attr, int $val): void;

    abstract public function addFloatAttribute(string $attr, float $val): void;

    abstract public function addStrAttribute(string $attr, string $val): void;

    abstract public function getAttributes(): array;
}

class DOMAttributeList extends AbstractDOMAttributeList {

    private $attributes = array();

    public function __construct(array $attr_list = []) {
        
        foreach ($attr_list as $attr => $val) {
            $val_type = gettype($val);
            $str_attr = (string) $attr;
            if ($val_type == gettype(false)) {
                $this->addBoolAttribute($str_attr, $val);
            } elseif ($val_type == gettype(0)) {
                $this->addIntAttribute($str_attr, $val);
            } elseif ($val_type == gettype(0.0)) {
                $this->addFloatAttribute($str_attr, $val);
            } elseif ($val_type == gettype("0")) {
                $this->addStrAttribute($str_attr, $val);
            } elseif ($val_type == gettype(array())) {
                $this->addArrAttribute($str_attr, $val);
            } elseif ($val_type == gettype(NULL)) {
                continue;
            }else {
                $this->throw_unsupported_exeption($val);
            }
        }
        
    }

    private function addAtttribute(string $attr, $val) {
        $this->attributes = array_merge(
            $this->attributes,
            array($attr => $val)
        );
    }

    public function addBoolAttribute(string $attr, bool $val): void {
        $this->addAtttribute($attr, (string) $val);
    }

    public function addIntAttribute(string $attr, int $val): void {
        $this->addAtttribute($attr, (string) $val);
    }

    public function addFloatAttribute(string $attr, float $val): void {
        $this->addAtttribute($attr, (string) $val);
    }

    public function addStrAttribute(string $attr, string $val): void {
        $this->addAtttribute($attr, $val);
    }

    public function addArrAttribute(string $attr, array $list): void {

        $vals = array();

        foreach ($list as &$val) {

            if ( $this->basic_value_is_supported($val) ) {
                if( !is_null( $val ) && ($val !== "") ){
                    array_push($vals, (string) $val);
                }else{
                    continue;
                }
               
            } else {
                $this->throw_basic_unsupported_exeption($val);
            }
        }

        $this->addAtttribute($attr, $vals);
    }

    public function getAttributes(): array {
        return $this->attributes;
    }

    private function basic_value_is_supported($val): bool {
        switch (gettype($val)) {
            case gettype(false):
                return true;
            case gettype(0):
                return true;
            case gettype(0.0):
                return true;
            case gettype("0"):
                return true;
            case gettype(NULL):
                return true;
            default:
                return false;
        }
    }

    private function throw_unsupported_exeption($val) {
        throw new Exception("'" . gettype($val) . "' is not supported.", -1);
    }
    
    private function throw_basic_unsupported_exeption($val) {
        throw new Exception("'" . gettype($val) . "' is not supported as basic value.", -2);
    }

}
