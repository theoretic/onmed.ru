/*
handle warningDisabled
AT
28.08.25
*/

window.addEventListener('cookieConcentAccepted', function(e) {
	let maxAge = 86400*30
	document.cookie = "cookieConcentAccepted=1; max-age=" + maxAge + "; path=/"
}, false)