<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login | MoizPayment</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?= base_url('assets/css/app.css'); ?>" rel="stylesheet">
</head>
<body class="auth-body">
<div class="auth-card card shadow-lg">
    <div class="card-body p-4 p-lg-5">
        <div class="mb-4">
            <div class="auth-badge">MoizPayment</div>
            <h1 class="h3 mt-3 mb-2">Masuk ke aplikasi</h1>
            <p class="text-secondary mb-0">Gunakan akun admin default untuk memulai konfigurasi sistem.</p>
        </div>

        <?php if ($this->session->flashdata('error')): ?>
            <div class="alert alert-danger"><?= html_escape($this->session->flashdata('error')); ?></div>
        <?php endif; ?>

        <?php if (validation_errors()): ?>
            <div class="alert alert-danger"><?= validation_errors(); ?></div>
        <?php endif; ?>

        <?= form_open('login'); ?>
            <div class="mb-3">
                <label class="form-label">Email / Username</label>
                <input type="text" name="identity" class="form-control form-control-lg" value="<?= set_value('identity', 'admin'); ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control form-control-lg" value="admin12345" required>
            </div>
            <button type="submit" class="btn btn-primary btn-lg w-100">Login</button>
        <?= form_close(); ?>

        <div class="demo-credential mt-4">
            <div><strong>Demo:</strong> `admin` / `admin12345`</div>
        </div>
    </div>
</div>
</body>
</html>
