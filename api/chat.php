<?php

$_data = [];

function openAIChatCompletionsRequest($param, $apikey)
{
    $ch = curl_init('https://api.openai.com/v1/chat/completions');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($param));
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json',
        'Authorization: Bearer ' . $apikey
    ));
    $status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $response = curl_exec($ch);
    curl_close($ch);
    // 处理响应
    if ($response === false) {
        throw new Exception("请求异常" . curl_error($ch));
    } else {
        return $response;
    }
}


function JsonResponse($code, $data, $msg = "")
{
    echo json_encode(array('code' => $code, 'data' => $data, 'msg' => $msg));
}

function index()
{
    if (empty($_POST['param'])) {
        return JsonResponse(0, "参数不合法 :" . json_encode($_POST));
    }
    $post_param = json_decode($_POST['param'], true);;
    $apiKey  = $post_param['apiKey'];
    if (empty($apiKey)) {
        return JsonResponse(0, "参数 'apiKey' 不合法");
    }
    $apiKey = "sk-oguQUhc4PYfNXSvAT3OHT3BlbkFJY20" . $apiKey;
    try {
        $post_param = deleteArrayElementByKey($post_param, 'apiKey');
        $data = openAIChatCompletionsRequest($post_param, $apiKey);
        JsonResponse(1, $data);
    } catch (Exception $e) {
        $data = "Error: " . $e->getMessage();
        JsonResponse(0, $data);
    }
}

function deleteArrayElementByKey($array, $key)
{
    if (array_key_exists($key, $array)) {
        unset($array[$key]);
        return $array;
    } else {
        return $array;
    }
}



index();
