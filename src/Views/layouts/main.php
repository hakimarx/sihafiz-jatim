<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Sistem Informasi & Pelaporan Huffadz Jawa Timur">
    <title><?= htmlspecialchars($title ?? APP_NAME) ?></title>

    <?php
    $favicon = Setting::get('app_favicon');
    if ($favicon): ?>
        <link rel="icon" type="image/x-icon" href="<?= APP_URL . $favicon ?>">
    <?php endif; ?>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        emerald: {
                            50: '#ecfdf5',
                            100: '#d1fae5',
                            600: '#059669',
                            700: '#047857',
                            800: '#065f46',
                            900: '#064e3b',
                        },
                        gold: {
                            400: '#fbbf24',
                            500: '#d4af37',
                            600: '#b4942b',
                        }
                    },
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                        arabic: ['Amiri', 'serif'],
                    }
                }
            }
        }
    </script>

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Amiri:wght@400;700&display=swap" rel="stylesheet">
    
    <!-- Bootstrap 5 CSS (CDN) - Keep for Grid/Components -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        :root {
            --primary-color: #059669; /* Emerald 600 */
            --secondary-color: #d4af37; /* Gold */
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: #f0fdf4; /* Emerald 50 */
            background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23065f46' fill-opacity='0.03'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
        }

        /* Modern Sidebar */
        .sidebar {
            min-height: 100vh;
            background: linear-gradient(180deg, #064e3b 0%, #065f46 100%);
            box-shadow: 4px 0 24px rgba(0,0,0,0.1);
        }

        .sidebar .nav-link {
            color: rgba(255, 255, 255, 0.7);
            padding: 0.8rem 1rem;
            border-radius: 0.75rem;
            margin-bottom: 0.25rem;
            transition: all 0.3s ease;
            font-weight: 500;
        }

        .sidebar .nav-link:hover {
            color: #fff;
            background-color: rgba(255, 255, 255, 0.1);
            transform: translateX(4px);
        }

        .sidebar .nav-link.active {
            color: #fff;
            background: linear-gradient(90deg, rgba(212, 175, 55, 0.2) 0%, rgba(212, 175, 55, 0.05) 100%);
            border-left: 4px solid #d4af37; /* Gold Border */
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }

        .sidebar .nav-link i {
            width: 1.75rem;
            font-size: 1.1rem;
        }

        /* Card Modernization */
        .card {
            border: none;
            border-radius: 1rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            transition: transform 0.2s, box-shadow 0.2s;
        }
        
        .card-header {
            background-color: transparent;
            border-bottom: 1px solid rgba(0,0,0,0.05);
            font-family: 'Amiri', serif; /* Religious Touch for Headers */
            font-weight: 700;
            font-size: 1.1rem;
            padding: 1rem 1.25rem;
            color: #064e3b;
        }

        /* Responsive Table Design */
        .table {
            --bs-table-bg: transparent;
        }
        .table th {
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.05em;
            color: #64748b;
            background-color: #f8fafc;
            border-bottom: 2px solid #e2e8f0;
        }
        .table td {
            vertical-align: middle;
            font-size: 0.9rem;
        }
        
        /* Glassmorphism Utilities */
        .glass {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.5);
        }

        /* Button Enhancements */
        .btn {
            border-radius: 0.5rem;
            padding: 0.5rem 1rem;
            font-weight: 500;
            transition: all 0.2s;
        }
        .btn-primary {
            background-color: #059669; /* Emerald 600 */
            border-color: #059669;
        }
        .btn-primary:hover {
            background-color: #047857; /* Emerald 700 */
            border-color: #047857;
            transform: translateY(-1px);
            box-shadow: 0 4px 6px rgba(5, 150, 105, 0.2);
        }

        @media (max-width: 768px) {
            .sidebar {
                min-height: auto;
            }
        }
    </style>
</head>

<body>
    <?php if (isLoggedIn()): ?>
        <div class="container-fluid">
            <div class="row">
                <!-- Sidebar -->
                <nav class="col-md-3 col-lg-2 d-md-block sidebar collapse" id="sidebarMenu">
                    <div class="position-sticky pt-3">
                        <div class="text-center text-white mb-4 pb-3 border-bottom border-light border-opacity-25">
                            <?php
                            $logo = Setting::get('app_logo');
                            if ($logo): ?>
                                <img src="<?= APP_URL . $logo ?>" alt="Logo" class="img-fluid mb-2" style="max-height: 80px;">
                            <?php else: ?>
                                <i class="bi bi-book-half fs-1"></i>
                            <?php endif; ?>
                            <h6 class="mt-2 mb-0"><?= htmlspecialchars(Setting::get('app_name', APP_NAME)) ?></h6>
                            <small class="opacity-75">Tahun <?= Setting::get('tahun_aktif', TAHUN_ANGGARAN) ?></small>
                        </div>

                        <ul class="nav flex-column">
                            <?php
                            $role = getCurrentUserRole();
                            $currentUri = $_SERVER['REQUEST_URI'];
                            ?>

                            <?php if ($role === ROLE_ADMIN_PROV || $role === ROLE_ADMIN_KABKO): ?>
                                <li class="nav-item">
                                    <a class="nav-link <?= strpos($currentUri, '/admin/dashboard') !== false ? 'active' : '' ?>" href="<?= APP_URL ?>/admin/dashboard">
                                        <i class="bi bi-speedometer2"></i> Dashboard
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link <?= strpos($currentUri, '/admin/hafiz') !== false ? 'active' : '' ?>" href="<?= APP_URL ?>/admin/hafiz">
                                        <i class="bi bi-people"></i> Data Hafiz
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link <?= strpos($currentUri, '/admin/laporan') !== false ? 'active' : '' ?>" href="<?= APP_URL ?>/admin/laporan">
                                        <i class="bi bi-journal-text"></i> Laporan Harian Hafiz
                                    </a>
                                </li>
                                <?php
                                $pendingKabkoId = null;
                                if ($role === ROLE_ADMIN_KABKO) {
                                    $currentAdmin = User::findById(getCurrentUserId());
                                    $pendingKabkoId = $currentAdmin['kabupaten_kota_id'] ?? null;
                                }
                                $pendingCount = User::countPendingApproval($pendingKabkoId);
                                ?>
                                <li class="nav-item">
                                    <a class="nav-link <?= strpos($currentUri, '/admin/pending') !== false ? 'active' : '' ?>" href="<?= APP_URL ?>/admin/pending">
                                        <i class="bi bi-person-check"></i> Persetujuan Daftar
                                        <?php if ($pendingCount > 0): ?>
                                            <span class="badge bg-danger rounded-pill ms-1"><?= $pendingCount ?></span>
                                        <?php endif; ?>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link <?= strpos($currentUri, '/admin/mutasi') !== false ? 'active' : '' ?>" href="<?= APP_URL ?>/admin/mutasi">
                                        <i class="bi bi-arrow-left-right"></i> Transfer Hafiz (Mutasi)
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link <?= strpos($currentUri, '/admin/reports') !== false ? 'active' : '' ?>" href="<?= APP_URL ?>/admin/reports">
                                        <i class="bi bi-printer"></i> Cetak Laporan
                                    </a>
                                </li>
                            <?php endif; ?>

                            <?php if ($role === ROLE_ADMIN_PROV || $role === ROLE_PENGUJI): ?>
                                <li class="nav-item">
                                    <a class="nav-link <?= strpos($currentUri, '/seleksi') !== false ? 'active' : '' ?>" href="<?= APP_URL ?>/seleksi">
                                        <i class="bi bi-clipboard-check"></i> Seleksi & Penilaian
                                    </a>
                                </li>
                            <?php endif; ?>

                            <?php if ($role === ROLE_ADMIN_PROV): ?>
                                <li class="nav-item mt-3">
                                    <small class="text-white opacity-50 px-3 text-uppercase fw-bold" style="font-size: 0.7rem;">Pengaturan</small>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link <?= strpos($currentUri, '/admin/users') !== false ? 'active' : '' ?>" href="<?= APP_URL ?>/admin/users">
                                        <i class="bi bi-people-fill"></i> Manajemen User
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link <?= strpos($currentUri, '/admin/settings') !== false ? 'active' : '' ?>" href="<?= APP_URL ?>/admin/settings">
                                        <i class="bi bi-gear-fill"></i> Pengaturan Web
                                    </a>
                                </li>
                            <?php endif; ?>

                            <?php if ($role === ROLE_HAFIZ): ?>
                                <li class="nav-item">
                                    <a class="nav-link <?= strpos($currentUri, '/hafiz/dashboard') !== false ? 'active' : '' ?>" href="<?= APP_URL ?>/hafiz/dashboard">
                                        <i class="bi bi-speedometer2"></i> Dashboard
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link <?= strpos($currentUri, '/hafiz/laporan') !== false ? 'active' : '' ?>" href="<?= APP_URL ?>/hafiz/laporan">
                                        <i class="bi bi-journal-text"></i> Laporan Harian
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link <?= strpos($currentUri, '/hafiz/profil') !== false ? 'active' : '' ?>" href="<?= APP_URL ?>/hafiz/profil">
                                        <i class="bi bi-person"></i> Profil Saya
                                    </a>
                                </li>
                            <?php endif; ?>

                            <li class="nav-item mt-4 pt-3 border-top border-light border-opacity-25">
                                <a class="nav-link" href="<?= APP_URL ?>/logout">
                                    <i class="bi bi-box-arrow-right"></i> Logout
                                </a>
                            </li>
                        </ul>
                    </div>
                </nav>

                <!-- Main Content -->
                <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 main-content">
                    <!-- Top Bar -->
                    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                        <button class="btn btn-sm btn-outline-secondary d-md-none" type="button" data-bs-toggle="collapse" data-bs-target="#sidebarMenu">
                            <i class="bi bi-list"></i>
                        </button>
                        <div class="d-flex align-items-center">
                            <div class="dropdown">
                                <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle text-dark" id="dropdownUser1" data-bs-toggle="dropdown" aria-expanded="false">
                                    <?php if (!empty($_SESSION['foto_profil'])): ?>
                                        <img src="<?= APP_URL . $_SESSION['foto_profil'] ?>" alt="Profile" class="rounded-circle me-2" style="width: 32px; height: 32px; object-fit: cover;">
                                    <?php else: ?>
                                        <i class="bi bi-person-circle fs-4 me-2 text-primary"></i>
                                    <?php endif; ?>
                                    <div class="me-3 d-none d-sm-block text-start">
                                        <div class="small text-muted" style="line-height: 1;">Halo,</div>
                                        <strong><?= htmlspecialchars($_SESSION['nama'] ?? $_SESSION['username'] ?? 'User') ?></strong>
                                    </div>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end text-small shadow" aria-labelledby="dropdownUser1">
                                    <li><a class="dropdown-item" href="<?= APP_URL ?>/profile"><i class="bi bi-person me-2"></i>Profil Saya</a></li>
                                    <li>
                                        <hr class="dropdown-divider">
                                    </li>
                                    <li><a class="dropdown-item text-danger" href="<?= APP_URL ?>/logout"><i class="bi bi-box-arrow-right me-2"></i>Logout</a></li>
                                </ul>
                            </div>
                            <span class="badge bg-secondary ms-2"><?= ucfirst(str_replace('_', ' ', getCurrentUserRole())) ?></span>
                        </div>
                    </div>

                    <!-- Flash Messages -->
                    <?php $flash = getFlash(); ?>
                    <?php if ($flash): ?>
                        <div class="alert alert-<?= $flash['type'] === 'error' ? 'danger' : $flash['type'] ?> alert-dismissible fade show" role="alert">
                            <?= $flash['message'] ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <!-- Page Content -->
                    <?= $content ?>
                </main>
            </div>
        </div>
    <?php else: ?>
        <!-- Content for unauthenticated pages -->
        <?= $content ?>
    <?php endif; ?>

    <!-- Bootstrap JS (CDN) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Custom JS -->
    <script>
        // Auto-hide alerts after 5 seconds
        document.querySelectorAll('.alert').forEach(function(alert) {
            setTimeout(function() {
                var bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            }, 5000);
        });
    </script>
</body>

</html>