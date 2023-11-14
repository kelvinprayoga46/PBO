<?php
class User
{
    private $twig, $pdo;
    
    public function __construct($twig, $db)
    {
        $this->twig = $twig;
        $this->pdo = $db->getConnection();
    }

    public function list($search = null)
    {
        $sql = "SELECT * FROM users";

        if ($search) {
            $sql .= " WHERE name LIKE :search OR email LIKE :search";
        }

        try {
            $stmt = $this->pdo->prepare($sql);

            if ($search) {
                $stmt->bindValue(':search', "%$search%");
            }
            $stmt->execute();
            $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return $this->twig->render('users/list.html', ['users' => $users]);
        } catch (PDOException $e) {
            return false;
        }
    }

    public function show($id)
    {
        $sql = "SELECT * FROM users WHERE id = ?";

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$id]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user) {
                return $this->twig->render('users/show.html', ['user' => $user]);
            } else {
                return 'User tidak ditemukan.';
            }
        } catch (PDOException $e) {
            return 'Database error.';
        }
    }

    public function create()
    {
        return $this->twig->render('users/create.html');
    }
    
    public function insert($requests)
    {
        $name = $requests['name'];
        $email = $requests['email'];
        $password = $requests['password'];
        $role = $requests['role'];

        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $sql = "INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)";

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$name, $email, $hashedPassword, $role]);
            if ($stmt->rowCount() > 0) {
                return true;
            } else {
                return false;
            }
        } catch (PDOException $e) {
            return false;
        }
    }

    public function edit($id)
    {
        $sql = "SELECT * FROM users WHERE id = ?";

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$id]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user) {
                return $this->twig->render('users/edit.html', ['user' => $user]);
            } else {
                return 'User tidak ditemukan.';
            }
        } catch (PDOException $e) {
            return 'Database error.';
        }
    }

    public function update($requests)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $requests['id'];
            $name = $requests['name'];
            $email = $requests['email'];
            $role = $requests['role'];

            $sql = "UPDATE users SET name = ?, email = ?, role = ? WHERE id = ?";

            try {
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute([$name, $email, $role, $id]);
                if ($stmt->rowCount() > 0) {
                    return true;
                } else {
                    return false;
                }
            } catch (PDOException $e) {
                return false;
            }
        }
        return false;
    }

    public function delete($id)
    {
        $sql = "DELETE FROM users WHERE id = ?";

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$id]);

            if ($stmt->rowCount() > 0) {
                return true; 
            } else {
                return false; 
            }
        } catch (PDOException $e) {
            echo "Database error";
            return false;
        }
    }
}
?>
