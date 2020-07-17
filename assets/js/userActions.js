function subscribe(userTo, userFrom, button){

    if(userTo == userFrom){
        alert("cant subs to own channel");
        return;
    }

    $.post("ajax/subscribe.php", {userTo: userTo, userFrom: userFrom})
    .done(function(){

        $(button).toggleClass("subscribe unsubscribe");

        var buttonText = $(button).hasClass("subscribe") ? "SUBSCRIBE" : "SUBSCRIBED" ; 
        $(button).text(buttonText);                                              
    });

}