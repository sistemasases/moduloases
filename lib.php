<?php

//es necesario para el 

class managerNavigationBlock {
    static $blocknode;
    
    
    public function __construct(){
        
    }
    
    public function setNodeBlock($node){
        $this->blocknode = $node;
    }
    
    public function getNodeBLock(){
        return $this->blocknode;
    }
}
