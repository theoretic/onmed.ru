/*
Stars input handler
AT
15.11.23
*/

$(function(){
	$('body').on('click', '.starsInput .star', function(e){
console.log('starsInput: e: ', e)
		let targetEL = $(e.target)

		if( targetEL.is('input') ) return

		let
			starEls = targetEL.parent('.starsInput').find('.star'),
			selectedIndex = starEls.index(e.target)

		starEls.removeClass('selected')

		starEls.each( function(i,starEl){
			if( i>selectedIndex ) return
			$(starEl).addClass('selected')
		})

	})
})