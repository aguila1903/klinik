
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `editAusgabeEinzel` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `editAusgabeEinzel`(Var_bezeichnung varchar(64),

Var_ausg_art_kz char(4), Var_ausg_kz char(4)

)
root:BEGIN



Declare Var_anzahl int;





Start Transaction;

Update klinikdb.ausgaben set

bezeichnung = Var_bezeichnung

Where ausg_art_kz = Var_ausg_art_kz and ausg_kz = Var_ausg_kz;



set Var_anzahl = ROW_COUNT();





 IF Var_anzahl != 1 

 Then 

 Rollback;

 Select Var_anzahl as ergebnis;

 Leave root;

 End IF;

commit;

Select Var_anzahl as ergebnis;



END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `deleteKunden` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `deleteKunden`(IN `Var_kunden_nr` int, IN `Var_user` varchar(15)

)
root:BEGIN



DECLARE Var_anzahl int;

DECLARE Var_anzahl_hist int;



DECLARE Var_lfn int;

DECLARE Var_feld varchar(264);

DECLARE Var_ainhalt varchar(264);

DECLARE Var_ninhalt varchar(264);

DECLARE Var_code char(3);



If exists (Select * from klinikdb.verkaeufe where verkauf_an = Var_kunden_nr)

Then

Select -99 as ergebnis, -99 as historie, Var_kunden_nr as kunden_nr;

Leave root;

End If;



Start transaction;

Delete from klinikdb.kunden where lfd_nr = Var_kunden_nr;



set Var_anzahl = ROW_COUNT();



Set Var_code = '003';



	  Set Var_feld =  'SİLİNDİ';

	  Set Var_ainhalt =  NULL;

	  Set Var_ninhalt =  NULL;

	  

      INSERT INTO hist_kunden

        (schluessel, codetext, user, aenderdat, feld, a_inhalt, n_inhalt)

      VALUES

        (Var_kunden_nr, Var_code, Var_user, curtime(), Var_feld, Var_ainhalt, Var_ninhalt);



set Var_anzahl_hist = ROW_COUNT();



 IF Var_anzahl !