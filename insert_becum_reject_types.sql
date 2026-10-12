-- Insert Becum Reject Types (SAFE - tidak menghapus IPS)
-- Copy dan paste ke phpMyAdmin atau MySQL client
-- Hanya tambah/update reject Becum, reject IPS tetap ada

-- Hapus hanya reject Becum yang lama (jika ada)
DELETE FROM reject_types WHERE nama_reject IN (
    'Start up', 'Blackspot', 'Mulut oval', 'Leher bengkok', 'Emboss penyok',
    'Mata ikan', 'Material', 'Garis kasar', 'Berair', 'Bergejolom bang',
    'Buram', 'Beda warna', 'Kontaminasi', 'Drop test', 'Bintik',
    'Berat kurang', 'Tidak sempurna', 'Lain-lain'
);

-- Insert reject Becum yang baru (sesuai referensi)
INSERT INTO reject_types (nama_reject, deskripsi, is_active, created_at, updated_at) VALUES
('Start up', 'Reject pada saat mesin baru start (warm up)', 1, NOW(), NOW()),
('Blackspot', 'Bintik hitam atau noda hitam pada produk', 1, NOW(), NOW()),
('Mulut oval', 'Bentuk mulut galon tidak bulat sempurna (oval/lonjong)', 1, NOW(), NOW()),
('Leher bengkok', 'Leher galon tidak lurus atau bengkok', 1, NOW(), NOW()),
('Emboss penyok', 'Emboss/logo penyok atau tidak jelas', 1, NOW(), NOW()),
('Mata ikan', 'Gelembung udara atau kontaminasi berbentuk mata ikan', 1, NOW(), NOW()),
('Material', 'Masalah material bahan baku (warna, kualitas)', 1, NOW(), NOW()),
('Garis kasar', 'Garis-garis kasar pada permukaan produk', 1, NOW(), NOW()),
('Berair', 'Permukaan produk berair atau basah', 1, NOW(), NOW()),
('Berlubang', 'Produk berlubang atau bocor', 1, NOW(), NOW()),
('Bergelombang', 'Permukaan produk bergelombang atau tidak rata', 1, NOW(), NOW()),
('Buram', 'Produk tidak transparan/jernih (buram/keruh)', 1, NOW(), NOW()),
('Beda warna', 'Warna produk tidak sesuai standar atau belang', 1, NOW(), NOW()),
('Kontam nasi', 'Kontaminasi material menyerupai butiran nasi', 1, NOW(), NOW()),
('Drop test', 'Produk tidak lolos uji jatuh (drop test)', 1, NOW(), NOW()),
('Bintik', 'Bintik-bintik kecil pada permukaan produk', 1, NOW(), NOW()),
('Berat kurang', 'Berat produk kurang dari standar minimum', 1, NOW(), NOW()),
('Tidak sempurna', 'Produk cacat bentuk atau tidak sempurna', 1, NOW(), NOW()),
('Lain-lain', 'Jenis reject lainnya yang tidak termasuk kategori di atas', 1, NOW(), NOW());
