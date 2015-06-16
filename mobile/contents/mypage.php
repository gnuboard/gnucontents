<?php
include_once('./_common.php');

if (!$is_member)
    alert('회원 로그인 후 이용해 주십시오.', G5_BBS_URL."/login.php?url=".urlencode(G5_CONTENTS_URL."/mypage.php"));

$g5['title'] = $member['mb_name'].'님 마이페이지';
include_once(G5_MCONTENTS_PATH.'/_head.php');

// 쿠폰
$cp_count = 0;
$sql = " select cp_id
            from {$g5['g5_contents_coupon_table']}
            where mb_id IN ( '{$member['mb_id']}', '전체회원' )
              and cp_start <= '".G5_TIME_YMD."'
              and cp_end >= '".G5_TIME_YMD."' ";
$res = sql_query($sql);

for($k=0; $cp=sql_fetch_array($res); $k++) {
    if(!cm_is_used_coupon($member['mb_id'], $cp['cp_id']))
        $cp_count++;
}
?>

<!-- 마이페이지 시작 { -->
<div id="cmb_my">

    <!-- 회원정보 개요 시작 { -->
    <section id="cmb_my_ov">
        <h2 class="accessibility">회원정보 개요</h2>

        <div id="cmb_my_act">
            <ul>
                <?php if ($is_admin == 'super') { ?><li id="adm"><a href="<?php echo G5_ADMIN_URL; ?>/" class="btn_admin">관리자</a></li><?php } ?>
                <li><a href="<?php echo G5_BBS_URL; ?>/memo.php" target="_blank" class="win_memo btn01">쪽지함</a></li>
                <li><a href="<?php echo G5_BBS_URL; ?>/member_confirm.php?url=register_form.php" class="btn01">회원정보수정</a></li>
                <li><a href="<?php echo G5_BBS_URL; ?>/member_confirm.php?url=member_leave.php" onclick="return member_leave();" class="btn02">회원탈퇴</a></li>
            </ul>
        </div>

        <dl>
            <dt>보유포인트</dt>
            <dd><a href="<?php echo G5_BBS_URL; ?>/point.php" target="_blank" class="win_point"><?php echo number_format($member['mb_point']); ?>점</a></dd>
            <dt>보유쿠폰</dt>
            <dd><a href="<?php echo G5_CONTENTS_URL; ?>/coupon.php" target="_blank" class="win_coupon"><?php echo number_format($cp_count); ?></a></dd>
            <dt>연락처</dt>
            <dd><?php echo ($member['mb_tel'] ? $member['mb_tel'] : '미등록'); ?></dd>
            <dt>E-Mail</dt>
            <dd><?php echo ($member['mb_email'] ? $member['mb_email'] : '미등록'); ?></dd>
            <dt>최종접속일시</dt>
            <dd><?php echo $member['mb_today_login']; ?></dd>
            <dt>회원가입일시</dt>
            <dd><?php echo $member['mb_datetime']; ?></dd>
        </dl>
    </section>
    <!-- } 회원정보 개요 끝 -->

    <!-- 최근 주문내역 시작 { -->
    <section id="cmb_my_od">
        <h2>최근 구매내역</h2>
        <?php
        // 최근 주문내역
        define("_ORDERINQUIRY_", true);

        $limit = " limit 0, 5 ";
        include G5_MCONTENTS_PATH.'/orderinquiry.sub.php';
        ?>

        <div class="cmb_my_more">
            <a href="./orderinquiry.php" class="btn01">구매내역 더보기</a>
        </div>
    </section>
    <!-- } 최근 주문내역 끝 -->

    <!-- 최근 캐시충전 내역 시작 { -->
     <section id="cmb_my_charge">
        <h2>최근 충전 내역</h2>

        <?php
        // 최근 캐시충전 내역
        define("_CASHLIST_", false);

        $limit = " limit 0, 5 ";
        include G5_MCONTENTS_PATH.'/cashlist.sub.php';
        ?>

        <div class="cmb_my_more">
            <a href="./cashlist.php" class="btn01">충전내역 더보기</a>
        </div>
    </section>
    <!-- } 최근 충전내역 끝 -->


    <!-- 최근 위시리스트 시작 { -->
    <section id="cmb_my_wish">
        <h2>최근 위시리스트</h2>

        <div class="tbl_head01 tbl_wrap">
            <table>
            <thead>
            <tr>
                <th scope="col" width="10%">이미지</th>
                <th scope="col" width="40%">상품명</th>
                <th scope="col" width="30%">보관일시</th>
            </tr>
            </thead>
            <tbody>
            <?php
            $sql = " select *
                       from {$g5['g5_contents_wish_table']} a,
                            {$g5['g5_contents_item_table']} b
                      where a.mb_id = '{$member['mb_id']}'
                        and a.it_id  = b.it_id
                      order by a.wi_id desc
                      limit 0, 3 ";
            $result = sql_query($sql);
            for ($i=0; $row = sql_fetch_array($result); $i++)
            {
                $image = cm_get_it_image($row['it_id'], 70, 70, true);
            ?>

            <tr>
                <td class="cmb_my_img"><?php echo $image; ?></td>
                <td class="t_l"><a href="./item.php?it_id=<?php echo $row['it_id']; ?>"><?php echo stripslashes($row['it_name']); ?></a></td>
                <td class="td_datetime"><?php echo $row['wi_time']; ?></td>
            </tr>

            <?php
            }

            if ($i == 0)
                echo '<tr><td colspan="3" class="empty_table">보관 내역이 없습니다.</td></tr>';
            ?>
            </tbody>
            </table>
        </div>

        <div class="cmb_my_more">
            <a href="./wishlist.php" class="btn01">위시리스트 더보기</a>
        </div>
    </section>
    <!-- } 최근 위시리스트 끝 -->

</div>

<script>
$(function() {
    $(".win_coupon").click(function() {
        var new_win = window.open($(this).attr("href"), "win_coupon", "left=100,top=100,width=700, height=600, scrollbars=1");
        new_win.focus();
        return false;
    });
});

function member_leave()
{
    return confirm('정말 회원에서 탈퇴 하시겠습니까?')
}
</script>
<!-- } 마이페이지 끝 -->

<?php
include_once(G5_MCONTENTS_PATH.'/_tail.php');
?>