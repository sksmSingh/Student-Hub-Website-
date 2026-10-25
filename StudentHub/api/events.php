<?php
require_once __DIR__ . '/../includes/bootstrap.php';
header('Content-Type: application/json; charset=utf-8'); header('X-Content-Type-Options: nosniff');
function api_response(int $status,array $data): never { http_response_code($status); echo json_encode($data,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES); exit; }
function api_body(): array { $raw=file_get_contents('php://input'); $data=json_decode($raw,true); return is_array($data)?$data:$_POST; }
function require_api_admin(): void { if (!is_logged_in()) api_response(401,['success'=>false,'message'=>'Sign in required.']); if (!is_admin()) api_response(403,['success'=>false,'message'=>'Administrator access required.']); }
$method=$_SERVER['REQUEST_METHOD'];
try {
 $conn=db();
 if($method==='GET') {
   $q=trim((string)($_GET['q']??'')); $category=trim((string)($_GET['category']??''));
   $sql='SELECT id,title,description,category,event_date,poster_path,created_at FROM events WHERE (title LIKE ? OR description LIKE ?)'; $like='%'.$q.'%'; $types='ss'; $params=[$like,$like]; if($category!==''&&$category!=='all'){$sql.=' AND category=?';$types.='s';$params[]=$category;} $sql.=' ORDER BY event_date ASC'; $stmt=$conn->prepare($sql); $stmt->bind_param($types,...$params); $stmt->execute(); $rows=$stmt->get_result()->fetch_all(MYSQLI_ASSOC);
   // If the DB is empty during first setup, provide the external JSON seed for the public event list.
   if(!$rows && (int)$conn->query('SELECT COUNT(*) AS n FROM events')->fetch_assoc()['n']===0 && $q==='' && ($category===''||$category==='all')) { $seed=json_decode((string)file_get_contents(__DIR__.'/../data/events.json'),true); if(is_array($seed)) $rows=array_map(fn($e)=>['id'=>$e['id'],'title'=>$e['title'],'description'=>$e['description'],'category'=>$e['type'],'event_date'=>$e['date'],'poster_path'=>null,'created_at'=>null],$seed); }
   api_response(200,['success'=>true,'data'=>$rows]);
 }
 require_api_admin();
 if($method==='POST') {
   if(!hash_equals(csrf_token(),(string)($_SERVER['HTTP_X_CSRF_TOKEN']??''))) api_response(419,['success'=>false,'message'=>'Security token invalid. Refresh and try again.']);
   $isMultipart=str_contains($_SERVER['CONTENT_TYPE']??'','multipart/form-data'); $data=$isMultipart?$_POST:api_body();
   $id=(int)($data['id']??0); $title=trim((string)($data['title']??'')); $description=trim((string)($data['description']??'')); $category=trim((string)($data['category']??'')); $date=trim((string)($data['event_date']??''));
   if(strlen($title)<3||strlen($title)>150) api_response(422,['success'=>false,'message'=>'Title must be 3–150 characters.']); if(strlen($description)<10||strlen($description)>2000) api_response(422,['success'=>false,'message'=>'Description must be 10–2000 characters.']); if(!in_array($category,['Technical','Career','Cultural'],true)) api_response(422,['success'=>false,'message'=>'Choose a valid category.']); $dateObj=DateTime::createFromFormat('Y-m-d',$date); if(!$dateObj||$dateObj->format('Y-m-d')!==$date) api_response(422,['success'=>false,'message'=>'Choose a valid event date.']);
   $posterPath=null;
   if(isset($_FILES['poster'])&&$_FILES['poster']['error']!==UPLOAD_ERR_NO_FILE) {
     $file=$_FILES['poster']; if($file['error']!==UPLOAD_ERR_OK) api_response(422,['success'=>false,'message'=>'Poster upload failed.']); if($file['size']>MAX_POSTER_BYTES) api_response(422,['success'=>false,'message'=>'Poster must be 2 MB or smaller.']);
     $finfo=new finfo(FILEINFO_MIME_TYPE); $mime=$finfo->file($file['tmp_name']); $extensions=['image/jpeg'=>'jpg','image/png'=>'png','image/webp'=>'webp']; if(!isset($extensions[$mime])||!in_array($mime,ALLOWED_POSTER_MIMES,true)||@getimagesize($file['tmp_name'])===false) api_response(422,['success'=>false,'message'=>'Only valid JPG, PNG or WebP image files are allowed.']);
     $filename=bin2hex(random_bytes(18)).'.'.$extensions[$mime]; $uploadDir=__DIR__.'/../uploads/event-posters'; if(!is_dir($uploadDir)&&!mkdir($uploadDir,0750,true)) api_response(500,['success'=>false,'message'=>'Upload folder could not be created.']); if(!move_uploaded_file($file['tmp_name'],$uploadDir.'/'.$filename)) api_response(500,['success'=>false,'message'=>'Poster could not be saved.']); $posterPath='uploads/event-posters/'.$filename;
   }
   if($id>0) { if($posterPath!==null){$stmt=$conn->prepare('SELECT poster_path FROM events WHERE id=?');$stmt->bind_param('i',$id);$stmt->execute();$old=$stmt->get_result()->fetch_assoc();$stmt=$conn->prepare('UPDATE events SET title=?,description=?,category=?,event_date=?,poster_path=? WHERE id=?');$stmt->bind_param('sssssi',$title,$description,$category,$date,$posterPath,$id);$stmt->execute();if($old&&$old['poster_path']&&str_starts_with($old['poster_path'],'uploads/event-posters/')){@unlink(__DIR__.'/../'.$old['poster_path']);}}else{$stmt=$conn->prepare('UPDATE events SET title=?,description=?,category=?,event_date=? WHERE id=?');$stmt->bind_param('ssssi',$title,$description,$category,$date,$id);$stmt->execute();} api_response(200,['success'=>true,'message'=>'Event updated.']); }
   $stmt=$conn->prepare('INSERT INTO events (title,description,category,event_date,poster_path,created_by) VALUES (?,?,?,?,?,?)'); $userId=(int)current_user()['id']; $stmt->bind_param('sssssi',$title,$description,$category,$date,$posterPath,$userId); $stmt->execute(); api_response(201,['success'=>true,'message'=>'Event created.','id'=>$conn->insert_id]);
 }
 if($method==='PATCH') { if(!hash_equals(csrf_token(),(string)($_SERVER['HTTP_X_CSRF_TOKEN']??''))) api_response(419,['success'=>false,'message'=>'Security token invalid.']); $data=api_body(); $id=(int)($data['id']??0); if(!$id) api_response(422,['success'=>false,'message'=>'Event ID required.']); $find=$conn->prepare('SELECT poster_path FROM events WHERE id=?');$find->bind_param('i',$id);$find->execute();$existing=$find->get_result()->fetch_assoc();if(!$existing)api_response(404,['success'=>false,'message'=>'Event not found.']);$stmt=$conn->prepare('DELETE FROM events WHERE id=?');$stmt->bind_param('i',$id);$stmt->execute();if($existing['poster_path']&&str_starts_with($existing['poster_path'],'uploads/event-posters/')){@unlink(__DIR__.'/../'.$existing['poster_path']);}api_response(200,['success'=>true,'message'=>'Event deleted.']); }
 api_response(405,['success'=>false,'message'=>'Method not allowed.']);
} catch(Throwable $ex) { error_log('Events API: '.$ex->getMessage()); api_response(500,['success'=>false,'message'=>'The request could not be completed. Check database setup and try again.']); }
