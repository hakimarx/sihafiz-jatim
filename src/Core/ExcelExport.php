<?php

/**
 * Simple Excel/CSV Export
 * ========================
 * Export data ke format CSV (dapat dibuka di Excel)
 * Tidak memerlukan library eksternal
 */

class ExcelExport
{
    private array $headers = [];
    private array $data = [];
    private string $filename;

    public function __construct(string $filename = 'export')
    {
        $this->filename = $filename . '_' . date('Y-m-d_His');
    }

    /**
     * Set column headers
     */
    public function setHeaders(array $headers): self
    {
        $this->headers = $headers;
        return $this;
    }

    /**
     * Set data rows
     */
    public function setData(array $data): self
    {
        $this->data = $data;
        return $this;
    }

    /**
     * Download as CSV
     */
    public function downloadCsv(): void
    {
        // Set headers for CSV download
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $this->filename . '.csv"');
        header('Pragma: no-cache');
        header('Expires: 0');

        // Open output stream
        $output = fopen('php://output', 'w');

        // Add BOM for Excel UTF-8 compatibility
        fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));

        // Write headers
        if (!empty($this->headers)) {
            fputcsv($output, $this->headers, ';');
        }

        // Write data rows
        foreach ($this->data as $row) {
            // Convert associative array to indexed array
            if (!empty($this->headers)) {
                $values = [];
                foreach (array_keys($this->headers) as $key) {
                    $values[] = $row[$key] ?? '';
                }
                fputcsv($output, $values, ';');
            } else {
                fputcsv($output, array_values($row), ';');
            }
        }

        fclose($output);
        exit;
    }

    /**
     * Download as HTML table (opens nicely in Excel)
     */
    public function downloadExcel(): void
    {
        // Set headers for Excel download
        header('Content-Type: application/vnd.ms-excel; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $this->filename . '.xls"');
        header('Pragma: no-cache');
        header('Expires: 0');

        echo '<!DOCTYPE html><html><head><meta charset="utf-8"></head><body>';
        echo '<table border="1" cellpadding="5" cellspacing="0">';

        // Headers
        if (!empty($this->headers)) {
            echo '<tr style="background-color: #4CAF50; color: white; font-weight: bold;">';
            foreach ($this->headers as $header) {
                echo '<th>' . htmlspecialchars($header) . '</th>';
            }
            echo '</tr>';
        }

        // Data rows
        $rowNum = 0;
        foreach ($this->data as $row) {
            $rowNum++;
            $bgColor = $rowNum % 2 === 0 ? '#f2f2f2' : '#ffffff';
            echo '<tr style="background-color: ' . $bgColor . ';">';

            if (!empty($this->headers)) {
                foreach (array_keys($this->headers) as $key) {
                    $value = $row[$key] ?? '';
                    echo '<td>' . htmlspecialchars($value) . '</td>';
                }
            } else {
                foreach ($row as $value) {
                    echo '<td>' . htmlspecialchars($value) . '</td>';
                }
            }

            echo '</tr>';
        }

        echo '</table></body></html>';
        exit;
    }

    /**
     * Static helper untuk export data hafiz
     */
    public static function exportHafiz(array $data, string $format = 'excel'): void
    {
        $export = new self('data_hafiz');

        $export->setHeaders([
            'nik' => 'NIK',
            'nama' => 'Nama Lengkap',
            'tempat_lahir' => 'Tempat Lahir',
            'tanggal_lahir' => 'Tanggal Lahir',
            'jenis_kelamin' => 'L/P',
            'alamat' => 'Alamat',
            'desa_kelurahan' => 'Desa/Kelurahan',
            'kecamatan' => 'Kecamatan',
            'kabupaten_kota' => 'Kabupaten/Kota',
            'telepon' => 'No. Telepon',
            'sertifikat_tahfidz' => 'Hafalan',
            'nilai_wawasan' => 'Nilai Wawasan',
            'nilai_hafalan' => 'Nilai Hafalan',
            'nilai_total' => 'Nilai Total',
            'status' => 'Status',
        ]);

        $export->setData($data);

        if ($format === 'csv') {
            $export->downloadCsv();
        } else {
            $export->downloadExcel();
        }
    }

    /**
     * Static helper untuk export laporan harian
     */
    public static function exportLaporan(array $data, string $format = 'excel'): void
    {
        $export = new self('laporan_harian');

        $export->setHeaders([
            'tanggal' => 'Tanggal',
            'hafiz_nama' => 'Nama Hafiz',
            'hafiz_nik' => 'NIK',
            'kabupaten_kota_nama' => 'Kabupaten/Kota',
            'jenis_kegiatan' => 'Jenis Kegiatan',
            'deskripsi' => 'Deskripsi',
            'lokasi' => 'Lokasi',
            'durasi_menit' => 'Durasi (menit)',
            'status_verifikasi' => 'Status',
            'verifier_nama' => 'Diverifikasi Oleh',
        ]);

        $export->setData($data);

        if ($format === 'csv') {
            $export->downloadCsv();
        } else {
            $export->downloadExcel();
        }
    }
}
