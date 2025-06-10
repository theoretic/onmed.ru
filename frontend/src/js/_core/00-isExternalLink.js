/*
https://stackoverflow.com/questions/2910946/test-if-links-are-external-with-jquery-javascript?rq=3
AT
04.02.25
*/

function isExternalLink(url) {
	//return (url !== window.location.host)
console.log('isExternalLink: ', url, window.location.host)

	//return ( url.indexOf(window.location.host) == -1 )

	let
		urlParts = url.split('.').reverse(),
		locationParts = window.location.host.split('.').reverse(),
		result = false

//console.log('urlParts: ', urlParts)
//console.log('locationParts: ', locationParts)

	//checking first and second array elements, e.g. ru.onmed

	for( i=0; i<=1; i++ ){
		if( urlParts[i] == locationParts[i] ) continue
		result = true
		break
	}

//console.log('result: ', result)

	return result
}