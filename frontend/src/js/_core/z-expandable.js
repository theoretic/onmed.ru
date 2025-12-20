/*
AT
01.09.25
*/

$(function(){
	$(".expander").on("click",function(e){
		//console.log(e.target);
		let
			expander = $(e.target),
			li = expander.parent("li"),
			expanderText

		li.toggleClass("expanded")
		expanderText = li.hasClass("expanded")? "-" : "+"
		expander.text(expanderText)
	})
})