# organize_assets.ps1
# Run from repository root (c:\xampp\htdocs\richescorsos)
# This script moves files from ./puppy1_files into ./assets/{css,js,images,fonts,other}
# and updates puppy1.html to reference the new locations.

$root = Get-Location
$srcDir = Join-Path $root 'puppy1_files'
$assetsDir = Join-Path $root 'assets'
$folders = @('css','js','images','fonts','other')

# create folders
foreach ($f in $folders) {
    $d = Join-Path $assetsDir $f
    if (-not (Test-Path $d)) { New-Item -ItemType Directory -Path $d | Out-Null }
}

# helper to get target folder and filename
function Get-Target($file) {
    $name = $file.Name
    $ext = $file.Extension.ToLower()
    if (-not $ext) {
        # file with no extension: inspect contents
        $content = Get-Content $file.FullName -Raw -ErrorAction SilentlyContinue
        if ($content -match '@font-face' -or $content -match 'url\(') {
            return @{folder='css'; filename = ($name + '.css')}
        } else {
            return @{folder='other'; filename = $name}
        }
    }
    # treat .download files
    if ($ext -eq '.download') {
        # try to see if original name had .js or .css inside
        if ($name -match '\.js\.download$') { return @{folder='js'; filename = ($name -replace '\.download$','')} }
        if ($name -match '\.css\.download$') { return @{folder='css'; filename = ($name -replace '\.download$','')} }
        # fallback: inspect content
        $content = Get-Content $file.FullName -Raw -ErrorAction SilentlyContinue
        if ($content -match 'function' -or $content -match 'jQuery' -or $content -match 'window') { return @{folder='js'; filename = ($name -replace '\.download$','.js')} }
        return @{folder='other'; filename = ($name -replace '\.download$','.download')}
    }
    switch ($ext) {
        '.css' { return @{folder='css'; filename = $name} }
        '.min.css' { return @{folder='css'; filename = $name} }
        '.js' { return @{folder='js'; filename = $name} }
        '.json' { return @{folder='other'; filename = $name} }
        '.png' { return @{folder='images'; filename = $name} }
        '.jpg' { return @{folder='images'; filename = $name} }
        '.jpeg' { return @{folder='images'; filename = $name} }
        '.gif' { return @{folder='images'; filename = $name} }
        '.svg' { return @{folder='images'; filename = $name} }
        '.woff' { return @{folder='fonts'; filename = $name} }
        '.woff2' { return @{folder='fonts'; filename = $name} }
        '.ttf' { return @{folder='fonts'; filename = $name} }
        '.eot' { return @{folder='fonts'; filename = $name} }
        default { return @{folder='other'; filename = $name} }
    }
}

# Build mapping of old -> new
$mapping = @{}
Get-ChildItem -Path $srcDir -File | ForEach-Object {
    $t = Get-Target $_
    $folder = $t.folder
    $newName = $t.filename
    $dest = Join-Path $assetsDir $folder
    $destPath = Join-Path $dest $newName
    # make unique if file exists
    $i = 1
    $base = [System.IO.Path]::GetFileNameWithoutExtension($newName)
    $extn = [System.IO.Path]::GetExtension($newName)
    while (Test-Path $destPath) {
        $destPath = Join-Path $dest ("$base-$i$extn")
        $i++
    }
    # move file
    try {
        Move-Item -LiteralPath $_.FullName -Destination $destPath -Force
        $oldRel = "./puppy1_files/" + $_.Name
        $newRel = "./assets/" + $folder + "/" + ([System.IO.Path]::GetFileName($destPath))
        $mapping[$oldRel] = $newRel
        Write-Host "Moved $($_.Name) -> $newRel"
    } catch {
        Write-Warning "Failed moving $($_.FullName): $_"
    }
}

# Update puppy1.html
$htmlPath = Join-Path $root 'puppy1.html'
if (Test-Path $htmlPath) {
    $html = Get-Content $htmlPath -Raw
    foreach ($k in $mapping.Keys) {
        $v = $mapping[$k]
        $escaped = [Regex]::Escape($k)
        $html = [Regex]::Replace($html, $escaped, [System.Text.RegularExpressions.MatchEvaluator]{ param($m) $using:v }, 'IgnoreCase')
    }
    Set-Content -Path $htmlPath -Value $html -Encoding UTF8
    Write-Host "Updated puppy1.html with new paths"
} else {
    Write-Warning "puppy1.html not found at $htmlPath"
}

# done
Write-Host "Done. Summary: Moved $($mapping.Count) files."

# If you want to remove the (now maybe-empty) puppy1_files folder, uncomment below:
# if ((Get-ChildItem -Path $srcDir -Recurse | Measure-Object).Count -eq 0) { Remove-Item -Path $srcDir -Recurse -Force }
