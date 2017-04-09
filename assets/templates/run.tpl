<form action="/" method="post">
	<input name="run" value="" type="hidden" />
	<div>
		<label>Собеседник</label><br/>
		<span>Оставьте пустым, чтобы брать значение из preset.php</span><br/>
		<input name="interlocutor" value="" type="text" placeholder="https://vk.com/durov" />
	</div>
	<div>
		<label>Количество сообщений</label><br/>
		<span>[0;1000]</span><br/>
		<input name="limit" value="1000" type="text" placeholder="1000" />
	</div>
	<div>
		<button type="submit">Запустить</button>
	</div>
</form>