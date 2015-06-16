/**
 * 동영상보기 창
 **/
var win_movie = function(href) {
    var new_win = window.open(href, 'win_movie', 'width=720, height=480, scrollbars=1');
    new_win.focus();
}

$(function() {
    $(".win_view_movie").click(function() {
        win_movie(this.href);
        return false;
    });
});