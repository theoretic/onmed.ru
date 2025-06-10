/*
AT
01.12.21
*/

//window.hideSearchResult = false

window.search = function(search){

	//if( window.hideSearchResult ) return
	if( search.length<3 ) return

	fetch( "/api/search?search="+search )
		.then( response => response.json() )
		.then( response => {
			//window.hideSearchResult = false
			let html=''
			for( i in response ){
				html += `<a href='${ response[i].url }'>${ response[i].title }</a>`
			}
			$("#search-results")
				.removeClass('hidden')
				.html(html)
		})
}

/*
$('body').on( 'keyup', function(e){
	if(e.keyCode == 27){
		window.hideSearchResult = true
		$("#search-results").addClass('hidden')
		}
	})
*/