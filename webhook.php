<?php
if(!file_exists('inc/config.inc.php')) die('Konfigurationsdatei nicht gefunden!');
include_once('inc/config.inc.php');
include_once('inc/functions.php');
define('DS', DIRECTORY_SEPARATOR);
define('ROOT', dirname(__FILE__));

$data = json_decode(file_get_contents('php://input'),true);

$action = $data['action'];
$issue = $data['issue']['number'];
$issueurl='http://'.GIT_DOM.'/'.GIT_USER.'/'.GIT_REPO.'/issues/'.$issue;
$noreply = 'noreply@'.substr(EMAIL_ALTERNATIVE,strpos(EMAIL_ALTERNATIVE,'@')+1);

switch($action)
{
    case 'created': //comment
        $username = $data['comment']['user']['username'];
        $comment = $data['comment']['body'];
        $etext = "Ticket $issue - $issueurl
$username hat folgendes kommentiert:

$comment";
        $lastmail = sendMail(EMAIL_TO,'[TICKET] '.$issue,$noreply,$etext);
    break;

    case 'reopened':
        $etext = "Ticket $issue - $issueurl
Ticket wurde wieder geöffnet";
        $lastmail = sendMail(EMAIL_TO,'[TICKET] '.$issue,$noreply,$etext);
    break;

    case 'closed':
        $etext = "Ticket $issue - $issueurl
Ticket wurde geschlossen";
        $lastmail = sendMail(EMAIL_TO,'[TICKET] '.$issue,$noreply,$etext);
    break;

    default:
        file_put_contents('tmp/lastfail.txt',file_get_contents('php://input'));
        exit('');
}


file_put_contents('tmp/lastmail.log',$lastmail);

file_put_contents('tmp/lasthook.txt',file_get_contents('php://input'));


//[TICKET] '.$id