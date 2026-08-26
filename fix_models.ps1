$files = Get-ChildItem "c:\xampp\htdocs\3DB\app\Models" -Filter "M*Mesin*.php" | Where-Object {
    -not (Select-String -Path $_.FullName -Pattern "jam_awal" -Quiet)
}
$old = "'status', 'extra_target_shifts',"
$new = "'status', 'extra_target_shifts', 'jam_awal', 'jam_akhir',"
foreach ($f in $files) {
    $content = [System.IO.File]::ReadAllText($f.FullName)
    if ($content.Contains($old)) {
        $updated = $content.Replace($old, $new)
        [System.IO.File]::WriteAllText($f.FullName, $updated)
        Write-Host "Updated: $($f.Name)"
    } else {
        Write-Host "NOT FOUND: $($f.Name)"
    }
}
