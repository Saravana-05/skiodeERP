<?php
/**
 * page_guard.php
 * Include at the top of every AJAX-fragment page (after session_start).
 * If the page is opened directly in a browser (not via jQuery $.load()),
 * redirect to index.php so the full application frame is always shown.
 */
if (empty($_SERVER['HTTP_X_REQUESTED_WITH']) ||
    strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) !== 'xmlhttprequest') {
    header("Location: index.php");
    exit;
}
