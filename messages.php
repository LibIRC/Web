<?php
define( 'INCLUDE_CHECK', true );
require 'connect.php';
require 'functions.php';
session_name( 'Login' );
session_set_cookie_params( 2 * 7 * 24 * 60 * 60 );
session_start();
if ( $_SESSION['id'] && !isset( $_COOKIE['Remember'] ) && !$_SESSION['rememberMe'] ) {
	$_SESSION = array();
	session_destroy();
}
if ( isset( $_GET['logoff'] ) ) {
	$_SESSION = array();
	session_destroy();
	header( "Location: index.php" );
	exit;
}
if ( $_POST['submit'] === 'Login' ) {
	$err = array();
	if ( !$_POST['username'] || !$_POST['password'] ){
		$err[] = 'All the fields must be filled in!';
	}
	if ( !count( $err ) ) {
		$_POST['username']   = mysql_real_escape_string( $_POST['username'] );
		$_POST['password']   = mysql_real_escape_string( $_POST['password'] );
		$_POST['rememberMe'] = (int) $_POST['rememberMe'];
		$row                 = mysql_fetch_assoc( mysql_query( "SELECT * FROM anope_NickCore WHERE email='{$_POST['username']}' AND pass='md5:" . md5( $_POST['password'] ) . "'" ) );
		if ( $row['display'] ) {
			$_SESSION['display']    = $row['display'];
			$_SESSION['id']         = $row['id'];
			$_SESSION['rememberMe'] = $_POST['rememberMe'];
			setcookie( 'Remember', $_POST['rememberMe'] );
		} else {
			$err[] = 'Wrong username and/or password!';
		}
	}
	if ( $err ) {
		$_SESSION['msg']['login-err'] = implode( '<br />', $err );
	}
	header( "Location: index.php" );
	exit;
} else if ( $_POST['submit'] === 'Register' ) {
	$err = array();
	if ( strlen( $_POST['username'] ) < 4 || strlen( $_POST['username'] ) > 32 ) {
		$err[] = 'Your username must be between 3 and 32 characters!';
	}
	if ( preg_match( '/[^a-z0-9\-\_\.]+/i', $_POST['username'] ) ) {
		$err[] = 'Your username contains invalid characters!';
	}
	if ( !checkEmail( $_POST['email'] ) ) {
		$err[] = 'Your email is not valid!';
	}
	if ( !count( $err ) ) {
		$pass              = mysql_real_escape_string( $_POST['password'] );
		$_POST['email']    = mysql_real_escape_string( $_POST['email'] );
		$_POST['username'] = mysql_real_escape_string( $_POST['username'] );
		mysql_query( "INSERT INTO anope_NickCore ( display, pass, email, access, timestamp ) VALUES ( '" . $_POST['username'] . "', 'md5:" . md5( $pass ) . "', '" . $_POST['email'] . "', '" . $_SERVER['REMOTE_ADDR'] . "', NOW() )" );
		mysql_query( "INSERT INTO anope_NickAlias ( nc, nick, timestamp ) VALUES ( '" . $_POST['username'] . "', '" . $_POST['username'] . "', NOW() )" );
		if ( mysql_affected_rows( $link ) == 1 ) {
			$_SESSION['msg']['reg-success'] = 'Your account has been successfully created!';
		} else {
			$err[] = 'This username is already taken!';
		}
	}
	if ( count( $err ) ) {
		$_SESSION['msg']['reg-err'] = implode( '<br>', $err );
	}
	header( "Location: index.php" );
	exit;
}
?>
<!DOCTYPE html>
<html class="no-js">
	<head>
		<meta charset="utf-8">
		<title>LibIRC.so</title>
		<meta name="description" content="The future of social media and chat.">
		<meta name="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=1">
		<meta name="apple-mobile-web-app-capable" content="yes">
		<meta name="apple-mobile-web-app-status-bar-style" content="black">
		<link rel="stylesheet" href="demo.css">
		<link rel="stylesheet" href="login_panel/css/slide.css">
		<script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
		<script src="login_panel/js/slide.js"></script>
<?php if ( $_SESSION['msg'] ) { ?>
		<script>
			$(document).ready(function () {
				$("div#panel").show();
				$("#toggle a").toggle();
			});
		</script>
<?php } ?>
	</head>
	<body>
		<!-- Panel -->
		<div id="toppanel">
			<div id="panel">
				<div class="content clearfix">
					<div class="left">
						Lulz let's put something here?
					</div>
<?php if ( !$_SESSION['id'] ) : ?>
						<div class="left">
							<!-- Login Form -->
							<form class="clearfix" action="" method="post">
								<h1>Member Login</h1>
<?php
if ( $_SESSION['msg']['login-err'] ) {
	echo '<div class="err">'.$_SESSION['msg']['login-err'].'</div>';
	unset( $_SESSION['msg']['login-err'] );
}
?>
								<label class="grey" for="username">Email:</label>
								<input class="field" type="text" name="username" id="username" value="" size="23" />
								<label class="grey" for="password">Password:</label>
								<input class="field" type="password" name="password" id="password" size="23" />
								<label><input name="rememberMe" id="rememberMe" type="checkbox" checked="checked" value="1" /> &nbsp;Remember me</label>
								<div class="clear"></div>
								<input type="submit" name="submit" value="Login" class="bt_login" />
							</form>
						</div>
						<div class="left right">
							<!-- Register Form -->
							<form action="" method="post">
								<h1>Not a member yet? Sign Up!</h1>
<?php
if ( $_SESSION['msg']['reg-err'] ) {
	echo '<div class="err">'.$_SESSION['msg']['reg-err'].'</div>';
	unset( $_SESSION['msg']['reg-err'] );
}
if ( $_SESSION['msg']['reg-success'] ) {
	echo '<div class="success">'.$_SESSION['msg']['reg-success'].'</div>';
	unset( $_SESSION['msg']['reg-success'] );
}
?>
								<label class="grey" for="username">Username:</label>
								<input class="field" type="text" name="username" id="username" value="" size="23" />
								<label class="grey" for="email">Email:</label>
								<input class="field" type="text" name="email" id="email" size="23" />
								<label class="grey" for="email">Password</label>
								<input class="field" type="text" name="password" id="password" size="23" />
								<input type="submit" name="submit" value="Register" class="bt_register" />
							</form>
						</div>
<?php else : ?>
						<div class="left">
							<h1>Members panel</h1>
							<a href="home.php">Home</a>
							<a href="?logoff">Log off</a>
						</div>
						<div class="left right"></div>
<?php endif; ?>
					</div>
				</div> <!-- /login -->
				<!-- The tab on top -->
				<div class="tab">
					<ul class="login">
						<li class="left">&nbsp;</li>
						<li>Hello <?php echo $_SESSION['display'] ? $_SESSION['display'] : 'Guest';?>!</li>
						<li class="sep">|</li>
						<li id="toggle">
							<a id="open" class="open" href="#"><?php echo $_SESSION['id']?'Open Panel':'Log In | Register';?></a>
							<a id="close" style="display: none;" class="close" href="#">Close Panel</a>
						</li>
						<li class="right">&nbsp;</li>
					</ul>
				</div> <!-- / top -->
			</div> <!--panel -->
			<div id="main">
				<div class="container">
<?php
$id = $_GET['id'];
$result = mysql_query( "SELECT * FROM anope_Memo WHERE owner='".$_SESSION['display']."' AND id='".$id."'" );
mysql_query( "UPDATE anope_Memo SET `unread` = '0' WHERE `anope_Memo`.`id` ='".$id."'" );
while ( $row = mysql_fetch_array( $result ) ) {
	echo $row['sender'].":".$row['text']."<br>";
}
?>
				</div>
				<div class="container">
					<a href="http://irc.libirc.so:7797/?nick=<?php echo $_SESSION['display'] ?>">Login to the chat client!
				</div>
			</div>
		</div>
	</body>
</html>
