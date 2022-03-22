new Chartist.Line('.lineChart3', {
	labels: ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'],
	series: [
		[3, 9, 7, 9, 12],
		[2, 4, 3, 4, 5],
		[1, 3, 2, 1, 3]
	]
}, {
	height: "200px",
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
	plugins: [
		Chartist.plugins.tooltip()
	],
});




// Animated
chart.on('draw', function(data) {
	if(data.type === 'line' || data.type === 'area') {
		data.element.animate({
			d: {
				begin: 4000 * data.index,
				dur: 5000,
				from: data.path.clone().scale(1, 0).translate(0, data.chartRect.height()).stringify(),
				to: data.path.clone().stringify(),
				easing: Chartist.Svg.Easing.easeOutQuint
			}
		});
	}
});


