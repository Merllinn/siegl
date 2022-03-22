new Chartist.Bar('.extreme-responsive', {
	labels: ['Quarter 1', 'Quarter 2', 'Quarter 3', 'Quarter 4'],
	series: [
		[5000, 4000, 3000, 7000],
		[3000, 2000, 9000, 5000],
		[1000, 5000, 8000, 4000],
		[2000, 3000, 4000, 6000],
		[4000, 1000, 2000, 1000]
	]
}, {
	// Default mobile configuration
	stackBars: true,
	height: "300px",
	axisX: {
		labelInterpolationFnc: function(value) {
			return value.split(/\s+/).map(function(word) {
				return word[0];
			}).join('');
		}
	},
	axisY: {
		offset: 0
	},
	plugins: [
		Chartist.plugins.tooltip()
	],
}, [
	// Options override for media > 400px
	['screen and (min-width: 400px)', {
		reverseData: true,
		horizontalBars: true,
		axisX: {
			labelInterpolationFnc: Chartist.noop
		},
		axisY: {
			offset: 40
		}
	}],
	// Options override for media > 800px
	['screen and (min-width: 800px)', {
		stackBars: false,
		seriesBarDistance: 10
	}],
	// Options override for media > 1000px
	['screen and (min-width: 1000px)', {
		reverseData: false,
		horizontalBars: false,
		seriesBarDistance: 15
	}]
]);
