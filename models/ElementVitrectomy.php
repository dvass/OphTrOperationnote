<?php
/**
 * OpenEyes
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2008-2011, Moorfields Eye Hospital NHS Foundation Trust
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */

/**
 * This is the model class for table "element_procedurelist".
 *
 * The followings are the available columns in table 'element_operation':
 * @property string $id
 * @property integer $event_id
 * @property integer $surgeon_id
 * @property integer $assistant_id
 * @property integer $anaesthetic_type
 *
 * The followings are the available model relations:
 * @property Event $event
 */
class ElementVitrectomy extends BaseEventTypeElement
{
	public $service;

	/**
	 * Returns the static model of the specified AR class.
	 * @return ElementOperation the static model class
	 */
	public static function model($className = __CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'et_ophtroperationnote_vitrectomy';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('event_id, gauge_id, pvd_induced, eyedraw, comments', 'safe'),
			array('gauge_id, pvd_induced', 'required'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, event_id, gauge_id, pvd_induced', 'safe', 'on' => 'search'),
		);
	}
	
	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
			'element_type' => array(self::HAS_ONE, 'ElementType', 'id','on' => "element_type.class_name='".get_class($this)."'"),
			'event' => array(self::BELONGS_TO, 'Event', 'event_id'),
			'gauge' => array(self::BELONGS_TO, 'VitrectomyGauge', 'gauge_id'),
			'user' => array(self::BELONGS_TO, 'User', 'created_user_id'),
			'usermodified' => array(self::BELONGS_TO, 'User', 'last_modified_user_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'gauge_id' => 'Gauge',
			'pvd_induced' => 'PVD Induced',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

		$criteria = new CDbCriteria;

		$criteria->compare('id', $this->id, true);
		$criteria->compare('event_id', $this->event_id, true);
		$criteria->compare('gauge_id', $this->gauge_id);
		$criteria->compare('pvd_induced', $this->pvd_induced);
		
		return new CActiveDataProvider(get_class($this), array(
				'criteria' => $criteria,
			));
	}

	/**
	 * Set default values for forms on create
	 */
	public function setDefaultOptions()
	{
	}

	public function getEye() {
		if ($this->event_id && $pl = ElementProcedureList::model()->find('event_id=?',array($this->event_id))) {
			return $pl->eye;
		}
	}

	public function getSelectedEye() {
		if (Yii::app()->getController()->getAction()->id == 'create') {
			// Get the procedure list and eye from the most recent booking for the episode of the current user's subspecialty
			if (!$patient = Patient::model()->findByPk(@$_GET['patient_id'])) {
				throw new SystemException('Patient not found: '.@$_GET['patient_id']);
			}

			if ($episode = $patient->getEpisodeForCurrentSubspecialty()) {
				if ($api = Yii::app()->moduleAPI->get('OphTrOperationbooking')) {
					if ($booking = $api->getMostRecentBookingForEpisode($patient, $episode)) {
						return $booking->operation->eye;
					}
				}
			}
		}

		if (Yii::app()->getController()->getAction()->id == 'update') {
			if ($this->eye) {
				return $this->eye;
			}
			if (preg_match('/\/default\/update\/([0-9]+)$/',$_SERVER['REQUEST_URI'],$m)) {
				if ($pl = ElementProcedureList::model()->find('event_id=?',array($m[1]))) {
					return $pl->eye;
				}
			}
		}

		if (isset($_GET['eye'])) {
			return Eye::model()->findByPk($_GET['eye']);
		}

		return new Eye;
	}
}
