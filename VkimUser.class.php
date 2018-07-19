<?php
class VkimUser {
	public $audioCount;
	/**
	 * @var string
	 * https://pp.vk.me/...f6e/4-funfNRMwg.jpg
	 */
	public $avatar;
	public $id;
	public $messagesCount;
	public $averageWordLength;
	public $wordsCount;
	public $popularWords;
	public $messagesByDay;
	public $repostsCount;
	public $stickersCount;
	public $punchcard;

	public function __construct($id) {
        $this->audioCount = 0;
        $this->avatar = 'https://pp.userapi.com/c637616/v637616028/360fa/xaVazY3QUnk.jpg';
        $this->id = $id;
        $this->docsCount = 0;
        $this->messagesCount = 0;
        $this->averageWordLength = 0;
        $this->wordsCount = 0;
        $this->popularWords = [];
        $this->messagesByDay = [];
        $this->attachmentsCount = 0;
        $this->imagesCount = 0;
        $this->repostsCount = 0;
        $this->stickersCount = 0;
        $this->punchcard = [
			1 => [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
			2 => [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
			3 => [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
			4 => [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
			5 => [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
			6 => [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
			7 => [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
		];
	}
}
