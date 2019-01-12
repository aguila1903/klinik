
// Aktueller Ay --------------------------------------------
  var jetzt = new Date();
  var Sene = jetzt.getFullYear();
  var Ay = jetzt.getMonth() + 1;
  var Tag = jetzt.getDate();
  var _tag = "";
  var _Monat = "";
  var monat = "";
  if(Ay.toString().length <= 1){
      _Monat = '0' + Ay + Sene;
      monat = '0' + Ay;
  } else{
      _Monat = Ay + '' + Sene;
      monat = Ay;
  }
  if(Tag.toString().length <= 1){
      _tag = '0' + Tag;
  } else{
      _tag = Tag;
  }

  var _Time = jetzt.getTime();
  var _Heute = Sene + "-" + monat + "-" + _tag; //2018-09-03
  var _heute_ = Sene + "" + monat + "" + _tag; //20180903
  var _Heute_ger = Tag + "." + Ay + "." + Sene; //3.9.2018
  /*
   * ******************** Upload-Function ************************
   * -------------------------------------------------------------
   */
  isc.defineClass('UploadForm', 'DynamicForm');
  UploadForm.addClassProperties({
      create: function(data){

          // We are creating IFRAME that will work as a target for upload.
          var iframeCanvas = Canvas.getById('uploadFormIframe');
          if(!iframeCanvas){
              Canvas.create({
                  ID: "uploadFormIframe",
                  contents: "<iframe name=\"uploadFormIframe\"></iframe>",
                  autoDraw: true,
                  visibility: "hidden"
              });
          }

          // parameters needed to submit a form
          isc.addProperties(data, {
              encoding: "multipart",
              canSubmit: "true",
              target: "uploadFormIframe"
          });

          // special field that will hold form's ID
          data.fields.push({
              name: "uploadFormID",
              type: "hidden"
          });

          // We are creating a form.
          var f = this.Super('create', data);

          // We are setting special field to an ID of newly created form.
          f.setValue('uploadFormID', f.getID());
          return f;
      }
  });


  /*
   * ******************** Upload-Function ************************
   * -------------------------------------------------------------
   */

  isc.defineClass('UploadForm', 'DynamicForm');
  UploadForm.addClassProperties({
      create: function(data){

          // We are creating IFRAME that will work as a target for upload.
          var iframeCanvas = Canvas.getById('uploadFormIframe');
          if(!iframeCanvas){
              Canvas.create({
                  ID: "uploadFormIframe",
                  contents: "<iframe name=\"uploadFormIframe\"></iframe>",
                  autoDraw: true,
                  visibility: "hidden"
              });
          }

          // parameters needed to submit a form
          isc.addProperties(data, {
              encoding: "multipart",
              canSubmit: "true",
              target: "uploadFormIframe"
          });
          // special field that will hold form's ID
          data.fields.push({
              name: "uploadFormID",
              type: "hidden"
          });
          // We are creating a form.
          var f = this.Super('create', data);
          // We are setting special field to an ID of newly created form.
          f.setValue('uploadFormID', f.getID());
          return f;
      }
  });

  /*
   * ******************** DropZone *******************************
   * ---------------------ToDo------------------------------------
   */
  function drop2(drop_zone, _form, _status, _list){// wird beim öffnen des Upload-Fensters gestartet
      var dropZone = document.getElementById(drop_zone);
      dropZone.addEventListener('dragover', handleDragOver, false);
      dropZone.addEventListener('drop', uploadFile(_form, _status, _list), false);
      document.getElementById(_status).innerHTML = "";
      document.getElementById(_list).innerHTML = "";
  }

  function handleDragOver(event){
      event.stopPropagation();
      event.preventDefault();
      var dt = event.dataTransfer;
      dt.dropEffect = 'copy'; // Explicitly show this is a copy.

  }

  function uploadFile(_form, _status, _list)
  {
      return function(event){ //callback
          event.stopPropagation();
          event.preventDefault();
          var files = event.dataTransfer.files; // FileList object.

          // files is a FileList of File objects. List some properties.
          var output = [];
          for(var i = 0, f; f = files[i]; i++){
              output.push('<li><strong>', f.name, '</strong> - ', f.size, ' bytes </li>');
              //  uploadFile(f, event);

              var xhr = new XMLHttpRequest();    // den AJAX Request anlegen
              // Angeben der URL und des Requesttyps
              xhr.open('POST', 'api/media_upload.php'); // Die Verbindung wird geöffnet
              xhr.responseType = "json";
              var formdata = new FormData();    // Anlegen eines FormData Objekts zum Versenden unserer Datei
              var typ = _form.getField("ref").getValue();
              var art = _form.getField("bild_art").getValue();
              var id = _form.getField("id").getValue();

              formdata.append('datei', f);  // Anhängen der Datei an das Objekt
              formdata.append('ref', typ);  // Anhängen der Datei an das Objekt
              formdata.append('bild_art', art);  // Anhängen der Datei an das Objekt
              formdata.append('id', id);  // Anhängen der Datei an das Objekt

              xhr.upload.addEventListener("progress", progressHandler(_status), false); // ist für den Übertragungsprozess verantwortlich u.a. für die Progressbar
              xhr.addEventListener("load", completeHandler(files.length), false); // Gibt die Antwort des Servers wieder bei abgeschlossener Übertragung
              xhr.addEventListener("error", errorHandler(_status), false); //Zeigt die Fehler-Texte an z.B. bei Fehlern im PHP Skript
              xhr.send(formdata);    // Absenden des Requests
          }
          //isc.say('<ul>' + output.join('') + '</ul>');

          document.getElementById(_list).innerHTML = '<ol>' + output.join('') + '</ol>'; //Namen der hochgel. Dateien werden angezeigt

          // Neu laden der Tabelle
      };
  }

  function progressHandler(_status){
//    document.getElementById('loaded_n_total').innerHTML = "Uploaded " + event.loaded + " bytes of " + event.total;
      return function(event){//callback
          var percent = (event.loaded / event.total) * 100;
          //   document.getElementById('progressBar').value = Math.round(percent); // Funktioniert wird aber nicht gebraucht
          document.getElementById(_status).innerHTML = Math.round(percent) + "% geladen";
      };
  }

  function completeHandler(i){
      return function(event){//callback
          var response = event.target.response;
          if(response == "ok"){
              if(i > 1){
                  isc.say(i + " Bilder erfolgreich hochgeladen");
              } else if(i == 1){
                  isc.say(i + " Bild erfolgreich hochgeladen");
              }
          } else{
              isc.say("Es konnten leider keine Bilder hochgeladen werden.<br />" + response);
          }
      };
      //  document.getElementById('progressBar').value = 0; // Funktioniert wird aber nicht gebraucht
  }
  function errorHandler(_status){
      return function(){//callback
          document.getElementById(_status).innerHTML = "Upload failed";
      };
  }
  
  function doUpdate(type_)
{
    RPCManager.send("", function (rpcResponse, data, rpcRequest)
    {
        var _data = isc.JSON.decode(data);
        if (_data.response.status === 0)
        {
            var rueckmeldung = _data.response.data;

            isc.say(rueckmeldung, function (value)
        {
                if (value)
                {
                    if (rueckmeldung != "Keine neuen Updates vorhanden!") // Es gab tatsächlich ein Update!
                    {
                        window.location.reload(true);
        }
                }
            });
        } else if (_data.response.status === 4)
        {
            var _errors = _data.response.errors;
            isc.say(_errors);
        }
    }, {// Übergabe der Parameter
        actionURL: "update/update.php",
        httpMethod: "POST",
        contentType: "application/x-www-form-urlencoded",
        useSimpleHttp: true,
        params: {type: type_}
    }); //Ende RPC
}
;

  /*
   * ******************** onRefresh-Function *********************
   * -------------------------------------------------------------
   */

  var onRefresh = function(_listgrid){
//    var jetzt = new Date();
      var dataSource = Canvas.getById(_listgrid).getDataSource();
      var request = {
          startRow: 0,
          endRow: (Canvas.getById(_listgrid).getVisibleRows()[1] + Canvas.getById(_listgrid).data.resultSize),
          sortBy: Canvas.getById(_listgrid).getSort(),
          showPrompt: false,
          params: {count: new Date()}
      };
      var callback = function(dsResponse, data, dsRequest){
          var resultSet = isc.ResultSet.create({
              dataSource: Canvas.getById(_listgrid).getDataSource(),
              initialLength: dsResponse.totalRows,
              initialData: dsResponse.data,
              sortSpecifiers: Canvas.getById(_listgrid).getSort()
          });
          Canvas.getById(_listgrid).setData(resultSet);
      };
      dataSource.fetchData(Canvas.getById(_listgrid).getCriteria(), callback, request);
  };

  var onRefreshAbrechnungsTree = function(_listgrid, _param){
//    var jetzt = new Date();
      var dataSource = Canvas.getById(_listgrid).getDataSource();
      var request = {
          startRow: 0,
          endRow: (Canvas.getById(_listgrid).getVisibleRows()[1] + Canvas.getById(_listgrid).data.resultSize),
          sortBy: Canvas.getById(_listgrid).getSort(),
          showPrompt: false,
          params: {count: _param}
      };
      var callback = function(dsResponse, data, dsRequest){
          var resultSet = isc.ResultSet.create({
              dataSource: Canvas.getById(_listgrid).getDataSource(),
              initialLength: dsResponse.totalRows,
              initialData: dsResponse.data,
              sortSpecifiers: Canvas.getById(_listgrid).getSort()
          });
          Canvas.getById(_listgrid).setData(resultSet);
      };
      dataSource.fetchData(Canvas.getById(_listgrid).getCriteria(), callback, request);
  };

  /*
   * ******************** onRefresh-Function *********************
   * -------------------------------------------------------------
   */

  var onRefreshAbrechnung = function(_listgrid, _param1, _param2){
      var dataSource = Canvas.getById(_listgrid).getDataSource();
      var request = {
          startRow: 0,
          endRow: (Canvas.getById(_listgrid).getVisibleRows()[1] + Canvas.getById(_listgrid).data.resultSize),
          sortBy: Canvas.getById(_listgrid).getSort(),
          showPrompt: false,
          params: {beleg_nr: _param1,
              count: _param2}
      };
      var callback = function(dsResponse, data, dsRequest){
          var resultSet = isc.ResultSet.create({
              dataSource: Canvas.getById(_listgrid).getDataSource(),
              initialLength: dsResponse.totalRows,
              initialData: dsResponse.data,
              sortSpecifiers: Canvas.getById(_listgrid).getSort()
          });
          Canvas.getById(_listgrid).setData(resultSet);
      };
      dataSource.fetchData(Canvas.getById(_listgrid).getCriteria(), callback, request);
  };
  /*
   * ******************** onRefresh-Einzahlungen *********************
   * -------------------------------------------------------------
   */

  var onRefreshEinzahlung = function(_listgrid, _param1, _param2, _param3, _param4){
      var dataSource = Canvas.getById(_listgrid).getDataSource();
      var request = {
          startRow: 0,
          endRow: (Canvas.getById(_listgrid).getVisibleRows()[1] + Canvas.getById(_listgrid).data.resultSize),
          sortBy: Canvas.getById(_listgrid).getSort(),
          showPrompt: false,
          params: {beleg_nr: _param1,
              count: _param2,
              jahr: _param3,
              monat: _param4}
      };
      var callback = function(dsResponse, data, dsRequest){
          var resultSet = isc.ResultSet.create({
              dataSource: Canvas.getById(_listgrid).getDataSource(),
              initialLength: dsResponse.totalRows,
              initialData: dsResponse.data,
              sortSpecifiers: Canvas.getById(_listgrid).getSort()
          });
          Canvas.getById(_listgrid).setData(resultSet);
      };
      dataSource.fetchData(Canvas.getById(_listgrid).getCriteria(), callback, request);
  };
  /*
   * ******************** onRefresh-Ausgaben *********************
   * -------------------------------------------------------------
   */

  var onRefreshAusgaben = function(_listgrid, _param1, _param2, _param3, _param4, _param5, _param6){
      var dataSource = Canvas.getById(_listgrid).getDataSource();
      var request = {
          startRow: 0,
          endRow: (Canvas.getById(_listgrid).getVisibleRows()[1] + Canvas.getById(_listgrid).data.resultSize),
          sortBy: Canvas.getById(_listgrid).getSort(),
          showPrompt: false,
          params: {auswahl: _param1,
              ausg_art_kz: _param2,
              jahr: _param3,
              monat: _param4,
              ausg_kz: _param5,
              count: _param6}
      };
      var callback = function(dsResponse, data, dsRequest){
          var resultSet = isc.ResultSet.create({
              dataSource: Canvas.getById(_listgrid).getDataSource(),
              initialLength: dsResponse.totalRows,
              initialData: dsResponse.data,
              sortSpecifiers: Canvas.getById(_listgrid).getSort()
          });
          Canvas.getById(_listgrid).setData(resultSet);
      };
      dataSource.fetchData(Canvas.getById(_listgrid).getCriteria(), callback, request);
  };



  /*
   * 
   * @param {type} zahl
   * @param {type} n
   * @returns {@exp;Math@call;round|@exp;@exp;Math@call;roundfaktor}
   */

  var rundung = function(zahl, n){
      var faktor;
      faktor = Math.pow(10, n);
      return(Math.round(zahl * faktor) / faktor);
  };

  /*
   * ********************** Variablen ****************************
   * -------------------------------------------------------------
   */
  kundenNr = "";

  var getNextTermin = function(_form, _datum, status = 'N'){
      if(status == 'J'){
          RPCManager.send("", function(rpcResponse, data, rpcRequest){
              var _data = isc.JSON.decode(data); // Daten aus dem PHP (rpcResponse)
              if(_data.response.status === 0){  // Status 0 bedeutet Keine Fehler

                  var nextTermin = '';
                  var nextEndTime = '';
                  nextTermin = _data.response.data["nextTermin"];
                  nextEndTime = _data.response.data["nextEndTime"];

                  if(nextTermin != '0000'){
                      _form.getField("startTime").setValue(nextTermin);
                      _form.getField("endTime").setValue(nextEndTime);
                  }


              } else{ // Wenn die Validierungen Fehler aufweisen dann:

                  dfErrorFormAbrechnung.setErrors(_data.response.errors, true);
                  var _errors = dfErrorFormAbrechnung.getErrors();
                  for(var i in _errors)
                  {
                      isc.say("<b>Fehler! </br>" + (_errors [i]) + "</b>");
                  }
              }
          }, {// Übergabe der Parameter
              actionURL: "api/getNextTermin.php",
              httpMethod: "GET",
              contentType: "application/x-www-form-urlencoded",
              useSimpleHttp: true,
              params: {
                  datum: _datum
              }

          }); //Ende RPC
  }
  };

  // Berechnungen
  var preisFunction = function(form){
      if(form.getField("prod_kz").getValue()){
          RPCManager.send("", function(rpcResponse, data, rpcRequest){
              var _data = isc.JSON.decode(data); // Daten aus dem PHP (rpcResponse)
              if(_data.response.status === 0){  // Durum 0 bedeutet Keine Hata
                  if(form.getField("preis_kat").getValue() == "4"){
                      if(form.getField("brutto_preis_").getValue()){
                          bruttoPreis = form.getField("brutto_preis_").getValue().toString().replace(",", ".");
                      } else{
                          bruttoPreis = 0.00;
                      }
                      if(form.getField("mwst_").getValue()){
                          MWST = form.getField("mwst_").getValue().toString().replace(",", ".");
                      } else{
                          MWST = 18.00;
                      }
                  } else{
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

              } else{

                  form.setErrors(_data.response.errors, true);
                  var _errors = form.getErrors();
                  for(var i in _errors)
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

  //Datum für den Kalender konvertieren
  function convertCalDate(_date){
      var year = _date.getFullYear();
      var day = _date.getDate();
      var month = _date.getMonth() + 1;

      var _day = "";
      var _month = "";
      if(month.toString().length <= 1){
          _month = '0' + month;
      } else{
          _month = month;
      }

      if(day.toString().length <= 1){
          _day = '0' + day;
      } else{
          _day = day;
      }

      return year + '-' + _month + '-' + _day;
  }
  ;