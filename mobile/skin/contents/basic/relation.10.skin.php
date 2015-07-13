<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

// add_stylesheet('css 구문', 출력순서); 숫자가 작을 수록 먼저 출력됨
add_stylesheet('<link rel="stylesheet" href="'.G5_MCONTENTS_CSS_URL.'/style.css">', 0);
?>

<!-- 상품진열 10 시작 { -->
<?php
$li_width = intval(100 / $this->list_mod);
$li_width_style = ' style="width:'.$li_width.'%;"';

for ($i=0; $row=sql_fetch_array($result); $i++) {
    if ($i == 0) {
        if ($this->css) {
            echo "<ul id=\"cct_wrap\" class=\"{$this->css}\">\n";
        } else {
            echo "<ul id=\"cct_wrap\" class=\"cct\">\n";
        }
    }

    if($i % $this->list_mod == 0)
        $cct_clear = ' cct_clear';
    else
        $cct_clear = '';

    echo "<li class=\"cct_li {$cct_clear}\"{$li_width_style}>\n";

    if ($this->href) {
        echo "<div class=\"reli_wr\"><div class=\"cct_img\"><a href=\"{$this->href}{$row['it_id']}\" class=\"sct_a\">\n";
    }

    if ($this->view_it_img) {
        echo cm_get_it_image($row['it_id'], $this->img_width, $this->img_height, '', '', stripslashes($row['it_name']))."\n";
    }

    if ($this->href) {
        echo "</a></div>\n";
    }

    if ($this->view_it_icon) {
        echo "<div class=\"sct_icon\">".cm_item_icon($row)."</div>\n";
    }

    if ($this->view_it_id) {
        echo "<div class=\"sct_id\">&lt;".stripslashes($row['it_id'])."&gt;</div>\n";
    }

    if ($this->href) {
        echo "<div class=\"sct_txt\"><a href=\"{$this->href}{$row['it_id']}\" class=\"sct_a\">\n";
    }

    if ($this->view_it_name) {
        echo stripslashes($row['it_name'])."\n";
    }

    if ($this->href) {
        echo "</a></div>\n";
    }

    if ($this->view_it_basic && $row['it_basic']) {
        echo "<div class=\"sct_basic\">".stripslashes($row['it_basic'])."</div>\n";
    }

    if ($this->view_it_price || $this->view_it_sum_qty || $this->view_it_wish_qty) {
        echo "<div class=\"sct_price\">\n";
        echo "<span class=\"rgoods_price\">" .cm_display_price(cm_get_price($row), $row['it_tel_inq'])." </span>\n";
        echo "</div>\n";
    }


    echo "</div></li>\n";
}

if ($i > 0) echo "</ul>\n";

if($i == 0) echo "<p class=\"cct_noitem\">등록된 상품이 없습니다.</p>\n";
?>
<!-- } 상품진열 10 끝 -->
