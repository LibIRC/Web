<?php require_once("header.php"); ?>

			<div class="pageContent">
				<div id="main">
					<div class="container">
						<h1>News</h1>
<?php
$result = mysql_query( "SELECT * FROM anope_NewsItem" );
$storeArray = Array();
while ( $row = mysql_fetch_array( $result ) ) {
	echo "<h2>".$row['text']."</h2>";
	echo "<br>";
}
?>
						<h1>Welcome to the LibIRC Project</h1>
						<p>
							We're currently in the beta stages of the project.<br>
							<br>
							Please join <a href="http://irc.libirc.so:7797/">#LibIRC-Devel on irc.libirc.so</a><br>
							<br>
							If you wish to contribute to our GitHub is located at <a href="https://github.com/libirc">https://github.com/libirc/</a>
						</p>
						<div class="clear"></div>
					</div>
				</div>
			</div>
		</div>
	</body>
</html>
