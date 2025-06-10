/*
on leave events
AT
04.02.25
*/

function preventLeave(){
//console.log('cookie: ', getCookie('onLeaveActionsDone'))

	Modal.show('#modal-onleave')
	setCookie('onLeaveActionsDone', 1, 1)

	//adaptImages('.modal img[data-src]', 'data-src')
	//adaptBacks('.modal [data-back]', 'data-back')
}

//

/*
document.onvisibilitychange = function(e) {
console.log('onvisibilitychange')
	if (document.visibilityState !== 'hidden') return
	preventLeave()
}


window.addEventListener("beforeunload", function(e){
console.log('beforeunload: ',e)
	if ( !isExternalLink(e.target) ) return
	preventLeave()
})

*/

window.addEventListener("blur", function(){
//console.log('blur')
	if ( getCookie('onLeaveActionsDone')==1 ) return
	preventLeave()
})

$("a").on("click touch",function(e){
//console.log( 'click touch: ', e.target.host )
	if ( !isExternalLink(e.target.host) ) return
	if ( getCookie('onLeaveActionsDone')==1 ) return
	e.preventDefault()
	preventLeave()
})