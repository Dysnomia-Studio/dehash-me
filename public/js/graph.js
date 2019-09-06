/**
 * Draw a circle graph with percentage
 *
 * @param      {number}  percent  Percent drawed
 * @param      {<type>}  name     Name of graph
 * @param      {<type>}  color    Color of graph
 */
function percentCircle(percent,name,color) {

	const datalist= [percent, 100-percent];
	const colist = [color, 'grey'];

	const canvas = document.getElementById(name);
	const ctx = canvas.getContext('2d');

	const w = canvas.width;
	const h = canvas.height;

	const radius = h / 2 - 5;
	const centerx = w / 2;
	const centery = h / 2;

	let total = 0;
	for(x=0; x < datalist.length; x++) {
		total += datalist[x];
	}

	let lastend= 0;
	const offset = Math.PI / 2;
	for(let x=0; x < datalist.length; x++) {
		const thispart = datalist[x];
		ctx.beginPath();
		ctx.fillStyle = colist[x];
		ctx.moveTo(centerx,centery);

		const arcsector = Math.PI * (2 * thispart / total);
		ctx.arc(centerx, centery, radius, lastend - offset, lastend + arcsector - offset, false);
		ctx.lineTo(centerx, centery);
		ctx.fill();
		ctx.closePath();
		lastend += arcsector;
	}
}

/**
 * Draw a linear graph
 *
 * @param      {number}  percent  Percent drawed
 * @param      {<type>}  name     Name of graph
 * @param      {<type>}  color    Color of graph
 */
function linearGraph(name,percent,color) {
	const percentContainer = document.getElementById(name);
	percentContainer.innerHTML = '<span class=skill-name">'+ name +'</span><div class="percentBar" id="' + name + '-percent">.</div>';
	const percentBar = document.getElementById(name + '-percent');
	percentBar.style.backgroundColor = color;
	percentBar.style.color = color;

	const percentOne = percent/50; //2 sec to draw
	let currentpercent = 0;
	const intervalID = setInterval(() => {
		currentpercent += percentOne;
		percentBar.style.width = currentpercent + '%';
		if(currentpercent>=percent) {
			clearInterval(intervalID);
		}
	},40);
}