<?php $messages = $this->messages->get(); ?>
<?php if (is_array($messages)) : ?>
<div id="messages">
	<?php foreach ($messages as $type => $msgs) : ?>
		<?php if (count($msgs > 0)) : ?>
			<?php foreach ($msgs as $message) : ?>
            <div class="<?= $type ?>"><?= $message ?></div>
            <?php endforeach; ?>
        <?php endif; ?>
    <?php endforeach; ?>
</div>
<?php endif; ?>