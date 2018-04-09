<?php
class Users
{   

	private $conn;
	public $error;
	
	public function __construct()
	{
		$database = new Database();
		$db = $database->dbConnection();
		$this->conn = $db;
    }

	//------------------------------------------
	var $name;
	public function set_name($new_name) {
		$this->name = $new_name;
	}
	public function get_name() {
		return $this->name;
	}	
	//------------------------------------------
	
	public function runQuery($sql)
	{
		$stmt = $this->conn->prepare($sql);
		return $stmt;
	}
	
	public function register($uname,$umail,$upass,$uid,$table)
	{
		try
		{
			$new_password = password_hash($upass, PASSWORD_DEFAULT);
			
			$stmt = $this->conn->prepare("INSERT INTO $table(iduser,user_name,user_email,user_pass) VALUES(:uid, :uname, :umail, :upass)");
												  
			$stmt->bindparam(":uid", $uid);
			$stmt->bindparam(":uname", $uname);
			$stmt->bindparam(":umail", $umail);
			$stmt->bindparam(":upass", $new_password);									  
				
			$stmt->execute();	
			
			return $stmt;	
		}
		catch(PDOException $e)
		{
			echo $e->getMessage();
		}				
	}
	public function registerOption($uname,$umail,$upass,$uid,$table)
	{
					try
					{
						$stmt = $this->runQuery("SELECT user_name, user_email FROM $table WHERE user_email=:umail");
						$stmt->execute(array(':umail'=>$umail));
						$row=$stmt->fetch(PDO::FETCH_ASSOC);
							
						if($row['user_email']==$umail) {
							//check if email exist before insertion
						}
						else
						{
							$this->register($uname,$umail,$upass,$uid,$table);
						}
					}
					catch(PDOException $e)
					{
						echo $e->getMessage();
					}
	}	
	public function doLogin($uname,$umail,$upass,$num)
	{
		try
		{
			$stmt = $this->conn->prepare("SELECT user_id, user_name, user_email, user_pass FROM users WHERE user_name=:uname OR user_email=:umail AND active=:num");
			$stmt->execute(array(':uname'=>$uname, ':umail'=>$umail, ':num'=>$num));
			$userRow=$stmt->fetch(PDO::FETCH_ASSOC);
			if($stmt->rowCount() == 1)
			{
				if(password_verify($upass, $userRow['user_pass']))
				{
					$_SESSION['user_session'] = $userRow['user_id'];
					$this->redirect('panel');
					return true;
				}
				else
				{
					$this->error = 'Wrong Details !';
					return false;
				}
			}
			else
			{
					$this->error = 'Wrong Details !';
					return false;
			}
		}
		catch(PDOException $e)
		{
			echo $e->getMessage();
		}
	}

		public function execute($query) 
	{
		$result = mysqli_query($this->conn, $query);
		$this->result = $result;
		if ($result == false) {
			echo 'Error: cannot execute the command';
			return false;
		} else {
			$this->result = $result;
			return true;
		}		
	}

		public function escape_string($value) {
		$value = trim($value);
		$value = stripslashes($value);
		$value = htmlspecialchars($value);
		return $this->conn->real_escape_string($value);
	}
	public function proceedTopage($uname,$umail,$upass)
	{
		if($uname=="")	{
			$this->error = "provide username !";	
		}
		else if($umail=="")	{
			$this->error = "provide email id !";	
		}
		else if(!filter_var($umail, FILTER_VALIDATE_EMAIL))	{
			$this->error = 'Please enter a valid email address !';
		}
		else if($upass=="")	{
			$this->error = "provide password !";
		}
		else if(strlen($upass) < 6){
			$this->error = "Password must be atleast 6 characters";	
		}
		else
		{		
				try
				{
					$stmt = $this->runQuery("SELECT user_name, user_email FROM users WHERE user_name=:uname OR user_email=:umail");
					$stmt->execute(array(':uname'=>$uname, ':umail'=>$umail));
					$row=$stmt->fetch(PDO::FETCH_ASSOC);
						
					if($row['user_name']==$uname) {
						$this->error = "sorry username already taken !";
					}
					else if($row['user_email']==$umail) {
						$this->error = "sorry email id already taken !";
					}
					else
					{	
						$this->redirect('start');
					}
				}
				catch(PDOException $e)
				{
					echo $e->getMessage();
				}
		}
	}
	public function CodeVerify($uname,$umail,$upass,$code,$verfied_code,$stand)
	{
		if($code != $verfied_code)	{
			$this->error = "Verification code is incorrect.";	
		}
		else
		{	
			if($stand == "work")	{
				$this->redirect('personal/');
			}else{
				$num = "1";
				$stmt = $this->runQuery("UPDATE users SET active=:num WHERE user_email=:umail");
				$stmt->execute(array(':umail'=>$umail, ':num'=>$num));				
				$this->doLogin($uname,$umail,$upass,$num);
				return true;
			}
		}
	}
	 public function getByEmail($umail,$table){
		$stmt = $this->runQuery("SELECT * FROM $table WHERE user_email = :umail");
		$stmt->execute(array(':umail'=>$umail));	
		$data = $stmt->fetch(PDO::FETCH_ASSOC);		 
		return $data; 
	 }	
	/*
	public function getUserId($umail,$uname,$table)
	{
		 $sql="SELECT * FROM $table WHERE user_email = :umail AND user_name = :uname ";
		 $q = $this->conn->prepare($sql);
		 $q->execute(array(':uname'=>$uname, ':umail'=>$umail));
		 $data = $q->fetch(PDO::FETCH_ASSOC);
		 return $data;
		 return $data['user_id'];
	}	
	*/	
	public function is_loggedin()
	{
		if(isset($_SESSION['user_session']))
		{
			return true;
		}
	}
	
	public function redirect($url)
	{
		header("Location: $url");
	}

	public function done($uname, $email, $password, $sd, $code, $uid, $fname, $msg)
	{
		try
				{
					$this->execute("INSERT INTO profile SET profile_id = $email, profile_fname = $fname, profile_desc = $msg");

					$this->execute("DELETE FROM users_temp WHERE user_email = '$email");
							
						
				}
				catch(PDOException $e)
				{
					echo $e->getMessage();
				}
	}
	
	public function doLogout()
	{
		session_destroy();
		unset($_SESSION['user_session']);
		return true;
	}	
	
}