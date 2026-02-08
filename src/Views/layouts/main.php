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

    <!-- Bootstrap 5 CSS (CDN) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        :root {
            --primary-color: #198754;
            --secondary-color: #0d6efd;
        }

        body {
            background-color: #f8f9fa;
            min-height: 100vh;
        }

        .sidebar {
            min-height: 100vh;
            background: linear-gradient(180deg, #198754 0%, #157347 100%);
        }

        .sidebar .nav-link {
            color: rgba(255, 255, 255, 0.8);
            padding: 0.75rem 1rem;
            border-radius: 0.375rem;
            margin-bottom: 0.25rem;
        }

        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            color: #fff;
            background-color: rgba(255, 255, 255, 0.15);
        }

        .sidebar .nav-link i {
            width: 1.5rem;
        }

        .main-content {
            padding: 1.5rem;
        }

        .card {
            border: none;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        }

        .stat-card {
            border-left: 4px solid var(--primary-color);
        }

        .stat-card.pending {
            border-left-color: #ffc107;
        }

        .stat-card.success {
            border-left-color: #198754;
        }

        .stat-card.danger {
            border-left-color: #dc3545;
        }

        .navbar-brand {
            font-weight: 600;
        }

        .page-header {
            margin-bottom: 1.5rem;
        }

        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .btn-primary:hover {
            background-color: #157347;
            border-color: #146c43;
        }

        .table th {
            background-color: #f8f9fa;
            font-weight: 600;
        }

        .badge-pending {
            background-color: #ffc107;
            color: #000;
        }

        .badge-disetujui {
            background-color: #198754;
        }

        .badge-ditolak {
            background-color: #dc3545;
        }

        .badge-lulus {
            background-color: #198754;
        }

        .badge-tidak_lulus {
            background-color: #dc3545;
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
                                        <i class="bi bi-file-earmark-check"></i> Verifikasi Laporan
                                    </a>
                                </li>
                            <?php endif; ?>

                            <?php if ($role === ROLE_ADMIN_PROV || $role === ROLE_ADMIN_KABKO || $role === ROLE_PENGUJI): ?>
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