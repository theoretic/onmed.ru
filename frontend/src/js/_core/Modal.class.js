/*
Modal
strict mode
AT
23.03.20
*/

function Modal(){

	this.show = function(_selector){
		//console.log('Modal.show(): ',_selector)
		let element = $(_selector)
		element.removeClass('hidden')
		if( element.css('position') == 'absolute' ){
			element.data.top = element.css('top')
			element.css('top',window.pageYOffset)
			}
		}

	this.hide = function(_selector){
		//console.log('Modal.hide(): ',_selector)
		let element = $(_selector)
		element.addClass('hidden')
		if( element.css('position') == 'absolute' ){
			let top = element.data.top? element.data.top : '-100%'
			element.css('top',top)
			}
		}

	}