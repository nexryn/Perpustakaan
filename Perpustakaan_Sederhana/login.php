<?php
session_start();
include 'koneksi.php';

$error = '';

if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    // Prepared statement untuk keamanan
    $stmt = $conn->prepare("SELECT * FROM user WHERE Username = ? AND Password = ?");
    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $_SESSION['user_id'] = $row['UserID'];
        $_SESSION['username'] = $row['Username'];
        $_SESSION['nama_lengkap'] = $row['NamaLengkap'];
        header("Location: dashboard.php");
        exit;
    } else {
        $error = "Username atau password salah!";
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - School Library</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            position: relative;
            overflow: hidden;
        }
        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><circle cx="50" cy="50" r="40" fill="none" stroke="rgba(255,255,255,0.05)" stroke-width="0.5"/></svg>');
            background-size: 50px 50px;
            animation: float 20s linear infinite;
        }
        @keyframes float {
            0% { transform: translateY(0) rotate(0deg); }
            100% { transform: translateY(-50px) rotate(360deg); }
        }
        .login-container {
            background: rgba(255, 255, 255, 0.95);
            padding: 50px;
            border-radius: 20px;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.3), 
                        0 0 100px rgba(102, 126, 234, 0.2);
            width: 100%;
            max-width: 420px;
            position: relative;
            backdrop-filter: blur(10px);
        }
        .login-container::before {
            content: '';
            position: absolute;
            top: -2px;
            left: -2px;
            right: -2px;
            bottom: -2px;
            background: linear-gradient(135deg, #667eea, #764ba2, #f093fb, #667eea);
            border-radius: 22px;
            z-index: -1;
            background-size: 400% 400%;
            animation: gradient 3s ease infinite;
        }
        @keyframes gradient {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }
        .logo {
            text-align: center;
            margin-bottom: 30px;
        }
        .logo-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 40px;
            color: white;
            box-shadow: 0 10px 30px rgba(102, 126, 234, 0.4);
            margin-bottom: 15px;
        }
        h2 {
            text-align: center;
            color: #1a1a2e;
            margin-bottom: 10px;
            font-size: 28px;
            font-weight: 700;
        }
        .subtitle {
            text-align: center;
            color: #666;
            margin-bottom: 35px;
            font-size: 14px;
        }
        .form-group {
            margin-bottom: 25px;
            position: relative;
        }
        label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: 600;
            font-size: 14px;
        }
        input {
            width: 100%;
            padding: 15px 20px;
            border: 2px solid #e0e0e0;
            border-radius: 12px;
            font-size: 15px;
            transition: all 0.3s ease;
            background: #f8f9fa;
        }
        input:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
            background: white;
        }
        .btn-login {
            width: 100%;
            padding: 16px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 16px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(102, 126, 234, 0.4);
        }
        .btn-login:active {
            transform: translateY(0);
        }
        .error {
            background: linear-gradient(135deg, #ff6b6b 0%, #ee5a5a 100%);
            color: white;
            padding: 15px;
            border-radius: 12px;
            margin-bottom: 20px;
            text-align: center;
            font-weight: 500;
            animation: shake 0.5s ease;
        }
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-5px); }
            75% { transform: translateX(5px); }
        }
        .footer {
            text-align: center;
            margin-top: 25px;
            color: #999;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="logo">
            <div class="logo-icon">📚</div>
        </div>
        <h2>School Library</h2>
        <p class="subtitle">Silakan login untuk mengakses sistem</p>
        <?php if ($error): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>
        <form method="POST" action="">
            <div class="form-group">
                <label>Username</label>
                <input type="text" name="username" required placeholder="Masukkan username">
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" required placeholder="Masukkan password">
            </div>
            <button type="submit" name="login" class="btn-login">Login</button>
        </form>
        <div class="footer">
            &copy; 2024 School Library. All rights reserved.
        </div>
    </div>
</body>
</html>
