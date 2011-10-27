<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xmlns:fb="http://www.facebook.com/2008/fbml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>SHOUTT! - Facebook Connect Testing</title>
<style type="text/css">
body {
	font-family: Arial, Helvetica, sans-serif;
	padding: 20px;
}

textarea {
	font-family: Arial, Helvetica, sans-serif;
	width: 302px;
	height: 100px;
}

input.txt {
	width: 250px;
	margin: 0 0 10px 10px;
}

#user {
	width: 320px;
}
</style>
</head>
<body>
<h3>Facebook Connect Testing.</h3>
<p>Following the Screen cast for integrating with facebook.</p>
<form action="#">
	<div id="user">
		<label>Name</label><input name="name" class="txt"/>
        Or, you can <fb:login-button length="long" onlogin="update_user_box();"></fb:login-button>
    </div>
    <textarea name="comment"></textarea><br/>
    <input type="button" value="Submit Comment"/>
</form>

<br/>
<br/>
<br/>


<fb:comments></fb:comments>

<script src="http://static.ak.connect.facebook.com/js/api_lib/v0.4/FeatureLoader.js.php/en_US" type="text/javascript"></script>
<script type="text/javascript">
function update_user_box() {

	var user_box = document.getElementById('user');
	
	user_box.innerHTML = 
	'<span>'+
		'<fb:profile-pic uid="loggedinuser" facebook-logo="true" size="square"></fb:profile-pic>'+
		'Welcome, <fb:name uid="loggedinuser" useyou="false"></fb:name>. '+
		'You are signed in with your facebook account.'+
	'</span>';

	FB.XFBML.Host.parseDomTree();
}

FB.init("62ec84027147641fc0436c7578b88b1d", "/xd_receiver.htm", {"ifUserConnected": update_user_box});
</script>
</body>
</html>