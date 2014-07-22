<h1>Logs elevator <?= $model['id'] ?></h1>

<?
  for ($i = 1; $i <= 10; $i++)
  {
    echo "На {$i} этаже был {$model->countVisitFloor($i)} раз <br/>";
  }
?>

<h3>Итерации лифта</h3>
<?php $this->widget('zii.widgets.CListView', array(
  'dataProvider' => $dataProvider,
  'itemView'     => '_elevator_iteration',
  'emptyText'    => 'Данный лифт не передвигался',
)); ?>


