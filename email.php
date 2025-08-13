<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php'; // pastikan path ini benar

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama = $_POST['name'];
    $email = $_POST['email'];
    $subjek = $_POST['subject'];
    $pesan = $_POST['message'];

    $mail = new PHPMailer(true);

    try {
        // Konfigurasi SMTP
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'smkmaarifterpaduclk@gmail.com'; // Email pengirim
        $mail->Password   = '***isi password Gmail atau App Password di sini***';
        $mail->SMTPSecure = 'tls';
        $mail->Port       = 587;

        // Penerima
        $mail->setFrom('smkmaarifterpaduclk@gmail.com', 'Form Website');
        $mail->addAddress('smkmaarifterpaduclk@gmail.com'); // Email tujuan

        // Konten Email
        $mail->isHTML(true);
        $mail->Subject = $subjek;
        $mail->Body    = "Nama: $nama <br>Email: $email <br>Pesan:<br>$pesan";

        $mail->send();
        echo "Pesan berhasil dikirim.";
    } catch (Exception $e) {
        echo "Pesan gagal dikirim. Mailer Error: {$mail->ErrorInfo}";
    }
}
?>
