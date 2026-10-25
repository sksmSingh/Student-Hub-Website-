<?php
require_once __DIR__ . '/../includes/bootstrap.php'; require_student(); if ($_SERVER['REQUEST_METHOD']!=='POST') redirect('event.php'); verify_csrf();
$eventId=filter_input(INPUT_POST,'event_id',FILTER_VALIDATE_INT); $user=current_user();
try { if (!$eventId) throw new RuntimeException('Choose a valid event.'); $conn=db(); $stmt=$conn->prepare('SELECT id FROM students WHERE user_id=?'); $stmt->bind_param('i',$user['id']); $stmt->execute(); $student=$stmt->get_result()->fetch_assoc(); if(!$student) throw new RuntimeException('Student profile not found.');
  $event=$conn->prepare('SELECT id FROM events WHERE id=?'); $event->bind_param('i',$eventId); $event->execute(); if(!$event->get_result()->fetch_assoc()) throw new RuntimeException('That event is no longer available.');
  $ins=$conn->prepare('INSERT INTO registrations (student_id,event_id) VALUES (?,?)'); $ins->bind_param('ii',$student['id'],$eventId); $ins->execute(); flash('success','You are registered for the selected event.');
} catch(mysqli_sql_exception $ex) { if ((int)$ex->getCode()===1062) flash('warning','You have already registered for that event.'); else { error_log('Event registration: '.$ex->getMessage()); flash('error','Could not register for the event. Please try again.'); } } catch(Throwable $ex) { flash('error',$ex->getMessage()); }
redirect('event.php');
