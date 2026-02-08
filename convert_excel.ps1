$excel = New-Object -ComObject Excel.Application
$excel.Visible = $false
$excel.DisplayAlerts = $false
try {
    $workbook = $excel.Workbooks.Open("d:\Seleksi Huffadz aplikasi data hafidz 2023\Seleksi Huffazh 2023.xlsm")
    $workbook.SaveAs("d:\Seleksi Huffadz aplikasi data hafidz 2023\Seleksi Huffazh 2023.csv", 6)
    $workbook.Close($false)
    Write-Host "Success: Excel converted to CSV"
} catch {
    Write-Host "Error: $($_.Exception.Message)"
} finally {
    $excel.Quit()
    [System.Runtime.Interopservices.Marshal]::ReleaseComObject($excel) | Out-Null
}
