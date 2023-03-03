<table class="table">
  <thead>
    <tr>
      <th scope="col">Suite</th>
      <th scope="col">Class</th>
      <th class="text-right" scope="col">Successful</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($total_cases_successful as $total_case_successful): 
      $names_successful = explode("\\", $total_case_successful['case']); ?>
      <tr>
        <td><?=$names_successful[1]?></td>
        <td><?=$names_successful[2]?></td>
        <td class="text-right"><?=$total_case_successful["case_successful"]?></td>
      </tr>
    <?php endforeach; ?> 
  </tbody>
</table>