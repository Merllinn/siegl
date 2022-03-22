// Donut Chart 1
new Chartist.Pie('.donutChart', {
	series: [985, 476, 670]
}, {
	donut: true,
	donutWidth: 20,
	donutSolid: true,
	startAngle: 270,
	showLabel: false,
	height: "200px",
	plugins: [
		Chartist.plugins.tooltip()
	],
});



// Donut Chart 2
new Chartist.Pie('.donutChart2', {
	series: [985, 476, 670]
}, {
	donut: true,
	donutWidth: 40,
	donutSolid: true,
	startAngle: 270,
	showLabel: false,
	height: "200px",
	plugins: [
		Chartist.plugins.tooltip()
	],
});



// Donut Chart 3
new Chartist.Pie('.donutChart3', {
	series: [985, 476, 670]
}, {
	donut: true,
	donutSolid: true,
	startAngle: 270,
	showLabel: true,
	height: "200px",
	plugins: [
		Chartist.plugins.tooltip()
	],
});



// Donut Chart 4
new Chartist.Pie('.donutChart4', {
	series: [985, 476, 670]
}, {
	donut: true,
	donutWidth: 30,
	donutSolid: true,
	startAngle: 270,
	showLabel: true,
	height: "200px",
	chartPadding: 30,
	labelOffset: 30,
	labelDirection: 'explode',
	plugins: [
		Chartist.plugins.tooltip()
	],
});