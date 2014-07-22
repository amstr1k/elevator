<?php
class SiteController extends Controller
{
  public function actionInit()
  {
    Yii::app()->db->createCommand()->truncateTable('elevator');
    Yii::app()->db->createCommand()->truncateTable('order_elevator');

    for ($i = 0; $i != 4; $i++)
    {
      $elevator        = new Elevator();
      $elevator->floor = rand(1, 10);
      $elevator->save();
    }

    $this->render('init');
  }

  public function actionIndex()
  {
    $order = new OrderElevator();

    if (Yii::app()->getRequest()->getIsPostRequest())
    {
      if ($active_order = OrderElevator::findActive())
        $active_order->elevator->moving();

      $this->_initOrderForFirstFloor();
    }

    $this->render(
      'index',
      array(
        'dataProvider' => new CActiveDataProvider('Elevator'),
        'model'        => $order
      )
    );
  }

  public function actionElevator($id)
  {
    if(!$elevator = Elevator::model()->findByPk($id))
      throw new CHttpException(404, 'Страница не существует');

    $this->render(
      'elevator',
      array(
        'dataProvider' => new CActiveDataProvider('OrderElevator',
            array('criteria' => array('condition' => 'status=0 AND elevator_id= ' . $elevator->getPrimaryKey()))
          ),
        'model'        => $elevator
      )
    );
  }

  public function actionCreate()
  {
    $order = new OrderElevator('create');

    if ($post = Yii::app()->getRequest()->getParam('OrderElevator'))
    {
      $order->setAttributes($post);

      if ($order->validate())
      {
        $elevator = Elevator::findNearest($order->start_floor);

        if ($elevator->floor != $order->start_floor) {
          $order_child              = new OrderElevator('create');
          $order_child->elevator_id = $elevator->getPrimaryKey();
          $order_child->start_floor = $elevator->floor;
          $order_child->final_floor = $order->start_floor;
          $order_child->direction   = OrderElevator::getDirection($elevator->floor, $order->start_floor);
          $order_child->status      = OrderElevator::STATUS_ACTIVE;
          $order_child->save();
        }

        $status = OrderElevator::STATUS_ACTIVE;

        if (isset($order_child))
        {
          $order->child_id = $order_child->getPrimaryKey();
          $status          = OrderElevator::STATUS_WAITING;
        }

        $order->status      = $status;
        $order->direction   = OrderElevator::getDirection($order->start_floor, $order->final_floor);
        $order->elevator_id = $elevator->getPrimaryKey();
        $order->setScenario('create');

        if ($order->save())
          $this->refresh();
      }
    }

    $this->render(
      'index',
      array(
        'dataProvider' => new CActiveDataProvider('Elevator'),
        'model'        => $order
      )
    );
  }

  protected function _initOrderForFirstFloor()
  {
    $order_for_first_floor    = OrderElevator::model()->find(array('condition' => 'status=1 AND final_floor=1'));
    $elevator_for_first_floor = Elevator::model()->find(array('condition' => 'floor=1'));

    if (!$order_for_first_floor && !$elevator_for_first_floor)
    {
      $order_for_first_floor              = new OrderElevator();
      $elevator_for_first_floor           = Elevator::findNearest(1);
      $order_for_first_floor->start_floor = $elevator_for_first_floor->floor;
      $order_for_first_floor->final_floor = 1;
      $order_for_first_floor->status      = OrderElevator::STATUS_ACTIVE;
      $order_for_first_floor->direction   = OrderElevator::DIRECTION_DOWN;
      $order_for_first_floor->elevator_id = $elevator_for_first_floor->getPrimaryKey();
      $order_for_first_floor->save();
    }
  }

  public function actionError()
  {
    $error=Yii::app()->errorHandler->error;

    $this->render('error', $error);
  }
}
