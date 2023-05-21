<?php


function openAIChatCompletionsRequest($param, $apikey)
{
    $ch = curl_init('https://api.openai.com/v1/chat/completions');
    curl_setopt_array($ch, array(
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => json_encode($param),
        CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json',
            'Authorization: Bearer ' . $apikey
        ),
        CURLOPT_TIMEOUT => 60
    ));
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
        return JsonResponse(0, "参数不合法 V1 :" . json_encode($_POST));
    }
    if (empty($_POST['apiKey'])) {
        return JsonResponse(0, "参数 'apiKey' 不合法 :" . json_encode($_POST));
    }
    $apiKey  = $_POST['apiKey'];
    $post_param = json_decode($_POST['param'], true);;
    if (empty($apiKey)) {
        return JsonResponse(0, "参数 'apiKey' 不合法");
    }
    $apiKey = "sk-oguQUhc4PYfNXSvAT3OHT3BlbkFJY20" . $apiKey;
    try {
        $data = openAIChatCompletionsRequest($post_param, $apiKey);
        JsonResponse(1, $data);
    } catch (Exception $e) {
        $data = "Error: " . $e->getMessage();
        JsonResponse(0, $data);
    }
}




index();
