Element.prototype.visible = function (partial) {
	var viewTop = window.scrollY || window.scrollTop || document.getElementsByTagName("html")[0].scrollTop,
		windowHeight = window.innerHeight || document.documentElement.clientHeight || document.getElementsByTagName('body')[0].clientHeight,
		viewBottom = viewTop + windowHeight,
		_top = (window.pageYOffset || document.documentElement.scrollTop) + this.getBoundingClientRect().top,
		_bottom = _top + this.clientHeight,
		compareTop = partial === true ? _bottom : _top,
		compareBottom = partial === true ? _top : _bottom

	return ((compareBottom <= viewBottom) && (compareTop >= viewTop))
}
