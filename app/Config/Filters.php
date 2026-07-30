<?php

namespace Config;

use CodeIgniter\Config\Filters as BaseFilters;
use CodeIgniter\Filters\Cors;
use CodeIgniter\Filters\CSRF;
use CodeIgniter\Filters\DebugToolbar;
use CodeIgniter\Filters\ForceHTTPS;
use CodeIgniter\Filters\Honeypot;
use CodeIgniter\Filters\InvalidChars;
use CodeIgniter\Filters\PageCache;
use CodeIgniter\Filters\PerformanceMetrics;
use CodeIgniter\Filters\SecureHeaders;

class Filters extends BaseFilters
{
    /**
     * Configures aliases for Filter classes to
     * make reading things nicer and simpler.
     *
     * @var array<string, class-string|list<class-string>>
     *
     * [filter_name => classname]
     * or [filter_name => [classname1, classname2, ...]]
     */
    public array $aliases = [
        'csrf'          => CSRF::class,
        'toolbar'       => DebugToolbar::class,
        'honeypot'      => Honeypot::class,
        'invalidchars'  => InvalidChars::class,
        'secureheaders' => SecureHeaders::class,
        'cors'          => Cors::class,
        'forcehttps'    => ForceHTTPS::class,
        'pagecache'     => PageCache::class,
        'performance'   => PerformanceMetrics::class,
        'auth'          => \App\Filters\AuthFilter::class,
        'manager'       => \App\Filters\RoleManager::class,
        'admin'         => \App\Filters\RoleAdmin::class,
        'home'          => \App\Filters\RoleHome::class,
        'ppic'          => \App\Filters\RolePpic::class,
        'gudang'        => \App\Filters\RoleGudang::class,
        'managerPpic'               => \App\Filters\RoleManagerPpic::class,
        'operatorReadOnly'          => \App\Filters\RoleOperatorReadOnly::class,
        'managerProduksi'           => \App\Filters\RoleManagerProduksi::class,
        'managerWarehouse2'         => \App\Filters\RoleManagerWarehouse2::class,
        'readOnlyProduksi'          => \App\Filters\RoleReadOnlyProduksi::class,
        'readOnlyWarehouse2'        => \App\Filters\RoleReadOnlyWarehouse2::class,

    ];

    /**
     * List of special required filters.
     *
     * The filters listed here are special. They are applied before and after
     * other kinds of filters, and always applied even if a route does not exist.
     *
     * Filters set by default provide framework functionality. If removed,
     * those functions will no longer work.
     *
     * @see https://codeigniter.com/user_guide/incoming/filters.html#provided-filters
     *
     * @var array{before: list<string>, after: list<string>}
     */
    public array $required = [
        'before' => [
            // 'forcehttps', // Disabled: site uses HTTP, enabling this causes redirect loop/blocked form
            'pagecache',  // Web Page Caching
        ],
        'after' => [
            'pagecache',   // Web Page Caching
            'performance', // Performance Metrics
            'toolbar',     // Debug Toolbar
        ],
    ];

    /**
     * List of filter aliases that are always
     * applied before and after every request.
     *
     * @var array{
     *     before: array<string, array{except: list<string>|string}>|list<string>,
     *     after: array<string, array{except: list<string>|string}>|list<string>
     * }
     */
    public array $globals = [
        'before' => [
            'csrf' => [
                'except' => [
                    // Chat & Profile
                    'pesan/sendMessage',
                    'profile/upload-foto',
                    'profile/upload-ttd',
                    'profile/auto-upload-ttd',

                    // Auth
                    'login',

                    // Produksi module
                    'produksi/simpan',
                    'produksi/update/*',
                    'produksi/delete/*',
                    'produksi/restore/*',
                    'produksi/delete_permanent/*',

                    // Operator
                    'operator/tambahData',
                    'operator/update/*',
                    'operator/delete/*',

                    // Gudang
                    'gudang/tambahData',
                    'gudang/update/*',
                    'gudang/delete/*',

                    // Gudang sub-modules
                    'gudang/bahan_baku/tambahData',
                    'gudang/bahan_baku/update',
                    'gudang/bahan_baku/update/*',
                    'gudang/bahan_baku/delete/*',
                    'gudang/pengiriman/tambahData',
                    'gudang/pengiriman/update',
                    'gudang/pengiriman/update/*',
                    'gudang/pengiriman/delete/*',
                    'gudang/bam/tambahData',
                    'gudang/bam/update',
                    'gudang/bam/update/*',
                    'gudang/bam/delete/*',
                    'gudang/finish_good/tambahData',
                    'gudang/finish_good/update',
                    'gudang/finish_good/update/*',
                    'gudang/finish_good/delete/*',
                    'gudang/packaging/tambahData',
                    'gudang/packaging/update',
                    'gudang/packaging/update/*',
                    'gudang/packaging/delete/*',
                    'gudang/masterbatch/tambahData',
                    'gudang/masterbatch/update',
                    'gudang/masterbatch/update/*',
                    'gudang/masterbatch/delete/*',
                    'gudang/galon_reject/tambahData',
                    'gudang/galon_reject/update',
                    'gudang/galon_reject/update/*',
                    'gudang/galon_reject/delete/*',
                    'gudang/gilingan_galon/tambahData',
                    'gudang/gilingan_galon/update/*',
                    'gudang/gilingan_galon/delete/*',
                    'gudang/stiker/tambahData',
                    'gudang/stiker/update/*',
                    'gudang/stiker/delete/*',
                    'gudang/reject_produksi/tambahData',
                    'gudang/reject_produksi/update/*',
                    'gudang/reject_produksi/delete/*',
                    'gudang/mesin_*/tambahData',
                    'gudang/mesin_*/update/*',
                    'gudang/mesin_*/delete/*',

                    // PPIC
                    'ppic/tambahData',
                    'ppic/update',
                    'ppic/update/*',
                    'ppic/delete/*',
                    'ppic/createUser',
                    'ppic/finish_good/*/tambah',
                    'ppic/finish_good/*/update/*',
                    'ppic/finish_good/*/delete/*',
                    'ppic/mesin_*/tambahData',
                    'ppic/mesin_*/update/*',
                    'ppic/mesin_*/delete/*',

                    // Admin
                    'admin/tambahData',
                    'admin/update/*',
                    'admin/delete/*',

                    // Laporan legacy
                    'laporan/tambahData',
                    'laporan/update/*',
                    'laporan/delete/*',

                    // Bahan baku legacy
                    'bahan_baku/tambahData',
                    'bahan_baku/update',
                    'bahan_baku/update/*',
                    'bahan_baku/delete/*',

                    // Pengiriman legacy
                    'pengiriman/tambahData',
                    'pengiriman/update',
                    'pengiriman/update/*',
                    'pengiriman/delete/*',

                    // Finish good legacy
                    'finish_good/tambahData',
                    'finish_good/update',
                    'finish_good/update/*',
                    'finish_good/delete/*',

                    // Warehouse2
                    'warehouse2/store-pemasok',
                    'warehouse2/update-status-pemasok/*',
                    'warehouse2/delete-pemasok/*',
                    'warehouse2/store-produksi',
                    'warehouse2/update-produksi/*',
                    'warehouse2/update-status-produksi/*',
                    'warehouse2/delete-produksi/*',
                    'warehouse2/store-pembeli',
                    'warehouse2/update-pembeli/*',
                    'warehouse2/update-status-pembeli/*',
                    'warehouse2/delete-pembeli/*',
                    'warehouse2/store-masuk-produksi',
                    'warehouse2/delete-masuk-produksi/*',
                    'warehouse2/store-orowarehouse',
                    'warehouse2/update-orowarehouse/*',
                    'warehouse2/delete-orowarehouse/*',
                    'warehouse2/restore-orowarehouse/*',
                    'warehouse2/delete-permanent-orowarehouse/*',
                ]
            ],
        ],
        'after' => [
            // 'honeypot',
            // 'secureheaders',
        ],
    ];

    /**
     * List of filter aliases that works on a
     * particular HTTP method (GET, POST, etc.).
     *
     * Example:
     * 'POST' => ['foo', 'bar']
     *
     * If you use this, you should disable auto-routing because auto-routing
     * permits any HTTP method to access a controller. Accessing the controller
     * with a method you don't expect could bypass the filter.
     *
     * @var array<string, list<string>>
     */
    public array $methods = [];

    /**
     * List of filter aliases that should run on any
     * before or after URI patterns.
     *
     * Example:
     * 'isLoggedIn' => ['before' => ['account/*', 'profiles/*']]
     *
     * @var array<string, array<string, list<string>>>
     */
    public array $filters = [];
}
