<?php

class Elevator extends CActiveRecord
{
	public function tableName()
	{
		return 'elevator';
	}

  public function relations()
  {
    return array(
      'order'  => array(
        self::HAS_ONE,
        'OrderElevator',
        'elevator_id',
        'condition' => 'status= ' .OrderElevator::STATUS_ACTIVE
      ),
      'orders' => array(
        self::HAS_MANY,
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

  public static function findActive()
  {
    $sql = "SELECT elevator.* FROM elevator
            INNER JOIN order_elevator ON order_elevator.elevator_id = elevator.id
              AND order_elevator.status = " . OrderElevator::STATUS_ACTIVE;

    return self::model()->findAllBySql($sql);
  }

  public function moving()
  {
    $active_elevators = self::findActive();

    foreach ($active_elevators as $elevator)
    {
      $order = $elevator->order;

      if ($elevator->floor == $order->final_floor)
        $elevator->stop();
      else
        $elevator->move();
    }
  }

  public function move()
  {
    $order = $this->order;

    $this->floor = $order->final_floor;
    $this->save();
  }

  public function stop()
  {
    $order         = $this->order;
    $order->status = OrderElevator::STATUS_PROCESSED;
    $order->save();

    if ($child_order = $order->child)
    {
      $child_order->status = OrderElevator::STATUS_ACTIVE;
      $child_order->save();
    }
  }

  public function countVisitFloor($floor)
  {
    return OrderElevator::model()->count(array('condition' => 'status = ' . OrderElevator::STATUS_PROCESSED . ' AND elevator_id = ' . $this->getPrimaryKey() . ' AND final_floor = ' . $floor));
  }
}
