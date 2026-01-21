/*
https://muffinman.io/blog/image-comparison-slider/
AT
22.01.26
*/

$(function(){

	function makeClipPathOblique(value){
		let
			shift=10, //%
			clipPoints = [
				[ 0, 0 ],
				//[ parseFloat(value + shift).toFixed(2), 0 ],
				[ parseInt(value) + shift, 0 ],
				//[ parseFloat(value - shift).toFixed(2), 100 ],
				[ parseInt(value) - shift, 100 ],
				[ 0, 100 ]
			],
			clipPairs = new Array(),
			clipPath = 'polygon'
		
		//calculating clipPairs
		for( let clipPoint of clipPoints )
			clipPairs.push( `${clipPoint[0]}% ${clipPoint[1]}%` )

		//calculating clipPath
		clipPath += '('
		clipPath += clipPairs.join(', ')
		clipPath += ')'
//console.log('clipPath: ', clipPath)
		return clipPath
	}

	//initialization
	$('.comparatorLeft').css( 'clip-path', makeClipPathOblique(50) )

	//interactivity
	$(".comparator input[type=range]").on('input', function(e){

		let
			targetEl=$(e.target),
			value = targetEl.val(),
			comparatorLeftEl = targetEl.parents(".comparator").first().find('.comparatorLeft').first()

		comparatorLeftEl.css( 'clip-path', makeClipPathOblique(value) )
	})

})