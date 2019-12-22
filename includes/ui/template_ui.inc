<?php

function btn_submit_search($type='submit',$name='',$id='',$value='',$title='Search')
{
	$path_to_root  = '../../..';
	echo '<center><button  type="'.$type.'"  name="'.$name.'" id="'.$id.'" value="'.$value.'" title="Search items"><img src="'.$path_to_root.'/themes/canvas/images/view.gif" alt="" height="12"><span>'.$title.'</span></button></center>';
}

function btn_submit_select($type='submit',$name='',$id='',$value='',$title='Select')
{
	$path_to_root  = '../../..';
	echo "<td>";
	echo '<center><button  type="'.$type.'"  name="'.$name.'" id="'.$id.'" value="'.$value.'" title="Search items"><img src="'.$path_to_root.'/themes/canvas/images/ok.gif" height="12" alt=""><span>'.$title.'</span></button></center>';
	echo "</td>";
}

function btn_submit_Add($type='submit',$name='',$id='',$value='',$title='Add')
{
	$path_to_root  = '../../..';
	echo '<center><button  type="'.$type.'"  name="'.$name.'" id="'.$id.'" value="'.$value.'" title="Search items"><img src="'.$path_to_root.'/themes/canvas/images/add.png" height="12" alt=""><span>'.$title.'</span></button></center>';
}
function btn_submit_delete($type='submit',$name='',$id='',$value='',$title='Delete',$confirm = false)
{
	$confirm = $confirm === true ? 'onclick="alertConfirm(this)"' : '';
	$path_to_root  = '../../..';
	echo '<center><button  type="'.$type.'" '.$confirm.' name="'.$name.'" id="'.$id.'" value="'.$value.'" title="Search items"><img src="'.$path_to_root.'/themes/canvas/images/delete.gif" height="12" alt=""><span>'.$title.'</span></button></center>';
}
?>
<script>
function alertConfirm(form) {
  if (confirm("All data of the date will be deleted ")) {
   	form.submit();
  }
}
</script>
