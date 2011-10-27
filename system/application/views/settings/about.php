<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link type="text/css" rel="stylesheet" href="/css/global.css"/>
<link type="text/css" rel="stylesheet" href="/css/about.css"/>
<script type="text/javascript" src="/js/jquery-1.3.2.min.js"></script>
<script type="text/javascript">
$(function(){
<?php $this->load->view('commons/invite_js'); ?>
});
</script>
<title><?= $this->config->item('title') ?> - Settings - About Me</title>
</head>
<body>
<?php $this->load->view('commons/header'); ?>
<?php $this->load->view('commons/messages'); ?>
<div id="main-content">
	<?php $this->load->view('commons/profile_settings'); ?>
    <h3>About Me</h3>
    <form action="/settings/about" method="post">
        <label>Username</label>
        <div class="non-edit"><?= $user_info['username'] ?></div>
        <label>Full Name</label><input class="txt" name="name" value="<?= set_value('name', $user_info['name']) ?>"/><br/>
        <?= form_error('name') ?>
        <label>Email</label>
        <div class="non-edit"><?= $user_info['email'] ?></div>
        <label>Gender</label>
        <select name="gender">
        	<option value="0"<?php echo set_select('gender', '0', ($user_info['gender'] == '0')); ?>>Select</option>
            <option value="1"<?php echo set_select('gender', '1', ($user_info['gender'] == '1')); ?>>Female</option>
            <option value="2"<?php echo set_select('gender', '2', ($user_info['gender'] == '2')); ?>>Male</option>
        </select><br/>
        <?= form_error('gender') ?>
        <label>Date of Birth</label>
		<select name="day">
        	<?php for($i = 1; $i <= 9; ++$i) : ?>
            <option value="0<?= $i ?>"<?php echo set_select('day', '0'.$i, (substr($user_info['dob'], 8, 2) == '0'.$i)); ?>><?= $i ?></option>
            <?php endfor; ?>
        	<?php for($i = 10; $i <= 31; ++$i) : ?>
        	<option value="<?= $i ?>"<?php echo set_select('day', $i, (substr($user_info['dob'], 8, 2) == $i)); ?>><?= $i ?></option>
            <?php endfor; ?>
        </select>
        <select name="month">
        	<option value="01"<?php echo set_select('month', '01', (substr($user_info['dob'], 5, 2) == '01')); ?>>January</option>
            <option value="02"<?php echo set_select('month', '02', (substr($user_info['dob'], 5, 2) == '02')); ?>>February</option>
            <option value="03"<?php echo set_select('month', '03', (substr($user_info['dob'], 5, 2) == '03')); ?>>March</option>
            <option value="04"<?php echo set_select('month', '04', (substr($user_info['dob'], 5, 2) == '04')); ?>>April</option>
            <option value="05"<?php echo set_select('month', '05', (substr($user_info['dob'], 5, 2) == '05')); ?>>May</option>
            <option value="06"<?php echo set_select('month', '06', (substr($user_info['dob'], 5, 2) == '06')); ?>>June</option>
            <option value="07"<?php echo set_select('month', '07', (substr($user_info['dob'], 5, 2) == '07')); ?>>July</option>
            <option value="08"<?php echo set_select('month', '08', (substr($user_info['dob'], 5, 2) == '08')); ?>>August</option>
            <option value="09"<?php echo set_select('month', '09', (substr($user_info['dob'], 5, 2) == '09')); ?>>September</option>
            <option value="10"<?php echo set_select('month', '10', (substr($user_info['dob'], 5, 2) == '10')); ?>>October</option>
            <option value="11"<?php echo set_select('month', '11', (substr($user_info['dob'], 5, 2) == '11')); ?>>November</option>
            <option value="12"<?php echo set_select('month', '12', (substr($user_info['dob'], 5, 2) == '12')); ?>>December</option>
        </select>
        <select name="year">
        	<?php 
				$start_year = date('Y') - 10;	
				$end_year = $start_year - 75;
			?>
            <?php for($i = $start_year; $i > $end_year; --$i) : ?>
            <option value="<?= $i ?>"<?php echo set_select('year', $i, (substr($user_info['dob'], 0, 4) == $i)); ?>><?= $i ?></option>
            <?php endfor; ?>
        </select><br/>
        <?= form_error('day') ?>
        <?= form_error('month') ?>
        <?= form_error('year') ?>
        <label>Location</label><input class="txt" name="location" value="<?= set_value('location', $user_info['location']) ?>"/><br/>
        <?= form_error('location') ?>
        <input type="submit" value="Save Information"/>
    </form>
</div>
<?php $this->load->view('commons/footer'); ?>
</body>
</html>
