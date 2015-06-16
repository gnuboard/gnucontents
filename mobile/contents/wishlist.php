<?php
include_once('./_common.php');

if (!$is_member)
    goto_url(G5_BBS_URL."/login.php?url=".urlencode(G5_CONTENTS_URL.'/wishlist.php'));

$g5['title'] = "위시리스트";
include_once(G5_MCONTENTS_PATH.'/_head.php');
?>

<!-- 위시리스트 시작 { -->
<div id="cod_ws">

    <div class="tbl_head01 tbl_wrap">
        <table>
        <thead>
        <tr>
            <th scope="col">이미지</th>
            <th scope="col">상품명</th>
            <th scope="col">보관일시</th>
            <th scope="col">삭제</th>
        </tr>
        </thead>
        <tbody>

        <?php
        $sql  = " select a.wi_id, a.wi_time, b.* from {$g5['g5_contents_wish_table']} a left join {$g5['g5_contents_item_table']} b on ( a.it_id = b.it_id ) ";
        $sql .= " where a.mb_id = '{$member['mb_id']}' order by a.wi_id desc ";
        $result = sql_query($sql);
        for ($i=0; $row = mysql_fetch_array($result); $i++) {

            $out_cd = '';
            $sql = " select count(*) as cnt from {$g5['g5_contents_item_option_table']} where it_id = '{$row['it_id']}' and io_type = '0' ";
            $tmp = sql_fetch($sql);
            if($tmp['cnt'])
                $out_cd = 'no';

            $it_price = cm_get_price($row);

            if ($row['it_tel_inq']) $out_cd = 'tel_inq';

            $image = cm_get_it_image($row['it_id'], 70, 70);
        ?>

        <tr>
            <td class="cod_ws_img"><?php echo $image; ?></td>
            <td class="t_l"><a href="./item.php?it_id=<?php echo $row['it_id']; ?>"><?php echo stripslashes($row['it_name']); ?></a></td>
            <td class="td_datetime"><?php echo $row['wi_time']; ?></td>
            <td class="td_mngsmall"><a href="./wishupdate.php?w=d&amp;wi_id=<?php echo $row['wi_id']; ?>">삭제</a></td>
        </tr>
        <?php
        }

        if ($i == 0)
            echo '<tr><td colspan="4" class="empty_table">보관함이 비었습니다.</td></tr>';
        ?>
        
        </tbody>
        </table>
    </div>

</div>

<!-- } 위시리스트 끝 -->

<?php
include_once(G5_MCONTENTS_PATH.'/_tail.php');
?>