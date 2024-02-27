<li class="nav-item">
  <a id="<?=$item?>" class="nav-link collapsed" href="#" data-toggle="collapse"
  data-target="#collapseUtilities_<?=$item?>" aria-expanded="true" aria-controls="collapseUtilities">
  <?php if ($is_failed): ?>
  <i class="fas fa-times text-warning"></i>
  <?php else: ?>
  <i class="fa fa-check text-success"></i>
  <?php endif; ?>
    <span>
      <?=$item?>
    </span>
  </a>
  <div id="collapseUtilities_<?=$item?>" class="collapse" aria-labelledby="headingUtilities" data-parent="#accordionSidebar">
    <div id="collapse_<?=$item?>" class="bg-white py-2 collapse-inner rounded">
    <?php foreach ($namesSuitessubmenu as $submenu): ?>
      <?php $suites = explode("\\", $submenu); ?>
      <?php if (in_array($item, $suites)): ?>
        <a class="collapse-item" href="#"><?=$suites[2]?></a>
      <?php endif; ?>
    <?php endforeach; ?>
    </div>
  </div>
</li>