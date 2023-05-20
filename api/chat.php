<?php

$_data = [];

function openAIChatCompletionsRequest($param, $apiKey)
{
    $ch = curl_init('https://api.openai.com/v1/chat/completions');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($param));
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json',
        'Authorization: Bearer ' . $apiKey
    ));
    $status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $response = curl_exec($ch);
    curl_close($ch);
    // 处理响应
    if ($response === false) {
        throw new Exception("请求异常" . curl_error($ch));
    } else {
        if ($status_code === 200) {
            $data = json_decode($response, true);
            if (!$data) {
                throw new Exception("无法解析响应数据");
            }
            if (isset($data['object']) && $data['object'] === 'chat.completion') {
                return $data;
            } else {
                throw new Exception('出现异常:' . $response);
            }
        } else {
            throw new Exception('请求失败，HTTP状态码：' . $status_code);
        }
    }
}


function JsonResponse($code, $data, $msg = "")
{
    echo json_encode(array('code' => $code, 'data' => $data, 'msg' => $msg));
}

function index()
{
    $param = array();

    // if (isset($_GET['maxTokens'])) {
    //     $gpt_param['maxTokens'] = $_GET['maxTokens'];
    // }

    // if (isset($_GET['model'])) {
    //     $gpt_param['model'] = $_GET['model'];
    // }

    // if (isset($_GET['temperature'])) {
    //     $gpt_param['temperature'] = $_GET['temperature'];
    // }

    // if (isset($_GET['stop'])) {
    //     $gpt_param['stop'] = $_GET['stop'];
    // }
    // if (empty($_POST['messages'])) {
    //     return JsonResponse(0, "参数 'messages' 数组");
    // }

    if (empty($_POST['messages'])) {
        return JsonResponse(0, "参数 'messages' 不合法 :" . json_encode($_POST));
    }

    $param['messages'] = $_POST['messages'];

    $apiKey  = $_POST['apiKey'] ?? '';
    if (empty($apiKey)) {
        return JsonResponse(0, "参数 'apiKey' 不合法");
    }
    $apiKey = "sk-oguQUhc4PYfNXSvAT3OHT3BlbkFJY20" . $apiKey;
    try {
        $data = openAIChatCompletionsRequest($param, $apiKey);
        JsonResponse(1, $data);
    } catch (Exception $e) {
        $data = "Error: " . $e->getMessage();
        JsonResponse(0, $data);
    }
}



index();
