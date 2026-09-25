<style>
    :root {
        --primary-modern: #4361ee;
        --secondary-modern: #2ec4b6;
        --danger-modern: #ef476f;
        --warning-modern: #ff9f1c;
        --bg-modern: #f4f7fe;
        --text-main: #2b3674;
        --text-muted: #a3aed1;
        --card-shadow: 0 10px 20px rgba(0, 0, 0, 0.03), 0 2px 6px rgba(0, 0, 0, 0.04);
        --header-bg: #ffffff;
        --radius-lg: 20px;
        --radius-md: 14px;
        --transition: all 0.3s ease;
    }

    body {
        background-color: var(--bg-modern);
        font-family: 'Inter', 'Nunito', sans-serif;
        color: var(--text-main);
    }

    .page-content-wrapper {
        animation: fadeIn 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        opacity: 0;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .card {
        background: #ffffff;
        border: none;
        border-radius: var(--radius-lg);
        box-shadow: var(--card-shadow);
        transition: var(--transition);
        overflow: hidden;
    }

    .card-header {
        background-color: var(--header-bg);
        border-bottom: 1px solid #f0f2f5;
        border-radius: var(--radius-lg) var(--radius-lg) 0 0 !important;
        padding: 1.5rem 1.8rem;
    }

    .production-toolbar {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
        gap: 1rem 1.25rem;
    }

    .toolbar-group {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 0.75rem;
    }

    .toolbar-btn {
        min-height: 40px;
        padding: 0.55rem 1rem;
        border-radius: 12px !important;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.45rem;
        font-weight: 600;
        white-space: nowrap;
    }

    .toolbar-btn i {
        font-size: 0.85rem;
    }

    .toolbar-search-form {
        margin: 0;
    }

    .toolbar-search {
        width: 240px;
    }

    .toolbar-search .input-group {
        width: 100%;
        flex-wrap: nowrap;
    }

    .toolbar-search .form-control,
    .toolbar-search .btn {
        min-height: 40px;
    }

    .toolbar-search .form-control {
        border-radius: 12px 0 0 12px !important;
    }

    .toolbar-search .btn {
        width: 42px;
        padding: 0;
        border-radius: 0 12px 12px 0 !important;
        flex: 0 0 42px;
    }

    .toolbar-filter-menu {
        border-radius: 12px;
        overflow: hidden;
    }

    .plant-table-wrap {
        overflow-x: auto !important;
        width: 100%;
        border-radius: 0 0 var(--radius-lg) var(--radius-lg);
    }

    .table {
        margin-bottom: 0;
        border-collapse: separate;
        border-spacing: 0;
    }

    .table-main {
        min-width: 1000px;
    }

    .table thead th {
        background-color: #f8fafc;
        border: none !important;
        color: var(--text-muted);
        font-weight: 700;
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        padding: 1.2rem 1rem;
    }

    .table tbody tr {
        transition: background-color 0.2s;
    }

    .table tbody tr:hover {
        background-color: #fcfdfe;
    }

    .table td {
        padding: 1.2rem 1rem;
        vertical-align: middle;
        border-top: 1px solid #f0f2f5;
        color: #4a5568;
        font-size: 0.875rem;
    }

    .badge-modern {
        padding: 0.4em 0.8em;
        border-radius: 8px;
        font-weight: 600;
        font-size: 0.75rem;
    }

    .badge-shift-1 { background: #4361ee; color: #ffffff; }
    .badge-shift-2 { background: #ff9f1c; color: #ffffff; }
    .badge-shift-3 { background: #ef476f; color: #ffffff; }

    .date-divider {
        background-color: #f8fafc;
        font-weight: 700;
        color: var(--text-main);
        font-size: 0.8rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .shift-divider {
        background-color: #ffffff;
    }

    .shift-subtotal-row > td {
        background-color: #f0f7f1;
        border-top: 1px solid #c3e6cb !important;
        border-bottom: 2px solid #c3e6cb !important;
    }

    .shift-subtotal-row:hover > td {
        background-color: #e4f2e7 !important;
    }

    /* Mobile: subtotal row juga jadi block tapi sederhana */
    @media (max-width: 767.98px) {
        .table-main tbody tr.shift-subtotal-row {
            display: block;
            margin: 0 0.5rem 0.25rem;
            border-radius: 8px;
            background: #f0f7f1 !important;
            border: 1px solid #c3e6cb !important;
            box-shadow: none;
            padding: 0.4rem 0.75rem;
        }
        .shift-subtotal-row td {
            display: flex !important;
            justify-content: space-between;
            border: none !important;
            padding: 0.2rem 0 !important;
            font-size: 0.78rem;
        }
    }

    .btn-action {
        width: 32px;
        height: 32px;
        padding: 0;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
        transition: var(--transition);
        border: none;
    }

    .btn-edit { background: rgba(67, 97, 238, 0.1); color: var(--primary-modern); }
    .btn-edit:hover { background: var(--primary-modern); color: white; }
    .btn-detail { background: rgba(46, 196, 182, 0.1); color: var(--secondary-modern); }
    .btn-detail:hover { background: var(--secondary-modern); color: white; }
    .btn-delete { background: rgba(239, 71, 111, 0.1); color: var(--danger-modern); }
    .btn-delete:hover { background: var(--danger-modern); color: white; }

    /* Modal Styling */
    .modal-content {
        border-radius: var(--radius-lg);
        border: none;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
    }
    
    .modal-header {
        border-bottom: 1px solid #f0f2f5;
        padding: 1.5rem 2rem;
    }

    .form-label {
        font-weight: 600;
        color: var(--text-main);
        font-size: 0.8rem;
        margin-bottom: 0.5rem;
        text-transform: uppercase;
        letter-spacing: 0.025em;
    }

    .form-control, .form-select {
        border-radius: 10px;
        padding: 0.6rem 1rem;
        border: 1px solid #e2e8f0;
        font-size: 0.9rem;
    }

    .form-control:focus {
        border-color: var(--primary-modern);
        box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.1);
    }

    .section-title {
        font-size: 0.9rem;
        font-weight: 700;
        color: var(--primary-modern);
        margin-bottom: 1.2rem;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .section-title::after {
        content: '';
        flex: 1;
        height: 1px;
        background: #f0f2f5;
    }

    .signature-box {
        border: 1px dashed #e2e8f0;
        border-radius: 12px;
        padding: 1rem;
        text-align: center;
        transition: var(--transition);
        cursor: pointer;
    }

    .signature-box:hover {
        border-color: var(--primary-modern);
        background: rgba(67, 97, 238, 0.02);
    }

    .signature-preview {
        max-height: 50px;
        margin-bottom: 0.3rem;
    }

    .upload-text {
        font-size: 9px;
        color: var(--text-muted);
        display: block;
        margin-bottom: 2px;
    }

    /* Filter Drawer */
    .filter-drawer {
        border-left: none;
        box-shadow: -5px 0 25px rgba(0,0,0,0.05);
    }

    .filter-count {
        background: var(--danger-modern);
        color: white;
        border-radius: 50%;
        padding: 0 6px;
        font-size: 10px;
        min-width: 18px;
        height: 18px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        margin-left: 6px;
    }

    @media (max-width: 991.98px) {
        .page-content-wrapper {
            padding-left: 1rem !important;
            padding-right: 1rem !important;
        }

        .card-header {
            padding: 1.25rem;
        }

        .production-toolbar,
        .toolbar-group {
            align-items: stretch;
        }

        .toolbar-group {
            width: 100%;
        }

        .toolbar-group:last-child {
            justify-content: flex-start;
        }

        .toolbar-search {
            width: 100%;
            min-width: 0;
            flex: 1 1 220px;
        }
    }

    @media (max-width: 575.98px) {
        .toolbar-btn,
        .toolbar-search,
        .toolbar-group .dropdown,
        .toolbar-group form {
            width: 100%;
        }

        .toolbar-group .dropdown .btn,
        .toolbar-group > .btn {
            width: 100%;
        }
    }

    /* Minimal local fallback for Bootstrap 5 components when CDN JS/CSS is unavailable */
    .dropdown-menu.show {
        display: block;
    }

    .modal {
        position: fixed;
        top: 0;
        left: 0;
        z-index: 1055;
        display: none;
        width: 100%;
        height: 100%;
        overflow-x: hidden;
        overflow-y: auto;
        outline: 0;
    }

    .modal.show {
        display: block;
    }

    .modal-backdrop,
    .offcanvas-backdrop {
        position: fixed;
        top: 0;
        left: 0;
        z-index: 1050;
        width: 100vw;
        height: 100vh;
        background-color: #000;
    }

    .modal-backdrop.show,
    .offcanvas-backdrop.show {
        opacity: 0.5;
    }

    .offcanvas {
        position: fixed;
        bottom: 0;
        z-index: 1045;
        display: flex;
        flex-direction: column;
        max-width: 100%;
        visibility: hidden;
        background-color: #fff;
        background-clip: padding-box;
        outline: 0;
        transition: transform .3s ease-in-out;
    }

    .offcanvas-end {
        top: 0;
        right: 0;
        width: 400px;
        border-left: 1px solid rgba(0, 0, 0, .2);
        transform: translateX(100%);
    }

    .offcanvas.show {
        transform: none;
        visibility: visible;
    }

    .offcanvas-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 1rem;
    }

    .offcanvas-body {
        flex-grow: 1;
        padding: 1rem;
        overflow-y: auto;
    }

    .alert-dismissible {
        position: relative;
        padding-right: 3rem;
    }

    .btn-close {
        position: absolute;
        top: 25%;
        right: 1.25rem;
        transform: translateY(-50%);
        box-sizing: content-box;
        width: 0.8em;
        height: 0.8em;
        padding: .25em;
        color: #000;
        background: transparent url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16' fill='%23000'%3e%3cpath d='M.293.293a1 1 0 0 1 1.414 0L8 6.586 14.293.293a1 1 0 1 1 1.414 1.414L9.414 8l6.293 6.293a1 1 0 0 1-1.414 1.414L8 9.414l-6.293 6.293A1 1 0 0 1 .293 14.293L6.586 8 .293 1.707A1 1 0 0 1 .293.293z'/%3e%3c/svg%3e") center / 1em auto no-repeat;
        border: 0;
        border-radius: .25rem;
        opacity: .5;
        transition: var(--transition);
        cursor: pointer;
    }

    .btn-close:hover {
        opacity: 0.8;
    }

    /* SPK status badges — shown under the SPK number in the table */
    .badge-spk-selesai {
        display: inline-block;
        padding: 0.22em 0.6em;
        border-radius: 6px;
        font-size: 0.7rem;
        font-weight: 700;
        letter-spacing: 0.02em;
        background: rgba(40, 167, 69, 0.12);
        color: #1a7a32;
        border: 1px solid rgba(40, 167, 69, 0.25);
        white-space: nowrap;
    }

    .badge-spk-ongoing {
        display: inline-block;
        padding: 0.22em 0.6em;
        border-radius: 6px;
        font-size: 0.7rem;
        font-weight: 600;
        letter-spacing: 0.02em;
        background: rgba(255, 159, 28, 0.1);
        color: #9a5e00;
        border: 1px solid rgba(255, 159, 28, 0.25);
        white-space: nowrap;
    }

    /* Subtle green-tinted row background for completed SPKs */
    tr.spk-selesai-row > td {
        background-color: rgba(40, 167, 69, 0.035) !important;
    }
    tr.spk-selesai-row:hover > td {
        background-color: rgba(40, 167, 69, 0.065) !important;
    }

    /* ============================================================
       MOBILE STYLES — Tasks 1, 3, 4, 5, 6, 7
       Breakpoint: ≤ 767px (phones)
       Desktop tampilan tidak berubah sama sekali.
       ============================================================ */

    @media (max-width: 767.98px) {

        /* ----------------------------------------------------------
           TASK 7: Toolbar vertical stack
           ---------------------------------------------------------- */
        .production-toolbar {
            flex-direction: column;
            align-items: stretch;
            gap: 0.6rem;
        }

        .toolbar-group {
            width: 100%;
            flex-wrap: wrap;
            gap: 0.5rem;
        }

        .toolbar-btn {
            flex: 1 1 auto;
            justify-content: center;
            min-height: 44px;
        }

        .toolbar-search {
            width: 100%;
        }

        .toolbar-search-form {
            width: 100%;
        }

        .toolbar-search .input-group {
            width: 100%;
        }

        /* ----------------------------------------------------------
           TASK 7: Machine links — horizontal scroll (tidak wrap)
           ---------------------------------------------------------- */
        .machine-links-wrapper {
            overflow-x: auto;
            white-space: nowrap;
            -webkit-overflow-scrolling: touch;
            padding-bottom: 4px;
            /* hide scrollbar visually but keep functional */
            scrollbar-width: none;
        }

        .machine-links-wrapper::-webkit-scrollbar {
            display: none;
        }

        .machine-links-wrapper .machine-btn-group {
            flex-wrap: nowrap;
            display: inline-flex;
            width: max-content;
            margin-bottom: 0;
        }

        /* ----------------------------------------------------------
           TASK 6: Machine links via card-header flex override
           (fallback jika wrapper class tidak ada)
           ---------------------------------------------------------- */
        .card-header .machine-btn-group {
            flex-wrap: nowrap !important;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            padding-bottom: 4px;
            scrollbar-width: none;
        }

        .card-header .machine-btn-group::-webkit-scrollbar {
            display: none;
        }

        .card-header .machine-link-btn {
            flex-shrink: 0;
            white-space: nowrap;
            font-size: 0.78rem;
            padding: 0.4rem 0.7rem;
        }

        /* ----------------------------------------------------------
           TASK 2: Tabel → card list per baris
           ---------------------------------------------------------- */

        /* Reset min-width — kunci agar tabel tidak overflow di HP */
        .table-main {
            min-width: unset !important;
        }

        /* Sembunyikan header tabel */
        .table-main thead {
            display: none;
        }

        /* Container tidak perlu horizontal scroll di mobile */
        .plant-table-wrap {
            overflow-x: unset;
        }

        /* Setiap baris data jadi card */
        .table-main tbody tr:not(.date-divider):not(.shift-divider) {
            display: block;
            margin: 0.5rem 0.75rem;
            border-radius: 12px;
            background: #fff;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
            padding: 0.6rem 0.75rem;
            border: 1px solid #e8ecf4;
        }

        /* green-tinted card untuk SPK selesai */
        tr.spk-selesai-row:not(.date-divider):not(.shift-divider) {
            background-color: rgba(40, 167, 69, 0.04) !important;
            border-color: rgba(40, 167, 69, 0.2) !important;
        }

        /* Date & shift divider: reset ke table-cell agar tetap full-width */
        .table-main tbody tr.date-divider,
        .table-main tbody tr.shift-divider {
            display: table-row;
        }

        .table-main tbody tr.date-divider td,
        .table-main tbody tr.shift-divider td {
            display: table-cell;
            padding: 0.5rem 0.75rem;
        }

        /* Setiap cell jadi flex row: label kiri, nilai kanan */
        .table-main td {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            padding: 0.35rem 0;
            border: none !important;
            border-bottom: 1px solid #f5f7fa !important;
            font-size: 0.82rem;
            gap: 0.5rem;
        }

        .table-main td:last-child {
            border-bottom: none !important;
        }

        /* Label pseudo-element dari data-label attribute */
        .table-main td[data-label]::before {
            content: attr(data-label);
            font-weight: 700;
            color: var(--text-muted);
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            flex-shrink: 0;
            min-width: 90px;
            margin-right: 0.75rem;
            padding-top: 0.15rem;
        }

        /* Kolom aksi: tombol lebih besar untuk jari */
        .table-main td[data-label="Aksi"] {
            padding-top: 0.6rem;
            border-top: 1px solid #f0f2f5 !important;
            border-bottom: none !important;
            margin-top: 0.25rem;
        }

        .table-main td[data-label="Aksi"] .d-flex {
            flex: 1;
            justify-content: flex-end;
        }

        .btn-action {
            width: 38px;
            height: 38px;
            border-radius: 10px;
        }

        /* ----------------------------------------------------------
           TASK 4: Modal add/edit/detail — bottom sheet (position:fixed)
           ---------------------------------------------------------- */
        #addDataModal .modal-dialog,
        #modalEdit .modal-dialog,
        #modalDetail .modal-dialog {
            margin: 0;
            max-width: 100%;
            width: 100%;
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            /* override Bootstrap's transform-based centering */
            transform: none !important;
        }

        #addDataModal .modal-content,
        #modalEdit .modal-content,
        #modalDetail .modal-content {
            border-radius: 20px 20px 0 0 !important;
            max-height: 93vh;
            overflow-y: auto;
            -webkit-overflow-scrolling: touch;
        }

        /* Handle drag indicator */
        #addDataModal .modal-header::before,
        #modalEdit .modal-header::before,
        #modalDetail .modal-header::before {
            display: none;
        }

        /* Sticky header — grid 3 kolom: [kosong] [judul center] [✕] */
        #addDataModal .modal-header,
        #modalEdit .modal-header,
        #modalDetail .modal-header {
            display: grid;
            grid-template-columns: 40px 1fr 40px;
            align-items: center;
            padding: 1rem;
            border-bottom: 1px solid #f0f2f5;
            position: sticky;
            top: 0;
            z-index: 10;
            background: #fff;
            border-radius: 20px 20px 0 0;
        }

        /* Judul tepat di tengah */
        #addDataModal .modal-header .modal-title,
        #modalEdit   .modal-header .modal-title,
        #modalDetail .modal-header .modal-title {
            grid-column: 2;
            text-align: center;
            font-size: 0.95rem;
            font-weight: 700;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            padding: 0;
            margin: 0;
        }

        /* Tombol close di kolom kanan */
        #addDataModal .modal-header .btn-close,
        #modalEdit   .modal-header .btn-close,
        #modalDetail .modal-header .btn-close {
            grid-column: 3;
            justify-self: end;
            position: static !important;
            transform: none !important;
            margin: 0;
            padding: 0.4rem;
        }

        #addDataModal .modal-body,
        #modalEdit .modal-body,
        #modalDetail .modal-body {
            padding: 1rem 1.25rem;
        }

        /* Sticky footer — tombol Simpan selalu kelihatan */
        #addDataModal .modal-footer,
        #modalEdit .modal-footer {
            position: sticky;
            bottom: 0;
            z-index: 10;
            background: #fff;
            border-top: 1px solid #f0f2f5;
            padding: 0.75rem 1rem;
            display: flex;
            gap: 0.5rem;
        }

        #addDataModal .modal-footer .btn,
        #modalEdit .modal-footer .btn {
            flex: 1;
            min-height: 46px;
            font-size: 0.95rem;
            border-radius: 12px;
        }

        #modalDetail .modal-footer {
            position: sticky;
            bottom: 0;
            z-index: 10;
            background: #fff;
            padding: 0.75rem 1.25rem;
        }

        #modalDetail .modal-footer .btn {
            width: 100%;
            min-height: 46px;
            font-size: 0.95rem;
            border-radius: 12px;
        }

        /* ----------------------------------------------------------
           TASK 5: Form fields — 1 kolom penuh, touch-friendly
           ---------------------------------------------------------- */

        /* Semua col-* di modal body jadi 100% */
        #addDataModal .modal-body .col-lg-3,
        #addDataModal .modal-body .col-lg-4,
        #addDataModal .modal-body .col-lg-6,
        #addDataModal .modal-body .col-md-3,
        #addDataModal .modal-body .col-md-4,
        #addDataModal .modal-body .col-md-5,
        #addDataModal .modal-body .col-md-6,
        #modalEdit .modal-body .col-lg-3,
        #modalEdit .modal-body .col-lg-4,
        #modalEdit .modal-body .col-lg-6,
        #modalEdit .modal-body .col-md-3,
        #modalEdit .modal-body .col-md-4,
        #modalEdit .modal-body .col-md-5,
        #modalEdit .modal-body .col-md-6 {
            flex: 0 0 100%;
            max-width: 100%;
        }

        /* Input lebih tinggi — mudah disentuh jari */
        #addDataModal .modal-body .form-control,
        #addDataModal .modal-body .form-select,
        #modalEdit .modal-body .form-control,
        #modalEdit .modal-body .form-select {
            min-height: 46px !important;
            font-size: 0.95rem !important;
            padding: 0.65rem 1rem;
        }

        /* Select2 juga ikut */
        #addDataModal .modal-body .select2-selection,
        #modalEdit .modal-body .select2-selection {
            min-height: 46px !important;
            font-size: 0.95rem !important;
        }

        /* Label */
        #addDataModal .modal-body .form-label,
        #modalEdit .modal-body .form-label {
            font-size: 0.78rem;
            margin-bottom: 0.3rem;
        }

        /* Section title lebih kompak */
        #addDataModal .modal-body .section-title,
        #modalEdit .modal-body .section-title {
            font-size: 0.82rem;
            margin-top: 1.25rem;
            margin-bottom: 0.75rem;
        }

        /* row gutter dikurangi */
        #addDataModal .modal-body .row.g-3,
        #addDataModal .modal-body .row.g-4,
        #modalEdit .modal-body .row.g-3,
        #modalEdit .modal-body .row.g-4 {
            --bs-gutter-x: 0.75rem;
            --bs-gutter-y: 0.5rem;
        }

        /* Modal detail: kolom jadi 1 kolom */
        #modalDetail .col-md-6 {
            flex: 0 0 100%;
            max-width: 100%;
        }

        /* Tabel detail lebih kompak */
        #modalDetail .table td,
        #modalDetail .table th {
            font-size: 0.8rem;
            padding: 0.4rem 0.5rem;
        }

    } /* end @media max-width: 767.98px */

    /* ── DOWNTIME CARD LIST ─────────────────────────────────────
       Berlaku di semua ukuran layar — konsisten desktop & mobile
       ─────────────────────────────────────────────────────────── */

    .dt-list-container {
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
        margin-bottom: 1.5rem;
    }

    .dt-card {
        border: 1px solid #e8ecf4;
        border-radius: 12px;
        background: #fff;
        overflow: hidden;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.04);
    }

    .dt-card-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0.6rem 1rem;
        background: #fef3f2;
        border-bottom: 1px solid #fde8e6;
    }

    .dt-card-title {
        font-size: 0.82rem;
        font-weight: 700;
        color: #c0392b;
    }

    .dt-remove-btn {
        padding: 0.2rem 0.5rem;
        font-size: 0.75rem;
        border-radius: 6px;
    }

    .dt-card-body {
        padding: 0.85rem 1rem;
        display: flex;
        flex-direction: column;
        gap: 0.65rem;
    }

    .dt-field {
        display: flex;
        flex-direction: column;
        gap: 0.25rem;
    }

    .dt-label {
        font-size: 0.72rem;
        font-weight: 700;
        color: #a3aed1;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        margin: 0;
    }

    /* Mulai & Selesai: 2 kolom berdampingan */
    .dt-field-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 0.65rem;
    }

    @media (max-width: 767.98px) {
        .dt-field-row {
            grid-template-columns: 1fr;
        }
    }

    /* Input dalam card sedikit lebih besar dari form-control-sm default */
    .dt-card .form-control,
    .dt-card .form-select {
        font-size: 0.875rem;
        min-height: 36px;
    }

    /* Empty state saat belum ada downtime */
    .dt-empty-state {
        text-align: center;
        padding: 1.5rem;
        color: #a3aed1;
        font-size: 0.82rem;
        border: 1px dashed #e2e8f0;
        border-radius: 12px;
        margin-bottom: 0;
    }

    /* Becum/Powerjet: reject diinput per jam, card Detail Jenis Reject disembunyikan */
    .jam-reject-hidden {
        display: none !important;
    }

    /* ── JAM REJECT INLINE ────────────────────────────── */

    /* Box sizing untuk kolom tabel */
    .table_jam_form col,
    .table_jam_form th,
    .table_jam_form td { 
        box-sizing: border-box; 
    }

    /* Kolom JAM (1st) */
    .table_jam_form th:nth-child(1),
    .table_jam_form td:nth-child(1) { 
        width: 90px !important; 
        min-width: 90px; 
        flex-shrink: 0;
    }

    /* Kolom HASIL (2nd) — lebih lebar, fleksibel */
    .table_jam_form th:nth-child(2),
    .table_jam_form td:nth-child(2) { 
        width: 190px !important; 
        min-width: 160px; 
        flex-grow: 0;
    }

    /* Kolom REJECT (3rd) — hanya untuk Becum/Powerjet, sisa ruang */
    .table_jam_form th:nth-child(3):not(:last-child),
    .table_jam_form td:nth-child(3):not(:last-child) { 
        width: auto !important; 
        min-width: 330px; 
        flex-grow: 1;
    }

    /* Kolom AKSI — last child (bisa 3rd atau 4th tergantung mesin) */
    .table_jam_form th:last-child,
    .table_jam_form td:last-child { 
        width: 50px !important; 
        min-width: 50px; 
        flex-shrink: 0;
    }

    /* Input HASIL lebih lebar */
    .table_jam_form .jam-hasil {
        width: 100% !important;
        min-width: unset;
        font-size: 0.875rem !important;
    }

    /* Dropdown reject — compact width */
    .jam-reject-type {
        max-width: 180px !important;
        min-width: 100px !important;
        flex: 1 1 0 !important;
    }

    /* Input jumlah reject — fixed width */
    .jam-reject-jumlah {
        width: 100px !important;
        min-width: 90px;
        flex-shrink: 0;
        font-size: 0.875rem !important;
        text-align: center;
    }

    /* Tombol remove reject — small icon button */
    .btn-remove-jam-reject {
        width: 24px !important;
        height: 24px !important;
        padding: 0 !important;
        font-size: 0.65rem !important;
        flex-shrink: 0;
        display: flex !important;
        align-items: center;
        justify-content: center;
    }

    /* Tombol add reject — link style */
    .btn-add-jam-reject {
        font-size: 0.68rem !important;
        color: #6c757d !important;
        text-decoration: none;
        display: block;
        margin-top: 2px;
    }

    .btn-add-jam-reject:hover { 
        color: #4361ee !important; 
    }

    /* Cell reject — padding lebih kecil */
    .jam-reject-cell {
        padding: 0.35rem 0.5rem !important;
        vertical-align: middle;
    }

    /* Setiap baris reject (dropdown + input + hapus) */
    .jam-reject-list {
        display: flex;
        flex-direction: column;
        gap: 0;
    }

    .jam-reject-row {
        display: flex;
        align-items: center;
        gap: 4px;
        margin-bottom: 3px;
        flex-wrap: nowrap;
    }

    /* Dropdown jenis reject — compact */
    .jam-reject-type {
        font-size: 0.72rem !important;
        padding: 0.2rem 0.35rem !important;
        min-width: 0 !important;
        flex: 1 1 0;
        height: 28px;
    }

    /* Input jumlah reject */
    .jam-reject-jumlah {
        width: 52px !important;
        flex-shrink: 0;
        font-size: 0.72rem !important;
        padding: 0.2rem 0.3rem !important;
        height: 28px;
        text-align: center;
    }

    /* Tombol hapus sub-baris reject */
    .btn-remove-jam-reject {
        flex-shrink: 0;
        width: 22px !important;
        height: 22px !important;
        padding: 0 !important;
        font-size: 0.65rem !important;
        line-height: 1 !important;
        display: flex !important;
        align-items: center;
        justify-content: center;
        border-radius: 4px !important;
    }

    /* Tombol + Reject */
    .btn-add-jam-reject {
        font-size: 0.68rem !important;
        padding: 0 !important;
        color: #a3aed1 !important;
        display: block;
        margin-top: 2px;
        text-decoration: none;
    }

    .btn-add-jam-reject:hover { color: #4361ee !important; }

    /* Mobile: kolom reject stack ke bawah */
    @media (max-width: 767.98px) {
        .jam-reject-type   { font-size: 0.7rem !important; }
        .jam-reject-jumlah { width: 46px !important; }
    }

    /* ── REMOVE NUMBER STEPPER (SPINNER) ────────────────────────
       Hilangkan up/down arrow buttons di semua input type="number"
       ────────────────────────────────────────────────────────── */

    /* Chrome, Safari, Edge, Opera */
    input[type="number"]::-webkit-outer-spin-button,
    input[type="number"]::-webkit-inner-spin-button {
        -webkit-appearance: none !important;
        margin: 0 !important;
    }

    /* Firefox */
    input[type="number"] {
        -moz-appearance: textfield !important;
    }

    /* Input number lebih clean tanpa spinner */
    input[type="number"] {
        /* pastikan focus border terlihat */
        transition: border-color 0.2s, box-shadow 0.2s;
    }

    input[type="number"]:focus {
        border-color: var(--primary-modern, #4361ee) !important;
        box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.1) !important;
    }
</style>