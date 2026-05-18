<?php include __DIR__ . '/partials/navbar.php'; ?>

<div class="shop-wrap">

    <!--  Hero  -->
    <!-- <section class="shop-hero">
        <div class="shop-hero__inner">
            <div class="shop-hero__text">
                <?php if (!empty($_SESSION['user_id'])): ?>
                    <h1>Welcome back, <?php echo e($_SESSION['name']); ?>!</h1>
                    <p>Browse our latest medicines and find what you need.</p>
                <?php else: ?>
                    <h1>Your Online Medicine Shop</h1>
                    <p>Browse medicines by category, compare prices, and order from home.</p>
                    <div class="shop-hero__cta">
                        <a href="index.php?page=register" class="btn btn--primary">Get Started</a>
                        <a href="index.php?page=login"    class="btn btn--outline">Sign In</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section> -->

    <section class="shop-hero">
    </section>

    <!--  Search & Filter bar  -->
    <section class="shop-search-bar">
        <div class="shop-inner">
            <div class="search-row">
                <div class="search-input-wrap">
                    <svg class="search-icon" viewBox="0 0 20 20" fill="currentColor" width="18" height="18">
                        <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd"/>
                    </svg>
                    <input type="text" id="search-q" class="search-input" placeholder="Search medicines…" autocomplete="off"
                           value="<?php echo e($_GET['q'] ?? ''); ?>">
                </div>

                <select id="filter-vendor" class="search-select">
                    <option value="">All Vendors</option>
                    <?php foreach ($vendors as $v): ?>
                        <option value="<?php echo e($v); ?>"
                            <?php echo (($_GET['vendor'] ?? '') === $v) ? 'selected' : ''; ?>>
                            <?php echo e($v); ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <select id="filter-genre" class="search-select">
                    <option value="">All Categories</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?php echo $cat['id']; ?>"
                            <?php echo (($activeCategoryId ?? 0) === (int)$cat['id']) ? 'selected' : ''; ?>>
                            <?php echo e($cat['name']); ?>
                            (<?= e($cat['category_type']) ?>)
                        </option>
                    <?php endforeach; ?>
                </select>

                <select id="filter-type" class="search-select">
                    <option value="">Liquid &amp; Solid</option>
                    <option value="liquid" <?php echo (($activeType ?? '') === 'liquid') ? 'selected' : ''; ?>>Liquid</option>
                    <option value="solid"  <?php echo (($activeType ?? '') === 'solid')  ? 'selected' : ''; ?>>Solid</option>
                </select>

                <button id="btn-clear-search" class="btn btn--ghost btn--sm" type="button">Clear</button>
            </div>
        </div>
    </section>

    <div class="shop-inner shop-layout">

        <!--  Sidebar: Categories  -->
        <aside class="cat-sidebar">
            <h2 class="cat-sidebar__title">Categories</h2>

            <?php
            $liquidCats = array_filter($categories, fn($c) => $c['category_type'] === 'liquid');
            $solidCats  = array_filter($categories, fn($c) => $c['category_type'] === 'solid');
            ?>

            <a href="index.php"
               class="cat-item <?php echo ($activeCategoryId === 0 && $activeType === '') ? 'cat-item--active' : ''; ?>">
                <span class="cat-item__name">All Medicines</span>
                <span class="cat-item__count"><?php echo array_sum(array_column($categories, 'medicine_count')); ?></span>
            </a>

            <?php if (!empty($liquidCats)): ?>
                <div class="cat-type-label">
                    <svg viewBox="0 0 20 20" fill="currentColor" width="14" height="14"><path d="M10 2C7 6 4 9 4 12a6 6 0 0012 0c0-3-3-6-6-10z"/></svg>
                    Liquid
                </div>
                <a href="index.php?type=liquid"
                   class="cat-item <?php echo ($activeType === 'liquid' && $activeCategoryId === 0) ? 'cat-item--active' : ''; ?>">
                    <span class="cat-item__name">All Liquid</span>
                    <span class="cat-item__count"><?php echo array_sum(array_column(array_values($liquidCats), 'medicine_count')); ?></span>
                </a>
                <?php foreach ($liquidCats as $cat): ?>
                    <a href="index.php?category=<?php echo $cat['id']; ?>"
                       class="cat-item cat-item--sub <?php echo ($activeCategoryId === (int)$cat['id']) ? 'cat-item--active' : ''; ?>">
                        <span class="cat-item__name"><?php echo e($cat['name']); ?></span>
                        <span class="cat-item__count"><?php echo $cat['medicine_count']; ?></span>
                    </a>
                <?php endforeach; ?>
            <?php endif; ?>

            <?php if (!empty($solidCats)): ?>
                <div class="cat-type-label">
                    <svg viewBox="0 0 20 20" fill="currentColor" width="14" height="14"><path d="M10 2a4 4 0 100 8 4 4 0 000-8zM2 16s0-4 8-4 8 4 8 4H2z"/></svg>
                    Solid
                </div>
                <a href="index.php?type=solid"
                   class="cat-item <?php echo ($activeType === 'solid' && $activeCategoryId === 0) ? 'cat-item--active' : ''; ?>">
                    <span class="cat-item__name">All Solid</span>
                    <span class="cat-item__count"><?php echo array_sum(array_column(array_values($solidCats), 'medicine_count')); ?></span>
                </a>
                <?php foreach ($solidCats as $cat): ?>
                    <a href="index.php?category=<?php echo $cat['id']; ?>"
                       class="cat-item cat-item--sub <?php echo ($activeCategoryId === (int)$cat['id']) ? 'cat-item--active' : ''; ?>">
                        <span class="cat-item__name"><?php echo e($cat['name']); ?></span>
                        <span class="cat-item__count"><?php echo $cat['medicine_count']; ?></span>
                    </a>
                <?php endforeach; ?>
            <?php endif; ?>
        </aside>

        <!--  Main: Medicine Grid  -->
        <main class="medicine-main">

            <!-- results header -->
            <div class="results-header">
                <span class="results-count" id="results-count">
                    <?php echo count($medicines); ?> medicine<?php echo count($medicines) !== 1 ? 's' : ''; ?> found
                </span>
                <?php if ($activeCategoryId > 0 || $activeType !== ''): ?>
                    <a href="index.php" class="results-clear">&#x2715; Clear filter</a>
                <?php endif; ?>
            </div>

            <!-- loading spinner (hidden by default) -->
            <div id="search-spinner" class="search-spinner" hidden>
                <div class="spinner"></div>
                <span>Searching…</span>
            </div>

            <!-- medicine grid -->
            <div class="medicine-grid" id="medicine-grid">
                <?php if (empty($medicines)): ?>
                    <div class="empty-state" id="empty-state">
                        <svg viewBox="0 0 64 64" fill="none" width="56" height="56">
                            <circle cx="32" cy="32" r="30" stroke="#d1d5db" stroke-width="2"/>
                            <path d="M22 32h20M32 22v20" stroke="#9ca3af" stroke-width="2.5" stroke-linecap="round"/>
                        </svg>
                        <p>No medicines found.</p>
                        <a href="index.php" class="btn btn--ghost btn--sm">Reset filters</a>
                    </div>
                <?php else: ?>
                    <?php foreach ($medicines as $med): ?>
                        <?php include __DIR__ . '/partials/medicine_card.php'; ?>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

        </main>
    </div><!-- /shop-layout -->
</div><!-- /shop-wrap -->

<script>
var IS_CUSTOMER = <?php echo json_encode(!empty($_SESSION['user_id']) && ($_SESSION['role'] ?? '') === 'customer'); ?>;

(function () {
    /*  elements  */
    var qInput = document.getElementById('search-q');
    var selVendor = document.getElementById('filter-vendor');
    var selGenre = document.getElementById('filter-genre');
    var selType = document.getElementById('filter-type');
    var grid = document.getElementById('medicine-grid');
    var spinner = document.getElementById('search-spinner');
    var countEl  = document.getElementById('results-count');
    var btnClear = document.getElementById('btn-clear-search');

    var debounceTimer = null;
    var currentXhr = null;

    function setSpinner(loading) {
        if (!spinner) return;
        spinner.hidden = !loading;
        spinner.classList.toggle('is-loading', loading);
    }

    setSpinner(false);

    /*  card template  */
    function renderCard(m) {
        var inStock  = m.availability > 0;
        var img      = m.image_path
            ? '<img src="' + escUrl(m.image_path) + '" alt="' + escHtml(m.name) + '" class="med-card__img" loading="lazy">'
            : '<div class="med-card__img-placeholder"><svg viewBox="0 0 48 48" fill="none" width="40" height="40"><rect x="8" y="14" width="32" height="22" rx="3" stroke="#d1d5db" stroke-width="2"/><path d="M24 20v10M19 25h10" stroke="#9ca3af" stroke-width="2" stroke-linecap="round"/></svg></div>';

        var catType = escCssClass(m.category_type, ['liquid', 'solid']);
        var typeBadge = catType
            ? '<span class="med-badge med-badge--' + catType + '">' + escHtml(m.category_type) + '</span>'
            : '';

        var stockBadge = inStock
            ? '<span class="med-badge med-badge--stock">In stock (' + m.availability + ')</span>'
            : '<span class="med-badge med-badge--out">Out of stock</span>';

        return '<div class="med-card">' +
            '<div class="med-card__media">' + img + '</div>' +
            '<div class="med-card__body">' +
                '<div class="med-card__badges">' + typeBadge + stockBadge + '</div>' +
                '<h3 class="med-card__name">' + escHtml(m.name) + '</h3>' +
                '<p class="med-card__vendor">' + escHtml(m.vendor_name) + '</p>' +
                '<div class="med-card__footer">' +
                    '<span class="med-card__price">৳ ' + Number(m.price).toFixed(2) + '</span>' +
                    cartActionHtml(m, inStock) +
                '</div>' +
            '</div>' +
        '</div>';
    }

    function cartActionHtml(m, inStock) {
        if (!inStock) {
            return '<button class="btn btn--ghost btn--sm" disabled>Unavailable</button>';
        }
        if (IS_CUSTOMER) {
            return '<div class="med-card__cart-wrap">' +
                '<input type="number" class="med-card__qty" value="1" min="1" max="' + m.availability + '" aria-label="Quantity">' +
                '<button type="button" class="btn btn--primary btn--sm med-card__cart" data-id="' + m.id + '" data-stock="' + m.availability + '">Add to Cart</button>' +
                '</div>';
        }
        return '<a href="index.php?page=login" class="btn btn--primary btn--sm">Add to Cart</a>';
    }

    /*  fetch & render  */
    function doSearch() {
        var q      = qInput.value.trim();
        var vendor = selVendor.value;
        var genre  = selGenre.value;
        var type   = selType.value;

        // if all empty just skip XHR and show native PHP result
        var params = new URLSearchParams({ q: q, vendor: vendor, genre: genre, type: type });

        if (currentXhr) { currentXhr.abort(); }

        setSpinner(true);
        grid.style.opacity = '0.4';

        var xhr = new XMLHttpRequest();
        currentXhr = xhr;
        xhr.open('GET', 'api/medicines/search.php?' + params.toString());
        xhr.onload = function () {
            if (currentXhr !== xhr) return;
            setSpinner(false);
            grid.style.opacity = '1';
            if (xhr.status !== 200) return;
            try {
                var res = JSON.parse(xhr.responseText);
                if (!res.success) return;
                var list = res.data;
                countEl.textContent = list.length + ' medicine' + (list.length !== 1 ? 's' : '') + ' found';
                if (list.length === 0) {
                    grid.innerHTML = '<div class="empty-state"><p>No medicines match your search.</p></div>';
                } else {
                    grid.innerHTML = list.map(renderCard).join('');
                }
            } catch (e) {}
        };
        xhr.onerror = function () {
            if (currentXhr !== xhr) return;
            setSpinner(false);
            grid.style.opacity = '1';
        };
        xhr.onabort = function () {
            if (currentXhr !== xhr) return;
            setSpinner(false);
            grid.style.opacity = '1';
        };
        xhr.send();
    }

    function scheduleSearch() {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(doSearch, 300);
    }

    /*  bind events  */
    qInput.addEventListener('input', scheduleSearch);
    selVendor.addEventListener('change', scheduleSearch);
    selGenre.addEventListener('change', scheduleSearch);
    selType.addEventListener('change', scheduleSearch);

    btnClear.addEventListener('click', function () {
        qInput.value    = '';
        selVendor.value = '';
        selGenre.value  = '';
        selType.value   = '';
        doSearch();
    });
})();
</script>
