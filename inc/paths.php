<?php

// App URL helpers shared by pages that need links before the header is rendered.
$appRoot = realpath(__DIR__ . '/..') ?: dirname(__DIR__);
$documentRoot = realpath($_SERVER['DOCUMENT_ROOT'] ?? '') ?: '';
$normalizedAppRoot = str_replace('\\', '/', rtrim($appRoot, '/'));
$normalizedDocumentRoot = str_replace('\\', '/', rtrim($documentRoot, '/'));

$requestPath = parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH) ?: '';
$scriptName = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '');
$basePath = '';

if ($normalizedDocumentRoot !== '' && str_starts_with($normalizedAppRoot, $normalizedDocumentRoot)) {
    $basePath = substr($normalizedAppRoot, strlen($normalizedDocumentRoot));
    $basePath = '/' . trim($basePath, '/');
    if ($basePath === '/') {
        $basePath = '';
    }
} else {
    $candidatePaths = [$requestPath, $scriptName];

    foreach ($candidatePaths as $candidatePath) {
        $candidatePath = str_replace('\\', '/', trim((string)$candidatePath));
        if ($candidatePath === '' || $candidatePath === '/') {
            continue;
        }

        foreach (['/pages/', '/shop/', '/blog/', '/account/', '/admin/'] as $knownDir) {
            $knownDirPos = strpos($candidatePath, $knownDir);
            if ($knownDirPos !== false) {
                $basePath = substr($candidatePath, 0, $knownDirPos);
                break 2;
            }
        }

        $segments = array_values(array_filter(explode('/', $candidatePath), 'strlen'));
        if (count($segments) >= 2) {
            $basePath = '/' . $segments[0];
            break;
        }
    }
}

if ($basePath === '/' || $basePath === '.') {
    $basePath = '';
}

if (!function_exists('site_url')) {
    function site_url(string $path = ''): string
    {
        global $basePath;
        $path = trim($path);

        if ($path === '' || $path === '/') {
            return $basePath !== '' ? $basePath : '/';
        }

        if (preg_match('#^(https?:)?//#', $path)) {
            return $path;
        }

        $prefix = $basePath !== '' ? $basePath : '';

        return $prefix . '/' . ltrim($path, '/');
    }
}

if (!function_exists('app_path')) {
    /**
     * Resolve a path stored in the database (e.g. "/richescorsos/uploads/x.jpg",
     * "/uploads/x.jpg", or "uploads/x.jpg") to an absolute filesystem path
     * inside the project itself.
     *
     * This intentionally does NOT use $_SERVER['DOCUMENT_ROOT'] + a hardcoded
     * "/richescorsos" folder, because that only matches production, where the
     * project happens to live in a "richescorsos" subfolder of the web root.
     * Locally (or on any host where the project *is* the document root), that
     * assumption is wrong and uploads get written to/read from the wrong
     * folder. $appRoot (from above) always points at this project's real
     * folder on disk, so building paths from it works in both environments.
     */
    function app_path(string $storedPath = ''): string
    {
        global $appRoot, $basePath;
        $path = str_replace('\\', '/', trim($storedPath));

        foreach (array_filter(['/richescorsos', $basePath]) as $knownBase) {
            if ($path === $knownBase) {
                $path = '';
                break;
            }
            if (str_starts_with($path, $knownBase . '/')) {
                $path = substr($path, strlen($knownBase));
                break;
            }
        }

        $path = ltrim($path, '/');

        return $path === '' ? rtrim($appRoot, '/') : rtrim($appRoot, '/') . '/' . $path;
    }
}

if (!function_exists('normalize_site_url')) {
    function normalize_site_url(?string $path, string $fallback = 'assets/images/luna.webp'): string
    {
        global $basePath;
        $path = str_replace('\\', '/', trim((string)$path));

        if ($path === '') {
            return site_url($fallback);
        }

        if (preg_match('#^https?://#', $path)) {
            return $path;
        }

        foreach (array_filter(['/richescorsos', $basePath]) as $knownBase) {
            if ($path === $knownBase) {
                $path = '';
                break;
            }
            if (str_starts_with($path, $knownBase . '/')) {
                $path = substr($path, strlen($knownBase));
                break;
            }
        }

        return site_url($path);
    }
}
