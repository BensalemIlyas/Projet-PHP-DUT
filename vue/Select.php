
<?php
class Select {
private $_select;
public function __construct($name){
		$this->_select .= '<select name="'.$name.'">';
	}
public function setOption($value,$contenu){
	$this->_select .= '<option value="'.$value.'">'.$contenu.'</option>';
}
public function getSelect(){
	$this->_select .= '</select>';
	return $this->_select;
}

}
?>