<?php
require 'token.php';//设置webhook

define('token', '填token');//改为你自己的token
define('link', 'https://api.telegram.org/bot'.token.'/');

function exec_curl_request($handle) {
    $response = curl_exec($handle);
    $http_code = intval(curl_getinfo($handle, CURLINFO_HTTP_CODE));
    curl_close($handle);
    $response = json_decode($response, true);
    $response = $response['result'];
    return $response;
}

function Request($method, $parameters) {
    if (!$parameters) {$parameters = array();}
    foreach ($parameters as $key => &$val) {
        if (!is_numeric($val) && !is_string($val)) {
            $val = json_encode($val);
        }
    }
    $url = link.$method.'?'.http_build_query($parameters);
    $handle = curl_init($url);
    curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($handle, CURLOPT_CONNECTTIMEOUT, 5);
    curl_setopt($handle, CURLOPT_TIMEOUT, 60);
    return exec_curl_request($handle);
}
function Processing($message) {
    $message_id = $message['message_id'];//int
    $chat_id = $message['chat']['id'];//int or string
    $is_bot=$message['chat']['is_bot'];//bool
    $text= $message['text'];//string: max - 4096
    $timestmp=$message['date'];//int - Unix timestamp
    $fname=$message['chat']['first_name'];//string
    $lname=$message['chat']['last_name'];//string
    if(isset($lname)){
        $user_name ="$fname $lname";
    }else{
        $user_name=$fname;
    }
    $caption=$message['caption'];//string
    $sticker=$message['sticker']['file_id'];
    $stickerset=$message['sticker']['set_name'];
    $replytotext=$message['reply_to_message']['text'];
    $replytoid=$message['reply_to_message']['message_id'];
    $replytouser_id = $message['reply_to_message']['from']['id'];
    $username=$message['chat']['username'];
    $sendername=$message['from']['username'];
    $replaytosendername=$message['reply_to_message']['from']['username'];
    $newinchat=$message['new_chat_members'];
    /*
    Actions when bot recieves a message
    */
    Request(
    "sendMessage",
    array(
        'chat_id' => $chat_id,
        "text" => "你好, ".$user_name.", 欢迎来到php机器人",
        'reply_markup' => array(
            'inline_keyboard' => array(
                array(
                    array(
                        'text'=>'个人主页',
                        'url'=> 'https://www.baidu.com'
                    ),
                    array(
                        'text'=>'本机器人源码',
                        'url'=> 'https://github.com/tcphp/telegram-bot/'
                    )
                ),array(
                    array(
                        'text'=>'回调测试',
                        'callback_data'=> '回调'
                    )
                )
            )
        )
    )
);
}
function ProcQuery($callback_query) {
    $fname=$callback_query['message']['chat']['first_name'];//string
    $lname=$callback_query['message']['chat']['last_name'];//string
    if(isset($lname)){
        $user_name ="$fname $lname";
    }else{
        $user_name=$fname;
    }

    $chat_id=$callback_query['from']['id'];
    $data=$callback_query['data'];
    $message_id=$callback_query['message']['message_id'];
    $message_text=$callback_query['message']['text'];
    $chat_id=$callback_query['message']['chat']['id'];
    $username=$callback_query['message']['chat']['username'];
    $id=$callback_query['id'];
   
    if($data=="回调"){
    
         Request("editMessageText", array('chat_id' => $chat_id, "message_id" => $message_id,'text'=>"新的文字",  'reply_markup' => array(
            'inline_keyboard' => array(
               array(
                    array(
                        'text'=>'个人主页',
                        'url'=> 'https://www.baidu.com'
                    ),
                    array(
                        'text'=>'本机器人源码',
                        'url'=> 'https://github.com/tcphp/telegram-bot/'
                    )
                ),array(
                    array(
                        'text'=>'回调测试',
                        'callback_data'=> '回调'
                    )
                )
            )
        )
        ));
    
    }
}

function ProcInline($inline_query) {
    $id=$inline_query[id];
    $userid=$inline_query[from][id];
    $is_bot=$inline_query[from][is_bot];
    $fname=$inline_query[from][first_name];
    $lname=$inline_query[from][last_name];
    if(isset($lname)){
        $user_name ="$fname $lname";
    }else{
        $user_name=$fname;
    }
    $username=$inline_query[from][username];
    $language=$inline_query[from][language_code];
    $query=$inline_query[query];
    $offset=$inline_query[offset];

    Request(
    "sendMessage",
    array(
        'chat_id' => $userid,
        "text" => $query
    )
    );

}
$content = file_get_contents("php://input");
$update = json_decode($content, true);
if (isset($update["message"])) {Processing($update["message"]);}
if (isset($update["callback_query"])) {ProcQuery($update["callback_query"]);}
if (isset($update["inline_query"])) {ProcInline($update["inline_query"]);}
?>
