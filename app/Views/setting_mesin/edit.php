<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1 class="h3 text-gray-800">
                <i class="fas fa-cogs me-2"></i>Edit Setting Mesin
            </h1>
        </div>
        <div class="col-md-4 text-end">
            <a href="<?= base_url('settings-mesin') ?>" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Kembali
            </a>
        </div>
    </div>

    <!-- Alert for validation errors -->
    <?php if (session()->getFlashdata('errors')): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <h6 class="alert-heading">
            <i class="fas fa-exclamation-circle me-2"></i>Validasi Gagal
        </h6>
        <ul class="mb-0">
            <?php foreach (session()->getFlashdata('errors') as $error): ?>
            <li><?= $error ?></li>
            <?php endforeach; ?>
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>

    <!-- Form Card -->
    <div class="card shadow">
        <div class="card-body">
            <form action="<?= base_url('settings-mesin/update/' . $item['id']) ?>" method="POST" novalidate>
                <?= csrf_field() ?>

                <div class="row mb-3">
                    <div class="col-12">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>ID Data:</strong> #<?= $item['id'] ?>
                        </div>
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-md-6">
                        <label for="tanggal" class="form-label fw-bold">
                            Tanggal <span class="text-danger">*</span>
                        </label>
                        <input type="date" 
                               class="form-control <?= session('errors.tanggal') ? 'is-invalid' : '' ?>" 
                               id="tanggal" 
                               name="tanggal" 
                               value="<?= old('tanggal', $item['tanggal']) ?>" 
                               required>
                        <?php if (session('errors.tanggal')): ?>
                        <div class="invalid-feedback" style="display: block;">
                            <?= session('errors.tanggal') ?>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-md-6">
                        <label for="product" class="form-label fw-bold">
                            Produk <span class="text-danger">*</span>
                        </label>
                        <input type="text" 
                               class="form-control <?= session('errors.product') ? 'is-invalid' : '' ?>" 
                               id="product" 
                               name="product" 
                               value="<?= old('product', $item['product']) ?>" 
                               placeholder="Masukkan nama produk"
                               required>
                        <?php if (session('errors.product')): ?>
                        <div class="invalid-feedback" style="display: block;">
                            <?= session('errors.product') ?>
                        </div>
                        <?php endif; ?>
                    </div>

                    <div class="col-md-6">
                        <label for="machine" class="form-label fw-bold">
                            Mesin <span class="text-danger">*</span>
                        </label>
                        <input type="text" 
                               class="form-control <?= session('errors.machine') ? 'is-invalid' : '' ?>" 
                               id="machine" 
                               name="machine" 
                               value="<?= old('machine', $item['machine']) ?>" 
                               placeholder="Masukkan nama mesin"
                               required>
                        <?php if (session('errors.machine')): ?>
                        <div class="invalid-feedback" style="display: block;">
                            <?= session('errors.machine') ?>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-md-6">
                        <label for="type" class="form-label fw-bold">
                            Tipe <span class="text-danger">*</span>
                        </label>
                        <input type="text" 
                               class="form-control <?= session('errors.type') ? 'is-invalid' : '' ?>" 
                               id="type" 
                               name="type" 
                               value="<?= old('type', $item['type']) ?>" 
                               placeholder="Masukkan tipe mesin"
                               required>
                        <?php if (session('errors.type')): ?>
                        <div class="invalid-feedback" style="display: block;">
                            <?= session('errors.type') ?>
                        </div>
                        <?php endif; ?>
                    </div>

                    <div class="col-md-6">
                        <label for="no_mc" class="form-label fw-bold">
                            Nomor Mesin <span class="text-danger">*</span>
                        </label>
                        <input type="text" 
                               class="form-control <?= session('errors.no_mc') ? 'is-invalid' : '' ?>" 
                               id="no_mc" 
                               name="no_mc" 
                               value="<?= old('no_mc', $item['no_mc']) ?>" 
                               placeholder="Masukkan nomor mesin"
                               required>
                        <?php if (session('errors.no_mc')): ?>
                        <div class="invalid-feedback" style="display: block;">
                            <?= session('errors.no_mc') ?>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-md-6">
                        <label for="shift" class="form-label fw-bold">
                            Shift <span class="text-danger">*</span>
                        </label>
                        <select class="form-select <?= session('errors.shift') ? 'is-invalid' : '' ?>" 
                                id="shift" 
                                name="shift" 
                                required>
                            <option value="">-- Pilih Shift --</option>
                            <option value="1" <?= old('shift', $item['shift']) == '1' ? 'selected' : '' ?>>Shift 1</option>
                            <option value="2" <?= old('shift', $item['shift']) == '2' ? 'selected' : '' ?>>Shift 2</option>
                            <option value="3" <?= old('shift', $item['shift']) == '3' ? 'selected' : '' ?>>Shift 3</option>
                        </select>
                        <?php if (session('errors.shift')): ?>
                        <div class="invalid-feedback" style="display: block;">
                            <?= session('errors.shift') ?>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="form-group mt-5 pt-3 border-top">
                    <button type="submit" class="btn btn-primary me-2">
                        <i class="fas fa-sync me-2"></i>Perbarui Data
                    </button>
                    <a href="<?= base_url('settings-mesin') ?>" class="btn btn-secondary">
                        <i class="fas fa-times me-2"></i>Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .form-label {
        color: #4a5568;
        margin-bottom: 0.5rem;
    }

    .form-control:focus,
    .form-select:focus {
        border-color: #4361ee;
        box-shadow: 0 0 0 0.2rem rgba(67, 97, 238, 0.25);
    }

    .form-control.is-invalid,
    .form-select.is-invalid {
        border-color: #dc3545;
    }

    .invalid-feedback {
        display: block;
        color: #dc3545;
        font-size: 0.875rem;
        margin-top: 0.25rem;
    }

    .text-danger {
        color: #dc3545;
    }
</style>
<?= $this->endSection() ?>
