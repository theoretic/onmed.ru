/*
adaptive images

dependencies:
	$cash.js
	preloadImage

avif support
data-prefer attr (width or height)
scale for displays with physical resolution > 3000px and/or scale > 1
testWebp()
$(window).scroll() for better calculation of parent area dimensions
checkWebp() call optimization
$(this).parent().width() instead of $(this).width() for defining image size
webp support
svg support
'force-src' attr
aspect format : 2:3

AT
03.02.22
*/

function AdaptiveImage( _datasrc, _width, _height, _aspect, _config ){

//console.log('AdaptiveImage(_width, _height, _aspect): ',_width, _height, _aspect)//
//console.log('window.imageFormat: ',window.imageFormat)//

	if(!window.imageFormat) return

	let
		matchingWidth = null,
		matchingHeight = null,
		//scale = window.devicePixelRatio != 1 && window.screen.width * window.devicePixelRatio > 3000? window.devicePixelRatio : 1
		scale = window.devicePixelRatio
		scale = scale >=3? 1.5 : scale //for SOME mobile devices!

//console.log('window.devicePixelRatio, window.screen.width, scale:', window.devicePixelRatio, window.screen.width, scale)

	if( !_width || _width==null ) _width = ''
	if( !_height || _height==null ) _height = ''

	//this.dataSrc = _datasrc
	//this.width = _width
	//this.height = _height
	this.aspect = _aspect
	//this.config = _config

	if(this.aspect){
		this.aspect = this.aspect.split(':')
		this.aspect = this.aspect[0]/this.aspect[1]
		}

	this.fixedWidths = [200,300,400,640,800,1024,1280,1600,1920]
	this.fixedHeights = [200,300,400,600,800,1000,1600]

	this.matchSize = function(_size,_fixedSizes) {
		if(_size <= _fixedSizes[0]) return _fixedSizes[0]
		if(_size >= _fixedSizes[_fixedSizes.length-1]) return _fixedSizes[_fixedSizes.length-1]

		for( let i = 0; i < _fixedSizes.length-1; i++ ){
			if( _size >= _fixedSizes[i] && _size <= _fixedSizes[i+1] ){
				//return _fixedSizes[i+1] * scale
				return _fixedSizes[i+1]
				}
			}
		}

	this.adaptSrc = function(_datasrc, _width, _height) {

		//console.log('adaptSrc(): _datasrc, _width, _height: ',_datasrc, _width, _height)//_width lost!

		if( !_width && !_height ) return _datasrc

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

	//

	//console.log('!!!aspect, _width, _height: ', this.aspect, _width, _height, this.aspect==undefined)


	//exact fit: no fixed sizes
	if( _config['fit'] == 'exact' ) {
		this.adaptedSrc = this.adaptSrc( _datasrc, Math.round(_width*scale), Math.round(_height*scale) )
		return
	}

	//fixed sizes
	matchingWidth = matchingHeight = null
	if( _width ) {
		//console.log('_width!==: this.aspect, _width, _height: ', this.aspect, _width, _height)
		matchingWidth = this.matchSize(_width,this.fixedWidths)
		if( this.aspect ) matchingHeight = matchingWidth / this.aspect
		}
	else if( _height ) {
		//console.log('_width==: this.aspect, _width, _height: ', this.aspect, _width, _height)
		matchingHeight = this.matchSize(_height,this.fixedHeights)
		if( this.aspect ) matchingWidth = matchingHeight * this.aspect
		}

	matchingWidth = Math.round( matchingWidth * scale )
	matchingHeight = Math.round( matchingHeight * scale )

	this.adaptedSrc = this.adaptSrc( _datasrc, matchingWidth, matchingHeight )

	//console.log('this.aspect, matchingWidth, matchingHeight: ',this.aspect, matchingWidth, matchingHeight)//ok
	}

////

function adaptImages(_selector, _attr) {
	$(_selector).each(function(i,el){
		let
			elOrig = el,
			adaptFlag = true,
			adaptIf

		el = $(el)

		adaptIf = el.data('adapt-if')

		if( !adaptIf && !elOrig.visible(true) ) return

		if( adaptIf ) {
			switch(adaptIf){
				case 'fully-visible':
					if( !elOrig.visible() )
						adaptFlag = false
				break
				default: //e.g. always
					adaptFlag = true
				break
				}
			}

		if(!adaptFlag) return

		let
			prefer = el.data('prefer'),
			widthOrig = el.data('width')? el.data('width') : el.parent().width(),
			heightOrig = el.data('height')? el.data('height') : el.parent().height(),б
			width = widthOrig,
			height = heightOrig,
			config = el.data('config') || {}

		//data-config.mode
		if( config.mode !== 'exact-fit') {
			//data-prefer
			width = prefer=='width' || !prefer? width : false
			height = prefer=='height' || !prefer? height : false
		}

		////
		let
			ai = new AdaptiveImage(
				el.attr(_attr),
				width,
				height,
				el.data('aspect'),
				config
			)

//console.log('adaptImages(): img, width, height: ', el.data('src'), width, height)//ok
//console.log('el: ', el)//
//console.log('ai.adaptedSrc: ', ai.adaptedSrc)//ok

		if( el.attr('src') !== ai.adaptedSrc ) {
//console.log('loading image for: ', el)
			el.addClass('loading')
			preloadImage(ai.adaptedSrc, function(){
//console.log('preload finished: ai.adaptedSrc: ', ai.adaptedSrc)//
				el.removeClass('loading')
				el.attr('src',ai.adaptedSrc)
				})
			}
		})
	}

////

$(function(){
	if(!window.imageFormat){
		testImagesFormats(function(){
			adaptImages('img[data-src]','data-src')
		})
	}
	else adaptImages('img[data-src]','data-src')

	$( window ).on('scroll resize',function() {
		adaptImages('img[data-src]','data-src')
		})

})