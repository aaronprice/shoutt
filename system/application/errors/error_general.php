<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link type="text/css" rel="stylesheet" href="/css/global.css"/>
<link type="text/css" rel="stylesheet" href="/css/error.css"/>
<title>SHOUTT! - Error</title>
</head>
<body>
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
                <div id="user-options"><a href="/login">Login</a> | <a href="/signup">Sign Up</a></div>
                <form action="/search" method="get"><input name="q" value=""/><input type="submit" value="Search"/></form>
        </div>
        <h2><a href="/">SHOUTT!  <img src="/img/tt.gif" alt="TT"/></a></h2>
    </div><div id="main-content">
    <div id="error">
        <h3><?= $heading ?></h3>
        <p><?= $message ?></p>
    </div>
</div>
</div>
<div id="footer-container">
	<div id="footer"><a href="/about">About</a> | <a href="/terms">Terms of Use</a> | <a href="/privacy">Privacy Policy</a> | <a href="/contact">Contact Us</a></div>
</div></body>
</html>