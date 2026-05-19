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
		switch( $condition['condition'] ?? null ) {
			default: //if
				foreach( ($condition['fields'] ?? []) as $fieldname_ => $value_ ) {
					if ( ($this->input->$fieldname_ ?? null) == $value_ ) continue;
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
		// Use !=="" / !==null instead of truthy check so the
		// fallback to $this->input doesn't trigger on legitimate empty
		// strings. And guard against $this->input being null (single-field
		// mode in /api/validator.php never sets it) — PHP 8 raises a
		// warning on null property access, which corrupts the JSON
		// response and traps the client field in `loading` state.
		if( $value === null || $value === '' )
			$value = is_object($this->input) ? ($this->input->$fieldname ?? null) : null;
		$field = $this->model[$fieldname] ?? null;

		if(!$field) return ['result'=>'success'];
		// All model-array accesses use `??` to avoid PHP 8.1+ "Undefined
		// array key" warnings, which would otherwise be echoed before the
		// JSON body and break JSON.parse on the client.
		$validateIf = $field['validate-if'] ?? null;
		$validateAs = $field['validate-as'] ?? null;

		if( $validateIf === 'nonempty' && !$value ) return ['success'=>1];
		if( !$validateAs ) return ['success'=>1];

		//conditional validation
		if( is_array($validateIf) ) {
			if ( !$this->checkConditions($validateIf) )
				return ['success'=>1];
		}

		$validatorCandidate = "{$_SERVER['DOCUMENT_ROOT']}/site/shared/validators/{$validateAs}.php";

		// Guard the include — previously `include` ran unconditionally even
		// when the file was missing, emitting a PHP warning that
		// corrupted the JSON response.
		if( !is_file($validatorCandidate) )
			return ['error' => defined('I18N_VALIDATOR') ? __('no validation rule',I18N_VALIDATOR) : 'не определён валидатор'];

		$validation = include $validatorCandidate;

		//empty output from validator php file means success
		if( $validation === 1 || $validation === true ) $validation = ['success'=>1];

		if( !is_array($validation) )
			$validation = ['error' => defined('I18N_VALIDATOR') ? __('validation error',I18N_VALIDATOR) : 'ошибка валидации'];

		return $validation;
	}

	public function validate($input=false,$model=false) {
		$input = $input? : $this->input;
		$model = $model? : $this->model;

		$validation = ['errors' => []];

		foreach($this->model as $fieldname=>$field) {
			$fieldValue = is_object($this->input) ? ($this->input->$fieldname ?? null) : null;
			$validation_ = $this->validateField($fieldname, $fieldValue);
			if( !empty($validation_['error']) )
				$validation['errors'][$fieldname] = $validation_['error'];
		}

		///field group validations
		//model fields may contain group attribute meaning that at least one field belonging to a group should be nonempty

		//getting groups
		$groups = [];
		foreach($this->model as $fieldname=>$field) {
			if( empty($field['group']) ) continue;
			$groups[] = $field['group'];
		}
		$groups = array_unique($groups);

		foreach( $groups as $group ) {
			$groupIsValid = false;
			foreach($this->model as $fieldname=>$field) {
				if( ($field['group'] ?? null) === $group && is_object($this->input) && !empty($this->input->$fieldname) ){
					$groupIsValid = true;
					break;
				}
			}
			if( !$groupIsValid ) $validation['errors'][] = "Хотя бы одно поле в группе '{$group}' должно быть заполнено.";
		}

		if( !empty($validation['errors']) )
			$validation['error'] = defined('I18N_VALIDATOR')? __('Some fields are filled incorrectly',I18N_VALIDATOR) : 'Некоторые поля заполнены неверно';
		else
			$validation = ['success'=>1];
		return $validation;
	}

}