<?php

class SiteController extends Controller
{
  public function actionInit()
  {
    Yii::app()->db->createCommand()->truncateTable('elevator');
    Yii::app()->db->createCommand()->truncateTable('order_elevator');

    for($i = 0; $i != Elevator::ELEVATORS_COUNT; $i++)
    {
      $elevator = new Elevator();
      $elevator->floor = rand(1,10);
      $elevator->save();
    }

    $this->render('init');
  }

  public function actionIndex()
  {
    $order = new OrderElevator();

    $this->render(
      'index',
      array(
        'dataProvider' => new CActiveDataProvider('Elevator'),
        'model' => $order
      )
    );
  }

  public function actionCreate()
  {
    $order = new OrderElevator();

    if($post = Yii::app()->getRequest()->getParam('OrderElevator'))
    {
      $order->setAttributes($post);

      if($order->validate())
      {
        $elevator = Elevator::findNearest($order->start_floor);
        if($elevator->floor != $order->start_floor)
        {
          $order_child = new OrderElevator();
          $order_child->elevator_id = $elevator->getPrimaryKey();
          $order_child->start_floor = $elevator->floor;
          $order_child->final_floor = $order->start_floor;
          $order_child->direction = OrderElevator::getDirection($elevator->floor, $order->start_floor);
          $order_child->status = 1;
          $order_child->save();
        }

        $status = 1;

        if(isset($order_child))
        {
          $order->child_id = $order_child->getPrimaryKey();
          $status = 2;
        }

        $order->status = $status;
        $order->direction = OrderElevator::getDirection($order->start_floor, $order->final_floor);
        $order->elevator_id = $elevator->getPrimaryKey();

        $order->setScenario('create');
        if($order->save())
          $this->refresh();
      }
    }

    $this->render(
      'index',
      array(
        'dataProvider' => new CActiveDataProvider('Elevator'),
        'model' => $order
      )
    );
  }


}