/*
AT
13.04.21
*/

function preloadImage(url, callback)
{
	let img=new Image()
	img.src=url
	img.onload = callback
}