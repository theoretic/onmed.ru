/*
Imask handler
AT
15.11.23
*/

$(function(){
	let imasks = new Array()
	$("input[data-mask]").each(function( i ) {
		let
			el = $(this),
			options = { mask:el.data('mask') }

//console.log('el[0]: ', el[0])
//console.log('options: ', options)
		imasks[i] = IMask(el[0], options)
	})
})