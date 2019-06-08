<?php
class Table{
	private $_table;
	public function __construct($border,$cellpadding,$cellspacing,$width){
		$this->_table .='<table border="'.$cellpadding.'" cellpadding= "'.$cellpadding.'"  cellspacing="'.$cellspacing.'" width="'.$width.'"> ';
	}


	public function trStart(){
		$this->_table .="<tr>";
	}
	public function trEnd(){
		$this->_table .="</tr>";
	}
	public function celluleTitre($contenu){
		$this->_table .= '<th>'.$contenu.'</th>';
	}
	public function celluleContenu($contenu){
		$this->_table .= '<td>'.$contenu.'</td>';
	}
	public  function getTableau(){
		$this->_table .=" </table>";
		return $this->_table;
	}
}

?>