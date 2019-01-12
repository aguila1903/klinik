<?php
session_start();
header("Cache-Control: no-cache, must-revalidate");
$host = (htmlspecialchars($_SERVER["HTTP_HOST"]));
$uri = rtrim(dirname(htmlspecialchars($_SERVER["PHP_SELF"])), "/\\");

    ?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html40/loose.dtd">
<html>
  <head>
    <title>Erdo-Login</title>
    <link rel="stylesheet" type="text/css" href="style_noadmin.css"/>
    <style type="text/css">
<!--
.Image1_nm {background:url("images/login/Image1_nm.gif") no-repeat transparent; width:300px; height:260px; }
.Image1_ov {background-image:url("images/login/Image1_nm.gif"); }
.Image1_dn {background-image:url("images/login/Image1_nm.gif"); }
.Image1_od {background-image:url("images/login/Image1_nm.gif"); }
.Image2_nm {background:url("images/login/Image2_nm.gif") no-repeat transparent; width:300px; height:260px; }
.Image2_ov {background-image:url("images/login/Image2_nm.gif"); }
.Image2_dn {background-image:url("images/login/Image2_nm.gif"); }
.Image2_od {background-image:url("images/login/Image2_nm.gif"); }
.Image3_nm {background:url("images/login/Image3_nm.png") no-repeat transparent; filter:progid:DXImageTransform.Microsoft.AlphaImageLoader(src=images/login/Image3_nm.png,sizingmethod=crop); background:expression("none"); width:750px; height:60px; }
.Image3_ov {background-image:url("images/login/Image3_nm.png"); filter:progid:DXImageTransform.Microsoft.AlphaImageLoader(src=images/login/Image3_nm.png,sizingmethod=crop); background:expression("none"); }
.Image3_dn {background-image:url("images/login/Image3_nm.png"); filter:progid:DXImageTransform.Microsoft.AlphaImageLoader(src=images/login/Image3_nm.png,sizingmethod=crop); background:expression("none"); }
.Image3_od {background-image:url("images/login/Image3_nm.png"); filter:progid:DXImageTransform.Microsoft.AlphaImageLoader(src=images/login/Image3_nm.png,sizingmethod=crop); background:expression("none"); }
.Image4_nm {background:url("images/login/Image4_nm.gif") no-repeat transparent; width:300px; height:260px; }
.Image4_ov {background-image:url("images/login/Image4_nm.gif"); }
.Image4_dn {background-image:url("images/login/Image4_nm.gif"); }
.Image4_od {background-image:url("images/login/Image4_nm.gif"); }
.Image5_nm {background:url("images/login/Image5_nm.gif") no-repeat transparent; width:320px; height:280px; }
.Image5_ov {background-image:url("images/login/Image5_nm.gif"); }
.Image5_dn {background-image:url("images/login/Image5_nm.gif"); }
.Image5_od {background-image:url("images/login/Image5_nm.gif"); }
.Image6_nm {background:url("images/login/Image6_nm.png") no-repeat transparent; filter:progid:DXImageTransform.Microsoft.AlphaImageLoader(src=images/login/Image6_nm.png,sizingmethod=crop); background:expression("none"); width:300px; height:120px; }
.Image6_ov {background-image:url("images/login/Image6_nm.png"); filter:progid:DXImageTransform.Microsoft.AlphaImageLoader(src=images/login/Image6_nm.png,sizingmethod=crop); background:expression("none"); }
.Image6_dn {background-image:url("images/login/Image6_nm.png"); filter:progid:DXImageTransform.Microsoft.AlphaImageLoader(src=images/login/Image6_nm.png,sizingmethod=crop); background:expression("none"); }
.Image6_od {background-image:url("images/login/Image6_nm.png"); filter:progid:DXImageTransform.Microsoft.AlphaImageLoader(src=images/login/Image6_nm.png,sizingmethod=crop); background:expression("none"); }
.Image7_nm {background:url("images/login/Image7_nm.png") no-repeat transparent; filter:progid:DXImageTransform.Microsoft.AlphaImageLoader(src=images/login/Image7_nm.png,sizingmethod=crop); background:expression("none"); width:96px; height:96px; }
.Image7_ov {background-image:url("images/login/Image7_nm.png"); filter:progid:DXImageTransform.Microsoft.AlphaImageLoader(src=images/login/Image7_nm.png,sizingmethod=crop); background:expression("none"); }
.Image7_dn {background-image:url("images/login/Image7_nm.png"); filter:progid:DXImageTransform.Microsoft.AlphaImageLoader(src=images/login/Image7_nm.png,sizingmethod=crop); background:expression("none"); }
.Image7_od {background-image:url("images/login/Image7_nm.png"); filter:progid:DXImageTransform.Microsoft.AlphaImageLoader(src=images/login/Image7_nm.png,sizingmethod=crop); background:expression("none"); }
.Link5_nm {background:url("images/login/Link5_nm.png") no-repeat transparent; filter:progid:DXImageTransform.Microsoft.AlphaImageLoader(src=images/login/Link5_nm.png,sizingmethod=crop); background:expression("none"); width:280px; height:128px; }
.Link5_ov {background-image:url("images/login/Link5_nm.png"); filter:progid:DXImageTransform.Microsoft.AlphaImageLoader(src=images/login/Link5_nm.png,sizingmethod=crop); background:expression("none"); }
.Link5_dn {background-image:url("images/login/Link5_nm.png"); filter:progid:DXImageTransform.Microsoft.AlphaImageLoader(src=images/login/Link5_nm.png,sizingmethod=crop); background:expression("none"); }
.Link5_od {background-image:url("images/login/Link5_nm.png"); filter:progid:DXImageTransform.Microsoft.AlphaImageLoader(src=images/login/Link5_nm.png,sizingmethod=crop); background:expression("none"); }
-->
    </style>
  </head>
  <body onload="preload('images/login/Image1_nm.gif','images/login/Image2_nm.gif','images/login/Image3_nm.png','images/login/Image4_nm.gif','images/login/Image5_nm.gif','images/login/Image6_nm.png','images/login/Image7_nm.png','images/login/Link5_nm.png')" vlink="#0000FF" class="index_nm" id="index">
    <script type="text/javascript" src="erdo_noadmin.js"></script>
    <div id="Panel1" style="width:800px; height:763px; position:absolute; overflow:hidden; top:0px; left:0px; right:0px; margin-left:auto; margin-right:auto; ">
      
      <div class="Panel5_nm" id="Panel5" style="background:none; width:750px; height:590px; position:absolute; overflow:hidden; top:50px; left:0px; right:0px; margin-left:auto; margin-right:auto; ">
        <div class="fill" style="background:url('images/login/Panel5_noadmin_middle.gif') repeat 0% 0%; z-index:-99;  left:0px; right:0px; top:0px; bottom:0px;"></div>
        <div noframe="true" class="noadmin_nm" id="noadmin" style="position:absolute; overflow:hidden; top:0px; left:0px; bottom:60px; right:0px; ">
          <div cbase="Image2" class="Image2_nm" id="Image2" style="position:absolute; overflow:hidden; top:0px; left:0px; "></div>
          <div cbase="Image1" class="Image1_nm" id="Image1" style="position:absolute; overflow:hidden; top:0px; left:450px; "></div>
          <div cbase="Image4" class="Image4_nm" id="Image4" style="position:absolute; overflow:hidden; top:301px; left:0px; "></div>
          <div cbase="Image5" class="Image5_nm" id="Image5" style="position:absolute; overflow:hidden; top:288px; left:434px; "></div>
          <div cbase="Image7" class="Image7_nm" id="Image7" style="position:absolute; overflow:hidden; top:8px; left:328px; "></div>
          <div id="Label4" style="background:url('images/login/Label4_nm.png') no-repeat transparent; filter:progid:DXImageTransform.Microsoft.AlphaImageLoader(src=images/login/Label4_nm.png,sizingmethod=crop); background:expression('none'); width:624px; height:76px; position:absolute; overflow:hidden; top:224px; left:88px; "></div>
          <div cbase="Link5" class="Link5_nm" id="Link5" style="position:absolute; overflow:hidden; top:398px; left:248px; "></div>
        </div>
        <div class="Panel7_nm" id="Panel7" style="height:59px; position:absolute; overflow:hidden; left:0px; bottom:0px; right:0px; ">
          <div cbase="Image3" class="Image3_nm" id="Image3" style="position:absolute; overflow:hidden; top:-1px; left:0px; "></div>
          <div class="Label32_nm" id="Label32" style="width:97px; height:18px; position:absolute; overflow:hidden; left:0px; right:0px; font-family:'Monotype Corsiva'; margin-left:auto; margin-right:auto; top:50%; margin-top:-9px; ">&#xA9; Ekinciler</div>
        </div>
      </div>
    </div>
    <script type="text/javascript">dhtml();</script>
  </body>
</html>