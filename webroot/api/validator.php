<?
/*
Form validator API

expected URL:
/api/validator/entity/discount

AT
31.07.23
*/

//echo __FILE__;//

$skipCSRF = 1;

//model
$model = $_REQUEST['request'];
$model = str_replace( 'validator/', '', $model );
$model = DOCUMENT_ROOT . '/site/shared/models/' . $model . '.php';

//echo '$model:', var_dump($model);

if( !is_file($model) ) {
	header("HTTP/1.0 404 Not found");
	die();
}

$model = include $model;

if(!$model){
	header("HTTP/1.0 500 Internal Server Error");
	die();
}

$validator = new Validator();
$validator->model = $model;

//echo '$input->get->mode: ', var_dump($input->get->mode);//

switch( $input->get->mode ) {
	case 'field': //single field, get only
		$fieldname = $input->get->name;
		if( strstr($fieldname,'[') ) $fieldname = substr( $fieldname, 0, strpos($fieldname,'[')); //for multiple fields
		return $validator->validateField( $fieldname, $input->get->value );
	//break;

	default: //
		$validator->input = count($input->post)? $input->post : $input->get;
		return $validator->validate();
}