/*
Modal
strict mode
AT
23.03.20

2-way hash binding (AT, 12.05.26):
  /#my-modal  → opens #my-modal on page load and popstate
  show()      → sets window.location.hash to selector id
  hide()      → clears window.location.hash if it matches the closed modal
*/

function Modal(){

	this.show = function(_selector){
		let element = $(_selector)
		element.removeClass('hidden')
		if( element.css('position') == 'absolute' ){
			element.data.top = element.css('top')
			element.css('top',window.pageYOffset)
			}
		// sync hash: strip leading # from selector and set as hash
		let id = _selector.replace(/^#/, '')
		if( window.location.hash !== '#' + id ){
			history.pushState(null, '', '#' + id)
			}
		}

	this.hide = function(_selector){
		let element = $(_selector)
		element.addClass('hidden')
		if( element.css('position') == 'absolute' ){
			let top = element.data.top? element.data.top : '-100%'
			element.css('top',top)
			}
		// clear hash if it matches this modal
		let id = _selector.replace(/^#/, '')
		if( window.location.hash === '#' + id ){
			history.pushState(null, '', window.location.pathname + window.location.search)
			}
		}

	// open modal matching current hash, if any
	let _self = this
	function _handleHash(){
		let hash = window.location.hash
		if( hash && hash.length > 1 ){
			let el = $(hash)
			if( el.length ){
				_self.show(hash)
				}
			}
		}

	// run on load
	_handleHash()

	// run on back/forward navigation
	window.addEventListener('popstate', _handleHash)

	}
