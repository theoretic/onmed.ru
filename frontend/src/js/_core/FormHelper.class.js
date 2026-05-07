/*
Form helper
dependences:
	cash or jquery
AT
06.05.26
*/

class FormHelper{

	//messaging
	updateMessaging(_form,_json){

//console.log('FormHelper.updateMessaging(): _form, _json: ', _form, _json)

		let
			messaging = _form.data('messaging')

		switch( messaging ){
			case 'html':
				let
					messageEL = _form.find(".message")

//console.log('FormHelper.updateMessaging(): messageEL: ', messageEL)

				messageEL.addClass('hidden')

				if( _json.error ) messageEL.removeClass('hidden warning success').addClass('error').html( _json.error )
				if( _json.success ) messageEL.removeClass('hidden error warning').addClass('success').html( _json.success )
				if( _json.message ) messageEL.removeClass('hidden error warning success').html( _json.message )
			break

			case 'alert':
				if( _json.error ) alert( _json.error )
				if( _json.success ) alert( _json.success )
				if( _json.message ) alert( _json.message )
		}
	}

	//field validity
	setFieldValidity(_field,_result){
		let
			label = _field.parents('label'),
			spanError = label.find('span.error')

		//if (_field.attr('type')=='radio') label = _field.parent('div').parent('div').first().find('label')

		_field.removeClass('valid invalid')
		spanError.html('')

		if(_result.error){
//console.log('_result.error:',_result.error)
			spanError.html( ' - ' + _result.error)
			_field.addClass('invalid')
		}
		else if(_result.success){
			_field.addClass('valid')
		}
	}

	// field validation
	validateField(_field){
		let
			self = this,
			form = _field.parents('form'),
			validatorUrl = form.data('validator'),
			value = encodeURIComponent( _field.val() ).substring(0,199)

		//truncate is needed to keep the resulting URL not too long
		validatorUrl += '?mode=field&name='+_field.attr('name')+'&value=' + value

		_field.addClass('loading')

		fetch(validatorUrl)
		.then(function(data) { return data.json() })
		.then(function(json) {
			_field.removeClass('loading')
			self.setFieldValidity(_field,json)
		})

	}

	// form validation
	validate(_form){
		let
			self = this,
			data = _form.serialize(),
			messaging = _form.data('messaging'),
			//validatorUrl = _form.data('validator')+'?'+data,
			validatorUrl,
			validity

		//truncating values to get a shorter URL
		let data_ = data.split('&')
		data = ''
		for( let pair of data_ ){
//console.log('pair: ', pair)
			let name_value = pair.split('=')
			name_value[1] = name_value[1].substring(0,50)
//console.log('name_value: ', name_value)
			data += '&' + name_value[0] + '=' + name_value[1]
		}

		validatorUrl = _form.data('validator') + '?' + data

		_form.addClass('loading')

		return new Promise(function(resolve, reject) {
			fetch(validatorUrl)
			.then(function(response) { return response.json() })
			.then(function(json) {
			if( json.errors ){
				for(let fieldname in json.errors){
					let
						field = $('[name='+fieldname+']',_form),
						error = json.errors[fieldname]
					self.setFieldValidity(field,{error:error})
				}
			}
			// skip messaging if form has data-action — final response from ajaxSubmit will handle it
			if( !_form.data('action') ) self.updateMessaging(_form,json)
			_form.removeClass('loading')
				resolve(json)
			})
		})
	}

	// form submission
	//it's supposed that the form should have at least data-action attribute
	//windows custom event will be triggered after submission

	ajaxSubmit(_form){

		let
			self = this,
			data,
			btn = _form.find('button').last(),
			url = _form.data('action'),
			method = _form.data('method') || 'get',
			event = _form.data('event'),
			messaging = _form.data('messaging'),
			params = {}

//console.log('method: ',method)
		switch(method) {
			case 'post':
				data = new FormData( _form[0] )
//console.log('data: ',data)
				//params = { method: 'POST', body: data, headers: {'Content-Type': 'application/x-www-form-urlencoded'} }
				params = { method: 'POST', body: data }
			break

			//get
			default:
				//url += '?' + Object.keys(data).map( k => encodeURIComponent(k) + '=' + encodeURIComponent(data[k]) ).join('&')
				data = _form.serialize() //query string
				url += '?' + data
			}

//console.log( 'FormHelper.ajaxSubmit(): _form, data:', _form, data )

		_form.addClass('loading')
		btn.attr('disabled','disabled')
		fetch( url, params )
			.then( response => response.json() )
			.then( json => {
				btn.removeAttr('disabled')
				_form.removeClass('loading')
				self.updateMessaging(_form,json)
				if( event ) window.dispatchEvent( new CustomEvent(event, {detail:json}) )
				return json
			})
	}

		handleSubmitEvent( _event ){
			let
				self = this,
				form = $(_event.target),
				dataAction = form.data('action')

			if(dataAction) self.ajaxSubmit(form)
			else _event.target.submit()
		}

}