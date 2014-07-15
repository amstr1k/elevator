<?php

class Elevator extends CActiveRecord
{
  const ELEVATORS_COUNT = 4;

	public function tableName()
	{
		return 'elevator';
	}

	public function relations()
	{
    return array(
      'orders' => array(
        self::HAS_ONE,
        'OrderElevator',
        'elevator_id',
      ),
    );
	}

	public function search()
	{
		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('is_busy',$this->is_busy);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

  public static function findNearest($floor)
  {
    $sql = "SELECT * FROM elevator WHERE is_busy = 0 ORDER BY ABS(floor - {$floor}) ASC LIMIT 1";

    return self::model()->findBySql($sql);
  }
}
