/*
handle warningDisabled
AT
15.11.23
*/

window.addEventListener('warningDisabled', function(e) {
	if( !e.detail.success ) return

	let warning = $("#header1-warning")

	//remembering the closed warning in a cookie: pages are served from the StaticPages cache,
	//so the server-side session can't hide it; the inline script in warning-template.php reads this cookie
	let id = warning.attr('data-warning-id')
	if( id ) document.cookie = "disabledWarning=" + id + "; max-age=" + 86400*365 + "; path=/; SameSite=Lax"

	warning.hide()
}, false)
