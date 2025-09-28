<?php 
function toArabicNumerals($num) {
    $western = range(0, 10);
    $eastern = ['٠','١','٢','٣','٤','٥','٦','٧','٨','٩','١٠'];
    return str_replace($western, $eastern, $num);
}

$links = $pager->links(); // get actual page links

// Find current page from links
$currentPage = 1;
$totalPages = 1;
foreach ($links as $link) {
    if ($link['active']) {
        $currentPage = (int)$link['title'];
    }
    if (is_numeric($link['title'])) {
        $totalPages = max($totalPages, (int)$link['title']);
    }
}
?>

<nav aria-label="صفحات">
  <ul class="pagination justify-content-center">
      <!-- زر السابق -->
      <li class="page-item <?= $currentPage <= 1 ? 'disabled' : '' ?>">
        <?php if ($currentPage > 1): ?>
          <a class="page-link" href="<?= site_url('inventoryController/index') . '?page_orders=' . ($currentPage - 1) ?>">السابق</a>
        <?php else: ?>
          <span class="page-link">السابق</span>
        <?php endif; ?>
      </li>
      
      <!-- أرقام الصفحات -->
    <?php foreach ($links as $link): ?>
      <li class="page-item <?= $link['active'] ? 'active' : '' ?>">
        <?php if ($link['active']): ?>
          <span class="page-link"><?= toArabicNumerals($link['title']) ?></span>
        <?php else: ?>
          <a class="page-link" href="<?= $link['uri'] ?>">
            <?= toArabicNumerals($link['title']) ?>
          </a>
        <?php endif; ?>
      </li>
    <?php endforeach; ?>
    
    <!-- زر التالي -->
    <li class="page-item <?= $currentPage >= $totalPages ? 'disabled' : '' ?>">
      <?php if ($currentPage < $totalPages): ?>
        <a class="page-link" href="<?= site_url('inventoryController/index') . '?page_orders=' . ($currentPage + 1) ?>">التالي</a>
      <?php else: ?>
        <span class="page-link">التالي</span>
      <?php endif; ?>
    </li>
  </ul>
</nav>