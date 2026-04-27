# RBAC Testing & Reference Guide

## Quick Reference Card 🎯

### Role Access Levels

```
Owner (🔑 Super Admin)
└── Access ke SEMUA routes
    └── Bypass semua role checks

Manager (👔 Administrator)
├── Branches (CRUD)
├── Products (CRUD)
├── Stocks (CRUD)
├── Stock Movements (View)
└── Audit Logs (View)

Warehouse (📦 Inventory Manager)
├── Products (View/Create/Update - NO Delete)
├── Stocks (View/Create/Update - NO Delete)
└── Stock Movements (View)

Finance (💰 Finance)
├── Finance Dashboard
└── Financial Reports (future)

Cashier (🛒 Point of Sale)
├── Cashier Dashboard
└── POS Operations (future)
```

---

## Manual Testing Checklist ✓

### Test Case 1: Owner User
```
1. Login sebagai user dengan role "owner"
2. Akses routes:
   ✅ GET /branches → 200 OK
   ✅ POST /products → 200 OK
   ✅ DELETE /stocks/1 → 200 OK
   ✅ GET /audit-log → 200 OK
3. Result: Semua accessible ✓
```

### Test Case 2: Manager User
```
1. Login sebagai user dengan role "manager"
2. Akses routes:
   ✅ GET /branches → 200 OK (allowed)
   ✅ POST /products → 200 OK (allowed)
   ✅ DELETE /stocks/1 → 200 OK (allowed)
   ✅ GET /audit-log → 200 OK (allowed)
3. Result: Semua accessible ✓
```

### Test Case 3: Warehouse User
```
1. Login sebagai user dengan role "warehouse"
2. Akses routes:
   ❌ GET /branches → 403 Forbidden (not allowed)
   ✅ GET /products → 200 OK (allowed)
   ✅ GET /stocks → 200 OK (allowed)
   ❌ DELETE /stocks/1 → 403 Forbidden (not allowed)
3. Result: Sesuai access control ✓
```

### Test Case 4: Finance User
```
1. Login sebagai user dengan role "finance"
2. Akses routes:
   ✅ GET /finance → 200 OK (own dashboard)
   ❌ GET /warehouse → 403 Forbidden (other dashboard)
   ❌ GET /products → 403 Forbidden (not allowed)
3. Result: Hanya akses dashboard sendiri ✓
```

### Test Case 5: Unauthenticated User
```
1. Tidak login / logout dulu
2. Coba akses:
   ❌ GET /branches → 302 Redirect to login
   ❌ GET /products → 302 Redirect to login
   ❌ GET /audit-log → 302 Redirect to login
3. Result: Semua redirect ke login ✓
```

---

## Automated Testing (Optional)

### Feature Test Template

```php
// tests/Feature/RBACTest.php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;

class RBACTest extends TestCase
{
    private $owner;
    private $manager;
    private $warehouse;
    
    protected function setUp(): void
    {
        parent::setUp();
        
        $this->owner = User::factory()->create(['role' => 'owner']);
        $this->manager = User::factory()->create(['role' => 'manager']);
        $this->warehouse = User::factory()->create(['role' => 'warehouse']);
    }
    
    public function test_owner_can_access_branches()
    {
        $this->actingAs($this->owner)
            ->get('/branches')
            ->assertStatus(200);
    }
    
    public function test_warehouse_cannot_access_branches()
    {
        $this->actingAs($this->warehouse)
            ->get('/branches')
            ->assertStatus(403);
    }
    
    public function test_unauthenticated_redirects_to_login()
    {
        $this->get('/branches')
            ->assertRedirect('/login');
    }
    
    public function test_warehouse_can_access_products()
    {
        $this->actingAs($this->warehouse)
            ->get('/products')
            ->assertStatus(200);
    }
}
```

### Run Test:
```bash
php artisan test tests/Feature/RBACTest.php
```

---

## Debugging RBAC Issues

### 1. User gets 403 when should be allowed
```php
// Check user's current role
$user = Auth::user();
dd($user->role); // Check the value

// Verify middleware is applied
php artisan route:list --name=branches
// Should show middleware: auth,role:owner,manager
```

### 2. Middleware not registered
```php
// Check app/Http/Kernel.php
// Should have:
'role' => \App\Http\Middleware\CheckRole::class,

// If missing, restart queue/cache:
php artisan config:cache
php artisan optimize
```

### 3. Role values inconsistent
```php
// In database, roles should be lowercase:
SELECT DISTINCT role FROM users;

// Update if needed:
UPDATE users SET role = LOWER(role);

// Or in middleware (already case-insensitive):
$userRole = strtolower(auth()->user()->role); // ← Already done
```

---

## API Integration (Future)

Untuk API routes, pattern yang sama:

```php
Route::middleware('auth:sanctum')->group(function () {
    Route::prefix('api/products')
        ->middleware('role:owner,manager,warehouse')
        ->group(function () {
            Route::get('/', [ProductController::class, 'index']);
            Route::post('/', [ProductController::class, 'store']);
        });
});
```

---

## Common Issues & Solutions

### Issue 1: "Anda tidak memiliki akses ke halaman ini" error
**Cause**: Role tidak cocok dengan yang diizinkan di route
**Solution**: 
1. Verify user role di database
2. Check route middleware untuk allowed roles
3. Pastikan role name eksak match (lowercase)

### Issue 2: Owner masih dapat 403 error
**Cause**: Middleware tidak parse 'owner' dengan benar
**Solution**: Middleware sudah handle ini, check if user role truly 'owner'

### Issue 3: Middleware not applied
**Cause**: Config cache not cleared
**Solution**:
```bash
php artisan config:cache --force
php artisan optimize
```

---

## Maintenance Checklist

- [ ] Semua routes punya middleware auth
- [ ] Sensitive routes punya role middleware
- [ ] Owner selalu bisa akses semua
- [ ] Role names consistent (lowercase in DB)
- [ ] Tests cover main RBAC scenarios
- [ ] Error messages user-friendly (dalam bahasa Indonesia)

---

## File References

| File | Purpose |
|------|---------|
| `app/Http/Middleware/CheckRole.php` | Role check implementation |
| `app/Http/Kernel.php` | Middleware registration |
| `routes/web.php` | Route definitions with RBAC |
| `RBAC_DOCUMENTATION.md` | Full RBAC documentation |
| `ROUTE_REFACTORING_CHANGELOG.md` | Changes summary |

---

## Quick Links

- [Laravel Middleware Docs](https://laravel.com/docs/middleware)
- [Laravel Authorization Docs](https://laravel.com/docs/authorization)
- [Route Groups Documentation](https://laravel.com/docs/routing#route-groups)

