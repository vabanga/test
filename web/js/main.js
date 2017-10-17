var growth = false;
var get = function(obj) {
    return document.getElementById(obj)
};
get('like').onclick = function() {
    if (growth) {
        $('#like-icon').removeClass('fa-heart-o');
        $('#like-icon').addClass('fa-heart');
        growth = false
    } else {
        $('#like-icon').removeClass('fa-heart');
        $('#like-icon').addClass('fa-heart-o');
        growth = true
    }
};



