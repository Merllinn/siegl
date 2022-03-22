var chart = new Chartist.Line('.lineChart7', {
	labels: ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'],
	series: [
		{
			name: 'Sales',
			data: [5000, 2000, 4000, 7000, 0, 3000, 5000]
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
		'Sales': {
			lineSmooth: Chartist.Interpolation.step()
		},
	},
	plugins: [
		Chartist.plugins.tooltip()
	],
});
