<?php
include 'access.php';
include 'Utils.class.php';
include 'VkimResponse.class.php';
include 'VkimUser.class.php';

class Vkim {
    private $accessToken = '';
    private $secret = '';
    private $apiVersion = '5.63';
    private $lastResponse;
    private $user;
    private $interlocutor;
	public $messagesLimit;

    public function __construct()
    {
		$preset = json_decode(file_get_contents('preset.php'));

		if (!is_object($preset)) {
			die('preset is not object!');
		}

		$this->messagesLimit = $preset->messages;
        $this->user = new VkimUser($preset->me);
        $this->interlocutor = new VkimUser($preset->interlocutor);

        $this->lastResponse = new VkimResponse();
        $this->logPath = $_SERVER['DOCUMENT_ROOT'].'/log';
    }

    public function setAccessToken(string $accessToken)
    {
        $this->accessToken = $accessToken;
    }

    public function setSecret(string $secret)
    {
        $this->secret = $secret;
    }

    public function getAccessToken(): string
    {
        return $this->accessToken;
    }

    public function getSecret(): string
    {
        return $this->secret;
    }

    private function getSig(string $methodName, array $requestParams): string
    {
        $getParams = http_build_query($requestParams);
        $methodString = '/method/'.$methodName.'?'. $getParams;
        $sig = md5($methodString.$this->getSecret());
        return $sig;
    }

    public function sendRequest(string $methodName, $requestParams)
    {
        $requestParams['v'] = $this->apiVersion;
        $requestParams['access_token'] = $this->getAccessToken();
        $getParams = http_build_query($requestParams);
        $methodString = '/method/'.$methodName.'?'. $getParams;
        $requestUri = 'https://api.vk.com'.$methodString.'&sig='.$this->getSig($methodName, $requestParams);
        $response = file_get_contents($requestUri);
        $vkResponse = json_decode($response);

		if (!is_object($vkResponse)) {
			return null;
		}

        if (is_object($vkResponse)) {
			if (isset($vkResponse->error) && is_object($vkResponse->error)) {
				echo '<p>Error code: '.$vkResponse->error->error_code.'</p>';
				echo '<p>Error message: '.$vkResponse->error->error_msg.'</p>';
				echo '<p>Request params: '.print_r($vkResponse->error->request_params, true).'</p>';
			} else {
				$this->logResponse($methodName, $vkResponse);
			}
        }
        return $vkResponse;
    }

    public function printR($variable)
    {
        return '<pre>'.print_r($variable, true).'</pre>';
    }

    public function getMessagesByDayLabels(array $messagesByDay): array
    {
        $labelsUser = [];
    	foreach ($messagesByDay as $date => $messagesByMe) {
    		$labelsUser[] = '"'.date('d.m.Y', $date).'"';
        }
        return $labelsUser;
    }

    public function getMessagesByDayData(array $messagesByDay): array
    {
    	$dataUser = [];
    	foreach ($messagesByDay as $date => $messagesByMe) {
    		$dataUser[] = (int)$messagesByMe;
        }
        return $dataUser;
    }

    public function replaceScalarUserPlaceholders(VkimUser $user, string $output, string $userPlaceholder): string
    {
        $a = get_object_vars($user);
		foreach ($a as $a1 => $a2) {
			if (is_scalar($a2)) {
				$output = str_replace('{$this->'.$userPlaceholder.'->'.$a1.'}', $a2, $output);
			}
		}
		$output = str_replace('{$this->preparePopularWords($this->'.$userPlaceholder.'->popularWords)}', $this->preparePopularWords($user->popularWords), $output);
        return $output;
    }

    public function PrintReport(): string
    {
		$report = file_get_contents('assets/templates/report.tpl');

        $output = $report;
		$output = $this->replaceScalarUserPlaceholders($this->user, $output, 'user');
		$output = $this->replaceScalarUserPlaceholders($this->user, $output, 'interlocutor');

		$labelsUser = $this->getMessagesByDayLabels($this->user->messagesByDay);
		$dataUser = $this->getMessagesByDayData($this->user->messagesByDay);

		$labelsInterlocutor = $this->getMessagesByDayLabels($this->interlocutor->messagesByDay);
		$dataInterlocutor = $this->getMessagesByDayData($this->interlocutor->messagesByDay);

		$messagesByDayOutput = '';
		foreach ($this->user->messagesByDay as $date => $messagesByMe) {
            $messagesByInterlocutor = (isset($this->interlocutor->messagesByDay[$date])) ? $this->interlocutor->messagesByDay[$date] : 0;
            $messagesByDayOutput .= '<tr>
										<td>'.date('d.m.Y', $date).'</td>
										<td>'.$messagesByMe.'</td>
										<td>'.$messagesByInterlocutor.'</td>
									</tr>';
        }

		$maxMessagesByHourUser = $this->getMaxMessagesByHour($this->user);
		$maxMessagesByHourInterlocutor = $this->getMaxMessagesByHour($this->interlocutor);


		// Punchcard
		$punchTpl = file_get_contents('assets/templates/punchItem.tpl');

		$punchcardUserOutput = $this->getUserPunchcardOutput($this->user, $maxMessagesByHourUser, $punchTpl);
		$punchcardInterlocutorOutput = $this->getUserPunchcardOutput($this->interlocutor, $maxMessagesByHourInterlocutor, $punchTpl);

		$output = str_replace('{$labelsUser}', implode(',', $labelsUser), $output);
		$output = str_replace('{$dataUser}', implode(',', $dataUser), $output);
		$output = str_replace('{$labelsInterlocutor}', implode(',', $labelsInterlocutor), $output);
		$output = str_replace('{$dataInterlocutor}', implode(',', $dataInterlocutor), $output);
		$output = str_replace('{$messagesByDay}', $messagesByDayOutput, $output);
		$output = str_replace('{$punchcardUser}', $punchcardUserOutput, $output);
		$output = str_replace('{$punchcardInterlocutor}', $punchcardInterlocutorOutput, $output);

		return $output;
    }

    public function getMaxMessagesByHour(VkimUser $user): int
    {
        $maxMessagesByHour = 0;
        foreach ($user->punchcard as $weekday => $hours) {
			foreach ($hours as $hour => $count) {
				if ($count > $maxMessagesByHour) {
					$maxMessagesByHour = $count;
				}
			}
		}
        return $maxMessagesByHour;
    }

    public function getUserPunchcardOutput(VkimUser $user, $maxMessagesByHour, $punchTpl)
    {
        $punchcardOutput = '';
        foreach ($user->punchcard as $weekday => $hours) {
            $weekdayName = Utils::getWeekdayName($weekday);
            $punchcardOutput .= '<tr>';
            $punchcardOutput .= '<td>'.$weekdayName.'</td>';
            foreach ($hours as $hour => $count) {
                $punchDegree = $this->getPunchDegree($count, $maxMessagesByHour);
                if ($count > 0) {
                    $punchTplProcessed = $punchTpl;
                    $punchTplProcessed = str_replace('{$punchDegree}', $punchDegree, $punchTplProcessed);
                    $punchTplProcessed = str_replace('{$count}', $count, $punchTplProcessed);
                    $punchcardOutput .= $punchTplProcessed;
                } else {
                    $punchcardOutput .= '<td>&nbsp;</td>';
                }
            }
            $punchcardOutput .= '</tr>';
        }
        return $punchcardOutput;
    }

	private function getPunchDegree(int $value, int $max): int
    {
		$k = ((int)$value > 0) ? $max/$value : 0;
		$punch = ($k > 0) ? intval(10/$k) : 0;
		return ($punch > 10) ? 10 : $punch;
	}

    public function logResponse(string $methodName, $response)
    {
        $microtime = microtime(true);
        $microtime = str_replace(' ', '', $microtime);
        $methodName = str_replace('.', '_', $methodName);
        $this->logPath = str_replace('..', '', $this->logPath);
        if (!file_exists($this->logPath)) {
            mkdir($this->logPath, 0777, false);
        }
        if (!file_exists($this->logPath)) {
            return false;
        }
        $filename = $methodName.'_'.$microtime.'.log';
        $fullPath = $this->logPath.'/'.$filename;
        if (is_object($response)) {
            file_put_contents($fullPath, print_r($response, true));
            return true;
        } else {
            echo '*';
        }
        return false;
    }

    public function isSuccessResponse($response)
    {
        return is_object($response) && isset($response->error) && is_object($response->error);
    }

    public function dumpDialogs()
    {
        $offset = 0;
        $limit = 20;
        $properties = [
            'access_token' => $this->getAccessToken(),
            'count' => $limit,
            'offset' => $offset,
        ];

        //echo $this->printR($response);
        $dialogs = [];

        $cx = 1;
        for ($i = 0; $i < $cx; $i++) {
            $properties['offset'] = $i * $limit;
            $vkResponse = $this->sendRequest('messages.getDialogs', $properties);
            //print_r($vkResponse);
            if (is_object($vkResponse)) {
                if ($vkResponse->response->count == 0) {
                    break;
                }
                echo gettype($vkResponse->response->items);
                foreach ($vkResponse->response->items as $dialog) {
                    if (isset($dialog->message) && is_object($dialog->message)) {
                        // Последние 200 сообщений из диалога
                        $properties = [
                            'user_id' => $dialog->message->user_id,
                            'count' => 200,
                        ];
                        $vkResponseMessages = $this->sendRequest('messages.getHistory', $properties);
                        $messages = $vkResponseMessages->items;
                        //print_r($messages);
                        $this->logResponse('messages.getHistory', $vkResponseMessages);
                    }
                }
                $dialogs = array_merge($dialogs, $vkResponse->response->response->items);
            }
        }
    }

    private function preparePopularWords($words)
    {
        $words = array_slice($words, 0, 100);
        $outputArray = [];
        //$words = array_flip($words);
        foreach ($words as $word => $count) {
            $outputArray[] = $word.'&nbsp;('.$count.')';
        }
        return implode(', ', $outputArray);
    }

    private function cleanWord($text)
    {
        $text = str_replace(',', '', $text);
        $text = str_replace('.', '', $text);
        $text = str_replace('—', '', $text);
        $text = str_replace('?', '', $text);
        //$text = str_replace('-', '', $text);
        $text = str_replace('!', '', $text);
        $text = str_replace(')', '', $text);
        $text = str_replace('(', '', $text);
        $text = str_replace('"', '', $text);
        $text = str_replace('\'', '', $text);
        //$text = str_replace(':', '', $text);
        $text = trim($text);
        return $text;
    }

    private function getPopularWords(VkimUser $user, array $words): array
    {
        $ignore = [
            'в', 'и', 'не', 'а', 'с', 'но', 'у', 'по',
            'на', 'то', 'к', 'А',
        ];
        $ignoreParts = [
            'http', 'https',
        ];
		$popularWords = $user->popularWords;
        //$ignore = [];
        foreach ($words as $word) {
            if (!Utils::containsCiryllicLetters($word)) {
                //continue;
            }
            if (empty($word)) {
                continue;
            }
            if (in_array($word, $ignore)) {
                continue;
            }
			foreach ($ignoreParts as $ignorePart) {
				if (substr_count($word, $ignorePart) > 0) {
					continue(2);
				}
			}
            if (!isset($popularWords[$word])) {
                $popularWords[$word] = 0;
            }
            $popularWords[$word]++;
        }
        asort($popularWords, SORT_NUMERIC);
        return array_reverse($popularWords);
    }

	private function getAverageWordLength($user)
    {
		$length = 0;
		$totalCount = 0;
		$average = 0;
		foreach ($user->popularWords as $word => $count) {
			$length += mb_strlen($word) * $count;
			$totalCount += $count;
		}

		if ($totalCount > 0) {
			$average = round($length/$totalCount, 4);
		}

		return $average;
	}

    public function getDialogMessages()
    {
        $properties = [
            'count' => 200,
            'offset' => 0,
            'user_id' => $this->interlocutor->id,
            'rev' => 1,
        ];

        $messages = [];
        $offset = 0;
        $limit = 200;
        $pages = 100;

		// Collect all messages
        for ($i = 0; $i < $pages; $i++) {
            $properties['offset'] = $i * $limit;
            $vkResponse = $this->sendRequest('messages.getHistory', $properties);
            if (is_object($vkResponse)) {
                $newMessages = (isset($vkResponse->response->items)) ? $vkResponse->response->items : [];
				if (count($newMessages) == 0) {
					break;
				}
                $messages = array_merge($messages, $newMessages);
            } else {

			}
			if (count($messages) >= $this->messagesLimit) {
				break;
			}
        }

        $firstMessageDateRound = 0;
        $lastMessageDateRound = 0;

		// Popular words
		// Words in timeline
        foreach ($messages as $messageIdx => $message) {
			$currentUser = ($message->from_id == $this->user->id) ? $this->user : $this->interlocutor;

            $message->body = $this->cleanWord($message->body);
            $messageDateRound = strtotime(date('00:00:00 d.m.Y', $message->date));
            if ($firstMessageDateRound == 0) {
                $firstMessageDateRound = $messageDateRound;
            }
            $lastMessageDateRound = $messageDateRound;

			// Attachments
			if (isset($message->attachments) && is_array($message->attachments)) {
				$currentUser->attachmentsCount += count($message->attachments);
				foreach ($message->attachments as $attachment) {
					switch ($attachment->type) {
						case 'audio':
							$currentUser->audioCount++;
							break;
						case 'doc':
                            if ($attachment->doc->type === 5) {
                                $currentUser->voiceCount++;
                            } else {
    							$currentUser->docsCount++;
                            }
							break;
						case 'photo':
							$currentUser->imagesCount++;
							break;
						case 'sticker':
							$currentUser->stickersCount++;
							break;
						case 'wall':
							$currentUser->repostsCount++;
							break;
					}
				}
			}

            if (empty($message->body)) {
                continue;
            }
            $words = explode(' ', $message->body);

			$currentUser->messagesCount++;
			$currentUser->wordsCount += count($words);

			$currentUser->popularWords = $this->getPopularWords($currentUser, $words);
			if (!isset($currentUser->messagesByDay[$messageDateRound])) {
				$currentUser->messagesByDay[$messageDateRound] = 0;
			}
			$currentUser->messagesByDay[$messageDateRound]++;

        }

		// Punchcard
		foreach ($messages as $messageIdx => $message) {
			$currentUser = ($message->from_id == $this->user->id) ? $this->user : $this->interlocutor;

			$hour = (int)date('H', $message->date);
			$weekday = (int)date('N', $message->date);
			$currentUser->punchcard[$weekday][$hour]++;
		}


        for ($i = $firstMessageDateRound; $i <= $lastMessageDateRound; $i++) {
            $dateRound = $firstMessageDateRound + $i * 86400;
            if ($dateRound > $lastMessageDateRound) {
                break;
            }
            if (!isset($currentUser->messagesByDay[$dateRound])) {
                $currentUser->messagesByDay[$dateRound] = 0;
            }
        }

		$this->user->averageWordLength = $this->getAverageWordLength($this->user);
		$this->interlocutor->averageWordLength = $this->getAverageWordLength($this->interlocutor);

    }

	public function setInterlocutor(string $link)
    {
		if (is_string($link)) {
			$cuts = [
				'https://m.', 'https://', 'http://m.', 'http://',
				'https://new.', 'http://m.', 'vk.com/id', 'vk.com/',
			];
			$link = str_replace($cuts, '', $link);

			$this->interlocutor->id = $link;
			$this->getUserInfo($this->interlocutor);
		}
	}

	// users.get
	public function getUsersInfo()
    {
		foreach ([$this->user, $this->interlocutor] as $user) {
			$this->getUserInfo($user);
		}
	}

	// users.get
	public function getUserInfo(VkimUser $user)
    {
		$properties = [
            'user_ids' => $user->id,
            'fields' => 'photo_50',
            'name_case' => 'Nom',
        ];
		$vkResponse = $this->sendRequest('users.get', $properties);
		if (is_object($vkResponse)) {
			foreach ($vkResponse->response as $info) {
				$user->id = (int)$info->id;
				$user->avatar = $info->photo_50;
				$user->fio = $info->first_name.' '.$info->last_name;
				break;
			}
		}
	}

};
