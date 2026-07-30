<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<style>
.profile-container {
    max-width: 900px;
    margin: 0 auto;
    padding: 2rem;
}

.profile-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 3rem 2rem;
    border-radius: 15px 15px 0 0;
    text-align: center;
    margin-bottom: 0;
}

.profile-header h1 {
    margin: 0;
    font-size: 2rem;
    font-weight: 600;
}

.profile-header p {
    margin: 0.5rem 0 0 0;
    opacity: 0.9;
    font-size: 1rem;
}

.profile-card {
    background: white;
    border-radius: 0 0 15px 15px;
    box-shadow: 0 5px 20px rgba(0,0,0,0.1);
    overflow: hidden;
}

.profile-content {
    padding: 2rem;
}

.upload-section {
    margin-bottom: 3rem;
    padding-bottom: 2rem;
    border-bottom: 2px solid #f0f0f0;
}

.upload-section:last-child {
    border-bottom: none;
    margin-bottom: 0;
    padding-bottom: 0;
}

.upload-title {
    font-size: 1.3rem;
    font-weight: 600;
    color: #333;
    margin-bottom: 1.5rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.upload-title i {
    color: #667eea;
    font-size: 1.5rem;
}

.upload-area {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 2rem;
    align-items: start;
}

.upload-form {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.file-input-wrapper {
    position: relative;
    display: block;
}

.file-input-label {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    padding: 1.5rem;
    border: 2px dashed #667eea;
    border-radius: 10px;
    background: #f8f9ff;
    cursor: pointer;
    transition: all 0.3s ease;
    font-weight: 500;
    color: #667eea;
}

.file-input-label:hover {
    background: #667eea;
    color: white;
    border-color: #667eea;
}

.file-input-label i {
    font-size: 1.5rem;
}

.file-input {
    display: none;
}

.preview-container {
    text-align: center;
}

.preview-image {
    max-width: 200px;
    max-height: 200px;
    border-radius: 10px;
    border: 2px solid #e0e0e0;
    object-fit: contain;
    background: #f9f9f9;
    padding: 0.5rem;
}

.no-preview {
    width: 200px;
    height: 200px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #f0f0f0;
    border-radius: 10px;
    color: #999;
    font-size: 3rem;
    margin: 0 auto;
}

.button-group {
    display: flex;
    gap: 0.5rem;
    justify-content: center;
    margin-top: 1rem;
}

.btn-upload {
    flex: 1;
    padding: 0.75rem 1.5rem;
    background: #667eea;
    color: white;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    font-weight: 600;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
}

.btn-upload:hover {
    background: #5568d3;
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
}

.btn-upload:disabled {
    background: #ccc;
    cursor: not-allowed;
    transform: none;
}

.btn-auto-ttd {
    flex: 1;
    padding: 0.75rem 1.5rem;
    background: #28a745;
    color: white;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    font-weight: 600;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
}

.btn-auto-ttd:hover {
    background: #218838;
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(40, 167, 69, 0.4);
}

.btn-delete {
    padding: 0.75rem 1.5rem;
    background: #dc3545;
    color: white;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    font-weight: 600;
    transition: all 0.3s ease;
}

.btn-delete:hover {
    background: #c82333;
}

.upload-status {
    padding: 0.75rem 1rem;
    border-radius: 8px;
    margin-top: 0.5rem;
    display: none;
    align-items: center;
    gap: 0.5rem;
}

.upload-status.success {
    background: #d4edda;
    color: #155724;
    display: flex;
}

.upload-status.error {
    background: #f8d7da;
    color: #721c24;
    display: flex;
}

.upload-status i {
    font-size: 1.1rem;
}

.loading-spinner {
    display: inline-block;
    width: 16px;
    height: 16px;
    border: 2px solid #f3f3f3;
    border-top: 2px solid #667eea;
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

.file-size-info {
    font-size: 0.85rem;
    color: #999;
    text-align: center;
    margin-top: 0.5rem;
}

@media (max-width: 768px) {
    .profile-container {
        padding: 1rem;
    }

    .upload-area {
        grid-template-columns: 1fr;
    }

    .button-group {
        flex-direction: column;
    }

    .profile-header {
        padding: 2rem 1rem;
    }

    .profile-header h1 {
        font-size: 1.5rem;
    }
}
</style>

<div class="profile-container">
    <!-- Header -->
    <div class="profile-header">
        <h1><i class="fas fa-user-circle"></i> Profile Pengguna</h1>
        <p>Username: <strong><?= esc($user['username']) ?></strong></p>
        <p>Role: <strong><?= esc($user['role']) ?></strong></p>
    </div>

    <!-- Card -->
    <div class="profile-card">
        <div class="profile-content">

            <!-- Foto Profile Section -->
            <div class="upload-section">
                <div class="upload-title">
                    <i class="fas fa-image"></i>
                    Foto Profile
                </div>

                <div class="upload-area">
                    <!-- Form Upload -->
                    <div class="upload-form">
                        <div class="file-input-wrapper">
                            <label for="fotoInput" class="file-input-label">
                                <i class="fas fa-cloud-upload-alt"></i>
                                Pilih Foto
                            </label>
                            <input
                                type="file"
                                id="fotoInput"
                                class="file-input"
                                accept="image/jpeg,image/png,image/gif,image/webp"
                                data-type="foto"
                            >
                        </div>
                        <div class="file-size-info">
                            Format: JPG, PNG, GIF, WebP<br>
                            Maksimal: 5MB
                        </div>
                        <div class="button-group">
                            <button type="button" class="btn-upload" id="uploadFotoBtn" disabled>
                                <i class="fas fa-upload"></i>
                                Upload Foto
                            </button>
                        </div>
                        <div class="upload-status" id="fotoStatus"></div>
                    </div>

                    <!-- Preview -->
                    <div class="preview-container">
                        <?php if (!empty($user['foto_profile'])) : ?>
                            <img 
                                src="<?= base_url($user['foto_profile']) ?>"
                                alt="Foto Profile"
                                class="preview-image"
                                id="fotoPreview"
                            >
                        <?php else : ?>
                            <div class="no-preview" id="fotoPreview">
                                <i class="fas fa-image"></i>
                            </div>
                        <?php endif; ?>
                        <p style="color: #999; font-size: 0.9rem; margin-top: 1rem;">
                            Preview Foto
                        </p>
                    </div>
                </div>
            </div>

            <!-- Tanda Tangan Section -->
            <div class="upload-section">
                <div class="upload-title">
                    <i class="fas fa-pen-fancy"></i>
                    Tanda Tangan
                </div>

                <div class="upload-area">
                    <!-- Form Upload -->
                    <div class="upload-form">
                        <div class="file-input-wrapper">
                            <label for="ttdInput" class="file-input-label">
                                <i class="fas fa-cloud-upload-alt"></i>
                                Pilih Tanda Tangan
                            </label>
                            <input
                                type="file"
                                id="ttdInput"
                                class="file-input"
                                accept="image/jpeg,image/png,image/gif,image/webp"
                                data-type="ttd"
                            >
                        </div>
                        <div class="file-size-info">
                            Format: JPG, PNG, GIF, WebP<br>
                            Maksimal: 5MB
                        </div>
                        <div class="button-group">
                            <button type="button" class="btn-upload" id="uploadTtdBtn" disabled>
                                <i class="fas fa-upload"></i>
                                Upload TTD
                            </button>
                            <button type="button" class="btn-auto-ttd" id="autoTtdBtn">
                                <i class="fas fa-magic"></i>
                                Gunakan Default
                            </button>
                        </div>
                        <div class="upload-status" id="ttdStatus"></div>
                    </div>

                    <!-- Preview -->
                    <div class="preview-container">
                        <?php if (!empty($user['tanda_tangan'])) : ?>
                            <img
                                src="<?= base_url($user['tanda_tangan']) ?>"
                                alt="Tanda Tangan"
                                class="preview-image"
                                id="ttdPreview"
                            >
                        <?php else : ?>
                            <div class="no-preview" id="ttdPreview">
                                <i class="fas fa-pen"></i>
                            </div>
                        <?php endif; ?>
                        <p style="color: #999; font-size: 0.9rem; margin-top: 1rem;">
                            Preview TTD
                        </p>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const fotoInput = document.getElementById('fotoInput');
    const ttdInput = document.getElementById('ttdInput');
    const uploadFotoBtn = document.getElementById('uploadFotoBtn');
    const uploadTtdBtn = document.getElementById('uploadTtdBtn');
    const autoTtdBtn = document.getElementById('autoTtdBtn');
    const fotoStatus = document.getElementById('fotoStatus');
    const ttdStatus = document.getElementById('ttdStatus');

    // Get CSRF token from meta tag or cookie
    function getCsrfToken() {
        // Try to get from meta tag first
        const meta = document.querySelector('meta[name="csrf-token"]');
        if (meta) return meta.getAttribute('content');
        
        // Try to get from cookie
        const name = 'XSRF-TOKEN';
        const value = `; ${document.cookie}`;
        const parts = value.split(`; ${name}=`);
        if (parts.length === 2) return parts.pop().split(';').shift();
        
        // Get from session/hidden input in form
        const csrfInput = document.querySelector('input[name="<?= csrf_token() ?>"]');
        if (csrfInput) return csrfInput.value;
        
        return null;
    }

    // Get CSRF hash field name
    function getCsrfHashField() {
        return '<?= csrf_header() ?>';
    }

    // Enable/disable upload button saat file dipilih
    fotoInput.addEventListener('change', function() {
        uploadFotoBtn.disabled = !this.files.length;
    });

    ttdInput.addEventListener('change', function() {
        uploadTtdBtn.disabled = !this.files.length;
    });

    // Upload Foto
    uploadFotoBtn.addEventListener('click', function() {
        uploadFile('foto', fotoInput, fotoStatus, fotoInput.files[0]);
    });

    // Upload TTD
    uploadTtdBtn.addEventListener('click', function() {
        uploadFile('ttd', ttdInput, ttdStatus, ttdInput.files[0]);
    });

    // Auto Upload TTD
    autoTtdBtn.addEventListener('click', function() {
        autoTtdBtn.disabled = true;
        const originalText = autoTtdBtn.innerHTML;
        autoTtdBtn.innerHTML = '<span class="loading-spinner"></span> Memproses...';

        const headers = {
            'X-Requested-With': 'XMLHttpRequest'
        };
        
        // Add CSRF token to headers
        const csrfToken = getCsrfToken();
        const csrfField = getCsrfHashField();
        if (csrfToken && csrfField) {
            headers[csrfField] = csrfToken;
        }

        fetch('<?= base_url('profile/auto-upload-ttd') ?>', {
            method: 'POST',
            headers: headers
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                updatePreview('ttd', data.tanda_tangan);
                showStatus(ttdStatus, 'success', data.message);
            } else {
                showStatus(ttdStatus, 'error', data.error || 'Gagal menggunakan tanda tangan default');
            }
        })
        .catch(error => {
            showStatus(ttdStatus, 'error', 'Terjadi kesalahan: ' + error.message);
        })
        .finally(function() {
            autoTtdBtn.disabled = false;
            autoTtdBtn.innerHTML = originalText;
        });
    });

    // Upload File Function
    function uploadFile(type, input, statusDiv, file) {
        if (!file) return;

        const formData = new FormData();
        formData.append(type, file);
        
        // Add CSRF token to FormData
        const csrfToken = getCsrfToken();
        const csrfField = getCsrfHashField();
        if (csrfToken && csrfField) {
            formData.append(csrfField, csrfToken);
        }

        const btn = type === 'foto' ? uploadFotoBtn : uploadTtdBtn;
        btn.disabled = true;
        const originalText = btn.innerHTML;
        btn.innerHTML = '<span class="loading-spinner"></span> Uploading...';

        fetch('<?= base_url('profile/upload-' . 'PLACEHOLDER') ?>'.replace('PLACEHOLDER', type), {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => {
            // Log response untuk debugging
            console.log('Response status:', response.status);
            if (!response.ok) {
                throw new Error('HTTP error, status = ' + response.status);
            }
            return response.text();
        })
        .then(text => {
            // Debug: log raw response
            console.log('Raw response:', text.substring(0, 200));
            if (!text) {
                throw new Error('Response kosong dari server');
            }
            return JSON.parse(text);
        })
        .then(data => {
            if (data.success) {
                const previewKey = type === 'foto' ? 'foto_profile' : 'tanda_tangan';
                updatePreview(type, data[previewKey]);
                showStatus(statusDiv, 'success', data.message);
                input.value = '';
                btn.disabled = true;
            } else {
                showStatus(statusDiv, 'error', data.error || 'Gagal upload file');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showStatus(statusDiv, 'error', 'Error: ' + error.message);
        })
        .finally(function() {
            btn.disabled = false;
            btn.innerHTML = originalText;
        });
    }

    // Update Preview Image
    function updatePreview(type, imageUrl) {
        const preview = type === 'foto' ? document.getElementById('fotoPreview') : document.getElementById('ttdPreview');
        
        if (preview.tagName === 'IMG') {
            preview.src = imageUrl;
        } else {
            // Replace placeholder with actual image
            const img = document.createElement('img');
            img.src = imageUrl;
            img.alt = type === 'foto' ? 'Foto Profile' : 'Tanda Tangan';
            img.className = 'preview-image';
            img.id = type === 'foto' ? 'fotoPreview' : 'ttdPreview';
            preview.parentNode.replaceChild(img, preview);
        }
    }

    // Show Status Message
    function showStatus(element, type, message) {
        element.className = 'upload-status ' + type;
        element.innerHTML = '<i class="fas fa-' + (type === 'success' ? 'check-circle' : 'exclamation-circle') + '"></i> ' + message;
        element.style.display = 'flex';

        if (type === 'success') {
            setTimeout(function() {
                element.style.display = 'none';
            }, 5000);
        }
    }
});
</script>

<?= $this->endSection() ?>
