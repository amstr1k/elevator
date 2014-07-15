<?php

$this->pageTitle=Yii::app()->name;
?>

<h1>Logs elevator <?= $model['id'] ?></h1>

<? foreach($moder['orders'] as $order) { ?>
  <b><?= $order->id ?></b>
<? } ?>