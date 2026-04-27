# RBAC Quick Reference Card

## Cheat Sheet 🎯

### Adding New Routes with RBAC

#### Template:
```php
Route::prefix('feature-name')
    ->name('feature.')
    ->middleware('role:owner,manager')  // ← Change roles as needed
    ->group(function () {
        Route::get('/', [FeatureController::class, 'index'])->name('index');
        Route::post('/', [FeatureController::class, 'store'])->name('store');
        Route::put('/{feature}', [FeatureController::class, 'update'])->name('update');
        Route::delete('/{feature}', [FeatureController::class, 'destroy'])->name('destroy');
    });
```

### Role Reference

| Role | Can Access | Cannot Access |
|------|-----------|---------------|
| **owner** | Everything | Nothing (super admin) |
| **manager** | Branches, Products, Stocks, Audit | Nothing (except above) |
| **warehouse** | Products, Stocks | Branches, Audit |
| **finance** | Finance Dashboard | Everything else |
| **cashier** | Cashier Dashboard | Everything else |

### Middleware Usage

```php
// Single role
->middleware('role:owner')

// Multiple roles (OR logic)
->middleware('role:owner,manager')

// Owner auto-passes, others need exact match
```

### Generated Route Names

```php
// Route prefix 'products' + name 'products.'
Route::prefix('products')->name('products.')->group(function () {
    Route::get('/', ...)->name('index');
    // Generated route name: 'products.index'
    // Used as: route('products.index')
});
```

---

## Common Tasks

### ❓ Check Current Route
```blade
{{-- In controller --}}
{{ Route::currentRouteName() }}  // Returns: 'products.index'

{{-- In view --}}
{{ Route::currentRouteName() }}
```

### ❓ Generate URL from Route
```blade
{{ route('products.index') }}           // /products
{{ route('products.edit', ['product' => 1]) }}  // /products/1/edit
{{ route('stocks.destroy', ['stock' => 1]) }}   // /stocks/1 (DELETE)
```

### ❓ Check User Role
```php
// In controller
if (auth()->user()->role === 'owner') {
    // ...
}

// In blade view
@if(auth()->user()->role === 'manager')
    <div>Manager only content</div>
@endif

// Case-insensitive check
if (strtolower(auth()->user()->role) === 'owner') {
    // ...
}
```

### ❓ List All Routes
```bash
# All routes
php artisan route:list

# Routes for specific feature
php artisan route:list --name=products

# Show route details
php artisan route:list --path=/products
```

### ❓ Test Specific Route
```bash
# In tinker
php artisan tinker

# Check if route exists
route('products.index')

# Get route info
Route::getRoutes()
```

---

## Middleware Stack Order

```
1. Global Middleware (global.php)
2. 'web' group middleware (Kernel.php)
   - EncryptCookies
   - AddQueuedCookiesToResponse
   - StartSession
   - ShareErrorsFromSession
   - VerifyCsrfToken
   - SubstituteBindings
3. 'auth' middleware (route group)
   - Redirects to login if not authenticated
4. 'role:x,y,z' middleware (route group)
   - Aborts 403 if role doesn't match
5. Controller method
```

---

## Error Codes

| Code | Meaning | Fix |
|------|---------|-----|
| **302** | Redirect to login | User not authenticated |
| **403** | Forbidden | Wrong role for this route |
| **404** | Not found | Route doesn't exist |
| **419** | CSRF token mismatch | Session expired or token invalid |
| **500** | Server error | Check logs: `storage/logs/` |

---

## Testing Authorization

```php
// Test user can access route
$user = User::factory()->create(['role' => 'manager']);
$this->actingAs($user)
    ->get('/products')
    ->assertStatus(200);

// Test user cannot access route
$warehouse = User::factory()->create(['role' => 'warehouse']);
$this->actingAs($warehouse)
    ->get('/branches')
    ->assertStatus(403);

// Test redirects to login if not authenticated
$this->get('/products')
    ->assertRedirect('/login');
```

---

## Routes Summary (34 total)

### By Feature:
- **Public**: 5 routes (login, logout, etc)
- **Dashboard**: 5 routes (one per role)
- **Branches**: 7 routes (role: owner,manager)
- **Products**: 5 routes (role: owner,manager,warehouse)
- **Stocks**: 5 routes (role: owner,manager,warehouse)
- **Stock Movements**: 2 routes (role: owner,manager,warehouse)
- **Audit Logs**: 1 route (role: owner,manager)

### By Protection:
- **Unprotected**: 5 (public routes)
- **Auth Only**: 0 (all protected routes have role check)
- **With Role Check**: 29

---

## Troubleshooting

### ❌ Getting 403 when should be 200
```
Check:
1. Is user logged in? (not getting redirected to login)
2. What's user role? Auth::user()->role
3. Does route require this role? route:list
4. Is role name exactly matching? (lowercase, no spaces)
```

### ❌ Route not found (404)
```
Check:
1. Is route defined in web.php?
2. Is route name correct? route('feature.action')
3. Run: php artisan route:cache
```

### ❌ Middleware not applied
```
Check:
1. php artisan config:cache --force
2. Check Kernel.php for middleware alias
3. Check route definition for middleware call
```

---

## Best Practices

✅ **DO:**
- Always use role middleware on protected routes
- Keep role names lowercase in database
- Use route names (never hardcode /urls)
- Test authorization for each role
- Log unauthorized access attempts

❌ **DON'T:**
- Mix auth middleware with individual route checks
- Hardcode URLs in views/controllers (use route())
- Trust only controller-level authorization
- Add roles in comments only
- Forget to test edge cases

---

## File Locations

| Component | Location |
|-----------|----------|
| Role Middleware | `app/Http/Middleware/CheckRole.php` |
| Middleware Registration | `app/Http/Kernel.php` |
| Routes Definition | `routes/web.php` |
| Full Documentation | `RBAC_DOCUMENTATION.md` |
| Testing Guide | `RBAC_TESTING_GUIDE.md` |
| Architecture | `RBAC_ARCHITECTURE_DIAGRAM.md` |

---

## Quick Links

- Route File: `/routes/web.php`
- Middleware: `/app/Http/Middleware/CheckRole.php`
- Kernel: `/app/Http/Kernel.php`
- User Model: `/app/Models/User.php`

---

## Support

📚 **Documentation Files:**
- RBAC_DOCUMENTATION.md (Complete guide)
- RBAC_TESTING_GUIDE.md (Testing procedures)
- RBAC_ARCHITECTURE_DIAGRAM.md (Visual diagrams)

🔗 **Laravel Docs:**
- https://laravel.com/docs/middleware
- https://laravel.com/docs/routing
- https://laravel.com/docs/authorization

