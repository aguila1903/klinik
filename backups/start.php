
<?php
session_start();
include 'api/db_psw.php';
header('Cache-Control: no-store, no-cache, must-revalidate');
$host = (htmlspecialchars($_SERVER["HTTP_HOST"]));
$uri = rtrim(dirname(htmlspecialchars($_SERVER["PHP_SELF"])), "/\\");

if (isset($_SESSION["login"]) && $_SESSION["login"] == login /* && $_SESSION["admin"] == admin */) {
    //  echo ("Hallo: " . $_SESSION["benutzer"]);
    ?>
    <HTML>
        <HEAD>
            <META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=UTF-8">
            <meta http-equiv="X-UA-Compatible" content="IE=8" >
            <TITLE>DOGAL TEDAVILER MERKEZI</TITLE>
            <STYLE type="text/css">
                .testStyle {
                    background-color:#FA8772;
                    border: 1px solid #004D99;
                    -webkit-border-radius: 10px;
                    -moz-border-radius: 10px;
                    border-radius: 10px;
                    padding: 0px;
                }

                .testStyleHeader {
                    font-family:Verdana,Bitstream Vera Sans,sans-serif; font-size: 9px;
                    color: white;
                    background-color:#B22222;
                    -webkit-border-radius: 9px;
                    -moz-border-radius: 9px;
                    border-radius: 9px;
                    padding: 0px;
                }

                .testStyleBody {
                    font-family:Verdana,Bitstream Vera Sans,sans-serif; font-size: 9px;
                    color:black;
                    background-color: #FA8072;
                    padding: 3px;
                }

                .testStyleResizer {
                    border-top:1px solid white;
                    border-bottom: 1px solid white;
                }
                .commonName {
                    font:red;
                    color: red;
                    margin-bottom: 40px;
                }
                .simpleCell,
                .simpleCellSelected,
                .simpleCellOver,
                .simpleCellSelectedOver {
                    font-family:Verdana,Bitstream Vera Sans,sans-serif; font-size:11px;
                    color:black;
                }

                .simpleCellDisabled {
                    font-family:Verdana,Bitstream Vera Sans,sans-serif; font-size:11px;
                    color:#808080;
                }

            </STYLE>

            <SCRIPT type="text/javascript">
                    var isomorphicDir = "isomorphic/";</SCRIPT>
            <SCRIPT type="text/javascript" SRC="isomorphic/system/modules/ISC_Core.js?locale=tr_TR">
            </SCRIPT>
            <SCRIPT type="text/javascript" SRC="isomorphic/system/modules/ISC_Foundation.js?locale=tr_TR">
            </SCRIPT>
            <SCRIPT type="text/javascript" SRC="isomorphic/system/modules/ISC_Containers.js?locale=tr_TR">
            </SCRIPT>
            <SCRIPT type="text/javascript" SRC="isomorphic/system/modules/ISC_Grids.js?locale=tr_TR">
            </SCRIPT>
            <SCRIPT type="text/javascript" SRC="isomorphic/system/modules/ISC_Forms.js?locale=tr_TR">
            </SCRIPT>
            <SCRIPT type="text/javascript" SRC="isomorphic/system/modules/ISC_DataBinding.js?locale=tr_TR">
            </SCRIPT>
            <SCRIPT type="text/javascript" SRC="isomorphic/system/modules/ISC_Calendar.js?locale=tr_TR">
            </SCRIPT>
            <SCRIPT type="text/javascript" SRC="isomorphic/system/modules/ISC_RichTextEditor.js?locale=tr_TR">
            </SCRIPT>
            <SCRIPT type="text/javascript" SRC="isomorphic/skins/EnterpriseBlue/load_skin.js?locale=tr_TR">
            </SCRIPT>
            <SCRIPT type="text/javascript" SRC="isomorphic/locales/frameworkMessages_tr_TR.properties">
            </SCRIPT>
            <SCRIPT type="text/javascript" SRC="functions.js">
            </SCRIPT>
            <script src="WdCalendar/src/jquery.js" type="text/javascript"></script>  
        </HEAD>
        <BODY>
            <noscript>Bitte aktivieren Sie JavaScript in Ihrem Browser, ansonsten kann diese Seite nicht korrekt angezeigt werden.</noscript><!--Info-Meldung falls JavaScript nicht aktiviert ist-->
            <SCRIPT type="text/javascript">
                // SeitenTitel
                Page.setTitle("DOGAL TEDAVILER MERKEZI");
                // kein Autodraw für TÜM
                isc.setAutoDraw(false);

                // Termin
                var setEndTime = function (time, form_, t) {
                    var st = time.toString().substring(16, 21).replace(":", "");
                    if (t == "start") {
                        var stInt = parseInt(st) + 100;
                        form_.getField("endTime").setValue(stInt.toString());
                    }
    //                    else {
    //                        var stInt = parseInt(st) - 100;
    //                        form_.getField("startTime").setValue(stInt.toString());
    //                    }
                };
                // Berechnungen
                var preisFunction = function (form) {
                    if (form.getField("prod_kz").getValue()) {
                        RPCManager.send("", function (rpcResponse, data, rpcRequest) {
                            var _data = isc.JSON.decode(data); // Daten aus dem PHP (rpcResponse)
                            if (_data.response.status === 0) {  // Durum 0 bedeutet Keine Hata
                                if (form.getField("preis_kat").getValue() == "4") {
                                    if (form.getField("brutto_preis_").getValue()) {
                                        bruttoPreis = form.getField("brutto_preis_").getValue().toString().replace(",", ".");
                                    } else {
                                        bruttoPreis = 0.00;
                                    }
                                    if (form.getField("mwst_").getValue()) {
                                        MWST = form.getField("mwst_").getValue().toString().replace(",", ".");
                                    } else {
                                        MWST = 18.00;
                                    }
                                } else {
                                    bruttoPreis = parseFloat(_data.response.data["brutto_preis"]);
                                    MWST = parseFloat(_data.response.data["mwst"]);
                                }

                                gesamtPreis = bruttoPreis * form.getField("menge").getValue();
                                $mwst1 = 100 - MWST;
                                $mwst2 = bruttoPreis * $mwst1;
                                $mwst3 = $mwst2 / 100;
                                $mwst4 = bruttoPreis - $mwst3;
                                $mwstToplam = $mwst4 * form.getField("menge").getValue();

                                form.getField("mwst").setValue(MWST.toString().replace(".", ","));
                                form.getField("brutto_preis").setValue(rundung(bruttoPreis, 2).toString().replace(".", ","));
                                form.getField("mwst_gesamtpr").setValue(rundung($mwstToplam, 2).toString().replace(".", ","));
                                form.getField("gesamtpr_brutto").setValue(rundung(gesamtPreis, 2).toString().replace(".", ","));

                            } else {

                                form.setErrors(_data.response.errors, true);
                                var _errors = form.getErrors();
                                for (var i in _errors)
                                {
                                    isc.say("<b>Hata! </br>" + (_errors [i]) + "</b>");
                                }
                            }
                        }, {// Übergabe der Parameter
                            actionURL: "api/preisKatLesen.php",
                            httpMethod: "POST",
                            contentType: "application/x-www-form-urlencoded",
                            useSimpleHttp: true,
                            params: {
                                prod_kz: form.getField("prod_kz").getValue(),
                                preis_kat: form.getField("preis_kat").getValue()
                            }

                        }); //Ende RPC
                    }
                };



                /*
                 * *********************** STYLES **********************************************
                 * =============================================================================
                 */


                // Farbe
                titleLableColor = "#FFFFFF";
                suchFelderColor = "#FFFFFF";
                keinBildColor = "#209B20";
                userFontColor = "#4D4D4D";
                anzahlLabelColor = "#D91D15";
                suchFelderDropDownColor = "#FFFFFF";


                // Schriftgröße
                titleLableFontSize = "30px";
                keinBildFontSize = "19px";
                suchFelderFontSize = "19px";
                anzahlLabelFontSize = "19px";
                userFontSize = "9px";
                suchFelderDropDownFontSize = "12px";


                // Schriftart
                titleLableFontFamily = "Verdana, Calibri";
                suchFelderFontFamily = "Verdana, Calibri";
                keinBildFontFamily = "Verdana, Calibri";
                anzahlLabelFontFamily = "Verdana, Calibri";
                userFontFamily = "Verdana, Calibri";
                suchFelderDropDownFontFamily = "Verdana, Calibri";


                // Benutzeroberflächen Farbe

                guiColor = "welcome.jpg";
                appFolder = "klinik";

                domain = location.host;
                user = "<?php echo $_SESSION["benutzer"] ?>";
                admin = "<?php echo $_SESSION["admin"] ?>";
                admin_ = "<?php echo admin ?>";
                calendar_ = '/' + appFolder + '/wdCalendar/takvim.php';

                // ICONS
                userIcon = "logos/dtm_user.png";

                var CatTree_LogoutUserLabelWidth = 231;
                /*
                 * *********************** ANFANG LAYOUT ***************************************
                 * =============================================================================
                 */


                isc.TreeGrid.create({
                    ID: "CategoryTree",
                    // customize appearance
                    width: 200,
                    height: "100%",
                    showHeader: false,
                    count: 0,
                    nodeIcon: "famfam/box.png",
                    folderIcon: "famfam/brick.png",
                    showOpenIcons: false,
                    showDropIcons: false,
                    closedIconSuffix: "",
                    data: isc.Tree.create({
                        modelType: "parent",
                        nameProperty: "Name",
                        idField: "Id", autoOpenRoot: true,
                        parentIdField: "parentId",
                        data: [
                            //                                {
                            //                                    Id: "10",
                            //                                    parentId: "1",
                            //                                    Name: "",
                            //                                    isFolder: false,
                            //                                    icon: "web/32/database.png"
                            //                                },
                            {
                                Id: "1013",
                                parentId: "10",
                                Name: "Randevular",
                                isFolder: false,
                                icon: "web/32/calendar.png"
                            }, {
                                Id: "1111",
                                parentId: "10",
                                Name: "Randevu oluşturma",
                                isFolder: false,
                                icon: "web/32/calendar_add.png"
                            },
                            {
                                Id: "1010",
                                parentId: "10",
                                Name: "Hastalar",
                                isFolder: false,
                                icon: "web/32/user.png"
                            },
                            {
                                Id: "1011",
                                parentId: "10",
                                Name: "Tedavi türleri",
                                isFolder: false,
                                icon: "web/32/pill.png"
                            }/*,
                             {
                             Id: "11",
                             parentId: "1",
                             Name: "Randevular",
                             isFolder: true,
                             icon: "web/32/calendar_view_day.png"
                             }*/

                            , {
                                Id: "1112",
                                parentId: "10",
                                Name: "Faturalar",
                                isFolder: false,
                                icon: "web/32/coins.png"
                            }, {
                                Id: "1012",
                                parentId: "10",
                                Name: "Kullanıcı yönetimi",
                                isFolder: false,
                                icon: "web/32/group.png"
                            }
                            // , {
                            // Id: "1113",
                            // parentId: "11",
                            // Name: "Forderungen",
                            // isFolder: false
                            // },
                            // {
                            // Id: "12",
                            // parentId: "1",
                            // Name: "Ausgaben",
                            // isFolder: true
                            // },
                            // {
                            // Id: "1210",
                            // parentId: "12",
                            // Name: "Laufende Ausgaben",
                            // isFolder: false
                            // },
                            // {
                            // Id: "13",
                            // parentId: "1",
                            // Name: "Website",
                            // isFolder: true
                            // }                           
                        ]
                    })
                    ,
                    leafClick: function (_viewer, _node, _recordNum) {
                        if (_node.Id == "1010") {
                            welcomeSite.hide();
                            VLayoutKunden.show();
                            VLayoutProdukte.hide();
                            VLayoutUser.hide();
                            VLayoutVerkaeufe.hide();
                            VLayoutBuchungen.hide();
                        } else if (_node.Id == "1011") {
                            welcomeSite.hide();
                            VLayoutKunden.hide();
                            VLayoutProdukte.show();
                            VLayoutUser.hide();
                            VLayoutVerkaeufe.hide();
                            VLayoutBuchungen.hide();
                        } else if (_node.Id == "1012") {
                            if (admin == admin_) {
                                welcomeSite.hide();
                                VLayoutKunden.hide();
                                VLayoutProdukte.hide();
                                VLayoutUser.show();
                                VLayoutVerkaeufe.hide();
                                VLayoutBuchungen.hide();
                                //                                VLayoutKalender.hide();
                            } else {
                                isc.warn("<b>Buraya girmek icin yeterice yetkiye sahip degilsiniz.</b>");
                            }
                        } else if (_node.Id == "1013") {
                            welcomeSite.show();
                            welcomeSite.setContentsURL(calendar_);
                            VLayoutKunden.hide();
                            VLayoutProdukte.hide();
                            VLayoutUser.hide();
                            VLayoutVerkaeufe.hide();
                            VLayoutBuchungen.hide();
                        } else if (_node.Id == "1111") {
                            //                            if ("<?php // echo $_SESSION["admin"]       ?>" == admin_) {
                            welcomeSite.hide();
                            VLayoutKunden.hide();
                            VLayoutProdukte.hide();
                            VLayoutUser.hide();
                            VLayoutVerkaeufe.show();
                            VLayoutBuchungen.hide();
                        } else if (_node.Id == "1112") {
                            if (admin == admin_) {
                                CategoryTree.count++;
                                welcomeSite.hide();
                                VLayoutBuchungen.show();
                                VLayoutKunden.hide();
                                VLayoutProdukte.hide();
                                VLayoutUser.hide();
                                VLayoutVerkaeufe.hide();
                                CategoryTree.firstLoadFunction(dfBuchungenZeitraum, CategoryTree.count, Sene);
                            } else {
                                isc.warn("<b>Buraya girmek icin yeterice yetkiye sahip degilsiniz.</b>");
                            }
                        } else if (_node.Id == "1210") {
                            welcomeSite.hide();
                            VLayoutKunden.hide();
                            VLayoutProdukte.hide();
                            VLayoutUser.hide();
                            VLayoutVerkaeufe.hide();
                            VLayoutBuchungen.hide();
                        }
                    },
                    firstLoadFunction: function (_DB_Form, _counter, jahr) {
                        if (_counter == 1) {// Funktion wird nur einmal ausgelöst
                            _DB_Form.clearValues();
                            _DB_Form.getField("jahr").fetchData();
                            _DB_Form.getField("jahr").setValue(jahr);
                            _DB_Form.getField("jahr").changed(_DB_Form, _DB_Form.getField("jahr"), _DB_Form.getField("jahr").getValue());
                        }
                    }

                });

                /*
                 * ***************** Çıkış und Kullanıcı Label *********************
                 * -------------------------------------------------------------
                 */
                isc.Label.create({
                    ID: "label",
                    height: 120,
                    width: "100%",
                    align: "center",
                    valign: "center",
                    backgroundColor: "#ffffff",
                    wrap: true,
                    icon: "logo.png",
                    iconWidth: 646,
                    iconHeight: 112,
                    showEdges: false,
                    contents: ""
                });

                /*
                 ***************** Çıkış Button ************************** 
                 */
                isc.ToolStripButton.create({
                    ID: "tsbLogout",
                    title: '<text style="color:#3765A4; font-size:17px; font-family:Calibri; text-decoration:none;"><b>Çıkış</b></text>',
                    width: 100,
                    height: 40,
                    padding: 5,
                    align: "left",
                    showDisabledIcon: false,
                    icon: "famfam/door_in.png",
                    prompt: "Geçerli oturumu sona erdirir",
                    hoverWidth: 100,
                    hoverDelay: 1000,
                    action: function () {
                        window.open('https://' + domain + '/' + appFolder + '/logoutDS.php', '_self', false);
                    }
                });

                /*
                 ***************** Kullanıcı Pane ALT !!! ************************** 
                 */
                isc.HTMLPane.create({
                    width: 220,
                    height: 40,
                    padding: 8,
                    align: "right",
                    valign: "center",
                    ID: "HtmlPaneUser",
                    //                    backgroundColor: "#E1E5EB",
                    contentsType: "page",
                    styleName: "exampleTextBlock",
                    contents: '<text style="color:#3765A4; font-size:20px; font-family:Calibri; text-decoration:none;"><center><b>Kullanıcı:  ' + user + '</b></center></text> '});

                isc.HLayout.create({
                    ID: "HLayoutLogoutToolStrip",
                    width: "100%",
                    height: "100%",
                    members: [
                        tsbLogout, HtmlPaneUser
                    ]
                });

                //                isc.ToolStrip.create({
                //                    ID: "tsLogoutUser",
                //                    width: 320,
                //                    height: 40,
                //                    members: [HLayoutLogoutToolStrip]});
                //
                //                isc.VLayout.create({
                //                    ID: "VLayoutLogoutLabel",
                //                    width: 320,
                //                    showResizeBar: true,
                //                    height: "100%",
                //                    members: [tsLogoutUser, CategoryTree]
                //                });

                /*
                 ***************** Website Pane ************************** 
                 */



                isc.HTMLPane.create({
                    width: "100%",
                    height: "100%",
                    padding: 10,
                    ID: "welcomeSite",
                    backgroundColor: "#E1E5EB",
                    contentsType: "page",
                    styleName: "exampleTextBlock",
                    contentsUrl: calendar_
                });



                /*
                 ***************** ANFANG RIBBONBAR USER LOGOUT ************************** 
                 */


                var typeMenu = {
                    _constructor: "Menu",
                    autoDraw: false,
                    showShadow: true,
                    shadowDepth: 10,
                    data: [
                        {title: "Document", keyTitle: "Ctrl+D", icon: "icons/16/document_plain_new.png"},
                        {title: "Picture", keyTitle: "Ctrl+P", icon: "icons/16/folder_out.png"},
                        {title: "Email", keyTitle: "Ctrl+E", icon: "icons/16/disk_blue.png"}
                    ]
                };
                function getIconButton(title, props) {
                    return isc.IconButton.create(isc.addProperties({
                        title: title,
                        icon: "pieces/16/cube_blue.png",
                        largeIcon: "pieces/48/cube_blue.png",
                        click: "isc.say(this.title + ' button clicked');"
                    }, props)
                            );
                }

                function getIconMenuButton(title, props) {
                    return isc.IconMenuButton.create(isc.addProperties({
                        title: title,
                        icon: "pieces/16/piece_blue.png",
                        largeIcon: "pieces/48/piece_blue.png",
                        click: "isc.say(this.title + ' button clicked');"
                    }, props)
                            );
                }

                isc.RibbonGroup.create({
                    ID: "logoutGroup",
                    title: "Çıkış",
                    numRows: 3,
                    rowHeight: 26,
                    colWidths: [10, 10, "*"],
                    controls: [
                        getIconButton('<text style="color:' + userFontColor + '; font-size:' + userFontSize + '; font-family:' + userFontFamily + '; text-decoration:none;">Çıkış</text>',
                                {orientation: "vertical", align: "center", colSpan: 2, largeIcon: "icons/new/logout.png", click: "tsbLogout.action()"})

                    ],
                    autoDraw: false
                });
                isc.RibbonGroup.create({
                    ID: "userGroup",
                    title: "Kullanıcı",
                    numRows: 3,
                    rowHeight: 26,
                    colWidths: [10, 10, "*"],
                    controls: [
                        getIconButton('<text style="color:' + userFontColor + '; font-size:' + userFontSize + '; font-family:' + userFontFamily + '; text-decoration:none;"><center>' + user + '</center></text> ',
                                {orientation: "vertical", align: "center", colSpan: 2, largeIcon: userIcon, click: function () {

                                    }
                                })
                    ],
                    autoDraw: false
                });
                isc.RibbonBar.create({
                    ID: "ribbonBar",
                    top: 30,
                    width: 200,
                    groupTitleAlign: "center",
                    groupTitleOrientation: "top",
                    membersMargin: 2,
                    layoutMargin: 2
                });
                isc.RibbonGroup.create({
                    ID: "backupGroup",
                    title: "Yedekleme",
                    numRows: 3,
                    rowHeight: 26,
                    colWidths: [40, "*"],
                    controls: [
                        getIconButton("Yedekle", {icon: "icons/16/disk_blue.png", click: function () {
                                tsbSaveDB.click();
                            }}),
                        getIconButton("Yükle", {icon: "famfam/folder.png", click: function () {
                                tsbLoadDB.click();
                            }}),
                        getIconButton("Güncelleme", {icon: "famfam/page_refresh.png", click: function () {
                                if (user == "sek") {
                                    wdUpdate.show();
                                } else {
                                    isc.say("Yetkili degilsiniz!");
                                }
                            }})
                    ],
                    autoDraw: false
                });
                ribbonBar.addGroup(logoutGroup, 0);
                ribbonBar.addGroup(userGroup, 1);
                ribbonBar.addGroup(backupGroup, 2);
                /*
                 ***************** ENDE RIBBONBAR USER LOGOUT ************************** 
                 */

                /*
                 ***************** TOOLSTRIP USER LOGOUT ************************** 
                 */

                isc.ToolStrip.create({
                    ID: "tsLogoutUser",
                    width: "100%",
                    height: 40,
                    members: [/*HLayoutLogoutToolStrip*/]});

                isc.VLayout.create({
                    ID: "VLayoutLogoutLabel",
                    showResizeBar: true,
                    height: "100%",
                    width: "200",
                    members: [CategoryTree]
                });


                /*
                 * *********************** ENDE LAYOUT *****************************************
                 * =============================================================================
                 */



                /*
                 * *********************** ANFANG CODE *****************************************
                 * =============================================================================
                 */


                /*
                 * ********************** Anfang DataSources *******************
                 * -------------------------------------------------------------
                 */

                isc.DataSource.create({
                    ID: "kundenDS",
                    allowAdvancedCriteria: true,
                    // serverType:"sql",
                    dataFormat: "json",
                    operationBindings: [
                        {operationType: "fetch",
                            dataURL: "api/ds/kundenDS.php"
                        }
                    ],
                    titleField: "text",
                    fields: [{
                            name: "kunden_nr",
                            title: "T.C. Kimlik No",
                            type: "text"
                        }, {
                            name: "lfd_nr",
                            title: "#",
                            type: "text",
                            primaryKey: true
                        }, {
                            name: "strasse",
                            title: "Adres",
                            type: "text"
                        }, {
                            name: "vorname",
                            title: "Isim",
                            type: "text"
                        }, {
                            name: "name",
                            title: "Soyisim",
                            type: "text"
                        },
                        {
                            name: "telefon",
                            title: "Telefon",
                            type: "phoneNumber"
                        },
                        {
                            name: "fax",
                            title: "Faks",
                            type: "text"
                        },
                        {
                            name: "email",
                            title: "E-posta",
                            type: "text"
                        },
                        {
                            name: "kommentar",
                            title: "Not",
                            type: "text"
                        },
                        {
                            name: "name_voll",
                            title: "Ad ve Soyad",
                            type: "text"
                        },
                        {
                            name: "geburtstag",
                            title: "Doğum tarihi",
                            type: "text"
                        }

                    ]
                });
                isc.DataSource.create({
                    ID: "kundenDS_Union_All",
                    allowAdvancedCriteria: true,
                    // serverType:"sql",
                    dataFormat: "json",
                    operationBindings: [
                        {operationType: "fetch",
                            dataURL: "api/ds/kundenDS_Union_All.php"
                        }
                    ],
                    titleField: "text",
                    fields: [{
                            name: "kunden_nr",
                            title: "T.C. Kimlik No",
                            type: "text"
                        }, {
                            name: "lfd_nr",
                            title: "#.",
                            type: "text",
                            primaryKey: true
                        }, {
                            name: "strasse",
                            title: "Adres",
                            type: "text"
                        }, {
                            name: "vorname",
                            title: "Isim",
                            type: "text"
                        }, {
                            name: "name",
                            title: "Soyisim",
                            type: "text"
                        },
                        {
                            name: "telefon",
                            title: "Telefon",
                            type: "phoneNumber"
                        },
                        {
                            name: "fax",
                            title: "Faks",
                            type: "text"
                        },
                        {
                            name: "email",
                            title: "E-posta",
                            type: "text"
                        },
                        {
                            name: "kommentar",
                            title: "Not",
                            type: "text"
                        },
                        {
                            name: "anzVorg",
                            title: "Sayı",
                            type: "text"
                        },
                        {
                            name: "name_voll",
                            title: "Ad ve Soyad",
                            type: "text"
                        },
                        {
                            name: "geburtstag",
                            title: "Doğum tarihi",
                            type: "text"
                        }
                    ]
                });

                isc.DataSource.create({
                    ID: "historieDS",
                    allowAdvancedCriteria: true,
                    // serverType:"sql",
                    dataFormat: "json",
                    operationBindings: [
                        {operationType: "fetch",
                            dataURL: "api/ds/historieDS.php"
                        }
                    ],
                    titleField: "text",
                    fields: [{
                            name: "lfn",
                            type: "text",
                            primaryKey: true
                        }, {
                            name: "name",
                            title: "Name",
                            type: "text"
                        }, {
                            name: "schluessel",
                            title: "ID",
                            type: "text"
                        }, {
                            name: "user",
                            title: "Kullanıcı",
                            type: "text"
                        }, {
                            name: "aenderdat",
                            title: "Tarih",
                            type: "text"
                        }, {
                            name: "feld",
                            title: "Alan",
                            type: "text"
                        }, {
                            name: "a_inhalt",
                            title: "Eski içerik",
                            type: "text"
                        }, {
                            name: "n_inhalt",
                            title: "Yeni içerik",
                            type: "text"
                        }, {
                            name: "codetext",
                            title: "İşlem",
                            type: "text"
                        }]});

                /*
                 * ******************* DS Tedavi *****************************
                 */

                isc.DataSource.create({
                    ID: "produkteDS",
                    allowAdvancedCriteria: true,
                    // serverType:"sql",
                    dataFormat: "json",
                    operationBindings: [
                        {operationType: "fetch",
                            dataURL: "api/ds/produkteDS.php"
                        }
                    ],
                    titleField: "text",
                    fields: [{
                            name: "prod_kz",
                            title: "Tedavi-No",
                            type: "text",
                            primaryKey: true
                        }, {
                            name: "bezeichnung",
                            title: "Tedavi",
                            type: "text"
                        }, {
                            name: "brutto_preis1",
                            title: "Fiyat 1",
                            type: "text"
                        }, {
                            name: "brutto_preis2",
                            title: "Fiyat 2",
                            type: "text"
                        }, /* {
                         name: "brutto_preis1",
                         title: "Brüt Fiyat 1",
                         type: "text"
                         }, {
                         name: "brutto_preis2",
                         title: "Brüt Fiyat 2",
                         type: "text"
                         },*/
                        {
                            name: "mwst",
                            title: "KDV",
                            type: "text"
                        },
                        {
                            name: "mwst2",
                            title: "KDV",
                            type: "text"
                        },
                        {
                            name: "lfd_nr",
                            title: "KDV",
                            type: "text"
                        },
                        {
                            name: "prod_bild",
                            title: "Tedavi-Bild",
                            type: "link"
                        },
                        {
                            name: "aktiv",
                            title: "Durum",
                            type: "select",
                            valueMap: {"1": "Aktif", "0": "Inaktif"}
                        }
                    ]
                });

                isc.DataSource.create({
                    ID: "mwstDS",
                    allowAdvancedCriteria: true,
                    // serverType:"sql",
                    dataFormat: "json",
                    operationBindings: [
                        {operationType: "fetch",
                            dataURL: "api/ds/mwstDS.php"
                        }
                    ],
                    titleField: "text",
                    fields: [{
                            name: "lfd_nr",
                            type: "text",
                            primaryKey: true
                        }, {
                            name: "mwst",
                            title: "KDV",
                            type: "text"
                        }]});


                /*
                 * ******************* DS Users *****************************
                 */

                isc.DataSource.create({
                    ID: "userDS",
                    allowAdvancedCriteria: true,
                    // serverType:"sql",
                    dataFormat: "json",
                    operationBindings: [
                        {operationType: "fetch",
                            dataURL: "api/ds/userDS.php"
                        }
                    ],
                    titleField: "text",
                    fields: [{
                            name: "UserID",
                            title: "UserID",
                            type: "text",
                            primaryKey: true
                        }, {
                            name: "benutzer",
                            title: "Kullanıcı",
                            type: "text"
                        }, {
                            name: "passwort",
                            title: "Şifre",
                            type: "text"
                        }, {
                            name: "admin",
                            title: "Admin",
                            type: "text",
                            valueMap: {"J": "JA", "N": "Nein"}
                        }, {
                            name: "status",
                            title: "Staus",
                            type: "text",
                            valueMap: {"O": "Engellendi", "B": "Açık"}
                        }, {
                            name: "email",
                            title: "e-Posta",
                            type: "text"
                        },
                        {
                            name: "onlineTime",
                            title: "Çevrimiçi",
                            type: "text"
                        },
                        {
                            name: "logoutTime",
                            title: "Son çıkış tarihi",
                            type: "text"
                        }, {
                            name: "loginTime",
                            title: "Giriş-Zamanı",
                            type: "text"
                        },
                        {
                            name: "loginCount",
                            title: "Login-Count",
                            type: "text"
                        },
                        {
                            name: "timeOut",
                            title: "Timeout",
                            type: "text"
                        }
                    ]
                });



                /*
                 * *********************** DS VERKÄUFE ************************* 
                 */

                isc.DataSource.create({
                    ID: "verkaeufeDS",
                    allowAdvancedCriteria: true,
                    // serverType:"sql",
                    dataFormat: "json",
                    operationBindings: [
                        {operationType: "fetch",
                            dataURL: "api/ds/verkaeufeDS.php"
                        }
                    ],
                    titleField: "text",
                    fields: [{name: "lfd_nr", type: "text", title: "#"},
                        {name: "prod_kz", type: "text", title: "Tedavi no"},
                        {name: "bezeichnung", type: "text", title: "Tedavi"},
                        {name: "name", type: "text", title: "Hasta ismi"},
                        {name: "verkauf_an", type: "text", title: "Hasta no"},
                        {name: "menge", type: "text", title: "Miktar"},
                        {name: "preis_kat", type: "text", title: "Fiyat kat."},
                        {name: "mwst", type: "float", title: "KDV"},
                        {name: "mwst_", type: "float", title: "KDV"},
                        {name: "brutto_preis", type: "float", title: "Fiyat"},
                        {name: "brutto_preis_", type: "float", title: "Fiyat"},
                        {name: "gesamtpr_brutto", type: "float", title: "Toplam tutarı (net)"},
                        {name: "mwst_gesamtpr", type: "float", title: "KDV Toplam tutarı"},
                        {name: "datum", type: "date", title: "Fatura tarihi"},
                        {name: "startTime", type: "text", title: "Başlangıç"},
                        {name: "endTime", type: "text", title: "Son"},
                        {name: "beleg_nr", type: "text", title: "Fatura no"},
                        {name: "beleg_pfad", type: "text", title: "Fatura"},
                        {name: "geburtstag", type: "text", title: "Doğum tarihi"},
                        {name: "zahlungsziel", type: "text", title: "Ödeme koşulları"},
                        {name: "bemerkung", type: "text", title: "Not"}
                    ]
                });

                isc.DataSource.create({
                    ID: "verkaeufeSucheFelderDS",
                    allowAdvancedCriteria: true,
                    // serverType:"sql",
                    dataFormat: "json",
                    operationBindings: [
                        {operationType: "fetch",
                            dataURL: "api/ds/verkaeufeSucheFelderDS.php"
                        }
                    ],
                    titleField: "text",
                    fields: [{name: "lfd_nr", type: "text", title: "#"},
                        {name: "prod_kz", type: "text", title: "Tedavi no"},
                        {name: "bezeichnung", type: "text", title: "Tedavi"},
                        {name: "name", type: "text", title: "Hasta ismi"},
                        {name: "verkauf_an", type: "text", title: "Hasta no"},
                        {name: "menge", type: "text", title: "Miktar"},
                        {name: "preis_kat", type: "text", title: "Fiyat kat."},
                        {name: "brutto_preis", type: "float", title: "Fiyat"},
                        {name: "mwst", type: "float", title: "KDV"},
                        {name: "gesamtpr_brutto", type: "float", title: "Toplam tutarı (net)"},
                        {name: "mwst_gesamtpr", type: "float", title: "KDV Toplam tutarı"},
                        {name: "gesamtpr_brutto", type: "float", title: "Toplam tutarı (brüt)"},
                        {name: "datum", type: "date", title: "Fatura tarihi"},
                        {name: "startTime", type: "text", title: "Başlangıç"},
                        {name: "endTime", type: "text", title: "Son"},
                        {name: "beleg_nr", type: "text", title: "Fatura no"},
                        {name: "beleg_pfad", type: "text", title: "Fatura"},
                        {name: "geburtstag", type: "text", title: "Doğum tarihi"},
                        {name: "zahlungsziel", type: "text", title: "Ödeme koşulları"},
                        {name: "zahlungsziel_kz", type: "text", title: "Ödeme koşulları"},
                        {name: "jahr", type: "text", title: "Sene"},
                        {name: "monat", type: "text", title: "Ay"},
                        {name: "monatAusg", type: "text", title: "Ay"}
                    ]
                });


                isc.DataSource.create({
                    ID: "verkaeufeDS_Tree",
                    allowAdvancedCriteria: true,
                    // serverType:"sql",
                    dataFormat: "json",
                    operationBindings: [
                        {operationType: "fetch",
                            dataURL: "api/ds/verkaeufeDS_Tree.php"
                        }
                    ],
                    titleField: "text",
                    fields: [{name: "anzahl", type: "text", title: "Muay. say."},
                        {name: "name", type: "text", title: "Isim"},
                        {name: "zahlungsziel", type: "text", title: "Ödeme koşulları"},
                        {name: "verkauf_an", type: "text", title: "Hasta-No."},
                        {name: "datum", type: "date", title: "Fatura tarihi"},
                        {name: "startTime", type: "text", title: "Başlangıç"},
                        {name: "endTime", type: "text", title: "Son"},
                        {name: "geburtstag", type: "text", title: "Doğum tarihi"},
                        {name: "beleg_nr", type: "text", title: "Fatura no"}
                    ]
                });

                isc.DataSource.create({
                    ID: "belegeDS",
                    allowAdvancedCriteria: true,
                    // serverType:"sql",
                    dataFormat: "json",
                    operationBindings: [
                        {operationType: "fetch",
                            dataURL: "api/ds/belegeDS.php"
                        }
                    ],
                    titleField: "text",
                    fields: [
                        {name: "beleg_nr", type: "text", title: "Fatura no"}
                    ]
                });

                isc.DataSource.create({
                    ID: "histAbrechnungDS",
                    allowAdvancedCriteria: true,
                    // serverType:"sql",
                    dataFormat: "json",
                    operationBindings: [
                        {operationType: "fetch",
                            dataURL: "api/ds/histAbrechnungDS.php"
                        }
                    ],
                    titleField: "text",
                    fields: [{
                            name: "lfn",
                            type: "text",
                            primaryKey: true
                        }, {
                            name: "lfd_nr",
                            title: "#",
                            type: "text"
                        }, {
                            name: "kunden_nr",
                            title: "Hasta no",
                            type: "text"
                        }, {
                            name: "prod_bez",
                            title: "Tedavi",
                            type: "text"
                        }, {
                            name: "prod_kz",
                            title: "Tedavi no",
                            type: "text"
                        }, {
                            name: "kunden_name",
                            title: "Hasta ismi",
                            type: "text"
                        }, {
                            name: "user",
                            title: "Kullanıcı",
                            type: "text"
                        }, {
                            name: "aenderdat",
                            title: "Tarih",
                            type: "text"
                        }, {
                            name: "feld",
                            title: "Alan",
                            type: "text"
                        }, {
                            name: "a_inhalt",
                            title: "Eski içerik",
                            type: "text"
                        }, {
                            name: "n_inhalt",
                            title: "Yeni içerik",
                            type: "text"
                        }, {
                            name: "codetext",
                            title: "İşlem",
                            type: "text"
                        }]});


                isc.DataSource.create({
                    ID: "AbrechnungSucheDS",
                    allowAdvancedCriteria: true,
                    //serverType:"sql",
                    dataFormat: "json",
                    operationBindings: [
                        {
                            operationType: "fetch",
                            dataURL: "api/ds/AbrechnungSucheDS.php",
                            dataProtocol: "postParams"
                        }
                    ],
                    transformResponse: function (dsResponse, dsRequest, jsonData) {
                        var status = isc.XMLTools.selectObjects(jsonData, "/response/status");
                        var data = isc.XMLTools.selectObjects(jsonData, "/response/data");
                        dsResponse.data = data;

                        if (status != 0) {
                            dsResponse.status = isc.RPCResponse.STATUS_VALIDATION_ERROR;
                            var errors = isc.XMLTools.selectObjects(jsonData, "/response/errors");
                            dsResponse.errors = errors;
                        } else {
                            dsResponse.startRow = 0;
                            dsResponse.endRow = data.length - 1;
                            dsResponse.totalRows = data.length;
                        }
                        //<< [1] Antwort umbauen
                    },
                    titleField: "name",
                    fields: [
                        {
                            name: "beleg_nr",
                            primaryKey: true,
                            type: "text"
                        }, {
                            name: "verkauf_an",
                            title: "Hasta no",
                            type: "text"
                        },
                        {
                            name: "kunden_name",
                            title: "Hasta ismi",
                            type: "text"
                        }, {
                            name: "gesamtpr_brutto",
                            title: "Fatura tutarı",
                            type: "text"
                        }, {
                            name: "datum",
                            type: "text",
                            title: "Fatura tarihi"
                        }, {
                            name: "anzPos",
                            type: "text",
                            title: "Muay. say."
                        }
                    ]
                });

                //-----AbrechnungSucheFelderDS-----------------------
                isc.DataSource.create({
                    ID: "AbrechnungSucheFelderDS",
                    allowAdvancedCriteria: true,
                    //serverType:"sql",
                    dataFormat: "json",
                    operationBindings: [
                        {
                            operationType: "fetch",
                            dataURL: "api/ds/AbrechnungSucheFelderDS.php",
                            dataProtocol: "postParams"
                        }
                    ],
                    transformResponse: function (dsResponse, dsRequest, jsonData) {
                        var status = isc.XMLTools.selectObjects(jsonData, "/response/status");
                        var data = isc.XMLTools.selectObjects(jsonData, "/response/data");
                        dsResponse.data = data;

                        if (status != 0) {
                            dsResponse.status = isc.RPCResponse.STATUS_VALIDATION_ERROR;
                            var errors = isc.XMLTools.selectObjects(jsonData, "/response/errors");
                            dsResponse.errors = errors;
                        } else {
                            dsResponse.startRow = 0;
                            dsResponse.endRow = data.length - 1;
                            dsResponse.totalRows = data.length;
                        }
                        //<< [1] Antwort umbauen
                    },
                    titleField: "name",
                    fields: [
                        {
                            name: "verkauf_an",
                            title: "Hasta no",
                            type: "text"
                        },
                        {
                            name: "kunden_name",
                            title: "Hasta ismi",
                            type: "text"
                        },
                        {
                            name: "prod_kz",
                            title: "Tedavi no",
                            type: "text"
                        },
                        {
                            name: "prod_bez",
                            title: "Tedavi",
                            type: "text"
                        },
                        {
                            name: "datum",
                            type: "text",
                            title: "Fatura tarihi"
                        },
                        {
                            name: "beleg_nr",
                            type: "text",
                            title: "Fatura-Nr"
                        }
                    ]
                });


                /*isc.DataSource.create({
                 ID: "calendarViewDS",
                 allowAdvancedCriteria: true,
                 //serverType:"sql",
                 dataFormat: "json",
                 operationBindings: [
                 {
                 operationType: "fetch",
                 dataURL: "api/ds/calendarDS.php",
                 dataProtocol: "postParams"
                 }
                 ],
                 titleField: "name",
                 fields: [
                 {
                 name: "eventId",
                 type: "integer",
                 primaryKey: true
                 },
                 {
                 name: "name",
                 type: "text"
                 },
                 {
                 name: "description",
                 type: "text"
                 },
                 {
                 name: "startDate",
                 type: "datetime"
                 },
                 {
                 name: "endDate",
                 type: "datetime"
                 },
                 {
                 name: "datum",
                 type: "text"
                 },
                 {
                 name: "canEdit",
                 type: "text"
                 },
                 {
                 name: "eventWindowStyle",
                 type: "text"
                 }
                 ]
                 });*/


                /*
                 * *********************** DS BUCHUNGEN ************************ 
                 */

                isc.DataSource.create({
                    ID: "buchungenHauptDS",
                    allowAdvancedCriteria: true,
                    // serverType:"sql",
                    dataFormat: "json",
                    operationBindings: [
                        {operationType: "fetch",
                            dataURL: "api/ds/buchungenHauptDS.php"
                        }
                    ],
                    titleField: "text",
                    fields: [
                        {name: "beleg_nr", type: "text", title: "Fatura no", primaryKey: true},
                        {name: "datum", type: "text", title: "Fatura tarihi"},
                        {name: "name", type: "text", title: "Hasta ismi"},
                        {name: "name_mit_knd_nr", type: "text", title: "Hasta ismi"},
                        {name: "verkauf_an", type: "text", title: "Hasta no"},
                        {name: "gesamtpr_brutto", type: "float", title: "Toplam tutarı (net)"},
                        {name: "mwst_gesamtpr", type: "float", title: "KDV Toplam tutarı"},
                        {name: "geburtstag", type: "text", title: "Doğum tarihi"},
                        {name: "gesamtpr_brutto", type: "float", title: "Toplam tutarı (brüt)"},
                        {name: "beleg_pfad", title: "Fatura",
                            type: "link",
                            linkText: isc.Canvas.imgHTML("famfam/pdf.png", 16, 16),
                            linkURLPrefix: "api/Abrechnungen/"}
                    ]
                });

                isc.DataSource.create({
                    ID: "buchungenHauptHastalarDS",
                    allowAdvancedCriteria: true,
                    // serverType:"sql",
                    dataFormat: "json",
                    operationBindings: [
                        {operationType: "fetch",
                            dataURL: "api/ds/buchungenHauptHastalarDS.php"
                        }
                    ],
                    titleField: "text",
                    fields: [
                        {name: "beleg_nr", type: "text", title: "Fatura no", primaryKey: true},
                        {name: "datum", type: "text", title: "Fatura tarihi"},
                        {name: "name", type: "text", title: "Hasta ismi"},
                        {name: "name_mit_knd_nr", type: "text", title: "Hasta ismi"},
                        {name: "verkauf_an", type: "text", title: "Hasta no"},
                        {name: "gesamtpr_brutto", type: "float", title: "Toplam tutarı"},
                        {name: "mwst_gesamtpr", type: "float", title: "KDV Toplam tutarı"},
                        {name: "geburtstag", type: "text", title: "Doğum tarihi"},
                        {name: "beleg_pfad", title: "Fatura",
                            type: "link",
                            linkText: isc.Canvas.imgHTML("famfam/pdf.png", 16, 16),
                            linkURLPrefix: "api/Abrechnungen/"}
                    ]
                });

                isc.DataSource.create({
                    ID: "buchungenDetailsDS",
                    allowAdvancedCriteria: true,
                    // serverType:"sql",
                    dataFormat: "json",
                    operationBindings: [
                        {operationType: "fetch",
                            dataURL: "api/ds/buchungenDetailsDS.php"
                        }
                    ],
                    titleField: "text",
                    fields: [
                        {name: "beleg_nr", type: "text", title: "Fatura no"},
                        {name: "lfd_nr", type: "text", title: "#"},
                        {name: "prod_kz", type: "text", title: "Tedavi no"},
                        {name: "bezeichnung", type: "text", title: "Tedavi"},
                        {name: "menge", type: "text", title: "Miktar"},
                        {name: "preis_kat", type: "text", title: "Fiyat kat."},
                        {name: "brutto_preis", type: "text", title: "Fiyat"},
                        {name: "brutto_preis_", type: "text", title: "Fiyat"},
                        {name: "mwst", type: "text", title: "KDV"},
                        {name: "mwst_", type: "text", title: "KDV"},
                        {name: "mwst_einzelpr", type: "text", title: "KDV Fiyat"}

                    ]
                });

                isc.DataSource.create({
                    ID: "jahrDS",
                    allowAdvancedCriteria: true,
                    // serverType:"sql",
                    dataFormat: "json",
                    operationBindings: [
                        {operationType: "fetch",
                            dataURL: "api/ds/jahrDS.php"
                        }
                    ],
                    titleField: "text",
                    fields: [
                        {name: "jahr", type: "text", title: "Sene"}

                    ]
                });

                isc.DataSource.create({
                    ID: "monatDS",
                    allowAdvancedCriteria: true,
                    // serverType:"sql",
                    dataFormat: "json",
                    operationBindings: [
                        {operationType: "fetch",
                            dataURL: "api/ds/monatDS.php"
                        }
                    ],
                    titleField: "text",
                    fields: [
                        {name: "monat", type: "text", title: "Ay"},
                        {name: "monatsname", type: "text", title: "Ay"}

                    ]
                });





                /*
                 * ********************** Ende DataSources *********************
                 * -------------------------------------------------------------
                 */


                /*
                 * ***************** Anfang ListGrid Hasta*********************
                 * -------------------------------------------------------------
                 */

                isc.ListGrid.create({
                    ID: "kundenListe",
                    //   header: "Daten düzenleme",
                    width: "100%", height: "100%",
                    alternateRecordStyles: true,
                    contextMenu: "",
                    dataSource: kundenDS,
                    autoFetchData: true,
                    taksit_count: 0,
                    showFilterEditor: true,
                    filterOnKeypress: true,
                    selectionType: "single",
                    showAllRecords: true,
                    canExpandRecords: false,
                    showGridSummary: true,
                    //                    showGroupSummary: true,
                    expansionMode: "details",
                    margin: 0,
                    fields: [{
                            name: "kunden_nr",
                            title: "T.C. Kimlik No",
                            showIf: "true",
                            width: 100
                        }, {
                            name: "lfd_nr",
                            type: "text",
                            showIf: "false"
                        }, {
                            name: "geburtstag",
                            type: "text",
                            showIf: "true",
                            width: 100
                        }, {
                            name: "vorname",
                            width: 120,
                            title: "Isim",
                            showIf: "true"
                        }, {
                            name: "name",
                            title: "Soyisim",
                            width: 120
                                    //                            showGridSummary: true, showGroupSummary: true, summaryFunction: "count"
                        }, {
                            name: "strasse",
                            title: "Adres",
                            width: 200
                        },
                        {
                            name: "telefon",
                            title: "Telefon",
                            width: 100
                        },
                        {
                            name: "fax",
                            title: "Faks",
                            width: 100
                        },
                        {
                            name: "email",
                            title: "e-Posta",
                            width: 150
                        }, {
                            name: "kommentar",
                            title: "Not",
                            showIf: "true",
                            width: "*"
                        }], hilites: [
                        {
                            textColor: "#000000",
                            cssText: "color:#000000;background-color:#E0E0E0;",
                            id: 0
                        }
                    ], selectionChanged: function (record, state) {
                        if (state) {
                            tsbKundenDelete.setDisabled(false);
                            tsbKundenEdit.setDisabled(false);
                        } else {
                            tsbKundenDelete.setDisabled(true);
                            tsbKundenEdit.setDisabled(true);
                        }

                    }, recordDoubleClick: function (viewer, record, recordNum, field, fieldNum, value, rawValue) {
                        dfEditKunden.editRecord(record);
                        wdEditKunden.show();
                        pgbEditKunden.setHeight(16);
                        tabHasta.selectTab(0);
                        isc.Timer.setTimeout("btnResetKundeEdit.click()", 100);

                    }
                });


                /*
                 * ***************** Ende ListGrid Hasta **********************
                 * -------------------------------------------------------------
                 */




                /*
                 * ****************** Anfang neuer Hasta ***********************
                 * -------------------------------------------------------------
                 */

                isc.Progressbar.create({
                    percentDone: 0,
                    ID: "pgbAddKunden",
                    showTitle: true,
                    title: "",
                    height: 16,
                    length: "100%"});

                isc.DynamicForm.create({
                    ID: "dfAddKunden",
                    width: "100%",
                    height: "100%",
                    kundenCount: 0,
                    colWidths: [150, "*"],
                    numCols: 2,
                    titleOrientation: "left",
                    validateOnExit: true,
                    validateOnChange: false,
                    margin: 5,
                    fields: [{
                            name: "vorname",
                            title: "Isim",
                            type: "text",
                            width: 150,
                            required: true,
                            changed: function (form, item, value) {
                                form.changeFunctionAddKunden();
                            }
                        }, {
                            name: "name",
                            title: "Soyisim",
                            type: "text",
                            width: 150,
                            required: true,
                            changed: function (form, item, value) {
                                form.changeFunctionAddKunden();
                            }
                        }, {
                            name: "kunden_nr",
                            title: "T.C. Kimlik No",
                            type: "text",
                            required: false,
                            length: 11,
                            keyPressFilter: "[0-9]",
                            validators: [{
                                    type: "lengthRange",
                                    min: 11,
                                    max: 11,
                                    stopIfFalse: false
                                }
                            ],
                            changed: function (form, item, value) {
                                form.changeFunctionAddKunden();
                            }
                        }, {
                            name: "geburtstag",
                            title: "Doğum tarihi",
                            width: 100,
                            type: "text",
                            hint: "gg.aa.YYYY",
                            showHintInField: true,
                            change: "dfEditKunden.changeFunctionEditKunden()",
                            //            colSpan: 2,
                            length: 10,
                            validators: [{
                                    type: "lengthRange",
                                    min: 0,
                                    max: 10,
                                    stopIfFalse: false
                                },
                                {
                                    type: "regexp",
                                    validateOnExit: true,
                                    expression: "^(([0-9]{2})+.(([0-9]{2})+.[0-9]{4}))|([ ])$",
                                    errorMessage: "Doğum tarihinü lütfen su sekilde giriniz 24.08.1981"
                                }
                            ]
                        },
                        {
                            name: "strasse",
                            required: false,
                            type: "textArea",
                            title: "Adres",
                            width: 250,
                            changed: function (form, item, value) {
                                form.changeFunctionAddKunden();
                            }
                        }, {
                            name: "telefon",
                            title: "Telefon",
                            type: "text",
                            required: false,
                            keyPressFilter: "[0-9]",
                            changed: function (form, item, value) {
                                form.changeFunctionAddKunden();
                            }
                        }, {
                            name: "fax",
                            title: "Fax",
                            type: "text",
                            required: false,
                            keyPressFilter: "[0-9]",
                            changed: function (form, item, value) {
                                form.changeFunctionAddKunden();
                            }
                        }, {
                            name: "email",
                            title: "e-Posta",
                            width: 250,
                            type: "text",
                            hint: "--- e-Posta giriniz ---",
                            showHintInField: true,
                            change: "dfAddKunden.changeFunctionAddKunden()",
                            //            colSpan: 2,
                            length: 128,
                            validators: [{
                                    type: "lengthRange",
                                    min: 0,
                                    max: 128,
                                    stopIfFalse: false
                                },
                                {
                                    type: "regexp",
                                    validateOnExit: true,
                                    expression: "^(([a-zA-Z0-9_.\\-+])+@(([a-zA-Z0-9\\-])+\\.)+[a-zA-Z0-9]{2,4})|([ ])$",
                                    errorMessage: "Die E-Mail-Adresse muss folgende struktur aufweisen: email@mail.de"
                                }
                            ]
                        }, {
                            type: "RowSpacer",
                            height: 10
                        },
                        {
                            name: "kommentar",
                            required: false,
                            type: "textArea",
                            title: "Not",
                            width: 250,
                            changed: function (form, item, value) {
                                form.changeFunctionAddKunden();
                            }
                        }
                    ], changeFunctionAddKunden: function () {
                        btnSpeichernKundeNeu.setDisabled(false);
                        btnResetKundeNeu.setDisabled(false);
                        btnCloseKundeNeu.setTitle("İptal et");
                        btnCloseKundeNeu.setIcon("famfam/cancel.png");
                    }
                });

                isc.IButton.create({
                    ID: "btnCloseKundeNeu",
                    type: "button",
                    disabled: false,
                    icon: "famfam/door_in.png",
                    name: "btnCloseKundeNeu",
                    showDisabledIcon: false,
                    title: "Kapat", width: 100, //Neuen Film anlegen
                    click: function () {
                        if (btnCloseKundeNeu.getTitle() == "İptal et") {
                            isc.ask("Gerçekten iptal etmek istiyor musunuz? Kaydedilmemiş veriler kaybolabilir.", function (value) {
                                if (value) {
                                    wdAddKunden.hide();
                                }
                            }, {title: "İşlem iptal?"});
                        } else {
                            wdAddKunden.hide();
                        }
                    }});

                isc.IButton.create({
                    ID: "btnSpeichernKundeNeu",
                    type: "button",
                    disabled: true,
                    showDisabledIcon: false,
                    icon: "famfam/database_save.png",
                    name: "btnSpeichernKundeNeu",
                    title: "Kaydet",
                    width: 100, //Neuen Film anlegen
                    click: function () {
                        if (!dfAddKunden.validate() && dfAddKunden.hasErrors()) {
                            isc.warn("Bir hata var. Lütfen bilgileri dogru girin.");
                            return;
                        }
                        var _percent = pgbAddKunden.percentDone + parseInt(10 + (50 * Math.random()));
                        pgbAddKunden.setPercentDone(_percent);
                        pgbAddKunden.setTitle(_percent + "%");

                        RPCManager.send("", function (rpcResponse, data, rpcRequest) {
                            var _data = isc.JSON.decode(data); // Daten aus dem PHP (rpcResponse)
                            if (_data.response.status === 0) {  // Durum 0 bedeutet Keine Hata
                                kundenNr = _data.response.data[0]["lfd_nr"];
                                if(kundenListe.isDrawn()){
                                onRefresh("kundenListe");
                            }
                                btnSpeichernKundeNeu.pgbAddKundenFunction();
                                isc.Timer.setTimeout("btnSpeichernKundeNeu.findKunden()", 300);
                                //                                isc.say(kundenNr);


                            } else { // Wenn die Validierungen Hata aufweisen dann:

                                dfAddKunden.setErrors(_data.response.errors, true);
                                var _errors = dfAddKunden.getErrors();
                                for (var i in _errors)
                                {
                                    isc.say("<b>Hata! </br>" + (_errors [i]) + "</b>", function (value) {
                                        if (value) {
                                            pgbAddKunden.setTitle("");
                                            pgbAddKunden.setPercentDone(0);
                                        }
                                    });
                                }

                            }
                        }, {// Übergabe der Parameter
                            actionURL: "api/addKunden.php",
                            httpMethod: "POST",
                            contentType: "application/x-www-form-urlencoded",
                            useSimpleHttp: true,
                            params: {
                                name: dfAddKunden.getField("name").getDisplayValue(),
                                vorname: dfAddKunden.getField("vorname").getDisplayValue(),
                                kunden_nr: dfAddKunden.getField("kunden_nr").getValue(),
                                strasse: dfAddKunden.getField("strasse").getDisplayValue(),
                                fax: dfAddKunden.getField("fax").getValue(),
                                email: dfAddKunden.getField("email").getValue(),
                                telefon: dfAddKunden.getField("telefon").getValue(),
                                geburtstag: dfAddKunden.getField("geburtstag").getValue(),
                                kommentar: dfAddKunden.getField("kommentar").getValue()}

                        }); //Ende RPC
                    }, // Ende Click
                    findKunden: function () {
                        if(kundenListe.isDrawn()){
                        var newKunde = kundenListe.data.find("lfd_nr", kundenNr);
                        var index = kundenListe.getRecordIndex(newKunde);
                        //                        kundenListe.deselectAllRecords();
                        kundenListe.selectRecord(index);
                        kundenListe.scrollToRow(index);
                    }
                    },
                    pgbAddKundenFunction: function () {
                        if (pgbAddKunden.percentDone < 100) {
                            var _percent = pgbAddKunden.percentDone + parseInt(10 + (50 * Math.random()));
                            pgbAddKunden.setPercentDone(_percent); // Zufallswert wird berechnet

                            if (_percent <= 100) {
                                pgbAddKunden.setTitle(_percent + "%");
                            } //Bis 100 wird mitgezählt
                            else {
                                pgbAddKunden.setTitle("100%"); // ab 100 darf nicht mehr gezählt werden, da 100 leicht überstiegen wird.
                            }

                            isc.Timer.setTimeout("btnSpeichernKundeNeu.pgbAddKundenFunction()", 200);
                        } else {
                            if (!dfAddKunden.validate() && dfAddKunden.hasErrors()) {
                                dfAddKunden.setErrors();
                                var _errors = dfAddKunden.getErrors();
                                for (var i in _errors)
                                {
                                    isc.say("<b>Hata! </br>" + (_errors [i]) + "</b>", function (value) {
                                        if (value) {
                                            pgbAddKunden.setTitle("");
                                            pgbAddKunden.setPercentDone(0);
                                        }
                                    }); // Hier wird jeder Wert des Array-Schlüssel angezeigt und das Feld oder die Feld-Bezeichnung ist irrelevant.
                                }
                            } else {
                                isc.ask("Hasta eklendi.</br>Bir Hasta daha eklemek istermisiniz?", function (value) {
                                    if (value) {
                                        dfAddKunden.clearValues();
                                        dfAddKunden.getField("vorname").focusInItem();
                                        isc.Timer.setTimeout("btnSpeichernKundeNeu.findKunden()", 300);
                                        pgbAddKunden.setTitle("");
                                        pgbAddKunden.setPercentDone(0);
                                        btnCloseKundeNeu.setTitle("Kapat");
                                        btnCloseKundeNeu.setIcon("famfam/door_in.png");
                                    } else {
                                        dfAddKunden.clearValues();
                                        wdAddKunden.hide();
                                        btnSpeichernKundeNeu.setDisabled(true);
                                        btnResetKundeNeu.setDisabled(true);
                                        isc.Timer.setTimeout("btnSpeichernKundeNeu.findKunden()", 300);
                                        pgbAddKunden.setTitle("");
                                        pgbAddKunden.setPercentDone(0);
                                        btnCloseKundeNeu.setTitle("Kapat");
                                        btnCloseKundeNeu.setIcon("famfam/door_in.png");
                                    }

                                }, {title: "Weiteren Hasta anlegen?"});
                            }
                        }
                    }// Ende ProgressbarFunction
                });
                isc.IButton.create({
                    ID: "btnResetKundeNeu",
                    type: "button",
                    showDisabledIcon: false,
                    icon: "famfam/arrow_undo.png",
                    disabled: true,
                    name: "btnResetKundeNeu",
                    title: "Reset", width: 100, //Neuen Film anlegen
                    click: function () {
                        dfAddKunden.reset();
                        btnSpeichernKundeNeu.setDisabled(true);
                        btnResetKundeNeu.setDisabled(true);
                        btnCloseKundeNeu.setTitle("Kapat");
                        btnCloseKundeNeu.setIcon("famfam/door_in.png");
                    }});


                isc.HLayout.create({
                    ID: "HLayoutKundeNeu",
                    height: 30,
                    width: "100%",
                    align: "center",
                    members: [btnCloseKundeNeu, isc.LayoutSpacer.create({
                            width: 20
                        }), btnSpeichernKundeNeu, isc.LayoutSpacer.create({
                            width: 20
                        }), btnResetKundeNeu]});



                isc.Window.create({
                    ID: "wdAddKunden",
                    title: "Yeni Hasta ekle",
                    autoSize: true,
                    autoCenter: true,
                    showFooter: false,
                    showMinimizeButton: false,
                    showCloseButton: false,
                    width: 510,
                    headerIconDefaults: {
                        width: 16,
                        height: 16,
                        src: "famfam/user_add.png"
                    },
                    canDragReposition: true,
                    canDragResize: false,
                    showShadow: true,
                    showModalMask: true,
                    modalMaskOpacity: 10,
                    isModal: true,
                    items: [dfAddKunden, HLayoutKundeNeu, pgbAddKunden]
                });
                /*
                 * ********************** Ende neuer Hasta *********************
                 * -------------------------------------------------------------
                 */


                /*
                 * ****************** Anfang edit Hasta ************************
                 * -------------------------------------------------------------
                 */

                isc.Progressbar.create({
                    percentDone: 0,
                    ID: "pgbEditKunden",
                    showTitle: true,
                    title: "",
                    height: 16,
                    length: "100%"});

                isc.DynamicForm.create({
                    ID: "dfEditKunden",
                    width: "100%",
                    height: "100%",
                    kundenCount: 0,
                    colWidths: [150, "*"],
                    numCols: 2,
                    titleOrientation: "left",
                    validateOnExit: true,
                    validateOnChange: false,
                    margin: 5,
                    fields: [{name: "lfd_nr",
                            type: "hidden"},
                        {
                            name: "vorname",
                            title: "Isim",
                            type: "text",
                            width: 150,
                            required: true,
                            changed: function (form, item, value) {
                                form.changeFunctionEditKunden();
                            }
                        }, {
                            name: "name",
                            title: "Soyisim",
                            type: "text",
                            width: 150,
                            required: true,
                            changed: function (form, item, value) {
                                form.changeFunctionEditKunden();
                            }
                        }, {
                            name: "kunden_nr",
                            title: "T.C. Kimlik No",
                            type: "text",
                            required: false,
                            length: 11,
                            keyPressFilter: "[0-9]",
                            validators: [{
                                    type: "lengthRange",
                                    min: 11,
                                    max: 11,
                                    stopIfFalse: false
                                }
                            ],
                            changed: function (form, item, value) {
                                form.changeFunctionEditKunden();
                            }
                        }, {
                            name: "geburtstag",
                            title: "Doğum tarihi",
                            width: 100,
                            type: "text",
                            hint: "gg.aa.YYYY",
                            showHintInField: true,
                            change: "dfEditKunden.changeFunctionEditKunden()",
                            //            colSpan: 2,
                            length: 10,
                            validators: [{
                                    type: "lengthRange",
                                    min: 0,
                                    max: 10,
                                    stopIfFalse: false
                                },
                                {
                                    type: "regexp",
                                    validateOnExit: true,
                                    expression: "^(([0-9]{2})+.(([0-9]{2})+.[0-9]{4}))|([ ])$",
                                    errorMessage: "Doğum tarihinü lütfen su sekilde giriniz 24.08.1981"
                                }
                            ]
                        },
                        {
                            name: "strasse",
                            required: false,
                            type: "textArea",
                            title: "Adres",
                            width: 250,
                            changed: function (form, item, value) {
                                form.changeFunctionEditKunden();
                            }
                        }, {
                            name: "telefon",
                            title: "Telefon",
                            type: "text",
                            required: false,
                            keyPressFilter: "[0-9]",
                            changed: function (form, item, value) {
                                form.changeFunctionEditKunden();
                            }
                        }, {
                            name: "fax",
                            title: "Fax",
                            type: "text",
                            required: false,
                            keyPressFilter: "[0-9]",
                            changed: function (form, item, value) {
                                form.changeFunctionEditKunden();
                            }
                        }, {
                            name: "email",
                            title: "e-Posta",
                            width: 250,
                            type: "text",
                            hint: "--- e-Posta giriniz ---",
                            showHintInField: true,
                            change: "dfEditKunden.changeFunctionEditKunden()",
                            //            colSpan: 2,
                            length: 128,
                            validators: [{
                                    type: "lengthRange",
                                    min: 0,
                                    max: 128,
                                    stopIfFalse: false
                                },
                                {
                                    type: "regexp",
                                    validateOnExit: true,
                                    expression: "^(([a-zA-Z0-9_.\\-+])+@(([a-zA-Z0-9\\-])+\\.)+[a-zA-Z0-9]{2,4})|([ ])$",
                                    errorMessage: "Die E-Mail-Adresse muss folgende struktur aufweisen: email@mail.de"
                                }
                            ]
                        }, {
                            type: "RowSpacer",
                            height: 10
                        },
                        {
                            name: "kommentar",
                            required: false,
                            type: "textArea",
                            title: "Not",
                            width: 250,
                            changed: function (form, item, value) {
                                form.changeFunctionEditKunden();
                            }
                        }
                    ], changeFunctionEditKunden: function () {
                        btnSpeichernKundeEdit.setDisabled(false);
                        btnResetKundeEdit.setDisabled(false);
                        btnCloseKundeEdit.setTitle("İptal et");
                        btnCloseKundeEdit.setIcon("famfam/cancel.png");
                    }
                });

                isc.IButton.create({
                    ID: "btnCloseKundeEdit",
                    type: "button",
                    disabled: false,
                    icon: "famfam/door_in.png",
                    name: "btnCloseKundeEdit",
                    showDisabledIcon: false,
                    title: "Kapat", width: 100,
                    click: function () {

                        if (btnCloseKundeEdit.getTitle() == "İptal et") {
                            isc.ask("Gerçekten iptal etmek istiyor musunuz? Kaydedilmemiş veriler kaybolabilir.", function (value) {
                                if (value) {
                                    wdEditKunden.hide();
                                }
                            }, {title: "İşlem iptal?"});
                        } else {
                            wdEditKunden.hide();
                        }

                    }});

                isc.IButton.create({
                    ID: "btnSpeichernKundeEdit",
                    type: "button",
                    disabled: true,
                    showDisabledIcon: false,
                    icon: "famfam/database_save.png",
                    name: "btnSpeichernKundeEdit",
                    title: "Kaydet",
                    width: 100, //Neuen Film anlegen
                    click: function () {
                        if (!dfEditKunden.validate() && dfEditKunden.hasErrors()) {
                            isc.warn("Bir hata var. Lütfen bilgileri dogru girin.");
                            return;
                        }
                        kundenNr = kundenListe.getSelectedRecord().lfd_nr;
                        var _percent = pgbEditKunden.percentDone + parseInt(10 + (50 * Math.random()));
                        pgbEditKunden.setPercentDone(_percent);
                        pgbEditKunden.setTitle(_percent + "%");
                        RPCManager.send("", function (rpcResponse, data, rpcRequest) {
                            var _data = isc.JSON.decode(data); // Daten aus dem PHP (rpcResponse)
                            if (_data.response.status === 0) {  // Durum 0 bedeutet Keine Hata

                                onRefresh("kundenListe");
                                isc.Timer.setTimeout("btnSpeichernKundeNeu.findKunden()", 500);
                                btnSpeichernKundeEdit.pgbEditKundenFunction();

                            } else { // Wenn die Validierungen Hata aufweisen dann:

                                dfEditKunden.setErrors(_data.response.errors, true);
                                var _errors = dfEditKunden.getErrors();
                                for (var i in _errors)
                                {
                                    isc.say("<b>Hata! </br>" + (_errors [i]) + "</b>", function (value) {
                                        if (value) {
                                            pgbEditKunden.setTitle("");
                                            pgbEditKunden.setPercentDone(0);
                                        }
                                    });
                                }

                            }
                        }, {// Übergabe der Parameter
                            actionURL: "api/editKunden.php",
                            httpMethod: "POST",
                            contentType: "application/x-www-form-urlencoded",
                            useSimpleHttp: true,
                            params: {
                                lfd_nr: kundenListe.getSelectedRecord().lfd_nr,
                                kunden_nr: dfEditKunden.getField("kunden_nr").getValue(),
                                name: dfEditKunden.getField("name").getValue(),
                                vorname: dfEditKunden.getField("vorname").getValue(),
                                strasse: dfEditKunden.getField("strasse").getValue(),
                                fax: dfEditKunden.getField("fax").getValue(),
                                email: dfEditKunden.getField("email").getValue(),
                                telefon: dfEditKunden.getField("telefon").getValue(),
                                geburtstag: dfEditKunden.getField("geburtstag").getValue(),
                                kommentar: dfEditKunden.getField("kommentar").getValue()}

                        }); //Ende RPC
                    }, // Ende Click
                    pgbEditKundenFunction: function () {
                        if (pgbEditKunden.percentDone < 100) {
                            var _percent = pgbEditKunden.percentDone + parseInt(10 + (50 * Math.random()));
                            pgbEditKunden.setPercentDone(_percent); // Zufallswert wird berechnet

                            if (_percent <= 100) {
                                pgbEditKunden.setTitle(_percent + "%");
                            } //Bis 100 wird mitgezählt
                            else {
                                pgbEditKunden.setTitle("100%"); // ab 100 darf nicht mehr gezählt werden, da 100 leicht überstiegen wird.
                            }

                            isc.Timer.setTimeout("btnSpeichernKundeEdit.pgbEditKundenFunction()", 200);
                        } else {
                            if (!dfEditKunden.validate() && dfEditKunden.hasErrors()) {
                                dfEditKunden.setErrors();
                                var _errors = dfEditKunden.getErrors();
                                for (var i in _errors)
                                {
                                    isc.say("<b>Hata! </br>" + (_errors [i]) + "</b>", function (value) {
                                        if (value) {
                                            pgbEditKunden.setTitle("");
                                            pgbEditKunden.setPercentDone(0);
                                        }
                                    }); // Hier wird jeder Wert des Array-Schlüssel angezeigt und das Feld oder die Feld-Bezeichnung ist irrelevant.
                                }
                            } else {

                                dfEditKunden.clearValues();
                                wdEditKunden.hide();
                                btnSpeichernKundeEdit.setDisabled(true);
                                btnResetKundeEdit.setDisabled(true);
                                isc.Timer.setTimeout("btnSpeichernKundeNeu.findKunden()", 300);
                                pgbEditKunden.setTitle("");
                                pgbEditKunden.setPercentDone(0);
                                btnCloseKundeEdit.setTitle("Kapat");
                                btnCloseKundeEdit.setIcon("famfam/door_in.png");
                            }
                        }
                    }// Ende ProgressbarFunction
                });
                isc.IButton.create({
                    ID: "btnResetKundeEdit",
                    type: "button",
                    disabled: true,
                    icon: "famfam/arrow_undo.png",
                    showDisabledIcon: false,
                    name: "btnResetKundeEdit",
                    title: "Reset", width: 100, //Neuen Film anlegen
                    click: function () {
                        dfEditKunden.reset();
                        btnSpeichernKundeEdit.setDisabled(true);
                        btnResetKundeEdit.setDisabled(true);
                        btnCloseKundeEdit.setTitle("Kapat");
                        btnCloseKundeEdit.setIcon("famfam/door_in.png");
                    }});


                isc.HLayout.create({
                    ID: "HLayoutKundeEdit",
                    height: 30,
                    width: "100%",
                    align: "center",
                    members: [btnCloseKundeEdit, isc.LayoutSpacer.create({
                            width: 20
                        }), btnSpeichernKundeEdit, isc.LayoutSpacer.create({
                            width: 20
                        }), btnResetKundeEdit]});

                /*
                 *********************** Protokol-ListGrid ********************* 
                 */


                // define a KachelListen class (subclass of ListGrid)
                ClassFactory.defineClass("historieListe", ListGrid);

                historieListe.addProperties({
                    //                    ID: "historieListe",
                    //   header: "Daten düzenleme",
                    alternateRecordStyles: true,
                    autoFetchData: false,
                    width: 849, height: 499,
                    dataSource: historieDS,
                    taksit_count: 0,
                    selectionType: "single",
                    showAllRecords: true,
                    canExpandRecords: false,
                    expansionMode: "details",
                    margin: 0,
                    fields: [{name: "lfn",
                            showIf: "false"},
                        {name: "schluessel",
                            showIf: "true",
                            width: 45},
                        {name: "name",
                            showIf: "true",
                            width: 200},
                        {name: "user",
                            showIf: "true",
                            width: 50},
                        {name: "aenderdat",
                            showIf: "true",
                            width: 130},
                        {name: "feld",
                            showIf: "true"},
                        {name: "a_inhalt",
                            showIf: "true"},
                        {name: "n_inhalt",
                            showIf: "true"},
                        {name: "codetext",
                            showIf: "true",
                            width: "*"}]
                });

                isc.historieListe.create({
                    ID: "kundenHistListeEditWD",
                    selectionChanged: function (record, state) {
                    }
                });

                isc.historieListe.create({
                    ID: "kundenHistListeEinzelWD",
                    showFilterEditor: true,
                    filterOnKeypress: true,
                    selectionChanged: function (record, state) {
                    }
                });

                /*
                 ***************** Window Gesamt Protokol ********************** 
                 */
                isc.Window.create({
                    ID: "wdKundenHist",
                    title: "Hasta-Protokol",
                    autoSize: true,
                    autoCenter: true,
                    showFooter: false,
                    showMinimizeButton: false,
                    showCloseButton: true,
                    width: 850,
                    height: 500,
                    headerIconDefaults: {
                        width: 16,
                        height: 16,
                        src: "famfam/report.png"
                    },
                    canDragReposition: true,
                    canDragResize: false,
                    showShadow: true,
                    showModalMask: true,
                    modalMaskOpacity: 10,
                    isModal: true,
                    items: [kundenHistListeEinzelWD, kundenHistListeEditWD]
                });



                isc.ListGrid.create({
                    ID: "buchungsListe_Hastalar",
                    //   header: "Daten düzenleme",
                    width: "100%", height: "100%",
                    alternateRecordStyles: true,
                    dataSource: buchungenHauptHastalarDS,
                    contextMenu: "",
                    autoFetchData: false,
                    taksit_count: 0,
                    showFilterEditor: false,
                    filterOnKeypress: true,
                    selectionType: "single",
                    showAllRecords: true,
                    canExpandRecords: true,
                    showGridSummary: true,
                    showGroupSummary: true,
                    expansionMode: "details",
                    margin: 0,
                    //                    groupByField: ['datum'],
                    //                    groupStartOpen: "all",
                    fields: [
                        {name: "beleg_nr", type: "text", width: 80, showIf: "true"},
                        {name: "datum", type: "date", width: 120, align: "center", showIf: "true"},
                        {name: "verkauf_an", type: "text", showIf: "false"},
                        {name: "mwst_gesamtpr", type: "text", width: 120, align: "right",
                            recordSummaryFunction: "multiplier",
                            summaryFunction: "sum",
                            formatCellValue: function (value) {
                                if (isc.isA.Number(value)) {
                                    return value.toCurrencyString("₺ ");
                                }
                                return value;
                            }},
                        {name: "gesamtpr_brutto", type: "text", width: 120, align: "right",
                            recordSummaryFunction: "multiplier",
                            summaryFunction: "sum",
                            formatCellValue: function (value) {
                                if (isc.isA.Number(value)) {
                                    return value.toCurrencyString("₺ ");
                                }
                                return value;
                            }},
                        {name: "beleg_pfad", width: 60, showIf: "true", align: "center"}
                    ], getExpansionComponent: function (record) {


                        var buchungsListeDetails2 = isc.ListGrid.create({
                            height: 120,
                            cellheight: 22,
                            dataSource: buchungenDetailsDS,
                            canEdit: false,
                            fields: [
                                {name: "beleg_nr", type: "text", showIf: "false", width: 80},
                                {name: "lfd_nr", showIf: "false", width: 50},
                                {name: "prod_kz", type: "text", title: "Tedavi no", width: 80},
                                {name: "bezeichnung", type: "text", title: "Tedavi", width: "*"},
                                {name: "menge", type: "text", title: "Miktar", width: 60},
                                {name: "preis_kat", type: "text", title: "Kat.", width: 50},
                                {name: "brutto_preis", type: "text", title: "Fiyat", width: 80},
                                {name: "mwst", type: "text", title: "KDV", width: 50,
                                    formatCellValue: function (value) {
                                        if (isc.isA.Number(value)) {
                                            return value + " %";
                                        }
                                        return value;
                                    }}
                            ]
                        });
                        buchungsListeDetails2.fetchRelatedData(record, buchungsListe_Hastalar);

                        return buchungsListeDetails2;
                    },
                    selectionChanged: function (record, state) {
                        if (state) {

                        } else {
                        }
                    }
                });



                isc.TabSet.create({
                    ID: "tabHasta",
                    width: "100%",
                    height: "100%",
                    count: 0,
                    tabs: [
                        {title: "Hasta bilgileri",
                            pane: dfEditKunden},
                        {title: "Tedaviler",
                            pane: buchungsListe_Hastalar}
                    ],
                    tabSelected: function (tabSet, tabNum, tabPane, ID, tab, name) {
                        if (tabHasta.getSelectedTabNumber() == 1) {
                            tabHasta.count++;
                            buchungsListe_Hastalar.fetchData({count: tabHasta.count, lfd_nr: kundenListe.getSelectedRecord().lfd_nr});
                        }

                    }
                });


                isc.Window.create({
                    ID: "wdEditKunden",
                    title: "Hasta düzenleme",
                    autoSize: false,
                    autoCenter: true,
                    showFooter: false,
                    showMinimizeButton: false,
                    showCloseButton: false,
                    width: 600,
                    height: 520,
                    headerIconDefaults: {
                        width: 16,
                        height: 16,
                        src: "famfam/vcard_edit.png"
                    },
                    canDragReposition: true,
                    canDragResize: false,
                    showShadow: true,
                    showModalMask: true,
                    modalMaskOpacity: 10,
                    isModal: true,
                    items: [tabHasta, HLayoutKundeEdit, pgbEditKunden]
                });
                /*
                 * ********************** Ende edit Hasta **********************
                 * -------------------------------------------------------------
                 */

                /*
                 * ********************** Ende Hasta **************************
                 * -------------------------------------------------------------
                 */



                /*
                 * ***************** Anfang ListGrid Hasta*********************
                 * -------------------------------------------------------------
                 */

                isc.ListGrid.create({
                    ID: "mwstListe",
                    //   header: "Daten düzenleme",
                    width: "100%", height: "100%",
                    alternateRecordStyles: true,
                    contextMenu: "",
                    dataSource: mwstDS,
                    autoFetchData: true,
                    selectionType: "single",
                    showAllRecords: true,
                    canExpandRecords: false,
                    margin: 0,
                    fields: [{
                            name: "lfd_nr",
                            showIf: "false",
                            width: 50
                        }, {
                            name: "mwst",
                            type: "text",
                            title: "K.D.V",
                            showIf: "true"
                        }],
                    selectionChanged: function (record, state) {
                        if (state) {
                            tsbMwstDelete.setDisabled(false);
                            tsbMwstEdit.setDisabled(false);
                        } else {
                            tsbMwstDelete.setDisabled(true);
                            tsbMwstEdit.setDisabled(true);
                        }

                    }, recordDoubleClick: function (viewer, record, recordNum, field, fieldNum, value, rawValue) {
                        dfEditMwst.editRecord(record);
                        wdEditMwst.show();
                        pgbEditMwst.setHeight(16);
                        //                        tabKundenEdit.selectTab(0);
                        isc.Timer.setTimeout("btnResetEditMwst.click()", 100);

                    }
                });




                /*
                 * ****************** Anfang neuer KDV ***********************
                 * -------------------------------------------------------------
                 */

                isc.Progressbar.create({
                    percentDone: 0,
                    ID: "pgAddMwst",
                    showTitle: true,
                    title: "",
                    height: 12,
                    length: "100%"});

                isc.DynamicForm.create({
                    ID: "dfAddMwst",
                    width: "100%",
                    height: "100%",
                    kundenCount: 0,
                    colWidths: [115, "*"],
                    numCols: 2,
                    titleOrientation: "left",
                    validateOnExit: true,
                    validateOnChange: false,
                    margin: 5,
                    fields: [{
                            name: "mwst",
                            title: "K.D.V",
                            type: "text",
                            keyPressFilter: "[0-9,]",
                            width: 80,
                            required: true,
                            changed: function (form, item, value) {
                                form.changeFunctionAddMwst();
                            }
                        }
                    ], changeFunctionAddMwst: function () {
                        btnSpeichernMwstNeu.setDisabled(false);
                        btnResetMwstNeu.setDisabled(false);
                        btnCloseMwstNeu.setTitle("İptal et");
                        btnCloseMwstNeu.setIcon("famfam/cancel.png");
                    }
                });

                isc.IButton.create({
                    ID: "btnCloseMwstNeu",
                    type: "button",
                    disabled: false,
                    icon: "famfam/door_in.png",
                    name: "btnCloseMwstNeu",
                    showDisabledIcon: false,
                    title: "", width: 30, //Neuen Film anlegen
                    click: function () {
                        if (btnCloseMwstNeu.getTitle() == "İptal et") {
                            isc.ask("Gerçekten iptal etmek istiyor musunuz? Kaydedilmemiş veriler kaybolabilir.", function (value) {
                                if (value) {
                                    wdAddMwst.hide();
                                }
                            }, {title: "İşlem iptal?"});
                        } else {
                            wdAddMwst.hide();
                        }
                    }});

                isc.IButton.create({
                    ID: "btnSpeichernMwstNeu",
                    type: "button",
                    disabled: true,
                    showDisabledIcon: false,
                    icon: "famfam/database_save.png",
                    name: "btnSpeichernMwstNeu",
                    title: "",
                    width: 30, //Neuen Film anlegen
                    click: function () {
                        var _percent = pgAddMwst.percentDone + parseInt(10 + (50 * Math.random()));
                        pgAddMwst.setPercentDone(_percent);
                        pgAddMwst.setTitle(_percent + "%");

                        RPCManager.send("", function (rpcResponse, data, rpcRequest) {
                            var _data = isc.JSON.decode(data); // Daten aus dem PHP (rpcResponse)
                            if (_data.response.status === 0) {  // Durum 0 bedeutet Keine Hata

                                onRefresh("mwstListe");
                                btnSpeichernMwstNeu.pgbAddMwstFunction();
                                //                                isc.say(kundenNr);


                            } else { // Wenn die Validierungen Hata aufweisen dann:

                                dfAddMwst.setErrors(_data.response.errors, true);
                                var _errors = dfAddMwst.getErrors();
                                for (var i in _errors)
                                {
                                    isc.say("<b>Hata! </br>" + (_errors [i]) + "</b>", function (value) {
                                        if (value) {
                                            pgAddMwst.setTitle("");
                                            pgAddMwst.setPercentDone(0);
                                        }
                                    });
                                }

                            }
                        }, {// Übergabe der Parameter
                            actionURL: "api/addMwst.php",
                            httpMethod: "POST",
                            contentType: "application/x-www-form-urlencoded",
                            useSimpleHttp: true,
                            params: {
                                mwst: dfAddMwst.getField("mwst").getValue()}

                        }); //Ende RPC
                    }, // Ende Click
                    pgbAddMwstFunction: function () {
                        if (pgAddMwst.percentDone < 100) {
                            var _percent = pgAddMwst.percentDone + parseInt(10 + (50 * Math.random()));
                            pgAddMwst.setPercentDone(_percent); // Zufallswert wird berechnet

                            if (_percent <= 100) {
                                pgAddMwst.setTitle(_percent + "%");
                            } //Bis 100 wird mitgezählt
                            else {
                                pgAddMwst.setTitle("100%"); // ab 100 darf nicht mehr gezählt werden, da 100 leicht überstiegen wird.
                            }

                            isc.Timer.setTimeout("btnSpeichernMwstNeu.pgbAddMwstFunction()", 200);
                        } else {
                            if (!dfAddMwst.validate() && dfAddMwst.hasErrors()) {
                                dfAddMwst.setErrors();
                                var _errors = dfAddMwst.getErrors();
                                for (var i in _errors)
                                {
                                    isc.say("<b>Hata! </br>" + (_errors [i]) + "</b>", function (value) {
                                        if (value) {
                                            pgAddMwst.setTitle("");
                                            pgAddMwst.setPercentDone(0);
                                        }
                                    }); // Hier wird jeder Wert des Array-Schlüssel angezeigt und das Feld oder die Feld-Bezeichnung ist irrelevant.
                                }
                            } else {
                                isc.ask("KDV eklendi.</br>Bir ekleme daha yapmak ister misiniz?", function (value) {
                                    if (value) {
                                        dfAddMwst.clearValues();
                                        dfAddMwst.getField("mwst").focusInItem();
                                        pgAddMwst.setTitle("");
                                        pgAddMwst.setPercentDone(0);
                                        btnCloseMwstNeu.setTitle("Kapat");
                                        btnCloseMwstNeu.setIcon("famfam/door_in.png");
                                    } else {
                                        dfAddMwst.clearValues();
                                        wdAddMwst.hide();
                                        btnSpeichernMwstNeu.setDisabled(true);
                                        btnResetMwstNeu.setDisabled(true);
                                        pgAddMwst.setTitle("");
                                        pgAddMwst.setPercentDone(0);
                                        btnCloseMwstNeu.setTitle("Kapat");
                                        btnCloseMwstNeu.setIcon("famfam/door_in.png");
                                    }

                                }, {title: "KDV ekleme?"});
                            }
                        }
                    }// Ende ProgressbarFunction
                });
                isc.IButton.create({
                    ID: "btnResetMwstNeu",
                    type: "button",
                    showDisabledIcon: false,
                    icon: "famfam/arrow_undo.png",
                    disabled: true,
                    name: "btnResetMwstNeu",
                    title: "", width: 30, //Neuen Film anlegen
                    click: function () {
                        dfAddMwst.reset();
                        btnSpeichernMwstNeu.setDisabled(true);
                        btnResetMwstNeu.setDisabled(true);
                        btnCloseMwstNeu.setTitle("Kapat");
                        btnCloseMwstNeu.setIcon("famfam/door_in.png");
                    }});


                isc.HLayout.create({
                    ID: "HLayoutMwstNeu",
                    height: 30,
                    width: "100%",
                    align: "center",
                    members: [btnCloseMwstNeu, isc.LayoutSpacer.create({
                            width: 20
                        }), btnSpeichernMwstNeu, isc.LayoutSpacer.create({
                            width: 20
                        }), btnResetMwstNeu]});



                isc.Window.create({
                    ID: "wdAddMwst",
                    title: "Yeni KDV ekle",
                    autoSize: false,
                    autoCenter: true,
                    showFooter: false,
                    showMinimizeButton: false,
                    showCloseButton: false,
                    width: 300,
                    height: 250,
                    headerIconDefaults: {
                        width: 16,
                        height: 16,
                        src: "famfam/user_add.png"
                    },
                    canDragReposition: true,
                    canDragResize: false,
                    isModal: false,
                    items: [dfAddMwst, HLayoutMwstNeu, pgAddMwst]
                });
                /*
                 * ********************** Ende neuer Hasta *********************
                 * -------------------------------------------------------------
                 */


                /*
                 * ****************** Anfang edit Hasta ************************
                 * -------------------------------------------------------------
                 */

                isc.Progressbar.create({
                    percentDone: 0,
                    ID: "pgbEditMwst",
                    showTitle: true,
                    title: "",
                    height: 12,
                    length: "100%"});

                isc.DynamicForm.create({
                    ID: "dfEditMwst",
                    width: "100%",
                    height: "100%",
                    kundenCount: 0,
                    colWidths: [115, "*"],
                    numCols: 2,
                    titleOrientation: "left",
                    validateOnExit: true,
                    validateOnChange: false,
                    margin: 5,
                    fields: [{name: "lfd_nr",
                            type: "hidden"},
                        {
                            name: "mwst",
                            title: "KDV",
                            keyPressFilter: "[0-9,]",
                            type: "text",
                            width: 80,
                            required: true,
                            changed: function (form, item, value) {
                                form.changeFunctionEditMwst();
                            }
                        }
                    ], changeFunctionEditMwst: function () {
                        btnSpeichernEditMwst.setDisabled(false);
                        btnResetEditMwst.setDisabled(false);
                        btnCloseEditMwst.setTitle("İptal et");
                        btnCloseEditMwst.setIcon("famfam/cancel.png");
                    }
                });

                isc.IButton.create({
                    ID: "btnCloseEditMwst",
                    type: "button",
                    disabled: false,
                    icon: "famfam/door_in.png",
                    name: "btnCloseEditMwst",
                    showDisabledIcon: false,
                    title: "", width: 30,
                    click: function () {

                        if (btnCloseEditMwst.getTitle() == "İptal et") {
                            isc.ask("Gerçekten iptal etmek istiyor musunuz? Kaydedilmemiş veriler kaybolabilir.", function (value) {
                                if (value) {
                                    wdEditMwst.hide();
                                }
                            }, {title: "İşlem iptal?"});
                        } else {
                            wdEditMwst.hide();
                        }

                    }});

                isc.IButton.create({
                    ID: "btnSpeichernEditMwst",
                    type: "button",
                    disabled: true,
                    showDisabledIcon: false,
                    icon: "famfam/database_save.png",
                    name: "btnSpeichernEditMwst",
                    title: "",
                    width: 30,
                    click: function () {
                        kundenNr = mwstListe.getSelectedRecord().lfd_nr;
                        var _percent = pgbEditMwst.percentDone + parseInt(10 + (50 * Math.random()));
                        pgbEditMwst.setPercentDone(_percent);
                        pgbEditMwst.setTitle(_percent + "%");
                        RPCManager.send("", function (rpcResponse, data, rpcRequest) {
                            var _data = isc.JSON.decode(data); // Daten aus dem PHP (rpcResponse)
                            if (_data.response.status === 0) {  // Durum 0 bedeutet Keine Hata

                                onRefresh("mwstListe");
                                btnSpeichernEditMwst.pgbEditMwstFunction();

                            } else { // Wenn die Validierungen Hata aufweisen dann:

                                dfEditMwst.setErrors(_data.response.errors, true);
                                var _errors = dfEditMwst.getErrors();
                                for (var i in _errors)
                                {
                                    isc.say("<b>Hata! </br>" + (_errors [i]) + "</b>", function (value) {
                                        if (value) {
                                            pgbEditMwst.setTitle("");
                                            pgbEditMwst.setPercentDone(0);
                                        }
                                    });
                                }

                            }
                        }, {// Übergabe der Parameter
                            actionURL: "api/editMwst.php",
                            httpMethod: "POST",
                            contentType: "application/x-www-form-urlencoded",
                            useSimpleHttp: true,
                            params: {
                                lfd_nr: mwstListe.getSelectedRecord().lfd_nr,
                                mwst: dfEditMwst.getField("mwst").getValue()}

                        }); //Ende RPC
                    }, // Ende Click
                    pgbEditMwstFunction: function () {
                        if (pgbEditMwst.percentDone < 100) {
                            var _percent = pgbEditMwst.percentDone + parseInt(10 + (50 * Math.random()));
                            pgbEditMwst.setPercentDone(_percent); // Zufallswert wird berechnet

                            if (_percent <= 100) {
                                pgbEditMwst.setTitle(_percent + "%");
                            } //Bis 100 wird mitgezählt
                            else {
                                pgbEditMwst.setTitle("100%"); // ab 100 darf nicht mehr gezählt werden, da 100 leicht überstiegen wird.
                            }

                            isc.Timer.setTimeout("btnSpeichernEditMwst.pgbEditMwstFunction()", 200);
                        } else {
                            if (!dfEditMwst.validate() && dfEditMwst.hasErrors()) {
                                dfEditMwst.setErrors();
                                var _errors = dfEditMwst.getErrors();
                                for (var i in _errors)
                                {
                                    isc.say("<b>Hata! </br>" + (_errors [i]) + "</b>", function (value) {
                                        if (value) {
                                            pgbEditMwst.setTitle("");
                                            pgbEditMwst.setPercentDone(0);
                                        }
                                    }); // Hier wird jeder Wert des Array-Schlüssel angezeigt und das Feld oder die Feld-Bezeichnung ist irrelevant.
                                }
                            } else {

                                dfEditMwst.clearValues();
                                wdEditMwst.hide();
                                btnSpeichernEditMwst.setDisabled(true);
                                btnResetEditMwst.setDisabled(true);
                                pgbEditMwst.setTitle("");
                                pgbEditMwst.setPercentDone(0);
                                btnCloseEditMwst.setTitle("Kapat");
                                btnCloseEditMwst.setIcon("famfam/door_in.png");
                            }
                        }
                    }// Ende ProgressbarFunction
                });
                isc.IButton.create({
                    ID: "btnResetEditMwst",
                    type: "button",
                    disabled: true,
                    icon: "famfam/arrow_undo.png",
                    showDisabledIcon: false,
                    name: "btnResetEditMwst",
                    title: "", width: 30, //Neuen Film anlegen
                    click: function () {
                        dfEditMwst.reset();
                        btnSpeichernEditMwst.setDisabled(true);
                        btnResetEditMwst.setDisabled(true);
                        btnCloseEditMwst.setTitle("Kapat");
                        btnCloseEditMwst.setIcon("famfam/door_in.png");
                    }});

                /*
                 ***************** Delete Button Tedavi ************************** 
                 */
                isc.ToolStripButton.create({
                    ID: "tsbMwstDelete",
                    title: "",
                    showDisabledIcon: false,
                    icon: "famfam/delete.png",
                    prompt: "Seçilen KDV'yi siler",
                    disabled: true,
                    hoverWidth: 100,
                    hoverDelay: 700,
                    action: function () {


                        if (mwstListe.getSelection().length == 1) {
                            var produkt = mwstListe.getSelectedRecord().bezeichnung;
                            isc.ask("Gerçekten kalıcı olarak silmek istiyor musunuz?", function (value) {
                                if (value) {
                                    RPCManager.send("", function (rpcResponse, data, rpcRequest) {
                                        var _data = isc.JSON.decode(data); // Daten aus dem PHP (rpcResponse)
                                        if (_data.response.status === 0) {  // Durum 0 bedeutet Keine Hata
                                            onRefresh("mwstListe");
                                        } else { // Wenn die Validierungen Hata aufweisen dann:

                                            dfErrorFormProdukte.setErrors(_data.response.errors, true);
                                            var _errors = dfErrorFormProdukte.getErrors();
                                            for (var i in _errors)
                                            {
                                                isc.say("<b>Hata! </br>" + (_errors [i]) + "</b>");
                                            }

                                        }
                                    }, {// Übergabe der Parameter
                                        actionURL: "api/deleteMwst.php",
                                        httpMethod: "POST",
                                        contentType: "application/x-www-form-urlencoded",
                                        useSimpleHttp: true,
                                        params: {
                                            lfd_nr: mwstListe.getSelectedRecord().lfd_nr}
                                    }); //Ende RPC 
                                }
                            }, {title: ""});
                        } else {
                            isc.say("Önce KDV'yi seçmelisiniz");
                        }

                    }
                });


                isc.HLayout.create({
                    ID: "HLayoutMwstEdit",
                    height: 30,
                    width: "100%",
                    align: "center",
                    members: [btnCloseEditMwst, isc.LayoutSpacer.create({
                            width: 20
                        }), btnSpeichernEditMwst, isc.LayoutSpacer.create({
                            width: 20
                        }), btnResetEditMwst]});


                isc.Window.create({
                    ID: "wdEditMwst",
                    title: "KDV düzenleme",
                    autoSize: false,
                    autoCenter: true,
                    showFooter: false,
                    showMinimizeButton: false,
                    showCloseButton: true,
                    width: 300,
                    height: 250,
                    headerIconDefaults: {
                        width: 16,
                        height: 16,
                        src: "famfam/user_edit.png"
                    },
                    canDragReposition: true,
                    canDragResize: false,
                    items: [dfEditMwst, HLayoutMwstEdit, pgbEditMwst]
                });



                /*
                 ***************** Add Button KDV *************************** 
                 */
                isc.ToolStripButton.create({
                    ID: "tsbAddMwst",
                    title: "",
                    showDisabledIcon: false,
                    icon: "famfam/user_add.png",
                    prompt: "Yeni bir KDV eklemek için ekranı açar.",
                    hoverWidth: 100,
                    hoverDelay: 700,
                    action: function () {
                        wdAddMwst.show();
                        isc.Timer.setTimeout("btnResetKundeNeu.click()", 50);
                    }
                });
                /*
                 ***************** Edit Button KDV ************************** 
                 */
                isc.ToolStripButton.create({
                    ID: "tsbMwstEdit",
                    title: "",
                    showDisabledIcon: false,
                    disabled: true,
                    icon: "famfam/pencil.png",
                    prompt: "Seçilen KDV için düzenleme ekranını açar",
                    hoverWidth: 100,
                    hoverDelay: 700,
                    action: function () {
                        if (kundenListe.getSelection().length == 1) {
                            record = kundenListe.getSelectedRecord();
                            dfEditMwst.editRecord(record);
                            wdEditMwst.show();
                            isc.Timer.setTimeout("btnResetMwstEdit.click()", 50);
                        } else {
                            isc.say("Önce bir KDV seçmelisiniz");
                        }

                    }
                });

                isc.ToolStrip.create({
                    ID: "tsMwst",
                    width: "100%",
                    height: 40,
                    members: [isc.LayoutSpacer.create({width: 10}),
                        tsbAddMwst, "separator", isc.LayoutSpacer.create({width: 10}),
                        tsbMwstDelete, "separator", isc.LayoutSpacer.create({width: 20}),
                        tsbMwstEdit]});


                isc.Window.create({
                    ID: "wdMwstList",
                    title: "KDV",
                    autoSize: false,
                    autoCenter: true,
                    showMinimizeButton: false,
                    showCloseButton: true,
                    width: 250,
                    height: 250,
                    headerIconDefaults: {
                        width: 16,
                        height: 16,
                        src: "famfam/money.png"
                    },
                    canDragReposition: true,
                    canDragResize: false,
                    items: [tsMwst, mwstListe]
                });







                /*
                 * ********************** Anfang Tedavi **********************
                 * =============================================================
                 * -------------------------------------------------------------
                 */

                isc.ListGrid.create({
                    ID: "produktListe",
                    //   header: "Daten düzenleme",
                    width: "100%", height: "100%",
                    alternateRecordStyles: true,
                    contextMenu: "",
                    dataSource: produkteDS,
                    autoFetchData: true,
                    taksit_count: 0,
                    showFilterEditor: true,
                    filterOnKeypress: true,
                    selectionType: "single",
                    showAllRecords: true,
                    canExpandRecords: false,
                    showGridSummary: true,
                    //                    showGroupSummary: true,
                    expansionMode: "details",
                    margin: 0,
                    fields: [{
                            name: "prod_kz",
                            showIf: "true",
                            width: 80
                        }, {
                            name: "bezeichnung",
                            title: "Tedavi",
                            width: 250,
                            showGridSummary: true, showGroupSummary: true, summaryFunction: "count"
                        }, {
                            name: "brutto_preis1",
                            width: 80
                        }, {
                            name: "brutto_preis2",
                            width: 80
                        },
                        /* {
                         name: "brutto_preis1",
                         width: 80
                         },
                         {
                         name: "brutto_preis2",
                         width: 80
                         },*/
                        {
                            name: "mwst",
                            width: 80,
                            showIf: "false"
                        },
                        {
                            name: "mwst2",
                            width: 80,
                            showIf: "true"
                        }/*,
                         {
                         name: "prod_bild",
                         width: 80,
                         type: "link",
                         linkText: isc.Canvas.imgHTML("famfam/picture.png", 16, 16),
                         linkURLPrefix: "api/images/produkt_bilder/"
                         }*/, {
                            name: "aktiv",
                            width: 80,
                            showIf: "false"
                        }]
                    , hilites: [
                        {
                            textColor: "#000000",
                            cssText: "color:#000000;background-color:#E0E0E0;",
                            id: 0
                        }
                    ]
                    , selectionChanged: function (record, state) {
                        if (state) {
                            tsbProdukteDelete.setDisabled(false);
                            tsbProdukteEdit.setDisabled(false);
                        } else {
                            tsbProdukteDelete.setDisabled(true);
                            tsbProdukteEdit.setDisabled(true);
                        }

                    }, recordDoubleClick: function (viewer, record, recordNum, field, fieldNum, value, rawValue) {
                        dfEditProdukte.editRecord(record);
                        wdEditProdukte.show();
                        pgbEditProdukte.setHeight(16);
                        //                            tabProdukteEdit.selectTab(0);
                        isc.Timer.setTimeout("btnResetProduktEdit.click()", 100);

                    }
                });


                /*
                 * ****************** Anfang neue Tedavi *********************
                 * -------------------------------------------------------------
                 */

                isc.Progressbar.create({
                    percentDone: 0,
                    ID: "pgbAddProdukte",
                    showTitle: true,
                    title: "",
                    height: 16,
                    length: "100%"});

                isc.DynamicForm.create({
                    ID: "dfAddProdukte",
                    width: "100%",
                    height: "100%",
                    ProdCount: 0,
                    colWidths: [150, "*"],
                    numCols: 2,
                    titleOrientation: "left",
                    validateOnExit: true,
                    validateOnChange: false,
                    margin: 5,
                    fields: [
                        {
                            name: "prod_kz",
                            title: "Tedavi-No",
                            type: "text",
                            characterCasing: "upper",
                            width: 100,
                            length: 4,
                            required: true,
                            icons: [{
                                    src: "famfam/help.png",
                                    prompt: "Lütfen 4 karakterden oluşan bir Tedavi no girin."
                                }],
                            validators: [
                                {
                                    type: "lengthRange",
                                    min: 4,
                                    max: 4,
                                    errorMessage: "Tam olarak 4 karakter girilmelidir!"
                                }
                            ],
                            changed: function (form, item, value) {
                                form.changeFunctionAddProdukte();
                            }
                        }, {
                            name: "bezeichnung",
                            title: "Tedavi",
                            type: "text",
                            width: 250,
                            required: true, validators: [
                                {
                                    type: "lengthRange",
                                    min: 1,
                                    max: 64,
                                    errorMessage: "Lütfen en az 1, maks. 64 karakter girin!"
                                }
                            ],
                            changed: function (form, item, value) {
                                form.changeFunctionAddProdukte();
                            }
                        }, {
                            name: "brutto_preis1",
                            title: "Fiyat 1",
                            type: "text",
                            width: 100,
                            required: true,
                            keyPressFilter: "[0-9,]",
                            changed: function (form, item, value) {
                                form.changeFunctionAddProdukte();
                            }
                        }, {
                            name: "brutto_preis2",
                            title: "Fiyat 2",
                            type: "text",
                            width: 100,
                            required: true,
                            keyPressFilter: "[0-9,]",
                            changed: function (form, item, value) {
                                form.changeFunctionAddProdukte();
                            }
                        },
                        {
                            name: "mwst",
                            title: "KDV",
                            optionDataSource: mwstDS,
                            valueField: "lfd_nr",
                            displayField: "mwst",
                            type: "select",
                            required: true,
                            width: 100,
                            changed: function (form, item, value) {
                                form.changeFunctionAddProdukte();
                            },
                            pickListProperties: {showShadow: true, showFilterEditor: false, showHeader: true},
                            pickListWidth: 100,
                            pickListFields: [
                                {name: "mwst", width: "*"}],
                            getPickListFilterCriteria: function () {
                                dfAddProdukte.ProdCount++;
                                var filter = {
                                    count: dfAddProdukte.ProdCount, mwst: dfAddProdukte.getField("mwst").getValue()};
                                return filter;
                            }, icons: [{
                                    src: "famfam/add.png",
                                    prompt: "K.D.V ekle",
                                    hoverWidth: 100,
                                    hoverDelay: 700,
                                    click: function (form, item, value) {
                                        wdMwstList.show();
                                    }
                                }]
                        },
                        {
                            type: "RowSpacer",
                            height: 10
                        }
                    ], changeFunctionAddProdukte: function () {
                        btnSpeichernProduktNeu.setDisabled(false);
                        btnResetProduktNeu.setDisabled(false);
                        btnCloseProduktNeu.setTitle("İptal et");
                    }
                });

                isc.IButton.create({
                    ID: "btnCloseProduktNeu",
                    type: "button",
                    disabled: false,
                    icon: "famfam/door_in.png",
                    name: "btnCloseProduktNeu",
                    showDisabledIcon: false,
                    title: "Kapat", width: 100, //Neuen Film anlegen
                    click: function () {

                        if (btnCloseProduktNeu.getTitle() == "İptal et") {
                            isc.ask("Gerçekten iptal etmek istiyor musunuz? Kaydedilmemiş veriler kaybolabilir.", function (value) {
                                if (value) {
                                    wdAddProdukte.hide();
                                }
                            }, {title: "İşlem iptal?"});
                        } else {
                            wdAddProdukte.hide();
                        }
                    }});

                isc.IButton.create({
                    ID: "btnSpeichernProduktNeu",
                    type: "button",
                    disabled: true,
                    showDisabledIcon: false,
                    icon: "famfam/database_save.png",
                    name: "btnSpeichernProduktNeu",
                    title: "Kaydet",
                    width: 100, //Neuen Film anlegen
                    click: function () {
                        var _percent = pgbAddProdukte.percentDone + parseInt(10 + (50 * Math.random()));
                        pgbAddProdukte.setPercentDone(_percent);
                        pgbAddProdukte.setTitle(_percent + "%");

                        RPCManager.send("", function (rpcResponse, data, rpcRequest) {
                            var _data = isc.JSON.decode(data); // Daten aus dem PHP (rpcResponse)
                            if (_data.response.status === 0) {  // Durum 0 bedeutet Keine Hata
                                produktKz = dfAddProdukte.getField("prod_kz").getValue();
                                onRefresh("produktListe");
                                btnSpeichernProduktNeu.pgbAddProduktFunction();
                                isc.Timer.setTimeout("btnSpeichernProduktNeu.findProdukt()", 300);
                                //                                isc.say(produktKz);


                            } else { // Wenn die Validierungen Hata aufweisen dann:

                                dfAddProdukte.setErrors(_data.response.errors, true);
                                var _errors = dfAddProdukte.getErrors();
                                for (var i in _errors)
                                {
                                    isc.say("<b>Hata! </br>" + (_errors [i]) + "</b>", function (value) {
                                        if (value) {
                                            pgbAddProdukte.setTitle("");
                                            pgbAddProdukte.setPercentDone(0);
                                        }
                                    });
                                }

                            }
                        }, {// Übergabe der Parameter
                            actionURL: "api/addProdukte.php",
                            httpMethod: "POST",
                            contentType: "application/x-www-form-urlencoded",
                            useSimpleHttp: true,
                            params: {
                                prod_kz: dfAddProdukte.getField("prod_kz").getValue(),
                                bezeichnung: dfAddProdukte.getField("bezeichnung").getValue(),
                                brutto_preis1: dfAddProdukte.getField("brutto_preis1").getValue(),
                                brutto_preis2: dfAddProdukte.getField("brutto_preis2").getValue(),
                                mwst: dfAddProdukte.getField("mwst").getValue(),
                                mwstSatz: dfAddProdukte.getField("mwst").getDisplayValue()
                            }

                        }); //Ende RPC
                    }, // Ende Click
                    findProdukt: function () {
                        var newProd = produktListe.data.find("prod_kz", produktKz);
                        var index = produktListe.getRecordIndex(newProd);
                        //                        produktListe.deselectAllRecords();
                        produktListe.selectRecord(index);
                        produktListe.scrollToRow(index);
                    },
                    pgbAddProduktFunction: function () {
                        var _percent = pgbAddProdukte.percentDone;

                        if (_percent < 100) {
                            _percent = pgbAddProdukte.percentDone + parseInt(10 + (50 * Math.random()));
                            pgbAddProdukte.setPercentDone(_percent); // Zufallswert wird berechnet

                            if (_percent <= 100) {
                                pgbAddProdukte.setTitle(_percent + "%");
                            } //Bis 100 wird mitgezählt
                            else {
                                pgbAddProdukte.setTitle("100%"); // ab 100 darf nicht mehr gezählt werden, da 100 leicht überstiegen wird.
                            }

                            isc.Timer.setTimeout("btnSpeichernProduktNeu.pgbAddProduktFunction()", 200);
                        } else {
                            if (!dfAddProdukte.validate() && dfAddProdukte.hasErrors()) {
                                dfAddProdukte.setErrors();
                                var _errors = dfAddProdukte.getErrors();
                                for (var i in _errors)
                                {
                                    isc.say("<b>Hata! </br>" + (_errors [i]) + "</b>", function (value) {
                                        if (value) {
                                            pgbAddProdukte.setTitle("");
                                            pgbAddProdukte.setPercentDone(0);
                                        }
                                    }); // Hier wird jeder Wert des Array-Schlüssel angezeigt und das Feld oder die Feld-Bezeichnung ist irrelevant.
                                }
                            } else {
                                isc.ask("Tedavi başarıyla oluşturuldu. </ Br> Başka bir tedavi eklemek ister misiniz?", function (value) {
                                    if (value) {
                                        dfAddProdukte.clearValues();
                                        dfAddProdukte.getField("vorname").focusInItem();
                                        isc.Timer.setTimeout("btnSpeichernProduktNeu.findProdukt()", 300);
                                        pgbAddProdukte.setTitle("");
                                        pgbAddProdukte.setPercentDone(0);
                                        btnCloseProduktNeu.setTitle("Kapat");
                                    } else {
                                        dfAddProdukte.clearValues();
                                        wdAddProdukte.hide();
                                        btnSpeichernProduktNeu.setDisabled(true);
                                        btnResetProduktNeu.setDisabled(true);
                                        isc.Timer.setTimeout("btnSpeichernProduktNeu.findProdukt()", 300);
                                        pgbAddProdukte.setTitle("");
                                        pgbAddProdukte.setPercentDone(0);
                                        btnCloseProduktNeu.setTitle("Kapat");
                                    }

                                }, {title: "Başka bir tedavi oluşturmak istermisiniz?"});
                            }
                        }
                    }// Ende ProgressbarFunction
                });
                isc.IButton.create({
                    ID: "btnResetProduktNeu",
                    type: "button",
                    showDisabledIcon: false,
                    icon: "famfam/arrow_undo.png",
                    disabled: true,
                    name: "btnResetProduktNeu",
                    title: "Reset", width: 100, //Neuen Film anlegen
                    click: function () {
                        dfAddProdukte.reset();
                        btnSpeichernProduktNeu.setDisabled(true);
                        btnResetProduktNeu.setDisabled(true);
                        btnCloseProduktNeu.setTitle("Kapat");
                    }});
                isc.HLayout.create({
                    ID: "HLayoutProduktNeu",
                    height: 30,
                    width: "100%",
                    align: "center",
                    members: [btnCloseProduktNeu, isc.LayoutSpacer.create({
                            width: 20
                        }), btnSpeichernProduktNeu, isc.LayoutSpacer.create({
                            width: 20
                        }), btnResetProduktNeu]});


                isc.Window.create({
                    ID: "wdAddProdukte",
                    title: "Neues Tedavi hinzufügen",
                    autoSize: true,
                    autoCenter: true,
                    showFooter: false,
                    showMinimizeButton: false,
                    showCloseButton: false,
                    width: 510,
                    headerIconDefaults: {
                        width: 16,
                        height: 16,
                        src: "famfam/pill_add.png"
                    },
                    canDragReposition: true,
                    canDragResize: false,
                    showShadow: true,
                    showModalMask: true,
                    modalMaskOpacity: 10,
                    isModal: true,
                    items: [dfAddProdukte, HLayoutProduktNeu, pgbAddProdukte]
                });
                /*
                 * ********************** Ende neues Tedavi *******************
                 * -------------------------------------------------------------
                 */



                /*
                 * ****************** Anfang edit Tedavi *********************
                 * -------------------------------------------------------------
                 */

                isc.Progressbar.create({
                    percentDone: 0,
                    ID: "pgbEditProdukte",
                    showTitle: true,
                    title: "",
                    height: 16,
                    length: "100%"});

                isc.DynamicForm.create({
                    ID: "dfEditProdukte",
                    width: "100%",
                    height: "100%",
                    prodCount: 0,
                    colWidths: [150, "*"],
                    numCols: 2,
                    titleOrientation: "left",
                    validateOnExit: true,
                    validateOnChange: false,
                    margin: 5,
                    fields: [
                        {
                            name: "prod_kz",
                            title: "Tedavi-No",
                            type: "text",
                            disabled: true,
                            characterCasing: "upper",
                            width: 100,
                            required: true,
                            length: 4,
                            validators: [
                                {
                                    type: "lengthRange",
                                    min: 4,
                                    max: 4,
                                    errorMessage: "Tam olarak 4 karakter girilmelidir!"
                                }
                            ],
                            changed: function (form, item, value) {
                                form.changeFunctionEditProdukte();
                            }
                        }, {
                            name: "bezeichnung",
                            title: "Tedavi",
                            type: "text",
                            width: 250,
                            required: true, validators: [
                                {
                                    type: "lengthRange",
                                    min: 1,
                                    max: 64,
                                    errorMessage: "Lütfen en az 1, maks. 64 karakter girin!"
                                }
                            ],
                            changed: function (form, item, value) {
                                form.changeFunctionEditProdukte();
                            }
                        }, {
                            name: "brutto_preis1",
                            title: "Fiyat 1",
                            type: "text",
                            width: 100,
                            required: true,
                            keyPressFilter: "[0-9,]",
                            changed: function (form, item, value) {
                                form.changeFunctionEditProdukte();
                            }
                        }, {
                            name: "brutto_preis2",
                            title: "Fiyat 2",
                            type: "text",
                            width: 100,
                            required: true,
                            keyPressFilter: "[0-9,]",
                            changed: function (form, item, value) {
                                form.changeFunctionEditProdukte();
                            }
                        },
                        {
                            name: "mwst",
                            title: "KDV",
                            optionDataSource: mwstDS,
                            valueField: "lfd_nr",
                            displayField: "mwst",
                            type: "select",
                            required: true,
                            autoFetchData: true,
                            width: 100,
                            changed: function (form, item, value) {
                                form.changeFunctionEditProdukte();
                            },
                            pickListProperties: {showShadow: true, showFilterEditor: false, showHeader: true},
                            pickListWidth: 100,
                            pickListFields: [
                                {name: "mwst", width: "*"}],
                            getPickListFilterCriteria: function () {
                                dfEditProdukte.ProdCount++;
                                var filter = {
                                    count: dfEditProdukte.ProdCount, mwst: dfEditProdukte.getField("mwst").getValue()};
                                return filter;
                            }, icons: [{
                                    src: "famfam/add.png",
                                    prompt: "K.D.V ekle",
                                    hoverWidth: 100,
                                    hoverDelay: 700,
                                    click: function (form, item, value) {
                                        wdMwstList.show();
                                    }
                                }]
                        },
                        {
                            type: "RowSpacer",
                            height: 10
                        }, {
                            name: "aktiv",
                            title: "Durum",
                            type: "radioGroup",
                            width: 100,
                            valueMap: {"1": "Aktif", "0": "Inaktif"},
                            required: true,
                            keyPressFilter: "[0-1]",
                            changed: function (form, item, value) {
                                form.changeFunctionEditProdukte();
                            }
                        },
                        {
                            type: "RowSpacer",
                            height: 10
                        }
                    ], changeFunctionEditProdukte: function () {
                        btnSpeichernProduktEdit.setDisabled(false);
                        btnResetProduktEdit.setDisabled(false);
                        btnCloseProduktEdit.setTitle("İptal et");
                    }
                });

                isc.IButton.create({
                    ID: "btnCloseProduktEdit",
                    type: "button",
                    disabled: false,
                    icon: "famfam/door_in.png",
                    name: "btnCloseProduktEdit",
                    showDisabledIcon: false,
                    title: "Kapat", width: 100,
                    click: function () {

                        if (btnCloseProduktEdit.getTitle() == "İptal et") {
                            isc.ask("Gerçekten iptal etmek istiyor musunuz? Kaydedilmemiş veriler kaybolabilir.", function (value) {
                                if (value) {
                                    wdEditProdukte.hide();
                                }
                            }, {title: "İşlem iptal?"});
                        } else {
                            wdEditProdukte.hide();
                        }
                    }});

                isc.IButton.create({
                    ID: "btnSpeichernProduktEdit",
                    type: "button",
                    disabled: true,
                    showDisabledIcon: false,
                    icon: "famfam/database_save.png",
                    name: "btnSpeichernProduktEdit",
                    title: "Kaydet",
                    width: 100,
                    click: function () {
                        var _percent = pgbEditProdukte.percentDone + parseInt(10 + (50 * Math.random()));
                        pgbEditProdukte.setPercentDone(_percent);
                        pgbEditProdukte.setTitle(_percent + "%");

                        RPCManager.send("", function (rpcResponse, data, rpcRequest) {
                            var _data = isc.JSON.decode(data); // Daten aus dem PHP (rpcResponse)
                            if (_data.response.status === 0) {  // Durum 0 bedeutet Keine Hata
                                produktKz = dfEditProdukte.getField("prod_kz").getValue();
                                onRefresh("produktListe");
                                btnSpeichernProduktEdit.pgbEditProduktFunction();
                                isc.Timer.setTimeout("btnSpeichernProduktEdit.findProdukt()", 300);
                                //                                isc.say(produktKz);


                            } else { // Wenn die Validierungen Hata aufweisen dann:

                                dfEditProdukte.setErrors(_data.response.errors, true);
                                var _errors = dfEditProdukte.getErrors();
                                for (var i in _errors)
                                {
                                    isc.say("<b>Hata! </br>" + (_errors [i]) + "</b>", function (value) {
                                        if (value) {
                                            pgbEditProdukte.setTitle("");
                                            pgbEditProdukte.setPercentDone(0);
                                        }
                                    });
                                }

                            }
                        }, {// Übergabe der Parameter
                            actionURL: "api/editProdukte.php",
                            httpMethod: "POST",
                            contentType: "application/x-www-form-urlencoded",
                            useSimpleHttp: true,
                            params: {
                                prod_kz: dfEditProdukte.getField("prod_kz").getValue(),
                                aktiv: dfEditProdukte.getField("aktiv").getValue(),
                                bezeichnung: dfEditProdukte.getField("bezeichnung").getValue(),
                                brutto_preis1: dfEditProdukte.getField("brutto_preis1").getValue(),
                                brutto_preis2: dfEditProdukte.getField("brutto_preis2").getValue(),
                                mwst: dfEditProdukte.getField("mwst").getValue(),
                                mwstSatz: dfEditProdukte.getField("mwst").getDisplayValue()}

                        }); //Ende RPC
                    }, // Ende Click
                    findProdukt: function () {
                        var newProd = produktListe.data.find("prod_kz", produktKz);
                        var index = produktListe.getRecordIndex(newProd);
                        //                        produktListe.deselectAllRecords();
                        produktListe.selectRecord(index);
                        produktListe.scrollToRow(index);
                    },
                    pgbEditProduktFunction: function () {
                        var _percent = pgbEditProdukte.percentDone;

                        if (_percent < 100) {
                            _percent = pgbEditProdukte.percentDone + parseInt(10 + (50 * Math.random()));
                            pgbEditProdukte.setPercentDone(_percent); // Zufallswert wird berechnet

                            if (_percent <= 100) {
                                pgbEditProdukte.setTitle(_percent + "%");
                            } //Bis 100 wird mitgezählt
                            else {
                                pgbEditProdukte.setTitle("100%"); // ab 100 darf nicht mehr gezählt werden, da 100 leicht überstiegen wird.
                            }

                            isc.Timer.setTimeout("btnSpeichernProduktEdit.pgbEditProduktFunction()", 200);
                        } else {
                            if (!dfEditProdukte.validate() && dfEditProdukte.hasErrors()) {
                                dfEditProdukte.setErrors();
                                var _errors = dfEditProdukte.getErrors();
                                for (var i in _errors)
                                {
                                    isc.say("<b>Hata! </br>" + (_errors [i]) + "</b>", function (value) {
                                        if (value) {
                                            pgbEditProdukte.setTitle("");
                                            pgbEditProdukte.setPercentDone(0);
                                        }
                                    }); // Hier wird jeder Wert des Array-Schlüssel angezeigt und das Feld oder die Feld-Bezeichnung ist irrelevant.
                                }
                            } else {
                                dfEditProdukte.clearValues();
                                wdEditProdukte.hide();
                                btnSpeichernProduktEdit.setDisabled(true);
                                btnResetProduktEdit.setDisabled(true);
                                isc.Timer.setTimeout("btnSpeichernProduktEdit.findProdukt()", 300);
                                pgbEditProdukte.setTitle("");
                                pgbEditProdukte.setPercentDone(0);
                                btnCloseProduktEdit.setTitle("Kapat");
                            }
                        }
                    }// Ende ProgressbarFunction
                });
                isc.IButton.create({
                    ID: "btnResetProduktEdit",
                    type: "button",
                    showDisabledIcon: false,
                    icon: "famfam/arrow_undo.png",
                    disabled: true,
                    name: "btnResetProduktEdit",
                    title: "Reset", width: 100,
                    click: function () {
                        dfEditProdukte.reset();
                        btnSpeichernProduktEdit.setDisabled(true);
                        btnResetProduktEdit.setDisabled(true);
                        btnCloseProduktEdit.setTitle("Kapat");
                    }});
                isc.HLayout.create({
                    ID: "HLayoutProduktEdit",
                    height: 30,
                    width: "100%",
                    align: "center",
                    members: [btnCloseProduktEdit, isc.LayoutSpacer.create({
                            width: 20
                        }), btnSpeichernProduktEdit, isc.LayoutSpacer.create({
                            width: 20
                        }), btnResetProduktEdit]});



                UploadForm.create({
                    ID: "produktBildUpload",
                    width: "100%",
                    height: "20%",
                    numCols: 2,
                    // location of our backend
                    action: 'api/produkt_bild_upload.php',
                    fields: [{
                            type: "RowSpacer",
                            height: 10
                        },
                        {type: "hidden",
                            name: "prod_kz",
                            title: ""
                        }, {
                            name: "datei",
                            type: "Upload",
                            title: "Dosya",
                            showTitle: "false",
                            colSpan: 2,
                            align: "left"
                        }, {
                            type: "RowSpacer",
                            height: 10
                        }, {
                            name: "upload",
                            title: "Yükle",
                            type: "submit",
                            colSpan: 2,
                            align: "center"
                        }
                    ],
                    submitDone: function (result, _status, bild) {
                        // den Erfolg überprüfen!
                        //   RecordIndexLB.getField("result").setValue(result);
                        ergebnis = result;
                        status = _status;
                        isc.say(ergebnis, function (value) {
                            if (value) {
                                if (status == "ok") {
                                    htmlBildVorschauProdukte.setContents("<center><img src='api/images/produkt_bilder/" + bild + "' width='270' height='200' alt='Grafik'></center>");
                                    onRefresh("produktListe");
                                    isc.Timer.setTimeout("btnSpeichernProduktEdit.findProdukt()", 500);
                                }// if status
                            }// if value
                        }); //isc.say
                    }//submit

                });

                isc.HTMLPane.create({
                    width: "100%",
                    height: "80%",
                    ID: "htmlBildVorschauProdukte",
                    styleName: "exampleTextBlock",
                    contents: "<center><img src='images/no_image.jpg' width='230' height='200' alt='Grafik'></center>"});


                isc.VLayout.create({
                    ID: "VLayoutProduktEditBildUpload_Vorschau",
                    height: "100%",
                    width: "100%",
                    align: "center",
                    members: [produktBildUpload, htmlBildVorschauProdukte]});


                isc.historieListe.create({
                    ID: "ProduktHistListeEditWD"
                });


                isc.historieListe.create({
                    ID: "produktHistListeEinzelWD",
                    showFilterEditor: true,
                    filterOnKeypress: true,
                    groupByField: "schluessel",
                    selectionChanged: function (record, state) {
                    }
                });



                isc.TabSet.create({
                    ID: "tabProdukteEdit",
                    width: "100%",
                    height: 350,
                    tabs: [
                        {title: "Tedavi",
                            pane: ""},
                        {title: "Tedavi-Bild",
                            pane: VLayoutProduktEditBildUpload_Vorschau}
                        //                    ,{title: "Protokol",
                        //                            pane: ProduktHistListeEditWD}
                    ],
                    tabSelected: function (tabSet, tabNum, tabPane, ID, tab, name) {
                        if (tabProdukteEdit.getSelectedTabNumber() == 1) {
                            produktKz = dfEditProdukte.getField("prod_kz").getValue();
                            produktBildUpload.getField("prod_kz").setValue(produktKz);
                            var bild = produktListe.getSelectedRecord().prod_bild;
                            if (bild == "") {
                                htmlBildVorschauProdukte.setContents("<center><img src='images/no_image.jpg' width='250' height='200' alt='Grafik'></center>");
                            } else {
                                htmlBildVorschauProdukte.setContents("<center><img src='api/images/produkt_bilder/" + bild + "' width='270' height='200' alt='Grafik'></center>");
                            }
                        }
                        //                        if (tabProdukteEdit.getSelectedTabNumber() == 2) {
                        //                             dfEditProdukte.prodCount++;
                        //                            ProduktHistListeEditWD.fetchData({tab: "produkte", prod_kz: dfEditProdukte.getField("prod_kz").getValue(), count: dfEditProdukte.prodCount});
                        //                        }
                    }
                });



                /*
                 ***************** Window Gesamt Protokol ********************** 
                 */
                isc.Window.create({
                    ID: "wdProduktHist",
                    title: "Tedavi-Protokol",
                    autoSize: true,
                    autoCenter: true,
                    showFooter: false,
                    showMinimizeButton: false,
                    showCloseButton: true,
                    width: 850,
                    height: 500,
                    headerIconDefaults: {
                        width: 16,
                        height: 16,
                        src: "famfam/report.png"
                    },
                    canDragReposition: true,
                    canDragResize: false,
                    showShadow: true,
                    showModalMask: true,
                    modalMaskOpacity: 10,
                    isModal: true,
                    items: [produktHistListeEinzelWD, ProduktHistListeEditWD]
                });



                isc.Window.create({
                    ID: "wdEditProdukte",
                    title: "Tedavi düzenleme",
                    autoSize: true,
                    autoCenter: true,
                    showFooter: false,
                    showMinimizeButton: false,
                    showCloseButton: false,
                    width: 510,
                    height: 295,
                    headerIconDefaults: {
                        width: 16,
                        height: 16,
                        src: "famfam/pill_add.png"
                    },
                    canDragReposition: true,
                    canDragResize: false,
                    showShadow: true,
                    showModalMask: true,
                    modalMaskOpacity: 10,
                    isModal: true,
                    items: [dfEditProdukte, HLayoutProduktEdit, pgbEditProdukte]
                });
                /*
                 * ********************** Ende edit Tedavi ********************
                 * -------------------------------------------------------------
                 */


                /*
                 * ********************** Ende Tedavi ************************
                 * -------------------------------------------------------------
                 */


                /*
                 * ************************* ANFANG USER ***************************************
                 * *****************************************************************************
                 */

                isc.ListGrid.create({
                    ID: "userListe",
                    //   header: "Daten düzenleme",
                    width: "100%", height: "100%",
                    alternateRecordStyles: true,
                    dataSource: userDS,
                    contextMenu: "",
                    autoFetchData: true,
                    taksit_count: 0,
                    showFilterEditor: true,
                    filterOnKeypress: true,
                    selectionType: "single",
                    showAllRecords: true,
                    canExpandRecords: false,
                    showGridSummary: true,
                    //                    showGroupSummary: true,
                    expansionMode: "details",
                    margin: 0,
                    fields: [{
                            name: "UserID",
                            showIf: "true",
                            width: 60
                        }, {
                            name: "benutzer",
                            width: 120,
                            showGridSummary: true, showGroupSummary: true, summaryFunction: "count"
                        }, {
                            name: "passwort",
                            width: 250
                        }, {
                            name: "admin",
                            width: 80
                        },
                        {
                            name: "status",
                            width: 80
                        },
                        {
                            name: "email",
                            width: 200
                        },
                        {
                            name: "onlineTime",
                            width: 150
                        },
                        {
                            name: "logoutTime",
                            width: 150
                        },
                        {
                            name: "loginCount",
                            width: 70,
                            showIf: "false"
                        },
                        {
                            name: "loginTime",
                            width: 150,
                            showIf: "false"
                        },
                        {
                            name: "timeOut",
                            width: 150,
                            showIf: "false"
                        }
                    ], hilites: [
                        {
                            textColor: "#000000",
                            cssText: "color:#000000;background-color:#FFDFFF;",
                            id: 0
                        }
                    ], selectionChanged: function (record, state) {
                        if (state) {
                            tsbUserEdit.setDisabled(false);
                        } else {
                            tsbUserEdit.setDisabled(true);
                        }

                    }, recordDoubleClick: function (viewer, record, recordNum, field, fieldNum, value, rawValue) {
                        dfEditUser.editRecord(record);
                        wdEditUser.show();
                        pgbEditUser.setHeight(16);
                        isc.Timer.setTimeout("btnResetEditUser.click()", 100);

                    }
                });


                /*
                 * ****************** Anfang edit Kullanıcı ************************
                 * -------------------------------------------------------------
                 */

                isc.Progressbar.create({
                    percentDone: 0,
                    ID: "pgbEditUser",
                    showTitle: true,
                    title: "",
                    height: 16,
                    length: "100%"});

                isc.ValuesManager.create({
                    ID: "dfEditUser"
                });

                isc.DynamicForm.create({
                    ID: "dfEditUserAdmin",
                    width: "100%",
                    height: "100%",
                    valuesManager: dfEditUser,
                    userCount: 0,
                    colWidths: [150, "*"],
                    numCols: 2,
                    titleOrientation: "left",
                    validateOnExit: true,
                    validateOnChange: false,
                    margin: 5,
                    fields: [{name: "UserID",
                            type: "hidden"}, {
                            type: "RowSpacer",
                            height: 10
                        },
                        {
                            name: "admin",
                            title: "Yönetici",
                            width: 150,
                            type: "select",
                            valueMap: {"J": "Admin", "N": "Admin değil"},
                            required: true,
                            changed: function (form, item, value) {
                                form.changeFunctionEditUser();
                            }

                        }, {
                            type: "RowSpacer",
                            height: 10
                        }, {
                            name: "status",
                            required: true,
                            type: "select",
                            title: "Statü",
                            valueMap: {"O": "Engellendi", "B": "Açık"},
                            width: 150,
                            changed: function (form, item, value) {
                                form.changeFunctionEditUser();
                            }
                        }, {
                            type: "RowSpacer",
                            height: 10
                        }, {
                            name: "email",
                            title: "e-Posta",
                            width: 200,
                            type: "text",
                            hint: "--- e-Posta giriniz ---",
                            showHintInField: true,
                            change: "form.changeFunctionEditUser()",
                            //            colSpan: 2,
                            length: 128,
                            validators: [{
                                    type: "lengthRange",
                                    min: 0,
                                    max: 128,
                                    stopIfFalse: false
                                },
                                {
                                    type: "regexp",
                                    validateOnExit: true,
                                    expression: "^(([a-zA-Z0-9_.\\-+])+@(([a-zA-Z0-9\\-])+\\.)+[a-zA-Z0-9]{2,4})|([ ])$",
                                    errorMessage: "E-posta adresinin aşağıdaki yapıya sahip olması gerekiyor: email@mail.de"
                                }
                            ]
                        }, {
                            type: "RowSpacer",
                            height: 30
                        }], changeFunctionEditUser: function () {
                        btnSpeichernEditUser2.setDisabled(false);
                        btnResetEditUser2.setDisabled(false);
                        btnCloseUserEdit2.setTitle("İptal et");
                        btnCloseUserEdit2.setIcon("famfam/cancel.png");
                    }});

                isc.DynamicForm.create({
                    ID: "dfEditUserPW",
                    width: "100%",
                    height: "100%",
                    valuesManager: dfEditUser,
                    userCount: 0,
                    colWidths: [150, "*"],
                    numCols: 2,
                    titleOrientation: "left",
                    validateOnExit: true,
                    validateOnChange: false,
                    margin: 5,
                    fields: [{name: "UserID",
                            type: "hidden"}, {
                            type: "RowSpacer",
                            height: 10
                        }, {
                            name: "orig_passwort",
                            title: "Geçerli şifre",
                            width: 200,
                            type: "password",
                            hint: "Geçerli şifrenizi giriniz",
                            showHintInField: true,
                            change: "form.changeFunctionEditUser()",
                            //            colSpan: 2,
                            length: 12,
                            validators: [{
                                    type: "lengthRange",
                                    min: 6,
                                    max: 12,
                                    stopIfFalse: false
                                },
                                {
                                    type: "regexp",
                                    validateOnExit: true,
                                    expression: "^([0-9a-zA-Z-+*_.]{6,12})$",
                                    errorMessage: "Şifre yalnızca 0-9 a-z A-Z - + * _ karakterlerinden oluşabilir ve en az 6 ve maks. 12 karakterden oluşabilir."
                                }
                            ]
                        }, {
                            type: "RowSpacer",
                            height: 10
                        }, {
                            name: "passwort",
                            title: "Yeni Şifre",
                            width: 200,
                            type: "password",
                            hint: "şifreyi giriniz",
                            showHintInField: true,
                            change: "form.changeFunctionEditUser()",
                            //            colSpan: 2,
                            length: 12,
                            validators: [{
                                    type: "lengthRange",
                                    min: 6,
                                    max: 12,
                                    stopIfFalse: false
                                },
                                {
                                    type: "regexp",
                                    validateOnExit: true,
                                    expression: "^([0-9a-zA-Z-+*_.]{6,12})$",
                                    errorMessage: "Şifre yalnızca 0-9 a-z A-Z - + * _ karakterlerinden oluşabilir ve en az 6 ve maks. 12 karakterden oluşabilir."
                                }
                            ]
                        }, {
                            name: "passwort2",
                            title: "Şifre tekrar",
                            width: 200,
                            type: "password",
                            hint: "şifreyi tekrarlayin",
                            showHintInField: true,
                            change: "form.changeFunctionEditUser()",
                            //            colSpan: 2,
                            length: 12,
                            validators: [{
                                    type: "lengthRange",
                                    min: 6,
                                    max: 12,
                                    stopIfFalse: false
                                },
                                {
                                    type: "regexp",
                                    validateOnExit: true,
                                    expression: "^([0-9a-zA-Z-+*_.]{6,12})$",
                                    errorMessage: "Şifre yalnızca 0-9 a-z A-Z - + * _ karakterlerinden oluşabilir ve en az 6 ve maks. 12 karakterden oluşabilir."
                                }
                            ]
                        }, {
                            type: "RowSpacer",
                            height: 30
                        }
                    ], changeFunctionEditUser: function () {
                        btnSpeichernEditUser.setDisabled(false);
                        btnResetEditUser.setDisabled(false);
                        btnCloseUserEdit.setTitle("İptal et");
                        btnCloseUserEdit.setIcon("famfam/cancel.png");
                    }
                });

                isc.IButton.create({
                    ID: "btnCloseUserEdit",
                    type: "button",
                    disabled: false,
                    icon: "famfam/door_in.png",
                    name: "btnCloseUserEdit",
                    showDisabledIcon: false,
                    title: "Kapat", width: 100,
                    click: function () {

                        if (btnCloseUserEdit.getTitle() == "İptal et") {
                            isc.ask("Gerçekten iptal etmek istiyor musunuz? Kaydedilmemiş veriler kaybolabilir.", function (value) {
                                if (value) {
                                    wdEditUser.hide();
                                }
                            }, {title: "İşlem iptal?"});
                        } else {
                            wdEditUser.hide();
                        }

                    }});

                isc.IButton.create({
                    ID: "btnSpeichernEditUser",
                    type: "button",
                    disabled: true,
                    showDisabledIcon: false,
                    icon: "famfam/database_save.png",
                    name: "btnSpeichernEditUser",
                    title: "Kaydet",
                    width: 100, //Neuen Film anlegen
                    click: function () {
                        var _percent = pgbEditUser.percentDone + parseInt(10 + (50 * Math.random()));
                        pgbEditUser.setPercentDone(_percent);
                        pgbEditUser.setTitle(_percent + "%");
                        RPCManager.send("", function (rpcResponse, data, rpcRequest) {
                            var _data = isc.JSON.decode(data); // Daten aus dem PHP (rpcResponse)
                            if (_data.response.status === 0) {  // Durum 0 bedeutet Keine Hata
                                User_ID = userListe.getSelectedRecord().UserID;
                                onRefresh("userListe");
                                isc.Timer.setTimeout("btnSpeichernEditUser.findUser()", 500);
                                btnSpeichernEditUser.pgbEditUserFunction();

                            } else { // Wenn die Validierungen Hata aufweisen dann:

                                dfEditUser.setErrors(_data.response.errors, true);
                                var _errors = dfEditUser.getErrors();
                                for (var i in _errors)
                                {
                                    isc.say("<b>Hata! </br>" + (_errors [i]) + "</b>", function (value) {
                                        if (value) {
                                            pgbEditUser.setTitle("");
                                            pgbEditUser.setPercentDone(0);
                                        }
                                    });
                                }

                            }
                        }, {// Übergabe der Parameter
                            actionURL: "api/editUser.php",
                            httpMethod: "POST",
                            contentType: "application/x-www-form-urlencoded",
                            useSimpleHttp: true,
                            params: {
                                UserID: userListe.getSelectedRecord().UserID,
                                passwort: dfEditUser.getField("passwort").getValue(),
                                passwort2: dfEditUser.getField("passwort2").getValue(),
                                orig_passwort: dfEditUser.getField("orig_passwort").getValue()}

                        }); //Ende RPC
                    }, findUser: function () {
                        var editedUser = userListe.data.find("UserID", User_ID);
                        var index = userListe.getRecordIndex(editedUser);
                        //                        userListe.deselectAllRecords();
                        userListe.selectRecord(index);
                        userListe.scrollToRow(index);
                    }, // Ende Click
                    pgbEditUserFunction: function () {
                        if (pgbEditUser.percentDone < 100) {
                            var _percent = pgbEditUser.percentDone + parseInt(10 + (50 * Math.random()));
                            pgbEditUser.setPercentDone(_percent); // Zufallswert wird berechnet

                            if (_percent <= 100) {
                                pgbEditUser.setTitle(_percent + "%");
                            } //Bis 100 wird mitgezählt
                            else {
                                pgbEditUser.setTitle("100%"); // ab 100 darf nicht mehr gezählt werden, da 100 leicht überstiegen wird.
                            }

                            isc.Timer.setTimeout("btnSpeichernEditUser.pgbEditUserFunction()", 200);
                        } else {
                            if (!dfEditUser.validate() && dfEditUser.hasErrors()) {
                                dfEditUser.setErrors();
                                var _errors = dfEditUser.getErrors();
                                for (var i in _errors)
                                {
                                    isc.say("<b>Hata! </br>" + (_errors [i]) + "</b>", function (value) {
                                        if (value) {
                                            pgbEditUser.setTitle("");
                                            pgbEditUser.setPercentDone(0);
                                        }
                                    }); // Hier wird jeder Wert des Array-Schlüssel angezeigt und das Feld oder die Feld-Bezeichnung ist irrelevant.
                                }
                            } else {

                                dfEditUser.clearValues();
                                wdEditUser.hide();
                                btnSpeichernEditUser.setDisabled(true);
                                btnResetEditUser.setDisabled(true);
                                isc.Timer.setTimeout("btnSpeichernEditUser.findUser()", 300);
                                pgbEditUser.setTitle("");
                                pgbEditUser.setPercentDone(0);
                                btnCloseUserEdit.setTitle("Kapat");
                                btnCloseUserEdit.setIcon("famfam/door_in.png");
                            }
                        }
                    }// Ende ProgressbarFunction
                });
                isc.IButton.create({
                    ID: "btnResetEditUser",
                    type: "button",
                    disabled: true,
                    icon: "famfam/arrow_undo.png",
                    showDisabledIcon: false,
                    name: "btnResetEditUser",
                    title: "Reset", width: 100,
                    click: function () {
                        dfEditUser.reset();
                        btnSpeichernEditUser.setDisabled(true);
                        btnResetEditUser.setDisabled(true);
                        btnCloseUserEdit.setTitle("Kapat");
                        btnCloseUserEdit.setIcon("famfam/door_in.png");
                    }});


                isc.HLayout.create({
                    ID: "HLayoutUserEdit",
                    height: 30,
                    width: "100%",
                    align: "center",
                    members: [btnCloseUserEdit, isc.LayoutSpacer.create({
                            width: 20
                        }), btnSpeichernEditUser, isc.LayoutSpacer.create({
                            width: 20
                        }), btnResetEditUser]});



                isc.IButton.create({
                    ID: "btnCloseUserEdit2",
                    type: "button",
                    disabled: false,
                    icon: "famfam/door_in.png",
                    name: "btnCloseUserEdit2",
                    showDisabledIcon: false,
                    title: "Kapat", width: 100,
                    click: function () {

                        if (btnCloseUserEdit2.getTitle() == "İptal et") {
                            isc.ask("Gerçekten iptal etmek istiyor musunuz? Kaydedilmemiş veriler kaybolabilir.", function (value) {
                                if (value) {
                                    wdEditUser.hide();
                                }
                            }, {title: "İşlem iptal?"});
                        } else {
                            wdEditUser.hide();
                        }

                    }});

                isc.IButton.create({
                    ID: "btnSpeichernEditUser2",
                    type: "button",
                    disabled: true,
                    showDisabledIcon: false,
                    icon: "famfam/database_save.png",
                    name: "btnSpeichernEditUser2",
                    title: "Kaydet",
                    width: 100,
                    click: function () {
                        var _percent = pgbEditUser.percentDone + parseInt(10 + (50 * Math.random()));
                        pgbEditUser.setPercentDone(_percent);
                        pgbEditUser.setTitle(_percent + "%");
                        RPCManager.send("", function (rpcResponse, data, rpcRequest) {
                            var _data = isc.JSON.decode(data); // Daten aus dem PHP (rpcResponse)
                            if (_data.response.status === 0) {  // Durum 0 bedeutet Keine Hata
                                User_ID = userListe.getSelectedRecord().UserID;
                                onRefresh("userListe");
                                isc.Timer.setTimeout("btnSpeichernEditUser2.findUser()", 500);
                                btnSpeichernEditUser2.pgbEditUserFunction();

                            } else { // Wenn die Validierungen Hata aufweisen dann:

                                dfEditUserAdmin.setErrors(_data.response.errors, true);
                                var _errors = dfEditUserAdmin.getErrors();
                                for (var i in _errors)
                                {
                                    isc.say("<b>Hata! </br>" + (_errors [i]) + "</b>", function (value) {
                                        if (value) {
                                            pgbEditUser.setTitle("");
                                            pgbEditUser.setPercentDone(0);
                                        }
                                    });
                                }

                            }
                        }, {// Übergabe der Parameter
                            actionURL: "api/editUser2.php",
                            httpMethod: "POST",
                            contentType: "application/x-www-form-urlencoded",
                            useSimpleHttp: true,
                            params: {
                                UserID: dfEditUserAdmin.getField("UserID").getValue(),
                                admin: dfEditUserAdmin.getField("admin").getValue(),
                                email: dfEditUserAdmin.getField("email").getValue(),
                                status: dfEditUserAdmin.getField("status").getValue()}

                        }); //Ende RPC
                    }, findUser: function () {
                        var editedUser = userListe.data.find("UserID", User_ID);
                        var index = userListe.getRecordIndex(editedUser);
                        //                        userListe.deselectAllRecords();
                        userListe.selectRecord(index);
                        userListe.scrollToRow(index);
                    }, // Ende Click
                    pgbEditUserFunction: function () {
                        if (pgbEditUser.percentDone < 100) {
                            var _percent = pgbEditUser.percentDone + parseInt(10 + (50 * Math.random()));
                            pgbEditUser.setPercentDone(_percent); // Zufallswert wird berechnet

                            if (_percent <= 100) {
                                pgbEditUser.setTitle(_percent + "%");
                            } //Bis 100 wird mitgezählt
                            else {
                                pgbEditUser.setTitle("100%"); // ab 100 darf nicht mehr gezählt werden, da 100 leicht überstiegen wird.
                            }

                            isc.Timer.setTimeout("btnSpeichernEditUser2.pgbEditUserFunction()", 200);
                        } else {
                            if (!dfEditUserAdmin.validate() && dfEditUserAdmin.hasErrors()) {
                                dfEditUserAdmin.setErrors();
                                var _errors = dfEditUserAdmin.getErrors();
                                for (var i in _errors)
                                {
                                    isc.say("<b>Hata! </br>" + (_errors [i]) + "</b>", function (value) {
                                        if (value) {
                                            pgbEditUser.setTitle("");
                                            pgbEditUser.setPercentDone(0);
                                        }
                                    }); // Hier wird jeder Wert des Array-Schlüssel angezeigt und das Feld oder die Feld-Bezeichnung ist irrelevant.
                                }
                            } else {

                                dfEditUserAdmin.clearValues();
                                wdEditUser.hide();
                                btnSpeichernEditUser2.setDisabled(true);
                                btnResetEditUser2.setDisabled(true);
                                isc.Timer.setTimeout("btnSpeichernEditUser2.findUser()", 300);
                                pgbEditUser.setTitle("");
                                pgbEditUser.setPercentDone(0);
                                btnCloseUserEdit2.setTitle("Kapat");
                                btnCloseUserEdit2.setIcon("famfam/door_in.png");
                            }
                        }
                    }// Ende ProgressbarFunction
                });
                isc.IButton.create({
                    ID: "btnResetEditUser2",
                    type: "button",
                    disabled: true,
                    icon: "famfam/arrow_undo.png",
                    showDisabledIcon: false,
                    name: "btnResetEditUser2",
                    title: "Reset", width: 100,
                    click: function () {
                        dfEditUserAdmin.reset();
                        btnSpeichernEditUser2.setDisabled(true);
                        btnResetEditUser2.setDisabled(true);
                        btnCloseUserEdit2.setTitle("Kapat");
                        btnCloseUserEdit2.setIcon("famfam/door_in.png");
                    }});


                isc.HLayout.create({
                    ID: "HLayoutUserEdit2",
                    height: 30,
                    width: "100%",
                    align: "center",
                    members: [btnCloseUserEdit2, isc.LayoutSpacer.create({
                            width: 20
                        }), btnSpeichernEditUser2, isc.LayoutSpacer.create({
                            width: 20
                        }), btnResetEditUser2]});


                isc.VLayout.create({
                    ID: "VLayoutUserEditFormBtn2",
                    height: 30,
                    width: "100%",
                    align: "center",
                    members: [dfEditUserAdmin, HLayoutUserEdit2]});

                isc.VLayout.create({
                    ID: "VLayoutUserEditFormBtn",
                    height: 30,
                    width: "100%",
                    align: "center",
                    members: [dfEditUserPW, HLayoutUserEdit]});


                isc.TabSet.create({
                    ID: "tabUser",
                    width: "100%",
                    height: "100%",
                    count: 0,
                    tabs: [
                        {title: "Kullanıcı düzenleme",
                            pane: VLayoutUserEditFormBtn2},
                        {title: "Sifre degistir",
                            pane: VLayoutUserEditFormBtn}
                    ],
                    tabSelected: function (tabSet, tabNum, tabPane, ID, tab, name) {

                    }
                });



                isc.Window.create({
                    ID: "wdEditUser",
                    title: "Kullanıcı düzenleme",
                    autoSize: false,
                    autoCenter: true,
                    showFooter: false,
                    showMinimizeButton: false,
                    showCloseButton: false,
                    width: 440,
                    height: 300,
                    headerIconDefaults: {
                        width: 16,
                        height: 16,
                        src: "famfam/user_edit.png"
                    },
                    canDragReposition: true,
                    canDragResize: false,
                    showShadow: true,
                    showModalMask: true,
                    modalMaskOpacity: 10,
                    isModal: true,
                    items: [tabUser, pgbEditUser]
                });
                /*
                 * ********************** Ende edit Kullanıcı **********************
                 * -------------------------------------------------------------
                 */


                /*
                 * ************************* ENDE USER *****************************************
                 * *****************************************************************************
                 */

                /*
                 * ************************* ANFANG ABRECHNUNG *********************************
                 * *****************************************************************************
                 */

                isc.Label.create({
                    padding: 0,
                    ID: "lblSumMenge",
                    width: "100%",
                    height: "100%",
                    align: "center"
                });

                isc.Label.create({
                    padding: 0,
                    ID: "lblSumEinzelpr_netto",
                    width: "100%",
                    height: "100%",
                    align: "center"
                });

                isc.Label.create({
                    padding: 0,
                    ID: "lblSumMwst_einzelpr",
                    width: "100%",
                    height: "100%",
                    align: "center"
                });

                isc.Label.create({
                    padding: 0,
                    ID: "lblSumEinzelpr_brutto",
                    width: "100%",
                    height: "100%",
                    align: "center"
                });

                isc.Label.create({
                    padding: 0,
                    ID: "lblSumGesamtpr_netto",
                    width: "100%",
                    height: "100%",
                    align: "center"
                });

                isc.Label.create({
                    padding: 0,
                    ID: "lblSumMwst_gesamtpr",
                    width: "100%",
                    height: "100%",
                    align: "center"
                });

                isc.Label.create({
                    padding: 0,
                    ID: "lblSumGesamtpr_brutto",
                    width: "100%",
                    height: "100%",
                    align: "center"
                });

                isc.ToolStrip.create({// Toolstrip
                    ID: "tsAbrechnungsSummen",
                    width: "100%",
                    height: 24,
                    members: [lblSumMenge, /*lblSumEinzelpr_netto, lblSumMwst_einzelpr, lblSumEinzelpr_brutto, */lblSumGesamtpr_netto, lblSumMwst_gesamtpr, lblSumGesamtpr_brutto]});

                /*
                 * ********************** LISTGRID ABRECHNUNG **********************************
                 * *****************************************************************************
                 */

                isc.ListGrid.create({
                    ID: "abrechnungsListe",
                    //   header: "Daten düzenleme",
                    width: "100%", height: "100%",
                    alternateRecordStyles: true,
                    dataSource: verkaeufeDS,
                    contextMenu: "",
                    autoFetchData: false,
                    taksit_count: 0,
                    showFilterEditor: false,
                    filterOnKeypress: true,
                    selectionType: "single",
                    showAllRecords: true,
                    canExpandRecords: false,
                    //                    showGridSummary: true,
                    //                    showGroupSummary: true,
                    expansionMode: "details",
                    margin: 0,
                    fields: [{name: "lfd_nr", type: "text", showIf: "false"},
                        {name: "prod_kz", type: "text", showIf: "false"},
                        {name: "bezeichnung", type: "text", showIf: "true", width: 250},
                        {name: "name", type: "text", width: 200, showIf: "false"},
                        {name: "verkauf_an", type: "text", showIf: "false"},
                        {name: "menge", type: "text", width: 60, align: "right"},
                        {name: "preis_kat", type: "text", width: 50, align: "center"},
                        //                            {name: "brutto_preis", type: "text", width: 80, align: "right"},
                        {name: "mwst", type: "text", width: 50, align: "right", showIf: "false"},
                        //                            {name: "mwst_einzelpr", type: "text", width: 80, align: "right", showIf: "false"},
                        {name: "brutto_preis", type: "text", width: 80, align: "right"},
                        //                            {name: "gesamtpr_brutto", type: "text", width: 80, align: "right"},
                        {name: "mwst_gesamtpr", type: "text", width: 90, align: "right"},
                        {name: "gesamtpr_brutto", type: "text", width: 100, align: "right"},
                        {name: "datum", type: "date", width: 80, showIf: "false"},
                        {name: "beleg_nr", type: "text", width: 80, showIf: "false"},
                        {name: "bemerkung", type: "text", width: "*"}
                    ], gridComponents: [/*"filterEditor",*/"header", "body", tsAbrechnungsSummen],
                    selectionChanged: function (record, state) {
                        if (state) {
                            //                            tsbPDFAbrechnung.setDisabled(false);
                            tsbAbrechnungEdit.setDisabled(false);
                            tsbPositionDelete.setDisabled(false);
                        } else {
                            //                            tsbPDFAbrechnung.setDisabled(true);
                            tsbAbrechnungEdit.setDisabled(true);
                            tsbPositionDelete.setDisabled(true);
                        }

                    }, dataArrived: function () {

                    }, recordDoubleClick: function (viewer, record, recordNum, field, fieldNum, value, rawValue) {
                        wdEditPositionen.show();
                        dfEditAbrechnung.editRecord(record);
                        pgbEditAbrechnung.setHeight(16);
                        isc.Timer.setTimeout("btnResetAbrechnungEdit2.click()", 100);

                    }, abrechnungsSummenFunction: function () {
                        if (abrechnungsListe.getTotalRows() > 0) {
                            var beleg_nr = abrechnungsTree.getSelectedRecord().beleg_nr;
                        } else {
                            var beleg_nr = null;
                        }

                        RPCManager.send("", function (rpcResponse, data, rpcRequest) {
                            var _data = isc.JSON.decode(data); // Daten aus dem PHP (rpcResponse)
                            if (_data.response.status === 0) {  // Durum 0 bedeutet Keine Hata

                                var einzelpr_netto = (_data.response.data["einzelpr_netto"]);
                                var einzelpr_brutto = (_data.response.data["einzelpr_brutto"]);
                                var mwst_einzelpr = (_data.response.data["mwst_einzelpr"]);
                                var menge = (_data.response.data["menge"]);
                                var gesamtpr_brutto = (_data.response.data["gesamtpr_brutto"]);
                                var mwst_gesamtpr = (_data.response.data["mwst_gesamtpr"]);
                                var gesamtpr_brutto = (_data.response.data["gesamtpr_brutto"]);

                                if (abrechnungsListe.getTotalRows() > 0) {
                                    lblSumMenge.setContents("Miktar: " + menge);
                                    lblSumEinzelpr_netto.setContents("Einzelpr. (netto): " + einzelpr_netto + "₺ ");
                                    lblSumMwst_einzelpr.setContents("KDV Fiyat: " + mwst_einzelpr + "₺ ");
                                    lblSumEinzelpr_brutto.setContents("Tek fiyat (brutto): " + einzelpr_brutto + "₺ ");
                                    lblSumGesamtpr_netto.setContents("Toplam tutarı  (netto): " + gesamtpr_brutto + "₺ ");
                                    lblSumMwst_gesamtpr.setContents("KDV: " + mwst_gesamtpr + "₺ ");
                                    lblSumGesamtpr_brutto.setContents("<b>Toplam tutarı  (brutto): " + gesamtpr_brutto + "₺ </b>");
                                } else {
                                    lblSumMenge.setContents("&nbsp;");
                                    lblSumEinzelpr_netto.setContents("&nbsp;");
                                    lblSumMwst_einzelpr.setContents("&nbsp;");
                                    lblSumEinzelpr_brutto.setContents("&nbsp;");
                                    lblSumGesamtpr_netto.setContents("&nbsp;");
                                    lblSumMwst_gesamtpr.setContents("&nbsp;");
                                    lblSumGesamtpr_brutto.setContents("&nbsp;");
                                }

                            } else if (_data.response.status === 99) {

                                lblSumMenge.setContents("&nbsp;");
                                lblSumEinzelpr_netto.setContents("&nbsp;");
                                lblSumMwst_einzelpr.setContents("&nbsp;");
                                lblSumEinzelpr_brutto.setContents("&nbsp;");
                                lblSumGesamtpr_netto.setContents("&nbsp;");
                                lblSumMwst_gesamtpr.setContents("&nbsp;");
                                lblSumGesamtpr_brutto.setContents("&nbsp;");
                            } else { // Wenn die Validierungen Hata aufweisen dann:

                                dfEditAbrechnung.setErrors(_data.response.errors, true);
                                var _errors = dfEditAbrechnung.getErrors();
                                for (var i in _errors)
                                {
                                    isc.say("<b>Hata! </br>" + (_errors [i]) + "</b>");
                                }
                            }
                        }, {// Übergabe der Parameter
                            actionURL: "api/abrechnungsSummen.php",
                            httpMethod: "POST",
                            contentType: "application/x-www-form-urlencoded",
                            useSimpleHttp: true,
                            params: {
                                beleg_nr: beleg_nr
                            }

                        }); //Ende RPC                    
                    }
                });

                /*
                 * ********************** TREEGRID ABRECHNUNG **********************************
                 * *****************************************************************************
                 */

                isc.ListGrid.create({
                    ID: "abrechnungsTree",
                    width: 250,
                    height: "100%",
                    count: 0,
                    count2: 0,
                    contextMenu: "",
                    showResizeBar: true,
                    animateFolders: false,
                    dataSource: "verkaeufeDS_Tree",
                    autoFetchData: true,
                    canMultiGroup: true,
                    leaveScrollbarGap: false,
                    //        nodeIcon: "icons/table.png",
                    //        folderIcon: "icons/folder.png",
                    //        showOpenIcons: false,
                    //        showDropIcons: false,
                    //        closedIconSuffix: "",
                    //                hilites: hiliteVersandListe,
                    showGroupSummary: false,
                    showGroupSummaryInHeader: false,
                    fields: [
                        {
                            name: "datum",
                            type: "date",
                            width: 1,
                            groupingMode: "dayOfMonth",
                            defaultGroupingMode: "dayOfMonth",
                            getGroupValue: function (value, record, field, fieldName, grid) {
                                var _datum;
                                var _tag = value.getDate();
                                var tag_;
                                var _monat = value.getMonth() + 1;

                                if (_monat.toString().length == 1) {
                                    _datum = "0" + _monat.toString();
                                } else {
                                    _datum = _monat;
                                }

                                if (_tag.toString().length == 1) {
                                    tag_ = "0" + _tag.toString();
                                } else {
                                    tag_ = _tag;
                                }

                                return tag_ + '.' + _datum + '.' + value.getFullYear();
                            },
                            getGroupTitle: function (groupValue, groupNode, field, fieldName, grid) {// Mit dieser Funktion wird der Gruppenwert
                                // der Gruppierung als Titel übergeben.
                                var datensatz = "Hasta";

                                baseTitle = "<b>" + groupValue + " (" + groupNode.groupMembers.length + " " + datensatz + ")"; // groupNode berechnet die Datensätze
                                return baseTitle; // und gibt diese im Gruppentitel wieder.
                            }
                        },
                        {
                            title: "Hasta",
                            name: "name",
                            type: "text",
                            width: 1,
                            getGroupValue: function (value, record, field, fieldName, grid) {
                                //                            var anzahl = record.anzahl;
                                var kunden_nr = record.verkauf_an;
                                var startTime = record.startTime;
                                var endTime = record.endTime;
                                //                            var pos = "";
                                //                            if (anzahl > 1) {
                                //                    pos = "Pos.";
                                //                }
                                //                else {
                                //                    pos = "Pos.";}

                                return /*"<b> " + kunden_nr + " " + value */  startTime + " - " + endTime;
                            }
                        }, {
                            name: "anzahl",
                            type: "text",
                            width: 10,
                            showIf: "false"
                        }, {
                            name: "startTime",
                            type: "text",
                            width: 40,
                            showIf: "false"
                        }, {
                            name: "endTime",
                            type: "text",
                            width: 40,
                            showIf: "false"
                        },
                        {
                            title: "Fatura no",
                            name: "beleg_nr",
                            type: "text",
                            width: 60,
                            showGrou1pSummary: false
                        },
                        {
                            title: "Hasta",
                            name: "name",
                            type: "text",
                            width: "*",
                            showGrou1pSummary: false
                        }
                    ],
                    groupByField: ['datum', 'name'],
                    groupStartOpen: "none"
                            //sortField: "datum",
                            // initialSort: [{property: "datum", direction: "descending"}, {property: "mandant", direction: "ascending"}, {property: "laufNr", direction: "descending"}],

                    , dataArrived: function () {
                        abrechnungsTree.selectFirstRecord();
                        //            isc.Timer.setTimeout("abrechnungsListe.abrechnungsSummenFunction()",1000);
                    }
                    , recordClick: function (viewer, record, recordNum, field, fieldNum, value, rawValue) {
                        //                        abrechnungsTree.count2++;
                        //                        if (abrechnungsTree.getTotalRows() > 0) {
                        //                            abrechnungsListeEdit.fetchData({count: abrechnungsTree.count2, beleg_nr: abrechnungsTree.getSelectedRecord().beleg_nr});
                        //                        }
                        //        isc.Timer.setTimeout("abrechnungsListe.abrechnungsSummenFunction()",100);
                    }
                    , selectFirstRecord: function () {
                        if (abrechnungsTree.count <= 100) {
                            if (!Array.isLoading(abrechnungsTree.getRecord(0)) && abrechnungsTree.getTotalRows() > 0) {
                                abrechnungsTree.openFolder(abrechnungsTree.getRecord(0));
                                abrechnungsTree.openFolder(abrechnungsTree.getRecord(1));
                                //                        abrechnungsTree.openFolder(abrechnungsTree.getRecord(2));
                                isc.Timer.setTimeout("abrechnungsTree.selectRecord(2)", 100);
                                isc.Timer.setTimeout("abrechnungsTree.recordClick()", 500);

                            } else {
                                abrechnungsTree.count = abrechnungsTree.count + 1;
                                if (abrechnungsTree.getTotalRows() == 0) {
                                    abrechnungsListe.setData([]);

                                    lblSumMenge.setContents("&nbsp;");
                                    lblSumEinzelpr_netto.setContents("&nbsp;");
                                    lblSumMwst_einzelpr.setContents("&nbsp;");
                                    lblSumEinzelpr_brutto.setContents("&nbsp;");
                                    lblSumGesamtpr_netto.setContents("&nbsp;");
                                    lblSumMwst_gesamtpr.setContents("&nbsp;");
                                    lblSumGesamtpr_brutto.setContents("&nbsp;");

                                }
                                isc.Timer.setTimeout("abrechnungsTree.selectFirstRecord()", 200);

                            }
                        }
                    }, selectionChanged: function (record, state) {

                        if (state) {
                            tsbBookAbrechnung.setDisabled(false);
                            tsbPDFAbrechnung.setDisabled(false);
                            tsbAddPosition.setDisabled(false);
                        } else {
                            tsbAddPosition.setDisabled(true);
                            tsbBookAbrechnung.setDisabled(true);
                            tsbPDFAbrechnung.setDisabled(true);
                        }

                    }
                    , recordDoubleClick: function (viewer, record, recordNum, field, fieldNum, value, rawValue) {

                        wdEditAbrechnung.show();
                        dfEditAbrechnung.editRecord(record);
                        pgbEditAbrechnung.setHeight(16);
                        isc.Timer.setTimeout("btnResetAbrechnungEdit2.click()", 100);
                        abrechnungsTree.count2++;
                        if (abrechnungsTree.getTotalRows() > 0) {
                            abrechnungsListeEdit.fetchData({count: abrechnungsTree.count2, beleg_nr: abrechnungsTree.getSelectedRecord().beleg_nr});
                        }
                        isc.Timer.setTimeout("abrechnungsListe.abrechnungsSummenFunction()", 100);
                    }
                });



                /*
                 * ********************** LISTGRID EDIT ABRECHNUNG *****************************
                 * *****************************************************************************
                 */
                isc.ListGrid.create({
                    ID: "abrechnungsListeEdit",
                    //   header: "Daten düzenleme",
                    width: 795, height: "100%",
                    alternateRecordStyles: true,
                    dataSource: verkaeufeDS,
                    contextMenu: "",
                    autoFetchData: false,
                    taksit_count: 0,
                    showFilterEditor: false,
                    filterOnKeypress: true,
                    selectionType: "single",
                    showAllRecords: true,
                    leaveScrollbarGap: false,
                    canExpandRecords: false,
                    showGridSummary: true,
                    showGroupSummary: true,
                    expansionMode: "details",
                    margin: 0,
                    fields: [{name: "lfd_nr", type: "text", showIf: "false"},
                        {name: "prod_kz", type: "text", showIf: "false"},
                        {name: "bezeichnung", type: "text", showIf: "true", width: 150},
                        {name: "menge", type: "text", width: 50, align: "right"},
                        {name: "preis_kat", type: "text", title: "Kat.", width: 50, align: "center"},
                        {name: "brutto_preis", type: "text", width: 80, align: "right", recordSummaryFunction: "multiplier",
                            summaryFunction: "sum",
                            formatCellValue: function (value) {
                                if (isc.isA.Number(value)) {
                                    return value.toCurrencyString("₺ ");
                                }
                                return value;
                            }},
                        {name: "mwst", type: "text", width: 40, align: "right"},
                        //                            {name: "mwst_einzelpr", type: "text", width: 80, align: "right", recordSummaryFunction: "multiplier",
                        //                                summaryFunction: "sum",
                        //                                formatCellValue: function (value) {
                        //                                    if (isc.isA.Number(value)) {
                        //                                        return value.toCurrencyString("₺ ");
                        //                                    }
                        //                                    return value;
                        //                                }},
                        //                            {name: "brutto_preis", type: "text", width: 80, align: "right", recordSummaryFunction: "multiplier",
                        //                                summaryFunction: "sum",
                        //                                formatCellValue: function (value) {
                        //                                    if (isc.isA.Number(value)) {
                        //                                        return value.toCurrencyString("₺ ");
                        //                                    }
                        //                                    return value;
                        //                                }},
                        //                            {name: "gesamtpr_brutto", type: "text", width: 80, align: "right",
                        //                                summaryFunction: "sum",
                        //                                formatCellValue: function (value) {
                        //                                    if (isc.isA.Number(value)) {
                        //                                        return value.toCurrencyString("₺ ");
                        //                                    }
                        //                                    return value;
                        //                                }},
                        {name: "mwst_gesamtpr", type: "text", width: 90, align: "right", recordSummaryFunction: "multiplier",
                            summaryFunction: "sum",
                            formatCellValue: function (value) {
                                if (isc.isA.Number(value)) {
                                    return value.toCurrencyString("₺ ");
                                }
                                return value;
                            }},
                        {name: "gesamtpr_brutto", type: "text", width: 100, align: "right", recordSummaryFunction: "multiplier",
                            summaryFunction: "sum",
                            formatCellValue: function (value) {
                                if (isc.isA.Number(value)) {
                                    return value.toCurrencyString("₺ ");
                                }
                                return value;
                            }},
                        {name: "bemerkung", type: "text", width: "*"}
                    ], selectionChanged: function (record, state) {
                        if (state) {
                            //                            tsbPDFAbrechnung.setDisabled(false);
                            tsbAbrechnungEdit.setDisabled(false);
                            tsbPositionDelete.setDisabled(false);
                        } else {
                            //                            tsbPDFAbrechnung.setDisabled(true);
                            tsbAbrechnungEdit.setDisabled(true);
                            tsbPositionDelete.setDisabled(true);
                        }

                    }, dataArrived: function () {
                    }, recordDoubleClick: function (viewer, record, recordNum, field, fieldNum, value, rawValue) {
                        record = abrechnungsListeEdit.getSelectedRecord();
                        dfEditAbrechnung2.editRecord(record);
                        wdEditPositionen.show();
                        pgbEditAbrechnung.setHeight(16);
                        preisFunction(dfEditAbrechnung2);

                    }
                });


                /*
                 * ****************** Anfang neue Abrechnung ********************
                 * -------------------------------------------------------------
                 */

                isc.Progressbar.create({
                    percentDone: 0,
                    ID: "pgbAddAbrechnung",
                    showTitle: true,
                    title: "",
                    height: 16,
                    length: "100%"});

                isc.DynamicForm.create({
                    ID: "dfAddAbrechnung",
                    width: "100%",
                    height: "100%",
                    verkaufCount: 0,
                    colWidths: [140, 100, 140, "*"],
                    numCols: 4,
                    titleOrientation: "left",
                    validateOnExit: true,
                    validateOnChange: false,
                    margin: 5,
                    fields: [{
                            name: "beleg_nr",
                            title: " Fatura no",
                            type: "text",
                            colSpan: 2,
                            disabled: false,
                            width: 120,
                            required: true,
                            change: "return false",
                            //                            changed: function(form, item, value) {
                            //                                form.changeFunctionAddAbrechnung();
                            //                                form.findKunden_nr(form);
                            //                                
                            //                            },
                            icons: [{
                                    src: "famfam/arrow_refresh.png",
                                    prompt: "Fatura numarası oluşturur",
                                    hoverWidth: 100,
                                    hoverDelay: 700,
                                    click: function (form, item, value) {
                                        form.belegNrBerechnen(form);
                                        form.getField("kunden_nr").setDisabled(false);
                                        form.getField("datum").setDisabled(false);
                                        form.changeFunctionAddAbrechnung();
                                    }
                                }]
                        }, {
                            name: "kunden_nr",
                            title: "Hasta",
                            colSpan: 2,
                            width: 260,
                            optionDataSource: kundenDS,
                            valueField: "lfd_nr",
                            displayField: "name_voll",
                            type: "comboBox",
                            required: true,
                            changed: function (form, item, value) {
                                form.changeFunctionAddAbrechnung();
                            },
                            pickListProperties: {showShadow: true, showFilterEditor: false, showHeader: true},
                            pickListWidth: 400,
                            pickListFields: [
                                {name: "lfd_nr", width: 60}, {name: "kunden_nr", width: 80}, {name: "name_voll", width: "*"}, {name: "geburtstag", width: 80}],
                            getPickListFilterCriteria: function () {
                                dfAddAbrechnung.verkaufCount++;
                                var filter = {
                                    count: dfAddAbrechnung.verkaufCount, aktiv: "ja", name_voll: dfAddAbrechnung.getField("kunden_nr").getDisplayValue()
                                };
                                return filter;
                            }, icons: [{
                                    src: "famfam/add.png",
                                    prompt: "Yeni Hasta kayıt et",
                                    hoverWidth: 100,
                                    hoverDelay: 700,
                                    click: function (form, item, value) {
                                        tsbAddKunden.action();
                                    }
                                }]

                        },
                        {
                            name: "prod_kz",
                            title: "Tedavi",
                            width: 260,
                            colSpan: 2,
                            optionDataSource: produkteDS,
                            valueField: "prod_kz",
                            displayField: "bezeichnung",
                            type: "select",
                            required: true,
                            changed: function (form, item, value) {
                                form.changeFunctionAddAbrechnung();
                                preisFunction(form);
                            },
                            pickListProperties: {showShadow: true, showFilterEditor: false, showHeader: true},
                            pickListWidth: 280,
                            pickListFields: [
                                {name: "prod_kz", width: 50}, {name: "bezeichnung", width: "*"}],
                            getPickListFilterCriteria: function () {
                                dfAddAbrechnung.verkaufCount++;
                                var filter = {
                                    count: dfAddAbrechnung.verkaufCount, aktiv: "ja", beleg_nr: dfAddAbrechnung.getField("beleg_nr").getValue()};
                                return filter;
                            }

                       },
                        {
                            name: "preis_kat",
                            title: "Fiyat kategorisi",
                            width: 150,
                            colSpan: 2,
                            valueMap: {"1": "tam Fiyat", "2": "indirimli Fiyat", "3": "ücretsiz", "4": "özel Fiyat"},
                            type: "radioGroup",
                            defaultValue: "1",
                            required: true,
                            vertical: false,
                            changed: function (form, item, value) {
                                form.changeFunctionAddAbrechnung();
                                preisFunction(form);
                                if (value == "4") {
                                    form.getField("brutto_preis_").show();
                                    form.getField("brutto_preis_").focusInItem();
                                    form.getField("brutto_preis").hide();
                                    form.getField("mwst_").show();
                                    form.getField("mwst_").focusInItem();
                                    form.getField("mwst").hide();
                                } else {
                                    form.getField("brutto_preis_").hide();
                                    form.getField("mwst_").hide();
                                    form.getField("brutto_preis").show();
                                    form.getField("mwst").show();
                                }
                            }}, {
                            name: "menge",
                            required: true,
                            colSpan: 2,
                            type: "spinner",
                            title: "Miktar",
                            defaultValue: 1,
                            width: 100,
                            change: "preisFunction(dfAddAbrechnung);",
                            icons: [{
                                    src: "famfam/calculator.png",
                                    prompt: "Fiyatı yeniden hesapla",
                                    hoverWidth: 100,
                                    hoverDelay: 700,
                                    click: function (form, item, value) {
                                        preisFunction(form);
                                    }
                                }], keyPress: function () {
                                if (isc.Event.getKey() == "Enter") {
                                    preisFunction(dfAddAbrechnung);
                                    dfAddAbrechnung.getField("mwst").focusInItem();
                                    dfAddAbrechnung.getField("menge").focusInItem();
                                    dfAddAbrechnung.changeFunctionAddAbrechnung();
                                }
                            },
                            changed: function (form, item, value) {
                                form.changeFunctionAddAbrechnung();

                            },
                            keyPressFilter: "[0-9]"
                        }, {
                            name: "datum",
                            title: "Fatura tarihi",
                            colSpan: 2,
                            type: "date",
                            width: 120,
                            required: true,
                            changed: function (form, item, value) {
                                form.changeFunctionAddAbrechnung();
                                getNextTermin(form, value);
                            }
                        },
                        {
                            name: "startTime", title: "Başlangıç",
                            //                                hint: "Picklist based time input",
                            editorType: "TimeItem",
                            useTextField: false,
                            hourItemTitle: "Saat",
                            minuteItemTitle: "Dak",
                            showSecondItem: false,
                            colSpan: 2,
                            minuteIncrement: 5,
                            changed: function (form, item, value) {
                                form.changeFunctionAddAbrechnung();
                                setEndTime(value, form, "start");
                            }
                        },
                        {
                            name: "endTime", title: "Son",
                            //                                hint: "Picklist based time input",
                            editorType: "TimeItem",
                            useTextField: false,
                            hourItemTitle: "Saat",
                            minuteItemTitle: "Dak",
                            showSecondItem: false,
                            colSpan: 2,
                            minuteIncrement: 5,
                            changed: function (form, item, value) {
                                form.changeFunctionAddAbrechnung();
                                setEndTime(value, form, "end");
                            }
                        }, {
                            name: "zahlungsziel",
                            title: "Ödeme şekli",
                            colSpan: 2,
                            type: "select",
                            defaultValue: "S",
                            width: 120,
                            valueMap: {"S": "Nakit", "Z": "Kredi Kart"},
                            required: true,
                            changed: function (form, item, value) {
                                form.changeFunctionAddAbrechnung();
                                //                                if (value == "S") {
                                //                                    form.getField("zahlfrist").setDisabled(true);
                                //                                    form.getField("zahldatum").setDisabled(true);
                                //                                }
                                //                                if (value == "Z") {
                                //                                    form.getField("zahlfrist").setDisabled(false);
                                //                                    form.getField("zahldatum").setDisabled(false);
                                //                                }

                            }
                        },
                        {
                            defaultValue: "<b>Fiyat derleme</b>",
                            name: "Abrechnung",
                            type: "section",
                            colSpan: 4,
                            sectionExpanded: true,
                            canCollapse: false,
                            itemIds: ["mwst", "brutto_preis", "mwst_gesamtpr", "gesamtpr_brutto"]
                        }, 
                        {
                            name: "mwst",
                            title: "KDV",
                            type: "text",
                            defaultValue: 0,
                            colSpan: 2,
                            width: 60,
                            required: true,
                            change: "return false"
                        }, 
                        {
                            name: "mwst_",
                            title: "KDV",
                            type: "text",
                            defaultValue: 18,
                            colSpan: 2,
                            selectOnFocus: true,
                            selectOnClick: true,
                            width: 60,
                            keyPressFilter: "[0-9,]",
                            change: "preisFunction(dfAddAbrechnung);"
                        }, 
                        {
                            name: "brutto_preis",
                            title: "Fiyat",
                            width: 100,
                            required: true,
                            colSpan: 2,
                            defaultValue: 0,
                            type: "text",
                            change: "return false"
                        }, {
                            name: "brutto_preis_",
                            title: "Fiyat",
                            width: 100,
                            required: true,
                            colSpan: 2,
                            selectOnFocus: true,
                            selectOnClick: true,
                            defaultValue: 0,
                            type: "text",
                            keyPressFilter: "[0-9,]",
                            change: "preisFunction(dfAddAbrechnung);"
                        }, {
                            name: "mwst_gesamtpr",
                            title: "KDV Toplam tutarı",
                            width: 100,
                            colSpan: 2,
                            required: true,
                            defaultValue: 0,
                            type: "text",
                            change: "return false"
                        }, {
                            name: "gesamtpr_brutto",
                            title: "Toplam tutarı",
                            width: 100,
                            colSpan: 2,
                            required: true,
                            defaultValue: 0,
                            type: "text",
                            change: "return false"
                        }, {
                            type: "RowSpacer",
                            height: 10
                        }, {
                            defaultValue: "<b>Faturalandırmaya  not ekleyin</b>",
                            name: "Notiz",
                            type: "section",
                            colSpan: 4,
                            sectionExpanded: false,
                            canCollapse: true,
                            itemIds: ["bemerkung"]
                        }, {
                            name: "bemerkung",
                            title: "Not",
                            width: 250,
                            colSpan: 2,
                            length: 260,
                            type: "textArea",
                            changed: function (form, item, value) {
                                form.changeFunctionAddAbrechnung();
                            }
                        }, {
                            type: "RowSpacer",
                            height: 10
                        }
                    ], changeFunctionAddAbrechnung: function () {
                        btnSpeichernAbrechnungNeu.setDisabled(false);
                        btnResetAbrechnungNeu.setDisabled(false);
                        btnCloseAbrechnungNeu.setTitle("İptal et");
                        btnCloseAbrechnungNeu.setIcon("famfam/cancel.png");
                    }, belegNrBerechnen: function (form) {
                        RPCManager.send("", function (rpcResponse, data, rpcRequest) {
                            var _data = isc.JSON.decode(data); // Daten aus dem PHP (rpcResponse)
                            if (_data.response.status === 0) {  // Durum 0 bedeutet Keine Hata

                                neueBelegNr = _data.response.data["beleg_nr"];
                                form.getField("beleg_nr").setValue(neueBelegNr);

                            } else { // Wenn die Validierungen Hata aufweisen dann:

                                form.setErrors(_data.response.errors, true);
                                var _errors = form.getErrors();
                                for (var i in _errors)
                                {
                                    isc.say("<b>Hata! </br>" + (_errors [i]) + "</b>");
                                }
                            }
                        }, {// Übergabe der Parameter
                            actionURL: "api/beleg_nr_berechnen.php",
                            httpMethod: "POST",
                            contentType: "application/x-www-form-urlencoded",
                            useSimpleHttp: true
                        }); //Ende RPC
                    }, findKunden_nr: function (form) {
                        RPCManager.send("", function (rpcResponse, data, rpcRequest) {
                            var _data = isc.JSON.decode(data); // Daten aus dem PHP (rpcResponse)
                            if (_data.response.status === 0) {  // Durum 0 bedeutet Keine Hata

                                Kunden_Nr = _data.response.data["kunden_nr"];
                                Tarih = _data.response.data["datum"];
                                form.getField("kunden_nr").setValue(Kunden_Nr);
                                form.getField("kunden_nr").setDisabled(true);
                                form.getField("datum").setValue(Tarih);
                                form.getField("datum").setDisabled(true);

                            } else { // Wenn die Validierungen Hata aufweisen dann:

                                form.setErrors(_data.response.errors, true);
                                var _errors = form.getErrors();
                                for (var i in _errors)
                                {
                                    isc.say("<b>Hata! </br>" + (_errors [i]) + "</b>");
                                }
                            }
                        }, {// Übergabe der Parameter
                            actionURL: "api/findKundenNr.php",
                            httpMethod: "POST",
                            contentType: "application/x-www-form-urlencoded",
                            useSimpleHttp: true,
                            params: {
                                beleg_nr: form.getField("beleg_nr").getValue()
                            }
                        }); //Ende RPC
                    }
                });

                isc.IButton.create({
                    ID: "btnCloseAbrechnungNeu",
                    type: "button",
                    disabled: false,
                    icon: "famfam/door_in.png",
                    name: "btnCloseAbrechnungNeu",
                    showDisabledIcon: false,
                    title: "Kapat", width: 100, //Neuen Film anlegen
                    click: function () {
                        if (btnCloseAbrechnungNeu.getTitle() == "İptal et") {
                            isc.ask("Gerçekten iptal etmek istiyor musunuz? Kaydedilmemiş veriler kaybolabilir.", function (value) {
                                if (value) {
                                    wdAddAbrechnung.hide();
                                }
                            }, {title: "İşlem iptal?"});
                        } else {
                            wdAddAbrechnung.hide();
                        }
                    }});

                isc.IButton.create({
                    ID: "btnSpeichernAbrechnungNeu",
                    type: "button",
                    count: 0,
                    disabled: true,
                    showDisabledIcon: false,
                    icon: "famfam/database_save.png",
                    name: "btnSpeichernAbrechnungNeu",
                    title: "Kaydet",
                    width: 100, //Neuen Film anlegen
                    click: function () {
                        belegNr_ = dfAddAbrechnung.getField("beleg_nr").getValue();
                        var aktErgebnis = dfAddAbrechnung.getField("gesamtpr_brutto").getValue().replace(",", ".");
                        var Ergebnis = dfAddAbrechnung.getField("menge").getValue() *
                                (parseFloat(dfAddAbrechnung.getField("brutto_preis").getValue().replace(",", ".")));

                        if (rundung(Ergebnis, 2) != aktErgebnis) {
                            isc.say("Girilen değerler brüt toplam fiyattan sapma gösterir.\n\
                    Lütfen girişlerinizi tekrar kontrol edin ve miktar alanının yanındaki hesaplama tuşunu tekrar çalıştırın");
                        } else {
                            var _percent = pgbAddAbrechnung.percentDone + parseInt(10 + (50 * Math.random()));
                            pgbAddAbrechnung.setPercentDone(_percent);
                            pgbAddAbrechnung.setTitle(_percent + "%");

                            RPCManager.send("", function (rpcResponse, data, rpcRequest) {
                                var _data = isc.JSON.decode(data); // Daten aus dem PHP (rpcResponse)
                                if (_data.response.status === 0) {  // Durum 0 bedeutet Keine Hata
                                    laufendeNr = _data.response.data[0]["lfd_nr"];
                                    welcomeSite.setContentsURL(calendar_);
                                    btnSpeichernAbrechnungNeu.count++;
                                    //                                onRefreshAbrechnung("abrechnungsListe", belegNr_, btnSpeichernAbrechnungNeu.count);
                                    //                                isc.Timer.setTimeout("abrechnungsListe.abrechnungsSummenFunction()",1000);
                                    onRefresh("abrechnungsTree");
                                    btnSpeichernAbrechnungNeu.pgbAddAbrechnungFunction();
                                    isc.Timer.setTimeout("btnSpeichernAbrechnungNeu.findAbrechnung()", 300);
                                    //                                isc.say(laufendeNr);


                                } else { // Wenn die Validierungen Hata aufweisen dann:

                                    dfAddAbrechnung.setErrors(_data.response.errors, true);
                                    var _errors = dfAddAbrechnung.getErrors();
                                    for (var i in _errors)
                                    {
                                        isc.say("<b>Hata! </br>" + (_errors [i]) + "</b>", function (value) {
                                            if (value) {
                                                pgbAddAbrechnung.setTitle("");
                                                pgbAddAbrechnung.setPercentDone(0);
                                            }
                                        });
                                    }
                                }
                            }, {// Übergabe der Parameter
                                actionURL: "api/addAbrechnung.php",
                                httpMethod: "POST",
                                contentType: "application/x-www-form-urlencoded",
                                useSimpleHttp: true,
                                params: {
                                    prod_kz: dfAddAbrechnung.getField("prod_kz").getValue(),
                                    prod_bez: dfAddAbrechnung.getField("prod_kz").getDisplayValue(),
                                    verkauf_an: dfAddAbrechnung.getField("kunden_nr").getValue(),
                                    kunden_name: dfAddAbrechnung.getField("kunden_nr").getDisplayValue(),
                                    menge: dfAddAbrechnung.getField("menge").getValue(),
                                    preis_kat: dfAddAbrechnung.getField("preis_kat").getValue(),
                                    brutto_preis: dfAddAbrechnung.getField("brutto_preis").getValue(),
                                    mwst: dfAddAbrechnung.getField("mwst").getValue(),
                                    gesamtpr_brutto: dfAddAbrechnung.getField("gesamtpr_brutto").getValue(),
                                    datum: dfAddAbrechnung.getField("datum").getValue(),
                                    beleg_nr: dfAddAbrechnung.getField("beleg_nr").getValue(),
                                    zahlungsziel: dfAddAbrechnung.getField("zahlungsziel").getValue(),
                                    endTime: dfAddAbrechnung.getField("endTime").getValue(),
                                    startTime: dfAddAbrechnung.getField("startTime").getValue(),
                                    bemerkung: dfAddAbrechnung.getField("bemerkung").getValue()}

                            }); //Ende RPC
                        }// Ende if
                    }, // Ende Click
                    findAbrechnung: function () {

                        //                        if(laufendeNr !== null){
                        //                        var newAbrechnung = abrechnungsListe.data.find("lfd_nr", laufendeNr);
                        //                        var index = abrechnungsListe.getRecordIndex(newAbrechnung);
                        //    //                        abrechnungsListe.deselectAllRecords();
                        //                        abrechnungsListe.selectRecord(index);
                        //                        abrechnungsListe.scrollToRow(index);
                        //                        
                        //                        }

                        if (belegNr_ != null) {
                            var Key = belegNr_;
                            var record = abrechnungsTree.data.find("beleg_nr", Key);
                            var index = abrechnungsTree.getRecordIndex(record);


                            abrechnungsTree.deselectAllRecords();
                            abrechnungsTree.selectRecord(record);
                            abrechnungsTree.scrollToRow(index);


                            var folder_0 = abrechnungsTree.data.getParents(abrechnungsTree.getSelectedRecord())[0];
                            var folder_1 = abrechnungsTree.data.getParents(abrechnungsTree.getSelectedRecord())[1];

                            abrechnungsTree.openFolder(folder_0);// Die Gruppe wird geöffnet
                            abrechnungsTree.openFolder(folder_1);
                            abrechnungsTree.scrollToRow(index); // Selektiertes Record wird angescrollt
                            abrechnungsTree.recordClick(); //Das Klicken aufs Record wird angestoßen                  

                        }
                    },
                    pgbAddAbrechnungFunction: function () {
                        if (pgbAddAbrechnung.percentDone < 100) {
                            var _percent = pgbAddAbrechnung.percentDone + parseInt(10 + (50 * Math.random()));
                            pgbAddAbrechnung.setPercentDone(_percent); // Zufallswert wird berechnet

                            if (_percent <= 100) {
                                pgbAddAbrechnung.setTitle(_percent + "%");
                            } //Bis 100 wird mitgezählt
                            else {
                                pgbAddAbrechnung.setTitle("100%"); // ab 100 darf nicht mehr gezählt werden, da 100 leicht überstiegen wird.
                            }

                            isc.Timer.setTimeout("btnSpeichernAbrechnungNeu.pgbAddAbrechnungFunction()", 200);
                        } else {
                            if (!dfAddAbrechnung.validate() && dfAddAbrechnung.hasErrors()) {
                                dfAddAbrechnung.setErrors();
                                var _errors = dfAddAbrechnung.getErrors();
                                for (var i in _errors)
                                {
                                    isc.say("<b>Hata! </br>" + (_errors [i]) + "</b>", function (value) {
                                        if (value) {
                                            pgbAddAbrechnung.setTitle("");
                                            pgbAddAbrechnung.setPercentDone(0);
                                        }
                                    }); // Hier wird jeder Wert des Array-Schlüssel angezeigt und das Feld oder die Feld-Bezeichnung ist irrelevant.
                                }
                            } else {
                                isc.ask("Faturalandırma başarıyla tamamlandı. </ br> Başka bir tedavi eklemek ister misiniz?", function (value) {
                                    if (value) {
                                        wdAddAbrechnung.hide();
                                        wdAddPosition.show();
                                    } else {
                                        dfAddAbrechnung.clearValues();
                                        wdAddAbrechnung.hide();
                                        btnSpeichernAbrechnungNeu.setDisabled(true);
                                        btnResetAbrechnungNeu.setDisabled(true);
                                        //                                        isc.Timer.setTimeout("btnSpeichernAbrechnungNeu.findAbrechnung()", 300);
                                        pgbAddAbrechnung.setTitle("");
                                        pgbAddAbrechnung.setPercentDone(0);
                                        btnCloseAbrechnungNeu.setTitle("Kapat");
                                        btnCloseAbrechnungNeu.setIcon("famfam/door_in.png");
                                    }

                                }, {title: "Başka bir tedavi eklemek?"});
                            }
                        }
                    }// Ende ProgressbarFunction
                });
                isc.IButton.create({
                    ID: "btnResetAbrechnungNeu",
                    type: "button",
                    showDisabledIcon: false,
                    icon: "famfam/arrow_undo.png",
                    disabled: true,
                    name: "btnResetAbrechnungNeu",
                    title: "Reset", width: 100, //Neuen Film anlegen
                    click: function () {
                        dfAddAbrechnung.reset();
                        btnSpeichernAbrechnungNeu.setDisabled(true);
                        btnResetAbrechnungNeu.setDisabled(true);
                        btnCloseAbrechnungNeu.setTitle("Kapat");
                        btnCloseAbrechnungNeu.setIcon("famfam/door_in.png");
                    }});


                isc.HLayout.create({
                    ID: "HLayoutAbrechnungNeu",
                    height: 30,
                    width: "100%",
                    align: "center",
                    members: [btnCloseAbrechnungNeu, isc.LayoutSpacer.create({
                            width: 20
                        }), btnSpeichernAbrechnungNeu, isc.LayoutSpacer.create({
                            width: 20
                        }), btnResetAbrechnungNeu]});



                isc.Window.create({
                    ID: "wdAddAbrechnung",
                    title: "Yeni randevu oluşturma",
                    autoSize: true,
                    autoCenter: true,
                    showFooter: false,
                    showMinimizeButton: true,
                    showCloseButton: false,
                    width: 500,
                    headerIconDefaults: {
                        width: 16,
                        height: 16,
                        src: "famfam/user_add.png"
                    },
                    canDragReposition: true,
                    canDragResize: false,
                    showShadow: false,
                    showModalMask: false,
                    isModal: false,
                    items: [dfAddAbrechnung, HLayoutAbrechnungNeu, pgbAddAbrechnung]
                });
                /*
                 * ********************** Ende neue Abrechnung *******************
                 * -------------------------------------------------------------
                 */


                /*
                 * ****************** Anfang neue Position *********************
                 * -------------------------------------------------------------
                 */

                isc.Progressbar.create({
                    percentDone: 0,
                    ID: "pgbAddPosition",
                    showTitle: true,
                    title: "",
                    height: 16,
                    length: "100%"});

                isc.DynamicForm.create({
                    ID: "dfAddPosition",
                    width: "100%",
                    height: "100%",
                    verkaufCount: 0,
                    colWidths: [140, 100, 140, "*"],
                    numCols: 4,
                    titleOrientation: "left",
                    validateOnExit: true,
                    validateOnChange: false,
                    margin: 5,
                    fields: [
                        {
                            name: "prod_kz",
                            title: "Tedavi",
                            width: 260,
                            colSpan: 2,
                            optionDataSource: produkteDS,
                            valueField: "prod_kz",
                            displayField: "bezeichnung",
                            type: "select",
                            required: true,
                            changed: function (form, item, value) {
                                form.changeFunctionAddPosition();
                                preisFunction(form);
                            },
                            pickListProperties: {showShadow: true, showFilterEditor: false, showHeader: true},
                            pickListWidth: 280,
                            pickListFields: [
                                {name: "prod_kz", width: 50}, {name: "bezeichnung", width: "*"}],
                            getPickListFilterCriteria: function () {
                                dfAddPosition.verkaufCount++;
                                var filter = {
                                    count: dfAddPosition.verkaufCount, aktiv: "ja", beleg_nr: abrechnungsTree.getSelectedRecord().beleg_nr};
                                return filter;
                            }

                        }, {
                            name: "preis_kat",
                            title: "Fiyat kategorisi",
                            width: 150,
                            colSpan: 2,
                            valueMap: {"1": "tam Fiyat", "2": "indirimli Fiyat", "3": "ücretsiz", "4": "özel Fiyat"},
                            type: "radioGroup",
                            defaultValue: "1",
                            required: true,
                            vertical: false,
                            changed: function (form, item, value) {
                                form.changeFunctionAddPosition();
                                preisFunction(form);
                                if (value == "4") {
                                    form.getField("brutto_preis_").show();
                                    form.getField("brutto_preis_").focusInItem();
                                    form.getField("brutto_preis").hide();
                                    form.getField("mwst_").show();
                                    form.getField("mwst_").focusInItem();
                                    form.getField("mwst").hide();
                                } else {
                                    form.getField("brutto_preis_").hide();
                                    form.getField("mwst_").hide();
                                    form.getField("brutto_preis").show();
                                    form.getField("mwst").show();
                                }
                            }
                        }, {
                            name: "menge",
                            required: true,
                            colSpan: 2,
                            type: "spinner",
                            title: "Miktar",
                            width: 100,
                            change: "preisFunction(dfAddPosition);",
                            icons: [{
                                    src: "famfam/calculator.png",
                                    prompt: "Fiyatı yeniden hesapla",
                                    hoverWidth: 100,
                                    hoverDelay: 700,
                                    click: function (form, item, value) {
                                        preisFunction(form);
                                    }
                                }], keyPress: function () {
                                if (isc.Event.getKey() == "Enter") {
                                    preisFunction(dfAddPosition);
                                    dfAddPosition.getField("mwst").focusInItem();
                                    dfAddPosition.getField("menge").focusInItem();
                                    dfAddPosition.changeFunctionAddPosition();
                                }
                            },
                            changed: function (form, item, value) {
                                form.changeFunctionAddPosition();

                            },
                            keyPressFilter: "[0-9]",
                            defaultValue: 1
                        },
                        {
                            defaultValue: "<b>Fiyat derleme</b>",
                            name: "Abrechnung",
                            type: "section",
                            colSpan: 4,
                            sectionExpanded: true,
                            canCollapse: false,
                            itemIds: ["mwst", "brutto_preis", /*"mwst_einzelpr", "brutto_preis", "gesamtpr_brutto",*/ "mwst_gesamtpr", "gesamtpr_brutto"]
                        }, {
                            name: "mwst",
                            title: "KDV",
                            type: "text",
                            defaultValue: 0,
                            colSpan: 2,
                            width: 60,
                            required: true,
                            change: "return false"
                        },                        {
                            name: "mwst_",
                            title: "KDV",
                            type: "text",
                            defaultValue: 18,
                            colSpan: 2,
                            selectOnFocus: true,
                            selectOnClick: true,
                            width: 60,
                            keyPressFilter: "[0-9,]",
                            change: "preisFunction(dfAddPosition);"
                        },  {
                            name: "brutto_preis",
                            title: "Fiyat",
                            width: 100,
                            required: true,
                            colSpan: 2,
                            defaultValue: 0,
                            type: "text",
                            change: "return false"
                        }, {
                            name: "brutto_preis_",
                            title: "Fiyat",
                            width: 100,
                            required: true,
                            colSpan: 2,
                            selectOnFocus: true,
                            selectOnClick: true,
                            defaultValue: 0,
                            type: "text",
                            keyPressFilter: "[0-9,.]",
                            changed: function (form, item, value) {
                                preisFunction(form);
                            }
                        }, {
                            name: "mwst_gesamtpr",
                            title: "KDV Toplam tutarı",
                            width: 100,
                            colSpan: 2,
                            required: true,
                            defaultValue: 0,
                            type: "text",
                            change: "return false"
                        }, {
                            name: "gesamtpr_brutto",
                            title: "Toplam tutarı",
                            width: 100,
                            colSpan: 2,
                            required: true,
                            defaultValue: 0,
                            type: "text",
                            change: "return false"
                        }, {
                            type: "RowSpacer",
                            height: 10
                        }, {
                            defaultValue: "<b>Faturalandırmaya not ekleyin</b>",
                            name: "Notiz",
                            type: "section",
                            colSpan: 4,
                            sectionExpanded: false,
                            canCollapse: true,
                            itemIds: ["bemerkung"]
                        }, {
                            name: "bemerkung",
                            title: "Not",
                            width: 250,
                            colSpan: 2,
                            length: 260,
                            type: "textArea",
                            changed: function (form, item, value) {
                                form.changeFunctionAddPosition();
                            }
                        }, {
                            type: "RowSpacer",
                            height: 10
                        }
                    ], changeFunctionAddPosition: function () {
                        btnSpeichernAddPosition.setDisabled(false);
                        btnResetAddPosition.setDisabled(false);
                        btnCloseAddPosition.setTitle("İptal et");
                        btnCloseAddPosition.setIcon("famfam/cancel.png");
                    }
                });

                isc.IButton.create({
                    ID: "btnCloseAddPosition",
                    type: "button",
                    disabled: false,
                    icon: "famfam/door_in.png",
                    name: "btnCloseAddPosition",
                    showDisabledIcon: false,
                    title: "Kapat", width: 100, //Neuen Film anlegen
                    click: function () {
                        if (btnCloseAddPosition.getTitle() == "İptal et") {
                            isc.ask("Gerçekten iptal etmek istiyor musunuz? Kaydedilmemiş veriler kaybolabilir.", function (value) {
                                if (value) {
                                    wdAddPosition.hide();
                                }
                            }, {title: "İşlem iptal?"});
                        } else {
                            wdAddPosition.hide();
                        }
                    }});

                isc.IButton.create({
                    ID: "btnSpeichernAddPosition",
                    type: "button",
                    count: 0,
                    disabled: true,
                    showDisabledIcon: false,
                    icon: "famfam/database_save.png",
                    name: "btnSpeichernAddPosition",
                    title: "Kaydet",
                    width: 100, //Neuen Film anlegen
                    click: function () {
                        belegNr_ = dfEditAbrechnung.getField("beleg_nr").getValue();
                        var aktErgebnis = dfAddPosition.getField("gesamtpr_brutto").getValue().replace(",", ".");
                        var Ergebnis = dfAddPosition.getField("menge").getValue() *
                                (parseFloat(dfAddPosition.getField("brutto_preis").getValue().replace(",", ".")));

                        if (rundung(Ergebnis, 2) != aktErgebnis) {
                            isc.say("Girilen değerler brüt toplam fiyattan sapma gösterir.\n\
                    Lütfen girişlerinizi tekrar kontrol edin ve miktar alanının yanındaki hesaplama tuşunu tekrar çalıştırın");
                        } else {
                            var _percent = pgbAddPosition.percentDone + parseInt(10 + (50 * Math.random()));
                            pgbAddPosition.setPercentDone(_percent);
                            pgbAddPosition.setTitle(_percent + "%");

                            RPCManager.send("", function (rpcResponse, data, rpcRequest) {
                                var _data = isc.JSON.decode(data); // Daten aus dem PHP (rpcResponse)
                                if (_data.response.status === 0) {  // Durum 0 bedeutet Keine Hata
                                    laufendeNr = _data.response.data[0]["lfd_nr"];

                                    btnSpeichernAddPosition.count++;
                                    if (abrechnungsListeEdit.isDrawn() && abrechnungsListeEdit.isVisible()) {
                                        onRefreshAbrechnung("abrechnungsListeEdit", belegNr_, btnSpeichernAddPosition.count);
                                    }
                                    //                                isc.Timer.setTimeout("abrechnungsListeEdit.abrechnungsSummenFunction()",1000);
                                    // onRefresh("abrechnungsTree");
                                    btnSpeichernAddPosition.pgbAddPositionFunction();
                                    isc.Timer.setTimeout("btnSpeichernAddPosition.findPosition()", 300);
                                    //                                isc.say(laufendeNr);


                                } else { // Wenn die Validierungen Hata aufweisen dann:

                                    dfAddPosition.setErrors(_data.response.errors, true);
                                    var _errors = dfAddPosition.getErrors();
                                    for (var i in _errors)
                                    {
                                        isc.say("<b>Hata! </br>" + (_errors [i]) + "</b>", function (value) {
                                            if (value) {
                                                pgbAddPosition.setTitle("");
                                                pgbAddPosition.setPercentDone(0);
                                            }
                                        });
                                    }

                                }
                            }, {// Übergabe der Parameter
                                actionURL: "api/addAbrechnung.php",
                                httpMethod: "POST",
                                contentType: "application/x-www-form-urlencoded",
                                useSimpleHttp: true,
                                params: {
                                    prod_kz: dfAddPosition.getField("prod_kz").getValue(),
                                    prod_bez: dfAddPosition.getField("prod_kz").getDisplayValue(),
                                    verkauf_an: abrechnungsTree.getSelectedRecord().verkauf_an,
                                    kunden_name: abrechnungsTree.getSelectedRecord().name,
                                    menge: dfAddPosition.getField("menge").getValue(),
                                    preis_kat: dfAddPosition.getField("preis_kat").getValue(),
                                    brutto_preis: dfAddPosition.getField("brutto_preis").getValue(),
                                    mwst: dfAddPosition.getField("mwst").getValue(),
                                    gesamtpr_brutto: dfAddPosition.getField("gesamtpr_brutto").getValue(),
                                    datum: abrechnungsTree.getSelectedRecord().datum,
                                    beleg_nr: abrechnungsTree.getSelectedRecord().beleg_nr,
                                    zahlungsziel: abrechnungsTree.getSelectedRecord().zahlungsziel,
                                    startTime: abrechnungsTree.getSelectedRecord().startTime,
                                    endTime: abrechnungsTree.getSelectedRecord().endTime,
                                    bemerkung: dfAddPosition.getField("bemerkung").getValue(),
                                    position: "J"}

                            }); //Ende RPC
                        }// Ende if
                    }, // Ende Click
                    findPosition: function () {

                        if (laufendeNr !== null) {
                            var newAbrechnung = abrechnungsListeEdit.data.find("lfd_nr", laufendeNr);
                            var index = abrechnungsListeEdit.getRecordIndex(newAbrechnung);
                            //                        abrechnungsListeEdit.deselectAllRecords();
                            abrechnungsListeEdit.selectRecord(index);
                            abrechnungsListeEdit.scrollToRow(index);

                        }


                    },
                    pgbAddPositionFunction: function () {
                        if (pgbAddPosition.percentDone < 100) {
                            var _percent = pgbAddPosition.percentDone + parseInt(10 + (50 * Math.random()));
                            pgbAddPosition.setPercentDone(_percent); // Zufallswert wird berechnet

                            if (_percent <= 100) {
                                pgbAddPosition.setTitle(_percent + "%");
                            } //Bis 100 wird mitgezählt
                            else {
                                pgbAddPosition.setTitle("100%"); // ab 100 darf nicht mehr gezählt werden, da 100 leicht überstiegen wird.
                            }

                            isc.Timer.setTimeout("btnSpeichernAddPosition.pgbAddPositionFunction()", 200);
                        } else {
                            if (!dfAddPosition.validate() && dfAddPosition.hasErrors()) {
                                dfAddPosition.setErrors();
                                var _errors = dfAddPosition.getErrors();
                                for (var i in _errors)
                                {
                                    isc.say("<b>Hata! </br>" + (_errors [i]) + "</b>", function (value) {
                                        if (value) {
                                            pgbAddPosition.setTitle("");
                                            pgbAddPosition.setPercentDone(0);
                                        }
                                    }); // Hier wird jeder Wert des Array-Schlüssel angezeigt und das Feld oder die Feld-Bezeichnung ist irrelevant.
                                }
                            } else {
                                isc.ask("Tedavi başarıyla oluşturuldu. </ br> Başka bir tedavi eklemek ister misiniz?", function (value) {
                                    if (value) {
                                        dfAddPosition.clearValues();
                                        // dfAddPosition.getField("beleg_nr").focusInItem();
                                        //                                        isc.Timer.setTimeout("btnSpeichernAddPosition.findPosition()", 300);
                                        pgbAddPosition.setTitle("");
                                        pgbAddPosition.setPercentDone(0);
                                        btnCloseAddPosition.setTitle("Kapat");
                                        btnCloseAddPosition.setIcon("famfam/door_in.png");
                                    } else {
                                        dfAddPosition.clearValues();
                                        wdAddPosition.hide();
                                        btnSpeichernAddPosition.setDisabled(true);
                                        btnResetAddPosition.setDisabled(true);
                                        //                                        isc.Timer.setTimeout("btnSpeichernAddPosition.findPosition()", 300);
                                        pgbAddPosition.setTitle("");
                                        pgbAddPosition.setPercentDone(0);
                                        btnCloseAddPosition.setTitle("Kapat");
                                        btnCloseAddPosition.setIcon("famfam/door_in.png");
                                    }

                                }, {title: "Başka bir tedavi eklemek ister misiniz?"});
                            }
                        }
                    }// Ende ProgressbarFunction
                });
                isc.IButton.create({
                    ID: "btnResetAddPosition",
                    type: "button",
                    showDisabledIcon: false,
                    icon: "famfam/arrow_undo.png",
                    disabled: true,
                    name: "btnResetAddPosition",
                    title: "Reset", width: 100, //Neuen Film anlegen
                    click: function () {
                        dfAddPosition.reset();
                        btnSpeichernAddPosition.setDisabled(true);
                        btnResetAddPosition.setDisabled(true);
                        btnCloseAddPosition.setTitle("Kapat");
                        btnCloseAddPosition.setIcon("famfam/door_in.png");
                    }});


                isc.HLayout.create({
                    ID: "HLayoutAddPosition",
                    height: 30,
                    width: "100%",
                    align: "center",
                    members: [btnCloseAddPosition, isc.LayoutSpacer.create({
                            width: 20
                        }), btnSpeichernAddPosition, isc.LayoutSpacer.create({
                            width: 20
                        }), btnResetAddPosition]});



                isc.Window.create({
                    ID: "wdAddPosition",
                    title: "Yeni tedavi ekleme",
                    autoSize: true,
                    autoCenter: true,
                    showFooter: false,
                    showMinimizeButton: false,
                    showCloseButton: false,
                    width: 500,
                    headerIconDefaults: {
                        width: 16,
                        height: 16,
                        src: "famfam/user_add.png"
                    },
                    canDragReposition: true,
                    canDragResize: false,
                    showShadow: true,
                    showModalMask: true,
                    modalMaskOpacity: 10,
                    isModal: true,
                    items: [dfAddPosition, HLayoutAddPosition, pgbAddPosition]
                });
                /*
                 * ********************** Ende neue Position *******************
                 * -------------------------------------------------------------
                 */


                /*
                 * ****************** Anfang edit Abrechnung ********************
                 * -------------------------------------------------------------
                 */

                isc.Progressbar.create({
                    percentDone: 0,
                    ID: "pgbEditAbrechnung",
                    showTitle: true,
                    title: "",
                    height: 16,
                    length: "100%"});

                isc.DynamicForm.create({
                    ID: "dfEditAbrechnung",
                    width: "100%",
                    height: "100%",
                    verkaufCount: 0,
                    colWidths: [140, 100, 140, "*"],
                    numCols: 4,
                    titleOrientation: "left",
                    validateOnExit: true,
                    validateOnChange: false,
                    margin: 5,
                    fields: [{
                            name: "beleg_nr",
                            title: " Fatura no",
                            type: "text",
                            disabled: true,
                            colSpan: 2
                        }, {
                            name: "verkauf_an",
                            title: "Hasta",
                            colSpan: 2,
                            width: 260,
                            optionDataSource: kundenDS,
                            valueField: "lfd_nr",
                            displayField: "name_voll",
                            type: "comboBox",
                            required: true,
                            changed: function (form, item, value) {
                                form.changeFunctionEditAbrechnung();
                            },
                            pickListProperties: {showShadow: true, showFilterEditor: false, showHeader: true},
                            pickListWidth: 400,
                            pickListFields: [
                                {name: "lfd_nr", width: 80}, {name: "kunden_nr", width: 80}, {name: "name_voll", width: "*"}, {name: "geburtstag", width: 80}],
                            getPickListFilterCriteria: function () {
                                dfEditAbrechnung.verkaufCount++;
                                var filter = {
                                    count: dfEditAbrechnung.verkaufCount, aktiv: "ja", name_voll: dfEditAbrechnung.getField("verkauf_an").getDisplayValue()};
                                return filter;
                            }, icons: [{
                                    src: "famfam/add.png",
                                    prompt: "Yeni Hasta kayıt et",
                                    hoverWidth: 100,
                                    hoverDelay: 700,
                                    click: function (form, item, value) {
                                        tsbAddKunden.action();
                                    }
                                }]

                        }, {
                            name: "datum",
                            title: "Fatura tarihi",
                            colSpan: 2,
                            type: "date",
                            width: 120,
                            required: true,
                            changed: function (form, item, value) {
                                form.changeFunctionEditAbrechnung();
                                getNextTermin(form, value);
                            }
                        },
                        {
                            name: "startTime", title: "Başlangıç",
                            //                                hint: "Picklist based time input",
                            editorType: "TimeItem",
                            useTextField: false,
                            hourItemTitle: "Saat",
                            minuteItemTitle: "Dak",
                            showSecondItem: false,
                            minuteIncrement: 5,
                            changed: function (form, item, value) {
                                form.changeFunctionEditAbrechnung();
                                setEndTime(value, form, "start");
                            }
                        },
                        {
                            name: "endTime", title: "Son",
                            //                                hint: "Picklist based time input",
                            editorType: "TimeItem",
                            useTextField: false,
                            hourItemTitle: "Saat",
                            minuteItemTitle: "Dak",
                            showSecondItem: false,
                            minuteIncrement: 5,
                            changed: function (form, item, value) {
                                form.changeFunctionEditAbrechnung();
                                setEndTime(value, form, "end");
                            }
                        }, {
                            name: "zahlungsziel",
                            title: "Ödeme koşulları",
                            colSpan: 2,
                            type: "select",
                            defaultValue: "S",
                            width: 120,
                            valueMap: {"S": "Nakit", "Z": "Kredi Kart"},
                            required: true,
                            changed: function (form, item, value) {
                                form.changeFunctionEditAbrechnung();
                                //                                if (value == "S") {
                                //                                    form.getField("zahlfrist").setDisabled(true);
                                //                                    form.getField("zahldatum").setDisabled(true);
                                //                                }
                                //                                if (value == "Z") {
                                //                                    form.getField("zahlfrist").setDisabled(false);
                                //                                    form.getField("zahldatum").setDisabled(false);
                                //                                }

                            }
                        }, {
                            type: "RowSpacer",
                            height: 10
                        }
                    ], changeFunctionEditAbrechnung: function () {
                        btnSpeichernAbrechnungEdit.setDisabled(false);
                        btnResetAbrechnungEdit.setDisabled(false);
                    }, findKunden_nr: function (form_) {

                        RPCManager.send("", function (rpcResponse, data, rpcRequest) {
                            var _data = isc.JSON.decode(data); // Daten aus dem PHP (rpcResponse)
                            if (_data.response.status === 0) {  // Durum 0 bedeutet Keine Hata

                                Kunden_Nr = _data.response.data["kunden_nr"];
                                Tarih = _data.response.data["datum"];
                                if (form_ == dfEditAbrechnung) {
                                    form_.getField("verkauf_an").setValue(Kunden_Nr);
                                    form_.getField("verkauf_an").setDisabled(true);
                                } else {
                                    form_.getField("kunden_nr").setValue(Kunden_Nr);
                                    form_.getField("kunden_nr").setDisabled(true);
                                }
                                form_.getField("datum").setValue(Tarih);
                                form_.getField("datum").setDisabled(true);

                            } else { // Wenn die Validierungen Hata aufweisen dann:

                                form_.setErrors(_data.response.errors, true);
                                var _errors = form_.getErrors();
                                for (var i in _errors)
                                {
                                    isc.say("<b>Hata! </br>" + (_errors [i]) + "</b>");
                                }
                            }
                        }, {// Übergabe der Parameter
                            actionURL: "api/findKundenNr.php",
                            httpMethod: "POST",
                            contentType: "application/x-www-form-urlencoded",
                            useSimpleHttp: true,
                            params: {
                                beleg_nr: form_.getField("beleg_nr").getValue()
                            }
                        }); //Ende RPC
                    }
                });




                isc.ToolStripButton.create({
                    ID: "btnSpeichernAbrechnungEdit",
                    type: "button",
                    disabled: true,
                    count: 0,
                    showDisabledIcon: false,
                    icon: "famfam/database_save.png",
                    name: "btnSpeichernAbrechnungEdit",
                    title: "Kaydet",
                    click: function () {
                        belegNr_ = dfEditAbrechnung.getField("beleg_nr").getValue();
                        btnSpeichernAbrechnungEdit.count++;
                        RPCManager.send("", function (rpcResponse, data, rpcRequest) {
                            var _data = isc.JSON.decode(data); // Daten aus dem PHP (rpcResponse)
                            if (_data.response.status === 0) {  // Durum 0 bedeutet Keine Hata

                                //                                abrechnungsTree.fetchData({count:  btnSpeichernAbrechnungEdit.count});
                                onRefreshAbrechnungsTree("abrechnungsTree", btnSpeichernAbrechnungEdit.count);
                                isc.Timer.setTimeout("btnSpeichernAbrechnungEdit.findAbrechnung()", 300);
                                dfEditAbrechnung.clearErrors();
                                welcomeSite.setContentsURL(calendar_);
                                //                                isc.say(laufendeNr);


                            } else { // Wenn die Validierungen Hata aufweisen dann:

                                dfEditAbrechnung.setErrors(_data.response.errors, true);
                                var _errors = dfEditAbrechnung.getErrors();
                                for (var i in _errors)
                                {
                                    isc.say("<b>Hata! </br>" + (_errors [i]) + "</b>", function (value) {
                                        if (value) {
                                            pgbEditAbrechnung.setTitle("");
                                            pgbEditAbrechnung.setPercentDone(0);
                                        }
                                    });
                                }

                            }
                        }, {// Übergabe der Parameter
                            actionURL: "api/editAbrechnung.php",
                            httpMethod: "POST",
                            contentType: "application/x-www-form-urlencoded",
                            useSimpleHttp: true,
                            params: {
                                verkauf_an: dfEditAbrechnung.getField("verkauf_an").getValue(),
                                kunden_name: dfEditAbrechnung.getField("verkauf_an").getDisplayValue(),
                                datum: dfEditAbrechnung.getField("datum").getValue(),
                                beleg_nr: dfEditAbrechnung.getField("beleg_nr").getValue(),
                                zahlungsziel: dfEditAbrechnung.getField("zahlungsziel").getValue(),
                                startTime: dfEditAbrechnung.getField("startTime").getValue(),
                                endTime: dfEditAbrechnung.getField("endTime").getValue(),
                            }

                        }); //Ende RPC

                    }, // Ende Click
                    findAbrechnung: function () {

                        if (belegNr_ != null) {
                            var Key = belegNr_;
                            var record = abrechnungsTree.data.find("beleg_nr", Key);
                            var index = abrechnungsTree.getRecordIndex(record);


                            abrechnungsTree.deselectAllRecords();
                            abrechnungsTree.selectRecord(record);
                            abrechnungsTree.scrollToRow(index);


                            var folder_0 = abrechnungsTree.data.getParents(abrechnungsTree.getSelectedRecord())[0];
                            var folder_1 = abrechnungsTree.data.getParents(abrechnungsTree.getSelectedRecord())[1];

                            abrechnungsTree.openFolder(folder_0);// Die Gruppe wird geöffnet
                            abrechnungsTree.openFolder(folder_1);
                            abrechnungsTree.scrollToRow(index); // Selektiertes Record wird angescrollt
                            if (typeof (laufendeNr) == "undefined") {
                                abrechnungsTree.recordClick(); //Das Klicken aufs Record wird angestoßen 
                                abrechnungsTree.scrollToRow(index);
                            }
                        }

                    }
                });
                isc.ToolStripButton.create({
                    ID: "btnResetAbrechnungEdit",
                    type: "button",
                    showDisabledIcon: false,
                    icon: "famfam/arrow_undo.png",
                    disabled: true,
                    name: "btnResetAbrechnungEdit",
                    title: "Reset", //Neuen Film anlegen
                    click: function () {
                        dfEditAbrechnung.reset();
                        btnSpeichernAbrechnungEdit.setDisabled(true);
                        btnResetAbrechnungEdit.setDisabled(true);
                    }});

                isc.ToolStripButton.create({
                    ID: "btnAddAbrechnungEdit",
                    type: "button",
                    showDisabledIcon: false,
                    icon: "icons/32/doctor.png",
                    disabled: false,
                    name: "btnAddAbrechnungEdit",
                    title: "Tedavi ekle", //Neuen Film anlegen
                    click: function () {
                        tsbAddPosition.action();
                    }});


                /*
                 ***************** Add Button Position *************************
                 */
                isc.ToolStripButton.create({
                    ID: "tsbAddPosition",
                    title: "",
                    showDisabledIcon: false,
                    icon: "icons/32/doctor.png",
                    prompt: "Tedavi ekle",
                    hoverWidth: 100,
                    hoverDelay: 700,
                    action: function () {
                        wdAddPosition.show();
                        pgbAddPosition.setHeight(16);
                        pgbAddPosition.setTitle("");
                        pgbAddPosition.setPercentDone(0);
                        isc.Timer.setTimeout("btnResetAddPosition.click()", 50);
                    }
                });
                /*
                 ***************** Edit Button Abrechnung **********************
                 */
                isc.ToolStripButton.create({
                    ID: "tsbAbrechnungEdit",
                    title: "Tedavi düzenle",
                    showDisabledIcon: false,
                    disabled: true,
                    icon: "icons/32/doctor_edit.png",
                    prompt: "Seçilen fatura için düzenleme ekranını açar",
                    hoverWidth: 100,
                    hoverDelay: 700,
                    action: function () {

                        if (abrechnungsListeEdit.getSelection().length == 1) {
                            record = abrechnungsListeEdit.getSelectedRecord();
                            dfEditAbrechnung2.editRecord(record);
                            wdEditPositionen.show();
                            pgbEditAbrechnung.setHeight(16);
                            preisFunction(dfEditAbrechnung2);
                        } else {
                            isc.say("Önce bir tedavi seçmelisiniz");
                        }

                    }
                });
                /*
                 ***************** Delete Button Abrechnung (Pos)***************
                 */
                isc.ToolStripButton.create({
                    ID: "tsbPositionDelete",
                    title: "Tedavi sil",
                    count: 0,
                    showDisabledIcon: false,
                    icon: "icons/32/doctor_delete.png",
                    prompt: "Seçilen tedaviyi siler",
                    disabled: true,
                    hoverWidth: 100,
                    hoverDelay: 700,
                    action: function () {
                        if (abrechnungsListeEdit.getTotalRows() > 1) {
                            belegNr_ = abrechnungsTree.getSelectedRecord().beleg_nr;
                        } else {
                            belegNr_ = null;
                        }
                        laufendeNr = null;
                        if (abrechnungsListeEdit.getSelection().length == 1) {
                            var pos = abrechnungsListeEdit.getSelectedRecord().bezeichnung;
                            isc.ask("<b> " + pos + " </ b> işlemini kalıcı olarak silmek istediğinizden emin misiniz?", function (value) {
                                if (value) {
                                    var totalRowsPos = abrechnungsListeEdit.getTotalRows();
                                    var totalRowsAbr = abrechnungsTree.getTotalRows();
                                    RPCManager.send("", function (rpcResponse, data, rpcRequest) {
                                        var _data = isc.JSON.decode(data); // Daten aus dem PHP (rpcResponse)
                                        if (_data.response.status === 0) {  // Durum 0 bedeutet Keine Hata
                                            tsbPositionDelete.count++;
                                            welcomeSite.setContentsURL(calendar_);
                                            if (totalRowsPos == 1 && totalRowsAbr == 3) { // Nur eine Abrechnung vorhanden.
                                                abrechnungsTree.invalidateCache();
                                                abrechnungsListeEdit.setData([]);
                                                dfEditAbrechnung.clearValues();
                                                wdEditAbrechnung.hide();
                                            } else if (totalRowsPos == 1 && totalRowsAbr > 3) {
                                                abrechnungsTree.invalidateCache();
                                                isc.Timer.setTimeout("abrechnungsTree.selectFirstRecord()", 200);
                                                wdEditAbrechnung.hide();
                                            } else {
                                                onRefreshAbrechnung("abrechnungsListeEdit", abrechnungsListeEdit.getSelectedRecord().beleg_nr, tsbPositionDelete.count);
                                            }



                                            //                                isc.Timer.setTimeout("btnSpeichernAbrechnungEdit2.findAbrechnung()", 300);
                                        } else { // Wenn die Validierungen Hata aufweisen dann:

                                            dfErrorFormAbrechnung.setErrors(_data.response.errors, true);
                                            var _errors = dfErrorFormAbrechnung.getErrors();
                                            for (var i in _errors)
                                            {
                                                isc.say("<b>Hata! </br>" + (_errors [i]) + "</b>");
                                            }

                                        }
                                    }, {// Übergabe der Parameter
                                        actionURL: "api/deletePosition.php",
                                        httpMethod: "POST",
                                        contentType: "application/x-www-form-urlencoded",
                                        useSimpleHttp: true,
                                        params: {
                                            lfd_nr: abrechnungsListeEdit.getSelectedRecord().lfd_nr,
                                            prod_bez: abrechnungsListeEdit.getSelectedRecord().bezeichnung,
                                            prod_kz: abrechnungsListeEdit.getSelectedRecord().prod_kz,
                                            verkauf_an: abrechnungsListeEdit.getSelectedRecord().verkauf_an,
                                            kunden_name: abrechnungsListeEdit.getSelectedRecord().name,
                                            beleg_nr: abrechnungsListeEdit.getSelectedRecord().beleg_nr
                                        }
                                    }); //Ende RPC 
                                }
                            }, {title: "Fatura Silmek?"});
                        } else {
                            isc.say("Önce bir fatura seçmelisiniz");
                        }

                    }
                });


                isc.HLayout.create({
                    ID: "HLayoutAbrechnungEdit",
                    height: 30,
                    width: "100%",
                    align: "center",
                    members: [btnSpeichernAbrechnungEdit, isc.LayoutSpacer.create({
                            width: 20
                        }), btnResetAbrechnungEdit, isc.LayoutSpacer.create({
                            width: 20
                        }), btnAddAbrechnungEdit, isc.LayoutSpacer.create({
                            width: 20
                        }), tsbAbrechnungEdit, isc.LayoutSpacer.create({
                            width: 20
                        }), tsbPositionDelete]});



                /*
                 * ************************** EDIT ABRECHNUNG 2 ********************************
                 -------------------------------------------------------------------------------
                 */


                isc.DynamicForm.create({
                    ID: "dfEditAbrechnung2",
                    width: "100%",
                    height: "100%",
                    verkaufCount: 0,
                    colWidths: [140, 100, 140, "*"],
                    numCols: 4,
                    titleOrientation: "left",
                    validateOnExit: true,
                    validateOnChange: false,
                    margin: 5,
                    fields: [
                        {
                            name: "prod_kz",
                            title: "Tedavi",
                            width: 260,
                            colSpan: 2,
                            optionDataSource: produkteDS,
                            valueField: "prod_kz",
                            displayField: "bezeichnung",
                            type: "select",
                            required: true,
                            changed: function (form, item, value) {
                                dfEditAbrechnung2.changeFunctionEditAbrechnung2();
                                preisFunction(form);
                            },
                            pickListProperties: {showShadow: true, showFilterEditor: false, showHeader: true},
                            pickListWidth: 280,
                            pickListFields: [
                                {name: "prod_kz", width: 50}, {name: "bezeichnung", width: "*"}],
                            getPickListFilterCriteria: function () {
                                dfEditAbrechnung.verkaufCount++;
                                var filter = {
                                    count: dfEditAbrechnung.verkaufCount, aktiv: "ja", beleg_nr: dfEditAbrechnung.getField("beleg_nr").getValue()};
                                return filter;
                            }

                        }, {
                            name: "preis_kat",
                            title: "Fiyat kategorisi",
                            width: 150,
                            colSpan: 2,
                            valueMap: {"1": "tam Fiyat", "2": "indirimli Fiyat", "3": "ücretsiz", "4": "özel Fiyat"},
                            type: "radioGroup",
                            defaultValue: "1",
                            required: true,
                            vertical: false,
                            changed: function (form, item, value) {
                                dfEditAbrechnung2.changeFunctionEditAbrechnung2();
                                preisFunction(form);
                                if (value == "4") {
                                    form.getField("brutto_preis_").show();
                                    form.getField("brutto_preis_").focusInItem();
                                    form.getField("brutto_preis").hide();
                                    form.getField("mwst_").show();
                                    form.getField("mwst_").focusInItem();
                                    form.getField("mwst").hide();
                                } else {
                                    form.getField("brutto_preis_").hide();
                                    form.getField("mwst_").hide();
                                    form.getField("brutto_preis").show();
                                    form.getField("mwst").show();
                                }
                            }
                        }, {
                            name: "menge",
                            required: true,
                            colSpan: 2,
                            type: "spinner",
                            title: "Miktar",
                            width: 100,
                            defaultValue: 1,
                            change: "preisFunction(dfEditAbrechnung2);",
                            icons: [{
                                    src: "famfam/calculator.png",
                                    prompt: "Fiyatı yeniden hesapla",
                                    hoverWidth: 100,
                                    hoverDelay: 700,
                                    click: function (form, item, value) {
                                        preisFunction(form);
                                        dfEditAbrechnung2.changeFunctionEditAbrechnung2();
                                    }
                                }], keyPress: function () {
                                if (isc.Event.getKey() == "Enter") {
                                    preisFunction(dfEditAbrechnung2);
                                    dfEditAbrechnung2.getField("mwst").focusInItem();
                                    dfEditAbrechnung2.getField("menge").focusInItem();
                                    dfEditAbrechnung2.changeFunctionEditAbrechnung2();
                                }
                            },
                            changed: function (form, item, value) {
                                dfEditAbrechnung2.changeFunctionEditAbrechnung2();

                            },
                            keyPressFilter: "[0-9]"
                        },
                        {
                            defaultValue: "<b>Fiyat derleme</b>",
                            name: "Abrechnung",
                            type: "section",
                            colSpan: 4,
                            sectionExpanded: true,
                            canCollapse: false,
                            itemIds: ["mwst", "brutto_preis", /*"mwst_einzelpr", "brutto_preis", "gesamtpr_brutto",*/ "mwst_gesamtpr", "gesamtpr_brutto"]
                        },{
                            name: "mwst_",
                            title: "KDV",
                            type: "text",
                            defaultValue: 18,
                            colSpan: 2,
                            selectOnFocus: true,
                            selectOnClick: true,
                            width: 60,
                            keyPressFilter: "[0-9,]",
                            change: "preisFunction(dfEditAbrechnung2);"
                        },  {
                            name: "mwst",
                            title: "KDV",
                            type: "text",
                            defaultValue: 0,
                            colSpan: 2,
                            width: 60,
                            required: true,
                            change: "return false"
                        }, {
                            name: "brutto_preis_",
                            title: "Fiyat",
                            width: 100,
                            required: true,
                            colSpan: 2,
                            selectOnFocus: true,
                            selectOnClick: true,
                            defaultValue: 0,
                            type: "text",
                            keyPressFilter: "[0-9,]",
                            changed: function (form, item, value) {
                                preisFunction(form);
                            }
                        }, {
                            name: "brutto_preis",
                            title: "Fiyat",
                            width: 100,
                            required: true,
                            colSpan: 2,
                            defaultValue: 0,
                            type: "text",
                            change: "return false"
                        }, {
                            name: "mwst_gesamtpr",
                            title: "KDV Toplam tutarı",
                            width: 100,
                            colSpan: 2,
                            required: true,
                            defaultValue: 0,
                            type: "text",
                            change: "return false"
                        }, {
                            name: "gesamtpr_brutto",
                            title: "Toplam tutarı",
                            width: 100,
                            colSpan: 2,
                            required: true,
                            defaultValue: 0,
                            type: "text",
                            change: "return false"
                        }, {
                            defaultValue: "<b>Faturalandırmaya  not ekleyin</b>",
                            name: "Notiz",
                            type: "section",
                            colSpan: 4,
                            sectionExpanded: false,
                            canCollapse: true,
                            itemIds: ["bemerkung"]
                        }, {
                            name: "bemerkung",
                            title: "Not",
                            width: 250,
                            colSpan: 2,
                            length: 260,
                            type: "textArea",
                            changed: function (form, item, value) {
                                dfEditAbrechnung2.changeFunctionEditAbrechnung2();
                            }
                        }, {
                            name: "lfd_nr",
                            type: "hidden"
                        }], changeFunctionEditAbrechnung2: function () {
                        btnSpeichernAbrechnungEdit2.setDisabled(false);
                        btnResetAbrechnungEdit2.setDisabled(false);
                        btnCloseAbrechnungEdit2.setTitle("İptal et");
                        btnCloseAbrechnungEdit2.setIcon("famfam/cancel.png");
                    }
                });

                isc.IButton.create({
                    ID: "btnCloseAbrechnungEdit2",
                    type: "button",
                    disabled: false,
                    icon: "famfam/door_in.png",
                    name: "btnCloseAbrechnungEdit2",
                    showDisabledIcon: false,
                    title: "Kapat", width: 100, //Neuen Film anlegen
                    click: function () {
                        if (btnCloseAbrechnungEdit2.getTitle() == "İptal et") {
                            isc.ask("Gerçekten iptal etmek istiyor musunuz? Kaydedilmemiş veriler kaybolabilir.", function (value) {
                                if (value) {
                                    wdEditPositionen.hide();
                                }
                            }, {title: "İşlem iptal?"});
                        } else {
                            wdEditPositionen.hide();
                        }
                    }});

                isc.IButton.create({
                    ID: "btnSpeichernAbrechnungEdit2",
                    type: "button",
                    disabled: true,
                    count: 0,
                    showDisabledIcon: false,
                    icon: "famfam/database_save.png",
                    name: "btnSpeichernAbrechnungEdit2",
                    title: "Kaydet",
                    width: 100, //Neuen Film anlegen
                    click: function () {
                        belegNr_ = dfEditAbrechnung.getField("beleg_nr").getValue();
                        var aktErgebnis = dfEditAbrechnung2.getField("gesamtpr_brutto").getValue().replace(",", ".");
                        var Ergebnis = dfEditAbrechnung2.getField("menge").getValue() *
                                (parseFloat(dfEditAbrechnung2.getField("brutto_preis").getValue().replace(",", ".")));

                        if (rundung(Ergebnis, 2) != aktErgebnis) {
                            isc.say("Girilen değerler brüt toplam fiyattan sapma gösterir.\n\
                    Lütfen girişlerinizi tekrar kontrol edin ve miktar alanının yanındaki hesaplama tuşunu tekrar çalıştırın");
                        } else {

                            var _percent = pgbEditAbrechnung.percentDone + parseInt(10 + (50 * Math.random()));
                            pgbEditAbrechnung.setPercentDone(_percent);
                            pgbEditAbrechnung.setTitle(_percent + "%");

                            RPCManager.send("", function (rpcResponse, data, rpcRequest) {
                                var _data = isc.JSON.decode(data); // Daten aus dem PHP (rpcResponse)
                                if (_data.response.status === 0) {  // Durum 0 bedeutet Keine Hata
                                    laufendeNr = dfEditAbrechnung2.getField("lfd_nr").getValue();
                                    welcomeSite.setContentsURL(calendar_);
                                    btnSpeichernAbrechnungEdit2.count++;
                                    onRefreshAbrechnung("abrechnungsListeEdit", belegNr_, btnSpeichernAbrechnungEdit2.count);
                                    //                                isc.Timer.setTimeout("abrechnungsListe.abrechnungsSummenFunction()",1000);
                                    //                                onRefresh("abrechnungsTree");
                                    btnSpeichernAbrechnungEdit2.pgbEditAbrechnungFunction();
                                    isc.Timer.setTimeout("btnSpeichernAbrechnungEdit2.findNewPosition()", 300);
                                    //                                isc.say(laufendeNr);


                                } else { // Wenn die Validierungen Hata aufweisen dann:

                                    dfEditAbrechnung2.setErrors(_data.response.errors, true);
                                    var _errors = dfEditAbrechnung2.getErrors();
                                    for (var i in _errors)
                                    {
                                        isc.say("<b>Hata! </br>" + (_errors [i]) + "</b>", function (value) {
                                            if (value) {
                                                dfEditAbrechnung2.setTitle("");
                                                dfEditAbrechnung2.setPercentDone(0);
                                            }
                                        });
                                    }

                                }
                            }, {// Übergabe der Parameter
                                actionURL: "api/editPosition.php",
                                httpMethod: "POST",
                                contentType: "application/x-www-form-urlencoded",
                                useSimpleHttp: true,
                                params: {
                                    lfd_nr: dfEditAbrechnung2.getField("lfd_nr").getValue(),
                                    prod_kz: dfEditAbrechnung2.getField("prod_kz").getValue(),
                                    prod_bez: dfEditAbrechnung2.getField("prod_kz").getDisplayValue(),
                                    menge: dfEditAbrechnung2.getField("menge").getValue(),
                                    preis_kat: dfEditAbrechnung2.getField("preis_kat").getValue(),
                                    brutto_preis: dfEditAbrechnung2.getField("brutto_preis").getValue(),
                                    mwst: dfEditAbrechnung2.getField("mwst").getValue(),
                                    gesamtpr_brutto: dfEditAbrechnung2.getField("gesamtpr_brutto").getValue(),
                                    bemerkung: dfEditAbrechnung2.getField("bemerkung").getValue(),
                                    verkauf_an: dfEditAbrechnung.getField("verkauf_an").getValue(),
                                    kunden_name: dfEditAbrechnung.getField("verkauf_an").getDisplayValue(),
                                    datum: dfEditAbrechnung.getField("datum").getValue(),
                                    startTime: abrechnungsTree.getSelectedRecord().startTime,
                                    endTime: abrechnungsTree.getSelectedRecord().endTime,
                                    beleg_nr: dfEditAbrechnung.getField("beleg_nr").getValue()}

                            }); //Ende RPC
                        }//Ende If
                    }, // Ende Click
                    findNewPosition: function () {
                        if (laufendeNr != null) {
                            var newAbrechnung = abrechnungsListeEdit.data.find("lfd_nr", laufendeNr);
                            var index = abrechnungsListeEdit.getRecordIndex(newAbrechnung);
                            //                        abrechnungsListe.deselectAllRecords();
                            abrechnungsListeEdit.selectRecord(index);
                            abrechnungsListeEdit.scrollToRow(index);
                        }
                    },
                    pgbEditAbrechnungFunction: function () {
                        if (pgbEditAbrechnung.percentDone < 100) {
                            var _percent = pgbEditAbrechnung.percentDone + parseInt(10 + (50 * Math.random()));
                            pgbEditAbrechnung.setPercentDone(_percent); // Zufallswert wird berechnet

                            if (_percent <= 100) {
                                pgbEditAbrechnung.setTitle(_percent + "%");
                            } //Bis 100 wird mitgezählt
                            else {
                                pgbEditAbrechnung.setTitle("100%"); // ab 100 darf nicht mehr gezählt werden, da 100 leicht überstiegen wird.
                            }

                            isc.Timer.setTimeout("btnSpeichernAbrechnungEdit2.pgbEditAbrechnungFunction()", 200);
                        } else {
                            if (!dfEditAbrechnung2.validate() && dfEditAbrechnung2.hasErrors()) {
                                dfEditAbrechnung2.setErrors();
                                var _errors = dfEditAbrechnung2.getErrors();
                                for (var i in _errors)
                                {
                                    isc.say("<b>Hata! </br>" + (_errors [i]) + "</b>", function (value) {
                                        if (value) {
                                            pgbEditAbrechnung.setTitle("");
                                            pgbEditAbrechnung.setPercentDone(0);
                                        }
                                    }); // Hier wird jeder Wert des Array-Schlüssel angezeigt und das Feld oder die Feld-Bezeichnung ist irrelevant.
                                }
                            } else {
                                dfEditAbrechnung2.clearValues();
                                wdEditPositionen.hide();
                                btnSpeichernAbrechnungEdit2.setDisabled(true);
                                btnResetAbrechnungEdit2.setDisabled(true);
                                isc.Timer.setTimeout("btnSpeichernAbrechnungEdit2.findNewPosition()", 300);
                                pgbEditAbrechnung.setTitle("");
                                pgbEditAbrechnung.setPercentDone(0);
                                btnCloseAbrechnungEdit2.setTitle("Kapat");
                                btnCloseAbrechnungEdit2.setIcon("famfam/door_in.png");
                            }
                        }
                    }// Ende ProgressbarFunction
                });
                isc.IButton.create({
                    ID: "btnResetAbrechnungEdit2",
                    type: "button",
                    showDisabledIcon: false,
                    icon: "famfam/arrow_undo.png",
                    disabled: true,
                    name: "btnResetAbrechnungEdit2",
                    title: "Reset", width: 100,
                    click: function () {
                        dfEditAbrechnung2.reset();
                        btnSpeichernAbrechnungEdit2.setDisabled(true);
                        btnResetAbrechnungEdit2.setDisabled(true);
                        btnCloseAbrechnungEdit2.setTitle("Kapat");
                        btnCloseAbrechnungEdit2.setIcon("famfam/door_in.png");
                    }});




                isc.HLayout.create({
                    ID: "HLayoutAbrechnungEdit2",
                    height: 30,
                    width: "100%",
                    align: "center",
                    members: [btnCloseAbrechnungEdit2, isc.LayoutSpacer.create({
                            width: 20
                        }), btnSpeichernAbrechnungEdit2, isc.LayoutSpacer.create({
                            width: 20
                        }), btnResetAbrechnungEdit2]});



                isc.Window.create({
                    ID: "wdEditPositionen", // Fenster zum Bearbeiten der Positionen
                    title: "Fatura düzenleme",
                    autoSize: true,
                    autoCenter: true,
                    showFooter: false,
                    showMinimizeButton: false,
                    showCloseButton: false,
                    width: 500,
                    headerIconDefaults: {
                        width: 16,
                        height: 16,
                        src: "famfam/user_add.png"
                    },
                    canDragReposition: true,
                    canDragResize: false,
                    showShadow: true,
                    showModalMask: true,
                    modalMaskOpacity: 10,
                    isModal: true,
                    items: [dfEditAbrechnung2, HLayoutAbrechnungEdit2, pgbEditAbrechnung]
                });
                /*
                 * ********************** Ende edit Abrechnung *******************
                 * -------------------------------------------------------------
                 */


                /*
                 * **************** ANFANG LAYOUT Abrechnung *******************
                 * -------------------------------------------------------------
                 */


                isc.VLayout.create({
                    ID: "VLayoutAbrechnungForm_Liste_Edit",
                    height: "100%",
                    width: "75%",
                    align: "center",
                    members: [dfEditAbrechnung, HLayoutAbrechnungEdit, abrechnungsListeEdit]});




                isc.HLayout.create({
                    ID: "HLayoutAbrechnungGrid_Tree",
                    height: "100%",
                    width: "100%",
                    align: "center",
                    members: [abrechnungsTree, welcomeSite/*, VLayoutAbrechnungForm_Liste_Edit*/]});


                isc.Window.create({
                    ID: "wdEditAbrechnung", // Fenster zum Bearbeiten der gesamten Abrechnung
                    title: "Fatura düzenleme",
                    autoSize: false,
                    autoCenter: true,
                    showFooter: false,
                    showMinimizeButton: true,
                    showCloseButton: true,
                    width: 700,
                    height: 500,
                    headerIconDefaults: {
                        width: 16,
                        height: 16,
                        src: "famfam/user_add.png"
                    },
                    canDragReposition: true,
                    canDragResize: false,
                    items: [VLayoutAbrechnungForm_Liste_Edit]
                });



                /*
                 * ****************** ENDE LAYOUT Abrechnung *******************
                 * -------------------------------------------------------------
                 */


                /*
                 ***************** Protokol-ListGrid Abrechnung **************** 
                 */


                // define a KachelListen class (subclass of ListGrid)
                ClassFactory.defineClass("histAbrechnungsListe", ListGrid);

                histAbrechnungsListe.addProperties({
                    alternateRecordStyles: true,
                    autoFetchData: false,
                    width: 849, height: 499,
                    dataSource: histAbrechnungDS,
                    selectionType: "single",
                    showAllRecords: true,
                    canExpandRecords: false,
                    expansionMode: "details",
                    margin: 0,
                    fields: [{name: "lfn",
                            showIf: "false"},
                        {
                            name: "lfd_nr",
                            type: "text",
                            showIf: "false",
                            width: 50
                        }, {
                            name: "kunden_nr",
                            type: "text",
                            showIf: "false",
                            width: 50
                        }, {
                            name: "kunden_name",
                            type: "text",
                            showIf: "true",
                            width: 150
                        }, {
                            name: "prod_kz",
                            type: "text",
                            showIf: "false",
                            width: 50
                        }, {
                            name: "prod_bez",
                            type: "text",
                            showIf: "true",
                            width: 200
                        },
                        {name: "user",
                            showIf: "true",
                            width: 50},
                        {name: "aenderdat",
                            showIf: "true",
                            width: 100},
                        {name: "feld",
                            showIf: "true"},
                        {name: "a_inhalt",
                            showIf: "true"},
                        {name: "n_inhalt",
                            showIf: "true"},
                        {name: "codetext",
                            showIf: "true",
                            width: "*"}]
                });

                isc.histAbrechnungsListe.create({
                    ID: "abrechnungHistListeEinzelWD",
                    selectionChanged: function (record, state) {
                    }
                });

                isc.histAbrechnungsListe.create({
                    ID: "abrechnungHistListeGesamtWD",
                    showFilterEditor: true,
                    filterOnKeypress: true,
                    selectionChanged: function (record, state) {
                    }
                });

                /*
                 ***************** Window Gesamt Protokol Abrechnung *********** 
                 */
                isc.Window.create({
                    ID: "wdAbrechnungHist",
                    title: "Abrechnungshistorie",
                    autoSize: true,
                    autoCenter: true,
                    showFooter: false,
                    showMinimizeButton: false,
                    showCloseButton: true,
                    width: 850,
                    height: 500,
                    headerIconDefaults: {
                        width: 16,
                        height: 16,
                        src: "famfam/report.png"
                    },
                    canDragReposition: true,
                    canDragResize: false,
                    showShadow: true,
                    showModalMask: true,
                    modalMaskOpacity: 10,
                    isModal: true,
                    items: [abrechnungHistListeEinzelWD, abrechnungHistListeGesamtWD]
                });

                //---------------ANFANG Abrechnungssuche---------------------------------------------------------------------------------------------------------

                var AbrechnungSuchFormWidths = 140;
                isc.DynamicForm.create({
                    ID: "AbrechnungSuchForm",
                    width: 150,
                    height: "100%",
                    backgroundColor: "#DFDFFF",
                    numCols: 1,
                    titleOrientation: "top",
                    validateOnExit: true,
                    count: 0,
                    validateOnChange: false,
                    margin: 0,
                    fields: [{
                            name: "verkauf_an",
                            title: "Hasta no",
                            //            align: "center",
                            type: "select",
                            disabled: false,
                            autoFetchData: false,
                            width: AbrechnungSuchFormWidths,
                            optionDataSource: "AbrechnungSucheFelderDS",
                            valueField: "verkauf_an",
                            displayField: "kunden_name",
                            pickListProperties: {
                                showShadow: false,
                                showFilterEditor: false,
                                showHeader: true
                            },
                            pickListWidth: AbrechnungSuchFormWidths + 60,
                            pickListFields: [{
                                    name: "verkauf_an",
                                    width: 40
                                }, {
                                    name: "kunden_name",
                                    width: "*"
                                }
                            ],
                            getPickListFilterCriteria: function () {
                                var filter = {
                                    prod_kz: AbrechnungSuchForm.getField("prod_kz").getValue(),
                                    datum: AbrechnungSuchForm.getField("datum").getValue(),
                                    beleg_nr: AbrechnungSuchForm.getField("beleg_nr").getValue(),
                                    lookFor: "v.verkauf_an",
                                    count: AbrechnungSuchForm.count++
                                };

                                return filter;
                            },
                            changed: function (form, item, value) {
                                form.detailAbrechnungsSuche(form);
                            }, icons: [{
                                    src: "famfam/delete.png",
                                    width: 14,
                                    height: 14,
                                    click: function () {
                                        AbrechnungSuchForm.getField("verkauf_an").clearValue();
                                        AbrechnungSuchForm.detailAbrechnungsSuche(AbrechnungSuchForm);
                                    }
                                }]
                        }, {
                            name: "prod_kz",
                            title: "Tedavi no",
                            width: AbrechnungSuchFormWidths,
                            type: "select",
                            //            align: "center",
                            disabled: false,
                            optionDataSource: "AbrechnungSucheFelderDS",
                            valueField: "prod_kz",
                            autoFetchData: false,
                            displayField: "prod_bez",
                            pickListProperties: {
                                showShadow: false,
                                showFilterEditor: false,
                                showHeader: true
                            },
                            pickListWidth: AbrechnungSuchFormWidths + 60,
                            pickListFields: [{
                                    name: "prod_kz",
                                    width: 40
                                }, {
                                    name: "prod_bez",
                                    width: "*"
                                }
                            ],
                            getPickListFilterCriteria: function () {

                                var filter_vkz_neu = {
                                    verkauf_an: AbrechnungSuchForm.getField("verkauf_an").getValue(),
                                    datum: AbrechnungSuchForm.getField("datum").getValue(),
                                    beleg_nr: AbrechnungSuchForm.getField("beleg_nr").getValue(),
                                    lookFor: "v.prod_kz",
                                    count: AbrechnungSuchForm.count++
                                };

                                return filter_vkz_neu;
                            },
                            changed: function (form, item, value) {
                                form.detailAbrechnungsSuche(form);
                            }, icons: [{
                                    src: "famfam/delete.png",
                                    width: 14,
                                    height: 14,
                                    click: function () {
                                        AbrechnungSuchForm.getField("prod_kz").clearValue();
                                        AbrechnungSuchForm.detailAbrechnungsSuche(AbrechnungSuchForm);
                                    }
                                }]
                        }, {
                            name: "datum",
                            type: "select",
                            //            align: "center",
                            keyPressFilter: "[0-9.]",
                            required: false,
                            disabled: false,
                            width: AbrechnungSuchFormWidths,
                            title: "Fatura tarihi",
                            optionDataSource: "AbrechnungSucheFelderDS",
                            valueField: "datum",
                            autoFetchData: false,
                            displayField: "datum",
                            pickListProperties: {
                                showShadow: false,
                                showFilterEditor: false,
                                showHeader: true
                            },
                            pickListWidth: AbrechnungSuchFormWidths + 5,
                            pickListFields: [{
                                    name: "datum",
                                    width: "*"
                                }
                            ],
                            getPickListFilterCriteria: function () {

                                var filter = {
                                    verkauf_an: AbrechnungSuchForm.getField("verkauf_an").getValue(),
                                    prod_kz: AbrechnungSuchForm.getField("prod_kz").getValue(),
                                    beleg_nr: AbrechnungSuchForm.getField("beleg_nr").getValue(),
                                    lookFor: "v.datum",
                                    count: AbrechnungSuchForm.count++
                                };

                                return filter;
                            },
                            changed: function (form, item, value) {
                                form.detailAbrechnungsSuche(form);
                            }, icons: [{
                                    src: "famfam/delete.png",
                                    width: 14,
                                    height: 14,
                                    click: function () {
                                        AbrechnungSuchForm.getField("datum").clearValue();
                                        AbrechnungSuchForm.detailAbrechnungsSuche(AbrechnungSuchForm);
                                    }
                                }]
                        }, {
                            name: "beleg_nr",
                            type: "select",
                            //            align: "center",
                            keyPressFilter: "[0-9]",
                            required: false,
                            disabled: false,
                            width: AbrechnungSuchFormWidths,
                            title: "Fatura-Nr",
                            optionDataSource: "AbrechnungSucheFelderDS",
                            valueField: "beleg_nr",
                            autoFetchData: false,
                            displayField: "beleg_nr",
                            pickListProperties: {
                                showShadow: false,
                                showFilterEditor: false,
                                showHeader: true
                            },
                            pickListWidth: AbrechnungSuchFormWidths - 5,
                            pickListFields: [{
                                    name: "beleg_nr",
                                    width: "*"
                                }
                            ],
                            getPickListFilterCriteria: function () {

                                var filter = {
                                    verkauf_an: AbrechnungSuchForm.getField("verkauf_an").getValue(),
                                    prod_kz: AbrechnungSuchForm.getField("prod_kz").getValue(),
                                    datum: AbrechnungSuchForm.getField("datum").getValue(),
                                    lookFor: "v.beleg_nr",
                                    count: AbrechnungSuchForm.count++
                                };

                                return filter;
                            },
                            changed: function (form, item, value) {
                                form.detailAbrechnungsSuche(form);
                            }, icons: [{
                                    src: "famfam/delete.png",
                                    width: 14,
                                    height: 14,
                                    click: function () {
                                        AbrechnungSuchForm.getField("beleg_nr").clearValue();
                                        AbrechnungSuchForm.detailAbrechnungsSuche(AbrechnungSuchForm);
                                    }
                                }]
                        }
                    ],
                    detailAbrechnungsSuche: function (_form) {
                        var noSearch = null;
                        var _verkauf_an = null;
                        var _prod_kz = null;
                        var _datum = null;
                        var _beleg_nr = null;

                        if (typeof (_form.getField("verkauf_an").getValue()) !== noSearch) {
                            _verkauf_an = _form.getField("verkauf_an").getValue();
                        }

                        if (typeof (_form.getField("prod_kz").getValue()) !== noSearch) {
                            _prod_kz = _form.getField("prod_kz").getValue();
                        }
                        if (typeof (_form.getField("datum").getValue()) !== noSearch) {
                            _datum = _form.getField("datum").getValue();
                        }
                        if (typeof (_form.getField("beleg_nr").getValue()) !== noSearch) {
                            _beleg_nr = _form.getField("beleg_nr").getValue();
                        }


                        AbrechnungSuchErgebnisListe.fetchData({verkauf_an: _verkauf_an, prod_kz: _prod_kz, datum: _datum, beleg_nr: _beleg_nr, count: AbrechnungSuchForm.count++});

                        isc.Timer.setTimeout("AbrechnungSuchErgebnisListe.redraw()", 500);
                    }
                });

                isc.DynamicForm.create({
                    ID: "AbrechnungFreieSuchForm",
                    width: "60%",
                    //    backgroundColor: "#DFDFFF",    
                    dataSource: AbrechnungSucheDS,
                    titleOrientation: "left",
                    validateOnExit: true,
                    validateOnChange: false,
                    margin: 0,
                    fields: [{
                            name: "freieSuche",
                            type: "text",
                            //            align: "center",
                            hint: "Buraya Fatura no girebilirsiniz",
                            showHintInField: true,
                            required: false,
                            width: 280,
                            keyPress: function () {
                                if (isc.Event.getKey() == "Enter") {
                                    AbrechnungSuchErgebnisListe.fetchData({freieSuche: AbrechnungFreieSuchForm.getField("freieSuche").getValue()});
                                    isc.Timer.setTimeout("AbrechnungSuchErgebnisListe.redraw()", 500);
                                }
                            },
                            title: "Genel arama",
                            icons: [{
                                    src: "famfam/magnifier.png",
                                    click: function () {
                                        AbrechnungSuchErgebnisListe.fetchData({freieSuche: AbrechnungFreieSuchForm.getField("freieSuche").getValue()});
                                        isc.Timer.setTimeout("AbrechnungSuchErgebnisListe.redraw()", 500);
                                    },
                                    prompt: "Aramaya başlar"
                                }]
                        }]
                });

                isc.ToolStripButton.create({icon: "famfam/arrow_right.png",
                    ID: "goToPriceButton",
                    name: "goToPriceButton",
                    disabled: true,
                    showDisabledIcon: false,
                    showSelectedIcon: false,
                    click: function () {
                        if (AbrechnungSuchErgebnisListe.getTotalRows() == 0) {
                            isc.say("Sonuç listesi boş!");
                            return;
                        }
                        if (AbrechnungSuchErgebnisListe.getSelection().length == 0) {
                            isc.say("Lütfen önce bir Fatura seçin");
                            return;
                        }
                        belegNr_ = AbrechnungSuchErgebnisListe.getSelectedRecord().beleg_nr;

                        isc.Timer.setTimeout("btnSpeichernAbrechnungNeu.findAbrechnung()", 300);
                    },
                    title: "",
                    prompt: "Seçilen Faturaya yönlendirir"});

                isc.ToolStripButton.create({name: "versSuchLeeren",
                    ID: "AbrechnungSuchFelderLeeren",
                    title: "",
                    prompt: "Tüm arama alanlarını boşalt",
                    icon: "famfam/textfield_delete.png",
                    click: function () {
                        AbrechnungFreieSuchForm.clearValues();
                        AbrechnungSuchForm.clearValues();
                        AbrechnungSuchErgebnisListe.setData([]);
                        label_abrechnungSuche.setContents("");
                    }});

                isc.Label.create({
                    ID: "label_abrechnungSuche",
                    //    height: 20,
                    //    padding: 5,
                    width: "30%",
                    align: "center",
                    valign: "center",
                    wrap: false,
                    //icon: "icons/16/close.png",
                    showEdges: false,
                    contents: ""
                });

                isc.ToolStrip.create({// Toolstrip
                    ID: "gridControlsAbrechnungSuche",
                    width: "100%",
                    height: 30,
                    members: [AbrechnungSuchFelderLeeren, AbrechnungFreieSuchForm, label_abrechnungSuche, goToPriceButton]});

                // ListGrid Preis-Suche 
                isc.ListGrid.create({
                    ID: "AbrechnungSuchErgebnisListe",
                    ListCnt: 0,
                    width: "100%",
                    height: 482,
                    headerHeight: 24,
                    leaveScrollbarGap: false,
                    alternateRecordStyles: true,
                    dataSource: AbrechnungSucheDS,
                    autoFetchData: false,
                    showFilterEditor: false,
                    filterOnKeypress: false, // <<<<< Suche beginnt bereits beim Eintippen
                    selectionType: "single",
                    canExpandRecords: false,
                    showAllRecords: true,
                    margin: 0,
                    fields: [{name: "beleg_nr",
                            title: "Fatura-Nr",
                            type: "text",
                            width: 60},
                        {
                            name: "verkauf_an",
                            width: 60
                        },
                        {
                            name: "kunden_name",
                            width: "*"
                        }, {
                            name: "datum",
                            width: 100
                        }, {
                            name: "gesamtpr_brutto",
                            width: 80
                        }, {
                            name: "anzPos",
                            width: 55
                        }
                    ],
                    selectionChanged: function (record, state) {
                        if (state) {
                            goToPriceButton.setDisabled(false);
                        } else {
                            goToPriceButton.setDisabled(true);
                        }
                        //Key wird in das RecordIndex Feld geschrieben, damit es von der Funktion SaveButtonEditPreiseForm.findRecordTimer()
                        // zum Selektieren des Preises genutzt werden kann
                        belegNr_ = record.beleg_nr;
                        laufendeNr = null;
                    },
                    dataChanged: function () {
                        // Preisanzahl-Berechnung
                        if (!Array.isLoading(AbrechnungSuchErgebnisListe.getRecord(0))) {
                            var _totalRows = AbrechnungSuchErgebnisListe.getTotalRows();
                            if (_totalRows > 0) {
                                if (_totalRows > 1) {
                                    label_abrechnungSuche.setContents(_totalRows + " Fatura bulundu");
                                } else {
                                    label_abrechnungSuche.setContents(_totalRows + " Fatura bulundu");
                                }
                            } else {
                                label_abrechnungSuche.setContents("Fatura bulunamadı");
                            }
                        }
                    }, recordDoubleClick: function () {
                        belegNr_ = AbrechnungSuchErgebnisListe.getSelectedRecord().beleg_nr;
                        laufendeNr = null;

                        isc.Timer.setTimeout("btnSpeichernAbrechnungNeu.findAbrechnung()", 300);
                    }
                });

                isc.HLayout.create({
                    ID: "HLayoutAbrechnungSuche",
                    height: "100%",
                    width: "100%",
                    align: "center",
                    layoutMargin: 0,
                    members: [AbrechnungSuchForm, AbrechnungSuchErgebnisListe]
                });

                // Window Preis-Suche
                PreisSuchIcon = "famfam/find.png";
                isc.Window.create({
                    ID: "AbrechnungSuchWindow",
                    title: "Gelişmiş arama",
                    autoSize: true,
                    autoCenter: true,
                    width: 700,
                    height: 500,
                    showCloseButton: true,
                    showStatusBar: false,
                    showFooter: false,
                    headerIconDefaults: {
                        width: 16,
                        height: 16,
                        src: PreisSuchIcon
                    },
                    showShadow: true,
                    showModalMask: true,
                    modalMaskOpacity: 10,
                    isModal: true,
                    showMinimizeButton: false,
                    canDragReposition: true,
                    canDragResize: false,
                    // headerIconDefaults: {width:16, height: 16, src: "currentIcon.png"},

                    items: [gridControlsAbrechnungSuche, HLayoutAbrechnungSuche]
                });

                //---------------ENDE Abrechnungssuche----------------------------------------


                /*
                 * ************************* ENDE ABRECHNUNG ***********************************
                 * *****************************************************************************
                 */



                /*
                 * ************************* ANFANG BUCHUNGEN **********************************
                 * *****************************************************************************
                 */

                /*
                 * ********************** LISTGRID BUCHUNGEN **********************************
                 * *****************************************************************************
                 */

                isc.ListGrid.create({
                    ID: "buchungsListe",
                    //   header: "Daten düzenleme",
                    width: "100%", height: "100%",
                    alternateRecordStyles: true,
                    dataSource: buchungenHauptDS,
                    contextMenu: "",
                    autoFetchData: false,
                    taksit_count: 0,
                    showFilterEditor: false,
                    filterOnKeypress: true,
                    selectionType: "single",
                    showAllRecords: true,
                    canExpandRecords: true,
                    showGridSummary: true,
                    showGroupSummary: true,
                    expansionMode: "details",
                    margin: 0,
                    //                    groupByField: ['datum'],
                    //                    groupStartOpen: "all",
                    fields: [
                        {name: "beleg_nr", type: "text", width: 80, showIf: "true"},
                        {name: "datum", type: "date", width: 120, align: "center", showIf: "true"},
                        {name: "name_mit_knd_nr", title: "Hasta", type: "text", width: 200, showIf: "true",
                            recordSummaryFunction: "multiplier",
                            summaryFunction: "count"},
                        {name: "verkauf_an", type: "text", showIf: "false"},
                        {name: "mwst_gesamtpr", type: "text", width: 120, align: "right",
                            recordSummaryFunction: "multiplier",
                            summaryFunction: "sum",
                            formatCellValue: function (value) {
                                if (isc.isA.Number(value)) {
                                    return value.toCurrencyString("₺ ");
                                }
                                return value;
                            }},
                        {name: "gesamtpr_brutto", type: "text", width: 120, align: "right",
                            recordSummaryFunction: "multiplier",
                            summaryFunction: "sum",
                            formatCellValue: function (value) {
                                if (isc.isA.Number(value)) {
                                    return value.toCurrencyString("₺ ");
                                }
                                return value;
                            }},
                        {name: "beleg_pfad", width: 60, showIf: "true", align: "center"}
                    ], getExpansionComponent: function (record) {


                        var buchungsListeDetails = isc.ListGrid.create({
                            height: 120,
                            cellheight: 22,
                            dataSource: buchungenDetailsDS,
                            canEdit: false,
                            fields: [
                                {name: "beleg_nr", type: "text", showIf: "false", width: 80},
                                {name: "lfd_nr", showIf: "false", width: 50},
                                {name: "prod_kz", type: "text", title: "Tedavi no", width: 100},
                                {name: "bezeichnung", type: "text", title: "Tedavi", width: "*"},
                                {name: "menge", type: "text", title: "Miktar", width: 80},
                                {name: "preis_kat", type: "text", title: "Fiyat kat.", width: 80},
                                {name: "brutto_preis", type: "text", title: "Fiyat", width: 100},
                                {name: "mwst", type: "text", title: "KDV", width: 80,
                                    formatCellValue: function (value) {
                                        if (isc.isA.Number(value)) {
                                            return value + " %";
                                        }
                                        return value;
                                    }}
                            ]
                        });
                        buchungsListeDetails.fetchRelatedData(record, buchungsListe);

                        return buchungsListeDetails;
                    },
                    selectionChanged: function (record, state) {
                        if (state) {

                        } else {
                        }
                    }
                });
                var PickListWidth = 200;
                var Width = 200;
                isc.DynamicForm.create({
                    ID: "dfBuchungenZeitraum",
                    width: 150,
                    height: "100%",
                    count: 0,
                    numCols: 1,
                    titleOrientation: "top",
                    validateOnExit: true,
                    validateOnChange: false,
                    margin: 5,
                    fields: [{
                            name: "jahr",
                            title: "Sene",
                            type: "select",
                            optionDataSource: "verkaeufeSucheFelderDS",
                            colSpan: 1,
                            width: Width,
                            valueField: "jahr",
                            autoFetchData: false,
                            displayField: "jahr",
                            pickListProperties: {
                                showShadow: false,
                                showFilterEditor: false,
                                showHeader: false
                            },
                            pickListWidth: PickListWidth,
                            pickListFields: [{name: "jahr", width: "*"}
                            ], icons: [{
                                    src: "famfam/delete.png",
                                    width: 14,
                                    height: 14,
                                    click: function () {
                                        dfBuchungenZeitraum.getField("jahr").clearValue();
                                        dfBuchungenZeitraum.detailSuche(dfBuchungenZeitraum, "nein");
                                    }
                                }],
                            getPickListFilterCriteria: function () {

                                var filter = {
                                    prod_kz: dfBuchungenZeitraum.getField("prod_kz").getValue(),
                                    verkauf_an: dfBuchungenZeitraum.getField("verkauf_an").getValue(),
                                    beleg_nr: dfBuchungenZeitraum.getField("beleg_nr").getValue(),
                                    zahlungsziel: dfBuchungenZeitraum.getField("zahlungsziel_kz").getValue(),
                                    geburtstag: dfBuchungenZeitraum.getField("geburtstag").getValue(),
                                    datum: dfBuchungenZeitraum.getField("datum").getValue(),
                                    monat: dfBuchungenZeitraum.getField("monat").getValue(),
                                    lookFor: "jahr",
                                    count: ++dfBuchungenZeitraum.count
                                };

                                return filter;
                            },
                            changed: function (form, item, value) {
                                dfBuchungenZeitraum.detailSuche(dfBuchungenZeitraum, "nein");
                            }
                        }, {
                            name: "monat",
                            title: "Ay",
                            type: "select",
                            optionDataSource: "verkaeufeSucheFelderDS",
                            colSpan: 1,
                            width: Width,
                            valueField: "monat",
                            autoFetchData: false,
                            displayField: "monatAusg",
                            pickListProperties: {
                                showShadow: false,
                                showFilterEditor: false,
                                showHeader: false
                            },
                            pickListWidth: PickListWidth,
                            pickListFields: [{name: "monatAusg", width: "*"}
                            ], icons: [{
                                    src: "famfam/delete.png",
                                    width: 14,
                                    height: 14,
                                    click: function () {
                                        dfBuchungenZeitraum.getField("monat").clearValue();
                                        dfBuchungenZeitraum.detailSuche(dfBuchungenZeitraum, "nein");
                                    }
                                }],
                            getPickListFilterCriteria: function () {

                                var filter = {
                                    prod_kz: dfBuchungenZeitraum.getField("prod_kz").getValue(),
                                    verkauf_an: dfBuchungenZeitraum.getField("verkauf_an").getValue(),
                                    beleg_nr: dfBuchungenZeitraum.getField("beleg_nr").getValue(),
                                    zahlungsziel: dfBuchungenZeitraum.getField("zahlungsziel_kz").getValue(),
                                    geburtstag: dfBuchungenZeitraum.getField("geburtstag").getValue(),
                                    datum: dfBuchungenZeitraum.getField("datum").getValue(),
                                    jahr: dfBuchungenZeitraum.getField("jahr").getValue(),
                                    lookFor: "monat",
                                    count: ++dfBuchungenZeitraum.count
                                };

                                return filter;
                            },
                            changed: function (form, item, value) {
                                dfBuchungenZeitraum.detailSuche(dfBuchungenZeitraum, "nein");
                            }
                        }, {
                            name: "datum",
                            title: "Tarih",
                            type: "select",
                            optionDataSource: "verkaeufeSucheFelderDS",
                            colSpan: 1,
                            width: Width,
                            valueField: "datum",
                            autoFetchData: false,
                            displayField: "datum",
                            pickListProperties: {
                                showShadow: false,
                                showFilterEditor: false,
                                showHeader: false
                            },
                            pickListWidth: PickListWidth,
                            pickListFields: [{name: "datum", width: "*"}
                            ], icons: [{
                                    src: "famfam/delete.png",
                                    width: 14,
                                    height: 14,
                                    click: function () {
                                        dfBuchungenZeitraum.getField("datum").clearValue();
                                        dfBuchungenZeitraum.detailSuche(dfBuchungenZeitraum, "nein");
                                    }
                                }],
                            getPickListFilterCriteria: function () {

                                var filter = {
                                    prod_kz: dfBuchungenZeitraum.getField("prod_kz").getValue(),
                                    verkauf_an: dfBuchungenZeitraum.getField("verkauf_an").getValue(),
                                    beleg_nr: dfBuchungenZeitraum.getField("beleg_nr").getValue(),
                                    zahlungsziel: dfBuchungenZeitraum.getField("zahlungsziel_kz").getValue(),
                                    geburtstag: dfBuchungenZeitraum.getField("geburtstag").getValue(),
                                    jahr: dfBuchungenZeitraum.getField("jahr").getValue(),
                                    monat: dfBuchungenZeitraum.getField("monat").getValue(),
                                    lookFor: "datum",
                                    count: ++dfBuchungenZeitraum.count
                                };

                                return filter;
                            },
                            changed: function (form, item, value) {
                                dfBuchungenZeitraum.detailSuche(dfBuchungenZeitraum, "nein");
                            }
                        }, {
                            name: "verkauf_an",
                            title: "Ad ve Soyad",
                            type: "select",
                            optionDataSource: "verkaeufeSucheFelderDS",
                            colSpan: 1,
                            width: Width,
                            valueField: "verkauf_an",
                            autoFetchData: false,
                            displayField: "name",
                            pickListProperties: {
                                showShadow: false,
                                showFilterEditor: false,
                                showHeader: false
                            },
                            pickListWidth: PickListWidth,
                            pickListFields: [{name: "verkauf_an", width: 60}, {name: "name", width: "*"}
                            ], icons: [{
                                    src: "famfam/delete.png",
                                    width: 14,
                                    height: 14,
                                    click: function () {
                                        dfBuchungenZeitraum.getField("verkauf_an").clearValue();
                                        dfBuchungenZeitraum.detailSuche(dfBuchungenZeitraum, "nein");
                                    }
                                }],
                            getPickListFilterCriteria: function () {

                                var filter = {
                                    datum: dfBuchungenZeitraum.getField("datum").getValue(),
                                    prod_kz: dfBuchungenZeitraum.getField("prod_kz").getValue(),
                                    beleg_nr: dfBuchungenZeitraum.getField("beleg_nr").getValue(),
                                    zahlungsziel: dfBuchungenZeitraum.getField("zahlungsziel_kz").getValue(),
                                    geburtstag: dfBuchungenZeitraum.getField("geburtstag").getValue(),
                                    jahr: dfBuchungenZeitraum.getField("jahr").getValue(),
                                    monat: dfBuchungenZeitraum.getField("monat").getValue(),
                                    lookFor: "verkauf_an",
                                    count: ++dfBuchungenZeitraum.count
                                };

                                return filter;
                            },
                            changed: function (form, item, value) {
                                dfBuchungenZeitraum.detailSuche(dfBuchungenZeitraum, "nein");
                            }
                        }, {
                            name: "prod_kz",
                            title: "Tedavi",
                            type: "select",
                            optionDataSource: "verkaeufeSucheFelderDS",
                            colSpan: 1,
                            width: Width,
                            valueField: "prod_kz",
                            autoFetchData: false,
                            displayField: "bezeichnung",
                            pickListProperties: {
                                showShadow: false,
                                showFilterEditor: false,
                                showHeader: false
                            },
                            pickListWidth: PickListWidth,
                            pickListFields: [{name: "prod_kz", width: 60}, {name: "bezeichnung", width: "*"}
                            ], icons: [{
                                    src: "famfam/delete.png",
                                    width: 14,
                                    height: 14,
                                    click: function () {
                                        dfBuchungenZeitraum.getField("prod_kz").clearValue();
                                        dfBuchungenZeitraum.detailSuche(dfBuchungenZeitraum, "nein");
                                    }
                                }],
                            getPickListFilterCriteria: function () {

                                var filter = {
                                    datum: dfBuchungenZeitraum.getField("datum").getValue(),
                                    verkauf_an: dfBuchungenZeitraum.getField("verkauf_an").getValue(),
                                    beleg_nr: dfBuchungenZeitraum.getField("beleg_nr").getValue(),
                                    zahlungsziel: dfBuchungenZeitraum.getField("zahlungsziel_kz").getValue(),
                                    geburtstag: dfBuchungenZeitraum.getField("geburtstag").getValue(),
                                    jahr: dfBuchungenZeitraum.getField("jahr").getValue(),
                                    monat: dfBuchungenZeitraum.getField("monat").getValue(),
                                    lookFor: "prod_kz",
                                    count: ++dfBuchungenZeitraum.count
                                };

                                return filter;
                            },
                            changed: function (form, item, value) {
                                dfBuchungenZeitraum.detailSuche(dfBuchungenZeitraum, "nein");
                            }
                        }, {
                            name: "beleg_nr",
                            title: "Fatura no",
                            type: "select",
                            optionDataSource: "verkaeufeSucheFelderDS",
                            colSpan: 1,
                            width: Width,
                            valueField: "beleg_nr",
                            autoFetchData: false,
                            displayField: "beleg_nr",
                            pickListProperties: {
                                showShadow: false,
                                showFilterEditor: false,
                                showHeader: false
                            },
                            pickListWidth: PickListWidth,
                            pickListFields: [{name: "beleg_nr", width: "*"}
                            ], icons: [{
                                    src: "famfam/delete.png",
                                    width: 14,
                                    height: 14,
                                    click: function () {
                                        dfBuchungenZeitraum.getField("beleg_nr").clearValue();
                                        dfBuchungenZeitraum.detailSuche(dfBuchungenZeitraum, "nein");
                                    }
                                }],
                            getPickListFilterCriteria: function () {

                                var filter = {
                                    datum: dfBuchungenZeitraum.getField("datum").getValue(),
                                    verkauf_an: dfBuchungenZeitraum.getField("verkauf_an").getValue(),
                                    prod_kz: dfBuchungenZeitraum.getField("prod_kz").getValue(),
                                    zahlungsziel: dfBuchungenZeitraum.getField("zahlungsziel_kz").getValue(),
                                    geburtstag: dfBuchungenZeitraum.getField("geburtstag").getValue(),
                                    jahr: dfBuchungenZeitraum.getField("jahr").getValue(),
                                    monat: dfBuchungenZeitraum.getField("monat").getValue(),
                                    lookFor: "beleg_nr",
                                    count: ++dfBuchungenZeitraum.count
                                };

                                return filter;
                            },
                            changed: function (form, item, value) {
                                dfBuchungenZeitraum.detailSuche(dfBuchungenZeitraum, "nein");
                            }
                        }, {
                            name: "geburtstag",
                            title: "Dogum Tarih",
                            type: "select",
                            optionDataSource: "verkaeufeSucheFelderDS",
                            colSpan: 1,
                            width: Width,
                            valueField: "geburtstag",
                            autoFetchData: false,
                            displayField: "geburtstag",
                            pickListProperties: {
                                showShadow: false,
                                showFilterEditor: false,
                                showHeader: false
                            },
                            pickListWidth: PickListWidth,
                            pickListFields: [{name: "geburtstag", width: "*"}
                            ], icons: [{
                                    src: "famfam/delete.png",
                                    width: 14,
                                    height: 14,
                                    click: function () {
                                        dfBuchungenZeitraum.getField("geburtstag").clearValue();
                                        dfBuchungenZeitraum.detailSuche(dfBuchungenZeitraum, "nein");
                                    }
                                }],
                            getPickListFilterCriteria: function () {

                                var filter = {
                                    datum: dfBuchungenZeitraum.getField("datum").getValue(),
                                    verkauf_an: dfBuchungenZeitraum.getField("verkauf_an").getValue(),
                                    prod_kz: dfBuchungenZeitraum.getField("prod_kz").getValue(),
                                    zahlungsziel: dfBuchungenZeitraum.getField("zahlungsziel_kz").getValue(),
                                    beleg_nr: dfBuchungenZeitraum.getField("beleg_nr").getValue(),
                                    jahr: dfBuchungenZeitraum.getField("jahr").getValue(),
                                    monat: dfBuchungenZeitraum.getField("monat").getValue(),
                                    lookFor: "geburtstag",
                                    count: ++dfBuchungenZeitraum.count
                                };

                                return filter;
                            },
                            changed: function (form, item, value) {
                                dfBuchungenZeitraum.detailSuche(dfBuchungenZeitraum, "nein");
                            }
                        }, {
                            name: "zahlungsziel_kz",
                            title: "Ödeme sekli",
                            type: "select",
                            optionDataSource: "verkaeufeSucheFelderDS",
                            colSpan: 1,
                            width: Width,
                            valueField: "zahlungsziel_kz",
                            autoFetchData: false,
                            displayField: "zahlungsziel",
                            pickListProperties: {
                                showShadow: false,
                                showFilterEditor: false,
                                showHeader: false
                            },
                            pickListWidth: PickListWidth,
                            pickListFields: [{name: "zahlungsziel", width: "*"}],
                            icons: [{
                                    src: "famfam/delete.png",
                                    width: 14,
                                    height: 14,
                                    click: function () {
                                        dfBuchungenZeitraum.getField("zahlungsziel_kz").clearValue();
                                        dfBuchungenZeitraum.detailSuche(dfBuchungenZeitraum, "nein");
                                    }
                                }],
                            getPickListFilterCriteria: function () {

                                var filter = {
                                    datum: dfBuchungenZeitraum.getField("datum").getValue(),
                                    verkauf_an: dfBuchungenZeitraum.getField("verkauf_an").getValue(),
                                    prod_kz: dfBuchungenZeitraum.getField("prod_kz").getValue(),
                                    geburtstag: dfBuchungenZeitraum.getField("geburtstag").getValue(),
                                    beleg_nr: dfBuchungenZeitraum.getField("beleg_nr").getValue(),
                                    jahr: dfBuchungenZeitraum.getField("jahr").getValue(),
                                    monat: dfBuchungenZeitraum.getField("monat").getValue(),
                                    lookFor: "zahlungsziel",
                                    count: ++dfBuchungenZeitraum.count
                                };

                                return filter;
                            },
                            changed: function (form, item, value) {
                                dfBuchungenZeitraum.detailSuche(dfBuchungenZeitraum, "nein");
                            }
                        }, {
                            name: "freieSuche",
                            title: "<b>Arama</b>",
                            required: false,
                            width: 200,
                            colSpan: 1,
                            type: "text",
                            //                            keyPressFilter: "[0-9/]", 
                            keyPress: function () {
                                if (isc.Event.getKey() == "Enter") {
                                    dfBuchungenZeitraum.count++;
                                    buchungsListe.fetchData({count: dfBuchungenZeitraum.count, freieSuche: dfBuchungenZeitraum.getField("freieSuche").getValue()});
                                }
                            }, icons: [{
                                    src: "famfam/magnifier.png",
                                    width: 14,
                                    height: 14,
                                    prompt: "Belge numara, Isim, Soyisim, T.C. Kimlik No ara",
                                    hoverWidth: 100,
                                    hoverDelay: 700,
                                    click: function () {
                                        dfBuchungenZeitraum.count++;
                                        buchungsListe.fetchData({count: dfBuchungenZeitraum.count, freieSuche: dfBuchungenZeitraum.getField("freieSuche").getValue()});
                                    }
                                }]

                        }
                    ],
                    /*
                     * 
                     * @param {ID} _form
                     * @param {string} _edit
                     * @returns {void}
                     */
                    detailSuche: function (_form, _edit) {
                        var noSearch = null;
                        var _prod_kz = null;
                        var _verkauf_an = null;
                        var _beleg_nr = null;
                        var _zahlungsziel = null;
                        var _geburtstag = null;
                        var _datum = null;
                        var _jahr = null;
                        var _monat = null;

                        if (typeof (_form.getField("monat").getValue()) !== noSearch) {
                            _monat = _form.getField("monat").getValue();
                        }

                        if (typeof (_form.getField("jahr").getValue()) !== noSearch) {
                            _jahr = _form.getField("jahr").getValue();
                        }

                        if (typeof (_form.getField("prod_kz").getValue()) !== noSearch) {
                            _prod_kz = _form.getField("prod_kz").getValue();
                        }

                        if (typeof (_form.getField("prod_kz").getValue()) !== noSearch) {
                            _prod_kz = _form.getField("prod_kz").getValue();
                        }

                        if (typeof (_form.getField("verkauf_an").getValue()) !== noSearch) {
                            _verkauf_an = _form.getField("verkauf_an").getValue();
                        }
                        if (typeof (_form.getField("beleg_nr").getValue()) !== noSearch) {
                            _beleg_nr = _form.getField("beleg_nr").getValue();
                        }
                        if (typeof (_form.getField("zahlungsziel_kz").getValue()) !== noSearch) {
                            _zahlungsziel = _form.getField("zahlungsziel_kz").getValue();
                        }
                        if (typeof (_form.getField("geburtstag").getValue()) !== noSearch) {
                            _geburtstag = _form.getField("geburtstag").getValue();
                        }
                        if (typeof (_form.getField("datum").getValue()) !== noSearch) {
                            _datum = _form.getField("datum").getValue();
                        }

                        dfBuchungenZeitraum.count++;
                        buchungsListe.fetchData({count: dfBuchungenZeitraum.count, prod_kz: _prod_kz, verkauf_an: _verkauf_an,
                            beleg_nr: _beleg_nr, zahlungsziel: _zahlungsziel, geburtstag: _geburtstag, datum: _datum, jahr: _jahr, monat: _monat});

                        if (_edit != "ja") {
                            isc.Timer.setTimeout("buchungsListe.redraw()", 500);
                        }
                    }
                });

                /*
                 * ************************* ENDE BUCHUNGEN ***********************************
                 * *****************************************************************************
                 */






                /*
                 * ****************** ANFANG TOOLSTRIP BUTTONS *****************
                 * -------------------------------------------------------------
                 */

                /*
                 ***************** Add Button Hasta *************************** 
                 */
                isc.ToolStripButton.create({
                    ID: "tsbAddKunden",
                    title: "",
                    showDisabledIcon: false,
                    prompt: "Yeni bir Hasta eklemek için ekranı açar.",
                    icon: "icons/32/patient.png",
                    iconHeight: 32,
                    iconWidth: 32,
                    hoverWidth: 100,
                    hoverDelay: 700,
                    action: function () {
                        wdAddKunden.show();
                        pgbAddKunden.setHeight(16);
                        isc.Timer.setTimeout("btnResetKundeNeu.click()", 50);
                    }
                });
                /*
                 ***************** Edit Button Hasta ************************** 
                 */
                isc.ToolStripButton.create({
                    ID: "tsbKundenEdit",
                    title: "",
                    showDisabledIcon: false,
                    disabled: true,
                    icon: "icons/32/patient_edit.png",
                    iconHeight: 32,
                    iconWidth: 32,
                    prompt: "Seçilen Hasta için düzenleme ekranını açar",
                    hoverWidth: 100,
                    hoverDelay: 700,
                    action: function () {
                        if (kundenListe.getSelection().length == 1) {
                            tabHasta.selectTab(0);
                            record = kundenListe.getSelectedRecord();
                            dfEditKunden.editRecord(record);
                            wdEditKunden.show();
                            pgbEditKunden.setHeight(16);
                            isc.Timer.setTimeout("btnResetKundeEdit.click()", 50);
                        } else {
                            isc.say("Önce bir Hasta seçmelisiniz");
                        }

                    }
                });
                /*
                 ***************** Delete Button Hasta ************************** 
                 */
                isc.ToolStripButton.create({
                    ID: "tsbKundenDelete",
                    title: "",
                    showDisabledIcon: false,
                    icon: "icons/32/patient_delete.png",
                    iconHeight: 32,
                    iconWidth: 32,
                    prompt: "Seçilen Hasta'yı siler",
                    disabled: true,
                    hoverWidth: 100,
                    hoverDelay: 700,
                    action: function () {

                        if (kundenListe.getSelection().length == 1) {
                            var kunde = kundenListe.getSelectedRecord().vorname + " " + kundenListe.getSelectedRecord().name;
                            isc.ask("Gerçekten Hasta <b> " + kunde + " yı </ b> kalıcı olarak silmek istiyor musunuz?", function (value) {
                                if (value) {
                                    RPCManager.send("", function (rpcResponse, data, rpcRequest) {
                                        var _data = isc.JSON.decode(data); // Daten aus dem PHP (rpcResponse)
                                        if (_data.response.status === 0) {  // Durum 0 bedeutet Keine Hata
                                            onRefresh("kundenListe");
                                        } else { // Wenn die Validierungen Hata aufweisen dann:

                                            dfErrorFormKunden.setErrors(_data.response.errors, true);
                                            var _errors = dfErrorFormKunden.getErrors();
                                            for (var i in _errors)
                                            {
                                                isc.say("<b>Hata! </br>" + (_errors [i]) + "</b>");
                                            }

                                        }
                                    }, {// Übergabe der Parameter
                                        actionURL: "api/deleteKunden.php",
                                        httpMethod: "POST",
                                        contentType: "application/x-www-form-urlencoded",
                                        useSimpleHttp: true,
                                        params: {
                                            lfd_nr: kundenListe.getSelectedRecord().lfd_nr}
                                    }); //Ende RPC 
                                }
                            }, {title: "Hasta Silmek?"});
                        } else {
                            isc.say("Önce bir Hasta seçmelisiniz");
                        }

                    }
                });
                /*
                 ***************** Protokol Button Hasta ************************** 
                 */
                isc.ToolStripButton.create({
                    ID: "tsbKundenHist",
                    title: "",
                    count: 0,
                    showDisabledIcon: false,
                    icon: "web/32/report.png",
                    iconHeight: 32,
                    iconWidth: 32,
                    prompt: "Protokolü görmek için tıkla",
                    hoverWidth: 100,
                    hoverDelay: 700,
                    action: function () {

                        if (kundenListe.getSelection().length == 1) {
                            dfEditKunden.kundenCount++;
                            wdKundenHist.show();
                            kundenHistListeEditWD.show();
                            kundenHistListeEinzelWD.hide();
                            kundenHistListeEditWD.fetchData({tab: "kunden", lfd_nr: kundenListe.getSelectedRecord().lfd_nr, count: dfEditKunden.kundenCount});
                        } else {
                            tsbKundenHist.count++;
                            wdKundenHist.show();
                            kundenHistListeEditWD.hide();
                            kundenHistListeEinzelWD.show();
                            kundenHistListeEinzelWD.fetchData({tab: "kunden", count: tsbKundenHist.count});
                        }
                    }
                });
                /*
                 ***************** Baskı Button Hasta ************************** 
                 */
                isc.ToolStripButton.create({
                    ID: "tsbKundenPrint",
                    title: "",
                    count: 0,
                    showDisabledIcon: false,
                    icon: "web/32/printer.png",
                    iconHeight: 32,
                    iconWidth: 32,
                    prompt: "Hasta listesinin baskı önizlemesini açar",
                    hoverWidth: 100,
                    hoverDelay: 700,
                    action: function () {
                        isc.Canvas.showPrintPreview(kundenListe);
                    }
                });
                /*
                 ***************** CSV-Export Button Hasta ************************** 
                 */
                isc.ToolStripButton.create({
                    ID: "tsbKundenCSV",
                    title: "",
                    count: 0,
                    showDisabledIcon: false,
                    icon: "famfam/excel.png",
                    iconHeight: 32,
                    iconWidth: 32,
                    prompt: "Listeyi bir CSV Dosyasına aktarır.",
                    hoverWidth: 100,
                    hoverDelay: 700,
                    action: function () {
                        window.open('/' + appFolder + '/api/csv_export_kunden.php', '_self', false);
                    }
                });
                /*
                 ***************** Refresh Button Hasta ************************ 
                 */

                isc.ToolStripButton.create({
                    ID: "tsbKundenRefresh",
                    title: "",
                    showDisabledIcon: false,
                    disabled: false,
                    icon: "web/32/refresh.png",
                    iconHeight: 32,
                    iconWidth: 32,
                    prompt: "Hasta listesini güncelleştirir",
                    hoverWidth: 100,
                    hoverDelay: 700,
                    action: function () {
                        onRefresh("kundenListe");
                    }
                });

                /*
                 ***************** Error-Form Hasta ************************** 
                 */
                isc.DynamicForm.create({
                    ID: "dfErrorFormKunden",
                    width: 1,
                    height: 1,
                    titleOrientation: "left",
                    fields: [{name: "errors",
                            width: 1,
                            type: "hidden"}]});



                /*
                 ***************** Add Button Tedavi *************************
                 */
                isc.ToolStripButton.create({
                    ID: "tsbAddProdukte",
                    title: "",
                    showDisabledIcon: false,
                    icon: "icons/32/doctor.png",
                    iconHeight: 32,
                    iconWidth: 32,
                    prompt: "Yeni bir tedavi eklemek için ekranı açar",
                    hoverWidth: 100,
                    hoverDelay: 700,
                    action: function () {

                        wdAddProdukte.show();
                        pgbAddProdukte.setHeight(16);
                        isc.Timer.setTimeout("btnResetProduktNeu.click()", 50);

                    }
                });
                /*
                 ***************** Edit Button Tedavi ************************** 
                 */
                isc.ToolStripButton.create({
                    ID: "tsbProdukteEdit",
                    title: "",
                    showDisabledIcon: false,
                    disabled: true,
                    icon: "icons/32/doctor_edit.png",
                    prompt: "Seçilen Tedavi için düzenleme ekranını açar",
                    iconHeight: 32,
                    iconWidth: 32,
                    hoverWidth: 100,
                    hoverDelay: 700,
                    action: function () {

                        if (produktListe.getSelection().length == 1) {
                            record = produktListe.getSelectedRecord();
                            dfEditProdukte.editRecord(record);
                            wdEditProdukte.show();
                            pgbEditProdukte.setHeight(16);
                            produktKz = dfEditProdukte.getField("prod_kz").getValue();
                            //                                tabProdukteEdit.selectTab(0);
                        } else {
                            isc.say("Önce Tedavi'yi seçmelisiniz");
                        }

                    }
                });
                /*
                 ***************** Delete Button Tedavi ************************** 
                 */
                isc.ToolStripButton.create({
                    ID: "tsbProdukteDelete",
                    title: "",
                    showDisabledIcon: false,
                    icon: "icons/32/doctor_delete.png",
                    iconHeight: 32,
                    iconWidth: 32,
                    prompt: "Seçilen Tedaviyi siler",
                    disabled: true,
                    hoverWidth: 100,
                    hoverDelay: 700,
                    action: function () {


                        if (produktListe.getSelection().length == 1) {
                            var produkt = produktListe.getSelectedRecord().bezeichnung;
                            isc.ask("Tedavi  <b>" + produkt + "'yı</b> gerçekten kalıcı olarak silmek istiyor musunuz?", function (value) {
                                if (value) {
                                    RPCManager.send("", function (rpcResponse, data, rpcRequest) {
                                        var _data = isc.JSON.decode(data); // Daten aus dem PHP (rpcResponse)
                                        if (_data.response.status === 0) {  // Durum 0 bedeutet Keine Hata
                                            onRefresh("produktListe");
                                        } else { // Wenn die Validierungen Hata aufweisen dann:

                                            dfErrorFormProdukte.setErrors(_data.response.errors, true);
                                            var _errors = dfErrorFormProdukte.getErrors();
                                            for (var i in _errors)
                                            {
                                                isc.say("<b>Hata! </br>" + (_errors [i]) + "</b>");
                                            }

                                        }
                                    }, {// Übergabe der Parameter
                                        actionURL: "api/deleteProdukte.php",
                                        httpMethod: "POST",
                                        contentType: "application/x-www-form-urlencoded",
                                        useSimpleHttp: true,
                                        params: {
                                            prod_kz: produktListe.getSelectedRecord().prod_kz}
                                    }); //Ende RPC 
                                }
                            }, {title: "Tedavi Silmek?"});
                        } else {
                            isc.say("Önce Tedavi'yi seçmelisiniz");
                        }

                    }
                });
                /*
                 ***************** Protokol Button Tedavi ************************** 
                 */
                isc.ToolStripButton.create({
                    ID: "tsbProdukteHist",
                    title: "",
                    count: 0,
                    showDisabledIcon: false,
                    icon: "web/32/report.png",
                    prompt: "Protokolü görmek için tıkla",
                    iconHeight: 32,
                    iconWidth: 32,
                    hoverWidth: 100,
                    hoverDelay: 700,
                    action: function () {


                        if (produktListe.getSelection().length == 1) {

                            wdProduktHist.show();
                            ProduktHistListeEditWD.show();
                            produktHistListeEinzelWD.hide();
                            dfEditProdukte.prodCount++;
                            ProduktHistListeEditWD.fetchData({tab: "produkte", prod_kz: produktListe.getSelectedRecord().prod_kz, count: dfEditProdukte.prodCount});
                        } else {
                            dfEditProdukte.prodCount++;
                            wdProduktHist.show();
                            ProduktHistListeEditWD.hide();
                            produktHistListeEinzelWD.show();
                            produktHistListeEinzelWD.fetchData({tab: "produkte", count: dfEditProdukte.prodCount});
                        }
                    }
                });
                /*
                 ***************** Baskı Button Tedavi ************************** 
                 */
                isc.ToolStripButton.create({
                    ID: "tsbProduktePrint",
                    title: "",
                    count: 0,
                    iconHeight: 32,
                    iconWidth: 32,
                    showDisabledIcon: false,
                    icon: "web/32/printer.png",
                    prompt: "Tedavi listesinin yazdırma önizlemesini açar",
                    hoverWidth: 100,
                    hoverDelay: 700,
                    action: function () {
                        isc.Canvas.showPrintPreview(produktListe);
                    }
                });
                /*
                 ***************** CSV-Export Button Tedavi ************************** 
                 */
                isc.ToolStripButton.create({
                    ID: "tsbProdukteCSV",
                    title: "",
                    count: 0,
                    showDisabledIcon: false,
                    icon: "famfam/excel.png",
                    iconHeight: 32,
                    iconWidth: 32,
                    prompt: "Listeyi bir CSV Dosyasına aktarır.",
                    hoverWidth: 100,
                    hoverDelay: 700,
                    action: function () {
                        window.open('https://' + domain + '/' + appFolder + '/api/csv_export_produkte.php', '_self', false);
                    }
                });
                /*
                 ***************** Refresh Button Tedavi ************************ 
                 */

                isc.ToolStripButton.create({
                    ID: "tsbProdukteRefresh",
                    title: "",
                    showDisabledIcon: false,
                    disabled: false,
                    icon: "web/32/refresh.png",
                    prompt: "Tedavi listesini günceller",
                    iconHeight: 32,
                    iconWidth: 32,
                    hoverWidth: 100,
                    hoverDelay: 700,
                    action: function () {
                        onRefresh("produktListe");
                    }
                });
                /*
                 ***************** Error-Form Tedavi ************************** 
                 */
                isc.DynamicForm.create({
                    ID: "dfErrorFormProdukte",
                    width: 1,
                    height: 1,
                    titleOrientation: "left",
                    fields: [{name: "errors",
                            width: 1,
                            type: "hidden"}]});



                /*
                 ***************** Add Button Abrechnung ***********************
                 */
                isc.ToolStripButton.create({
                    ID: "tsbAddAbrechnung",
                    title: "",
                    showDisabledIcon: false,
                    icon: "web/32/calendar_add.png",
                    prompt: "Yeni bir randevu ekle",
                    iconHeight: 32,
                    iconWidth: 32,
                    hoverWidth: 100,
                    hoverDelay: 700,
                    action: function () {

                        wdAddAbrechnung.show();
                        pgbAddAbrechnung.setHeight(16);
                        pgbAddAbrechnung.setTitle("");
                        pgbAddAbrechnung.setPercentDone(0);
                        isc.Timer.setTimeout("btnResetAbrechnungNeu.click()", 50);
                        dfAddAbrechnung.getField("kunden_nr").setDisabled(false);

                        //                        if (abrechnungsTree.getSelection().length == 1) {
                        //                            dfAddAbrechnung.getField("beleg_nr").setValue(abrechnungsTree.getSelectedRecord().beleg_nr);
                        //                            dfAddAbrechnung.getField("beleg_nr").changed(dfAddAbrechnung);
                        //                        }else{
                        isc.Timer.setTimeout("dfAddAbrechnung.belegNrBerechnen(dfAddAbrechnung)", 150);
                        isc.Timer.setTimeout("getNextTermin(dfAddAbrechnung, dfAddAbrechnung.getField('datum').getValue())", 150);
                        //                            ;}

                    }
                });


                /*
                 ***************** Edit Button Abrechnung (Abr.)***************
                 */
                isc.ToolStripButton.create({
                    ID: "tsbAbrechnungGesamtEdit",
                    title: "",
                    count: 0,
                    showDisabledIcon: false,
                    icon: "web/32/calendar_edit.png",
                    prompt: "Randevuyu düzenle",
                    iconHeight: 32,
                    iconWidth: 32,
                    disabled: false,
                    hoverWidth: 100,
                    hoverDelay: 700,
                    action: function () {
                        if (abrechnungsTree.getSelection().length == 1) {
                            wdEditAbrechnung.show();
                            var record = abrechnungsTree.getSelectedRecord();
                            dfEditAbrechnung.editRecord(record);
                            pgbEditAbrechnung.setHeight(16);
                            isc.Timer.setTimeout("btnResetAbrechnungEdit2.click()", 100);
                            abrechnungsTree.count2++;
                            if (abrechnungsTree.getTotalRows() > 0) {
                                abrechnungsListeEdit.fetchData({count: abrechnungsTree.count2, beleg_nr: abrechnungsTree.getSelectedRecord().beleg_nr});
                            }
                            //                            isc.Timer.setTimeout("abrechnungsListe.abrechnungsSummenFunction()",100);
                        } else {
                            isc.say("Önce bir Tedavi secin!");
                        }
                    }
                });


                /*
                 ***************** Delete Button Abrechnung (Abr.)***************
                 */
                isc.ToolStripButton.create({
                    ID: "tsbAbrechnungDelete",
                    title: "",
                    count: 0,
                    showDisabledIcon: false,
                    icon: "web/32/calendar_delete.png",
                    iconHeight: 32,
                    iconWidth: 32,
                    prompt: "Seçilen randevuyu tamamen siler",
                    disabled: false,
                    hoverWidth: 100,
                    hoverDelay: 700,
                    action: function () {
                        if (abrechnungsTree.getTotalRows() > 1) {
                            belegNr_ = abrechnungsTree.getSelectedRecord().beleg_nr;
                        } else {
                            belegNr_ = null;
                        }
                        laufendeNr = null;
                        if (abrechnungsTree.getSelection().length == 1) {
                            isc.ask("<b> " + belegNr_ + " </b> nolu Faturau kalıcı olarak silmek istiyor musunuz?", function (value) {
                                if (value) {
                                    var totalRows = abrechnungsTree.getTotalRows();
                                    RPCManager.send("", function (rpcResponse, data, rpcRequest) {
                                        var _data = isc.JSON.decode(data); // Daten aus dem PHP (rpcResponse)
                                        if (_data.response.status === 0) {  // Durum 0 bedeutet Keine Hata
                                            welcomeSite.setContentsURL(calendar_);
                                            //                                onRefreshAbrechnung("abrechnungsListeEdit", abrechnungsListeEdit.getSelectedRecord().beleg_nr, tsbAbrechnungDelete.count);
                                            //                                isc.Timer.setTimeout("abrechnungsListe.abrechnungsSummenFunction()",500);
                                            if (totalRows == 3) {
                                                abrechnungsListeEdit.setData([]);
                                                dfEditAbrechnung.clearValues();
                                                wdEditAbrechnung.hide();
                                            } else {
                                                isc.Timer.setTimeout("abrechnungsTree.selectFirstRecord()", 200);
                                            }
                                            abrechnungsTree.invalidateCache();
                                            //                                isc.Timer.setTimeout("btnSpeichernAbrechnungEdit2.findAbrechnung()", 300);
                                        } else { // Wenn die Validierungen Hata aufweisen dann:

                                            dfErrorFormAbrechnung.setErrors(_data.response.errors, true);
                                            var _errors = dfErrorFormAbrechnung.getErrors();
                                            for (var i in _errors)
                                            {
                                                isc.say("<b>Hata! </br>" + (_errors [i]) + "</b>");
                                            }

                                        }
                                    }, {// Übergabe der Parameter
                                        actionURL: "api/deleteAbrechnung.php",
                                        httpMethod: "POST",
                                        contentType: "application/x-www-form-urlencoded",
                                        useSimpleHttp: true,
                                        params: {
                                            beleg_nr: belegNr_,
                                            verkauf_an: abrechnungsTree.getSelectedRecord().verkauf_an,
                                            kunden_name: abrechnungsTree.getSelectedRecord().name
                                        }
                                    }); //Ende RPC 
                                }
                            }, {title: "Fatura Silmek?"});
                        } else {
                            isc.say("Önce bir fatura seçmelisiniz");
                        }

                    }
                });

                /*
                 ***************** Buchungs Button Abrechnung *******************
                 */
                isc.ToolStripButton.create({
                    ID: "tsbBookAbrechnung",
                    title: "",
                    showDisabledIcon: false,
                    disabled: true,
                    icon: "web/32/accept.png",
                    iconHeight: 32,
                    iconWidth: 32,
                    prompt: "Randevuyu başarılı olarak işaretleyin ve Fatura oluşturun.",
                    hoverWidth: 200,
                    hoverDelay: 700,
                    action: function () {
                        var Beleg_nr = abrechnungsTree.getSelectedRecord().beleg_nr;
                        if (abrechnungsTree.getSelection().length == 1) {
                            isc.ask("<b>" + Beleg_nr + "</b> nolu Fatura'yı kaydetmek istiyormusunuz ? Kaydettikten sonra herhangi bir değişiklik yapılamaz..", function (value) {
                                if (value) {
                                    var totalRows = abrechnungsTree.getTotalRows();
                                    RPCManager.send("", function (rpcResponse, data, rpcRequest) {
                                        var _data = isc.JSON.decode(data); // Daten aus dem PHP (rpcResponse)
                                        if (_data.response.status === 0) {  // Durum 0 bedeutet Keine Hata
                                            isc.say(Beleg_nr + " nolu Randevu başarıyla kaydedildi.");
                                            welcomeSite.setContentsURL(calendar_);
                                            if (totalRows == 3) {
                                                abrechnungsListeEdit.setData([]);
                                                dfEditAbrechnung.clearValues();
                                            } else {
                                                isc.Timer.setTimeout("abrechnungsTree.selectFirstRecord()", 200);
                                            }
                                            abrechnungsTree.invalidateCache();
                                            isc.Timer.setTimeout("onRefresh('buchungsListe')", 1000);



                                            /*
                                             * ******* Erstellt die Rechnung und legt sie in den Abrechnungsordner ab *********
                                             */
                                            RPCManager.send("", function (rpcResponse, data, rpcRequest) {

                                            }, {// Übergabe der Parameter
                                                actionURL: "api/abrechnung_pdf.php",
                                                httpMethod: "POST",
                                                contentType: "application/x-www-form-urlencoded",
                                                useSimpleHttp: true,
                                                params: {
                                                    beleg_nr: Beleg_nr,
                                                    verkauf_an: abrechnungsTree.getSelectedRecord().verkauf_an,
                                                    datum: abrechnungsTree.getSelectedRecord().datum.toEuropeanShortDate(),
                                                    art: "buchen"}
                                            }); //Ende RPC 

                                        } else { // Wenn die Validierungen Hata aufweisen dann:

                                            dfErrorFormAbrechnung.setErrors(_data.response.errors, true);
                                            var _errors = dfErrorFormAbrechnung.getErrors();
                                            for (var i in _errors)
                                            {
                                                isc.say("<b>Hata! </br>" + (_errors [i]) + "</b>");
                                            }

                                        }
                                    }, {// Übergabe der Parameter
                                        actionURL: "api/abrechnungBuchen.php",
                                        httpMethod: "POST",
                                        contentType: "application/x-www-form-urlencoded",
                                        useSimpleHttp: true,
                                        params: {
                                            beleg_nr: abrechnungsTree.getSelectedRecord().beleg_nr,
                                            kunden_nr: abrechnungsTree.getSelectedRecord().verkauf_an,
                                            kunden_name: abrechnungsTree.getSelectedRecord().name,
                                            datum: abrechnungsTree.getSelectedRecord().datum
                                        }
                                    }); //Ende RPC    
                                }

                            }, {title: " Randevu kaydetmek?"});
                        } else {
                            isc.say("Önce bir fatura seçmelisiniz");
                        }

                    }
                });

                /*
                 ***************** PDF Button Abrechnung *********************** 
                 */
                isc.ToolStripButton.create({
                    ID: "tsbPDFAbrechnung",
                    title: "",
                    disabled: true,
                    showDisabledIcon: false,
                    icon: "icons/32/document.png",
                    iconHeight: 32,
                    iconWidth: 32,
                    prompt: "Fatura önizlemesi",
                    hoverWidth: 100,
                    hoverDelay: 700,
                    action: function () {

                        if (abrechnungsTree.getSelection().length == 1) {
                            var belegNr = abrechnungsTree.getSelectedRecord().beleg_nr;
                            var kundenNr = abrechnungsTree.getSelectedRecord().verkauf_an;
                            var datum = abrechnungsTree.getSelectedRecord().datum.toEuropeanShortDate();

                            window.open('http://' + domain + '/' + appFolder + '/api/abrechnung_pdf.php?beleg_nr=' + belegNr + '&verkauf_an=' + kundenNr + '&datum=' + datum + '&art=vorschau', +'" target="_blank"');

                        } else {
                            isc.say("Önce bir fatura seçmelisiniz");
                        }
                    }
                });
                /*
                 ***************** Protokol Button Abrechnung ************************** 
                 */
                isc.ToolStripButton.create({
                    ID: "tsbAbrechnungHist",
                    title: "",
                    count: 0,
                    showDisabledIcon: false,
                    icon: "web/32/report.png",
                    iconHeight: 32,
                    iconWidth: 32,
                    prompt: "Protokolü görmek için tıkla",
                    hoverWidth: 100,
                    hoverDelay: 700,
                    action: function () {


                        if (abrechnungsListe.getSelection().length == 1) {
                            wdAbrechnungHist.show();
                            abrechnungHistListeEinzelWD.show();
                            abrechnungHistListeGesamtWD.hide();
                            tsbAbrechnungHist.count++;
                            abrechnungHistListeEinzelWD.fetchData({lfd_nr: abrechnungsListe.getSelectedRecord().lfd_nr, count: tsbAbrechnungHist.count});
                        } else {
                            tsbAbrechnungHist.count++;
                            wdAbrechnungHist.show();
                            abrechnungHistListeEinzelWD.hide();
                            abrechnungHistListeGesamtWD.show();
                            abrechnungHistListeGesamtWD.fetchData({count: tsbAbrechnungHist.count});
                        }
                    }
                });
                /*
                 ***************** Baskı Button Abrechnung ************************** 
                 */
                isc.ToolStripButton.create({
                    ID: "tsbAbrechnungPrint",
                    title: "",
                    count: 0,
                    showDisabledIcon: false,
                    icon: "web/32/printer.png",
                    iconHeight: 32,
                    iconWidth: 32,
                    prompt: "Faturalandırma listesinin yazdırma önizlemesini açar",
                    hoverWidth: 100,
                    hoverDelay: 700,
                    action: function () {
                        isc.Canvas.showPrintPreview(abrechnungsListeEdit);
                    }
                });
                /*
                 ***************** CSV-Export Button Abrechnung ************************** 
                 */
                isc.ToolStripButton.create({
                    ID: "tsbAbrechnungCSV",
                    title: "",
                    count: 0,
                    iconHeight: 32,
                    iconWidth: 32,
                    showDisabledIcon: false,
                    icon: "famfam/excel.png",
                    prompt: "Listeyi bir CSV Dosyasına aktarır.",
                    hoverWidth: 100,
                    hoverDelay: 700,
                    action: function () {
                        window.open('https://' + domain + '/' + appFolder + '/api/csv_export_abrechnung.php', '_self', false);
                    }
                });
                /*
                 ***************** Refresh Button Abrechnungen ************************ 
                 */

                isc.ToolStripButton.create({
                    ID: "tsbAbrechnungRefresh",
                    title: "",
                    count: 0,
                    iconHeight: 32,
                    iconWidth: 32,
                    showDisabledIcon: false,
                    disabled: false,
                    icon: "web/32/refresh.png",
                    prompt: "Fatura listesini günceller",
                    hoverWidth: 100,
                    hoverDelay: 700,
                    action: function () {
                        //                        tsbAbrechnungRefresh.count++;
                        //                                onRefreshAbrechnung("abrechnungsListeEdit", abrechnungsTree.getSelectedRecord().beleg_nr, tsbAbrechnungRefresh.count);
                        //                                isc.Timer.setTimeout("abrechnungsListe.abrechnungsSummenFunction()",1000);
                        abrechnungsTree.invalidateCache();
                        welcomeSite.setContentsURL(calendar_);
                    }
                });

                /*
                 ***************** Suchen Button Abrechnung ************************** 
                 */
                isc.ToolStripButton.create({
                    ID: "tsbAbrechnungSuche",
                    title: "",
                    count: 0,
                    showDisabledIcon: false,
                    icon: "famfam/find.png",
                    prompt: "Ayrıntılı arama penceresini açar",
                    hoverWidth: 100,
                    hoverDelay: 700,
                    action: function () {
                        AbrechnungSuchWindow.show();
                    }
                });
                /*
                 ***************** Error-Form Abrechnung ************************** 
                 */
                isc.DynamicForm.create({
                    ID: "dfErrorFormAbrechnung",
                    width: 1,
                    height: 1,
                    titleOrientation: "left",
                    fields: [{name: "errors",
                            width: 1,
                            type: "hidden"}]});





                /*
                 ***************** Edit Button Kullanıcı **************************** 
                 */
                isc.ToolStripButton.create({
                    ID: "tsbUserEdit",
                    title: "",
                    showDisabledIcon: false,
                    disabled: true,
                    icon: "web/32/user_edit.png",
                    iconHeight: 32,
                    iconWidth: 32,
                    prompt: "Seçilen Kullanıcı için düzenleme ekranını açar",
                    hoverWidth: 100,
                    hoverDelay: 700,
                    action: function () {
                        if (userListe.getSelection().length == 1) {
                            record = userListe.getSelectedRecord();
                            dfEditUser.editRecord(record);
                            wdEditUser.show();
                            pgbEditUser.setHeight(16);
                            isc.Timer.setTimeout("btnResetEditUser.click()", 50);
                        } else {
                            isc.say("Önce bir kullanıcı seçmelisiniz");
                        }

                    }
                });
                /*
                 ***************** New Kullanıcı Button **************************** 
                 */
                isc.ToolStripButton.create({
                    ID: "tsbUserAdd",
                    title: "",
                    showDisabledIcon: false,
                    disabled: false,
                    icon: "web/32/user_add.png",
                    iconHeight: 32,
                    iconWidth: 32,
                    prompt: "Yeni kullanici oluştur",
                    hoverWidth: 100,
                    hoverDelay: 700,
                    action: function () {
                        window.open('https://' + domain + '/' + appFolder + '/register.php', +'" target="_blank"');
                    }
                });

                /*
                 ***************** Refresh Button Kullanıcı ************************ 
                 */
                isc.ToolStripButton.create({
                    ID: "tsbUserRefresh",
                    title: "",
                    showDisabledIcon: false,
                    disabled: false,
                    icon: "web/32/refresh.png",
                    iconHeight: 32,
                    iconWidth: 32,
                    prompt: "Kullanıcı listesini güncelleştirir",
                    hoverWidth: 100,
                    hoverDelay: 700,
                    action: function () {
                        onRefresh("userListe");
                    }
                });
                /*
                 ***************** Error-Form Kullanıcı ***************************** 
                 */
                isc.DynamicForm.create({
                    ID: "dfErrorFormUser",
                    width: 1,
                    height: 1,
                    titleOrientation: "left",
                    fields: [{name: "errors",
                            width: 1,
                            type: "hidden"}]});




                /*
                 ***************** Error-Form Einzahlungen ***************************** 
                 */
                isc.DynamicForm.create({
                    ID: "dfErrorFormEinzahlungen",
                    width: 1,
                    height: 1,
                    titleOrientation: "left",
                    fields: [{name: "errors",
                            width: 1,
                            type: "hidden"}]});

                /*
                 * ******************* ENDE TOOLSTRIP BUTTONS ******************
                 * -------------------------------------------------------------
                 */

                /*
                 * ******************* ANFANG MENU & RIBBON ********************
                 * -------------------------------------------------------------
                 */



                //----------Anfang Menu Hasta---------------------------------- 
                isc.Menu.create({
                    ID: "menuKunden",
                    autoDraw: false,
                    showShadow: true,
                    shadowDepth: 10,
                    data: [
                        {title: "Yeni hastayı oluşturmak", icon: "famfam/user_add.png", click: function () {
                                tsbAddKunden.action();
                            }},
                        {title: "Hastayı düzenlemek", icon: "famfam/vcard_edit.png", click: function () {
                                tsbKundenEdit.action();
                            }},
                        {title: "Hastayı sil", icon: "famfam/user_delete.png", click: function () {
                                tsbKundenDelete.action();
                            }},
                        {isSeparator: true},
                        {title: "Randevu oluştur", icon: "famfam/calendar_add.png", click: function () {

                                var kunden_nr = kundenListe.getSelectedRecord().lfd_nr;
                                tabHauptFensterKlinik.selectTab(0);
                                tsbAddAbrechnung.action();
                                dfAddAbrechnung.getField("kunden_nr").setValue(kunden_nr);

                            }},
                        {isSeparator: true},
                        {title: "Dışa aktar...", icon: "icons/16/export1.png", submenu: [
                                {title: "CSV / EXCEL", icon: "famfam/excel.png", click: function () {
                                        tsbKundenCSV.action();
                                    }}]},
                        {isSeparator: true},
                        {title: "Baskı", enabled: true, icon: "icons/16/printer3.png", click: "isc.Canvas.showPrintPreview(kundenListe)"},
                        {isSeparator: true},
                        {title: "Protokolü görüntüle", enabled: true, icon: "famfam/report.png", click: "tsbKundenHist.action()"},
                        {isSeparator: true},
                        {title: "Edit Hilites", icon: "famfam/color_swatch.png", click: function () {
                                kundenListe.editHilites();
                            }},
                        {isSeparator: true},
                        {title: "Hasta Listesini güncelleştirme", icon: "famfam/arrow_refresh.png", click: function () {
                                tsbKundenRefresh.action();
                            }}
                    ]
                });


                var menuButton = isc.MenuButton.create({
                    ID: "menuButtonKunden",
                    autoDraw: false,
                    title: "Seçenekler",
                    width: 100,
                    menu: menuKunden
                });
                //----------Ende Menu Hasta----------------------------------

                //----------Anfang Menu Tedavi ------------------------------- 
                isc.Menu.create({
                    ID: "menuProdukte",
                    autoDraw: false,
                    showShadow: true,
                    shadowDepth: 10,
                    data: [
                        {title: "Yeni Tedavi oluştur", icon: "icons/32/doctor.png", click: function () {
                                tsbAddProdukte.action();
                            }},
                        {title: "Tedavi düzenlemek", icon: "icons/32/doctor_edit.png", click: function () {
                                tsbProdukteEdit.action();
                            }},
                        {title: "Tedavi silmek", icon: "icons/32/doctor_delete.png", click: function () {
                                tsbProdukteDelete.action();
                            }},
                        {isSeparator: true},
                        {title: "Dışa aktar ...", icon: "icons/16/export1.png", submenu: [
                                {title: "CSV / EXCEL", icon: "famfam/excel.png", click: function () {
                                        tsbProdukteCSV.action();
                                    }}]},
                        {isSeparator: true},
                        {title: "Baskı", enabled: true, icon: "icons/16/printer3.png", click: "isc.Canvas.showPrintPreview(produktListe)"},
                        {isSeparator: true},
                        {title: "Protokolü görüntüle", enabled: true, icon: "famfam/report.png", click: "tsbProdukteHist.action()"},
                        {isSeparator: true},
                        {title: "Edit Hilites", icon: "famfam/color_swatch.png", click: function () {
                                produktListe.editHilites();
                            }},
                        {isSeparator: true},
                        {title: "Tedavi listesini güncelle", icon: "web/32/refresh.png", click: function () {
                                tsbProdukteRefresh.action();
                            }}
                    ]
                });


                var menuButton = isc.MenuButton.create({
                    ID: "menuButtonProdukte",
                    autoDraw: false,
                    title: "Seçenekler",
                    width: 100,
                    menu: menuProdukte
                });
                //----------Ende Menu Tedavi---------------------------------
                //
                //
                //----------Anfang Menu Kullanıcı ----------------------------------
                isc.Menu.create({
                    ID: "menuUser",
                    autoDraw: false,
                    showShadow: true,
                    shadowDepth: 10,
                    data: [
                        {title: "Kullanıcı düzenlemek", icon: "famfam/user_edit.png", click: function () {
                                tsbUserEdit.action();
                            }},
                        {title: "Kullanıcı listesini güncelle", icon: "famfam/arrow_refresh.png", click: function () {
                                tsbUserRefresh.action();
                            }}
                    ]
                });


                var menuButton = isc.MenuButton.create({
                    ID: "menuButtonUser",
                    autoDraw: false,
                    title: "Seçenekler",
                    width: 100,
                    menu: menuUser
                });
                //----------Ende Menu Kullanıcı ----------------------------------

                //----------Anfang Menu Abrechnung ------------------------------- 
                isc.Menu.create({
                    ID: "menuAbrechnung",
                    autoDraw: false,
                    showShadow: true,
                    shadowDepth: 10,
                    data: [{title: "Faturayi onayla", icon: "famfam/accept.png", click: function () {
                                tsbBookAbrechnung.action();
                            }},
                        {title: "Yeni Fatura oluştur", icon: "famfam/invoice.png", click: function () {
                                tsbAddAbrechnung.action();
                            }},
                        {title: "Faturayı düzenle", icon: "famfam/pencil.png", click: function () {
                                tsbAbrechnungGesamtEdit.action();
                            }},
                        {title: "Faturayı sil", icon: "famfam/invoice_delete.png", click: function () {
                                tsbAbrechnungDelete.action();
                            }},
                        {isSeparator: true},
                        {title: "Dışa aktar ...", icon: "icons/16/export1.png", submenu: [
                                {title: "CSV / EXCEL", icon: "famfam/excel.png", click: function () {
                                        tsbAbrechnungCSV.action();
                                    }}, {title: "PDF", icon: "famfam/pdf.png", click: function () {
                                        tsbPDFAbrechnung.action();
                                    }}]},
                        //                        {isSeparator: true},
                        //                        {title: "Baskı", enabled: true, icon: "icons/16/printer3.png", click: "isc.Canvas.showPrintPreview(abrechnungsListe)"},
                        {isSeparator: true},
                        {title: "Protokolü görüntüle", enabled: true, icon: "famfam/report.png", click: "tsbAbrechnungHist.action()"},
                        {isSeparator: true},
                        {title: "Tedavi listesini güncelle", icon: "famfam/arrow_refresh.png", click: function () {
                                tsbAbrechnungRefresh.action();
                            }}
                    ]
                });

                isc.Menu.create({
                    ID: "menuEditAbrechnung",
                    autoDraw: false,
                    showShadow: true,
                    shadowDepth: 10,
                    data: [
                        {title: "Yeni tedavi oluştur", icon: "famfam/pill_add.png", click: function () {
                                tsbAddPosition.action();
                            }},
                        {title: "Tedavi düzenle", icon: "famfam/pencil.png", click: function () {
                                tsbAbrechnungEdit.action();
                            }},
                        {title: "Tedaviyi sil", icon: "famfam/pill_delete.png", click: function () {
                                tsbPositionDelete.action();
                            }},
                        {isSeparator: true},
                        {title: "Dışa aktar ...", icon: "icons/16/export1.png", submenu: [
                                {title: "CSV / EXCEL", icon: "famfam/excel.png", click: function () {
                                        tsbAbrechnungCSV.action();
                                    }}, {title: "PDF", icon: "famfam/pdf.png", click: function () {
                                        tsbPDFAbrechnung.action();
                                    }}]},
                        //                        {isSeparator: true},
                        //                        {title: "Baskı", enabled: true, icon: "icons/16/printer3.png", click: "isc.Canvas.showPrintPreview(abrechnungsListe)"},
                        {isSeparator: true},
                        {title: "Protokolü görüntüle", enabled: true, icon: "famfam/report.png", click: "tsbAbrechnungHist.action()"},
                        {isSeparator: true},
                        {title: "Tedavi listesini güncelle", icon: "famfam/arrow_refresh.png", click: function () {
                                tsbAbrechnungRefresh.count++;
                                onRefreshAbrechnung("abrechnungsListeEdit", abrechnungsTree.getSelectedRecord().beleg_nr, tsbAbrechnungRefresh.count);
                            }}
                    ]
                });


                var menuButton = isc.MenuButton.create({
                    ID: "menuButtonAbrechnung",
                    autoDraw: false,
                    title: "Seçenekler",
                    width: 100,
                    menu: menuEditAbrechnung
                });
                //----------Ende Menu Abrechnung -------------------------------

                /*
                 * ******************* ENDE MENU & RIBBON **********************
                 * -------------------------------------------------------------
                 */


                /*
                 * ************************ANFANG Yedekleme ****************************************************************************
                 * ==================================================================================================================
                 * ******************************************************************************************************************
                 */

                isc.DataSource.create({
                    ID: "backupDataDS",
                    allowAdvancedCriteria: true,
                    // serverType:"sql",
                    dataFormat: "json",
                    operationBindings: [// =>> zum Einbinden der verschiedenen php-Scripte
                        {operationType: "fetch",
                            dataURL: "api/backupDataDS.php"
                        }
                    ],
                    //  testData: IDData,
                    //   clientOnly: true,
                    //dataURL: "genreDS.php",
                    titleField: "text",
                    fields: [{
                            name: "dateiname",
                            type: "text"
                        }]
                });


                isc.ToolStripButton.create({
                    icon: "icons/new/save.png",
                    ID: "tsbSaveDB",
                    iconWidth: 32,
                    iconHeight: 32,
                    count: 0,
                    showDisabledIcon: false,
                    prompt: "Erstellt ein Yedekleme von den aktuellen Daten",
                    click: function () {
                        RPCManager.send("", function (rpcResponse, data, rpcRequest) {
                            var _data = isc.JSON.decode(data); // Daten aus dem PHP (rpcResponse)
                            var rueckmeldung = _data.response.data["rueckmeldung"];
                            var _errors = _data.response.errors;
                            if (_data.response.status === 0) {  // Durum 0 bedeutet Keine Hata

                                isc.say("Verileriniz başarıyla kaydedildi ve Backups klasörinde " + rueckmeldung + " dosya adı altında kaydedildi.");

                            }


                            // Wenn Dosya schon existiert
                            else if (_data.response.status === -66) {
                                isc.ask("Bugünün Tarihiyle bir yedek var zaten. Üzerine yazılsınmı?", function (value) {
                                    if (value) {

                                        RPCManager.send("", function (rpcResponse, data, rpcRequest) {
                                            var _data = isc.JSON.decode(data); // Daten aus dem PHP (rpcResponse)
                                            var rueckmeldung = _data.response.data["rueckmeldung"];
                                            var _errors = _data.response.errors;
                                            if (_data.response.status === 0) {  // Durum 0 bedeutet Keine Hata

                                                isc.say("Yedekleme başarıyla oluşturuldu ve " + rueckmeldung + " kaydedildi.");
                                            } else { // Wenn die Validierungen Hata aufweisen dann:

                                                isc.say("Verileriniz kaydedilemedi!</br></br>" + _errors);
                                            }
                                        }, {// Übergabe der Parameter
                                            actionURL: "api/backup_ueber.php",
                                            httpMethod: "POST",
                                            contentType: "application/x-www-form-urlencoded",
                                            useSimpleHttp: true
                                        }); //Ende RPC Dosya schon existiert
                                    } else { // Dosya soll anderen namen erhalten
                                        wdBackup.show();
                                    }
                                });
                            } else { // Wenn die Validierungen Hata aufweisen dann:

                                isc.say("Verileriniz kaydedilemedi!</br></br>" + _errors);
                            }
                        }, {// Übergabe der Parameter
                            actionURL: "api/backup.php",
                            httpMethod: "POST",
                            contentType: "application/x-www-form-urlencoded",
                            useSimpleHttp: true
                        }); //Ende RPC
                    }}),
                        isc.LayoutSpacer.create({
                            width: 20
                        });


                isc.ToolStripButton.create({
                    icon: "icons/new/load.png",
                    ID: "tsbLoadDB",
                    iconWidth: 32,
                    iconHeight: 32,
                    count: 0,
                    showDisabledIcon: false,
                    prompt: "Seçilen yedeklemeyi geri yükler",
                    click: function () {
                        wdBackup.counter++;
                        wdLoadBackup.show();
                        backupDataListe.fetchData({counter: wdBackup.counter});
                    }});



                isc.DynamicForm.create({
                    ID: "dfBackup",
                    width: "100%",
                    height: "100%",
                    // numCols: 2,
                    titleOrientation: "top",
                    validateOnExit: true,
                    validateOnChange: true,
                    margin: 5,
                    fields: [{name: "newName",
                            title: "Dateiname",
                            width: 210,
                            required: true,
                            validators: [
                                {type: "lengthRange", min: 1, max: 30,
                                    errorMessage: "En az 1, maksimum 30 karakter!"}
                            ]
                        }
                    ]});

                isc.IButton.create({
                    ID: "btnBackup",
                    //                    top: 250,
                    align: "center",
                    icon: "icons/new/save.png",
                    title: "Kaydet",
                    click: function () {
                        if (dfBackup.getField("newName").validate()) {

                            RPCManager.send("", function (rpcResponse, data, rpcRequest) {
                                var _data = isc.JSON.decode(data); // Daten aus dem PHP (rpcResponse)
                                var rueckmeldung = _data.response.data["rueckmeldung"];
                                var _errors = _data.response.errors;
                                if (_data.response.status === 0) {  // Durum 0 bedeutet Keine Hata
                                    dfBackup.getField("newName").clearValue();
                                    wdBackup.hide();
                                    isc.say("Verileriniz başarıyla kaydedildi ve Backups klasörinde " + rueckmeldung + " dosya adı altında kaydedildi.", function (value) {
                                        if (value) {
                                            wdBackup.counter++;
                                        }
                                    });
                                } else { // Wenn die Validierungen Hata aufweisen dann:

                                    isc.say("Verileriniz kaydedilemedi!</br></br>" + _errors);
                                }
                            }, {// Übergabe der Parameter
                                actionURL: "api/backup_nameNeu.php",
                                httpMethod: "POST",
                                contentType: "application/x-www-form-urlencoded",
                                useSimpleHttp: true,
                                params: {dateiname: dfBackup.getField("newName").getValue()}
                            }); //Ende RPC Dosya schon existiert
                        } else {
                            isc.say("Lütfen doğru bir yedek adı girin");
                        }
                    }
                });

                isc.VLayout.create({
                    ID: "BackupLayout",
                    height: "100%",
                    width: "100%",
                    align: "center",
                    layoutMargin: 5,
                    members: [dfBackup, btnBackup]});

                currentIcon = "icons/new/save.png";
                isc.Window.create({
                    ID: "wdBackup",
                    title: "yedek adı",
                    // autoSize: true,
                    width: 250,
                    height: 180,
                    counter: 0,
                    autoCenter: true,
                    showFooter: false,
                    headerIconDefaults: {width: 16, height: 16, src: currentIcon},
                    showMinimizeButton: false,
                    showCloseButton: true,
                    canDragReposition: true,
                    canDragResize: true,
                    showShadow: true,
                    showModalMask: true,
                    modalMaskOpacity: 10,
                    isModal: true,
                    items: [BackupLayout]
                });



                isc.ListGrid.create({
                    ID: "backupDataListe",
                    //   header: "Daten düzenleme",
                    width: "100%", height: "100%",
                    alternateRecordStyles: false,
                    showHeader: false,
                    dataSource: backupDataDS,
                    autoFetchData: false,
                    showFilterEditor: false,
                    filterOnKeypress: true,
                    selectionType: "single",
                    canExpandRecords: false,
                    expansionMode: "single",
                    baseStyle: "simpleCell",
                    emptyMessage: "<br><br>Yükleme için hiçbir veri içermiyor",
                    margin: 3,
                    fields: [
                        {name: "dateiname",
                            width: "*"}
                    ], showSelectionCanvas: true,
                    animateSelectionUnder: true,
                    selectionUnderCanvasProperties: {
                        animateShowEffect: "fade",
                        animateFadeTime: 1000,
                        backgroundColor: "#ffff40"
                    },
                    showRollOverCanvas: true,
                    animateRollUnder: true,
                    rollUnderCanvasProperties: {
                        animateShowEffect: "fade",
                        animateFadeTime: 1000,
                        backgroundColor: "#00ffff",
                        opacity: 50
                    },
                    recordDoubleClick: function (record, state) {
                        wdLoadBackupStatus.show();
                        wdLoadBackup.hide();

                        wdLoadBackupStatus.LoadingStatusProgFoo();

                        RPCManager.send("", function (rpcResponse, data, rpcRequest) {
                            var _data = isc.JSON.decode(data);

                            var _errors = _data.response.errors;
                            if (_data.response.status === 0) {
                                wdLoadBackupStatus.LoadingStatusProgFoo2();
                                CategoryTree.backupFunction();
                            } else {
                                wdLoadBackupStatus.hide();
                                prBarLabelLoadingStatus.setPercentDone(0);
                                isc.say("Veriler geri yüklenemedi!</br></br>" + _errors);
                            }
                        }, {// Übergabe der Parameter
                            actionURL: "api/LoadBackup.php",
                            httpMethod: "POST",
                            contentType: "application/x-www-form-urlencoded",
                            useSimpleHttp: true,
                            params: {dateiname: backupDataListe.getSelectedRecord().dateiname}
                        });// Ende RPC
                    }

                });
                isc.Progressbar.create({
                    percentDone: 0,
                    ID: "prBarLabelLoadingStatus",
                    showTitle: true,
                    title: "",
                    height: 13,
                    length: "100%"});
                // XXX Label


                // XXX Label
                isc.Label.create({// Label welches im Toolstrip den selektierten Mandanten und Verlag anzeigt
                    ID: "lblLoadingStatus",
                    height: "100%",
                    padding: 0,
                    width: "100%",
                    align: "center",
                    icon: "icons/new/backup.png",
                    iconSize: 48,
                    valign: "center",
                    wrap: false,
                    //     icon: "icons/16/close.png",
                    showEdges: false,
                    contents: "<b>Veritabanınız yeniden yükleniyor, </br>bu da veritabanının boyutuna bağlı olarak </br>birkaç dakika sürebilir. Lütfen biraz sabırlı olun..</b>"
                });


                currentIcon = "icons/new/loading.png";
                isc.Window.create({
                    ID: "wdLoadBackupStatus",
                    title: "Veritabanı geri yükleniyor ...",
                    // autoSize: true,
                    width: 380,
                    height: 150,
                    count: 1,
                    autoCenter: true,
                    showFooter: false,
                    headerIconDefaults: {width: 16, height: 16, src: currentIcon},
                    showMinimizeButton: false,
                    showCloseButton: false,
                    canDragReposition: true,
                    canDragResize: true,
                    showShadow: true,
                    showModalMask: true,
                    modalMaskOpacity: 10,
                    isModal: true,
                    items: [lblLoadingStatus, prBarLabelLoadingStatus],
                    LoadingStatusProgFoo: function () {
                        if (prBarLabelLoadingStatus.percentDone < 87) {
                            var _percent = prBarLabelLoadingStatus.percentDone + parseInt(2 + (7 * Math.random()));
                            prBarLabelLoadingStatus.setPercentDone(_percent); // Zufallswert wird berechnet

                            if (_percent <= 100) {
                                prBarLabelLoadingStatus.setTitle(_percent + "%");
                            } //Bis 100 wird mitgezählt
                            else {
                                prBarLabelLoadingStatus.setTitle("100%"); // ab 100 darf nicht mehr gezählt werden, da 100 leicht überstiegen wird.
                            }

                            isc.Timer.setTimeout("wdLoadBackupStatus.LoadingStatusProgFoo()", 1000);
                        }
                    },
                    LoadingStatusProgFoo2: function () {
                        if (prBarLabelLoadingStatus.percentDone < 100) {
                            var _percent = prBarLabelLoadingStatus.percentDone + parseInt(2 + (7 * Math.random()));
                            prBarLabelLoadingStatus.setPercentDone(_percent); // Zufallswert wird berechnet

                            if (_percent <= 100) {
                                prBarLabelLoadingStatus.setTitle(_percent + "%");
                            } //Bis 100 wird mitgezählt
                            else {
                                prBarLabelLoadingStatus.setTitle("100%"); // ab 100 darf nicht mehr gezählt werden, da 100 leicht überstiegen wird.
                            }

                            isc.Timer.setTimeout("wdLoadBackupStatus.LoadingStatusProgFoo2()", 500);
                        } else {
                            wdLoadBackupStatus.hide();
                            isc.say("Verileriniz başarıyla geri yüklendi.");
                            prBarLabelLoadingStatus.setTitle("");
                            prBarLabelLoadingStatus.setPercentDone(0);
                            SpieleSuchForm.clearValues();

                        }
                    }
                });


                isc.IButton.create({
                    ID: "btnLoadBackup",
                    //                    top: 250,
                    align: "center",
                    icon: "icons/new/load.png",
                    title: "Yükle",
                    click: function () {
                        if (backupDataListe.getSelection().length == 1) {
                            isc.ask("Mevcut verileri gerçekten " + backupDataListe.getSelectedRecord().dateiname + "  ile değiştirmek istiyormusunuz?", function (value) {
                                if (value) {
                                    wdLoadBackupStatus.show();
                                    wdLoadBackup.hide();

                                    wdLoadBackupStatus.LoadingStatusProgFoo();

                                    RPCManager.send("", function (rpcResponse, data, rpcRequest) {
                                        var _data = isc.JSON.decode(data);

                                        var _errors = _data.response.errors;
                                        if (_data.response.status === 0) {
                                            wdLoadBackupStatus.LoadingStatusProgFoo2();
                                            CategoryTree.backupFunction();
                                        } else {
                                            isc.say("Veritabanı geri yüklenemedi!</br></br>" + _errors);
                                            wdLoadBackupStatus.hide();
                                            prBarLabelLoadingStatus.setPercentDone(0);
                                        }
                                    }, {// Übergabe der Parameter
                                        actionURL: "api/LoadBackup.php",
                                        httpMethod: "POST",
                                        contentType: "application/x-www-form-urlencoded",
                                        useSimpleHttp: true,
                                        params: {dateiname: backupDataListe.getSelectedRecord().dateiname}
                                    });// Ende RPC                
                                }
                            });

                        } else {
                            isc.say("Lütfen önce bir yedekleme Dosya seçin");
                        }
                    }});

                currentIcon = "icons/new/load.png";
                isc.Window.create({
                    ID: "wdLoadBackup",
                    title: "Verileri yükle",
                    // autoSize: true,
                    width: 300,
                    height: 300,
                    autoCenter: true,
                    showFooter: false,
                    headerIconDefaults: {width: 16, height: 16, src: currentIcon},
                    showMinimizeButton: false,
                    showCloseButton: true,
                    canDragReposition: true,
                    canDragResize: true,
                    showShadow: true,
                    showModalMask: true,
                    modalMaskOpacity: 10,
                    isModal: true,
                    items: [backupDataListe, btnLoadBackup]
                });



                /*
                 * ************************ENDE Yedekleme ******************************************************************************
                 * ==================================================================================================================
                 * ******************************************************************************************************************
                 */


                /*
                 * ************* Anfang UPDATE *********************************
                 * -------------------------------------------------------------
                 */


                UploadForm.create({
                    ID: "ufUpdate",
                    width: "100%",
                    count: 0,
                    count2: 0,
                    height: 148,
                    numCols: 2,
                    // location of our backend
    //                    action: 'api/update.php',
                    action: 'api/update_direkt.php',
                    fields: [{
                            type: "RowSpacer",
                            height: 10
                        }, /* {
                         name: "datei",
                         type: "Upload",
                         title: "Update-Dosya",
                         showTitle: "false",
                         colSpan: 2,
                         align: "left",
                         width: 350
                         },*/ {
                            type: "RowSpacer",
                            height: 10
                        }, {
                            name: "upload",
                            title: "Update",
                            type: "submit",
                            icon: "famfam/image_add.png",
                            colSpan: 2,
                            align: "center"
                        }
                    ],
                    submitDone: function (result, _status, bild) {
                        // den Erfolg überprüfen!
                        //   RecordIndexLB.getField("result").setValue(result);
                        ergebnis = result;
                        status = _status;
                        ufUpdate.count++;
                        isc.say(ergebnis, function (value) {
                            // if (value) {
                            // if (status == "ok") {                      


                            // }// if status
                            // }// if value
                        }); //isc.say
                    }//submit

                });

                isc.IButton.create({
                    ID: "btnUpdateClose",
                    type: "button",
                    showDisabledIcon: false,
                    icon: "famfam/door_in.png",
                    disabled: false,
                    name: "btnUpdateClose",
                    title: "Kapat", width: 100,
                    click: function () {

                        wdUpdate.hide();
                    }});

                isc.HLayout.create({
                    ID: "HLayoutBtnUpdateClose",
                    height: 20,
                    width: "100%",
                    align: "center",
                    margin: 5,
                    members: [btnUpdateClose]});

                isc.VLayout.create({
                    ID: "VLayoutUfUpdate_BtnUpdateClose",
                    height: "100%",
                    width: "100%",
                    align: "center",
                    members: [ufUpdate, HLayoutBtnUpdateClose]});

                isc.Window.create({
                    ID: "wdUpdate",
                    title: "Güncelleme",
                    autoSize: true,
                    autoCenter: true,
                    showFooter: false,
                    showMinimizeButton: false,
                    showCloseButton: true,
                    width: 550,
                    height: 150,
                    headerIconDefaults: {
                        width: 16,
                        height: 16,
                        src: "famfam/page_refresh.png"
                    },
                    canDragReposition: true,
                    canDragResize: false,
                    showShadow: true,
                    showModalMask: true,
                    modalMaskOpacity: 10,
                    isModal: true,
                    items: [VLayoutUfUpdate_BtnUpdateClose]
                });



                /*
                 * ************* Ende UPDATE ***********************************
                 * -------------------------------------------------------------
                 */



                /*
                 * ******************* ANFANG TOOLSTRIPS ***********************
                 * -------------------------------------------------------------
                 */

                /*
                 ***************** Toolstrip Hasta ************************** 
                 */
                isc.Label.create({
                    padding: 0,
                    ID: "lblKunden",
                    width: 200,
                    height: "100%",
                    align: "center",
                    contents: '<text style="color:#3765A4; font-size:19px; font-family:Calibri; text-decoration:none;"><b>Ana veriler - Hastalar</b></text>'
                });

                isc.ToolStrip.create({
                    ID: "tsKunden",
                    width: "100%",
                    height: 40,
                    members: [isc.LayoutSpacer.create({width: 10}),
                        tsbAddKunden, isc.LayoutSpacer.create({width: 10}),
                        tsbKundenEdit, isc.LayoutSpacer.create({width: 10}),
                        tsbKundenDelete, isc.LayoutSpacer.create({width: 10}), "separator",
                        tsbKundenHist, isc.LayoutSpacer.create({width: 10}), "separator",
                        tsbKundenPrint, isc.LayoutSpacer.create({width: 10}),
                        tsbKundenCSV, isc.LayoutSpacer.create({width: 10}), "separator",
                        tsbKundenRefresh, isc.LayoutSpacer.create({width: 10}), "separator",
                        dfErrorFormKunden, isc.LayoutSpacer.create({width: 50}), lblKunden]});

                /*
                 ***************** Toolstrip Tedavi ************************** 
                 */
                isc.Label.create({
                    padding: 0,
                    ID: "lblProdukte",
                    width: 200,
                    height: "100%",
                    align: "center",
                    contents: '<text style="color:#3765A4; font-size:19px; font-family:Calibri; text-decoration:none;"><b>Ana veriler - Tedaviler</b></text>'
                });

                isc.ToolStrip.create({
                    ID: "tsProdukte",
                    width: "100%",
                    height: 40,
                    members: [isc.LayoutSpacer.create({width: 10}),
                        tsbAddProdukte, isc.LayoutSpacer.create({width: 10}),
                        tsbProdukteEdit, isc.LayoutSpacer.create({width: 10}),
                        tsbProdukteDelete, isc.LayoutSpacer.create({width: 10}), "separator",
                        tsbProdukteHist, isc.LayoutSpacer.create({width: 10}), "separator",
                        tsbProduktePrint, isc.LayoutSpacer.create({width: 10}),
                        tsbProdukteCSV, isc.LayoutSpacer.create({width: 10}), "separator",
                        tsbProdukteRefresh, isc.LayoutSpacer.create({width: 10}),
                        dfErrorFormProdukte, isc.LayoutSpacer.create({width: 50}), lblProdukte]});

                /*
                 ***************** Toolstrip Kullanıcı ************************** 
                 */
                isc.Label.create({
                    padding: 0,
                    ID: "lblUser",
                    width: 200,
                    height: "100%",
                    align: "center",
                    contents: '<text style="color:#3765A4; font-size:19px; font-family:Calibri; text-decoration:none;"><b>Kullanıcılar</b></text>'
                });

                isc.ToolStrip.create({
                    ID: "tsUser",
                    width: "100%",
                    height: 40,
                    members: [isc.LayoutSpacer.create({width: 10}), tsbUserEdit, isc.LayoutSpacer.create({width: 10}), tsbUserAdd, isc.LayoutSpacer.create({width: 10}), tsbUserRefresh, dfErrorFormUser / isc.LayoutSpacer.create({width: 50}), lblUser]});


                /*
                 ***************** Toolstrip Abrechnung ************************** 
                 */
                isc.Label.create({
                    padding: 0,
                    ID: "lblAbrechnung",
                    width: 200,
                    height: "100%",
                    align: "center",
                    contents: '<text style="color:#3765A4; font-size:19px; font-family:Calibri; text-decoration:none;"><b>Randevular</b></text>'
                });

                isc.ToolStrip.create({
                    ID: "tsVerkaeufe",
                    width: "100%",
                    height: 40,
                    members: [isc.LayoutSpacer.create({width: 10}),
                        tsbAddAbrechnung, isc.LayoutSpacer.create({width: 10}),
                        tsbAbrechnungDelete, isc.LayoutSpacer.create({width: 10}),
                        tsbAbrechnungGesamtEdit, "separator", isc.LayoutSpacer.create({width: 10}),
                        //                        tsbAddPosition, isc.LayoutSpacer.create({width: 10}),
                        //                        tsbAbrechnungEdit, isc.LayoutSpacer.create({width: 10}),
                        //                        tsbPositionDelete, "separator", isc.LayoutSpacer.create({width: 10}),
                        tsbAbrechnungHist, "separator", isc.LayoutSpacer.create({width: 10}),
                        //                        tsbAbrechnungPrint, isc.LayoutSpacer.create({width: 10}),
                        tsbPDFAbrechnung, isc.LayoutSpacer.create({width: 10}),
                        tsbAbrechnungCSV, "separator", isc.LayoutSpacer.create({width: 10}),
                        tsbAbrechnungRefresh, "separator", isc.LayoutSpacer.create({width: 10}),
                        //                            tsbAbrechnungSuche, "separator", isc.LayoutSpacer.create({width: 10}),
                        tsbBookAbrechnung, "separator", isc.LayoutSpacer.create({width: 10}),
                        dfErrorFormAbrechnung, isc.LayoutSpacer.create({width: 50}),
                        lblAbrechnung]});
                /*
                 ***************** Toolstrip Kalender ************************** 
                 */
                /* isc.Label.create({
                 padding: 0,
                 ID: "lblKalender", 
                 width: 200,
                 height: "100%",
                 align: "center",
                 contents: '<text style="color:#3765A4; font-size:19px; font-family:Calibri; text-decoration:none;"><b>Kalender</b></text>'
                 });
                     
                 isc.ToolStrip.create({
                 ID: "tsKalender",
                 width: "100%",
                 height: 40,
                 members: [ lblKalender]});*/


                /*
                 ***************** Toolstrip Buchungen ************************** 
                 */
                isc.Label.create({
                    padding: 0,
                    ID: "lblBuchungen",
                    width: 200,
                    height: "100%",
                    align: "center",
                    contents: '<text style="color:#3765A4; font-size:19px; font-family:Calibri; text-decoration:none;"><b>Faturalar</b></text>'
                });

                isc.ToolStrip.create({
                    ID: "tsBuchungen",
                    width: "100%",
                    height: 40,
                    members: [isc.LayoutSpacer.create({width: 50}), lblBuchungen]});


                /*
                 * ******************** Ende Toolstrip *************************
                 * -------------------------------------------------------------
                 */


                /*
                 * ******************** Anfang VLayouts ************************
                 * -------------------------------------------------------------
                 */


                isc.VLayout.create({
                    ID: "VLayoutKunden",
                    width: "100%",
                    height: "100%",
                    members: [
                        tsKunden, kundenListe
                    ]
                });


                isc.VLayout.create({
                    ID: "VLayoutProdukte",
                    width: "100%",
                    height: "100%",
                    members: [
                        tsProdukte, produktListe
                    ]
                });



                isc.VLayout.create({
                    ID: "VLayoutUser",
                    width: "100%",
                    height: "100%",
                    members: [
                        tsUser, userListe
                    ]
                });

                isc.VLayout.create({
                    ID: "VLayoutVerkaeufe",
                    width: "100%",
                    height: "100%",
                    members: [
                        tsVerkaeufe, HLayoutAbrechnungGrid_Tree
                    ]
                });

                isc.HLayout.create({
                    ID: "HLayoutBuchungen",
                    width: "100%",
                    height: "100%",
                    members: [
                        dfBuchungenZeitraum, buchungsListe
                    ]
                });

                isc.VLayout.create({
                    ID: "VLayoutBuchungen",
                    width: "100%",
                    height: "100%",
                    members: [tsBuchungen,
                        HLayoutBuchungen
                    ]
                });



                /*   isc.VLayout.create({
                 ID: "VLayoutKalender",
                 width: "100%",
                 height: "100%",
                 members: [tsKalender,eventCalendar]
                 });*/

                /*
                 * ******************** ENDE VLayouts ************************
                 * -------------------------------------------------------------
                 */


                /*
                 * ************************* ENDE CODE *****************************************
                 * =============================================================================
                 */




                isc.HLayout.create({
                    ID: "VLayoutCategoryTree",
                    width: "100%",
                    height: "100%",
                    //                        border: "1px solid black",
                    members: [
                        VLayoutLogoutLabel/*, welcomeSite, VLayoutKunden, VLayoutProdukte, VLayoutUser, VLayoutVerkaeufe, VLayoutBuchungen , VLayoutKalender*/
                    ]
                });
                isc.HLayout.create({
                    ID: "HLayoutRibbonLabel",
                    width: "100%",
                    height: 40,
                    members: [
                        ribbonBar, label
                    ]
                });


                isc.TabSet.create({
                    ID: "tabHauptFensterKlinik",
                    width: "100%",
                    height: "100%",
                    count: 0,
                    tabs: [
                        {title: "Randevu olustur",
                            icon: "famfam/calendar.png",
                            pane: VLayoutVerkaeufe},
                        {title: "Hastalar",
                            icon: "icons/32/patient.png",
                            pane: VLayoutKunden},
                        {title: "Tedaviler",
                            icon: "icons/32/doctor.png",
                            pane: VLayoutProdukte},
                        {title: "Faturalar",
                            icon: "famfam/invoice.png",
                            pane: VLayoutBuchungen},
                        {title: "Kullanıcı yönetimi",
                            icon: "famfam/user.png",
                            pane: VLayoutUser}
                    ],
                    tabSelected: function (tabSet, tabNum, tabPane, ID, tab, name) {

                        if (tabHauptFensterKlinik.getSelectedTabNumber() == 0) {//Randevu olustur
                            welcomeSite.setContentsURL(calendar_);
                        }
                        if (tabHauptFensterKlinik.getSelectedTabNumber() == 1) {//Hastalar

                        }
                        if (tabHauptFensterKlinik.getSelectedTabNumber() == 2) {//Tedaviler

                        }
                        if (tabHauptFensterKlinik.getSelectedTabNumber() == 3) {//Faturalar
                            CategoryTree.count++;
                            CategoryTree.firstLoadFunction(dfBuchungenZeitraum, CategoryTree.count, Sene);
                        }
                        if (tabHauptFensterKlinik.getSelectedTabNumber() == 4) {//Kullanıcı yönetimi

                        }

                    }
                });



                isc.VLayout.create({
                    ID: "VLayoutHauptFenster",
                    height: "100%",
                    width: "100%",
                    members: [HLayoutRibbonLabel, tabHauptFensterKlinik]});

                /*
                 * **************** Nur das Hauptfenster soll angezeigt werden beim Start *****
                 */
                VLayoutHauptFenster.show();
                //
                //                VLayoutKunden.hide();
                //                VLayoutProdukte.hide();
                //                VLayoutUser.hide();
                //                VLayoutVerkaeufe.hide();
                //                VLayoutBuchungen.hide();

                //                VLayoutKalender.hide();

                /*
                 * ******* Progressbars werden angepasst ******************
                 */
                pgbEditKunden.setHeight(16);
                pgbAddKunden.setHeight(16);
                pgbEditProdukte.setHeight(16);
                pgbAddProdukte.setHeight(16);

                /*
                 * ******* Zuordnung der ContextMenüs ******************
                 */
                kundenListe.contextMenu = menuKunden;
                produktListe.contextMenu = menuProdukte;
                userListe.contextMenu = menuUser;
                abrechnungsListeEdit.contextMenu = menuEditAbrechnung;
                abrechnungsTree.contextMenu = menuAbrechnung;
                welcomeSite.setContentsURL(calendar_);
                dfAddAbrechnung.getField("brutto_preis_").hide();
                dfAddPosition.getField("brutto_preis_").hide();
                dfEditAbrechnung2.getField("brutto_preis_").hide();
                dfAddAbrechnung.getField("mwst_").hide();
                dfAddPosition.getField("mwst_").hide();
                dfEditAbrechnung2.getField("mwst_").hide();
            </SCRIPT>
        </BODY>
    </HTML>
    <?php
} else {
    header("Location: ".protokol."://$host$uri/login.php");
//    header("Location: https://$host$uri/noadmin.php");
}
?>


