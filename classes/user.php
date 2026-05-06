<?php
class User {
    private $pdo;

    public function __construct($db) {
        $this->pdo = $db;
    }

    // Create a new user with account and profile info
    // BAGO: Idinagdag ang $currentUserId = null sa pinakadulo ng parameters
    public function createUser($username, $email, $password, $roleId, $firstName, $lastName, $middleName = null, $suffix = null, $contactNumber = null, $profilePicture = null, $currentUserId = null) {
        try {
            // Start transaction
            $this->pdo->beginTransaction();
            
            // Insert into user_accounts
            $passwordHash = password_hash($password, PASSWORD_DEFAULT);
            
            // BAGO: Isinama ang created_by at is_deleted sa INSERT query
            $sql1 = "INSERT INTO user_accounts (username, email, password_hash, user_role_id, account_status, created_by, is_deleted) 
                     VALUES (:username, :email, :password_hash, :role_id, 'Active', :created_by, 0)";
            $stmt1 = $this->pdo->prepare($sql1);
            $stmt1->bindParam(':username', $username);
            $stmt1->bindParam(':email', $email);
            $stmt1->bindParam(':password_hash', $passwordHash);
            $stmt1->bindParam(':role_id', $roleId, PDO::PARAM_INT);
            $stmt1->bindParam(':created_by', $currentUserId, PDO::PARAM_INT); // BAGO: I-bind ang user ID
            $stmt1->execute();
            
            $userAccountId = $this->pdo->lastInsertId();
            
            // Insert into user_info (Walang babaguhin dito)
            $sql2 = "INSERT INTO user_info (user_account_id, first_name, last_name, middle_name, suffix, contact_number, profile_picture) 
                     VALUES (:user_account_id, :first_name, :last_name, :middle_name, :suffix, :contact_number, :profile_picture)";
            $stmt2 = $this->pdo->prepare($sql2);
            $stmt2->bindParam(':user_account_id', $userAccountId, PDO::PARAM_INT);
            $stmt2->bindParam(':first_name', $firstName);
            $stmt2->bindParam(':last_name', $lastName);
            $stmt2->bindParam(':middle_name', $middleName);
            $stmt2->bindParam(':suffix', $suffix);
            $stmt2->bindParam(':contact_number', $contactNumber);
            $stmt2->bindParam(':profile_picture', $profilePicture);
            $stmt2->execute();
            
            // Commit transaction
            $this->pdo->commit();
            
            return ["status" => "success", "message" => "User created successfully!", "user_id" => $userAccountId];
            
        } catch (PDOException $e) {
            // Rollback transaction on error
            $this->pdo->rollBack();
            
            // Check for duplicate entry (Error code 23000)
            if ($e->getCode() == 23000) {
                if (strpos($e->getMessage(), 'username') !== false) {
                    return ["status" => "error", "message" => "Username already exists!"];
                } elseif (strpos($e->getMessage(), 'email') !== false) {
                    return ["status" => "error", "message" => "Email already exists!"];
                }
            }
            return ["status" => "error", "message" => $e->getMessage()];
        }
    }

    // Update user account status
    // BAGO: Idinagdag ang $currentUserId
    public function updateUserStatus($userId, $status, $currentUserId) {
        try {
            $validStatuses = ['Active', 'Deactivated', 'Blocked'];
            if (!in_array($status, $validStatuses)) {
                return ["status" => "error", "message" => "Invalid status. Must be Active, Deactivated, or Blocked."];
            }
            
            // BAGO: Isinama ang updated_by sa SET clause
            $sql = "UPDATE user_accounts 
                    SET account_status = :status, 
                        updated_by = :updated_by, 
                        updated_at = NOW() 
                    WHERE user_account_id = :id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':id', $userId, PDO::PARAM_INT);
            $stmt->bindParam(':status', $status);
            $stmt->bindParam(':updated_by', $currentUserId, PDO::PARAM_INT); // BAGO: I-bind ang user ID
            
            if ($stmt->execute()) {
                if ($stmt->rowCount() > 0) {
                    return ["status" => "success", "message" => "User status updated successfully!"];
                } else {
                    return ["status" => "error", "message" => "User not found."];
                }
            }
        } catch (PDOException $e) {
            return ["status" => "error", "message" => $e->getMessage()];
        }
    }

    // Update user role
    // BAGO: Idinagdag ang $currentUserId
    public function updateUserRole($userId, $roleId, $currentUserId) {
        try {
            // BAGO: Isinama ang updated_by sa SET clause
            $sql = "UPDATE user_accounts 
                    SET user_role_id = :role_id, 
                        updated_by = :updated_by, 
                        updated_at = NOW() 
                    WHERE user_account_id = :id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':id', $userId, PDO::PARAM_INT);
            $stmt->bindParam(':role_id', $roleId, PDO::PARAM_INT);
            $stmt->bindParam(':updated_by', $currentUserId, PDO::PARAM_INT); // BAGO: I-bind ang user ID
            
            if ($stmt->execute()) {
                if ($stmt->rowCount() > 0) {
                    return ["status" => "success", "message" => "User role updated successfully!"];
                } else {
                    return ["status" => "error", "message" => "User not found."];
                }
            }
        } catch (PDOException $e) {
            return ["status" => "error", "message" => $e->getMessage()];
        }
    }

    // Update user profile information
    // BAGO: Idinagdag ang $currentUserId sa dulo ng parameters
    public function updateUserProfile($userId, $firstName, $lastName, $middleName = null, $suffix = null, $contactNumber = null, $profilePicture = null, $currentUserId = null) {
        try {
            // BAGO: Isinama ang updated_by sa SET clause
            $sql = "UPDATE user_info 
                    SET first_name = :first_name, 
                        last_name = :last_name, 
                        middle_name = :middle_name, 
                        suffix = :suffix, 
                        contact_number = :contact_number,
                        profile_picture = :profile_picture,
                        updated_by = :updated_by, 
                        updated_at = NOW() 
                    WHERE user_account_id = :user_id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
            $stmt->bindParam(':first_name', $firstName);
            $stmt->bindParam(':last_name', $lastName);
            $stmt->bindParam(':middle_name', $middleName);
            $stmt->bindParam(':suffix', $suffix);
            $stmt->bindParam(':contact_number', $contactNumber);
            $stmt->bindParam(':profile_picture', $profilePicture);
            $stmt->bindParam(':updated_by', $currentUserId, PDO::PARAM_INT); // BAGO: I-bind ang user ID
            
            if ($stmt->execute()) {
                if ($stmt->rowCount() > 0) {
                    return ["status" => "success", "message" => "User profile updated successfully!"];
                } else {
                    return ["status" => "error", "message" => "No changes were made or user not found."];
                }
            }
        } catch (PDOException $e) {
            return ["status" => "error", "message" => $e->getMessage()];
        }
    }

    // Update user login timestamp (WALANG BINAGO DITO)
    public function updateLastLogin($userId) {
        try {
            $sql = "UPDATE user_accounts SET last_login = NOW() WHERE user_account_id = :id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':id', $userId, PDO::PARAM_INT);
            $stmt->execute();
        } catch (PDOException $e) {
            // Silent fail for login update
        }
    }

    // Get user by ID with all details
    public function getUserById($userId) {
        try {
            // BAGO: Idinagdag ang audit columns sa SELECT
            $sql = "SELECT ua.user_account_id, ua.username, ua.email, ua.user_role_id, ua.account_status, 
                           ua.last_login, ua.created_at as account_created_at,
                           ua.created_by, ua.updated_by as account_updated_by, ua.is_deleted,
                           ui.first_name, ui.last_name, ui.middle_name, ui.suffix, 
                           ui.contact_number, ui.profile_picture,
                           ui.updated_by as profile_updated_by,
                           ur.user_role_name
                    FROM user_accounts ua
                    LEFT JOIN user_info ui ON ua.user_account_id = ui.user_account_id
                    LEFT JOIN user_roles ur ON ua.user_role_id = ur.user_role_id
                    WHERE ua.user_account_id = :id AND ua.is_deleted = 0"; // BAGO: is_deleted filter
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':id', $userId, PDO::PARAM_INT);
            $stmt->execute();
            
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($user) {
                return ["status" => "success", "data" => $user];
            } else {
                return ["status" => "error", "message" => "User not found."];
            }
        } catch (PDOException $e) {
            return ["status" => "error", "message" => $e->getMessage()];
        }
    }

    // Get user by username or email (for login)
    public function getUserByUsernameOrEmail($usernameOrEmail) {
        try {
            // BAGO: Idinagdag ang audit columns sa SELECT
            $sql = "SELECT ua.user_account_id, ua.username, ua.email, ua.password_hash, 
                           ua.user_role_id, ua.account_status, ua.last_login,
                           ua.created_by, ua.updated_by as account_updated_by, ua.is_deleted,
                           ui.first_name, ui.last_name, ui.middle_name, ui.suffix, 
                           ui.contact_number, ui.profile_picture,
                           ui.updated_by as profile_updated_by,
                           ur.user_role_name
                    FROM user_accounts ua
                    LEFT JOIN user_info ui ON ua.user_account_id = ui.user_account_id
                    LEFT JOIN user_roles ur ON ua.user_role_id = ur.user_role_id
                    WHERE (ua.username = :credential OR ua.email = :credential) AND ua.is_deleted = 0"; 
                    // BAGO: Nilagyan ng parenthesis ( ) ang OR bago lagyan ng AND is_deleted = 0. Sobrang importane nito para hindi ma-bypass ang soft delete sa login!
                    
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':credential', $usernameOrEmail);
            $stmt->execute();
            
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return null;
        }
    }

    // Get all users with optional filters
    public function getAllUsers($status = null, $roleId = null) {
        try {
            // BAGO: Idinagdag ang audit columns sa SELECT
            $sql = "SELECT ua.user_account_id, ua.username, ua.email, ua.user_role_id, ua.account_status, 
                           ua.last_login, ua.created_at as account_created_at,
                           ua.created_by, ua.updated_by as account_updated_by, ua.is_deleted,
                           ui.first_name, ui.last_name, ui.middle_name, ui.suffix, 
                           ui.contact_number, ui.profile_picture,
                           ui.updated_by as profile_updated_by,
                           ur.user_role_name
                    FROM user_accounts ua
                    LEFT JOIN user_info ui ON ua.user_account_id = ui.user_account_id
                    LEFT JOIN user_roles ur ON ua.user_role_id = ur.user_role_id
                    WHERE ua.is_deleted = 0"; // BAGO: Pinalitan ang 'WHERE 1=1' ng 'WHERE ua.is_deleted = 0'
            
            $params = [];
            
            if ($status !== null) {
                $sql .= " AND ua.account_status = :status";
                $params[':status'] = $status;
            }
            
            if ($roleId !== null) {
                $sql .= " AND ua.user_role_id = :role_id";
                $params[':role_id'] = $roleId;
            }
            
            $sql .= " ORDER BY ua.user_account_id DESC";
            
            $stmt = $this->pdo->prepare($sql);
            
            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value);
            }
            
            $stmt->execute();
            $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            return ["status" => "success", "data" => $users];
        } catch (PDOException $e) {
            return ["status" => "error", "message" => $e->getMessage()];
        }
    }

    // Delete user (hard delete with cascade)
    // Delete user (Soft Delete)
    // BAGO: Dinagdag ang $currentUserId bilang pangalawang parameter
    public function deleteUser($userId, $currentUserId) {
        try {
            // BAGO: Pinalitan ang DELETE ng UPDATE para sa Soft Delete
            $sql = "UPDATE user_accounts 
                    SET is_deleted = 1, 
                        updated_by = :current_user,
                        updated_at = NOW() 
                    WHERE user_account_id = :id AND is_deleted = 0";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':id', $userId, PDO::PARAM_INT);
            $stmt->bindParam(':current_user', $currentUserId, PDO::PARAM_INT); // BAGO: I-bind ang user ID
            
            if ($stmt->execute()) {
                if ($stmt->rowCount() > 0) {
                    return ["status" => "success", "message" => "User deleted successfully!"];
                } else {
                    return ["status" => "error", "message" => "User not found or already deleted."];
                }
            }
        } catch (PDOException $e) {
            return ["status" => "error", "message" => $e->getMessage()];
        }
    }

    // Change password
    // BAGO: Dinagdag ang $currentUserId bilang pang-apat na parameter
    public function changePassword($userId, $currentPassword, $newPassword, $currentUserId = null) {
        try {
            // Get current password hash
            // BAGO: Idinagdag ang is_deleted = 0 para di makapag-change pass ang deleted na
            $sql = "SELECT password_hash FROM user_accounts WHERE user_account_id = :id AND is_deleted = 0";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':id', $userId, PDO::PARAM_INT);
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$user) {
                return ["status" => "error", "message" => "User not found or account is deactivated/deleted."];
            }
            
            // Verify current password
            if (!password_verify($currentPassword, $user['password_hash'])) {
                return ["status" => "error", "message" => "Current password is incorrect."];
            }
            
            // Update to new password
            $newPasswordHash = password_hash($newPassword, PASSWORD_DEFAULT);
            
            // BAGO: Isinama ang updated_by sa UPDATE query
            $updateSql = "UPDATE user_accounts 
                          SET password_hash = :password_hash, 
                              updated_by = :updated_by,
                              updated_at = NOW() 
                          WHERE user_account_id = :id";
            $updateStmt = $this->pdo->prepare($updateSql);
            $updateStmt->bindParam(':id', $userId, PDO::PARAM_INT);
            $updateStmt->bindParam(':password_hash', $newPasswordHash);
            $updateStmt->bindParam(':updated_by', $currentUserId, PDO::PARAM_INT); // BAGO: I-bind ang user ID
            $updateStmt->execute();
            
            return ["status" => "success", "message" => "Password changed successfully!"];
            
        } catch (PDOException $e) {
            return ["status" => "error", "message" => $e->getMessage()];
        }
    }
}
?>