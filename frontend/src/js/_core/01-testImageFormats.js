/*
https://stackoverflow.com/questions/5573096/detecting-webp-support
testWebp() should be called before any javascript data-src or data-back image manipulation
AT
21.03.22
*/

window.imageFormatTests = {
	'avif' : 'data:image/avif;base64,AAAAIGZ0eXBhdmlmAAAAAGF2aWZtaWYxbWlhZk1BMUIAAADybWV0YQAAAAAAAAAoaGRscgAAAAAAAAAAcGljdAAAAAAAAAAAAAAAAGxpYmF2aWYAAAAADnBpdG0AAAAAAAEAAAAeaWxvYwAAAABEAAABAAEAAAABAAABGgAAAB0AAAAoaWluZgAAAAAAAQAAABppbmZlAgAAAAABAABhdjAxQ29sb3IAAAAAamlwcnAAAABLaXBjbwAAABRpc3BlAAAAAAAAAAIAAAACAAAAEHBpeGkAAAAAAwgICAAAAAxhdjFDgQ0MAAAAABNjb2xybmNseAACAAIAAYAAAAAXaXBtYQAAAAAAAAABAAEEAQKDBAAAACVtZGF0EgAKCBgANogQEAwgMg8f8D///8WfhwB8+ErK42A=',

	'webp' : 'data:image/webp;base64,UklGRjoAAABXRUJQVlA4IC4AAACyAgCdASoCAAIALmk0mk0iIiIiIgBoSygABc6WWgAA/veff/0PP8bA//LwYAAA',
}

window.imageFormat = false

async function testImageFormat(src,callback){
//console.log('-- testImageFormat: src:',src)

	let img = new Image()
	img.src = src

	try{
		await img.decode()
//console.log('-- testImageFormat: src, result:', src, img.height == 2)
//console.log('-- testImageFormat: result:', img.height == 2)
		callback( img.height == 2 )
	}
	catch{
//console.log('-- testImageFormat: catch')
		callback( false )
	}
}

async function testImagesFormats(callback){

	window.imageFormat = false

	for( format in window.imageFormatTests ) {

//console.log('testing format:',format)

		try{
			await testImageFormat(window.imageFormatTests[format],function(result){
//console.log('- format, result:',format, result)
			if(!result) return
			window.imageFormat = format
//console.log('- test done')
			callback()
			})
		}
		catch (exception){
//console.log('- exception:',exception)
		}

	if(window.imageFormat) break
	}

	window.imageFormat = window.imageFormat || '_fallback'
	callback()
}