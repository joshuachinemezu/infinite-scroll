<?php
require_once('includes/functions.php');

$where = '';
	if(isset($_REQUEST['where'])){
		$search = $_REQUEST['where'];
		$where .= "WHERE category LIKE '%$search%'";
		// $where .= "WHERE category = '$search'";
}

$limit = (intval($_GET['limit']) != 0 ) ? $_GET['limit'] : 5;
$offset = (intval($_GET['offset']) != 0 ) ? $_GET['offset'] : 0;

$sql = "SELECT * FROM users_des {$where} ORDER BY id ASC LIMIT $limit OFFSET $offset";
try {
  $stmt = $user->runQuery($sql);
  $stmt->execute();
  $results = $stmt->fetchAll();
} catch (Exception $ex) {
  echo $ex->getMessage();
}
if (count($results) > 0) {
  foreach ($results as $res) {
?>

			<div class="col-sm-4 my-4 text-center">
				<div class="card" style="width:500px; padding:40px;">
					<h3><?php echo $res['id']; ?></h3>
					<?php echo $res['name']; ?>
					<small><?php echo $res['iduser']; ?></small>
				</div>
			</div>
<?php	
  }
}
?>