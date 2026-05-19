/*
Form helper
strict mode
dependences:
	debounce
	cash
AT
06.05.26
*/

$(function(){

	window.formHelper = new FormHelper()

	// Per-field debounce. A single shared `debounce(handler)` (the previous
	// implementation) drops validation calls for any field touched within
	// the debounce window of another field, leaving the earlier field in
	// a stale `loading` state. Keep one timer per field name so concurrent
	// edits to different fields each get their own validation cycle.
	const fieldDebounceTimers = {}

	//field validity
	$(document).on( 'input', function(event){

		let
			field = $(event.target),
			form = field.closest('form')

		if( !form.data('validator')) return

		form.data('validity',false)

		const name = field.attr('name') || '__anon__'
		clearTimeout( fieldDebounceTimers[name] )
		fieldDebounceTimers[name] = setTimeout( function(){
			window.formHelper.validateField(field)
		}, 250 )
	})

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