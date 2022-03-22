var chart = new Chartist.Line('.lineChart4', {
	labels: ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'],
	series: [
		[3, 5, 10, 9, 12],
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

// Listening for draw events that get emitted by the Chartist chart
chart.on('draw', function(data) {
	// If the draw event was triggered from drawing a point on the line chart
	if(data.type === 'point') {
		// We are creating a new path SVG element that draws a triangle around the point coordinates
		var triangle = new Chartist.Svg('path', {
			d: ['M',
				data.x,
				data.y - 8,
				'L',
				data.x - 8,
				data.y + 8,
				'L',
				data.x + 8,
				data.y + 8,
				'z'].join(' '),
			style: 'fill-opacity: 1'
		}, 'ct-area');

		// With data.element we get the Chartist SVG wrapper and we can replace the original point drawn by Chartist with our newly created triangle
		data.element.replace(triangle);
	}
});

// Animated
chart.on('draw', function(data) {
	if(data.type === 'line' || data.type === 'area') {
		data.element.animate({
			d: {
				begin: 4000 * data.index,
				dur: 7000,
				from: data.path.clone().scale(1, 0).translate(0, data.chartRect.height()).stringify(),
				to: data.path.clone().stringify(),
				easing: Chartist.Svg.Easing.easeOutQuint
			}
		});
	}
});










