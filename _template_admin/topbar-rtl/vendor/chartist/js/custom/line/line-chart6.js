var chart = new Chartist.Line('.lineChart6', {
	labels: ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'],
	series: [
		{
			name: 'Series-1',
			data: [5000, 2000, -4000, 2000, 0, -2000, 5000]
		}, {
			name: 'Series-2',
			data: [4000, 3000, 5000, 3000, 1000, 3000, 6000]
		}, {
			name: 'Series-3',
			data: [2000, 4000, 3000, 1000, 4000, 5000, 3000]
		}   
	]
}, {
	height: "300px",
	fullWidth: true,
	chartPadding: {
		right: 30,
		left: 20,
		top: 20,
		bottom: 10,
	},
	axisX: {
		offset: 20,
		showGrid: true,
		showLabel: true,
	}, 
	axisY: {
		offset: 30,
		showLabel: true,
	},
	series: {
		'Series-1': {
			lineSmooth: Chartist.Interpolation.step()
		},
		'Series-2': {
			lineSmooth: Chartist.Interpolation.simple(),
			showArea: true,
		},
		'Series-3': {
			showPoint: true,
		}
	},
	plugins: [
		Chartist.plugins.tooltip()
	],
});
