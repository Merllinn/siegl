new Chartist.Pie('.booking-source-donut', {
	labels: ['US', 'UK', 'BR', 'IN', 'TR'],
  series: [900, 750, 500, 350, 200]
}, {
	pie: true,
	donutWidth: 48,
	donutSolid: true,
	startAngle: 270,
	showLabel: true,
	height: "210px",
	plugins: [
		Chartist.plugins.tooltip()
	],
	low: 0
});