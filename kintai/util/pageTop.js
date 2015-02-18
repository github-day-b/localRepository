/*
     *   @author 
     *   FileName pageTop.js
     *   @create 
     *   Remark #pageTopが押された際にページの先頭へ戻す。
*/

$(function() {
    var topBtn = $('#pageTop');
    topBtn.hide();
    $(window).scroll(function () {
        if ($(this).scrollTop() > 140) {
            topBtn.fadeIn();
        } else {
            topBtn.fadeOut();
        }
    });
    topBtn.click(function () {
        $('body,html').animate({
            scrollTop: 0
        }, 350);
        return false;
    });
});
//EOF