$(document).ready(function() {

    $(".navShowHide").on("click",function() {
        
            var main = $("#mainSectionContainer");
            var nav = $("#sideNavContainer");

            if(main.hasClass("leftPadding")){
                nav.hide();
            }
            else{
                nav.show();
            }

            main.toggleClass("leftPadding");
    });

});  //can be in action only when the whole page is loaded otherwise nai chalta

function notSignedIn(){

    alert("You must signin before !!");
}