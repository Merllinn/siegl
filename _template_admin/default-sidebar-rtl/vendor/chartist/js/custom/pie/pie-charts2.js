// Pie Chart 1
new Chartist.Pie('.pieChart', {
	labels: ['US', 'UK', 'BR', 'IN', 'TR'],
  series: [900, 750, 500, 350, 200]
}, {
	pie: true,
	donutWidth: 50,
	donutSolid: true,
	startAngle: 270,
	showLabel: true,
	height: "270px",
	plugins: [
		Chartist.plugins.tooltip()
	],
	low: 0
});




// Pie Chart 2
new Chartist.Pie('.pieChart2', {
	labels: ['Germany', 'Canada', 'Turky', 'Brazil', 'India'],
  series: [800, 750, 500, 350, 400]
}, {
	pie: true,
	donutWidth: 50,
	donutSolid: true,
	startAngle: 270,
	showLabel: true,
	labelOffset: 70,
	chartPadding: 40,
	height: "270px",
	plugins: [
		Chartist.plugins.tooltip()
	],
	low: 0
});