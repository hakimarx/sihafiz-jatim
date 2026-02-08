<?php

/**
 * Profile Controller
 * ==================
 * Handle profile management for all users
 */

class ProfileController extends Controller
{
    /**
     * Show profile form
     */
    public function index(): void
    {
        // Require any logged in role
        if (!isLoggedIn()) {
            $this->redirect(APP_URL . '/login');
            return;
        }

        $userId = getCurrentUserId();
        $user = User::findById($userId);

        // If user is Hafiz, get hafiz details too
        $hafiz = null;
        if (hasRole(ROLE_HAFIZ)) {
            $hafiz = Hafiz::findByUserId($userId);
        }

        $this->view('profile.index', [
            'title' => 'Profil Saya - ' . APP_NAME,
            'user' => $user,
            'hafiz' => $hafiz,
        ]);
    }

    /**
     * Update profile
     */
    public function update(): void
    {
        if (!isLoggedIn()) {
            $this->redirect(APP_URL . '/login');
            return;
        }

        if (!$this->isPost() || !$this->validateCsrf()) {
            setFlash('error', 'Request tidak valid.');
            $this->redirect(APP_URL . '/profile');
            return;
        }

        $userId = getCurrentUserId();

        $data = [
            'nama' => $this->input('nama'),
            'telepon' => $this->input('telepon'),
            'email' => $this->input('email'),
        ];

        // Handle Profile Picture Upload
        if (isset($_FILES['foto_profil']) && $_FILES['foto_profil']['error'] === UPLOAD_ERR_OK) {
            $fotoPath = $this->handleFileUpload($_FILES['foto_profil'], 'profile_' . $userId);
            if ($fotoPath) {
                $data['foto_profil'] = $fotoPath;
                // Update Session
                $_SESSION['foto_profil'] = $fotoPath;
            }
        }

        // Handle Password Change
        $password = $this->input('password');
        $passwordConfirmation = $this->input('password_confirmation');

        if (!empty($password)) {
            if ($password !== $passwordConfirmation) {
                setFlash('error', 'Konfirmasi password tidak cocok.');
                $this->redirect(APP_URL . '/profile');
                return;
            }
            $data['password'] = $password;
        }

        try {
            User::update($userId, $data);

            // Update session data
            if (isset($data['nama'])) $_SESSION['nama'] = $data['nama'];

            // If user is Hafiz, update Hafiz record too
            if (hasRole(ROLE_HAFIZ)) {
                $hafiz = Hafiz::findByUserId($userId);
                if ($hafiz) {
                    $hafizData = [
                        'nama' => $data['nama'],
                        'telepon' => $data['telepon'],
                        'email' => $data['email'],
                    ];

                    if (isset($data['foto_profil'])) {
                        $hafizData['foto_profil'] = $data['foto_profil'];
                    }

                    Hafiz::update($hafiz['id'], $hafizData);
                }
            }

            setFlash('success', 'Profil berhasil diperbarui.');
        } catch (Exception $e) {
            error_log("Error updating profile: " . $e->getMessage());
            setFlash('error', 'Gagal memperbarui profil.');
        }

        $this->redirect(APP_URL . '/profile');
    }

    /**
     * Helper to handle file upload
     */
    private function handleFileUpload(array $file, string $prefix): ?string
    {
        $targetDir = UPLOAD_PATH . '/profiles/';
        if (!is_dir($targetDir)) {
            if (!mkdir($targetDir, 0755, true) && !is_dir($targetDir)) {
                error_log("Failed to create directory: " . $targetDir);
                return null;
            }
        }

        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        // Validate extension (simple check)
        if (!in_array(strtolower($extension), ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
            return null;
        }

        $fileName = $prefix . '_' . time() . '.' . $extension;
        $targetFile = $targetDir . $fileName;

        if (move_uploaded_file($file['tmp_name'], $targetFile)) {
            return '/uploads/profiles/' . $fileName;
        }

        return null;
    }
}
