<?php 

	class userAccount {
		public $id;
		public $firmid;
		public $firmName;
		public $username;
		public $firstName;
		public $lastName;
		public $accessLevel;
		public $role;
		public $email;
		public $profileImage;
		
		public function __construct($userId, $firmId) {
			$mysqli = $GLOBALS['mysqli'];
			
			$user = $mysqli->prepare("SELECT `grid_users`.*, `grid_level`.role, `grid_firm`.name AS firm_name FROM `grid_users` LEFT OUTER JOIN `grid_level` ON `grid_level`.access_level = `grid_users`.access_level LEFT OUTER JOIN `grid_firm` ON `grid_firm`.id = `grid_users`.firm_id WHERE `grid_users`.id = ? AND `grid_users`.firm_id = ? LIMIT 1");
			$user->bind_param('ii', $userId, $firmId);
			$ex = $user->execute();
			
			if($ex === false) {
				echo 'Error: Failed to access user data';
				exit();
			}
			
			$result = $user->get_result();
			
			if($result->num_rows <= 0) {
				echo 'Error: User does not appear to exist';
				exit();
			}
			
			$user = $result->fetch_assoc();
			
			$this->id = $user['id'];
			$this->firmId = $user['firm_id'];
			$this->firmName = $user['firm_name'];
			$this->username = $user['username'];
			$this->firstName = $user['first_name'];
			$this->lastName = $user['last_name'];
			$this->accessLevel = $user['access_level'];
			$this->role = $user['role'];
			$this->email = $user['email'];
			$this->profileImage = $user['image'];
		}
	}

?>