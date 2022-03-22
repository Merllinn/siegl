var chart8 = c3.generate({
	bindto: '#barAreaGraphOne',
	padding: {
		left: 40,
	},
	data: {
		columns: [
			['data1', 74, 20, 31, 52, 25, 55, 76, 12, 61, 35, 80, 22, 78, 21, 86, 102, 54, 92, 58, 10, 117, 67, 126],
			['data2', 15, 16, 19, 38, 36, 32, 36, 53, 58, 62, 65, 61, 64, 62, 59, 63, 69, 72, 71, 75, 80, 65, 71],
		],
		types: {
			data1: 'bar',
			data2: 'area-spline'
		},
		names: {
			data1: 'Customers',
			data2: 'Sales'
		},
		colors: {
			data1: '#1a8e5f',
			data2: '#262b31',
		},
	}
});