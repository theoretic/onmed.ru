/*
Slider
dependencies: $cash
AT
27.11.19
*/

function Slider( _element, _config ){

	var self = this

	self.element = $(_element)
	self.config = _config || {}
	self.slides = self.element.find('.slide')
	var currentSlide = self.element.find('.current.slide')
	self.index = self.slides.index(currentSlide)

	//navigation
	self.next = self.element.find('.next')
	self.prev = self.element.find('.prev')
	self.dots = self.element.find('.dot')

	//carousel
	if( self.config.carousel ){
		self.scrollable = self.element.find('.scrollable')
		self.scrollable = self.scrollable[Object.keys(self.scrollable)[0]]
		}

//html manipulations to reveal the current slide

	self.slide = function(index){
		var currentSlide = self.slides.eq(index)

		self.slides.removeClass('current')
		currentSlide.addClass('current')

		if( self.config.carousel ){
			var scrollTo = currentSlide.outerWidth() * index
			scrollTo = scrollTo>0? scrollTo : 0
			self.scrollable.scrollLeft = scrollTo
			}

		if( self.dots.length ){
			self.dots.removeClass('current')
			self.dots.eq(index).addClass('current')
			}
		}

//html manipulations to prepare the carousel

	self.carousel = function(){

		var overflow = self.scrollable.scrollWidth - self.scrollable.offsetWidth
		if( overflow>0 ) self.element.addClass('overflown')
		else self.element.removeClass('overflown')

		}

//slide changes

	self.step = function(direction){
		if( !direction ) self.index = ( ++self.index < self.slides.length )? self.index : 0
		else self.index = ( --self.index >= 0 )? self.index : self.slides.length-1
		self.slide(self.index)
		}

	self.autoplay = function(autoplay)
		{
		if( !autoplay ) self.timer = setInterval(self.step,5000)
		else clearInterval(self.timer)
		}

//interactions

	self.element.on('mouseover',function(){
		if( self.config.autoplay !== false ) self.autoplay('stop')
		})

	self.element.on('mouseout',function(){
		if( self.config.autoplay !== false ) self.autoplay()
		})

	self.dots.on('click',function(element){
		var index = self.dots.index(element.target)
		if( self.config.autoplay !== false ) self.autoplay('stop')
		self.slide(index)
		})

	self.next.on('click', function(){
			self.autoplay('stop')
			self.step()
			}
		)

	self.prev.on('click', function(){
			if( self.config.autoplay !== false ) self.autoplay('stop')
			self.step('back')
			}
		)

//initializing

	if( self.config.autoplay !== false ) self.autoplay()
	if( self.config.carousel ) self.carousel()

//handling resizes
	$(window).on('resize',function(){
		if( self.config.carousel ) self.carousel()
		})

	}