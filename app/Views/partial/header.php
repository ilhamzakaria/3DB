<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title><?= $title ?? 'Dashboard'; ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Nunito:wght@200;300;400;600;700;800;900&display=swap" rel="stylesheet">
    <?php if (isset($modern_layout) && $modern_layout): ?>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <?php else: ?>
        <link href="<?= base_url('assets/vendor/fontawesome-free/css/all.min.css') ?>" rel="stylesheet" type="text/css">
        <link href="<?= base_url('assets/css/sb-admin-2.min.css') ?>" rel="stylesheet">
    <?php endif; ?>
</head>

<body id="page-top">
    <div id="wrapper">