<?php
//==============================================================================
// 컨텐츠몰 라이브러리 모음 시작
//==============================================================================

/*
간편 사용법 : 상품유형을 1~5 사이로 지정합니다.
$disp = new item_list(1);
echo $disp->run();


유형+분류별로 노출하는 경우 상세 사용법 : 상품유형을 지정하는 것은 동일합니다.
$disp = new item_list(1);
// 사용할 스킨을 바꿉니다.
$disp->set_list_skin("type_user.skin.php");
// 1단계분류를 20으로 시작되는 분류로 지정합니다.
$disp->set_category("20", 1);
echo $disp->run();


분류별로 노출하는 경우 상세 사용법
// type13.skin.php 스킨으로 3개씩 2줄을 폭 150 사이즈로 분류코드 30 으로 시작되는 상품을 노출합니다.
$disp = new item_list(0, "type13.skin.php", 3, 2, 150, 0, "30");
echo $disp->run();


이벤트로 노출하는 경우 상세 사용법
// type13.skin.php 스킨으로 3개씩 2줄을 폭 150 사이즈로 상품을 노출합니다.
$disp = new item_list(0, "type13.skin.php", 3, 2, 150, 0);
// 이벤토번호를 설정합니다.
$disp->set_event("12345678");
echo $disp->run();

참고) 영카트4의 display_type 함수와 사용방법이 비슷한 class 입니다.
      display_category 나 display_event 로 사용하기 위해서는 $type 값만 넘기지 않으면 됩니다.
*/

class cm_item_list
{
    // 상품유형 : 기본적으로 1~5 까지 사용할수 있으며 0 으로 설정하는 경우 상품유형별로 노출하지 않습니다.
    // 분류나 이벤트로 노출하는 경우 상품유형을 0 으로 설정하면 됩니다.
    protected $type;

    protected $list_skin;
    protected $list_mod;
    protected $list_row;
    protected $img_width;
    protected $img_height;

    // 상품상세보기 경로
    protected $href = "";

    // select 에 사용되는 필드
    protected $fields = "*";

    // 분류코드로만 사용하는 경우 상품유형($type)을 0 으로 설정하면 됩니다.
    protected $ca_id = "";
    protected $ca_id2 = "";
    protected $ca_id3 = "";

    // 노출순서
    protected $order_by = "it_order, it_id desc";

    // 상품의 이벤트번호를 저장합니다.
    protected $event = "";

    // 스킨의 기본 css 를 다른것으로 사용하고자 할 경우에 사용합니다.
    protected $css = "";

    // 상품의 사용여부를 따져 노출합니다. 0 인 경우 모든 상품을 노출합니다.
    protected $use = 1;

    // 모바일에서 노출하고자 할 경우에 true 로 설정합니다.
    protected $is_mobile = false;

    // 기본으로 보여지는 필드들
    protected $view_it_id    = false;       // 상품코드
    protected $view_it_img   = true;        // 상품이미지
    protected $view_it_name  = true;        // 상품명
    protected $view_it_basic = true;        // 기본설명
    protected $view_it_price = true;        // 판매가격
    protected $view_it_icon = false;        // 아이콘
    protected $view_sns = false;            // SNS

    // 몇번째 class 호출인지를 저장합니다.
    protected $count = 0;

    // true 인 경우 페이지를 구한다.
    protected $is_page = false;

    // 페이지 표시를 위하여 총 상품수를 구합니다.
    public $total_count = 0;

    // sql limit 의 시작 레코드
    protected $from_record = 0;

    // 외부에서 쿼리문을 넘겨줄 경우에 담아두는 변수
    protected $query = "";


    // $type        : 상품유형 (기본으로 1~5까지 사용)
    // $list_skin   : 상품리스트를 노출할 스킨을 설정합니다. 스킨위치는 skin/contents/컨텐츠몰설정스킨/type??.skin.php
    // $list_mod    : 1줄에 몇개의 상품을 노출할지를 설정합니다.
    // $list_row    : 상품을 몇줄에 노출할지를 설정합니다.
    // $img_width   : 상품이미지의 폭을 설정합니다.
    // $img_height  : 상품이미지의 높이을 설정합니다. 0 으로 설정하는 경우 썸네일 이미지의 높이는 폭에 비례하여 생성합니다.
    //function __construct($type=0, $list_skin='', $list_mod='', $list_row='', $img_width='', $img_height=0, $ca_id='') {
    function __construct($list_skin='', $list_mod='', $list_row='', $img_width='', $img_height=0) {
        $this->list_skin  = $list_skin;
        $this->list_mod   = $list_mod;
        $this->list_row   = $list_row;
        $this->img_width  = $img_width;
        $this->img_height = $img_height;
        $this->set_href(G5_CONTENTS_URL.'/item.php?it_id=');
        $this->count++;
    }

    function set_type($type) {
        $this->type = $type;
        if ($type) {
            $this->set_list_skin($this->list_skin);
            $this->set_list_mod($this->list_mod);
            $this->set_list_row($this->list_row);
            $this->set_img_size($this->img_width, $this->img_height);
        }
    }

    // 분류코드로 검색을 하고자 하는 경우 아래와 같이 인수를 넘겨줍니다.
    // 1단계 분류는 (분류코드, 1)
    // 2단계 분류는 (분류코드, 2)
    // 3단계 분류는 (분류코드, 3)
    function set_category($ca_id, $level=1) {
        if ($level == 2) {
            $this->ca_id2 = $ca_id;
        } else if ($level == 3) {
            $this->ca_id3 = $ca_id;
        } else {
            $this->ca_id = $ca_id;
        }
    }

    // 이벤트코드를 인수로 넘기게 되면 해당 이벤트에 속한 상품을 노출합니다.
    function set_event($ev_id) {
        $this->event = $ev_id;
    }

    // 리스트 스킨을 바꾸고자 하는 경우에 사용합니다.
    // 리스트 스킨의 위치는 skin/contents/컨텐츠몰설정스킨/type??.skin.php 입니다.
    // 특별히 설정하지 않는 경우 상품유형을 사용하는 경우는 컨텐츠몰설정 값을 그대로 따릅니다.
    function set_list_skin($list_skin) {
        global $setting;
        if ($this->is_mobile) {
            $this->list_skin = $list_skin ? $list_skin : G5_MCONTENTS_SKIN_PATH.'/'.$setting['de_mobile_type'.$this->type.'_list_skin'];
        } else {
            $this->list_skin = $list_skin ? $list_skin : G5_CONTENTS_SKIN_PATH.'/'.$setting['de_type'.$this->type.'_list_skin'];
        }
    }

    // 1줄에 몇개를 노출할지를 사용한다.
    // 특별히 설정하지 않는 경우 상품유형을 사용하는 경우는 컨텐츠몰설정 값을 그대로 따릅니다.
    function set_list_mod($list_mod) {
        global $setting;
        if ($this->is_mobile) {
            $this->list_mod = $list_mod ? $list_mod : $setting['de_mobile_type'.$this->type.'_list_mod'];
        } else {
            $this->list_mod = $list_mod ? $list_mod : $setting['de_type'.$this->type.'_list_mod'];
        }
    }

    // 몇줄을 노출할지를 사용한다.
    // 특별히 설정하지 않는 경우 상품유형을 사용하는 경우는 컨텐츠몰설정 값을 그대로 따릅니다.
    function set_list_row($list_row) {
        global $setting;
        if ($this->is_mobile) {
            $this->list_row = $list_row ? $list_row : $setting['de_mobile_type'.$this->type.'_list_row'];
        } else {
            $this->list_row = $list_row ? $list_row : $setting['de_type'.$this->type.'_list_row'];
        }
        if (!$this->list_row)
            $this->list_row = 1;
    }

    // 노출이미지(썸네일생성)의 폭, 높이를 설정합니다. 높이를 0 으로 설정하는 경우 쎰네일 비율에 따릅니다.
    // 특별히 설정하지 않는 경우 상품유형을 사용하는 경우는 컨텐츠몰설정 값을 그대로 따릅니다.
    function set_img_size($img_width, $img_height=0) {
        global $setting;
        if ($this->is_mobile) {
            $this->img_width = $img_width ? $img_width : $setting['de_mobile_type'.$this->type.'_img_width'];
            $this->img_height = $img_height ? $img_height : $setting['de_mobile_type'.$this->type.'_img_height'];
        } else {
            $this->img_width = $img_width ? $img_width : $setting['de_type'.$this->type.'_img_width'];
            $this->img_height = $img_height ? $img_height : $setting['de_type'.$this->type.'_img_height'];
        }
    }

    // 특정 필드만 select 하는 경우에는 필드명을 , 로 구분하여 "field1, field2, field3, ... fieldn" 으로 인수를 넘겨줍니다.
    function set_fields($str) {
        $this->fields = $str;
    }

    // 특정 필드로 정렬을 하는 경우 필드와 정렬순서를 , 로 구분하여 "field1 desc, field2 asc, ... fieldn desc " 으로 인수를 넘겨줍니다.
    function set_order_by($str) {
        $this->order_by = $str;
    }

    // 사용하는 상품외에 모든 상품을 노출하려면 0 을 인수로 넘겨줍니다.
    function set_use($use) {
        $this->use = $use;
    }

    // 모바일로 사용하려는 경우 true 를 인수로 넘겨줍니다.
    function set_mobile($mobile=true) {
        $this->is_mobile = $mobile;
    }

    // 스킨에서 특정 필드를 노출하거나 하지 않게 할수 있습니다.
    // 가령 소비자가는 처음에 노출되지 않도록 설정되어 있지만 노출을 하려면
    // ("it_price", true) 와 같이 인수를 넘겨줍니다.
    // 이때 인수로 넘겨주는 값은 스킨에 정의된 필드만 가능하다는 것입니다.
    function set_view($field, $view=true) {
        $this->{"view_".$field} = $view;
    }

    // anchor 태그에 하이퍼링크를 다른 주소로 걸거나 아예 링크를 걸지 않을 수 있습니다.
    // 인수를 "" 공백으로 넘기면 링크를 걸지 않습니다.
    function set_href($href) {
        $this->href = $href;
    }

    // ul 태그의 css 를 교체할수 있다. "sct sct_abc" 를 인수로 넘기게 되면
    // 기존의 ul 태그에 걸린 css 는 무시되며 인수로 넘긴 css 가 사용됩니다.
    function set_css($css) {
        $this->css = $css;
    }

    // 페이지를 노출하기 위해 true 로 설정할때 사용합니다.
    function set_is_page($is_page) {
        $this->is_page = $is_page;
    }

    // select ... limit 의 시작값
    function set_from_record($from_record) {
        $this->from_record = $from_record;
    }

    // 외부에서 쿼리문을 넘겨줄 경우에 담아둡니다.
    function set_query($query) {
        $this->query = $query;
    }

    // class 에 설정된 값으로 최종 실행합니다.
    function run() {

        global $g5, $config, $member, $setting;

        if ($this->query) {

            $sql = $this->query;
            $result = sql_query($sql);
            $this->total_count = @mysql_num_rows($result);

        } else {

            $where = array();
            if ($this->use) {
                $where[] = " it_use = '1' ";
            }

            if ($this->type) {
                $where[] = " it_type{$this->type} = '1' ";
            }

            if ($this->ca_id || $this->ca_id2 || $this->ca_id3) {
                $where_ca_id = array();
                if ($this->ca_id) {
                    $where_ca_id[] = " ca_id like '{$this->ca_id}%' ";
                }
                if ($this->ca_id2) {
                    $where_ca_id[] = " ca_id2 like '{$this->ca_id2}%' ";
                }
                if ($this->ca_id3) {
                    $where_ca_id[] = " ca_id3 like '{$this->ca_id3}%' ";
                }
                $where[] = " ( " . implode(" or ", $where_ca_id) . " ) ";
            }

            if ($this->order_by) {
                $sql_order = " order by {$this->order_by} ";
            }

            if ($this->event) {
                $sql_select = " select {$this->fields} ";
                $sql_common = " from `{$g5['g5_contents_event_item_table']}` a left join `{$g5['g5_contents_item_table']}` b on (a.it_id = b.it_id) ";
                $where[] = " a.ev_id = '{$this->event}' ";
            } else {
                $sql_select = " select {$this->fields} ";
                $sql_common = " from `{$g5['g5_contents_item_table']}` ";
            }
            $sql_where = " where " . implode(" and ", $where);
            $sql_limit = " limit " . $this->from_record . " , " . ($this->list_mod * $this->list_row);

            $sql = $sql_select . $sql_common . $sql_where . $sql_order . $sql_limit;
            $result = sql_query($sql);

            if ($this->is_page) {
                $sql2 = " select count(*) as cnt " . $sql_common . $sql_where;
                $row2 = sql_fetch($sql2);
                $this->total_count = $row2['cnt'];
            }

        }

        $file = $this->list_skin;

        if ($this->list_skin == "") {
            return $this->count."번 cm_item_list() 의 스킨파일이 지정되지 않았습니다.";
        } else if (!file_exists($file)) {
            return $file." 파일을 찾을 수 없습니다.";
        } else {
            ob_start();
            $list_mod = $this->list_mod;
            include($file);
            $content = ob_get_contents();
            ob_end_clean();
            return $content;
        }
    }
}


// 장바구니 건수 검사
function cm_get_cart_count($cart_id)
{
    global $g5, $setting;

    $sql = " select count(ct_id) as cnt from {$g5['g5_contents_cart_table']} where od_id = '$cart_id' ";
    if($contetns['de_cart_keep_term']) {
        $ctime = date('Y-m-d', G5_SERVER_TIME - ($setting['de_cart_keep_term'] * 86400));
        $sql .= " and substring(ct_time, 1, 10) >= '$ctime' ";
    }
    $row = sql_fetch($sql);
    $cnt = (int)$row['cnt'];
    return $cnt;
}


// 이미지를 얻는다
function cm_get_image($img, $width=0, $height=0, $img_id='')
{
    global $g5, $setting;

    $full_img = G5_DATA_PATH.'/cmitem/'.$img;

    if (file_exists($full_img) && $img)
    {
        if (!$width)
        {
            $size = getimagesize($full_img);
            $width = $size[0];
            $height = $size[1];
        }
        $str = '<img src="'.G5_DATA_URL.'/cmitem/'.$img.'" alt="" width="'.$width.'" height="'.$height.'"';

        if($img_id)
            $str .= ' id="'.$img_id.'"';

        $str .= '>';
    }
    else
    {
        $str = '<img src="'.G5_CONTENTS_URL.'/img/no_image.gif" alt="" ';
        if ($width)
            $str .= 'width="'.$width.'" height="'.$height.'"';
        else
            $str .= 'width="'.$setting['de_mimg_width'].'" height="'.$setting['de_mimg_height'].'"';

        if($img_id)
            $str .= ' id="'.$img_id.'"'.
        $str .= '>';
    }

    return $str;
}


// 상품 이미지를 얻는다
function cm_get_it_image($it_id, $width, $height=0, $anchor=false, $img_id='', $img_alt='')
{
    global $g5;

    if(!$it_id || !$width)
        return '';

    $sql = " select it_id, it_img1, it_img2, it_img3, it_img4, it_img5, it_img6, it_img7, it_img8, it_img9, it_img10 from {$g5['g5_contents_item_table']} where it_id = '$it_id' ";
    $row = sql_fetch($sql);

    if(!$row['it_id'])
        return '';

    for($i=1;$i<=10; $i++) {
        $file = G5_DATA_PATH.'/cmitem/'.$row['it_img'.$i];
        if(is_file($file) && $row['it_img'.$i]) {
            $size = @getimagesize($file);
            if($size[2] < 1 || $size[2] > 3)
                continue;

            $filename = basename($file);
            $filepath = dirname($file);
            $img_width = $size[0];
            $img_height = $size[1];

            break;
        }
    }

    if($img_width && !$height) {
        $height = round(($width * $img_height) / $img_width);
    }

    if($filename) {
        //thumbnail($filename, $source_path, $target_path, $thumb_width, $thumb_height, $is_create, $is_crop=false, $crop_mode='center', $is_sharpen=true, $um_value='80/0.5/3')
        $thumb = thumbnail($filename, $filepath, $filepath, $width, $height, false, true, 'center', true, $um_value='80/0.5/3');
    }

    if($thumb) {
        $file_url = str_replace(G5_PATH, G5_URL, $filepath.'/'.$thumb);
        $img = '<img src="'.$file_url.'" width="'.$width.'" height="'.$height.'" alt="'.$img_alt.'"';
    } else {
        $img = '<img src="'.G5_CONTENTS_URL.'/img/no_image.gif" width="'.$width.'"';
        if($height)
            $img .= ' height="'.$height.'"';
        $img .= ' alt="'.$img_alt.'"';
    }

    if($img_id)
        $img .= ' id="'.$img_id.'"';
    $img .= '>';

    if($anchor)
        $img = '<a href="'.G5_CONTENTS_URL.'/item.php?it_id='.$it_id.'">'.$img.'</a>';

    return $img;
}


// 상품이미지 썸네일 생성
function cm_get_it_thumbnail($img, $width, $height=0, $id='')
{
    $str = '';

    $file = G5_DATA_PATH.'/cmitem/'.$img;
    if(is_file($file))
        $size = @getimagesize($file);

    if($size[2] < 1 || $size[2] > 3)
        return '';

    $img_width = $size[0];
    $img_height = $size[1];
    $filename = basename($file);
    $filepath = dirname($file);

    if($img_width && !$height) {
        $height = round(($width * $img_height) / $img_width);
    }

    $thumb = thumbnail($filename, $filepath, $filepath, $width, $height, false, true, 'center', true, $um_value='80/0.5/3');

    if($thumb) {
        $file_url = str_replace(G5_PATH, G5_URL, $filepath.'/'.$thumb);
        $str = '<img src="'.$file_url.'" width="'.$width.'" height="'.$height.'"';
        if($id)
            $str .= ' id="'.$id.'"';
        $str .= ' alt="">';
    }

    return $str;
}


// 이미지 URL 을 얻는다.
function cm_get_it_imageurl($it_id)
{
    global $g5;

    $sql = " select it_img1, it_img2, it_img3, it_img4, it_img5, it_img6, it_img7, it_img8, it_img9, it_img10
                from {$g5['g5_contents_item_table']}
                where it_id = '$it_id' ";
    $row = sql_fetch($sql);
    $filepath = '';

    for($i=1; $i<=10; $i++) {
        $img = $row['it_img'.$i];
        $file = G5_DATA_PATH.'/cmitem/'.$img;
        if(!is_file($file))
            continue;

        $size = @getimagesize($file);
        if($size[2] < 1 || $size[2] > 3)
            continue;

        $filepath = $file;
    }

    if($filepath)
        $str = str_replace(G5_PATH, G5_URL, $filepath);
    else
        $str = G5_CONTENTS_URL.'/img/no_image.gif';

    return $str;
}


// 큰 이미지
function cm_get_large_image($img, $it_id, $btn_image=true)
{
    global $g5;

    if (file_exists(G5_DATA_PATH.'/cmitem/'.$img) && $img != '')
    {
        $size   = getimagesize(G5_DATA_PATH.'/cmitem/'.$img);
        $width  = $size[0];
        $height = $size[1];
        $str = '<a href="javascript:popup_large_image(\''.$it_id.'\', \''.$img.'\', '.$width.', '.$height.', \''.G5_CONTENTS_URL.'\')">';
        if ($btn_image)
            $str .= '큰이미지</a>';
    }
    else
        $str = '';
    return $str;
}


// 금액 표시
function cm_display_price($price, $tel_inq=false)
{
    if ($tel_inq)
        $price = '전화문의';
    else
        $price = number_format($price, 0).'원';

    return $price;
}


// 금액표시
// $it : 상품 배열
function cm_get_price($it)
{
    global $member;

    if ($it['it_tel_inq']) return '전화문의';

    $price = $it['it_price'];

    return (int)$price;
}


// 포인트 표시
function cm_display_point($point)
{
    return number_format($point, 0).'점';
}


// 포인트를 구한다
function cm_get_point($amount, $point)
{
    return (int)($amount * $point / 100);
}


// 상품이미지 업로드
function cm_it_img_upload($srcfile, $filename, $dir)
{
    if($filename == '')
        return '';

    $size = @getimagesize($srcfile);
    if($size[2] < 1 || $size[2] > 3)
        return '';

    if(!is_dir($dir)) {
        @mkdir($dir, G5_DIR_PERMISSION);
        @chmod($dir, G5_DIR_PERMISSION);
    }

    $pattern = "/[#\&\+\-%@=\/\\:;,'\"\^`~\|\!\?\*\$#<>\(\)\[\]\{\}]/";

    $filename = preg_replace("/\s+/", "", $filename);
    $filename = preg_replace($pattern, "", $filename);

    $filename = preg_replace_callback(
                          "/[가-힣]+/",
                          create_function('$matches', 'return base64_encode($matches[0]);'),
                          $filename);

    $filename = preg_replace($pattern, "", $filename);

    cm_upload_file($srcfile, $filename, $dir);

    $file = str_replace(G5_DATA_PATH.'/cmitem/', '', $dir.'/'.$filename);

    return $file;
}


// 파일을 업로드 함
function cm_upload_file($srcfile, $destfile, $dir)
{
    if ($destfile == "") return false;
    // 업로드 한후 , 퍼미션을 변경함
    @move_uploaded_file($srcfile, $dir.'/'.$destfile);
    @chmod($dir.'/'.$destfile, G5_FILE_PERMISSION);
    return true;
}


// 시간이 비어 있는지 검사
function cm_is_null_time($datetime)
{
    // 공란 0 : - 제거
    //$datetime = ereg_replace("[ 0:-]", "", $datetime); // 이 함수는 PHP 5.3.0 에서 배제되고 PHP 6.0 부터 사라집니다.
    $datetime = preg_replace("/[ 0:-]/", "", $datetime);
    if ($datetime == "")
        return true;
    else
        return false;
}


// 별
function cm_get_star($score)
{
    $star = round($score);
    if ($star > 5) $star = 5;
    else if ($star < 0) $star = 0;

    return $star;
}


// 별 이미지
function cm_get_star_image($it_id)
{
    global $g5;

    $sql = "select (SUM(is_score) / COUNT(*)) as score from {$g5['g5_contents_item_use_table']} where it_id = '$it_id' ";
    $row = sql_fetch($sql);

    return (int)cm_get_star($row['score']);
}


// 타임스탬프 형식으로 넘어와야 한다.
// 시작시간, 종료시간
function cm_gap_time($begin_time, $end_time)
{
    $gap = $end_time - $begin_time;
    $time['days']    = (int)($gap / 86400);
    $time['hours']   = (int)(($gap - ($time['days'] * 86400)) / 3600);
    $time['minutes'] = (int)(($gap - ($time['days'] * 86400 + $time['hours'] * 3600)) / 60);
    $time['seconds'] = (int)($gap - ($time['days'] * 86400 + $time['hours'] * 3600 + $time['minutes'] * 60));
    return $time;
}


// 공란없이 이어지는 문자 자르기 (wayboard 참고 (way.co.kr))
function cm_continue_cut_str($str, $len=80)
{
    /*
    $pattern = "[^ \n<>]{".$len."}";
    return eregi_replace($pattern, "\\0\n", $str);
    */
    $pattern = "/[^ \n<>]{".$len."}/";
    return preg_replace($pattern, "\\0\n", $str);
}


// 제목별로 컬럼 정렬하는 QUERY STRING
// $type 이 1이면 반대
function cm_title_sort($col, $type=0)
{
    global $sort1, $sort2;
    global $_SERVER;
    global $page;
    global $doc;

    $q1 = 'sort1='.$col;
    if ($type) {
        $q2 = 'sort2=desc';
        if ($sort1 == $col) {
            if ($sort2 == 'desc') {
                $q2 = 'sort2=asc';
            }
        }
    } else {
        $q2 = 'sort2=asc';
        if ($sort1 == $col) {
            if ($sort2 == 'asc') {
                $q2 = 'sort2=desc';
            }
        }
    }
    #return "$_SERVER[PHP_SELF]?$q1&amp;$q2&amp;page=$page";
    return "{$_SERVER['PHP_SELF']}?$q1&amp;$q2&amp;page=$page";
}


// 세션값을 체크하여 이쪽에서 온것이 아니면 메인으로
function cm_session_check()
{
    global $g5;

    if (!trim(get_session('ss_uniqid')))
        goto_url(G5_CONTENTS_URL);
}


// 상품 선택옵션
function cm_get_item_options($it)
{
    global $g5;

    $sql = " select * from {$g5['g5_contents_item_option_table']} where it_id = '{$it['it_id']}' and io_use = '1' order by io_no asc ";
    $result = sql_query($sql);
    if(!mysql_num_rows($result))
        return '';

    $str = '';
    $count = 0;

    for($i=0; $row=sql_fetch_array($result); $i++) {
        if(!trim($row['io_name']))
            continue;

        $io_name = get_text($row['io_name']);
        $io_price = cm_get_price($it) + $row['io_price'];

        $disabled = '';
        if(!$row['io_download'])
            $disabled = ' disabled="disabled"';

        $str .= '<tr>'.PHP_EOL;
        $str .= '<td>'.$io_name.'</td>'.PHP_EOL;
        $str .= '<td class="t_c"><label for="chk_it_'.$count.'" class="sound_only">'.$io_name.'선택</label><input type="checkbox" name="io_chk[]" value="'.$count.'" id="chk_it_'.$count.'"'.$disabled.'></td>'.PHP_EOL;
        $str .= '<td class="cit_qty">'.PHP_EOL;
        $str .= '<label for="it_option_'.$count.'" class="sound_only">'.$io_name.'수량</label>';
        $str .= '<input type="hidden" name="io_id['.$count.']" value="'.$row['io_id'].'">';
        $str .= '<input type="hidden" name="io_price['.$count.']" value="'.$io_price.'">';
        $str .= '<button type="button" class="change_qty">감소</button>';
        $str .= '<input type="text" name="ct_qty['.$count.']" value="1" size="5" class="it_qty" id="it_option_'.$count.'">';
        $str .= '<button type="button" class="change_qty change_qty1">증가</button>';
        $str .= '</td>'.PHP_EOL;
        $str .= '<td class="op_pr">'.cm_display_price($it['it_price'] + $row['io_price']).'</td>'.PHP_EOL;
;
        $str .= '</tr>'.PHP_EOL;

        $count++;
    }

    if($count > 0)
        return $str;
    else
        return '';
}

// 모바일 상품 선택옵션
function cm_get_mobile_item_options($it)
{
    global $g5;

    $sql = " select * from {$g5['g5_contents_item_option_table']} where it_id = '{$it['it_id']}' and io_use = '1' order by io_no asc ";
    $result = sql_query($sql);
    if(!mysql_num_rows($result))
        return '';

    $str = '';
    $count = 0;

    for($i=0; $row=sql_fetch_array($result); $i++) {
        if(!trim($row['io_name']))
            continue;

        $io_name = get_text($row['io_name']);
        $io_price = cm_get_price($it) + $row['io_price'];

        $disabled = '';
        if(!$row['io_download'])
            $disabled = ' disabled="disabled"';

        $str .= '<tr>'.PHP_EOL;
        $str .= '<td colspan="2" class="op_tit"><div >'.$io_name.'<label for="chk_it_'.$count.'" class="sound_only">선택</label><input type="checkbox" name="io_chk[]" value="'.$count.'" id="chk_it_'.$count.'"'.$disabled.'></div></td>'.PHP_EOL;
        $str .= '</tr>'.PHP_EOL;
        $str .= '<tr>'.PHP_EOL;
        $str .= '<td class="cit_qty">'.PHP_EOL;
        $str .= '<label for="it_option_'.$count.'" class="sound_only">'.$io_name.'수량</label>';
        $str .= '<input type="hidden" name="io_id['.$count.']" value="'.$row['io_id'].'">';
        $str .= '<input type="hidden" name="io_price['.$count.']" value="'.$io_price.'">';
        $str .= '<button type="button" class="change_qty">감소</button>';
        $str .= '<input type="text" name="ct_qty['.$count.']" value="1" size="5" class="it_qty" id="it_option_'.$count.'">';
        $str .= '<button type="button" class="change_qty change_qty1">증가</button>';
        $str .= '</td>'.PHP_EOL;
        $str .= '<td>'.cm_display_price($it['it_price'] + $row['io_price']).'</td>'.PHP_EOL;
;
        $str .= '</tr>'.PHP_EOL;

        $count++;
    }

    if($count > 0)
        return $str;
    else
        return '';
}


// 장바구니 상품 선택옵션
function cm_get_cart_options($it, $cart_id)
{
    global $g5;

    $sql = " select * from {$g5['g5_contents_item_option_table']} where it_id = '{$it['it_id']}' and io_use = '1' order by io_no asc ";
    $result = sql_query($sql);
    if(!mysql_num_rows($result))
        return '';

    // 장바구니 자료를 배열에 저장
    $sql2 = " select * from {$g5['g5_contents_cart_table']} where od_id = '$cart_id' and it_id = '{$it['it_id']}' ";
    $result2 = sql_query($sql2);
    if(!mysql_num_rows($result2))
        return '';

    $cart = array();
    for($i=0; $row2=sql_fetch_array($result2); $i++) {
        $cart[$row2['io_id']] = $row2;
    }

    $str = '';
    $count = 0;

    for($i=0; $row=sql_fetch_array($result); $i++) {
        if(!trim($row['io_name']))
            continue;

        $io_name = get_text($row['io_name']);
        $io_price = cm_get_price($it) + $row['io_price'];
        $ct_qty = 1;
        if($cart[$row['io_id']]['ct_qty'])
            $ct_qty = $cart[$row['io_id']]['ct_qty'];
        $io_chk = '';
        if($cart[$row['io_id']]['ct_id'])
            $io_chk = ' checked="checked"';
        $disabled = '';
        if(!$row['io_download'])
            $disabled = ' disabled="disabled"';

        $str .= '<tr>'.PHP_EOL;
        $str .= '<td class="t_l">'.$io_name.'</td>'.PHP_EOL;
        $str .= '<td>'.PHP_EOL;
        $str .= '<label for="chk_it_'.$count.'" class="sound_only">'.$io_name.'선택</label>';
        $str .= '<input type="checkbox" name="io_chk[]" value="'.$count.'" id="chk_it_'.$count.'"'.$io_chk.$disabled.'>';
        $str .= '</td>'.PHP_EOL;
        $str .= '<td>'.PHP_EOL;
        $str .= '<label for="it_option_'.$count.'" class="sound_only">'.$io_name.'수량</label>';
        $str .= '<input type="hidden" name="io_id['.$count.']" value="'.$row['io_id'].'">';
        $str .= '<input type="hidden" name="io_price['.$count.']" value="'.$io_price.'">';
        $str .= '<button type="button" class="change_qty">감소</button>';
        $str .= '<input type="text" name="ct_qty['.$count.']" value="'.$ct_qty.'" size="3" class="input_opt">';
        $str .= '<button type="button" class="change_qty change_qty1">증가</button>';

        $str .= '</td>'.PHP_EOL;
        $str .= '<td class="op_pr">'.cm_display_price($it['it_price'] + $row['io_price']).'</td>'.PHP_EOL;

        $str .= '</tr>'.PHP_EOL;

        $count++;
    }

    if($count > 0)
        return $str;
    else
        return '';
}

// 모바일 장바구니 상품 선택옵션
function cm_get_mobile_cart_options($it, $cart_id)
{
    global $g5;

    $sql = " select * from {$g5['g5_contents_item_option_table']} where it_id = '{$it['it_id']}' and io_use = '1' order by io_no asc ";
    $result = sql_query($sql);
    if(!mysql_num_rows($result))
        return '';

    // 장바구니 자료를 배열에 저장
    $sql2 = " select * from {$g5['g5_contents_cart_table']} where od_id = '$cart_id' and it_id = '{$it['it_id']}' ";
    $result2 = sql_query($sql2);
    if(!mysql_num_rows($result2))
        return '';

    $cart = array();
    for($i=0; $row2=sql_fetch_array($result2); $i++) {
        $cart[$row2['io_id']] = $row2;
    }

    $str = '';
    $count = 0;

    for($i=0; $row=sql_fetch_array($result); $i++) {
        if(!trim($row['io_name']))
            continue;

        $io_name = get_text($row['io_name']);
        $io_price = cm_get_price($it) + $row['io_price'];
        $ct_qty = 1;
        if($cart[$row['io_id']]['ct_qty'])
            $ct_qty = $cart[$row['io_id']]['ct_qty'];
        $io_chk = '';
        if($cart[$row['io_id']]['ct_id'])
            $io_chk = ' checked="checked"';
        $disabled = '';
        if(!$row['io_download'])
            $disabled = ' disabled="disabled"';

        $str .= '<tr>'.PHP_EOL;
        $str .= '<td class="t_l" colspan="2"><div>'.$io_name.PHP_EOL;
        $str .= '<label for="chk_it_'.$count.'" class="sound_only">'.$io_name.'선택</label>';
        $str .= '<input type="checkbox" name="io_chk[]" value="'.$count.'" id="chk_it_'.$count.'"'.$io_chk.$disabled.'>';
        $str .= '</div></td>'.PHP_EOL;
        $str .= '</tr>'.PHP_EOL;
        $str .= '<tr>'.PHP_EOL;
        $str .= '<td>'.PHP_EOL;
        $str .= '<label for="it_option_'.$count.'" class="sound_only">'.$io_name.'수량</label>';
        $str .= '<input type="hidden" name="io_id['.$count.']" value="'.$row['io_id'].'">';
        $str .= '<input type="hidden" name="io_price['.$count.']" value="'.$io_price.'">';
        $str .= '<button type="button" class="change_qty">감소</button>';
        $str .= '<input type="text" name="ct_qty['.$count.']" value="'.$ct_qty.'" size="3" class="input_opt">';
        $str .= '<button type="button" class="change_qty change_qty1">증가</button>';
        $str .= '</td>'.PHP_EOL;
        $str .= '<td>'.cm_display_price($it['it_price'] + $row['io_price']).'</td>'.PHP_EOL;
        $str .= '</tr>'.PHP_EOL;

        $count++;
    }

    if($count > 0)
        return $str;
    else
        return '';
}



function cm_print_item_options($it_id, $cart_id)
{
    global $g5;

    $sql = " select ct_option, ct_qty, io_price
                from {$g5['g5_contents_cart_table']} where it_id = '$it_id' and od_id = '$cart_id' order by ct_id asc ";
    $result = sql_query($sql);

    $str = '';
    for($i=0; $row=sql_fetch_array($result); $i++) {
        if($i == 0)
            $str .= '<ul>'.PHP_EOL;
        $price_plus = '';
        if($row['io_price'] >= 0)
            $price_plus = '+';
        $str .= '<li>'.$row['ct_option'].' '.$row['ct_qty'].'개 ('.$price_plus.cm_display_price($row['io_price']).')</li>'.PHP_EOL;
    }

    if($i > 0)
        $str .= '</ul>';

    return $str;
}


// 일자형식변환
function cm_date_conv($date, $case=1)
{
    if ($case == 1) { // 년-월-일 로 만들어줌
        $date = preg_replace("/([0-9]{4})([0-9]{2})([0-9]{2})/", "\\1-\\2-\\3", $date);
    } else if ($case == 2) { // 년월일 로 만들어줌
        $date = preg_replace("/-/", "", $date);
    }

    return $date;
}


// 배너출력
function cm_display_banner($position, $skin='')
{
    global $g5;

    if (!$position) $position = '왼쪽';
    if (!$skin) $skin = 'boxbanner.skin.php';

    $skin_path = G5_CONTENTS_SKIN_PATH.'/'.$skin;

    if(file_exists($skin_path)) {
        // 배너 출력
        $sql = " select * from {$g5['g5_contents_banner_table']} where '".G5_TIME_YMDHIS."' between bn_begin_time and bn_end_time and bn_position = '$position' order by bn_order, bn_id desc ";
        $result = sql_query($sql);

        include $skin_path;
    } else {
        echo '<p>'.str_replace(G5_PATH.'/', '', $skin_path).'파일이 존재하지 않습니다.</p>';
    }
}


function cm_get_yn($val, $case='')
{
    switch ($case) {
        case '1' : $result = ($val > 0) ? 'Y' : 'N'; break;
        default :  $result = ($val > 0) ? '예' : '아니오';
    }
    return $result;
}


// 상품명과 건수를 반환
function cm_get_goods($cart_id)
{
    global $g5;

    // 상품명만들기
    $row = sql_fetch(" select a.it_id, b.it_name from {$g5['g5_contents_cart_table']} a, {$g5['g5_contents_item_table']} b where a.it_id = b.it_id and a.od_id = '$cart_id' order by ct_id limit 1 ");
    // 상품명에 "(쌍따옴표)가 들어가면 오류 발생함
    $goods['it_id'] = $row['it_id'];
    $goods['full_name']= $goods['name'] = addslashes($row['it_name']);
    // 특수문자제거
    $goods['full_name'] = preg_replace ("/[ #\&\+\-%@=\/\\\:;,\.'\"\^`~\_|\!\?\*$#<>()\[\]\{\}]/i", "",  $goods['full_name']);

    // 상품건수
    $row = sql_fetch(" select count(*) as cnt from {$g5['g5_contents_cart_table']} where od_id = '$cart_id' ");
    $cnt = $row['cnt'] - 1;
    if ($cnt)
        $goods['full_name'] .= ' 외 '.$cnt.'건';
    $goods['count'] = $row['cnt'];

    return $goods;
}


// 패턴의 내용대로 해당 디렉토리에서 정렬하여 <select> 태그에 적용할 수 있게 반환
function cm_get_list_skin_options($pattern, $dirname='./', $sval='')
{
    $str = '<option value="">선택</option>'.PHP_EOL;

    unset($arr);
    $handle = opendir($dirname);
    while ($file = readdir($handle)) {
        if (preg_match("/$pattern/", $file, $matches)) {
            $arr[] = $matches[0];
        }
    }
    closedir($handle);

    sort($arr);
    foreach($arr as $value) {
        if($value == $sval)
            $selected = ' selected="selected"';
        else
            $selected = '';

        $str .= '<option value="'.$value.'"'.$selected.'>'.$value.'</option>'.PHP_EOL;
    }

    return $str;
}


// 일자 시간을 검사한다.
function cm_check_datetime($datetime)
{
    if ($datetime == "0000-00-00 00:00:00")
        return true;

    $year   = substr($datetime, 0, 4);
    $month  = substr($datetime, 5, 2);
    $day    = substr($datetime, 8, 2);
    $hour   = substr($datetime, 11, 2);
    $minute = substr($datetime, 14, 2);
    $second = substr($datetime, 17, 2);

    $timestamp = mktime($hour, $minute, $second, $month, $day, $year);

    $tmp_datetime = date("Y-m-d H:i:s", $timestamp);
    if ($datetime == $tmp_datetime)
        return true;
    else
        return false;
}


// 경고메세지를 경고창으로
function cm_alert_opener($msg='', $url='')
{
    global $g5;

    if (!$msg) $msg = '올바른 방법으로 이용해 주십시오.';

    echo "<meta http-equiv=\"content-type\" content=\"text/html; charset=utf-8\">";
    echo "<script>";
    echo "alert(\"$msg\");";
    echo "opener.location.href=\"$url\";";
    echo "self.close();";
    echo "</script>";
    exit;
}


// option 리스트에 selected 추가
function cm_conv_selected_option($options, $value)
{
    if(!$options)
        return '';

    $options = str_replace('value="'.$value.'"', 'value="'.$value.'" selected', $options);

    return $options;
}


// cart id 설정
function cm_set_cart_id($direct)
{
    global $g5, $setting, $member;

    if ($direct) {
        $tmp_cart_id = get_session('ss_cm_cart_direct');
        if(!$tmp_cart_id) {
            $tmp_cart_id = get_uniqid();
            set_session('ss_cm_cart_direct', $tmp_cart_id);
        }
    } else {
        $tmp_cart_id = get_session('ss_cm_cart_id');
        if(!$tmp_cart_id) {
            $tmp_cart_id = get_uniqid();
            set_session('ss_cm_cart_id', $tmp_cart_id);
        }

        // 보관된 회원장바구니 자료 cart id 변경
        if($member['mb_id'] && $tmp_cart_id) {
            $sql = " update {$g5['g5_contents_cart_table']}
                        set od_id = '$tmp_cart_id'
                        where mb_id = '{$member['mb_id']}'
                          and ct_direct = '0'
                          and ct_status = '쇼핑' ";
            if($setting['de_cart_keep_term']) {
                $ctime = date('Y-m-d', G5_SERVER_TIME - ($setting['de_cart_keep_term'] * 86400));
                $sql .= " and substring(ct_time, 1, 10) >= '$ctime' ";
            }

            sql_query($sql);
        }
    }
}


// 상품 목록 : 관련 상품 출력
function cm_relation_item($it_id, $width, $height, $rows=3)
{
    global $g5;

    $str = '';

    if(!$it_id)
        return $str;

    $sql = " select b.it_id, b.it_name, b.it_price, b.it_tel_inq from {$g5['g5_contents_item_relation_table']} a left join {$g5['g5_contents_item_table']} b on ( a.it_id2 = b.it_id ) where a.it_id = '$it_id' order by ir_no asc limit 0, $rows ";
    $result = sql_query($sql);

    for($i=0; $row=sql_fetch_array($result); $i++) {
        if($i == 0) {
            $str .= '<span class="sound_only">관련 상품 시작</span>';
            $str .= '<ul class="sct_rel_ul">';
        }

        $it_name = get_text($row['it_name']); // 상품명
        $it_price = cm_get_price($row); // 상품가격
        if(!$row['it_tel_inq'])
            $it_price = cm_display_price($it_price);

        $img = cm_get_it_image($row['it_id'], $width, $height);

        $str .= '<li class="sct_rel_li"><a href="'.G5_CONTENTS_URL.'/item.php?it_id='.$row['it_id'].'" class="sct_rel_a">'.$img.'</a></li>';
    }

    if($i > 0)
        $str .= '</ul><span class="sound_only">관련 상품 끝</span>';

    return $str;
}


// 상품이미지에 유형 아이콘 출력
function cm_item_icon($it)
{
    global $g5;

    $icon = '<span class="sit_icon">';

    if ($it['it_type1'])
        $icon .= '<img src="'.G5_CONTENTS_URL.'/img/icon_rec.gif" alt="추천상품">';

    if ($it['it_type2'])
        $icon .= '<img src="'.G5_CONTENTS_URL.'/img/icon_hit.gif" alt="인기상품">';

    if ($it['it_type3'])
        $icon .= '<img src="'.G5_CONTENTS_URL.'/img/icon_new.gif" alt="최신상품">';

    if ($it['it_type4'])
        $icon .= '<img src="'.G5_CONTENTS_URL.'/img/icon_discount.gif" alt="할인상품">';

    // 쿠폰상품
    $sql = " select count(*) as cnt
                from {$g5['g5_contents_coupon_table']}
                where cp_start <= '".G5_TIME_YMD."'
                  and cp_end >= '".G5_TIME_YMD."'
                  and (
                        ( cp_method = '0' and cp_target = '{$it['it_id']}' )
                        OR
                        ( cp_method = '1' and ( cp_target IN ( '{$it['ca_id']}', '{$it['ca_id2']}', '{$it['ca_id3']}' ) ) )
                      ) ";
    $row = sql_fetch($sql);
    if($row['cnt'])
        $icon .= '<img src="'.G5_CONTENTS_URL.'/img/icon_cp.gif" alt="쿠폰상품">';

    $icon .= '</span>';

    return $icon;
}


// sns 공유하기
function cm_get_sns_share_link($sns, $url, $title, $img)
{
    if(!$sns)
        return '';

    switch($sns) {
        case 'facebook':
            $str = '<a href="https://www.facebook.com/sharer/sharer.php?u='.urlencode($url).'&amp;p='.urlencode($title).'" class="share-facebook" target="_blank"><img src="'.$img.'" alt="페이스북에 공유"></a>';
            break;
        case 'twitter':
            $str = '<a href="https://twitter.com/share?url='.urlencode($url).'&amp;text='.urlencode($title).'" class="share-twitter" target="_blank"><img src="'.$img.'" alt="트위터에 공유"></a>';
            break;
        case 'googleplus':
            $str = '<a href="https://plus.google.com/share?url='.urlencode($url).'" class="share-googleplus" target="_blank"><img src="'.$img.'" alt="구글플러스에 공유"></a>';
            break;
    }

    return $str;
}


// 상품이미지 썸네일 삭제
function cm_delete_item_thumbnail($dir, $file)
{
    if(!$dir || !$file)
        return;

    $filename = preg_replace("/\.[^\.]+$/i", "", $file); // 확장자제거

    $files = glob($dir.'/thumb-'.$filename.'*');

    if(is_array($files)) {
        foreach($files as $thumb_file) {
            @unlink($thumb_file);
        }
    }
}


// 쿠폰번호 생성함수
function cm_get_coupon_id()
{
    $len = 16;
    $chars = "ABCDEFGHJKLMNPQRSTUVWXYZ123456789";

    srand((double)microtime()*1000000);

    $i = 0;
    $str = '';

    while ($i < $len) {
        $num = rand() % strlen($chars);
        $tmp = substr($chars, $num, 1);
        $str .= $tmp;
        $i++;
    }

    $str = preg_replace("/([0-9A-Z]{4})([0-9A-Z]{4})([0-9A-Z]{4})([0-9A-Z]{4})/", "\\1-\\2-\\3-\\4", $str);

    return $str;
}


// 주문의 금액, 배송비 과세금액 등의 정보를 가져옴
function cm_get_order_info($od_id)
{
    global $g5;

    // 주문정보
    $sql = " select * from {$g5['g5_contents_order_table']} where od_id = '$od_id' ";
    $od = sql_fetch($sql);

    if(!$od['od_id'])
        return false;

    $info = array();

    // 장바구니 주문금액정보
    $sql = " select SUM((ct_price + io_price) * ct_qty) as price,
                    SUM(cp_price) as coupon
                from {$g5['g5_contents_cart_table']}
                where od_id = '$od_id'
                  and ct_status IN ( '주문', '입금' ) ";
    $sum = sql_fetch($sql);

    $cart_price = $sum['price'];
    $cart_coupon = $sum['coupon'];

    $od_coupon = 0;

    if($od['mb_id']) {
        // 주문할인 쿠폰
        $sql = " select a.cp_id, a.cp_type, a.cp_price, a.cp_trunc, a.cp_minimum, a.cp_maximum
                    from {$g5['g5_contents_coupon_table']} a right join {$g5['g5_contents_coupon_log_table']} b on ( a.cp_id = b.cp_id )
                    where b.od_id = '$od_id'
                      and b.mb_id = '{$od['mb_id']}'
                      and a.cp_method = '2' ";
        $cp = sql_fetch($sql);

        $tot_od_price = $cart_price - $cart_coupon;

        if($cp['cp_id']) {
            $dc = 0;

            if($cp['cp_minimum'] <= $tot_od_price) {
                if($cp['cp_type']) {
                    $dc = floor(($tot_od_price * ($cp['cp_price'] / 100)) / $cp['cp_trunc']) * $cp['cp_trunc'];
                } else {
                    $dc = $cp['cp_price'];
                }

                if($cp['cp_maximum'] && $dc > $cp['cp_maximum'])
                    $dc = $cp['cp_maximum'];

                if($tot_od_price < $dc)
                    $dc = $tot_od_price;

                $tot_od_price -= $dc;
                $od_coupon = $dc;
            }
        }
    }

    // 장바구니 취소금액 정보
    $sql = " select SUM((ct_price + io_price) * ct_qty) as price
                from {$g5['g5_contents_cart_table']}
                where od_id = '$od_id'
                  and ct_status IN ( '취소' ) ";
    $sum = sql_fetch($sql);
    $cancel_price = $sum['price'];

    // 미수금액
    $od_misu = $cart_price
               - ( $cart_coupon + $od_coupon )
               - ( $od['od_receipt_price'] + $od['od_receipt_cash'] + $od['od_receipt_point'] - $od['od_refund_price'] );

    // 장바구니상품금액
    $od_cart_price = $cart_price + $cancel_price;

    // 결과처리
    $info['od_cart_price']      = $od_cart_price;
    $info['od_coupon']          = $od_coupon;
    $info['od_cart_coupon']     = $cart_coupon;
    $info['od_cancel_price']    = $cancel_price;
    $info['od_misu']            = $od_misu;

    return $info;
}


// 상품포인트
function cm_get_item_point($it, $io_id='', $trunc=10)
{
    global $g5;

    $it_point = 0;

    if($it['it_point_type'] > 0) {
        $it_price = $it['it_price'];

        if($it['it_point_type'] == 2 && $io_id) {
            $sql = " select io_id, io_price
                        from {$g5['g5_contents_item_option_table']}
                        where it_id = '{$it['it_id']}'
                          and io_id = '$io_id'
                          and io_use = '1' ";
            $opt = sql_fetch($sql);

            if($opt['io_id'])
                $it_price += $opt['io_price'];
        }

        $it_point = floor(($it_price * ($it['it_point'] / 100) / $trunc)) * $trunc;
    } else {
        $it_point = $it['it_point'];
    }

    return $it_point;
}


// 쿠폰 사용체크
function cm_is_used_coupon($mb_id, $cp_id)
{
    global $g5;

    $used = false;

    $sql = " select count(*) as cnt from {$g5['g5_contents_coupon_log_table']} where mb_id = '$mb_id' and cp_id = '$cp_id' ";
    $row = sql_fetch($sql);

    if($row['cnt'])
        $used = true;

    return $used;
}

// 상품후기 작성가능한지 체크
function cm_check_itemuse_write($it_id, $mb_id, $close=true)
{
    global $g5, $setting, $is_admin;

    if(!$is_admin && $setting['de_item_use_write'])
    {
        $sql = " select count(*) as cnt
                    from {$g5['g5_contents_cart_table']}
                    where it_id = '$it_id'
                      and mb_id = '$mb_id'
                      and ct_status = '완료' ";
        $row = sql_fetch($sql);

        if($row['cnt'] == 0)
        {
            if($close)
                alert_close('사용후기는 주문이 완료된 경우에만 작성하실 수 있습니다.');
            else
                alert('사용후기는 주문하신 상품의 상태가 완료인 경우에만 작성하실 수 있습니다.');
        }
    }
}


// 구매 본인인증 체크
function cm_member_cert_check($id, $type)
{
    global $g5, $member;

    $msg = '';

    switch($type)
    {
        case 'item':
            $sql = " select ca_id, ca_id2, ca_id3 from {$g5['g5_contents_item_table']} where it_id = '$id' ";
            $it = sql_fetch($sql);

            $seq = '';
            for($i=0; $i<3; $i++) {
                $ca_id = $it['ca_id'.$seq];

                if(!$ca_id)
                    continue;

                $sql = " select ca_cert_use, ca_adult_use from {$g5['g5_contents_category_table']} where ca_id = '$ca_id' ";
                $row = sql_fetch($sql);

                // 본인확인체크
                if($row['ca_cert_use'] && !$member['mb_certify']) {
                    if($member['mb_id'])
                        $msg = '회원정보 수정에서 본인확인 후 이용해 주십시오.';
                    else
                        $msg = '본인확인된 로그인 회원만 이용할 수 있습니다.';

                    break;
                }

                // 성인인증체크
                if($row['ca_adult_use'] && !$member['mb_adult']) {
                    if($member['mb_id'])
                        $msg = '본인확인으로 성인인증된 회원만 이용할 수 있습니다.\\n회원정보 수정에서 본인확인을 해주십시오.';
                    else
                        $msg = '본인확인으로 성인인증된 회원만 이용할 수 있습니다.';

                    break;
                }

                if($i == 0)
                    $seq = 1;
                $seq++;
            }

            break;
        case 'list':
            $sql = " select * from {$g5['g5_contents_category_table']} where ca_id = '$id' ";
            $ca = sql_fetch($sql);

            // 본인확인체크
            if($ca['ca_cert_use'] && !$member['mb_certify']) {
                if($member['mb_id'])
                    $msg = '회원정보 수정에서 본인확인 후 이용해 주십시오.';
                else
                    $msg = '본인확인된 로그인 회원만 이용할 수 있습니다.';
            }

            // 성인인증체크
            if($ca['ca_adult_use'] && !$member['mb_adult']) {
                if($member['mb_id'])
                    $msg = '본인확인으로 성인인증된 회원만 이용할 수 있습니다.\\n회원정보 수정에서 본인확인을 해주십시오.';
                else
                    $msg = '본인확인으로 성인인증된 회원만 이용할 수 있습니다.';
            }

            break;
        default:
            break;
    }

    return $msg;
}


// 사용후기의 확인된 건수를 상품테이블에 저장합니다.
function cm_update_use_cnt($it_id)
{
    global $g5;
    $row = sql_fetch(" select count(*) as cnt from {$g5['g5_contents_item_use_table']} where it_id = '{$it_id}' and is_confirm = 1 ");
    return sql_query(" update {$g5['g5_contents_item_table']} set it_use_cnt = '{$row['cnt']}' where it_id = '{$it_id}' ");
}


// 사용후기의 선호도(별) 평균을 상품테이블에 저장합니다.
function cm_update_use_avg($it_id)
{
    global $g5;
    $row = sql_fetch(" select count(*) as cnt, sum(is_score) as total from {$g5['g5_contents_item_use_table']} where it_id = '{$it_id}' ");
    $average = ($row['total'] && $row['cnt']) ? $row['total'] / $row['cnt'] : 0;
    return sql_query(" update {$g5['g5_contents_item_table']} set it_use_avg = '$average' where it_id = '{$it_id}' ");
}


//------------------------------------------------------------------------------
// 주문포인트를 적립한다.
// 설정일이 지난 포인트 부여되지 않은 배송완료된 장바구니 자료에 포인트 부여
// 설정일이 0 이면 주문서 완료 설정 시점에서 포인트를 바로 부여합니다.
//------------------------------------------------------------------------------
function cm_save_order_point($ct_status="입금")
{
    global $g5, $setting;

    $beforedays = date("Y-m-d H:i:s", ( time() - (86400 * (int)$setting['de_point_days']) ) ); // 86400초는 하루
    $sql = " select * from {$g5['g5_contents_cart_table']} where ct_status = '$ct_status' and ct_point_use = '0' and ct_time <= '$beforedays' ";
    $result = sql_query($sql);
    for ($i=0; $row=sql_fetch_array($result); $i++) {
        // 회원 ID 를 얻는다.
        $od_row = sql_fetch("select od_id, mb_id from {$g5['g5_contents_order_table']} where od_id = '{$row['od_id']}' ");
        if ($od_row['mb_id'] && $row['ct_point'] > 0) { // 회원이면서 포인트가 0보다 크다면
            $po_point = $row['ct_point'] * $row['ct_qty'];
            $po_content = "컨텐츠몰 주문번호 {$od_row['od_id']} ({$row['ct_id']}) 구매완료";
            insert_point($od_row['mb_id'], $po_point, $po_content, "@contents", $od_row['mb_id'], "{$od_row['od_id']},{$row['ct_id']}");
        }
        sql_query("update {$g5['g5_contents_cart_table']} set ct_point_use = '1' where ct_id = '{$row['ct_id']}' ");
    }
}


// 사용후기 썸네일 생성
function cm_get_itemuselist_thumbnail($it_id, $contents, $thumb_width, $thumb_height, $is_create=false, $is_crop=true, $crop_mode='center', $is_sharpen=true, $um_value='80/0.5/3')
{
    global $g5, $config;
    $img = $filename = $alt = "";

    if($contents) {
        $matches = get_editor_image($contents, false);

        for($i=0; $i<count($matches[1]); $i++)
        {
            // 이미지 path 구함
            $p = parse_url($matches[1][$i]);
            if(strpos($p['path'], '/'.G5_DATA_DIR.'/') != 0)
                $data_path = preg_replace('/^\/.*\/'.G5_DATA_DIR.'/', '/'.G5_DATA_DIR, $p['path']);
            else
                $data_path = $p['path'];

            $srcfile = G5_PATH.$data_path;

            if(preg_match("/\.({$config['cf_image_extension']})$/i", $srcfile) && is_file($srcfile)) {
                $size = @getimagesize($srcfile);
                if(empty($size))
                    continue;

                $filename = basename($srcfile);
                $filepath = dirname($srcfile);

                preg_match("/alt=[\"\']?([^\"\']*)[\"\']?/", $matches[0][$i], $malt);
                $alt = get_text($malt[1]);

                break;
            }
        }

        if($filename) {
            $thumb = thumbnail($filename, $filepath, $filepath, $thumb_width, $thumb_height, $is_create, $is_crop, $crop_mode, $is_sharpen, $um_value);

            if($thumb) {
                $src = G5_URL.str_replace($filename, $thumb, $data_path);
                $img = '<img src="'.$src.'" width="'.$thumb_width.'" height="'.$thumb_height.'" alt="'.$alt.'">';
            }
        }
    }

    if(!$img)
        $img = cm_get_it_image($it_id, $thumb_width, $thumb_height);

    return $img;
}

// 컨텐츠 파일 업로드
function contents_file_upload($it_id, $tmp_file, $filename, $i)
{
    $data_dir = G5_DATA_PATH.'/'.G5_CONTENTS_SAVE_DIR.'/'.$it_id;

    if(!is_dir($data_dir)) {
        @mkdir($data_dir, G5_DIR_PERMISSION);
        @chmod($data_dir, G5_DIR_PERMISSION);
    }

    $destname = md5(rand().$i.$filename);

    @move_uploaded_file($tmp_file, $data_dir.'/'.$destname);
    @chmod($data_dir.'/'.$destname, G5_FILE_PERMISSION);

    return $destname;
}

// 캐시 히스토리 기록
function insert_cash($mb_id, $cs_id, $cash, $memo)
{
    global $g5;

    $ch_sum = get_member_cash($mb_id) + $cash;

    $sql = " insert into {$g5['g5_contents_cash_history_table']}
                set mb_id     = '$mb_id',
                    cs_id     = '$cs_id',
                    ch_price  = '$cash',
                    ch_sum    = '$ch_sum',
                    ch_memo   = '$memo',
                    ch_time   = '".G5_TIME_YMDHIS."',
                    ch_ip     = '{$_SERVER['REMOTE_ADDR']}' ";
    sql_query($sql);
}

// 회원 보유캐시
function get_member_cash($mb_id)
{
    global $g5;

    $mb_cash = 0;

    $sql = " select sum(ch_price) as cash from {$g5['g5_contents_cash_history_table']} where mb_id = '$mb_id' ";
    $row = sql_fetch($sql);

    $mb_cash = (int)$row['cash'];

    if($mb_cash < 0)
        $mb_cash = 0;

    return $mb_cash;
}

// 다운로드 수 기록
function update_download_count($od_id, $ct_id)
{
    global $g5;

    $sql = " update {$g5['g5_contents_cart_table']} set ct_download = ct_download + 1 where od_id = '$od_id' and ct_id = '$ct_id' ";
    sql_query($sql, false);
}

// Referer 체크
function check_referer()
{
    $p1 = parse_url($_SERVER['HTTP_REFERER']);
    $p2 = parse_url(G5_URL);

    if($p1['host'] != $p2['host'])
        die('Error.');
}

// 판매수량 증가
function add_item_sale_qty($od_id, $ct_id='')
{
    global $g5;

    if($ct_id)
        $sql2 = " select it_id, ct_qty as sum_qty from {$g5['g5_contents_cart_table']} where od_id = '$od_id' and ct_id = '$ct_id' and ct_status = '입금' ";
    else
        $sql2 = " select it_id, sum(ct_qty) as sum_qty from {$g5['g5_contents_cart_table']} where od_id = '$od_id' and ct_status = '입금' group by it_id ";

    $result2 = sql_query($sql2);

    for ($k=0; $row2=sql_fetch_array($result2); $k++) {
        $sql3 = " update {$g5['g5_contents_item_table']} set it_sum_qty = it_sum_qty + '{$row2['sum_qty']}' where it_id = '{$row2['it_id']}' ";
        sql_query($sql3);
    }
}

// 판매수량 차감
function substract_item_sale_qty($od_id, $ct_id='')
{
    global $g5;

    if($ct_id)
        $sql2 = " select it_id, ct_qty as sum_qty from {$g5['g5_contents_cart_table']} where od_id = '$od_id' and ct_id = '$ct_id' ";
    else
        $sql2 = " select it_id, sum(ct_qty) as sum_qty from {$g5['g5_contents_cart_table']} where od_id = '$od_id' and ct_status = '취소' group by it_id ";

    $result2 = sql_query($sql2);

    for ($k=0; $row2=sql_fetch_array($result2); $k++) {
        $sql3 = " select it_sum_qty from {$g5['g5_contents_item_table']} where it_id = '{$row2['it_id']}' ";
        $row3 = sql_fetch($sql3);

        $it_sum_qty = $row3['it_sum_qty'] - $row2['sum_qty'];
        if($it_sum_qty < 0)
            $it_sum_qty = 0;

        sql_query(" update {$g5['g5_contents_item_table']} set it_sum_qty = '$it_sum_qty' where it_id = '{$row2['it_id']}' ");
    }
}

// 모바일 PG 주문 필드 생성
function make_order_field($data, $exclude)
{
    $field = '';

    foreach($data as $key=>$value) {
        if(in_array($key, $exclude))
            continue;

        if(is_array($value)) {
            foreach($value as $k=>$v) {
                $field .= '<input type="hidden" name="'.$key.'['.$k.']" value="'.$v.'">'.PHP_EOL;
            }
        } else {
            $field .= '<input type="hidden" name="'.$key.'" value="'.$value.'">'.PHP_EOL;
        }
    }

    return $field;
}

// 컨텐츠허브 등록
function insert_contentshub($code, $name, $explan, $price, $caid, $tag, $w)
{
    global $setting;

    if(!($setting['de_chub_mid'] && defined('G5_CONTENTS_HUB_URL') && G5_CONTENTS_HUB_URL))
        return;

    if(!trim($caid))
        return;

    $price = cm_get_chub_item_price($code);
    if($price < 1)
        return;

    $purl   = G5_CONTENTS_URL.'/item.php?it_id='.$code;
    $imgurl = cm_get_chub_item_imageurl($code);

    if($w == 'u')
        $type = 'MOD';
    else
        $type = 'REG';

    $post_data = array(
        'MID'    => $setting['de_chub_mid'],
        'CODE'   => $code,
        'NAME'   => strip_tags($name),
        'EXPLAN' => strip_tags($explan),
        'PRICE'  => $price,
        'PURL'   => $purl,
        'IMGURL' => $imgurl,
        'TAG'    => strip_tags($tag),
        'CAID'   => $caid,
        'TYPE'   => $type
    );

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, G5_CONTENTS_HUB_URL);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $return = curl_exec($ch);

    if(defined('G5_CONTENTS_HUB_CODE_DISPLAY') && G5_CONTENTS_HUB_CODE_DISPLAY) {
        echo $return;
        exit;
    }
}

// 컨텐츠허브 이미지 URL.
function cm_get_chub_item_imageurl($it_id)
{
    global $g5;

    $sql = " select it_img1, it_img2, it_img3, it_img4, it_img5, it_img6, it_img7, it_img8, it_img9, it_img10
                from {$g5['g5_contents_item_table']}
                where it_id = '$it_id' ";
    $row = sql_fetch($sql);
    $filepath = '';

    for($i=1; $i<=10; $i++) {
        $img = $row['it_img'.$i];
        $file = G5_DATA_PATH.'/cmitem/'.$img;
        if(!is_file($file))
            continue;

        $size = @getimagesize($file);
        if($size[2] < 1 || $size[2] > 3)
            continue;

        $filepath = $file;
        break;
    }

    if($filepath)
        $str = str_replace(G5_PATH, G5_URL, $filepath);
    else
        $str = '';

    return $str;
}

// 컨텐츠허브 아이템 가격
function cm_get_chub_item_price($it_id)
{
    global $g5;

    $sql = " select it_price from {$g5['g5_contents_item_table']} where it_id = '$it_id' ";
    $row = sql_fetch($sql);

    $it_price = $row['it_price'];

    $sql = " select max(io_price) as max_price from {$g5['g5_contents_item_option_table']} where it_id = '$it_id' ";
    $row = sql_fetch($sql);

    $it_price += $row['max_price'];

    return $it_price;
}

//==============================================================================
// 컨텐츠몰 라이브러리 모음 끝
//==============================================================================
?>