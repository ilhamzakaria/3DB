<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>404 - Halaman Tidak Ditemukan</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <style>
        body {
            margin: 0;
            padding: 0;
            background: linear-gradient(135deg, #16a34a, #22c55e);
            font-family: 'Segoe UI', sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .container {
            background: #ffffff;
            padding: 30px;
            border-radius: 20px;
            text-align: center;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
            max-width: 450px;
            width: 90%;
        }

        h1 {
            font-size: 90px;
            margin: 0;
            color: #16a34a;
        }

        h2 {
            margin-top: 10px;
            font-weight: 600;
            color: #111827;
        }

        p {
            margin-top: 20px;
            color: #4b5563;
        }

        a {
            display: inline-block;
            margin-top: 30px;
            padding: 12px 35px;
            background: #16a34a;
            color: #ffffff;
            text-decoration: none;
            border-radius: 30px;
            font-weight: bold;
            transition: 0.3s ease;
        }

        a:hover {
            background: #15803d;
            transform: translateY(-3px);
        }
    </style>
</head>

<body>

    <div class="container">
        <h1>404</h1>
        <h2>Halaman Tidak Ditemukan</h2>
        <p>Maaf, halaman yang kamu cari tidak tersedia atau sudah dipindahkan.</p>

        <a href="<?= base_url() ?>">Kembali ke Beranda</a>
    </div>

</body>

</html>