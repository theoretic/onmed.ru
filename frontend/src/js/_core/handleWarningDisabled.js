/*
handle warningDisabled
AT
15.11.23
*/

window.addEventListener('warningDisabled', function(e) {
	if( !e.detail.success ) return
	$("#header-warning").hide()
}, false)