var times = function(n) {
	return Array.apply(null, new Array(n));
};

var data = times(20).map(Math.random).reduce(function(data, rnd, index) {
	data.labels.push(index + 1);
	data.series.forEach(function(series) {
		series.push(Math.random() * 100)
	});

	return data;
}, {
	labels: [],
	series: times(4).map(function() { return new Array() })
});

var options = {
	showLine: false,
	height: "320px",
	fullWidth: true,
	chartPadding: {
		right: 10,
		left: 10,
		top: 10,
		bottom: 10,
	},
	plugins: [
		Chartist.plugins.tooltip()
	],
	axisX: {
		labelInterpolationFnc: function(value, index) {
			return index % 13 === 0 ? 'W' + value : null;
		}
	}
};

var responsiveOptions = [
	['screen and (min-width: 640px)', {
		axisX: {
			labelInterpolationFnc: function(value, index) {
				return index % 4 === 0 ? 'W' + value : null;
			}
		}
	}]
];

new Chartist.Line('.scatterChart', data, options, responsiveOptions);