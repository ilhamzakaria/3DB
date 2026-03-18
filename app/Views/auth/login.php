<!-- <form method="post" action="<?= base_url('login') ?>">
    <?= csrf_field() ?>

    <input type="text" name="username" placeholder="Username" class="form-control mb-2">
    <input type="password" name="password" placeholder="Password" class="form-control mb-2">

    <button class="btn btn-primary w-100">Login</button>
</form> -->
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>

    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            background: #e9e9e9;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .login-box {
            width: 320px;
            background: #fff;
            border: 1px solid #ddd;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
        }

        .login-header {
            border-top: 5px solid #2da8d8;
            padding: 30px 35px 20px;
            position: relative;
        }

        .login-header h2 {
            margin: 0 0 20px;
            font-size: 24px;
            color: #2da8d8;
            font-weight: normal;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-control {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            font-size: 14px;
            box-sizing: border-box;
        }

        .btn-login {
            width: 100%;
            background: #2da8d8;
            color: #fff;
            border: none;
            padding: 10px;
            font-size: 16px;
            cursor: pointer;
        }

        .btn-login:hover {
            background: #2498c6;
        }

        .login-footer {
            background: #f5f5f5;
            text-align: center;
            padding: 15px;
            font-size: 13px;
            color: #666;
        }

        .login-footer a {
            color: #666;
            text-decoration: none;
        }

        .login-footer a:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>

    <div class="login-box">

        <div class="login-header">
            <h2>PT ORO PLASTINDO</h2>

            <form method="post" action="<?= base_url('login') ?>">
                <?= csrf_field() ?>
                <div class="form-group">
                    <input type="text" class="form-control" name="username" placeholder="Username">
                </div>

                <div class="form-group">
                    <input type="password" class="form-control" name="password" placeholder="Password">
                </div>

                <button type="submit" class="btn-login">Login</button>
            </form>



        </div>

        <div class="login-footer">
            <a href="#">Forgot your password?</a>
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <?php if (session()->getFlashdata('error')): ?>
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Login Gagal',
                text: '<?= session()->getFlashdata('error'); ?>',
                confirmButtonColor: '#3085d6',
                background: '#fff',
                color: '#333',
                showClass: {
                    popup: 'animate__animated animate__fadeInDown'
                },
                hideClass: {
                    popup: 'animate__animated animate__fadeOutUp'
                }
            });
        </script>
    <?php endif; ?>

</body>

</html>

<!--  -->

<script>
    setTimeout(function() {
        $('.alert').fadeOut('slow');
    }, 3000);
</script>