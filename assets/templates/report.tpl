<table class="double-table">
	<tr>
		<td>
			<img src="{$this->user->avatar}" />
			<span>{$this->user->fio}</span>
		</td>
		<td>
			<img src="{$this->interlocutor->avatar}" />
			<span>{$this->interlocutor->fio}</span>
		</td>
	</tr>
	<tr>
		<td colspan="2" style="text-align: center; font-weight: bold;">Количество сообщений</td>
	</tr>
	<tr>
		<td>{$this->user->messagesCount}</td>
		<td>{$this->interlocutor->messagesCount}</td>
	</tr>
	<tr>
		<td colspan="2" style="text-align: center; font-weight: bold;">Количество стикеров</td>
	</tr>
	<tr>
		<td>{$this->user->stickersCount}</td>
		<td>{$this->interlocutor->stickersCount}</td>
	</tr>
	<tr>
		<td colspan="2" style="text-align: center; font-weight: bold;">Количество репостов</td>
	</tr>
	<tr>
		<td>{$this->user->repostsCount}</td>
		<td>{$this->interlocutor->repostsCount}</td>
	</tr>
	<tr>
		<td colspan="2" style="text-align: center; font-weight: bold;">Количество изображений</td>
	</tr>
	<tr>
		<td>{$this->user->imagesCount}</td>
		<td>{$this->interlocutor->imagesCount}</td>
	</tr>
	<tr>
		<td colspan="2" style="text-align: center; font-weight: bold;">Количество файлов (gif в том числе)</td>
	</tr>
	<tr>
		<td>{$this->user->docsCount}</td>
		<td>{$this->interlocutor->docsCount}</td>
	</tr>
	<tr>
		<td colspan="2" style="text-align: center; font-weight: bold;">Количество слов</td>
	</tr>
	<tr>
		<td>{$this->user->wordsCount}</td>
		<td>{$this->interlocutor->wordsCount}</td>
	</tr>
	<tr>
		<td colspan="2" style="text-align: center; font-weight: bold;">Популярные слова</td>
	</tr>
	<tr>
		<td>{$this->preparePopularWords($this->user->popularWords)}</td>
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