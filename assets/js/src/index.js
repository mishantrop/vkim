class App {
	constructor() {
		console.log('App');
	}

	init() {
		this.spoilerTrigger = document.querySelector('.spoiler .spoiler__trigger');
		if (this.spoilerTrigger) {
			this.spoilerTrigger.addEventListener('click', () => {
				const spoilerContent = this.spoilerTrigger.parentNode.querySelector('.spoiler__content');
				if (spoilerContent) {
					spoilerContent.style.display = (spoilerContent.style.display === 'block') ? 'none' : 'block';
				}
			});
		}
		this.initCharts();
	}

	initChart(selector, labels, data) {
		const ctx = document.querySelector(selector);
		const myChart = new Chart(ctx, {
			type: 'bar',
			data: {
				labels: labels,
				datasets: [{
					label: 'Count of messages',
					data: data,
					backgroundColor: 'rgba(255, 99, 132, 0.2)',
					borderColor: 'rgba(255,99,132,1)',
					borderWidth: 1,
				}],
			},
			options: {
				scales: {
					yAxes: [{
						ticks: {
							beginAtZero:true,
							stacked: true,
						},
					}],
				},
			},
		});
	}

	initCharts() {
		this.initChart('#chartMessagesUser', window.vkim.user.labels, window.vkim.user.data);
		this.initChart('#chartMessagesInterlocutor', window.vkim.interlocutor.labels, window.vkim.interlocutor.data);
	}
}

const init = () => {
	const app = new App();
	app.init();
}


if (document.readyState === 'complete' || document.readyState !== 'loading') {
  console.log('developed by quasi-art.ru');
  init();
} else {
  document.addEventListener('DOMContentLoaded', init);
}
