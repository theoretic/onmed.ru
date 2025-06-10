/*
https://www.zhenghao.io/posts/verify-image-url
AT
13.02.23
*/

async function isImgUrl(url) {
	let result = await fetch(url, {method: 'HEAD'}).then(res => {
		return res.headers.get('Content-Type').startsWith('image')
	})
	return result
}