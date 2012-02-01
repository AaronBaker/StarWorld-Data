<div style="background-color:yellow;" ><?php if ($session->check('Message.flash')) { $session->flash(); } ?></div>

<h2>Please enter the username or Email Address for your account.</h2>
<?php
 echo $form->create('User', array('action' => 'forgot'));
    echo $form->input('username',array("placeholder"=>"Username or Email"));
    echo $form->end('Reset Password');

?>

