<div id="profile-menu">
	<div id="actual-profile-menu">
        <a href="/users/<?= $url_username ?>">Profile</a>
        | <a href="/users/<?= $url_username ?>/history">History</a>
        | <a href="/users/<?= $url_username ?>/history/favorites">Favorites</a>
        <?php if($this->util->user_is_admin()) : ?>
        | <a href="/users/<?= $url_username ?>/log">Log</a>
        | <a href="/users/<?= $url_username ?>/tech_details">Tech Details</a>
            <?php if($show_ban) : ?>
            | <?php if($info['status'] == '2') : ?>
              <a id="res">Unban</a>
              <?php else : ?>
              <a id="res">Ban</a>
              <?php endif; ?>
            <?php endif; ?>
        <?php endif; ?>
    </div>
    <?php if(empty($info['name'])) : ?>
    <h2><?= $url_username ?></h2>
    <?php else : ?>
    <h2><?= $info['name'] ?> (<?= $url_username ?>)</h2>
    <?php endif; ?>
    <?php if($info['gender'] > 0 || !empty($info['location'])) : ?>
    <div id="user-info">
    	<?php if(false) : ?>A <?php if(!empty($info['dob'])) : ?><?= $this->util->get_age($info['dob']) ?> year old<?php endif; ?><?php endif; ?> <?php if($info['gender'] > 0) : ?><?= ($info['gender'] == '1') ? 'Female' : 'Male' ?><?php endif; ?> <?php if(!empty($info['location'])) : ?>from <?= $info['location'] ?><?php endif; ?>
    </div>
    <?php endif; ?>
</div>