/*
adaptive backs

avif support
scaling for displays with physical resolution > 3000px and DPR > 1
width and height can be taken from parent
strict mode
uses cash js
adapt-if
testWebp()
checkWebp() call optimization
webp support
aspect format : 2:3

data-min-width
data-min-height

data-min-display-width
data-min-display-height

fitting by height disabled

rounding

AT
24.10.24
*/

function AdaptiveBack( _datasrc, _width, _height, _aspect ){

	if(!window.imageFormat) return

//console.log('AdaptiveBack( _datasrc, _width, _height, _aspect ): ', _datasrc, _width, _height, _aspect)

	let
		matchingWidth = null,
		matchingHeight = null,
		scaling = window.devicePixelRatio != 1 && window.screen.width * window.devicePixelRatio > 3000? window.devicePixelRatio : 1

//console.log('window.devicePixelRatio, window.screen.width, scaling:', window.devicePixelRatio, window.screen.width, scaling)

	if( !_width || _width==null ) _width = ''
	if( !_height || _height==null ) _height = ''

	this.dataSrc = _datasrc
	this.width = _width
	this.height = _height
	this.aspect = _aspect

	if( typeof this.aspect !== 'undefined' ){
		this.aspect = this.aspect.split(':')
		this.aspect = this.aspect[0]/this.aspect[1]
	}

	this.fixedWidths = [400,640,800,1024,1280,1600,1920,2400,3000,4000]
	this.fixedHeights = [200,400,600,800,1000,1600,2000]

	this.matchSize = function(_size,_fixedSizes) {
		if(_size <= _fixedSizes[0]) return _fixedSizes[0]
		if(_size >= _fixedSizes[_fixedSizes.length-1]) return _fixedSizes[_fixedSizes.length-1]

		for(let i = 0; i < _fixedSizes.length-1; i++){
			if( _size >= _fixedSizes[i] && _size <= _fixedSizes[i+1] ){
				return _fixedSizes[i+1] * scaling
				}
			}
		}

	this.adaptSrc = function(_datasrc, _width, _height) {

		if( !_width || _width==null ) _width = ''
		if( !_height || _height==null ) _height = ''

		let
			beforeFile = _datasrc.substr(0,_datasrc.lastIndexOf('/')),
			file = _datasrc.substr(_datasrc.lastIndexOf('/') + 1),
			ext = file.substr(file.lastIndexOf('.') + 1)

		switch(ext)
			{
			case 'svg':
				return _datasrc
			break

			default:
				let newExt = window.imageFormat != '_fallback'? window.imageFormat : ext
				file += '.' + newExt
			}

		return beforeFile + '/' + _width + 'x' + _height + '/' + file
		}

	////

	if( typeof _aspect == 'undefined' || !_aspect ){

		if( _width!=='' ){
			matchingWidth = this.matchSize(_width,this.fixedWidths)
			if( _height!=='' ){
				matchingHeight = this.matchSize(_height,this.fixedHeights)
				if(_height > _width)
					matchingWidth = null
				else
					matchingWidth = _width
			}
		}
		else{ //no width
			if( _height!==''){
				matchingHeight = this.matchSize(_height,this.fixedHeights)
				matchingWidth = null
			}
		}
	}

	else { //aspect defined
		if(_width!==''){
			matchingWidth = this.matchSize(_width,this.fixedWidths)
			matchingHeight = Math.round(matchingWidth / this.aspect)
//console.log('width: this.aspect, _width, matchingWidth, matchingHeight: ',this.aspect, _width, matchingWidth, matchingHeight)
		}
		if(_height!==''){
			matchingHeight = this.matchSize(_height,this.fixedHeights)
			matchingWidth = Math.round(matchingHeight * this.aspect)
//console.log('height: this.aspect, _height, matchingHeight, matchingWidth: ',this.aspect, _height, matchingHeight, matchingWidth)
		}
	}

	//rounding
	matchingWidth = 100 * Math.round(matchingWidth/100)
	matchingHeight = 100 * Math.round(matchingHeight/100)

	this.adaptedSrc = this.adaptSrc(this.dataSrc,matchingWidth,matchingHeight)

//console.log('_datasrc, this.aspect, matchingWidth, matchingHeight: ', _datasrc, this.aspect, matchingWidth, matchingHeight)//
//console.log('this.adaptedSrc: ', this.adaptedSrc)//

	if(!this.adaptedSrc) {
		this.backUrl = false
		return
	}

	this.backUrl = 'url("' + this.adaptedSrc + '")'
	this.backUrl = this.backUrl.replace('""','"')
	}


function adaptBacks(_selector, _attr) {
	$(_selector).each(function(i,el){

		let
			elOrig = el,
			parent,
			adaptFlag = true,
			adaptIf

		el = $(el)
		parent = $(el.parent().get(0))

		//no display if width or height below data-min-
		let
			width = el.data('width') || el.width(),
			height = el.data('height') || el.height(),
			noDisplay = false

		if( width=='parent' ) width = parent.width()
		if( height=='parent' ) height = parent.height()

		if( el.data('min-height') ){
			height = el.height() >= el.data('min-height')? el.height() : el.data('min-height');
		}

//console.log('el, width, height, elOrig.visible(1):', el, width, height, elOrig.visible(1))

		if( (width && el.data('min-display-width')) || (height && el.data('min-display-height')) ) {
			noDisplay =
				( el.data('min-display-width') && width <= el.data('min-display-width') ) ||
				( el.data('min-display-height') && height <= el.data('min-display-height') )
		}

		if( noDisplay ){
			el.css('background-image','')
			return
			}

		adaptIf = el.data('adapt-if')
		if( !adaptIf && !elOrig.visible(true) ) return

		if( adaptIf ) {
			switch(adaptIf){
				case 'fully-visible':
					adaptFlag = elOrig.visible()
				break
				default: //e.g. always
					adaptFlag = true
				break
				}
			}

		if(!adaptFlag) return

		//rounding
//		width = 100 * Math.round(width/100)
//		height = 100 * Math.round(height/100)

//console.log('width, height: ', width, height)

		let ab = new AdaptiveBack( el.attr(_attr), width, height, el.data('aspect') )

//console.log('ab.backUrl: ',ab.backUrl)

		if( ab.backUrl && el.css('background-image') !== ab.backUrl )
			el.css('background-image',ab.backUrl)
		})
	}

////

$(function(){

	if(!window.imageFormat){
		testImagesFormats(function(){
			adaptBacks('[data-back]','data-back')
		})
	}
	else adaptBacks('[data-back]','data-back')

	$( window ).on('scroll resize',function() {
		adaptBacks('[data-back]','data-back')
	})

})