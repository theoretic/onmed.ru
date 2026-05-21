/*
Form helper
dependences:
	cash or jquery
AT
21.05.26
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

				messageEL
					.removeClass('error warning success')
					.addClass('hidden')

				if( _json.error ) messageEL.addClass('error').html( _json.error )
				if( _json.warning ) messageEL.addClass('warning').html( _json.warning )
				if( _json.success ) messageEL.addClass('success').html( _json.success )
				if( _json.message ) messageEL.html( _json.message )
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

		// Always clear loading state, no matter what fails. Without the
		// .catch() the field stayed in the `loading` CSS state forever
		// (visible as constant blinking) whenever the validator endpoint
		// returned non-JSON (PHP warning prepended), HTTP 5xx, or any
		// network failure — symptom reported on iOS Safari.
		const clearLoading = () => { _field.removeClass('loading') }

		fetch(validatorUrl)
			.then(async function(response) {
				const text = await response.text()
				try { return JSON.parse(text) }
				catch (e) {
					throw new Error('Validator returned non-JSON (HTTP ' + response.status + ')')
				}
			})
			.then(function(json) {
				clearLoading()
				self.setFieldValidity(_field, json)
			})
			.catch(function(err) {
				clearLoading()
				console.error('FormHelper.validateField() failed:', err)
				// Leave field neutral — don't flip to invalid on transient
				// network/server hiccup. Full-form validate() before submit
				// is authoritative.
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
			.catch(function(err) {
				// Validator request failed (network error, non-JSON response, etc.).
				// Without this, the form stays in the "loading" state forever.
				console.error('FormHelper.validate() failed:', err)
				_form.removeClass('loading')
				self.updateMessaging(_form, {
					error: 'Не удалось проверить форму. Проверьте интернет-соединение и попробуйте ещё раз.'
				})
				resolve({ success: false })
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
				params = { method: 'POST', body: data, credentials: 'same-origin' }
			break

			//get
			default:
				//url += '?' + Object.keys(data).map( k => encodeURIComponent(k) + '=' + encodeURIComponent(data[k]) ).join('&')
				data = _form.serialize() //query string
				url += '?' + data
				params = { credentials: 'same-origin' }
			}

//console.log( 'FormHelper.ajaxSubmit(): _form, data:', _form, data )

		_form.addClass('loading')
		btn.attr('disabled','disabled')

		// Always clear loading state and surface a message, no matter what
		// fails (network error, non-JSON response, HTTP 4xx/5xx). Without
		// this, an iOS Safari fetch failure leaves the form stuck in the
		// "loading" animation indefinitely with no feedback to the user.
		const clearLoading = () => {
			btn.removeAttr('disabled')
			_form.removeClass('loading')
		}

		fetch( url, params )
			.then( async response => {
				const text = await response.text()
				let json
				try { json = JSON.parse(text) }
				catch (e) {
					throw new Error('Сервер вернул некорректный ответ (HTTP ' + response.status + ').')
				}
				if( !response.ok && !json.error ) {
					json.error = 'Ошибка отправки (HTTP ' + response.status + '). Попробуйте ещё раз.'
				}
				return json
			})
			.then( json => {
				clearLoading()
				self.updateMessaging(_form,json)
				if( event ) window.dispatchEvent( new CustomEvent(event, {detail:json}) )
				return json
			})
			.catch( err => {
				clearLoading()
				console.error('FormHelper.ajaxSubmit() failed:', err)
				self.updateMessaging(_form, {
					error: err && err.message
						? err.message
						: 'Не удалось отправить форму. Проверьте интернет-соединение и попробуйте ещё раз.'
				})
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