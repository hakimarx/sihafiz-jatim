# ============================================
# SCRIPT PEMBERSIHAN DATA HAFIZ
# ============================================
# Hanya NIK Valid (16 digit angka) yang diexport
# Data duplikat NIK ambil yang terbaru (tahun tertinggi)
# ============================================

Write-Host "============================================"
Write-Host " PEMBERSIHAN DATA HAFIZ - SIHAFIZ JATIM"
Write-Host "============================================"

$data = Import-Csv -Path 'Book1.csv' -Delimiter ';'
Write-Host "`nTotal baris mentah: $($data.Count)"

# Step 1: Filter NIK Valid (16 digit angka)
$validNik = $data | Where-Object { 
    $_.NIK -match '^\d{16}$' -and 
    $_.NAMA -ne '' -and 
    $null -ne $_.NAMA -and
    $_.NAMA -ne '-'
}
Write-Host "Setelah filter NIK valid (16 digit): $($validNik.Count)"

# Step 2: Filter data yg NIK kosong atau invalid
$invalidData = $data | Where-Object { 
    $_.NIK -notmatch '^\d{16}$' -or 
    $_.NAMA -eq '' -or 
    $null -eq $_.NAMA -or
    $_.NAMA -eq '-'
}
Write-Host "Data DIBUANG (NIK invalid/kosong/Nama kosong): $($invalidData.Count)"

# Step 3: Deduplikasi - ambil data tahun terbaru per NIK
$grouped = $validNik | Group-Object -Property 'NIK'
$deduplicated = @()
foreach ($group in $grouped) {
    # Ambil yang tahun terbaru
    $latest = $group.Group | Sort-Object { [int]$_.TAHUN } -Descending | Select-Object -First 1
    $deduplicated += $latest
}
Write-Host "Setelah deduplikasi (ambil tahun terbaru): $($deduplicated.Count)"

# Step 4: Export ke CSV bersih
$deduplicated | Export-Csv -Path 'Book1_clean.csv' -Delimiter ';' -NoTypeInformation -Encoding UTF8
Write-Host "`nFile bersih disimpan: Book1_clean.csv"

# Step 5: Export data yg dibuang untuk referensi
$invalidData | Export-Csv -Path 'Book1_rejected.csv' -Delimiter ';' -NoTypeInformation -Encoding UTF8
Write-Host "File ditolak disimpan: Book1_rejected.csv"

# Step 6: Statistik
Write-Host "`n============================================"
Write-Host " RINGKASAN PEMBERSIHAN"
Write-Host "============================================"
Write-Host "Total data mentah     : $($data.Count)"
Write-Host "NIK valid (16 digit)  : $($validNik.Count)"
Write-Host "NIK duplikat dihapus  : $($validNik.Count - $deduplicated.Count)"
Write-Host "Data bersih (unik)    : $($deduplicated.Count)"
Write-Host "Data ditolak          : $($invalidData.Count)"

# Distribusi per kabupaten dari data bersih
Write-Host "`n=== Distribusi Data Bersih per Kabupaten ==="
$deduplicated | Group-Object -Property 'ASAL' | Select-Object Name, Count | Sort-Object Count -Descending | Format-Table -AutoSize

# Distribusi per tahun dari data bersih
Write-Host "=== Distribusi Data Bersih per Tahun Terakhir ==="
$deduplicated | Group-Object -Property 'TAHUN' | Select-Object Name, Count | Sort-Object Name -Descending | Format-Table -AutoSize

Write-Host "`n============================================"
Write-Host " SELESAI! Data siap import ke database"
Write-Host "============================================"
