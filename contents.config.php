<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

if (!defined('G5_USE_CONTENTS') || !G5_USE_CONTENTS) return;

//------------------------------------------------------------------------------
// 컨텐츠몰 상수 모음 시작
//------------------------------------------------------------------------------

define('G5_CONTENTS_DIR', 'contents');

define('G5_CONTENTS_PATH',  G5_PATH.'/'.G5_CONTENTS_DIR);
define('G5_CONTENTS_URL',   G5_URL.'/'.G5_CONTENTS_DIR);
define('G5_MCONTENTS_PATH', G5_MOBILE_PATH.'/'.G5_CONTENTS_DIR);
define('G5_MCONTENTS_URL',  G5_MOBILE_URL.'/'.G5_CONTENTS_DIR);

// 보안서버주소 설정
if (G5_HTTPS_DOMAIN) {
    define('G5_HTTPS_CONTENTS_URL',  G5_HTTPS_DOMAIN.'/'.G5_CONTENTS_DIR);
    define('G5_HTTPS_MCONTENTS_URL', G5_HTTPS_DOMAIN.'/'.G5_MOBILE_DIR.'/'.G5_CONTENTS_DIR);
} else {
    define('G5_HTTPS_CONTENTS_URL',  G5_CONTENTS_URL);
    define('G5_HTTPS_MCONTENTS_URL', G5_MCONTENTS_URL);
}

// 컨테츠 파일 저장 DIR
define('G5_CONTENTS_SAVE_DIR',    'contents');

// 상품등록시 기본 옵션 개수
define('G5_CONTENTS_OPTION_COUNT', 3);

// 컨텐츠허브 연동 URL
define('G5_CONTENTS_HUB_URL', 'http://sir.co.kr/chub/contents.php');

// 컨텐츠허브 분류
$sir_chub_category = array('10'=>'테마', '20'=>'빌더', '30'=>'스킨', '40'=>'플러그인', '50'=>'디자인소스', '60'=>'솔루션');

// 컨텐츠허브 연동 후 결과코드 출력
define('G5_CONTENTS_HUB_CODE_DISPLAY', false);

//------------------------------------------------------------------------------
// 컨텐츠몰 상수 모음 끝
//------------------------------------------------------------------------------


//==============================================================================
// 컨텐츠몰 필수 실행코드 모음 시작
//==============================================================================

// 컨텐츠몰 설정값 배열변수
$setting = sql_fetch(" select * from {$g5['g5_contents_default_table']} ");

if(!defined('_THEME_PREVIEW_')) {
    // 테마 경로 설정
    if(defined('G5_THEME_PATH')) {
        define('G5_THEME_CONTENTS_PATH',   G5_THEME_PATH.'/'.G5_CONTENTS_DIR);
        define('G5_THEME_CONTENTS_URL',    G5_THEME_URL.'/'.G5_CONTENTS_DIR);
        define('G5_THEME_MCONTENTS_PATH',  G5_THEME_PATH.'/'.G5_MOBILE_DIR.'/'.G5_CONTENTS_DIR);
        define('G5_THEME_MCONTENTS_URL',   G5_THEME_URL.'/'.G5_MOBILE_DIR.'/'.G5_CONTENTS_DIR);
    }

    // 스킨 경로 설정
    if(preg_match('#^theme/(.+)$#', $setting['de_contents_skin'], $match)) {
        define('G5_CONTENTS_SKIN_PATH',  G5_THEME_PATH.'/'.G5_SKIN_DIR.'/contents/'.$match[1]);
        define('G5_CONTENTS_SKIN_URL',   G5_THEME_URL .'/'.G5_SKIN_DIR.'/contents/'.$match[1]);
    } else {
        define('G5_CONTENTS_SKIN_PATH',  G5_PATH.'/'.G5_SKIN_DIR.'/contents/'.$setting['de_contents_skin']);
        define('G5_CONTENTS_SKIN_URL',   G5_URL .'/'.G5_SKIN_DIR.'/contents/'.$setting['de_contents_skin']);
    }

    if(preg_match('#^theme/(.+)$#', $setting['de_contents_mobile_skin'], $match)) {
        define('G5_MCONTENTS_SKIN_PATH', G5_THEME_MOBILE_PATH.'/'.G5_SKIN_DIR.'/contents/'.$match[1]);
        define('G5_MCONTENTS_SKIN_URL',  G5_THEME_URL .'/'.G5_MOBILE_DIR.'/'.G5_SKIN_DIR.'/contents/'.$match[1]);
    } else {
        define('G5_MCONTENTS_SKIN_PATH', G5_MOBILE_PATH.'/'.G5_SKIN_DIR.'/contents/'.$setting['de_contents_mobile_skin']);
        define('G5_MCONTENTS_SKIN_URL',  G5_MOBILE_URL .'/'.G5_SKIN_DIR.'/contents/'.$setting['de_contents_mobile_skin']);
    }
}

define('G5_MEDIAELEMENT_PATH',   G5_PLUGIN_PATH.'/mediaelement');
define('G5_MEDIAELEMENT_URL',    G5_PLUGIN_URL.'/mediaelement');

//==============================================================================
// 컨텐츠몰 필수 실행코드 모음 끝
//==============================================================================
?>