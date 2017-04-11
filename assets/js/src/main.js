$(function() {
	$('.spoiler .spoiler__trigger').click(function(e){
		var spoiler = $(this).parent();
		$(spoiler).find('.spoiler__content').toggle();
	});
	
	var ctx = document.querySelector('#chartMessagesUser');
	var myChart = new Chart(ctx, {
		type: 'bar',
		data: {
			labels: window.vkim.user.labels,
			datasets: [{
				label: 'Count of messages',
				data: window.vkim.user.data,
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
	
	
	var ctx = document.querySelector('#chartMessagesInterlocutor');
	var myChart = new Chart(ctx, {
		type: 'bar',
		data: {
			labels: window.vkim.interlocutor.labels,
			datasets: [{
				label: 'Count of messages',
				data: window.vkim.interlocutor.data,
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
});