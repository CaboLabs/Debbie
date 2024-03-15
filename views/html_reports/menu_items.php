<li class="nav-item position-relative">
  <a id="<?=$item?>" class="nav-link collapsed" href="#" data-toggle="collapse"
    data-target="#collapseUtilities_<?=$item?>" aria-expanded="true" aria-controls="collapseUtilities">
    <?php $style_item = "fa fa-check text-success" ?>
    <?php if ($is_failed): ?>
      <?php $style_item = "fas fa-times text-warning" ?>
    <?php endif; ?>
    <i class="<?=$style_item?>"></i>
    <span class="mr-1"><?=$item?></span>
    <?php $style_badge = "top-0 badge badge-success" ?>
    <?php if ($badge['case_successfull'] < $badge['total_cases']): ?>
      <?php $style_badge = "top-0 badge badge-danger" ?>
    <?php endif; ?>
    <span class="<?=$style_badge?>"><?=$badge['case_successfull']?> / <?=$badge['total_cases']?></span>
  </a>
  <div id="collapseUtilities_<?=$item?>" class="collapse" aria-labelledby="headingUtilities" data-parent="#accordionSidebar">
    <div id="collapse_<?=$item?>" class="bg-white py-2 collapse-inner rounded">
    <?php foreach ($namesSuitessubmenu as $submenu): ?>
      <?php $suites = explode("\\", $submenu); ?>
      <?php if (in_array($item, $suites)): ?>
        <a id="<?=$suites[2]?>" class="collapse-item" style=<?=$style_text?> href="#"><?=$suites[2]?></a>
      <?php endif; ?>
    <?php endforeach; ?>
    </div>
  </div>
</li>