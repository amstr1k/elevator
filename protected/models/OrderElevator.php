<?php

class OrderElevator extends CActiveRecord
{
  const STATUS_PROCESSED = 0;
  const STATUS_ACTIVE    = 1;
  const STATUS_WAITING   = 2;

  const DIRECTION_DOWN = 0;
  const DIRECTION_UP   = 1;

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
      'child'    => array(
        self::HAS_ONE,
        'OrderElevator',
        'child_id',
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
        'min'         => 1,
        'max'         => 10,
        'on'          => 'create'
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
    return $final_floor > $start_floor ? self::DIRECTION_UP : self::DIRECTION_DOWN;
  }

  public static function model($className = __CLASS__)
  {
    return parent::model($className);
  }

  public static function findActive()
  {
    return self::model()->find(array('condition' => 'status= ' . self::STATUS_ACTIVE));
  }
}
