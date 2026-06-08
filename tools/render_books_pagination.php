<?php
$section = 'Books';
$search = '';
$total_pages = 2;
$current_page = 2;
ob_start();
include __DIR__ . '/../view/partials/pagination.php';
echo ob_get_clean();
