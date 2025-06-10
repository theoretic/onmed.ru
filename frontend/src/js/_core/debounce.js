/*
https://www.freecodecamp.org/news/javascript-debounce-example/
AT
16.02.22
*/

function debounce(func, timeout = 200){
	let timer
	return (...args) => {
		clearTimeout(timer)
		timer = setTimeout(() => { func.apply(this, args) }, timeout)
	}
}