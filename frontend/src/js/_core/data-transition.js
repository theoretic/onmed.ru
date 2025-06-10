/*
AT
04.12.19
*/

$('[data-transition]').on('click touch',function(e){
	//e.preventDefault();
	window.data.transition = $(this).data('transition')
})