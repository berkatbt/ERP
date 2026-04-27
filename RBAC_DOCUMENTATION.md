# Role-Based Access Control (RBAC) Documentation

## Sistem RBAC yang Diterapkan

### Middleware yang Digunakan
- **auth** - Memastikan user sudah login
- **role:owner,manager** - Mengecek apakah user memiliki role owner atau manager

### Owner Access 🔑
Owner dapat mengakses **SEMUA** route tanpa pembatasan. Owner memiliki akses penuh ke sistem.

### Available Roles
1. **Owner** - Full system access
2. **Manager** - Manage branches, products, stocks, audit logs
3. **Warehouse** - View/manage products and stocks
4. **Finance** - Financial operations (untuk modul berikutnya)
5. **Cashier** - POS operations (untuk modul berikutnya)

---

## Route Access Matrix

### Public Routes (No Authentication)
```
GET  /                 - Login page
GET  /login            - Show login form
POST /login            - Process login
POST /logout           - Logout
```

### Dashboard Routes
| Route | Method | Allowed Roles |
|-------|--------|---------------|
| /owner | GET | owner |
| /manager | GET | manager |
| /finance | GET | finance |
| /warehouse | GET | warehouse |
| /cashier | GET | cashier |

### Branch Management Routes
| Route | Method | Allowed Roles |
|-------|--------|---------------|
| /branches | GET | owner, manager |
| /branches | POST | owner, manager |
| /branches/{branch} | GET | owner, manager |
| /branches/{branch}/edit | GET | owner, manager |
| /branches/{branch} | PUT | owner, manager |
| /branches/{branch} | DELETE | owner, manager |
| /branches/{branch}/detail | GET | owner, manager |

### Product Management Routes
| Route | Method | Allowed Roles |
|-------|--------|---------------|
| /products | GET | owner, manager, warehouse |
| /products | POST | owner, manager, warehouse |
| /products/{product}/edit | GET | owner, manager, warehouse |
| /products/{product} | PUT | owner, manager, warehouse |
| /products/{product} | DELETE | owner, manager, warehouse |

### Stock Management Routes
| Route | Method | Allowed Roles |
|-------|--------|---------------|
| /stocks | GET | owner, manager, warehouse |
| /stocks | POST | owner, manager, warehouse |
| /stocks/{stock}/edit | GET | owner, manager, warehouse |
| /stocks/{stock} | PUT | owner, manager, warehouse |
| /stocks/{stock} | DELETE | owner, manager |
| /stock-movements | GET | owner, manager, warehouse |
| /stock-movements/summary | GET | owner, manager, warehouse |

### Audit Log Routes
| Route | Method | Allowed Roles |
|-------|--------|---------------|
| /audit-log | GET | owner, manager |

---

## Middleware Pengecek Role

### File: `app/Http/Middleware/CheckRole.php`

```php
// Contoh penggunaan dalam routes:
Route::post('/products', [ProductController::class, 'store'])
    ->middleware('role:owner,manager,warehouse');
```

### Fitur Middleware:
- ✅ Owner bypass semua role check (akses semua route)
- ✅ Case-insensitive role checking
- ✅ Multiple roles support (comma-separated)
- ✅ Automatic 403 error jika user tidak memiliki role yang sesuai

---

## Implementasi di Routes

### Grouping by Feature (V2)
Routes diorganisir menggunakan `prefix` dan `middleware`:

```php
Route::prefix('products')
    ->name('products.')
    ->middleware('role:owner,manager,warehouse')
    ->group(function () {
        Route::get('/', [ProductController::class, 'index'])->name('index');
        Route::post('/', [ProductController::class, 'store'])->name('store');
        // ...
    });
```

### Benefits:
- ✅ Middleware ditulis sekali, berlaku untuk semua routes di group
- ✅ Prefix dan name automatik
- ✅ Mudah di-maintain dan di-scale
- ✅ Struktur hierarchical yang jelas

---

## Controller-level Authorization (Optional)

Di controller, masih ada pengecekan authorization yang bisa ditambahkan:

```php
public function destroy(Product $product)
{
    $user = Auth::user();
    
    // Route-level middleware sudah handle ini, tapi boleh punya double check
    if (! $user || ! in_array(strtolower($user->role), ['owner', 'manager', 'warehouse'])) {
        abort(403, 'Akses tidak diizinkan.');
    }
    
    // ... rest of code
}
```

---

## Testing Authorization

### Cara test di browser:
1. Login sebagai user dengan role tertentu
2. Coba akses route yang tidak diizinkan
3. Harus dapat error 403: "Anda tidak memiliki akses ke halaman ini."

### Test Commands (optional):
```bash
# Lihat semua routes dengan middleware
php artisan route:list

# Lihat routes untuk resource tertentu
php artisan route:list --name=products

# Lihat routes dengan middleware tertentu
php artisan route:list | grep "role"
```

---

## Notes
- Middleware `auth` diaplikasikan ke semua protected routes via group
- Middleware `role` hanya diaplikasikan ke routes yang memerlukan role-specific access
- Owner memiliki bypass untuk semua role check (sumber tunggal kebenaran)
- Jika role tidak sesuai, Laravel akan return 403 Forbidden

