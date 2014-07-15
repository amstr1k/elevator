<?php

class OrderElevator extends CActiveRecord
{
	public function tableName()
	{
		return 'order_elevator';
	}

	public function relations()
	{
    return array(
      'elevator' => array(
        self::BELONGS_TO,
        'Elevator',
        'elevator_id',
      ),
    );
	}

  public function rules()
  {
    return array(
      array(
        'start_floor, final_floor',
        'required',
        'on' => 'create'
      ),
      array(
        'start_floor, final_floor',
        'numerical',
        'integerOnly' => true,
        'min'=> 1,
        'max' => 10,
        'on' => 'create'
      ),
      array(
        'start_floor, final_floor',
        'safe'
      ),
    );
  }

  public function attributeLabels()
  {
    return array(
      'start_floor' => 'Вызываемый этаж',
      'final_floor' => 'Назначаемый этаж',
    );
  }

  public static function getDirection($start_floor, $final_floor)
  {
    return $final_floor > $start_floor ? 1 : 0;
  }

  public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
