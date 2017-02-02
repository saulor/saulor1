<?php

class View2 {
    
    private $_modulo;
    private $_layout;
    private $_view;
    private $_menu;
    private $_params;
      
    public function __construct($_modulo = null, $_layout = null, $_view = null, $_menu = false) {
        $this->_modulo = $_modulo;
        $this->_layout = $_layout;
        $this->_view = $_view;
        $this->_menu = $_menu;
    }  
            
    public function setParams($_params) {
        $this->_params = $_params;
    }
      
    public function getParams() {
        return $this->_params;
    }
      
    public function getContents() {
        ob_start();
        if(isset($this->_view)) {
            $viewPath = 'views' . DS . $this->_modulo . DS . $this->_view;
            require_once $viewPath;
        }
        $contents = ob_get_contents();
        ob_end_clean();
        return $contents;  
    }
    
    public function getMenu() {
        ob_start();
        //$menuPath = 'views' . DS . "includes" . DS . "menus" . DS . "menu-default.phtml";
        //if($this->_menu) {
        //  $menuPath = 'views' . DS . "includes" . DS . "menus" . DS . $this->_menu;
        //}
        //require_once $menuPath;
        $contents = ob_get_contents();
        ob_end_clean();
        return $contents; 
    }
      
    public function showContents() {
        $contents = $this->getContents();
        $menu = $this->getMenu();
        $layoutPath = 'views' . DS . "layouts" . DS . $this->_layout . '.phtml';
        require_once($layoutPath);
    }
}

?>