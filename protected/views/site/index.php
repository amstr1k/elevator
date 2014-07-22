<?php $this->pageTitle = Yii::app()->name; ?>

<?php $this->widget('zii.widgets.CListView', array(
  'dataProvider' => $dataProvider,
  'summaryText'  => false,
  'itemView'     => '_view',
)); ?>

<?= CHtml::form('/', 'post') ?>
<?= CHtml::submitButton('Вперёд') ?>
<?= CHtml::endForm() ?>

<? if (!count(OrderElevator::model()->find(array('condition' => 'status= ' . OrderElevator::STATUS_ACTIVE)))) { ?>
  <?php $form = $this->beginWidget('CActiveForm', array(
    'action'      => Yii::app()->createUrl('site/create'),
    'htmlOptions' => array('enctype' => 'multipart/form-data', 'class' => 'order'),
  ));
  ?>
  <div>
    <div>
      <?= $form->error($model, 'start_floor', array('class' => 'error')) ?>
      <?= $form->labelEx($model, 'start_floor') ?></br>
      <?= $form->textfield($model, 'start_floor', array('placeholder' => 'Этаж на который вызывать')) ?>
    </div>
    <div>
      <?= $form->error($model, 'final_floor', array('class' => 'error')) ?>
      <?= $form->labelEx($model, 'final_floor') ?></br>
      <?= $form->textfield($model, 'final_floor', array('placeholder' => 'Этаж на котором находитесь')) ?>
    </div>
    <button class="btn btn-info" type="submit">Отправить</button>
  </div>
  <?php $this->endWidget(); ?>
<? } ?>
