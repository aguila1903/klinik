<!DOCTYPE html>
<html lang="tr">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>DTK Login</title>

        <!-- Bootstrap -->
        <link href="styles.css" rel="stylesheet">
        <script src="login.js"></script>
        <!--<script src="..\login\md5.js" type="text/javascript"></script>--> 
        <script src="..\login\sha512.js" type="text/javascript"></script> 
  <script src="crd\crd.js" type="text/javascript"></script> 
    </head>
    <body>
<!--        <img alt="Login">-->
        <div class="box">            
            <form>
                <br />
                <label for="name">Kullanıcı: </label>
                <input class="feld" type="text" name="benutzername" id="benutzername" />
                <br />
                <br />
                <label for="passwort">Şifre: </label>
                <input class="feld" type="password" name="passwort" id="passwort"/>
                <br />              
            </form> 
            <br/>
            <button class="button" id="btnLogin">Giriş</button>            
            <p id="antwort"></p>
        </div>


    </body>
</html>