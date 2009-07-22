<?php
 if( isset($_POST[name]) ) {
  $name = $_POST[name];
  $email = $_POST[email];
  $cap = $_POST[captcha];
  
  if( $cap == $_SESSION[captcha] and $name!='' and $email!='' )
  {

  }

 }
?>
<h2>Subscribe to Mailing List</h2>
<p>Subscribe to the mailing list and get yourself updated regularly</p>
<p>
Mailing list will be added pretty soon....in a couple of days
You can contact me for further info <a href="index.php?slug=contact-us">here</a>
<!--
<form action="mailinglist.php" method="post">
<input type="text" name="name"> - Name<br />
<input type="text" name="email"> - Email<br />
<input type="submit" value="Subscribe"><br />
</form>
-->
</p>