<?php
class StatistikController extends Controller {

	public function actionIndex() {
		$saisonId 							= Saison::model()->getAktuelleSaisonId(Yii::app()->params['wettbewerbId']);
		$spieltagsAnzeige 					= WTFunctions::getDatenFuerSpieltagsAnzeigeBySaisonId($saisonId);
		$spieltagsAnzeige['gesamtAnzeige'] 	= true;
		$saison 							= Saison::model()->findByPk($saisonId);
		$topTore							= SpielStatistik::model()->getTopSpielStatistik($saisonId,'tore DESC');
		$flopTore							= SpielStatistik::model()->getTopSpielStatistik($saisonId,'tore ASC');
		$flopGegentore						= SpielStatistik::model()->getTopSpielStatistik($saisonId,'gegentore DESC');
		$topGegentore						= SpielStatistik::model()->getTopSpielStatistik($saisonId,'gegentore ASC');
		$flopChancen						= SpielStatistik::model()->getTopSpielStatistik($saisonId,'chancen ASC');
		$meisteAbseitsstellungen			= SpielStatistik::model()->getTopSpielStatistik($saisonId,'abseits DESC');	
		$meisteKarten						= SpielStatistik::model()->getTopSpielStatistik($saisonId,'karten DESC,rote DESC,gelbrote DESC,gelbe DESC');
		$wenigsteKarten						= SpielStatistik::model()->getTopSpielStatistik($saisonId,'karten ASC,rote ASC,gelbrote ASC,gelbe ASC');
		$efektivitaetTore					= SpielStatistik::model()->getTopSpielStatistik($saisonId,'schuesse_pro_tor ASC, tore DESC');
		$meisteFouls						= SpielStatistik::model()->getTopSpielStatistik($saisonId,'fouls DESC');
		$heimTabelleTop  					= Saison::model()->getHeimTabelleEinEintrag($saisonId,'punkte DESC, tore_diff DESC, tore_plus DESC');		
		$heimTabelleFlop  					= Saison::model()->getHeimTabelleEinEintrag($saisonId,'punkte ASC, tore_diff ASC, tore_plus ASC');
		$gastTabelleTop  					= Saison::model()->getGastTabelleEinEintrag($saisonId,'punkte DESC, tore_diff DESC, tore_plus DESC');		
		$gastTabelleFlop  					= Saison::model()->getGastTabelleEinEintrag($saisonId,'punkte ASC, tore_diff ASC, tore_plus ASC');
		
		$halbzeitEinsTabelleTop  			= Saison::model()->getHalbzeitEinsTabelleEinEintrag($saisonId,'punkte DESC, tore_diff DESC, tore_plus DESC');		
		$halbzeitZweiTabelleTop  			= Saison::model()->getHalbzeitZweiTabelleEinEintrag($saisonId,'punkte DESC, tore_diff DESC, tore_plus DESC');
		
		$teamDerStunde  					= Saison::model()->getTabelleFormEinEintrag($saisonId,$spieltagsAnzeige['gewaehlterSpieltag']);
		
		$viewparams = array(
    			'saisonId' => $saisonId,
    			'spieltagsAnzeige' => $spieltagsAnzeige,
    			'topTore' => $topTore,
    			'flopGegentore' => $flopGegentore,
    			'topGegentore' => $topGegentore,
    			'meisteKarten' => $meisteKarten,
    			'meisteAbseits' => $meisteAbseitsstellungen,
    			'wenigsteKarten' => $wenigsteKarten,
    			'effektivitaetTore' => $efektivitaetTore,
    			'flopTore' => $flopTore,
    			'meisteFouls' => $meisteFouls,
    			'flopChancen' => $flopChancen,
    			'heimTabelleTop' => $heimTabelleTop,
    			'heimTabelleFlop' => $heimTabelleFlop,
    			'gastTabelleTop' => $gastTabelleTop,
    			'gastTabelleFlop' => $gastTabelleFlop,
    			'ersteHzTabelleTop' => $halbzeitEinsTabelleTop,
    			'zweiteHzTabelleTop' => $halbzeitZweiTabelleTop,
                'teamDerStunde' => $teamDerStunde,
        );
		$this->breadcrumbs = array('Statistiken');
		$this->render('index',$viewparams);
	}
	
	
	
	public function actionFehlentscheidungen() {
		$saisonId 						= Saison::model()->getAktuelleSaisonId(Yii::app()->params['wettbewerbId']);
    	$spieltagsAnzeige 				= WTFunctions::getDatenFuerSpieltagsAnzeigeBySaisonId($saisonId,true,true);
		$nummerAktuellerSpieltag 		= $spieltagsAnzeige['aktuellerSpieltag'];
        $nummerGewaehlterSpieltag 		= $spieltagsAnzeige['gewaehlterSpieltag'];
        $saison 						= Saison::model()->findByPk($saisonId);
		$vereine 						= $saison->getWahreTabelleEinfachBySpieltag($spieltagsAnzeige['gewaehlterSpieltag']);
		$fehler['fehler_gesamt'] 	= 0;
		$fehler['fehler_elfer'] 	= 0;
		$fehler['fehler_abseits'] 	= 0;
		$fehler['fehler_sonstige'] 	= 0;	
		foreach($vereine as $k => $v) {
			if($nummerGewaehlterSpieltag > $nummerAktuellerSpieltag) {
				$fehlentscheidung[$k] 				= Spiel::model()->getFehlentscheidungenVereinBySaison($v['verein_id'],$saisonId);				
				$vereine[$k]['gegner_verein_id']	= 0;			
			} else {
				$fehlentscheidung[$k] 				= Spiel::model()->getFehlentscheidungenVereinBySaisonSpieltag($v['verein_id'],$saisonId,$nummerGewaehlterSpieltag);				
				$spiel[$k] 							= Spiel::model()->getSpielByVereinAndSpieltag($v['verein_id'],$nummerGewaehlterSpieltag,$saisonId);
				if($spiel[$k]['verein_id_h'] == $v['verein_id']) {
					$vereine[$k]['gegner_verein_id'] = $spiel[$k]['verein_id_g'];
				} else {
					$vereine[$k]['gegner_verein_id'] = $spiel[$k]['verein_id_h'];
				}
				$vereine[$k]['schiedsrichter_id'] 	= $spiel[$k]['schiedsrichter_id'];
			}
			$vereine[$k]['elfer_erhalten']			= $fehlentscheidung[$k]['elfer_erhalten'];
			$vereine[$k]['elfer_verweigert']		= $fehlentscheidung[$k]['elfer_verweigert'];
			$vereine[$k]['sonstige_verweigert']		= $fehlentscheidung[$k]['sonstige_verweigert'];
			$vereine[$k]['sonstige_erhalten']		= $fehlentscheidung[$k]['sonstige_erhalten'];
			$vereine[$k]['abseits_verweigert']		= $fehlentscheidung[$k]['abseits_verweigert'];
			$vereine[$k]['abseits_erhalten']		= $fehlentscheidung[$k]['abseits_erhalten'];
			$vereine[$k]['erhalten']				= $fehlentscheidung[$k]['erhalten'];
			$vereine[$k]['verweigert']				= $fehlentscheidung[$k]['verweigert'];
			$fehler['fehler_gesamt']				+= $fehlentscheidung[$k]['erhalten']+$fehlentscheidung[$k]['verweigert'];
			$fehler['fehler_elfer']					+= $fehlentscheidung[$k]['elfer_erhalten']+$fehlentscheidung[$k]['elfer_verweigert'];
			$fehler['fehler_abseits']				+= $fehlentscheidung[$k]['abseits_erhalten']+$fehlentscheidung[$k]['abseits_verweigert'];
			$fehler['fehler_sonstige']				+= $fehlentscheidung[$k]['sonstige_erhalten']+$fehlentscheidung[$k]['sonstige_verweigert'];
		}
		
    	$viewparams = array(
    			'saisonId' => $saisonId,
    			'spieltagsAnzeige' => $spieltagsAnzeige,
    			'fehlentscheidungen' => $vereine,
    			'fehler' => $fehler
        );
		$this->breadcrumbs = array('Fehlentscheidungen');		
		$this->render('fehlentscheidungen',$viewparams);
	}
	
	
	public function actionWahreTabelle() {
		$saisonId 								= Saison::model()->getAktuelleSaisonId(Yii::app()->params['wettbewerbId']);
    	$spieltagsAnzeige 						= WTFunctions::getDatenFuerSpieltagsAnzeigeBySaisonId($saisonId,true,true);
		$nummerAktuellerSpieltag 				= $spieltagsAnzeige['aktuellerSpieltag'];
        $nummerGewaehlterSpieltag 				= $spieltagsAnzeige['gewaehlterSpieltag'];
		$saison 								= Saison::model()->findByPk($saisonId);
		$tabelleWahr 							= $saison->getWahreTabelleEinfachBySpieltag($spieltagsAnzeige['gewaehlterSpieltag']);
        $tabelleOffiziell 						= $saison->getOffizielleTabelleEinfachBySpieltag($spieltagsAnzeige['gewaehlterSpieltag']);
		
        Saison::model()->kreuzverwurstung($tabelleWahr, $tabelleOffiziell);
        
		$viewparams = array(
    			'saisonId' => $saisonId,
    			'spieltagsAnzeige' => $spieltagsAnzeige,
                'tabelleWahr' => $tabelleWahr,
                'tabelleOffiziell' => $tabelleOffiziell
        );
		
		$this->breadcrumbs = array('Die wahre Tabelle');
		$this->render('wahretabelle',$viewparams);
	}
	
	
	public function actionHalbzeitTabelle() {
		$saisonId 								= Saison::model()->getAktuelleSaisonId(Yii::app()->params['wettbewerbId']);
    	$spieltagsAnzeige 						= WTFunctions::getDatenFuerSpieltagsAnzeigeBySaisonId($saisonId,true,true);
		$nummerAktuellerSpieltag 				= $spieltagsAnzeige['aktuellerSpieltag'];
        $nummerGewaehlterSpieltag 				= $spieltagsAnzeige['gewaehlterSpieltag'];
		$saison 								= Saison::model()->findByPk($saisonId);
		$ersteHalbzeitTabelle  					= $saison->getErsteHalbzeitTabelleBySpieltag($spieltagsAnzeige['gewaehlterSpieltag']);
		$zweiteHalbzeitTabelle  				= $saison->getZweiteHalbzeitTabelleBySpieltag($spieltagsAnzeige['gewaehlterSpieltag']);
       
		$viewparams = array(
    			'saisonId' => $saisonId,
    			'spieltagsAnzeige' => $spieltagsAnzeige,
                'ersteHalbzeitTabelle' => $ersteHalbzeitTabelle,
                'zweiteHalbzeitTabelle' => $zweiteHalbzeitTabelle,
        );
		$this->breadcrumbs = array('Statistiken' => array('/statistik'),'Halbzeittabellen');
		$this->render('halbzeittabelle',$viewparams);
	}
	
	public function actionHeimGastTabelle() {
		$saisonId 								= Saison::model()->getAktuelleSaisonId(Yii::app()->params['wettbewerbId']);
    	$spieltagsAnzeige 						= WTFunctions::getDatenFuerSpieltagsAnzeigeBySaisonId($saisonId,true,true);
		$nummerAktuellerSpieltag 				= $spieltagsAnzeige['aktuellerSpieltag'];
        $nummerGewaehlterSpieltag 				= $spieltagsAnzeige['gewaehlterSpieltag'];
		$saison 								= Saison::model()->findByPk($saisonId);
		$heimTabelle  							= $saison->getHeimTabelleBySpieltag($spieltagsAnzeige['gewaehlterSpieltag']);
		$gastTabelle  							= $saison->getGastTabelleBySpieltag($spieltagsAnzeige['gewaehlterSpieltag']);
       
		$viewparams = array(
    			'saisonId' => $saisonId,
    			'spieltagsAnzeige' => $spieltagsAnzeige,
                'heimTabelle' => $heimTabelle,
                'gastTabelle' => $gastTabelle,
        );
		$this->breadcrumbs = array('Statistiken' => array('/statistik'),'Heim- & Auswärts-Tabelle');
		$this->render('heimgasttabelle',$viewparams);
	}
	
	
	public function actionFormTabelle() {
		$saisonId 								= Saison::model()->getAktuelleSaisonId(Yii::app()->params['wettbewerbId']);
    	$spieltagsAnzeige 						= WTFunctions::getDatenFuerSpieltagsAnzeigeBySaisonId($saisonId,false,true);
		$nummerAktuellerSpieltag 				= $spieltagsAnzeige['aktuellerSpieltag'];
        $nummerGewaehlterSpieltag 				= $spieltagsAnzeige['gewaehlterSpieltag'];
        if($nummerGewaehlterSpieltag > $nummerAktuellerSpieltag) {
        	$nummerGewaehlterSpieltag = $nummerAktuellerSpieltag;
        	$spieltagsAnzeige['gewaehlterSpieltag'] = $nummerAktuellerSpieltag;
        }
		$saison 								= Saison::model()->findByPk($saisonId);
		$formTabelle  							= $saison->getTabelleForm($spieltagsAnzeige['gewaehlterSpieltag']);
		$tabelleOffiziell 						= $saison->getOffizielleTabelleEinfachBySpieltag($spieltagsAnzeige['gewaehlterSpieltag']);
		
		$viewparams = array(
    			'saisonId' => $saisonId,
    			'spieltagsAnzeige' => $spieltagsAnzeige,
                'formTabelle' => $formTabelle,
                'tabelleOffiziell' => $tabelleOffiziell
        );
		$this->breadcrumbs = array('Statistiken' => array('statistk/formtabelle'),'Formtabelle');
		$this->render('formtabelle',$viewparams);
	}
	
	public function actionAntiFairplay() {
    	$saisonId 								= Saison::model()->getAktuelleSaisonId(Yii::app()->params['wettbewerbId']);
    	$spieltagsAnzeige 						= WTFunctions::getDatenFuerSpieltagsAnzeigeBySaisonId($saisonId,true,true);
		$nummerAktuellerSpieltag 				= $spieltagsAnzeige['aktuellerSpieltag'];
        $nummerGewaehlterSpieltag 				= $spieltagsAnzeige['gewaehlterSpieltag'];
        
    	$antiFairplayTabelle = SpielStatistik::model()->getAntiFairplay($saisonId,$nummerGewaehlterSpieltag,$nummerAktuellerSpieltag);
    	$gelbe_gesamt = 0;
    	$rote_gesamt = 0;
    	$gelbrote_gesamt = 0;
    	$punkte_gesamt = 0;
    	$karten_gesamt = 0;
		foreach($antiFairplayTabelle as $k => $v) {
			$gelbe_gesamt = $gelbe_gesamt+$v['gelbe'];
			$rote_gesamt = $rote_gesamt+$v['rote'];
			$gelbrote_gesamt = $gelbrote_gesamt+$v['gelbrote'];
			$punkte_gesamt = $punkte_gesamt+$v['punkte'];
			$karten_gesamt = $karten_gesamt+$v['karten'];
		}

		$spiele = new CActiveDataProvider('SpielStatistik', array(
			'criteria' => SpielStatistik::getAntiFairplayCriteria($saisonId,$nummerGewaehlterSpieltag,$nummerAktuellerSpieltag),
			'pagination' => false,
			'sort' => SpielStatistik::getAntiFairplaySort(),
		));
    	$viewparams = array(
    			'saisonId' => $saisonId,
    			'spieltagsAnzeige' => $spieltagsAnzeige,
                'antifairplay' => $spiele,
                'gelbe_gesamt' => $gelbe_gesamt,
                'rote_gesamt' => $rote_gesamt,
                'gelbrote_gesamt' => $gelbrote_gesamt,
                'punkte_gesamt' => $punkte_gesamt,
                'karten_gesamt' => $karten_gesamt
        );
        $this->breadcrumbs = array('Statistiken' => array('/statistik'),'Antifairplay-Statistik');
    	$this->render('antifairplay', $viewparams);
    }
    
    public function actionTreter() {
    	$saisonId 								= Saison::model()->getAktuelleSaisonId(Yii::app()->params['wettbewerbId']);
    	$spieltagsAnzeige 						= WTFunctions::getDatenFuerSpieltagsAnzeigeBySaisonId($saisonId,true,true);
		$nummerAktuellerSpieltag 				= $spieltagsAnzeige['aktuellerSpieltag'];
        $nummerGewaehlterSpieltag 				= $spieltagsAnzeige['gewaehlterSpieltag'];
    	$treter = SpielStatistik::model()->getTreter($saisonId,$nummerGewaehlterSpieltag,$nummerAktuellerSpieltag);
    	$fouls_gesamt = 0;
    	$fouls_erhalten_gesamt = 0;
		foreach($treter as $k => $v) {
			$fouls_gesamt = $fouls_gesamt+$v['fouls'];
			$fouls_erhalten_gesamt = $fouls_erhalten_gesamt+$v['fouls_erhalten'];
		}

		$spiele = new CActiveDataProvider('SpielStatistik', array(
			'criteria' => SpielStatistik::getTreterCriteria($saisonId,$nummerGewaehlterSpieltag,$nummerAktuellerSpieltag),
			'pagination' => false,
			'sort' => SpielStatistik::getTreterSort(),
		));
    	$viewparams = array(
    			'saisonId' => $saisonId,
    			'spieltagsAnzeige' => $spieltagsAnzeige,
                'treter' => $spiele,
                'fouls_gesamt' => $fouls_gesamt,
                'fouls_erhalten_gesamt' => $fouls_erhalten_gesamt,
        );
        $this->breadcrumbs = array('Statistiken' => array('/statistik'),'Treter-Statistik');
    	$this->render('treter', $viewparams);
    }
    
    public function actionAbseits() {
    	$saisonId 								= Saison::model()->getAktuelleSaisonId(Yii::app()->params['wettbewerbId']);
    	$spieltagsAnzeige 						= WTFunctions::getDatenFuerSpieltagsAnzeigeBySaisonId($saisonId,true,true);
		$nummerAktuellerSpieltag 				= $spieltagsAnzeige['aktuellerSpieltag'];
        $nummerGewaehlterSpieltag 				= $spieltagsAnzeige['gewaehlterSpieltag'];
    	$abseits = SpielStatistik::model()->getAbseits($saisonId,$nummerGewaehlterSpieltag,$nummerAktuellerSpieltag);
    	$abseits_gesamt = 0;
    	$abseits_erhalten_gesamt = 0;
		foreach($abseits as $k => $v) {
			$abseits_gesamt = $abseits_gesamt+$v['abseits'];
			$abseits_erhalten_gesamt = $abseits_erhalten_gesamt+$v['abseits_erhalten'];
		}

		$spiele = new CActiveDataProvider('SpielStatistik', array(
			'criteria' => SpielStatistik::getAbseitsCriteria($saisonId,$nummerGewaehlterSpieltag,$nummerAktuellerSpieltag),
			'pagination' => false,
			'sort' => SpielStatistik::getAbseitsSort(),
		));
    	$viewparams = array(
    			'saisonId' => $saisonId,
    			'spieltagsAnzeige' => $spieltagsAnzeige,
                'abseits' => $spiele,
                'abseits_gesamt' => $abseits_gesamt,
                'abseits_erhalten_gesamt' => $abseits_erhalten_gesamt,
        );
        $this->breadcrumbs = array('Statistiken' => array('/statistik'),'Abseits-Statistik');
    	$this->render('abseits', $viewparams);
    }
    
    public function actionChancen() {
    	$saisonId 								= Saison::model()->getAktuelleSaisonId(Yii::app()->params['wettbewerbId']);
    	$spieltagsAnzeige 						= WTFunctions::getDatenFuerSpieltagsAnzeigeBySaisonId($saisonId,true,true);
		$nummerAktuellerSpieltag 				= $spieltagsAnzeige['aktuellerSpieltag'];
        $nummerGewaehlterSpieltag 				= $spieltagsAnzeige['gewaehlterSpieltag'];
    	$chancen = SpielStatistik::model()->getChancen($saisonId,$nummerGewaehlterSpieltag,$nummerAktuellerSpieltag);
    	
    	$chancen_gesamt = 0;
    	$schuesse_gesamt = 0;
    	$chancen_zugelassen_gesamt = 0;
    	$schuesse_zugelassen_gesamt = 0;
    	$tore_gesamt = 0;
    	$gegentore_gesamt = 0;
		foreach($chancen as $k => $v) {
			$chancen_gesamt = $chancen_gesamt+$v['chancen'];
			$schuesse_gesamt = $schuesse_gesamt+$v['schuesse'];
			$chancen_zugelassen_gesamt = $chancen_zugelassen_gesamt+$v['chancen_zugelassen'];
			$schuesse_zugelassen_gesamt = $schuesse_zugelassen_gesamt+$v['schuesse_zugelassen'];
			$tore_gesamt = $tore_gesamt+$v['tore'];
			$gegentore_gesamt = $gegentore_gesamt+$v['gegentore'];
		}

		$spiele = new CActiveDataProvider('SpielStatistik', array(
			'criteria' => SpielStatistik::getChancenCriteria($saisonId,$nummerGewaehlterSpieltag,$nummerAktuellerSpieltag),
			'pagination' => false,
			'sort' => SpielStatistik::getChancenSort(),
		));
    	$viewparams = array(
    			'saisonId' => $saisonId,
    			'spieltagsAnzeige' => $spieltagsAnzeige,
                'chancen' => $spiele,
                'chancen_gesamt' => $chancen_gesamt ,
                'schuesse_gesamt' => $schuesse_gesamt,
                'chancen_zugelassen_gesamt' => $chancen_zugelassen_gesamt ,
                'schuesse_zugelassen_gesamt' => $schuesse_zugelassen_gesamt,
                'tore_gesamt' => $tore_gesamt,
                'gegentore_gesamt' => $gegentore_gesamt,
        );
        $this->breadcrumbs = array('Statistiken' => array('/statistik'),'Chancen');
    	$this->render('chancen', $viewparams);
    }
    
    public function actionSchiedsrichter() {
		$saisonId 								= Saison::model()->getAktuelleSaisonId(Yii::app()->params['wettbewerbId']);
    	$spieltagsAnzeige 						= WTFunctions::getDatenFuerSpieltagsAnzeigeBySaisonId($saisonId,true,true);
		$nummerAktuellerSpieltag 				= $spieltagsAnzeige['aktuellerSpieltag'];
        $nummerGewaehlterSpieltag 				= $spieltagsAnzeige['gewaehlterSpieltag'];
		$saison 								= Saison::model()->findByPk($saisonId);
		$schiedsrichter  						= Schiedsrichter::model()->getSchiedsrichterBySaison($saisonId);

		$viewparams = array(
    			'saisonId' => $saisonId,
    			'spieltagsAnzeige' => $spieltagsAnzeige,
                'schiedsrichter' => $schiedsrichter
        );
		$this->breadcrumbs = array('Statistiken' => array('statistk/schiedsrichter'),'Schiedsrichter');
		$this->render('schiedsrichter',$viewparams);
	}
} 