# Superadmin Role & Permission System Documentation

## Overview

This system provides granular role-based access control (RBAC) for admin users. The superadmin (user ID 1) can create other admin users and assign specific permissions, controlling which sidebar menu items and features they can access.

---

## Features

### 1. Superadmin Role

-   User ID 1 is automatically designated as the superadmin
-   Has full access to all permissions by default
-   Can create, edit, and delete other admin users
-   Cannot be edited or deleted through the admin interface (protected)

### 2. Permission System

The system supports **10 granular permissions** corresponding to sidebar menu items:

| Permission Key     | Label              | Description                                            |
| ------------------ | ------------------ | ------------------------------------------------------ |
| `dashboard`        | Dashboard          | Access to main dashboard and statistics                |
| `users`            | Users              | Manage regular users (view, edit, credit/debit wallet) |
| `sims`             | User Sims          | View and manage user SIM cards                         |
| `plans`            | Subscription Plans | Create, edit, and manage subscription plans            |
| `subscriptions`    | Subscriptions      | View and manage user subscriptions, renewals           |
| `payments`         | Payments           | View payment history and transactions                  |
| `networks`         | Networks           | Manage network providers (MTN, Airtel, etc.)           |
| `reports`          | Reports            | Access analytics and reports                           |
| `settings`         | Settings           | Configure system settings                              |
| `admin_management` | Admin Management   | Create and manage other admins (superadmin only)       |

### 3. Dynamic Sidebar

-   Sidebar menu items are shown/hidden based on user permissions
-   If an admin doesn't have permission for a section, it won't appear in their sidebar
-   Provides clean, focused interface for each admin role

### 4. Route Protection

-   All admin routes are protected by permission middleware
-   Attempting to access unauthorized routes results in a 403 Forbidden error
-   Prevents direct URL access to restricted features

---

## Database Schema

### Users Table Additions

```sql
is_superadmin  BOOLEAN DEFAULT FALSE  -- Designates superadmin status
permissions    JSON NULL               -- Stores array of permission keys
```

### Example Permission Data

```json
["dashboard", "users", "subscriptions", "payments"]
```

---

## Usage Guide

### For Superadmin (User ID 1)

#### Creating a New Admin

1. Navigate to **Admin Management** in the sidebar (crown icon)
2. Click **"Create Admin"** button
3. Fill in the form:
    - **Full Name**: Admin's full name
    - **Email**: Unique email address
    - **Password**: Minimum 8 characters
    - **Confirm Password**: Must match password
    - **Permissions**: Check all permissions this admin should have
4. Use **Select All** or **Deselect All** buttons for quick permission selection
5. Click **"Create Admin"**

#### Editing an Admin

1. Go to **Admin Management**
2. Find the admin in the table
3. Click **"Edit"** button
4. Update name, email, permissions, or password (optional)
5. Click **"Update Admin"**

**Note**: The superadmin (User ID 1) shows as "Protected" and cannot be edited or deleted.

#### Deleting an Admin

1. Go to **Admin Management**
2. Find the admin in the table
3. Click **"Delete"** button
4. Confirm the deletion

**Note**: Superadmin cannot be deleted.

---

## Implementation Details

### User Model Methods

```php
// Check if user is the superadmin
auth()->user()->isSuperAdmin()
// Returns: true only if user ID is 1 AND is_superadmin flag is true

// Check single permission
auth()->user()->hasPermission('users')
// Returns: true if user has the permission (superadmin always returns true)

// Check if has any of multiple permissions
auth()->user()->hasAnyPermission(['users', 'sims'])
// Returns: true if user has at least one of the permissions

// Check if has all permissions
auth()->user()->hasAllPermissions(['users', 'sims'])
// Returns: true only if user has all specified permissions

// Get all available permissions (static)
User::getAllPermissions()
// Returns: Array of all permission keys with labels
```

### Blade Permission Checks

In views, wrap sections with permission checks:

```blade
@if(auth()->user()->hasPermission('users'))
    <!-- Users menu item or content -->
@endif
```

Example from admin sidebar:

```blade
<!-- Users Management -->
@if(auth()->user()->hasPermission('users'))
<a href="{{ route('admin.users') }}" class="...">
    <i class="fas fa-users mr-3"></i>
    Users
</a>
@endif
```

### Route Protection

Routes are protected using the `permission` middleware:

```php
// Single route
Route::get('/users', [AdminController::class, 'users'])
    ->middleware(['permission:users'])
    ->name('users');

// Route group
Route::middleware(['permission:users'])->group(function () {
    Route::get('/users', [AdminController::class, 'users'])->name('users');
    Route::get('/users/{user}', [AdminController::class, 'showUser'])->name('users.show');
});
```

### Controller Protection

In controllers, additional checks can be added:

```php
public function admins(Request $request)
{
    // Only superadmin can access
    if (!auth()->user()->isSuperAdmin()) {
        abort(403, 'Unauthorized. Superadmin access required.');
    }

    // ... rest of method
}
```

---

## Permission Scenarios

### Scenario 1: Customer Support Admin

**Permissions**: `dashboard`, `users`, `sims`, `subscriptions`

**Can Access**:

-   Dashboard (view statistics)
-   Users (view, edit, credit/debit wallet)
-   User Sims (view and manage)
-   Subscriptions (view, renew, manage)

**Cannot Access**:

-   Plans, Payments, Networks, Reports, Settings, Admin Management

**Use Case**: Handle customer inquiries, manage user accounts, renew subscriptions

---

### Scenario 2: Financial Admin

**Permissions**: `dashboard`, `payments`, `reports`

**Can Access**:

-   Dashboard (financial overview)
-   Payments (view all transactions)
-   Reports (generate financial reports)

**Cannot Access**:

-   Users, Sims, Plans, Subscriptions, Networks, Settings, Admin Management

**Use Case**: Monitor finances, generate reports, reconcile payments

---

### Scenario 3: Technical Admin

**Permissions**: `dashboard`, `networks`, `plans`, `settings`

**Can Access**:

-   Dashboard (system health)
-   Networks (manage providers)
-   Plans (configure subscription plans)
-   Settings (system configuration)

**Cannot Access**:

-   Users, Sims, Subscriptions, Payments, Reports, Admin Management

**Use Case**: Configure system, manage network integrations, update plans

---

### Scenario 4: Full Admin (Not Superadmin)

**Permissions**: All except `admin_management`

**Can Access**: Everything except creating/managing other admins

**Cannot Access**: Admin Management

**Use Case**: Senior staff member with full operational access but cannot create admins

---

## Security Features

### 1. Superadmin Protection

-   User ID 1 is protected at database and application level
-   Cannot be edited or deleted through UI
-   Migration automatically sets user ID 1 as superadmin

### 2. Permission Validation

-   Middleware checks permissions before route access
-   Invalid permission attempts result in 403 Forbidden
-   Form validation requires at least one permission when creating admins

### 3. Role Verification

-   All admin management methods check `isSuperAdmin()` status
-   Double verification: user ID must be 1 AND is_superadmin flag must be true
-   Prevents privilege escalation

### 4. Cascade Protection

-   Superadmin cannot edit/delete themselves
-   Regular admins cannot access admin management routes
-   Permission checks occur at multiple layers (middleware, controller, view)

---

## Admin Management Interface

### Admin List View

Displays:

-   Admin avatar (initial letter)
-   Name and email
-   Role badge (Superadmin with crown icon, or Admin)
-   Permission chips showing granted permissions
-   Creation date
-   Action buttons (Edit/Delete) or "Protected" label

Features:

-   Search functionality (by name or email)
-   Statistics cards showing:
    -   Total Admins
    -   Superadmins count (always 1)
    -   Regular Admins count
-   Pagination for large admin lists

### Create Admin Form

Fields:

-   Full Name (required)
-   Email Address (required, unique)
-   Password (required, min 8 chars)
-   Confirm Password (required, must match)
-   Permissions (checkboxes, at least one required)

Helpers:

-   Select All / Deselect All buttons
-   Visual permission grid (2 columns)
-   Validation error messages

### Edit Admin Form

Same as create form with additions:

-   Admin info card showing current details
-   Password is optional (leave blank to keep current)
-   Pre-selected permissions
-   Cannot edit superadmin (redirects with error)

---

## Testing the System

### Test Case 1: Create Limited Admin

1. Login as superadmin (user ID 1)
2. Go to Admin Management
3. Create admin with only "Users" and "Subscriptions" permissions
4. Logout and login as new admin
5. **Expected**: Sidebar shows only Dashboard, Users, and Subscriptions
6. Try accessing `/admin/payments` directly
7. **Expected**: 403 Forbidden error

### Test Case 2: Permission Updates

1. Login as superadmin
2. Edit an existing admin
3. Remove "Users" permission, add "Networks" permission
4. Have admin logout and login again
5. **Expected**: Users menu disappears, Networks menu appears

### Test Case 3: Superadmin Protection

1. Login as superadmin
2. Go to Admin Management
3. Try to edit/delete superadmin (User ID 1)
4. **Expected**: Shows "Protected" instead of action buttons

### Test Case 4: Middleware Protection

1. Create admin with no "payments" permission
2. Login as that admin
3. Try accessing `/admin/payments` via URL
4. **Expected**: 403 Forbidden error page

---

## Common Permission Combinations

| Role                   | Permissions                           | Use Case                  |
| ---------------------- | ------------------------------------- | ------------------------- |
| **Support Agent**      | dashboard, users, sims                | Basic customer support    |
| **Senior Support**     | dashboard, users, sims, subscriptions | Advanced customer support |
| **Accountant**         | dashboard, payments, reports          | Financial oversight       |
| **Technical Admin**    | dashboard, networks, plans, settings  | System configuration      |
| **Operations Manager** | All except admin_management           | Full operational control  |
| **Superadmin**         | All (automatic)                       | Full system access        |

---

## Troubleshooting

### Issue: Admin can't see any menu items

**Solution**: Ensure admin has at least the "dashboard" permission assigned.

### Issue: Permission changes not taking effect

**Solution**: Have the admin logout and login again. Permissions are loaded on login.

### Issue: Can't access Admin Management menu

**Solution**: Only user ID 1 (superadmin) has access to Admin Management. This is by design.

### Issue: Getting 403 error on valid routes

**Solution**: Check if the admin has the required permission for that route. Review permission assignments.

### Issue: Want to change superadmin user

**Solution**: Currently, only user ID 1 can be superadmin. To change:

1. Use database tool (phpMyAdmin, Tinker, etc.)
2. Set desired user's `is_superadmin` = 1 and `id` = 1 (requires careful database manipulation)
3. **Recommended**: Keep user ID 1 as permanent superadmin

---

## API Reference

### Routes

#### Admin Management Routes

All routes require `auth`, `admin`, and `permission:admin_management` middleware.

```
GET    /admin/admins                  List all admins
GET    /admin/admins/create           Show create form
POST   /admin/admins                  Store new admin
GET    /admin/admins/{admin}/edit     Show edit form
PUT    /admin/admins/{admin}          Update admin
DELETE /admin/admins/{admin}          Delete admin
```

### Controller Methods

```php
AdminController::admins()           // List admins with search
AdminController::createAdmin()      // Show create form
AdminController::storeAdmin()       // Validate and create admin
AdminController::editAdmin()        // Show edit form (blocks superadmin)
AdminController::updateAdmin()      // Validate and update admin
AdminController::destroyAdmin()     // Delete admin (blocks superadmin)
```

### Middleware

```php
CheckAdminPermission::handle($request, $next, $permission)
// Checks if authenticated admin user has the specified permission
// Returns 403 if permission denied
```

---

## Best Practices

1. **Principle of Least Privilege**: Only assign permissions users actually need
2. **Regular Audits**: Periodically review admin permissions
3. **Single Superadmin**: Keep only one superadmin account (user ID 1)
4. **Strong Passwords**: Enforce strong passwords for all admin accounts
5. **Activity Logging**: Consider adding admin action logging (future enhancement)
6. **Permission Documentation**: Document which permissions each role should have
7. **Onboarding**: Train new admins on their specific permissions
8. **Backup Superadmin**: Have a secure backup of superadmin credentials

---

## Future Enhancements

Potential improvements to consider:

1. **Activity Logging**: Track admin actions with timestamps
2. **Permission Groups**: Predefined role templates (Support, Finance, etc.)
3. **Time-Based Access**: Set expiration dates for admin accounts
4. **IP Restrictions**: Limit admin access by IP address
5. **Two-Factor Authentication**: Add 2FA for admin accounts
6. **Custom Permissions**: Allow creating custom permissions beyond the 10 default
7. **Permission Inheritance**: Create permission hierarchies
8. **Audit Dashboard**: Visual report of admin activities and permission usage

---

## Support

For questions or issues with the permission system:

1. Check this documentation first
2. Review the code comments in:
    - `app/Models/User.php` (permission methods)
    - `app/Http/Middleware/CheckAdminPermission.php`
    - `app/Http/Controllers/Admin/AdminController.php` (admin management)
3. Test with different permission combinations
4. Ensure migrations have run: `php artisan migrate`

---

**Version**: 1.0
**Last Updated**: November 2, 2025
**Author**: Holluman ISP Management System
