<?php
// Mulai session dan load kelas User
require_once 'User.php';
require_once 'koneksi.php'; // File koneksi database
session_start();

$errors = [];
$successMessage = '';

// Fungsi untuk validasi email
function isValidEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

// Fungsi untuk menyimpan data ke file
function saveToFile($name, $email, $phone, $program, $schedule) {
    $file = fopen("pendaftaran.txt", "a");
    if ($file) {
        $data = "Nama: $name, Email: $email, Telepon: $phone, Program: $program, Jadwal: $schedule\n";
        fwrite($file, $data);
        fclose($file);
        return true;
    }
    return false;
}

// Fungsi untuk menyimpan data ke database
function saveToDatabase($koneksi, $name, $email, $phone, $program, $schedule) {
    $sql = "INSERT INTO pendaftaran (nama, email, telepon, program, jadwal) 
            VALUES (?, ?, ?, ?, ?)";
    $stmt = $koneksi->prepare($sql);
    $stmt->bind_param("sssss", $name, $email, $phone, $program, $schedule);
    return $stmt->execute();
}

// Proses Update Data
if (isset($_POST['update'])) {
    $id = $_POST['id'];
    $name = htmlspecialchars($_POST['name']);
    $email = htmlspecialchars($_POST['email']);
    $phone = htmlspecialchars($_POST['phone']);
    $program = htmlspecialchars($_POST['program']);
    $schedule = htmlspecialchars($_POST['jadwal']);

    $sql = "UPDATE pendaftaran SET nama=?, email=?, telepon=?, program=?, jadwal=? WHERE id=?";
    $stmt = $koneksi->prepare($sql);
    $stmt->bind_param("sssssi", $name, $email, $phone, $program, $schedule, $id);

    if ($stmt->execute()) {
        $successMessage = "Data berhasil diperbarui!";
    } else {
        $errors[] = "Gagal memperbarui data: " . $koneksi->error;
    }
}

// Proses Delete Data
if (isset($_POST['delete'])) {
    $id = $_POST['id'];

    $sql = "DELETE FROM pendaftaran WHERE id=?";
    $stmt = $koneksi->prepare($sql);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        $successMessage = "Data berhasil dihapus!";
    } else {
        $errors[] = "Gagal menghapus data: " . $koneksi->error;
    }
}

// Proses form jika dikirim
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['daftar'])) {
    // Ambil data dari form
    $name = htmlspecialchars($_POST['name']);
    $email = htmlspecialchars($_POST['email']);
    $password = htmlspecialchars($_POST['password']);
    $phone = htmlspecialchars($_POST['phone']);
    $program = htmlspecialchars($_POST['program']);
    $schedule = htmlspecialchars($_POST['jadwal']);

    // Validasi data
    if (empty($name) || empty($email) || empty($password) || empty($phone)) {
        $errors[] = "Semua kolom harus diisi.";
    }
    if (!isValidEmail($email)) {
        $errors[] = "Email tidak valid.";
    }

    // Jika tidak ada error, simpan data
    if (empty($errors)) {
        $user = new User($name, $email, $password, $phone, $program, $schedule);

        // Simpan ke file
        $fileSaved = saveToFile($name, $email, $phone, $program, $schedule);

        // Simpan ke database
        if ($koneksi->connect_error) {
            $errors[] = "Gagal menyambungkan ke database.";
        } else {
            $dbSaved = saveToDatabase($koneksi, $name, $email, $phone, $program, $schedule);
        }

        if ($fileSaved && $dbSaved) {
            // Set cookie untuk menyimpan nama pengguna selama 7 hari
            setcookie("username", $name, time() + (7 * 24 * 60 * 60), "/"); // Cookie berlaku 7 hari
            $successMessage = "Pendaftaran berhasil!";
        } else {
            $errors[] = "Gagal menyimpan data.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulir Pendaftaran</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #0A0F29, #1D2848);
            color: #ffffff;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .container {
            background: #141D3B;
            padding: 40px;
            border-radius: 8px;
            width: 400px;
            text-align: center;
            box-shadow: 0px 4px 12px rgba(0, 0, 0, 0.3),
                        0px 0px 15px 4px #00D1FF;
        }

        form label {
            display: block;
            text-align: left;
            margin-bottom: 8px;
            font-weight: bold;
        }

        form input,
        form select {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: none;
            border-radius: 4px;
            background: #2A3351;
            color: #ffffff;
            font-size: 16px;
            outline: none;
        }

        form button {
            width: 100%;
            padding: 12px;
            background: #00D1FF;
            color: #141D3B;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            font-weight: bold;
        }
    </style>
</head>
<body>
<div class="container">
        <?php if (!empty($successMessage)) : ?>
            <div style="color: #00FF00; font-weight: bold;">
                <p><?= $successMessage ?></p>
                <p>Terimakasih telah mendaftar di Edu Mastery</p>
            </div>
            <script>
                setTimeout(function() {
                    window.location.href = "index.html"; // Redirect ke halaman index.html
                }, 3000); // Redirect setelah 3 detik
            </script>
        <?php else : ?>
            <h2>Formulir Pendaftaran</h2>

            <?php if (!empty($errors)) : ?>
                <ul style="color: #FF0000; font-weight: bold;">
                    <?php foreach ($errors as $error) : ?>
                        <li><?= $error ?></li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>

            <form action="" method="post">
                <label for="name">Nama Lengkap:</label>
                <input type="text" id="name" name="name" required>

                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>

                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>

                <label for="phone">Nomor Telepon:</label>
                <input type="tel" id="phone" name="phone" required>

                <label for="program">Pilih Program Bimbel:</label>
                <select id="program" name="program">
                    <option value="matematika">Matematika</option>
                    <option value="fisika">Fisika</option>
                    <option value="kimia">Kimia</option>
                    <option value="bahasa-inggris">Bahasa Inggris</option>
                </select>

                <label for="jadwal">Pilih Jadwal:</label>
                <select id="jadwal" name="jadwal">
                    <option value="senin-rabu">Senin & Rabu</option>
                    <option value="selasa-kamis">Selasa & Kamis</option>
                    <option value="jumat-sabtu">Jumat & Sabtu</option>
                </select>

                <button type="submit" name="daftar">Daftar</button>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>
