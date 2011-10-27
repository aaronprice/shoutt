<?php if(isset($user_info['id']) && $this->user->has_invites($user_info['id'])) : ?>
<div class="side-box">
    <h4><div id="manage-invites"><a href="/settings/invitations">Manage</a></div>Invite a friend</h4>
    <p>You have <span id="invites_remaining"><?= $this->user->invites_remaining($user_info['id']) ?></span> invites remaining. Separate multiple addresses with a comma.</p>
    <form action="#" method="post" name="invite">
        <label>Email</label><input name="email" class="txt" value=""/><br/>
        <input type="submit" value="Send Invite"/>
        <span id="inv_info"></span>
    </form>
</div>
<?php endif; ?>