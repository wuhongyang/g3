<?php

class GoodsFactory {
    
    private static $types = array(
        -1 => 'package',
        1 => 'car',
        //2 => 'vip',
        3 => 'function_card',
        4 => 'liang',
        5 => 'daemon',
        6 => 'aristocracy'
    );

    public static function getInstance($category){
        if(!in_array($category,array_keys(self::$types))){
            require_once 'goods.class.php';
            return new Goods();
        }
        require_once self::$types[$category].'.class.php';
        $class = implode('', array_map('ucfirst', explode('_', self::$types[$category])));
        //$class = ucfirst(self::$types[$type]);
        return new $class();
    }
}
