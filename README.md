# Task 3 — Customer Cart, Checkout & Orders
**Student:** 23-54596-3 | **Course:** Web Technologies | **Group:** 11

---

## Features Implemented

| # | Grading Criterion | Implementation |
|---|---|---|
| 1 | **Basic Web Security** | CSRF tokens on all forms/AJAX, prepared statements (PDO), XSS via `htmlspecialchars()`, passwords from Task 1 |
| 2 | **UI (HTML/CSS)** | Responsive CSS Grid/Flexbox layout, DM Serif Display + DM Sans fonts, mobile-friendly |
| 3 | **Feature Completeness** | Add to Cart, Cart CRUD, Checkout, Invoice, Payment Selection, Order Confirmation, My Orders |
| 4 | **DB** | Uses shared schema; foreign keys, transactions for order placement |
| 5 | **Auth (Session/Cookie)** | `require_customer()` guard on all pages; redirects unauthenticated users |
| 6 | **MVC** | `controllers/`, `models/`, `views/`, `config/` separation |
| 7 | **JS Validation** | Quantity (min 1, max stock), address not empty, payment method selected — all client-side |
| 8 | **PHP Validation** | Stock check, medicine existence, address required, payment method whitelist, CSRF verification |
| 9 | **AJAX/JSON** | `api/cart_add.php`, `api/cart_update.php`, `api/cart_remove.php` — all return JSON |
| 10 | **Git Contribution** | Feature branch `feature/task3-23-54596-3` — ≥3 commits, PR into main |

---

## File Structure

```
task3/
├── config/
│   ├── database.php          ← PDO singleton
│   └── app.php               ← Constants, helpers (e(), csrf, redirect, guards)
├── controllers/
│   ├── CartController.php    ← Cart page
│   ├── CheckoutController.php ← Checkout, payment, place order
│   └── OrderController.php   ← Confirmation, my orders
├── models/
│   ├── CartModel.php         ← Cart CRUD (DB)
│   └── OrderModel.php        ← Order placement (transaction)
├── views/
│   ├── header.php            ← Shared navbar + session alert
│   ├── footer.php
│   ├── cart/
│   │   └── index.php         ← Cart table with AJAX qty/remove
│   ├── checkout/
│   │   ├── index.php         ← Address form + invoice preview
│   │   └── payment.php       ← Payment method selection
│   └── orders/
│       ├── confirmation.php  ← Order confirmation receipt
│       └── my_orders.php     ← All orders list
├── api/
│   ├── cart_add.php          ← POST /api/cart_add.php
│   ├── cart_update.php       ← POST /api/cart_update.php
│   └── cart_remove.php       ← DELETE /api/cart_remove.php
├── public/
│   └── css/style.css
├── index.php                 ← Front controller / router
├── schema.sql                ← Shared DB schema
└── .htaccess
```

---

## Setup

1. **Import** `schema.sql` into MySQL.
2. **Configure** `config/database.php` with your DB credentials.
3. **Set** `BASE_URL` in `config/app.php` to match your server path.
4. Place project folder at your Apache/Nginx web root (e.g. `htdocs/task3/`).
5. Ensure `public/uploads/medicines/` is writable (used by Task 2 for medicine images).

---

## AJAX Endpoints

| Endpoint | Method | Body | Response |
|---|---|---|---|
| `/api/cart_add.php` | POST | `{ medicine_id, quantity }` | `{ success, cart_count }` |
| `/api/cart_update.php` | POST | `{ cart_id, quantity }` | `{ success, cart_count, cart_total }` |
| `/api/cart_remove.php` | DELETE | `{ cart_id }` | `{ success, cart_count, cart_total }` |

All AJAX calls require `X-CSRF-TOKEN` header.

---

## Git Workflow

```bash
git checkout main
git pull origin main
git checkout -b feature/task3-23-54596-3

# Commit 1: initial structure
git add config/ models/ controllers/
git commit -m "feat(task3): add MVC skeleton, CartModel, OrderModel, config"

# Commit 2: views
git add views/ public/
git commit -m "feat(task3): add cart, checkout, payment, confirmation views + CSS"

# Commit 3: AJAX API endpoints
git add api/ index.php schema.sql .htaccess
git commit -m "feat(task3): add AJAX cart endpoints, front controller, schema"

git push origin feature/task3-23-54596-3
# → Open Pull Request into main
```
