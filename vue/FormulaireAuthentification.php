<?php
require_once 'Formulaire.php';

class FormulaireAuthentification extends Formulaire{
    public function __construct($action, $method){
        parent::__construct($action, $method);
    }
    public function setText($name, $placeholder)
    {
        $this->_form.='<input placeholder="'.$placeholder.'" type="text" name="'.$name.'"</input>';
    }

    public function setPassword($name,$placeholder){
      $this->_form.='<input placeholder="'.$placeholder.'" type="password" name="'.$name.'"</input>';
  }

}