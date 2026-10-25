<?php
require_once __DIR__ . '/../includes/bootstrap.php'; header('Content-Type: application/json; charset=utf-8'); header('X-Content-Type-Options: nosniff');
$type=(string)($_GET['type']??'');$allowed=['students','faqs','notices'];if(!in_array($type,$allowed,true)){http_response_code(400);echo json_encode(['success'=>false,'message'=>'Unknown data type.']);exit;}$file=__DIR__.'/../data/'.$type.'.json';$data=json_decode((string)file_get_contents($file),true);echo json_encode(['success'=>true,'data'=>is_array($data)?$data:[]],JSON_UNESCAPED_UNICODE);
