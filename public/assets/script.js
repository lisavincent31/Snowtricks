$(document).ready(function() {
    var scrollDistance = $(window).scrollTop();
    if(scrollDistance > 100) {
        $('#go-top').removeClass('d-none');
        $('#go-bottom').addClass('d-none');

        $('.go-top-btn').removeClass('d-none');
        $('.go-bottom-btn').addClass('d-none');
    }

    if(scrollDistance < 100) {
        $('#go-bottom').removeClass('d-none');
        $('#go-top').addClass('d-none');

        $('.go-bottom-btn').removeClass('d-none');
        $('.go-top-btn').addClass('d-none');
    }
});
$(window).scroll(function() {
    var scrollDistance = $(window).scrollTop();
    if(scrollDistance > 100) {
        $('#go-top').removeClass('d-none');
        $('#go-bottom').addClass('d-none');

        $('.go-top-btn').removeClass('d-none');
        $('.go-bottom-btn').addClass('d-none');
    }

    if(scrollDistance < 100) {
        $('#go-bottom').removeClass('d-none');
        $('#go-top').addClass('d-none');

        $('.go-bottom-btn').removeClass('d-none');
        $('.go-top-btn').addClass('d-none');
    }
});