new Chartist.Bar('.multilineLabels', {
  labels: ['First quarter of the year', 'Second quarter of the year', 'Third quarter of the year', 'Fourth quarter of the year'],
  series: [
    [60000, 40000, 80000, 70000],
    [40000, 30000, 60000, 40000],
    [8000, 3000, 10000, 6000]
  ]
}, {
  seriesBarDistance: 15,
  height: "300px",
  axisX: {
    offset: 30
  },
  axisY: {
    offset: 80,
    labelInterpolationFnc: function(value) {
      return value + ' CHF'
    },
    scaleMinSpace: 15
  },
  plugins: [
		Chartist.plugins.tooltip()
	],
});