function chPostAvatar() {
	var pAvaImg  = document.getElementById('postuserpic').value;
	if (pAvaImg == 'no_avatar.png')
		document.getElementById('postavatar').src = gkl_avatar_img + '/no_avatar.png';
	else
		document.getElementById('postavatar').src = gkl_avatar + pAvaImg;
	
	return true;
}

function nextPostAvatar() {	
	if (document.getElementById('postuserpic').selectedIndex < document.getElementById('postuserpic').length) {
		document.getElementById('postuserpic').selectedIndex++;
	}

	document.getElementById('postavatar').src = gkl_avatar + document.getElementById('postuserpic').options[document.getElementById('postuserpic').selectedIndex].text;
}

function prevPostAvatar() {
	if (document.getElementById('postuserpic').selectedIndex > 1) {
		document.getElementById('postuserpic').selectedIndex--;
	}

	document.getElementById('postavatar').src = gkl_avatar + document.getElementById('postuserpic').options[document.getElementById('postuserpic').selectedIndex].text;	
}