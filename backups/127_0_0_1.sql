-- phpMyAdmin SQL Dump
-- version 4.1.12
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Erstellungszeit: 18. Sep 2014 um 08:18
-- Server Version: 5.6.16
-- PHP-Version: 5.5.11

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Datenbank: `erdodb`
--
CREATE DATABASE IF NOT EXISTS `erdodb` DEFAULT CHARACTER SET utf8 COLLATE utf8_bin;
USE `erdodb`;

DELIMITER $$
--
-- Prozeduren
--
DROP PROCEDURE IF EXISTS `addAbrechnung`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `addAbrechnung`(
Var_prod_kz char(4),
Var_prod_bez varchar(64),
Var_kunden_nr int,
Var_kunden_name varchar(64),
Var_menge int,
Var_preis_kat tinyint,
Var_netto_preis DECIMAL(8,2),
Var_netto_gesamt_preis DECIMAL(8,2),
Var_mwst DECIMAL(5,2),
Var_datum datetime,
Var_beleg_nr varchar(45),
Var_bemerkung varchar(260),
Var_user varchar(15)
)
root:BEGIN

Declare Var_anzahl int;
Declare Var_anzahl_hist int;
Declare Var_lfd_nr int;

/*DECLARE Var_lfn int;*/
DECLARE Var_feld varchar(264);
DECLARE Var_ainhalt varchar(264);
DECLARE Var_ninhalt varchar(264);
DECLARE Var_code char(3);

/*Es dürfen keine unterschiedlichen Kunden auf der selben Belegnr. sein */
IF exists (select * from verkaeufe where beleg_nr = Var_beleg_nr)
then 
  If exists (select * from verkaeufe Where beleg_nr = Var_beleg_nr and verkauf_an != Var_kunden_nr) 
  Then
  Select -99 as ergebnis, -99 as historie, -99 as lfd_nr;
  Leave root;
  Else
  If exists (select * from verkaeufe Where beleg_nr = Var_beleg_nr and DATE_FORMAT(datum,GET_FORMAT(DATE,'EUR')) != DATE_FORMAT(Var_datum,GET_FORMAT(DATE,'EUR'))) 
  Then
  Select -98 as ergebnis, -98 as historie, -98 as lfd_nr;
  Leave root;
  Else
  Start Transaction;
  Insert into erdodb.verkaeufe
  (prod_kz,
  verkauf_an ,
  menge,
  preis_kat,
  datum,
  bemerkung,
  beleg_nr,
  mwst,
  einzelpr_netto,
  gesamtpr_netto,
  beleg_pfad
  )
  Values(
 /*Var_kunden_nr,*/
  Var_prod_kz,
  Var_kunden_nr,
  Var_menge,
  Var_preis_kat,
  Var_datum,
  Var_bemerkung,
  Var_beleg_nr,
  Var_mwst,
  Var_netto_preis,
  Var_netto_gesamt_preis,
  NULL
  );

 set Var_anzahl = ROW_COUNT();

 Set Var_code = '008';

 Set Var_lfd_nr = (SELECT IFNULL(MAX(lfd_nr),0) FROM verkaeufe);

/*Set Var_lfn = (SELECT IFNULL(MAX(lfn),0) FROM hist_kunden)+1;*/
	  Set Var_feld =  'NEU';
	  Set Var_ainhalt =  NULL;
	  Set Var_ninhalt =  NULL;
	  
      INSERT INTO hist_abrechnung
        (lfd_nr, kunden_nr, prod_kz, prod_bez, kunden_name, codetext, user, aenderdat, feld, a_inhalt, n_inhalt)
      VALUES
        (Var_lfd_nr, Var_kunden_nr, Var_prod_kz, Var_prod_bez, Var_kunden_name, Var_code, Var_user, curtime(), Var_feld, Var_ainhalt, Var_ninhalt);

 set Var_anzahl_hist = ROW_COUNT();

  IF Var_anzahl != 1 OR Var_anzahl_hist != 1
  Then 
  Rollback;
Select 
    Var_anzahl as ergebnis,
    Var_anzahl_hist as historie,
    Var_lfd_nr as lfd_nr;
  Leave root;
  Else 
  Commit;
  End IF;
  commit;

Select 
    Var_anzahl as ergebnis,
    Var_anzahl_hist as historie,
    Var_lfd_nr as lfd_nr;
  End if;
  End if;

Else
Start Transaction;
Insert into erdodb.verkaeufe
(prod_kz,
verkauf_an ,
menge,
preis_kat,
datum,
bemerkung,
beleg_nr,
mwst,
einzelpr_netto,
gesamtpr_netto,
beleg_pfad
)
 Values(
/*Var_kunden_nr,*/
Var_prod_kz,
Var_kunden_nr,
Var_menge,
Var_preis_kat,
Var_datum,
Var_bemerkung,
Var_beleg_nr,
Var_mwst,
Var_netto_preis,
Var_netto_gesamt_preis,
NULL
);

set Var_anzahl = ROW_COUNT();

Set Var_code = '008';

Set Var_lfd_nr = (SELECT IFNULL(MAX(lfd_nr),0) FROM verkaeufe);

/*Set Var_lfn = (SELECT IFNULL(MAX(lfn),0) FROM hist_kunden)+1;*/
	  Set Var_feld =  'NEU';
	  Set Var_ainhalt =  NULL;
	  Set Var_ninhalt =  NULL;
	  
      INSERT INTO hist_abrechnung
        (lfd_nr, kunden_nr, prod_kz, prod_bez, kunden_name, codetext, user, aenderdat, feld, a_inhalt, n_inhalt)
      VALUES
        (Var_lfd_nr, Var_kunden_nr, Var_prod_kz, Var_prod_bez, Var_kunden_name, Var_code, Var_user, curtime(), Var_feld, Var_ainhalt, Var_ninhalt);

set Var_anzahl_hist = ROW_COUNT();

 IF Var_anzahl != 1 OR Var_anzahl_hist != 1
 Then 
 Rollback;
Select 
    Var_anzahl as ergebnis,
    Var_anzahl_hist as historie,
    Var_lfd_nr as lfd_nr;
 Leave root;
 Else 
 Commit;
 End IF;
commit;

Select 
    Var_anzahl as ergebnis,
    Var_anzahl_hist as historie,
    Var_lfd_nr as lfd_nr;
End if;

END$$

DROP PROCEDURE IF EXISTS `addFiliale`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `addFiliale`(
Var_name varchar(64),
Var_user varchar(15)
)
root:BEGIN

Declare Var_anzahl int;
Declare Var_anzahl_hist int;
Declare Var_filial_nr int;

/*DECLARE Var_lfn int;*/
DECLARE Var_feld varchar(264);
DECLARE Var_ainhalt varchar(264);
DECLARE Var_ninhalt varchar(264);
DECLARE Var_code char(3);

/*set Var_filial_nr = (Select max(kunden_nr) from erdodb.kunden) + 1;*/

Start Transaction;
Insert into erdodb.filialen
(name)
 Values(Var_name);

set Var_anzahl = ROW_COUNT();

Set Var_code = '011';

Set Var_filial_nr = (SELECT  IFNULL(MAX(filial_nr),0) FROM filialen);

/*Set Var_lfn = (SELECT IFNULL(MAX(lfn),0) FROM hist_kunden)+1;*/
	  Set Var_feld =  'NEU';
	  Set Var_ainhalt =  NULL;
	  Set Var_ninhalt =  NULL;
	  
      INSERT INTO hist_kunden
        (schluessel, name, codetext, user, aenderdat, feld, a_inhalt, n_inhalt)
      VALUES
        ( Var_filial_nr, Var_name, Var_code, Var_user, curtime(), Var_feld, Var_ainhalt, Var_ninhalt);

set Var_anzahl_hist = ROW_COUNT();

 IF Var_anzahl != 1 OR Var_anzahl_hist != 1
 Then 
 Rollback;
 Select Var_anzahl as ergebnis, Var_anzahl_hist as historie, Var_filial_nr as filial_nr;
 Leave root;
 Else 
 Commit;
 End IF;
commit;

Select Var_anzahl as ergebnis, Var_anzahl_hist as historie, Var_filial_nr as filial_nr;


END$$

DROP PROCEDURE IF EXISTS `addKunden`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `addKunden`(
Var_name varchar(64),
Var_strasse varchar(250),
Var_nr varchar(10),
Var_plz char(5),
Var_ort varchar(64),
Var_stadtteil varchar(64),
Var_telefon varchar(45),
Var_fax varchar(45),
Var_e_mail varchar(264),
Var_adresszusatz varchar(250),
Var_filial_nr int,
Var_user varchar(15)
)
root:BEGIN

Declare Var_anzahl int;
Declare Var_anzahl_hist int;
Declare Var_kunden_nr int;

/*DECLARE Var_lfn int;*/
DECLARE Var_feld varchar(264);
DECLARE Var_ainhalt varchar(264);
DECLARE Var_ninhalt varchar(264);
DECLARE Var_code char(3);

/*set Var_kunden_nr = (Select max(kunden_nr) from erdodb.kunden) + 1;*/

Start Transaction;
Insert into erdodb.kunden
(-- name,
strasse ,
nr,
plz,
ort,
stadtteil,
adresszusatz,
aktiv,
telefon,
fax,
email,
filial_nr)
 Values(
/*Var_kunden_nr,*/
-- Var_name,
Var_strasse ,
Var_nr,
Var_plz,
Var_ort,
Var_stadtteil,
Var_adresszusatz,
1,
Var_telefon,
Var_fax,
Var_e_mail,
Var_filial_nr);

set Var_anzahl = ROW_COUNT();

Set Var_code = '001';

Set Var_kunden_nr = (SELECT concat(Var_filial_nr, Var_plz, IFNULL(MAX(lfd_nr),0)) FROM kunden);

/*Set Var_lfn = (SELECT IFNULL(MAX(lfn),0) FROM hist_kunden)+1;*/
	  Set Var_feld =  'NEU';
	  Set Var_ainhalt =  NULL;
	  Set Var_ninhalt =  NULL;
	  
      INSERT INTO hist_kunden
        (/*lfn, */schluessel, name, codetext, user, aenderdat, feld, a_inhalt, n_inhalt)
      VALUES
        (/*Var_lfn,*/ Var_kunden_nr, Var_name, Var_code, Var_user, curtime(), Var_feld, Var_ainhalt, Var_ninhalt);

set Var_anzahl_hist = ROW_COUNT();

 IF Var_anzahl != 1 OR Var_anzahl_hist != 1
 Then 
 Rollback;
 Select Var_anzahl as ergebnis, Var_anzahl_hist as historie, Var_kunden_nr as kunden_nr;
 Leave root;
 Else 
 Commit;
 End IF;
commit;

Select Var_anzahl as ergebnis, Var_anzahl_hist as historie, Var_kunden_nr as kunden_nr;


END$$

DROP PROCEDURE IF EXISTS `addProdukte`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `addProdukte`(
Var_prod_kz char(4),
Var_bezeichnung varchar(64),
Var_netto_preis1 decimal(8,2),
Var_netto_preis2 decimal(8,2),
Var_mwst decimal(6,2),
Var_user varchar(15)
)
root:BEGIN

Declare Var_anzahl int;
Declare Var_anzahl_hist int;

DECLARE Var_lfn int;
DECLARE Var_feld varchar(264);
DECLARE Var_ainhalt varchar(264);
DECLARE Var_ninhalt varchar(264);
DECLARE Var_code char(3);

If exists (Select * from erdodb.produkte where prod_kz = Var_prod_kz)
Then
Select -99 as ergebnis, -99 as historie;
Leave root;
End If;

/*If exists (Select * from erdodb.hist_produkte where schluessel = Var_prod_kz)
Then
Select -98 as ergebnis, -98 as historie;
Leave root;
End If;*/

Start Transaction;
Insert into erdodb.produkte
(prod_kz, bezeichnung, netto_preis1, netto_preis2, mwst)
Values
(Var_prod_kz, Var_bezeichnung, Var_netto_preis1, Var_netto_preis2, Var_mwst);

set Var_anzahl = ROW_COUNT();

Set Var_code = '004';

Set Var_lfn = (SELECT IFNULL(MAX(lfn),0) FROM hist_produkte)+1;
	  Set Var_feld =  'NEU';
	  Set Var_ainhalt =  NULL;
	  Set Var_ninhalt =  NULL;
	  
      INSERT INTO hist_produkte
        (lfn, schluessel, name, codetext, user, aenderdat, feld, a_inhalt, n_inhalt)
      VALUES
        (Var_lfn, Var_prod_kz, Var_bezeichnung, Var_code, Var_user, curtime(), Var_feld, Var_ainhalt, Var_ninhalt);

set Var_anzahl_hist = ROW_COUNT();

 IF Var_anzahl != 1 OR Var_anzahl_hist != 1
 Then 
 Rollback;
 Select Var_anzahl as ergebnis, Var_anzahl_hist as historie;
 Leave root;
 Else 
 Commit;
 End IF;
commit;

Select Var_anzahl as ergebnis, Var_anzahl_hist as historie;

END$$

DROP PROCEDURE IF EXISTS `belegNrBerechnung`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `belegNrBerechnung`()
begin

declare letzteNr int;
declare belegNr varchar(45);

set letzteNr = (select cast(substring(beleg_nr,6) as unsigned) from verkaeufe order by cast(substring(beleg_nr,6) as unsigned) desc LIMIT 1) +1;
set belegNr = concat(year(curdate()),'/',letzteNr);

Select Ifnull(belegNr,concat(year(curdate()),'/',1)) as beleg_nr;

End$$

DROP PROCEDURE IF EXISTS `deleteAbrechnung`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `deleteAbrechnung`(
Var_lfd_nr int,
Var_prod_kz char(4),
Var_prod_bez varchar(64),
Var_kunden_nr int,
Var_kunden_name varchar(64),
Var_user varchar(15)
)
root:BEGIN

DECLARE Var_anzahl int;
DECLARE Var_anzahl_hist int;

DECLARE Var_lfn int;
DECLARE Var_feld varchar(264);
DECLARE Var_ainhalt varchar(264);
DECLARE Var_ninhalt varchar(264);
DECLARE Var_code char(3);



Start transaction;
Delete from erdodb.verkaeufe where lfd_nr = Var_lfd_nr;

set Var_anzahl = ROW_COUNT();

Set Var_code = '010';

	  Set Var_feld =  'GELÖSCHT';
	  Set Var_ainhalt =  NULL;
	  Set Var_ninhalt =  NULL;
	  
      INSERT INTO hist_abrechnung
        (lfd_nr, kunden_nr, prod_kz, prod_bez, kunden_name, codetext, user, aenderdat, feld, a_inhalt, n_inhalt)
      VALUES
        (Var_lfd_nr, Var_kunden_nr, Var_prod_kz, Var_prod_bez, Var_kunden_name, Var_code, Var_user, curtime(), Var_feld, Var_ainhalt, Var_ninhalt);

set Var_anzahl_hist = ROW_COUNT();

 IF Var_anzahl != 1 OR Var_anzahl_hist != 1
 Then 
 Rollback;
 Select Var_anzahl as ergebnis, Var_anzahl_hist as historie, Var_kunden_nr as kunden_nr;
 Leave root;
 Else 
 Commit;
 End IF;
commit;

Select Var_anzahl as ergebnis, Var_anzahl_hist as historie, Var_kunden_nr as kunden_nr;

END$$

DROP PROCEDURE IF EXISTS `deleteFiliale`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `deleteFiliale`(
Var_filial_nr int, Var_user varchar(15)
)
root:BEGIN

DECLARE Var_anzahl int;
DECLARE Var_anzahl_hist int;

DECLARE Var_lfn int;
DECLARE Var_feld varchar(264);
DECLARE Var_ainhalt varchar(264);
DECLARE Var_ninhalt varchar(264);
DECLARE Var_code char(3);

If exists (Select * from erdodb.kunden where filial_nr = Var_filial_nr)
Then
Select -99 as ergebnis, -99 as historie, Var_filial_nr as filial_nr;
Leave root;
End If;

Start transaction;
Delete from erdodb.filialen where filial_nr = Var_filial_nr;

set Var_anzahl = ROW_COUNT();

Set Var_code = '013';

	  Set Var_feld =  'GELÖSCHT';
	  Set Var_ainhalt =  NULL;
	  Set Var_ninhalt =  NULL;
	  
      INSERT INTO hist_kunden
        (schluessel, codetext, user, aenderdat, feld, a_inhalt, n_inhalt)
      VALUES
        (Var_filial_nr, Var_code, Var_user, curtime(), Var_feld, Var_ainhalt, Var_ninhalt);

set Var_anzahl_hist = ROW_COUNT();

 IF Var_anzahl != 1 OR Var_anzahl_hist != 1
 Then 
 Rollback;
 Select Var_anzahl as ergebnis, Var_anzahl_hist as historie, Var_filial_nr as filial_nr;
 Leave root;
 Else 
 Commit;
 End IF;
commit;

Select Var_anzahl as ergebnis, Var_anzahl_hist as historie, Var_filial_nr as filial_nr;

END$$

DROP PROCEDURE IF EXISTS `deleteKunden`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `deleteKunden`(
Var_kunden_nr int, Var_user varchar(15)
)
root:BEGIN

DECLARE Var_anzahl int;
DECLARE Var_anzahl_hist int;

DECLARE Var_lfn int;
DECLARE Var_feld varchar(264);
DECLARE Var_ainhalt varchar(264);
DECLARE Var_ninhalt varchar(264);
DECLARE Var_code char(3);

If exists (Select * from erdodb.verkaeufe where verkauf_an = Var_kunden_nr)
Then
Select -99 as ergebnis, -99 as historie, Var_kunden_nr as kunden_nr;
Leave root;
End If;

Start transaction;
Delete from erdodb.kunden where concat(filial_nr,plz,lfd_nr) = Var_kunden_nr;

set Var_anzahl = ROW_COUNT();

Set Var_code = '003';

	  Set Var_feld =  'GELÖSCHT';
	  Set Var_ainhalt =  NULL;
	  Set Var_ninhalt =  NULL;
	  
      INSERT INTO hist_kunden
        (schluessel, codetext, user, aenderdat, feld, a_inhalt, n_inhalt)
      VALUES
        (Var_kunden_nr, Var_code, Var_user, curtime(), Var_feld, Var_ainhalt, Var_ninhalt);

set Var_anzahl_hist = ROW_COUNT();

 IF Var_anzahl != 1 OR Var_anzahl_hist != 1
 Then 
 Rollback;
 Select Var_anzahl as ergebnis, Var_anzahl_hist as historie, Var_kunden_nr as kunden_nr;
 Leave root;
 Else 
 Commit;
 End IF;
commit;

Select Var_anzahl as ergebnis, Var_anzahl_hist as historie, Var_kunden_nr as kunden_nr;

END$$

DROP PROCEDURE IF EXISTS `deleteProdukte`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `deleteProdukte`(
Var_prod_kz char(4), Var_user varchar(15)
)
root:BEGIN

DECLARE Var_anzahl int;
DECLARE Var_anzahl_hist int;

DECLARE Var_lfn int;
DECLARE Var_feld varchar(264);
DECLARE Var_ainhalt varchar(264);
DECLARE Var_ninhalt varchar(264);
DECLARE Var_code char(3);

If exists (Select * from erdodb.verkaeufe where prod_kz = Var_prod_kz)
Then
Select -99 as ergebnis, -99 as historie, Var_prod_kz as prod_kz;
Leave root;
End If;

Start transaction;
Delete from erdodb.produkte where prod_kz = Var_prod_kz;

set Var_anzahl = ROW_COUNT();

Set Var_code = '006';

	  Set Var_feld =  'GELÖSCHT';
	  Set Var_ainhalt =  NULL;
	  Set Var_ninhalt =  NULL;
	  
      INSERT INTO hist_produkte
        (schluessel, codetext, user, aenderdat, feld, a_inhalt, n_inhalt)
      VALUES
        (Var_prod_kz, Var_code, Var_user, curtime(), Var_feld, Var_ainhalt, Var_ninhalt);

set Var_anzahl_hist = ROW_COUNT();

 IF Var_anzahl != 1 OR Var_anzahl_hist != 1
 Then 
   Rollback;
   Select Var_anzahl as ergebnis, Var_anzahl_hist as historie, Var_prod_kz as prod_kz;
   Leave root;
 Else 
   Commit;
 End IF;

 commit;

Select Var_anzahl as ergebnis, Var_anzahl_hist as historie, Var_prod_kz as prod_kz;

END$$

DROP PROCEDURE IF EXISTS `editAbrechnung`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `editAbrechnung`(
Var_lfd_nr int,
Var_prod_kz char(4),
Var_prod_bez varchar(64),
Var_kunden_nr int,
Var_kunden_name varchar(64),
Var_menge int,
Var_preis_kat tinyint,
Var_netto_preis DECIMAL(8,2),
Var_netto_gesamt_preis DECIMAL(8,2),
Var_mwst DECIMAL(5,2),
Var_datum datetime,
Var_beleg_nr varchar(45),
Var_bemerkung varchar(260),
Var_user varchar(15)
)
root:BEGIN

Declare Var_anzahl int;
Declare Var_anzahl_hist int;

/*DECLARE Var_lfn int;*/
DECLARE Var_feld varchar(264);
DECLARE Var_ainhalt varchar(264);
DECLARE Var_ninhalt varchar(264);
DECLARE Var_code char(3);


DECLARE Vor_prod_kz char(4);
DECLARE Vor_kunden_nr int;
DECLARE Vor_menge int;
DECLARE Vor_preis_kat tinyint;
DECLARE Vor_netto_preis DECIMAL(8,2);
DECLARE Vor_netto_gesamt_preis DECIMAL(8,2);
DECLARE Vor_mwst DECIMAL(5,2);
DECLARE Vor_datum datetime;
DECLARE Vor_beleg_nr varchar(45);
DECLARE Vor_bemerkung varchar(260);
/*DECLARE Vor_kunden_name varchar(64);
DECLARE Vor_prod_bez varchar(64);*/


DECLARE Nach_prod_kz char(4);
DECLARE Nach_kunden_nr int;
DECLARE Nach_menge int;
DECLARE Nach_preis_kat tinyint;
DECLARE Nach_netto_preis DECIMAL(8,2);
DECLARE Nach_netto_gesamt_preis DECIMAL(8,2);
DECLARE Nach_mwst DECIMAL(5,2);
DECLARE Nach_datum datetime;
DECLARE Nach_beleg_nr varchar(45);
DECLARE Nach_bemerkung varchar(260);
/*DECLARE Nach_kunden_name varchar(64);
DECLARE Nach_prod_bez varchar(64);*/

/*Es dürfen keine unterschiedlichen Kunden auf der selben Belegnr. sein */
IF exists (select * from verkaeufe where beleg_nr = Var_beleg_nr)
then 
  If  (select distinct verkauf_an from verkaeufe Where beleg_nr = Var_beleg_nr) != Var_kunden_nr
  Then
  Select -99 as ergebnis, -99 as historie, -99 as lfd_nr;
  Leave root;
  Else
  If  (select distinct DATE_FORMAT(datum,GET_FORMAT(DATE,'EUR')) from verkaeufe Where beleg_nr = Var_beleg_nr) != DATE_FORMAT(Var_datum,GET_FORMAT(DATE,'EUR'))
  Then
  Select -98 as ergebnis, -98 as historie, -98 as lfd_nr;
  Leave root;
  Else
  Set Vor_prod_kz = (Select prod_kz from verkaeufe where lfd_nr = Var_lfd_nr);
  Set Vor_kunden_nr = (Select verkauf_an from verkaeufe where lfd_nr = Var_lfd_nr);
  Set Vor_menge = (Select menge from verkaeufe where lfd_nr = Var_lfd_nr);
  Set Vor_preis_kat = (Select preis_kat from verkaeufe where lfd_nr = Var_lfd_nr);
  Set Vor_netto_preis = (Select einzelpr_netto from verkaeufe where lfd_nr = Var_lfd_nr);
  Set Vor_netto_gesamt_preis = (Select gesamtpr_netto from verkaeufe where lfd_nr = Var_lfd_nr);
  Set Vor_mwst = (Select mwst from verkaeufe where lfd_nr = Var_lfd_nr);
  Set Vor_datum = (Select datum from verkaeufe where lfd_nr = Var_lfd_nr);
  Set Vor_beleg_nr = (Select beleg_nr from verkaeufe where lfd_nr = Var_lfd_nr);
  Set Vor_bemerkung = (Select bemerkung from verkaeufe where lfd_nr = Var_lfd_nr);
  
  
  Start Transaction;
UPDATE erdodb.verkaeufe 
set 
    prod_kz = Var_prod_kz,
    verkauf_an = Var_kunden_nr,
    menge = Var_menge,
    preis_kat = Var_preis_kat,
    datum = Var_datum,
    bemerkung = Var_bemerkung,
    beleg_nr = Var_beleg_nr,
    mwst = Var_mwst,
    einzelpr_netto = Var_netto_preis,
    gesamtpr_netto = Var_netto_gesamt_preis
Where
    lfd_nr = Var_lfd_nr;

  set Var_anzahl = ROW_COUNT();

  Set Nach_prod_kz = (Select prod_kz from verkaeufe where lfd_nr = Var_lfd_nr);
  Set Nach_kunden_nr = (Select verkauf_an from verkaeufe where lfd_nr = Var_lfd_nr);
  Set Nach_menge = (Select menge from verkaeufe where lfd_nr = Var_lfd_nr);
  Set Nach_preis_kat = (Select preis_kat from verkaeufe where lfd_nr = Var_lfd_nr);
  Set Nach_netto_preis = (Select einzelpr_netto from verkaeufe where lfd_nr = Var_lfd_nr);
  Set Nach_netto_gesamt_preis = (Select gesamtpr_netto from verkaeufe where lfd_nr = Var_lfd_nr);
  Set Nach_mwst = (Select mwst from verkaeufe where lfd_nr = Var_lfd_nr);
  Set Nach_datum = (Select datum from verkaeufe where lfd_nr = Var_lfd_nr);
  Set Nach_beleg_nr = (Select beleg_nr from verkaeufe where lfd_nr = Var_lfd_nr);
  Set Nach_bemerkung = (Select bemerkung from verkaeufe where lfd_nr = Var_lfd_nr);

  Set Var_code = '009';


	  Set Var_feld =  'prod_kz';
	  Set Var_ainhalt =  Vor_prod_kz;
	  Set Var_ninhalt =  Nach_prod_kz;
	  
	  If Vor_prod_kz != Nach_prod_kz
	  Then
        INSERT INTO hist_abrechnung
          (lfd_nr, kunden_nr, prod_kz, prod_bez, kunden_name, codetext, user, aenderdat, feld, a_inhalt, n_inhalt)
        VALUES
          (Var_lfd_nr, Var_kunden_nr, Var_prod_kz, Var_prod_bez, Var_kunden_name, Var_code, Var_user, curtime(), Var_feld, Var_ainhalt, Var_ninhalt);
	  End if;

      set Var_anzahl_hist = ROW_COUNT();
	  
	  Set Var_feld =  'verkauf_an';
	  Set Var_ainhalt =  Vor_kunden_nr;
	  Set Var_ninhalt =  Nach_kunden_nr;	
  
	  If Vor_kunden_nr != Nach_kunden_nr
	  Then
        INSERT INTO hist_abrechnung
          (lfd_nr, kunden_nr, prod_kz, prod_bez, kunden_name, codetext, user, aenderdat, feld, a_inhalt, n_inhalt)
        VALUES
          (Var_lfd_nr, Var_kunden_nr, Var_prod_kz, Var_prod_bez, Var_kunden_name, Var_code, Var_user, curtime(), Var_feld, Var_ainhalt, Var_ninhalt);
	  End if;

      set Var_anzahl_hist = Var_anzahl_hist + ROW_COUNT();
	  
	  Set Var_feld =  'menge';
	  Set Var_ainhalt =  Vor_menge;
	  Set Var_ninhalt =  Nach_menge;	  
	  If Nach_menge != Vor_menge
	  Then
        INSERT INTO hist_abrechnung
          (lfd_nr, kunden_nr, prod_kz, prod_bez, kunden_name, codetext, user, aenderdat, feld, a_inhalt, n_inhalt)
        VALUES
          (Var_lfd_nr, Var_kunden_nr, Var_prod_kz, Var_prod_bez, Var_kunden_name, Var_code, Var_user, curtime(), Var_feld, Var_ainhalt, Var_ninhalt);
	  End if;

      set Var_anzahl_hist = Var_anzahl_hist + ROW_COUNT();
	  
	  Set Var_feld =  'preis_kat';
	  Set Var_ainhalt =  Vor_preis_kat;
	  Set Var_ninhalt =  Nach_preis_kat;	  
	  If Nach_preis_kat != Vor_preis_kat
	  Then
        INSERT INTO hist_abrechnung
          (lfd_nr, kunden_nr, prod_kz, prod_bez, kunden_name, codetext, user, aenderdat, feld, a_inhalt, n_inhalt)
        VALUES
          (Var_lfd_nr, Var_kunden_nr, Var_prod_kz, Var_prod_bez, Var_kunden_name, Var_code, Var_user, curtime(), Var_feld, Var_ainhalt, Var_ninhalt);
	  End if;

      set Var_anzahl_hist = Var_anzahl_hist + ROW_COUNT();
	  
	  Set Var_feld =  'datum';
	  Set Var_ainhalt =  Vor_datum;
	  Set Var_ninhalt =  Nach_datum;	  
	  If Vor_datum != Nach_datum
	  Then
        INSERT INTO hist_abrechnung
          (lfd_nr, kunden_nr, prod_kz, prod_bez, kunden_name, codetext, user, aenderdat, feld, a_inhalt, n_inhalt)
        VALUES
          (Var_lfd_nr, Var_kunden_nr, Var_prod_kz, Var_prod_bez, Var_kunden_name, Var_code, Var_user, curtime(), Var_feld, Var_ainhalt, Var_ninhalt);
	  End if;

      set Var_anzahl_hist = Var_anzahl_hist + ROW_COUNT();
	  
	  Set Var_feld =  'bemerkung';
	  Set Var_ainhalt =  Vor_bemerkung;
	  Set Var_ninhalt =  Nach_bemerkung;	  
	  If Vor_bemerkung != Nach_bemerkung
	  Then
        INSERT INTO hist_abrechnung
          (lfd_nr, kunden_nr, prod_kz, prod_bez, kunden_name, codetext, user, aenderdat, feld, a_inhalt, n_inhalt)
        VALUES
          (Var_lfd_nr, Var_kunden_nr, Var_prod_kz, Var_prod_bez, Var_kunden_name, Var_code, Var_user, curtime(), Var_feld, Var_ainhalt, Var_ninhalt);
	  End if;

      set Var_anzahl_hist = Var_anzahl_hist + ROW_COUNT();
	  
	  Set Var_feld =  'beleg_nr';
	  Set Var_ainhalt =  Vor_beleg_nr;
	  Set Var_ninhalt =  Nach_beleg_nr;	  
	  If Vor_beleg_nr != Nach_beleg_nr
	  Then
        INSERT INTO hist_abrechnung
          (lfd_nr, kunden_nr, prod_kz, prod_bez, kunden_name, codetext, user, aenderdat, feld, a_inhalt, n_inhalt)
        VALUES
          (Var_lfd_nr, Var_kunden_nr, Var_prod_kz, Var_prod_bez, Var_kunden_name, Var_code, Var_user, curtime(), Var_feld, Var_ainhalt, Var_ninhalt);
	  End if;

      set Var_anzahl_hist = Var_anzahl_hist + ROW_COUNT();
	  
	  Set Var_feld =  'mwst';
	  Set Var_ainhalt =  Vor_mwst;
	  Set Var_ninhalt =  Nach_mwst;	  
	  If Vor_mwst != Nach_mwst
	  Then
        INSERT INTO hist_abrechnung
          (lfd_nr, kunden_nr, prod_kz, prod_bez, kunden_name, codetext, user, aenderdat, feld, a_inhalt, n_inhalt)
        VALUES
          (Var_lfd_nr, Var_kunden_nr, Var_prod_kz, Var_prod_bez, Var_kunden_name, Var_code, Var_user, curtime(), Var_feld, Var_ainhalt, Var_ninhalt);
	  End if;

      set Var_anzahl_hist = Var_anzahl_hist + ROW_COUNT();
	  
	  Set Var_feld =  'einzelpr_netto';
	  Set Var_ainhalt =  Vor_netto_preis;
	  Set Var_ninhalt =  Nach_netto_preis;	  
	  If Vor_netto_preis != Nach_netto_preis
	  Then
        INSERT INTO hist_abrechnung
          (lfd_nr, kunden_nr, prod_kz, prod_bez, kunden_name, codetext, user, aenderdat, feld, a_inhalt, n_inhalt)
        VALUES
          (Var_lfd_nr, Var_kunden_nr, Var_prod_kz, Var_prod_bez, Var_kunden_name, Var_code, Var_user, curtime(), Var_feld, Var_ainhalt, Var_ninhalt);
	  End if;

      set Var_anzahl_hist = Var_anzahl_hist + ROW_COUNT();
	  
	  Set Var_feld =  'gesamtpr_netto';
	  Set Var_ainhalt =  Vor_netto_gesamt_preis;
	  Set Var_ninhalt =  Nach_netto_gesamt_preis;	  
	  If Vor_netto_gesamt_preis != Nach_netto_gesamt_preis
	  Then
        INSERT INTO hist_abrechnung
          (lfd_nr, kunden_nr, prod_kz, prod_bez, kunden_name, codetext, user, aenderdat, feld, a_inhalt, n_inhalt)
        VALUES
          (Var_lfd_nr, Var_kunden_nr, Var_prod_kz, Var_prod_bez, Var_kunden_name, Var_code, Var_user, curtime(), Var_feld, Var_ainhalt, Var_ninhalt);
	  End if;

      set Var_anzahl_hist = Var_anzahl_hist + ROW_COUNT();

  IF Var_anzahl != 1 OR Var_anzahl_hist < 1
  Then 
  Rollback;
Select 
    Var_anzahl as ergebnis,
    Var_anzahl_hist as historie,
    'fail' as lfd_nr;
  Leave root;
  Else 
  Commit;
  End IF;
  commit;

Select 
    Var_anzahl as ergebnis,
    Var_anzahl_hist as historie,
    Var_lfd_nr as lfd_nr;
  End if;
  End if;
Else   
  Set Vor_prod_kz = (Select prod_kz from verkaeufe where lfd_nr = Var_lfd_nr);
  Set Vor_kunden_nr = (Select verkauf_an from verkaeufe where lfd_nr = Var_lfd_nr);
  Set Vor_menge = (Select menge from verkaeufe where lfd_nr = Var_lfd_nr);
  Set Vor_preis_kat = (Select preis_kat from verkaeufe where lfd_nr = Var_lfd_nr);
  Set Vor_netto_preis = (Select einzelpr_netto from verkaeufe where lfd_nr = Var_lfd_nr);
  Set Vor_netto_gesamt_preis = (Select gesamtpr_netto from verkaeufe where lfd_nr = Var_lfd_nr);
  Set Vor_mwst = (Select mwst from verkaeufe where lfd_nr = Var_lfd_nr);
  Set Vor_datum = (Select datum from verkaeufe where lfd_nr = Var_lfd_nr);
  Set Vor_beleg_nr = (Select beleg_nr from verkaeufe where lfd_nr = Var_lfd_nr);
  Set Vor_bemerkung = (Select bemerkung from verkaeufe where lfd_nr = Var_lfd_nr);
  
  
  Start Transaction;
UPDATE erdodb.verkaeufe 
set 
    prod_kz = Var_prod_kz,
    verkauf_an = Var_kunden_nr,
    menge = Var_menge,
    preis_kat = Var_preis_kat,
    datum = Var_datum,
    bemerkung = Var_bemerkung,
    beleg_nr = Var_beleg_nr,
    mwst = Var_mwst,
    einzelpr_netto = Var_netto_preis,
    gesamtpr_netto = Var_netto_gesamt_preis
Where
    lfd_nr = Var_lfd_nr;

  set Var_anzahl = ROW_COUNT();

  Set Nach_prod_kz = (Select prod_kz from verkaeufe where lfd_nr = Var_lfd_nr);
  Set Nach_kunden_nr = (Select verkauf_an from verkaeufe where lfd_nr = Var_lfd_nr);
  Set Nach_menge = (Select menge from verkaeufe where lfd_nr = Var_lfd_nr);
  Set Nach_preis_kat = (Select preis_kat from verkaeufe where lfd_nr = Var_lfd_nr);
  Set Nach_netto_preis = (Select einzelpr_netto from verkaeufe where lfd_nr = Var_lfd_nr);
  Set Nach_netto_gesamt_preis = (Select gesamtpr_netto from verkaeufe where lfd_nr = Var_lfd_nr);
  Set Nach_mwst = (Select mwst from verkaeufe where lfd_nr = Var_lfd_nr);
  Set Nach_datum = (Select datum from verkaeufe where lfd_nr = Var_lfd_nr);
  Set Nach_beleg_nr = (Select beleg_nr from verkaeufe where lfd_nr = Var_lfd_nr);
  Set Nach_bemerkung = (Select bemerkung from verkaeufe where lfd_nr = Var_lfd_nr);

  Set Var_code = '009';


	  Set Var_feld =  'prod_kz';
	  Set Var_ainhalt =  Vor_prod_kz;
	  Set Var_ninhalt =  Nach_prod_kz;
	  
	  If Vor_prod_kz != Nach_prod_kz
	  Then
        INSERT INTO hist_abrechnung
          (lfd_nr, kunden_nr, prod_kz, prod_bez, kunden_name, codetext, user, aenderdat, feld, a_inhalt, n_inhalt)
        VALUES
          (Var_lfd_nr, Var_kunden_nr, Var_prod_kz, Var_prod_bez, Var_kunden_name, Var_code, Var_user, curtime(), Var_feld, Var_ainhalt, Var_ninhalt);
	  End if;

      set Var_anzahl_hist = ROW_COUNT();
	  
	  Set Var_feld =  'verkauf_an';
	  Set Var_ainhalt =  Vor_kunden_nr;
	  Set Var_ninhalt =  Nach_kunden_nr;	
  
	  If Vor_kunden_nr != Nach_kunden_nr
	  Then
        INSERT INTO hist_abrechnung
          (lfd_nr, kunden_nr, prod_kz, prod_bez, kunden_name, codetext, user, aenderdat, feld, a_inhalt, n_inhalt)
        VALUES
          (Var_lfd_nr, Var_kunden_nr, Var_prod_kz, Var_prod_bez, Var_kunden_name, Var_code, Var_user, curtime(), Var_feld, Var_ainhalt, Var_ninhalt);
	  End if;

      set Var_anzahl_hist = Var_anzahl_hist + ROW_COUNT();
	  
	  Set Var_feld =  'menge';
	  Set Var_ainhalt =  Vor_menge;
	  Set Var_ninhalt =  Nach_menge;	  
	  If Nach_menge != Vor_menge
	  Then
        INSERT INTO hist_abrechnung
          (lfd_nr, kunden_nr, prod_kz, prod_bez, kunden_name, codetext, user, aenderdat, feld, a_inhalt, n_inhalt)
        VALUES
          (Var_lfd_nr, Var_kunden_nr, Var_prod_kz, Var_prod_bez, Var_kunden_name, Var_code, Var_user, curtime(), Var_feld, Var_ainhalt, Var_ninhalt);
	  End if;

      set Var_anzahl_hist = Var_anzahl_hist + ROW_COUNT();
	  
	  Set Var_feld =  'preis_kat';
	  Set Var_ainhalt =  Vor_preis_kat;
	  Set Var_ninhalt =  Nach_preis_kat;	  
	  If Nach_preis_kat != Vor_preis_kat
	  Then
        INSERT INTO hist_abrechnung
          (lfd_nr, kunden_nr, prod_kz, prod_bez, kunden_name, codetext, user, aenderdat, feld, a_inhalt, n_inhalt)
        VALUES
          (Var_lfd_nr, Var_kunden_nr, Var_prod_kz, Var_prod_bez, Var_kunden_name, Var_code, Var_user, curtime(), Var_feld, Var_ainhalt, Var_ninhalt);
	  End if;

      set Var_anzahl_hist = Var_anzahl_hist + ROW_COUNT();
	  
	  Set Var_feld =  'datum';
	  Set Var_ainhalt =  Vor_datum;
	  Set Var_ninhalt =  Nach_datum;	  
	  If Vor_datum != Nach_datum
	  Then
        INSERT INTO hist_abrechnung
          (lfd_nr, kunden_nr, prod_kz, prod_bez, kunden_name, codetext, user, aenderdat, feld, a_inhalt, n_inhalt)
        VALUES
          (Var_lfd_nr, Var_kunden_nr, Var_prod_kz, Var_prod_bez, Var_kunden_name, Var_code, Var_user, curtime(), Var_feld, Var_ainhalt, Var_ninhalt);
	  End if;

      set Var_anzahl_hist = Var_anzahl_hist + ROW_COUNT();
	  
	  Set Var_feld =  'bemerkung';
	  Set Var_ainhalt =  Vor_bemerkung;
	  Set Var_ninhalt =  Nach_bemerkung;	  
	  If Vor_bemerkung != Nach_bemerkung
	  Then
        INSERT INTO hist_abrechnung
          (lfd_nr, kunden_nr, prod_kz, prod_bez, kunden_name, codetext, user, aenderdat, feld, a_inhalt, n_inhalt)
        VALUES
          (Var_lfd_nr, Var_kunden_nr, Var_prod_kz, Var_prod_bez, Var_kunden_name, Var_code, Var_user, curtime(), Var_feld, Var_ainhalt, Var_ninhalt);
	  End if;

      set Var_anzahl_hist = Var_anzahl_hist + ROW_COUNT();
	  
	  Set Var_feld =  'beleg_nr';
	  Set Var_ainhalt =  Vor_beleg_nr;
	  Set Var_ninhalt =  Nach_beleg_nr;	  
	  If Vor_beleg_nr != Nach_beleg_nr
	  Then
        INSERT INTO hist_abrechnung
          (lfd_nr, kunden_nr, prod_kz, prod_bez, kunden_name, codetext, user, aenderdat, feld, a_inhalt, n_inhalt)
        VALUES
          (Var_lfd_nr, Var_kunden_nr, Var_prod_kz, Var_prod_bez, Var_kunden_name, Var_code, Var_user, curtime(), Var_feld, Var_ainhalt, Var_ninhalt);
	  End if;

      set Var_anzahl_hist = Var_anzahl_hist + ROW_COUNT();
	  
	  Set Var_feld =  'mwst';
	  Set Var_ainhalt =  Vor_mwst;
	  Set Var_ninhalt =  Nach_mwst;	  
	  If Vor_mwst != Nach_mwst
	  Then
        INSERT INTO hist_abrechnung
          (lfd_nr, kunden_nr, prod_kz, prod_bez, kunden_name, codetext, user, aenderdat, feld, a_inhalt, n_inhalt)
        VALUES
          (Var_lfd_nr, Var_kunden_nr, Var_prod_kz, Var_prod_bez, Var_kunden_name, Var_code, Var_user, curtime(), Var_feld, Var_ainhalt, Var_ninhalt);
	  End if;

      set Var_anzahl_hist = Var_anzahl_hist + ROW_COUNT();
	  
	  Set Var_feld =  'einzelpr_netto';
	  Set Var_ainhalt =  Vor_netto_preis;
	  Set Var_ninhalt =  Nach_netto_preis;	  
	  If Vor_netto_preis != Nach_netto_preis
	  Then
        INSERT INTO hist_abrechnung
          (lfd_nr, kunden_nr, prod_kz, prod_bez, kunden_name, codetext, user, aenderdat, feld, a_inhalt, n_inhalt)
        VALUES
          (Var_lfd_nr, Var_kunden_nr, Var_prod_kz, Var_prod_bez, Var_kunden_name, Var_code, Var_user, curtime(), Var_feld, Var_ainhalt, Var_ninhalt);
	  End if;

      set Var_anzahl_hist = Var_anzahl_hist + ROW_COUNT();
	  
	  Set Var_feld =  'gesamtpr_netto';
	  Set Var_ainhalt =  Vor_netto_gesamt_preis;
	  Set Var_ninhalt =  Nach_netto_gesamt_preis;	  
	  If Vor_netto_gesamt_preis != Nach_netto_gesamt_preis
	  Then
        INSERT INTO hist_abrechnung
          (lfd_nr, kunden_nr, prod_kz, prod_bez, kunden_name, codetext, user, aenderdat, feld, a_inhalt, n_inhalt)
        VALUES
          (Var_lfd_nr, Var_kunden_nr, Var_prod_kz, Var_prod_bez, Var_kunden_name, Var_code, Var_user, curtime(), Var_feld, Var_ainhalt, Var_ninhalt);
	  End if;

      set Var_anzahl_hist = Var_anzahl_hist + ROW_COUNT();

  IF Var_anzahl != 1 OR Var_anzahl_hist < 1
  Then 
  Rollback;
Select 
    Var_anzahl as ergebnis,
    Var_anzahl_hist as historie,
    'fail' as lfd_nr;
  Leave root;
  Else 
  Commit;
  End IF;
  commit;

Select 
    Var_anzahl as ergebnis,
    Var_anzahl_hist as historie,
    Var_lfd_nr as lfd_nr;
End if;
END$$

DROP PROCEDURE IF EXISTS `editFiliale`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `editFiliale`(IN `Var_name` varchar(64),IN `Var_filial_nr` int, IN `Var_user` varchar(15))
root:BEGIN

Declare Var_anzahl int;
Declare Var_anzahl_hist int;

Declare Vor_name varchar(64); 
Declare Nach_name varchar(64); 

/*DECLARE Var_lfn int;*/
DECLARE Var_feld varchar(264);
DECLARE Var_ainhalt varchar(264);
DECLARE Var_ninhalt varchar(264);
DECLARE Var_code char(3);


Set Vor_name = (Select name From erdodb.filialen Where filial_nr = Var_filial_nr);

Start Transaction;
Update erdodb.filialen set
name = Var_name
Where filial_nr = Var_filial_nr;

set Var_anzahl = ROW_COUNT();

Set Nach_name = (Select name From erdodb.filialen Where filial_nr = Var_filial_nr);


Set Var_code = '012';
    
    -- Beginn mit den Einträgen in die Historie

IF Vor_name != Nach_name
    Then
      /*Set Var_lfn = (SELECT IFNULL(MAX(lfn),0) FROM hist_kunden)+1;*/
	  Set Var_feld =  'name';
	  Set Var_ainhalt =  Vor_name;
	  Set Var_ninhalt =  Nach_name;
	  
      INSERT INTO hist_kunden
        (/*lfn,*/ schluessel, name,  codetext, user, aenderdat, feld, a_inhalt, n_inhalt)
      VALUES
        (/*Var_lfn,*/ Var_filial_nr, Var_name,  Var_code, Var_user, curtime(), Var_feld, Var_ainhalt, Var_ninhalt);
    END IF;

set Var_anzahl_hist = ROW_COUNT();


 IF Var_anzahl != 1 OR Var_anzahl_hist < 1
 Then 
 Rollback;
 Select Var_anzahl as ergebnis, Var_anzahl_hist as historie, Nach_name as name, Var_filial_nr as filial_nr;
 Leave root;
 Else 
 Commit;
 End IF;
commit;
Select Var_anzahl as ergebnis, Var_anzahl_hist as historie, Nach_name as name, Var_filial_nr as filial_nr;


END$$

DROP PROCEDURE IF EXISTS `editKunden`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `editKunden`(IN `Var_lfd_nr` int, IN `Var_name` varchar(64), IN `Var_strasse` varchar(250), IN `Var_nr` varchar(10), IN `Var_plz` char(5), IN `Var_ort` varchar(64), IN `Var_stadtteil` varchar(64), IN `Var_telefon` varchar(45), IN `Var_fax` varchar(45), IN `Var_e_mail` varchar(264), IN `Var_adresszusatz` varchar(250), 
IN `Var_aktiv` BIT, IN `Var_zahlfrist` tinyint, IN `Var_mahnstufe` tinyint,IN `Var_filial_nr` int, IN `Var_kunden_nr_old` int, IN `Var_user` varchar(15))
root:BEGIN

Declare Var_anzahl int;
Declare Var_anzahl_hist int;

-- Declare Vor_name varchar(64); 
Declare Vor_strasse varchar(250); 
Declare Vor_nr varchar(10); 
Declare Vor_plz char(5); 
Declare Vor_ort varchar(64); 
Declare Vor_stadtteil varchar(64); 
Declare Vor_telefon varchar(45); 
Declare Vor_fax varchar(45); 
Declare Vor_email varchar(264); 
Declare Vor_adresszusatz varchar(250);
Declare Vor_aktiv bit;
Declare Vor_zahlfrist tinyint;
Declare Vor_mahnstufe tinyint;
Declare Vor_filial_nr int;


-- Declare Nach_name varchar(64); 
Declare Nach_strasse varchar(250); 
Declare Nach_nr varchar(10); 
Declare Nach_plz char(5); 
Declare Nach_ort varchar(64); 
Declare Nach_stadtteil varchar(64); 
Declare Nach_telefon varchar(45); 
Declare Nach_fax varchar(45); 
Declare Nach_email varchar(264); 
Declare Nach_adresszusatz varchar(250);
Declare Nach_aktiv bit;
Declare Nach_zahlfrist tinyint;
Declare Nach_mahnstufe tinyint;
Declare Nach_filial_nr int;

/*DECLARE Var_lfn int;*/
DECLARE Var_feld varchar(264);
DECLARE Var_ainhalt varchar(264);
DECLARE Var_ninhalt varchar(264);
DECLARE Var_code char(3);


-- Set Vor_name = (Select name From erdodb.kunden Where concat(filial_nr,plz,lfd_nr) = Var_kunden_nr_old);
Set Vor_strasse = (Select strasse From erdodb.kunden Where concat(filial_nr,plz,lfd_nr) = Var_kunden_nr_old);
Set Vor_nr = (Select nr From erdodb.kunden Where concat(filial_nr,plz,lfd_nr) = Var_kunden_nr_old);
Set Vor_plz = (Select plz From erdodb.kunden Where concat(filial_nr,plz,lfd_nr) = Var_kunden_nr_old);
Set Vor_ort = (Select ort From erdodb.kunden Where concat(filial_nr,plz,lfd_nr) = Var_kunden_nr_old);
Set Vor_stadtteil = (Select stadtteil From erdodb.kunden Where concat(filial_nr,plz,lfd_nr) = Var_kunden_nr_old);
Set Vor_adresszusatz = (Select adresszusatz From erdodb.kunden Where concat(filial_nr,plz,lfd_nr) = Var_kunden_nr_old);
Set Vor_telefon = (Select telefon From erdodb.kunden Where concat(filial_nr,plz,lfd_nr) = Var_kunden_nr_old);
Set Vor_fax = (Select fax From erdodb.kunden Where concat(filial_nr,plz,lfd_nr) = Var_kunden_nr_old);
Set Vor_email = (Select email From erdodb.kunden Where concat(filial_nr,plz,lfd_nr) = Var_kunden_nr_old);
Set Vor_aktiv = (Select aktiv From erdodb.kunden Where concat(filial_nr,plz,lfd_nr) = Var_kunden_nr_old);
Set Vor_zahlfrist = (Select zahlfrist From erdodb.kunden Where concat(filial_nr,plz,lfd_nr) = Var_kunden_nr_old);
Set Vor_mahnstufe = (Select mahnstufe From erdodb.kunden Where concat(filial_nr,plz,lfd_nr) = Var_kunden_nr_old);
Set Vor_filial_nr = (Select filial_nr From erdodb.kunden Where concat(filial_nr,plz,lfd_nr) = Var_kunden_nr_old);

Start Transaction;
Update erdodb.kunden set
-- name = Var_name,
strasse = Var_strasse,
nr = Var_nr,
plz = Var_plz,
ort = Var_ort,
stadtteil = Var_stadtteil,
adresszusatz = Var_adresszusatz,
telefon = Var_telefon,
fax = Var_fax,
email = Var_e_mail,
aktiv = Var_aktiv,
zahlfrist = Var_zahlfrist,
mahnstufe = Var_mahnstufe
Where concat(filial_nr,plz,lfd_nr) = Var_kunden_nr_old;

set Var_anzahl = ROW_COUNT();

-- Set Nach_name = (Select name From erdodb.kunden Where concat(filial_nr,plz,lfd_nr) = concat(Var_filial_nr,Var_plz,Var_lfd_nr));
Set Nach_strasse = (Select strasse From erdodb.kunden Where concat(filial_nr,plz,lfd_nr) = concat(Var_filial_nr,Var_plz,Var_lfd_nr));
Set Nach_nr = (Select nr From erdodb.kunden Where concat(filial_nr,plz,lfd_nr) = concat(Var_filial_nr,Var_plz,Var_lfd_nr));
Set Nach_plz = (Select plz From erdodb.kunden Where concat(filial_nr,plz,lfd_nr) = concat(Var_filial_nr,Var_plz,Var_lfd_nr));
Set Nach_ort = (Select ort From erdodb.kunden Where concat(filial_nr,plz,lfd_nr) = concat(Var_filial_nr,Var_plz,Var_lfd_nr));
Set Nach_stadtteil = (Select stadtteil From erdodb.kunden Where concat(filial_nr,plz,lfd_nr) = concat(Var_filial_nr,Var_plz,Var_lfd_nr));
Set Nach_adresszusatz = (Select adresszusatz From erdodb.kunden Where concat(filial_nr,plz,lfd_nr) = concat(Var_filial_nr,Var_plz,Var_lfd_nr));
Set Nach_telefon = (Select telefon From erdodb.kunden Where concat(filial_nr,plz,lfd_nr) = concat(Var_filial_nr,Var_plz,Var_lfd_nr));
Set Nach_fax = (Select fax From erdodb.kunden Where concat(filial_nr,plz,lfd_nr) = concat(Var_filial_nr,Var_plz,Var_lfd_nr));
Set Nach_email = (Select email From erdodb.kunden Where concat(filial_nr,plz,lfd_nr) = concat(Var_filial_nr,Var_plz,Var_lfd_nr));
Set Nach_aktiv = (Select aktiv From erdodb.kunden Where concat(filial_nr,plz,lfd_nr) = concat(Var_filial_nr,Var_plz,Var_lfd_nr));
Set Nach_zahlfrist = (Select zahlfrist From erdodb.kunden Where concat(filial_nr,plz,lfd_nr) = concat(Var_filial_nr,Var_plz,Var_lfd_nr));
Set Nach_mahnstufe = (Select mahnstufe From erdodb.kunden Where concat(filial_nr,plz,lfd_nr) = concat(Var_filial_nr,Var_plz,Var_lfd_nr));

Set Var_code = '002';
    
    -- Beginn mit den Einträgen in die Historie
    
--   IF Vor_name != Nach_name
--    Then
      /*Set Var_lfn = (SELECT IFNULL(MAX(lfn),0) FROM hist_kunden)+1;*/
--  Set Var_feld =  'name';
--  Set Var_ainhalt =  Vor_name;
--  Set Var_ninhalt =  Nach_name;
	  
 --     INSERT INTO hist_kunden
 --       (/*lfn,*/ schluessel, name,  codetext, user, aenderdat, feld, a_inhalt, n_inhalt)
 --     VALUES
 --       (/*Var_lfn,*/ concat(Var_filial_nr,Var_plz,Var_lfd_nr), Var_name,  Var_code, Var_user, curtime(), Var_feld, Var_ainhalt, Var_ninhalt);
 --   END IF; 

-- set Var_anzahl_hist = ROW_COUNT();

 IF Vor_strasse != Nach_strasse
    Then
      /*Set Var_lfn = (SELECT IFNULL(MAX(lfn),0) FROM hist_kunden)+1;*/
	  Set Var_feld =  'strasse';
	  Set Var_ainhalt =  Vor_strasse;
	  Set Var_ninhalt =  Nach_strasse;
	  
      INSERT INTO hist_kunden
        (/*lfn,*/ schluessel, name,  codetext, user, aenderdat, feld, a_inhalt, n_inhalt)
      VALUES
        (/*Var_lfn,*/ concat(Var_filial_nr,Var_plz,Var_lfd_nr), Var_name,  Var_code, Var_user, curtime(), Var_feld, Var_ainhalt, Var_ninhalt);
    END IF;

set Var_anzahl_hist = ROW_COUNT();

 IF Vor_ort != Nach_ort
    Then
      /*Set Var_lfn = (SELECT IFNULL(MAX(lfn),0) FROM hist_kunden)+1;*/
	  Set Var_feld =  'ort';
	  Set Var_ainhalt =  Vor_ort;
	  Set Var_ninhalt =  Nach_ort;
	  
      INSERT INTO hist_kunden
        (/*lfn,*/ schluessel, name,  codetext, user, aenderdat, feld, a_inhalt, n_inhalt)
      VALUES
        (/*Var_lfn,*/ concat(Var_filial_nr,Var_plz,Var_lfd_nr), Var_name,  Var_code, Var_user, curtime(), Var_feld, Var_ainhalt, Var_ninhalt);
    END IF;

set Var_anzahl_hist = Var_anzahl_hist + ROW_COUNT();

 IF Vor_plz != Nach_plz
    Then
      /*Set Var_lfn = (SELECT IFNULL(MAX(lfn),0) FROM hist_kunden)+1;*/
	  Set Var_feld =  'plz';
	  Set Var_ainhalt =  Vor_plz;
	  Set Var_ninhalt =  Nach_plz;
	  
      INSERT INTO hist_kunden
        (/*lfn,*/ schluessel, name,  codetext, user, aenderdat, feld, a_inhalt, n_inhalt)
      VALUES
        (/*Var_lfn,*/ concat(Var_filial_nr,Var_plz,Var_lfd_nr), Var_name,  Var_code, Var_user, curtime(), Var_feld, Var_ainhalt, Var_ninhalt);
    END IF;

set Var_anzahl_hist = Var_anzahl_hist + ROW_COUNT();

 IF Vor_stadtteil != Nach_stadtteil
    Then
      /*Set Var_lfn = (SELECT IFNULL(MAX(lfn),0) FROM hist_kunden)+1;*/
	  Set Var_feld =  'stadtteil';
	  Set Var_ainhalt =  Vor_stadtteil;
	  Set Var_ninhalt =  Nach_stadtteil;
	  
      INSERT INTO hist_kunden
        (/*lfn,*/ schluessel, name,  codetext, user, aenderdat, feld, a_inhalt, n_inhalt)
      VALUES
        (/*Var_lfn,*/ concat(Var_filial_nr,Var_plz,Var_lfd_nr), Var_name,  Var_code, Var_user, curtime(), Var_feld, Var_ainhalt, Var_ninhalt);
    END IF;

set Var_anzahl_hist = Var_anzahl_hist + ROW_COUNT();

 IF Vor_telefon != Nach_telefon
    Then
      /*Set Var_lfn = (SELECT IFNULL(MAX(lfn),0) FROM hist_kunden)+1;*/
	  Set Var_feld =  'telefon';
	  Set Var_ainhalt =  Vor_telefon;
	  Set Var_ninhalt =  Nach_telefon;
	  
      INSERT INTO hist_kunden
        (/*lfn,*/ schluessel, name,  codetext, user, aenderdat, feld, a_inhalt, n_inhalt)
      VALUES
        (/*Var_lfn,*/ concat(Var_filial_nr,Var_plz,Var_lfd_nr), Var_name,  Var_code, Var_user, curtime(), Var_feld, Var_ainhalt, Var_ninhalt);
    END IF;

set Var_anzahl_hist = Var_anzahl_hist + ROW_COUNT();

 IF Vor_fax != Nach_fax
    Then
      /*Set Var_lfn = (SELECT IFNULL(MAX(lfn),0) FROM hist_kunden)+1;*/
	  Set Var_feld =  'fax';
	  Set Var_ainhalt =  Vor_fax;
	  Set Var_ninhalt =  Nach_fax;
	  
      INSERT INTO hist_kunden
        (/*lfn,*/ schluessel, name,  codetext, user, aenderdat, feld, a_inhalt, n_inhalt)
      VALUES
        (/*Var_lfn,*/ concat(Var_filial_nr,Var_plz,Var_lfd_nr), Var_name,  Var_code, Var_user, curtime(), Var_feld, Var_ainhalt, Var_ninhalt);
    END IF;

set Var_anzahl_hist = Var_anzahl_hist + ROW_COUNT();

 IF Vor_email != Nach_email
    Then
      /*Set Var_lfn = (SELECT IFNULL(MAX(lfn),0) FROM hist_kunden)+1;*/
	  Set Var_feld =  'email';
	  Set Var_ainhalt =  Vor_email;
	  Set Var_ninhalt =  Nach_email;
	  
      INSERT INTO hist_kunden
        (/*lfn,*/ schluessel, name,  codetext, user, aenderdat, feld, a_inhalt, n_inhalt)
      VALUES
        (/*Var_lfn,*/ concat(Var_filial_nr,Var_plz,Var_lfd_nr), Var_name,  Var_code, Var_user, curtime(), Var_feld, Var_ainhalt, Var_ninhalt);
    END IF;

set Var_anzahl_hist = Var_anzahl_hist + ROW_COUNT();

 IF Vor_aktiv != Nach_aktiv
    Then
      /*Set Var_lfn = (SELECT IFNULL(MAX(lfn),0) FROM hist_kunden)+1;*/
	  Set Var_feld =  'aktiv';
	  Set Var_ainhalt =  Vor_aktiv;
	  Set Var_ninhalt =  Nach_aktiv;
	  
      INSERT INTO hist_kunden
        (/*lfn,*/ schluessel, name,  codetext, user, aenderdat, feld, a_inhalt, n_inhalt)
      VALUES
        (/*Var_lfn,*/ concat(Var_filial_nr,Var_plz,Var_lfd_nr), Var_name,  Var_code, Var_user, curtime(), Var_feld, Var_ainhalt, Var_ninhalt);
    END IF;

set Var_anzahl_hist = Var_anzahl_hist + ROW_COUNT();

IF Vor_adresszusatz != Nach_adresszusatz
    Then
      /*Set Var_lfn = (SELECT IFNULL(MAX(lfn),0) FROM hist_kunden)+1;*/
	  Set Var_feld =  'adresszusatz';
	  Set Var_ainhalt =  Vor_adresszusatz;
	  Set Var_ninhalt =  Nach_adresszusatz;
	  
      INSERT INTO hist_kunden
        (/*lfn,*/ schluessel, name,  codetext, user, aenderdat, feld, a_inhalt, n_inhalt)
      VALUES
        (/*Var_lfn,*/ concat(Var_filial_nr,Var_plz,Var_lfd_nr), Var_name,  Var_code, Var_user, curtime(), Var_feld, Var_ainhalt, Var_ninhalt);
    END IF;

set Var_anzahl_hist = Var_anzahl_hist + ROW_COUNT();

IF Vor_nr != Nach_nr
    Then
      /*Set Var_lfn = (SELECT IFNULL(MAX(lfn),0) FROM hist_kunden)+1;*/
	  Set Var_feld =  'nr';
	  Set Var_ainhalt =  Vor_nr;
	  Set Var_ninhalt =  Nach_nr;
	  
      INSERT INTO hist_kunden
        (/*lfn,*/ schluessel, name,  codetext, user, aenderdat, feld, a_inhalt, n_inhalt)
      VALUES
        (/*Var_lfn,*/ concat(Var_filial_nr,Var_plz,Var_lfd_nr), Var_name,  Var_code, Var_user, curtime(), Var_feld, Var_ainhalt, Var_ninhalt);
    END IF;

set Var_anzahl_hist = Var_anzahl_hist + ROW_COUNT();

IF Vor_zahlfrist != Nach_zahlfrist
    Then
      /*Set Var_lfn = (SELECT IFNULL(MAX(lfn),0) FROM hist_kunden)+1;*/
	  Set Var_feld =  'zahlfrist';
	  Set Var_ainhalt =  Vor_zahlfrist;
	  Set Var_ninhalt =  Nach_zahlfrist;
	  
      INSERT INTO hist_kunden
        (/*lfn,*/ schluessel, name,  codetext, user, aenderdat, feld, a_inhalt, n_inhalt)
      VALUES
        (/*Var_lfn,*/ concat(Var_filial_nr,Var_plz,Var_lfd_nr), Var_name,  Var_code, Var_user, curtime(), Var_feld, Var_ainhalt, Var_ninhalt);
    END IF;

set Var_anzahl_hist = Var_anzahl_hist + ROW_COUNT();

IF Vor_mahnstufe != Nach_mahnstufe
    Then
      /*Set Var_lfn = (SELECT IFNULL(MAX(lfn),0) FROM hist_kunden)+1;*/
	  Set Var_feld =  'mahnstufe';
	  Set Var_ainhalt =  Vor_mahnstufe;
	  Set Var_ninhalt =  Nach_mahnstufe;
	  
      INSERT INTO hist_kunden
        (/*lfn,*/ schluessel, name,  codetext, user, aenderdat, feld, a_inhalt, n_inhalt)
      VALUES
        (/*Var_lfn,*/ concat(Var_filial_nr,Var_plz,Var_lfd_nr), Var_name,  Var_code, Var_user, curtime(), Var_feld, Var_ainhalt, Var_ninhalt);
    END IF;

set Var_anzahl_hist = Var_anzahl_hist + ROW_COUNT();

IF Vor_filial_nr != Nach_filial_nr
    Then
      /*Set Var_lfn = (SELECT IFNULL(MAX(lfn),0) FROM hist_kunden)+1;*/
	  Set Var_feld =  'filial_nr';
	  Set Var_ainhalt =  Vor_filial_nr;
	  Set Var_ninhalt =  Nach_filial_nr;
	  
      INSERT INTO hist_kunden
        (/*lfn,*/ schluessel, name,  codetext, user, aenderdat, feld, a_inhalt, n_inhalt)
      VALUES
        (/*Var_lfn,*/ concat(Var_filial_nr,Var_plz,Var_lfd_nr), Var_name,  Var_code, Var_user, curtime(), Var_feld, Var_ainhalt, Var_ninhalt);
    END IF;

set Var_anzahl_hist = Var_anzahl_hist + ROW_COUNT();


 IF Var_anzahl != 1 OR Var_anzahl_hist < 1
 Then 
 Rollback;
 Select Var_anzahl as ergebnis, Var_anzahl_hist as historie, concat(Var_filial_nr,Var_plz,Var_lfd_nr) as kunden_nr;
 Leave root;
 Else 
 Commit;
 End IF;
commit;
Select Var_anzahl as ergebnis, Var_anzahl_hist as historie, concat(Var_filial_nr,Var_plz,Var_lfd_nr) as kunden_nr;


END$$

DROP PROCEDURE IF EXISTS `editProdukte`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `editProdukte`(
Var_prod_kz char(4), 
Var_bezeichnung varchar(64),
Var_netto_preis1 decimal(8,2),
Var_netto_preis2 decimal(8,2),
Var_mwst decimal(5,2),
Var_aktiv bit,
Var_user varchar(15))
root:BEGIN

Declare Var_anzahl int;
Declare Var_anzahl_hist int;

Declare Vor_bezeichnung varchar(64); 
Declare Vor_netto_preis1 decimal(8,2);
Declare Vor_netto_preis2 decimal(8,2); 
Declare Vor_mwst decimal(5,2); 
Declare Vor_aktiv bit;


Declare Nach_bezeichnung varchar(64); 
Declare Nach_netto_preis1 decimal(8,2);
Declare Nach_netto_preis2 decimal(8,2); 
Declare Nach_mwst decimal(5,2); 
Declare Nach_aktiv bit;

/*DECLARE Var_lfn int;*/
DECLARE Var_feld varchar(264);
DECLARE Var_ainhalt varchar(264);
DECLARE Var_ninhalt varchar(264);
DECLARE Var_code char(3);


Set Vor_bezeichnung = (Select bezeichnung From erdodb.produkte Where prod_kz = Var_prod_kz);
Set Vor_netto_preis1 = (Select netto_preis1 From erdodb.produkte Where prod_kz = Var_prod_kz);
Set Vor_netto_preis2 = (Select netto_preis2 From erdodb.produkte Where prod_kz = Var_prod_kz);
Set Vor_mwst = (Select mwst From erdodb.produkte Where prod_kz = Var_prod_kz);
Set Vor_aktiv = (Select aktiv From erdodb.produkte Where prod_kz = Var_prod_kz);

Start Transaction;
Update erdodb.produkte set
bezeichnung = Var_bezeichnung,
netto_preis1 = Var_netto_preis1,
netto_preis2 = Var_netto_preis2,
mwst = Var_mwst,
aktiv = Var_aktiv
Where prod_kz = Var_prod_kz;

set Var_anzahl = ROW_COUNT();

Set Nach_bezeichnung = (Select bezeichnung From erdodb.produkte Where prod_kz = Var_prod_kz);
Set Nach_netto_preis1 = (Select netto_preis1 From erdodb.produkte Where prod_kz = Var_prod_kz);
Set Nach_netto_preis2 = (Select netto_preis2 From erdodb.produkte Where prod_kz = Var_prod_kz);
Set Nach_mwst = (Select mwst From erdodb.produkte Where prod_kz = Var_prod_kz);
Set Nach_aktiv = (Select aktiv From erdodb.produkte Where prod_kz = Var_prod_kz);


Set Var_code = '005';
    
    -- Beginn mit den Einträgen in die Historie
    
    IF Vor_bezeichnung != Nach_bezeichnung
    Then
      /*Set Var_lfn = (SELECT IFNULL(MAX(lfn),0) FROM hist_produkte)+1;*/
	  Set Var_feld =  'bezeichnung';
	  Set Var_ainhalt =  Vor_bezeichnung;
	  Set Var_ninhalt =  Nach_bezeichnung;
	  
      INSERT INTO hist_produkte
        (/*lfn,*/  schluessel, name, codetext, user, aenderdat, feld, a_inhalt, n_inhalt)
      VALUES
        (/*Var_lfn,*/ Var_prod_kz, Var_bezeichnung, Var_code, Var_user, curtime(), Var_feld, Var_ainhalt, Var_ninhalt);
    END IF;

set Var_anzahl_hist = ROW_COUNT();

 IF Vor_netto_preis1 != Nach_netto_preis1
    Then
      /*Set Var_lfn = (SELECT IFNULL(MAX(lfn),0) FROM hist_produkte)+1;*/
	  Set Var_feld =  'netto_preis1';
	  Set Var_ainhalt =  Vor_netto_preis1;
	  Set Var_ninhalt =  Nach_netto_preis1;
	  
      INSERT INTO hist_produkte
        (/*lfn,*/  schluessel, name, codetext, user, aenderdat, feld, a_inhalt, n_inhalt)
      VALUES
        (/*Var_lfn,*/ Var_prod_kz, Var_bezeichnung, Var_code, Var_user, curtime(), Var_feld, Var_ainhalt, Var_ninhalt);
    END IF;

set Var_anzahl_hist = Var_anzahl_hist + ROW_COUNT();

 IF Vor_netto_preis2 != Nach_netto_preis2
    Then
      /*Set Var_lfn = (SELECT IFNULL(MAX(lfn),0) FROM hist_produkte)+1;*/
	  Set Var_feld =  'netto_preis2';
	  Set Var_ainhalt =  Vor_netto_preis2;
	  Set Var_ninhalt =  Nach_netto_preis2;
	  
      INSERT INTO hist_produkte
        (/*lfn,*/  schluessel, name, codetext, user, aenderdat, feld, a_inhalt, n_inhalt)
      VALUES
        (/*Var_lfn,*/ Var_prod_kz, Var_bezeichnung, Var_code, Var_user, curtime(), Var_feld, Var_ainhalt, Var_ninhalt);
    END IF;

set Var_anzahl_hist = Var_anzahl_hist + ROW_COUNT();

 IF Vor_mwst != Nach_mwst
    Then
      /*Set Var_lfn = (SELECT IFNULL(MAX(lfn),0) FROM hist_produkte)+1;*/
	  Set Var_feld =  'mwst';
	  Set Var_ainhalt =  Vor_mwst;
	  Set Var_ninhalt =  Nach_mwst;
	  
      INSERT INTO hist_produkte
        (/*lfn,*/ schluessel, name, codetext, user, aenderdat, feld, a_inhalt, n_inhalt)
      VALUES
        (/*Var_lfn,*/ Var_prod_kz, Var_bezeichnung, Var_code, Var_user, curtime(), Var_feld, Var_ainhalt, Var_ninhalt);
    END IF;

set Var_anzahl_hist = Var_anzahl_hist + ROW_COUNT();

 IF Vor_aktiv != Nach_aktiv
    Then
      /*Set Var_lfn = (SELECT IFNULL(MAX(lfn),0) FROM hist_produkte)+1;*/
	  Set Var_feld =  'aktiv';
	  Set Var_ainhalt =  Vor_aktiv;
	  Set Var_ninhalt =  Nach_aktiv;
	  
      INSERT INTO hist_produkte
        (/*lfn,*/ schluessel, name, codetext, user, aenderdat, feld, a_inhalt, n_inhalt)
      VALUES
        (/*Var_lfn,*/ Var_prod_kz, Var_bezeichnung, Var_code, Var_user, curtime(), Var_feld, Var_ainhalt, Var_ninhalt);
    END IF;

set Var_anzahl_hist = Var_anzahl_hist + ROW_COUNT();


 IF Var_anzahl != 1 OR Var_anzahl_hist < 1
 Then 
 Rollback;
 Select Var_anzahl as ergebnis, Var_anzahl_hist as historie, Var_prod_kz as prod_kz;
 Leave root;
 Else 
 Commit;
 End IF;
commit;
Select Var_anzahl as ergebnis, Var_anzahl_hist as historie, Var_prod_kz as prod_kz;


END$$

DROP PROCEDURE IF EXISTS `editUser`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `editUser`(
IN `Var_UserID` int,  
IN `Var_admin` char(1), 
IN `Var_status` char(1), 
IN `Var_e_mail` varchar(264), 
IN `Var_user` varchar(15)
)
root:BEGIN

Declare Var_anzahl int;

Start Transaction;
Update erdodb.users set
admin = Var_admin,
status = Var_status,
email = Var_e_mail
Where UserID = Var_UserID;

set Var_anzahl = ROW_COUNT();


 IF Var_anzahl != 1  Then 
 Rollback;
 Select Var_anzahl as ergebnis, Var_UserID as UserID;
 Leave root;
 End IF;
 
commit;
Select Var_anzahl as ergebnis, Var_UserID as UserID;


END$$

DROP PROCEDURE IF EXISTS `loginProc`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `loginProc`(IN Var_benutzer varchar(50),
IN Var_passwort varchar(400))
root:Begin
  Declare Var_ergebnis int;
  Declare Var_loginCount smallint;
  Declare Var_loginTime datetime;
  Declare Var_timeOut datetime;
  Declare Var_status char(1);
  Declare Var_admin char(1);
  
  -- Holen der Login-Verifizierungsdaten
  SET Var_loginCount = (Select ifnull(loginCount,0)  from Users where benutzer = Var_benutzer);
  SET Var_loginTime = (Select ifnull(loginTime,CURTIME()) from Users where benutzer = Var_benutzer);
  SET Var_timeOut = (Select timeOut from Users where benutzer = Var_benutzer);  

  
  -- IF BLOCK
  IF TIMESTAMPDIFF(MINUTE, Var_timeOut, CURTIME()) < 30 OR Var_timeOut != NULL -- Konto ist bereits für 30 Min. gesperrt
  THEN
    Select -99 as Ergebnis, NULL as admin, NULL as 'status';
    LEAVE root;
  
  ELSE
  SET SQL_SAFE_UPDATES=0;
    Update Users set timeOut = NULL where benutzer = Var_benutzer;
  END IF;

    
    Set Var_ergebnis = (Select count(Concat(benutzer,passwort)) from users where benutzer = Var_benutzer and passwort = Var_passwort
    group by admin, status);
    Set Var_status = (Select status from users where benutzer = Var_benutzer and passwort = Var_passwort
    group by admin, status);
    Set Var_admin = (Select admin from users where benutzer = Var_benutzer and passwort = Var_passwort
    group by admin, status);
    
    If Var_ergebnis = 1
    THEN
		IF Var_status != 'O'
		THEN
			Update Users set loginCount = 0, loginTime = NULL, timeOut = NULL, onlineTime = CURTIME(), logoutTime = NULL where benutzer = Var_benutzer;
			Select Var_ergebnis as Ergebnis, Var_admin as admin, Var_status as status;
			LEAVE root;
		ELSE
			Update Users set loginCount = 0, loginTime = NULL, timeOut = NULL where benutzer = Var_benutzer;
			Select Var_ergebnis as Ergebnis, Var_admin as admin, Var_status as status;
			LEAVE root;
		END IF;
    ELSE
    
      If not exists (select 1 from Users where benutzer = Var_benutzer)
      THEN
        Select 0 as Ergebnis, NULL as admin, NULL as 'status'; -- Anmeldung fehlgeschlagen und User existiert auch nicht
        LEAVE root;
      
      Else-- Anmeldung fehlgeschlagen aber benutzer existiert
      
        if Var_loginCount <= 2 -- Anmeldung wieder fehlgeschlagen aber loginCount ist nicht überschritten
        THEN
          If TIMESTAMPDIFF(MINUTE, Var_loginTime, CURTIME()) < 10
          THEN
            Update Users set loginCount = Var_loginCount +1, loginTime = CURTIME() where benutzer = Var_benutzer;
            Select 0 as Ergebnis, NULL as admin, NULL as 'status'; 
            LEAVE root;
          
          ELSE
          
            Update Users set loginCount = 1, loginTime = CURTIME() where benutzer = Var_benutzer;
            Select 0 as Ergebnis, NULL as admin, NULL as 'status'; 
            LEAVE root;
          END IF;
        
        ELSE
         -- 3 Falsche login-versuche: Konto wird für 30 Min. gesperrt
          Update Users set loginCount = 0, loginTime = NULL, timeOut = CURTIME() where benutzer = Var_benutzer;
          Select -98 as Ergebnis, NULL as admin, NULL as 'status'; 
          LEAVE root;
       
      END IF;
    END IF;
  END IF;
  SET SQL_SAFE_UPDATES=1;
End$$

DROP PROCEDURE IF EXISTS `logoutProc`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `logoutProc`(
 Var_user varchar(15)
)
root:BEGIN

DECLARE Var_anzahl int;

SET SQL_SAFE_UPDATES=0;
Update Users set logoutTime = CURTIME(), onlineTime = NULL where benutzer = Var_user;

Set Var_anzahl = ROW_COUNT();

 IF Var_anzahl != 1 
 Then 
   Select -99 as ergebnis;
   Leave root;
 Else 
   Select Var_anzahl as ergebnis;
   Leave root;
 End IF;

END$$

DROP PROCEDURE IF EXISTS `prodBildUpload`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `prodBildUpload`(Var_bild_name varchar(264),
Var_prod_kz char(4), Var_user varchar(15))
root:Begin
Declare Var_anzahl int;  
Declare Var_anzahl_hist int;

DECLARE Var_lfn int;
DECLARE Var_feld varchar(264);
DECLARE Var_ainhalt varchar(264);
DECLARE Var_ninhalt varchar(264);
DECLARE Var_code char(3);

Start Transaction;  
Update produkte set prod_bild = Var_bild_name where prod_kz = Var_prod_kz; 
 
Set Var_anzahl = ROW_COUNT(); 

Set Var_code = '007';

Set Var_lfn = (SELECT IFNULL(MAX(lfn),0) FROM hist_produkte)+1;
	  Set Var_feld =  'NEUES BILD';
	  Set Var_ainhalt =  NULL;
	  Set Var_ninhalt =  NULL;
	  
      INSERT INTO hist_produkte
        (lfn, schluessel, codetext, user, aenderdat, feld, a_inhalt, n_inhalt)
      VALUES
        (Var_lfn, Var_prod_kz, Var_code, Var_user, curtime(), Var_feld, Var_ainhalt, Var_ninhalt);

set Var_anzahl_hist = ROW_COUNT();
 
 IF Var_anzahl != 1 OR Var_anzahl_hist != 1
 Then 
 Rollback;
 Select Var_anzahl as ergebnis, Var_anzahl_hist as historie;
 Leave root;
 Else 
 Commit;
 End IF;
commit;

Select Var_anzahl as ergebnis, Var_anzahl_hist as historie; 
 
End$$

DROP PROCEDURE IF EXISTS `selectPreisKat`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `selectPreisKat`(Var_prod_kz char(4), Var_preis_kat int)
begin
SELECT mwst, case when Var_preis_kat = 1 then netto_preis1 when Var_preis_kat = 2 then netto_preis2 end as netto_preis
From produkte 
Where prod_kz = Var_prod_kz;
end$$

DROP PROCEDURE IF EXISTS `selectVerkaeufe`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `selectVerkaeufe`(Var_beleg_nr varchar(45) )
begin
SELECT v.lfd_nr, v.prod_kz, p.bezeichnung, verkauf_an, 
(select f.name from filialen f where k.filial_nr = f.filial_nr) as name, 
menge, preis_kat, 
einzelpr_netto as netto_preis,
v.mwst,
einzelpr_netto*(v.mwst/100) as mwst_einzelpr,
(einzelpr_netto*(v.mwst/100))+einzelpr_netto as brutto_preis,
gesamtpr_netto as gesamtpr_netto,
gesamtpr_netto *(v.mwst/100) as mwst_gesamtpr,
(gesamtpr_netto*(v.mwst/100))+gesamtpr_netto as gesamtpr_brutto,
DATE_FORMAT(v.datum,GET_FORMAT(DATE,'ISO')) as datum, 
bemerkung, 
beleg_nr, 
beleg_pfad,
bemerkung
FROM erdodb.verkaeufe v left join produkte p on v.prod_kz = p.prod_kz 
left join kunden k on v.verkauf_an = concat(filial_nr,plz,k.lfd_nr) 
where v.beleg_nr = Var_beleg_nr;
end$$

DROP PROCEDURE IF EXISTS `selectVerkaeufeAll`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `selectVerkaeufeAll`()
begin
SELECT v.lfd_nr, v.prod_kz, p.bezeichnung, verkauf_an, 
(select f.name from filialen f where k.filial_nr = f.filial_nr) as name, 
menge, preis_kat, 
einzelpr_netto as netto_preis,
v.mwst,
einzelpr_netto*(v.mwst/100) as mwst_einzelpr,
(einzelpr_netto*(v.mwst/100))+einzelpr_netto as brutto_preis,
gesamtpr_netto as gesamtpr_netto,
gesamtpr_netto *(v.mwst/100) as mwst_gesamtpr,
(gesamtpr_netto*(v.mwst/100))+gesamtpr_netto as gesamtpr_brutto,
DATE_FORMAT(v.datum,GET_FORMAT(DATE,'ISO')) as datum, 
bemerkung, 
beleg_nr, 
beleg_pfad,
bemerkung
FROM erdodb.verkaeufe v left join produkte p on v.prod_kz = p.prod_kz 
left join kunden k on v.verkauf_an = concat(filial_nr,plz,k.lfd_nr);
end$$

DROP PROCEDURE IF EXISTS `UserAddProc`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `UserAddProc`(IN Var_benutzer varchar(50),
IN Var_passwort varchar(40),
IN Var_email varchar(64))
root:begin

declare Var_anzahl int;
declare Var_userID int DEFAULT 0;
declare Var_admin char(1) DEFAULT 'N';
declare Var_status char(1) DEFAULT 'O';

IF CHAR_LENGTH(Var_benutzer) = 0 or CHAR_LENGTH(Var_passwort) = 0
THEN
Select 0 as ergebnis; 
LEAVE root;
End If;



if exists (Select * from users where benutzer = Var_benutzer)
THEN
Select -1 as ergebnis, Var_userID as userID; -- Benutzer existiert bereits
LEAVE root;
End If;

INSERT INTO erdodb.Users 
( benutzer, Passwort, admin, status, email) VALUES (Var_benutzer, Var_passwort, Var_admin, Var_status, Var_email); 

set Var_anzahl = ROW_COUNT();

if Var_anzahl != 1
THEN
Select -2 as ergebnis; -- Fehler beim Update aufgetreten
LEAVE root;
End IF;

SET Var_userID = (Select max(userID) from Users);

select Var_anzahl as ergebnis, Var_userID as userID;

end$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `ausgaben`
--

DROP TABLE IF EXISTS `ausgaben`;
CREATE TABLE IF NOT EXISTS `ausgaben` (
  `ausg_kz` char(4) COLLATE utf8_bin NOT NULL,
  `ausg_art_kz` char(4) COLLATE utf8_bin NOT NULL,
  `bezeichnung` varchar(64) COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`ausg_kz`,`ausg_art_kz`),
  KEY `ausg_art_kz_idx` (`ausg_art_kz`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Daten für Tabelle `ausgaben`
--

INSERT INTO `ausgaben` (`ausg_kz`, `ausg_art_kz`, `bezeichnung`) VALUES
('LOGE', 'PROD', 'Löhne und Gehälter'),
('MATE', 'PROD', 'Materialkosten'),
('MIET', 'BETR', 'Miete'),
('MUEL', 'BETR', 'Müllkosten'),
('REIN', 'BETR', 'Reinigungskosten'),
('REPA', 'PROD', 'Reparaturkosten'),
('SPRI', 'PROD', 'Spritkosten'),
('STRO', 'BETR', 'Stromkosten'),
('WASS', 'BETR', 'Wasserkosten'),
('WERB', 'PROD', 'Werbekosten');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `ausgaben_arten`
--

DROP TABLE IF EXISTS `ausgaben_arten`;
CREATE TABLE IF NOT EXISTS `ausgaben_arten` (
  `ausg_art_kz` char(4) COLLATE utf8_bin NOT NULL,
  `bezeichnung` varchar(64) COLLATE utf8_bin DEFAULT NULL,
  PRIMARY KEY (`ausg_art_kz`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Daten für Tabelle `ausgaben_arten`
--

INSERT INTO `ausgaben_arten` (`ausg_art_kz`, `bezeichnung`) VALUES
('BETR', 'Betrieb'),
('PROD', 'Produktion');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `filialen`
--

DROP TABLE IF EXISTS `filialen`;
CREATE TABLE IF NOT EXISTS `filialen` (
  `filial_nr` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(64) COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`filial_nr`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=20 ;

--
-- Daten für Tabelle `filialen`
--

INSERT INTO `filialen` (`filial_nr`, `name`) VALUES
(1, 'Yakamoz Markt'),
(2, 'Erden Market'),
(3, 'Öncü Supermarkt GmbH '),
(4, 'Meydan Markt e.K. '),
(5, 'Eidelstedter Schlachterei '),
(6, 'Frisch & Lecker Edeka '),
(7, 'Luruper Markt'),
(8, 'Altona City Markt'),
(9, 'Dogutürk'),
(10, 'ONUR GmbH & Co. KG'),
(11, 'SÖNMEZ Markt'),
(12, 'ÖZKA Market');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `hist_abrechnung`
--

DROP TABLE IF EXISTS `hist_abrechnung`;
CREATE TABLE IF NOT EXISTS `hist_abrechnung` (
  `lfn` int(11) NOT NULL AUTO_INCREMENT,
  `lfd_nr` int(11) NOT NULL,
  `kunden_nr` int(11) NOT NULL,
  `prod_kz` char(4) COLLATE utf8_bin NOT NULL,
  `prod_bez` varchar(64) COLLATE utf8_bin NOT NULL,
  `kunden_name` varchar(64) COLLATE utf8_bin NOT NULL,
  `user` varchar(15) COLLATE utf8_bin NOT NULL,
  `aenderdat` datetime NOT NULL,
  `feld` varchar(264) COLLATE utf8_bin NOT NULL,
  `a_inhalt` varchar(264) COLLATE utf8_bin DEFAULT NULL,
  `n_inhalt` varchar(264) COLLATE utf8_bin DEFAULT NULL,
  `codetext` char(3) COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`lfn`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=321 ;

--
-- Daten für Tabelle `hist_abrechnung`
--

INSERT INTO `hist_abrechnung` (`lfn`, `lfd_nr`, `kunden_nr`, `prod_kz`, `prod_bez`, `kunden_name`, `user`, `aenderdat`, `feld`, `a_inhalt`, `n_inhalt`, `codetext`) VALUES
(49, 3, 11, 'KAFU', 'Gefüllte Grießfrikadellen - Kartoffelfüllung', 'Altona City Markt', 'sek', '2014-09-03 16:56:06', 'NEU', NULL, NULL, '008'),
(50, 4, 11, 'KAFU', 'Gefüllte Grießfrikadellen - Kartoffelfüllung', 'Altona City Markt', 'sek', '2014-09-03 16:57:32', 'NEU', NULL, NULL, '008'),
(51, 5, 11, 'HAFU', 'Gefüllte Grießfrikadellen - Hackfleischfüllung', 'Altona City Markt', 'sek', '2014-09-03 17:59:49', 'NEU', NULL, NULL, '008'),
(52, 6, 6, 'HAFU', 'Gefüllte Grießfrikadellen - Hackfleischfüllung', 'Eidelstedter Schlachterei ', 'sek', '2014-09-03 18:02:38', 'NEU', NULL, NULL, '008'),
(53, 7, 12, 'HAFU', 'Gefüllte Grießfrikadellen - Hackfleischfüllung', 'Dogutürk', 'sek', '2014-09-03 18:03:17', 'NEU', NULL, NULL, '008'),
(54, 8, 6, 'HAFU', 'Gefüllte Grießfrikadellen - Hackfleischfüllung', 'Eidelstedter Schlachterei ', 'sek', '2014-09-03 23:35:55', 'NEU', NULL, NULL, '008'),
(55, 9, 7, 'HAFU', 'Gefüllte Grießfrikadellen - Hackfleischfüllung', 'Erden Market', 'sek', '2014-09-03 23:36:19', 'NEU', NULL, NULL, '008'),
(56, 10, 7, 'SUAT', 'Suat', 'Erden Market', 'sek', '2014-09-03 23:37:17', 'NEU', NULL, NULL, '008'),
(57, 11, 12, 'HAFU', 'Gefüllte Grießfrikadellen - Hackfleischfüllung', 'Dogutürk', 'sek', '2014-09-04 12:42:19', 'NEU', NULL, NULL, '008'),
(58, 12, 9, 'KAFU', 'Gefüllte Grießfrikadellen - Kartoffelfüllung', 'Frisch & Lecker Edeka ', 'sek', '2014-09-05 01:48:08', 'NEU', NULL, NULL, '008'),
(59, 13, 9, 'HAFU', 'Gefüllte Grießfrikadellen - Hackfleischfüllung', 'Frisch & Lecker Edeka ', 'sek', '2014-09-05 01:48:37', 'NEU', NULL, NULL, '008'),
(60, 14, 11, 'HAFU', 'Gefüllte Grießfrikadellen - Hackfleischfüllung', 'Altona City Markt', 'sek', '2014-09-05 20:50:58', 'NEU', NULL, NULL, '008'),
(61, 15, 11, 'HAFU', 'Gefüllte Grießfrikadellen - Hackfleischfüllung', 'Altona City Markt', 'sek', '2014-09-05 20:51:44', 'NEU', NULL, NULL, '008'),
(62, 16, 6, 'HAFU', 'Gefüllte Grießfrikadellen - Hackfleischfüllung', 'Eidelstedter Schlachterei ', 'sek', '2014-09-05 20:55:11', 'NEU', NULL, NULL, '008'),
(63, 17, 11, 'SUAT', 'Suat', 'Altona City Markt', 'sek', '2014-09-05 20:58:45', 'NEU', NULL, NULL, '008'),
(64, 1, 12, 'GEFU', 'Gefüllte Grießfrikadellen - Gemüsefüllung', 'Dogutürk', 'sek', '2014-09-05 22:18:35', 'NEU', NULL, NULL, '008'),
(65, 2, 12, 'HAFU', 'Gefüllte Grießfrikadellen - Hackfleischfüllung', 'Dogutürk', 'sek', '2014-09-05 22:18:54', 'NEU', NULL, NULL, '008'),
(66, 3, 12, 'KAFU', 'Gefüllte Grießfrikadellen - Kartoffelfüllung', 'Dogutürk', 'sek', '2014-09-05 22:19:57', 'NEU', NULL, NULL, '008'),
(67, 4, 12, 'GEFU', 'Gefüllte Grießfrikadellen - Gemüsefüllung', 'Dogutürk', 'sek', '2014-09-06 20:48:39', 'NEU', NULL, NULL, '008'),
(68, 5, 12, 'KAFU', 'Gefüllte Grießfrikadellen - Kartoffelfüllung', 'Dogutürk', 'sek', '2014-09-06 20:52:57', 'NEU', NULL, NULL, '008'),
(69, 6, 12, 'HAFU', 'Gefüllte Grießfrikadellen - Hackfleischfüllung', 'Dogutürk', 'sek', '2014-09-06 20:53:14', 'NEU', NULL, NULL, '008'),
(70, 7, 12, 'GEFU', 'Gefüllte Grießfrikadellen - Gemüsefüllung', 'Dogutürk', 'sek', '2014-09-06 20:53:32', 'NEU', NULL, NULL, '008'),
(71, 8, 12, 'KAFU', 'Gefüllte Grießfrikadellen - Kartoffelfüllung', 'Dogutürk', 'sek', '2014-09-06 20:53:46', 'NEU', NULL, NULL, '008'),
(72, 9, 12, 'HAFU', 'Gefüllte Grießfrikadellen - Hackfleischfüllung', 'Dogutürk', 'sek', '2014-09-06 20:54:00', 'NEU', NULL, NULL, '008'),
(73, 10, 12, 'HAFU', 'Gefüllte Grießfrikadellen - Hackfleischfüllung', 'Dogutürk', 'sek', '2014-09-06 20:54:16', 'NEU', NULL, NULL, '008'),
(74, 11, 12, 'HAFU', 'Gefüllte Grießfrikadellen - Hackfleischfüllung', 'Dogutürk', 'sek', '2014-09-06 20:54:33', 'NEU', NULL, NULL, '008'),
(75, 12, 12, 'HAFU', 'Gefüllte Grießfrikadellen - Hackfleischfüllung', 'Dogutürk', 'sek', '2014-09-06 20:54:47', 'NEU', NULL, NULL, '008'),
(76, 13, 12, 'KAFU', 'Gefüllte Grießfrikadellen - Kartoffelfüllung', 'Dogutürk', 'sek', '2014-09-06 20:55:00', 'NEU', NULL, NULL, '008'),
(77, 14, 12, 'GEFU', 'Gefüllte Grießfrikadellen - Gemüsefüllung', 'Dogutürk', 'sek', '2014-09-06 20:57:45', 'NEU', NULL, NULL, '008'),
(78, 15, 12, 'HAFU', 'Gefüllte Grießfrikadellen - Hackfleischfüllung', 'Dogutürk', 'sek', '2014-09-06 20:57:57', 'NEU', NULL, NULL, '008'),
(79, 16, 12, 'KAFU', 'Gefüllte Grießfrikadellen - Kartoffelfüllung', 'Dogutürk', 'sek', '2014-09-06 20:58:08', 'NEU', NULL, NULL, '008'),
(80, 17, 12, 'HAFU', 'Gefüllte Grießfrikadellen - Hackfleischfüllung', 'Dogutürk', 'sek', '2014-09-06 21:39:13', 'NEU', NULL, NULL, '008'),
(81, 18, 7, 'KAFU', 'Gefüllte Grießfrikadellen - Kartoffelfüllung', 'Erden Market', 'sek', '2014-09-06 22:40:06', 'NEU', NULL, NULL, '008'),
(82, 19, 12, 'GEFU', 'Gefüllte Grießfrikadellen - Gemüsefüllung', 'Dogutürk', 'sek', '2014-09-08 00:13:59', 'NEU', NULL, NULL, '008'),
(83, 20, 12, 'HAFU', 'Gefüllte Grießfrikadellen - Hackfleischfüllung', 'Dogutürk', 'sek', '2014-09-08 00:14:19', 'NEU', NULL, NULL, '008'),
(84, 21, 12, 'HAFU', 'Gefüllte Grießfrikadellen - Hackfleischfüllung', 'Dogutürk', 'sek', '2014-09-08 10:04:01', 'NEU', NULL, NULL, '008'),
(85, 22, 12, 'HAFU', 'Gefüllte Grießfrikadellen - Hackfleischfüllung', 'Dogutürk', 'sek', '2014-09-08 10:04:18', 'NEU', NULL, NULL, '008'),
(86, 23, 12, 'HAFU', 'Gefüllte Grießfrikadellen - Hackfleischfüllung', 'Dogutürk', 'sek', '2014-09-08 10:04:30', 'NEU', NULL, NULL, '008'),
(87, 24, 12, 'HAFU', 'Gefüllte Grießfrikadellen - Hackfleischfüllung', 'Dogutürk', 'sek', '2014-09-08 10:04:48', 'NEU', NULL, NULL, '008'),
(88, 25, 12, 'HAFU', 'Gefüllte Grießfrikadellen - Hackfleischfüllung', 'Dogutürk', 'sek', '2014-09-08 10:05:04', 'NEU', NULL, NULL, '008'),
(89, 26, 12, 'HAFU', 'Gefüllte Grießfrikadellen - Hackfleischfüllung', 'Dogutürk', 'sek', '2014-09-08 10:05:17', 'NEU', NULL, NULL, '008'),
(90, 27, 6, 'GEFU', 'Gefüllte Grießfrikadellen - Gemüsefüllung', 'Eidelstedter Schlachterei ', 'sek', '2014-09-08 18:23:33', 'NEU', NULL, NULL, '008'),
(91, 28, 10, 'KAFU', 'Gefüllte Grießfrikadellen - Kartoffelfüllung', 'Luruper Markt', 'sek', '2014-09-09 15:40:25', 'NEU', NULL, NULL, '008'),
(92, 18, 7, 'KAFU', 'Gefüllte Grießfrikadellen - Kartoffelfüllung', 'Erden Market', 'sek', '2014-09-09 17:28:14', 'menge', '500', '501', '009'),
(93, 18, 7, 'KAFU', 'Gefüllte Grießfrikadellen - Kartoffelfüllung', 'Erden Market', 'sek', '2014-09-09 17:28:14', 'gesamtpr_netto', '300.00', '300.60', '009'),
(94, 28, 10, 'KAFU', 'Gefüllte Grießfrikadellen - Kartoffelfüllung', 'Luruper Markt', 'sek', '2014-09-09 17:30:33', 'menge', '633', '630', '009'),
(95, 28, 10, 'KAFU', 'Gefüllte Grießfrikadellen - Kartoffelfüllung', 'Luruper Markt', 'sek', '2014-09-09 17:30:33', 'gesamtpr_netto', '379.80', '378.00', '009'),
(96, 18, 7, 'KAFU', 'Gefüllte Grießfrikadellen - Kartoffelfüllung', 'Erden Market', 'sek', '2014-09-09 17:30:44', 'menge', '501', '500', '009'),
(97, 18, 7, 'KAFU', 'Gefüllte Grießfrikadellen - Kartoffelfüllung', 'Erden Market', 'sek', '2014-09-09 17:30:44', 'gesamtpr_netto', '300.60', '300.00', '009'),
(98, 25, 12, 'HAFU', 'Gefüllte Grießfrikadellen - Hackfleischfüllung', 'Dogutürk', 'sek', '2014-09-09 17:39:28', 'menge', '698', '700', '009'),
(99, 25, 12, 'HAFU', 'Gefüllte Grießfrikadellen - Hackfleischfüllung', 'Dogutürk', 'sek', '2014-09-09 17:39:28', 'gesamtpr_netto', '418.80', '420.00', '009'),
(100, 24, 12, 'HAFU', 'Gefüllte Grießfrikadellen - Hackfleischfüllung', 'Dogutürk', 'sek', '2014-09-09 17:39:57', 'menge', '696', '700', '009'),
(101, 24, 12, 'HAFU', 'Gefüllte Grießfrikadellen - Hackfleischfüllung', 'Dogutürk', 'sek', '2014-09-09 17:39:57', 'gesamtpr_netto', '417.60', '420.00', '009'),
(102, 29, 7, 'HAFU', 'Gefüllte Grießfrikadellen - Hackfleischfüllung', 'Erden Market', 'sek', '2014-09-09 17:51:50', 'NEU', NULL, NULL, '008'),
(103, 23, 7, 'HAFU', 'Gefüllte Grießfrikadellen - Hackfleischfüllung', 'Erden Market', 'sek', '2014-09-09 17:56:36', 'verkauf_an', '12', '7', '009'),
(104, 23, 7, 'HAFU', 'Gefüllte Grießfrikadellen - Hackfleischfüllung', 'Erden Market', 'sek', '2014-09-09 17:56:36', 'beleg_nr', '2014/1', '2014/2', '009'),
(105, 23, 6, 'HAFU', 'Gefüllte Grießfrikadellen - Hackfleischfüllung', 'Eidelstedter Schlachterei ', 'sek', '2014-09-09 18:04:19', 'verkauf_an', '7', '6', '009'),
(106, 23, 6, 'HAFU', 'Gefüllte Grießfrikadellen - Hackfleischfüllung', 'Eidelstedter Schlachterei ', 'sek', '2014-09-09 18:04:19', 'beleg_nr', '2014/2', '2014/5', '009'),
(107, 22, 12, 'HAFU', 'Gefüllte Grießfrikadellen - Hackfleischfüllung', 'Dogutürk', 'sek', '2014-09-09 18:04:34', 'preis_kat', '2', '1', '009'),
(108, 22, 12, 'HAFU', 'Gefüllte Grießfrikadellen - Hackfleischfüllung', 'Dogutürk', 'sek', '2014-09-09 18:04:34', 'einzelpr_netto', '0.80', '0.60', '009'),
(109, 22, 12, 'HAFU', 'Gefüllte Grießfrikadellen - Hackfleischfüllung', 'Dogutürk', 'sek', '2014-09-09 18:04:34', 'gesamtpr_netto', '80.00', '60.00', '009'),
(110, 20, 12, 'HAFU', 'Gefüllte Grießfrikadellen - Hackfleischfüllung', 'Dogutürk', 'sek', '2014-09-10 16:11:26', 'GELÖSCHT', NULL, NULL, '010'),
(111, 24, 12, 'HAFU', 'Gefüllte Grießfrikadellen - Hackfleischfüllung', 'Dogutürk', 'sek', '2014-09-10 16:11:31', 'GELÖSCHT', NULL, NULL, '010'),
(112, 28, 10, 'KAFU', 'Gefüllte Grießfrikadellen - Kartoffelfüllung', 'Luruper Markt', 'sek', '2014-09-10 16:11:36', 'GELÖSCHT', NULL, NULL, '010'),
(113, 21, 12, 'HAFU', 'Gefüllte Grießfrikadellen - Hackfleischfüllung', 'Dogutürk', 'sek', '2014-09-10 16:17:28', 'mwst', '19.00', '7.00', '009'),
(114, 21, 12, 'HAFU', 'Gefüllte Grießfrikadellen - Hackfleischfüllung', 'Dogutürk', 'sek', '2014-09-10 16:17:28', 'einzelpr_netto', '0.50', '0.60', '009'),
(115, 21, 12, 'HAFU', 'Gefüllte Grießfrikadellen - Hackfleischfüllung', 'Dogutürk', 'sek', '2014-09-10 16:17:28', 'gesamtpr_netto', '538.40', '421.80', '009'),
(116, 27, 6, 'GEFU', 'Gefüllte Grießfrikadellen - Gemüsefüllung', 'Eidelstedter Schlachterei ', 'sek', '2014-09-10 18:01:44', 'menge', '500', '630', '009'),
(117, 27, 6, 'GEFU', 'Gefüllte Grießfrikadellen - Gemüsefüllung', 'Eidelstedter Schlachterei ', 'sek', '2014-09-10 18:01:44', 'gesamtpr_netto', '300.00', '312.00', '009'),
(118, 17, 12, 'HAFU', 'Gefüllte Grießfrikadellen - Hackfleischfüllung', 'Dogutürk', 'sek', '2014-09-10 18:24:06', 'menge', '10', '12', '009'),
(119, 17, 12, 'HAFU', 'Gefüllte Grießfrikadellen - Hackfleischfüllung', 'Dogutürk', 'sek', '2014-09-10 18:24:06', 'gesamtpr_netto', '6.00', '7.20', '009'),
(120, 17, 12, 'HAFU', 'Gefüllte Grießfrikadellen - Hackfleischfüllung', 'Dogutürk', 'sek', '2014-09-10 18:24:27', 'menge', '12', '15', '009'),
(121, 17, 12, 'HAFU', 'Gefüllte Grießfrikadellen - Hackfleischfüllung', 'Dogutürk', 'sek', '2014-09-10 18:24:27', 'gesamtpr_netto', '7.20', '9.00', '009'),
(122, 17, 12, 'HAFU', 'Gefüllte Grießfrikadellen - Hackfleischfüllung', 'Dogutürk', 'sek', '2014-09-10 18:25:00', 'menge', '15', '963', '009'),
(123, 17, 12, 'HAFU', 'Gefüllte Grießfrikadellen - Hackfleischfüllung', 'Dogutürk', 'sek', '2014-09-10 18:25:00', 'gesamtpr_netto', '9.00', '577.80', '009'),
(124, 17, 12, 'HAFU', 'Gefüllte Grießfrikadellen - Hackfleischfüllung', 'Dogutürk', 'sek', '2014-09-10 19:10:11', 'menge', '963', '985', '009'),
(125, 17, 12, 'HAFU', 'Gefüllte Grießfrikadellen - Hackfleischfüllung', 'Dogutürk', 'sek', '2014-09-10 19:10:11', 'gesamtpr_netto', '577.80', '591.00', '009'),
(126, 17, 12, 'HAFU', 'Gefüllte Grießfrikadellen - Hackfleischfüllung', 'Dogutürk', 'sek', '2014-09-10 19:10:30', 'menge', '985', '3', '009'),
(127, 17, 12, 'HAFU', 'Gefüllte Grießfrikadellen - Hackfleischfüllung', 'Dogutürk', 'sek', '2014-09-10 19:10:30', 'gesamtpr_netto', '591.00', '1.80', '009'),
(128, 19, 12, 'GEFU', 'Gefüllte Grießfrikadellen - Gemüsefüllung', 'Dogutürk', 'sek', '2014-09-10 19:15:38', 'menge', '1000', '1001', '009'),
(129, 19, 12, 'GEFU', 'Gefüllte Grießfrikadellen - Gemüsefüllung', 'Dogutürk', 'sek', '2014-09-10 19:15:38', 'gesamtpr_netto', '800.00', '800.80', '009'),
(130, 19, 12, 'GEFU', 'Gefüllte Grießfrikadellen - Gemüsefüllung', 'Dogutürk', 'sek', '2014-09-10 19:15:57', 'menge', '1001', '1000', '009'),
(131, 19, 12, 'GEFU', 'Gefüllte Grießfrikadellen - Gemüsefüllung', 'Dogutürk', 'sek', '2014-09-10 19:15:57', 'gesamtpr_netto', '800.80', '800.00', '009'),
(132, 22, 12, 'HAFU', 'Gefüllte Grießfrikadellen - Hackfleischfüllung', 'Dogutürk', 'sek', '2014-09-10 19:21:15', 'menge', '100', '1000', '009'),
(133, 22, 12, 'HAFU', 'Gefüllte Grießfrikadellen - Hackfleischfüllung', 'Dogutürk', 'sek', '2014-09-10 19:21:15', 'gesamtpr_netto', '60.00', '600.00', '009'),
(134, 23, 6, 'HAFU', 'Gefüllte Grießfrikadellen - Hackfleischfüllung', 'Eidelstedter Schlachterei ', 'sek', '2014-09-10 19:23:34', 'menge', '7', '8', '009'),
(135, 23, 6, 'HAFU', 'Gefüllte Grießfrikadellen - Hackfleischfüllung', 'Eidelstedter Schlachterei ', 'sek', '2014-09-10 19:23:34', 'gesamtpr_netto', '4.20', '4.80', '009'),
(136, 21, 12, 'HAFU', 'Gefüllte Grießfrikadellen - Hackfleischfüllung', 'Dogutürk', 'sek', '2014-09-10 23:10:32', 'menge', '703', '705', '009'),
(137, 21, 12, 'HAFU', 'Gefüllte Grießfrikadellen - Hackfleischfüllung', 'Dogutürk', 'sek', '2014-09-10 23:10:32', 'gesamtpr_netto', '421.80', '423.00', '009'),
(138, 21, 5, 'HAFU', 'Gefüllte Grießfrikadellen - Hackfleischfüllung', 'Erden Market', 'sek', '2014-09-10 23:20:30', 'verkauf_an', '12', '5', '009'),
(139, 21, 5, 'HAFU', 'Gefüllte Grießfrikadellen - Hackfleischfüllung', 'Erden Market', 'sek', '2014-09-10 23:20:30', 'menge', '705', '852', '009'),
(140, 21, 5, 'HAFU', 'Gefüllte Grießfrikadellen - Hackfleischfüllung', 'Erden Market', 'sek', '2014-09-10 23:20:30', 'beleg_nr', '2014/1', '2014/6', '009'),
(141, 21, 5, 'HAFU', 'Gefüllte Grießfrikadellen - Hackfleischfüllung', 'Erden Market', 'sek', '2014-09-10 23:20:30', 'gesamtpr_netto', '423.00', '511.20', '009'),
(142, 30, 6, 'HAFU', 'Gefüllte Grießfrikadellen - Hackfleischfüllung', 'Eidelstedter Schlachterei ', 'sek', '2014-09-10 23:25:13', 'NEU', NULL, NULL, '008'),
(143, 30, 6, 'HAFU', 'Gefüllte Grießfrikadellen - Hackfleischfüllung', 'Eidelstedter Schlachterei ', 'sek', '2014-09-10 23:25:19', 'GELÖSCHT', NULL, NULL, '010'),
(144, 1, 12, 'GEFU', 'Gefüllte Grießfrikadellen - Gemüsefüllung', 'Dogutürk', 'sek', '2014-09-10 23:57:59', 'GELÖSCHT', NULL, NULL, '010'),
(145, 21, 5, 'HAFU', 'Gefüllte Grießfrikadellen - Hackfleischfüllung', 'Erden Market', 'sek', '2014-09-10 23:58:06', 'GELÖSCHT', NULL, NULL, '010'),
(146, 3, 12, 'KAFU', 'Gefüllte Grießfrikadellen - Kartoffelfüllung', 'Dogutürk', 'sek', '2014-09-10 23:58:09', 'GELÖSCHT', NULL, NULL, '010'),
(147, 25, 12, 'HAFU', 'Gefüllte Grießfrikadellen - Hackfleischfüllung', 'Dogutürk', 'sek', '2014-09-10 23:58:13', 'GELÖSCHT', NULL, NULL, '010'),
(148, 23, 6, 'HAFU', 'Gefüllte Grießfrikadellen - Hackfleischfüllung', 'Eidelstedter Schlachterei ', 'sek', '2014-09-10 23:58:17', 'GELÖSCHT', NULL, NULL, '010'),
(149, 27, 6, 'GEFU', 'Gefüllte Grießfrikadellen - Gemüsefüllung', 'Eidelstedter Schlachterei ', 'sek', '2014-09-10 23:58:20', 'GELÖSCHT', NULL, NULL, '010'),
(150, 19, 12, 'GEFU', 'Gefüllte Grießfrikadellen - Gemüsefüllung', 'Dogutürk', 'sek', '2014-09-10 23:58:24', 'GELÖSCHT', NULL, NULL, '010'),
(151, 29, 7, 'HAFU', 'Gefüllte Grießfrikadellen - Hackfleischfüllung', 'Erden Market', 'sek', '2014-09-10 23:58:28', 'GELÖSCHT', NULL, NULL, '010'),
(152, 22, 12, 'HAFU', 'Gefüllte Grießfrikadellen - Hackfleischfüllung', 'Dogutürk', 'sek', '2014-09-10 23:58:31', 'GELÖSCHT', NULL, NULL, '010'),
(153, 17, 12, 'HAFU', 'Gefüllte Grießfrikadellen - Hackfleischfüllung', 'Dogutürk', 'sek', '2014-09-10 23:58:38', 'GELÖSCHT', NULL, NULL, '010'),
(154, 18, 7, 'KAFU', 'Gefüllte Grießfrikadellen - Kartoffelfüllung', 'Erden Market', 'sek', '2014-09-10 23:58:41', 'GELÖSCHT', NULL, NULL, '010'),
(155, 26, 12, 'HAFU', 'Gefüllte Grießfrikadellen - Hackfleischfüllung', 'Dogutürk', 'sek', '2014-09-10 23:58:45', 'GELÖSCHT', NULL, NULL, '010'),
(156, 31, 12, 'HAFU', 'Gefüllte Grießfrikadellen - Hackfleischfüllung', 'Dogutürk', 'sek', '2014-09-11 00:04:48', 'NEU', NULL, NULL, '008'),
(157, 32, 12, 'GEFU', 'Gefüllte Grießfrikadellen - Gemüsefüllung', 'Dogutürk', 'sek', '2014-09-11 00:05:37', 'NEU', NULL, NULL, '008'),
(158, 33, 12, 'KAFU', 'Gefüllte Grießfrikadellen - Kartoffelfüllung', 'Dogutürk', 'sek', '2014-09-11 00:06:20', 'NEU', NULL, NULL, '008'),
(159, 34, 4, 'HAFU', 'Gefüllte Grießfrikadellen - Hackfleischfüllung', 'Meydan Markt e.K. ', 'sek', '2014-09-11 00:10:02', 'NEU', NULL, NULL, '008'),
(160, 35, 4, 'KAFU', 'Gefüllte Grießfrikadellen - Kartoffelfüllung', 'Meydan Markt e.K. ', 'sek', '2014-09-11 00:10:30', 'NEU', NULL, NULL, '008'),
(161, 36, 9, 'HAFU', 'Gefüllte Grießfrikadellen - Hackfleischfüllung', 'Frisch & Lecker Edeka ', 'sek', '2014-09-11 00:13:25', 'NEU', NULL, NULL, '008'),
(162, 33, 12, 'KAFU', 'Gefüllte Grießfrikadellen - Kartoffelfüllung', 'Dogutürk', 'sek', '2014-09-11 20:52:54', 'menge', '500', '852', '009'),
(163, 33, 12, 'KAFU', 'Gefüllte Grießfrikadellen - Kartoffelfüllung', 'Dogutürk', 'sek', '2014-09-11 20:52:54', 'gesamtpr_netto', '300.00', '511.20', '009'),
(164, 37, 3, 'HAFU', 'Gefüllte Grießfrikadellen - Hackfleischfüllung', 'Öncü Supermarkt GmbH ', 'sek', '2014-09-11 21:57:53', 'NEU', NULL, NULL, '008'),
(165, 38, 3, 'HAFU', 'Gefüllte Grießfrikadellen - Hackfleischfüllung', 'Öncü Supermarkt GmbH ', 'sek', '2014-09-11 21:58:10', 'NEU', NULL, NULL, '008'),
(166, 33, 12, 'KAFU', 'Gefüllte Grießfrikadellen - Kartoffelfüllung', 'Dogutürk', 'sek', '2014-09-11 23:10:13', 'GELÖSCHT', NULL, NULL, '010'),
(167, 31, 12, 'HAFU', 'Gefüllte Grießfrikadellen - Hackfleischfüllung', 'Dogutürk', 'sek', '2014-09-11 23:10:21', 'GELÖSCHT', NULL, NULL, '010'),
(168, 39, 12, 'HAFU', 'Gefüllte Grießfrikadellen - Hackfleischfüllung', 'Dogutürk', 'sek', '2014-09-11 23:11:23', 'NEU', NULL, NULL, '008'),
(169, 39, 12, 'HAFU', 'Gefüllte Grießfrikadellen - Hackfleischfüllung', 'Dogutürk', 'sek', '2014-09-11 23:25:59', 'datum', '2014-09-11 00:00:00', '2014-09-06 00:00:00', '009'),
(170, 36, 9, 'HAFU', 'Gefüllte Grießfrikadellen - Hackfleischfüllung', 'Frisch & Lecker Edeka ', 'sek', '2014-09-11 23:28:11', 'menge', '300', '301', '009'),
(171, 36, 9, 'HAFU', 'Gefüllte Grießfrikadellen - Hackfleischfüllung', 'Frisch & Lecker Edeka ', 'sek', '2014-09-11 23:28:11', 'gesamtpr_netto', '180.00', '180.60', '009'),
(172, 36, 9, 'HAFU', 'Gefüllte Grießfrikadellen - Hackfleischfüllung', 'Frisch & Lecker Edeka ', 'sek', '2014-09-11 23:28:43', 'menge', '301', '300', '009'),
(173, 36, 9, 'HAFU', 'Gefüllte Grießfrikadellen - Hackfleischfüllung', 'Frisch & Lecker Edeka ', 'sek', '2014-09-11 23:28:43', 'gesamtpr_netto', '180.60', '180.00', '009'),
(174, 40, 2, 'HAFU', 'Gefüllte Grießfrikadellen - Hackfleischfüllung', 'Erden Market', 'sek', '2014-09-12 00:00:01', 'NEU', NULL, NULL, '008'),
(175, 41, 9, 'HAFU', 'Gefüllte Grießfrikadellen - Hackfleischfüllung', 'Frisch & Lecker Edeka ', 'sek', '2014-09-12 00:01:25', 'NEU', NULL, NULL, '008'),
(176, 42, 2, 'HAFU', 'Gefüllte Grießfrikadellen - Hackfleischfüllung', 'Erden Market', 'sek', '2014-09-12 00:14:20', 'NEU', NULL, NULL, '008'),
(177, 43, 5, 'HAFU', 'Gefüllte Grießfrikadellen - Hackfleischfüllung', 'Erden Market', 'sek', '2014-09-12 00:15:27', 'NEU', NULL, NULL, '008'),
(178, 44, 6, 'HAFU', 'Gefüllte Grießfrikadellen - Hackfleischfüllung', 'Eidelstedter Schlachterei ', 'sek', '2014-09-12 00:33:20', 'NEU', NULL, NULL, '008'),
(179, 39, 3, 'HAFU', 'Gefüllte Grießfrikadellen - Hackfleischfüllung', 'Öncü Supermarkt GmbH ', 'sek', '2014-09-12 00:50:02', 'verkauf_an', '12', '3', '009'),
(180, 39, 3, 'HAFU', 'Gefüllte Grießfrikadellen - Hackfleischfüllung', 'Öncü Supermarkt GmbH ', 'sek', '2014-09-12 00:50:02', 'datum', '2014-09-06 00:00:00', '2014-09-11 00:00:00', '009'),
(181, 39, 3, 'HAFU', 'Gefüllte Grießfrikadellen - Hackfleischfüllung', 'Öncü Supermarkt GmbH ', 'sek', '2014-09-12 00:50:02', 'beleg_nr', '2014/1', '2014/4', '009'),
(182, 45, 2, 'HAFU', 'Gefüllte Grießfrikadellen - Hackfleischfüllung', 'Erden Market', 'sek', '2014-09-12 00:51:03', 'NEU', NULL, NULL, '008'),
(183, 40, 5, 'HAFU', 'Gefüllte Grießfrikadellen - Hackfleischfüllung', 'Erden Market', 'sek', '2014-09-12 00:52:34', 'verkauf_an', '2', '5', '009'),
(184, 40, 5, 'HAFU', 'Gefüllte Grießfrikadellen - Hackfleischfüllung', 'Erden Market', 'sek', '2014-09-12 00:52:34', 'datum', '2014-09-11 00:00:00', '2014-09-12 00:00:00', '009'),
(185, 40, 5, 'HAFU', 'Gefüllte Grießfrikadellen - Hackfleischfüllung', 'Erden Market', 'sek', '2014-09-12 00:52:34', 'beleg_nr', '2014/5', '2014/7', '009'),
(186, 38, 12, 'HAFU', 'Gefüllte Grießfrikadellen - Hackfleischfüllung', 'Dogutürk', 'sek', '2014-09-12 00:55:56', 'verkauf_an', '3', '12', '009'),
(187, 38, 12, 'HAFU', 'Gefüllte Grießfrikadellen - Hackfleischfüllung', 'Dogutürk', 'sek', '2014-09-12 00:55:56', 'beleg_nr', '2014/4', '2014/1', '009'),
(188, 46, 9, 'HAFU', 'Gefüllte Grießfrikadellen - Hackfleischfüllung', 'Frisch & Lecker Edeka ', 'sek', '2014-09-12 00:56:57', 'NEU', NULL, NULL, '008'),
(189, 47, 8, 'HAFU', 'Gefüllte Grießfrikadellen - Hackfleischfüllung', 'Erden Market', 'sek', '2014-09-12 01:01:09', 'NEU', NULL, NULL, '008'),
(190, 47, 8, 'HAFU', 'Gefüllte Grießfrikadellen - Hackfleischfüllung', 'Erden Market', 'sek', '2014-09-12 01:05:40', 'GELÖSCHT', NULL, NULL, '010'),
(191, 46, 9, 'HAFU', 'Gefüllte Grießfrikadellen - Hackfleischfüllung', 'Frisch & Lecker Edeka ', 'sek', '2014-09-12 01:05:53', 'GELÖSCHT', NULL, NULL, '010'),
(192, 48, 5, 'HAFU', 'Gefüllte Grießfrikadellen - Hackfleischfüllung', 'Erden Market', 'sek', '2014-09-12 01:06:09', 'NEU', NULL, NULL, '008'),
(193, 48, 4, 'HAFU', 'Gefüllte Grießfrikadellen - Hackfleischfüllung', 'Meydan Markt e.K. ', 'sek', '2014-09-12 01:06:27', 'verkauf_an', '5', '4', '009'),
(194, 48, 4, 'HAFU', 'Gefüllte Grießfrikadellen - Hackfleischfüllung', 'Meydan Markt e.K. ', 'sek', '2014-09-12 01:06:27', 'datum', '2014-09-12 00:00:00', '2014-09-11 00:00:00', '009'),
(195, 48, 4, 'HAFU', 'Gefüllte Grießfrikadellen - Hackfleischfüllung', 'Meydan Markt e.K. ', 'sek', '2014-09-12 01:06:27', 'beleg_nr', '2014/9', '2014/2', '009'),
(196, 49, 2, 'HAFU', 'Gefüllte Grießfrikadellen - Hackfleischfüllung', 'Erden Market', 'sek', '2014-09-12 01:10:55', 'NEU', NULL, NULL, '008'),
(197, 50, 5, 'HAFU', 'Gefüllte Grießfrikadellen - Hackfleischfüllung', 'Erden Market', 'sek', '2014-09-12 01:11:06', 'NEU', NULL, NULL, '008'),
(198, 51, 5, 'HAFU', 'Gefüllte Grießfrikadellen - Hackfleischfüllung', 'Erden Market', 'sek', '2014-09-12 01:23:49', 'NEU', NULL, NULL, '008'),
(199, 52, 4, 'HAFU', 'Gefüllte Grießfrikadellen - Hackfleischfüllung', 'Meydan Markt e.K. ', 'sek', '2014-09-12 10:22:26', 'NEU', NULL, NULL, '008'),
(200, 35, 4, 'KAFU', 'Gefüllte Grießfrikadellen - Kartoffelfüllung', 'Meydan Markt e.K. ', 'sek', '2014-09-12 10:22:45', 'menge', '700', '800', '009'),
(201, 35, 4, 'KAFU', 'Gefüllte Grießfrikadellen - Kartoffelfüllung', 'Meydan Markt e.K. ', 'sek', '2014-09-12 10:22:45', 'gesamtpr_netto', '420.00', '480.00', '009'),
(202, 53, 5, 'HAFU', 'Gefüllte Grießfrikadellen - Hackfleischfüllung', 'Erden Market', 'sek', '2014-09-12 10:49:20', 'NEU', NULL, NULL, '008'),
(203, 52, 4, 'HAFU', 'Gefüllte Grießfrikadellen - Hackfleischfüllung', 'Meydan Markt e.K. ', 'sek', '2014-09-12 15:40:52', 'menge', '500', '100', '009'),
(204, 52, 4, 'HAFU', 'Gefüllte Grießfrikadellen - Hackfleischfüllung', 'Meydan Markt e.K. ', 'sek', '2014-09-12 15:40:52', 'gesamtpr_netto', '300.00', '60.00', '009'),
(205, 45, 2, 'HAFU', 'Gefüllte Grießfrikadellen - Hackfleischfüllung', 'Erden Market', 'sek', '2014-09-12 15:51:56', 'menge', '500', '504', '009'),
(206, 45, 2, 'HAFU', 'Gefüllte Grießfrikadellen - Hackfleischfüllung', 'Erden Market', 'sek', '2014-09-12 15:51:56', 'gesamtpr_netto', '300.00', '302.40', '009'),
(207, 34, 4, 'HAFU', 'Gefüllte Grießfrikadellen - Hackfleischfüllung', 'Meydan Markt e.K. ', 'sek', '2014-09-12 16:01:35', 'menge', '780', '700', '009'),
(208, 34, 4, 'HAFU', 'Gefüllte Grießfrikadellen - Hackfleischfüllung', 'Meydan Markt e.K. ', 'sek', '2014-09-12 16:01:35', 'gesamtpr_netto', '624.00', '560.00', '009'),
(209, 52, 4, 'HAFU', 'Gefüllte Grießfrikadellen - Hackfleischfüllung', 'Meydan Markt e.K. ', 'sek', '2014-09-12 16:01:52', 'menge', '100', '50', '009'),
(210, 52, 4, 'HAFU', 'Gefüllte Grießfrikadellen - Hackfleischfüllung', 'Meydan Markt e.K. ', 'sek', '2014-09-12 16:01:52', 'gesamtpr_netto', '60.00', '30.00', '009'),
(211, 54, 4, 'HAFU', 'Gefüllte Grießfrikadellen - Hackfleischfüllung', 'Meydan Markt e.K. ', 'sek', '2014-09-12 16:03:08', 'NEU', NULL, NULL, '008'),
(212, 55, 8, 'HAFU', 'Gefüllte Grießfrikadellen - Hackfleischfüllung', 'Erden Market', 'sek', '2014-09-13 02:00:35', 'NEU', NULL, NULL, '008'),
(213, 56, 8, 'HAFU', 'Gefüllte Grießfrikadellen - Hackfleischfüllung', 'Erden Market', 'sek', '2014-09-13 02:00:57', 'NEU', NULL, NULL, '008'),
(214, 57, 2, 'HAFU', 'Gefüllte Grießfrikadellen - Hackfleischfüllung', 'Erden Market', 'sek', '2014-09-13 12:56:39', 'NEU', NULL, NULL, '008'),
(215, 58, 2, 'GEFU', 'Gefüllte Grießfrikadellen - Gemüsefüllung', 'Erden Market', 'sek', '2014-09-13 12:56:59', 'NEU', NULL, NULL, '008'),
(216, 59, 12, 'GEFU', 'Gefüllte Grießfrikadellen - Gemüsefüllung', 'Dogutürk', 'sek', '2014-09-13 12:59:44', 'NEU', NULL, NULL, '008'),
(217, 60, 12, 'KAFU', 'Gefüllte Grießfrikadellen - Kartoffelfüllung', 'Dogutürk', 'sek', '2014-09-13 13:00:15', 'NEU', NULL, NULL, '008'),
(218, 61, 6, 'GEFU', 'Gefüllte Grießfrikadellen - Gemüsefüllung', 'Eidelstedter Schlachterei ', 'sek', '2014-09-14 01:23:46', 'NEU', NULL, NULL, '008'),
(219, 62, 5, 'GEFU', 'Gefüllte Grießfrikadellen - Gemüsefüllung', 'Erden Market', 'sek', '2014-09-14 01:24:50', 'NEU', NULL, NULL, '008'),
(220, 63, 12, 'HAFU', 'Gefüllte Grießfrikadellen - Hackfleischfüllung', 'Dogutürk', 'sek', '2014-09-14 01:27:43', 'NEU', NULL, NULL, '008'),
(221, 63, 12, 'HAFU', 'Gefüllte Grießfrikadellen - Hackfleischfüllung', 'Dogutürk', 'sek', '2014-09-14 01:28:47', 'menge', '800', '801', '009'),
(222, 63, 12, 'HAFU', 'Gefüllte Grießfrikadellen - Hackfleischfüllung', 'Dogutürk', 'sek', '2014-09-14 01:28:47', 'gesamtpr_netto', '480.00', '480.60', '009'),
(223, 64, 5, 'HAFU', 'Gefüllte Grießfrikadellen - Hackfleischfüllung', 'Erden Market', 'sek', '2014-09-14 01:29:39', 'NEU', NULL, NULL, '008'),
(224, 65, 5, 'HAFU', 'Gefüllte Grießfrikadellen - Hackfleischfüllung', 'Erden Market', 'sek', '2014-09-14 01:30:43', 'NEU', NULL, NULL, '008'),
(225, 66, 2, 'HAFU', 'Gefüllte Grießfrikadellen - Hackfleischfüllung', 'Erden Market', 'sek', '2014-09-14 01:33:00', 'NEU', NULL, NULL, '008'),
(226, 67, 9, 'KAFU', 'Gefüllte Grießfrikadellen - Kartoffelfüllung', 'Frisch & Lecker Edeka ', 'sek', '2014-09-14 01:33:24', 'NEU', NULL, NULL, '008'),
(227, 68, 5, 'HAFU', 'Gefüllte Grießfrikadellen - Hackfleischfüllung', 'Erden Market', 'sek', '2014-09-14 01:36:53', 'NEU', NULL, NULL, '008'),
(228, 68, 5, 'HAFU', 'Gefüllte Grießfrikadellen - Hackfleischfüllung', 'Erden Market', 'sek', '2014-09-14 01:37:26', 'GELÖSCHT', NULL, NULL, '010'),
(229, 43, 5, 'HAFU', 'Gefüllte Grießfrikadellen - Hackfleischfüllung', 'Erden Market', 'sek', '2014-09-14 01:37:31', 'GELÖSCHT', NULL, NULL, '010'),
(230, 40, 5, 'HAFU', 'Gefüllte Grießfrikadellen - Hackfleischfüllung', 'Erden Market', 'sek', '2014-09-14 01:37:37', 'GELÖSCHT', NULL, NULL, '010'),
(231, 53, 5, 'HAFU', 'Gefüllte Grießfrikadellen - Hackfleischfüllung', 'Erden Market', 'sek', '2014-09-14 01:37:51', 'GELÖSCHT', NULL, NULL, '010'),
(232, 50, 5, 'HAFU', 'Gefüllte Grießfrikadellen - Hackfleischfüllung', 'Erden Market', 'sek', '2014-09-14 01:37:57', 'GELÖSCHT', NULL, NULL, '010'),
(233, 65, 5, 'HAFU', 'Gefüllte Grießfrikadellen - Hackfleischfüllung', 'Erden Market', 'sek', '2014-09-14 01:38:09', 'GELÖSCHT', NULL, NULL, '010'),
(234, 51, 5, 'HAFU', 'Gefüllte Grießfrikadellen - Hackfleischfüllung', 'Erden Market', 'sek', '2014-09-14 01:38:13', 'GELÖSCHT', NULL, NULL, '010'),
(235, 60, 12, 'KAFU', 'Gefüllte Grießfrikadellen - Kartoffelfüllung', 'Dogutürk', 'sek', '2014-09-14 01:39:35', 'GELÖSCHT', NULL, NULL, '010'),
(236, 69, 12, 'GEFU', 'Gefüllte Grießfrikadellen - Gemüsefüllung', 'Dogutürk', 'sek', '2014-09-14 12:20:07', 'NEU', NULL, NULL, '008'),
(237, 70, 12, 'KAFU', 'Gefüllte Grießfrikadellen - Kartoffelfüllung', 'Dogutürk', 'sek', '2014-09-14 12:20:26', 'NEU', NULL, NULL, '008'),
(238, 63, 12, 'HAFU', 'Gefüllte Grießfrikadellen - Hackfleischfüllung', 'Dogutürk', 'sek', '2014-09-14 12:20:47', 'menge', '801', '800', '009'),
(239, 63, 12, 'HAFU', 'Gefüllte Grießfrikadellen - Hackfleischfüllung', 'Dogutürk', 'sek', '2014-09-14 12:20:47', 'gesamtpr_netto', '480.60', '480.00', '009'),
(240, 67, 9, 'KAFU', 'Gefüllte Grießfrikadellen - Kartoffelfüllung', 'Frisch & Lecker Edeka ', 'sek', '2014-09-14 12:25:38', 'GELÖSCHT', NULL, NULL, '010'),
(241, 70, 12, 'KAFU', 'Gefüllte Grießfrikadellen - Kartoffelfüllung', 'Dogutürk', 'sek', '2014-09-14 12:34:16', 'GELÖSCHT', NULL, NULL, '010'),
(242, 63, 12, 'HAFU', 'Gefüllte Grießfrikadellen - Hackfleischfüllung', 'Dogutürk', 'sek', '2014-09-14 12:43:51', 'GELÖSCHT', NULL, NULL, '010'),
(243, 69, 12, 'GEFU', 'Gefüllte Grießfrikadellen - Gemüsefüllung', 'Dogutürk', 'sek', '2014-09-14 12:44:06', 'GELÖSCHT', NULL, NULL, '010'),
(244, 62, 5, 'GEFU', 'Gefüllte Grießfrikadellen - Gemüsefüllung', 'Erden Market', 'sek', '2014-09-14 12:48:55', 'GELÖSCHT', NULL, NULL, '010'),
(245, 64, 5, 'HAFU', 'Gefüllte Grießfrikadellen - Hackfleischfüllung', 'Erden Market', 'sek', '2014-09-14 12:49:04', 'GELÖSCHT', NULL, NULL, '010'),
(246, 59, 12, 'GEFU', 'Gefüllte Grießfrikadellen - Gemüsefüllung', 'Dogutürk', 'sek', '2014-09-14 12:50:12', 'GELÖSCHT', NULL, NULL, '010'),
(247, 57, 2, 'HAFU', 'Gefüllte Grießfrikadellen - Hackfleischfüllung', 'Erden Market', 'sek', '2014-09-14 12:51:03', 'GELÖSCHT', NULL, NULL, '010'),
(248, 58, 2, 'GEFU', 'Gefüllte Grießfrikadellen - Gemüsefüllung', 'Erden Market', 'sek', '2014-09-14 12:51:12', 'GELÖSCHT', NULL, NULL, '010'),
(249, 42, 2, 'HAFU', 'Gefüllte Grießfrikadellen - Hackfleischfüllung', 'Erden Market', 'sek', '2014-09-14 12:53:25', 'GELÖSCHT', NULL, NULL, '010'),
(250, 61, 6, 'GEFU', 'Gefüllte Grießfrikadellen - Gemüsefüllung', 'Eidelstedter Schlachterei ', 'sek', '2014-09-14 12:56:22', 'GELÖSCHT', NULL, NULL, '010'),
(251, 45, 2, 'HAFU', 'Gefüllte Grießfrikadellen - Hackfleischfüllung', 'Erden Market', 'sek', '2014-09-14 12:56:34', 'GELÖSCHT', NULL, NULL, '010'),
(252, 39, 3, 'HAFU', 'Gefüllte Grießfrikadellen - Hackfleischfüllung', 'Öncü Supermarkt GmbH ', 'sek', '2014-09-14 12:56:44', 'GELÖSCHT', NULL, NULL, '010'),
(253, 37, 3, 'HAFU', 'Gefüllte Grießfrikadellen - Hackfleischfüllung', 'Öncü Supermarkt GmbH ', 'sek', '2014-09-14 12:56:54', 'GELÖSCHT', NULL, NULL, '010'),
(254, 48, 4, 'HAFU', 'Gefüllte Grießfrikadellen - Hackfleischfüllung', 'Meydan Markt e.K. ', 'sek', '2014-09-14 12:57:06', 'GELÖSCHT', NULL, NULL, '010'),
(255, 54, 4, 'HAFU', 'Gefüllte Grießfrikadellen - Hackfleischfüllung', 'Meydan Markt e.K. ', 'sek', '2014-09-14 12:57:10', 'GELÖSCHT', NULL, NULL, '010'),
(256, 35, 4, 'KAFU', 'Gefüllte Grießfrikadellen - Kartoffelfüllung', 'Meydan Markt e.K. ', 'sek', '2014-09-14 12:57:14', 'GELÖSCHT', NULL, NULL, '010'),
(257, 52, 4, 'HAFU', 'Gefüllte Grießfrikadellen - Hackfleischfüllung', 'Meydan Markt e.K. ', 'sek', '2014-09-14 12:57:19', 'GELÖSCHT', NULL, NULL, '010'),
(258, 34, 4, 'HAFU', 'Gefüllte Grießfrikadellen - Hackfleischfüllung', 'Meydan Markt e.K. ', 'sek', '2014-09-14 12:57:24', 'GELÖSCHT', NULL, NULL, '010'),
(259, 36, 9, 'HAFU', 'Gefüllte Grießfrikadellen - Hackfleischfüllung', 'Frisch & Lecker Edeka ', 'sek', '2014-09-14 12:57:38', 'GELÖSCHT', NULL, NULL, '010'),
(260, 41, 9, 'HAFU', 'Gefüllte Grießfrikadellen - Hackfleischfüllung', 'Frisch & Lecker Edeka ', 'sek', '2014-09-14 12:57:43', 'GELÖSCHT', NULL, NULL, '010'),
(261, 38, 12, 'HAFU', 'Gefüllte Grießfrikadellen - Hackfleischfüllung', 'Dogutürk', 'sek', '2014-09-14 12:57:53', 'GELÖSCHT', NULL, NULL, '010'),
(262, 32, 12, 'GEFU', 'Gefüllte Grießfrikadellen - Gemüsefüllung', 'Dogutürk', 'sek', '2014-09-14 12:58:03', 'GELÖSCHT', NULL, NULL, '010'),
(263, 66, 2, 'HAFU', 'Gefüllte Grießfrikadellen - Hackfleischfüllung', 'Erden Market', 'sek', '2014-09-14 12:58:10', 'GELÖSCHT', NULL, NULL, '010'),
(264, 49, 2, 'HAFU', 'Gefüllte Grießfrikadellen - Hackfleischfüllung', 'Erden Market', 'sek', '2014-09-14 12:58:14', 'GELÖSCHT', NULL, NULL, '010'),
(265, 44, 6, 'HAFU', 'Gefüllte Grießfrikadellen - Hackfleischfüllung', 'Eidelstedter Schlachterei ', 'sek', '2014-09-14 12:58:21', 'GELÖSCHT', NULL, NULL, '010'),
(266, 56, 8, 'HAFU', 'Gefüllte Grießfrikadellen - Hackfleischfüllung', 'Erden Market', 'sek', '2014-09-14 12:58:28', 'GELÖSCHT', NULL, NULL, '010'),
(267, 55, 8, 'HAFU', 'Gefüllte Grießfrikadellen - Hackfleischfüllung', 'Erden Market', 'sek', '2014-09-14 12:58:32', 'GELÖSCHT', NULL, NULL, '010'),
(268, 71, 14, 'GEFU', 'Gefüllte Grießfrikadellen - Gemüsefüllung', 'ONUR GmbH & Co. KG', 'sek', '2014-09-14 12:58:54', 'NEU', NULL, NULL, '008'),
(269, 72, 14, 'HAFU', 'Gefüllte Grießfrikadellen - Hackfleischfüllung', 'ONUR GmbH & Co. KG', 'sek', '2014-09-14 12:59:07', 'NEU', NULL, NULL, '008'),
(270, 73, 14, 'KAFU', 'Gefüllte Grießfrikadellen - Kartoffelfüllung', 'ONUR GmbH & Co. KG', 'sek', '2014-09-14 12:59:19', 'NEU', NULL, NULL, '008'),
(271, 72, 14, 'HAFU', 'Gefüllte Grießfrikadellen - Hackfleischfüllung', 'ONUR GmbH & Co. KG', 'sek', '2014-09-14 13:00:48', 'menge', '900', '800', '009'),
(272, 72, 14, 'HAFU', 'Gefüllte Grießfrikadellen - Hackfleischfüllung', 'ONUR GmbH & Co. KG', 'sek', '2014-09-14 13:00:48', 'gesamtpr_netto', '540.00', '480.00', '009'),
(273, 73, 14, 'KAFU', 'Gefüllte Grießfrikadellen - Kartoffelfüllung', 'ONUR GmbH & Co. KG', 'sek', '2014-09-14 13:01:45', 'menge', '1000', '800', '009'),
(274, 73, 14, 'KAFU', 'Gefüllte Grießfrikadellen - Kartoffelfüllung', 'ONUR GmbH & Co. KG', 'sek', '2014-09-14 13:01:45', 'gesamtpr_netto', '600.00', '480.00', '009'),
(275, 74, 8, 'HAFU', 'Gefüllte Grießfrikadellen - Hackfleischfüllung', 'Erden Market', 'sek', '2014-09-14 13:02:17', 'NEU', NULL, NULL, '008'),
(276, 72, 14, 'HAFU', 'Gefüllte Grießfrikadellen - Hackfleischfüllung', 'ONUR GmbH & Co. KG', 'sek', '2014-09-14 17:38:53', 'GELÖSCHT', NULL, NULL, '010'),
(277, 73, 14, 'KAFU', 'Gefüllte Grießfrikadellen - Kartoffelfüllung', 'ONUR GmbH & Co. KG', 'sek', '2014-09-14 17:39:02', 'GELÖSCHT', NULL, NULL, '010'),
(278, 71, 14, 'GEFU', 'Gefüllte Grießfrikadellen - Gemüsefüllung', 'ONUR GmbH & Co. KG', 'sek', '2014-09-14 17:39:14', 'GELÖSCHT', NULL, NULL, '010'),
(279, 74, 8, 'HAFU', 'Gefüllte Grießfrikadellen - Hackfleischfüllung', 'Erden Market', 'sek', '2014-09-14 17:55:15', 'GELÖSCHT', NULL, NULL, '010'),
(280, 75, 12, 'HAFU', 'Gefüllte Grießfrikadellen - Hackfleischfüllung', 'Dogutürk', 'sek', '2014-09-14 17:55:43', 'NEU', NULL, NULL, '008'),
(281, 76, 12, 'KAFU', 'Gefüllte Grießfrikadellen - Kartoffelfüllung', 'Dogutürk', 'sek', '2014-09-14 17:55:57', 'NEU', NULL, NULL, '008'),
(282, 76, 12, 'KAFU', 'Gefüllte Grießfrikadellen - Kartoffelfüllung', 'Dogutürk', 'sek', '2014-09-14 17:56:05', 'GELÖSCHT', NULL, NULL, '010'),
(283, 77, 12, 'GEFU', 'Gefüllte Grießfrikadellen - Gemüsefüllung', 'Dogutürk', 'sek', '2014-09-14 17:58:59', 'NEU', NULL, NULL, '008'),
(284, 75, 12, 'HAFU', 'Gefüllte Grießfrikadellen - Hackfleischfüllung', 'Dogutürk', 'sek', '2014-09-14 17:59:48', 'GELÖSCHT', NULL, NULL, '010'),
(285, 77, 12, 'GEFU', 'Gefüllte Grießfrikadellen - Gemüsefüllung', 'Dogutürk', 'sek', '2014-09-14 18:01:29', 'GELÖSCHT', NULL, NULL, '010'),
(286, 78, 6, 'GEFU', 'Gefüllte Grießfrikadellen - Gemüsefüllung', 'Eidelstedter Schlachterei ', 'sek', '2014-09-14 18:02:14', 'NEU', NULL, NULL, '008'),
(287, 79, 6, 'KAFU', 'Gefüllte Grießfrikadellen - Kartoffelfüllung', 'Eidelstedter Schlachterei ', 'sek', '2014-09-14 18:02:26', 'NEU', NULL, NULL, '008'),
(288, 80, 6, 'HAFU', 'Gefüllte Grießfrikadellen - Hackfleischfüllung', 'Eidelstedter Schlachterei ', 'sek', '2014-09-14 18:02:38', 'NEU', NULL, NULL, '008'),
(289, 79, 6, 'KAFU', 'Gefüllte Grießfrikadellen - Kartoffelfüllung', 'Eidelstedter Schlachterei ', 'sek', '2014-09-14 18:04:49', 'mwst', '19.00', '7.00', '009'),
(290, 81, 16, 'HAFU', 'Gefüllte Grießfrikadellen - Hackfleischfüllung', 'SÖNMEZ Markt', 'sek', '2014-09-14 18:07:07', 'NEU', NULL, NULL, '008'),
(291, 82, 16, 'GEFU', 'Gefüllte Grießfrikadellen - Gemüsefüllung', 'SÖNMEZ Markt', 'sek', '2014-09-14 18:07:21', 'NEU', NULL, NULL, '008'),
(292, 83, 16, 'KAFU', 'Gefüllte Grießfrikadellen - Kartoffelfüllung', 'SÖNMEZ Markt', 'sek', '2014-09-14 18:07:35', 'NEU', NULL, NULL, '008'),
(293, 83, 16, 'KAFU', 'Gefüllte Grießfrikadellen - Kartoffelfüllung', 'SÖNMEZ Markt', 'sek', '2014-09-14 18:11:20', 'menge', '600', '650', '009'),
(294, 83, 16, 'KAFU', 'Gefüllte Grießfrikadellen - Kartoffelfüllung', 'SÖNMEZ Markt', 'sek', '2014-09-14 18:11:20', 'gesamtpr_netto', '360.00', '390.00', '009'),
(295, 84, 12, 'GEFU', 'Gefüllte Grießfrikadellen - Gemüsefüllung', 'Dogutürk', 'sek', '2014-09-14 18:12:14', 'NEU', NULL, NULL, '008'),
(296, 85, 12, 'HAFU', 'Gefüllte Grießfrikadellen - Hackfleischfüllung', 'Dogutürk', 'sek', '2014-09-14 20:13:35', 'NEU', NULL, NULL, '008'),
(297, 86, 12, 'KAFU', 'Gefüllte Grießfrikadellen - Kartoffelfüllung', 'Dogutürk', 'sek', '2014-09-14 20:13:50', 'NEU', NULL, NULL, '008'),
(298, 87, 12, 'GEFU', 'Gefüllte Grießfrikadellen - Gemüsefüllung', 'Dogutürk', 'sek', '2014-09-14 20:16:48', 'NEU', NULL, NULL, '008'),
(299, 88, 12, 'HAFU', 'Gefüllte Grießfrikadellen - Hackfleischfüllung', 'Dogutürk', 'sek', '2014-09-14 20:17:10', 'NEU', NULL, NULL, '008'),
(300, 89, 12, 'KAFU', 'Gefüllte Grießfrikadellen - Kartoffelfüllung', 'Dogutürk', 'sek', '2014-09-14 20:17:35', 'NEU', NULL, NULL, '008'),
(301, 1, 22276713, 'HAFU', 'Gefüllte Grießfrikadellen - Hackfleischfüllung', 'Erden Market', 'sek', '2014-09-16 02:47:20', 'NEU', NULL, NULL, '008'),
(302, 2, 3221113, 'HAFU', 'Gefüllte Grießfrikadellen - Hackfleischfüllung', 'Öncü Supermarkt GmbH ', 'sek', '2014-09-16 02:51:08', 'NEU', NULL, NULL, '008'),
(303, 3, 22276713, 'GEFU', 'Gefüllte Grießfrikadellen - Gemüsefüllung', 'Erden Market', 'sek', '2014-09-17 00:39:11', 'NEU', NULL, NULL, '008'),
(304, 4, 3221113, 'GEFU', 'Gefüllte Grießfrikadellen - Gemüsefüllung', 'Öncü Supermarkt GmbH ', 'sek', '2014-09-17 08:39:02', 'NEU', NULL, NULL, '008'),
(305, 2, 3221113, 'HAFU', 'Gefüllte Grießfrikadellen - Hackfleischfüllung', '3', 'sek', '2014-09-17 08:39:16', 'preis_kat', '1', '2', '009'),
(306, 2, 3221113, 'HAFU', 'Gefüllte Grießfrikadellen - Hackfleischfüllung', '3', 'sek', '2014-09-17 08:39:16', 'einzelpr_netto', '0.60', '0.80', '009'),
(307, 2, 3221113, 'HAFU', 'Gefüllte Grießfrikadellen - Hackfleischfüllung', '3', 'sek', '2014-09-17 08:39:16', 'gesamtpr_netto', '300.00', '400.00', '009'),
(308, 5, 112009915, 'GEFU', 'Gefüllte Grießfrikadellen - Gemüsefüllung', 'SÖNMEZ Markt', 'sek', '2014-09-17 08:55:29', 'NEU', NULL, NULL, '008'),
(309, 6, 112009915, 'HAFU', 'Gefüllte Grießfrikadellen - Hackfleischfüllung', 'SÖNMEZ Markt', 'sek', '2014-09-17 08:55:47', 'NEU', NULL, NULL, '008'),
(310, 7, 112009915, 'KAFU', 'Gefüllte Grießfrikadellen - Kartoffelfüllung', 'SÖNMEZ Markt', 'sek', '2014-09-17 08:56:00', 'NEU', NULL, NULL, '008'),
(311, 8, 4221194, 'GEFU', 'Gefüllte Grießfrikadellen - Gemüsefüllung', 'Meydan Markt e.K. ', 'sek', '2014-09-17 08:56:34', 'NEU', NULL, NULL, '008'),
(312, 9, 4221194, 'HAFU', 'Gefüllte Grießfrikadellen - Hackfleischfüllung', 'Meydan Markt e.K. ', 'sek', '2014-09-17 08:56:49', 'NEU', NULL, NULL, '008'),
(313, 10, 4221194, 'KAFU', 'Gefüllte Grießfrikadellen - Kartoffelfüllung', 'Meydan Markt e.K. ', 'sek', '2014-09-17 08:57:04', 'NEU', NULL, NULL, '008'),
(314, 11, 72254710, 'GEFU', 'Gefüllte Grießfrikadellen - Gemüsefüllung', 'Luruper Markt', 'sek', '2014-09-17 08:57:30', 'NEU', NULL, NULL, '008'),
(315, 12, 72254710, 'HAFU', 'Gefüllte Grießfrikadellen - Hackfleischfüllung', 'Luruper Markt', 'sek', '2014-09-17 08:57:44', 'NEU', NULL, NULL, '008'),
(316, 13, 72254710, 'KAFU', 'Gefüllte Grießfrikadellen - Kartoffelfüllung', 'Luruper Markt', 'sek', '2014-09-17 08:58:01', 'NEU', NULL, NULL, '008'),
(317, 14, 82276711, 'HAFU', 'Gefüllte Grießfrikadellen - Hackfleischfüllung', 'Altona City Markt', 'sek', '2014-09-17 08:58:34', 'NEU', NULL, NULL, '008'),
(318, 15, 82276711, 'GEFU', 'Gefüllte Grießfrikadellen - Gemüsefüllung', 'Altona City Markt', 'sek', '2014-09-17 08:58:46', 'NEU', NULL, NULL, '008'),
(319, 16, 82276711, 'KAFU', 'Gefüllte Grießfrikadellen - Kartoffelfüllung', 'Altona City Markt', 'sek', '2014-09-17 08:59:58', 'NEU', NULL, NULL, '008'),
(320, 17, 2225498, 'GEFU', 'Gefüllte Grießfrikadellen - Gemüsefüllung', 'Erden Market', 'sek', '2014-09-17 23:54:44', 'NEU', NULL, NULL, '008');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `hist_bem`
--

DROP TABLE IF EXISTS `hist_bem`;
CREATE TABLE IF NOT EXISTS `hist_bem` (
  `code` char(3) COLLATE utf8_bin NOT NULL,
  `codetext` varchar(64) COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Daten für Tabelle `hist_bem`
--

INSERT INTO `hist_bem` (`code`, `codetext`) VALUES
('001', 'Neuen Kunden angelegt'),
('002', 'Kunden editiert'),
('003', 'Kunden entfernt'),
('004', 'Neues Produkt angelegt'),
('005', 'Produkt editiert'),
('006', 'Produkt entfernt'),
('007', 'Produkt-Bild hochgeladen'),
('008', 'Neue Abrechnung erstellt'),
('009', 'Abrechnung editiert'),
('010', 'Abrechnung entfernt'),
('011', 'Filiale angelegt'),
('012', 'Filiale editiert'),
('013', 'Filiale gelöscht');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `hist_kunden`
--

DROP TABLE IF EXISTS `hist_kunden`;
CREATE TABLE IF NOT EXISTS `hist_kunden` (
  `lfn` int(11) NOT NULL AUTO_INCREMENT,
  `schluessel` int(11) NOT NULL,
  `name` varchar(250) COLLATE utf8_bin NOT NULL,
  `user` varchar(15) COLLATE utf8_bin NOT NULL,
  `aenderdat` datetime NOT NULL,
  `feld` varchar(264) COLLATE utf8_bin NOT NULL,
  `a_inhalt` varchar(264) COLLATE utf8_bin DEFAULT NULL,
  `n_inhalt` varchar(264) COLLATE utf8_bin DEFAULT NULL,
  `codetext` char(3) COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`lfn`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=92 ;

--
-- Daten für Tabelle `hist_kunden`
--

INSERT INTO `hist_kunden` (`lfn`, `schluessel`, `name`, `user`, `aenderdat`, `feld`, `a_inhalt`, `n_inhalt`, `codetext`) VALUES
(31, 17, '', 'sek', '2014-08-26 15:47:26', 'NEU', NULL, NULL, '001'),
(32, 17, '', 'sek', '2014-08-26 15:47:35', 'GELÖSCHT', NULL, NULL, '003'),
(33, 14, '', 'sek', '2014-08-26 20:02:07', 'GELÖSCHT', NULL, NULL, '003'),
(34, 18, '', 'sek', '2014-08-26 20:02:40', 'NEU', NULL, NULL, '001'),
(35, 18, '', 'sek', '2014-08-26 20:02:54', 'GELÖSCHT', NULL, NULL, '003'),
(36, 19, '', 'sek', '2014-08-26 20:03:38', 'NEU', NULL, NULL, '001'),
(37, 19, '', 'sek', '2014-08-26 20:04:09', 'GELÖSCHT', NULL, NULL, '003'),
(38, 5, '', 'sek', '2014-08-27 09:32:20', 'stadtteil', 'Steilshoopp', 'Steilshoop', '002'),
(39, 5, '', 'sek', '2014-08-27 15:12:22', 'stadtteil', 'Steilshoop', 'Steilshoopp', '002'),
(40, 5, 'Erden Market', 'sek', '2014-08-27 15:15:46', 'stadtteil', 'Steilshoopp', 'Steilshoop', '002'),
(41, 14, 'ONUR GmbH & Co. KG', 'sek', '2014-08-29 17:53:48', 'NEU', NULL, NULL, '001'),
(42, 15, 'SÖNMEZ Markt', 'sek', '2014-08-29 17:55:13', 'NEU', NULL, NULL, '001'),
(43, 16, 'SÖNMEZ Markt', 'sek', '2014-08-29 17:56:29', 'NEU', NULL, NULL, '001'),
(44, 17, 'ÖZKA Market', 'sek', '2014-08-29 17:58:39', 'NEU', NULL, NULL, '001'),
(45, 18, 'Test', 'sek', '2014-08-30 19:31:46', 'NEU', NULL, NULL, '001'),
(46, 18, '', 'sek', '2014-08-30 19:31:57', 'GELÖSCHT', NULL, NULL, '003'),
(47, 19, 'Erden Market', 'sek', '2014-09-02 01:06:58', 'NEU', NULL, NULL, '001'),
(48, 19, 'Erden Market', 'sek', '2014-09-02 01:07:51', 'stadtteil', 'Langenhorn', 'Hummelsbüttel', '002'),
(49, 19, '', 'sek', '2014-09-08 18:43:35', 'GELÖSCHT', NULL, NULL, '003'),
(50, 1, 'Yakamoz Markt', 'sek', '2014-09-08 19:39:12', 'aktiv', '1', '0', '002'),
(51, 1, 'Yakamoz Markt', 'sek', '2014-09-10 19:37:30', 'aktiv', '0', '1', '002'),
(52, 20, 'Erden Market', 'sek', '2014-09-10 20:13:08', 'NEU', NULL, NULL, '001'),
(53, 12, 'Do?utürk', 'sek', '2014-09-15 02:12:03', 'name', 'Dogutürk', 'Do?utürk', '002'),
(54, 12, 'Dogutürk', 'sek', '2014-09-15 02:13:31', 'name', 'Do?utürk', 'Dogutürk', '002'),
(55, 9, 'Frisch & Lecker Edeka ', 'sek', '2014-09-15 18:19:43', 'zahlfrist', '14', '15', '002'),
(56, 9, 'Frisch & Lecker Edeka ', 'sek', '2014-09-15 18:19:43', 'mahnstufe', '1', '2', '002'),
(57, 9, 'Frisch & Lecker Edeka ', 'sek', '2014-09-15 18:20:05', 'zahlfrist', '15', '14', '002'),
(58, 9, 'Frisch & Lecker Edeka ', 'sek', '2014-09-15 18:20:05', 'mahnstufe', '2', '1', '002'),
(59, 15, 'SÖNMEZ Markt', 'sek', '2014-09-15 18:20:38', 'zahlfrist', '14', '7', '002'),
(60, 15, 'SÖNMEZ Markt', 'sek', '2014-09-15 18:20:38', 'mahnstufe', '1', '2', '002'),
(61, 16, 'SÖNMEZ Markt', 'sek', '2014-09-15 18:20:45', 'zahlfrist', '14', '7', '002'),
(62, 16, 'SÖNMEZ Markt', 'sek', '2014-09-15 18:20:45', 'mahnstufe', '1', '2', '002'),
(63, 22258921, 'Erden Market', 'sek', '2014-09-16 02:31:09', 'NEU', NULL, NULL, '001'),
(64, 22258921, 'Erden Market', 'sek', '2014-09-16 02:40:01', 'nr', '36', '35', '002'),
(65, 22258921, 'Erden Market', 'sek', '2014-09-16 15:34:43', 'name', 'Luruper Markt', 'Erden Market', '002'),
(66, 22285121, 'Erden Market', 'sek', '2014-09-16 15:37:29', 'ort', 'Hamburg', 'Norderstedt', '002'),
(67, 22285121, 'Erden Market', 'sek', '2014-09-16 15:37:29', 'plz', '22589', '22851', '002'),
(68, 22285121, 'Erden Market', 'sek', '2014-09-16 15:37:29', 'stadtteil', 'Blankenese', 'Norderstedt', '002'),
(69, 22285121, '', 'sek', '2014-09-16 15:38:37', 'GELÖSCHT', NULL, NULL, '003'),
(70, 32285122, 'Öncü Supermarkt GmbH ', 'sek', '2014-09-16 15:39:29', 'NEU', NULL, NULL, '001'),
(71, 16, 'Peters Frittenbude', 'sek', '2014-09-16 19:46:51', 'NEU', NULL, NULL, '011'),
(72, 17, 'Jonnys Styling House', 'sek', '2014-09-16 19:47:29', 'NEU', NULL, NULL, '011'),
(73, 16, 'Peters Frittenbud', 'sek', '2014-09-16 20:22:42', 'name', 'Peters Frittenbude', 'Peters Frittenbud', '012'),
(74, 17, 'Jonnys Styling House', 'sek', '2014-09-16 20:23:29', 'name', 'Jonnys Styling Hous', 'Jonnys Styling House', '012'),
(75, 17, 'Jonnys Slaughting House', 'sek', '2014-09-16 20:23:46', 'name', 'Jonnys Styling House', 'Jonnys Slaughting House', '012'),
(76, 17, 'Ömer Üründüls Döner Schuppen', 'sek', '2014-09-16 20:24:11', 'name', 'Jonnys Slaughting House', 'Ömer Üründüls Döner Schuppen', '012'),
(77, 16, 'Peters Frittenbude', 'sek', '2014-09-16 23:36:00', 'name', 'Peters Frittenbud', 'Peters Frittenbude', '012'),
(78, 18, 'Heinz Ketchup', 'sek', '2014-09-16 23:39:18', 'NEU', NULL, NULL, '011'),
(79, 18, 'Heinz Ketchupp', 'sek', '2014-09-16 23:39:31', 'name', 'Heinz Ketchup', 'Heinz Ketchupp', '012'),
(80, 18, 'Heinz Ketchup', 'sek', '2014-09-16 23:39:35', 'name', 'Heinz Ketchupp', 'Heinz Ketchup', '012'),
(81, 18, '', 'sek', '2014-09-17 00:00:02', 'GELÖSCHT', NULL, NULL, '013'),
(82, 16, '', 'sek', '2014-09-17 00:00:18', 'GELÖSCHT', NULL, NULL, '013'),
(83, 17, '', 'sek', '2014-09-17 00:00:47', 'GELÖSCHT', NULL, NULL, '013'),
(84, 19, 'Ahmeds Dream Palast', 'sek', '2014-09-17 00:01:51', 'NEU', NULL, NULL, '011'),
(85, 19, '', 'sek', '2014-09-17 00:02:12', 'GELÖSCHT', NULL, NULL, '013'),
(86, 2, 'Erden Markett', 'sek', '2014-09-17 00:06:18', 'name', 'Erden Market', 'Erden Markett', '012'),
(87, 2, 'Erden Market', 'sek', '2014-09-17 00:14:37', 'name', 'Erden Markett', 'Erden Market', '012'),
(88, 2, 'Erden Markettt', 'sek', '2014-09-17 00:54:39', 'name', 'Erden Market', 'Erden Markettt', '012'),
(89, 2, 'Erden Market', 'sek', '2014-09-17 00:54:47', 'name', 'Erden Markettt', 'Erden Market', '012'),
(90, 2, 'Test', 'sek', '2014-09-17 01:31:05', 'name', 'Erden Market', 'Test', '012'),
(91, 2, 'Erden Market', 'sek', '2014-09-17 01:31:13', 'name', 'Test', 'Erden Market', '012');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `hist_produkte`
--

DROP TABLE IF EXISTS `hist_produkte`;
CREATE TABLE IF NOT EXISTS `hist_produkte` (
  `lfn` int(11) NOT NULL AUTO_INCREMENT,
  `schluessel` char(4) COLLATE utf8_bin NOT NULL,
  `name` varchar(264) COLLATE utf8_bin NOT NULL,
  `user` varchar(15) COLLATE utf8_bin NOT NULL,
  `aenderdat` datetime NOT NULL,
  `feld` varchar(264) COLLATE utf8_bin NOT NULL,
  `a_inhalt` varchar(264) COLLATE utf8_bin DEFAULT NULL,
  `n_inhalt` varchar(264) COLLATE utf8_bin DEFAULT NULL,
  `codetext` char(3) COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`lfn`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=68 ;

--
-- Daten für Tabelle `hist_produkte`
--

INSERT INTO `hist_produkte` (`lfn`, `schluessel`, `name`, `user`, `aenderdat`, `feld`, `a_inhalt`, `n_inhalt`, `codetext`) VALUES
(44, '1234', '', 'sek', '2014-08-26 19:52:13', 'GELÖSCHT', NULL, NULL, '006'),
(45, '1235', '', 'sek', '2014-08-26 19:52:17', 'GELÖSCHT', NULL, NULL, '006'),
(46, '1236', '', 'sek', '2014-08-26 19:52:20', 'GELÖSCHT', NULL, NULL, '006'),
(47, 'SUAT', '', 'sek', '2014-08-26 22:59:16', 'NEUES BILD', NULL, NULL, '007'),
(48, 'SUAT', '', 'sek', '2014-08-26 22:59:37', 'NEUES BILD', NULL, NULL, '007'),
(49, 'SUAT', '', 'sek', '2014-08-26 23:17:22', 'NEUES BILD', NULL, NULL, '007'),
(50, 'KAFU', '', 'sek', '2014-08-27 07:36:15', 'netto_preis1', '0.60', '0.61', '005'),
(51, 'KAFU', '', 'sek', '2014-08-27 07:36:44', 'netto_preis1', '0.61', '0.60', '005'),
(52, 'SUAT', '', 'sek', '2014-08-27 07:38:20', 'aktiv', '1', '0', '005'),
(53, 'GEFU', '', 'sek', '2014-08-27 07:39:37', 'netto_preis2', '9999.99', '0.80', '005'),
(54, 'SUAT', 'Suattt', 'sek', '2014-08-27 18:19:47', 'bezeichnung', 'Suat', 'Suattt', '005'),
(55, 'SUAT', 'Suat', 'sek', '2014-08-27 18:20:13', 'bezeichnung', 'Suattt', 'Suat', '005'),
(56, 'FDFP', 'faddsfdsf', 'sek', '2014-08-27 23:41:10', 'NEU', NULL, NULL, '004'),
(57, 'SADD', 'sdfds', 'sek', '2014-08-27 23:41:47', 'NEU', NULL, NULL, '004'),
(58, 'SADD', '', 'sek', '2014-08-27 23:42:07', 'GELÖSCHT', NULL, NULL, '006'),
(59, 'FDFP', '', 'sek', '2014-08-27 23:42:11', 'GELÖSCHT', NULL, NULL, '006'),
(60, 'SUAT', 'Suat', 'sek', '2014-08-29 18:14:28', 'aktiv', '0', '1', '005'),
(61, 'SUAT', '', 'sek', '2014-08-29 18:16:05', 'NEUES BILD', NULL, NULL, '007'),
(62, 'SUAT', '', 'sek', '2014-08-30 11:08:12', 'NEUES BILD', NULL, NULL, '007'),
(63, 'SUAT', 'Suat', 'sek', '2014-08-30 19:32:27', 'aktiv', '1', '0', '005'),
(64, 'KAFU', 'Gefüllte Grießfrikadellen - Kartoffelfüllung', 'sek', '2014-09-10 15:41:48', 'mwst', '7.00', '19.00', '005'),
(65, 'KAFU', 'Gefüllte Grießfrikadellen - Kartoffelfüllung', 'sek', '2014-09-14 18:04:21', 'mwst', '19.00', '7.00', '005'),
(66, 'KAFU', 'Gefüllte Grießfrikadellen - Kartoffelfüllung', 'sek', '2014-09-14 18:11:56', 'aktiv', '1', '0', '005'),
(67, 'KAFU', 'Gefüllte Grießfrikadellen - Kartoffelfüllung', 'sek', '2014-09-14 18:12:22', 'aktiv', '0', '1', '005');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `kunden`
--

DROP TABLE IF EXISTS `kunden`;
CREATE TABLE IF NOT EXISTS `kunden` (
  `lfd_nr` int(11) NOT NULL AUTO_INCREMENT,
  `filial_nr` int(11) NOT NULL,
  `strasse` varchar(250) CHARACTER SET utf8 DEFAULT NULL,
  `nr` varchar(10) CHARACTER SET utf8 NOT NULL,
  `plz` char(5) CHARACTER SET utf8 NOT NULL,
  `ort` varchar(64) CHARACTER SET utf8 DEFAULT NULL,
  `stadtteil` varchar(64) CHARACTER SET utf8 DEFAULT NULL,
  `adresszusatz` varchar(250) CHARACTER SET utf8 DEFAULT NULL,
  `aktiv` bit(1) NOT NULL,
  `telefon` varchar(45) CHARACTER SET utf8 DEFAULT NULL,
  `fax` varchar(45) CHARACTER SET utf8 DEFAULT NULL,
  `email` varchar(264) CHARACTER SET utf8 DEFAULT NULL,
  `mahnstufe` tinyint(4) DEFAULT '1',
  `zahlfrist` tinyint(4) DEFAULT '14',
  PRIMARY KEY (`lfd_nr`,`plz`,`filial_nr`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=23 ;

--
-- Daten für Tabelle `kunden`
--

INSERT INTO `kunden` (`lfd_nr`, `filial_nr`, `strasse`, `nr`, `plz`, `ort`, `stadtteil`, `adresszusatz`, `aktiv`, `telefon`, `fax`, `email`, `mahnstufe`, `zahlfrist`) VALUES
(1, 1, 'Borstelmannsweg', '38', '20537', 'Hamburg', 'Hamm', NULL, b'1', NULL, NULL, 'test@mail.com', 1, 14),
(2, 2, 'Neugrabener Bahnhofstraße', '10a', '21149', 'Hamburg', 'Neugraben', NULL, b'1', '040 76 96 97 20', NULL, NULL, 1, 14),
(3, 3, 'Billstedter Hauptstraße', '49', '22111', 'Hamburg', 'Billstedt', NULL, b'1', '040 73 12 78 38', NULL, NULL, 1, 14),
(4, 4, 'Hermannsthal', '103', '22119', 'Hamburg', 'Billstedt', NULL, b'1', '040 63 94 64 27', NULL, NULL, 1, 14),
(5, 2, 'Gründgensstraße', '26', '22309', 'Hamburg', 'Steilshoop', NULL, b'1', '040 64 86 20 48', NULL, NULL, 1, 14),
(6, 5, 'Alte Elbgaustraße', '16', '22523', 'Hamburg', 'Eidelstedt', NULL, b'1', '040 55 28 92 77', NULL, NULL, 1, 14),
(7, 2, 'Lohkampstraße', '11', '22523', 'Hamburg', 'Eidelstedt', NULL, b'1', '040 27 86 95 12', NULL, NULL, 1, 14),
(8, 2, 'Bornheide', '23', '22549', 'Hamburg', 'Osdorf', NULL, b'1', '040 28 66 83 47', NULL, NULL, 1, 14),
(9, 6, 'Bornheide', '55a', '22549', 'Hamburg', 'Osdorf', 'Osdorfer Born Center', b'1', '040 831 74 22', NULL, NULL, 1, 14),
(10, 7, 'Luruper Hauptstraße', '138', '22547', 'Hamburg', 'Lurup', NULL, b'1', NULL, NULL, NULL, 1, 14),
(11, 8, 'Große Bergstraße', '237', '22767', 'Hamburg', 'Altona', NULL, b'1', '040 380 62 76', NULL, NULL, 1, 14),
(12, 9, 'Große Bergstraße', '187', '22767', 'Hamburg', 'Altona', NULL, b'1', '040 38 90 77 76', NULL, NULL, 1, 14),
(13, 2, 'Blücherstraße', '3', '22767', 'Hamburg', 'Altona', NULL, b'1', '040 38 08 68 20', NULL, NULL, 1, 14),
(14, 10, 'Kleiner Schippsee', '15', '21073', 'Hamburg', 'Harburg', NULL, b'1', '040 76 75 55 01', NULL, NULL, 1, 14),
(15, 11, 'Steindamm', '33', '20099', 'Hamburg', 'St. Georg', NULL, b'1', '040 24 46 84', NULL, NULL, 2, 7),
(16, 11, 'Steindamm', '8', '20099', 'Hamburg', 'St. Georg', NULL, b'1', '040 24 54 84', NULL, NULL, 2, 7),
(17, 12, 'Steindamm', '47', '20099', 'Hamburg', 'St. Georg', NULL, b'1', '040 28 66 97 77', NULL, NULL, 1, 14),
(20, 2, 'Große Bergstraße', '6', '22767', 'Hamburg', 'Ottensen', NULL, b'1', NULL, NULL, NULL, 1, 14),
(22, 3, 'Blücherstraße', '99', '22851', 'Norderstedt', 'Norderstedt', NULL, b'1', NULL, NULL, NULL, 1, 14);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `laufende_ausgaben`
--

DROP TABLE IF EXISTS `laufende_ausgaben`;
CREATE TABLE IF NOT EXISTS `laufende_ausgaben` (
  `lfd_nr` int(11) NOT NULL AUTO_INCREMENT,
  `ausg_art_kz` char(4) COLLATE utf8_bin NOT NULL,
  `ausg_kz` char(4) COLLATE utf8_bin NOT NULL,
  `betrag` decimal(8,2) NOT NULL,
  `datum` datetime NOT NULL,
  `beleg` varchar(200) COLLATE utf8_bin DEFAULT NULL,
  PRIMARY KEY (`lfd_nr`,`ausg_art_kz`,`ausg_kz`),
  KEY `ausg_kz_idx` (`ausg_kz`),
  KEY `ausg_art_kz_lfd_idx` (`ausg_art_kz`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=2 ;

--
-- Daten für Tabelle `laufende_ausgaben`
--

INSERT INTO `laufende_ausgaben` (`lfd_nr`, `ausg_art_kz`, `ausg_kz`, `betrag`, `datum`, `beleg`) VALUES
(1, 'PROD', 'LOGE', '3265.65', '2014-03-25 00:00:00', NULL);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `plz_tabelle`
--

DROP TABLE IF EXISTS `plz_tabelle`;
CREATE TABLE IF NOT EXISTS `plz_tabelle` (
  `lkz` char(2) CHARACTER SET utf8 DEFAULT NULL,
  `plz` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `ort_stadtteil` varchar(264) CHARACTER SET utf8 NOT NULL,
  `bundesland` varchar(264) CHARACTER SET utf8 NOT NULL,
  `blkz` char(2) COLLATE utf8_unicode_ci NOT NULL,
  `kreis` varchar(264) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Daten für Tabelle `plz_tabelle`
--

INSERT INTO `plz_tabelle` (`lkz`, `plz`, `ort_stadtteil`, `bundesland`, `blkz`, `kreis`) VALUES
('DE', '10115', 'Berlin', 'Berlin', 'BE', 'Kreisfreie Stadt Berlin'),
('DE', '10117', 'Berlin', 'Berlin', 'BE', 'Kreisfreie Stadt Berlin'),
('DE', '10119', 'Berlin', 'Berlin', 'BE', 'Kreisfreie Stadt Berlin'),
('DE', '10178', 'Berlin', 'Berlin', 'BE', 'Berlin, Stadt'),
('DE', '10179', 'Berlin', 'Berlin', 'BE', 'Kreisfreie Stadt Berlin'),
('DE', '10243', 'Berlin Friedrichshain', 'Berlin', 'BE', 'Kreisfreie Stadt Berlin'),
('DE', '10245', 'Berlin Friedrichshain', 'Berlin', 'BE', 'Kreisfreie Stadt Berlin'),
('DE', '10247', 'Berlin Friedrichshain', 'Berlin', 'BE', 'Kreisfreie Stadt Berlin'),
('DE', '10249', 'Berlin Friedrichshain', 'Berlin', 'BE', 'Kreisfreie Stadt Berlin'),
('DE', '10315', 'Berlin', 'Berlin', 'BE', 'Kreisfreie Stadt Berlin'),
('DE', '10317', 'Berlin', 'Berlin', 'BE', 'Kreisfreie Stadt Berlin'),
('DE', '10318', 'Berlin', 'Berlin', 'BE', 'Kreisfreie Stadt Berlin'),
('DE', '10319', 'Berlin', 'Berlin', 'BE', 'Kreisfreie Stadt Berlin'),
('DE', '10365', 'Berlin', 'Berlin', 'BE', 'Kreisfreie Stadt Berlin'),
('DE', '10367', 'Berlin', 'Berlin', 'BE', 'Kreisfreie Stadt Berlin'),
('DE', '10369', 'Berlin', 'Berlin', 'BE', 'Kreisfreie Stadt Berlin'),
('DE', '10405', 'Berlin', 'Berlin', 'BE', 'Kreisfreie Stadt Berlin'),
('DE', '10407', 'Berlin', 'Berlin', 'BE', 'Kreisfreie Stadt Berlin'),
('DE', '10409', 'Berlin', 'Berlin', 'BE', 'Kreisfreie Stadt Berlin'),
('DE', '10435', 'Berlin', 'Berlin', 'BE', 'Kreisfreie Stadt Berlin'),
('DE', '10437', 'Berlin', 'Berlin', 'BE', 'Kreisfreie Stadt Berlin'),
('DE', '10439', 'Berlin', 'Berlin', 'BE', 'Kreisfreie Stadt Berlin'),
('DE', '10551', 'Berlin', 'Berlin', 'BE', 'Kreisfreie Stadt Berlin'),
('DE', '10553', 'Berlin', 'Berlin', 'BE', 'Kreisfreie Stadt Berlin'),
('DE', '10555', 'Berlin', 'Berlin', 'BE', 'Kreisfreie Stadt Berlin'),
('DE', '10557', 'Berlin', 'Berlin', 'BE', 'Kreisfreie Stadt Berlin'),
('DE', '10559', 'Berlin', 'Berlin', 'BE', 'Kreisfreie Stadt Berlin'),
('DE', '10585', 'Berlin', 'Berlin', 'BE', 'Kreisfreie Stadt Berlin'),
('DE', '10587', 'Berlin', 'Berlin', 'BE', 'Kreisfreie Stadt Berlin'),
('DE', '10589', 'Berlin', 'Berlin', 'BE', 'Kreisfreie Stadt Berlin'),
('DE', '10623', 'Berlin', 'Berlin', 'BE', 'Kreisfreie Stadt Berlin'),
('DE', '10625', 'Berlin', 'Berlin', 'BE', 'Kreisfreie Stadt Berlin'),
('DE', '10627', 'Berlin', 'Berlin', 'BE', 'Kreisfreie Stadt Berlin'),
('DE', '10629', 'Berlin', 'Berlin', 'BE', 'Kreisfreie Stadt Berlin'),
('DE', '10707', 'Berlin', 'Berlin', 'BE', 'Kreisfreie Stadt Berlin'),
('DE', '10709', 'Berlin', 'Berlin', 'BE', 'Kreisfreie Stadt Berlin'),
('DE', '10711', 'Berlin', 'Berlin', 'BE', 'Kreisfreie Stadt Berlin'),
('DE', '10713', 'Berlin', 'Berlin', 'BE', 'Kreisfreie Stadt Berlin'),
('DE', '10715', 'Berlin', 'Berlin', 'BE', 'Kreisfreie Stadt Berlin'),
('DE', '10717', 'Berlin', 'Berlin', 'BE', 'Kreisfreie Stadt Berlin'),
('DE', '10719', 'Berlin', 'Berlin', 'BE', 'Kreisfreie Stadt Berlin'),
('DE', '10777', 'Berlin', 'Berlin', 'BE', 'Kreisfreie Stadt Berlin'),
('DE', '10779', 'Berlin', 'Berlin', 'BE', 'Kreisfreie Stadt Berlin'),
('DE', '10781', 'Berlin', 'Berlin', 'BE', 'Kreisfreie Stadt Berlin'),
('DE', '10783', 'Berlin', 'Berlin', 'BE', 'Kreisfreie Stadt Berlin'),
('DE', '10785', 'Berlin', 'Berlin', 'BE', 'Kreisfreie Stadt Berlin'),
('DE', '10787', 'Berlin', 'Berlin', 'BE', 'Kreisfreie Stadt Berlin'),
('DE', '10789', 'Berlin', 'Berlin', 'BE', 'Kreisfreie Stadt Berlin'),
('DE', '10823', 'Berlin', 'Berlin', 'BE', 'Kreisfreie Stadt Berlin'),
('DE', '10825', 'Berlin', 'Berlin', 'BE', 'Kreisfreie Stadt Berlin'),
('DE', '10827', 'Berlin', 'Berlin', 'BE', 'Kreisfreie Stadt Berlin'),
('DE', '10829', 'Berlin', 'Berlin', 'BE', 'Kreisfreie Stadt Berlin'),
('DE', '10961', 'Berlin Kreuzberg', 'Berlin', 'BE', 'Kreisfreie Stadt Berlin'),
('DE', '10963', 'Berlin Kreuzberg', 'Berlin', 'BE', 'Kreisfreie Stadt Berlin'),
('DE', '10965', 'Berlin Kreuzberg', 'Berlin', 'BE', 'Kreisfreie Stadt Berlin'),
('DE', '10967', 'Berlin Kreuzberg', 'Berlin', 'BE', 'Kreisfreie Stadt Berlin'),
('DE', '10969', 'Berlin Kreuzberg', 'Berlin', 'BE', 'Kreisfreie Stadt Berlin'),
('DE', '10997', 'Berlin Kreuzberg', 'Berlin', 'BE', 'Kreisfreie Stadt Berlin'),
('DE', '10999', 'Berlin Kreuzberg', 'Berlin', 'BE', 'Kreisfreie Stadt Berlin'),
('DE', '12043', 'Berlin', 'Berlin', 'BE', 'Kreisfreie Stadt Berlin'),
('DE', '12045', 'Berlin', 'Berlin', 'BE', 'Kreisfreie Stadt Berlin'),
('DE', '12047', 'Berlin', 'Berlin', 'BE', 'Kreisfreie Stadt Berlin'),
('DE', '12049', 'Berlin', 'Berlin', 'BE', 'Kreisfreie Stadt Berlin'),
('DE', '12051', 'Berlin', 'Berlin', 'BE', 'Kreisfreie Stadt Berlin'),
('DE', '12053', 'Berlin', 'Berlin', 'BE', 'Kreisfreie Stadt Berlin'),
('DE', '12055', 'Berlin', 'Berlin', 'BE', 'Kreisfreie Stadt Berlin'),
('DE', '12057', 'Berlin', 'Berlin', 'BE', 'Kreisfreie Stadt Berlin'),
('DE', '12059', 'Berlin', 'Berlin', 'BE', 'Kreisfreie Stadt Berlin'),
('DE', '12099', 'Berlin', 'Berlin', 'BE', 'Kreisfreie Stadt Berlin'),
('DE', '12101', 'Berlin', 'Berlin', 'BE', 'Kreisfreie Stadt Berlin'),
('DE', '12103', 'Berlin', 'Berlin', 'BE', 'Kreisfreie Stadt Berlin'),
('DE', '12105', 'Berlin', 'Berlin', 'BE', 'Kreisfreie Stadt Berlin'),
('DE', '12107', 'Berlin', 'Berlin', 'BE', 'Kreisfreie Stadt Berlin'),
('DE', '12109', 'Berlin', 'Berlin', 'BE', 'Kreisfreie Stadt Berlin'),
('DE', '12157', 'Berlin', 'Berlin', 'BE', 'Kreisfreie Stadt Berlin'),
('DE', '12159', 'Berlin', 'Berlin', 'BE', 'Kreisfreie Stadt Berlin'),
('DE', '12161', 'Berlin', 'Berlin', 'BE', 'Kreisfreie Stadt Berlin'),
('DE', '12163', 'Berlin', 'Berlin', 'BE', 'Kreisfreie Stadt Berlin'),
('DE', '12165', 'Berlin', 'Berlin', 'BE', 'Kreisfreie Stadt Berlin'),
('DE', '12167', 'Berlin', 'Berlin', 'BE', 'Kreisfreie Stadt Berlin'),
('DE', '12169', 'Berlin', 'Berlin', 'BE', 'Kreisfreie Stadt Berlin'),
('DE', '12203', 'Berlin', 'Berlin', 'BE', 'Kreisfreie Stadt Berlin'),
('DE', '12205', 'Berlin', 'Berlin', 'BE', 'Kreisfreie Stadt Berlin'),
('DE', '12207', 'Berlin', 'Berlin', 'BE', 'Kreisfreie Stadt Berlin'),
('DE', '12209', 'Berlin', 'Berlin', 'BE', 'Kreisfreie Stadt Berlin'),
('DE', '12247', 'Berlin', 'Berlin', 'BE', 'Kreisfreie Stadt Berlin'),
('DE', '12249', 'Berlin', 'Berlin', 'BE', 'Kreisfreie Stadt Berlin'),
('DE', '12277', 'Berlin', 'Berlin', 'BE', 'Kreisfreie Stadt Berlin'),
('DE', '12279', 'Berlin', 'Berlin', 'BE', 'Kreisfreie Stadt Berlin'),
('DE', '12305', 'Berlin', 'Berlin', 'BE', 'Kreisfreie Stadt Berlin'),
('DE', '12307', 'Berlin', 'Berlin', 'BE', 'Kreisfreie Stadt Berlin'),
('DE', '12309', 'Berlin', 'Berlin', 'BE', 'Kreisfreie Stadt Berlin'),
('DE', '12347', 'Berlin', 'Berlin', 'BE', 'Kreisfreie Stadt Berlin'),
('DE', '12349', 'Berlin', 'Berlin', 'BE', 'Kreisfreie Stadt Berlin'),
('DE', '12351', 'Berlin', 'Berlin', 'BE', 'Kreisfreie Stadt Berlin'),
('DE', '12353', 'Berlin', 'Berlin', 'BE', 'Kreisfreie Stadt Berlin'),
('DE', '12355', 'Berlin', 'Berlin', 'BE', 'Kreisfreie Stadt Berlin'),
('DE', '12357', 'Berlin', 'Berlin', 'BE', 'Kreisfreie Stadt Berlin'),
('DE', '12359', 'Berlin', 'Berlin', 'BE', 'Kreisfreie Stadt Berlin'),
('DE', '12435', 'Berlin', 'Berlin', 'BE', 'Kreisfreie Stadt Berlin'),
('DE', '12437', 'Berlin', 'Berlin', 'BE', 'Kreisfreie Stadt Berlin'),
('DE', '12439', 'Berlin', 'Berlin', 'BE', 'Kreisfreie Stadt Berlin'),
('DE', '12459', 'Berlin', 'Berlin', 'BE', 'Kreisfreie Stadt Berlin'),
('DE', '12487', 'Berlin', 'Berlin', 'BE', 'Kreisfreie Stadt Berlin'),
('DE', '12489', 'Berlin', 'Berlin', 'BE', 'Kreisfreie Stadt Berlin'),
('DE', '12524', 'Berlin', 'Berlin', 'BE', 'Kreisfreie Stadt Berlin'),
('DE', '12526', 'Berlin', 'Berlin', 'BE', 'Kreisfreie Stadt Berlin'),
('DE', '12527', 'Berlin', 'Berlin', 'BE', 'Kreisfreie Stadt Berlin'),
('DE', '12555', 'Berlin', 'Berlin', 'BE', 'Berlin, Stadt'),
('DE', '12557', 'Berlin', 'Berlin', 'BE', 'Kreisfreie Stadt Berlin'),
('DE', '12559', 'Berlin', 'Berlin', 'BE', 'Kreisfreie Stadt Berlin'),
('DE', '12587', 'Berlin', 'Berlin', 'BE', 'Kreisfreie Stadt Berlin'),
('DE', '12589', 'Berlin', 'Berlin', 'BE', 'Kreisfreie Stadt Berlin'),
('DE', '12619', 'Berlin', 'Berlin', 'BE', 'Kreisfreie Stadt Berlin'),
('DE', '12621', 'Berlin', 'Berlin', 'BE', 'Kreisfreie Stadt Berlin'),
('DE', '12623', 'Berlin', 'Berlin', 'BE', 'Kreisfreie Stadt Berlin'),
('DE', '12627', 'Berlin', 'Berlin', 'BE', 'Kreisfreie Stadt Berlin'),
('DE', '12629', 'Berlin', 'Berlin', 'BE', 'Kreisfreie Stadt Berlin'),
('DE', '12679', 'Berlin', 'Berlin', 'BE', 'Kreisfreie Stadt Berlin'),
('DE', '12681', 'Berlin', 'Berlin', 'BE', 'Kreisfreie Stadt Berlin'),
('DE', '12683', 'Berlin', 'Berlin', 'BE', 'Kreisfreie Stadt Berlin'),
('DE', '12685', 'Berlin', 'Berlin', 'BE', 'Kreisfreie Stadt Berlin'),
('DE', '12687', 'Berlin', 'Berlin', 'BE', 'Kreisfreie Stadt Berlin'),
('DE', '12689', 'Berlin', 'Berlin', 'BE', 'Kreisfreie Stadt Berlin'),
('DE', '13051', 'Berlin', 'Berlin', 'BE', 'Kreisfreie Stadt Berlin'),
('DE', '13053', 'Berlin', 'Berlin', 'BE', 'Kreisfreie Stadt Berlin'),
('DE', '13055', 'Berlin', 'Berlin', 'BE', 'Kreisfreie Stadt Berlin'),
('DE', '13057', 'Berlin', 'Berlin', 'BE', 'Kreisfreie Stadt Berlin'),
('DE', '13059', 'Berlin', 'Berlin', 'BE', 'Kreisfreie Stadt Berlin'),
('DE', '13086', 'Berlin', 'Berlin', 'BE', 'Kreisfreie Stadt Berlin'),
('DE', '13088', 'Berlin', 'Berlin', 'BE', 'Kreisfreie Stadt Berlin'),
('DE', '13089', 'Berlin', 'Berlin', 'BE', 'Kreisfreie Stadt Berlin'),
('DE', '13125', 'Berlin', 'Berlin', 'BE', 'Kreisfreie Stadt Berlin'),
('DE', '13127', 'Berlin', 'Berlin', 'BE', 'Kreisfreie Stadt Berlin'),
('DE', '13129', 'Berlin', 'Berlin', 'BE', 'Kreisfreie Stadt Berlin'),
('DE', '13156', 'Berlin', 'Berlin', 'BE', 'Kreisfreie Stadt Berlin'),
('DE', '13158', 'Berlin', 'Berlin', 'BE', 'Kreisfreie Stadt Berlin'),
('DE', '13159', 'Berlin', 'Berlin', 'BE', 'Kreisfreie Stadt Berlin'),
('DE', '13187', 'Berlin', 'Berlin', 'BE', 'Kreisfreie Stadt Berlin'),
('DE', '13189', 'Berlin', 'Berlin', 'BE', 'Kreisfreie Stadt Berlin'),
('DE', '13347', 'Berlin', 'Berlin', 'BE', 'Kreisfreie Stadt Berlin'),
('DE', '13349', 'Berlin', 'Berlin', 'BE', 'Kreisfreie Stadt Berlin'),
('DE', '13351', 'Berlin', 'Berlin', 'BE', 'Kreisfreie Stadt Berlin'),
('DE', '13353', 'Berlin', 'Berlin', 'BE', 'Kreisfreie Stadt Berlin'),
('DE', '13355', 'Berlin', 'Berlin', 'BE', 'Kreisfreie Stadt Berlin'),
('DE', '13357', 'Berlin', 'Berlin', 'BE', 'Kreisfreie Stadt Berlin'),
('DE', '13359', 'Berlin', 'Berlin', 'BE', 'Kreisfreie Stadt Berlin'),
('DE', '13403', 'Berlin', 'Berlin', 'BE', 'Kreisfreie Stadt Berlin'),
('DE', '13405', 'Berlin', 'Berlin', 'BE', 'Kreisfreie Stadt Berlin'),
('DE', '13407', 'Berlin', 'Berlin', 'BE', 'Kreisfreie Stadt Berlin'),
('DE', '13409', 'Berlin', 'Berlin', 'BE', 'Kreisfreie Stadt Berlin'),
('DE', '13435', 'Berlin', 'Berlin', 'BE', 'Kreisfreie Stadt Berlin'),
('DE', '13437', 'Berlin', 'Berlin', 'BE', 'Kreisfreie Stadt Berlin'),
('DE', '13439', 'Berlin', 'Berlin', 'BE', 'Kreisfreie Stadt Berlin'),
('DE', '13465', 'Berlin', 'Berlin', 'BE', 'Kreisfreie Stadt Berlin'),
('DE', '13467', 'Berlin', 'Berlin', 'BE', 'Kreisfreie Stadt Berlin'),
('DE', '13469', 'Berlin', 'Berlin', 'BE', 'Kreisfreie Stadt Berlin'),
('DE', '13503', 'Berlin', 'Berlin', 'BE', 'Kreisfreie Stadt Berlin'),
('DE', '13505', 'Berlin', 'Berlin', 'BE', 'Kreisfreie Stadt Berlin'),
('DE', '13507', 'Berlin', 'Berlin', 'BE', 'Kreisfreie Stadt Berlin'),
('DE', '13509', 'Berlin', 'Berlin', 'BE', 'Kreisfreie Stadt Berlin'),
('DE', '13581', 'Berlin', 'Berlin', 'BE', 'Kreisfreie Stadt Berlin'),
('DE', '13583', 'Berlin', 'Berlin', 'BE', 'Berlin, Stadt'),
('DE', '13585', 'Berlin', 'Berlin', 'BE', 'Kreisfreie Stadt Berlin'),
('DE', '13587', 'Berlin', 'Berlin', 'BE', 'Berlin, Stadt'),
('DE', '13589', 'Berlin', 'Berlin', 'BE', 'Berlin, Stadt'),
('DE', '13591', 'Berlin', 'Berlin', 'BE', 'Kreisfreie Stadt Berlin'),
('DE', '13593', 'Berlin', 'Berlin', 'BE', 'Berlin, Stadt'),
('DE', '13595', 'Berlin', 'Berlin', 'BE', 'Berlin, Stadt'),
('DE', '13597', 'Berlin', 'Berlin', 'BE', 'Kreisfreie Stadt Berlin'),
('DE', '13599', 'Berlin', 'Berlin', 'BE', 'Kreisfreie Stadt Berlin'),
('DE', '13627', 'Berlin', 'Berlin', 'BE', 'Kreisfreie Stadt Berlin'),
('DE', '13629', 'Berlin', 'Berlin', 'BE', 'Kreisfreie Stadt Berlin'),
('DE', '14050', 'Berlin', 'Berlin', 'BE', 'Kreisfreie Stadt Berlin'),
('DE', '14052', 'Berlin', 'Berlin', 'BE', 'Kreisfreie Stadt Berlin'),
('DE', '14053', 'Berlin', 'Berlin', 'BE', 'Kreisfreie Stadt Berlin'),
('DE', '14055', 'Berlin', 'Berlin', 'BE', 'Kreisfreie Stadt Berlin'),
('DE', '14057', 'Berlin', 'Berlin', 'BE', 'Kreisfreie Stadt Berlin'),
('DE', '14059', 'Berlin', 'Berlin', 'BE', 'Kreisfreie Stadt Berlin'),
('DE', '14089', 'Berlin', 'Berlin', 'BE', 'Kreisfreie Stadt Berlin'),
('DE', '14109', 'Berlin', 'Berlin', 'BE', 'Kreisfreie Stadt Berlin'),
('DE', '14129', 'Berlin', 'Berlin', 'BE', 'Kreisfreie Stadt Berlin'),
('DE', '14131', 'Berlin', 'Berlin', 'BE', 'Kreisfreie Stadt Berlin'),
('DE', '14163', 'Berlin', 'Berlin', 'BE', 'Kreisfreie Stadt Berlin'),
('DE', '14165', 'Berlin', 'Berlin', 'BE', 'Kreisfreie Stadt Berlin'),
('DE', '14167', 'Berlin', 'Berlin', 'BE', 'Kreisfreie Stadt Berlin'),
('DE', '14169', 'Berlin', 'Berlin', 'BE', 'Kreisfreie Stadt Berlin'),
('DE', '14193', 'Berlin', 'Berlin', 'BE', 'Kreisfreie Stadt Berlin'),
('DE', '14195', 'Berlin', 'Berlin', 'BE', 'Kreisfreie Stadt Berlin'),
('DE', '14197', 'Berlin', 'Berlin', 'BE', 'Kreisfreie Stadt Berlin'),
('DE', '14199', 'Berlin', 'Berlin', 'BE', 'Kreisfreie Stadt Berlin'),
('DE', '27568', 'Bremerhaven', 'Bremen', 'HB', 'Bremerhaven, Stadt'),
('DE', '27570', 'Bremerhaven', 'Bremen', 'HB', 'Bremerhaven, Stadt'),
('DE', '27572', 'Bremerhaven', 'Bremen', 'HB', 'Bremerhaven, Stadt'),
('DE', '27574', 'Bremerhaven', 'Bremen', 'HB', 'Bremerhaven, Stadt'),
('DE', '27576', 'Bremerhaven', 'Bremen', 'HB', 'Bremerhaven, Stadt'),
('DE', '27578', 'Bremerhaven', 'Bremen', 'HB', 'Bremerhaven, Stadt'),
('DE', '27580', 'Bremerhaven', 'Bremen', 'HB', 'Bremerhaven, Stadt'),
('DE', '28195', 'Bremen', 'Bremen', 'HB', 'Bremen, Stadt'),
('DE', '28197', 'Bremen', 'Bremen', 'HB', 'Bremen, Stadt'),
('DE', '28199', 'Bremen', 'Bremen', 'HB', 'Bremen, Stadt'),
('DE', '28201', 'Bremen', 'Bremen', 'HB', 'Bremen, Stadt'),
('DE', '28203', 'Bremen', 'Bremen', 'HB', 'Bremen, Stadt'),
('DE', '28205', 'Bremen', 'Bremen', 'HB', 'Bremen, Stadt'),
('DE', '28207', 'Bremen', 'Bremen', 'HB', 'Bremen, Stadt'),
('DE', '28209', 'Bremen', 'Bremen', 'HB', 'Bremen, Stadt'),
('DE', '28211', 'Bremen', 'Bremen', 'HB', 'Bremen, Stadt'),
('DE', '28213', 'Bremen', 'Bremen', 'HB', 'Bremen, Stadt'),
('DE', '28215', 'Bremen', 'Bremen', 'HB', 'Bremen, Stadt'),
('DE', '28217', 'Bremen', 'Bremen', 'HB', 'Bremen, Stadt'),
('DE', '28219', 'Bremen', 'Bremen', 'HB', 'Bremen, Stadt'),
('DE', '28237', 'Bremen', 'Bremen', 'HB', 'Bremen, Stadt'),
('DE', '28239', 'Bremen', 'Bremen', 'HB', 'Bremen, Stadt'),
('DE', '28259', 'Bremen', 'Bremen', 'HB', 'Bremen, Stadt'),
('DE', '28277', 'Bremen', 'Bremen', 'HB', 'Bremen, Stadt'),
('DE', '28279', 'Bremen', 'Bremen', 'HB', 'Bremen, Stadt'),
('DE', '28307', 'Bremen', 'Bremen', 'HB', 'Bremen, Stadt'),
('DE', '28309', 'Bremen', 'Bremen', 'HB', 'Bremen, Stadt'),
('DE', '28325', 'Bremen', 'Bremen', 'HB', 'Bremen, Stadt'),
('DE', '28327', 'Bremen', 'Bremen', 'HB', 'Bremen, Stadt'),
('DE', '28329', 'Bremen', 'Bremen', 'HB', 'Bremen, Stadt'),
('DE', '28335', 'Bremen', 'Bremen', 'HB', 'Kreisfreie Stadt Bremen'),
('DE', '28355', 'Bremen', 'Bremen', 'HB', 'Bremen, Stadt'),
('DE', '28357', 'Bremen', 'Bremen', 'HB', 'Bremen, Stadt'),
('DE', '28359', 'Bremen', 'Bremen', 'HB', 'Bremen, Stadt'),
('DE', '28717', 'Bremen', 'Bremen', 'HB', 'Bremen, Stadt'),
('DE', '28719', 'Bremen', 'Bremen', 'HB', 'Bremen, Stadt'),
('DE', '28755', 'Bremen', 'Bremen', 'HB', 'Bremen, Stadt'),
('DE', '28757', 'Bremen', 'Bremen', 'HB', 'Bremen, Stadt'),
('DE', '28759', 'Bremen', 'Bremen', 'HB', 'Bremen, Stadt'),
('DE', '28777', 'Bremen', 'Bremen', 'HB', 'Bremen, Stadt'),
('DE', '28779', 'Bremen', 'Bremen', 'HB', 'Bremen, Stadt'),
('DE', '20038', 'Hamburg', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '20088', 'Hamburg', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '20095', 'Hamburg', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '20095', 'Hamburg Sankt Georg', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '20095', 'Hamburg Altstadt', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '20095', 'Hamburg Klostertor', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '20097', 'Hamburg', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '20097', 'Hamburg Sankt Georg', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '20097', 'Hamburg Klostertor', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '20097', 'Hamburg Hammerbrook', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '20099', 'Hamburg', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '20099', 'Hamburg Sankt Georg', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '20099', 'Hamburg Hamburg-Altstadt', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '20144', 'Hamburg Hoheluft-Ost', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '20144', 'Hamburg Eimsbüttel', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '20144', 'Hamburg Harvestehude', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '20144', 'Hamburg', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '20144', 'Hamburg Rotherbaum', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '20146', 'Hamburg', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '20146', 'Hamburg Rotherbaum', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '20146', 'Hamburg Harvestehude', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '20148', 'Hamburg Harvestehude', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '20148', 'Hamburg', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '20148', 'Hamburg Rotherbaum', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '20149', 'Hamburg', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '20149', 'Hamburg Harvestehude', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '20149', 'Hamburg Rotherbaum', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '20249', 'Hamburg', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '20249', 'Hamburg Eppendorf', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '20249', 'Hamburg Hoheluft-Ost', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '20249', 'Hamburg Harvestehude', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '20249', 'Hamburg Winterhude', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '20251', 'Hamburg Alsterdorf', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '20251', 'Hamburg Hoheluft-Ost', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '20251', 'Hamburg Eppendorf', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '20251', 'Hamburg', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '20253', 'Hamburg Lokstedt', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '20253', 'Hamburg Hoheluft-West', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '20253', 'Hamburg Harvestehude', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '20253', 'Hamburg Hoheluft-Ost', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '20253', 'Hamburg Eimsbüttel', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '20253', 'Hamburg', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '20255', 'Hamburg', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '20255', 'Hamburg Hoheluft-West', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '20255', 'Hamburg Stellingen', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '20255', 'Hamburg Eimsbüttel', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '20255', 'Hamburg Lokstedt', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '20257', 'Hamburg Altona-Nord', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '20257', 'Hamburg Eimsbüttel', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '20257', 'Hamburg', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '20259', 'Hamburg', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '20259', 'Hamburg Eimsbüttel', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '20350', 'Hamburg', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '20354', 'Hamburg Neustadt', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '20354', 'Hamburg', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '20354', 'Hamburg Sankt Pauli', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '20354', 'Hamburg Rotherbaum', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '20355', 'Hamburg', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '20355', 'Hamburg Neustadt', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '20355', 'Hamburg Sankt Pauli', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '20357', 'Hamburg', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '20357', 'Hamburg Sankt Pauli', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '20357', 'Hamburg Rotherbaum', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '20357', 'Hamburg Altona-Nord', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '20357', 'Hamburg Altona-Altstadt', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '20357', 'Hamburg Eimsbüttel', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '20359', 'Hamburg Neustadt', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '20359', 'Hamburg Altona-Altstadt', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '20359', 'Hamburg', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '20359', 'Hamburg Sankt Pauli', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '20457', 'Hamburg', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '20457', 'Hamburg Steinwerder', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '20457', 'Hamburg Neustadt', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '20457', 'Hamburg Klostertor', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '20457', 'Hamburg Kleiner Grasbrook', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '20457', 'Hamburg Hamburg-Altstadt', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '20459', 'Hamburg', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '20459', 'Hamburg Sankt Pauli', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '20459', 'Hamburg Neustadt', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '20459', 'Hamburg Hamburg-Altstadt', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '20535', 'Hamburg', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '20535', 'Hamburg Borgfelde', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '20535', 'Hamburg Hamm-Nord', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '20537', 'Hamburg', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '20537', 'Hamburg Hamm-Süd', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '20537', 'Hamburg Hammerbrook', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '20537', 'Hamburg Hamm-Mitte', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '20537', 'Hamburg Borgfelde', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '20539', 'Hamburg', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '20539', 'Hamburg Rothenburgsort', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '20539', 'Hamburg Kleiner Grasbrook', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '20539', 'Hamburg Wilhelmsburg', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '20539', 'Hamburg Veddel', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '21029', 'Hamburg', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '21029', 'Hamburg Bergedorf', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '21029', 'Hamburg Altengamme', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '21029', 'Hamburg Curslack', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '21031', 'Hamburg', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '21031', 'Hamburg Lohbrügge', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '21031', 'Hamburg Bergedorf', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '21033', 'Hamburg Lohbrügge', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '21033', 'Hamburg Billwerder', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '21033', 'Hamburg', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '21033', 'Hamburg Bergedorf', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '21035', 'Hamburg', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '21035', 'Hamburg Billwerder', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '21035', 'Hamburg Bergedorf', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '21035', 'Hamburg Allermöhe', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '21037', 'Hamburg', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '21037', 'Hamburg Ochsenwerder', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '21037', 'Hamburg Spadenland', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '21037', 'Hamburg Tatenberg', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '21037', 'Hamburg Reitbrook', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '21037', 'Hamburg Kirchwerder', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '21037', 'Hamburg Neuengamme', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '21037', 'Hamburg Allermöhe', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '21037', 'Hamburg Curslack', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '21073', 'Hamburg', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '21073', 'Hamburg Heimfeld', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '21073', 'Hamburg Harburg', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '21073', 'Hamburg Eißendorf', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '21073', 'Hamburg Wilstorf', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '21075', 'Hamburg Eißendorf', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '21075', 'Hamburg', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '21075', 'Hamburg Harburg', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '21075', 'Hamburg Heimfeld', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '21075', 'Hamburg Hausbruch', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '21077', 'Hamburg Rönneburg', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '21077', 'Hamburg Sinstorf', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '21077', 'Hamburg Langenbek', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '21077', 'Hamburg Marmstorf', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '21077', 'Hamburg', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '21077', 'Hamburg Wilstorf', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '21077', 'Hamburg Eißendorf', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '21079', 'Hamburg', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '21079', 'Hamburg Gut Moor', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '21079', 'Hamburg Heimfeld', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '21079', 'Hamburg Sinstorf', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '21079', 'Hamburg Hausbruch', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '21079', 'Hamburg Harburg', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '21079', 'Hamburg Neuland', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '21079', 'Hamburg Rönneburg', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '21079', 'Hamburg Langenbek', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '21079', 'Hamburg Moorburg', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '21079', 'Hamburg Wilstorf', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '21107', 'Hamburg Steinwerder', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '21107', 'Hamburg', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '21107', 'Hamburg Wilhelmsburg', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '21109', 'Hamburg', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '21109', 'Hamburg Wilhelmsburg', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '21109', 'Hamburg Veddel', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '21129', 'Hamburg Moorburg', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '21129', 'Hamburg Neuenfelde', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '21129', 'Hamburg', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '21129', 'Hamburg Waltershof', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '21129', 'Hamburg Francop', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '21129', 'Hamburg Finkenwerder', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '21129', 'Hamburg Cranz', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '21129', 'Hamburg Altenwerder', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '21147', 'Hamburg Neugraben-Fischbek', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '21147', 'Hamburg', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '21147', 'Hamburg Hausbruch', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '21149', 'Hamburg Neugraben-Fischbek', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '21149', 'Hamburg', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '21149', 'Hamburg Hausbruch', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '22041', 'Hamburg Marienthal', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '22041', 'Hamburg Tonndorf', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '22041', 'Hamburg', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '22041', 'Hamburg Wandsbek', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '22043', 'Hamburg Jenfeld', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '22043', 'Hamburg Marienthal', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '22043', 'Hamburg', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '22043', 'Hamburg Tonndorf', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '22045', 'Hamburg', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '22045', 'Hamburg Jenfeld', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '22045', 'Hamburg Tonndorf', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '22047', 'Hamburg Bramfeld', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '22047', 'Hamburg', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '22047', 'Hamburg Tonndorf', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '22047', 'Hamburg Wandsbek', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '22049', 'Hamburg', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '22049', 'Hamburg Dulsberg', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '22049', 'Hamburg Wandsbek', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '22081', 'Hamburg Barmbek-Süd', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '22081', 'Hamburg Uhlenhorst', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '22081', 'Hamburg', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '22083', 'Hamburg Barmbek-Süd', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '22083', 'Hamburg', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '22085', 'Hamburg Uhlenhorst', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '22085', 'Hamburg', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '22085', 'Hamburg Barmbek-Süd', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '22087', 'Hamburg Hamm-Nord', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '22087', 'Hamburg Hohenfelde', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '22087', 'Hamburg Eilbek', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '22087', 'Hamburg', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '22087', 'Hamburg Uhlenhorst', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '22089', 'Hamburg', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '22089', 'Hamburg Marienthal', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '22089', 'Hamburg Eilbek', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '22089', 'Hamburg Hohenfelde', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '22089', 'Hamburg Hamm-Nord', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '22089', 'Hamburg Wandsbek', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '22111', 'Hamburg Billbrook', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '22111', 'Hamburg Billstedt', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '22111', 'Hamburg Horn', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '22111', 'Hamburg', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '22115', 'Hamburg', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '22115', 'Hamburg Lohbrügge', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '22115', 'Hamburg Billstedt', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '22117', 'Hamburg Billstedt', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '22117', 'Hamburg', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '22119', 'Hamburg', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '22119', 'Hamburg Billstedt', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '22119', 'Hamburg Horn', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '22143', 'Hamburg Rahlstedt', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '22143', 'Hamburg', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '22147', 'Hamburg', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '22147', 'Hamburg Rahlstedt', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '22149', 'Hamburg Tonndorf', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '22149', 'Hamburg', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '22149', 'Hamburg Rahlstedt', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '22159', 'Hamburg Tonndorf', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '22159', 'Hamburg Farmsen-Berne', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '22159', 'Hamburg Sasel', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '22159', 'Hamburg', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '22159', 'Hamburg Bramfeld', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '22175', 'Hamburg Bramfeld', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '22175', 'Hamburg', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '22177', 'Hamburg', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '22177', 'Hamburg Steilshoop', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '22177', 'Hamburg Bramfeld', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '22179', 'Hamburg', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '22179', 'Hamburg Bramfeld', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '22297', 'Hamburg Barmbek-Nord', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '22297', 'Hamburg Alsterdorf', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '22297', 'Hamburg', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '22297', 'Hamburg Groß Borstel', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '22297', 'Hamburg Winterhude', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '22299', 'Hamburg Winterhude', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '22299', 'Hamburg', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '22301', 'Hamburg Winterhude', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '22301', 'Hamburg', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '22303', 'Hamburg Winterhude', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '22303', 'Hamburg Barmbek-Nord', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '22303', 'Hamburg', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '22305', 'Hamburg Barmbek-Nord', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '22305', 'Hamburg Winterhude', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '22305', 'Hamburg', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '22305', 'Hamburg Barmbek-Süd', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '22307', 'Hamburg', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '22307', 'Hamburg Barmbek-Nord', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '22309', 'Hamburg Steilshoop', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '22309', 'Hamburg', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '22309', 'Hamburg Ohlsdorf', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '22309', 'Hamburg Bramfeld', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '22309', 'Hamburg Barmbek-Nord', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '22335', 'Hamburg Alsterdorf', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '22335', 'Hamburg', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '22335', 'Hamburg Ohlsdorf', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '22335', 'Hamburg Groß Borstel', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '22335', 'Hamburg Fuhlsbüttel', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '22337', 'Hamburg', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '22337', 'Hamburg Ohlsdorf', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '22337', 'Hamburg Alsterdorf', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '22339', 'Hamburg', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '22339', 'Hamburg Hummelsbüttel', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '22339', 'Hamburg Fuhlsbüttel', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '22359', 'Hamburg Bergstedt', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '22359', 'Hamburg Volksdorf', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '22359', 'Hamburg Rahlstedt', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '22359', 'Hamburg', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '22391', 'Hamburg Bramfeld', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '22391', 'Hamburg Sasel', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '22391', 'Hamburg Ohlsdorf', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '22391', 'Hamburg Poppenbüttel', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '22391', 'Hamburg', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '22391', 'Hamburg Hummelsbüttel', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '22391', 'Hamburg Wellingsbüttel', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '22393', 'Hamburg Bramfeld', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '22393', 'Hamburg', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '22393', 'Hamburg Wellingsbüttel', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '22393', 'Hamburg Sasel', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '22393', 'Hamburg Poppenbüttel', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '22395', 'Hamburg', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '22395', 'Hamburg Wohldorf-Ohlstedt', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '22395', 'Hamburg Sasel', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '22395', 'Hamburg Poppenbüttel', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '22395', 'Hamburg Bergstedt', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '22397', 'Hamburg Wohldorf-Ohlstedt', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '22397', 'Hamburg Lemsahl-Mellingstedt', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '22397', 'Hamburg Duvenstedt', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '22397', 'Hamburg', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '22399', 'Hamburg', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '22399', 'Hamburg Lemsahl-Mellingstedt', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '22399', 'Hamburg Poppenbüttel', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '22399', 'Hamburg Hummelsbüttel', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '22415', 'Hamburg Hummelsbüttel', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '22415', 'Hamburg', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '22415', 'Hamburg Langenhorn', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '22415', 'Hamburg Fuhlsbüttel', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '22417', 'Hamburg', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '22417', 'Hamburg Hummelsbüttel', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '22417', 'Hamburg Langenhorn', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '22419', 'Hamburg', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '22419', 'Hamburg Langenhorn', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '22453', 'Hamburg', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '22453', 'Hamburg Niendorf', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '22453', 'Hamburg Fuhlsbüttel', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '22453', 'Hamburg Groß Borstel', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '22455', 'Hamburg', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '22455', 'Hamburg Schnelsen', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '22455', 'Hamburg Niendorf', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '22457', 'Hamburg', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '22457', 'Hamburg Schnelsen', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '22457', 'Hamburg Niendorf', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '22457', 'Hamburg Eidelstedt', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '22459', 'Hamburg Schnelsen', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '22459', 'Hamburg Niendorf', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '22459', 'Hamburg', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '22523', 'Hamburg', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '22523', 'Hamburg Eidelstedt', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '22525', 'Hamburg Lurup', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '22525', 'Hamburg Stellingen', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '22525', 'Hamburg Bahrenfeld', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '22525', 'Hamburg Eidelstedt', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '22525', 'Hamburg Eimsbüttel', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '22525', 'Hamburg', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '22527', 'Hamburg Stellingen', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '22527', 'Hamburg Lokstedt', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '22527', 'Hamburg', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '22527', 'Hamburg Eidelstedt', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '22527', 'Hamburg Eimsbüttel', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '22529', 'Hamburg Eppendorf', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '22529', 'Hamburg Hoheluft-West', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '22529', 'Hamburg Groß Borstel', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '22529', 'Hamburg Stellingen', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '22529', 'Hamburg Niendorf', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '22529', 'Hamburg', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '22529', 'Hamburg Lokstedt', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '22547', 'Hamburg Osdorf', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '22547', 'Hamburg Lurup', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '22547', 'Hamburg Bahrenfeld', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '22547', 'Hamburg', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '22547', 'Hamburg Eidelstedt', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '22549', 'Hamburg', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '22549', 'Hamburg Osdorf', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '22549', 'Hamburg Lurup', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '22549', 'Hamburg Bahrenfeld', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '22559', 'Hamburg Rissen', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '22559', 'Hamburg', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '22587', 'Hamburg Osdorf', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '22587', 'Hamburg Rissen', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '22587', 'Hamburg Nienstedten', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '22587', 'Hamburg Sülldorf', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '22587', 'Hamburg Blankenese', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '22587', 'Hamburg', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '22589', 'Hamburg Iserbrook', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '22589', 'Hamburg Sülldorf', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '22589', 'Hamburg Osdorf', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '22589', 'Hamburg Blankenese', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '22589', 'Hamburg', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '22605', 'Hamburg Groß Flottbek', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '22605', 'Hamburg Bahrenfeld', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '22605', 'Hamburg Othmarschen', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '22605', 'Hamburg', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '22607', 'Hamburg Nienstedten', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '22607', 'Hamburg Othmarschen', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '22607', 'Hamburg Bahrenfeld', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '22607', 'Hamburg Groß Flottbek', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '22607', 'Hamburg', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '22609', 'Hamburg', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '22609', 'Hamburg Osdorf', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '22609', 'Hamburg Othmarschen', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '22609', 'Hamburg Groß Flottbek', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '22609', 'Hamburg Nienstedten', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '22761', 'Hamburg Bahrenfeld', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '22761', 'Hamburg', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '22763', 'Hamburg', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '22763', 'Hamburg Othmarschen', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '22763', 'Hamburg Ottensen', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '22765', 'Hamburg', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '22765', 'Hamburg Altona-Nord', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '22765', 'Hamburg Altona-Altstadt', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '22765', 'Hamburg Ottensen', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '22767', 'Hamburg Ottensen', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '22767', 'Hamburg Sankt Pauli', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '22767', 'Hamburg Altona-Altstadt', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '22767', 'Hamburg', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '22769', 'Hamburg Stellingen', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '22769', 'Hamburg Sankt Pauli', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '22769', 'Hamburg Bahrenfeld', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '22769', 'Hamburg', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '22769', 'Hamburg Altona-Nord', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '22769', 'Hamburg Altona-Altstadt', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '22769', 'Hamburg Eimsbüttel', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '27499', 'Hamburg Neuwerk', 'Hamburg', 'HH', 'Hamburg, Freie und Hansestadt'),
('DE', '21217', 'Seevetal', 'Niedersachsen', 'NI', 'Harburg'),
('DE', '21218', 'Seevetal', 'Niedersachsen', 'NI', 'Harburg'),
('DE', '21220', 'Seevetal', 'Niedersachsen', 'NI', 'Harburg'),
('DE', '21224', 'Rosengarten', 'Niedersachsen', 'NI', 'Harburg'),
('DE', '21227', 'Bendestorf', 'Niedersachsen', 'NI', 'Harburg'),
('DE', '21228', 'Harmstorf', 'Niedersachsen', 'NI', 'Harburg'),
('DE', '21244', 'Buchholz in der Nordheide', 'Niedersachsen', 'NI', 'Harburg'),
('DE', '21255', 'Kakenstorf', 'Niedersachsen', 'NI', 'Harburg'),
('DE', '21255', 'Königsmoor', 'Niedersachsen', 'NI', 'Harburg'),
('DE', '21255', 'Tostedt', 'Niedersachsen', 'NI', 'Harburg'),
('DE', '21255', 'Wistedt', 'Niedersachsen', 'NI', 'Harburg'),
('DE', '21255', 'Dohren', 'Niedersachsen', 'NI', 'Harburg'),
('DE', '21256', 'Handeloh', 'Niedersachsen', 'NI', 'Harburg'),
('DE', '21258', 'Heidenau', 'Niedersachsen', 'NI', 'Harburg'),
('DE', '21259', 'Otter', 'Niedersachsen', 'NI', 'Harburg'),
('DE', '21261', 'Welle', 'Niedersachsen', 'NI', 'Harburg');
INSERT INTO `plz_tabelle` (`lkz`, `plz`, `ort_stadtteil`, `bundesland`, `blkz`, `kreis`) VALUES
('DE', '21266', 'Jesteburg', 'Niedersachsen', 'NI', 'Harburg'),
('DE', '21271', 'Hanstedt', 'Niedersachsen', 'NI', 'Harburg'),
('DE', '21271', 'Asendorf', 'Niedersachsen', 'NI', 'Harburg'),
('DE', '21272', 'Egestorf', 'Niedersachsen', 'NI', 'Harburg'),
('DE', '21274', 'Undeloh', 'Niedersachsen', 'NI', 'Harburg'),
('DE', '21279', 'Hollenstedt', 'Niedersachsen', 'NI', 'Harburg'),
('DE', '21279', 'Appel', 'Niedersachsen', 'NI', 'Harburg'),
('DE', '21279', 'Wenzendorf', 'Niedersachsen', 'NI', 'Harburg'),
('DE', '21279', 'Drestedt', 'Niedersachsen', 'NI', 'Harburg'),
('DE', '21335', 'Lüneburg', 'Niedersachsen', 'NI', 'Lüneburg'),
('DE', '21337', 'Lüneburg', 'Niedersachsen', 'NI', 'Lüneburg'),
('DE', '21339', 'Lüneburg', 'Niedersachsen', 'NI', 'Lüneburg'),
('DE', '21354', 'Bleckede', 'Niedersachsen', 'NI', 'Lüneburg'),
('DE', '21357', 'Bardowick', 'Niedersachsen', 'NI', 'Lüneburg'),
('DE', '21357', 'Wittorf', 'Niedersachsen', 'NI', 'Lüneburg'),
('DE', '21357', 'Barum', 'Niedersachsen', 'NI', 'Lüneburg'),
('DE', '21358', 'Mechtersen', 'Niedersachsen', 'NI', 'Lüneburg'),
('DE', '21360', 'Vögelsen', 'Niedersachsen', 'NI', 'Lüneburg'),
('DE', '21365', 'Adendorf', 'Niedersachsen', 'NI', 'Lüneburg'),
('DE', '21368', 'Boitze', 'Niedersachsen', 'NI', 'Lüneburg'),
('DE', '21368', 'Dahlem', 'Niedersachsen', 'NI', 'Lüneburg'),
('DE', '21368', 'Dahlenburg', 'Niedersachsen', 'NI', 'Lüneburg'),
('DE', '21369', 'Nahrendorf', 'Niedersachsen', 'NI', 'Lüneburg'),
('DE', '21371', 'Tosterglope', 'Niedersachsen', 'NI', 'Lüneburg'),
('DE', '21376', 'Garlstorf', 'Niedersachsen', 'NI', 'Harburg'),
('DE', '21376', 'Salzhausen', 'Niedersachsen', 'NI', 'Harburg'),
('DE', '21376', 'Gödenstorf', 'Niedersachsen', 'NI', 'Harburg'),
('DE', '21376', 'Eyendorf', 'Niedersachsen', 'NI', 'Harburg'),
('DE', '21379', 'Lüdersburg', 'Niedersachsen', 'NI', 'Lüneburg'),
('DE', '21379', 'Echem', 'Niedersachsen', 'NI', 'Lüneburg'),
('DE', '21379', 'Rullstorf', 'Niedersachsen', 'NI', 'Lüneburg'),
('DE', '21379', 'Scharnebeck', 'Niedersachsen', 'NI', 'Lüneburg'),
('DE', '21380', 'Artlenburg', 'Niedersachsen', 'NI', 'Lüneburg'),
('DE', '21382', 'Brietlingen', 'Niedersachsen', 'NI', 'Lüneburg'),
('DE', '21385', 'Rehlingen', 'Niedersachsen', 'NI', 'Lüneburg'),
('DE', '21385', 'Oldendorf (Luhe)', 'Niedersachsen', 'NI', 'Lüneburg'),
('DE', '21385', 'Amelinghausen', 'Niedersachsen', 'NI', 'Lüneburg'),
('DE', '21386', 'Betzendorf', 'Niedersachsen', 'NI', 'Lüneburg'),
('DE', '21388', 'Soderstorf', 'Niedersachsen', 'NI', 'Lüneburg'),
('DE', '21391', 'Dachtmissen', 'Niedersachsen', 'NI', 'Lüneburg'),
('DE', '21391', 'Reppenstedt', 'Niedersachsen', 'NI', 'Lüneburg'),
('DE', '21394', 'Kirchgellersen', 'Niedersachsen', 'NI', 'Lüneburg'),
('DE', '21394', 'Südergellersen', 'Niedersachsen', 'NI', 'Lüneburg'),
('DE', '21394', 'Westergellersen', 'Niedersachsen', 'NI', 'Lüneburg'),
('DE', '21394', 'Heiligenthal', 'Niedersachsen', 'NI', 'Lüneburg'),
('DE', '21395', 'Tespe', 'Niedersachsen', 'NI', 'Harburg'),
('DE', '21397', 'Vastorf', 'Niedersachsen', 'NI', 'Lüneburg'),
('DE', '21397', 'Barendorf', 'Niedersachsen', 'NI', 'Lüneburg'),
('DE', '21398', 'Neetze', 'Niedersachsen', 'NI', 'Lüneburg'),
('DE', '21400', 'Reinstorf', 'Niedersachsen', 'NI', 'Lüneburg'),
('DE', '21401', 'Thomasburg', 'Niedersachsen', 'NI', 'Lüneburg'),
('DE', '21403', 'Wendisch Evern', 'Niedersachsen', 'NI', 'Lüneburg'),
('DE', '21406', 'Barnstedt', 'Niedersachsen', 'NI', 'Lüneburg'),
('DE', '21406', 'Melbeck', 'Niedersachsen', 'NI', 'Lüneburg'),
('DE', '21407', 'Deutsch Evern', 'Niedersachsen', 'NI', 'Lüneburg'),
('DE', '21409', 'Embsen', 'Niedersachsen', 'NI', 'Lüneburg'),
('DE', '21423', 'Drage', 'Niedersachsen', 'NI', 'Harburg'),
('DE', '21423', 'Winsen (Luhe)', 'Niedersachsen', 'NI', 'Harburg'),
('DE', '21435', 'Stelle', 'Niedersachsen', 'NI', 'Harburg'),
('DE', '21436', 'Marschacht', 'Niedersachsen', 'NI', 'Harburg'),
('DE', '21438', 'Brackel', 'Niedersachsen', 'NI', 'Harburg'),
('DE', '21439', 'Marxen', 'Niedersachsen', 'NI', 'Harburg'),
('DE', '21441', 'Garstedt', 'Niedersachsen', 'NI', 'Harburg'),
('DE', '21442', 'Toppenstedt', 'Niedersachsen', 'NI', 'Harburg'),
('DE', '21444', 'Vierhöfen', 'Niedersachsen', 'NI', 'Harburg'),
('DE', '21445', 'Wulfsen', 'Niedersachsen', 'NI', 'Harburg'),
('DE', '21447', 'Handorf', 'Niedersachsen', 'NI', 'Lüneburg'),
('DE', '21449', 'Radbruch', 'Niedersachsen', 'NI', 'Lüneburg'),
('DE', '21522', 'Hittbergen', 'Niedersachsen', 'NI', 'Lüneburg'),
('DE', '21522', 'Hohnstorf (Elbe)', 'Niedersachsen', 'NI', 'Lüneburg'),
('DE', '21614', 'Buxtehude', 'Niedersachsen', 'NI', 'Stade'),
('DE', '21629', 'Neu Wulmstorf', 'Niedersachsen', 'NI', 'Harburg'),
('DE', '21635', 'Jork', 'Niedersachsen', 'NI', 'Stade'),
('DE', '21640', 'Neuenkirchen', 'Niedersachsen', 'NI', 'Stade'),
('DE', '21640', 'Horneburg', 'Niedersachsen', 'NI', 'Stade'),
('DE', '21640', 'Nottensdorf', 'Niedersachsen', 'NI', 'Stade'),
('DE', '21640', 'Bliedersdorf', 'Niedersachsen', 'NI', 'Stade'),
('DE', '21641', 'Apensen', 'Niedersachsen', 'NI', 'Stade'),
('DE', '21643', 'Beckdorf', 'Niedersachsen', 'NI', 'Stade'),
('DE', '21644', 'Sauensiek', 'Niedersachsen', 'NI', 'Stade'),
('DE', '21646', 'Halvesbostel', 'Niedersachsen', 'NI', 'Harburg'),
('DE', '21647', 'Moisburg', 'Niedersachsen', 'NI', 'Harburg'),
('DE', '21649', 'Regesbostel', 'Niedersachsen', 'NI', 'Harburg'),
('DE', '21680', 'Stade', 'Niedersachsen', 'NI', 'Stade'),
('DE', '21682', 'Stade', 'Niedersachsen', 'NI', 'Stade'),
('DE', '21683', 'Stade', 'Niedersachsen', 'NI', 'Stade'),
('DE', '21684', 'Stade', 'Niedersachsen', 'NI', 'Stade'),
('DE', '21684', 'Agathenburg', 'Niedersachsen', 'NI', 'Stade'),
('DE', '21698', 'Bargstedt', 'Niedersachsen', 'NI', 'Stade'),
('DE', '21698', 'Brest', 'Niedersachsen', 'NI', 'Stade'),
('DE', '21698', 'Harsefeld', 'Niedersachsen', 'NI', 'Stade'),
('DE', '21702', 'Ahlerstedt', 'Niedersachsen', 'NI', 'Stade'),
('DE', '21706', 'Drochtersen', 'Niedersachsen', 'NI', 'Stade'),
('DE', '21709', 'Düdenbüttel', 'Niedersachsen', 'NI', 'Stade'),
('DE', '21709', 'Burweg', 'Niedersachsen', 'NI', 'Stade'),
('DE', '21709', 'Himmelpforten', 'Niedersachsen', 'NI', 'Stade'),
('DE', '21710', 'Engelschoff', 'Niedersachsen', 'NI', 'Stade'),
('DE', '21712', 'Großenwörden', 'Niedersachsen', 'NI', 'Stade'),
('DE', '21714', 'Hammah', 'Niedersachsen', 'NI', 'Stade'),
('DE', '21717', 'Fredenbeck', 'Niedersachsen', 'NI', 'Stade'),
('DE', '21717', 'Deinste', 'Niedersachsen', 'NI', 'Stade'),
('DE', '21720', 'Mittelnkirchen', 'Niedersachsen', 'NI', 'Stade'),
('DE', '21720', 'Guderhandviertel', 'Niedersachsen', 'NI', 'Stade'),
('DE', '21720', 'Steinkirchen', 'Niedersachsen', 'NI', 'Stade'),
('DE', '21720', 'Grünendeich', 'Niedersachsen', 'NI', 'Stade'),
('DE', '21723', 'Hollern-Twielenfleth', 'Niedersachsen', 'NI', 'Stade'),
('DE', '21726', 'Oldendorf', 'Niedersachsen', 'NI', 'Stade'),
('DE', '21726', 'Heinbockel', 'Niedersachsen', 'NI', 'Stade'),
('DE', '21726', 'Kranenburg', 'Niedersachsen', 'NI', 'Stade'),
('DE', '21727', 'Estorf', 'Niedersachsen', 'NI', 'Stade'),
('DE', '21729', 'Freiburg (Elbe)', 'Niedersachsen', 'NI', 'Stade'),
('DE', '21730', 'Balje', 'Niedersachsen', 'NI', 'Stade'),
('DE', '21732', 'Krummendeich', 'Niedersachsen', 'NI', 'Stade'),
('DE', '21734', 'Oederquart', 'Niedersachsen', 'NI', 'Stade'),
('DE', '21737', 'Wischhafen', 'Niedersachsen', 'NI', 'Stade'),
('DE', '21739', 'Dollern', 'Niedersachsen', 'NI', 'Stade'),
('DE', '21745', 'Hemmoor', 'Niedersachsen', 'NI', 'Cuxhaven'),
('DE', '21755', 'Hechthausen', 'Niedersachsen', 'NI', 'Cuxhaven'),
('DE', '21756', 'Osten', 'Niedersachsen', 'NI', 'Cuxhaven'),
('DE', '21762', 'Otterndorf', 'Niedersachsen', 'NI', 'Cuxhaven'),
('DE', '21762', 'Osterbruch', 'Niedersachsen', 'NI', 'Cuxhaven'),
('DE', '21763', 'Neuenkirchen', 'Niedersachsen', 'NI', 'Cuxhaven'),
('DE', '21765', 'Nordleda', 'Niedersachsen', 'NI', 'Cuxhaven'),
('DE', '21769', 'Hollnseth', 'Niedersachsen', 'NI', 'Cuxhaven'),
('DE', '21769', 'Lamstedt', 'Niedersachsen', 'NI', 'Cuxhaven'),
('DE', '21769', 'Armstorf', 'Niedersachsen', 'NI', 'Cuxhaven'),
('DE', '21770', 'Mittelstenahe', 'Niedersachsen', 'NI', 'Cuxhaven'),
('DE', '21772', 'Stinstedt', 'Niedersachsen', 'NI', 'Cuxhaven'),
('DE', '21775', 'Odisheim', 'Niedersachsen', 'NI', 'Cuxhaven'),
('DE', '21775', 'Steinau', 'Niedersachsen', 'NI', 'Cuxhaven'),
('DE', '21775', 'Ihlienworth', 'Niedersachsen', 'NI', 'Cuxhaven'),
('DE', '21776', 'Wanna', 'Niedersachsen', 'NI', 'Cuxhaven'),
('DE', '21781', 'Cadenberge', 'Niedersachsen', 'NI', 'Cuxhaven'),
('DE', '21782', 'Bülkau', 'Niedersachsen', 'NI', 'Cuxhaven'),
('DE', '21784', 'Geversdorf', 'Niedersachsen', 'NI', 'Cuxhaven'),
('DE', '21785', 'Belum', 'Niedersachsen', 'NI', 'Cuxhaven'),
('DE', '21785', 'Neuhaus an der Oste', 'Niedersachsen', 'NI', 'Cuxhaven'),
('DE', '21787', 'Oberndorf', 'Niedersachsen', 'NI', 'Cuxhaven'),
('DE', '21789', 'Wingst', 'Niedersachsen', 'NI', 'Cuxhaven'),
('DE', '26121', 'Oldenburg (Oldenburg)', 'Niedersachsen', 'NI', 'Oldenburg (Oldenburg), Stadt'),
('DE', '26122', 'Oldenburg (Oldenburg)', 'Niedersachsen', 'NI', 'Oldenburg (Oldenburg), Stadt'),
('DE', '26123', 'Oldenburg (Oldenburg)', 'Niedersachsen', 'NI', 'Oldenburg (Oldenburg), Stadt'),
('DE', '26125', 'Oldenburg (Oldenburg)', 'Niedersachsen', 'NI', 'Oldenburg (Oldenburg), Stadt'),
('DE', '26127', 'Oldenburg (Oldenburg)', 'Niedersachsen', 'NI', 'Oldenburg (Oldenburg), Stadt'),
('DE', '26129', 'Oldenburg (Oldenburg)', 'Niedersachsen', 'NI', 'Oldenburg (Oldenburg), Stadt'),
('DE', '26131', 'Oldenburg (Oldenburg)', 'Niedersachsen', 'NI', 'Oldenburg (Oldenburg), Stadt'),
('DE', '26133', 'Oldenburg (Oldenburg)', 'Niedersachsen', 'NI', 'Oldenburg (Oldenburg), Stadt'),
('DE', '26135', 'Oldenburg (Oldenburg)', 'Niedersachsen', 'NI', 'Oldenburg (Oldenburg), Stadt'),
('DE', '26160', 'Bad Zwischenahn', 'Niedersachsen', 'NI', 'Ammerland'),
('DE', '26169', 'Friesoythe', 'Niedersachsen', 'NI', 'Cloppenburg'),
('DE', '26180', 'Rastede', 'Niedersachsen', 'NI', 'Ammerland'),
('DE', '26188', 'Edewecht', 'Niedersachsen', 'NI', 'Ammerland'),
('DE', '26197', 'Großenkneten', 'Niedersachsen', 'NI', 'Oldenburg'),
('DE', '26203', 'Wardenburg', 'Niedersachsen', 'NI', 'Oldenburg'),
('DE', '26209', 'Hatten', 'Niedersachsen', 'NI', 'Oldenburg'),
('DE', '26215', 'Wiefelstede', 'Niedersachsen', 'NI', 'Ammerland'),
('DE', '26219', 'Bösel', 'Niedersachsen', 'NI', 'Cloppenburg'),
('DE', '26316', 'Varel Hohenberge', 'Niedersachsen', 'NI', 'Friesland'),
('DE', '26316', 'Varel Winkelsheide', 'Niedersachsen', 'NI', 'Friesland'),
('DE', '26316', 'Varel Rallenbüschen', 'Niedersachsen', 'NI', 'Friesland'),
('DE', '26316', 'Varel Dangast', 'Niedersachsen', 'NI', 'Friesland'),
('DE', '26316', 'Varel Neudorf', 'Niedersachsen', 'NI', 'Friesland'),
('DE', '26316', 'Varel Varel', 'Niedersachsen', 'NI', 'Friesland'),
('DE', '26316', 'Varel Neuenwege', 'Niedersachsen', 'NI', 'Friesland'),
('DE', '26316', 'Varel Jeringhave', 'Niedersachsen', 'NI', 'Friesland'),
('DE', '26316', 'Varel Altjührden', 'Niedersachsen', 'NI', 'Friesland'),
('DE', '26316', 'Varel Borgstede', 'Niedersachsen', 'NI', 'Friesland'),
('DE', '26316', 'Varel', 'Niedersachsen', 'NI', 'Friesland'),
('DE', '26316', 'Varel Obenstrohe', 'Niedersachsen', 'NI', 'Friesland'),
('DE', '26316', 'Varel Dangastermoor', 'Niedersachsen', 'NI', 'Friesland'),
('DE', '26316', 'Varel Grünenkamp', 'Niedersachsen', 'NI', 'Friesland'),
('DE', '26316', 'Varel Langendamm', 'Niedersachsen', 'NI', 'Friesland'),
('DE', '26316', 'Varel Streek', 'Niedersachsen', 'NI', 'Friesland'),
('DE', '26316', 'Varel Hohelucht', 'Niedersachsen', 'NI', 'Friesland'),
('DE', '26316', 'Varel Jethausen', 'Niedersachsen', 'NI', 'Friesland'),
('DE', '26316', 'Varel Rosenberg', 'Niedersachsen', 'NI', 'Friesland'),
('DE', '26316', 'Varel Moorhausen', 'Niedersachsen', 'NI', 'Friesland'),
('DE', '26316', 'Varel Seghorn', 'Niedersachsen', 'NI', 'Friesland'),
('DE', '26316', 'Varel Büppel', 'Niedersachsen', 'NI', 'Friesland'),
('DE', '26340', 'Zetel', 'Niedersachsen', 'NI', 'Friesland'),
('DE', '26340', 'Zetel Neuenburg', 'Niedersachsen', 'NI', 'Friesland'),
('DE', '26340', 'Zetel Zetel', 'Niedersachsen', 'NI', 'Friesland'),
('DE', '26345', 'Bockhorn Adelheidsgroden', 'Niedersachsen', 'NI', 'Friesland'),
('DE', '26345', 'Bockhorn Grabstede', 'Niedersachsen', 'NI', 'Friesland'),
('DE', '26345', 'Bockhorn Moorwinkelsdamm', 'Niedersachsen', 'NI', 'Friesland'),
('DE', '26345', 'Bockhorn Petersgroden', 'Niedersachsen', 'NI', 'Friesland'),
('DE', '26345', 'Bockhorn Ellenserdammersiel', 'Niedersachsen', 'NI', 'Friesland'),
('DE', '26345', 'Bockhorn Steinhausen', 'Niedersachsen', 'NI', 'Friesland'),
('DE', '26345', 'Bockhorn Osterforde', 'Niedersachsen', 'NI', 'Friesland'),
('DE', '26345', 'Bockhorn Bockhorn', 'Niedersachsen', 'NI', 'Friesland'),
('DE', '26345', 'Bockhorn Bockhornerfeld', 'Niedersachsen', 'NI', 'Friesland'),
('DE', '26345', 'Bockhorn Goelriehenfeld', 'Niedersachsen', 'NI', 'Friesland'),
('DE', '26345', 'Bockhorn Jührdenerfeld', 'Niedersachsen', 'NI', 'Friesland'),
('DE', '26345', 'Bockhorn Blauhand', 'Niedersachsen', 'NI', 'Friesland'),
('DE', '26345', 'Bockhorn Bredehorn', 'Niedersachsen', 'NI', 'Friesland'),
('DE', '26345', 'Bockhorn', 'Niedersachsen', 'NI', 'Friesland'),
('DE', '26345', 'Bockhorn Kranenkamp', 'Niedersachsen', 'NI', 'Friesland'),
('DE', '26349', 'Jade', 'Niedersachsen', 'NI', 'Wesermarsch'),
('DE', '26382', 'Wilhelmshaven', 'Niedersachsen', 'NI', 'Wilhelmshaven, Stadt'),
('DE', '26384', 'Wilhelmshaven', 'Niedersachsen', 'NI', 'Wilhelmshaven, Stadt'),
('DE', '26386', 'Wilhelmshaven', 'Niedersachsen', 'NI', 'Wilhelmshaven, Stadt'),
('DE', '26388', 'Wilhelmshaven', 'Niedersachsen', 'NI', 'Wilhelmshaven, Stadt'),
('DE', '26389', 'Wilhelmshaven', 'Niedersachsen', 'NI', 'Wilhelmshaven, Stadt'),
('DE', '26409', 'Wittmund Berdum', 'Niedersachsen', 'NI', 'Wittmund'),
('DE', '26409', 'Wittmund Blersum', 'Niedersachsen', 'NI', 'Wittmund'),
('DE', '26409', 'Wittmund Leerhafe', 'Niedersachsen', 'NI', 'Wittmund'),
('DE', '26409', 'Wittmund Funnix', 'Niedersachsen', 'NI', 'Wittmund'),
('DE', '26409', 'Wittmund Buttforde', 'Niedersachsen', 'NI', 'Wittmund'),
('DE', '26409', 'Wittmund Uttel', 'Niedersachsen', 'NI', 'Wittmund'),
('DE', '26409', 'Wittmund Hovel', 'Niedersachsen', 'NI', 'Wittmund'),
('DE', '26409', 'Wittmund', 'Niedersachsen', 'NI', 'Wittmund'),
('DE', '26409', 'Wittmund Burhafe', 'Niedersachsen', 'NI', 'Wittmund'),
('DE', '26409', 'Wittmund Carolinensiel', 'Niedersachsen', 'NI', 'Wittmund'),
('DE', '26409', 'Wittmund Eggelingen', 'Niedersachsen', 'NI', 'Wittmund'),
('DE', '26409', 'Wittmund Wittmund', 'Niedersachsen', 'NI', 'Wittmund'),
('DE', '26409', 'Wittmund Willen', 'Niedersachsen', 'NI', 'Wittmund'),
('DE', '26409', 'Wittmund Ardorf', 'Niedersachsen', 'NI', 'Wittmund'),
('DE', '26409', 'Wittmund Asel', 'Niedersachsen', 'NI', 'Wittmund'),
('DE', '26419', 'Schortens Schortens', 'Niedersachsen', 'NI', 'Friesland'),
('DE', '26419', 'Schortens Ostiem', 'Niedersachsen', 'NI', 'Friesland'),
('DE', '26419', 'Schortens Schoost', 'Niedersachsen', 'NI', 'Friesland'),
('DE', '26419', 'Schortens', 'Niedersachsen', 'NI', 'Friesland'),
('DE', '26419', 'Schortens Oestringfelde', 'Niedersachsen', 'NI', 'Friesland'),
('DE', '26419', 'Schortens Accum', 'Niedersachsen', 'NI', 'Friesland'),
('DE', '26419', 'Schortens Grafschaft', 'Niedersachsen', 'NI', 'Friesland'),
('DE', '26419', 'Schortens Heidmühle', 'Niedersachsen', 'NI', 'Friesland'),
('DE', '26419', 'Schortens Upjever', 'Niedersachsen', 'NI', 'Friesland'),
('DE', '26419', 'Schortens Middelsfähr', 'Niedersachsen', 'NI', 'Friesland'),
('DE', '26419', 'Schortens Addernhausen', 'Niedersachsen', 'NI', 'Friesland'),
('DE', '26419', 'Schortens Roffhausen', 'Niedersachsen', 'NI', 'Friesland'),
('DE', '26419', 'Schortens Sillenstede', 'Niedersachsen', 'NI', 'Friesland'),
('DE', '26427', 'Holtgast', 'Niedersachsen', 'NI', 'Wittmund'),
('DE', '26427', 'Dunum', 'Niedersachsen', 'NI', 'Wittmund'),
('DE', '26427', 'Stedesdorf', 'Niedersachsen', 'NI', 'Wittmund'),
('DE', '26427', 'Neuharlingersiel', 'Niedersachsen', 'NI', 'Wittmund'),
('DE', '26427', 'Esens', 'Niedersachsen', 'NI', 'Wittmund'),
('DE', '26427', 'Moorweg', 'Niedersachsen', 'NI', 'Wittmund'),
('DE', '26427', 'Werdum', 'Niedersachsen', 'NI', 'Wittmund'),
('DE', '26434', 'Wangerland Minsen', 'Niedersachsen', 'NI', 'Friesland'),
('DE', '26434', 'Wangerland Hohenkirchen', 'Niedersachsen', 'NI', 'Friesland'),
('DE', '26434', 'Wangerland Hooksiel', 'Niedersachsen', 'NI', 'Friesland'),
('DE', '26434', 'Wangerland Tettens', 'Niedersachsen', 'NI', 'Friesland'),
('DE', '26434', 'Wangerland Waddewarden', 'Niedersachsen', 'NI', 'Friesland'),
('DE', '26434', 'Wangerland', 'Niedersachsen', 'NI', 'Friesland'),
('DE', '26441', 'Jever', 'Niedersachsen', 'NI', 'Friesland'),
('DE', '26441', 'Jever Jever', 'Niedersachsen', 'NI', 'Friesland'),
('DE', '26446', 'Friedeburg Hesel', 'Niedersachsen', 'NI', 'Wittmund'),
('DE', '26446', 'Friedeburg Bentstreek', 'Niedersachsen', 'NI', 'Wittmund'),
('DE', '26446', 'Friedeburg Wiesede', 'Niedersachsen', 'NI', 'Wittmund'),
('DE', '26446', 'Friedeburg Reepsholt, Hoheesche', 'Niedersachsen', 'NI', 'Wittmund'),
('DE', '26446', 'Friedeburg Wiesedermeer', 'Niedersachsen', 'NI', 'Wittmund'),
('DE', '26446', 'Friedeburg Reepsholt, Reepsholt', 'Niedersachsen', 'NI', 'Wittmund'),
('DE', '26446', 'Friedeburg', 'Niedersachsen', 'NI', 'Wittmund'),
('DE', '26446', 'Friedeburg Horsten', 'Niedersachsen', 'NI', 'Wittmund'),
('DE', '26446', 'Friedeburg Reepsholt, Dose', 'Niedersachsen', 'NI', 'Wittmund'),
('DE', '26446', 'Friedeburg Marx', 'Niedersachsen', 'NI', 'Wittmund'),
('DE', '26446', 'Friedeburg Friedeburg', 'Niedersachsen', 'NI', 'Wittmund'),
('DE', '26446', 'Friedeburg Reepsholt', 'Niedersachsen', 'NI', 'Wittmund'),
('DE', '26446', 'Friedeburg Etzel', 'Niedersachsen', 'NI', 'Wittmund'),
('DE', '26446', 'Friedeburg Reepsholt, Abickhafe', 'Niedersachsen', 'NI', 'Wittmund'),
('DE', '26452', 'Sande', 'Niedersachsen', 'NI', 'Friesland'),
('DE', '26452', 'Sande Dykhausen', 'Niedersachsen', 'NI', 'Friesland'),
('DE', '26452', 'Sande Sande', 'Niedersachsen', 'NI', 'Friesland'),
('DE', '26452', 'Sande Neustadtgödens', 'Niedersachsen', 'NI', 'Friesland'),
('DE', '26452', 'Sande Mariensiel', 'Niedersachsen', 'NI', 'Friesland'),
('DE', '26452', 'Sande Sanderbusch', 'Niedersachsen', 'NI', 'Friesland'),
('DE', '26452', 'Sande Cäciliengroden', 'Niedersachsen', 'NI', 'Friesland'),
('DE', '26465', 'Langeoog', 'Niedersachsen', 'NI', 'Wittmund'),
('DE', '26474', 'Spiekeroog', 'Niedersachsen', 'NI', 'Wittmund'),
('DE', '26486', 'Wangerooge', 'Niedersachsen', 'NI', 'Friesland'),
('DE', '26487', 'Blomberg', 'Niedersachsen', 'NI', 'Wittmund'),
('DE', '26487', 'Neuschoo', 'Niedersachsen', 'NI', 'Wittmund'),
('DE', '26489', 'Ochtersum Westochtersum', 'Niedersachsen', 'NI', 'Wittmund'),
('DE', '26489', 'Ochtersum Ostochtersum', 'Niedersachsen', 'NI', 'Wittmund'),
('DE', '26489', 'Ochtersum', 'Niedersachsen', 'NI', 'Wittmund'),
('DE', '26506', 'Norden Norddeich', 'Niedersachsen', 'NI', 'Aurich'),
('DE', '26506', 'Norden Norden', 'Niedersachsen', 'NI', 'Aurich'),
('DE', '26506', 'Norden', 'Niedersachsen', 'NI', 'Aurich'),
('DE', '26506', 'Norden Bargebur', 'Niedersachsen', 'NI', 'Aurich'),
('DE', '26506', 'Norden Tidofeld', 'Niedersachsen', 'NI', 'Aurich'),
('DE', '26506', 'Norden Westermarsch II', 'Niedersachsen', 'NI', 'Aurich'),
('DE', '26506', 'Norden Ostermarsch', 'Niedersachsen', 'NI', 'Aurich'),
('DE', '26506', 'Norden Süderneuland II', 'Niedersachsen', 'NI', 'Aurich'),
('DE', '26506', 'Norden Westermarsch I', 'Niedersachsen', 'NI', 'Aurich'),
('DE', '26506', 'Norden Leybuchtpolder', 'Niedersachsen', 'NI', 'Aurich'),
('DE', '26506', 'Norden Süderneuland I', 'Niedersachsen', 'NI', 'Aurich'),
('DE', '26506', 'Norden Neuwesteel', 'Niedersachsen', 'NI', 'Aurich'),
('DE', '26524', 'Berumbur', 'Niedersachsen', 'NI', 'Aurich'),
('DE', '26524', 'Lütetsburg', 'Niedersachsen', 'NI', 'Aurich'),
('DE', '26524', 'Hagermarsch', 'Niedersachsen', 'NI', 'Aurich'),
('DE', '26524', 'Hage', 'Niedersachsen', 'NI', 'Aurich'),
('DE', '26524', 'Halbemond', 'Niedersachsen', 'NI', 'Aurich'),
('DE', '26529', 'Osteel', 'Niedersachsen', 'NI', 'Aurich'),
('DE', '26529', 'Rechtsupweg', 'Niedersachsen', 'NI', 'Aurich'),
('DE', '26529', 'Wirdum', 'Niedersachsen', 'NI', 'Aurich'),
('DE', '26529', 'Upgant-Schott', 'Niedersachsen', 'NI', 'Aurich'),
('DE', '26529', 'Leezdorf', 'Niedersachsen', 'NI', 'Aurich'),
('DE', '26529', 'Marienhafe', 'Niedersachsen', 'NI', 'Aurich'),
('DE', '26532', 'Großheide', 'Niedersachsen', 'NI', 'Aurich'),
('DE', '26532', 'Großheide Arle', 'Niedersachsen', 'NI', 'Aurich'),
('DE', '26532', 'Großheide Menstede-Coldinne, Menstede', 'Niedersachsen', 'NI', 'Aurich'),
('DE', '26532', 'Großheide Westerende', 'Niedersachsen', 'NI', 'Aurich'),
('DE', '26532', 'Großheide Menstede-Coldinne, Coldinne', 'Niedersachsen', 'NI', 'Aurich'),
('DE', '26532', 'Großheide Großheide', 'Niedersachsen', 'NI', 'Aurich'),
('DE', '26532', 'Großheide Berumerfehn', 'Niedersachsen', 'NI', 'Aurich'),
('DE', '26532', 'Großheide Menstede-Coldinne', 'Niedersachsen', 'NI', 'Aurich'),
('DE', '26548', 'Norderney', 'Niedersachsen', 'NI', 'Aurich'),
('DE', '26553', 'Dornum Neßmersiel', 'Niedersachsen', 'NI', 'Aurich'),
('DE', '26553', 'Dornum Dornumergrode', 'Niedersachsen', 'NI', 'Aurich'),
('DE', '26553', 'Dornum', 'Niedersachsen', 'NI', 'Aurich'),
('DE', '26553', 'Dornum Westdorf', 'Niedersachsen', 'NI', 'Aurich'),
('DE', '26553', 'Dornum Westeraccum', 'Niedersachsen', 'NI', 'Aurich'),
('DE', '26553', 'Dornum Westerbur', 'Niedersachsen', 'NI', 'Aurich'),
('DE', '26553', 'Dornum Dornum', 'Niedersachsen', 'NI', 'Aurich'),
('DE', '26553', 'Dornum Schwittersum', 'Niedersachsen', 'NI', 'Aurich'),
('DE', '26553', 'Dornum Dornumersiel', 'Niedersachsen', 'NI', 'Aurich'),
('DE', '26553', 'Dornum Roggenstede', 'Niedersachsen', 'NI', 'Aurich'),
('DE', '26553', 'Dornum Westeraccumersiel', 'Niedersachsen', 'NI', 'Aurich'),
('DE', '26553', 'Dornum Nesse', 'Niedersachsen', 'NI', 'Aurich'),
('DE', '26556', 'Schweindorf', 'Niedersachsen', 'NI', 'Wittmund'),
('DE', '26556', 'Nenndorf', 'Niedersachsen', 'NI', 'Wittmund'),
('DE', '26556', 'Westerholt', 'Niedersachsen', 'NI', 'Wittmund'),
('DE', '26556', 'Eversmeer', 'Niedersachsen', 'NI', 'Wittmund'),
('DE', '26556', 'Utarp', 'Niedersachsen', 'NI', 'Wittmund'),
('DE', '26571', 'Juist', 'Niedersachsen', 'NI', 'Aurich'),
('DE', '26579', 'Baltrum', 'Niedersachsen', 'NI', 'Aurich'),
('DE', '26603', 'Aurich', 'Niedersachsen', 'NI', 'Aurich'),
('DE', '26605', 'Aurich Egels', 'Niedersachsen', 'NI', 'Aurich'),
('DE', '26605', 'Aurich', 'Niedersachsen', 'NI', 'Aurich'),
('DE', '26605', 'Aurich Haxtum', 'Niedersachsen', 'NI', 'Aurich'),
('DE', '26605', 'Aurich Brockzetel', 'Niedersachsen', 'NI', 'Aurich'),
('DE', '26607', 'Aurich', 'Niedersachsen', 'NI', 'Aurich'),
('DE', '26624', 'Südbrookmerland Theene', 'Niedersachsen', 'NI', 'Aurich'),
('DE', '26624', 'Südbrookmerland Wiegboldsbur', 'Niedersachsen', 'NI', 'Aurich'),
('DE', '26624', 'Südbrookmerland Forlitz-Blaukirchen', 'Niedersachsen', 'NI', 'Aurich'),
('DE', '26624', 'Südbrookmerland Moordorf', 'Niedersachsen', 'NI', 'Aurich'),
('DE', '26624', 'Südbrookmerland Bedekaspel', 'Niedersachsen', 'NI', 'Aurich'),
('DE', '26624', 'Südbrookmerland', 'Niedersachsen', 'NI', 'Aurich'),
('DE', '26624', 'Südbrookmerland Münkeboe', 'Niedersachsen', 'NI', 'Aurich'),
('DE', '26624', 'Südbrookmerland Uthwerdum', 'Niedersachsen', 'NI', 'Aurich'),
('DE', '26624', 'Südbrookmerland Victorbur', 'Niedersachsen', 'NI', 'Aurich'),
('DE', '26624', 'Südbrookmerland Oldeborg', 'Niedersachsen', 'NI', 'Aurich'),
('DE', '26624', 'Südbrookmerland Moorhusen', 'Niedersachsen', 'NI', 'Aurich'),
('DE', '26629', 'Großefehn Aurich-Oldendorf', 'Niedersachsen', 'NI', 'Aurich'),
('DE', '26629', 'Großefehn Bagband', 'Niedersachsen', 'NI', 'Aurich'),
('DE', '26629', 'Großefehn Fiebing', 'Niedersachsen', 'NI', 'Aurich'),
('DE', '26629', 'Großefehn', 'Niedersachsen', 'NI', 'Aurich'),
('DE', '26629', 'Großefehn Wrisse', 'Niedersachsen', 'NI', 'Aurich'),
('DE', '26629', 'Großefehn Mittegroßefehn', 'Niedersachsen', 'NI', 'Aurich'),
('DE', '26629', 'Großefehn Akelsbarg', 'Niedersachsen', 'NI', 'Aurich'),
('DE', '26629', 'Großefehn Felde', 'Niedersachsen', 'NI', 'Aurich'),
('DE', '26629', 'Großefehn Westgroßefehn', 'Niedersachsen', 'NI', 'Aurich'),
('DE', '26629', 'Großefehn Strackholt', 'Niedersachsen', 'NI', 'Aurich'),
('DE', '26629', 'Großefehn Holtrop', 'Niedersachsen', 'NI', 'Aurich'),
('DE', '26629', 'Großefehn Ostgroßefehn', 'Niedersachsen', 'NI', 'Aurich'),
('DE', '26629', 'Großefehn Timmel', 'Niedersachsen', 'NI', 'Aurich'),
('DE', '26629', 'Großefehn Ulbargen', 'Niedersachsen', 'NI', 'Aurich'),
('DE', '26629', 'Großefehn Spetzerfehn', 'Niedersachsen', 'NI', 'Aurich'),
('DE', '26632', 'Ihlow Ihlowerfehn', 'Niedersachsen', 'NI', 'Aurich'),
('DE', '26632', 'Ihlow Barstede', 'Niedersachsen', 'NI', 'Aurich'),
('DE', '26632', 'Ihlow Ochtelbur', 'Niedersachsen', 'NI', 'Aurich'),
('DE', '26632', 'Ihlow Bangstede', 'Niedersachsen', 'NI', 'Aurich'),
('DE', '26632', 'Ihlow Riepe', 'Niedersachsen', 'NI', 'Aurich'),
('DE', '26632', 'Ihlow Riepsterhammrich', 'Niedersachsen', 'NI', 'Aurich'),
('DE', '26632', 'Ihlow', 'Niedersachsen', 'NI', 'Aurich'),
('DE', '26632', 'Ihlow Ostersander', 'Niedersachsen', 'NI', 'Aurich'),
('DE', '26632', 'Ihlow Simonswolde', 'Niedersachsen', 'NI', 'Aurich'),
('DE', '26632', 'Ihlow Ludwigsdorf', 'Niedersachsen', 'NI', 'Aurich'),
('DE', '26632', 'Ihlow Westerende-Holzloog', 'Niedersachsen', 'NI', 'Aurich'),
('DE', '26632', 'Ihlow Westerende-Kirchloog', 'Niedersachsen', 'NI', 'Aurich'),
('DE', '26639', 'Wiesmoor', 'Niedersachsen', 'NI', 'Aurich'),
('DE', '26639', 'Wiesmoor Wiesederfehn', 'Niedersachsen', 'NI', 'Aurich'),
('DE', '26639', 'Wiesmoor Wiesmoor', 'Niedersachsen', 'NI', 'Aurich'),
('DE', '26639', 'Wiesmoor Voßbarg', 'Niedersachsen', 'NI', 'Aurich'),
('DE', '26639', 'Wiesmoor Marcardsmoor', 'Niedersachsen', 'NI', 'Aurich'),
('DE', '26639', 'Wiesmoor Zwischenbergen', 'Niedersachsen', 'NI', 'Aurich'),
('DE', '26655', 'Westerstede', 'Niedersachsen', 'NI', 'Ammerland'),
('DE', '26670', 'Uplengen', 'Niedersachsen', 'NI', 'Leer'),
('DE', '26676', 'Barßel', 'Niedersachsen', 'NI', 'Cloppenburg'),
('DE', '26683', 'Saterland', 'Niedersachsen', 'NI', 'Cloppenburg'),
('DE', '26689', 'Apen', 'Niedersachsen', 'NI', 'Ammerland'),
('DE', '26721', 'Emden', 'Niedersachsen', 'NI', 'Emden, Stadt'),
('DE', '26723', 'Emden', 'Niedersachsen', 'NI', 'Emden, Stadt'),
('DE', '26725', 'Emden', 'Niedersachsen', 'NI', 'Emden, Stadt'),
('DE', '26736', 'Krummhörn Woquard', 'Niedersachsen', 'NI', 'Aurich'),
('DE', '26736', 'Krummhörn', 'Niedersachsen', 'NI', 'Aurich'),
('DE', '26736', 'Krummhörn Canum', 'Niedersachsen', 'NI', 'Aurich'),
('DE', '26736', 'Krummhörn Eilsum', 'Niedersachsen', 'NI', 'Aurich'),
('DE', '26736', 'Krummhörn Manslagt', 'Niedersachsen', 'NI', 'Aurich'),
('DE', '26736', 'Krummhörn Freepsum', 'Niedersachsen', 'NI', 'Aurich'),
('DE', '26736', 'Krummhörn Groothusen', 'Niedersachsen', 'NI', 'Aurich'),
('DE', '26736', 'Krummhörn Uttum', 'Niedersachsen', 'NI', 'Aurich'),
('DE', '26736', 'Krummhörn Greetsiel', 'Niedersachsen', 'NI', 'Aurich'),
('DE', '26736', 'Krummhörn Pilsum', 'Niedersachsen', 'NI', 'Aurich'),
('DE', '26736', 'Krummhörn Pewsum', 'Niedersachsen', 'NI', 'Aurich'),
('DE', '26736', 'Krummhörn Visquard', 'Niedersachsen', 'NI', 'Aurich'),
('DE', '26736', 'Krummhörn Woltzeten', 'Niedersachsen', 'NI', 'Aurich'),
('DE', '26736', 'Krummhörn Grimersum', 'Niedersachsen', 'NI', 'Aurich'),
('DE', '26736', 'Krummhörn Hamswehrum', 'Niedersachsen', 'NI', 'Aurich'),
('DE', '26736', 'Krummhörn Rysum', 'Niedersachsen', 'NI', 'Aurich'),
('DE', '26736', 'Krummhörn Upleward', 'Niedersachsen', 'NI', 'Aurich'),
('DE', '26736', 'Krummhörn Jennelt', 'Niedersachsen', 'NI', 'Aurich'),
('DE', '26736', 'Krummhörn Loquard', 'Niedersachsen', 'NI', 'Aurich'),
('DE', '26736', 'Krummhörn Campen', 'Niedersachsen', 'NI', 'Aurich'),
('DE', '26757', 'Borkum', 'Niedersachsen', 'NI', 'Leer'),
('DE', '26759', 'Hinte Groß Midlum', 'Niedersachsen', 'NI', 'Aurich'),
('DE', '26759', 'Hinte', 'Niedersachsen', 'NI', 'Aurich'),
('DE', '26759', 'Hinte Loppersum', 'Niedersachsen', 'NI', 'Aurich'),
('DE', '26759', 'Hinte Westerhusen', 'Niedersachsen', 'NI', 'Aurich'),
('DE', '26759', 'Hinte Hinte', 'Niedersachsen', 'NI', 'Aurich'),
('DE', '26759', 'Hinte Suurhusen', 'Niedersachsen', 'NI', 'Aurich'),
('DE', '26759', 'Hinte Canhusen', 'Niedersachsen', 'NI', 'Aurich'),
('DE', '26759', 'Hinte Cirkwehrum', 'Niedersachsen', 'NI', 'Aurich'),
('DE', '26759', 'Hinte Osterhusen', 'Niedersachsen', 'NI', 'Aurich'),
('DE', '26789', 'Leer (Ostfriesland)', 'Niedersachsen', 'NI', 'Leer'),
('DE', '26802', 'Moormerland', 'Niedersachsen', 'NI', 'Leer'),
('DE', '26810', 'Westoverledingen', 'Niedersachsen', 'NI', 'Leer'),
('DE', '26817', 'Rhauderfehn', 'Niedersachsen', 'NI', 'Leer'),
('DE', '26826', 'Weener', 'Niedersachsen', 'NI', 'Leer'),
('DE', '26831', 'Dollart', 'Niedersachsen', 'NI', 'Leer'),
('DE', '26831', 'Boen', 'Niedersachsen', 'NI', 'Leer'),
('DE', '26831', 'Wymeer', 'Niedersachsen', 'NI', 'Leer'),
('DE', '26831', 'Bunderhee', 'Niedersachsen', 'NI', 'Leer'),
('DE', '26831', 'Bunde', 'Niedersachsen', 'NI', 'Leer'),
('DE', '26835', 'Schwerinsdorf', 'Niedersachsen', 'NI', 'Leer'),
('DE', '26835', 'Firrel', 'Niedersachsen', 'NI', 'Leer'),
('DE', '26835', 'Hesel', 'Niedersachsen', 'NI', 'Leer'),
('DE', '26835', 'Holtland', 'Niedersachsen', 'NI', 'Leer'),
('DE', '26835', 'Brinkum', 'Niedersachsen', 'NI', 'Leer'),
('DE', '26835', 'Neukamperfehn', 'Niedersachsen', 'NI', 'Leer'),
('DE', '26842', 'Ostrhauderfehn', 'Niedersachsen', 'NI', 'Leer'),
('DE', '26844', 'Jemgum', 'Niedersachsen', 'NI', 'Leer'),
('DE', '26845', 'Nortmoor', 'Niedersachsen', 'NI', 'Leer'),
('DE', '26847', 'Detern', 'Niedersachsen', 'NI', 'Leer'),
('DE', '26849', 'Filsum', 'Niedersachsen', 'NI', 'Leer'),
('DE', '26871', 'Papenburg', 'Niedersachsen', 'NI', 'Emsland'),
('DE', '26892', 'Heede', 'Niedersachsen', 'NI', 'Emsland'),
('DE', '26892', 'Kluse', 'Niedersachsen', 'NI', 'Emsland'),
('DE', '26892', 'Dörpen', 'Niedersachsen', 'NI', 'Emsland'),
('DE', '26892', 'Wippingen', 'Niedersachsen', 'NI', 'Emsland'),
('DE', '26892', 'Lehe', 'Niedersachsen', 'NI', 'Emsland'),
('DE', '26897', 'Bockhorst', 'Niedersachsen', 'NI', 'Emsland'),
('DE', '26897', 'Breddenberg', 'Niedersachsen', 'NI', 'Emsland'),
('DE', '26897', 'Esterwegen', 'Niedersachsen', 'NI', 'Emsland'),
('DE', '26897', 'Hilkenbrook', 'Niedersachsen', 'NI', 'Emsland'),
('DE', '26899', 'Rhede', 'Niedersachsen', 'NI', 'Emsland'),
('DE', '26901', 'Rastdorf', 'Niedersachsen', 'NI', 'Emsland'),
('DE', '26901', 'Lorup', 'Niedersachsen', 'NI', 'Emsland'),
('DE', '26903', 'Surwold', 'Niedersachsen', 'NI', 'Emsland'),
('DE', '26904', 'Börger', 'Niedersachsen', 'NI', 'Emsland'),
('DE', '26906', 'Dersum', 'Niedersachsen', 'NI', 'Emsland'),
('DE', '26907', 'Walchum', 'Niedersachsen', 'NI', 'Emsland'),
('DE', '26909', 'Neulehe', 'Niedersachsen', 'NI', 'Emsland'),
('DE', '26909', 'Neubörger', 'Niedersachsen', 'NI', 'Emsland'),
('DE', '26919', 'Brake', 'Niedersachsen', 'NI', 'Wesermarsch'),
('DE', '26931', 'Elsfleth', 'Niedersachsen', 'NI', 'Wesermarsch'),
('DE', '26935', 'Stadland', 'Niedersachsen', 'NI', 'Wesermarsch'),
('DE', '26936', 'Stadland', 'Niedersachsen', 'NI', 'Wesermarsch'),
('DE', '26937', 'Stadland', 'Niedersachsen', 'NI', 'Wesermarsch'),
('DE', '26939', 'Ovelgönne', 'Niedersachsen', 'NI', 'Wesermarsch'),
('DE', '26954', 'Nordenham', 'Niedersachsen', 'NI', 'Wesermarsch'),
('DE', '26969', 'Butjadingen', 'Niedersachsen', 'NI', 'Wesermarsch'),
('DE', '27211', 'Bassum', 'Niedersachsen', 'NI', 'Diepholz'),
('DE', '27232', 'Sulingen', 'Niedersachsen', 'NI', 'Diepholz'),
('DE', '27239', 'Twistringen', 'Niedersachsen', 'NI', 'Diepholz'),
('DE', '27243', 'Kirchseelte', 'Niedersachsen', 'NI', 'Oldenburg'),
('DE', '27243', 'Beckeln', 'Niedersachsen', 'NI', 'Oldenburg'),
('DE', '27243', 'Dünsen', 'Niedersachsen', 'NI', 'Oldenburg'),
('DE', '27243', 'Colnrade', 'Niedersachsen', 'NI', 'Oldenburg'),
('DE', '27243', 'Winkelsett', 'Niedersachsen', 'NI', 'Oldenburg'),
('DE', '27243', 'Groß Ippener', 'Niedersachsen', 'NI', 'Oldenburg'),
('DE', '27243', 'Prinzhöfte', 'Niedersachsen', 'NI', 'Oldenburg'),
('DE', '27243', 'Harpstedt', 'Niedersachsen', 'NI', 'Oldenburg'),
('DE', '27245', 'Kirchdorf', 'Niedersachsen', 'NI', 'Diepholz'),
('DE', '27245', 'Bahrenborstel', 'Niedersachsen', 'NI', 'Diepholz'),
('DE', '27245', 'Barenburg', 'Niedersachsen', 'NI', 'Diepholz'),
('DE', '27246', 'Borstel', 'Niedersachsen', 'NI', 'Diepholz'),
('DE', '27248', 'Ehrenburg', 'Niedersachsen', 'NI', 'Diepholz'),
('DE', '27249', 'Maasen', 'Niedersachsen', 'NI', 'Diepholz'),
('DE', '27249', 'Mellinghausen', 'Niedersachsen', 'NI', 'Diepholz'),
('DE', '27251', 'Neuenkirchen', 'Niedersachsen', 'NI', 'Diepholz'),
('DE', '27251', 'Scholen', 'Niedersachsen', 'NI', 'Diepholz'),
('DE', '27252', 'Schwaförden', 'Niedersachsen', 'NI', 'Diepholz'),
('DE', '27254', 'Siedenburg', 'Niedersachsen', 'NI', 'Diepholz'),
('DE', '27254', 'Staffhorst', 'Niedersachsen', 'NI', 'Diepholz'),
('DE', '27257', 'Affinghausen', 'Niedersachsen', 'NI', 'Diepholz'),
('DE', '27257', 'Sudwalde', 'Niedersachsen', 'NI', 'Diepholz'),
('DE', '27259', 'Freistatt', 'Niedersachsen', 'NI', 'Diepholz'),
('DE', '27259', 'Wehrbleck', 'Niedersachsen', 'NI', 'Diepholz'),
('DE', '27259', 'Varrel', 'Niedersachsen', 'NI', 'Diepholz'),
('DE', '27283', 'Verden (Aller)', 'Niedersachsen', 'NI', 'Verden'),
('DE', '27299', 'Langwedel', 'Niedersachsen', 'NI', 'Verden'),
('DE', '27305', 'Engeln', 'Niedersachsen', 'NI', 'Diepholz'),
('DE', '27305', 'Süstedt', 'Niedersachsen', 'NI', 'Diepholz'),
('DE', '27305', 'Bruchhausen-Vilsen', 'Niedersachsen', 'NI', 'Diepholz'),
('DE', '27308', 'Kirchlinteln', 'Niedersachsen', 'NI', 'Verden'),
('DE', '27313', 'Dörverden', 'Niedersachsen', 'NI', 'Verden'),
('DE', '27318', 'Hoya', 'Niedersachsen', 'NI', 'Nienburg (Weser)'),
('DE', '27318', 'Hilgermissen', 'Niedersachsen', 'NI', 'Nienburg (Weser)'),
('DE', '27318', 'Hoyerhagen', 'Niedersachsen', 'NI', 'Nienburg (Weser)'),
('DE', '27321', 'Thedinghausen', 'Niedersachsen', 'NI', 'Verden'),
('DE', '27321', 'Morsum', 'Niedersachsen', 'NI', 'Verden'),
('DE', '27321', 'Emtinghausen', 'Niedersachsen', 'NI', 'Verden'),
('DE', '27324', 'Gandesbergen', 'Niedersachsen', 'NI', 'Nienburg (Weser)'),
('DE', '27324', 'Hämelhausen', 'Niedersachsen', 'NI', 'Nienburg (Weser)'),
('DE', '27324', 'Eystrup', 'Niedersachsen', 'NI', 'Nienburg (Weser)'),
('DE', '27324', 'Hassel (Weser)', 'Niedersachsen', 'NI', 'Nienburg (Weser)'),
('DE', '27327', 'Martfeld', 'Niedersachsen', 'NI', 'Diepholz'),
('DE', '27327', 'Schwarme', 'Niedersachsen', 'NI', 'Diepholz'),
('DE', '27330', 'Asendorf', 'Niedersachsen', 'NI', 'Diepholz'),
('DE', '27333', 'Bücken', 'Niedersachsen', 'NI', 'Nienburg (Weser)'),
('DE', '27333', 'Schweringen', 'Niedersachsen', 'NI', 'Nienburg (Weser)'),
('DE', '27333', 'Warpe', 'Niedersachsen', 'NI', 'Nienburg (Weser)'),
('DE', '27336', 'Rethem (Aller)', 'Niedersachsen', 'NI', 'Soltau-Fallingbostel'),
('DE', '27336', 'Frankenfeld', 'Niedersachsen', 'NI', 'Soltau-Fallingbostel'),
('DE', '27336', 'Häuslingen', 'Niedersachsen', 'NI', 'Soltau-Fallingbostel'),
('DE', '27337', 'Blender', 'Niedersachsen', 'NI', 'Verden'),
('DE', '27339', 'Riede', 'Niedersachsen', 'NI', 'Verden'),
('DE', '27356', 'Rotenburg (Wümme)', 'Niedersachsen', 'NI', 'Rotenburg (Wümme)'),
('DE', '27367', 'Bötersen', 'Niedersachsen', 'NI', 'Rotenburg (Wümme)'),
('DE', '27367', 'Sottrum', 'Niedersachsen', 'NI', 'Rotenburg (Wümme)'),
('DE', '27367', 'Horstedt', 'Niedersachsen', 'NI', 'Rotenburg (Wümme)'),
('DE', '27367', 'Ahausen', 'Niedersachsen', 'NI', 'Rotenburg (Wümme)'),
('DE', '27367', 'Reeßum', 'Niedersachsen', 'NI', 'Rotenburg (Wümme)'),
('DE', '27367', 'Hassendorf', 'Niedersachsen', 'NI', 'Rotenburg (Wümme)'),
('DE', '27367', 'Hellwege', 'Niedersachsen', 'NI', 'Rotenburg (Wümme)'),
('DE', '27374', 'Visselhövede', 'Niedersachsen', 'NI', 'Rotenburg (Wümme)'),
('DE', '27383', 'Scheeßel', 'Niedersachsen', 'NI', 'Rotenburg (Wümme)'),
('DE', '27386', 'Hemslingen', 'Niedersachsen', 'NI', 'Rotenburg (Wümme)'),
('DE', '27386', 'Bothel', 'Niedersachsen', 'NI', 'Rotenburg (Wümme)'),
('DE', '27386', 'Westerwalsede', 'Niedersachsen', 'NI', 'Rotenburg (Wümme)'),
('DE', '27386', 'Hemsbünde', 'Niedersachsen', 'NI', 'Rotenburg (Wümme)'),
('DE', '27386', 'Brockel', 'Niedersachsen', 'NI', 'Rotenburg (Wümme)'),
('DE', '27386', 'Kirchwalsede', 'Niedersachsen', 'NI', 'Rotenburg (Wümme)'),
('DE', '27389', 'Lauenbrück', 'Niedersachsen', 'NI', 'Rotenburg (Wümme)'),
('DE', '27389', 'Vahlde', 'Niedersachsen', 'NI', 'Rotenburg (Wümme)'),
('DE', '27389', 'Fintel', 'Niedersachsen', 'NI', 'Rotenburg (Wümme)'),
('DE', '27389', 'Helvesiek', 'Niedersachsen', 'NI', 'Rotenburg (Wümme)'),
('DE', '27389', 'Stemmen', 'Niedersachsen', 'NI', 'Rotenburg (Wümme)'),
('DE', '27404', 'Gyhum', 'Niedersachsen', 'NI', 'Rotenburg (Wümme)'),
('DE', '27404', 'Rhade', 'Niedersachsen', 'NI', 'Rotenburg (Wümme)'),
('DE', '27404', 'Ostereistedt', 'Niedersachsen', 'NI', 'Rotenburg (Wümme)'),
('DE', '27404', 'Zeven', 'Niedersachsen', 'NI', 'Rotenburg (Wümme)'),
('DE', '27404', 'Seedorf', 'Niedersachsen', 'NI', 'Rotenburg (Wümme)'),
('DE', '27404', 'Heeslingen', 'Niedersachsen', 'NI', 'Rotenburg (Wümme)'),
('DE', '27404', 'Elsdorf', 'Niedersachsen', 'NI', 'Rotenburg (Wümme)'),
('DE', '27412', 'Westertimke', 'Niedersachsen', 'NI', 'Rotenburg (Wümme)'),
('DE', '27412', 'Hepstedt', 'Niedersachsen', 'NI', 'Rotenburg (Wümme)'),
('DE', '27412', 'Vorwerk', 'Niedersachsen', 'NI', 'Rotenburg (Wümme)'),
('DE', '27412', 'Breddorf', 'Niedersachsen', 'NI', 'Rotenburg (Wümme)'),
('DE', '27412', 'Bülstedt', 'Niedersachsen', 'NI', 'Rotenburg (Wümme)'),
('DE', '27412', 'Wilstedt', 'Niedersachsen', 'NI', 'Rotenburg (Wümme)'),
('DE', '27412', 'Kirchtimke', 'Niedersachsen', 'NI', 'Rotenburg (Wümme)'),
('DE', '27412', 'Tarmstedt', 'Niedersachsen', 'NI', 'Rotenburg (Wümme)'),
('DE', '27419', 'Wohnste', 'Niedersachsen', 'NI', 'Rotenburg (Wümme)'),
('DE', '27419', 'Sittensen', 'Niedersachsen', 'NI', 'Rotenburg (Wümme)'),
('DE', '27419', 'Groß Meckelsen', 'Niedersachsen', 'NI', 'Rotenburg (Wümme)'),
('DE', '27419', 'Tiste', 'Niedersachsen', 'NI', 'Rotenburg (Wümme)'),
('DE', '27419', 'Vierden', 'Niedersachsen', 'NI', 'Rotenburg (Wümme)'),
('DE', '27419', 'Klein Meckelsen', 'Niedersachsen', 'NI', 'Rotenburg (Wümme)'),
('DE', '27419', 'Kalbe', 'Niedersachsen', 'NI', 'Rotenburg (Wümme)'),
('DE', '27419', 'Hamersen', 'Niedersachsen', 'NI', 'Rotenburg (Wümme)'),
('DE', '27419', 'Lengenbostel', 'Niedersachsen', 'NI', 'Rotenburg (Wümme)'),
('DE', '27432', 'Hipstedt', 'Niedersachsen', 'NI', 'Rotenburg (Wümme)'),
('DE', '27432', 'Basdahl', 'Niedersachsen', 'NI', 'Rotenburg (Wümme)'),
('DE', '27432', 'Alfstedt', 'Niedersachsen', 'NI', 'Rotenburg (Wümme)'),
('DE', '27432', 'Oerel', 'Niedersachsen', 'NI', 'Rotenburg (Wümme)'),
('DE', '27432', 'Bremervörde', 'Niedersachsen', 'NI', 'Rotenburg (Wümme)'),
('DE', '27432', 'Ebersdorf', 'Niedersachsen', 'NI', 'Rotenburg (Wümme)'),
('DE', '27442', 'Gnarrenburg', 'Niedersachsen', 'NI', 'Rotenburg (Wümme)'),
('DE', '27446', 'Sandbostel', 'Niedersachsen', 'NI', 'Rotenburg (Wümme)'),
('DE', '27446', 'Deinstedt', 'Niedersachsen', 'NI', 'Rotenburg (Wümme)'),
('DE', '27446', 'Anderlingen', 'Niedersachsen', 'NI', 'Rotenburg (Wümme)'),
('DE', '27446', 'Selsingen', 'Niedersachsen', 'NI', 'Rotenburg (Wümme)'),
('DE', '27446', 'Farven', 'Niedersachsen', 'NI', 'Rotenburg (Wümme)'),
('DE', '27449', 'Kutenholz', 'Niedersachsen', 'NI', 'Stade'),
('DE', '27472', 'Cuxhaven', 'Niedersachsen', 'NI', 'Cuxhaven'),
('DE', '27474', 'Cuxhaven', 'Niedersachsen', 'NI', 'Cuxhaven'),
('DE', '27476', 'Cuxhaven', 'Niedersachsen', 'NI', 'Cuxhaven'),
('DE', '27478', 'Cuxhaven', 'Niedersachsen', 'NI', 'Cuxhaven'),
('DE', '27607', 'Langen', 'Niedersachsen', 'NI', 'Cuxhaven'),
('DE', '27612', 'Loxstedt', 'Niedersachsen', 'NI', 'Cuxhaven'),
('DE', '27616', 'Beverstedt', 'Niedersachsen', 'NI', 'Cuxhaven'),
('DE', '27616', 'Bokel', 'Niedersachsen', 'NI', 'Cuxhaven'),
('DE', '27616', 'Appeln', 'Niedersachsen', 'NI', 'Cuxhaven'),
('DE', '27616', 'Heerstedt', 'Niedersachsen', 'NI', 'Cuxhaven'),
('DE', '27616', 'Hollen', 'Niedersachsen', 'NI', 'Cuxhaven'),
('DE', '27616', 'Stubben', 'Niedersachsen', 'NI', 'Cuxhaven'),
('DE', '27616', 'Frelsdorf', 'Niedersachsen', 'NI', 'Cuxhaven'),
('DE', '27616', 'Kirchwistedt', 'Niedersachsen', 'NI', 'Cuxhaven'),
('DE', '27616', 'Lunestedt', 'Niedersachsen', 'NI', 'Cuxhaven'),
('DE', '27619', 'Schiffdorf', 'Niedersachsen', 'NI', 'Cuxhaven'),
('DE', '27624', 'Flögeln', 'Niedersachsen', 'NI', 'Cuxhaven'),
('DE', '27624', 'Bad Bederkesa', 'Niedersachsen', 'NI', 'Cuxhaven'),
('DE', '27624', 'Elmlohe', 'Niedersachsen', 'NI', 'Cuxhaven'),
('DE', '27624', 'Kührstedt', 'Niedersachsen', 'NI', 'Cuxhaven'),
('DE', '27624', 'Ringstedt', 'Niedersachsen', 'NI', 'Cuxhaven'),
('DE', '27624', 'Drangstedt', 'Niedersachsen', 'NI', 'Cuxhaven'),
('DE', '27624', 'Köhlen', 'Niedersachsen', 'NI', 'Cuxhaven'),
('DE', '27624', 'Lintig', 'Niedersachsen', 'NI', 'Cuxhaven'),
('DE', '27628', 'Uthlede', 'Niedersachsen', 'NI', 'Cuxhaven'),
('DE', '27628', 'Bramstedt', 'Niedersachsen', 'NI', 'Cuxhaven'),
('DE', '27628', 'Sandstedt', 'Niedersachsen', 'NI', 'Cuxhaven'),
('DE', '27628', 'Hagen im Bremischen', 'Niedersachsen', 'NI', 'Cuxhaven'),
('DE', '27628', 'Wulsbüttel', 'Niedersachsen', 'NI', 'Cuxhaven'),
('DE', '27628', 'Driftsethe', 'Niedersachsen', 'NI', 'Cuxhaven'),
('DE', '27632', 'Mulsum', 'Niedersachsen', 'NI', 'Cuxhaven'),
('DE', '27632', 'Misselwarden', 'Niedersachsen', 'NI', 'Cuxhaven'),
('DE', '27632', 'Midlum', 'Niedersachsen', 'NI', 'Cuxhaven'),
('DE', '27632', 'Cappel', 'Niedersachsen', 'NI', 'Cuxhaven'),
('DE', '27632', 'Dorum', 'Niedersachsen', 'NI', 'Cuxhaven'),
('DE', '27632', 'Padingbüttel', 'Niedersachsen', 'NI', 'Cuxhaven'),
('DE', '27637', 'Nordholz', 'Niedersachsen', 'NI', 'Cuxhaven'),
('DE', '27638', 'Wremen', 'Niedersachsen', 'NI', 'Cuxhaven'),
('DE', '27711', 'Osterholz-Scharmbeck', 'Niedersachsen', 'NI', 'Osterholz'),
('DE', '27721', 'Ritterhude', 'Niedersachsen', 'NI', 'Osterholz'),
('DE', '27726', 'Worpswede', 'Niedersachsen', 'NI', 'Osterholz'),
('DE', '27729', 'Axstedt', 'Niedersachsen', 'NI', 'Osterholz'),
('DE', '27729', 'Lübberstedt', 'Niedersachsen', 'NI', 'Osterholz'),
('DE', '27729', 'Hambergen', 'Niedersachsen', 'NI', 'Osterholz'),
('DE', '27729', 'Holste', 'Niedersachsen', 'NI', 'Osterholz'),
('DE', '27729', 'Vollersode', 'Niedersachsen', 'NI', 'Osterholz'),
('DE', '27749', 'Delmenhorst', 'Niedersachsen', 'NI', 'Delmenhorst, Stadt'),
('DE', '27751', 'Delmenhorst', 'Niedersachsen', 'NI', 'Delmenhorst, Stadt'),
('DE', '27753', 'Delmenhorst', 'Niedersachsen', 'NI', 'Delmenhorst, Stadt'),
('DE', '27755', 'Delmenhorst', 'Niedersachsen', 'NI', 'Delmenhorst, Stadt'),
('DE', '27777', 'Ganderkesee', 'Niedersachsen', 'NI', 'Oldenburg'),
('DE', '27793', 'Wildeshausen', 'Niedersachsen', 'NI', 'Oldenburg'),
('DE', '27798', 'Hude (Oldenburg)', 'Niedersachsen', 'NI', 'Oldenburg'),
('DE', '27801', 'Dötlingen', 'Niedersachsen', 'NI', 'Oldenburg'),
('DE', '27804', 'Berne', 'Niedersachsen', 'NI', 'Wesermarsch'),
('DE', '27809', 'Lemwerder', 'Niedersachsen', 'NI', 'Wesermarsch'),
('DE', '28790', 'Schwanewede', 'Niedersachsen', 'NI', 'Osterholz'),
('DE', '28816', 'Stuhr', 'Niedersachsen', 'NI', 'Diepholz'),
('DE', '28832', 'Achim', 'Niedersachsen', 'NI', 'Verden'),
('DE', '28844', 'Weyhe', 'Niedersachsen', 'NI', 'Diepholz'),
('DE', '28857', 'Syke', 'Niedersachsen', 'NI', 'Diepholz'),
('DE', '28865', 'Lilienthal', 'Niedersachsen', 'NI', 'Osterholz'),
('DE', '28870', 'Ottersberg', 'Niedersachsen', 'NI', 'Verden'),
('DE', '28876', 'Oyten', 'Niedersachsen', 'NI', 'Verden'),
('DE', '28879', 'Grasberg', 'Niedersachsen', 'NI', 'Osterholz'),
('DE', '29221', 'Celle', 'Niedersachsen', 'NI', 'Celle'),
('DE', '29223', 'Celle', 'Niedersachsen', 'NI', 'Celle'),
('DE', '29225', 'Celle', 'Niedersachsen', 'NI', 'Celle'),
('DE', '29227', 'Celle', 'Niedersachsen', 'NI', 'Celle'),
('DE', '29229', 'Celle', 'Niedersachsen', 'NI', 'Celle'),
('DE', '29303', 'Bergen', 'Niedersachsen', 'NI', 'Celle'),
('DE', '29308', 'Winsen (Aller)', 'Niedersachsen', 'NI', 'Celle'),
('DE', '29313', 'Hambühren', 'Niedersachsen', 'NI', 'Celle'),
('DE', '29320', 'Hermannsburg', 'Niedersachsen', 'NI', 'Celle'),
('DE', '29323', 'Wietze', 'Niedersachsen', 'NI', 'Celle'),
('DE', '29328', 'Faßberg', 'Niedersachsen', 'NI', 'Celle'),
('DE', '29331', 'Lachendorf', 'Niedersachsen', 'NI', 'Celle'),
('DE', '29336', 'Nienhagen', 'Niedersachsen', 'NI', 'Celle'),
('DE', '29339', 'Wathlingen', 'Niedersachsen', 'NI', 'Celle'),
('DE', '29342', 'Wienhausen', 'Niedersachsen', 'NI', 'Celle'),
('DE', '29345', 'Unterlüß', 'Niedersachsen', 'NI', 'Celle'),
('DE', '29348', 'Scharnhorst', 'Niedersachsen', 'NI', 'Celle'),
('DE', '29348', 'Eschede', 'Niedersachsen', 'NI', 'Celle'),
('DE', '29351', 'Eldingen', 'Niedersachsen', 'NI', 'Celle'),
('DE', '29352', 'Adelheidsdorf', 'Niedersachsen', 'NI', 'Celle'),
('DE', '29353', 'Ahnsbeck', 'Niedersachsen', 'NI', 'Celle'),
('DE', '29355', 'Beedenbostel', 'Niedersachsen', 'NI', 'Celle'),
('DE', '29356', 'Bröckel', 'Niedersachsen', 'NI', 'Celle'),
('DE', '29358', 'Eicklingen', 'Niedersachsen', 'NI', 'Celle'),
('DE', '29359', 'Habighorst', 'Niedersachsen', 'NI', 'Celle'),
('DE', '29361', 'Höfer', 'Niedersachsen', 'NI', 'Celle'),
('DE', '29362', 'Hohne', 'Niedersachsen', 'NI', 'Celle'),
('DE', '29364', 'Langlingen', 'Niedersachsen', 'NI', 'Celle'),
('DE', '29365', 'Sprakensehl', 'Niedersachsen', 'NI', 'Gifhorn'),
('DE', '29367', 'Steinhorst', 'Niedersachsen', 'NI', 'Gifhorn'),
('DE', '29369', 'Ummern', 'Niedersachsen', 'NI', 'Gifhorn'),
('DE', '29378', 'Wittingen', 'Niedersachsen', 'NI', 'Gifhorn'),
('DE', '29379', 'Wittingen', 'Niedersachsen', 'NI', 'Gifhorn'),
('DE', '29386', 'Obernholz', 'Niedersachsen', 'NI', 'Gifhorn'),
('DE', '29386', 'Dedelstorf', 'Niedersachsen', 'NI', 'Gifhorn'),
('DE', '29386', 'Hankensbüttel', 'Niedersachsen', 'NI', 'Gifhorn'),
('DE', '29389', 'Bad Bodenteich', 'Niedersachsen', 'NI', 'Uelzen'),
('DE', '29392', 'Wesendorf', 'Niedersachsen', 'NI', 'Gifhorn'),
('DE', '29393', 'Groß Oesingen', 'Niedersachsen', 'NI', 'Gifhorn'),
('DE', '29394', 'Lüder', 'Niedersachsen', 'NI', 'Uelzen'),
('DE', '29396', 'Schönewörde', 'Niedersachsen', 'NI', 'Gifhorn'),
('DE', '29399', 'Wahrenholz', 'Niedersachsen', 'NI', 'Gifhorn'),
('DE', '29439', 'Lüchow', 'Niedersachsen', 'NI', 'Lüchow-Dannenberg'),
('DE', '29451', 'Dannenberg (Elbe) Lüggau', 'Niedersachsen', 'NI', 'Lüchow-Dannenberg'),
('DE', '29451', 'Dannenberg (Elbe)', 'Niedersachsen', 'NI', 'Lüchow-Dannenberg'),
('DE', '29451', 'Dannenberg (Elbe) Tramm', 'Niedersachsen', 'NI', 'Lüchow-Dannenberg'),
('DE', '29451', 'Dannenberg (Elbe) Niestedt', 'Niedersachsen', 'NI', 'Lüchow-Dannenberg'),
('DE', '29451', 'Dannenberg (Elbe) Streetz', 'Niedersachsen', 'NI', 'Lüchow-Dannenberg'),
('DE', '29451', 'Dannenberg (Elbe) Pisselberg', 'Niedersachsen', 'NI', 'Lüchow-Dannenberg'),
('DE', '29451', 'Dannenberg (Elbe) Riskau', 'Niedersachsen', 'NI', 'Lüchow-Dannenberg'),
('DE', '29451', 'Schaafhausen', 'Niedersachsen', 'NI', 'Lüchow-Dannenberg'),
('DE', '29451', 'Dannenberg (Elbe) Tripkau', 'Niedersachsen', 'NI', 'Lüchow-Dannenberg'),
('DE', '29451', 'Dannenberg (Elbe) Gümse', 'Niedersachsen', 'NI', 'Lüchow-Dannenberg'),
('DE', '29451', 'Dannenberg (Elbe) Sipnitz', 'Niedersachsen', 'NI', 'Lüchow-Dannenberg'),
('DE', '29451', 'Dannenberg (Elbe) Klein Heide', 'Niedersachsen', 'NI', 'Lüchow-Dannenberg'),
('DE', '29451', 'Dannenberg (Elbe) Riekau', 'Niedersachsen', 'NI', 'Lüchow-Dannenberg'),
('DE', '29451', 'Dannenberg (Elbe) Soven', 'Niedersachsen', 'NI', 'Lüchow-Dannenberg'),
('DE', '29451', 'Dannenberg (Elbe) Penkefitz', 'Niedersachsen', 'NI', 'Lüchow-Dannenberg'),
('DE', '29451', 'Dannenberg (Elbe) Prisser', 'Niedersachsen', 'NI', 'Lüchow-Dannenberg'),
('DE', '29451', 'Dannenberg (Elbe) Bückau', 'Niedersachsen', 'NI', 'Lüchow-Dannenberg'),
('DE', '29451', 'Dannenberg (Elbe) Neu Tramm', 'Niedersachsen', 'NI', 'Lüchow-Dannenberg'),
('DE', '29451', 'Dannenberg (Elbe) Prabstorf', 'Niedersachsen', 'NI', 'Lüchow-Dannenberg'),
('DE', '29451', 'Dannenberg (Elbe) Groß Heide', 'Niedersachsen', 'NI', 'Lüchow-Dannenberg'),
('DE', '29451', 'Dannenberg (Elbe) Splietau', 'Niedersachsen', 'NI', 'Lüchow-Dannenberg'),
('DE', '29451', 'Dannenberg (Elbe) Dambeck', 'Niedersachsen', 'NI', 'Lüchow-Dannenberg'),
('DE', '29451', 'Dannenberg (Elbe) Seybruch', 'Niedersachsen', 'NI', 'Lüchow-Dannenberg'),
('DE', '29451', 'Dannenberg (Elbe) Seedorf', 'Niedersachsen', 'NI', 'Lüchow-Dannenberg'),
('DE', '29451', 'Dannenberg (Elbe) Breese in der Marsch', 'Niedersachsen', 'NI', 'Lüchow-Dannenberg'),
('DE', '29451', 'Dannenberg (Elbe) Predöhlsau', 'Niedersachsen', 'NI', 'Lüchow-Dannenberg'),
('DE', '29451', 'Dannenberg (Elbe) Schmarsau', 'Niedersachsen', 'NI', 'Lüchow-Dannenberg'),
('DE', '29451', 'Dannenberg (Elbe) Liepehöfen', 'Niedersachsen', 'NI', 'Lüchow-Dannenberg'),
('DE', '29451', 'Dannenberg (Elbe) Dannenberg', 'Niedersachsen', 'NI', 'Lüchow-Dannenberg'),
('DE', '29451', 'Dannenberg (Elbe) Nebenstedt', 'Niedersachsen', 'NI', 'Lüchow-Dannenberg'),
('DE', '29456', 'Hitzacker Posade', 'Niedersachsen', 'NI', 'Lüchow-Dannenberg'),
('DE', '29456', 'Hitzacker', 'Niedersachsen', 'NI', 'Lüchow-Dannenberg'),
('DE', '29456', 'Hitzacker Nienwedel', 'Niedersachsen', 'NI', 'Lüchow-Dannenberg'),
('DE', '29456', 'Hitzacker Wussegel', 'Niedersachsen', 'NI', 'Lüchow-Dannenberg'),
('DE', '29456', 'Hitzacker Kähmen', 'Niedersachsen', 'NI', 'Lüchow-Dannenberg'),
('DE', '29456', 'Hitzacker Pussade', 'Niedersachsen', 'NI', 'Lüchow-Dannenberg'),
('DE', '29456', 'Hitzacker Grabau', 'Niedersachsen', 'NI', 'Lüchow-Dannenberg'),
('DE', '29456', 'Hitzacker Bahrendorf', 'Niedersachsen', 'NI', 'Lüchow-Dannenberg'),
('DE', '29456', 'Hitzacker Meudelfitz, Eichengrund', 'Niedersachsen', 'NI', 'Lüchow-Dannenberg'),
('DE', '29456', 'Hitzacker Leitstade', 'Niedersachsen', 'NI', 'Lüchow-Dannenberg'),
('DE', '29456', 'Hitzacker Seerau', 'Niedersachsen', 'NI', 'Lüchow-Dannenberg'),
('DE', '29456', 'Hitzacker Gut Hagen', 'Niedersachsen', 'NI', 'Lüchow-Dannenberg'),
('DE', '29456', 'Hitzacker Wietzetze bei Hitzacker', 'Niedersachsen', 'NI', 'Lüchow-Dannenberg'),
('DE', '29456', 'Hitzacker Tiesmesland', 'Niedersachsen', 'NI', 'Lüchow-Dannenberg'),
('DE', '29456', 'Hitzacker Sarchem', 'Niedersachsen', 'NI', 'Lüchow-Dannenberg'),
('DE', '29456', 'Hitzacker Meudelfitz, Gut Meudelfitz', 'Niedersachsen', 'NI', 'Lüchow-Dannenberg'),
('DE', '29456', 'Hitzacker Meudelfitz', 'Niedersachsen', 'NI', 'Lüchow-Dannenberg'),
('DE', '29456', 'Hitzacker Tießau', 'Niedersachsen', 'NI', 'Lüchow-Dannenberg'),
('DE', '29456', 'Hitzacker Hitzacker', 'Niedersachsen', 'NI', 'Lüchow-Dannenberg');
INSERT INTO `plz_tabelle` (`lkz`, `plz`, `ort_stadtteil`, `bundesland`, `blkz`, `kreis`) VALUES
('DE', '29456', 'Hitzacker Harlingen', 'Niedersachsen', 'NI', 'Lüchow-Dannenberg'),
('DE', '29459', 'Clenze Bösen', 'Niedersachsen', 'NI', 'Lüchow-Dannenberg'),
('DE', '29459', 'Clenze Mützen', 'Niedersachsen', 'NI', 'Lüchow-Dannenberg'),
('DE', '29459', 'Clenze Dalitz', 'Niedersachsen', 'NI', 'Lüchow-Dannenberg'),
('DE', '29459', 'Clenze Meußließen', 'Niedersachsen', 'NI', 'Lüchow-Dannenberg'),
('DE', '29459', 'Clenze Quartzau', 'Niedersachsen', 'NI', 'Lüchow-Dannenberg'),
('DE', '29459', 'Clenze Kloster', 'Niedersachsen', 'NI', 'Lüchow-Dannenberg'),
('DE', '29459', 'Clenze Korvin', 'Niedersachsen', 'NI', 'Lüchow-Dannenberg'),
('DE', '29459', 'Clenze Prießeck', 'Niedersachsen', 'NI', 'Lüchow-Dannenberg'),
('DE', '29459', 'Clenze', 'Niedersachsen', 'NI', 'Lüchow-Dannenberg'),
('DE', '29459', 'Clenze Lefitz', 'Niedersachsen', 'NI', 'Lüchow-Dannenberg'),
('DE', '29459', 'Clenze Braudel', 'Niedersachsen', 'NI', 'Lüchow-Dannenberg'),
('DE', '29459', 'Clenze Reddereitz', 'Niedersachsen', 'NI', 'Lüchow-Dannenberg'),
('DE', '29459', 'Clenze Bausen', 'Niedersachsen', 'NI', 'Lüchow-Dannenberg'),
('DE', '29459', 'Clenze Granstedt', 'Niedersachsen', 'NI', 'Lüchow-Dannenberg'),
('DE', '29459', 'Clenze Clenze', 'Niedersachsen', 'NI', 'Lüchow-Dannenberg'),
('DE', '29459', 'Clenze Kussebode', 'Niedersachsen', 'NI', 'Lüchow-Dannenberg'),
('DE', '29459', 'Clenze Bussau', 'Niedersachsen', 'NI', 'Lüchow-Dannenberg'),
('DE', '29459', 'Clenze Beseland', 'Niedersachsen', 'NI', 'Lüchow-Dannenberg'),
('DE', '29459', 'Clenze Vaddensen', 'Niedersachsen', 'NI', 'Lüchow-Dannenberg'),
('DE', '29459', 'Clenze Gohlefanz', 'Niedersachsen', 'NI', 'Lüchow-Dannenberg'),
('DE', '29459', 'Clenze Satkau', 'Niedersachsen', 'NI', 'Lüchow-Dannenberg'),
('DE', '29459', 'Clenze Groß Sachau', 'Niedersachsen', 'NI', 'Lüchow-Dannenberg'),
('DE', '29459', 'Clenze Schlannau', 'Niedersachsen', 'NI', 'Lüchow-Dannenberg'),
('DE', '29459', 'Clenze Kassau', 'Niedersachsen', 'NI', 'Lüchow-Dannenberg'),
('DE', '29459', 'Clenze Gistenbeck', 'Niedersachsen', 'NI', 'Lüchow-Dannenberg'),
('DE', '29459', 'Clenze Guhreitzen', 'Niedersachsen', 'NI', 'Lüchow-Dannenberg'),
('DE', '29459', 'Clenze Seelwig', 'Niedersachsen', 'NI', 'Lüchow-Dannenberg'),
('DE', '29462', 'Wustrow', 'Niedersachsen', 'NI', 'Lüchow-Dannenberg'),
('DE', '29465', 'Schnega Solkau', 'Niedersachsen', 'NI', 'Lüchow-Dannenberg'),
('DE', '29465', 'Schnega Warpke', 'Niedersachsen', 'NI', 'Lüchow-Dannenberg'),
('DE', '29465', 'Schnega Winterweyhe', 'Niedersachsen', 'NI', 'Lüchow-Dannenberg'),
('DE', '29465', 'Schnega', 'Niedersachsen', 'NI', 'Lüchow-Dannenberg'),
('DE', '29465', 'Schnega Molden', 'Niedersachsen', 'NI', 'Lüchow-Dannenberg'),
('DE', '29465', 'Schnega Starrel', 'Niedersachsen', 'NI', 'Lüchow-Dannenberg'),
('DE', '29465', 'Schnega Schäpingen', 'Niedersachsen', 'NI', 'Lüchow-Dannenberg'),
('DE', '29465', 'Schnega Lütenthien', 'Niedersachsen', 'NI', 'Lüchow-Dannenberg'),
('DE', '29465', 'Schnega Harper Mühle', 'Niedersachsen', 'NI', 'Lüchow-Dannenberg'),
('DE', '29465', 'Schnega Oldendorfer Mühle', 'Niedersachsen', 'NI', 'Lüchow-Dannenberg'),
('DE', '29465', 'Schnega Thune', 'Niedersachsen', 'NI', 'Lüchow-Dannenberg'),
('DE', '29465', 'Schnega Schnega', 'Niedersachsen', 'NI', 'Lüchow-Dannenberg'),
('DE', '29465', 'Schnega Proitze', 'Niedersachsen', 'NI', 'Lüchow-Dannenberg'),
('DE', '29465', 'Schnega Proitzer Mühle', 'Niedersachsen', 'NI', 'Lüchow-Dannenberg'),
('DE', '29465', 'Schnega Bahnhof Varbitz', 'Niedersachsen', 'NI', 'Lüchow-Dannenberg'),
('DE', '29465', 'Schnega Billerbeck', 'Niedersachsen', 'NI', 'Lüchow-Dannenberg'),
('DE', '29465', 'Schnega Schnega/Bahnhof', 'Niedersachsen', 'NI', 'Lüchow-Dannenberg'),
('DE', '29465', 'Schnega Gielau', 'Niedersachsen', 'NI', 'Lüchow-Dannenberg'),
('DE', '29465', 'Schnega Gielauer Mühle', 'Niedersachsen', 'NI', 'Lüchow-Dannenberg'),
('DE', '29465', 'Schnega Göhr', 'Niedersachsen', 'NI', 'Lüchow-Dannenberg'),
('DE', '29465', 'Schnega Grotenhof', 'Niedersachsen', 'NI', 'Lüchow-Dannenberg'),
('DE', '29465', 'Schnega Harpe', 'Niedersachsen', 'NI', 'Lüchow-Dannenberg'),
('DE', '29465', 'Schnega Kreyenhagen', 'Niedersachsen', 'NI', 'Lüchow-Dannenberg'),
('DE', '29465', 'Schnega Leisten', 'Niedersachsen', 'NI', 'Lüchow-Dannenberg'),
('DE', '29465', 'Schnega Gledeberg', 'Niedersachsen', 'NI', 'Lüchow-Dannenberg'),
('DE', '29465', 'Schnega Loitze', 'Niedersachsen', 'NI', 'Lüchow-Dannenberg'),
('DE', '29465', 'Schnega Külitz', 'Niedersachsen', 'NI', 'Lüchow-Dannenberg'),
('DE', '29465', 'Schnega Oldendorf', 'Niedersachsen', 'NI', 'Lüchow-Dannenberg'),
('DE', '29468', 'Bergen (Dumme) Bergen', 'Niedersachsen', 'NI', 'Lüchow-Dannenberg'),
('DE', '29468', 'Bergen (Dumme)', 'Niedersachsen', 'NI', 'Lüchow-Dannenberg'),
('DE', '29468', 'Bergen (Dumme) Spithal', 'Niedersachsen', 'NI', 'Lüchow-Dannenberg'),
('DE', '29468', 'Bergen (Dumme) Jiggel', 'Niedersachsen', 'NI', 'Lüchow-Dannenberg'),
('DE', '29468', 'Bergen (Dumme) Malsleben', 'Niedersachsen', 'NI', 'Lüchow-Dannenberg'),
('DE', '29468', 'Bergen (Dumme) Banzau', 'Niedersachsen', 'NI', 'Lüchow-Dannenberg'),
('DE', '29468', 'Bergen (Dumme) Belau', 'Niedersachsen', 'NI', 'Lüchow-Dannenberg'),
('DE', '29468', 'Bergen (Dumme) Nienbergen', 'Niedersachsen', 'NI', 'Lüchow-Dannenberg'),
('DE', '29468', 'Bergen (Dumme) Brüchauer Mühle', 'Niedersachsen', 'NI', 'Lüchow-Dannenberg'),
('DE', '29468', 'Bergen (Dumme) Wöhningen', 'Niedersachsen', 'NI', 'Lüchow-Dannenberg'),
('DE', '29471', 'Gartow Falkenmoor', 'Niedersachsen', 'NI', 'Lüchow-Dannenberg'),
('DE', '29471', 'Gartow Laasche', 'Niedersachsen', 'NI', 'Lüchow-Dannenberg'),
('DE', '29471', 'Gartow', 'Niedersachsen', 'NI', 'Lüchow-Dannenberg'),
('DE', '29471', 'Gartow Nienwalde', 'Niedersachsen', 'NI', 'Lüchow-Dannenberg'),
('DE', '29471', 'Gartow Gartow', 'Niedersachsen', 'NI', 'Lüchow-Dannenberg'),
('DE', '29471', 'Gartow Rucksmoor', 'Niedersachsen', 'NI', 'Lüchow-Dannenberg'),
('DE', '29471', 'Gartow Rondel', 'Niedersachsen', 'NI', 'Lüchow-Dannenberg'),
('DE', '29471', 'Gartow Quarnstedt', 'Niedersachsen', 'NI', 'Lüchow-Dannenberg'),
('DE', '29472', 'Damnatz Landsatz', 'Niedersachsen', 'NI', 'Lüchow-Dannenberg'),
('DE', '29472', 'Damnatz Kamerun', 'Niedersachsen', 'NI', 'Lüchow-Dannenberg'),
('DE', '29472', 'Damnatz Barnitz', 'Niedersachsen', 'NI', 'Lüchow-Dannenberg'),
('DE', '29472', 'Damnatz Damnatz', 'Niedersachsen', 'NI', 'Lüchow-Dannenberg'),
('DE', '29472', 'Damnatz Jasebeck', 'Niedersachsen', 'NI', 'Lüchow-Dannenberg'),
('DE', '29472', 'Damnatz', 'Niedersachsen', 'NI', 'Lüchow-Dannenberg'),
('DE', '29473', 'Göhrde Nadlitz', 'Niedersachsen', 'NI', 'Lüchow-Dannenberg'),
('DE', '29473', 'Göhrde Dübbekold', 'Niedersachsen', 'NI', 'Lüchow-Dannenberg'),
('DE', '29473', 'Göhrde Metzingen', 'Niedersachsen', 'NI', 'Lüchow-Dannenberg'),
('DE', '29473', 'Göhrde Kollase', 'Niedersachsen', 'NI', 'Lüchow-Dannenberg'),
('DE', '29473', 'Göhrde Sarenseck', 'Niedersachsen', 'NI', 'Lüchow-Dannenberg'),
('DE', '29473', 'Göhrde Mailage', 'Niedersachsen', 'NI', 'Lüchow-Dannenberg'),
('DE', '29473', 'Göhrde Tollendorf', 'Niedersachsen', 'NI', 'Lüchow-Dannenberg'),
('DE', '29473', 'Göhrde Schmardau', 'Niedersachsen', 'NI', 'Lüchow-Dannenberg'),
('DE', '29473', 'Göhrde Schnadlitz', 'Niedersachsen', 'NI', 'Lüchow-Dannenberg'),
('DE', '29473', 'Göhrde Plumbohm', 'Niedersachsen', 'NI', 'Lüchow-Dannenberg'),
('DE', '29473', 'Göhrde Bredenbock', 'Niedersachsen', 'NI', 'Lüchow-Dannenberg'),
('DE', '29473', 'Göhrde Schmessau', 'Niedersachsen', 'NI', 'Lüchow-Dannenberg'),
('DE', '29473', 'Göhrde Govelin', 'Niedersachsen', 'NI', 'Lüchow-Dannenberg'),
('DE', '29473', 'Göhrde Wedderin', 'Niedersachsen', 'NI', 'Lüchow-Dannenberg'),
('DE', '29473', 'Göhrde', 'Niedersachsen', 'NI', 'Lüchow-Dannenberg'),
('DE', '29473', 'Göhrde Zienitz', 'Niedersachsen', 'NI', 'Lüchow-Dannenberg'),
('DE', '29473', 'Göhrde Göhrde', 'Niedersachsen', 'NI', 'Lüchow-Dannenberg'),
('DE', '29475', 'Gorleben Gorleben', 'Niedersachsen', 'NI', 'Lüchow-Dannenberg'),
('DE', '29475', 'Gorleben Meetschow', 'Niedersachsen', 'NI', 'Lüchow-Dannenberg'),
('DE', '29475', 'Gorleben', 'Niedersachsen', 'NI', 'Lüchow-Dannenberg'),
('DE', '29476', 'Gusborn Quickborn', 'Niedersachsen', 'NI', 'Lüchow-Dannenberg'),
('DE', '29476', 'Gusborn Siemen', 'Niedersachsen', 'NI', 'Lüchow-Dannenberg'),
('DE', '29476', 'Gusborn Zadrau', 'Niedersachsen', 'NI', 'Lüchow-Dannenberg'),
('DE', '29476', 'Gusborn Groß Gusborn', 'Niedersachsen', 'NI', 'Lüchow-Dannenberg'),
('DE', '29476', 'Gusborn Klein Gusborn', 'Niedersachsen', 'NI', 'Lüchow-Dannenberg'),
('DE', '29476', 'Gusborn', 'Niedersachsen', 'NI', 'Lüchow-Dannenberg'),
('DE', '29478', 'Höhbeck', 'Niedersachsen', 'NI', 'Lüchow-Dannenberg'),
('DE', '29478', 'Höhbeck Vietze', 'Niedersachsen', 'NI', 'Lüchow-Dannenberg'),
('DE', '29478', 'Höhbeck Restorf', 'Niedersachsen', 'NI', 'Lüchow-Dannenberg'),
('DE', '29478', 'Höhbeck Brünkendorf', 'Niedersachsen', 'NI', 'Lüchow-Dannenberg'),
('DE', '29478', 'Höhbeck Pevestorf', 'Niedersachsen', 'NI', 'Lüchow-Dannenberg'),
('DE', '29478', 'Höhbeck Schwedenschanze', 'Niedersachsen', 'NI', 'Lüchow-Dannenberg'),
('DE', '29479', 'Jameln Breese im Bruche', 'Niedersachsen', 'NI', 'Lüchow-Dannenberg'),
('DE', '29479', 'Jameln Jameln', 'Niedersachsen', 'NI', 'Lüchow-Dannenberg'),
('DE', '29479', 'Jameln Breselenz', 'Niedersachsen', 'NI', 'Lüchow-Dannenberg'),
('DE', '29479', 'Jameln', 'Niedersachsen', 'NI', 'Lüchow-Dannenberg'),
('DE', '29479', 'Jameln Wibbese', 'Niedersachsen', 'NI', 'Lüchow-Dannenberg'),
('DE', '29479', 'Jameln Platenlaase', 'Niedersachsen', 'NI', 'Lüchow-Dannenberg'),
('DE', '29479', 'Jameln Teichlosen', 'Niedersachsen', 'NI', 'Lüchow-Dannenberg'),
('DE', '29479', 'Jameln Langenhorst', 'Niedersachsen', 'NI', 'Lüchow-Dannenberg'),
('DE', '29479', 'Jameln Volkfien', 'Niedersachsen', 'NI', 'Lüchow-Dannenberg'),
('DE', '29479', 'Jameln Mehlfien', 'Niedersachsen', 'NI', 'Lüchow-Dannenberg'),
('DE', '29479', 'Jameln Breustian', 'Niedersachsen', 'NI', 'Lüchow-Dannenberg'),
('DE', '29479', 'Jameln Hoheluft', 'Niedersachsen', 'NI', 'Lüchow-Dannenberg'),
('DE', '29479', 'Jameln Jamelner Mühle', 'Niedersachsen', 'NI', 'Lüchow-Dannenberg'),
('DE', '29481', 'Karwitz Lenzen', 'Niedersachsen', 'NI', 'Lüchow-Dannenberg'),
('DE', '29481', 'Karwitz Dragahn', 'Niedersachsen', 'NI', 'Lüchow-Dannenberg'),
('DE', '29481', 'Karwitz Gamehlen', 'Niedersachsen', 'NI', 'Lüchow-Dannenberg'),
('DE', '29481', 'Karwitz', 'Niedersachsen', 'NI', 'Lüchow-Dannenberg'),
('DE', '29481', 'Karwitz Pudripp', 'Niedersachsen', 'NI', 'Lüchow-Dannenberg'),
('DE', '29481', 'Karwitz Nausen', 'Niedersachsen', 'NI', 'Lüchow-Dannenberg'),
('DE', '29481', 'Karwitz Lebbien', 'Niedersachsen', 'NI', 'Lüchow-Dannenberg'),
('DE', '29481', 'Karwitz Pudripp/Bahnhof', 'Niedersachsen', 'NI', 'Lüchow-Dannenberg'),
('DE', '29481', 'Karwitz Thunpadel', 'Niedersachsen', 'NI', 'Lüchow-Dannenberg'),
('DE', '29481', 'Karwitz Karwitz', 'Niedersachsen', 'NI', 'Lüchow-Dannenberg'),
('DE', '29482', 'Küsten Sallahn', 'Niedersachsen', 'NI', 'Lüchow-Dannenberg'),
('DE', '29482', 'Küsten Karmitz', 'Niedersachsen', 'NI', 'Lüchow-Dannenberg'),
('DE', '29482', 'Küsten Saggrian', 'Niedersachsen', 'NI', 'Lüchow-Dannenberg'),
('DE', '29482', 'Küsten', 'Niedersachsen', 'NI', 'Lüchow-Dannenberg'),
('DE', '29482', 'Küsten Oldemühle', 'Niedersachsen', 'NI', 'Lüchow-Dannenberg'),
('DE', '29482', 'Küsten Krummasel', 'Niedersachsen', 'NI', 'Lüchow-Dannenberg'),
('DE', '29482', 'Küsten Tüschau', 'Niedersachsen', 'NI', 'Lüchow-Dannenberg'),
('DE', '29482', 'Küsten Klein Witzeetze', 'Niedersachsen', 'NI', 'Lüchow-Dannenberg'),
('DE', '29482', 'Küsten Tolstefanz', 'Niedersachsen', 'NI', 'Lüchow-Dannenberg'),
('DE', '29484', 'Langendorf', 'Niedersachsen', 'NI', 'Lüchow-Dannenberg'),
('DE', '29485', 'Lemgow', 'Niedersachsen', 'NI', 'Lüchow-Dannenberg'),
('DE', '29487', 'Luckau', 'Niedersachsen', 'NI', 'Lüchow-Dannenberg'),
('DE', '29488', 'Lübbow', 'Niedersachsen', 'NI', 'Lüchow-Dannenberg'),
('DE', '29490', 'Neu Darchau', 'Niedersachsen', 'NI', 'Lüchow-Dannenberg'),
('DE', '29491', 'Prezelle', 'Niedersachsen', 'NI', 'Lüchow-Dannenberg'),
('DE', '29493', 'Schnackenburg', 'Niedersachsen', 'NI', 'Lüchow-Dannenberg'),
('DE', '29494', 'Trebel', 'Niedersachsen', 'NI', 'Lüchow-Dannenberg'),
('DE', '29496', 'Waddeweitz Sareitz', 'Niedersachsen', 'NI', 'Lüchow-Dannenberg'),
('DE', '29496', 'Waddeweitz Dickfeitzen', 'Niedersachsen', 'NI', 'Lüchow-Dannenberg'),
('DE', '29496', 'Waddeweitz Zebelin', 'Niedersachsen', 'NI', 'Lüchow-Dannenberg'),
('DE', '29496', 'Waddeweitz Kröte', 'Niedersachsen', 'NI', 'Lüchow-Dannenberg'),
('DE', '29496', 'Waddeweitz Kiefen', 'Niedersachsen', 'NI', 'Lüchow-Dannenberg'),
('DE', '29496', 'Waddeweitz Schlanze', 'Niedersachsen', 'NI', 'Lüchow-Dannenberg'),
('DE', '29496', 'Waddeweitz Diahren', 'Niedersachsen', 'NI', 'Lüchow-Dannenberg'),
('DE', '29496', 'Waddeweitz Wittfeitzen, Groß Wittfeitzen', 'Niedersachsen', 'NI', 'Lüchow-Dannenberg'),
('DE', '29496', 'Waddeweitz Salderatzen', 'Niedersachsen', 'NI', 'Lüchow-Dannenberg'),
('DE', '29496', 'Waddeweitz Klein Gaddau', 'Niedersachsen', 'NI', 'Lüchow-Dannenberg'),
('DE', '29496', 'Waddeweitz Wittfeitzen, Klein Wittfeitzen', 'Niedersachsen', 'NI', 'Lüchow-Dannenberg'),
('DE', '29496', 'Waddeweitz Maddau', 'Niedersachsen', 'NI', 'Lüchow-Dannenberg'),
('DE', '29496', 'Waddeweitz Dommatzen', 'Niedersachsen', 'NI', 'Lüchow-Dannenberg'),
('DE', '29496', 'Waddeweitz Groß Gaddau', 'Niedersachsen', 'NI', 'Lüchow-Dannenberg'),
('DE', '29496', 'Waddeweitz Hohenvolkfien', 'Niedersachsen', 'NI', 'Lüchow-Dannenberg'),
('DE', '29496', 'Waddeweitz Wittfeitzen', 'Niedersachsen', 'NI', 'Lüchow-Dannenberg'),
('DE', '29496', 'Waddeweitz Gohlau', 'Niedersachsen', 'NI', 'Lüchow-Dannenberg'),
('DE', '29496', 'Waddeweitz Waddeweitz', 'Niedersachsen', 'NI', 'Lüchow-Dannenberg'),
('DE', '29496', 'Waddeweitz', 'Niedersachsen', 'NI', 'Lüchow-Dannenberg'),
('DE', '29496', 'Waddeweitz Bischof', 'Niedersachsen', 'NI', 'Lüchow-Dannenberg'),
('DE', '29496', 'Waddeweitz Marlin', 'Niedersachsen', 'NI', 'Lüchow-Dannenberg'),
('DE', '29496', 'Waddeweitz Kukate', 'Niedersachsen', 'NI', 'Lüchow-Dannenberg'),
('DE', '29497', 'Woltersdorf', 'Niedersachsen', 'NI', 'Lüchow-Dannenberg'),
('DE', '29499', 'Zernien', 'Niedersachsen', 'NI', 'Lüchow-Dannenberg'),
('DE', '29525', 'Uelzen', 'Niedersachsen', 'NI', 'Uelzen'),
('DE', '29549', 'Bad Bevensen', 'Niedersachsen', 'NI', 'Uelzen'),
('DE', '29553', 'Bienenbüttel', 'Niedersachsen', 'NI', 'Uelzen'),
('DE', '29556', 'Suderburg', 'Niedersachsen', 'NI', 'Uelzen'),
('DE', '29559', 'Wrestedt', 'Niedersachsen', 'NI', 'Uelzen'),
('DE', '29562', 'Suhlendorf', 'Niedersachsen', 'NI', 'Uelzen'),
('DE', '29565', 'Wriedel', 'Niedersachsen', 'NI', 'Uelzen'),
('DE', '29568', 'Wieren', 'Niedersachsen', 'NI', 'Uelzen'),
('DE', '29571', 'Rosche', 'Niedersachsen', 'NI', 'Uelzen'),
('DE', '29574', 'Ebstorf', 'Niedersachsen', 'NI', 'Uelzen'),
('DE', '29575', 'Altenmedingen', 'Niedersachsen', 'NI', 'Uelzen'),
('DE', '29576', 'Barum', 'Niedersachsen', 'NI', 'Uelzen'),
('DE', '29578', 'Eimke', 'Niedersachsen', 'NI', 'Uelzen'),
('DE', '29579', 'Emmendorf', 'Niedersachsen', 'NI', 'Uelzen'),
('DE', '29581', 'Gerdau', 'Niedersachsen', 'NI', 'Uelzen'),
('DE', '29582', 'Hanstedt I', 'Niedersachsen', 'NI', 'Uelzen'),
('DE', '29584', 'Himbergen', 'Niedersachsen', 'NI', 'Uelzen'),
('DE', '29585', 'Jelmstorf', 'Niedersachsen', 'NI', 'Uelzen'),
('DE', '29587', 'Natendorf', 'Niedersachsen', 'NI', 'Uelzen'),
('DE', '29588', 'Oetzen', 'Niedersachsen', 'NI', 'Uelzen'),
('DE', '29590', 'Rätzlingen', 'Niedersachsen', 'NI', 'Uelzen'),
('DE', '29591', 'Römstedt', 'Niedersachsen', 'NI', 'Uelzen'),
('DE', '29593', 'Schwienau', 'Niedersachsen', 'NI', 'Uelzen'),
('DE', '29594', 'Soltendieck', 'Niedersachsen', 'NI', 'Uelzen'),
('DE', '29596', 'Stadensen', 'Niedersachsen', 'NI', 'Uelzen'),
('DE', '29597', 'Stoetze', 'Niedersachsen', 'NI', 'Uelzen'),
('DE', '29599', 'Weste', 'Niedersachsen', 'NI', 'Uelzen'),
('DE', '29614', 'Soltau', 'Niedersachsen', 'NI', 'Soltau-Fallingbostel'),
('DE', '29633', 'Munster', 'Niedersachsen', 'NI', 'Soltau-Fallingbostel'),
('DE', '29640', 'Schneverdingen', 'Niedersachsen', 'NI', 'Soltau-Fallingbostel'),
('DE', '29643', 'Neuenkirchen', 'Niedersachsen', 'NI', 'Soltau-Fallingbostel'),
('DE', '29646', 'Bispingen', 'Niedersachsen', 'NI', 'Soltau-Fallingbostel'),
('DE', '29649', 'Wietzendorf', 'Niedersachsen', 'NI', 'Soltau-Fallingbostel'),
('DE', '29664', 'Walsrode', 'Niedersachsen', 'NI', 'Soltau-Fallingbostel'),
('DE', '29683', 'Fallingbostel', 'Niedersachsen', 'NI', 'Soltau-Fallingbostel'),
('DE', '29690', 'Buchholz (Aller)', 'Niedersachsen', 'NI', 'Soltau-Fallingbostel'),
('DE', '29690', 'Gilten', 'Niedersachsen', 'NI', 'Soltau-Fallingbostel'),
('DE', '29690', 'Grethem', 'Niedersachsen', 'NI', 'Soltau-Fallingbostel'),
('DE', '29690', 'Lindwedel', 'Niedersachsen', 'NI', 'Soltau-Fallingbostel'),
('DE', '29690', 'Schwarmstedt', 'Niedersachsen', 'NI', 'Soltau-Fallingbostel'),
('DE', '29690', 'Essel', 'Niedersachsen', 'NI', 'Soltau-Fallingbostel'),
('DE', '29693', 'Hodenhagen', 'Niedersachsen', 'NI', 'Soltau-Fallingbostel'),
('DE', '29693', 'Ahlden (Aller)', 'Niedersachsen', 'NI', 'Soltau-Fallingbostel'),
('DE', '29693', 'Hademstorf', 'Niedersachsen', 'NI', 'Soltau-Fallingbostel'),
('DE', '29693', 'Böhme', 'Niedersachsen', 'NI', 'Soltau-Fallingbostel'),
('DE', '29693', 'Eickeloh', 'Niedersachsen', 'NI', 'Soltau-Fallingbostel'),
('DE', '29699', 'Bomlitz', 'Niedersachsen', 'NI', 'Soltau-Fallingbostel'),
('DE', '30159', 'Hannover', 'Niedersachsen', 'NI', 'Region Hannover'),
('DE', '30161', 'Hannover', 'Niedersachsen', 'NI', 'Region Hannover'),
('DE', '30163', 'Hannover', 'Niedersachsen', 'NI', 'Region Hannover'),
('DE', '30165', 'Hannover', 'Niedersachsen', 'NI', 'Region Hannover'),
('DE', '30167', 'Hannover', 'Niedersachsen', 'NI', 'Region Hannover'),
('DE', '30169', 'Hannover', 'Niedersachsen', 'NI', 'Region Hannover'),
('DE', '30171', 'Hannover', 'Niedersachsen', 'NI', 'Region Hannover'),
('DE', '30173', 'Hannover', 'Niedersachsen', 'NI', 'Region Hannover'),
('DE', '30175', 'Hannover', 'Niedersachsen', 'NI', 'Region Hannover'),
('DE', '30177', 'Hannover', 'Niedersachsen', 'NI', 'Region Hannover'),
('DE', '30179', 'Hannover', 'Niedersachsen', 'NI', 'Region Hannover'),
('DE', '30419', 'Hannover', 'Niedersachsen', 'NI', 'Region Hannover'),
('DE', '30449', 'Hannover', 'Niedersachsen', 'NI', 'Region Hannover'),
('DE', '30451', 'Hannover', 'Niedersachsen', 'NI', 'Region Hannover'),
('DE', '30453', 'Hannover', 'Niedersachsen', 'NI', 'Region Hannover'),
('DE', '30455', 'Hannover', 'Niedersachsen', 'NI', 'Region Hannover'),
('DE', '30457', 'Hannover', 'Niedersachsen', 'NI', 'Region Hannover'),
('DE', '30459', 'Hannover', 'Niedersachsen', 'NI', 'Region Hannover'),
('DE', '30519', 'Hannover', 'Niedersachsen', 'NI', 'Region Hannover'),
('DE', '30521', 'Hannover', 'Niedersachsen', 'NI', 'Region Hannover'),
('DE', '30539', 'Hannover', 'Niedersachsen', 'NI', 'Region Hannover'),
('DE', '30559', 'Hannover', 'Niedersachsen', 'NI', 'Region Hannover'),
('DE', '30625', 'Hannover', 'Niedersachsen', 'NI', 'Region Hannover'),
('DE', '30627', 'Hannover', 'Niedersachsen', 'NI', 'Region Hannover'),
('DE', '30629', 'Hannover', 'Niedersachsen', 'NI', 'Region Hannover'),
('DE', '30655', 'Hannover', 'Niedersachsen', 'NI', 'Region Hannover'),
('DE', '30657', 'Hannover', 'Niedersachsen', 'NI', 'Region Hannover'),
('DE', '30659', 'Hannover', 'Niedersachsen', 'NI', 'Region Hannover'),
('DE', '30669', 'Hannover', 'Niedersachsen', 'NI', 'Region Hannover'),
('DE', '30823', 'Garbsen', 'Niedersachsen', 'NI', 'Region Hannover'),
('DE', '30826', 'Garbsen', 'Niedersachsen', 'NI', 'Region Hannover'),
('DE', '30827', 'Garbsen', 'Niedersachsen', 'NI', 'Region Hannover'),
('DE', '30851', 'Langenhagen', 'Niedersachsen', 'NI', 'Region Hannover'),
('DE', '30853', 'Langenhagen', 'Niedersachsen', 'NI', 'Region Hannover'),
('DE', '30855', 'Langenhagen', 'Niedersachsen', 'NI', 'Region Hannover'),
('DE', '30880', 'Laatzen', 'Niedersachsen', 'NI', 'Region Hannover'),
('DE', '30890', 'Barsinghausen', 'Niedersachsen', 'NI', 'Region Hannover'),
('DE', '30900', 'Wedemark', 'Niedersachsen', 'NI', 'Region Hannover'),
('DE', '30916', 'Isernhagen', 'Niedersachsen', 'NI', 'Region Hannover'),
('DE', '30926', 'Seelze', 'Niedersachsen', 'NI', 'Region Hannover'),
('DE', '30938', 'Burgwedel', 'Niedersachsen', 'NI', 'Region Hannover'),
('DE', '30952', 'Ronnenberg', 'Niedersachsen', 'NI', 'Region Hannover'),
('DE', '30966', 'Hemmingen', 'Niedersachsen', 'NI', 'Region Hannover'),
('DE', '30974', 'Wennigsen', 'Niedersachsen', 'NI', 'Region Hannover'),
('DE', '30982', 'Pattensen', 'Niedersachsen', 'NI', 'Region Hannover'),
('DE', '30989', 'Gehrden', 'Niedersachsen', 'NI', 'Region Hannover'),
('DE', '31008', 'Elze', 'Niedersachsen', 'NI', 'Hildesheim'),
('DE', '31020', 'Salzhemmendorf', 'Niedersachsen', 'NI', 'Hameln-Pyrmont'),
('DE', '31028', 'Gronau (Leine)', 'Niedersachsen', 'NI', 'Hildesheim'),
('DE', '31029', 'Banteln', 'Niedersachsen', 'NI', 'Hildesheim'),
('DE', '31032', 'Betheln', 'Niedersachsen', 'NI', 'Hildesheim'),
('DE', '31033', 'Brüggen', 'Niedersachsen', 'NI', 'Hildesheim'),
('DE', '31035', 'Despetal', 'Niedersachsen', 'NI', 'Hildesheim'),
('DE', '31036', 'Eime', 'Niedersachsen', 'NI', 'Hildesheim'),
('DE', '31039', 'Rheden', 'Niedersachsen', 'NI', 'Hildesheim'),
('DE', '31061', 'Alfeld (Leine)', 'Niedersachsen', 'NI', 'Hildesheim'),
('DE', '31073', 'Delligsen', 'Niedersachsen', 'NI', 'Holzminden'),
('DE', '31079', 'Adenstedt', 'Niedersachsen', 'NI', 'Hildesheim'),
('DE', '31079', 'Almstedt', 'Niedersachsen', 'NI', 'Hildesheim'),
('DE', '31079', 'Sibbesse', 'Niedersachsen', 'NI', 'Hildesheim'),
('DE', '31079', 'Eberholzen', 'Niedersachsen', 'NI', 'Hildesheim'),
('DE', '31079', 'Westfeld', 'Niedersachsen', 'NI', 'Hildesheim'),
('DE', '31084', 'Freden (Leine)', 'Niedersachsen', 'NI', 'Hildesheim'),
('DE', '31085', 'Everode', 'Niedersachsen', 'NI', 'Hildesheim'),
('DE', '31087', 'Landwehr', 'Niedersachsen', 'NI', 'Hildesheim'),
('DE', '31088', 'Winzenburg', 'Niedersachsen', 'NI', 'Hildesheim'),
('DE', '31089', 'Duingen', 'Niedersachsen', 'NI', 'Hildesheim'),
('DE', '31091', 'Coppengrave', 'Niedersachsen', 'NI', 'Hildesheim'),
('DE', '31093', 'Hoyershausen', 'Niedersachsen', 'NI', 'Hildesheim'),
('DE', '31094', 'Marienhagen', 'Niedersachsen', 'NI', 'Hildesheim'),
('DE', '31096', 'Weenzen', 'Niedersachsen', 'NI', 'Hildesheim'),
('DE', '31097', 'Harbarnsen', 'Niedersachsen', 'NI', 'Hildesheim'),
('DE', '31099', 'Woltershausen', 'Niedersachsen', 'NI', 'Hildesheim'),
('DE', '31134', 'Hildesheim', 'Niedersachsen', 'NI', 'Hildesheim'),
('DE', '31135', 'Hildesheim', 'Niedersachsen', 'NI', 'Hildesheim'),
('DE', '31137', 'Hildesheim', 'Niedersachsen', 'NI', 'Hildesheim'),
('DE', '31139', 'Hildesheim', 'Niedersachsen', 'NI', 'Hildesheim'),
('DE', '31141', 'Hildesheim', 'Niedersachsen', 'NI', 'Hildesheim'),
('DE', '31157', 'Sarstedt', 'Niedersachsen', 'NI', 'Hildesheim'),
('DE', '31162', 'Bad Salzdetfurth', 'Niedersachsen', 'NI', 'Hildesheim'),
('DE', '31167', 'Bockenem', 'Niedersachsen', 'NI', 'Hildesheim'),
('DE', '31171', 'Nordstemmen', 'Niedersachsen', 'NI', 'Hildesheim'),
('DE', '31174', 'Schellerten', 'Niedersachsen', 'NI', 'Hildesheim'),
('DE', '31177', 'Harsum', 'Niedersachsen', 'NI', 'Hildesheim'),
('DE', '31180', 'Giesen', 'Niedersachsen', 'NI', 'Hildesheim'),
('DE', '31185', 'Söhlde', 'Niedersachsen', 'NI', 'Hildesheim'),
('DE', '31188', 'Holle', 'Niedersachsen', 'NI', 'Hildesheim'),
('DE', '31191', 'Algermissen', 'Niedersachsen', 'NI', 'Hildesheim'),
('DE', '31195', 'Lamspringe', 'Niedersachsen', 'NI', 'Hildesheim'),
('DE', '31195', 'Neuhof', 'Niedersachsen', 'NI', 'Hildesheim'),
('DE', '31196', 'Sehlem', 'Niedersachsen', 'NI', 'Hildesheim'),
('DE', '31199', 'Diekholzen', 'Niedersachsen', 'NI', 'Hildesheim'),
('DE', '31224', 'Peine', 'Niedersachsen', 'NI', 'Peine'),
('DE', '31226', 'Peine', 'Niedersachsen', 'NI', 'Peine'),
('DE', '31228', 'Peine', 'Niedersachsen', 'NI', 'Peine'),
('DE', '31234', 'Edemissen', 'Niedersachsen', 'NI', 'Peine'),
('DE', '31241', 'Ilsede', 'Niedersachsen', 'NI', 'Peine'),
('DE', '31246', 'Lahstedt', 'Niedersachsen', 'NI', 'Peine'),
('DE', '31249', 'Hohenhameln', 'Niedersachsen', 'NI', 'Peine'),
('DE', '31275', 'Lehrte', 'Niedersachsen', 'NI', 'Region Hannover'),
('DE', '31303', 'Burgdorf', 'Niedersachsen', 'NI', 'Region Hannover'),
('DE', '31311', 'Uetze', 'Niedersachsen', 'NI', 'Region Hannover'),
('DE', '31319', 'Sehnde', 'Niedersachsen', 'NI', 'Region Hannover'),
('DE', '31515', 'Wunstorf', 'Niedersachsen', 'NI', 'Region Hannover'),
('DE', '31535', 'Neustadt am Rübenberge', 'Niedersachsen', 'NI', 'Region Hannover'),
('DE', '31542', 'Bad Nenndorf Riepen', 'Niedersachsen', 'NI', 'Schaumburg'),
('DE', '31542', 'Bad Nenndorf Horsten', 'Niedersachsen', 'NI', 'Schaumburg'),
('DE', '31542', 'Bad Nenndorf Waltringhausen', 'Niedersachsen', 'NI', 'Schaumburg'),
('DE', '31542', 'Bad Nenndorf Bad Nenndorf', 'Niedersachsen', 'NI', 'Schaumburg'),
('DE', '31542', 'Bad Nenndorf', 'Niedersachsen', 'NI', 'Schaumburg'),
('DE', '31547', 'Rehburg-Loccum Loccum', 'Niedersachsen', 'NI', 'Nienburg (Weser)'),
('DE', '31547', 'Rehburg-Loccum Münchehagen', 'Niedersachsen', 'NI', 'Nienburg (Weser)'),
('DE', '31547', 'Rehburg-Loccum Bad Rehburg', 'Niedersachsen', 'NI', 'Nienburg (Weser)'),
('DE', '31547', 'Rehburg-Loccum Winzlar', 'Niedersachsen', 'NI', 'Nienburg (Weser)'),
('DE', '31547', 'Rehburg-Loccum Rehburg', 'Niedersachsen', 'NI', 'Nienburg (Weser)'),
('DE', '31547', 'Rehburg-Loccum', 'Niedersachsen', 'NI', 'Nienburg (Weser)'),
('DE', '31552', 'Apelern Soldorf', 'Niedersachsen', 'NI', 'Schaumburg'),
('DE', '31552', 'Apelern Apelern', 'Niedersachsen', 'NI', 'Schaumburg'),
('DE', '31552', 'Rodenberg Rodenberg', 'Niedersachsen', 'NI', 'Schaumburg'),
('DE', '31552', 'Apelern', 'Niedersachsen', 'NI', 'Schaumburg'),
('DE', '31552', 'Apelern Kleinhegesdorf', 'Niedersachsen', 'NI', 'Schaumburg'),
('DE', '31552', 'Rodenberg', 'Niedersachsen', 'NI', 'Schaumburg'),
('DE', '31552', 'Rodenberg Algesdorf', 'Niedersachsen', 'NI', 'Schaumburg'),
('DE', '31552', 'Apelern Groß Hegesdorf', 'Niedersachsen', 'NI', 'Schaumburg'),
('DE', '31552', 'Apelern Lyhren', 'Niedersachsen', 'NI', 'Schaumburg'),
('DE', '31552', 'Apelern Reinsdorf', 'Niedersachsen', 'NI', 'Schaumburg'),
('DE', '31553', 'Auhagen', 'Niedersachsen', 'NI', 'Schaumburg'),
('DE', '31553', 'Auhagen Auhagen', 'Niedersachsen', 'NI', 'Schaumburg'),
('DE', '31553', 'Sachsenhagen', 'Niedersachsen', 'NI', 'Schaumburg'),
('DE', '31553', 'Auhagen Düdinghausen', 'Niedersachsen', 'NI', 'Schaumburg'),
('DE', '31553', 'Sachsenhagen Sachsenhagen', 'Niedersachsen', 'NI', 'Schaumburg'),
('DE', '31553', 'Sachsenhagen Nienbrügge', 'Niedersachsen', 'NI', 'Schaumburg'),
('DE', '31555', 'Suthfeld', 'Niedersachsen', 'NI', 'Schaumburg'),
('DE', '31555', 'Suthfeld Kreuzriehe', 'Niedersachsen', 'NI', 'Schaumburg'),
('DE', '31555', 'Suthfeld Helsinghausen', 'Niedersachsen', 'NI', 'Schaumburg'),
('DE', '31555', 'Suthfeld Riehe', 'Niedersachsen', 'NI', 'Schaumburg'),
('DE', '31556', 'Wölpinghausen Schmalenbruch-Windhorn', 'Niedersachsen', 'NI', 'Schaumburg'),
('DE', '31556', 'Wölpinghausen Bergkirchen', 'Niedersachsen', 'NI', 'Schaumburg'),
('DE', '31556', 'Wölpinghausen Wölpinghausen', 'Niedersachsen', 'NI', 'Schaumburg'),
('DE', '31556', 'Wölpinghausen', 'Niedersachsen', 'NI', 'Schaumburg'),
('DE', '31556', 'Wölpinghausen Wiedenbrügge', 'Niedersachsen', 'NI', 'Schaumburg'),
('DE', '31558', 'Hagenburg Hagenburg', 'Niedersachsen', 'NI', 'Schaumburg'),
('DE', '31558', 'Hagenburg Altenhagen', 'Niedersachsen', 'NI', 'Schaumburg'),
('DE', '31558', 'Hagenburg', 'Niedersachsen', 'NI', 'Schaumburg'),
('DE', '31559', 'Hohnhorst Ohndorf', 'Niedersachsen', 'NI', 'Schaumburg'),
('DE', '31559', 'Hohnhorst Rehren A.R.', 'Niedersachsen', 'NI', 'Schaumburg'),
('DE', '31559', 'Hohnhorst', 'Niedersachsen', 'NI', 'Schaumburg'),
('DE', '31559', 'Haste', 'Niedersachsen', 'NI', 'Schaumburg'),
('DE', '31559', 'Hohnhorst Hohnhorst', 'Niedersachsen', 'NI', 'Schaumburg'),
('DE', '31582', 'Nienburg (Weser) Nienburg (Weser)', 'Niedersachsen', 'NI', 'Nienburg (Weser)'),
('DE', '31582', 'Nienburg (Weser) Holtorf', 'Niedersachsen', 'NI', 'Nienburg (Weser)'),
('DE', '31582', 'Nienburg (Weser)', 'Niedersachsen', 'NI', 'Nienburg (Weser)'),
('DE', '31582', 'Nienburg (Weser) Langendamm', 'Niedersachsen', 'NI', 'Nienburg (Weser)'),
('DE', '31582', 'Nienburg (Weser) Erichshagen', 'Niedersachsen', 'NI', 'Nienburg (Weser)'),
('DE', '31592', 'Stolzenau Nendorf', 'Niedersachsen', 'NI', 'Nienburg (Weser)'),
('DE', '31592', 'Stolzenau Anemolter-Schinna, Anemolter', 'Niedersachsen', 'NI', 'Nienburg (Weser)'),
('DE', '31592', 'Stolzenau Diethe', 'Niedersachsen', 'NI', 'Nienburg (Weser)'),
('DE', '31592', 'Stolzenau Anemolter-Schinna, Schinna', 'Niedersachsen', 'NI', 'Nienburg (Weser)'),
('DE', '31592', 'Stolzenau Stolzenau', 'Niedersachsen', 'NI', 'Nienburg (Weser)'),
('DE', '31592', 'Stolzenau Müsleringen', 'Niedersachsen', 'NI', 'Nienburg (Weser)'),
('DE', '31592', 'Stolzenau', 'Niedersachsen', 'NI', 'Nienburg (Weser)'),
('DE', '31592', 'Stolzenau Holzhausen', 'Niedersachsen', 'NI', 'Nienburg (Weser)'),
('DE', '31592', 'Stolzenau Hibben', 'Niedersachsen', 'NI', 'Nienburg (Weser)'),
('DE', '31592', 'Stolzenau Anemolter-Schinna', 'Niedersachsen', 'NI', 'Nienburg (Weser)'),
('DE', '31592', 'Stolzenau Frestorf', 'Niedersachsen', 'NI', 'Nienburg (Weser)'),
('DE', '31595', 'Steyerberg Düdinghausen', 'Niedersachsen', 'NI', 'Nienburg (Weser)'),
('DE', '31595', 'Steyerberg Steyerberg', 'Niedersachsen', 'NI', 'Nienburg (Weser)'),
('DE', '31595', 'Steyerberg Wellie', 'Niedersachsen', 'NI', 'Nienburg (Weser)'),
('DE', '31595', 'Steyerberg Deblinghausen', 'Niedersachsen', 'NI', 'Nienburg (Weser)'),
('DE', '31595', 'Steyerberg', 'Niedersachsen', 'NI', 'Nienburg (Weser)'),
('DE', '31595', 'Steyerberg Voigtei', 'Niedersachsen', 'NI', 'Nienburg (Weser)'),
('DE', '31600', 'Uchte', 'Niedersachsen', 'NI', 'Nienburg (Weser)'),
('DE', '31603', 'Diepenau', 'Niedersachsen', 'NI', 'Nienburg (Weser)'),
('DE', '31604', 'Raddestorf', 'Niedersachsen', 'NI', 'Nienburg (Weser)'),
('DE', '31606', 'Warmsen', 'Niedersachsen', 'NI', 'Nienburg (Weser)'),
('DE', '31608', 'Marklohe', 'Niedersachsen', 'NI', 'Nienburg (Weser)'),
('DE', '31609', 'Balge', 'Niedersachsen', 'NI', 'Nienburg (Weser)'),
('DE', '31613', 'Wietzen', 'Niedersachsen', 'NI', 'Nienburg (Weser)'),
('DE', '31618', 'Liebenau', 'Niedersachsen', 'NI', 'Nienburg (Weser)'),
('DE', '31619', 'Binnen', 'Niedersachsen', 'NI', 'Nienburg (Weser)'),
('DE', '31621', 'Pennigsehl', 'Niedersachsen', 'NI', 'Nienburg (Weser)'),
('DE', '31622', 'Heemsen Gadesbünden', 'Niedersachsen', 'NI', 'Nienburg (Weser)'),
('DE', '31622', 'Heemsen Lichtenmoor', 'Niedersachsen', 'NI', 'Nienburg (Weser)'),
('DE', '31622', 'Heemsen Anderten', 'Niedersachsen', 'NI', 'Nienburg (Weser)'),
('DE', '31622', 'Heemsen', 'Niedersachsen', 'NI', 'Nienburg (Weser)'),
('DE', '31622', 'Heemsen Heemsen', 'Niedersachsen', 'NI', 'Nienburg (Weser)'),
('DE', '31623', 'Drakenburg', 'Niedersachsen', 'NI', 'Nienburg (Weser)'),
('DE', '31626', 'Haßbergen', 'Niedersachsen', 'NI', 'Nienburg (Weser)'),
('DE', '31627', 'Rohrsen', 'Niedersachsen', 'NI', 'Nienburg (Weser)'),
('DE', '31628', 'Landesbergen', 'Niedersachsen', 'NI', 'Nienburg (Weser)'),
('DE', '31628', 'Landesbergen Brokeloh', 'Niedersachsen', 'NI', 'Nienburg (Weser)'),
('DE', '31628', 'Landesbergen Heidhausen', 'Niedersachsen', 'NI', 'Nienburg (Weser)'),
('DE', '31628', 'Landesbergen Landesbergen', 'Niedersachsen', 'NI', 'Nienburg (Weser)'),
('DE', '31628', 'Landesbergen Hahnenberg', 'Niedersachsen', 'NI', 'Nienburg (Weser)'),
('DE', '31629', 'Estorf', 'Niedersachsen', 'NI', 'Nienburg (Weser)'),
('DE', '31629', 'Estorf Estorf', 'Niedersachsen', 'NI', 'Nienburg (Weser)'),
('DE', '31629', 'Estorf Leeseringen', 'Niedersachsen', 'NI', 'Nienburg (Weser)'),
('DE', '31632', 'Husum', 'Niedersachsen', 'NI', 'Nienburg (Weser)'),
('DE', '31633', 'Leese', 'Niedersachsen', 'NI', 'Nienburg (Weser)'),
('DE', '31634', 'Steimbke Lichtenhorst', 'Niedersachsen', 'NI', 'Nienburg (Weser)'),
('DE', '31634', 'Steimbke', 'Niedersachsen', 'NI', 'Nienburg (Weser)'),
('DE', '31634', 'Steimbke Steimbke', 'Niedersachsen', 'NI', 'Nienburg (Weser)'),
('DE', '31634', 'Steimbke Wenden', 'Niedersachsen', 'NI', 'Nienburg (Weser)'),
('DE', '31634', 'Steimbke Wendenborstel', 'Niedersachsen', 'NI', 'Nienburg (Weser)'),
('DE', '31636', 'Linsburg', 'Niedersachsen', 'NI', 'Nienburg (Weser)'),
('DE', '31637', 'Rodewald', 'Niedersachsen', 'NI', 'Nienburg (Weser)'),
('DE', '31638', 'Stöckse', 'Niedersachsen', 'NI', 'Nienburg (Weser)'),
('DE', '31655', 'Stadthagen Enzen', 'Niedersachsen', 'NI', 'Schaumburg'),
('DE', '31655', 'Stadthagen Habichhorst-Blyinghausen, Blyinghausen', 'Niedersachsen', 'NI', 'Schaumburg'),
('DE', '31655', 'Stadthagen Habichhorst-Blyinghausen', 'Niedersachsen', 'NI', 'Schaumburg'),
('DE', '31655', 'Stadthagen', 'Niedersachsen', 'NI', 'Schaumburg'),
('DE', '31655', 'Stadthagen Habichhorst-Blyinghausen, Habichhorst', 'Niedersachsen', 'NI', 'Schaumburg'),
('DE', '31655', 'Stadthagen Obernwöhren', 'Niedersachsen', 'NI', 'Schaumburg'),
('DE', '31655', 'Stadthagen Hobbensen', 'Niedersachsen', 'NI', 'Schaumburg'),
('DE', '31655', 'Stadthagen Probsthagen', 'Niedersachsen', 'NI', 'Schaumburg'),
('DE', '31655', 'Stadthagen Reinsen-Remeringhausen', 'Niedersachsen', 'NI', 'Schaumburg'),
('DE', '31655', 'Stadthagen Stadthagen', 'Niedersachsen', 'NI', 'Schaumburg'),
('DE', '31655', 'Stadthagen Wendthagen-Ehlen', 'Niedersachsen', 'NI', 'Schaumburg'),
('DE', '31655', 'Stadthagen Krebshagen', 'Niedersachsen', 'NI', 'Schaumburg'),
('DE', '31655', 'Stadthagen Hörkamp-Langenbruch', 'Niedersachsen', 'NI', 'Schaumburg'),
('DE', '31675', 'Bückeburg Bergdorf', 'Niedersachsen', 'NI', 'Schaumburg'),
('DE', '31675', 'Bückeburg Meinsen', 'Niedersachsen', 'NI', 'Schaumburg'),
('DE', '31675', 'Bückeburg Warber', 'Niedersachsen', 'NI', 'Schaumburg'),
('DE', '31675', 'Bückeburg Scheie', 'Niedersachsen', 'NI', 'Schaumburg'),
('DE', '31675', 'Bückeburg Achum', 'Niedersachsen', 'NI', 'Schaumburg'),
('DE', '31675', 'Bückeburg', 'Niedersachsen', 'NI', 'Schaumburg'),
('DE', '31675', 'Bückeburg Cammer', 'Niedersachsen', 'NI', 'Schaumburg'),
('DE', '31675', 'Bückeburg Bückeburg', 'Niedersachsen', 'NI', 'Schaumburg'),
('DE', '31675', 'Bückeburg Müsingen', 'Niedersachsen', 'NI', 'Schaumburg'),
('DE', '31675', 'Bückeburg Evesen', 'Niedersachsen', 'NI', 'Schaumburg'),
('DE', '31675', 'Bückeburg Rusbend', 'Niedersachsen', 'NI', 'Schaumburg'),
('DE', '31683', 'Obernkirchen', 'Niedersachsen', 'NI', 'Schaumburg'),
('DE', '31683', 'Obernkirchen Krainhagen', 'Niedersachsen', 'NI', 'Schaumburg'),
('DE', '31683', 'Obernkirchen Vehlen', 'Niedersachsen', 'NI', 'Schaumburg'),
('DE', '31683', 'Obernkirchen Röhrkasten', 'Niedersachsen', 'NI', 'Schaumburg'),
('DE', '31683', 'Obernkirchen Gelldorf', 'Niedersachsen', 'NI', 'Schaumburg'),
('DE', '31683', 'Obernkirchen Obernkirchen', 'Niedersachsen', 'NI', 'Schaumburg'),
('DE', '31688', 'Nienstädt', 'Niedersachsen', 'NI', 'Schaumburg'),
('DE', '31688', 'Nienstädt Liekwegen', 'Niedersachsen', 'NI', 'Schaumburg'),
('DE', '31688', 'Nienstädt Nienstädt', 'Niedersachsen', 'NI', 'Schaumburg'),
('DE', '31691', 'Helpsen Helpsen', 'Niedersachsen', 'NI', 'Schaumburg'),
('DE', '31691', 'Seggebruch', 'Niedersachsen', 'NI', 'Schaumburg'),
('DE', '31691', 'Seggebruch Schierneichen-Deinsen', 'Niedersachsen', 'NI', 'Schaumburg'),
('DE', '31691', 'Helpsen Kirchhorsten', 'Niedersachsen', 'NI', 'Schaumburg'),
('DE', '31691', 'Seggebruch Seggebruch', 'Niedersachsen', 'NI', 'Schaumburg'),
('DE', '31691', 'Helpsen', 'Niedersachsen', 'NI', 'Schaumburg'),
('DE', '31691', 'Helpsen Südhorsten', 'Niedersachsen', 'NI', 'Schaumburg'),
('DE', '31691', 'Seggebruch Tallensen-Echtorf', 'Niedersachsen', 'NI', 'Schaumburg'),
('DE', '31693', 'Hespe Stemmen', 'Niedersachsen', 'NI', 'Schaumburg'),
('DE', '31693', 'Hespe Levesen', 'Niedersachsen', 'NI', 'Schaumburg'),
('DE', '31693', 'Hespe Hespe-Hiddensen', 'Niedersachsen', 'NI', 'Schaumburg'),
('DE', '31693', 'Hespe', 'Niedersachsen', 'NI', 'Schaumburg'),
('DE', '31698', 'Lindhorst Schöttlingen', 'Niedersachsen', 'NI', 'Schaumburg'),
('DE', '31698', 'Lindhorst', 'Niedersachsen', 'NI', 'Schaumburg'),
('DE', '31698', 'Lindhorst Lindhorst', 'Niedersachsen', 'NI', 'Schaumburg'),
('DE', '31698', 'Lindhorst Ottensen', 'Niedersachsen', 'NI', 'Schaumburg'),
('DE', '31699', 'Beckedorf', 'Niedersachsen', 'NI', 'Schaumburg'),
('DE', '31700', 'Heuerßen Kobbensen', 'Niedersachsen', 'NI', 'Schaumburg'),
('DE', '31700', 'Heuerßen', 'Niedersachsen', 'NI', 'Schaumburg'),
('DE', '31700', 'Heuerßen Heuerßen', 'Niedersachsen', 'NI', 'Schaumburg'),
('DE', '31702', 'Lüdersfeld', 'Niedersachsen', 'NI', 'Schaumburg'),
('DE', '31702', 'Lüdersfeld Lüdersfeld', 'Niedersachsen', 'NI', 'Schaumburg'),
('DE', '31702', 'Lüdersfeld Vornhagen', 'Niedersachsen', 'NI', 'Schaumburg'),
('DE', '31707', 'Bad Eilsen', 'Niedersachsen', 'NI', 'Schaumburg'),
('DE', '31707', 'Heeßen', 'Niedersachsen', 'NI', 'Schaumburg'),
('DE', '31708', 'Ahnsen', 'Niedersachsen', 'NI', 'Schaumburg'),
('DE', '31710', 'Buchholz', 'Niedersachsen', 'NI', 'Schaumburg'),
('DE', '31711', 'Luhden Luhden', 'Niedersachsen', 'NI', 'Schaumburg'),
('DE', '31711', 'Luhden Schermbeck', 'Niedersachsen', 'NI', 'Schaumburg'),
('DE', '31711', 'Luhden', 'Niedersachsen', 'NI', 'Schaumburg'),
('DE', '31712', 'Niedernwöhren', 'Niedersachsen', 'NI', 'Schaumburg'),
('DE', '31714', 'Lauenhagen', 'Niedersachsen', 'NI', 'Schaumburg'),
('DE', '31714', 'Lauenhagen Lauenhagen', 'Niedersachsen', 'NI', 'Schaumburg'),
('DE', '31714', 'Lauenhagen Hülshagen', 'Niedersachsen', 'NI', 'Schaumburg'),
('DE', '31715', 'Meerbeck', 'Niedersachsen', 'NI', 'Schaumburg'),
('DE', '31715', 'Meerbeck Kuckshagen', 'Niedersachsen', 'NI', 'Schaumburg'),
('DE', '31715', 'Meerbeck Meerbeck', 'Niedersachsen', 'NI', 'Schaumburg'),
('DE', '31715', 'Meerbeck Volksdorf', 'Niedersachsen', 'NI', 'Schaumburg'),
('DE', '31717', 'Nordsehl', 'Niedersachsen', 'NI', 'Schaumburg'),
('DE', '31718', 'Pollhagen', 'Niedersachsen', 'NI', 'Schaumburg'),
('DE', '31719', 'Wiedensahl', 'Niedersachsen', 'NI', 'Schaumburg'),
('DE', '31737', 'Rinteln Strücken', 'Niedersachsen', 'NI', 'Schaumburg'),
('DE', '31737', 'Rinteln', 'Niedersachsen', 'NI', 'Schaumburg'),
('DE', '31737', 'Rinteln Ahe', 'Niedersachsen', 'NI', 'Schaumburg'),
('DE', '31737', 'Rinteln Deckbergen', 'Niedersachsen', 'NI', 'Schaumburg'),
('DE', '31737', 'Rinteln Friedrichswald', 'Niedersachsen', 'NI', 'Schaumburg'),
('DE', '31737', 'Rinteln Todenmann', 'Niedersachsen', 'NI', 'Schaumburg'),
('DE', '31737', 'Rinteln Engern', 'Niedersachsen', 'NI', 'Schaumburg'),
('DE', '31737', 'Rinteln Volksen', 'Niedersachsen', 'NI', 'Schaumburg'),
('DE', '31737', 'Rinteln Uchtorf', 'Niedersachsen', 'NI', 'Schaumburg'),
('DE', '31737', 'Rinteln Exten', 'Niedersachsen', 'NI', 'Schaumburg'),
('DE', '31737', 'Rinteln Rinteln', 'Niedersachsen', 'NI', 'Schaumburg'),
('DE', '31737', 'Rinteln Hohenrode', 'Niedersachsen', 'NI', 'Schaumburg'),
('DE', '31737', 'Rinteln Goldbeck', 'Niedersachsen', 'NI', 'Schaumburg'),
('DE', '31737', 'Rinteln Kohlenstädt', 'Niedersachsen', 'NI', 'Schaumburg'),
('DE', '31737', 'Rinteln Steinbergen', 'Niedersachsen', 'NI', 'Schaumburg'),
('DE', '31737', 'Rinteln Westendorf', 'Niedersachsen', 'NI', 'Schaumburg'),
('DE', '31737', 'Rinteln Möllenbeck', 'Niedersachsen', 'NI', 'Schaumburg'),
('DE', '31737', 'Rinteln Wennenkamp', 'Niedersachsen', 'NI', 'Schaumburg'),
('DE', '31737', 'Rinteln Schaumburg', 'Niedersachsen', 'NI', 'Schaumburg'),
('DE', '31737', 'Rinteln Krankenhagen', 'Niedersachsen', 'NI', 'Schaumburg'),
('DE', '31749', 'Auetal Escher', 'Niedersachsen', 'NI', 'Schaumburg'),
('DE', '31749', 'Auetal Schoholtensen', 'Niedersachsen', 'NI', 'Schaumburg'),
('DE', '31749', 'Auetal Borstel', 'Niedersachsen', 'NI', 'Schaumburg'),
('DE', '31749', 'Auetal Rannenberg', 'Niedersachsen', 'NI', 'Schaumburg'),
('DE', '31749', 'Auetal Raden', 'Niedersachsen', 'NI', 'Schaumburg'),
('DE', '31749', 'Auetal Antendorf', 'Niedersachsen', 'NI', 'Schaumburg'),
('DE', '31749', 'Auetal Wiersen', 'Niedersachsen', 'NI', 'Schaumburg'),
('DE', '31749', 'Auetal', 'Niedersachsen', 'NI', 'Schaumburg'),
('DE', '31749', 'Auetal Hattendorf', 'Niedersachsen', 'NI', 'Schaumburg'),
('DE', '31749', 'Auetal Klein Holtensen', 'Niedersachsen', 'NI', 'Schaumburg'),
('DE', '31749', 'Auetal Bernsen', 'Niedersachsen', 'NI', 'Schaumburg'),
('DE', '31749', 'Auetal Rehren A.O.', 'Niedersachsen', 'NI', 'Schaumburg'),
('DE', '31749', 'Auetal Westerwald', 'Niedersachsen', 'NI', 'Schaumburg'),
('DE', '31749', 'Auetal Rolfshagen', 'Niedersachsen', 'NI', 'Schaumburg'),
('DE', '31749', 'Auetal Altenhagen', 'Niedersachsen', 'NI', 'Schaumburg'),
('DE', '31749', 'Auetal Poggenhagen', 'Niedersachsen', 'NI', 'Schaumburg'),
('DE', '31749', 'Auetal Kathrinhagen', 'Niedersachsen', 'NI', 'Schaumburg'),
('DE', '31785', 'Hameln', 'Niedersachsen', 'NI', 'Hameln-Pyrmont'),
('DE', '31787', 'Hameln', 'Niedersachsen', 'NI', 'Hameln-Pyrmont'),
('DE', '31789', 'Hameln', 'Niedersachsen', 'NI', 'Hameln-Pyrmont'),
('DE', '31812', 'Bad Pyrmont', 'Niedersachsen', 'NI', 'Hameln-Pyrmont'),
('DE', '31832', 'Springe', 'Niedersachsen', 'NI', 'Region Hannover'),
('DE', '31840', 'Hessisch Oldendorf', 'Niedersachsen', 'NI', 'Hameln-Pyrmont'),
('DE', '31848', 'Bad Münder am Deister', 'Niedersachsen', 'NI', 'Hameln-Pyrmont'),
('DE', '31855', 'Aerzen', 'Niedersachsen', 'NI', 'Hameln-Pyrmont'),
('DE', '31860', 'Emmerthal', 'Niedersachsen', 'NI', 'Hameln-Pyrmont'),
('DE', '31863', 'Coppenbrügge', 'Niedersachsen', 'NI', 'Hameln-Pyrmont'),
('DE', '31867', 'Hülsede Schmarrie', 'Niedersachsen', 'NI', 'Schaumburg'),
('DE', '31867', 'Hülsede', 'Niedersachsen', 'NI', 'Schaumburg'),
('DE', '31867', 'Lauenau Feggendorf', 'Niedersachsen', 'NI', 'Schaumburg'),
('DE', '31867', 'Messenkamp Altenhagen II', 'Niedersachsen', 'NI', 'Schaumburg'),
('DE', '31867', 'Hülsede Hülsede', 'Niedersachsen', 'NI', 'Schaumburg'),
('DE', '31867', 'Lauenau', 'Niedersachsen', 'NI', 'Schaumburg'),
('DE', '31867', 'Messenkamp Messenkamp', 'Niedersachsen', 'NI', 'Schaumburg'),
('DE', '31867', 'Messenkamp', 'Niedersachsen', 'NI', 'Schaumburg'),
('DE', '31867', 'Lauenau Lauenau', 'Niedersachsen', 'NI', 'Schaumburg'),
('DE', '31867', 'Hülsede Meinsen', 'Niedersachsen', 'NI', 'Schaumburg'),
('DE', '31867', 'Pohle', 'Niedersachsen', 'NI', 'Schaumburg'),
('DE', '31868', 'Ottenstein', 'Niedersachsen', 'NI', 'Holzminden'),
('DE', '34346', 'Hannoversch Münden', 'Niedersachsen', 'NI', 'Göttingen'),
('DE', '34355', 'Staufenberg', 'Niedersachsen', 'NI', 'Göttingen'),
('DE', '37073', 'Göttingen', 'Niedersachsen', 'NI', 'Göttingen'),
('DE', '37075', 'Göttingen', 'Niedersachsen', 'NI', 'Göttingen'),
('DE', '37077', 'Göttingen', 'Niedersachsen', 'NI', 'Göttingen'),
('DE', '37079', 'Göttingen', 'Niedersachsen', 'NI', 'Göttingen'),
('DE', '37081', 'Göttingen', 'Niedersachsen', 'NI', 'Göttingen'),
('DE', '37083', 'Göttingen', 'Niedersachsen', 'NI', 'Göttingen'),
('DE', '37085', 'Göttingen', 'Niedersachsen', 'NI', 'Göttingen'),
('DE', '37115', 'Duderstadt', 'Niedersachsen', 'NI', 'Göttingen'),
('DE', '37120', 'Bovenden', 'Niedersachsen', 'NI', 'Göttingen'),
('DE', '37124', 'Rosdorf', 'Niedersachsen', 'NI', 'Göttingen'),
('DE', '37127', 'Dransfeld', 'Niedersachsen', 'NI', 'Göttingen'),
('DE', '37127', 'Scheden', 'Niedersachsen', 'NI', 'Göttingen'),
('DE', '37127', 'Jühnde', 'Niedersachsen', 'NI', 'Göttingen'),
('DE', '37127', 'Bühren', 'Niedersachsen', 'NI', 'Göttingen'),
('DE', '37127', 'Niemetal', 'Niedersachsen', 'NI', 'Göttingen'),
('DE', '37130', 'Gleichen', 'Niedersachsen', 'NI', 'Göttingen'),
('DE', '37133', 'Friedland', 'Niedersachsen', 'NI', 'Göttingen'),
('DE', '37136', 'Seulingen', 'Niedersachsen', 'NI', 'Göttingen'),
('DE', '37136', 'Ebergötzen', 'Niedersachsen', 'NI', 'Göttingen'),
('DE', '37136', 'Seeburg', 'Niedersachsen', 'NI', 'Göttingen'),
('DE', '37136', 'Landolfshausen', 'Niedersachsen', 'NI', 'Göttingen'),
('DE', '37136', 'Waake', 'Niedersachsen', 'NI', 'Göttingen'),
('DE', '37139', 'Adelebsen', 'Niedersachsen', 'NI', 'Göttingen'),
('DE', '37154', 'Northeim', 'Niedersachsen', 'NI', 'Northeim'),
('DE', '37170', 'Uslar', 'Niedersachsen', 'NI', 'Northeim'),
('DE', '37176', 'Nörten-Hardenberg', 'Niedersachsen', 'NI', 'Northeim'),
('DE', '37181', 'Hardegsen', 'Niedersachsen', 'NI', 'Northeim'),
('DE', '37186', 'Moringen', 'Niedersachsen', 'NI', 'Northeim'),
('DE', '37191', 'Katlenburg-Lindau', 'Niedersachsen', 'NI', 'Northeim'),
('DE', '37194', 'Wahlsburg', 'Niedersachsen', 'NI', 'Northeim'),
('DE', '37194', 'Bodenfelde', 'Niedersachsen', 'NI', 'Northeim'),
('DE', '37197', 'Hattorf am Harz', 'Niedersachsen', 'NI', 'Osterode am Harz'),
('DE', '37199', 'Wulften', 'Niedersachsen', 'NI', 'Osterode am Harz'),
('DE', '37412', 'Hörden', 'Niedersachsen', 'NI', 'Osterode am Harz'),
('DE', '37412', 'Elbingerode', 'Niedersachsen', 'NI', 'Osterode am Harz'),
('DE', '37412', 'Herzberg am Harz', 'Niedersachsen', 'NI', 'Osterode am Harz'),
('DE', '37431', 'Bad Lauterberg im Harz', 'Niedersachsen', 'NI', 'Osterode am Harz'),
('DE', '37434', 'Bodensee', 'Niedersachsen', 'NI', 'Göttingen'),
('DE', '37434', 'Gieboldehausen', 'Niedersachsen', 'NI', 'Göttingen'),
('DE', '37434', 'Obernfeld', 'Niedersachsen', 'NI', 'Göttingen'),
('DE', '37434', 'Wollbrandshausen', 'Niedersachsen', 'NI', 'Göttingen'),
('DE', '37434', 'Krebeck', 'Niedersachsen', 'NI', 'Göttingen'),
('DE', '37434', 'Rhumspringe', 'Niedersachsen', 'NI', 'Göttingen'),
('DE', '37434', 'Rüdershausen', 'Niedersachsen', 'NI', 'Göttingen'),
('DE', '37434', 'Rollshausen', 'Niedersachsen', 'NI', 'Göttingen'),
('DE', '37434', 'Wollershausen', 'Niedersachsen', 'NI', 'Göttingen'),
('DE', '37434', 'Bilshausen', 'Niedersachsen', 'NI', 'Göttingen'),
('DE', '37441', 'Bad Sachsa', 'Niedersachsen', 'NI', 'Osterode am Harz'),
('DE', '37444', 'Sankt Andreasberg', 'Niedersachsen', 'NI', 'Goslar'),
('DE', '37445', 'Walkenried', 'Niedersachsen', 'NI', 'Osterode am Harz'),
('DE', '37447', 'Wieda', 'Niedersachsen', 'NI', 'Osterode am Harz'),
('DE', '37449', 'Zorge', 'Niedersachsen', 'NI', 'Osterode am Harz'),
('DE', '37520', 'Osterode am Harz', 'Niedersachsen', 'NI', 'Osterode am Harz'),
('DE', '37534', 'Eisdorf', 'Niedersachsen', 'NI', 'Osterode am Harz'),
('DE', '37534', 'Badenhausen', 'Niedersachsen', 'NI', 'Osterode am Harz'),
('DE', '37534', 'Gittelde', 'Niedersachsen', 'NI', 'Osterode am Harz'),
('DE', '37539', 'Bad Grund (Harz)', 'Niedersachsen', 'NI', 'Osterode am Harz'),
('DE', '37539', 'Windhausen', 'Niedersachsen', 'NI', 'Osterode am Harz'),
('DE', '37547', 'Kreiensen', 'Niedersachsen', 'NI', 'Northeim'),
('DE', '37574', 'Einbeck', 'Niedersachsen', 'NI', 'Northeim'),
('DE', '37581', 'Bad Gandersheim', 'Niedersachsen', 'NI', 'Northeim'),
('DE', '37586', 'Dassel', 'Niedersachsen', 'NI', 'Northeim'),
('DE', '37589', 'Kalefeld', 'Niedersachsen', 'NI', 'Northeim'),
('DE', '37603', 'Holzminden', 'Niedersachsen', 'NI', 'Holzminden'),
('DE', '37619', 'Bodenwerder', 'Niedersachsen', 'NI', 'Holzminden'),
('DE', '37619', 'Pegestorf', 'Niedersachsen', 'NI', 'Holzminden'),
('DE', '37619', 'Hehlen', 'Niedersachsen', 'NI', 'Holzminden'),
('DE', '37619', 'Kirchbrak', 'Niedersachsen', 'NI', 'Holzminden'),
('DE', '37619', 'Heyen', 'Niedersachsen', 'NI', 'Holzminden'),
('DE', '37620', 'Halle', 'Niedersachsen', 'NI', 'Holzminden'),
('DE', '37627', 'Heinade', 'Niedersachsen', 'NI', 'Holzminden'),
('DE', '37627', 'Stadtoldendorf', 'Niedersachsen', 'NI', 'Holzminden'),
('DE', '37627', 'Arholzen', 'Niedersachsen', 'NI', 'Holzminden'),
('DE', '37627', 'Deensen', 'Niedersachsen', 'NI', 'Holzminden'),
('DE', '37627', 'Lenne', 'Niedersachsen', 'NI', 'Holzminden'),
('DE', '37627', 'Wangelnstedt', 'Niedersachsen', 'NI', 'Holzminden'),
('DE', '37632', 'Eimen', 'Niedersachsen', 'NI', 'Holzminden'),
('DE', '37632', 'Holzen', 'Niedersachsen', 'NI', 'Holzminden'),
('DE', '37632', 'Eschershausen', 'Niedersachsen', 'NI', 'Holzminden'),
('DE', '37633', 'Dielmissen', 'Niedersachsen', 'NI', 'Holzminden'),
('DE', '37635', 'Lüerdissen', 'Niedersachsen', 'NI', 'Holzminden'),
('DE', '37639', 'Bevern', 'Niedersachsen', 'NI', 'Holzminden'),
('DE', '37640', 'Golmbach', 'Niedersachsen', 'NI', 'Holzminden'),
('DE', '37642', 'Holenberg', 'Niedersachsen', 'NI', 'Holzminden'),
('DE', '37643', 'Negenborn', 'Niedersachsen', 'NI', 'Holzminden'),
('DE', '37647', 'Brevörde', 'Niedersachsen', 'NI', 'Holzminden'),
('DE', '37647', 'Polle', 'Niedersachsen', 'NI', 'Holzminden'),
('DE', '37647', 'Vahlbruch', 'Niedersachsen', 'NI', 'Holzminden'),
('DE', '37649', 'Heinsen', 'Niedersachsen', 'NI', 'Holzminden'),
('DE', '37691', 'Derental', 'Niedersachsen', 'NI', 'Holzminden'),
('DE', '37691', 'Boffzen', 'Niedersachsen', 'NI', 'Holzminden'),
('DE', '37697', 'Lauenförde', 'Niedersachsen', 'NI', 'Holzminden'),
('DE', '37699', 'Fürstenberg', 'Niedersachsen', 'NI', 'Holzminden'),
('DE', '38023', 'Braunschweig', 'Niedersachsen', 'NI', 'Kreisfreie Stadt Braunschweig'),
('DE', '38100', 'Braunschweig', 'Niedersachsen', 'NI', 'Braunschweig, Stadt'),
('DE', '38102', 'Braunschweig', 'Niedersachsen', 'NI', 'Braunschweig, Stadt'),
('DE', '38104', 'Braunschweig', 'Niedersachsen', 'NI', 'Braunschweig, Stadt'),
('DE', '38106', 'Braunschweig', 'Niedersachsen', 'NI', 'Braunschweig, Stadt'),
('DE', '38108', 'Braunschweig', 'Niedersachsen', 'NI', 'Braunschweig, Stadt'),
('DE', '38110', 'Braunschweig', 'Niedersachsen', 'NI', 'Braunschweig, Stadt'),
('DE', '38112', 'Braunschweig', 'Niedersachsen', 'NI', 'Braunschweig, Stadt'),
('DE', '38114', 'Braunschweig', 'Niedersachsen', 'NI', 'Braunschweig, Stadt'),
('DE', '38116', 'Braunschweig', 'Niedersachsen', 'NI', 'Braunschweig, Stadt'),
('DE', '38118', 'Braunschweig', 'Niedersachsen', 'NI', 'Braunschweig, Stadt'),
('DE', '38120', 'Braunschweig', 'Niedersachsen', 'NI', 'Braunschweig, Stadt'),
('DE', '38122', 'Braunschweig', 'Niedersachsen', 'NI', 'Braunschweig, Stadt'),
('DE', '38124', 'Braunschweig', 'Niedersachsen', 'NI', 'Braunschweig, Stadt'),
('DE', '38126', 'Braunschweig', 'Niedersachsen', 'NI', 'Braunschweig, Stadt'),
('DE', '38154', 'Königslutter am Elm', 'Niedersachsen', 'NI', 'Helmstedt'),
('DE', '38159', 'Vechelde', 'Niedersachsen', 'NI', 'Peine'),
('DE', '38162', 'Cremlingen', 'Niedersachsen', 'NI', 'Wolfenbüttel'),
('DE', '38165', 'Lehre', 'Niedersachsen', 'NI', 'Helmstedt'),
('DE', '38170', 'Uehrde', 'Niedersachsen', 'NI', 'Wolfenbüttel'),
('DE', '38170', 'Winnigstedt', 'Niedersachsen', 'NI', 'Wolfenbüttel'),
('DE', '38170', 'Dahlum', 'Niedersachsen', 'NI', 'Wolfenbüttel'),
('DE', '38170', 'Schöppenstedt', 'Niedersachsen', 'NI', 'Wolfenbüttel');
INSERT INTO `plz_tabelle` (`lkz`, `plz`, `ort_stadtteil`, `bundesland`, `blkz`, `kreis`) VALUES
('DE', '38170', 'Vahlberg', 'Niedersachsen', 'NI', 'Wolfenbüttel'),
('DE', '38170', 'Kneitlingen', 'Niedersachsen', 'NI', 'Wolfenbüttel'),
('DE', '38173', 'Erkerode', 'Niedersachsen', 'NI', 'Wolfenbüttel'),
('DE', '38173', 'Veltheim', 'Niedersachsen', 'NI', 'Wolfenbüttel'),
('DE', '38173', 'Evessen', 'Niedersachsen', 'NI', 'Wolfenbüttel'),
('DE', '38173', 'Dettum', 'Niedersachsen', 'NI', 'Wolfenbüttel'),
('DE', '38173', 'Sickte', 'Niedersachsen', 'NI', 'Wolfenbüttel'),
('DE', '38176', 'Wendeburg', 'Niedersachsen', 'NI', 'Peine'),
('DE', '38179', 'Schwülper', 'Niedersachsen', 'NI', 'Gifhorn'),
('DE', '38226', 'Salzgitter Lebenstedt', 'Niedersachsen', 'NI', 'Salzgitter, Stadt'),
('DE', '38226', 'Salzgitter', 'Niedersachsen', 'NI', 'Salzgitter, Stadt'),
('DE', '38228', 'Salzgitter', 'Niedersachsen', 'NI', 'Salzgitter, Stadt'),
('DE', '38228', 'Salzgitter Bruchmachtersen', 'Niedersachsen', 'NI', 'Salzgitter, Stadt'),
('DE', '38228', 'Salzgitter Reppner', 'Niedersachsen', 'NI', 'Salzgitter, Stadt'),
('DE', '38228', 'Salzgitter Osterlinde', 'Niedersachsen', 'NI', 'Salzgitter, Stadt'),
('DE', '38228', 'Salzgitter Lebenstedt', 'Niedersachsen', 'NI', 'Salzgitter, Stadt'),
('DE', '38228', 'Salzgitter Lichtenberg', 'Niedersachsen', 'NI', 'Salzgitter, Stadt'),
('DE', '38228', 'Salzgitter Lesse', 'Niedersachsen', 'NI', 'Salzgitter, Stadt'),
('DE', '38229', 'Salzgitter Gebhardshagen', 'Niedersachsen', 'NI', 'Salzgitter, Stadt'),
('DE', '38229', 'Salzgitter Barum', 'Niedersachsen', 'NI', 'Salzgitter, Stadt'),
('DE', '38229', 'Salzgitter Hallendorf', 'Niedersachsen', 'NI', 'Salzgitter, Stadt'),
('DE', '38229', 'Salzgitter Heerte', 'Niedersachsen', 'NI', 'Salzgitter, Stadt'),
('DE', '38229', 'Salzgitter Engenrode', 'Niedersachsen', 'NI', 'Salzgitter, Stadt'),
('DE', '38229', 'Salzgitter Salder', 'Niedersachsen', 'NI', 'Salzgitter, Stadt'),
('DE', '38229', 'Salzgitter Engelnstedt', 'Niedersachsen', 'NI', 'Salzgitter, Stadt'),
('DE', '38229', 'Salzgitter Lebenstedt', 'Niedersachsen', 'NI', 'Salzgitter, Stadt'),
('DE', '38229', 'Salzgitter', 'Niedersachsen', 'NI', 'Salzgitter, Stadt'),
('DE', '38229', 'Salzgitter Calbecht', 'Niedersachsen', 'NI', 'Salzgitter, Stadt'),
('DE', '38239', 'Salzgitter Immendorf', 'Niedersachsen', 'NI', 'Salzgitter, Stadt'),
('DE', '38239', 'Salzgitter Üfingen', 'Niedersachsen', 'NI', 'Salzgitter, Stadt'),
('DE', '38239', 'Salzgitter Bleckenstedt', 'Niedersachsen', 'NI', 'Salzgitter, Stadt'),
('DE', '38239', 'Salzgitter', 'Niedersachsen', 'NI', 'Salzgitter, Stadt'),
('DE', '38239', 'Salzgitter Beddingen', 'Niedersachsen', 'NI', 'Salzgitter, Stadt'),
('DE', '38239', 'Salzgitter Drütte', 'Niedersachsen', 'NI', 'Salzgitter, Stadt'),
('DE', '38239', 'Salzgitter Watenstedt', 'Niedersachsen', 'NI', 'Salzgitter, Stadt'),
('DE', '38239', 'Salzgitter Sauingen', 'Niedersachsen', 'NI', 'Salzgitter, Stadt'),
('DE', '38239', 'Salzgitter Thiede', 'Niedersachsen', 'NI', 'Salzgitter, Stadt'),
('DE', '38259', 'Salzgitter Lobmachtersen', 'Niedersachsen', 'NI', 'Salzgitter, Stadt'),
('DE', '38259', 'Salzgitter Ringelheim', 'Niedersachsen', 'NI', 'Salzgitter, Stadt'),
('DE', '38259', 'Salzgitter Ohlendorf', 'Niedersachsen', 'NI', 'Salzgitter, Stadt'),
('DE', '38259', 'Salzgitter Beinum', 'Niedersachsen', 'NI', 'Salzgitter, Stadt'),
('DE', '38259', 'Salzgitter Flachstöckheim', 'Niedersachsen', 'NI', 'Salzgitter, Stadt'),
('DE', '38259', 'Salzgitter Groß Mahner', 'Niedersachsen', 'NI', 'Salzgitter, Stadt'),
('DE', '38259', 'Salzgitter Gitter', 'Niedersachsen', 'NI', 'Salzgitter, Stadt'),
('DE', '38259', 'Salzgitter', 'Niedersachsen', 'NI', 'Salzgitter, Stadt'),
('DE', '38259', 'Salzgitter Hohenrode', 'Niedersachsen', 'NI', 'Salzgitter, Stadt'),
('DE', '38259', 'Salzgitter Bad', 'Niedersachsen', 'NI', 'Salzgitter, Stadt'),
('DE', '38268', 'Lengede', 'Niedersachsen', 'NI', 'Peine'),
('DE', '38271', 'Baddeckenstedt', 'Niedersachsen', 'NI', 'Wolfenbüttel'),
('DE', '38272', 'Burgdorf', 'Niedersachsen', 'NI', 'Wolfenbüttel'),
('DE', '38274', 'Elbe', 'Niedersachsen', 'NI', 'Wolfenbüttel'),
('DE', '38275', 'Haverlah', 'Niedersachsen', 'NI', 'Wolfenbüttel'),
('DE', '38277', 'Heere', 'Niedersachsen', 'NI', 'Wolfenbüttel'),
('DE', '38279', 'Sehlde', 'Niedersachsen', 'NI', 'Wolfenbüttel'),
('DE', '38281', 'Wolfenbüttel', 'Niedersachsen', 'NI', 'Landkreis Wolfenbüttel'),
('DE', '38300', 'Wolfenbüttel', 'Niedersachsen', 'NI', 'Wolfenbüttel'),
('DE', '38302', 'Wolfenbüttel', 'Niedersachsen', 'NI', 'Wolfenbüttel'),
('DE', '38304', 'Wolfenbüttel', 'Niedersachsen', 'NI', 'Wolfenbüttel'),
('DE', '38312', 'Heiningen', 'Niedersachsen', 'NI', 'Wolfenbüttel'),
('DE', '38312', 'Achim', 'Niedersachsen', 'NI', 'Wolfenbüttel'),
('DE', '38312', 'Cramme', 'Niedersachsen', 'NI', 'Wolfenbüttel'),
('DE', '38312', 'Dorstadt', 'Niedersachsen', 'NI', 'Wolfenbüttel'),
('DE', '38312', 'Ohrum', 'Niedersachsen', 'NI', 'Wolfenbüttel'),
('DE', '38312', 'Flöthe', 'Niedersachsen', 'NI', 'Wolfenbüttel'),
('DE', '38312', 'Börßum', 'Niedersachsen', 'NI', 'Wolfenbüttel'),
('DE', '38315', 'Gielde', 'Niedersachsen', 'NI', 'Wolfenbüttel'),
('DE', '38315', 'Hornburg', 'Niedersachsen', 'NI', 'Wolfenbüttel'),
('DE', '38315', 'Werlaburgdorf', 'Niedersachsen', 'NI', 'Wolfenbüttel'),
('DE', '38315', 'Schladen', 'Niedersachsen', 'NI', 'Wolfenbüttel'),
('DE', '38319', 'Remlingen', 'Niedersachsen', 'NI', 'Wolfenbüttel'),
('DE', '38321', 'Denkte', 'Niedersachsen', 'NI', 'Wolfenbüttel'),
('DE', '38322', 'Hedeper', 'Niedersachsen', 'NI', 'Wolfenbüttel'),
('DE', '38324', 'Kissenbrück', 'Niedersachsen', 'NI', 'Wolfenbüttel'),
('DE', '38325', 'Roklum', 'Niedersachsen', 'NI', 'Wolfenbüttel'),
('DE', '38327', 'Semmenstedt', 'Niedersachsen', 'NI', 'Wolfenbüttel'),
('DE', '38329', 'Wittmar', 'Niedersachsen', 'NI', 'Wolfenbüttel'),
('DE', '38350', 'Helmstedt', 'Niedersachsen', 'NI', 'Helmstedt'),
('DE', '38364', 'Schöningen', 'Niedersachsen', 'NI', 'Helmstedt'),
('DE', '38368', 'Querenhorst', 'Niedersachsen', 'NI', 'Helmstedt'),
('DE', '38368', 'Rennau', 'Niedersachsen', 'NI', 'Helmstedt'),
('DE', '38368', 'Grasleben', 'Niedersachsen', 'NI', 'Helmstedt'),
('DE', '38368', 'Mariental', 'Niedersachsen', 'NI', 'Helmstedt'),
('DE', '38372', 'Büddenstedt', 'Niedersachsen', 'NI', 'Helmstedt'),
('DE', '38373', 'Süpplingen', 'Niedersachsen', 'NI', 'Helmstedt'),
('DE', '38373', 'Frellstedt', 'Niedersachsen', 'NI', 'Helmstedt'),
('DE', '38375', 'Räbke', 'Niedersachsen', 'NI', 'Helmstedt'),
('DE', '38376', 'Süpplingenburg', 'Niedersachsen', 'NI', 'Helmstedt'),
('DE', '38378', 'Warberg', 'Niedersachsen', 'NI', 'Helmstedt'),
('DE', '38379', 'Wolsdorf', 'Niedersachsen', 'NI', 'Helmstedt'),
('DE', '38381', 'Jerxheim', 'Niedersachsen', 'NI', 'Helmstedt'),
('DE', '38382', 'Beierstedt', 'Niedersachsen', 'NI', 'Helmstedt'),
('DE', '38384', 'Gevensleben', 'Niedersachsen', 'NI', 'Helmstedt'),
('DE', '38385', 'Ingeleben', 'Niedersachsen', 'NI', 'Helmstedt'),
('DE', '38387', 'Söllingen', 'Niedersachsen', 'NI', 'Helmstedt'),
('DE', '38388', 'Twieflingen', 'Niedersachsen', 'NI', 'Helmstedt'),
('DE', '38440', 'Wolfsburg', 'Niedersachsen', 'NI', 'Wolfsburg, Stadt'),
('DE', '38442', 'Wolfsburg', 'Niedersachsen', 'NI', 'Wolfsburg, Stadt'),
('DE', '38444', 'Wolfsburg', 'Niedersachsen', 'NI', 'Wolfsburg, Stadt'),
('DE', '38446', 'Wolfsburg', 'Niedersachsen', 'NI', 'Wolfsburg, Stadt'),
('DE', '38448', 'Wolfsburg', 'Niedersachsen', 'NI', 'Wolfsburg, Stadt'),
('DE', '38458', 'Velpke', 'Niedersachsen', 'NI', 'Helmstedt'),
('DE', '38459', 'Bahrdorf', 'Niedersachsen', 'NI', 'Helmstedt'),
('DE', '38461', 'Danndorf', 'Niedersachsen', 'NI', 'Helmstedt'),
('DE', '38462', 'Grafhorst', 'Niedersachsen', 'NI', 'Helmstedt'),
('DE', '38464', 'Groß Twülpstedt', 'Niedersachsen', 'NI', 'Helmstedt'),
('DE', '38465', 'Brome', 'Niedersachsen', 'NI', 'Gifhorn'),
('DE', '38467', 'Bergfeld', 'Niedersachsen', 'NI', 'Gifhorn'),
('DE', '38468', 'Ehra-Lessien', 'Niedersachsen', 'NI', 'Gifhorn'),
('DE', '38470', 'Parsau', 'Niedersachsen', 'NI', 'Gifhorn'),
('DE', '38471', 'Rühen', 'Niedersachsen', 'NI', 'Gifhorn'),
('DE', '38473', 'Tiddische', 'Niedersachsen', 'NI', 'Gifhorn'),
('DE', '38474', 'Tülau', 'Niedersachsen', 'NI', 'Gifhorn'),
('DE', '38476', 'Barwedel', 'Niedersachsen', 'NI', 'Gifhorn'),
('DE', '38477', 'Jembke', 'Niedersachsen', 'NI', 'Gifhorn'),
('DE', '38479', 'Tappenbeck', 'Niedersachsen', 'NI', 'Gifhorn'),
('DE', '38518', 'Gifhorn', 'Niedersachsen', 'NI', 'Gifhorn'),
('DE', '38524', 'Sassenburg', 'Niedersachsen', 'NI', 'Gifhorn'),
('DE', '38527', 'Meine', 'Niedersachsen', 'NI', 'Gifhorn'),
('DE', '38528', 'Adenbüttel', 'Niedersachsen', 'NI', 'Gifhorn'),
('DE', '38530', 'Didderse', 'Niedersachsen', 'NI', 'Gifhorn'),
('DE', '38531', 'Rötgesbüttel', 'Niedersachsen', 'NI', 'Gifhorn'),
('DE', '38533', 'Vordorf', 'Niedersachsen', 'NI', 'Gifhorn'),
('DE', '38536', 'Meinersen', 'Niedersachsen', 'NI', 'Gifhorn'),
('DE', '38539', 'Müden (Aller)', 'Niedersachsen', 'NI', 'Gifhorn'),
('DE', '38542', 'Leiferde', 'Niedersachsen', 'NI', 'Gifhorn'),
('DE', '38543', 'Hillerse', 'Niedersachsen', 'NI', 'Gifhorn'),
('DE', '38547', 'Calberlah', 'Niedersachsen', 'NI', 'Gifhorn'),
('DE', '38550', 'Isenbüttel', 'Niedersachsen', 'NI', 'Gifhorn'),
('DE', '38551', 'Ribbesbüttel', 'Niedersachsen', 'NI', 'Gifhorn'),
('DE', '38553', 'Wasbüttel', 'Niedersachsen', 'NI', 'Gifhorn'),
('DE', '38554', 'Weyhausen', 'Niedersachsen', 'NI', 'Gifhorn'),
('DE', '38556', 'Bokensdorf', 'Niedersachsen', 'NI', 'Gifhorn'),
('DE', '38557', 'Osloß', 'Niedersachsen', 'NI', 'Gifhorn'),
('DE', '38559', 'Wagenhoff', 'Niedersachsen', 'NI', 'Gifhorn'),
('DE', '38640', 'Goslar', 'Niedersachsen', 'NI', 'Goslar'),
('DE', '38640', 'Goslar Goslar', 'Niedersachsen', 'NI', 'Goslar'),
('DE', '38642', 'Goslar', 'Niedersachsen', 'NI', 'Goslar'),
('DE', '38642', 'Goslar Goslar', 'Niedersachsen', 'NI', 'Goslar'),
('DE', '38642', 'Goslar Oker', 'Niedersachsen', 'NI', 'Goslar'),
('DE', '38644', 'Goslar', 'Niedersachsen', 'NI', 'Goslar'),
('DE', '38644', 'Goslar Hahndorf', 'Niedersachsen', 'NI', 'Goslar'),
('DE', '38644', 'Goslar Jerstedt', 'Niedersachsen', 'NI', 'Goslar'),
('DE', '38644', 'Goslar Hahnenklee-Bockswiese', 'Niedersachsen', 'NI', 'Goslar'),
('DE', '38667', 'Bad Harzburg Eckertal', 'Niedersachsen', 'NI', 'Goslar'),
('DE', '38667', 'Bad Harzburg Bündheim/Schlewecke, Schlewecke', 'Niedersachsen', 'NI', 'Goslar'),
('DE', '38667', 'Bad Harzburg Bad Harzburg', 'Niedersachsen', 'NI', 'Goslar'),
('DE', '38667', 'Bad Harzburg Bündheim/Schlewecke, Bündheim', 'Niedersachsen', 'NI', 'Goslar'),
('DE', '38667', 'Bad Harzburg Bettingerode', 'Niedersachsen', 'NI', 'Goslar'),
('DE', '38667', 'Bad Harzburg Westerode', 'Niedersachsen', 'NI', 'Goslar'),
('DE', '38667', 'Bad Harzburg Bündheim/Schlewecke', 'Niedersachsen', 'NI', 'Goslar'),
('DE', '38667', 'Bad Harzburg Göttingerode', 'Niedersachsen', 'NI', 'Goslar'),
('DE', '38667', 'Bad Harzburg Harlingerode', 'Niedersachsen', 'NI', 'Goslar'),
('DE', '38667', 'Bad Harzburg', 'Niedersachsen', 'NI', 'Goslar'),
('DE', '38678', 'Clausthal-Zellerfeld', 'Niedersachsen', 'NI', 'Goslar'),
('DE', '38685', 'Langelsheim', 'Niedersachsen', 'NI', 'Goslar'),
('DE', '38690', 'Vienenburg', 'Niedersachsen', 'NI', 'Goslar'),
('DE', '38700', 'Braunlage', 'Niedersachsen', 'NI', 'Goslar'),
('DE', '38704', 'Liebenburg', 'Niedersachsen', 'NI', 'Goslar'),
('DE', '38707', 'Altenau', 'Niedersachsen', 'NI', 'Goslar'),
('DE', '38707', 'Schulenberg im Oberharz', 'Niedersachsen', 'NI', 'Goslar'),
('DE', '38709', 'Wildemann', 'Niedersachsen', 'NI', 'Goslar'),
('DE', '38723', 'Seesen', 'Niedersachsen', 'NI', 'Goslar'),
('DE', '38729', 'Hahausen', 'Niedersachsen', 'NI', 'Goslar'),
('DE', '38729', 'Lutter am Barenberge', 'Niedersachsen', 'NI', 'Goslar'),
('DE', '38729', 'Wallmoden', 'Niedersachsen', 'NI', 'Goslar'),
('DE', '48455', 'Bad Bentheim', 'Niedersachsen', 'NI', 'Grafschaft Bentheim'),
('DE', '48465', 'Schüttorf', 'Niedersachsen', 'NI', 'Grafschaft Bentheim'),
('DE', '48465', 'Ohne', 'Niedersachsen', 'NI', 'Grafschaft Bentheim'),
('DE', '48465', 'Engden', 'Niedersachsen', 'NI', 'Grafschaft Bentheim'),
('DE', '48465', 'Isterberg', 'Niedersachsen', 'NI', 'Grafschaft Bentheim'),
('DE', '48465', 'Suddendorf', 'Niedersachsen', 'NI', 'Grafschaft Bentheim'),
('DE', '48465', 'Samern', 'Niedersachsen', 'NI', 'Grafschaft Bentheim'),
('DE', '48465', 'Quendorf', 'Niedersachsen', 'NI', 'Grafschaft Bentheim'),
('DE', '48480', 'Lünne', 'Niedersachsen', 'NI', 'Emsland'),
('DE', '48480', 'Spelle', 'Niedersachsen', 'NI', 'Emsland'),
('DE', '48480', 'Schapen', 'Niedersachsen', 'NI', 'Emsland'),
('DE', '48488', 'Emsbüren', 'Niedersachsen', 'NI', 'Emsland'),
('DE', '48499', 'Salzbergen', 'Niedersachsen', 'NI', 'Emsland'),
('DE', '48527', 'Nordhorn', 'Niedersachsen', 'NI', 'Grafschaft Bentheim'),
('DE', '48529', 'Nordhorn', 'Niedersachsen', 'NI', 'Grafschaft Bentheim'),
('DE', '48531', 'Nordhorn', 'Niedersachsen', 'NI', 'Grafschaft Bentheim'),
('DE', '49074', 'Osnabrück', 'Niedersachsen', 'NI', 'Osnabrück, Stadt'),
('DE', '49076', 'Osnabrück', 'Niedersachsen', 'NI', 'Osnabrück, Stadt'),
('DE', '49078', 'Osnabrück', 'Niedersachsen', 'NI', 'Osnabrück, Stadt'),
('DE', '49080', 'Osnabrück', 'Niedersachsen', 'NI', 'Osnabrück, Stadt'),
('DE', '49082', 'Osnabrück', 'Niedersachsen', 'NI', 'Osnabrück, Stadt'),
('DE', '49084', 'Osnabrück', 'Niedersachsen', 'NI', 'Osnabrück, Stadt'),
('DE', '49086', 'Osnabrück', 'Niedersachsen', 'NI', 'Osnabrück, Stadt'),
('DE', '49088', 'Osnabrück', 'Niedersachsen', 'NI', 'Osnabrück, Stadt'),
('DE', '49090', 'Osnabrück', 'Niedersachsen', 'NI', 'Osnabrück, Stadt'),
('DE', '49124', 'Georgsmarienhütte', 'Niedersachsen', 'NI', 'Osnabrück'),
('DE', '49134', 'Wallenhorst', 'Niedersachsen', 'NI', 'Osnabrück'),
('DE', '49143', 'Bissendorf', 'Niedersachsen', 'NI', 'Osnabrück'),
('DE', '49152', 'Bad Essen', 'Niedersachsen', 'NI', 'Osnabrück'),
('DE', '49163', 'Bohmte', 'Niedersachsen', 'NI', 'Osnabrück'),
('DE', '49170', 'Hagen am Teutoburger Wald', 'Niedersachsen', 'NI', 'Osnabrück'),
('DE', '49176', 'Hilter am Teutoburger Wald', 'Niedersachsen', 'NI', 'Osnabrück'),
('DE', '49179', 'Ostercappeln', 'Niedersachsen', 'NI', 'Osnabrück'),
('DE', '49186', 'Bad Iburg', 'Niedersachsen', 'NI', 'Osnabrück'),
('DE', '49191', 'Belm', 'Niedersachsen', 'NI', 'Osnabrück'),
('DE', '49196', 'Bad Laer', 'Niedersachsen', 'NI', 'Osnabrück'),
('DE', '49201', 'Dissen am Teutoburger Wald', 'Niedersachsen', 'NI', 'Osnabrück'),
('DE', '49205', 'Hasbergen', 'Niedersachsen', 'NI', 'Osnabrück'),
('DE', '49214', 'Bad Rothenfelde', 'Niedersachsen', 'NI', 'Osnabrück'),
('DE', '49219', 'Glandorf', 'Niedersachsen', 'NI', 'Osnabrück'),
('DE', '49324', 'Melle', 'Niedersachsen', 'NI', 'Osnabrück'),
('DE', '49326', 'Melle', 'Niedersachsen', 'NI', 'Osnabrück'),
('DE', '49328', 'Melle', 'Niedersachsen', 'NI', 'Osnabrück'),
('DE', '49356', 'Diepholz', 'Niedersachsen', 'NI', 'Diepholz'),
('DE', '49377', 'Vechta', 'Niedersachsen', 'NI', 'Vechta'),
('DE', '49393', 'Lohne (Oldenburg)', 'Niedersachsen', 'NI', 'Vechta'),
('DE', '49401', 'Damme', 'Niedersachsen', 'NI', 'Vechta'),
('DE', '49406', 'Drentwede', 'Niedersachsen', 'NI', 'Diepholz'),
('DE', '49406', 'Barnstorf', 'Niedersachsen', 'NI', 'Diepholz'),
('DE', '49406', 'Eydelstedt', 'Niedersachsen', 'NI', 'Diepholz'),
('DE', '49413', 'Dinklage', 'Niedersachsen', 'NI', 'Vechta'),
('DE', '49419', 'Wagenfeld', 'Niedersachsen', 'NI', 'Diepholz'),
('DE', '49424', 'Goldenstedt', 'Niedersachsen', 'NI', 'Vechta'),
('DE', '49429', 'Visbek', 'Niedersachsen', 'NI', 'Vechta'),
('DE', '49434', 'Neuenkirchen-Vörden', 'Niedersachsen', 'NI', 'Vechta'),
('DE', '49439', 'Steinfeld (Oldenburg)', 'Niedersachsen', 'NI', 'Vechta'),
('DE', '49448', 'Marl', 'Niedersachsen', 'NI', 'Diepholz'),
('DE', '49448', 'Lemförde', 'Niedersachsen', 'NI', 'Diepholz'),
('DE', '49448', 'Quernheim', 'Niedersachsen', 'NI', 'Diepholz'),
('DE', '49448', 'Hüde', 'Niedersachsen', 'NI', 'Diepholz'),
('DE', '49448', 'Brockum', 'Niedersachsen', 'NI', 'Diepholz'),
('DE', '49448', 'Stemshorn', 'Niedersachsen', 'NI', 'Diepholz'),
('DE', '49451', 'Holdorf', 'Niedersachsen', 'NI', 'Vechta'),
('DE', '49453', 'Dickel', 'Niedersachsen', 'NI', 'Diepholz'),
('DE', '49453', 'Wetschen', 'Niedersachsen', 'NI', 'Diepholz'),
('DE', '49453', 'Barver', 'Niedersachsen', 'NI', 'Diepholz'),
('DE', '49453', 'Rehden', 'Niedersachsen', 'NI', 'Diepholz'),
('DE', '49453', 'Hemsloh', 'Niedersachsen', 'NI', 'Diepholz'),
('DE', '49456', 'Bakum', 'Niedersachsen', 'NI', 'Vechta'),
('DE', '49457', 'Drebber', 'Niedersachsen', 'NI', 'Diepholz'),
('DE', '49459', 'Lembruch', 'Niedersachsen', 'NI', 'Diepholz'),
('DE', '49565', 'Bramsche', 'Niedersachsen', 'NI', 'Osnabrück'),
('DE', '49577', 'Eggermühlen', 'Niedersachsen', 'NI', 'Osnabrück'),
('DE', '49577', 'Kettenkamp', 'Niedersachsen', 'NI', 'Osnabrück'),
('DE', '49577', 'Ankum', 'Niedersachsen', 'NI', 'Osnabrück'),
('DE', '49584', 'Fürstenau', 'Niedersachsen', 'NI', 'Osnabrück'),
('DE', '49586', 'Merzen', 'Niedersachsen', 'NI', 'Osnabrück'),
('DE', '49586', 'Neuenkirchen', 'Niedersachsen', 'NI', 'Osnabrück'),
('DE', '49593', 'Bersenbrück', 'Niedersachsen', 'NI', 'Osnabrück'),
('DE', '49594', 'Alfhausen', 'Niedersachsen', 'NI', 'Osnabrück'),
('DE', '49596', 'Gehrde', 'Niedersachsen', 'NI', 'Osnabrück'),
('DE', '49597', 'Rieste', 'Niedersachsen', 'NI', 'Osnabrück'),
('DE', '49599', 'Voltlage', 'Niedersachsen', 'NI', 'Osnabrück'),
('DE', '49610', 'Quakenbrück', 'Niedersachsen', 'NI', 'Osnabrück'),
('DE', '49624', 'Löningen', 'Niedersachsen', 'NI', 'Cloppenburg'),
('DE', '49626', 'Bippen', 'Niedersachsen', 'NI', 'Osnabrück'),
('DE', '49626', 'Berge', 'Niedersachsen', 'NI', 'Osnabrück'),
('DE', '49632', 'Essen (Oldenburg)', 'Niedersachsen', 'NI', 'Cloppenburg'),
('DE', '49635', 'Badbergen', 'Niedersachsen', 'NI', 'Osnabrück'),
('DE', '49637', 'Menslage', 'Niedersachsen', 'NI', 'Osnabrück'),
('DE', '49638', 'Nortrup', 'Niedersachsen', 'NI', 'Osnabrück'),
('DE', '49661', 'Cloppenburg', 'Niedersachsen', 'NI', 'Cloppenburg'),
('DE', '49681', 'Garrel', 'Niedersachsen', 'NI', 'Cloppenburg'),
('DE', '49685', 'Emstek', 'Niedersachsen', 'NI', 'Cloppenburg'),
('DE', '49688', 'Lastrup', 'Niedersachsen', 'NI', 'Cloppenburg'),
('DE', '49692', 'Cappeln (Oldenburg)', 'Niedersachsen', 'NI', 'Cloppenburg'),
('DE', '49696', 'Molbergen', 'Niedersachsen', 'NI', 'Cloppenburg'),
('DE', '49699', 'Lindern (Oldenburg)', 'Niedersachsen', 'NI', 'Cloppenburg'),
('DE', '49716', 'Meppen', 'Niedersachsen', 'NI', 'Emsland'),
('DE', '49733', 'Haren', 'Niedersachsen', 'NI', 'Emsland'),
('DE', '49740', 'Haselünne', 'Niedersachsen', 'NI', 'Emsland'),
('DE', '49744', 'Geeste', 'Niedersachsen', 'NI', 'Emsland'),
('DE', '49751', 'Spahnharrenstätte', 'Niedersachsen', 'NI', 'Emsland'),
('DE', '49751', 'Hüven', 'Niedersachsen', 'NI', 'Emsland'),
('DE', '49751', 'Werpeloh', 'Niedersachsen', 'NI', 'Emsland'),
('DE', '49751', 'Sögel', 'Niedersachsen', 'NI', 'Emsland'),
('DE', '49757', 'Werlte', 'Niedersachsen', 'NI', 'Emsland'),
('DE', '49757', 'Lahn', 'Niedersachsen', 'NI', 'Emsland'),
('DE', '49757', 'Vrees', 'Niedersachsen', 'NI', 'Emsland'),
('DE', '49762', 'Fresenburg', 'Niedersachsen', 'NI', 'Emsland'),
('DE', '49762', 'Lathen', 'Niedersachsen', 'NI', 'Emsland'),
('DE', '49762', 'Sustrum', 'Niedersachsen', 'NI', 'Emsland'),
('DE', '49762', 'Renkenberge', 'Niedersachsen', 'NI', 'Emsland'),
('DE', '49767', 'Twist', 'Niedersachsen', 'NI', 'Emsland'),
('DE', '49770', 'Herzlake', 'Niedersachsen', 'NI', 'Emsland'),
('DE', '49770', 'Dohren', 'Niedersachsen', 'NI', 'Emsland'),
('DE', '49774', 'Lähden', 'Niedersachsen', 'NI', 'Emsland'),
('DE', '49777', 'Stavern', 'Niedersachsen', 'NI', 'Emsland'),
('DE', '49777', 'Klein Berßen', 'Niedersachsen', 'NI', 'Emsland'),
('DE', '49777', 'Groß Berßen', 'Niedersachsen', 'NI', 'Emsland'),
('DE', '49779', 'Oberlangen', 'Niedersachsen', 'NI', 'Emsland'),
('DE', '49779', 'Niederlangen', 'Niedersachsen', 'NI', 'Emsland'),
('DE', '49808', 'Lingen', 'Niedersachsen', 'NI', 'Emsland'),
('DE', '49809', 'Lingen', 'Niedersachsen', 'NI', 'Emsland'),
('DE', '49811', 'Lingen', 'Niedersachsen', 'NI', 'Emsland'),
('DE', '49824', 'Ringe', 'Niedersachsen', 'NI', 'Grafschaft Bentheim'),
('DE', '49824', 'Laar', 'Niedersachsen', 'NI', 'Grafschaft Bentheim'),
('DE', '49824', 'Emlichheim', 'Niedersachsen', 'NI', 'Grafschaft Bentheim'),
('DE', '49828', 'Osterwald', 'Niedersachsen', 'NI', 'Grafschaft Bentheim'),
('DE', '49828', 'Lage', 'Niedersachsen', 'NI', 'Grafschaft Bentheim'),
('DE', '49828', 'Neuenhaus', 'Niedersachsen', 'NI', 'Grafschaft Bentheim'),
('DE', '49828', 'Georgsdorf', 'Niedersachsen', 'NI', 'Grafschaft Bentheim'),
('DE', '49828', 'Esche', 'Niedersachsen', 'NI', 'Grafschaft Bentheim'),
('DE', '49832', 'Andervenne', 'Niedersachsen', 'NI', 'Emsland'),
('DE', '49832', 'Messingen', 'Niedersachsen', 'NI', 'Emsland'),
('DE', '49832', 'Freren', 'Niedersachsen', 'NI', 'Emsland'),
('DE', '49832', 'Beesten', 'Niedersachsen', 'NI', 'Emsland'),
('DE', '49832', 'Thuine', 'Niedersachsen', 'NI', 'Emsland'),
('DE', '49835', 'Wietmarschen', 'Niedersachsen', 'NI', 'Grafschaft Bentheim'),
('DE', '49838', 'Wettrup', 'Niedersachsen', 'NI', 'Emsland'),
('DE', '49838', 'Gersten', 'Niedersachsen', 'NI', 'Emsland'),
('DE', '49838', 'Langen', 'Niedersachsen', 'NI', 'Emsland'),
('DE', '49838', 'Lengerich', 'Niedersachsen', 'NI', 'Emsland'),
('DE', '49838', 'Handrup', 'Niedersachsen', 'NI', 'Emsland'),
('DE', '49843', 'Halle', 'Niedersachsen', 'NI', 'Grafschaft Bentheim'),
('DE', '49843', 'Getelo', 'Niedersachsen', 'NI', 'Grafschaft Bentheim'),
('DE', '49843', 'Wielen', 'Niedersachsen', 'NI', 'Grafschaft Bentheim'),
('DE', '49843', 'Gölenkamp', 'Niedersachsen', 'NI', 'Grafschaft Bentheim'),
('DE', '49843', 'Uelsen', 'Niedersachsen', 'NI', 'Grafschaft Bentheim'),
('DE', '49844', 'Bawinkel', 'Niedersachsen', 'NI', 'Emsland'),
('DE', '49846', 'Hoogstede', 'Niedersachsen', 'NI', 'Grafschaft Bentheim'),
('DE', '49847', 'Itterbeck', 'Niedersachsen', 'NI', 'Grafschaft Bentheim'),
('DE', '49849', 'Wilsum', 'Niedersachsen', 'NI', 'Grafschaft Bentheim'),
('DE', '21039', 'Börnsen', 'Schleswig-Holstein', 'SH', 'Herzogtum Lauenburg'),
('DE', '21039', 'Escheburg', 'Schleswig-Holstein', 'SH', 'Herzogtum Lauenburg'),
('DE', '21039', 'Hamburg Curslack', 'Schleswig-Holstein', 'SH', 'Herzogtum Lauenburg'),
('DE', '21039', 'Hamburg', 'Schleswig-Holstein', 'SH', 'Herzogtum Lauenburg'),
('DE', '21039', 'Hamburg Bergedorf', 'Schleswig-Holstein', 'SH', 'Herzogtum Lauenburg'),
('DE', '21039', 'Hamburg Neuengamme', 'Schleswig-Holstein', 'SH', 'Herzogtum Lauenburg'),
('DE', '21039', 'Hamburg Altengamme', 'Schleswig-Holstein', 'SH', 'Herzogtum Lauenburg'),
('DE', '21465', 'Wentorf bei Hamburg', 'Schleswig-Holstein', 'SH', 'Stormarn'),
('DE', '21465', 'Reinbek', 'Schleswig-Holstein', 'SH', 'Stormarn'),
('DE', '21481', 'Lauenburg/Elbe', 'Schleswig-Holstein', 'SH', 'Herzogtum Lauenburg'),
('DE', '21481', 'Schnakenbek', 'Schleswig-Holstein', 'SH', 'Herzogtum Lauenburg'),
('DE', '21481', 'Buchhorst', 'Schleswig-Holstein', 'SH', 'Herzogtum Lauenburg'),
('DE', '21483', 'Wangelau', 'Schleswig-Holstein', 'SH', 'Herzogtum Lauenburg'),
('DE', '21483', 'Lanze', 'Schleswig-Holstein', 'SH', 'Herzogtum Lauenburg'),
('DE', '21483', 'Krukow', 'Schleswig-Holstein', 'SH', 'Herzogtum Lauenburg'),
('DE', '21483', 'Lütau', 'Schleswig-Holstein', 'SH', 'Herzogtum Lauenburg'),
('DE', '21483', 'Juliusburg', 'Schleswig-Holstein', 'SH', 'Herzogtum Lauenburg'),
('DE', '21483', 'Gülzow', 'Schleswig-Holstein', 'SH', 'Herzogtum Lauenburg'),
('DE', '21483', 'Krüzen', 'Schleswig-Holstein', 'SH', 'Herzogtum Lauenburg'),
('DE', '21483', 'Basedow', 'Schleswig-Holstein', 'SH', 'Herzogtum Lauenburg'),
('DE', '21483', 'Dalldorf', 'Schleswig-Holstein', 'SH', 'Herzogtum Lauenburg'),
('DE', '21493', 'Elmenhorst', 'Schleswig-Holstein', 'SH', 'Herzogtum Lauenburg'),
('DE', '21493', 'Fuhlenhagen', 'Schleswig-Holstein', 'SH', 'Herzogtum Lauenburg'),
('DE', '21493', 'Schwarzenbek', 'Schleswig-Holstein', 'SH', 'Herzogtum Lauenburg'),
('DE', '21493', 'Schretstaken', 'Schleswig-Holstein', 'SH', 'Herzogtum Lauenburg'),
('DE', '21493', 'Möhnsen', 'Schleswig-Holstein', 'SH', 'Herzogtum Lauenburg'),
('DE', '21493', 'Havekost', 'Schleswig-Holstein', 'SH', 'Herzogtum Lauenburg'),
('DE', '21493', 'Sahms', 'Schleswig-Holstein', 'SH', 'Herzogtum Lauenburg'),
('DE', '21493', 'Grabau', 'Schleswig-Holstein', 'SH', 'Herzogtum Lauenburg'),
('DE', '21493', 'Basthorst', 'Schleswig-Holstein', 'SH', 'Herzogtum Lauenburg'),
('DE', '21493', 'Groß Pampau', 'Schleswig-Holstein', 'SH', 'Herzogtum Lauenburg'),
('DE', '21493', 'Grove', 'Schleswig-Holstein', 'SH', 'Herzogtum Lauenburg'),
('DE', '21493', 'Mühlenrade', 'Schleswig-Holstein', 'SH', 'Herzogtum Lauenburg'),
('DE', '21493', 'Talkau', 'Schleswig-Holstein', 'SH', 'Herzogtum Lauenburg'),
('DE', '21502', 'Worth', 'Schleswig-Holstein', 'SH', 'Herzogtum Lauenburg'),
('DE', '21502', 'Geesthacht', 'Schleswig-Holstein', 'SH', 'Herzogtum Lauenburg'),
('DE', '21502', 'Wiershop', 'Schleswig-Holstein', 'SH', 'Herzogtum Lauenburg'),
('DE', '21502', 'Hamwarde', 'Schleswig-Holstein', 'SH', 'Herzogtum Lauenburg'),
('DE', '21509', 'Glinde', 'Schleswig-Holstein', 'SH', 'Stormarn'),
('DE', '21514', 'Roseburg', 'Schleswig-Holstein', 'SH', 'Herzogtum Lauenburg'),
('DE', '21514', 'Siebeneichen', 'Schleswig-Holstein', 'SH', 'Herzogtum Lauenburg'),
('DE', '21514', 'Klein Pampau', 'Schleswig-Holstein', 'SH', 'Herzogtum Lauenburg'),
('DE', '21514', 'Kankelau', 'Schleswig-Holstein', 'SH', 'Herzogtum Lauenburg'),
('DE', '21514', 'Hornbek', 'Schleswig-Holstein', 'SH', 'Herzogtum Lauenburg'),
('DE', '21514', 'Büchen', 'Schleswig-Holstein', 'SH', 'Herzogtum Lauenburg'),
('DE', '21514', 'Fitzen', 'Schleswig-Holstein', 'SH', 'Herzogtum Lauenburg'),
('DE', '21514', 'Göttin', 'Schleswig-Holstein', 'SH', 'Herzogtum Lauenburg'),
('DE', '21514', 'Langenlehsten', 'Schleswig-Holstein', 'SH', 'Herzogtum Lauenburg'),
('DE', '21514', 'Witzeeze', 'Schleswig-Holstein', 'SH', 'Herzogtum Lauenburg'),
('DE', '21514', 'Güster', 'Schleswig-Holstein', 'SH', 'Herzogtum Lauenburg'),
('DE', '21514', 'Bröthen', 'Schleswig-Holstein', 'SH', 'Herzogtum Lauenburg'),
('DE', '21516', 'Woltersdorf', 'Schleswig-Holstein', 'SH', 'Herzogtum Lauenburg'),
('DE', '21516', 'Müssen', 'Schleswig-Holstein', 'SH', 'Herzogtum Lauenburg'),
('DE', '21516', 'Schulendorf', 'Schleswig-Holstein', 'SH', 'Herzogtum Lauenburg'),
('DE', '21516', 'Tramm', 'Schleswig-Holstein', 'SH', 'Herzogtum Lauenburg'),
('DE', '21521', 'Wohltorf', 'Schleswig-Holstein', 'SH', 'Herzogtum Lauenburg'),
('DE', '21521', 'Aumühle', 'Schleswig-Holstein', 'SH', 'Herzogtum Lauenburg'),
('DE', '21521', 'Dassendorf', 'Schleswig-Holstein', 'SH', 'Herzogtum Lauenburg'),
('DE', '21524', 'Brunstorf', 'Schleswig-Holstein', 'SH', 'Herzogtum Lauenburg'),
('DE', '21526', 'Hohenhorn', 'Schleswig-Holstein', 'SH', 'Herzogtum Lauenburg'),
('DE', '21527', 'Kollow', 'Schleswig-Holstein', 'SH', 'Herzogtum Lauenburg'),
('DE', '21529', 'Kröppelshagen-Fahrendorf', 'Schleswig-Holstein', 'SH', 'Herzogtum Lauenburg'),
('DE', '22113', 'Hamburg', 'Schleswig-Holstein', 'SH', 'Stormarn'),
('DE', '22113', 'Hamburg Moorfleet', 'Schleswig-Holstein', 'SH', 'Stormarn'),
('DE', '22113', 'Oststeinbek', 'Schleswig-Holstein', 'SH', 'Stormarn'),
('DE', '22113', 'Hamburg Lohbrügge', 'Schleswig-Holstein', 'SH', 'Stormarn'),
('DE', '22113', 'Hamburg Billwerder', 'Schleswig-Holstein', 'SH', 'Stormarn'),
('DE', '22113', 'Hamburg Allermöhe', 'Schleswig-Holstein', 'SH', 'Stormarn'),
('DE', '22113', 'Hamburg Billstedt', 'Schleswig-Holstein', 'SH', 'Stormarn'),
('DE', '22113', 'Hamburg Billbrook', 'Schleswig-Holstein', 'SH', 'Stormarn'),
('DE', '22113', 'Hamburg Horn', 'Schleswig-Holstein', 'SH', 'Stormarn'),
('DE', '22145', 'Hamburg Farmsen-Berne', 'Schleswig-Holstein', 'SH', 'Stormarn'),
('DE', '22145', 'Braak', 'Schleswig-Holstein', 'SH', 'Stormarn'),
('DE', '22145', 'Stapelfeld', 'Schleswig-Holstein', 'SH', 'Stormarn'),
('DE', '22145', 'Hamburg', 'Schleswig-Holstein', 'SH', 'Stormarn'),
('DE', '22145', 'Hamburg Rahlstedt', 'Schleswig-Holstein', 'SH', 'Stormarn'),
('DE', '22844', 'Norderstedt', 'Schleswig-Holstein', 'SH', 'Segeberg'),
('DE', '22846', 'Norderstedt', 'Schleswig-Holstein', 'SH', 'Segeberg'),
('DE', '22848', 'Norderstedt', 'Schleswig-Holstein', 'SH', 'Segeberg'),
('DE', '22850', 'Norderstedt', 'Schleswig-Holstein', 'SH', 'Segeberg'),
('DE', '22851', 'Norderstedt', 'Schleswig-Holstein', 'SH', 'Segeberg'),
('DE', '22869', 'Schenefeld', 'Schleswig-Holstein', 'SH', 'Pinneberg'),
('DE', '22880', 'Wedel', 'Schleswig-Holstein', 'SH', 'Pinneberg'),
('DE', '22885', 'Barsbüttel', 'Schleswig-Holstein', 'SH', 'Stormarn'),
('DE', '22889', 'Tangstedt', 'Schleswig-Holstein', 'SH', 'Stormarn'),
('DE', '22926', 'Ahrensburg', 'Schleswig-Holstein', 'SH', 'Stormarn'),
('DE', '22927', 'Großhansdorf', 'Schleswig-Holstein', 'SH', 'Stormarn'),
('DE', '22929', 'Rausdorf', 'Schleswig-Holstein', 'SH', 'Stormarn'),
('DE', '22929', 'Köthel', 'Schleswig-Holstein', 'SH', 'Stormarn'),
('DE', '22929', 'Hamfelde in Lauenburg', 'Schleswig-Holstein', 'SH', 'Stormarn'),
('DE', '22929', 'Köthel', 'Schleswig-Holstein', 'SH', 'Stormarn'),
('DE', '22929', 'Kasseburg', 'Schleswig-Holstein', 'SH', 'Stormarn'),
('DE', '22929', 'Schönberg', 'Schleswig-Holstein', 'SH', 'Stormarn'),
('DE', '22929', 'Hammoor', 'Schleswig-Holstein', 'SH', 'Stormarn'),
('DE', '22929', 'Hamfelde in Holstein', 'Schleswig-Holstein', 'SH', 'Stormarn'),
('DE', '22929', 'Delingsdorf', 'Schleswig-Holstein', 'SH', 'Stormarn'),
('DE', '22941', 'Bargteheide', 'Schleswig-Holstein', 'SH', 'Stormarn'),
('DE', '22941', 'Jersbek', 'Schleswig-Holstein', 'SH', 'Stormarn'),
('DE', '22946', 'Großensee', 'Schleswig-Holstein', 'SH', 'Stormarn'),
('DE', '22946', 'Grande', 'Schleswig-Holstein', 'SH', 'Stormarn'),
('DE', '22946', 'Dahmker', 'Schleswig-Holstein', 'SH', 'Stormarn'),
('DE', '22946', 'Trittau', 'Schleswig-Holstein', 'SH', 'Stormarn'),
('DE', '22946', 'Hohenfelde', 'Schleswig-Holstein', 'SH', 'Stormarn'),
('DE', '22946', 'Brunsbek', 'Schleswig-Holstein', 'SH', 'Stormarn'),
('DE', '22949', 'Ammersbek', 'Schleswig-Holstein', 'SH', 'Stormarn'),
('DE', '22952', 'Lütjensee', 'Schleswig-Holstein', 'SH', 'Stormarn'),
('DE', '22955', 'Hoisdorf', 'Schleswig-Holstein', 'SH', 'Stormarn'),
('DE', '22956', 'Grönwohld', 'Schleswig-Holstein', 'SH', 'Stormarn'),
('DE', '22958', 'Kuddewörde', 'Schleswig-Holstein', 'SH', 'Herzogtum Lauenburg'),
('DE', '22959', 'Linau', 'Schleswig-Holstein', 'SH', 'Herzogtum Lauenburg'),
('DE', '22962', 'Siek', 'Schleswig-Holstein', 'SH', 'Stormarn'),
('DE', '22964', 'Steinburg', 'Schleswig-Holstein', 'SH', 'Stormarn'),
('DE', '22965', 'Todendorf', 'Schleswig-Holstein', 'SH', 'Stormarn'),
('DE', '22967', 'Tremsbüttel', 'Schleswig-Holstein', 'SH', 'Stormarn'),
('DE', '22969', 'Witzhave', 'Schleswig-Holstein', 'SH', 'Stormarn'),
('DE', '23539', 'Lübeck', 'Schleswig-Holstein', 'SH', 'Lübeck, Hansestadt'),
('DE', '23552', 'Lübeck', 'Schleswig-Holstein', 'SH', 'Lübeck, Hansestadt'),
('DE', '23554', 'Lübeck', 'Schleswig-Holstein', 'SH', 'Lübeck, Hansestadt'),
('DE', '23556', 'Lübeck', 'Schleswig-Holstein', 'SH', 'Lübeck, Hansestadt'),
('DE', '23558', 'Lübeck', 'Schleswig-Holstein', 'SH', 'Lübeck, Hansestadt'),
('DE', '23560', 'Lübeck', 'Schleswig-Holstein', 'SH', 'Lübeck, Hansestadt'),
('DE', '23562', 'Lübeck', 'Schleswig-Holstein', 'SH', 'Lübeck, Hansestadt'),
('DE', '23564', 'Lübeck', 'Schleswig-Holstein', 'SH', 'Lübeck, Hansestadt'),
('DE', '23566', 'Lübeck', 'Schleswig-Holstein', 'SH', 'Lübeck, Hansestadt'),
('DE', '23568', 'Lübeck', 'Schleswig-Holstein', 'SH', 'Lübeck, Hansestadt'),
('DE', '23569', 'Lübeck', 'Schleswig-Holstein', 'SH', 'Lübeck, Hansestadt'),
('DE', '23570', 'Lübeck', 'Schleswig-Holstein', 'SH', 'Lübeck, Hansestadt'),
('DE', '23611', 'Bad Schwartau', 'Schleswig-Holstein', 'SH', 'Ostholstein'),
('DE', '23617', 'Stockelsdorf', 'Schleswig-Holstein', 'SH', 'Ostholstein'),
('DE', '23619', 'Zarpen', 'Schleswig-Holstein', 'SH', 'Stormarn'),
('DE', '23619', 'Hamberge', 'Schleswig-Holstein', 'SH', 'Stormarn'),
('DE', '23619', 'Mönkhagen', 'Schleswig-Holstein', 'SH', 'Stormarn'),
('DE', '23619', 'Heilshoop', 'Schleswig-Holstein', 'SH', 'Stormarn'),
('DE', '23619', 'Rehhorst', 'Schleswig-Holstein', 'SH', 'Stormarn'),
('DE', '23619', 'Badendorf', 'Schleswig-Holstein', 'SH', 'Stormarn'),
('DE', '23623', 'Ahrensbök', 'Schleswig-Holstein', 'SH', 'Ostholstein'),
('DE', '23626', 'Ratekau', 'Schleswig-Holstein', 'SH', 'Ostholstein'),
('DE', '23627', 'Groß Sarau', 'Schleswig-Holstein', 'SH', 'Herzogtum Lauenburg'),
('DE', '23627', 'Groß Grönau', 'Schleswig-Holstein', 'SH', 'Herzogtum Lauenburg'),
('DE', '23628', 'Krummesse', 'Schleswig-Holstein', 'SH', 'Herzogtum Lauenburg'),
('DE', '23628', 'Klempau', 'Schleswig-Holstein', 'SH', 'Herzogtum Lauenburg'),
('DE', '23629', 'Sarkwitz', 'Schleswig-Holstein', 'SH', 'Ostholstein'),
('DE', '23669', 'Timmendorfer Strand', 'Schleswig-Holstein', 'SH', 'Ostholstein'),
('DE', '23683', 'Scharbeutz', 'Schleswig-Holstein', 'SH', 'Ostholstein'),
('DE', '23684', 'Scharbeutz', 'Schleswig-Holstein', 'SH', 'Ostholstein'),
('DE', '23689', 'Pansdorf, Holstein', 'Schleswig-Holstein', 'SH', 'Ostholstein'),
('DE', '23701', 'Eutin', 'Schleswig-Holstein', 'SH', 'Ostholstein'),
('DE', '23701', 'Süsel', 'Schleswig-Holstein', 'SH', 'Ostholstein'),
('DE', '23714', 'Kirchnüchel', 'Schleswig-Holstein', 'SH', 'Plön'),
('DE', '23714', 'Malente', 'Schleswig-Holstein', 'SH', 'Plön'),
('DE', '23715', 'Bosau', 'Schleswig-Holstein', 'SH', 'Ostholstein'),
('DE', '23717', 'Kasseedorf', 'Schleswig-Holstein', 'SH', 'Ostholstein'),
('DE', '23719', 'Glasau', 'Schleswig-Holstein', 'SH', 'Segeberg'),
('DE', '23730', 'Neustadt in Holstein', 'Schleswig-Holstein', 'SH', 'Ostholstein'),
('DE', '23730', 'Sierksdorf', 'Schleswig-Holstein', 'SH', 'Ostholstein'),
('DE', '23730', 'Altenkrempe', 'Schleswig-Holstein', 'SH', 'Ostholstein'),
('DE', '23730', 'Schashagen', 'Schleswig-Holstein', 'SH', 'Ostholstein'),
('DE', '23738', 'Manhagen', 'Schleswig-Holstein', 'SH', 'Ostholstein'),
('DE', '23738', 'Harmsdorf', 'Schleswig-Holstein', 'SH', 'Ostholstein'),
('DE', '23738', 'Damlos', 'Schleswig-Holstein', 'SH', 'Ostholstein'),
('DE', '23738', 'Riepsdorf', 'Schleswig-Holstein', 'SH', 'Ostholstein'),
('DE', '23738', 'Kabelhorst', 'Schleswig-Holstein', 'SH', 'Ostholstein'),
('DE', '23738', 'Beschendorf', 'Schleswig-Holstein', 'SH', 'Ostholstein'),
('DE', '23738', 'Lensahn', 'Schleswig-Holstein', 'SH', 'Ostholstein'),
('DE', '23743', 'Grömitz', 'Schleswig-Holstein', 'SH', 'Ostholstein'),
('DE', '23744', 'Schönwalde am Bungsberg', 'Schleswig-Holstein', 'SH', 'Ostholstein'),
('DE', '23746', 'Kellenhusen', 'Schleswig-Holstein', 'SH', 'Ostholstein'),
('DE', '23747', 'Dahme', 'Schleswig-Holstein', 'SH', 'Ostholstein'),
('DE', '23749', 'Grube', 'Schleswig-Holstein', 'SH', 'Ostholstein'),
('DE', '23758', 'Wangels', 'Schleswig-Holstein', 'SH', 'Ostholstein'),
('DE', '23758', 'Göhl', 'Schleswig-Holstein', 'SH', 'Ostholstein'),
('DE', '23758', 'Gremersdorf', 'Schleswig-Holstein', 'SH', 'Ostholstein'),
('DE', '23758', 'Oldenburg in Holstein', 'Schleswig-Holstein', 'SH', 'Ostholstein'),
('DE', '23769', 'Fehmarn', 'Schleswig-Holstein', 'SH', 'Ostholstein'),
('DE', '23774', 'Heiligenhafen', 'Schleswig-Holstein', 'SH', 'Ostholstein'),
('DE', '23775', 'Großenbrode', 'Schleswig-Holstein', 'SH', 'Ostholstein'),
('DE', '23777', 'Heringsdorf', 'Schleswig-Holstein', 'SH', 'Ostholstein'),
('DE', '23779', 'Neukirchen', 'Schleswig-Holstein', 'SH', 'Ostholstein'),
('DE', '23795', 'Weede', 'Schleswig-Holstein', 'SH', 'Segeberg'),
('DE', '23795', 'Schackendorf', 'Schleswig-Holstein', 'SH', 'Segeberg'),
('DE', '23795', 'Mözen', 'Schleswig-Holstein', 'SH', 'Segeberg'),
('DE', '23795', 'Traventhal', 'Schleswig-Holstein', 'SH', 'Segeberg'),
('DE', '23795', 'Stipsdorf', 'Schleswig-Holstein', 'SH', 'Segeberg'),
('DE', '23795', 'Klein Rönnau', 'Schleswig-Holstein', 'SH', 'Segeberg'),
('DE', '23795', 'Klein Gladebrügge', 'Schleswig-Holstein', 'SH', 'Segeberg'),
('DE', '23795', 'Högersdorf', 'Schleswig-Holstein', 'SH', 'Segeberg'),
('DE', '23795', 'Negernbötel', 'Schleswig-Holstein', 'SH', 'Segeberg'),
('DE', '23795', 'Groß Rönnau', 'Schleswig-Holstein', 'SH', 'Segeberg'),
('DE', '23795', 'Fahrenkrug', 'Schleswig-Holstein', 'SH', 'Segeberg'),
('DE', '23795', 'Bad Segeberg', 'Schleswig-Holstein', 'SH', 'Segeberg'),
('DE', '23795', 'Schieren', 'Schleswig-Holstein', 'SH', 'Segeberg'),
('DE', '23795', 'Schwissel', 'Schleswig-Holstein', 'SH', 'Segeberg'),
('DE', '23812', 'Wahlstedt', 'Schleswig-Holstein', 'SH', 'Segeberg'),
('DE', '23813', 'Nehms', 'Schleswig-Holstein', 'SH', 'Segeberg'),
('DE', '23813', 'Blunk', 'Schleswig-Holstein', 'SH', 'Segeberg'),
('DE', '23815', 'Westerrade', 'Schleswig-Holstein', 'SH', 'Segeberg'),
('DE', '23815', 'Geschendorf', 'Schleswig-Holstein', 'SH', 'Segeberg'),
('DE', '23815', 'Strukdorf', 'Schleswig-Holstein', 'SH', 'Segeberg'),
('DE', '23816', 'Neversdorf', 'Schleswig-Holstein', 'SH', 'Segeberg'),
('DE', '23816', 'Leezen', 'Schleswig-Holstein', 'SH', 'Segeberg'),
('DE', '23816', 'Groß Niendorf', 'Schleswig-Holstein', 'SH', 'Segeberg'),
('DE', '23816', 'Bebensee', 'Schleswig-Holstein', 'SH', 'Segeberg'),
('DE', '23818', 'Neuengörs', 'Schleswig-Holstein', 'SH', 'Segeberg'),
('DE', '23820', 'Pronstorf', 'Schleswig-Holstein', 'SH', 'Segeberg'),
('DE', '23821', 'Rohlstorf', 'Schleswig-Holstein', 'SH', 'Segeberg'),
('DE', '23823', 'Seedorf', 'Schleswig-Holstein', 'SH', 'Segeberg'),
('DE', '23824', 'Damsdorf', 'Schleswig-Holstein', 'SH', 'Segeberg'),
('DE', '23824', 'Tensfeld', 'Schleswig-Holstein', 'SH', 'Segeberg'),
('DE', '23826', 'Fredesdorf', 'Schleswig-Holstein', 'SH', 'Segeberg'),
('DE', '23826', 'Bark', 'Schleswig-Holstein', 'SH', 'Segeberg'),
('DE', '23826', 'Todesfelde', 'Schleswig-Holstein', 'SH', 'Segeberg'),
('DE', '23827', 'Wensin', 'Schleswig-Holstein', 'SH', 'Segeberg'),
('DE', '23827', 'Travenhorst', 'Schleswig-Holstein', 'SH', 'Segeberg'),
('DE', '23827', 'Krems II', 'Schleswig-Holstein', 'SH', 'Segeberg'),
('DE', '23829', 'Wittenborn', 'Schleswig-Holstein', 'SH', 'Segeberg'),
('DE', '23829', 'Kükels', 'Schleswig-Holstein', 'SH', 'Segeberg'),
('DE', '23843', 'Neritz', 'Schleswig-Holstein', 'SH', 'Stormarn'),
('DE', '23843', 'Bad Oldesloe', 'Schleswig-Holstein', 'SH', 'Stormarn'),
('DE', '23843', 'Rümpel', 'Schleswig-Holstein', 'SH', 'Stormarn'),
('DE', '23843', 'Travenbrück', 'Schleswig-Holstein', 'SH', 'Stormarn'),
('DE', '23845', 'Dreggers', 'Schleswig-Holstein', 'SH', 'Stormarn'),
('DE', '23845', 'Seth', 'Schleswig-Holstein', 'SH', 'Stormarn'),
('DE', '23845', 'Wakendorf I', 'Schleswig-Holstein', 'SH', 'Stormarn'),
('DE', '23845', 'Itzstedt', 'Schleswig-Holstein', 'SH', 'Stormarn'),
('DE', '23845', 'Oering', 'Schleswig-Holstein', 'SH', 'Stormarn'),
('DE', '23845', 'Bahrenhof', 'Schleswig-Holstein', 'SH', 'Stormarn'),
('DE', '23845', 'Grabau', 'Schleswig-Holstein', 'SH', 'Stormarn'),
('DE', '23845', 'Bühnsdorf', 'Schleswig-Holstein', 'SH', 'Stormarn'),
('DE', '23847', 'Düchelsdorf', 'Schleswig-Holstein', 'SH', 'Stormarn'),
('DE', '23847', 'Grinau', 'Schleswig-Holstein', 'SH', 'Stormarn'),
('DE', '23847', 'Schiphorst', 'Schleswig-Holstein', 'SH', 'Stormarn'),
('DE', '23847', 'Bliestorf', 'Schleswig-Holstein', 'SH', 'Stormarn'),
('DE', '23847', 'Rethwisch', 'Schleswig-Holstein', 'SH', 'Stormarn'),
('DE', '23847', 'Lasbek', 'Schleswig-Holstein', 'SH', 'Stormarn'),
('DE', '23847', 'Steinhorst', 'Schleswig-Holstein', 'SH', 'Stormarn'),
('DE', '23847', 'Kastorf', 'Schleswig-Holstein', 'SH', 'Stormarn'),
('DE', '23847', 'Schürensöhlen', 'Schleswig-Holstein', 'SH', 'Stormarn'),
('DE', '23847', 'Pölitz', 'Schleswig-Holstein', 'SH', 'Stormarn'),
('DE', '23847', 'Stubben', 'Schleswig-Holstein', 'SH', 'Stormarn'),
('DE', '23847', 'Siebenbäumen', 'Schleswig-Holstein', 'SH', 'Stormarn'),
('DE', '23847', 'Meddewade', 'Schleswig-Holstein', 'SH', 'Stormarn'),
('DE', '23847', 'Sierksrade', 'Schleswig-Holstein', 'SH', 'Stormarn'),
('DE', '23847', 'Westerau', 'Schleswig-Holstein', 'SH', 'Stormarn'),
('DE', '23847', 'Groß Boden', 'Schleswig-Holstein', 'SH', 'Stormarn'),
('DE', '23858', 'Wesenberg', 'Schleswig-Holstein', 'SH', 'Stormarn'),
('DE', '23858', 'Feldhorst', 'Schleswig-Holstein', 'SH', 'Stormarn'),
('DE', '23858', 'Barnitz', 'Schleswig-Holstein', 'SH', 'Stormarn'),
('DE', '23858', 'Reinfeld (Holstein)', 'Schleswig-Holstein', 'SH', 'Stormarn'),
('DE', '23858', 'Heidekamp', 'Schleswig-Holstein', 'SH', 'Stormarn'),
('DE', '23860', 'Groß Schenkenberg', 'Schleswig-Holstein', 'SH', 'Stormarn'),
('DE', '23860', 'Klein Wesenberg', 'Schleswig-Holstein', 'SH', 'Stormarn'),
('DE', '23863', 'Kayhude', 'Schleswig-Holstein', 'SH', 'Stormarn'),
('DE', '23863', 'Nienwohld', 'Schleswig-Holstein', 'SH', 'Stormarn'),
('DE', '23863', 'Bargfeld-Stegen', 'Schleswig-Holstein', 'SH', 'Stormarn'),
('DE', '23866', 'Nahe', 'Schleswig-Holstein', 'SH', 'Segeberg'),
('DE', '23867', 'Sülfeld', 'Schleswig-Holstein', 'SH', 'Segeberg'),
('DE', '23869', 'Elmenhorst', 'Schleswig-Holstein', 'SH', 'Stormarn'),
('DE', '23879', 'Mölln', 'Schleswig-Holstein', 'SH', 'Herzogtum Lauenburg'),
('DE', '23881', 'Niendorf an der Stecknitz', 'Schleswig-Holstein', 'SH', 'Herzogtum Lauenburg'),
('DE', '23881', 'Bälau', 'Schleswig-Holstein', 'SH', 'Herzogtum Lauenburg'),
('DE', '23881', 'Koberg', 'Schleswig-Holstein', 'SH', 'Herzogtum Lauenburg'),
('DE', '23881', 'Alt Mölln', 'Schleswig-Holstein', 'SH', 'Herzogtum Lauenburg'),
('DE', '23881', 'Lankau', 'Schleswig-Holstein', 'SH', 'Herzogtum Lauenburg'),
('DE', '23881', 'Breitenfelde', 'Schleswig-Holstein', 'SH', 'Herzogtum Lauenburg'),
('DE', '23881', 'Borstorf', 'Schleswig-Holstein', 'SH', 'Herzogtum Lauenburg'),
('DE', '23883', 'Brunsmark', 'Schleswig-Holstein', 'SH', 'Herzogtum Lauenburg'),
('DE', '23883', 'Klein Zecher', 'Schleswig-Holstein', 'SH', 'Herzogtum Lauenburg'),
('DE', '23883', 'Horst', 'Schleswig-Holstein', 'SH', 'Herzogtum Lauenburg'),
('DE', '23883', 'Hollenbek', 'Schleswig-Holstein', 'SH', 'Herzogtum Lauenburg'),
('DE', '23883', 'Seedorf', 'Schleswig-Holstein', 'SH', 'Herzogtum Lauenburg'),
('DE', '23883', 'Lehmrade', 'Schleswig-Holstein', 'SH', 'Herzogtum Lauenburg'),
('DE', '23883', 'Grambek', 'Schleswig-Holstein', 'SH', 'Herzogtum Lauenburg'),
('DE', '23883', 'Sterley', 'Schleswig-Holstein', 'SH', 'Herzogtum Lauenburg'),
('DE', '23896', 'Walksfelde', 'Schleswig-Holstein', 'SH', 'Herzogtum Lauenburg'),
('DE', '23896', 'Nusse', 'Schleswig-Holstein', 'SH', 'Herzogtum Lauenburg'),
('DE', '23896', 'Ritzerau', 'Schleswig-Holstein', 'SH', 'Herzogtum Lauenburg'),
('DE', '23896', 'Poggensee', 'Schleswig-Holstein', 'SH', 'Herzogtum Lauenburg'),
('DE', '23896', 'Panten', 'Schleswig-Holstein', 'SH', 'Herzogtum Lauenburg'),
('DE', '23898', 'Wentorf', 'Schleswig-Holstein', 'SH', 'Herzogtum Lauenburg'),
('DE', '23898', 'Labenz', 'Schleswig-Holstein', 'SH', 'Herzogtum Lauenburg'),
('DE', '23898', 'Duvensee', 'Schleswig-Holstein', 'SH', 'Herzogtum Lauenburg'),
('DE', '23898', 'Klinkrade', 'Schleswig-Holstein', 'SH', 'Herzogtum Lauenburg'),
('DE', '23898', 'Kühsen', 'Schleswig-Holstein', 'SH', 'Herzogtum Lauenburg'),
('DE', '23898', 'Sirksfelde', 'Schleswig-Holstein', 'SH', 'Herzogtum Lauenburg'),
('DE', '23898', 'Sandesneben', 'Schleswig-Holstein', 'SH', 'Herzogtum Lauenburg'),
('DE', '23898', 'Lüchow', 'Schleswig-Holstein', 'SH', 'Herzogtum Lauenburg'),
('DE', '23899', 'Gudow', 'Schleswig-Holstein', 'SH', 'Herzogtum Lauenburg'),
('DE', '23899', 'Besenthal', 'Schleswig-Holstein', 'SH', 'Herzogtum Lauenburg'),
('DE', '23909', 'Giesensdorf', 'Schleswig-Holstein', 'SH', 'Herzogtum Lauenburg'),
('DE', '23909', 'Fredeburg', 'Schleswig-Holstein', 'SH', 'Herzogtum Lauenburg'),
('DE', '23909', 'Albsfelde', 'Schleswig-Holstein', 'SH', 'Herzogtum Lauenburg'),
('DE', '23909', 'Mechow', 'Schleswig-Holstein', 'SH', 'Herzogtum Lauenburg'),
('DE', '23909', 'Ratzeburg', 'Schleswig-Holstein', 'SH', 'Herzogtum Lauenburg'),
('DE', '23909', 'Römnitz', 'Schleswig-Holstein', 'SH', 'Herzogtum Lauenburg'),
('DE', '23909', 'Bäk', 'Schleswig-Holstein', 'SH', 'Herzogtum Lauenburg'),
('DE', '23911', 'Schmilau', 'Schleswig-Holstein', 'SH', 'Herzogtum Lauenburg'),
('DE', '23911', 'Mustin', 'Schleswig-Holstein', 'SH', 'Herzogtum Lauenburg'),
('DE', '23911', 'Ziethen', 'Schleswig-Holstein', 'SH', 'Herzogtum Lauenburg'),
('DE', '23911', 'Kittlitz', 'Schleswig-Holstein', 'SH', 'Herzogtum Lauenburg'),
('DE', '23911', 'Einhaus', 'Schleswig-Holstein', 'SH', 'Herzogtum Lauenburg'),
('DE', '23911', 'Harmsdorf', 'Schleswig-Holstein', 'SH', 'Herzogtum Lauenburg'),
('DE', '23911', 'Kulpin', 'Schleswig-Holstein', 'SH', 'Herzogtum Lauenburg'),
('DE', '23911', 'Salem', 'Schleswig-Holstein', 'SH', 'Herzogtum Lauenburg'),
('DE', '23911', 'Pogeez', 'Schleswig-Holstein', 'SH', 'Herzogtum Lauenburg'),
('DE', '23911', 'Buchholz', 'Schleswig-Holstein', 'SH', 'Herzogtum Lauenburg'),
('DE', '23911', 'Groß Disnack', 'Schleswig-Holstein', 'SH', 'Herzogtum Lauenburg'),
('DE', '23919', 'Niendorf', 'Schleswig-Holstein', 'SH', 'Herzogtum Lauenburg'),
('DE', '23919', 'Göldenitz', 'Schleswig-Holstein', 'SH', 'Herzogtum Lauenburg'),
('DE', '23919', 'Behlendorf', 'Schleswig-Holstein', 'SH', 'Herzogtum Lauenburg'),
('DE', '23919', 'Berkenthin', 'Schleswig-Holstein', 'SH', 'Herzogtum Lauenburg'),
('DE', '23919', 'Rondeshagen', 'Schleswig-Holstein', 'SH', 'Herzogtum Lauenburg'),
('DE', '24103', 'Kiel', 'Schleswig-Holstein', 'SH', 'Kiel, Landeshauptstadt'),
('DE', '24105', 'Kiel', 'Schleswig-Holstein', 'SH', 'Kiel, Landeshauptstadt'),
('DE', '24106', 'Kiel', 'Schleswig-Holstein', 'SH', 'Kiel, Landeshauptstadt'),
('DE', '24107', 'Quarnbek', 'Schleswig-Holstein', 'SH', 'Rendsburg-Eckernförde'),
('DE', '24107', 'Kiel', 'Schleswig-Holstein', 'SH', 'Rendsburg-Eckernförde'),
('DE', '24107', 'Ottendorf', 'Schleswig-Holstein', 'SH', 'Rendsburg-Eckernförde'),
('DE', '24109', 'Melsdorf', 'Schleswig-Holstein', 'SH', 'Rendsburg-Eckernförde'),
('DE', '24109', 'Kiel', 'Schleswig-Holstein', 'SH', 'Rendsburg-Eckernförde'),
('DE', '24111', 'Kiel', 'Schleswig-Holstein', 'SH', 'Kiel, Landeshauptstadt'),
('DE', '24113', 'Kiel', 'Schleswig-Holstein', 'SH', 'Rendsburg-Eckernförde'),
('DE', '24113', 'Molfsee', 'Schleswig-Holstein', 'SH', 'Rendsburg-Eckernförde'),
('DE', '24114', 'Kiel', 'Schleswig-Holstein', 'SH', 'Kiel, Landeshauptstadt'),
('DE', '24116', 'Kiel', 'Schleswig-Holstein', 'SH', 'Kiel, Landeshauptstadt'),
('DE', '24118', 'Kiel', 'Schleswig-Holstein', 'SH', 'Kiel, Landeshauptstadt'),
('DE', '24119', 'Kronshagen', 'Schleswig-Holstein', 'SH', 'Rendsburg-Eckernförde'),
('DE', '24143', 'Kiel', 'Schleswig-Holstein', 'SH', 'Kiel, Landeshauptstadt'),
('DE', '24145', 'Kiel', 'Schleswig-Holstein', 'SH', 'Kiel, Landeshauptstadt'),
('DE', '24146', 'Kiel', 'Schleswig-Holstein', 'SH', 'Kiel, Landeshauptstadt'),
('DE', '24147', 'Klausdorf', 'Schleswig-Holstein', 'SH', 'Landkreis Plön'),
('DE', '24147', 'Kiel', 'Schleswig-Holstein', 'SH', 'Kreisfreie Stadt Kiel'),
('DE', '24148', 'Kiel', 'Schleswig-Holstein', 'SH', 'Kiel, Landeshauptstadt'),
('DE', '24149', 'Kiel', 'Schleswig-Holstein', 'SH', 'Kiel, Landeshauptstadt'),
('DE', '24159', 'Kiel', 'Schleswig-Holstein', 'SH', 'Kiel, Landeshauptstadt'),
('DE', '24161', 'Altenholz', 'Schleswig-Holstein', 'SH', 'Rendsburg-Eckernförde'),
('DE', '24211', 'Schellhorn', 'Schleswig-Holstein', 'SH', 'Plön'),
('DE', '24211', 'Honigsee', 'Schleswig-Holstein', 'SH', 'Plön'),
('DE', '24211', 'Wahlstorf', 'Schleswig-Holstein', 'SH', 'Plön'),
('DE', '24211', 'Preetz', 'Schleswig-Holstein', 'SH', 'Plön'),
('DE', '24211', 'Lehmkuhlen', 'Schleswig-Holstein', 'SH', 'Plön'),
('DE', '24211', 'Postfeld', 'Schleswig-Holstein', 'SH', 'Plön'),
('DE', '24211', 'Rastorf', 'Schleswig-Holstein', 'SH', 'Plön'),
('DE', '24211', 'Kühren', 'Schleswig-Holstein', 'SH', 'Plön'),
('DE', '24211', 'Pohnsdorf', 'Schleswig-Holstein', 'SH', 'Plön'),
('DE', '24214', 'Schinkel', 'Schleswig-Holstein', 'SH', 'Rendsburg-Eckernförde'),
('DE', '24214', 'Neudorf-Bornstein', 'Schleswig-Holstein', 'SH', 'Rendsburg-Eckernförde'),
('DE', '24214', 'Neuwittenbek', 'Schleswig-Holstein', 'SH', 'Rendsburg-Eckernförde'),
('DE', '24214', 'Tüttendorf', 'Schleswig-Holstein', 'SH', 'Rendsburg-Eckernförde'),
('DE', '24214', 'Noer', 'Schleswig-Holstein', 'SH', 'Rendsburg-Eckernförde'),
('DE', '24214', 'Lindau', 'Schleswig-Holstein', 'SH', 'Rendsburg-Eckernförde'),
('DE', '24214', 'Gettorf', 'Schleswig-Holstein', 'SH', 'Rendsburg-Eckernförde'),
('DE', '24217', 'Bendfeld', 'Schleswig-Holstein', 'SH', 'Plön'),
('DE', '24217', 'Höhndorf', 'Schleswig-Holstein', 'SH', 'Plön'),
('DE', '24217', 'Stakendorf', 'Schleswig-Holstein', 'SH', 'Plön'),
('DE', '24217', 'Wisch', 'Schleswig-Holstein', 'SH', 'Plön'),
('DE', '24217', 'Krummbek', 'Schleswig-Holstein', 'SH', 'Plön'),
('DE', '24217', 'Schönberg (Holstein)', 'Schleswig-Holstein', 'SH', 'Plön'),
('DE', '24217', 'Fiefbergen', 'Schleswig-Holstein', 'SH', 'Plön'),
('DE', '24217', 'Krokau', 'Schleswig-Holstein', 'SH', 'Plön'),
('DE', '24217', 'Barsbek', 'Schleswig-Holstein', 'SH', 'Plön'),
('DE', '24220', 'Flintbek', 'Schleswig-Holstein', 'SH', 'Rendsburg-Eckernförde'),
('DE', '24220', 'Böhnhusen', 'Schleswig-Holstein', 'SH', 'Rendsburg-Eckernförde'),
('DE', '24220', 'Schönhorst', 'Schleswig-Holstein', 'SH', 'Rendsburg-Eckernförde'),
('DE', '24220', 'Boksee', 'Schleswig-Holstein', 'SH', 'Rendsburg-Eckernförde'),
('DE', '24220', 'Techelsdorf', 'Schleswig-Holstein', 'SH', 'Rendsburg-Eckernförde'),
('DE', '24222', 'Schwentinental', 'Schleswig-Holstein', 'SH', 'Plön'),
('DE', '24223', 'Raisdorf', 'Schleswig-Holstein', 'SH', 'Plön'),
('DE', '24226', 'Heikendorf', 'Schleswig-Holstein', 'SH', 'Plön'),
('DE', '24229', 'Dänischenhagen', 'Schleswig-Holstein', 'SH', 'Rendsburg-Eckernförde'),
('DE', '24229', 'Strande', 'Schleswig-Holstein', 'SH', 'Rendsburg-Eckernförde'),
('DE', '24229', 'Schwedeneck', 'Schleswig-Holstein', 'SH', 'Rendsburg-Eckernförde'),
('DE', '24232', 'Dobersdorf', 'Schleswig-Holstein', 'SH', 'Plön'),
('DE', '24232', 'Schönkirchen', 'Schleswig-Holstein', 'SH', 'Plön'),
('DE', '24235', 'Wendtorf', 'Schleswig-Holstein', 'SH', 'Plön'),
('DE', '24235', 'Lutterbek', 'Schleswig-Holstein', 'SH', 'Plön'),
('DE', '24235', 'Laboe', 'Schleswig-Holstein', 'SH', 'Plön'),
('DE', '24235', 'Stein', 'Schleswig-Holstein', 'SH', 'Plön'),
('DE', '24235', 'Brodersdorf', 'Schleswig-Holstein', 'SH', 'Plön'),
('DE', '24238', 'Lammershagen', 'Schleswig-Holstein', 'SH', 'Plön'),
('DE', '24238', 'Selent', 'Schleswig-Holstein', 'SH', 'Plön'),
('DE', '24238', 'Martensrade', 'Schleswig-Holstein', 'SH', 'Plön'),
('DE', '24238', 'Mucheln', 'Schleswig-Holstein', 'SH', 'Plön'),
('DE', '24239', 'Achterwehr', 'Schleswig-Holstein', 'SH', 'Rendsburg-Eckernförde'),
('DE', '24241', 'Sören', 'Schleswig-Holstein', 'SH', 'Rendsburg-Eckernförde'),
('DE', '24241', 'Grevenkrug', 'Schleswig-Holstein', 'SH', 'Rendsburg-Eckernförde'),
('DE', '24241', 'Reesdorf', 'Schleswig-Holstein', 'SH', 'Rendsburg-Eckernförde');
INSERT INTO `plz_tabelle` (`lkz`, `plz`, `ort_stadtteil`, `bundesland`, `blkz`, `kreis`) VALUES
('DE', '24241', 'Blumenthal', 'Schleswig-Holstein', 'SH', 'Rendsburg-Eckernförde'),
('DE', '24241', 'Schmalstede', 'Schleswig-Holstein', 'SH', 'Rendsburg-Eckernförde'),
('DE', '24241', 'Schierensee', 'Schleswig-Holstein', 'SH', 'Rendsburg-Eckernförde'),
('DE', '24242', 'Felde', 'Schleswig-Holstein', 'SH', 'Rendsburg-Eckernförde'),
('DE', '24244', 'Felm', 'Schleswig-Holstein', 'SH', 'Rendsburg-Eckernförde'),
('DE', '24245', 'Großbarkau', 'Schleswig-Holstein', 'SH', 'Plön'),
('DE', '24245', 'Klein Barkau', 'Schleswig-Holstein', 'SH', 'Plön'),
('DE', '24245', 'Kirchbarkau', 'Schleswig-Holstein', 'SH', 'Plön'),
('DE', '24245', 'Barmissen', 'Schleswig-Holstein', 'SH', 'Plön'),
('DE', '24247', 'Rodenbek', 'Schleswig-Holstein', 'SH', 'Rendsburg-Eckernförde'),
('DE', '24247', 'Mielkendorf', 'Schleswig-Holstein', 'SH', 'Rendsburg-Eckernförde'),
('DE', '24248', 'Mönkeberg', 'Schleswig-Holstein', 'SH', 'Plön'),
('DE', '24250', 'Löptin', 'Schleswig-Holstein', 'SH', 'Plön'),
('DE', '24250', 'Bothkamp', 'Schleswig-Holstein', 'SH', 'Plön'),
('DE', '24250', 'Nettelsee', 'Schleswig-Holstein', 'SH', 'Plön'),
('DE', '24250', 'Warnau', 'Schleswig-Holstein', 'SH', 'Plön'),
('DE', '24251', 'Osdorf', 'Schleswig-Holstein', 'SH', 'Rendsburg-Eckernförde'),
('DE', '24253', 'Passade', 'Schleswig-Holstein', 'SH', 'Plön'),
('DE', '24253', 'Probsteierhagen', 'Schleswig-Holstein', 'SH', 'Plön'),
('DE', '24253', 'Fahren', 'Schleswig-Holstein', 'SH', 'Plön'),
('DE', '24253', 'Prasdorf', 'Schleswig-Holstein', 'SH', 'Plön'),
('DE', '24254', 'Rumohr', 'Schleswig-Holstein', 'SH', 'Rendsburg-Eckernförde'),
('DE', '24256', 'Stoltenberg', 'Schleswig-Holstein', 'SH', 'Plön'),
('DE', '24256', 'Fargau-Pratjau', 'Schleswig-Holstein', 'SH', 'Plön'),
('DE', '24256', 'Schlesen', 'Schleswig-Holstein', 'SH', 'Plön'),
('DE', '24257', 'Schwartbuck', 'Schleswig-Holstein', 'SH', 'Plön'),
('DE', '24257', 'Köhn', 'Schleswig-Holstein', 'SH', 'Plön'),
('DE', '24257', 'Hohenfelde', 'Schleswig-Holstein', 'SH', 'Plön'),
('DE', '24259', 'Westensee', 'Schleswig-Holstein', 'SH', 'Rendsburg-Eckernförde'),
('DE', '24306', 'Plön', 'Schleswig-Holstein', 'SH', 'Plön'),
('DE', '24306', 'Wittmoldt', 'Schleswig-Holstein', 'SH', 'Plön'),
('DE', '24306', 'Rathjensdorf', 'Schleswig-Holstein', 'SH', 'Plön'),
('DE', '24306', 'Lebrade', 'Schleswig-Holstein', 'SH', 'Plön'),
('DE', '24306', 'Bösdorf', 'Schleswig-Holstein', 'SH', 'Plön'),
('DE', '24321', 'Hohwacht', 'Schleswig-Holstein', 'SH', 'Plön'),
('DE', '24321', 'Helmstorf', 'Schleswig-Holstein', 'SH', 'Plön'),
('DE', '24321', 'Tröndel', 'Schleswig-Holstein', 'SH', 'Plön'),
('DE', '24321', 'Klamp', 'Schleswig-Holstein', 'SH', 'Plön'),
('DE', '24321', 'Giekau', 'Schleswig-Holstein', 'SH', 'Plön'),
('DE', '24321', 'Panker', 'Schleswig-Holstein', 'SH', 'Plön'),
('DE', '24321', 'Lütjenburg', 'Schleswig-Holstein', 'SH', 'Plön'),
('DE', '24321', 'Behrensdorf', 'Schleswig-Holstein', 'SH', 'Plön'),
('DE', '24326', 'Dörnick', 'Schleswig-Holstein', 'SH', 'Segeberg'),
('DE', '24326', 'Stocksee', 'Schleswig-Holstein', 'SH', 'Segeberg'),
('DE', '24326', 'Dersau', 'Schleswig-Holstein', 'SH', 'Segeberg'),
('DE', '24326', 'Ascheberg', 'Schleswig-Holstein', 'SH', 'Segeberg'),
('DE', '24326', 'Kalübbe', 'Schleswig-Holstein', 'SH', 'Segeberg'),
('DE', '24326', 'Nehmten', 'Schleswig-Holstein', 'SH', 'Segeberg'),
('DE', '24327', 'Kletkamp', 'Schleswig-Holstein', 'SH', 'Plön'),
('DE', '24327', 'Blekendorf', 'Schleswig-Holstein', 'SH', 'Plön'),
('DE', '24327', 'Högsdorf', 'Schleswig-Holstein', 'SH', 'Plön'),
('DE', '24329', 'Dannau', 'Schleswig-Holstein', 'SH', 'Plön'),
('DE', '24329', 'Rantzau', 'Schleswig-Holstein', 'SH', 'Plön'),
('DE', '24329', 'Grebin', 'Schleswig-Holstein', 'SH', 'Plön'),
('DE', '24340', 'Windeby', 'Schleswig-Holstein', 'SH', 'Rendsburg-Eckernförde'),
('DE', '24340', 'Gammelby', 'Schleswig-Holstein', 'SH', 'Rendsburg-Eckernförde'),
('DE', '24340', 'Goosefeld', 'Schleswig-Holstein', 'SH', 'Rendsburg-Eckernförde'),
('DE', '24340', 'Altenhof', 'Schleswig-Holstein', 'SH', 'Rendsburg-Eckernförde'),
('DE', '24340', 'Eckernförde', 'Schleswig-Holstein', 'SH', 'Rendsburg-Eckernförde'),
('DE', '24351', 'Damp', 'Schleswig-Holstein', 'SH', 'Rendsburg-Eckernförde'),
('DE', '24351', 'Thumby', 'Schleswig-Holstein', 'SH', 'Rendsburg-Eckernförde'),
('DE', '24354', 'Kosel', 'Schleswig-Holstein', 'SH', 'Rendsburg-Eckernförde'),
('DE', '24354', 'Rieseby', 'Schleswig-Holstein', 'SH', 'Rendsburg-Eckernförde'),
('DE', '24357', 'Güby', 'Schleswig-Holstein', 'SH', 'Rendsburg-Eckernförde'),
('DE', '24357', 'Fleckeby', 'Schleswig-Holstein', 'SH', 'Rendsburg-Eckernförde'),
('DE', '24357', 'Hummelfeld', 'Schleswig-Holstein', 'SH', 'Rendsburg-Eckernförde'),
('DE', '24358', 'Ascheffel', 'Schleswig-Holstein', 'SH', 'Rendsburg-Eckernförde'),
('DE', '24358', 'Hütten', 'Schleswig-Holstein', 'SH', 'Rendsburg-Eckernförde'),
('DE', '24358', 'Bistensee', 'Schleswig-Holstein', 'SH', 'Rendsburg-Eckernförde'),
('DE', '24360', 'Barkelsby', 'Schleswig-Holstein', 'SH', 'Rendsburg-Eckernförde'),
('DE', '24361', 'Damendorf', 'Schleswig-Holstein', 'SH', 'Rendsburg-Eckernförde'),
('DE', '24361', 'Haby', 'Schleswig-Holstein', 'SH', 'Rendsburg-Eckernförde'),
('DE', '24361', 'Klein Wittensee', 'Schleswig-Holstein', 'SH', 'Rendsburg-Eckernförde'),
('DE', '24361', 'Holzbunge', 'Schleswig-Holstein', 'SH', 'Rendsburg-Eckernförde'),
('DE', '24361', 'Groß Wittensee', 'Schleswig-Holstein', 'SH', 'Rendsburg-Eckernförde'),
('DE', '24363', 'Holtsee', 'Schleswig-Holstein', 'SH', 'Rendsburg-Eckernförde'),
('DE', '24364', 'Holzdorf', 'Schleswig-Holstein', 'SH', 'Rendsburg-Eckernförde'),
('DE', '24366', 'Loose', 'Schleswig-Holstein', 'SH', 'Rendsburg-Eckernförde'),
('DE', '24367', 'Osterby', 'Schleswig-Holstein', 'SH', 'Rendsburg-Eckernförde'),
('DE', '24369', 'Waabs', 'Schleswig-Holstein', 'SH', 'Rendsburg-Eckernförde'),
('DE', '24376', 'Rabel', 'Schleswig-Holstein', 'SH', 'Schleswig-Flensburg'),
('DE', '24376', 'Kappeln', 'Schleswig-Holstein', 'SH', 'Schleswig-Flensburg'),
('DE', '24376', 'Hasselberg', 'Schleswig-Holstein', 'SH', 'Schleswig-Flensburg'),
('DE', '24376', 'Grödersby', 'Schleswig-Holstein', 'SH', 'Schleswig-Flensburg'),
('DE', '24376', 'Arnis', 'Schleswig-Holstein', 'SH', 'Schleswig-Flensburg'),
('DE', '24392', 'Scheggerott', 'Schleswig-Holstein', 'SH', 'Schleswig-Flensburg'),
('DE', '24392', 'Nottfeld', 'Schleswig-Holstein', 'SH', 'Schleswig-Flensburg'),
('DE', '24392', 'Wagersrott', 'Schleswig-Holstein', 'SH', 'Schleswig-Flensburg'),
('DE', '24392', 'Süderbrarup', 'Schleswig-Holstein', 'SH', 'Schleswig-Flensburg'),
('DE', '24392', 'Ekenis', 'Schleswig-Holstein', 'SH', 'Schleswig-Flensburg'),
('DE', '24392', 'Kiesby', 'Schleswig-Holstein', 'SH', 'Schleswig-Flensburg'),
('DE', '24392', 'Dollrottfeld', 'Schleswig-Holstein', 'SH', 'Schleswig-Flensburg'),
('DE', '24392', 'Boren', 'Schleswig-Holstein', 'SH', 'Schleswig-Flensburg'),
('DE', '24392', 'Brebel', 'Schleswig-Holstein', 'SH', 'Schleswig-Flensburg'),
('DE', '24392', 'Saustrup', 'Schleswig-Holstein', 'SH', 'Schleswig-Flensburg'),
('DE', '24392', 'Norderbrarup', 'Schleswig-Holstein', 'SH', 'Schleswig-Flensburg'),
('DE', '24395', 'Rabenholz', 'Schleswig-Holstein', 'SH', 'Schleswig-Flensburg'),
('DE', '24395', 'Nieby', 'Schleswig-Holstein', 'SH', 'Schleswig-Flensburg'),
('DE', '24395', 'Pommerby', 'Schleswig-Holstein', 'SH', 'Schleswig-Flensburg'),
('DE', '24395', 'Kronsgaard', 'Schleswig-Holstein', 'SH', 'Schleswig-Flensburg'),
('DE', '24395', 'Gelting', 'Schleswig-Holstein', 'SH', 'Schleswig-Flensburg'),
('DE', '24395', 'Niesgrau', 'Schleswig-Holstein', 'SH', 'Schleswig-Flensburg'),
('DE', '24395', 'Stangheck', 'Schleswig-Holstein', 'SH', 'Schleswig-Flensburg'),
('DE', '24398', 'Winnemark', 'Schleswig-Holstein', 'SH', 'Rendsburg-Eckernförde'),
('DE', '24398', 'Brodersby', 'Schleswig-Holstein', 'SH', 'Rendsburg-Eckernförde'),
('DE', '24398', 'Dörphof', 'Schleswig-Holstein', 'SH', 'Rendsburg-Eckernförde'),
('DE', '24398', 'Karby', 'Schleswig-Holstein', 'SH', 'Rendsburg-Eckernförde'),
('DE', '24399', 'Arnis', 'Schleswig-Holstein', 'SH', 'Schleswig-Flensburg'),
('DE', '24401', 'Böel', 'Schleswig-Holstein', 'SH', 'Schleswig-Flensburg'),
('DE', '24402', 'Esgrus', 'Schleswig-Holstein', 'SH', 'Schleswig-Flensburg'),
('DE', '24404', 'Maasholm', 'Schleswig-Holstein', 'SH', 'Schleswig-Flensburg'),
('DE', '24405', 'Mohrkirch', 'Schleswig-Holstein', 'SH', 'Schleswig-Flensburg'),
('DE', '24405', 'Rügge', 'Schleswig-Holstein', 'SH', 'Schleswig-Flensburg'),
('DE', '24407', 'Rabenkirchen-Faulück', 'Schleswig-Holstein', 'SH', 'Schleswig-Flensburg'),
('DE', '24407', 'Oersberg', 'Schleswig-Holstein', 'SH', 'Schleswig-Flensburg'),
('DE', '24409', 'Stoltebüll', 'Schleswig-Holstein', 'SH', 'Schleswig-Flensburg'),
('DE', '24534', 'Neumünster', 'Schleswig-Holstein', 'SH', 'Neumünster, Stadt'),
('DE', '24536', 'Tasdorf', 'Schleswig-Holstein', 'SH', 'Plön'),
('DE', '24536', 'Neumünster', 'Schleswig-Holstein', 'SH', 'Plön'),
('DE', '24537', 'Neumünster', 'Schleswig-Holstein', 'SH', 'Neumünster, Stadt'),
('DE', '24539', 'Neumünster', 'Schleswig-Holstein', 'SH', 'Neumünster, Stadt'),
('DE', '24558', 'Wakendorf II', 'Schleswig-Holstein', 'SH', 'Segeberg'),
('DE', '24558', 'Henstedt-Ulzburg', 'Schleswig-Holstein', 'SH', 'Segeberg'),
('DE', '24568', 'Winsen', 'Schleswig-Holstein', 'SH', 'Segeberg'),
('DE', '24568', 'Nützen', 'Schleswig-Holstein', 'SH', 'Segeberg'),
('DE', '24568', 'Kattendorf', 'Schleswig-Holstein', 'SH', 'Segeberg'),
('DE', '24568', 'Kaltenkirchen', 'Schleswig-Holstein', 'SH', 'Segeberg'),
('DE', '24568', 'Oersdorf', 'Schleswig-Holstein', 'SH', 'Segeberg'),
('DE', '24576', 'Weddelbrook', 'Schleswig-Holstein', 'SH', 'Segeberg'),
('DE', '24576', 'Hagen', 'Schleswig-Holstein', 'SH', 'Segeberg'),
('DE', '24576', 'Mönkloh', 'Schleswig-Holstein', 'SH', 'Segeberg'),
('DE', '24576', 'Hitzhusen', 'Schleswig-Holstein', 'SH', 'Segeberg'),
('DE', '24576', 'Bad Bramstedt', 'Schleswig-Holstein', 'SH', 'Segeberg'),
('DE', '24576', 'Bimöhlen', 'Schleswig-Holstein', 'SH', 'Segeberg'),
('DE', '24582', 'Wattenbek', 'Schleswig-Holstein', 'SH', 'Rendsburg-Eckernförde'),
('DE', '24582', 'Brügge', 'Schleswig-Holstein', 'SH', 'Rendsburg-Eckernförde'),
('DE', '24582', 'Hoffeld', 'Schleswig-Holstein', 'SH', 'Rendsburg-Eckernförde'),
('DE', '24582', 'Schönbek', 'Schleswig-Holstein', 'SH', 'Rendsburg-Eckernförde'),
('DE', '24582', 'Bordesholm', 'Schleswig-Holstein', 'SH', 'Rendsburg-Eckernförde'),
('DE', '24582', 'Bissee', 'Schleswig-Holstein', 'SH', 'Rendsburg-Eckernförde'),
('DE', '24582', 'Mühbrook', 'Schleswig-Holstein', 'SH', 'Rendsburg-Eckernförde'),
('DE', '24582', 'Groß Buchwald', 'Schleswig-Holstein', 'SH', 'Rendsburg-Eckernförde'),
('DE', '24589', 'Dätgen', 'Schleswig-Holstein', 'SH', 'Rendsburg-Eckernförde'),
('DE', '24589', 'Borgdorf-Seedorf', 'Schleswig-Holstein', 'SH', 'Rendsburg-Eckernförde'),
('DE', '24589', 'Ellerdorf', 'Schleswig-Holstein', 'SH', 'Rendsburg-Eckernförde'),
('DE', '24589', 'Schülp bei Nortorf', 'Schleswig-Holstein', 'SH', 'Rendsburg-Eckernförde'),
('DE', '24589', 'Eisendorf', 'Schleswig-Holstein', 'SH', 'Rendsburg-Eckernförde'),
('DE', '24589', 'Nortorf', 'Schleswig-Holstein', 'SH', 'Rendsburg-Eckernförde'),
('DE', '24594', 'Remmels', 'Schleswig-Holstein', 'SH', 'Rendsburg-Eckernförde'),
('DE', '24594', 'Tappendorf', 'Schleswig-Holstein', 'SH', 'Rendsburg-Eckernförde'),
('DE', '24594', 'Grauel', 'Schleswig-Holstein', 'SH', 'Rendsburg-Eckernförde'),
('DE', '24594', 'Jahrsdorf', 'Schleswig-Holstein', 'SH', 'Rendsburg-Eckernförde'),
('DE', '24594', 'Hohenwestedt', 'Schleswig-Holstein', 'SH', 'Rendsburg-Eckernförde'),
('DE', '24594', 'Heinkenborstel', 'Schleswig-Holstein', 'SH', 'Rendsburg-Eckernförde'),
('DE', '24594', 'Wapelfeld', 'Schleswig-Holstein', 'SH', 'Rendsburg-Eckernförde'),
('DE', '24594', 'Rade bei Hohenwestedt', 'Schleswig-Holstein', 'SH', 'Rendsburg-Eckernförde'),
('DE', '24594', 'Nindorf', 'Schleswig-Holstein', 'SH', 'Rendsburg-Eckernförde'),
('DE', '24594', 'Meezen', 'Schleswig-Holstein', 'SH', 'Rendsburg-Eckernförde'),
('DE', '24594', 'Mörel', 'Schleswig-Holstein', 'SH', 'Rendsburg-Eckernförde'),
('DE', '24598', 'Latendorf', 'Schleswig-Holstein', 'SH', 'Segeberg'),
('DE', '24598', 'Heidmühlen', 'Schleswig-Holstein', 'SH', 'Segeberg'),
('DE', '24598', 'Boostedt', 'Schleswig-Holstein', 'SH', 'Segeberg'),
('DE', '24601', 'Belau', 'Schleswig-Holstein', 'SH', 'Plön'),
('DE', '24601', 'Stolpe', 'Schleswig-Holstein', 'SH', 'Plön'),
('DE', '24601', 'Ruhwinkel', 'Schleswig-Holstein', 'SH', 'Plön'),
('DE', '24601', 'Wankendorf', 'Schleswig-Holstein', 'SH', 'Plön'),
('DE', '24610', 'Trappenkamp', 'Schleswig-Holstein', 'SH', 'Segeberg'),
('DE', '24610', 'Gönnebek', 'Schleswig-Holstein', 'SH', 'Segeberg'),
('DE', '24613', 'Wiedenborstel', 'Schleswig-Holstein', 'SH', 'Steinburg'),
('DE', '24613', 'Aukrug', 'Schleswig-Holstein', 'SH', 'Steinburg'),
('DE', '24616', 'Willenscharen', 'Schleswig-Holstein', 'SH', 'Steinburg'),
('DE', '24616', 'Brokstedt', 'Schleswig-Holstein', 'SH', 'Steinburg'),
('DE', '24616', 'Hasenkrug', 'Schleswig-Holstein', 'SH', 'Steinburg'),
('DE', '24616', 'Borstel', 'Schleswig-Holstein', 'SH', 'Steinburg'),
('DE', '24616', 'Hardebek', 'Schleswig-Holstein', 'SH', 'Steinburg'),
('DE', '24616', 'Sarlhusen', 'Schleswig-Holstein', 'SH', 'Steinburg'),
('DE', '24616', 'Armstedt', 'Schleswig-Holstein', 'SH', 'Steinburg'),
('DE', '24619', 'Tarbek', 'Schleswig-Holstein', 'SH', 'Segeberg'),
('DE', '24619', 'Bornhöved', 'Schleswig-Holstein', 'SH', 'Segeberg'),
('DE', '24619', 'Rendswühren', 'Schleswig-Holstein', 'SH', 'Segeberg'),
('DE', '24620', 'Bönebüttel', 'Schleswig-Holstein', 'SH', 'Plön'),
('DE', '24622', 'Gnutz', 'Schleswig-Holstein', 'SH', 'Rendsburg-Eckernförde'),
('DE', '24623', 'Großenaspe', 'Schleswig-Holstein', 'SH', 'Segeberg'),
('DE', '24625', 'Negenharrie', 'Schleswig-Holstein', 'SH', 'Rendsburg-Eckernförde'),
('DE', '24625', 'Großharrie', 'Schleswig-Holstein', 'SH', 'Rendsburg-Eckernförde'),
('DE', '24626', 'Groß Kummerfeld', 'Schleswig-Holstein', 'SH', 'Segeberg'),
('DE', '24628', 'Hartenholm', 'Schleswig-Holstein', 'SH', 'Segeberg'),
('DE', '24629', 'Kisdorf', 'Schleswig-Holstein', 'SH', 'Segeberg'),
('DE', '24631', 'Langwedel', 'Schleswig-Holstein', 'SH', 'Rendsburg-Eckernförde'),
('DE', '24632', 'Heidmoor', 'Schleswig-Holstein', 'SH', 'Segeberg'),
('DE', '24632', 'Lentföhrden', 'Schleswig-Holstein', 'SH', 'Segeberg'),
('DE', '24634', 'Padenstedt', 'Schleswig-Holstein', 'SH', 'Rendsburg-Eckernförde'),
('DE', '24634', 'Arpsdorf', 'Schleswig-Holstein', 'SH', 'Rendsburg-Eckernförde'),
('DE', '24635', 'Daldorf', 'Schleswig-Holstein', 'SH', 'Segeberg'),
('DE', '24635', 'Rickling', 'Schleswig-Holstein', 'SH', 'Segeberg'),
('DE', '24637', 'Schillsdorf', 'Schleswig-Holstein', 'SH', 'Plön'),
('DE', '24638', 'Schmalensee', 'Schleswig-Holstein', 'SH', 'Segeberg'),
('DE', '24640', 'Hasenmoor', 'Schleswig-Holstein', 'SH', 'Segeberg'),
('DE', '24640', 'Schmalfeld', 'Schleswig-Holstein', 'SH', 'Segeberg'),
('DE', '24641', 'Sievershütten', 'Schleswig-Holstein', 'SH', 'Segeberg'),
('DE', '24641', 'Hüttblek', 'Schleswig-Holstein', 'SH', 'Segeberg'),
('DE', '24641', 'Stuvenborn', 'Schleswig-Holstein', 'SH', 'Segeberg'),
('DE', '24643', 'Struvenhütten', 'Schleswig-Holstein', 'SH', 'Segeberg'),
('DE', '24644', 'Loop', 'Schleswig-Holstein', 'SH', 'Rendsburg-Eckernförde'),
('DE', '24644', 'Timmaspe', 'Schleswig-Holstein', 'SH', 'Rendsburg-Eckernförde'),
('DE', '24644', 'Krogaspe', 'Schleswig-Holstein', 'SH', 'Rendsburg-Eckernförde'),
('DE', '24646', 'Warder', 'Schleswig-Holstein', 'SH', 'Rendsburg-Eckernförde'),
('DE', '24647', 'Ehndorf', 'Schleswig-Holstein', 'SH', 'Rendsburg-Eckernförde'),
('DE', '24647', 'Wasbek', 'Schleswig-Holstein', 'SH', 'Rendsburg-Eckernförde'),
('DE', '24649', 'Fuhlendorf', 'Schleswig-Holstein', 'SH', 'Segeberg'),
('DE', '24649', 'Wiemersdorf', 'Schleswig-Holstein', 'SH', 'Segeberg'),
('DE', '24768', 'Rendsburg', 'Schleswig-Holstein', 'SH', 'Rendsburg-Eckernförde'),
('DE', '24782', 'Büdelsdorf', 'Schleswig-Holstein', 'SH', 'Rendsburg-Eckernförde'),
('DE', '24782', 'Rickert', 'Schleswig-Holstein', 'SH', 'Rendsburg-Eckernförde'),
('DE', '24783', 'Osterrönfeld', 'Schleswig-Holstein', 'SH', 'Rendsburg-Eckernförde'),
('DE', '24784', 'Westerrönfeld', 'Schleswig-Holstein', 'SH', 'Rendsburg-Eckernförde'),
('DE', '24787', 'Fockbek', 'Schleswig-Holstein', 'SH', 'Rendsburg-Eckernförde'),
('DE', '24790', 'Schacht-Audorf', 'Schleswig-Holstein', 'SH', 'Rendsburg-Eckernförde'),
('DE', '24790', 'Schülldorf', 'Schleswig-Holstein', 'SH', 'Rendsburg-Eckernförde'),
('DE', '24790', 'Rade bei Rendsburg', 'Schleswig-Holstein', 'SH', 'Rendsburg-Eckernförde'),
('DE', '24790', 'Haßmoor', 'Schleswig-Holstein', 'SH', 'Rendsburg-Eckernförde'),
('DE', '24790', 'Ostenfeld', 'Schleswig-Holstein', 'SH', 'Rendsburg-Eckernförde'),
('DE', '24791', 'Alt Duvenstedt', 'Schleswig-Holstein', 'SH', 'Rendsburg-Eckernförde'),
('DE', '24793', 'Bargstedt', 'Schleswig-Holstein', 'SH', 'Rendsburg-Eckernförde'),
('DE', '24793', 'Oldenhütten', 'Schleswig-Holstein', 'SH', 'Rendsburg-Eckernförde'),
('DE', '24793', 'Brammer', 'Schleswig-Holstein', 'SH', 'Rendsburg-Eckernförde'),
('DE', '24794', 'Borgstedt', 'Schleswig-Holstein', 'SH', 'Rendsburg-Eckernförde'),
('DE', '24794', 'Neu Duvenstedt', 'Schleswig-Holstein', 'SH', 'Rendsburg-Eckernförde'),
('DE', '24794', 'Bünsdorf', 'Schleswig-Holstein', 'SH', 'Rendsburg-Eckernförde'),
('DE', '24796', 'Krummwisch', 'Schleswig-Holstein', 'SH', 'Rendsburg-Eckernförde'),
('DE', '24796', 'Bredenbek', 'Schleswig-Holstein', 'SH', 'Rendsburg-Eckernförde'),
('DE', '24796', 'Bovenau', 'Schleswig-Holstein', 'SH', 'Rendsburg-Eckernförde'),
('DE', '24797', 'Hörsten', 'Schleswig-Holstein', 'SH', 'Rendsburg-Eckernförde'),
('DE', '24797', 'Breiholz', 'Schleswig-Holstein', 'SH', 'Rendsburg-Eckernförde'),
('DE', '24799', 'Christiansholm', 'Schleswig-Holstein', 'SH', 'Schleswig-Flensburg'),
('DE', '24799', 'Friedrichsholm', 'Schleswig-Holstein', 'SH', 'Schleswig-Flensburg'),
('DE', '24799', 'Königshügel', 'Schleswig-Holstein', 'SH', 'Schleswig-Flensburg'),
('DE', '24799', 'Friedrichsgraben', 'Schleswig-Holstein', 'SH', 'Schleswig-Flensburg'),
('DE', '24799', 'Meggerdorf', 'Schleswig-Holstein', 'SH', 'Schleswig-Flensburg'),
('DE', '24800', 'Elsdorf-Westermühlen', 'Schleswig-Holstein', 'SH', 'Rendsburg-Eckernförde'),
('DE', '24802', 'Bokel', 'Schleswig-Holstein', 'SH', 'Rendsburg-Eckernförde'),
('DE', '24802', 'Emkendorf', 'Schleswig-Holstein', 'SH', 'Rendsburg-Eckernförde'),
('DE', '24802', 'Groß Vollstedt', 'Schleswig-Holstein', 'SH', 'Rendsburg-Eckernförde'),
('DE', '24803', 'Tielen', 'Schleswig-Holstein', 'SH', 'Schleswig-Flensburg'),
('DE', '24803', 'Erfde', 'Schleswig-Holstein', 'SH', 'Schleswig-Flensburg'),
('DE', '24805', 'Prinzenmoor', 'Schleswig-Holstein', 'SH', 'Rendsburg-Eckernförde'),
('DE', '24805', 'Hamdorf', 'Schleswig-Holstein', 'SH', 'Rendsburg-Eckernförde'),
('DE', '24806', 'Sophienhamm', 'Schleswig-Holstein', 'SH', 'Rendsburg-Eckernförde'),
('DE', '24806', 'Hohn', 'Schleswig-Holstein', 'SH', 'Rendsburg-Eckernförde'),
('DE', '24806', 'Bargstall', 'Schleswig-Holstein', 'SH', 'Rendsburg-Eckernförde'),
('DE', '24806', 'Lohe-Föhrden', 'Schleswig-Holstein', 'SH', 'Rendsburg-Eckernförde'),
('DE', '24808', 'Jevenstedt', 'Schleswig-Holstein', 'SH', 'Rendsburg-Eckernförde'),
('DE', '24809', 'Nübbel', 'Schleswig-Holstein', 'SH', 'Rendsburg-Eckernförde'),
('DE', '24811', 'Ahlefeld', 'Schleswig-Holstein', 'SH', 'Rendsburg-Eckernförde'),
('DE', '24811', 'Owschlag', 'Schleswig-Holstein', 'SH', 'Rendsburg-Eckernförde'),
('DE', '24811', 'Brekendorf', 'Schleswig-Holstein', 'SH', 'Rendsburg-Eckernförde'),
('DE', '24813', 'Schülp bei Rendsburg', 'Schleswig-Holstein', 'SH', 'Rendsburg-Eckernförde'),
('DE', '24814', 'Sehestedt', 'Schleswig-Holstein', 'SH', 'Rendsburg-Eckernförde'),
('DE', '24816', 'Luhnstedt', 'Schleswig-Holstein', 'SH', 'Rendsburg-Eckernförde'),
('DE', '24816', 'Stafstedt', 'Schleswig-Holstein', 'SH', 'Rendsburg-Eckernförde'),
('DE', '24816', 'Brinjahe', 'Schleswig-Holstein', 'SH', 'Rendsburg-Eckernförde'),
('DE', '24816', 'Hamweddel', 'Schleswig-Holstein', 'SH', 'Rendsburg-Eckernförde'),
('DE', '24817', 'Tetenhusen', 'Schleswig-Holstein', 'SH', 'Schleswig-Flensburg'),
('DE', '24819', 'Haale', 'Schleswig-Holstein', 'SH', 'Rendsburg-Eckernförde'),
('DE', '24819', 'Todenbüttel', 'Schleswig-Holstein', 'SH', 'Rendsburg-Eckernförde'),
('DE', '24819', 'Nienborstel', 'Schleswig-Holstein', 'SH', 'Rendsburg-Eckernförde'),
('DE', '24819', 'Embühren', 'Schleswig-Holstein', 'SH', 'Rendsburg-Eckernförde'),
('DE', '24837', 'Schleswig', 'Schleswig-Holstein', 'SH', 'Schleswig-Flensburg'),
('DE', '24848', 'Klein Bennebek', 'Schleswig-Holstein', 'SH', 'Schleswig-Flensburg'),
('DE', '24848', 'Alt Bennebek', 'Schleswig-Holstein', 'SH', 'Schleswig-Flensburg'),
('DE', '24848', 'Kropp', 'Schleswig-Holstein', 'SH', 'Schleswig-Flensburg'),
('DE', '24848', 'Klein Rheide', 'Schleswig-Holstein', 'SH', 'Schleswig-Flensburg'),
('DE', '24850', 'Schuby', 'Schleswig-Holstein', 'SH', 'Schleswig-Flensburg'),
('DE', '24850', 'Hüsby', 'Schleswig-Holstein', 'SH', 'Schleswig-Flensburg'),
('DE', '24850', 'Lürschau', 'Schleswig-Holstein', 'SH', 'Schleswig-Flensburg'),
('DE', '24852', 'Eggebek', 'Schleswig-Holstein', 'SH', 'Schleswig-Flensburg'),
('DE', '24852', 'Sollerup', 'Schleswig-Holstein', 'SH', 'Schleswig-Flensburg'),
('DE', '24852', 'Langstedt', 'Schleswig-Holstein', 'SH', 'Schleswig-Flensburg'),
('DE', '24852', 'Süderhackstedt', 'Schleswig-Holstein', 'SH', 'Schleswig-Flensburg'),
('DE', '24855', 'Bollingstedt', 'Schleswig-Holstein', 'SH', 'Schleswig-Flensburg'),
('DE', '24855', 'Jübek', 'Schleswig-Holstein', 'SH', 'Schleswig-Flensburg'),
('DE', '24857', 'Fahrdorf', 'Schleswig-Holstein', 'SH', 'Schleswig-Flensburg'),
('DE', '24857', 'Borgwedel', 'Schleswig-Holstein', 'SH', 'Schleswig-Flensburg'),
('DE', '24860', 'Uelsby', 'Schleswig-Holstein', 'SH', 'Schleswig-Flensburg'),
('DE', '24860', 'Böklund', 'Schleswig-Holstein', 'SH', 'Schleswig-Flensburg'),
('DE', '24860', 'Klappholz', 'Schleswig-Holstein', 'SH', 'Schleswig-Flensburg'),
('DE', '24861', 'Bergenhusen', 'Schleswig-Holstein', 'SH', 'Schleswig-Flensburg'),
('DE', '24863', 'Börm', 'Schleswig-Holstein', 'SH', 'Schleswig-Flensburg'),
('DE', '24864', 'Brodersby', 'Schleswig-Holstein', 'SH', 'Schleswig-Flensburg'),
('DE', '24864', 'Goltoft', 'Schleswig-Holstein', 'SH', 'Schleswig-Flensburg'),
('DE', '24866', 'Busdorf', 'Schleswig-Holstein', 'SH', 'Schleswig-Flensburg'),
('DE', '24867', 'Dannewerk', 'Schleswig-Holstein', 'SH', 'Schleswig-Flensburg'),
('DE', '24869', 'Dörpstedt', 'Schleswig-Holstein', 'SH', 'Schleswig-Flensburg'),
('DE', '24870', 'Ellingstedt', 'Schleswig-Holstein', 'SH', 'Schleswig-Flensburg'),
('DE', '24872', 'Groß Rheide', 'Schleswig-Holstein', 'SH', 'Schleswig-Flensburg'),
('DE', '24873', 'Havetoft', 'Schleswig-Holstein', 'SH', 'Schleswig-Flensburg'),
('DE', '24875', 'Havetoftloit', 'Schleswig-Holstein', 'SH', 'Schleswig-Flensburg'),
('DE', '24876', 'Hollingstedt', 'Schleswig-Holstein', 'SH', 'Schleswig-Flensburg'),
('DE', '24878', 'Jagel', 'Schleswig-Holstein', 'SH', 'Schleswig-Flensburg'),
('DE', '24878', 'Lottorf', 'Schleswig-Holstein', 'SH', 'Schleswig-Flensburg'),
('DE', '24879', 'Idstedt', 'Schleswig-Holstein', 'SH', 'Schleswig-Flensburg'),
('DE', '24879', 'Neuberend', 'Schleswig-Holstein', 'SH', 'Schleswig-Flensburg'),
('DE', '24881', 'Nübel', 'Schleswig-Holstein', 'SH', 'Schleswig-Flensburg'),
('DE', '24882', 'Schaalby', 'Schleswig-Holstein', 'SH', 'Schleswig-Flensburg'),
('DE', '24884', 'Selk', 'Schleswig-Holstein', 'SH', 'Schleswig-Flensburg'),
('DE', '24884', 'Geltorf', 'Schleswig-Holstein', 'SH', 'Schleswig-Flensburg'),
('DE', '24885', 'Sieverstedt', 'Schleswig-Holstein', 'SH', 'Schleswig-Flensburg'),
('DE', '24887', 'Silberstedt', 'Schleswig-Holstein', 'SH', 'Schleswig-Flensburg'),
('DE', '24888', 'Steinfeld', 'Schleswig-Holstein', 'SH', 'Schleswig-Flensburg'),
('DE', '24888', 'Loit', 'Schleswig-Holstein', 'SH', 'Schleswig-Flensburg'),
('DE', '24890', 'Süderfahrenstedt', 'Schleswig-Holstein', 'SH', 'Schleswig-Flensburg'),
('DE', '24890', 'Stolk', 'Schleswig-Holstein', 'SH', 'Schleswig-Flensburg'),
('DE', '24891', 'Struxdorf', 'Schleswig-Holstein', 'SH', 'Schleswig-Flensburg'),
('DE', '24891', 'Schnarup-Thumby', 'Schleswig-Holstein', 'SH', 'Schleswig-Flensburg'),
('DE', '24893', 'Taarstedt', 'Schleswig-Holstein', 'SH', 'Schleswig-Flensburg'),
('DE', '24894', 'Twedt', 'Schleswig-Holstein', 'SH', 'Schleswig-Flensburg'),
('DE', '24894', 'Tolk', 'Schleswig-Holstein', 'SH', 'Schleswig-Flensburg'),
('DE', '24896', 'Treia', 'Schleswig-Holstein', 'SH', 'Schleswig-Flensburg'),
('DE', '24897', 'Ulsnis', 'Schleswig-Holstein', 'SH', 'Schleswig-Flensburg'),
('DE', '24899', 'Wohlde', 'Schleswig-Holstein', 'SH', 'Schleswig-Flensburg'),
('DE', '24937', 'Flensburg', 'Schleswig-Holstein', 'SH', 'Flensburg, Stadt'),
('DE', '24939', 'Flensburg', 'Schleswig-Holstein', 'SH', 'Flensburg, Stadt'),
('DE', '24941', 'Flensburg', 'Schleswig-Holstein', 'SH', 'Kreisfreie Stadt Flensburg'),
('DE', '24941', 'Jarplund-Weding', 'Schleswig-Holstein', 'SH', 'Landkreis Schleswig-Flensburg'),
('DE', '24943', 'Flensburg', 'Schleswig-Holstein', 'SH', 'Schleswig-Flensburg'),
('DE', '24943', 'Tastrup', 'Schleswig-Holstein', 'SH', 'Schleswig-Flensburg'),
('DE', '24944', 'Flensburg', 'Schleswig-Holstein', 'SH', 'Flensburg, Stadt'),
('DE', '24955', 'Harrislee', 'Schleswig-Holstein', 'SH', 'Schleswig-Flensburg'),
('DE', '24960', 'Munkbrarup', 'Schleswig-Holstein', 'SH', 'Schleswig-Flensburg'),
('DE', '24960', 'Glücksburg', 'Schleswig-Holstein', 'SH', 'Schleswig-Flensburg'),
('DE', '24963', 'Tarp', 'Schleswig-Holstein', 'SH', 'Schleswig-Flensburg'),
('DE', '24963', 'Jerrishoe', 'Schleswig-Holstein', 'SH', 'Schleswig-Flensburg'),
('DE', '24966', 'Sörup', 'Schleswig-Holstein', 'SH', 'Schleswig-Flensburg'),
('DE', '24969', 'Großenwiehe', 'Schleswig-Holstein', 'SH', 'Schleswig-Flensburg'),
('DE', '24969', 'Lindewitt', 'Schleswig-Holstein', 'SH', 'Schleswig-Flensburg'),
('DE', '24972', 'Steinberg', 'Schleswig-Holstein', 'SH', 'Schleswig-Flensburg'),
('DE', '24972', 'Steinbergkirche', 'Schleswig-Holstein', 'SH', 'Schleswig-Flensburg'),
('DE', '24972', 'Quern', 'Schleswig-Holstein', 'SH', 'Schleswig-Flensburg'),
('DE', '24975', 'Maasbüll', 'Schleswig-Holstein', 'SH', 'Schleswig-Flensburg'),
('DE', '24975', 'Husby', 'Schleswig-Holstein', 'SH', 'Schleswig-Flensburg'),
('DE', '24975', 'Hürup', 'Schleswig-Holstein', 'SH', 'Schleswig-Flensburg'),
('DE', '24975', 'Ausacker', 'Schleswig-Holstein', 'SH', 'Schleswig-Flensburg'),
('DE', '24976', 'Handewitt', 'Schleswig-Holstein', 'SH', 'Schleswig-Flensburg'),
('DE', '24977', 'Grundhof', 'Schleswig-Holstein', 'SH', 'Schleswig-Flensburg'),
('DE', '24977', 'Langballig', 'Schleswig-Holstein', 'SH', 'Schleswig-Flensburg'),
('DE', '24977', 'Westerholz', 'Schleswig-Holstein', 'SH', 'Schleswig-Flensburg'),
('DE', '24977', 'Ringsberg', 'Schleswig-Holstein', 'SH', 'Schleswig-Flensburg'),
('DE', '24980', 'Wallsbüll', 'Schleswig-Holstein', 'SH', 'Schleswig-Flensburg'),
('DE', '24980', 'Schafflund', 'Schleswig-Holstein', 'SH', 'Schleswig-Flensburg'),
('DE', '24980', 'Hörup', 'Schleswig-Holstein', 'SH', 'Schleswig-Flensburg'),
('DE', '24980', 'Nordhackstedt', 'Schleswig-Holstein', 'SH', 'Schleswig-Flensburg'),
('DE', '24980', 'Meyn', 'Schleswig-Holstein', 'SH', 'Schleswig-Flensburg'),
('DE', '24983', 'Handewitt', 'Schleswig-Holstein', 'SH', 'Schleswig-Flensburg'),
('DE', '24986', 'Rüde', 'Schleswig-Holstein', 'SH', 'Schleswig-Flensburg'),
('DE', '24986', 'Satrup', 'Schleswig-Holstein', 'SH', 'Schleswig-Flensburg'),
('DE', '24988', 'Oeversee', 'Schleswig-Holstein', 'SH', 'Schleswig-Flensburg'),
('DE', '24988', 'Sankelmark', 'Schleswig-Holstein', 'SH', 'Schleswig-Flensburg'),
('DE', '24989', 'Dollerup', 'Schleswig-Holstein', 'SH', 'Schleswig-Flensburg'),
('DE', '24991', 'Freienwill', 'Schleswig-Holstein', 'SH', 'Schleswig-Flensburg'),
('DE', '24991', 'Großsolt', 'Schleswig-Holstein', 'SH', 'Schleswig-Flensburg'),
('DE', '24992', 'Janneby', 'Schleswig-Holstein', 'SH', 'Schleswig-Flensburg'),
('DE', '24992', 'Jörl', 'Schleswig-Holstein', 'SH', 'Schleswig-Flensburg'),
('DE', '24994', 'Weesby', 'Schleswig-Holstein', 'SH', 'Schleswig-Flensburg'),
('DE', '24994', 'Osterby', 'Schleswig-Holstein', 'SH', 'Schleswig-Flensburg'),
('DE', '24994', 'Jardelund', 'Schleswig-Holstein', 'SH', 'Schleswig-Flensburg'),
('DE', '24994', 'Holt', 'Schleswig-Holstein', 'SH', 'Schleswig-Flensburg'),
('DE', '24994', 'Böxlund', 'Schleswig-Holstein', 'SH', 'Schleswig-Flensburg'),
('DE', '24994', 'Medelby', 'Schleswig-Holstein', 'SH', 'Schleswig-Flensburg'),
('DE', '24996', 'Sterup', 'Schleswig-Holstein', 'SH', 'Schleswig-Flensburg'),
('DE', '24996', 'Ahneby', 'Schleswig-Holstein', 'SH', 'Schleswig-Flensburg'),
('DE', '24997', 'Wanderup', 'Schleswig-Holstein', 'SH', 'Schleswig-Flensburg'),
('DE', '24999', 'Wees', 'Schleswig-Holstein', 'SH', 'Schleswig-Flensburg'),
('DE', '25335', 'Altenmoor', 'Schleswig-Holstein', 'SH', 'Pinneberg'),
('DE', '25335', 'Elmshorn', 'Schleswig-Holstein', 'SH', 'Pinneberg'),
('DE', '25335', 'Bokholt-Hanredder', 'Schleswig-Holstein', 'SH', 'Pinneberg'),
('DE', '25335', 'Raa-Besenbek', 'Schleswig-Holstein', 'SH', 'Pinneberg'),
('DE', '25335', 'Neuendorf bei Elmshorn', 'Schleswig-Holstein', 'SH', 'Pinneberg'),
('DE', '25336', 'Elmshorn', 'Schleswig-Holstein', 'SH', 'Pinneberg'),
('DE', '25336', 'Klein Nordende', 'Schleswig-Holstein', 'SH', 'Pinneberg'),
('DE', '25337', 'Elmshorn', 'Schleswig-Holstein', 'SH', 'Pinneberg'),
('DE', '25337', 'Kölln-Reisiek', 'Schleswig-Holstein', 'SH', 'Pinneberg'),
('DE', '25337', 'Seeth-Ekholt', 'Schleswig-Holstein', 'SH', 'Pinneberg'),
('DE', '25348', 'Engelbrechtsche Wildnis', 'Schleswig-Holstein', 'SH', 'Steinburg'),
('DE', '25348', 'Glückstadt', 'Schleswig-Holstein', 'SH', 'Steinburg'),
('DE', '25348', 'Blomesche Wildnis', 'Schleswig-Holstein', 'SH', 'Steinburg'),
('DE', '25355', 'Bullenkuhlen', 'Schleswig-Holstein', 'SH', 'Pinneberg'),
('DE', '25355', 'Heede', 'Schleswig-Holstein', 'SH', 'Pinneberg'),
('DE', '25355', 'Lutzhorn', 'Schleswig-Holstein', 'SH', 'Pinneberg'),
('DE', '25355', 'Groß Offenseth-Aspern', 'Schleswig-Holstein', 'SH', 'Pinneberg'),
('DE', '25355', 'Barmstedt', 'Schleswig-Holstein', 'SH', 'Pinneberg'),
('DE', '25355', 'Bevern', 'Schleswig-Holstein', 'SH', 'Pinneberg'),
('DE', '25358', 'Horst (Holstein)', 'Schleswig-Holstein', 'SH', 'Steinburg'),
('DE', '25358', 'Hohenfelde', 'Schleswig-Holstein', 'SH', 'Steinburg'),
('DE', '25358', 'Sommerland', 'Schleswig-Holstein', 'SH', 'Steinburg'),
('DE', '25361', 'Elskop', 'Schleswig-Holstein', 'SH', 'Steinburg'),
('DE', '25361', 'Süderau', 'Schleswig-Holstein', 'SH', 'Steinburg'),
('DE', '25361', 'Krempe', 'Schleswig-Holstein', 'SH', 'Steinburg'),
('DE', '25361', 'Grevenkop', 'Schleswig-Holstein', 'SH', 'Steinburg'),
('DE', '25364', 'Westerhorn', 'Schleswig-Holstein', 'SH', 'Pinneberg'),
('DE', '25364', 'Bokel', 'Schleswig-Holstein', 'SH', 'Pinneberg'),
('DE', '25364', 'Osterhorn', 'Schleswig-Holstein', 'SH', 'Pinneberg'),
('DE', '25364', 'Brande-Hörnerkirchen', 'Schleswig-Holstein', 'SH', 'Pinneberg'),
('DE', '25365', 'Klein Offenseth-Sparrieshoop', 'Schleswig-Holstein', 'SH', 'Pinneberg'),
('DE', '25368', 'Kiebitzreihe', 'Schleswig-Holstein', 'SH', 'Steinburg'),
('DE', '25370', 'Seester', 'Schleswig-Holstein', 'SH', 'Pinneberg'),
('DE', '25371', 'Seestermühe', 'Schleswig-Holstein', 'SH', 'Pinneberg'),
('DE', '25373', 'Ellerhoop', 'Schleswig-Holstein', 'SH', 'Pinneberg'),
('DE', '25376', 'Borsfleth', 'Schleswig-Holstein', 'SH', 'Steinburg'),
('DE', '25376', 'Krempdorf', 'Schleswig-Holstein', 'SH', 'Steinburg'),
('DE', '25377', 'Kollmar', 'Schleswig-Holstein', 'SH', 'Steinburg'),
('DE', '25379', 'Herzhorn', 'Schleswig-Holstein', 'SH', 'Steinburg'),
('DE', '25404', 'Pinneberg', 'Schleswig-Holstein', 'SH', 'Landkreis Pinneberg'),
('DE', '25421', 'Pinneberg', 'Schleswig-Holstein', 'SH', 'Pinneberg'),
('DE', '25429', 'Uetersen', 'Schleswig-Holstein', 'SH', 'Pinneberg'),
('DE', '25436', 'Uetersen', 'Schleswig-Holstein', 'SH', 'Pinneberg'),
('DE', '25436', 'Heidgraben', 'Schleswig-Holstein', 'SH', 'Pinneberg'),
('DE', '25436', 'Tornesch', 'Schleswig-Holstein', 'SH', 'Pinneberg'),
('DE', '25436', 'Moorrege', 'Schleswig-Holstein', 'SH', 'Pinneberg'),
('DE', '25436', 'Neuendeich', 'Schleswig-Holstein', 'SH', 'Pinneberg'),
('DE', '25436', 'Groß Nordende', 'Schleswig-Holstein', 'SH', 'Pinneberg'),
('DE', '25437', 'Tornesch', 'Schleswig-Holstein', 'SH', 'Pinneberg'),
('DE', '25451', 'Quickborn', 'Schleswig-Holstein', 'SH', 'Pinneberg'),
('DE', '25462', 'Rellingen', 'Schleswig-Holstein', 'SH', 'Pinneberg'),
('DE', '25469', 'Halstenbek', 'Schleswig-Holstein', 'SH', 'Pinneberg'),
('DE', '25474', 'Ellerbek', 'Schleswig-Holstein', 'SH', 'Pinneberg'),
('DE', '25474', 'Hasloh', 'Schleswig-Holstein', 'SH', 'Pinneberg'),
('DE', '25474', 'Bönningstedt', 'Schleswig-Holstein', 'SH', 'Pinneberg'),
('DE', '25479', 'Ellerau', 'Schleswig-Holstein', 'SH', 'Segeberg'),
('DE', '25482', 'Appen', 'Schleswig-Holstein', 'SH', 'Pinneberg'),
('DE', '25485', 'Hemdingen', 'Schleswig-Holstein', 'SH', 'Pinneberg'),
('DE', '25485', 'Langeln', 'Schleswig-Holstein', 'SH', 'Pinneberg'),
('DE', '25485', 'Bilsen', 'Schleswig-Holstein', 'SH', 'Pinneberg'),
('DE', '25486', 'Alveslohe', 'Schleswig-Holstein', 'SH', 'Segeberg'),
('DE', '25488', 'Holm', 'Schleswig-Holstein', 'SH', 'Pinneberg'),
('DE', '25489', 'Haselau', 'Schleswig-Holstein', 'SH', 'Pinneberg'),
('DE', '25489', 'Haseldorf', 'Schleswig-Holstein', 'SH', 'Pinneberg'),
('DE', '25491', 'Hetlingen', 'Schleswig-Holstein', 'SH', 'Pinneberg'),
('DE', '25492', 'Heist', 'Schleswig-Holstein', 'SH', 'Pinneberg'),
('DE', '25494', 'Borstel-Hohenraden', 'Schleswig-Holstein', 'SH', 'Pinneberg'),
('DE', '25495', 'Kummerfeld', 'Schleswig-Holstein', 'SH', 'Pinneberg'),
('DE', '25497', 'Prisdorf', 'Schleswig-Holstein', 'SH', 'Pinneberg'),
('DE', '25499', 'Tangstedt', 'Schleswig-Holstein', 'SH', 'Pinneberg'),
('DE', '25524', 'Oelixdorf', 'Schleswig-Holstein', 'SH', 'Steinburg'),
('DE', '25524', 'Itzehoe', 'Schleswig-Holstein', 'SH', 'Steinburg'),
('DE', '25524', 'Bekmünde', 'Schleswig-Holstein', 'SH', 'Steinburg'),
('DE', '25524', 'Kollmoor', 'Schleswig-Holstein', 'SH', 'Steinburg'),
('DE', '25524', 'Heiligenstedtenerkamp', 'Schleswig-Holstein', 'SH', 'Steinburg'),
('DE', '25524', 'Heiligenstedten', 'Schleswig-Holstein', 'SH', 'Steinburg'),
('DE', '25524', 'Breitenburg', 'Schleswig-Holstein', 'SH', 'Steinburg'),
('DE', '25541', 'Brunsbüttel', 'Schleswig-Holstein', 'SH', 'Dithmarschen'),
('DE', '25548', 'Kellinghusen', 'Schleswig-Holstein', 'SH', 'Steinburg'),
('DE', '25548', 'Rosdorf', 'Schleswig-Holstein', 'SH', 'Steinburg'),
('DE', '25548', 'Mühlenbarbek', 'Schleswig-Holstein', 'SH', 'Steinburg'),
('DE', '25548', 'Oeschebüttel', 'Schleswig-Holstein', 'SH', 'Steinburg'),
('DE', '25548', 'Störkathen', 'Schleswig-Holstein', 'SH', 'Steinburg'),
('DE', '25548', 'Wittenbergen', 'Schleswig-Holstein', 'SH', 'Steinburg'),
('DE', '25548', 'Auufer', 'Schleswig-Holstein', 'SH', 'Steinburg'),
('DE', '25551', 'Winseldorf', 'Schleswig-Holstein', 'SH', 'Steinburg'),
('DE', '25551', 'Silzen', 'Schleswig-Holstein', 'SH', 'Steinburg'),
('DE', '25551', 'Hohenlockstedt', 'Schleswig-Holstein', 'SH', 'Steinburg'),
('DE', '25551', 'Peissen', 'Schleswig-Holstein', 'SH', 'Steinburg'),
('DE', '25551', 'Schlotfeld', 'Schleswig-Holstein', 'SH', 'Steinburg'),
('DE', '25551', 'Lohbarbek', 'Schleswig-Holstein', 'SH', 'Steinburg'),
('DE', '25551', 'Lockstedt', 'Schleswig-Holstein', 'SH', 'Steinburg'),
('DE', '25554', 'Wilster', 'Schleswig-Holstein', 'SH', 'Steinburg'),
('DE', '25554', 'Neuendorf-Sachsenbande', 'Schleswig-Holstein', 'SH', 'Steinburg'),
('DE', '25554', 'Dammfleth', 'Schleswig-Holstein', 'SH', 'Steinburg'),
('DE', '25554', 'Bekdorf', 'Schleswig-Holstein', 'SH', 'Steinburg'),
('DE', '25554', 'Moorhusen', 'Schleswig-Holstein', 'SH', 'Steinburg'),
('DE', '25554', 'Landrecht', 'Schleswig-Holstein', 'SH', 'Steinburg'),
('DE', '25554', 'Stördorf', 'Schleswig-Holstein', 'SH', 'Steinburg'),
('DE', '25554', 'Krummendiek', 'Schleswig-Holstein', 'SH', 'Steinburg'),
('DE', '25554', 'Kleve', 'Schleswig-Holstein', 'SH', 'Steinburg'),
('DE', '25554', 'Neuendorf-Sachsenbande Neuendorf bei Wilster', 'Schleswig-Holstein', 'SH', 'Steinburg'),
('DE', '25554', 'Neuendorf-Sachsenbande Sachsenbande', 'Schleswig-Holstein', 'SH', 'Steinburg'),
('DE', '25554', 'Nortorf', 'Schleswig-Holstein', 'SH', 'Steinburg'),
('DE', '25557', 'Gokels', 'Schleswig-Holstein', 'SH', 'Rendsburg-Eckernförde'),
('DE', '25557', 'Oldenbüttel', 'Schleswig-Holstein', 'SH', 'Rendsburg-Eckernförde'),
('DE', '25557', 'Thaden', 'Schleswig-Holstein', 'SH', 'Rendsburg-Eckernförde'),
('DE', '25557', 'Beldorf', 'Schleswig-Holstein', 'SH', 'Rendsburg-Eckernförde'),
('DE', '25557', 'Steenfeld', 'Schleswig-Holstein', 'SH', 'Rendsburg-Eckernförde'),
('DE', '25557', 'Bendorf', 'Schleswig-Holstein', 'SH', 'Rendsburg-Eckernförde'),
('DE', '25557', 'Seefeld', 'Schleswig-Holstein', 'SH', 'Rendsburg-Eckernförde'),
('DE', '25557', 'Hanerau-Hademarschen', 'Schleswig-Holstein', 'SH', 'Rendsburg-Eckernförde'),
('DE', '25560', 'Schenefeld', 'Schleswig-Holstein', 'SH', 'Steinburg'),
('DE', '25560', 'Siezbüttel', 'Schleswig-Holstein', 'SH', 'Steinburg'),
('DE', '25560', 'Warringholz', 'Schleswig-Holstein', 'SH', 'Steinburg'),
('DE', '25560', 'Oldenborstel', 'Schleswig-Holstein', 'SH', 'Steinburg'),
('DE', '25560', 'Bokhorst', 'Schleswig-Holstein', 'SH', 'Steinburg'),
('DE', '25560', 'Agethorst', 'Schleswig-Holstein', 'SH', 'Steinburg'),
('DE', '25560', 'Puls', 'Schleswig-Holstein', 'SH', 'Steinburg'),
('DE', '25560', 'Pöschendorf', 'Schleswig-Holstein', 'SH', 'Steinburg'),
('DE', '25560', 'Kaisborstel', 'Schleswig-Holstein', 'SH', 'Steinburg'),
('DE', '25560', 'Aasbüttel', 'Schleswig-Holstein', 'SH', 'Steinburg'),
('DE', '25560', 'Hadenfeld', 'Schleswig-Holstein', 'SH', 'Steinburg'),
('DE', '25563', 'Wrist', 'Schleswig-Holstein', 'SH', 'Steinburg'),
('DE', '25563', 'Hingstheide', 'Schleswig-Holstein', 'SH', 'Steinburg'),
('DE', '25563', 'Wulfsmoor', 'Schleswig-Holstein', 'SH', 'Steinburg'),
('DE', '25563', 'Föhrden-Barl', 'Schleswig-Holstein', 'SH', 'Steinburg'),
('DE', '25563', 'Quarnstedt', 'Schleswig-Holstein', 'SH', 'Steinburg'),
('DE', '25566', 'Rethwisch', 'Schleswig-Holstein', 'SH', 'Steinburg'),
('DE', '25566', 'Lägerdorf', 'Schleswig-Holstein', 'SH', 'Steinburg'),
('DE', '25569', 'Kremperheide', 'Schleswig-Holstein', 'SH', 'Steinburg'),
('DE', '25569', 'Bahrenfleth', 'Schleswig-Holstein', 'SH', 'Steinburg'),
('DE', '25569', 'Krempermoor', 'Schleswig-Holstein', 'SH', 'Steinburg'),
('DE', '25569', 'Hodorf', 'Schleswig-Holstein', 'SH', 'Steinburg'),
('DE', '25572', 'Ecklak', 'Schleswig-Holstein', 'SH', 'Steinburg'),
('DE', '25572', 'Kudensee', 'Schleswig-Holstein', 'SH', 'Steinburg'),
('DE', '25572', 'Büttel', 'Schleswig-Holstein', 'SH', 'Steinburg'),
('DE', '25572', 'Aebtissinwisch', 'Schleswig-Holstein', 'SH', 'Steinburg'),
('DE', '25572', 'Landscheide', 'Schleswig-Holstein', 'SH', 'Steinburg'),
('DE', '25572', 'Sankt Margarethen', 'Schleswig-Holstein', 'SH', 'Steinburg'),
('DE', '25573', 'Beidenfleth', 'Schleswig-Holstein', 'SH', 'Steinburg'),
('DE', '25575', 'Beringstedt', 'Schleswig-Holstein', 'SH', 'Rendsburg-Eckernförde'),
('DE', '25576', 'Brokdorf', 'Schleswig-Holstein', 'SH', 'Steinburg'),
('DE', '25578', 'Dägeling', 'Schleswig-Holstein', 'SH', 'Steinburg'),
('DE', '25578', 'Neuenbrook', 'Schleswig-Holstein', 'SH', 'Steinburg'),
('DE', '25579', 'Rade', 'Schleswig-Holstein', 'SH', 'Steinburg'),
('DE', '25579', 'Fitzbek', 'Schleswig-Holstein', 'SH', 'Steinburg'),
('DE', '25581', 'Hennstedt', 'Schleswig-Holstein', 'SH', 'Steinburg'),
('DE', '25581', 'Poyenberg', 'Schleswig-Holstein', 'SH', 'Steinburg'),
('DE', '25582', 'Drage', 'Schleswig-Holstein', 'SH', 'Steinburg'),
('DE', '25582', 'Kaaks', 'Schleswig-Holstein', 'SH', 'Steinburg'),
('DE', '25582', 'Hohenaspe', 'Schleswig-Holstein', 'SH', 'Steinburg'),
('DE', '25582', 'Looft', 'Schleswig-Holstein', 'SH', 'Steinburg'),
('DE', '25584', 'Holstenniendorf', 'Schleswig-Holstein', 'SH', 'Steinburg'),
('DE', '25584', 'Besdorf', 'Schleswig-Holstein', 'SH', 'Steinburg'),
('DE', '25585', 'Tackesdorf', 'Schleswig-Holstein', 'SH', 'Rendsburg-Eckernförde'),
('DE', '25585', 'Lütjenwestedt', 'Schleswig-Holstein', 'SH', 'Rendsburg-Eckernförde'),
('DE', '25587', 'Münsterdorf', 'Schleswig-Holstein', 'SH', 'Steinburg'),
('DE', '25588', 'Mehlbek', 'Schleswig-Holstein', 'SH', 'Steinburg'),
('DE', '25588', 'Huje', 'Schleswig-Holstein', 'SH', 'Steinburg'),
('DE', '25588', 'Oldendorf', 'Schleswig-Holstein', 'SH', 'Steinburg'),
('DE', '25590', 'Osterstedt', 'Schleswig-Holstein', 'SH', 'Rendsburg-Eckernförde'),
('DE', '25591', 'Ottenbüttel', 'Schleswig-Holstein', 'SH', 'Steinburg'),
('DE', '25593', 'Christinenthal', 'Schleswig-Holstein', 'SH', 'Steinburg'),
('DE', '25593', 'Reher', 'Schleswig-Holstein', 'SH', 'Steinburg'),
('DE', '25594', 'Vaalermoor', 'Schleswig-Holstein', 'SH', 'Steinburg'),
('DE', '25594', 'Nutteln', 'Schleswig-Holstein', 'SH', 'Steinburg'),
('DE', '25594', 'Vaale', 'Schleswig-Holstein', 'SH', 'Steinburg'),
('DE', '25596', 'Bokelrehm', 'Schleswig-Holstein', 'SH', 'Steinburg'),
('DE', '25596', 'Gribbohm', 'Schleswig-Holstein', 'SH', 'Steinburg'),
('DE', '25596', 'Nienbüttel', 'Schleswig-Holstein', 'SH', 'Steinburg'),
('DE', '25596', 'Wacken', 'Schleswig-Holstein', 'SH', 'Steinburg'),
('DE', '25597', 'Moordiek', 'Schleswig-Holstein', 'SH', 'Steinburg'),
('DE', '25597', 'Westermoor', 'Schleswig-Holstein', 'SH', 'Steinburg'),
('DE', '25597', 'Moordorf', 'Schleswig-Holstein', 'SH', 'Steinburg'),
('DE', '25597', 'Kronsmoor', 'Schleswig-Holstein', 'SH', 'Steinburg'),
('DE', '25597', 'Breitenberg', 'Schleswig-Holstein', 'SH', 'Steinburg'),
('DE', '25599', 'Wewelsfleth', 'Schleswig-Holstein', 'SH', 'Steinburg'),
('DE', '25693', 'Sankt Michaelisdonn', 'Schleswig-Holstein', 'SH', 'Dithmarschen'),
('DE', '25693', 'Volsemenhusen', 'Schleswig-Holstein', 'SH', 'Dithmarschen'),
('DE', '25693', 'Gudendorf', 'Schleswig-Holstein', 'SH', 'Dithmarschen'),
('DE', '25693', 'Trennewurth', 'Schleswig-Holstein', 'SH', 'Dithmarschen'),
('DE', '25704', 'Nindorf', 'Schleswig-Holstein', 'SH', 'Dithmarschen'),
('DE', '25704', 'Meldorf', 'Schleswig-Holstein', 'SH', 'Dithmarschen'),
('DE', '25704', 'Bargenstedt', 'Schleswig-Holstein', 'SH', 'Dithmarschen'),
('DE', '25704', 'Epenwöhrden', 'Schleswig-Holstein', 'SH', 'Dithmarschen'),
('DE', '25704', 'Elpersbüttel', 'Schleswig-Holstein', 'SH', 'Dithmarschen'),
('DE', '25704', 'Nordermeldorf', 'Schleswig-Holstein', 'SH', 'Dithmarschen'),
('DE', '25704', 'Wolmersdorf', 'Schleswig-Holstein', 'SH', 'Dithmarschen'),
('DE', '25709', 'Marnerdeich', 'Schleswig-Holstein', 'SH', 'Dithmarschen'),
('DE', '25709', 'Diekhusen-Fahrstedt', 'Schleswig-Holstein', 'SH', 'Dithmarschen'),
('DE', '25709', 'Helse', 'Schleswig-Holstein', 'SH', 'Dithmarschen'),
('DE', '25709', 'Kaiser-Wilhelm-Koog', 'Schleswig-Holstein', 'SH', 'Dithmarschen'),
('DE', '25709', 'Kronprinzenkoog', 'Schleswig-Holstein', 'SH', 'Dithmarschen'),
('DE', '25709', 'Marne', 'Schleswig-Holstein', 'SH', 'Dithmarschen'),
('DE', '25712', 'Burg (Dithmarschen)', 'Schleswig-Holstein', 'SH', 'Dithmarschen'),
('DE', '25712', 'Brickeln', 'Schleswig-Holstein', 'SH', 'Dithmarschen'),
('DE', '25712', 'Buchholz', 'Schleswig-Holstein', 'SH', 'Dithmarschen'),
('DE', '25712', 'Hochdonn', 'Schleswig-Holstein', 'SH', 'Dithmarschen'),
('DE', '25712', 'Großenrade', 'Schleswig-Holstein', 'SH', 'Dithmarschen'),
('DE', '25712', 'Kuden', 'Schleswig-Holstein', 'SH', 'Dithmarschen'),
('DE', '25712', 'Quickborn', 'Schleswig-Holstein', 'SH', 'Dithmarschen'),
('DE', '25715', 'Eddelak', 'Schleswig-Holstein', 'SH', 'Dithmarschen'),
('DE', '25715', 'Averlak', 'Schleswig-Holstein', 'SH', 'Dithmarschen'),
('DE', '25715', 'Dingen', 'Schleswig-Holstein', 'SH', 'Dithmarschen'),
('DE', '25715', 'Ramhusen', 'Schleswig-Holstein', 'SH', 'Dithmarschen'),
('DE', '25718', 'Friedrichskoog', 'Schleswig-Holstein', 'SH', 'Dithmarschen'),
('DE', '25719', 'Barlt', 'Schleswig-Holstein', 'SH', 'Dithmarschen'),
('DE', '25719', 'Busenwurth', 'Schleswig-Holstein', 'SH', 'Dithmarschen'),
('DE', '25721', 'Eggstedt', 'Schleswig-Holstein', 'SH', 'Dithmarschen'),
('DE', '25724', 'Neufeld', 'Schleswig-Holstein', 'SH', 'Dithmarschen'),
('DE', '25724', 'Neufelderkoog', 'Schleswig-Holstein', 'SH', 'Dithmarschen'),
('DE', '25724', 'Schmedeswurth', 'Schleswig-Holstein', 'SH', 'Dithmarschen'),
('DE', '25725', 'Schafstedt', 'Schleswig-Holstein', 'SH', 'Dithmarschen'),
('DE', '25725', 'Bornholt', 'Schleswig-Holstein', 'SH', 'Dithmarschen'),
('DE', '25727', 'Krumstedt', 'Schleswig-Holstein', 'SH', 'Dithmarschen'),
('DE', '25727', 'Frestedt', 'Schleswig-Holstein', 'SH', 'Dithmarschen'),
('DE', '25727', 'Süderhastedt', 'Schleswig-Holstein', 'SH', 'Dithmarschen'),
('DE', '25729', 'Windbergen', 'Schleswig-Holstein', 'SH', 'Dithmarschen'),
('DE', '25746', 'Ostrohe', 'Schleswig-Holstein', 'SH', 'Dithmarschen'),
('DE', '25746', 'Wesseln', 'Schleswig-Holstein', 'SH', 'Dithmarschen'),
('DE', '25746', 'Lohe-Rickelshof', 'Schleswig-Holstein', 'SH', 'Dithmarschen'),
('DE', '25746', 'Norderwöhrden', 'Schleswig-Holstein', 'SH', 'Dithmarschen'),
('DE', '25746', 'Heide', 'Schleswig-Holstein', 'SH', 'Dithmarschen'),
('DE', '25761', 'Büsum', 'Schleswig-Holstein', 'SH', 'Dithmarschen'),
('DE', '25761', 'Oesterdeichstrich', 'Schleswig-Holstein', 'SH', 'Dithmarschen'),
('DE', '25761', 'Büsumer Deichhausen', 'Schleswig-Holstein', 'SH', 'Dithmarschen'),
('DE', '25761', 'Hedwigenkoog', 'Schleswig-Holstein', 'SH', 'Dithmarschen'),
('DE', '25761', 'Westerdeichstrich', 'Schleswig-Holstein', 'SH', 'Dithmarschen'),
('DE', '25761', 'Warwerort', 'Schleswig-Holstein', 'SH', 'Dithmarschen'),
('DE', '25764', 'Oesterwurth', 'Schleswig-Holstein', 'SH', 'Dithmarschen'),
('DE', '25764', 'Süderdeich', 'Schleswig-Holstein', 'SH', 'Dithmarschen'),
('DE', '25764', 'Reinsbüttel', 'Schleswig-Holstein', 'SH', 'Dithmarschen'),
('DE', '25764', 'Friedrichsgabekoog', 'Schleswig-Holstein', 'SH', 'Dithmarschen'),
('DE', '25764', 'Hillgroven', 'Schleswig-Holstein', 'SH', 'Dithmarschen'),
('DE', '25764', 'Norddeich', 'Schleswig-Holstein', 'SH', 'Dithmarschen'),
('DE', '25764', 'Hellschen-Heringsand-Unterschaar', 'Schleswig-Holstein', 'SH', 'Dithmarschen'),
('DE', '25764', 'Wesselburener-Deichhausen', 'Schleswig-Holstein', 'SH', 'Dithmarschen'),
('DE', '25764', 'Schülp', 'Schleswig-Holstein', 'SH', 'Dithmarschen'),
('DE', '25764', 'Wesselburen', 'Schleswig-Holstein', 'SH', 'Dithmarschen'),
('DE', '25764', 'Wesselburenerkoog', 'Schleswig-Holstein', 'SH', 'Dithmarschen'),
('DE', '25767', 'Osterrade', 'Schleswig-Holstein', 'SH', 'Dithmarschen'),
('DE', '25767', 'Bunsoh', 'Schleswig-Holstein', 'SH', 'Dithmarschen'),
('DE', '25767', 'Albersdorf', 'Schleswig-Holstein', 'SH', 'Dithmarschen'),
('DE', '25767', 'Tensbüttel-Röst', 'Schleswig-Holstein', 'SH', 'Dithmarschen'),
('DE', '25767', 'Offenbüttel', 'Schleswig-Holstein', 'SH', 'Dithmarschen'),
('DE', '25767', 'Wennbüttel', 'Schleswig-Holstein', 'SH', 'Dithmarschen'),
('DE', '25767', 'Arkebek', 'Schleswig-Holstein', 'SH', 'Dithmarschen'),
('DE', '25770', 'Lieth', 'Schleswig-Holstein', 'SH', 'Dithmarschen'),
('DE', '25770', 'Hemmingstedt', 'Schleswig-Holstein', 'SH', 'Dithmarschen'),
('DE', '25774', 'Lehe', 'Schleswig-Holstein', 'SH', 'Dithmarschen'),
('DE', '25774', 'Groven', 'Schleswig-Holstein', 'SH', 'Dithmarschen'),
('DE', '25774', 'Lunden', 'Schleswig-Holstein', 'SH', 'Dithmarschen'),
('DE', '25774', 'Krempel', 'Schleswig-Holstein', 'SH', 'Dithmarschen'),
('DE', '25774', 'Karolinenkoog', 'Schleswig-Holstein', 'SH', 'Dithmarschen'),
('DE', '25774', 'Hemme', 'Schleswig-Holstein', 'SH', 'Dithmarschen'),
('DE', '25776', 'Schlichting', 'Schleswig-Holstein', 'SH', 'Dithmarschen'),
('DE', '25776', 'Rehm-Flehde-Bargen', 'Schleswig-Holstein', 'SH', 'Dithmarschen'),
('DE', '25776', 'Sankt Annen', 'Schleswig-Holstein', 'SH', 'Dithmarschen'),
('DE', '25779', 'Süderheistedt', 'Schleswig-Holstein', 'SH', 'Dithmarschen'),
('DE', '25779', 'Norderheistedt', 'Schleswig-Holstein', 'SH', 'Dithmarschen'),
('DE', '25779', 'Wiemerstedt', 'Schleswig-Holstein', 'SH', 'Dithmarschen'),
('DE', '25779', 'Bergewöhrden', 'Schleswig-Holstein', 'SH', 'Dithmarschen'),
('DE', '25779', 'Hennstedt', 'Schleswig-Holstein', 'SH', 'Dithmarschen'),
('DE', '25779', 'Fedderingen', 'Schleswig-Holstein', 'SH', 'Dithmarschen'),
('DE', '25779', 'Kleve', 'Schleswig-Holstein', 'SH', 'Dithmarschen'),
('DE', '25779', 'Hägen', 'Schleswig-Holstein', 'SH', 'Dithmarschen'),
('DE', '25779', 'Glüsing', 'Schleswig-Holstein', 'SH', 'Dithmarschen'),
('DE', '25782', 'Welmbüttel', 'Schleswig-Holstein', 'SH', 'Dithmarschen'),
('DE', '25782', 'Schrum', 'Schleswig-Holstein', 'SH', 'Dithmarschen'),
('DE', '25782', 'Tellingstedt', 'Schleswig-Holstein', 'SH', 'Dithmarschen'),
('DE', '25782', 'Hövede', 'Schleswig-Holstein', 'SH', 'Dithmarschen'),
('DE', '25782', 'Süderdorf', 'Schleswig-Holstein', 'SH', 'Dithmarschen'),
('DE', '25782', 'Schalkholz', 'Schleswig-Holstein', 'SH', 'Dithmarschen'),
('DE', '25782', 'Westerborstel', 'Schleswig-Holstein', 'SH', 'Dithmarschen'),
('DE', '25782', 'Gaushorn', 'Schleswig-Holstein', 'SH', 'Dithmarschen'),
('DE', '25785', 'Nordhastedt', 'Schleswig-Holstein', 'SH', 'Dithmarschen'),
('DE', '25785', 'Odderade', 'Schleswig-Holstein', 'SH', 'Dithmarschen'),
('DE', '25785', 'Sarzbüttel', 'Schleswig-Holstein', 'SH', 'Dithmarschen'),
('DE', '25786', 'Dellstedt', 'Schleswig-Holstein', 'SH', 'Dithmarschen'),
('DE', '25788', 'Hollingstedt', 'Schleswig-Holstein', 'SH', 'Dithmarschen'),
('DE', '25788', 'Delve', 'Schleswig-Holstein', 'SH', 'Dithmarschen'),
('DE', '25788', 'Wallen', 'Schleswig-Holstein', 'SH', 'Dithmarschen'),
('DE', '25791', 'Linden', 'Schleswig-Holstein', 'SH', 'Dithmarschen'),
('DE', '25791', 'Barkenholm', 'Schleswig-Holstein', 'SH', 'Dithmarschen'),
('DE', '25792', 'Strübbel', 'Schleswig-Holstein', 'SH', 'Dithmarschen'),
('DE', '25792', 'Neuenkirchen', 'Schleswig-Holstein', 'SH', 'Dithmarschen'),
('DE', '25794', 'Dörpling', 'Schleswig-Holstein', 'SH', 'Dithmarschen'),
('DE', '25794', 'Pahlen', 'Schleswig-Holstein', 'SH', 'Dithmarschen'),
('DE', '25794', 'Tielenhemme', 'Schleswig-Holstein', 'SH', 'Dithmarschen'),
('DE', '25795', 'Weddingstedt', 'Schleswig-Holstein', 'SH', 'Dithmarschen'),
('DE', '25795', 'Stelle-Wittenwurth', 'Schleswig-Holstein', 'SH', 'Dithmarschen'),
('DE', '25797', 'Wöhrden', 'Schleswig-Holstein', 'SH', 'Dithmarschen'),
('DE', '25799', 'Wrohm', 'Schleswig-Holstein', 'SH', 'Dithmarschen'),
('DE', '25813', 'Husum', 'Schleswig-Holstein', 'SH', 'Nordfriesland'),
('DE', '25813', 'Südermarsch', 'Schleswig-Holstein', 'SH', 'Nordfriesland');
INSERT INTO `plz_tabelle` (`lkz`, `plz`, `ort_stadtteil`, `bundesland`, `blkz`, `kreis`) VALUES
('DE', '25813', 'Simonsberg', 'Schleswig-Holstein', 'SH', 'Nordfriesland'),
('DE', '25813', 'Schwesing', 'Schleswig-Holstein', 'SH', 'Nordfriesland'),
('DE', '25821', 'Breklum', 'Schleswig-Holstein', 'SH', 'Nordfriesland'),
('DE', '25821', 'Almdorf', 'Schleswig-Holstein', 'SH', 'Nordfriesland'),
('DE', '25821', 'Bredstedt', 'Schleswig-Holstein', 'SH', 'Nordfriesland'),
('DE', '25821', 'Reußenköge', 'Schleswig-Holstein', 'SH', 'Nordfriesland'),
('DE', '25821', 'Sönnebüll', 'Schleswig-Holstein', 'SH', 'Nordfriesland'),
('DE', '25821', 'Struckum', 'Schleswig-Holstein', 'SH', 'Nordfriesland'),
('DE', '25821', 'Vollstedt', 'Schleswig-Holstein', 'SH', 'Nordfriesland'),
('DE', '25826', 'Sankt Peter-Ording', 'Schleswig-Holstein', 'SH', 'Nordfriesland'),
('DE', '25832', 'Tönning', 'Schleswig-Holstein', 'SH', 'Nordfriesland'),
('DE', '25832', 'Kotzenbüll', 'Schleswig-Holstein', 'SH', 'Nordfriesland'),
('DE', '25836', 'Vollerwiek', 'Schleswig-Holstein', 'SH', 'Nordfriesland'),
('DE', '25836', 'Poppenbüll', 'Schleswig-Holstein', 'SH', 'Nordfriesland'),
('DE', '25836', 'Grothusenkoog', 'Schleswig-Holstein', 'SH', 'Nordfriesland'),
('DE', '25836', 'Kirchspiel Garding', 'Schleswig-Holstein', 'SH', 'Nordfriesland'),
('DE', '25836', 'Katharinenheerd', 'Schleswig-Holstein', 'SH', 'Nordfriesland'),
('DE', '25836', 'Welt', 'Schleswig-Holstein', 'SH', 'Nordfriesland'),
('DE', '25836', 'Garding', 'Schleswig-Holstein', 'SH', 'Nordfriesland'),
('DE', '25836', 'Osterhever', 'Schleswig-Holstein', 'SH', 'Nordfriesland'),
('DE', '25840', 'Friedrichstadt', 'Schleswig-Holstein', 'SH', 'Nordfriesland'),
('DE', '25840', 'Koldenbüttel', 'Schleswig-Holstein', 'SH', 'Nordfriesland'),
('DE', '25842', 'Lütjenholm', 'Schleswig-Holstein', 'SH', 'Nordfriesland'),
('DE', '25842', 'Langenhorn', 'Schleswig-Holstein', 'SH', 'Nordfriesland'),
('DE', '25842', 'Bargum', 'Schleswig-Holstein', 'SH', 'Nordfriesland'),
('DE', '25842', 'Ockholm', 'Schleswig-Holstein', 'SH', 'Nordfriesland'),
('DE', '25845', 'Elisabeth-Sophien-Koog', 'Schleswig-Holstein', 'SH', 'Nordfriesland'),
('DE', '25845', 'Nordstrand', 'Schleswig-Holstein', 'SH', 'Nordfriesland'),
('DE', '25849', 'Pellworm', 'Schleswig-Holstein', 'SH', 'Nordfriesland'),
('DE', '25850', 'Behrendorf', 'Schleswig-Holstein', 'SH', 'Nordfriesland'),
('DE', '25850', 'Bondelum', 'Schleswig-Holstein', 'SH', 'Nordfriesland'),
('DE', '25852', 'Bordelum', 'Schleswig-Holstein', 'SH', 'Nordfriesland'),
('DE', '25853', 'Drelsdorf', 'Schleswig-Holstein', 'SH', 'Nordfriesland'),
('DE', '25853', 'Bohmstedt', 'Schleswig-Holstein', 'SH', 'Nordfriesland'),
('DE', '25853', 'Ahrenshöft', 'Schleswig-Holstein', 'SH', 'Nordfriesland'),
('DE', '25855', 'Haselund', 'Schleswig-Holstein', 'SH', 'Nordfriesland'),
('DE', '25856', 'Wobbenbüll', 'Schleswig-Holstein', 'SH', 'Nordfriesland'),
('DE', '25856', 'Hattstedt', 'Schleswig-Holstein', 'SH', 'Nordfriesland'),
('DE', '25856', 'Hattstedtermarsch', 'Schleswig-Holstein', 'SH', 'Nordfriesland'),
('DE', '25858', 'Högel', 'Schleswig-Holstein', 'SH', 'Nordfriesland'),
('DE', '25859', 'Hooge', 'Schleswig-Holstein', 'SH', 'Nordfriesland'),
('DE', '25860', 'Olderup', 'Schleswig-Holstein', 'SH', 'Nordfriesland'),
('DE', '25860', 'Arlewatt', 'Schleswig-Holstein', 'SH', 'Nordfriesland'),
('DE', '25860', 'Horstedt', 'Schleswig-Holstein', 'SH', 'Nordfriesland'),
('DE', '25862', 'Joldelund', 'Schleswig-Holstein', 'SH', 'Nordfriesland'),
('DE', '25862', 'Kolkerheide', 'Schleswig-Holstein', 'SH', 'Nordfriesland'),
('DE', '25862', 'Goldebek', 'Schleswig-Holstein', 'SH', 'Nordfriesland'),
('DE', '25862', 'Goldelund', 'Schleswig-Holstein', 'SH', 'Nordfriesland'),
('DE', '25863', 'Langeneß', 'Schleswig-Holstein', 'SH', 'Nordfriesland'),
('DE', '25864', 'Löwenstedt', 'Schleswig-Holstein', 'SH', 'Nordfriesland'),
('DE', '25866', 'Mildstedt', 'Schleswig-Holstein', 'SH', 'Nordfriesland'),
('DE', '25868', 'Norderstapel', 'Schleswig-Holstein', 'SH', 'Schleswig-Flensburg'),
('DE', '25869', 'Gröde', 'Schleswig-Holstein', 'SH', 'Nordfriesland'),
('DE', '25870', 'Oldenswort', 'Schleswig-Holstein', 'SH', 'Nordfriesland'),
('DE', '25870', 'Norderfriedrichskoog', 'Schleswig-Holstein', 'SH', 'Nordfriesland'),
('DE', '25872', 'Wittbek', 'Schleswig-Holstein', 'SH', 'Nordfriesland'),
('DE', '25872', 'Ostenfeld', 'Schleswig-Holstein', 'SH', 'Nordfriesland'),
('DE', '25873', 'Oldersbek', 'Schleswig-Holstein', 'SH', 'Nordfriesland'),
('DE', '25873', 'Rantrum', 'Schleswig-Holstein', 'SH', 'Nordfriesland'),
('DE', '25875', 'Schobüll', 'Schleswig-Holstein', 'SH', 'Landkreis Nordfriesland'),
('DE', '25876', 'Fresendelf', 'Schleswig-Holstein', 'SH', 'Nordfriesland'),
('DE', '25876', 'Süderhöft', 'Schleswig-Holstein', 'SH', 'Nordfriesland'),
('DE', '25876', 'Hude', 'Schleswig-Holstein', 'SH', 'Nordfriesland'),
('DE', '25876', 'Wisch', 'Schleswig-Holstein', 'SH', 'Nordfriesland'),
('DE', '25876', 'Schwabstedt', 'Schleswig-Holstein', 'SH', 'Nordfriesland'),
('DE', '25876', 'Ramstedt', 'Schleswig-Holstein', 'SH', 'Nordfriesland'),
('DE', '25878', 'Drage', 'Schleswig-Holstein', 'SH', 'Nordfriesland'),
('DE', '25878', 'Seeth', 'Schleswig-Holstein', 'SH', 'Nordfriesland'),
('DE', '25879', 'Süderstapel', 'Schleswig-Holstein', 'SH', 'Schleswig-Flensburg'),
('DE', '25881', 'Augustenkoog', 'Schleswig-Holstein', 'SH', 'Nordfriesland'),
('DE', '25881', 'Westerhever', 'Schleswig-Holstein', 'SH', 'Nordfriesland'),
('DE', '25881', 'Tümlauer Koog', 'Schleswig-Holstein', 'SH', 'Nordfriesland'),
('DE', '25881', 'Tating', 'Schleswig-Holstein', 'SH', 'Nordfriesland'),
('DE', '25882', 'Tetenbüll', 'Schleswig-Holstein', 'SH', 'Nordfriesland'),
('DE', '25884', 'Norstedt', 'Schleswig-Holstein', 'SH', 'Nordfriesland'),
('DE', '25884', 'Viöl', 'Schleswig-Holstein', 'SH', 'Nordfriesland'),
('DE', '25884', 'Sollwitt', 'Schleswig-Holstein', 'SH', 'Nordfriesland'),
('DE', '25885', 'Oster-Ohrstedt', 'Schleswig-Holstein', 'SH', 'Nordfriesland'),
('DE', '25885', 'Wester-Ohrstedt', 'Schleswig-Holstein', 'SH', 'Nordfriesland'),
('DE', '25885', 'Ahrenviölfeld', 'Schleswig-Holstein', 'SH', 'Nordfriesland'),
('DE', '25885', 'Immenstedt', 'Schleswig-Holstein', 'SH', 'Nordfriesland'),
('DE', '25885', 'Immenstedt', 'Schleswig-Holstein', 'SH', 'Nordfriesland'),
('DE', '25885', 'Ahrenviöl', 'Schleswig-Holstein', 'SH', 'Nordfriesland'),
('DE', '25887', 'Winnert', 'Schleswig-Holstein', 'SH', 'Nordfriesland'),
('DE', '25889', 'Witzwort', 'Schleswig-Holstein', 'SH', 'Nordfriesland'),
('DE', '25889', 'Uelvesbüll', 'Schleswig-Holstein', 'SH', 'Nordfriesland'),
('DE', '25899', 'Klixbüll', 'Schleswig-Holstein', 'SH', 'Nordfriesland'),
('DE', '25899', 'Bosbüll', 'Schleswig-Holstein', 'SH', 'Nordfriesland'),
('DE', '25899', 'Galmsbüll', 'Schleswig-Holstein', 'SH', 'Nordfriesland'),
('DE', '25899', 'Dagebüll', 'Schleswig-Holstein', 'SH', 'Nordfriesland'),
('DE', '25899', 'Niebüll', 'Schleswig-Holstein', 'SH', 'Nordfriesland'),
('DE', '25917', 'Tinningstedt', 'Schleswig-Holstein', 'SH', 'Nordfriesland'),
('DE', '25917', 'Achtrup', 'Schleswig-Holstein', 'SH', 'Nordfriesland'),
('DE', '25917', 'Enge-Sande', 'Schleswig-Holstein', 'SH', 'Nordfriesland'),
('DE', '25917', 'Stadum', 'Schleswig-Holstein', 'SH', 'Nordfriesland'),
('DE', '25917', 'Sprakebüll', 'Schleswig-Holstein', 'SH', 'Nordfriesland'),
('DE', '25917', 'Leck', 'Schleswig-Holstein', 'SH', 'Nordfriesland'),
('DE', '25920', 'Risum-Lindholm', 'Schleswig-Holstein', 'SH', 'Nordfriesland'),
('DE', '25920', 'Stedesand', 'Schleswig-Holstein', 'SH', 'Nordfriesland'),
('DE', '25923', 'Lexgaard', 'Schleswig-Holstein', 'SH', 'Nordfriesland'),
('DE', '25923', 'Uphusum', 'Schleswig-Holstein', 'SH', 'Nordfriesland'),
('DE', '25923', 'Humptrup', 'Schleswig-Holstein', 'SH', 'Nordfriesland'),
('DE', '25923', 'Ellhöft', 'Schleswig-Holstein', 'SH', 'Nordfriesland'),
('DE', '25923', 'Holm', 'Schleswig-Holstein', 'SH', 'Nordfriesland'),
('DE', '25923', 'Süderlügum', 'Schleswig-Holstein', 'SH', 'Nordfriesland'),
('DE', '25923', 'Braderup', 'Schleswig-Holstein', 'SH', 'Nordfriesland'),
('DE', '25924', 'Friedrich-Wilhelm-Lübke-Koog', 'Schleswig-Holstein', 'SH', 'Nordfriesland'),
('DE', '25924', 'Emmelsbüll-Horsbüll', 'Schleswig-Holstein', 'SH', 'Nordfriesland'),
('DE', '25924', 'Klanxbüll', 'Schleswig-Holstein', 'SH', 'Nordfriesland'),
('DE', '25924', 'Rodenäs', 'Schleswig-Holstein', 'SH', 'Nordfriesland'),
('DE', '25926', 'Bramstedtlund', 'Schleswig-Holstein', 'SH', 'Nordfriesland'),
('DE', '25926', 'Ladelund', 'Schleswig-Holstein', 'SH', 'Nordfriesland'),
('DE', '25926', 'Westre', 'Schleswig-Holstein', 'SH', 'Nordfriesland'),
('DE', '25926', 'Karlum', 'Schleswig-Holstein', 'SH', 'Nordfriesland'),
('DE', '25927', 'Neukirchen', 'Schleswig-Holstein', 'SH', 'Nordfriesland'),
('DE', '25927', 'Aventoft', 'Schleswig-Holstein', 'SH', 'Nordfriesland'),
('DE', '25938', 'Dunsum', 'Schleswig-Holstein', 'SH', 'Nordfriesland'),
('DE', '25938', 'Alkersum', 'Schleswig-Holstein', 'SH', 'Nordfriesland'),
('DE', '25938', 'Süderende', 'Schleswig-Holstein', 'SH', 'Nordfriesland'),
('DE', '25938', 'Borgsum', 'Schleswig-Holstein', 'SH', 'Nordfriesland'),
('DE', '25938', 'Utersum', 'Schleswig-Holstein', 'SH', 'Nordfriesland'),
('DE', '25938', 'Oldsum', 'Schleswig-Holstein', 'SH', 'Nordfriesland'),
('DE', '25938', 'Wyk auf Föhr', 'Schleswig-Holstein', 'SH', 'Nordfriesland'),
('DE', '25938', 'Oevenum', 'Schleswig-Holstein', 'SH', 'Nordfriesland'),
('DE', '25938', 'Witsum', 'Schleswig-Holstein', 'SH', 'Nordfriesland'),
('DE', '25938', 'Nieblum', 'Schleswig-Holstein', 'SH', 'Nordfriesland'),
('DE', '25938', 'Wrixum', 'Schleswig-Holstein', 'SH', 'Nordfriesland'),
('DE', '25938', 'Midlum', 'Schleswig-Holstein', 'SH', 'Nordfriesland'),
('DE', '25946', 'Wittdün', 'Schleswig-Holstein', 'SH', 'Nordfriesland'),
('DE', '25946', 'Nebel', 'Schleswig-Holstein', 'SH', 'Nordfriesland'),
('DE', '25946', 'Norddorf', 'Schleswig-Holstein', 'SH', 'Nordfriesland'),
('DE', '25980', 'Westerland', 'Schleswig-Holstein', 'SH', 'Nordfriesland'),
('DE', '25980', 'Rantum', 'Schleswig-Holstein', 'SH', 'Nordfriesland'),
('DE', '25980', 'Sylt-Ost', 'Schleswig-Holstein', 'SH', 'Nordfriesland'),
('DE', '25992', 'List', 'Schleswig-Holstein', 'SH', 'Nordfriesland'),
('DE', '25996', 'Wenningstedt', 'Schleswig-Holstein', 'SH', 'Nordfriesland'),
('DE', '25997', 'Hörnum', 'Schleswig-Holstein', 'SH', 'Nordfriesland'),
('DE', '25999', 'Kampen', 'Schleswig-Holstein', 'SH', 'Nordfriesland'),
('DE', '27498', 'Helgoland', 'Schleswig-Holstein', 'SH', 'Pinneberg');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `produkte`
--

DROP TABLE IF EXISTS `produkte`;
CREATE TABLE IF NOT EXISTS `produkte` (
  `prod_kz` char(4) COLLATE utf8_bin NOT NULL,
  `bezeichnung` varchar(64) COLLATE utf8_bin NOT NULL,
  `netto_preis1` decimal(8,2) NOT NULL,
  `netto_preis2` decimal(8,2) NOT NULL,
  `mwst` decimal(5,2) NOT NULL,
  `aktiv` bit(1) NOT NULL DEFAULT b'1',
  `prod_bild` varchar(264) COLLATE utf8_bin DEFAULT NULL,
  PRIMARY KEY (`prod_kz`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Daten für Tabelle `produkte`
--

INSERT INTO `produkte` (`prod_kz`, `bezeichnung`, `netto_preis1`, `netto_preis2`, `mwst`, `aktiv`, `prod_bild`) VALUES
('GEFU', 'Gefüllte Grießfrikadellen - Gemüsefüllung', '0.60', '0.80', '7.00', b'1', 'erdoding_hack_web.jpg'),
('HAFU', 'Gefüllte Grießfrikadellen - Hackfleischfüllung', '0.60', '0.80', '7.00', b'1', 'erdoding_gemuese_web.jpg'),
('KAFU', 'Gefüllte Grießfrikadellen - Kartoffelfüllung', '0.60', '0.80', '7.00', b'1', 'erdoding_hack_web.jpg');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `UserID` int(11) NOT NULL AUTO_INCREMENT,
  `benutzer` varchar(15) COLLATE utf8_bin NOT NULL,
  `passwort` varchar(40) COLLATE utf8_bin DEFAULT NULL,
  `admin` char(1) COLLATE utf8_bin DEFAULT NULL,
  `status` char(1) COLLATE utf8_bin DEFAULT NULL,
  `email` varchar(128) COLLATE utf8_bin DEFAULT NULL,
  `loginCount` smallint(6) DEFAULT NULL,
  `loginTime` datetime DEFAULT NULL,
  `timeOut` datetime DEFAULT NULL,
  `logoutTime` datetime DEFAULT NULL,
  `onlineTime` datetime DEFAULT NULL,
  PRIMARY KEY (`UserID`,`benutzer`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=11 ;

--
-- Daten für Tabelle `users`
--

INSERT INTO `users` (`UserID`, `benutzer`, `passwort`, `admin`, `status`, `email`, `loginCount`, `loginTime`, `timeOut`, `logoutTime`, `onlineTime`) VALUES
(1, 'sek', '07b801cd4a9fff595ffe8cd804f4925da416df2f', 'J', 'B', 'suat.ekinci@gmx.de', 0, NULL, NULL, '2014-09-18 01:59:43', NULL),
(5, 'rum', '07b801cd4a9fff595ffe8cd804f4925da416df2f', 'J', 'B', 'rumdidum@mail.com', 0, NULL, NULL, '2014-09-04 12:36:20', NULL),
(7, 'test', '07b801cd4a9fff595ffe8cd804f4925da416df2f', 'N', 'B', 'test@mail.com', 0, NULL, NULL, '2014-09-04 12:33:46', NULL),
(8, 'peter', '07b801cd4a9fff595ffe8cd804f4925da416df2f', 'N', 'O', 'mail@mail.hamburg', NULL, NULL, NULL, NULL, NULL),
(9, 'ahi', '07b801cd4a9fff595ffe8cd804f4925da416df2f', 'J', 'B', 'ahi@mail.de', 0, NULL, NULL, '2014-09-03 02:44:28', NULL),
(10, 'suat', 'dddd5d7b474d2c78ebbb833789c4bfd721edf4bf', 'N', 'O', 'mail@fake.de', 0, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `verkaeufe`
--

DROP TABLE IF EXISTS `verkaeufe`;
CREATE TABLE IF NOT EXISTS `verkaeufe` (
  `lfd_nr` int(11) NOT NULL AUTO_INCREMENT,
  `prod_kz` char(4) COLLATE utf8_bin NOT NULL,
  `verkauf_an` int(11) NOT NULL,
  `menge` int(11) NOT NULL,
  `preis_kat` tinyint(4) NOT NULL DEFAULT '1',
  `datum` datetime NOT NULL,
  `bemerkung` varchar(260) COLLATE utf8_bin DEFAULT NULL,
  `beleg_nr` varchar(45) COLLATE utf8_bin NOT NULL,
  `beleg_pfad` varchar(260) COLLATE utf8_bin DEFAULT NULL,
  `mwst` decimal(5,2) NOT NULL,
  `einzelpr_netto` decimal(8,2) NOT NULL,
  `gesamtpr_netto` decimal(8,2) NOT NULL,
  PRIMARY KEY (`lfd_nr`,`prod_kz`,`verkauf_an`),
  KEY `fk_prod_kz_idx` (`prod_kz`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=18 ;

--
-- Daten für Tabelle `verkaeufe`
--

INSERT INTO `verkaeufe` (`lfd_nr`, `prod_kz`, `verkauf_an`, `menge`, `preis_kat`, `datum`, `bemerkung`, `beleg_nr`, `beleg_pfad`, `mwst`, `einzelpr_netto`, `gesamtpr_netto`) VALUES
(1, 'HAFU', 22276713, 500, 1, '2014-09-16 00:00:00', NULL, '2014/1', NULL, '7.00', '0.60', '300.00'),
(2, 'HAFU', 3221113, 500, 2, '2014-09-16 00:00:00', NULL, '2014/2', NULL, '7.00', '0.80', '400.00'),
(3, 'GEFU', 22276713, 600, 1, '2014-09-16 00:00:00', NULL, '2014/1', NULL, '7.00', '0.60', '360.00'),
(4, 'GEFU', 3221113, 500, 1, '2014-09-16 00:00:00', NULL, '2014/2', NULL, '7.00', '0.60', '300.00'),
(5, 'GEFU', 112009915, 600, 1, '2014-09-17 00:00:00', NULL, '2014/3', NULL, '7.00', '0.60', '360.00'),
(6, 'HAFU', 112009915, 400, 1, '2014-09-17 00:00:00', NULL, '2014/3', NULL, '7.00', '0.60', '240.00'),
(7, 'KAFU', 112009915, 500, 1, '2014-09-17 00:00:00', NULL, '2014/3', NULL, '7.00', '0.60', '300.00'),
(8, 'GEFU', 4221194, 800, 1, '2014-09-15 00:00:00', NULL, '2014/4', NULL, '7.00', '0.60', '480.00'),
(9, 'HAFU', 4221194, 1200, 1, '2014-09-15 00:00:00', NULL, '2014/4', NULL, '7.00', '0.60', '720.00'),
(10, 'KAFU', 4221194, 500, 1, '2014-09-15 00:00:00', NULL, '2014/4', NULL, '7.00', '0.60', '300.00'),
(11, 'GEFU', 72254710, 300, 1, '2014-09-13 00:00:00', NULL, '2014/5', NULL, '7.00', '0.60', '180.00'),
(12, 'HAFU', 72254710, 700, 1, '2014-09-13 00:00:00', NULL, '2014/5', NULL, '7.00', '0.60', '420.00'),
(13, 'KAFU', 72254710, 750, 1, '2014-09-13 00:00:00', NULL, '2014/5', NULL, '7.00', '0.60', '450.00'),
(14, 'HAFU', 82276711, 630, 1, '2014-09-13 00:00:00', NULL, '2014/6', NULL, '7.00', '0.60', '378.00'),
(15, 'GEFU', 82276711, 450, 1, '2014-09-13 00:00:00', NULL, '2014/6', NULL, '7.00', '0.60', '270.00'),
(16, 'KAFU', 82276711, 900, 1, '2014-09-13 00:00:00', NULL, '2014/6', NULL, '7.00', '0.60', '540.00'),
(17, 'GEFU', 2225498, 50, 1, '2014-09-13 00:00:00', NULL, '2014/7', NULL, '7.00', '0.60', '30.00');

--
-- Constraints der exportierten Tabellen
--

--
-- Constraints der Tabelle `ausgaben`
--
ALTER TABLE `ausgaben`
  ADD CONSTRAINT `ausg_art_kz` FOREIGN KEY (`ausg_art_kz`) REFERENCES `ausgaben_arten` (`ausg_art_kz`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints der Tabelle `laufende_ausgaben`
--
ALTER TABLE `laufende_ausgaben`
  ADD CONSTRAINT `ausg_art_kz_lfd` FOREIGN KEY (`ausg_art_kz`) REFERENCES `ausgaben_arten` (`ausg_art_kz`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `ausg_kz` FOREIGN KEY (`ausg_kz`) REFERENCES `ausgaben` (`ausg_kz`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints der Tabelle `verkaeufe`
--
ALTER TABLE `verkaeufe`
  ADD CONSTRAINT `fk_prod_kz` FOREIGN KEY (`prod_kz`) REFERENCES `produkte` (`prod_kz`) ON DELETE NO ACTION ON UPDATE NO ACTION;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
