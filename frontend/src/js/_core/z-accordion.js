/*
Simple accordion
AT
27.11.23
*/

$(function(){

	$('body').on('click touch', "[data-accordion-toggle]", function(event){
//console.log('event:', event)
		let
			toggle = $(event.target),
			item = toggle.parents("[data-accordion-item]"),
			content = item.find("[data-accordion-content]"),
			icon = item.find("[data-accordion-toggle-icon]"),
			isHidden = content.hasClass('hidden'),
			accordion = toggle.parents("[data-accordion]")
			//config = accordion.data('accordion') || {}

		switch( isHidden ){
			case true:
				icon.html('-')
				content.removeClass('hidden')
			break

			default:
				icon.html('+')
				content.addClass('hidden')
		}

	})

})