<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link type="text/css" rel="stylesheet" href="/css/global.css"/>
<link type="text/css" rel="stylesheet" href="/css/admin.css"/>
<link type="text/css" rel="stylesheet" href="/css/admin_users.css"/>
<title><?= $this->config->item('title') ?> - Admin - Users</title>
</head>
<body>
<?php $this->load->view('commons/header'); ?>
<?php $this->load->view('commons/admin_menu'); ?>
<div id="main-content">
	<?php if(count((array) $users) > 0) : ?>
    <table cellspacing="0">
        <tr>
            <th>Username</th>
            <th>Email</th>
            <th>Date Registered</th>
            <th>Last Active</th>
        </tr>
        <?php foreach($users as $i => $user) : ?>
        <?php $second_row = ($i % 2 == 0) ? '' : ' class="nd"'; ?>
        <tr>
            <td<?= $second_row ?>><a href="/users/<?= $user['username'] ?>"><?= $user['username'] ?></a></td>
            <td<?= $second_row ?>>
            	<a href="mailto:<?= $user['email'] ?>"><?= $user['email'] ?></a>
                <?php if($user['status'] == 0) : ?>
                <span class="not-verified">Not Verified</span>
                <?php endif; ?>
            </td>
            <td<?= $second_row ?>><?= empty($user['dateregistered']) ? 'Never' : timespan($user['dateregistered']) ?></td>
            <td<?= $second_row ?>><?= empty($user['activity_time']) ? 'Never' : timespan($user['activity_time']) ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
    <div id="pages"><?= $this->pagination->create_links() ?></div>
    <?php else : ?>
    <p>There are no users.</p>
    <?php endif; ?>
</div>
<?php $this->load->view('commons/footer'); ?>
</body>
</html>