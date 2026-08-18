$ErrorActionPreference = 'Stop'

$repoRoot = Split-Path -Parent $PSScriptRoot
$compiler = 'C:\Windows\Microsoft.NET\Framework64\v4.0.30319\csc.exe'
$outputDirectory = Join-Path $PSScriptRoot 'dist'
$output = Join-Path $outputDirectory 'MikroBILL-TBank-Mobile-Fix-1.1.exe'

if (-not (Test-Path -LiteralPath $compiler)) {
    throw "C# compiler not found: $compiler"
}

New-Item -ItemType Directory -Path $outputDirectory -Force | Out-Null

$manifest = Join-Path $PSScriptRoot 'app.manifest'
$source = Join-Path $PSScriptRoot 'Program.cs'
$functionsPayload = Join-Path $repoRoot 'template\functions.php'
$tinkoffPayload = Join-Path $repoRoot 'tinkoff2.php'
$certificatePayload = Join-Path $repoRoot 'cert\russian-trusted-root-ca.pem'
$compilerArguments = @(
    '/nologo',
    '/target:winexe',
    '/platform:anycpu',
    '/optimize+',
    "/out:$output",
    "/win32manifest:$manifest",
    '/reference:System.dll',
    '/reference:System.Core.dll',
    '/reference:System.Windows.Forms.dll',
    "/resource:$functionsPayload,Payload.FunctionsPhp",
    "/resource:$tinkoffPayload,Payload.Tinkoff2Php",
    "/resource:$certificatePayload,Payload.Certificate",
    $source
)

& $compiler $compilerArguments

if ($LASTEXITCODE -ne 0) {
    throw "Compiler failed with exit code $LASTEXITCODE"
}

Get-FileHash -Algorithm SHA256 -LiteralPath $output
