new Chartist.Line('.areaChartThree', {
	labels: ['Q1', 'Q2', 'Q3', 'Q4'],
	series: [
		[
			{meta: 'Income', value: 5000},
			{meta: 'Income', value: 3000},
			{meta: 'Income', value: 7000},
			{meta: 'Income', value: 4000},
		]
	]
}, {
	low: 0,
	lineSmooth: true,
	showArea: true,
	showLine: true,
	showPoint: true,
	showLabel: true,
	fullWidth: true,
	height: "276px",
	chartPadding: {
		right: 20
	},
	axisX: {
		showGrid: true,
		showLabel: true,
	}, 
	axisY: {
		showGrid: true,
		showLabel: true,
	},
	plugins: [
		Chartist.plugins.tooltip()
	],
});