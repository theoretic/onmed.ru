/*
AT
27.11.19
*/

$(function(){
	$("#offers-menu .expander").on("click",function(e){
		//console.log(e.target);
		var
			expander = $(e.target),
			li = expander.parent("li"),
			expanderText

		li.toggleClass("expanded")
		expanderText = li.hasClass("expanded")? "-" : "+"
		expander.text(expanderText)
	})
})