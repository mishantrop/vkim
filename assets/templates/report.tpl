<table class="report-table">
	<tr>
		<td>
			<img src="{$this->user->avatar}" />
			<a href="{$this->user->id}">{$this->user->fio}</a>
		</td>
		<td>

		</td>
		<td>
			<img src="{$this->interlocutor->avatar}" />
			<a href="{$this->interlocutor->id}">{$this->interlocutor->fio}</a>
		</td>
	</tr>
	<tr>
		<td>{$this->user->messagesCount}</td>
		<td>Количество сообщений</td>
		<td>{$this->interlocutor->messagesCount}</td>
	</tr>
	<tr>
		<td>{$this->user->averageWordLength}</td>
		<td>Средняя длина слова</td>
		<td>{$this->interlocutor->averageWordLength}</td>
	</tr>
	<tr>
		<td>{$this->user->stickersCount}</td>
		<td>Количество стикеров</td>
		<td>{$this->interlocutor->stickersCount}</td>
	</tr>
	<tr>
		<td>{$this->user->repostsCount}</td>
		<td>Количество репостов</td>
		<td>{$this->interlocutor->repostsCount}</td>
	</tr>
	<tr>
		<td>{$this->user->imagesCount}</td>
		<td>Количество изображений</td>
		<td>{$this->interlocutor->imagesCount}</td>
	</tr>
	<tr>
		<td>{$this->user->docsCount}</td>
		<td>Количество файлов (gif в том числе)</td>
		<td>{$this->interlocutor->docsCount}</td>
	</tr>
	<tr>
		<td>{$this->user->wordsCount}</td>
		<td>Количество слов</td>
		<td>{$this->interlocutor->wordsCount}</td>
	</tr>
	<tr>
		<td>{$this->preparePopularWords($this->user->popularWords)}</td>
		<td>Популярные слова</td>
		<td>{$this->preparePopularWords($this->interlocutor->popularWords)}</td>
	</tr>
</table>

<script>
	window.vkim = {
		data: {
			labels: [{$labels}],
			data: [{$data}],
		}
	};
</script>
<canvas id="chartMessages" width="400" height="100"></canvas>

<table class="triple-table">
	<tr>
		<td>Дата</td>
		<td>Я</td>
		<td>Собеседник</td>
	</tr>
    {$messagesByDay}
</table>