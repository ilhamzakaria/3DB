// ============ PEMASOK TAB FUNCTIONS ============

let currentPemasokPage = 1;
let canEdit = <?= isset($canEdit) && $canEdit ? 'true' : 'false' ?>;

function initPemasokTab() {
    loadPemasokData(1);
    setupPemasokEventListeners();
}

function loadPemasokData(page = 1) {
    currentPemasokPage = page;
    
    const filters = {
        page: page,
        per_page: document.getElementById('perPageSelectPemasok').value || 10,
        tanggal_dari: document.getElementById('pemasokFilterTanggalDari').value || '',
        tanggal_sampai: document.getElementById('pemasokFilterTanggalSampai').value || '',
        status: document.getElementById('pemasokFilterStatus').value || '',
        nama_pemasok: document.getElementById('pemasokFilterNamaPemasok').value || '',
        search: document.getElementById('pemasokSearchQuery').value || ''
    };

    const url = new URL('<?= base_url('warehouse2/api/barang-masuk-pemasok') ?>');
    Object.keys(filters).forEach(key => {
        if (filters[key]) url.searchParams.append(key, filters[key]);
    });

    fetch(url)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                renderPemasokTable(data.data);
                updatePemasokPagination(data.pager || { current_page: 1, total_pages: 1, has_prev: false, has_next: false });
            }
        })
        .catch(error => console.error('Error loading pemasok data:', error));
}

function renderPemasokTable(data) {
    const tbody = document.getElementById('tbodyPemasok');
    
    if (!data || data.length === 0) {
        tbody.innerHTML = `<tr><td colspan="9" class="empty-state"><i class="fas fa-inbox"></i><p>Belum ada data</p></td></tr>`;
        return;
    }

    let html = '';
    data.forEach(item => {
        const statusColor = item.status === 'diterima' ? 'success' : (item.status === 'pending' ? 'warning' : 'danger');
        const actionButtons = canEdit ? 
            `<button class="btn btn-outline-danger btn-sm" onclick="deletePemasokRecord(${item.id})"><i class="fas fa-trash"></i></button>` :
            '';
        html += `<tr>
            <td>${new Date(item.tanggal).toLocaleDateString('id-ID')}</td>
            <td>${item.no_dokumen}</td>
            <td>${item.nama_pemasok}</td>
            <td>${item.nama_barang}</td>
            <td>${item.jumlah}</td>
            <td>${item.harga_satuan || 0}</td>
            <td>${item.total_harga || 0}</td>
            <td><span class="badge bg-${statusColor}">${item.status}</span></td>
            <td>
                <div class="btn-group btn-group-sm">
                    ${actionButtons}
                </div>
            </td>
        </tr>`;
    });
    
    tbody.innerHTML = html;
}

function updatePemasokPagination(pager) {
    document.getElementById('currentPageNumPemasok').textContent = pager.current_page || 1;
    document.getElementById('totalPagesPemasok').textContent = pager.total_pages || 1;

    const prevBtn = document.getElementById('prevBtnPemasok');
    if (pager.has_prev) {
        prevBtn.classList.remove('disabled');
        prevBtn.querySelector('a').onclick = (e) => {
            e.preventDefault();
            loadPemasokData(pager.current_page - 1);
        };
    } else {
        prevBtn.classList.add('disabled');
    }

    const nextBtn = document.getElementById('nextBtnPemasok');
    if (pager.has_next) {
        nextBtn.classList.remove('disabled');
        nextBtn.querySelector('a').onclick = (e) => {
            e.preventDefault();
            loadPemasokData(pager.current_page + 1);
        };
    } else {
        nextBtn.classList.add('disabled');
    }
}

function applyPemasokCustomFilter() {
    loadPemasokData(1);
    updatePemasokFilterCount();
    const offcanvas = document.getElementById('pemasokCustomFilterDrawer');
    const offcanvasInstance = bootstrap.Offcanvas.getInstance(offcanvas);
    if (offcanvasInstance) offcanvasInstance.hide();
}

function resetPemasokCustomFilter() {
    document.getElementById('pemasokFilterTanggalDari').value = '';
    document.getElementById('pemasokFilterTanggalSampai').value = '';
    document.getElementById('pemasokFilterStatus').value = '';
    document.getElementById('pemasokFilterNamaPemasok').value = '';
    document.getElementById('pemasokSearchQuery').value = '';
    updatePemasokFilterCount();
    loadPemasokData(1);
}

function updatePemasokFilterCount() {
    let count = 0;
    if (document.getElementById('pemasokFilterTanggalDari').value) count++;
    if (document.getElementById('pemasokFilterTanggalSampai').value) count++;
    if (document.getElementById('pemasokFilterStatus').value) count++;
    if (document.getElementById('pemasokFilterNamaPemasok').value) count++;

    const badge = document.getElementById('pemasokFilterCount');
    if (count > 0) {
        badge.textContent = count;
        badge.style.display = 'inline-block';
    } else {
        badge.style.display = 'none';
    }
}

function deletePemasokRecord(id) {
    if (!confirm('Hapus data ini?')) return;
    
    fetch(`<?= base_url('warehouse2/delete-pemasok') ?>/${id}`, {
        method: 'DELETE',
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(r => r.json())
    .then(data => {
        if (data.status === 'success' || data.success) {
            alert('Data berhasil dihapus');
            loadPemasokData(currentPemasokPage);
        } else {
            alert('Gagal menghapus data');
        }
    });
}

function setupPemasokEventListeners() {
    const perPageSelect = document.getElementById('perPageSelectPemasok');
    if (perPageSelect) {
        perPageSelect.addEventListener('change', () => loadPemasokData(1));
    }

    const searchForm = document.getElementById('pemasokSearchForm');
    if (searchForm) {
        searchForm.addEventListener('submit', (e) => {
            e.preventDefault();
            loadPemasokData(1);
        });
    }
}

// ============ PRODUKSI TAB FUNCTIONS ============

let currentProduksiPage = 1;

function initProduksiTab() {
    loadProduksiData(1);
    setupProduksiEventListeners();
}

function loadProduksiData(page = 1) {
    currentProduksiPage = page;
    
    const filters = {
        page: page,
        per_page: document.getElementById('perPageSelectProduksi').value || 10,
        tanggal_dari: document.getElementById('produksiFilterTanggalDari').value || '',
        tanggal_sampai: document.getElementById('produksiFilterTanggalSampai').value || '',
        status: document.getElementById('produksiFilterStatus').value || '',
        no_spk: document.getElementById('produksiFilterNoSpk').value || '',
        search: document.getElementById('produksiSearchQuery').value || ''
    };

    const url = new URL('<?= base_url('warehouse2/api/barang-masuk-produksi') ?>');
    Object.keys(filters).forEach(key => {
        if (filters[key]) url.searchParams.append(key, filters[key]);
    });

    fetch(url)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                renderProduksiTable(data.data);
                updateProduksiPagination(data.pager || { current_page: 1, total_pages: 1, has_prev: false, has_next: false });
            }
        })
        .catch(error => console.error('Error loading produksi data:', error));
}

function renderProduksiTable(data) {
    const tbody = document.getElementById('tbodyProduksi');
    
    if (!data || data.length === 0) {
        tbody.innerHTML = `<tr><td colspan="8" class="empty-state"><i class="fas fa-inbox"></i><p>Belum ada data</p></td></tr>`;
        return;
    }

    let html = '';
    data.forEach(item => {
        const statusColor = item.status === 'terima' ? 'success' : (item.status === 'pending' ? 'warning' : 'danger');
        const actionButtons = canEdit ? 
            `<button class="btn btn-outline-danger btn-sm" onclick="deleteProduksiRecord(${item.id})"><i class="fas fa-trash"></i></button>` :
            '';
        html += `<tr>
            <td>${new Date(item.tanggal).toLocaleDateString('id-ID')}</td>
            <td>${item.nomor_spk || '-'}</td>
            <td>${item.no_spk || '-'}</td>
            <td>${item.nama_barang}</td>
            <td>${item.jumlah_keluar || 0}</td>
            <td>${item.jumlah_terima || '-'}</td>
            <td><span class="badge bg-${statusColor}">${item.status}</span></td>
            <td>
                <div class="btn-group btn-group-sm">
                    ${actionButtons}
                </div>
            </td>
        </tr>`;
    });
    
    tbody.innerHTML = html;
}

function updateProduksiPagination(pager) {
    document.getElementById('currentPageNumProduksi').textContent = pager.current_page || 1;
    document.getElementById('totalPagesProduksi').textContent = pager.total_pages || 1;

    const prevBtn = document.getElementById('prevBtnProduksi');
    if (pager.has_prev) {
        prevBtn.classList.remove('disabled');
        prevBtn.querySelector('a').onclick = (e) => {
            e.preventDefault();
            loadProduksiData(pager.current_page - 1);
        };
    } else {
        prevBtn.classList.add('disabled');
    }

    const nextBtn = document.getElementById('nextBtnProduksi');
    if (pager.has_next) {
        nextBtn.classList.remove('disabled');
        nextBtn.querySelector('a').onclick = (e) => {
            e.preventDefault();
            loadProduksiData(pager.current_page + 1);
        };
    } else {
        nextBtn.classList.add('disabled');
    }
}

function applyProduksiCustomFilter() {
    loadProduksiData(1);
    updateProduksiFilterCount();
    const offcanvas = document.getElementById('produksiCustomFilterDrawer');
    const offcanvasInstance = bootstrap.Offcanvas.getInstance(offcanvas);
    if (offcanvasInstance) offcanvasInstance.hide();
}

function resetProduksiCustomFilter() {
    document.getElementById('produksiFilterTanggalDari').value = '';
    document.getElementById('produksiFilterTanggalSampai').value = '';
    document.getElementById('produksiFilterStatus').value = '';
    document.getElementById('produksiFilterNoSpk').value = '';
    document.getElementById('produksiSearchQuery').value = '';
    updateProduksiFilterCount();
    loadProduksiData(1);
}

function updateProduksiFilterCount() {
    let count = 0;
    if (document.getElementById('produksiFilterTanggalDari').value) count++;
    if (document.getElementById('produksiFilterTanggalSampai').value) count++;
    if (document.getElementById('produksiFilterStatus').value) count++;
    if (document.getElementById('produksiFilterNoSpk').value) count++;

    const badge = document.getElementById('produksiFilterCount');
    if (count > 0) {
        badge.textContent = count;
        badge.style.display = 'inline-block';
    } else {
        badge.style.display = 'none';
    }
}

function deleteProduksiRecord(id) {
    if (!confirm('Hapus data ini?')) return;
    
    fetch(`<?= base_url('warehouse2/delete-produksi') ?>/${id}`, {
        method: 'DELETE',
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(r => r.json())
    .then(data => {
        if (data.status === 'success' || data.success) {
            alert('Data berhasil dihapus');
            loadProduksiData(currentProduksiPage);
        } else {
            alert('Gagal menghapus data');
        }
    });
}

function setupProduksiEventListeners() {
    const perPageSelect = document.getElementById('perPageSelectProduksi');
    if (perPageSelect) {
        perPageSelect.addEventListener('change', () => loadProduksiData(1));
    }

    const searchForm = document.getElementById('produksiSearchForm');
    if (searchForm) {
        searchForm.addEventListener('submit', (e) => {
            e.preventDefault();
            loadProduksiData(1);
        });
    }
}

// ============ PEMBELI TAB FUNCTIONS ============

let currentPembeliPage = 1;

function initPembeliTab() {
    loadPembeliData(1);
    setupPembeliEventListeners();
}

function loadPembeliData(page = 1) {
    currentPembeliPage = page;
    
    const filters = {
        page: page,
        per_page: document.getElementById('perPageSelectPembeli').value || 10,
        tanggal_dari: document.getElementById('pembeliFilterTanggalDari').value || '',
        tanggal_sampai: document.getElementById('pembeliFilterTanggalSampai').value || '',
        status: document.getElementById('pembeliFilterStatus').value || '',
        nama_pembeli: document.getElementById('pembeliFilterNamaPembeli').value || '',
        search: document.getElementById('pembeliSearchQuery').value || ''
    };

    const url = new URL('<?= base_url('warehouse2/api/barang-keluar-pembeli') ?>');
    Object.keys(filters).forEach(key => {
        if (filters[key]) url.searchParams.append(key, filters[key]);
    });

    fetch(url)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                renderPembeliTable(data.data);
                updatePembeliPagination(data.pager || { current_page: 1, total_pages: 1, has_prev: false, has_next: false });
            }
        })
        .catch(error => console.error('Error loading pembeli data:', error));
}

function renderPembeliTable(data) {
    const tbody = document.getElementById('tbodyPembeli');
    
    if (!data || data.length === 0) {
        tbody.innerHTML = `<tr><td colspan="8" class="empty-state"><i class="fas fa-inbox"></i><p>Belum ada data</p></td></tr>`;
        return;
    }

    let html = '';
    data.forEach(item => {
        const statusColor = item.status === 'dikirim' ? 'success' : (item.status === 'pending' ? 'warning' : 'danger');
        const actionButtons = canEdit ? 
            `<button class="btn btn-outline-primary btn-sm me-1" onclick="editPembeli(${item.id})" title="Edit"><i class="fas fa-edit"></i></button>
             <button class="btn btn-outline-danger btn-sm" onclick="deletePembeliRecord(${item.id})" title="Hapus"><i class="fas fa-trash"></i></button>` :
            '';
        html += `<tr>
            <td>${new Date(item.tanggal).toLocaleDateString('id-ID')}</td>
            <td><strong>${item.nomor_spk || item.no_dokumen || '-'}</strong></td>
            <td>${item.nama_pembeli}</td>
            <td>${item.nama_barang}</td>
            <td>${item.jumlah} ${item.satuan || ''}</td>
            <td>Rp ${parseFloat(item.total_harga || 0).toLocaleString('id-ID')}</td>
            <td><span class="badge bg-${statusColor}">${item.status}</span></td>
            <td>
                <div class="btn-group btn-group-sm">
                    ${actionButtons}
                </div>
            </td>
        </tr>`;
    });
    
    tbody.innerHTML = html;
}

function updatePembeliPagination(pager) {
    document.getElementById('currentPageNumPembeli').textContent = pager.current_page || 1;
    document.getElementById('totalPagesPembeli').textContent = pager.total_pages || 1;

    const prevBtn = document.getElementById('prevBtnPembeli');
    if (pager.has_prev) {
        prevBtn.classList.remove('disabled');
        prevBtn.querySelector('a').onclick = (e) => {
            e.preventDefault();
            loadPembeliData(pager.current_page - 1);
        };
    } else {
        prevBtn.classList.add('disabled');
    }

    const nextBtn = document.getElementById('nextBtnPembeli');
    if (pager.has_next) {
        nextBtn.classList.remove('disabled');
        nextBtn.querySelector('a').onclick = (e) => {
            e.preventDefault();
            loadPembeliData(pager.current_page + 1);
        };
    } else {
        nextBtn.classList.add('disabled');
    }
}

function applyPembeliCustomFilter() {
    loadPembeliData(1);
    updatePembeliFilterCount();
    const offcanvas = document.getElementById('pembeliCustomFilterDrawer');
    const offcanvasInstance = bootstrap.Offcanvas.getInstance(offcanvas);
    if (offcanvasInstance) offcanvasInstance.hide();
}

function resetPembeliCustomFilter() {
    document.getElementById('pembeliFilterTanggalDari').value = '';
    document.getElementById('pembeliFilterTanggalSampai').value = '';
    document.getElementById('pembeliFilterStatus').value = '';
    document.getElementById('pembeliFilterNamaPembeli').value = '';
    document.getElementById('pembeliSearchQuery').value = '';
    updatePembeliFilterCount();
    loadPembeliData(1);
}

function updatePembeliFilterCount() {
    let count = 0;
    if (document.getElementById('pembeliFilterTanggalDari').value) count++;
    if (document.getElementById('pembeliFilterTanggalSampai').value) count++;
    if (document.getElementById('pembeliFilterStatus').value) count++;
    if (document.getElementById('pembeliFilterNamaPembeli').value) count++;

    const badge = document.getElementById('pembeliFilterCount');
    if (count > 0) {
        badge.textContent = count;
        badge.style.display = 'inline-block';
    } else {
        badge.style.display = 'none';
    }
}

function deletePembeliRecord(id) {
    if (!confirm('Hapus data ini?')) return;
    
    fetch(`<?= base_url('warehouse2/delete-pembeli') ?>/${id}`, {
        method: 'DELETE',
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(r => r.json())
    .then(data => {
        if (data.status === 'success' || data.success) {
            alert('Data berhasil dihapus');
            loadPembeliData(currentPembeliPage);
        } else {
            alert('Gagal menghapus data');
        }
    });
}

function setupPembeliEventListeners() {
    const perPageSelect = document.getElementById('perPageSelectPembeli');
    if (perPageSelect) {
        perPageSelect.addEventListener('change', () => loadPembeliData(1));
    }

    const searchForm = document.getElementById('pembeliSearchForm');
    if (searchForm) {
        searchForm.addEventListener('submit', (e) => {
            e.preventDefault();
            loadPembeliData(1);
        });
    }
}
