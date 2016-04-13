$(document).ready(function(){
    $("#keyword").on("keydown",function(event){
       if(event.keyCode==13)
           search();
    });
});

function collapseForm(){
    $(".search-form").animate({"margin-top": "4%", "margin-bottom": "4%"}, 600);
    $("#hiRateSB").hide(600);
    $("#newComm").hide(600)
}

function addButton()
{
    $(".search-item").on("mouseenter", function(){
        $(this).find(".search-item-detail").show();
        $(this).find(".search-item-add").show();
    });
    $(".search-item").on("mouseleave", function(){
        $(this).find(".search-item-detail").hide();
        $(this).find(".search-item-add").hide();
    });
}