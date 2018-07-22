<section id="comparison" class="section">
	<table class="report-table">
		<tr>
			<td>

			</td>
			<td>
				<div class="report__bage">
					<img src="{$this->user->avatar}" class="report__avatar" alt="" />
					<a href="{$this->user->id}" class="report__link">{$this->user->fio}</a>
				</div>
			</td>
			<td>
				<div class="report__bage">
					<img src="{$this->interlocutor->avatar}" class="report__avatar" alt="" />
					<a href="{$this->interlocutor->id}" class="report__link">{$this->interlocutor->fio}</a>
				</div>
			</td>
		</tr>
		<tr>
			<td>Количество сообщений</td>
			<td>{$this->user->messagesCount}</td>
			<td>{$this->interlocutor->messagesCount}</td>
		</tr>
		<tr>
			<td>Количество слов</td>
			<td>{$this->user->wordsCount}</td>
			<td>{$this->interlocutor->wordsCount}</td>
		</tr>
		<tr>
			<td>Средняя длина слова</td>
			<td>{$this->user->averageWordLength}</td>
			<td>{$this->interlocutor->averageWordLength}</td>
		</tr>
		<tr>
			<td>Количество стикеров</td>
			<td>{$this->user->stickersCount}</td>
			<td>{$this->interlocutor->stickersCount}</td>
		</tr>
		<tr>
			<td>Количество репостов</td>
			<td>{$this->user->repostsCount}</td>
			<td>{$this->interlocutor->repostsCount}</td>
		</tr>
		<tr>
			<td>Количество изображений</td>
			<td>{$this->user->imagesCount}</td>
			<td>{$this->interlocutor->imagesCount}</td>
		</tr>
		<tr>
			<td>Количество файлов (gif в том числе)</td>
			<td>{$this->user->docsCount}</td>
			<td>{$this->interlocutor->docsCount}</td>
		</tr>
		<tr>
			<td>Количество аудиосообщений</td>
			<td>{$this->user->voiceCount}</td>
			<td>{$this->interlocutor->voiceCount}</td>
		</tr>
		<tr>
			<td>Количество аудиозаписей</td>
			<td>{$this->user->audioCount}</td>
			<td>{$this->interlocutor->audioCount}</td>
		</tr>
		<tr>
			<td>Популярные слова</td>
			<td>{$this->preparePopularWords($this->user->popularWords)}</td>
			<td>{$this->preparePopularWords($this->interlocutor->popularWords)}</td>
		</tr>
	</table>
</section>

<section id="timeline-charts" class="section">
	<h2>Timeline Chart</h2>
	<script>
		window.vkim = {
			user: {
				labels: [{$labelsUser}],
				data: [{$dataUser}],
			},
			interlocutor: {
				labels: [{$labelsInterlocutor}],
				data: [{$dataInterlocutor}],
			}
		};
	</script>
	<h3>Me</h3>
	<canvas id="chartMessagesUser" width="400" height="100"></canvas>
	<h3>Interlocutor</h3>
	<canvas id="chartMessagesInterlocutor" width="400" height="100"></canvas>

	<h2>Timeline Table</h2>
	<div class="spoiler">
		<div class="spoiler__trigger">Показать или скрыть</div>
		<div class="spoiler__content" style="display: none;">
			<table class="triple-table">
				<tr>
					<td>Дата</td>
					<td>Я</td>
					<td>Собеседник</td>
				</tr>
			    {$messagesByDay}
			</table>
		</div>
	</div>
</section>

<section id="punchcards" class="section">
	<h2>Punchcard</h2>
	<h3>Me</h3>
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
</section>
