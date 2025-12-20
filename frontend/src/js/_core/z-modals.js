/*
modals handler
strict mode
handles elements from both original and javasciprt-updated DOM
AT
28.08.25
*/

$(function() {
	window.Modal = new Modal()

	$('body').on( "click", "[data-modal]", function(event){
		event.preventDefault()
		let selector_ = $(this).data('modal')
		//console.log('[data-modal] clicked: ',selector_)
		Modal.show(selector_)
		adaptImages('.modal img[data-src]', 'data-src')
		adaptBacks('.modal [data-back]', 'data-back')
		})

	$('body').on( "click", "[data-modal-close]", function(event){
		let selector_ = '#' + $(this).parents(".modal").eq(0).attr('id')
		Modal.hide(selector_)
		})

	$('body').on( 'keyup', function(e){
		if(e.keyCode == 27){
			Modal.hide( $(".modal") )
			}
		})

})