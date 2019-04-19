/**
 * Created by namdd on 10/13/2016.
 */
function setCookie(cname, cvalue, exdays) {
    var d = new Date();
    d.setTime(d.getTime() + (exdays*24*60*60*1000));
    var expires = "expires="+ d.toUTCString();
    document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
}

function getCookie(cname) {
    var name = cname + "=";
    var ca = document.cookie.split(';');
    for(var i = 0; i <ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0)==' ') {
            c = c.substring(1);
        }
        if (c.indexOf(name) == 0) {
            return c.substring(name.length,c.length);
        }
    }
    return "";
}
function poll(poll_id){

    var vote_id = '0';
    $(".poll_option").each(function(){
        if($(this).attr('checked') == 'checked'){
            vote_id = vote_id + ',' + $(this).val();
        }
    })

    $.post("ajax.php?fnc=vote_poll&path=news", {'poll_id': poll_id,'option':vote_id},
        function (data) {
            setCookie('poll',poll_id);
            alert('Cảm ơn bạn đã bình chọn!');
            $("#poll_submit").hide();

        }
    );
}

function poll_result(poll_id) {
    window.open("http://congly.vn/ajax.php?fnc=result_poll&path=news&poll_id=" + poll_id);
}