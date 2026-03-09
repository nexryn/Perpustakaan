<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Get statistics
$anggota = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM anggota"));
$buku = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM buku"));
$kategori = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM kategoribuku"));
$peminjaman = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM peminjaman"));
$peminjaman_aktif = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM peminjaman WHERE StatusPeminjaman = 'Dipinjam'"));
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - School Library</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
        }
        .header {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            color: white;
            padding: 20px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        .header h1 {
            font-size: 24px;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .header h1 i {
            background: linear-gradient(135deg, #667eea, #764ba2);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            font-size: 28px;
        }
        .user-info {
            display: flex;
            align-items: center;
            gap: 20px;
        }
        .user-info span {
            font-size: 14px;
            opacity: 0.9;
        }
        .user-info strong {
            background: linear-gradient(135deg, #667eea, #764ba2);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            font-weight: 600;
        }
        .logout-btn {
            background: linear-gradient(135deg, #ff6b6b, #ee5a5a);
            color: white;
            padding: 10px 25px;
            text-decoration: none;
            border-radius: 25px;
            font-weight: 500;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .logout-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(255, 107, 107, 0.4);
        }
        .container {
            padding: 40px;
            max-width: 1400px;
            margin: 0 auto;
        }
        .welcome-section {
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.2), rgba(118, 75, 162, 0.2));
            border-radius: 20px;
            padding: 30px 40px;
            margin-bottom: 40px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
        }
        .welcome-section h2 {
            color: white;
            font-size: 32px;
            margin-bottom: 10px;
        }
        .welcome-section p {
            color: rgba(255, 255, 255, 0.7);
            font-size: 14px;
        }
        .stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 25px;
            margin-bottom: 40px;
        }
        .stat-card {
            background: rgba(255, 255, 255, 0.95);
            padding: 30px;
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(135deg, #667eea, #764ba2);
        }
        .stat-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        }
        .stat-card .icon {
            width: 60px;
            height: 60px;
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            margin-bottom: 20px;
        }
        .stat-card:nth-child(1) .icon { background: linear-gradient(135deg, #667eea, #764ba2); color: white; }
        .stat-card:nth-child(2) .icon { background: linear-gradient(135deg, #f093fb, #f5576c); color: white; }
        .stat-card:nth-child(3) .icon { background: linear-gradient(135deg, #4facfe, #00f2fe); color: white; }
        .stat-card:nth-child(4) .icon { background: linear-gradient(135deg, #43e97b, #38f9d7); color: white; }
        .stat-card:nth-child(5) .icon { background: linear-gradient(135deg, #fa709a, #fee140); color: white; }
        
        .stat-card h3 {
            color: #666;
            font-size: 14px;
            font-weight: 500;
            margin-bottom: 10px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .stat-card .number {
            font-size: 42px;
            font-weight: 700;
            background: linear-gradient(135deg, #1a1a2e, #16213e);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        
        .menu-section {
            margin-bottom: 40px;
        }
        .menu-section h2 {
            color: white;
            font-size: 24px;
            margin-bottom: 25px;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .menu {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 25px;
        }
        .menu-card {
            background: rgba(255, 255, 255, 0.95);
            padding: 35px 25px;
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
            text-align: center;
            text-decoration: none;
            color: #333;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        .menu-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.1), rgba(118, 75, 162, 0.1));
            transition: all 0.5s ease;
        }
        .menu-card:hover::before {
            left: 0;
        }
        .menu-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        }
        .menu-card .icon {
            font-size: 50px;
            margin-bottom: 20px;
            display: block;
        }
        .menu-card h3 {
            font-size: 18px;
            font-weight: 600;
            position: relative;
        }
        .menu-card p {
            font-size: 12px;
            color: #666;
            margin-top: 8px;
            position: relative;
        }
        
        @media (max-width: 768px) {
            .header {
                flex-direction: column;
                gap: 15px;
                text-align: center;
            }
            .container {
                padding: 20px;
            }
            .stats {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1><i class="fas fa-book-open"></i> School Library</h1>
        <div class="user-info">
            <span>Halo, <strong><?php echo $_SESSION['nama_lengkap']; ?></strong></span>
            <a href="logout.php" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>
    </div>
    
    <div class="container">
        <div class="welcome-section">
            <h2>Selamat Datang di Dashboard</h2>
            <p>Kelola perpustakaan dengan mudah dan cepat</p>
        </div>
        
        <div class="stats">
            <div class="stat-card">
                <div class="icon"><i class="fas fa-users"></i></div>
                <h3>Total Anggota</h3>
                <div class="number"><?php echo $anggota['total']; ?></div>
            </div>
            <div class="stat-card">
                <div class="icon"><i class="fas fa-book"></i></div>
                <h3>Total Buku</h3>
                <div class="number"><?php echo $buku['total']; ?></div>
            </div>
            <div class="stat-card">
                <div class="icon"><i class="fas fa-folder"></i></div>
                <h3>Total Kategori</h3>
                <div class="number"><?php echo $kategori['total']; ?></div>
            </div>
            <div class="stat-card">
                <div class="icon"><i class="fas fa-clock"></i></div>
                <h3>Peminjaman Aktif</h3>
                <div class="number"><?php echo $peminjaman_aktif['total']; ?></div>
            </div>
            <div class="stat-card">
                <div class="icon"><i class="fas fa-clipboard-list"></i></div>
                <h3>Total Peminjaman</h3>
                <div class="number"><?php echo $peminjaman['total']; ?></div>
            </div>
        </div>
        
        <div class="menu-section">
            <h2><i class="fas fa-tasks"></i> Menu Manajemen</h2>
            <div class="menu">
                <a href="anggota.php" class="menu-card">
                    <span class="icon">👥</span>
                    <h3>Kelola Anggota</h3>
                    <p>Kelola data anggota perpustakaan</p>
                </a>
                <a href="buku.php" class="menu-card">
                    <span class="icon">📖</span>
                    <h3>Kelola Buku</h3>
                    <p>Kelola data buku perpustakaan</p>
                </a>
                <a href="kategori.php" class="menu-card">
                    <span class="icon">📁</span>
                    <h3>Kelola Kategori</h3>
                    <p>Kelola kategori buku</p>
                </a>
                <a href="peminjaman.php" class="menu-card">
                    <span class="icon">📋</span>
                    <h3>Kelola Peminjaman</h3>
                    <p>Kelola peminjaman buku</p>
                </a>
            </div>
        </div>
    </div>
</body>
</html>
