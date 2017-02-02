<?php

class Banner extends Model {
    
    /**
    * @column
    * @readwrite
    * @primary
    * @type autonumber
    */
    protected $_id;
    
    /**
    * @column
    * @readwrite
    * @type text
    * @length 255
    */
    protected $_descricao;
    
    /**
    * @column
    * @readwrite
    * @type text
    * @length 255
    */
    protected $_link;
    
    /**
    * @column
    * @readwrite
    * @type text
    * @length 255
    */
    protected $_banner;
    
    /**
    * @column
    * @readwrite
    * @type integer
    */
    protected $_status;
    
    /**
    * @column
    * @readwrite
    * @type datetime
    */
    protected $_data;
    
    /**
    * @column
    * @readwrite
    * @type integer
    */
    protected $_timestamp;
    
    protected $_table = 'banners';
    
}

?>