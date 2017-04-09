<?php
include('access.php');

class VkimResponse {
	
}

class VkimUser {
	/**
	 * @var string
	 * https://pp.vk.me/...f6e/4-funfNRMwg.jpg
	 */
	public $audioCount;
	public $avatar;
	public $id;
	public $messagesCount;
	public $averageWordLength;
	public $wordsCount;
	public $popularWords;
	public $messagesByDay;
	public $repostsCount;
	public $stickersCount;
	
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
	}
}

class Vkim {
    private $accessToken = '';
    private $secret = '';
    private $apiVersion = '5.63';
    private $lastResponse;
    private $user;
    private $interlocutor;
	public $messagesLimit;

    public function __construct() {
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
    
    public function setAccessToken($accessToken) {
        $this->accessToken = $accessToken;
    }
    
    public function setSecret($secret) {
        $this->secret = $secret;
    }
    
    public function getAccessToken() {
        return $this->accessToken;
    }
    
    public function getSecret() {
        return $this->secret;
    }
    
    private function getSig($methodName, $requestParams) {
        $getParams = http_build_query($requestParams);
        $methodString = '/method/'.$methodName.'?'. $getParams;
        $sig = md5($methodString.$this->getSecret());
        return $sig;
    }
    
    public function sendRequest($methodName, $requestParams) {
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
    
    public function printR($variable) {
        return '<pre>'.print_r($variable, true).'</pre>';
    }
    
    public function PrintReport() {
		$report = file_get_contents('assets/templates/report.tpl');
		
        $output = $report;
		
		$a = get_object_vars($this->user);
		foreach ($a as $a1 => $a2) {
			if (is_scalar($a2)) {
				$output = str_replace('{$this->user->'.$a1.'}', $a2, $output);
			}
		}
		$output = str_replace('{$this->preparePopularWords($this->user->popularWords)}', $this->preparePopularWords($this->user->popularWords), $output);
		$b = get_object_vars($this->interlocutor);
		foreach ($b as $b1 => $b2) {
			if (is_scalar($b2)) {
				$output = str_replace('{$this->interlocutor->'.$b1.'}', $b2, $output);
			}
		}
		$output = str_replace('{$this->preparePopularWords($this->interlocutor->popularWords)}', $this->preparePopularWords($this->interlocutor->popularWords), $output);
		

		
		$messagesByDayOutput = '';
		$labels = [];
		$data = [];
		foreach ($this->user->messagesByDay as $date => $messagesByMe) {
            $messagesByInterlocutor = (isset($this->interlocutor->messagesByDay[$date])) ? $this->interlocutor->messagesByDay[$date] : 0;
            $messagesByDayOutput .= '<tr>
										<td>'.date('d.m.Y', $date).'</td>
										<td>'.$messagesByMe.'</td>
										<td>'.$messagesByInterlocutor.'</td>
									</tr>';
			$labels[] = '"'.date('d.m.Y', $date).'"';
			$data[] = (int)$messagesByMe;
        }
		
		
		$output = str_replace('{$labels}', implode(',', $labels), $output);
		$output = str_replace('{$data}', implode(',', $data), $output);
		$output = str_replace('{$messagesByDay}', $messagesByDayOutput, $output);
		
		return $output;
    }
    
    public function logResponse($methodName, $response) {
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
    
    public function isSuccessResponse($response) {
        if (is_object($response)) {
            if (isset($response->error) && is_object($response->error))
            return true;
        }
        return false;
    }
    
    public function dumpDialogs() {
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
    
    private function preparePopularWords($words) {
        $words = array_slice($words, 0, 100);
        $outputArray = [];
        //$words = array_flip($words);
        foreach ($words as $word => $count) {
            $outputArray[] = $word.'&nbsp;('.$count.')';
        }
        return implode(', ', $outputArray);
    }
    
    private function containsCiryllicLetters($string) {
        $length = strlen($string);
        for ($i = 0; $i < $length; $i++) {
            $code = $this->uniOrd($string[$i]);
            if ($code >= 1040 && $code <= 1103) {
                return true;
            }
        }
        return false;
    }
    
    private function cleanWord($text) {
        $text = str_replace(',', '', $text);
        $text = str_replace('.', '', $text);
        $text = str_replace('—', '', $text);
        $text = str_replace('?', '', $text);
        $text = str_replace('-', '', $text);
        $text = str_replace('!', '', $text);
        $text = str_replace(')', '', $text);
        $text = str_replace('(', '', $text);
        $text = str_replace('"', '', $text);
        $text = str_replace('\'', '', $text);
        //$text = str_replace(':', '', $text);
        $text = trim($text);
        return $text;
    }
    
    private function uniOrd($u) {
        $k = mb_convert_encoding($u, 'UCS-2LE', 'UTF-8');
        $k1 = ord(substr($k, 0, 1));
        $k2 = ord(substr($k, 1, 1));
        return $k2 * 256 + $k1;
    }
    
    private function getPopularWords($user, $words) {
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
            if (!$this->containsCiryllicLetters($word)) {
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
    
	private function getAverageWordLength($user) {
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
	
    public function getDialogMessages() {
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
			//echo $this->printR($vkResponse);
            if (is_object($vkResponse)) {
                if ($vkResponse->response->count == 0) {
                    break;
                }
                $messages = array_merge($messages, $vkResponse->response->items);
				if (count($messages) > $this->messagesLimit) {
					break;
				}
            }
        }

        $firstMessageDateRound = 0;
        $lastMessageDateRound = 0;
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
							$currentUser->docsCount++;
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

	public function setInterlocutor($link) {
		if (is_string($link)) {
			$cuts = [
				'https://m.', 'https://', 'http://m.', 'http://', 
				'https://new.', 'http://m.', 'vk.com/id', 'vk.com/', 
			];
			foreach ($cuts as $cut) {
				$link = str_replace($cut, '', $link);
			}
			
			$this->interlocutor->id = $link;
			$this->getUserInfo($this->interlocutor);
		}
	}
	
	// users.get
	public function getUsersInfo() {
		foreach ([$this->user, $this->interlocutor] as $user) {
			$this->getUserInfo($user);
		}
	}
	
	// users.get
	public function getUserInfo($user) {
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