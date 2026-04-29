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

    /* Page Transition Animation */
    .page-content-wrapper {
        animation: fadeIn 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        opacity: 0;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(20px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* Modern Card */
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

    .card-body {
        padding: 1.5rem 1.8rem;
    }

    /* Table Styles */
    .ppic-table-wrap {
        overflow-x: auto !important;
        -webkit-overflow-scrolling: touch;
        width: 100%;
        border-radius: 0 0 var(--radius-lg) var(--radius-lg);
    }

    #dataTable {
        margin-bottom: 0;
        min-width: 1200px;
        border-collapse: separate;
        border-spacing: 0;
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
        position: sticky;
        top: 0;
        z-index: 10;
    }

    .table thead th:first-child {
        border-top-left-radius: var(--radius-md);
    }

    .table thead th:last-child {
        border-top-right-radius: var(--radius-md);
    }

    .table tbody tr {
        transition: background-color 0.2s;
    }

    .table tbody tr:hover {
        background-color: rgba(67, 97, 238, 0.02);
    }

    .table tbody td {
        padding: 1rem;
        vertical-align: middle;
        border-bottom: 1px solid #f0f2f5;
        border-top: none;
        font-size: 0.9rem;
        color: #475569;
    }

    /* Row Group Headers */
    .table-secondary td {
        background-color: #f8fafc !important;
        border-bottom: 2px solid #e2e8f0;
    }

    .date-header {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        background: #fff;
        padding: 0.4rem 1rem;
        border-radius: 99px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.02);
        color: var(--text-main);
    }

    .table-primary td {
        background-color: rgba(67, 97, 238, 0.04) !important;
        border-bottom: 1px solid rgba(67, 97, 238, 0.1);
        color: var(--primary-modern);
    }

    /* Buttons & Inputs */
    .btn {
        border-radius: 10px;
        font-weight: 600;
        padding: 0.5rem 1.2rem;
        transition: var(--transition);
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }

    .btn-sm {
        padding: 0.4rem 0.8rem;
        font-size: 0.85rem;
        border-radius: 8px;
    }

    .btn-primary {
        background-color: var(--primary-modern);
        border-color: var(--primary-modern);
        box-shadow: 0 4px 10px rgba(67, 97, 238, 0.2);
    }

    .btn-primary:hover {
        background-color: #3651d4;
        border-color: #3651d4;
        transform: translateY(-1px);
        box-shadow: 0 6px 15px rgba(67, 97, 238, 0.3);
    }

    .btn-outline-success {
        color: var(--secondary-modern);
        border-color: var(--secondary-modern);
    }

    .btn-outline-success:hover {
        background-color: var(--secondary-modern);
        border-color: var(--secondary-modern);
        color: white;
    }

    .btn-action {
        width: 32px;
        height: 32px;
        padding: 0;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
    }

    .form-control,
    .form-select {
        border-radius: 10px;
        border: 1px solid #e2e8f0;
        padding: 0.5rem 1rem;
        transition: var(--transition);
        font-size: 0.9rem;
    }

    .form-control:focus,
    .form-select:focus {
        border-color: var(--primary-modern);
        box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.1);
    }

    .input-group .form-control {
        border-top-right-radius: 0;
        border-bottom-right-radius: 0;
    }

    .input-group .btn {
        border-top-left-radius: 0;
        border-bottom-left-radius: 0;
    }

    /* Badges & Chips */
    .badge-modern {
        padding: 0.35em 0.8em;
        border-radius: 6px;
        font-weight: 600;
        letter-spacing: 0.02em;
        font-size: 0.8rem;
    }

    .badge-spk {
        background: rgba(67, 97, 238, 0.1) !important;
        color: #4361ee !important;
        border: 1px solid rgba(67, 97, 238, 0.3) !important;
        font-weight: 700 !important;
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

    .filter-chip {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 14px;
        border-radius: 99px;
        background: rgba(67, 97, 238, 0.1);
        color: var(--primary-modern);
        font-size: 0.8rem;
        font-weight: 600;
    }

    /* Drawers */
    .filter-drawer {
        border-left: none;
        box-shadow: -5px 0 25px rgba(0, 0, 0, 0.05);
    }

    .filter-drawer .offcanvas-header {
        border-bottom: 1px solid #f0f2f5;
        padding: 1.5rem;
    }

    .filter-section-title {
        margin-bottom: 8px;
        color: var(--text-muted);
        font-size: 0.75rem;
        font-weight: 700;
        letter-spacing: 0.08em;
        text-transform: uppercase;
    }

    .revisi-text {
        font-size: 0.85rem;
        line-height: 1.4;
    }

    .revisi-item {
        display: inline-block;
        background: rgba(239, 71, 111, 0.1);
        color: var(--danger-modern);
        padding: 2px 8px;
        border-radius: 4px;
        margin: 2px;
        font-weight: 600;
        white-space: nowrap;
    }

    /* Instagram Style Modal */
    .ig-modal .modal-content {
        border-radius: 12px;
        border: none;
        overflow: hidden;
        width: 300px;
        margin: auto;
    }

    .ig-modal-body {
        padding: 32px 24px;
        text-align: center;
    }

    .ig-modal-title {
        font-size: 1.1rem;
        font-weight: 700;
        margin-bottom: 8px;
        color: #262626;
    }

    .ig-modal-text {
        font-size: 0.9rem;
        color: #8e8e8e;
        line-height: 1.4;
    }

    .ig-btn-list {
        display: flex;
        flex-direction: column;
        border-top: 1px solid #dbdbdb;
    }

    .ig-btn {
        padding: 12px;
        background: none;
        border: none;
        border-bottom: 1px solid #dbdbdb;
        font-size: 0.9rem;
        font-weight: 600;
        cursor: pointer;
        transition: background 0.1s;
        text-decoration: none !important;
        text-align: center;
    }

    .ig-btn:last-child {
        border-bottom: none;
    }

    .ig-btn:active {
        background: #fafafa;
    }

    .ig-btn-danger {
        color: #ed4956;
        font-weight: 700;
        border: none;
        background: none;
    }

    .ig-btn-secondary {
        color: #262626;
        font-weight: 400;
    }
</style>
