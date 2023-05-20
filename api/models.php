<?php

function getAvailableModels($apiKey)
{

    // 检查参数合法性
    if (empty($apiKey) || !is_string($apiKey)) {
        throw new Exception("参数 'apiKey' 不合法");
    }

    $ch = curl_init('https://api.openai.com/v1/models');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
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
    $apiKey  = $_GET['apiKey'] ?? '';
    $apiKey = "sk-oguQUhc4PYfNXSvAT3OHT3BlbkFJY20" . $apiKey;
    try {
        $models = getAvailableModels($apiKey);
        echo JsonResponse(1, $models);
    } catch (Exception $e) {
        $data = "Error: " . $e->getMessage();
        echo JsonResponse(0, ['models' => $data]);
    }
}

index();
