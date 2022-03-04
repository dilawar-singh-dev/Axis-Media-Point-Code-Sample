<?php

function JsonResponse($status_code,$message,$data){
    $json_response = [
        'status_code' => $status_code,
        'status_message' => $message,
        'data' => $data
    ];
    return response()->json($json_response, 200, [], JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
}
function JsonResponsePaginate($status_code,$message,$data){
    $json_response = [
        'status_code' => $status_code,
        'status_message' => $message
    ];
    $data_array = $data->toArray();
    $array_merge = array_merge($json_response,$data_array);
    
    return response()->json($array_merge, 200, [], JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
}
