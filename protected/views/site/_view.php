<? if(!$data->order) { ?>
  <p><a href="/site/elevator/<?= $data['id'] ?>">Лифт <?= $data['id'] ?></a>, находится на <?= $data['floor'] ?> этаже</p>
<? } else { ?>
  <p><a href="/site/elevator/<?= $data['id'] ?>">Лифт <?= $data['id'] ?></a>, находится на <?= $data['floor'] ?> этаже, движется на <?=$data->order->final_floor ?></p>
<? } ?>
