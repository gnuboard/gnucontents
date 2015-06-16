<?php
include_once('./_common.php');

if(!$config['cf_sms_use'])
    return;

$action_url = G5_CONTENTS_URL.'/smsinquiryupdate.php';
include_once(G5_CONTENTS_SKIN_PATH.'/smsinquiry.skin.php'); // SMS문의
?>