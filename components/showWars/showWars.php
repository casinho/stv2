<?php
class showWars extends CWidget {
	
	public $type = false;
	public $limit = false;
	public $h1class = false;
	public $squad = false;
	
	public function init() {
		return true;
	}
	
	public function run() {

		if($this->type === false || $this->type == 'all') {
			$result = Yii::app()->db->cache(3600)->createCommand()
				->select('w.*,COUNT(k.kommentar_id) AS anzahl,c.clan,c.clan_id,c.tag,s.squad_name,s.squad_tag,f.flaggenname,f.nationalname,l.tag AS liga_tag,l.text AS ligaliga')
				->from('clanwars AS w')
				->leftJoin('kommentarzuweisung AS k', "k.fremd_id = w.id AND k.zuweisung = 'clanwars'")
				->leftJoin('clans AS c', 'c.clan_id = w.enemy_id')
				->leftJoin('flaggen AS f', 'f.id = c.land_id')
				->leftJoin('squad AS s', 's.squad_id = w.squad_id')
				->leftJoin('link AS l', 'l.id = w.liga_id')
				->group('w.id')
				->order('w.datum DESC')
				->limit($this->limit)
				->queryAll();
		} else {
			
			$squad_id = $this->type;
			
			$this->squad = Squad::model()->findByPk($squad_id);
			
			$result = Yii::app()->db->cache(3600)->createCommand()
			->select('w.*,COUNT(k.kommentar_id) AS anzahl,c.clan,c.clan_id,c.tag,s.squad_name,s.squad_tag,f.flaggenname,f.nationalname,l.tag AS liga_tag,l.text AS liga')
			->from('clanwars AS w')
			->leftJoin('kommentarzuweisung AS k', "k.fremd_id = w.id AND k.zuweisung = 'clanwars'")
			->leftJoin('clans AS c', 'c.clan_id = w.enemy_id')
			->leftJoin('flaggen AS f', 'f.id = c.land_id')
			->leftJoin('squad AS s', 's.squad_id = w.squad_id')
			->leftJoin('link AS l', 'l.id = w.liga_id')
			->where('s.squad_id = :squad_id', array(':squad_id' => $squad_id))
			->group('w.id')
			->order('w.datum DESC')
			->limit($this->limit)
			->queryAll();
				
		}
		
		$output = array();
		foreach($result as $k => $v) {
			$output[$k] = $v;
			if(!isset($output[$k]['image']) || $output[$k]['image']=='') {
				$output[$k]['image'] = 'images/man.jpg';
			}
			
			$output[$k]['image'] = '';
			
			$titel = Yii::t('clanwars','match_vs_clan',array('{squad}' => $v['squad_tag'],'{Clan}'=> $v['clan'], '{liga}'=>'liga'));
			
			$output[$k]['url'] = Yii::app()->createUrl('clanwars/detail',array('id'=>$v['id'],'seo'=>GFunctions::normalisiereString($titel)));
			$output[$k]['alt'] = $titel;
			
			$text = Clanwars::getWertung($v['wertung'],'padding:8px 5px 5px 5px;margin-right:10px;float:left;',CHtml::tag('b',array('style'=>'font-size:28px;'),Clanwars::getScore($v)));
			$text.= CHtml::tag('span',array('style'=>'color:#ccc'),Yii::app()->dateFormatter->formatDateTime($v['datum'],'medium',false));
			$text.= CHtml::tag('br');
			
			$output[$k]['info'] = array(
					'title' => $titel,
					'text' => $text,
			);
		}
		
		
		$this->render('showWars',array('cw'=>$output));
	}
}
?>