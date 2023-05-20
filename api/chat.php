<?php

function openAIChatCompletionsRequest($prompt, $maxTokens, $temperature, $model, $n, $stop, $apiKey)
{
    // 检查参数合法性
    if (empty($prompt) || !is_string($prompt)) {
        throw new Exception("参数 'prompt' 必须是一个非空字符串");
    }

    if (!is_int($maxTokens) || $maxTokens <= 0) {
        throw new Exception("参数 'maxTokens' 必须是一个大于零的整数");
    }

    if (!is_numeric($temperature) || $temperature <= 0 || $temperature > 1) {
        throw new Exception("参数 'temperature' 必须是一个介于 0 到 1 之间的数值");
    }

    if (empty($model) || !is_string($model)) {
        throw new Exception("参数 'model' 必须是一个非空字符串");
    }

    if (!is_int($n) || $n <= 0) {
        throw new Exception("参数 'n' 必须是一个大于零的整数");
    }

    // 构建请求数据
    $data = array(
        'prompt' => $prompt,
        'max_tokens' => $maxTokens,
        'temperature' => $temperature,
        'model' => $model,
        'n' => $n,
        'stop' => $stop
    );

    // 发起请求
    $ch = curl_init('https://api.openai.com/v1/chat/completions');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
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
    // 外部获取 GET 请求参数
    $prompt = isset($_GET['prompt']) ? $_GET['prompt'] : '';
    $maxTokens = isset($_GET['maxTokens']) ? intval($_GET['maxTokens']) : 0;
    $temperature = isset($_GET['temperature']) ? floatval($_GET['temperature']) : 0.0;
    $model = isset($_GET['model']) ? $_GET['model'] : '';
    $n = isset($_GET['n']) ? intval($_GET['n']) : 0;
    $stop = isset($_GET['stop']) ? $_GET['stop'] : '';
    $apiKey  = $_GET['apiKey'] ?? '';

    // 检查参数合法性
    if (empty($apiKey) || !is_string($apiKey)) {
        return JsonResponse(0, "参数 'apiKey' 不合法");
    }

    $apiKey = "sk-oguQUhc4PYfNXSvAT3OHT3BlbkFJY20" . $apiKey;

    try {
        $data = openAIChatCompletionsRequest($prompt, $maxTokens, $temperature, $model, $n, $stop, $apiKey);
        JsonResponse(1, $data);
    } catch (Exception $e) {
        $data = "Error: " . $e->getMessage();
        JsonResponse(0, $data);
    }
}

index();
