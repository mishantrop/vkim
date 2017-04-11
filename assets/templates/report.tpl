<table class="report-table">
	<tr>
		<td>
			<img src="{$this->user->avatar}" class="report__avatar" />
			<a href="{$this->user->id}" class="report__link">{$this->user->fio}</a>
		</td>
		<td>

		</td>
		<td>
			<img src="{$this->interlocutor->avatar}" class="report__avatar" />
			<a href="{$this->interlocutor->id}" class="report__link">{$this->interlocutor->fio}</a>
		</td>
	</tr>
	<tr>
		<td>{$this->user->messagesCount}</td>
		<td>Количество сообщений</td>
		<td>{$this->interlocutor->messagesCount}</td>
	</tr>
	<tr>
		<td>{$this->user->wordsCount}</td>
		<td>Количество слов</td>
		<td>{$this->interlocutor->wordsCount}</td>
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
		<td>{$this->preparePopularWords($this->user->popularWords)}</td>
		<td>Популярные слова</td>
		<td>{$this->preparePopularWords($this->interlocutor->popularWords)}</td>
	</tr>
</table>

<h2>Timeline Chart</h2>
<script>
	window.vkim = {
		data: {
			labels: [{$labels}],
			data: [{$data}],
		}
	};
</script>
<canvas id="chartMessages" width="400" height="100"></canvas>

<h2>Timeline Table</h2>
<table class="triple-table">
	<tr>
		<td>Дата</td>
		<td>Я</td>
		<td>Собеседник</td>
	</tr>
    {$messagesByDay}
</table>

<h2>Punchcard</h2>
<h3>You</h3>
<table class="punchcard-table">
	<tr>
		<td>День недели/Час</td>
		<td>0</td>
		<td>1</td>
		<td>2</td>
		<td>3</td>
		<td>4</td>
		<td>5</td>
		<td>6</td>
		<td>7</td>
		<td>8</td>
		<td>9</td>
		<td>10</td>
		<td>11</td>
		<td>12</td>
		<td>13</td>
		<td>14</td>
		<td>15</td>
		<td>16</td>
		<td>17</td>
		<td>18</td>
		<td>19</td>
		<td>20</td>
		<td>21</td>
		<td>22</td>
		<td>23</td>
	</tr>
	{$punchcardUser}
</table>

<h3>Interlocutor</h3>
<table class="punchcard-table">
	<tr>
		<td>День недели/Час</td>
		<td>0</td>
		<td>1</td>
		<td>2</td>
		<td>3</td>
		<td>4</td>
		<td>5</td>
		<td>6</td>
		<td>7</td>
		<td>8</td>
		<td>9</td>
		<td>10</td>
		<td>11</td>
		<td>12</td>
		<td>13</td>
		<td>14</td>
		<td>15</td>
		<td>16</td>
		<td>17</td>
		<td>18</td>
		<td>19</td>
		<td>20</td>
		<td>21</td>
		<td>22</td>
		<td>23</td>
	</tr>
	{$punchcardInterlocutor}
</table>