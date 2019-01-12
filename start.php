<?php
session_start();
require_once('db_psw.php');
header("Cache-Control: no-cache, must-revalidate");
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Datum in der Vergangenheit
$host = (htmlspecialchars($_SERVER["HTTP_HOST"]));
$uri = rtrim(dirname(htmlspecialchars($_SERVER["PHP_SELF"])), "/\\");

if (isset($_SESSION["login"]) && $_SESSION["login"] == login && $_SESSION["admin"] == admin) {
	//  echo ("Hallo: " . $_SESSION["benutzer"]);
    ?>
    <HTML>
        <HEAD>
            <meta charset="UTF-8">
            <meta http-equiv="X-UA-Compatible" content="IE=edge" >
            <meta name="viewport" content="width = device-width, initial-scale = 1">
            <TITLE>DOGAL TEDAVILER KLINIGI</TITLE>
            <link href="main.css" rel="stylesheet">
            <SCRIPT type="text/javascript">
                  var isomorphicDir = "../include/isomorphic_tr/";</SCRIPT>
            <SCRIPT type="text/javascript" SRC="../include/isomorphic_tr/system/modules/ISC_Core.js?locale=tr">
            </SCRIPT>
            <SCRIPT type="text/javascript" SRC="../include/isomorphic_tr/system/modules/ISC_Foundation.js?locale=tr">
            </SCRIPT>
            <SCRIPT type="text/javascript" SRC="../include/isomorphic_tr/system/modules/ISC_Containers.js?locale=tr">
            </SCRIPT>
            <SCRIPT type="text/javascript" SRC="../include/isomorphic_tr/system/modules/ISC_Grids.js?locale=tr">
            </SCRIPT>
            <SCRIPT type="text/javascript" SRC="../include/isomorphic_tr/system/modules/ISC_Forms.js?locale=tr">
            </SCRIPT>
            <SCRIPT type="text/javascript" SRC="../include/isomorphic_tr/system/modules/ISC_DataBinding.js?locale=tr">
            </SCRIPT>
            <SCRIPT type="text/javascript" SRC="../include/isomorphic_tr/system/modules/ISC_Calendar.js?locale=tr">
            </SCRIPT>
            <SCRIPT type="text/javascript" SRC="../include/isomorphic_tr/system/modules/ISC_RichTextEditor.js?locale=tr">
            </SCRIPT>
            <SCRIPT type="text/javascript" SRC="../include/isomorphic_tr/skins/EnterpriseBlue/load_skin.js?locale=tr">
            </SCRIPT>
            <SCRIPT type="text/javascript" SRC="../include/isomorphic_tr/locales/frameworkMessages_tr_TR.properties">
            </SCRIPT>
<!--            <script src="js/amcharts.js" type="text/javascript"></script>-->
            <link rel='stylesheet' href='calendar/fullcalendar.css' />
            <script src='lib/jquery.min.js'></script>
            <script src='lib/moment.js'></script>
            <script src='calendar/fullcalendar.js'></script> 
            <script src='calendar/locale-all.js'></script>
            <script src="..\login\sha512.js" type="text/javascript"></script> 
            <!--<script src="takvim.js" type="text/javascript"></script>-->
                        <!--<script src="amcharts_3/amcharts.js" type="text/javascript"></script>-->

        </HEAD>
        <BODY>
            <SCRIPT type="text/javascript">
			sidAdmin = '<?php echo $_SESSION["admin"]?>';
			admin = '<?php echo admin ?>';
			user = '<?php echo $_SESSION["benutzer"] ?>';
            </script>
            <noscript>Bitte aktivieren Sie JavaScript in Ihrem Browser, ansonsten kann diese Seite nicht korrekt angezeigt werden.</noscript><!--Info-Meldung falls JavaScript nicht aktiviert ist-->
            <script src="functions.js" type="text/javascript"></script> 
            <script src="start.js" type="text/javascript"></script>    
        </BODY>
    </HTML>
    <?php
} else {
    header("Location: http://$host$uri/login.php");
}
?>


