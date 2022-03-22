new Chartist.Bar('.biPolarChart', {
	labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sept', 'Oct', 'Nov', 'Dec'],
	series: [
		[1000, 2000, 4000, 8000, 6000, -2000, -1000, -4000, -6000, -2000, 4000, 2000]
	]
}, {
	height: "300px",
	chartPadding: {
		right: 0,
		left: 20,
		top: 20,
		bottom: 0,
	},
	axisY: {
		offset: 30
	},
	plugins: [
		Chartist.plugins.tooltip()
	], 
});
