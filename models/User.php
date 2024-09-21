<?php
class User
{
    private $db;

    public function __construct($database)
    {
        $this->db = $database->getConnection();
    }

    public function createUser($data)
    {
        // Hash the password using bcrypt
        $hashedPassword = password_hash($data['password'], PASSWORD_BCRYPT);

        // Prepare the SQL query to insert the new user
        $stmt = $this->db->prepare("INSERT INTO users (username, password, email) VALUES (:username, :password, :email)");
        $stmt->bindValue(':username', $data['username'], SQLITE3_TEXT);
        $stmt->bindValue(':password', $hashedPassword, SQLITE3_TEXT); // Use hashed password
        $stmt->bindValue(':email', $data['email'], SQLITE3_TEXT);

        // Execute the statement and return success or error response
        if ($stmt->execute()) {
            return [
                'status' => 'success',
                'message' => 'User created successfully'
            ];
        } else {
            return [
                'status' => 'error',
                'message' => 'Failed to create user'
            ];
        }
    }

    public function updatePassword($id, $data)
    {
        // Fetch the user's current password from the database
        $stmt = $this->db->prepare("SELECT password FROM users WHERE id = :id LIMIT 1");
        $stmt->bindValue(':id', $id, SQLITE3_INTEGER);
        $result = $stmt->execute();
        $user = $result->fetchArray(SQLITE3_ASSOC);

        // Verify the old password
        if (!$user || !password_verify($data['oldPassword'], $user['password'])) {
            return [
                'status' => 'error',
                'message' => 'Incorrect old password'
            ];
        }

        // Check if new password and confirm password match
        if ($data['newPassword'] !== $data['confirmPassword']) {
            return [
                'status' => 'error',
                'message' => 'New password and confirmation password do not match'
            ];
        }

        // Hash the new password using bcrypt
        $hashedPassword = password_hash($data['newPassword'], PASSWORD_BCRYPT);

        // Prepare the SQL query to update the password
        $stmtUpdate = $this->db->prepare("UPDATE users SET password = :password WHERE id = :id");
        $stmtUpdate->bindValue(':password', $hashedPassword, SQLITE3_TEXT); // Use hashed password
        $stmtUpdate->bindValue(':id', $id, SQLITE3_INTEGER);

        // Execute the update query
        if ($stmtUpdate->execute()) {
            return [
                'status' => 'success',
                'message' => 'Password updated successfully'
            ];
        } else {
            return [
                'status' => 'error',
                'message' => 'Failed to update password'
            ];
        }
    }

    public function updateEmail($id, $data)
    {
        $stmt = $this->db->prepare("UPDATE users SET email = :email WHERE id = :id");
        $stmt->bindValue(':email', $data['email'], SQLITE3_TEXT);
        $stmt->bindValue(':id', $id, SQLITE3_INTEGER);
        return $stmt->execute();
    }

    public function updateUsername($id, $data)
    {
        $stmt = $this->db->prepare("UPDATE users SET username = :username WHERE id = :id");
        $stmt->bindValue(':username', $data['username'], SQLITE3_TEXT);
        $stmt->bindValue(':id', $id, SQLITE3_INTEGER);
        return $stmt->execute();
    }

    public function login($data)
    {
        $username = $data['username'];
        $password = $data['password'];

        // Prepare the SQL query to find the user by username
        $stmt = $this->db->prepare("SELECT * FROM users WHERE username = :username LIMIT 1");
        $stmt->bindValue(':username', $username, SQLITE3_TEXT);

        // Execute the query
        $result = $stmt->execute();

        // Fetch the user from the database
        $user = $result->fetchArray(SQLITE3_ASSOC);

        // Check if user exists and verify the hashed password
        if ($user && password_verify($password, $user['password'])) {
            // Password matches, set session or return success
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];

            // Update the last login timestamp
            $stmtUpdate = $this->db->prepare("UPDATE users SET last_login = :last_login WHERE id = :id");
            $stmtUpdate->bindValue(':last_login', date('Y-m-d H:i:s'), SQLITE3_TEXT);  // Current timestamp
            $stmtUpdate->bindValue(':id', $user['id'], SQLITE3_INTEGER);
            $stmtUpdate->execute();

            return [
                'status' => 'success',
                'message' => 'Login successful',
                'user' => $user
            ];
        } else {
            // Invalid login attempt
            return [
                'status' => 'error',
                'message' => 'Invalid username or password'
            ];
        }
    }
}
