<?
/*
Default title content
AT
06.12.23
*/

$return = "{$page->title} {$page->firstname} {$page->patronymic}";

switch(true){
	case $page->parent->template->name == 'news':
		$return .= '- Новости клиники ОНМЕД';
	break;
}

return $return;