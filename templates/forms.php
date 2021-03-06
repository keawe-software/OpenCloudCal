<?php
  function datepicker($name,$time=null){
  	if ($time==null) $time=date(TIME_FMT);
    print '<div class="datepicker">'.PHP_EOL;

    $year=date('Y');
    $selected_year=substr($time, 0,4);
    print '  <select name="'.$name.'[year]" size="1">'.PHP_EOL;
    for ($i=0; $i<10; $i++){
      if ($year==$selected_year){
        print '    <option selected>'.$year.'</option>'.PHP_EOL;
      } else {
        print '    <option>'.$year.'</option>'.PHP_EOL;
      }
      $year+=1;
    }
    print '  </select>'.PHP_EOL;

    $month=1;
    $selected_month=ltrim(substr($time, 5,2),'0');
    print '  <select name="'.$name.'[month]" size="1">'.PHP_EOL;
    for ($i=0; $i<12; $i++){
      if ($month==$selected_month){
        print '    <option selected>'.$month.'</option>'.PHP_EOL;
      } else {
        print '    <option>'.$month.'</option>'.PHP_EOL;
      }
      $month+=1;
    }
    print '  </select>'.PHP_EOL;

    $day=1;
    $selected_day=ltrim(substr($time, 8,2),'0');
    print '  <select name="'.$name.'[day]" size="1">'.PHP_EOL;
    for ($i=0; $i<31; $i++){
      if ($day==$selected_day){
        print '    <option selected>'.$day.'</option>'.PHP_EOL;
      } else {
        print '    <option>'.$day.'</option>'.PHP_EOL;
      }
      $day+=1;
    }
    print '  </select>'.PHP_EOL;
    if (isset($_POST['clone'])||isset($_POST['edit'])){ ?>
    	+<select name="<?php print $name.'[addtime]';?>">
    	<option>0</option>
    	<option value="<?php echo (7*86400); ?>">1</option>
    	<option value="<?php echo (14*86400); ?>">2</option>
    	<option value="<?php echo (21*86400); ?>">3</option>
    	<option value="<?php echo (28*86400); ?>">4</option>
    	<option value="<?php echo (35*86400); ?>">5</option>
    	</select>&nbsp;<?php echo loc('weeks');
    }
    print '</div>'.PHP_EOL;

  }

  function timepicker($name,$time=null){

    print '<div class="timepicker">'.PHP_EOL;

    if ($time==null){
      $selected_hour=0;
    } else {
      $selected_hour = ltrim(substr($time, 11,2),'0');
    }
    $hour=0;
    print '  <select name="'.$name.'[hour]" size="1">'.PHP_EOL;
    for ($i=0; $i<24; $i++){
      if ($hour==$selected_hour){
        print '    <option selected>'.$hour.'</option>'.PHP_EOL;
      } else {
        print '    <option>'.$hour.'</option>'.PHP_EOL;
      }
      $hour+=1;
    }
    print '  </select>'.PHP_EOL;

    if ($time==null){
      $selected_minute=0;
    } else {
      $selected_minute=ltrim(substr($time, 14,2),'0');
    }
    $minute=0;
    print '  <select name="'.$name.'[minute]" size="1">'.PHP_EOL;
    for ($i=0; $i<60; $i++){
      if ($minute==$selected_minute){
        print '    <option selected>'.$minute.'</option>'.PHP_EOL;
      } else {
        print '    <option>'.$minute.'</option>'.PHP_EOL;
      }
      $minute+=1;
    }
    print '  </select>'.PHP_EOL;

    print '</div>'.PHP_EOL;
  }

  if (!isset($appointment)){
  	$appointment=false;
  }
?>
