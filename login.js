var xhr = new XMLHttpRequest();
var errColor = '#EE2C2C';
var normColor = '#F0F0F0';
var path = 'http://' + document.location.host + '/klinik/';


function sendLogin()
{

    var user = document.forms[0][0].value;
    var pw = document.forms[0][1].value;

    if (user == "")
    {
        document.getElementById("benutzername").style = "background-color: " + errColor;
        document.getElementById("antwort").innerHTML = 'Lütfen Kullanıcı adını giriniz';
        return;
    }
    if (pw == "")
    {
        document.getElementById("passwort").style = "background-color: " + errColor;
        document.getElementById("antwort").innerHTML = 'Lütfen Şifreyi giriniz';
        return;
    }

    pw = hex_sha512(pw + user);    
    var params = "benutzername=" + user + "&passwort=" + pw;
    xhr.open("POST", "api/userDS.php", true);
    xhr.responseType = "json";
    xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
    xhr.onreadystatechange = handleResponse;
    xhr.send(params);
}

function handleResponse()
{
    var rueckmeldung;
    // rueckmeldung = JSON.parse(xhr.response);
    rueckmeldung = xhr.response;

    if (xhr.readyState == 4)
    {
        if (rueckmeldung["ergebnis"] == erg && rueckmeldung["status"] == stat)
        {
            document.getElementById("antwort").innerHTML = rueckmeldung["text"];
            window.open(path + 'start.php', '_self', false);
        } else
        {
            document.getElementById("antwort").innerHTML = rueckmeldung["text"];
            switch (rueckmeldung["ergebnis"])
            {
                case 4: // Kein Benutzername
                    document.getElementById("benutzername").style = "background-color: " + errColor;
                    break;
                case 5: // Kein Passwort
                    document.getElementById("passwort").style = "background-color: " + errColor;
                    break;
            }
        }
    }
}

function init()
{
    const passwd = document.getElementById("passwort");
    const user = document.getElementById("benutzername");
    var login = document.getElementById("btnLogin");
    // var register = document.getElementById("btnRegister");
    try {
        if (typeof login !== 'undefined' && typeof login !== 'null')
        {
            login.onclick = sendLogin;
        }
    } catch (err) {
        document.getElementById("antwort").innerHTML = err.message;
    }
    // register.onclick = function () {
    // window.open('http://' + document.location.host + '/klinik/register.php', '_self', false);
    // };
    // document.images[0].src = "bilder/Panel1_nm.gif";

    document.addEventListener("keypress", function ()
    {
        document.getElementById("passwort").style = "background-color: " + normColor;
        document.getElementById("benutzername").style = "background-color: " + normColor;
    });
    document.addEventListener("change", function ()
    {
        document.getElementById("passwort").style = "background-color: " + normColor;
        document.getElementById("benutzername").style = "background-color: " + normColor;
    });
    passwd.addEventListener("keydown", function (event)
    {
        if (event.key === "Enter")
        {
            event.preventDefault();
            sendLogin();
        }
    });

    user.addEventListener("keydown", function (event)
    {
        if (event.key === "Enter")
        {
            event.preventDefault();
            sendLogin();
        }
    });
}
window.onload = init;