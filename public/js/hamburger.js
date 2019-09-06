window.addEventListener('load', function() {
	const hamburger = document.getElementById('hamburger');
	const menu = document.getElementById('menu');

	const boutonsMenu = document.getElementsByClassName('menuAncre');
	for(let i = 0; i < boutonsMenu.length; i++) {
		const bouton = boutonsMenu[i];
		bouton.onclick = function() {
			CloseMenu();
		};
	}

	hamburger.addEventListener('click', function() {
		if(menu.classList.contains('menu')) {
			CloseMenu();
		} else {
			menu.classList.add('menu');
		}
	});

	function CloseMenu() {
		menu.classList.remove('menu');
		menu.classList.add('menu-bye');
		setTimeout(function() {
			menu.classList.remove('menu-bye');
		},1450);
	}
});