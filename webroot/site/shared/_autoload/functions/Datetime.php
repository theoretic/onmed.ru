<?
/*
AT
25.03.19
*/

namespace Datetime;

function dateRu($time){
	$monthsRu = [
		false,
		'января',
		'февраля',
		'марта',
		'апреля',
		'мая',
		'июня',
		'июля',
		'августа',
		'сентября',
		'октября',
		'ноября',
		'декабря',
		];

	$date = date( 'j.n.Y', $time );
	list( $day,$month,$year ) = explode( '.' , $date );
	return "$day $monthsRu[$month]";
	}