window.onload = function(){
	//$('body').removeClass('transition');
	//console.log('removing transition...');//
	document.body.classList.remove('transition');
	}

window.onbeforeunload = function(){
	//$('body').addClass('transition');
	document.body.classList.add('transition');
	}
