<?php foreach ($users as $users_item): ?>

    <h2><?php echo $users_item['firstname'] ?></h2>
    <div id="main">
        <?php echo $users_item['lastname'] ?>
    </div>
    <p><a href="<?php echo base_url();?>index.php/users/<?php echo $users_item['id'] ?>">View user</a></p>

<?php endforeach ?>