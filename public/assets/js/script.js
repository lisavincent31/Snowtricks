$(document).ready(function() {
    /**
     * Show or Hide buttons go-to-top on SCROLL
     */
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
/**
 * SCROLL FUNCTION
 */
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

/**
 * Change class on mobile device
 */
$(document).ready(function() {
    if($(window).width() < 640) {
        $('.list-group').removeClass('list-group-horizontal').addClass('list-group-vertical mx-auto p-0');
        $('#slideshow').find('.row').addClass('w-100 mx-auto');
        $('#tricks').find('.row').addClass('w-100 mx-auto');
        $('#medias_show').addClass('d-flex').removeClass('hide-desktop');

    }else{
        $('#medias_show').removeClass('d-flex').addClass('hide-desktop');
    }
});
