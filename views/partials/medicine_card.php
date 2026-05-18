<?php
// Expected: $med array with keys: id, name, vendor_name, price, availability,
//           image_path, category_name, category_type, description
$inStock = (int)($med['availability'] ?? 0) > 0;
?>
<div class="med-card">
    <div class="med-card__media">
        <?php if (!empty($med['image_path']) && file_exists($med['image_path'])): ?>
            <img src="<?php echo htmlspecialchars($med['image_path']); ?>"
                 alt="<?php echo htmlspecialchars($med['name']); ?>"
                 class="med-card__img" loading="lazy">
        <?php else: ?>
            <div class="med-card__img-placeholder">
                <svg viewBox="0 0 48 48" fill="none" width="40" height="40">
                    <rect x="8" y="14" width="32" height="22" rx="3" stroke="#d1d5db" stroke-width="2"/>
                    <path d="M24 20v10M19 25h10" stroke="#9ca3af" stroke-width="2" stroke-linecap="round"/>
                </svg>
            </div>
        <?php endif; ?>
    </div>

    <div class="med-card__body">
        <div class="med-card__badges">
            <?php if (!empty($med['category_type'])): ?>
                <span class="med-badge med-badge--<?php echo htmlspecialchars($med['category_type']); ?>">
                    <?php echo htmlspecialchars($med['category_type']); ?>
                </span>
            <?php endif; ?>
            <?php if ($inStock): ?>
                <span class="med-badge med-badge--stock">In stock (<?php echo (int)$med['availability']; ?>)</span>
            <?php else: ?>
                <span class="med-badge med-badge--out">Out of stock</span>
            <?php endif; ?>
        </div>

        <h3 class="med-card__name"><?php echo htmlspecialchars($med['name']); ?></h3>

        <p class="med-card__vendor"><?php echo htmlspecialchars($med['vendor_name']); ?></p>

        <?php if (!empty($med['category_name'])): ?>
            <p class="med-card__category">
                <a href="index.php?category=<?php echo (int)$med['category_id']; ?>">
                    <?php echo htmlspecialchars($med['category_name']); ?>
                </a>
            </p>
        <?php endif; ?>

        <div class="med-card__footer">
            <span class="med-card__price">৳ <?php echo number_format((float)$med['price'], 2); ?></span>
            <?php if ($inStock): ?>
                <?php if (!empty($_SESSION['user_id']) && ($_SESSION['role'] ?? '') === 'customer'): ?>
                    <div class="med-card__cart-wrap">
                        <input type="number" class="med-card__qty" value="1" min="1"
                               max="<?php echo (int)$med['availability']; ?>"
                               aria-label="Quantity">
                        <button type="button" class="btn btn--primary btn--sm med-card__cart"
                                data-id="<?php echo (int)$med['id']; ?>"
                                data-stock="<?php echo (int)$med['availability']; ?>">
                            Add to Cart
                        </button>
                    </div>
                <?php else: ?>
                    <a href="index.php?page=login" class="btn btn--primary btn--sm">Add to Cart</a>
                <?php endif; ?>
            <?php else: ?>
                <button class="btn btn--ghost btn--sm" disabled>Unavailable</button>
            <?php endif; ?>
        </div>
    </div>
</div>
