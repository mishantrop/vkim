<html>
<head>
    <meta charset="utf-8" />
    <title>VKIM</title>
    <meta name="description" content="" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="theme-color" content="#4279b8" />
    <meta name="msapplication-navbutton-color" content="#4279b8" />
    <meta name="apple-mobile-web-app-status-bar-style" content="#4279b8" />
    <link rel="icon" href="favicon.ico" />
    <link href="assets/css/main.min.css?v=05052009042017" rel="stylesheet" />
</head>
<body>
    {$output}

</body>
<script src="assets/js/moment.js"></script>
<script src="assets/js/chart.min.js"></script>
<script>
	var ctx = document.querySelector('#chartMessages');
	var myChart = new Chart(ctx, {
		type: 'bar',
		data: {
			labels: window.vkim.data.labels,
			datasets: [{
				label: 'Count of messages',
				data: window.vkim.data.data,
				backgroundColor: 
					'rgba(255, 99, 132, 0.2)'
				,
				borderColor: 
					'rgba(255,99,132,1)'
				,
				borderWidth: 1
			}]
		},
		options: {
			scales: {
				yAxes: [{
					ticks: {
						beginAtZero:true,
						stacked: true
					}
				}]
			}
		}
	});
</script>
</html>