<?
/*
class Validator
external validator files
php8
added ['validate-if']=='nonempty' 
AT
14.11.23
*/

class Validator{

	public
		$input,
		$model,
		$fields
		;

	private function checkCondition($condition) {
		$mismatch = false;
		switch( $condition['condition'] ) {
			default: //if
				foreach( $condition['fields'] as $fieldname_ => $value_ ) {
					if ( $this->input->$fieldname_ == $value_ ) continue;
					$mismatch = true;
					break;
				}

			}
		return !$mismatch;
	}

	private function checkConditions($conditions) {
		$mismatch = false;
		foreach( $conditions as $condition ) {
			if( $this->checkCondition($condition) ) continue;
			$mismatch = true;
			break;
		}
		return !$mismatch;
	}

	public function validateField($fieldname, $value) {
		$value = $value? : $this->input->$fieldname;
		$field = $this->model[$fieldname];

		if(!$field) return ['result'=>'success'];
		if( $field['validate-if']=='nonempty' && !$value ) return ['success'=>1];
		if( !$field['validate-as'] || $field['validate-as']=='' ) return ['success'=>1];

		//conditional validation
		if( is_array($field['validate-if']) ) {
			//var_dump($field['validate-if']);
			if ( !$this->checkConditions($field['validate-if']) )
				return ['success'=>1];
		}

		//echo "-$fieldname- -$value- -".$field['validate-if']."-<br>";//

		$validatorCandidate = "{$_SERVER['DOCUMENT_ROOT']}/site/shared/validators/{$field['validate-as']}.php";

//echo "\$validatorCandidate for $fieldname: ", var_dump($validatorCandidate);

		if( !is_file($validatorCandidate) )
			$validation['error'] = defined('I18N_VALIDATOR')? __('no validation rule',I18N_VALIDATOR) : 'не определён валидатор';

		$validation = include $validatorCandidate;

		if( !$validation || $validation == NULL)
			$validation['error'] = defined('I18N_VALIDATOR')? __('validation error',I18N_VALIDATOR) : 'ошибка вадидации';

//echo '$validation: ', var_dump($validation);

		//empty output from validator php file means success
		if( $validation == 1 ) $validation = ['success'=>1];

		return $validation;
	}

	public function validate($input=false,$model=false) {
		$input = $input? : $this->input;
		$model = $model? : $this->model;

		foreach($this->model as $fieldname=>$field) {
			$validation_ = $this->validateField($fieldname,$this->input->$fieldname);
			if($validation_['error'])
				$validation['errors'][$fieldname] = $validation_['error'];
		}
/*
		//special cases like conditions, one-or-another etc.
		foreach($this->model as $fieldname=>$field) {
			if($field['one-or-another']) {
				$fieldnameAnother = $field['one-or-another'];
				if( $validation['errors'][$fieldname] && $validation['errors'][$fieldnameAnother])
					unset($validation['errors'][$fieldnameAnother]);
			}
		}
*/
		///field group validations
		//model ]fields may contain group attribute meaning that at least one field belonging to a group should be nonempty

		//getting groups
		$groups = [];
		foreach($this->model as $fieldname=>$field) {
			if( !$field['group'] ) continue;
			$groups[] = $field['group'];
		}
		$groups = array_unique($groups);

//echo '$groups: ', var_dump($groups);

		foreach( $groups as $group ) {
			$groupIsValid = false;
			foreach($this->model as $fieldname=>$field) {
				if( $field['group'] == $group && $this->input->$fieldname ){
					$groupIsValid = true;
					break;
				}
			}
			if( !$groupIsValid ) $validation['errors'][] = "Хотя бы одно поле в группе '{$group}' должно быть заполнено.";
		}

//echo '$input: ', var_dump($input);
//echo '$validation: ', var_dump($validation);
		if( $validation['errors'] )
			$validation['error'] = defined('I18N_VALIDATOR')? __('Some fields are filled incorrectly',I18N_VALIDATOR) : 'Некоторые поля заполнены неверно';
		else
			$validation = ['success'=>1];
		return $validation;
	}

}