<?php

class BeispielController extends Controller
{

	public function actionSpieler()
	{
		$this->render('spieler');
	}
	
	public function actionStartseite()
	{
		$this->render('startseite');
	}
	
	public function actionElemente()
	{
		$this->render('elemente');
	}
	
	public function actionStatistik()
	{
		$this->render('statistik');
	}
}