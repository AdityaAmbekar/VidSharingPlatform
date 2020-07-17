function postComment(button, postedBy, videoId, replyTo, containerClass){

    var textArea = $(button).siblings("textArea");
    var commentText = textArea.val();
    textArea.val("");

    if(commentText){

        $.post("ajax/postComment.php", {commentText: commentText, postedBy: postedBy, videoId: videoId, responseTo: replyTo})
        .done(function(comment){
            
            if(!replyTo){
                $("." + containerClass).prepend(comment);
            }

            else{
                $(button).parent().siblings("." + containerClass).append(comment);
            }
            
        });
    }
    else{
        alert("cant post empty comment");
    }

}

function toggleReply(button){

    var parent = $(button).closest(".itemContainer");
    var commentForm = parent.find(".commentForm").first();

    commentForm.toggleClass("hidden");
}

function likeComment(commentId, button, videoId){

    $.post("ajax/likeComment.php", {commentId: commentId, videoId: videoId})
    .done(function(data) { 
        
        var likeButton  = $(button);
        var dislikeButton  = $(button).siblings(".dislikeButton");

        likeButton.addClass("active");
        dislikeButton.removeClass("active");
        
        var result = JSON.parse(data);
        updateLikesValue(likeButton.find(".text"), result.likes);
        updateLikesValue(dislikeButton.find(".text"), result.dislikes);

        if(result.likes < 0){
            likeButton.removeClass("active");
            likeButton.find("img:first").attr("src", "assets/images/icons/thumb-up.png");
        }
        else{
            likeButton.find("img:first").attr("src", "assets/images/icons/thumb-up-active.png");
        }

        dislikeButton.find("img:first").attr("src", "assets/images/icons/thumb-down.png");

    });


}

function dislikeComment(commentId, button, videoId){

    $.post("ajax/dislikeComment.php", {commentId: commentId, videoId: videoId})
    .done(function(data) { 
        
        var likeButton  = $(button);
        var dislikeButton  = $(button).siblings(".likeButton");

        dislikeButton.addClass("active");
        likeButton.removeClass("active");
          
        var result = JSON.parse(data);
        updateLikesValue(likeButton.find(".text"), result.likes);
        updateLikesValue(dislikeButton.find(".text"), result.dislikes);

        if(result.dislikes < 0){
            dislikeButton.removeClass("active");
            dislikeButton.find("img:first").attr("src", "assets/images/icons/thumb-up.png");
        }
        else{
            dislikeButton.find("img:first").attr("src", "assets/images/icons/thumb-up-active.png");
        }

        likeButton.find("img:first").attr("src", "assets/images/icons/thumb-down.png");

    });

}

function updateLikesValue(element, num){

    var likesCountVal = element.text() || 0;
    element.text(parseInt(likesCountVal) + parseInt(num));

}

function getReplies(commentId, button, videoId){

    $.post("ajax/getCommentReplies.php", {commentId: commentId, videoId: videoId})
    .done(function(comments) {

        var replies = $("<div>").addClass("repliesSection");
        replies.append(comments);

        $(button).replaceWith(replies);
    });
}