/*
AT
07.12.21
*/

$( window ).on('csrfChanged',function(event) {

	$("[rel=csrf]")
		.attr('name',event.detail.name)
		.attr('value',event.detail.value)

})