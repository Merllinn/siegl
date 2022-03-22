new Chartist.Bar('.labelPlacementBarChart', {
	labels: ['Q1', 'Q2', 'Q3', 'Q4'],
	series: [
		[
			{meta: 'Online', value: 2000},
			{meta: 'Online', value: 4000},
			{meta: 'Online', value: 6000},
			{meta: 'Online', value: 8000},
		],
		[
			{meta: 'Offline', value: 3000},
			{meta: 'Offline', value: 5000},
			{meta: 'Offline', value: 7000},
			{meta: 'Offline', value: 9000},
		],
	],
}, {
	reverseData: true,
	horizontalBars: true,
	seriesBarDistance: 15,
	height: "300px",
	chartPadding: {
		right: 0,
		left: 20,
		top: 0,
		bottom: 0,
	},
	axisY: {
		offset: 50,
		position: 'end',
		scaleMinSpace: 50,
	},
	axisX: {
		position: 'start',
		scaleMinSpace: 50,
	},
	plugins: [
		Chartist.plugins.tooltip()
	],
});
