<?php

function callChatGPT($question, $api_key)
{
    $endpoint = 'https://api.openai.com/v1/chat/completions';
    $headers = array(
        'Content-Type: application/json',
        'Authorization: Bearer ' . $api_key
    );
    $data = array(
        'model' => 'gpt-3.5-turbo',
        'messages' => array(
            array('role' => 'system', 'content' => 'You are a helpful assistant.'),
            array('role' => 'user', 'content' => $question)
        )
    );
    $ch = curl_init();
    curl_setopt_array($ch, array(
        CURLOPT_URL => $endpoint,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => $headers,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => json_encode($data)
    ));
    $response = curl_exec($ch);
    if ($response === false) {
        $error = curl_error($ch);
        throw new Exception("cURL request failed: " . $error);
    }
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    if ($httpCode !== 200) {
        throw new Exception("HTTP request failed with code " . $httpCode);
    }
    return $response;
}

function JsonResponse($code, $data)
{
    return json_encode(array('code' => $code, 'data' => $data));
}

function index()
{
    if (!isset($_GET['q'])) {
        echo JsonResponse(0, "488");
        return "";
    }

    if (!isset($_GET['k'])) {
        echo JsonResponse(0, "488");
        return "";
    }
    $q  = $_GET['q'];
    $k  = $_GET['k'];
    $open_key = "sk-oguQUhc4PYfNXSvAT3OHT3BlbkFJY20" . $k;
    try {
        $data = callChatGPT($q, $open_key);
        echo JsonResponse(1, $data);
    } catch (Exception $e) {
        // 处理异常
        $data = "Error: " . $e->getMessage();
        echo JsonResponse(0, $data);
    }
}

index();
