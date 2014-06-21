<?php
include 'forms.php';
$start=$appointment->start;
$start_sec=strtotime($start);
if (isset($appointment->sessions)){
	foreach ($appointment->sessions as $session){
		$session_end=strtotime($session->end);
		if ($session_end>$start_sec){
			$start_sec=$session_end;
			$start=$session->end;
		}
	}
}
$end=date($db_time_format,$start_sec+3600);
?>
<div class="addsession">
	<h2>
		<?php echo loc('add session');?>
	</h2>
	<form class="addsession" method="POST">
		<?php echo '<input type="hidden" name="newsession[aid]" value="'.$appointment->id.'" />'.PHP_EOL;?>
		<div class="start">
			<?php echo loc('start'); datepicker('newsession[start]',$start); echo loc('start time'); timepicker('newsession[start]',$start); ?>
		</div>
		<div class="end">
			<?php echo loc('end (optional)'); datepicker('newsession[end]',$end); echo loc('end time'); timepicker('newsession[end]',$end) ?>
		</div>
		<div id="description">
			<?php echo loc('description'); ?>
			<input type="text" name="newsession[description]" />
		</div>
		<div class="submit">
			<input type="checkbox" name="addsession" />
			<?php echo loc('Add a session to this appointment in the next step.'); ?>
			<input type="checkbox" name="addlink" />
			<?php echo loc('Add a link to this appointment in the next step.'); ?>
			<?php echo '<input type="submit" value="'.loc('add session').'"/><br/>'.PHP_EOL; ?>
		</div>
	</form>
</div>