<div class="posts form">
<?php echo $this->Form->create('Post');?>
	<fieldset>
		<legend><?php __('Add Post'); ?></legend>
	<?php
		echo $this->Form->input('body');
		echo $this->Form->input('y');
		echo $this->Form->input('x');
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit', true));?>
</div>
<div class="actions">
	<h3><?php __('Actions'); ?></h3>
	<ul>

		<li><?php echo $this->Html->link(__('List Posts', true), array('action' => 'index'));?></li>
		<li><?php echo $this->Html->link(__('List Users', true), array('controller' => 'users', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New User', true), array('controller' => 'users', 'action' => 'add')); ?> </li>
	</ul>
</div>