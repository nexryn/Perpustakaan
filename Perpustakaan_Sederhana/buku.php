<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Handle form submission
if (isset($_POST['submit'])) {
    $judul = $_POST['judul'];
    $penulis = $_POST['penulis'];
    $penerbit = $_POST['penerbit'];
    $tahun = $_POST['tahun'];
    $stok = $_POST['stok'];
    
    $stmt = $conn->prepare("INSERT INTO buku (Judul, Penulis, Penerbit, TahunTerbit, Stok) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssii", $judul, $penulis, $penerbit, $tahun, $stok);
    $stmt->execute();
    $stmt->close();
    header("Location: buku.php");
    exit;
}

// Handle delete
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM buku WHERE BukuID = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
    header("Location: buku.php");
    exit;
}

// Handle update
if (isset($_POST['update'])) {
    $id = $_POST['id'];
    $judul = $_POST['judul'];
    $penulis = $_POST['penulis'];
    $penerbit = $_POST['penerbit'];
    $tahun = $_POST['tahun'];
    $stok = $_POST['stok'];
    
    $stmt = $conn->prepare("UPDATE buku SET Judul=?, Penulis=?, Penerbit=?, TahunTerbit=?, Stok=? WHERE BukuID=?");
    $stmt->bind_param("sssiii", $judul, $penulis, $penerbit, $tahun, $stok, $id);
    $stmt->execute();
    $stmt->close();
    header("Location: buku.php");
    exit;
}

$result = mysqli_query($conn, "SELECT * FROM buku ORDER BY BukuID DESC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Buku - Perpustakaan Yao</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Poppins', sans-serif; background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%); min-height: 100vh; }
        .header { background: rgba(255, 255, 255, 0.1); backdrop-filter: blur(10px); color: white; padding: 20px 40px; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1); border-bottom: 1px solid rgba(255, 255, 255, 0.1); }
        .header h1 { font-size: 22px; display: flex; align-items: center; gap: 12px; }
        .header h1 i { background: linear-gradient(135deg, #667eea, #764ba2); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
        .logout-btn { background: linear-gradient(135deg, #ff6b6b, #ee5a5a); color: white; padding: 10px 25px; text-decoration: none; border-radius: 25px; font-weight: 500; transition: all 0.3s ease; display: flex; align-items: center; gap: 8px; }
        .logout-btn:hover { transform: translateY(-2px); box-shadow: 0 5px 20px rgba(255, 107, 107, 0.4); }
        .container { padding: 40px; max-width: 1400px; margin: 0 auto; }
        .page-title { color: white; font-size: 28px; margin-bottom: 30px; display: flex; align-items: center; gap: 15px; }
        .back-btn { display: inline-flex; align-items: center; gap: 8px; margin-bottom: 25px; padding: 12px 25px; background: rgba(255, 255, 255, 0.1); color: white; text-decoration: none; border-radius: 12px; transition: all 0.3s ease; border: 1px solid rgba(255, 255, 255, 0.2); }
        .back-btn:hover { background: rgba(255, 255, 255, 0.2); transform: translateX(-5px); }
        .card { background: rgba(255, 255, 255, 0.98); padding: 30px; border-radius: 20px; box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2); margin-bottom: 30px; }
        .card h3 { color: #1a1a2e; font-size: 20px; margin-bottom: 25px; display: flex; align-items: center; gap: 12px; padding-bottom: 15px; border-bottom: 2px solid #f0f0f0; }
        .card h3 i { background: linear-gradient(135deg, #667eea, #764ba2); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
        .form-group { margin-bottom: 20px; }
        label { display: block; margin-bottom: 8px; color: #333; font-weight: 600; font-size: 14px; }
        input { width: 100%; padding: 14px 18px; border: 2px solid #e0e0e0; border-radius: 12px; font-size: 14px; transition: all 0.3s ease; font-family: 'Poppins', sans-serif; }
        input:focus { outline: none; border-color: #667eea; box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1); }
        .btn { padding: 14px 28px; border: none; border-radius: 12px; cursor: pointer; font-size: 14px; font-weight: 600; transition: all 0.3s ease; display: inline-flex; align-items: center; gap: 8px; font-family: 'Poppins', sans-serif; }
        .btn-primary { background: linear-gradient(135deg, #667eea, #764ba2); color: white; }
        .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 5px 20px rgba(102, 126, 234, 0.4); }
        .btn-danger { background: linear-gradient(135deg, #ff6b6b, #ee5a5a); color: white; }
        .btn-danger:hover { transform: translateY(-2px); box-shadow: 0 5px 20px rgba(255, 107, 107, 0.4); }
        .btn-success { background: linear-gradient(135deg, #43e97b, #38f9d7); color: white; }
        .btn-success:hover { transform: translateY(-2px); box-shadow: 0 5px 20px rgba(67, 233, 123, 0.4); }
        .table-container { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; background: white; border-radius: 15px; overflow: hidden; }
        th, td { padding: 16px; text-align: left; border-bottom: 1px solid #f0f0f0; }
        th { background: linear-gradient(135deg, #667eea, #764ba2); color: white; font-weight: 600; font-size: 13px; text-transform: uppercase; letter-spacing: 0.5px; }
        tr:hover { background: #f8f9ff; }
        .stok-badge { padding: 5px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; }
        .stok-tersedia { background: linear-gradient(135deg, #43e97b, #38f9d7); color: white; }
        .stok-kosong { background: linear-gradient(135deg, #ff6b6b, #ee5a5a); color: white; }
        .actions { display: flex; gap: 8px; }
        .actions a { padding: 8px 14px; border-radius: 8px; text-decoration: none; font-size: 12px; font-weight: 600; transition: all 0.3s ease; }
        .actions .edit { background: linear-gradient(135deg, #ffc107, #ffb300); color: #333; }
        .actions .edit:hover { transform: translateY(-2px); box-shadow: 0 3px 15px rgba(255, 193, 7, 0.4); }
        .actions .delete { background: linear-gradient(135deg, #ff6b6b, #ee5a5a); color: white; }
        .actions .delete:hover { transform: translateY(-2px); box-shadow: 0 3px 15px rgba(255, 107, 107, 0.4); }
        .empty-state { text-align: center; padding: 50px; color: #999; }
        .empty-state i { font-size: 50px; margin-bottom: 15px; opacity: 0.5; }
        
        @media (max-width: 768px) {
            .header { flex-direction: column; gap: 15px; text-align: center; }
            .container { padding: 20px; }
            .card { padding: 20px; }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1><i class="fas fa-book"></i> Kelola Buku</h1>
        <a href="logout.php" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>
    <div class="container">
        <a href="dashboard.php" class="back-btn"><i class="fas fa-arrow-left"></i> Kembali ke Dashboard</a>
        
        <div class="card">
            <h3><i class="fas fa-plus-circle"></i> Tambah Buku Baru</h3>
            <form method="POST" action="">
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px;">
                    <div class="form-group">
                        <label>Judul Buku</label>
                        <input type="text" name="judul" required placeholder="Masukkan judul buku">
                    </div>
                    <div class="form-group">
                        <label>Penulis</label>
                        <input type="text" name="penulis" required placeholder="Nama penulis">
                    </div>
                    <div class="form-group">
                        <label>Penerbit</label>
                        <input type="text" name="penerbit" required placeholder="Nama penerbit">
                    </div>
                    <div class="form-group">
                        <label>Tahun Terbit</label>
                        <input type="number" name="tahun" required placeholder="Tahun">
                    </div>
                    <div class="form-group">
                        <label>Stok</label>
                        <input type="number" name="stok" value="0" required placeholder="Jumlah stok">
                    </div>
                </div>
                <button type="submit" name="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan Data</button>
            </form>
        </div>
        
        <div class="card">
            <h3><i class="fas fa-list"></i> Daftar Buku</h3>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Judul</th>
                            <th>Penulis</th>
                            <th>Penerbit</th>
                            <th>Tahun</th>
                            <th>Stok</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (mysqli_num_rows($result) > 0): ?>
                            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                            <tr>
                                <td><?php echo $row['BukuID']; ?></td>
                                <td><strong><?php echo $row['Judul']; ?></strong></td>
                                <td><?php echo $row['Penulis']; ?></td>
                                <td><?php echo $row['Penerbit']; ?></td>
                                <td><?php echo $row['TahunTerbit']; ?></td>
                                <td>
                                    <?php if ($row['Stok'] > 0): ?>
                                        <span class="stok-badge stok-tersedia"><?php echo $row['Stok']; ?> Buku</span>
                                    <?php else: ?>
                                        <span class="stok-badge stok-kosong">Habis</span>
                                    <?php endif; ?>
                                </td>
                                <td class="actions">
                                    <a href="?edit=<?php echo $row['BukuID']; ?>" class="edit"><i class="fas fa-edit"></i> Edit</a>
                                    <a href="?delete=<?php echo $row['BukuID']; ?>" class="delete" onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?')"><i class="fas fa-trash"></i> Hapus</a>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7">
                                    <div class="empty-state">
                                        <i class="fas fa-book-dead"></i>
                                        <p>Belum ada data buku</p>
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        
        <?php if (isset($_GET['edit'])): ?>
        <?php 
        $edit_id = $_GET['edit'];
        $stmt = $conn->prepare("SELECT * FROM buku WHERE BukuID = ?");
        $stmt->bind_param("i", $edit_id);
        $stmt->execute();
        $edit_data = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        ?>
        <div class="card">
            <h3><i class="fas fa-edit"></i> Edit Buku</h3>
            <form method="POST" action="">
                <input type="hidden" name="id" value="<?php echo $edit_data['BukuID']; ?>">
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px;">
                    <div class="form-group">
                        <label>Judul Buku</label>
                        <input type="text" name="judul" value="<?php echo $edit_data['Judul']; ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Penulis</label>
                        <input type="text" name="penulis" value="<?php echo $edit_data['Penulis']; ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Penerbit</label>
                        <input type="text" name="penerbit" value="<?php echo $edit_data['Penerbit']; ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Tahun Terbit</label>
                        <input type="number" name="tahun" value="<?php echo $edit_data['TahunTerbit']; ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Stok</label>
                        <input type="number" name="stok" value="<?php echo $edit_data['Stok']; ?>" required>
                    </div>
                </div>
                <button type="submit" name="update" class="btn btn-success"><i class="fas fa-sync-alt"></i> Update Data</button>
                <a href="buku.php" class="btn btn-danger"><i class="fas fa-times"></i> Batal</a>
            </form>
        </div>
        <?php endif; ?>
    </div>
</body>
</html>
