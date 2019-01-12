<!DOCTYPE html>
<html lang="tr">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>DTK Kayıt</title>

        <!-- Bootstrap -->
        <link href="styles.css" rel="stylesheet">
        <script src="register.js"></script>
        <!--<script src="..\login\md5.js" type="text/javascript"></script>--> 
        <script src="..\login\sha512.js" type="text/javascript"></script> 
    </head>
    <body>
        <!--<img alt="Register">-->
        <div class="box">
            <form>
                <br />
                <label for="name">Kullanıcı adı: </label>
                <input class="feld" type="text" name="benutzername" id="benutzername" size="20"/>
                <br />
                <br />
                <label for="passwort">Şifre: </label>
                <input class="feld" type="password" name="passwort" id="passwort" size="20"/>
                <br />               
                <br />
                <label for="passwort2">Şifre onayla: </label>
                <input class="feld" type="password" name="passwort2" id="passwort2" size="20"/>
                <br />               
            </form> 
            <br />
            <button class="button" id="btnLogin">Login</button>      
            <p id="antwort"></p>
        </div>
    </body>
</html>

