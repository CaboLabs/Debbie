<li class="nav-item">
  <a id="<?=$item?>" class="nav-link collapsed" href="#" data-toggle="collapse"
    data-target="#collapseUtilities_<?=$item?>" aria-expanded="true" aria-controls="collapseUtilities">
    <?php $icon = '<i class="fa fa-check text-success"></i>' ?>
    <?php if ($is_failed): ?>
      <?php $icon = '<i class="fas fa-times text-warning"></i>' ?>
    <?php endif ?>
    <?=$icon?>
    <span class="d-inline-block text-truncate align-text-top" style="max-width: 150px;">
      <?=$item?>
    </span>
  </a>
  <div id="collapseUtilities_<?=$item?>" class="collapse" aria-labelledby="headingUtilities" data-parent="#accordionSidebar">
    <div id="collapse_<?=$item?>" class="bg-white py-2 collapse-inner rounded">
    <?php foreach ($namesSuitessubmenu as $submenu): ?>
      <?php $suites = explode("\\", $submenu); ?>
      <?php if (in_array($item, $suites)): ?>
        <a class="d-inline-block text-truncate collapse-item" style="max-width: 165px;" href="#"><?=$suites[2]?></a>
      <?php endif; ?>
    <?php endforeach; ?>
    </div>
  </div>
</li>