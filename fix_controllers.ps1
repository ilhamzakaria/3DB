$files = Get-ChildItem "c:\xampp\htdocs\3DB\app\Controllers" -Filter "Mesin*.php" | Where-Object {
    -not (Select-String -Path $_.FullName -Pattern "jam_awal" -Quiet)
}
$old = "            'status'                => `$p('status') ?: 'ganti bottom rata',"
$new = "            'status'                => `$p('status') ?: 'ganti bottom rata',`n            'jam_awal'              => `$p('jam_awal'),`n            'jam_akhir'             => `$p('jam_akhir'),"
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
