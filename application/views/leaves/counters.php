
<h1>My summary</h1>

<table class="table table-bordered table-hover">
<thead>
    <tr>
      <th>Leave type</th>
      <th>Taken</th>
      <th>Entitled</th>
    </tr>
  </thead>
  <tbody>
  <?php foreach ($summary as $key => $value) { ?>
    <tr>
      <td><?php echo $key; ?></td>
      <td><?php echo $value[0]; ?></td>
      <td><?php echo $value[1]; ?></td>
    </tr>
  <?php } ?>
  </tbody>
</table>
