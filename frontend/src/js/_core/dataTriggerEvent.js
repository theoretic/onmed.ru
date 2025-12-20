/*
custom event triggering with a[data-trigger-event]
AT
11.01.23
*/

function triggerEvent(e){
//console.log('data-trigger-event clicked: ',e)

	let
		el = $(e.target)

	window.dispatchEvent( new CustomEvent(el.data('trigger-event'), {detail:el.data('params')}) )
}

$(function(){
	$("body").on( 'click touch', "[data-trigger-event]", function(e){ triggerEvent(e) } )
})
