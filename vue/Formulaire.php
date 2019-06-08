<?php
/*
ALTER TABLE usager ADD UNIQUE UNQ_Usager (nom,prenom,adresse)
ALTER TABLE medecin ADD UNIQUE UNQ_Medecin(nom,prenom)
ALTER TABLE consultation ADD UNIQUE UNQ_Consultation(id_medecin,dateConsultation,heureConsultation)
*/
class Formulaire{
  protected $_form;

  public function __construct($action,$method) {
    $this->_form .= '<form action="'.$action.'" method="'.$method.'"><fieldset>';
  }

  public function setLegend($titre) {
    $this->_form.='<legend> '.$titre. '</legend>';
  }

  public function setLabel($for,$label){
    $this->_form.='<label for="'.$for.'">'.$label.'</label>';
  }

  public function setText($name,$id){
    $this->_form.='<input type="text" name="'.$name.'" id="'.$id.'"</input> </br>';
  }

  public function setTextValue($name,$id,$value){
     $this->_form.='<input type="text" name="'.$name.'" id="'.$id.'" value="'.$value.'" </input> </br>';
  }

  public function setTextPh($name,$id,$placeholder) {
      $this->_form.='<input placeholder="'.$placeholder.'" type="text" name="'.$name.'"</input> </br>';
  }

  public function setDate($name,$id)
  {
    $this->_form.='<input type="date" name="'.$name.'" id="'.$id.'"</input> </br>';
  }

  public function setDateValue($name,$id,$value)
  {
    $this->_form.='<input type="date" name="'.$name.'" id="'.$id.'" value="'.$value.'"</input> </br>';
  }
  public function setTime($name,$id,$value){
    $this->_form.='<input type="time" name="'.$name.'" id="'.$id.'" value="'.$value.'"</input> </br>';
  }
  public function setNumber($name,$id,$min,$max,$step,$value){
        $this->_form.='<input type="number" name="'.$name.'" id="'.$id.'" min="'.$min.'" max="'.$max.'" step="'.$step.'" value="'.$value.'""</input> </br>';
  }

  public function setSubmit($value){
    $this->_form .='<input type="submit"  value="'.$value.'"></form>';
  }
    public function setReset($value){
    $this->_form .='<input type="reset" value="'.$value.'"></form>';
  }

  public function setHidden($name,$value)
  {
    $this->_form.='<input type="hidden" name="'.$name.'" value="'.$value.'"</input> </br>';
  }


  public function setSelect($name, $id){
    $this->_form .= '<select name="'.$name.'" id="'.$id.'">';
  }
  public function setOption ($value,$contenu){
     $this->_form .= '<option value="'.$value.'">'.$contenu.'</option>';
  }
  public function setOptionSelected ($value,$contenu){
    $this->_form .= '<option selected="selected" value="'.$value.'">'.$contenu.'</option>';
  }
  public function setEndSelect(){
    $this->_form .= '</select> </br>';
  }
  public function getForm() {
    $this->_form.='</fieldset>
                </form>';
    return $this->_form;
  }
}
?>
