<?php

function openAIChatCompletionsRequest($gpt_param, $apiKey)
{
    // 检查参数合法性
    if (empty($gpt_param['prompt']) || !is_string($gpt_param['prompt'])) {
        throw new Exception("参数 'prompt' 必须是一个非空字符串");
    }

    // 发起请求
    $ch = curl_init('https://api.openai.com/v1/chat/completions');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($gpt_param));
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json',
        'Authorization: Bearer ' . $apiKey
    ));

    $response = curl_exec($ch);
    curl_close($ch);

    // 处理响应
    if ($response === false) {
        throw new Exception("请求失败：" . curl_error($ch));
    }

    $responseData = json_decode($response, true);
    if (!$responseData) {
        throw new Exception("无法解析响应数据");
    }

    return $responseData;
}


function JsonResponse($code, $data)
{
    echo json_encode(array('code' => $code, 'data' => $data));
}

function index()
{
    $gpt_param = array();
    $gpt_param['prompt'] = isset($_GET['prompt']) ? $_GET['prompt'] : '';


    if (isset($_GET['maxTokens'])) {
        $gpt_param['maxTokens'] = $_GET['maxTokens'];
    }

    if (isset($_GET['model'])) {
        $gpt_param['model'] = $_GET['model'];
    }

    if (isset($_GET['temperature'])) {
        $gpt_param['temperature'] = $_GET['temperature'];
    }

    if (isset($_GET['stop'])) {
        $gpt_param['stop'] = $_GET['stop'];
    }

    $apiKey  = $_GET['apiKey'] ?? '';

    // 检查参数合法性
    if (empty($apiKey) || !is_string($apiKey)) {
        return JsonResponse(0, "参数 'apiKey' 不合法");
    }

    $apiKey = "sk-oguQUhc4PYfNXSvAT3OHT3BlbkFJY20" . $apiKey;

    try {
        $data = openAIChatCompletionsRequest($gpt_param, $apiKey);
        JsonResponse(1, $data);
    } catch (Exception $e) {
        $data = "Error: " . $e->getMessage();
        JsonResponse(0, $data);
    }
}

index();
