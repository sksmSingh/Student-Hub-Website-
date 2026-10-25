<?php
// Run from the project root: php scripts/create_admin.php admin@example.com Admin@12345 "Portal Administrator"
require_once __DIR__ . '/../config/database.php';
if (PHP_SAPI !== 'cli') { http_response_code(403); exit("Run this script from the command line only.\n"); }
$email=$argv[1]??''; $password=$argv[2]??''; $name=$argv[3]??'Portal Administrator';
if(!filter_var($email,FILTER_VALIDATE_EMAIL)||strlen($password)<12){fwrite(STDERR,"Usage: php scripts/create_admin.php admin@example.com StrongPassword12! \"Portal Administrator\"\nPassword must be at least 12 characters.\n");exit(1);}
$username='admin';$mobile='9999999999';$hash=password_hash($password,PASSWORD_DEFAULT);
try{$conn=db();$stmt=$conn->prepare("INSERT INTO users(full_name,username,email,mobile,password_hash,role) VALUES(?,?,?,?,?,'admin') ON DUPLICATE KEY UPDATE full_name=VALUES(full_name),password_hash=VALUES(password_hash),role='admin',is_active=1");$stmt->bind_param('sssss',$name,$username,$email,$mobile,$hash);$stmt->execute();echo "Admin account ready. Email: {$email}; username: admin\nKeep this password private and change it after your demonstration.\n";}catch(Throwable $e){fwrite(STDERR,"Could not create admin. Import database/studenthub.sql first. Details: ".$e->getMessage()."\n");exit(1);}
