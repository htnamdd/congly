<?php
require_once 'application/user/includes/comment.php';
require_once UTILS_PATH . 'captchar.php';
ini_set('display_errors', 1);
$action = SystemIO::post('action', 'def', '');
switch ($action) {
    case 'send_comment':
        send_comment();
        break;
    case 'show_captchar':
        show_captchar();
        break;
    case 'send_comment_reply':
        send_comment_reply();
        break;
    case 'approve_comment':
        approve_comment();
        break;
    case 'delete_comment':
        delete_comment();
        break;
    case 'approve_adv':
        approve_adv();
        break;

    case 'delete_adv':
        delete_adv();
        break;
    case 'like_comment':
        like_comment();
        break;
}
function like_comment(){
    $comment = new Comment();
    $id = SystemIO::post('id','int');
    $com = $comment->readData($id);
    if($com){
        $comment->updateLike($id);
    }
    echo $com['number_like'] + 1;

}
function send_comment()
{
    $full_name = SystemIO::post('name', 'def', '');
    $comment = SystemIO::post('comment', 'def', '');
    $news_id = SystemIO::post('news_id', 'int', '');
    $data = array('full_name' => $full_name, 'email' => $full_name, 'content' => $comment, 'time_post' => time(), 'nw_id' => $news_id, 'ip_address' => isset($_SERVER['HTTP_X_REAL_IP']) ? $_SERVER['HTTP_X_REAL_IP'] : $_SERVER['REMOTE_ADDR'], 'status' => 0);
    $cm = new Comment();
    $insert = $cm->insertData($data);
    if ($insert)
        echo 1;
    else
        echo 0;
}
function send_comment_reply()
{
    $full_name = SystemIO::post('name', 'def', '');
    $comment = SystemIO::post('comment', 'def', '');
    $news_id = SystemIO::post('news_id', 'int', '');
    $parent_id = SystemIO::post('cm_id','int',0);
    $data = array('full_name' => $full_name, 'email' => $full_name, 'content' => $comment,'parent_id'=>$parent_id, 'time_post' => time(), 'nw_id' => $news_id, 'ip_address' => isset($_SERVER['HTTP_X_REAL_IP']) ? $_SERVER['HTTP_X_REAL_IP'] : $_SERVER['REMOTE_ADDR'], 'status' => 0);
    $cm = new Comment();
    $insert = $cm->insertData($data);
    if ($insert)
        echo 1;
    else
        echo 0;
}

function show_captchar()
{
    $captcha = new Captcha(4);
    $src = $captcha->getCaptcha(false, true);
    echo $src;
}

function approve_comment()
{
    $id = SystemIO::post('id', 'int', '');
    $status = SystemIO::post('status', 'int', '');
    $user_name = UserCurrent::$current->data['user_name'];#var_dump($user_name);
    $update = '';
    if ($status == 0)
        $update = news()->update('comment', array('status' => '1', 'user_name' => $user_name), "id = " . $id);
    else
        $update = news()->update('comment', array('status' => 0, 'user_name' => $user_name), "id = " . $id);
    if ($update)
        echo 1;
    else
        echo 0;
}

function delete_comment()
{
    $id = SystemIO::post('id', 'int', '');
    if ($id)
        $dete = news()->delete('comment', "id = " . $id);
    else
        echo 0;
    if ($dete) echo 1;
    else echo 0;
}

function approve_adv()
{
    $id = SystemIO::post('id', 'int', '');
    $status = SystemIO::post('status', 'int', '');
    $user_name = UserCurrent::$current->data['user_name'];#var_dump($user_name);
    $update = '';
    if ($status == 0)
        $update = news()->update('banner', array('status' => '1', 'user_name' => $user_name), "id = " . $id);
    else
        $update = news()->update('banner', array('status' => 0, 'user_name' => $user_name), "id = " . $id);
    if ($update)
        echo 1;
    else
        echo 0;
}

function delete_adv()
{
    $id = SystemIO::post('id', 'int', '');
    if ($id)
        $dete = news()->delete('banner', "id = " . $id);
    else
        echo 0;
    if ($dete) echo 1;
    else echo 0;
}

?>