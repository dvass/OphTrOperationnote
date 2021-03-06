<?php

class m120810_130413_add_limbal_relaxing_incision_procedure_element extends CDbMigration
{
	public function up()
	{
		$this->createTable('et_ophtroperationnote_limbal_relaxing_incision', array(
				'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
				'event_id' => 'int(10) unsigned NOT NULL',
				'comments' => 'varchar(4096) COLLATE utf8_bin NOT NULL',
				'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'last_modified_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'created_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'PRIMARY KEY (`id`)',
				'KEY `et_ophtroperationnote_lri_last_modified_user_id_fk` (`last_modified_user_id`)',
				'KEY `et_ophtroperationnote_lri_created_user_id_fk` (`created_user_id`)',
				'CONSTRAINT `et_ophtroperationnote_lri_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `et_ophtroperationnote_lri_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)'
			),
			'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin'
		);

		$event_type = $this->dbConnection->createCommand()->select('id')->from('event_type')->where('name=:name', array(':name'=>'Operation note'))->queryRow();

		$this->insert('element_type', array('name' => 'Limbal relaxing incision', 'class_name' => 'ElementLimbalRelaxingIncision', 'event_type_id' => $event_type['id'], 'display_order' => 20, 'default' => 0));

		$proc = $this->dbConnection->createCommand()->select('id')->from('proc')->where('term=:term',array(':term'=>'Limbal relaxing incision'))->queryRow();
		$element_type = $this->dbConnection->createCommand()->select('id')->from('element_type')->where('class_name=:class_name',array(':class_name'=>'ElementLimbalRelaxingIncision'))->queryRow();

		$this->insert('et_ophtroperationnote_procedure_element',array('procedure_id'=>$proc['id'],'element_type_id'=>$element_type['id']));
	}

	public function down()
	{
		$proc = $this->dbConnection->createCommand()->select('id')->from('proc')->where('term=:term',array(':term'=>'Limbal relaxing incision'))->queryRow();
		$event_type = $this->dbConnection->createCommand()->select('id')->from('event_type')->where('name=:name', array(':name'=>'Operation note'))->queryRow();
		$element_type = $this->dbConnection->createCommand()->select('id')->from('element_type')->where('event_type_id=:event_type_id and class_name=:class_name',array(':event_type_id'=>$event_type['id'],':class_name'=>'ElementLimbalRelaxingIncision'))->queryRow();

		$this->delete('et_ophtroperationnote_procedure_element','procedure_id='.$proc['id'].' and element_type_id='.$element_type['id']);

		$this->delete('element_type','id='.$element_type['id']);

		$this->dropTable('et_ophtroperationnote_limbal_relaxing_incision');
	}
}
