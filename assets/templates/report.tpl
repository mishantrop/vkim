<table class="double-table">
	<tr>
		<td>�</td>
		<td>����������</td>
	</tr>
	<tr>
		<td><img src="{$this->user->avatar}" /></td>
		<td><img src="{$this->interlocutor->avatar}" /></td>
	</tr>
	<tr>
		<td colspan="2" style="text-align: center; font-weight: bold;">���������� ���������</td>
	</tr>
	<tr>
		<td>{$this->user->messagesCount}</td>
		<td>{$this->interlocutor->messagesCount}</td>
	</tr>
	<tr>
		<td colspan="2" style="text-align: center; font-weight: bold;">���������� ��������</td>
	</tr>
	<tr>
		<td>{$this->user->stickersCount}</td>
		<td>{$this->interlocutor->stickersCount}</td>
	</tr>
	<tr>
		<td colspan="2" style="text-align: center; font-weight: bold;">���������� ��������</td>
	</tr>
	<tr>
		<td>{$this->user->repostsCount}</td>
		<td>{$this->interlocutor->repostsCount}</td>
	</tr>
	<tr>
		<td colspan="2" style="text-align: center; font-weight: bold;">���������� �����������</td>
	</tr>
	<tr>
		<td>{$this->user->imagesCount}</td>
		<td>{$this->interlocutor->imagesCount}</td>
	</tr>
	<tr>
		<td colspan="2" style="text-align: center; font-weight: bold;">���������� ������ (gif � ��� �����)</td>
	</tr>
	<tr>
		<td>{$this->user->docsCount}</td>
		<td>{$this->interlocutor->docsCount}</td>
	</tr>
	<tr>
		<td colspan="2" style="text-align: center; font-weight: bold;">���������� ����</td>
	</tr>
	<tr>
		<td>{$this->user->wordsCount}</td>
		<td>{$this->interlocutor->wordsCount}</td>
	</tr>
	<tr>
		<td colspan="2" style="text-align: center; font-weight: bold;">���������� �����</td>
	</tr>
	<tr>
		<td>{$this->preparePopularWords($this->user->popularWords)}</td>
		<td>{$this->preparePopularWords($this->interlocutor->popularWords)}</td>
	</tr>
</table>
        

<table class="triple-table">
	<tr>
		<td>����</td>
		<td>�</td>
		<td>����������</td>
	</tr>
    <tr>
		<td>{$date}</td>
		<td>{$messagesByMe}</td>
		<td>{$messagesByInterlocutor}</td>
	</tr>
</table>