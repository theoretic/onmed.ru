/*
ajax actions for a[data-href]
AT
16.09.22
*/

function processDataHref( href, params, event ){

	if( params.method=='POST' && params.body ){
		let body = new FormData()
		for( name in params.body ) body.append( name, params.body[name] )
		params.body = body
	}

	fetch( href, params )
	.then( response => response.json() )
	.then( data => {
		if( data.message ) alert( data.message )
		if( data.error ) alert( data.error )
		if( data.csrf ) window.dispatchEvent( new CustomEvent('csrfChanged', {detail:data.csrf}) )
		if( event ) window.dispatchEvent( new CustomEvent(event, {detail:data}) )
	})
}

function handleDataHref(e){
//console.log('data-href clicked: ',e)

	let
		el = $(e.target).data('href')? $(e.target) : $(e.target).parents("[data-href]").first(),
		href = el.data('href'),
		event = el.data('event'),
		confirmText = el.data('confirm'),
params = el.data('params') || {}

	if( confirmText) {
		if ( confirm(confirmText) ) processDataHref( href, params, event )
		return
	}

	processDataHref( href, params, event )
}

//$("[data-href]").on( 'click touch', function(e){ handleDataHref(e) } )
//this call is needed for dynamically generated [data-href] elements
$("body").on( 'click touch', "[data-href]", function(e){ handleDataHref(e) } )