<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Handle form submission
if (isset($_POST['submit'])) {
    $user_id = $_POST['user_id'];
    $buku_id = $_POST['buku_id'];
    $tgl_pinjam = $_POST['tgl_pinjam'];
    $tgl_kembali = $_POST['tgl_kembali'];
    $status = $_POST['status'];
    
    $stmt = $conn->prepare("INSERT INTO peminjaman (UserID, BukuID, TanggalPeminjaman, TanggalPengembalian, StatusPeminjaman) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("iisss", $user_id, $buku_id, $tgl_pinjam, $tgl_kembali, $status);
    $stmt->execute();
    $stmt->close();
    header("Location: peminjaman.php");
    exit;
}

// Handle delete
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM peminjaman WHERE PeminjamanID = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
    header("Location: peminjaman.php");
    exit;
}

// Handle update
if (isset($_POST['update'])) {
    $id = $_POST['id'];
    $user_id = $_POST['user_id'];
    $buku_id = $_POST['buku_id'];
    $tgl_pinjam = $_POST['tgl_pinjam'];
    $tgl_kembali = $_POST['tgl_kembali'];
    $status = $_POST['status'];
    
    $stmt = $conn->prepare("UPDATE peminjaman SET UserID=?, BukuID=?, TanggalPeminjaman=?, TanggalPengembalian=?, StatusPeminjaman=? WHERE PeminjamanID=?");
    $stmt->bind_param("iisssi", $user_id, $buku_id, $tgl_pinjam, $tgl_kembali, $status, $id);
    $stmt->execute();
    $stmt->close();
    header("Location: peminjaman.php");
    exit;
}

$result = mysqli_query($conn, "SELECT p.*, u.NamaLengkap, b.Judul 
    FROM peminjaman p 
    LEFT JOIN user u ON p.UserID = u.UserID 
    LEFT JOIN buku b ON p.BukuID = b.BukuID 
    ORDER BY p.PeminjamanID DESC");

$users = mysqli_query($conn, "SELECT * FROM user ORDER BY UserID DESC");
$books = mysqli_query($conn, "SELECT * FROM buku ORDER BY BukuID DESC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Peminjaman - Perpustakaan Yao</title>
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
        select, input { width: 100%; padding: 14px 18px; border: 2px solid #e0e0e0; border-radius: 12px; font-size: 14px; transition: all 0.3s ease; font-family: 'Poppins', sans-serif; background: white; }
        select:focus, input:focus { outline: none; border-color: #667eea; box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1); }
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
        .status-badge { padding: 6px 14px; border-radius: 20px; font-size: 12px; font-weight: 600; }
        .status-dipinjam { background: linear-gradient(135deg, #ffc107, #ffb300); color: #333; }
        .status-kembali { background: linear-gradient(135deg, #43e97b, #38f9d7); color: white; }
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
        <h1><i class="fas fa-clipboard-list"></i> Kelola Peminjaman</h1>
        <a href="logout.php" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>
    <div class="container">
        <a href="dashboard.php" class="back-btn"><i class="fas fa-arrow-left"></i> Kembali ke Dashboard</a>
        
        <div class="card">
            <h3><i class="fas fa-plus-circle"></i> Tambah Peminjaman Baru</h3>
            <form method="POST" action="">
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px;">
                    <div class="form-group">
                        <label>Peminjam (User)</label>
                        <select name="user_id" required>
                            <option value="">-- Pilih User --</option>
                            <?php mysqli_data_seek($users, 0); while ($u = mysqli_fetch_assoc($users)): ?>
                            <option value="<?php echo $u['UserID']; ?>"><?php echo $u['NamaLengkap']; ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Buku</label>
                        <select name="buku_id" required>
                            <option value="">-- Pilih Buku --</option>
                            <?php mysqli_data_seek($books, 0); while ($b = mysqli_fetch_assoc($books)): ?>
                            <option value="<?php echo $b['BukuID']; ?>"><?php echo $b['Judul']; ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Tanggal Peminjaman</label>
                        <input type="date" name="tgl_pinjam" required>
                    </div>
                    <div class="form-group">
                        <label>Tanggal Pengembalian</label>
                        <input type="date" name="tgl_kembali" required>
                    </div>
                    <div class="form-group">
                        <label>Status</label>
                        <select name="status" required>
                            <option value="Dipinjam">Dipinjam</option>
                            <option value="Dikembalikan">Dikembalikan</option>
                        </select>
                    </div>
                </div>
                <button type="submit" name="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan Data</button>
            </form>
        </div>
        
        <div class="card">
            <h3><i class="fas fa-list"></i> Daftar Peminjaman</h3>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Peminjam</th>
                            <th>Buku</th>
                            <th>Tgl Pinjam</th>
                            <th>Tgl Kembali</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (mysqli_num_rows($result) > 0): ?>
                            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                            <tr>
                                <td><?php echo $row['PeminjamanID']; ?></td>
                                <td><strong><?php echo $row['NamaLengkap']; ?></strong></td>
                                <td><?php echo $row['Judul']; ?></td>
                                <td><?php echo $row['TanggalPeminjaman']; ?></td>
                                <td><?php echo $row['TanggalPengembalian']; ?></td>
                                <td>
                                    <?php if ($row['StatusPeminjaman'] == 'Dipinjam'): ?>
                                        <span class="status-badge status-dipinjam">Dipinjam</span>
                                    <?php else: ?>
                                        <span class="status-badge status-kembali">Dikembalikan</span>
                                    <?php endif; ?>
                                </td>
                                <td class="actions">
                                    <a href="?edit=<?php echo $row['PeminjamanID']; ?>" class="edit"><i class="fas fa-edit"></i> Edit</a>
                                    <a href="?delete=<?php echo $row['PeminjamanID']; ?>" class="delete" onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?')"><i class="fas fa-trash"></i> Hapus</a>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7">
                                    <div class="empty-state">
                                        <i class="fas fa-clipboard"></i>
                                        <p>Belum ada data peminjaman</p>
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
        $stmt = $conn->prepare("SELECT * FROM peminjaman WHERE PeminjamanID = ?");
        $stmt->bind_param("i", $edit_id);
        $stmt->execute();
        $edit_data = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        $users2 = mysqli_query($conn, "SELECT * FROM user ORDER BY UserID DESC");
        $books2 = mysqli_query($conn, "SELECT * FROM buku ORDER BY BukuID DESC");
        ?>
        <div class="card">
            <h3><i class="fas fa-edit"></i> Edit Peminjaman</h3>
            <form method="POST" action="">
                <input type="hidden" name="id" value="<?php echo $edit_data['PeminjamanID']; ?>">
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px;">
                    <div class="form-group">
                        <label>Peminjam (User)</label>
                        <select name="user_id" required>
                            <?php while ($u = mysqli_fetch_assoc($users2)): ?>
                            <option value="<?php echo $u['UserID']; ?>" <?php echo ($u['UserID'] == $edit_data['UserID']) ? 'selected' : ''; ?>><?php echo $u['NamaLengkap']; ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Buku</label>
                        <select name="buku_id" required>
                            <?php while ($b = mysqli_fetch_assoc($books2)): ?>
                            <option value="<?php echo $b['BukuID']; ?>" <?php echo ($b['BukuID'] == $edit_data['BukuID']) ? 'selected' : ''; ?>><?php echo $b['Judul']; ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Tanggal Peminjaman</label>
                        <input type="date" name="tgl_pinjam" value="<?php echo $edit_data['TanggalPeminjaman']; ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Tanggal Pengembalian</label>
                        <input type="date" name="tgl_kembali" value="<?php echo $edit_data['TanggalPengembalian']; ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Status</label>
                        <select name="status" required>
                            <option value="Dipinjam" <?php echo ($edit_data['StatusPeminjaman'] == 'Dipinjam') ? 'selected' : ''; ?>>Dipinjam</option>
                            <option value="Dikembalikan" <?php echo ($edit_data['StatusPeminjaman'] == 'Dikembalikan') ? 'selected' : ''; ?>>Dikembalikan</option>
                        </select>
                    </div>
                </div>
                <button type="submit" name="update" class="btn btn-success"><i class="fas fa-sync-alt"></i> Update Data</button>
                <a href="peminjaman.php" class="btn btn-danger"><i class="fas fa-times"></i> Batal</a>
            </form>
        </div>
        <?php endif; ?>
   body>
</html>
