/*
Form helper
strict mode
dependences:
	debounce
	cash
AT
08.07.24
*/

$(function(){

	window.formHelper = new FormHelper()

	//field validity
	$(document).on( 'input', debounce( function(event){

		let
			field = $(event.target),
			form = field.closest('form')

		if( !form.data('validator')) return

		form.data('validity',false)
		window.formHelper.validateField(field)
	})
	)

	//form submit handling
	$(document).on( 'submit', 'form', function(event){

		let
			form = $(event.target),
			dataAction = form.data('action'),
			validator = form.data('validator') || false,
			confirmText = form.data('confirm-text'),
			validity = form.data('validity')

		if( confirmText) {
			if ( !confirm(confirmText) ) {
				event.preventDefault()
				return
			}
		}

		event.preventDefault()

		//submitting form without validation
		if( !validator ){
			window.formHelper.handleSubmitEvent(event)
			return
		}

		//submitting form with validation
		if( validator ){
			formHelper.validate(form).then(function(response){
				if( response.success ) window.formHelper.handleSubmitEvent(event)
			})
		return
		}

	})

})