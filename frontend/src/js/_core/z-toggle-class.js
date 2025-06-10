/*
html element class toggle

expected element format:
<element data-toggle-class='{ "#some-selector":"some-class", "#some-selector-2":"some-class-2" }'>

AT
29.11.19
*/

$(function(){
	$("[data-toggle-class]").on("click touch",function(){
		var data = $(this).data('toggle-class')
		for( var el in data ) {
			$(el).toggleClass(data[el])
		}
	})
})