<?php
function is_empty_val($str){
	if (!empty($_POST[$str])){return $_POST[$str];}
	return null;
}
$user="root";
$pass="";
$filter_on=false;
	if (!(empty($_POST['insert']))){
	$pdo = new PDO('mysql:host=localhost;dbname=solopov;charset=utf8', $user, $pass);
	$prep_q = $pdo->prepare('INSERT INTO tasks(description, is_done, date_added) VALUES (?,?,?)');	
	$newtask=is_empty_val('newtask');	
	$prep_q->execute(array($newtask,1,date('Y-m-d H:i:s')));
}	
if (!(empty($_POST['delete']))){
	$pdo = new PDO('mysql:host=localhost;dbname=solopov;charset=utf8', $user, $pass);
	$prep_q = $pdo->prepare('DELETE FROM tasks WHERE id=?');
	$numtask=is_empty_val('delete');
	$prep_q->execute(array($numtask));
	}	

if (!(empty($_POST['onchange']))){
	$change_on=true;
	$tmp=$_POST['onchange'];
}	

if (!(empty($_POST['ready']))){
	$pdo = new PDO('mysql:host=localhost;dbname=solopov;charset=utf8', $user, $pass);
	$prep_q = $pdo->prepare('UPDATE tasks SET description=? WHERE id=?');
	$changedtask=is_empty_val('changetask');
	if (!(empty($changedtask))){
		$id=$_POST['ready'];
		$prep_q->execute(array($changedtask,$id));
	}
}
if (!(empty($_POST['execute']))){
	$pdo = new PDO('mysql:host=localhost;dbname=solopov;charset=utf8', $user, $pass);
	$prep_q = $pdo->prepare('UPDATE tasks SET is_done=0 WHERE id=?');
	$id=$_POST['execute'];
	$prep_q->execute(array($id));
}

try {
	$pdo = new PDO('mysql:host=localhost;dbname=solopov;charset=utf8', $user, $pass);	
	$col_name = $pdo->query("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name = 'tasks' AND table_schema = 'solopov'");
	if (!(empty($_POST['sortbydate']))){
		$data =	$pdo->query("SELECT * FROM tasks ORDER BY date_added");
	}
	elseif (!(empty($_POST['sortbydescription']))){
		$data =	$pdo->query("SELECT * FROM tasks ORDER BY description");
	}
	elseif(!(empty($_POST['sortbystatus']))){
		$data =	$pdo->query("SELECT * FROM tasks ORDER BY is_done");
	}
	else{	
		$data =	$pdo->query("SELECT * FROM tasks");		
	}
	}catch (PDOException $e) {
		print "Error!: " . $e->getMessage() . "<br/>";
		die();
	}
header("Refresh");	
//echo date('Y-m-d H:i:s');
?>



<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width">
  <style>
	table {
    border-collapse: collapse;
	
	}
	th{
	background: gray;
	}
	td, th{
    border: 1px solid black;
	}
</style>  
</head>
<body>
<form action="tasks.php" method="post">

	<input name="newtask" type="text" placeholder="Новая задача">
	
	<button type="submit" name="insert" value="insert">Добавить</button>
	Сортировать по:
	<button type="submit" name="sortbydate" value="sortbydate">дате</button>
	<button type="submit" name="sortbydescription" value="sortbydescription">описанию</button>
	<button type="submit" name="sortbystatus" value="sortbystatus">статусу</button>
	<table>	
		<tr>
		<?php
			while ($row=$col_name->fetch(PDO::FETCH_ASSOC) ){
				foreach($row as $key=>$value){ ?>
				<th> <?php echo $value; ?></th>
				<?php } ?>	
			<?php } ?>		
		</tr>
		<?php
			while ($row=$data->fetch(PDO::FETCH_ASSOC) ){ 
		?>
				<tr>
				<?php 
					foreach($row as $key=>$value){ 
					if (($key=='is_done')){
						if (!(empty($row[$key]))){ ?>
							<td> В процессе </td>
							<?php continue; } else { ?>
							<td style="background-color: LightGreen  "> Завершено </td>
							<?php continue; } ?>
					<?php }; ?>
					<td> <?php echo strip_tags($value) ."</br>"; ?></td>							
					<?php } ?>	
				<td style="border-style: none">
					<button type="submit" name="onchange" value="<?php echo $row['id']?>">Изменить</button>
					<button type="submit" name="execute" value="<?php echo $row['id']?>">Выполнить</button>
					<button type="submit" name="delete" value="<?php echo $row['id']?>">Удалить</button>
					<?php
						if ( $change_on & $tmp==$row['id']) {?>
							<input name="changetask" type="text" placeholder="Изменить задачу">
							<button type="submit" name="ready" value="<?php echo $row['id'] ?>">Готово</button>
					<?php } ?>

				</td>
				
				</tr>
				
		<?php }; ?>			
	</table>
  </form>
</body>
</html>
