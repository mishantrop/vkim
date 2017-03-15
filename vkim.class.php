<?php
include('access.php');

class Vkim {
    private $accessToken = '';
    private $secret = '';
    private $apiVersion = '5.53';
    
    // Объект последнего запроса
    private $lastResponse;
    
    // Текущий пользователь
    private $user;
    // Собеседник
    private $interlocutor;
    // Сообщений по дате [14234234 => 2, 15234234 => 23, ...]

    public function __construct() {
        $this->user = new StdClass();
        $this->user->id = 1; // My id
        $this->user->messagesCount = 0;
        $this->user->wordsCount = 0;
        $this->user->popularWords = [];
        $this->user->messagesByDay = [];
        
        $this->interlocutor = new StdClass();
        $this->interlocutor->id = 2; // Interlocutor id
        $this->interlocutor->messagesCount = 0;
        $this->interlocutor->wordsCount = 0;
        $this->interlocutor->popularWords = [];
        $this->interlocutor->messagesByDay = [];
        
        $this->lastResponse = null;
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
        if (is_object($vkResponse)) {
            $this->logResponse($methodName, $vkResponse);
        }
        return $vkResponse;
    }
    
    public function printR($variable) {
        return '<pre>'.print_r($variable, true).'</pre>';
    }
    
    public function PrintReport() {
        $output = '<table>';
        
        // Общая статистика
        $output .= '<table class="double-table">';
        $output .= '<tr><td>Я</td><td>Собеседник</td></tr>';
        $output .= '<tr><td colspan="2" style="text-align: center; font-weight: bold;">Количество сообщений</td></tr>';
        $output .= '<tr><td>'.$this->user->messagesCount.'</td><td>'.$this->interlocutor->messagesCount.'</td></tr>';
        $output .= '<tr><td colspan="2" style="text-align: center; font-weight: bold;">Количество слов</td></tr>';
        $output .= '<tr><td>'.$this->user->wordsCount.'</td><td>'.$this->interlocutor->wordsCount.'</td></tr>';
        $output .= '<tr><td colspan="2" style="text-align: center; font-weight: bold;">Популярные слова</td></tr>';
        $output .= '<tr><td>'.$this->preparePopularWords($this->user->popularWords).'</td><td>'.$this->preparePopularWords($this->interlocutor->popularWords).'</td></tr>';
        $output .= '</table>';
        
        // Статистика по сообщениям в день
        $output .= '<table class="triple-table">';
        $output .= '<tr><td>Дата</td><td>Я</td><td>Собеседник</td></tr>';
        foreach ($this->user->messagesByDay as $date => $messagesByMe) {
            $messagesByInterlocutor = (isset($this->interlocutor->messagesByDay[$date])) ? $this->interlocutor->messagesByDay[$date] : 0;
            $output .= '<tr><td>'.date('d.m.Y', $date).'</td><td>'.$messagesByMe.'</td><td>'.$messagesByInterlocutor.'</td></tr>';
        }
        $output .= '</table>';
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
    
    private function cleanWords($text) {
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
        $text = str_replace(':', '', $text);
        $text = trim($text);
        return $text;
    }
    
    private function uniOrd($u) {
        $k = mb_convert_encoding($u, 'UCS-2LE', 'UTF-8');
        $k1 = ord(substr($k, 0, 1));
        $k2 = ord(substr($k, 1, 1));
        return $k2 * 256 + $k1;
    }
    
    private function fillPopularWords(&$user, $words) {
        $ignore = [
            'в', 'и', 'не', 'это', 'а', 'с', 'но', 'что', 'у', 'по', 'как',
            'Ну', 'на', 'то', 'так', 'где', 'к', 'Да', 'да', 'А', 'было', 'Не',
            'там', 'нет', 'Ага', '', '', '', '', '', '', '', '',
        ];
        $ignore = [];
        foreach ($words as $word) {
            if (!$this->containsCiryllicLetters($word)) {
                //continue;
            }
            if (in_array($word, $ignore)) {
                continue;
            }
            if (empty($word)) {
                continue;
            }
            if (!isset($user->popularWords[$word])) {
                $user->popularWords[$word] = 0;
            }
            $user->popularWords[$word]++;
        }
        asort($user->popularWords, SORT_NUMERIC);
        $user->popularWords = array_reverse($user->popularWords);
    }
    
    public function getDialogMessages() {
        $properties = [
            'count' => 200,
            'offset' => 0,
            'user_id' => $this->interlocutor->id,
            'rev' => 1,
        ];
        
        //echo $this->printR($response);
        $messages = [];
        $offset = 0;
        $limit = 200;
        
        $cx = 100;
        for ($i = 0; $i < $cx; $i++) {
            $properties['offset'] = $i * $limit;
            $vkResponse = $this->sendRequest('messages.getHistory', $properties);
            if (is_object($vkResponse)) {
                if ($vkResponse->response->count == 0) {
                    break;
                }
                $messages = array_merge($messages, $vkResponse->response->items);
            }
        }

        $firstMessageDateRound = 0;
        $lastMessageDateRound = 0;
        foreach ($messages as $message) {
            $message->body = $this->cleanWords($message->body);
            $messageDateRound = strtotime(date('00:00:00 d.m.Y', $message->date));
            if ($firstMessageDateRound == 0) {
                $firstMessageDateRound = $messageDateRound;
            }
            $lastMessageDateRound = $messageDateRound;
            if (empty($message->body)) {
                continue;
            }
            $words = explode(' ', $message->body);
            if ($message->from_id == $this->user->id) {
                $this->user->messagesCount++;
                $this->user->wordsCount += count($words);
                $this->fillPopularWords($this->user, $words);
                if (!isset($this->user->messagesByDay[$messageDateRound])) {
                    $this->user->messagesByDay[$messageDateRound] = 0;
                }
                $this->user->messagesByDay[$messageDateRound]++;
            } elseif ($message->from_id == $this->interlocutor->id) {
                $this->interlocutor->messagesCount++;
                $this->interlocutor->wordsCount += count($words);
                $this->fillPopularWords($this->interlocutor, $words);
                if (!isset($this->interlocutor->messagesByDay[$messageDateRound])) {
                    $this->interlocutor->messagesByDay[$messageDateRound] = 0;
                }
                $this->interlocutor->messagesByDay[$messageDateRound]++;
            }
        }
        for ($i = $firstMessageDateRound; $i <= $lastMessageDateRound; $i++) {
            $dateRound = $firstMessageDateRound + $i * 86400;
            if ($dateRound > $lastMessageDateRound) {
                break;
            }
            if (!isset($this->user->messagesByDay[$dateRound])) {
                $this->user->messagesByDay[$dateRound] = 0;
            }
            if (!isset($this->interlocutor->messagesByDay[$dateRound])) {
                $this->interlocutor->messagesByDay[$dateRound] = 0;
            }
        }
        
        
    }
};