var options = {
	chart: {
		height: 320,
		type: 'area',
		toolbar: {
			show: false,
		},
	},
	dataLabels: {
		enabled: false
	},
	stroke: {
		curve: 'smooth',
		width: 3
	},
	series: [{
		name: 'Wafi Admin',
		data: [600, 4000, 2800, 2100, 4200, 1090, 5000]
	}, {
		name: 'Wafi Dashboard',
		data: [1100, 3200, 4500, 3200, 3400, 5200, 2100]
	}],
	grid: {
		row: {
			colors: ['#ffffff'], // takes an array which will be repeated on columns
			opacity: 0.5
		},
	},
	xaxis: {
		type: 'datetime',
		categories: ["2018-09-19T00:00:00", "2018-09-19T01:30:00", "2018-09-19T02:30:00", "2018-09-19T03:30:00", "2018-09-19T04:30:00", "2018-09-19T05:30:00", "2018-09-19T06:30:00"],                
	},
	theme: {
		monochrome: {
			enabled: true,
			color: '#1a8e5f',
			shadeIntensity: 0.1
		},
	},
	markers: {
		size: 0,
		opacity: 0.2,
		colors: ["#1a8e5f"],
		strokeColor: "#fff",
		strokeWidth: 2,
		hover: {
			size: 7,
		}
	},
	tooltip: {
		x: {
			format: 'dd/MM/yy'
		},
	}
}

var chart = new ApexCharts(
	document.querySelector("#basic-area-spline-graph"),
	options
);

chart.render();
