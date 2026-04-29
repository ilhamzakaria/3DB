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
        top: 50%;
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
</style>
