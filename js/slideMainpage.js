screenWidth = $(window).width();
screenHeight = $(window).height();


/*window.onscroll = function(){
    screenWidth = $(window).width();
    screenHeight = $(window).height();
    var t = document.documentElement.scrollTop || document.body.scrollTop;
    if(screenWidth > 768) {
        if( t >= 30 ) {
            $("#navbar").addClass("ace-navbar-blue");
        } else {
            $("#navbar").removeClass("ace-navbar-blue");
        }
    }
};
window.onload = window.onresize = function () {
    screenWidth = $(window).width();
    screenHeight = $(window).height();
    $("#banner_img>li").css({ height: screenHeight });
    $("#banner").css({ height: screenHeight });
    if(screenWidth <= 768) {
        $("#navbar").addClass("ace-navbar-gray");
    } else {
        $("#navbar").removeClass("ace-navbar-gray");
    }
};*/


var curIndex = 0;
var time = 2000;
var slideTime = 3000;
var adImg = $("#banner_img>li>div>.ad_img");
var cardBack = $("#ace-card>div>a")
var int = setInterval("autoSlide()", slideTime);
$("#ace-card>div>a").mouseenter(function () {
    show($(this).index());
    window.clearInterval(int);
});

$("#ace-card>div>a").mouseleave(function () {
    int = setInterval("autoSlide()", slideTime);
});

function autoSlide() {
    curIndex + 1 >= $("#banner_img>li").length ? curIndex = -1 : false;
    show(curIndex + 1);
}
function show(index) {
    $.easing.def = "easeOutQuad";
    $("#banner_img>li").eq(curIndex).stop(false, true).fadeOut(time);
    adImg.eq(curIndex).stop(false, true).animate({ right: "120px" }, time);
    cardBack.eq(curIndex).stop(false, true).removeClass("select");
    // setTimeout(function () {
    $("#banner_img>li").eq(index).stop(false, true).fadeIn(time);
    adImg.eq(index).css({ right: "-50px", opacity: "0" }).stop(false, true).animate({ right: "10px", opacity: "1" }, time);
    cardBack.eq(index).stop(false, true).addClass("select");
    // }, 0);
    curIndex = index;
}