<?php $session_username = $this->session->userdata($this->config->item('session_key').'_unm'); ?>
<div id="disclaimer">
	<div>
        <h2>IMPORTANT</h2>
        Please be advised that stories posted on this website are user generated 
        and are not filtered, edited, or fact checked.
    </div>
</div>
<div id="container">
	<div id="header">
        <div id="top-options">
        <?php if(empty($session_username)) : ?>
        <div id="user-options"><a href="/login">Login</a> | <a href="/signup">Sign Up</a></div>
        <?php else : ?>
        <div id="user-options">
			<?php if($this->util->user_is_admin()) : ?>
            <a href="/admin">Admin</a> |
            <?php endif; ?>
        	<a href="/users/<?= $session_username ?>">Profile</a> | 
            <a href="/settings">Settings</a> | 
            <a href="/compose">Compose</a> | 
            <a href="/submit">Submit</a> | 
            <a href="/logout">Logout</a>
        </div>
        <?php endif; ?>
        <form action="/search" method="get">
            <input name="q" value="<?= isset($q) ? htmlspecialchars($q) : '' ?>"/>
            <input type="submit" value="Search"/>
        </form>
        </div>
        <h2><a href="/">SHOUTT!  <img src="/img/tt.gif" alt="TT"/></a></h2>
    </div>