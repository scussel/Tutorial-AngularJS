<?php

class Conexao
{
	private static $factory;
	private $database;

	public static function getFactory()
	{
		if (!self::$factory) {
			self::$factory = new Conexao();
		}
		return self::$factory;
	}

	public function getConnection()
	{
		if (!$this->database) {
			$options = array(PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ, PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING, PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8");
			$this->database = new PDO('mysql:host=localhost;dbname=angular_tutorial;port=3306','root', '12345678', $options);
		}
		return $this->database;
	}
}

$postdata = file_get_contents("php://input");
$request = json_decode($postdata);

$function = trim($request->function);
$id       = trim($request->id);
$text     = trim($request->text);
$status   = trim($request->status);
$archived = trim($request->archived);

if($function == 'addTodo'){
	$param = array('text' => $text);
}elseif($function == 'getAllTodos'){
	$param = array(
		'status' => $status,
		'archived' => $archived
	);
}elseif($function == 'archiveTodos'){
	$param = array('id' => $id);
}

$return = call_user_func_array($function, $param);

echo $return;

function addTodo($text){
	$database = Conexao::getFactory()->getConnection();

	$sql = " INSERT INTO todolist (description, date_time, completed, archived) VALUES (:description, :date_time, '0', '0') ";
	$query = $database->prepare($sql);
	$query->execute(array(':description'     => $text,
						  ':date_time'       => date('Y-m-d H:i:s')));
	$count =  $query->rowCount();

	if ($count == 1) {
		$sql = "SELECT MAX(id) AS Id FROM todolist ";
		$query = $database->prepare($sql);
		$query->execute();
		$rowMax = $query->fetch();

		$id = $rowMax->Id+1;

		$arr = array(
			'id'        => $id,
			'text'      => $text,
			'date_time' => date('d/m/Y H:i:s')
		);
	}else{
		$arr = array('error' => 'Sorry, an ocurred error to insert your todo!!');
	}

	return json_encode($arr);
}

function getAllTodos($completed, $archived){
	$database = Conexao::getFactory()->getConnection();

	$sql = " SELECT id, description, date_time, completed FROM todolist WHERE completed = :completed AND archived = :archived ORDER BY id ASC ";
	$query = $database->prepare($sql);
	$query->execute(array(':completed' => $completed, ':archived' => $archived));

	$output = '';
	foreach($query->fetchAll() as $todo){
		if ($output != ''){ $output .= ","; }

		if($todo->completed=='0'){ $done = false; }
		else{ $done = true; }

		$output .= '{"id":"'. $todo->id .'",';
		$output .= '"text":"'. $todo->description .'",';
		$output .= '"datetime":"'. $todo->date_time .'",'; 
		$output .= '"done":"'. $done .'"}'; 
	}

	$output ='{"records":['.$output.']}';

	return $output;
}

function archiveTodos($id){
	$database = Conexao::getFactory()->getConnection();

	$sql = " UPDATE todolist SET completed = '1', archived = '1' WHERE id = :id ";
	$query = $database->prepare($sql);
	$query->execute(array(':id' => $id));
	$count =  $query->rowCount();

	if($count == 1){
		return true;
	}

	return false;
}
