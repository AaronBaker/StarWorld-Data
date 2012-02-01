<div style="clear: both">
    <h1>Reset Password</h1>
	<div >
		
		    <?php echo $form->create('User', array('url' => 'otpreset/'.$username.'/'.$ttime.'/'.$hash.'/') ); ?>
			<div>
				
                            Your user name is <b><?php echo $username;?></b>.  Please enter your new password:<br><Br>
				<span  class="input text required">New password:</span><span ><?php echo $form->input('password',array('tabindex' => '2' ,'size' => '50' , 'maxlength' => '50' , 'label'=> false,"placeholder"=>"New Password")); ?></span>

				<?php echo $form->end(array('tabindex' => '4' ,'label' => 'Reset Password', 'name' => 'save'));  ?>
        	</div>
		</div>
	</div>
</div>


