<?php session_start(); ?>
<?php setcookie('testCookie','testCookieValue',time()+3600); ?>
<!DOCTYPE html>
<html lang="en">

<head>

</head>

<script>
    /**
        쿠키설정
     */
    function setCookie(name,value,day){
        var date = new Date();
        date.setDate(date.getDate()+day);

        var willCookie ='';
        willCookie += name + '=' + encodeURIComponent(value) +';';
        willCookie +='expires='+date.toUTCString()+'';

        document.cookie = willCookie;
    }

    /**
        쿠키 읽기
     */
    function getCookie(name){
        var cookies = document.cookie.split(';');
        for(var i in cookies){
            if(cookies[i].search(name)!=-1){
                return decodeURIComponent(cookies[i].replace(name+'=',''));
            }
        }
    }

    /**
        쿠키 삭제
     */
    function removeCookie(name){
        var date = new Date();
        date.setDate(date.getDate()-1);

        var willCookie ='';
        willCookie += name + '=remove;';
        willCookie +='expires='+date.toUTCString();

        document.cookie = willCookie;
    }
</script>

<body>
    쿠키<br>
    <?php
    echo $_COOKIE['testCookie'];
    ?>

    <br><br><br>
    
    <button type="button" onclick="setCookie('abc','123',1)">setCookie</button>
    <button type="button" onclick="alert(getCookie('abc'))">getCookie</button>
    <button type="button" onclick="removeCookie('abc')">removeCookie</button>

</body>

</html>