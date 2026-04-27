# RBAC Architecture Diagram

## Route Protection Flow

```
                            ┌─────────────────────────┐
                            │   User Request          │
                            └────────┬────────────────┘
                                     │
                                     ▼
                            ┌─────────────────────────┐
                            │  Is User Logged In?     │
                            │  (auth middleware)      │
                            └────────┬────────────────┘
                                     │
                    ┌────────────────┼────────────────┐
                    │                                 │
                    ▼ NO                              ▼ YES
         ┌──────────────────────┐         ┌──────────────────────┐
         │  Redirect to /login  │         │  Check User Role     │
         │  (302 Found)         │         │  (CheckRole mdware)  │
         └──────────────────────┘         └──────┬───────────────┘
                                                  │
                                    ┌─────────────┴─────────────┐
                                    │                           │
                                    ▼                           ▼
                            ┌──────────────────┐      ┌──────────────────┐
                            │  Is Owner Role?  │      │  Role Matches?   │
                            └────┬─────────┬───┘      └────┬─────────┬───┘
                                 │ YES    │ NO             │ YES    │ NO
                    ┌────────────┘        │      ┌────────┘        │
                    │                     │      │                 │
                    ▼                     ▼      ▼                 ▼
         ┌─────────────────────┐  ┌───────────────────┐  ┌─────────────────┐
         │  ✅ ALLOW REQUEST   │  │  403 Forbidden    │  │  403 Forbidden  │
         │  (Grant Access)     │  │  (Unauthorized)   │  │  (Unauthorized) │
         └─────────────────────┘  └───────────────────┘  └─────────────────┘
```

---

## Route Grouping Structure

```
┌─────────────────────────────────────────────────────────────────┐
│  web.php - Route Configuration                                  │
└─────────────────────────────────────────────────────────────────┘
                                │
                ┌───────────────┼───────────────┐
                │               │               │
                ▼               ▼               ▼
        ┌──────────────┐  ┌─────────────────────────────┐
        │ Public       │  │ Protected Routes            │
        │ Routes       │  │ middleware('auth')          │
        ├──────────────┤  └─────────────┬───────────────┘
        │ GET /        │                │
        │ GET /login   │    ┌───────────┼───────────────────────────┐
        │ POST /login  │    │           │           │               │
        │ POST /logout │    ▼           ▼           ▼               ▼
        └──────────────┘  ┌──────────┐ ┌────────┐ ┌──────────┐ ┌─────────────┐
                          │Dashboard │ │Branches│ │Products  │ │  Stocks &   │
                          │Routes    │ │Group   │ │Group     │ │  Movements  │
                          │(role)    │ │(r:o,m) │ │(r:o,m,w) │ │  (r:o,m,w)  │
                          └────┬─────┘ └────┬───┘ └────┬─────┘ └────┬────────┘
                               │            │          │            │
                    ┌──────────┴────────────┴──────────┴────────────┘
                    │
                    ▼
        ┌──────────────────────────────────┐
        │  Each Group has middleware       │
        │  - prefix (auto route prefix)    │
        │  - name (auto name prefix)       │
        │  - middleware (role check)       │
        └──────────────────────────────────┘
```

---

## Role Hierarchy & Permissions

```
                        ┌─────────────────────┐
                        │   OWNER (🔑)        │
                        │   Super Admin       │
                        │   Bypass all checks │
                        └──────────┬──────────┘
                                   │
                    ┌──────────────┼──────────────┐
                    │              │              │
                    ▼              ▼              ▼
        ┌──────────────────┐ ┌────────────┐ ┌────────────┐
        │ MANAGER (👔)     │ │ WAREHOUSE  │ │ FINANCE    │
        │ - Branches ✅    │ │ (📦)       │ │ (💰)       │
        │ - Products ✅    │ │ - Products │ │ - Dash ✅  │
        │ - Stocks ✅      │ │   (no del) │ │ - Rpts ✅  │
        │ - Movements ✅   │ │ - Stocks   │ │ - POS ❌   │
        │ - Audit ✅       │ │   (no del) │ │ - Stock ❌ │
        └──────────────────┘ │ - Movements│ └────────────┘
                             │ - Audit ❌ │
                             └────────────┘
                                    │
                                    ▼
                        ┌──────────────────────┐
                        │ CASHIER (🛒)         │
                        │ - Cashier Dash ✅    │
                        │ - POS ✅             │
                        │ - Stock View ❌      │
                        └──────────────────────┘
```

---

## Middleware Stack Visualization

```
Request
   │
   ▼
┌────────────────────────────┐
│ Web Middleware Group       │
│ - Session                  │
│ - CSRF Protection          │
│ - Cookie Encryption        │
└────────┬───────────────────┘
         │
         ▼
┌────────────────────────────┐
│ auth Middleware            │
│ Checks: Is logged in?      │
│ If NO → Redirect login     │
│ If YES → Continue          │
└────────┬───────────────────┘
         │
         ▼
┌────────────────────────────┐
│ role Middleware            │
│ Checks: Does user have     │
│ required role?             │
│ If NO → 403 Forbidden      │
│ If YES → Call Controller   │
└────────┬───────────────────┘
         │
         ▼
┌────────────────────────────┐
│ Controller Method          │
│ (if all middleware pass)   │
└────────────────────────────┘
```

---

## Route Example Breakdown

```php
Route::middleware('auth')->group(function () {                // ← Auth group
    Route::prefix('products')                                 // ← URL prefix
        ->name('products.')                                   // ← Name prefix
        ->middleware('role:owner,manager,warehouse')         // ← Role check
        ->group(function () {
            Route::get('/', [ProductController::class, 'index'])->name('index');
                    ↑                                             ↑
                    │                                             └─ route name: products.index
                    └─ HTTP method

            // Results in:
            // Route name: products.index
            // Route URL: /products
            // Required: auth + (owner OR manager OR warehouse)
        });
});
```

---

## Decision Matrix

```
Is User Authenticated?
│
├─ NO  → 302 Redirect to /login
│
└─ YES → Check if Owner?
    │
    ├─ YES → ✅ Allow (bypass role check)
    │
    └─ NO  → Check if Role Matches?
        │
        ├─ YES → ✅ Allow Request
        │
        └─ NO  → 403 Forbidden Error
```

---

## File Structure Overview

```
app/
├── Http/
│   ├── Kernel.php                  ← Register 'role' middleware
│   ├── Middleware/
│   │   ├── CheckRole.php           ← Role checking logic
│   │   └── Authenticate.php        ← Auth middleware
│   └── Controllers/
│       ├── ProductController.php   ← Protected by role:owner,manager,warehouse
│       ├── StockController.php     ← Protected by role:owner,manager,warehouse
│       ├── BranchController.php    ← Protected by role:owner,manager
│       └── ...
│
routes/
└── web.php                          ← Route definitions with grouping & RBAC

Models/
└── User.php                         ← Has 'role' attribute
```

---

## Complete Flow Example

```
Scenario: Warehouse user accesses /stocks

1. User clicks "Lihat Stok" link
   └─ GET /stocks?branch_id=1

2. Router matches to stocks.index route
   └─ Checks middlewares in order:
      a. 'web' group middlewares (session, CSRF, etc)
      b. 'auth' middleware → User logged in? YES ✓
      c. 'role:owner,manager,warehouse' → User is warehouse? YES ✓

3. All middleware pass
   └─ Calls: StockController@index($request)

4. Controller executes
   └─ Fetches stocks for selected branch
   └─ Returns view with stock data

5. Response sent to user
   └─ HTTP 200 OK with stocks list

---

Alternative Scenario: Warehouse user accesses /branches

1. User tries to access /branches (somehow)

2. Router matches to branches.index route
   └─ Checks middlewares:
      a. 'web' group middlewares ✓
      b. 'auth' middleware ✓
      c. 'role:owner,manager' → User is warehouse? NO ✗

3. CheckRole middleware fails
   └─ Aborts with 403
   └─ Shows: "Anda tidak memiliki akses ke halaman ini"

4. HTTP 403 Forbidden response sent
   └─ User cannot access branches list
```

---

## Benefits Visualization

### Before Refactoring
```
  Complexity: ▓▓▓▓▓▓▓▓▓░ (90%)
  Code DRY: ▓▓▓░░░░░░░ (30%)
  Security: ▓▓▓▓░░░░░░ (40%)
  Maintainability: ▓▓▓░░░░░░░ (30%)
```

### After Refactoring
```
  Complexity: ▓▓▓░░░░░░░ (30%)
  Code DRY: ▓▓▓▓▓▓▓▓▓░ (90%)
  Security: ▓▓▓▓▓▓▓▓▓░ (90%)
  Maintainability: ▓▓▓▓▓▓▓▓▓░ (90%)
```

