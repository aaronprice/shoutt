<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="keywords" content="submit news,post news,share news,trinidad news,trinidad tobago news" />
<meta name="description" content="Got news? Be heard! Write the news as you see it and read it as it happens. Pinpoint story locations in T&T and share images and video. Got something to say? Shoutt it!" />
<link type="text/css" rel="stylesheet" href="/css/global.css"/>
<link type="text/css" rel="stylesheet" href="/css/signup.css"/>
<title><?= $this->config->item('title') ?> - Sign up</title>
</head>
<body>
<?php $this->load->view('commons/header'); ?>
<div id="main-content">
	<h3>Sign up</h3>
    <form action="/signup" method="post">
    	<input name="vcode" type="hidden" value="<?= $vcode ?>"/>
        <label>Username</label><input class="txt" name="username" value="<?= set_value('username') ?>"/><br/>
        <?= form_error('username') ?>
        <label>Password</label><input class="txt" name="password" type="password"/><br/>
        <?= form_error('password') ?>
        <label>Retype Password</label><input class="txt" name="confirm" type="password"/><br/>
        <?= form_error('confirm') ?>
        <label>Email</label><input class="txt" name="email" value="<?= set_value('email', $email) ?>"/><br/>
        <?= form_error('email') ?>
        <label>Date of Birth</label>
		<select name="day">
        	<option value="Day"<?php echo set_select('day', 'Day', TRUE); ?>>Day</option>
        	<?php for($i = 1; $i <= 9; ++$i) : ?>
            <option value="0<?= $i ?>"<?php echo set_select('day', '0'.$i); ?>><?= $i ?></option>
            <?php endfor; ?>
        	<?php for($i = 10; $i <= 31; ++$i) : ?>
        	<option value="<?= $i ?>"<?php echo set_select('day', $i); ?>><?= $i ?></option>
            <?php endfor; ?>
        </select>
        <select name="month">
        	<option value="Month"<?php echo set_select('month', 'Month', TRUE); ?>>Month</option>
        	<option value="01"<?php echo set_select('month', '01'); ?>>January</option>
            <option value="02"<?php echo set_select('month', '02'); ?>>February</option>
            <option value="03"<?php echo set_select('month', '03'); ?>>March</option>
            <option value="04"<?php echo set_select('month', '04'); ?>>April</option>
            <option value="05"<?php echo set_select('month', '05'); ?>>May</option>
            <option value="06"<?php echo set_select('month', '06'); ?>>June</option>
            <option value="07"<?php echo set_select('month', '07'); ?>>July</option>
            <option value="08"<?php echo set_select('month', '08'); ?>>August</option>
            <option value="09"<?php echo set_select('month', '09'); ?>>September</option>
            <option value="10"<?php echo set_select('month', '10'); ?>>October</option>
            <option value="11"<?php echo set_select('month', '11'); ?>>November</option>
            <option value="12"<?php echo set_select('month', '12'); ?>>December</option>
        </select>
        <select name="year">
        	<option value="Year"<?php echo set_select('year', 'Year', TRUE); ?>>Year</option>
        	<?php 
				$start_year = date('Y') - 18;	
				$end_year = $start_year - 75;
			?>
            <?php for($i = $start_year; $i > $end_year; --$i) : ?>
            <option value="<?= $i ?>"<?php echo set_select('year', $i); ?>><?= $i ?></option>
            <?php endfor; ?>
        </select><br/>
        <?= form_error('day') ?>
        <?= form_error('month') ?>
        <?= form_error('year') ?>
        <label>Enter the text you see in the image below</label><input name="captcha" class="captcha"/><br/>
        <?= $image ?><br/>
        <?= form_error('captcha') ?>
        <?= form_error('ip') ?>
        <br/>
        <p>I agree to the <a href="/terms">Terms of Use</a> and <a href="/privacy">Privacy Policy</a></p>
        <div class="inv"><input name="ip" value="<?= set_value('ip') ?>"/></div>
        <input type="submit" value="I agree, Sign up."/>
    </form>
</div>
<?php $this->load->view('commons/footer'); ?>
<?php if($_SERVER['REMOTE_ADDR'] != '127.0.0.1') : ?>
<script type="text/javascript">
var gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "http://www.");
document.write(unescape("%3Cscript src='" + gaJsHost + "google-analytics.com/ga.js' type='text/javascript'%3E%3C/script%3E"));
</script>
<script type="text/javascript">
try {
var pageTracker = _gat._getTracker("UA-4391221-3");
pageTracker._trackPageview();
} catch(err) {}</script>
<?php if(false) : ?><script src="http://static.ak.connect.facebook.com/js/api_lib/v0.4/FeatureLoader.js.php/en_US" type="text/javascript"></script><script type="text/javascript">FB.init("62ec84027147641fc0436c7578b88b1d");</script><?php endif; ?>
<?php endif; ?>
</body>
</html>
