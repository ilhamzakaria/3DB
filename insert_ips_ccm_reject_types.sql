-- ====================================
-- Update Reject Types untuk IPS & CCM
-- Safe: DELETE hanya specific types, INSERT new list
-- ====================================

-- STEP 1: Delete existing IPS reject types (keep Becum types)
DELETE FROM reject_types 
WHERE nama_reject IN (
    'Start up',
    'Bottom Putih',
    'Oval',
    'Flashing',
    'Short shoot',
    'Kotor',
    'Beda warna',
    'Sampling QC',
    'Kontaminasi',
    'Black spot',
    'Gosong',
    'Struktur tdk std',
    'Inject poin tidak standar',
    'Bolong',
    'Bubble',
    'Berair',
    'Neck panjang',
    'Support ring',
    'Bottom bergelombang',
    'Residu',
    'Feeling',
    'Longgate'
);

-- STEP 2: Delete existing CCM reject types
DELETE FROM reject_types 
WHERE nama_reject IN (
    'Karantina',
    'Sobek',
    'Camera'
);

-- STEP 3: Insert IPS reject types (23 types)
INSERT INTO reject_types (nama_reject) VALUES
('Start up'),
('Bottom Putih'),
('Oval'),
('Flashing'),
('Short shoot'),
('Kotor'),
('Beda warna'),
('Sampling QC'),
('Kontaminasi'),
('Black spot'),
('Gosong'),
('Struktur tdk std'),
('Inject poin tidak standar'),
('Bolong'),
('Bubble'),
('Berair'),
('Neck panjang'),
('Support ring'),
('Bottom bergelombang'),
('Residu'),
('Feeling'),
('Longgate');

-- STEP 4: Insert CCM reject types (11 types)
INSERT INTO reject_types (nama_reject) VALUES
('Start up'),
('Karantina'),
('Sobek'),
('Flashing'),
('Short shoot'),
('Kotor'),
('Beda warna'),
('Sampling QC'),
('Kontaminasi'),
('Black spot'),
('Camera');

-- ====================================
-- Verification Queries (after running above)
-- ====================================
-- SELECT COUNT(*) as total_reject_types FROM reject_types;
-- SELECT * FROM reject_types ORDER BY id;
-- SELECT DISTINCT COUNT(*) FROM reject_types;
