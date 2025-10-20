# Subscription Status Update System

## 🎯 **Overview**

This system efficiently manages subscription status updates on shared hosting without background jobs. It uses a smart combination of on-demand checks and external cron triggers.

---

## 🚀 **How It Works**

### **1. User Dashboard Access (Automatic)**

-   When a user accesses their dashboard, their subscriptions are checked
-   Expired subscriptions are automatically updated to "expired" status
-   Uses 10-minute cache to prevent repeated checks
-   ✅ **No performance impact** - only checks that user's subscriptions

### **2. External Cron Job (Recommended)**

-   Set up an external cron service to call your endpoint
-   Updates ALL expired subscriptions across the system
-   Runs once per hour (recommended)
-   Uses 1-hour cache to prevent excessive runs

---

## 🔧 **Setup Instructions**

### **Step 1: Secure Your Cron Token**

1. Open your `.env` file
2. Find this line:

```env
CRON_TOKEN=veesta_cron_secret_2025_change_this_token
```

3. **IMPORTANT:** Change it to a strong random string:

```env
CRON_TOKEN=your_super_secret_random_token_here_12345
```

💡 **Generate a secure token using:**

```bash
php artisan tinker
>>> Str::random(40)
```

---

### **Step 2: Set Up External Cron (cron-job.org)**

#### **Option A: cron-job.org (Recommended - FREE)**

1. Go to: https://cron-job.org/
2. Sign up for a free account
3. Create a new cron job with these settings:

**Cron Job Settings:**

```
Title: Veesta - Update Expired Subscriptions
URL: https://yourdomain.com/cron/update-subscriptions?token=YOUR_CRON_TOKEN
Schedule: Every 1 hour
Method: GET
Notifications: Enable email on failure
```

**Example URL:**

```
https://veesta.com/cron/update-subscriptions?token=veesta_cron_secret_2025_change_this_token
```

#### **Option B: EasyCron (Alternative - FREE tier available)**

1. Go to: https://www.easycron.com/
2. Create a free account
3. Add new cron job:

```
Cron Expression: 0 * * * * (Every hour)
URL: https://yourdomain.com/cron/update-subscriptions?token=YOUR_CRON_TOKEN
Request Method: GET
```

#### **Option C: UptimeRobot (Monitor + Cron)**

1. Go to: https://uptimerobot.com/
2. Create monitor:

```
Monitor Type: HTTP(s)
URL: https://yourdomain.com/cron/update-subscriptions?token=YOUR_CRON_TOKEN
Monitoring Interval: 60 minutes
```

---

## 📍 **Available Endpoints**

### **1. Health Check (Public - No Token Required)**

```
URL: https://yourdomain.com/cron/health
Method: GET
Purpose: Test if cron endpoint is working
```

**Response:**

```json
{
    "status": "ok",
    "service": "Veesta Cron Service",
    "timestamp": "2025-10-20 14:30:00",
    "message": "Cron endpoint is accessible and working"
}
```

### **2. Update Subscriptions (Protected)**

```
URL: https://yourdomain.com/cron/update-subscriptions?token=YOUR_TOKEN
Method: GET
Purpose: Update all expired subscriptions
Recommended: Run every 1 hour
```

**Response:**

```json
{
    "success": true,
    "message": "Subscription statuses updated successfully",
    "updated": 5,
    "timestamp": "2025-10-20 14:30:00"
}
```

### **3. Subscription Statistics (Protected)**

```
URL: https://yourdomain.com/cron/subscription-stats?token=YOUR_TOKEN
Method: GET
Purpose: Get subscription statistics
```

**Response:**

```json
{
    "success": true,
    "stats": {
        "total_active": 150,
        "expired_needing_update": 3,
        "total_expired": 45,
        "expiring_soon": 12
    },
    "timestamp": "2025-10-20 14:30:00"
}
```

---

## 🔒 **Security Features**

1. **Token Authentication**

    - All update endpoints require valid token
    - Token is stored in `.env` file (not in code)
    - Unauthorized attempts are logged

2. **Rate Limiting (Built-in)**

    - User checks: 10-minute cache per user
    - Global checks: 1-hour cache
    - Prevents excessive database queries

3. **Logging**
    - All cron executions are logged
    - Failed attempts are tracked
    - Easy to monitor in `storage/logs/laravel.log`

---

## 📊 **Performance Impact**

### **User Dashboard:**

-   ✅ **Minimal** - Only checks that user's subscriptions
-   ✅ **Cached** - 10 minutes per user
-   ✅ **Fast** - Usually < 50ms
-   ✅ **Scales well** - Each user independent

### **Cron Job:**

-   ✅ **Efficient** - Only updates expired subscriptions
-   ✅ **Cached** - 1 hour globally
-   ✅ **Batched** - All updates in one query
-   ✅ **Non-blocking** - Runs externally

---

## 🧪 **Testing**

### **Test 1: Health Check**

```bash
curl https://yourdomain.com/cron/health
```

### **Test 2: Manual Update (with your token)**

```bash
curl https://yourdomain.com/cron/update-subscriptions?token=YOUR_TOKEN
```

### **Test 3: Check Stats**

```bash
curl https://yourdomain.com/cron/subscription-stats?token=YOUR_TOKEN
```

### **Test 4: User Dashboard**

1. Log in as a user
2. Go to dashboard
3. Check `storage/logs/laravel.log` for subscription update logs

---

## 📝 **Monitoring**

### **Check Logs:**

```bash
tail -f storage/logs/laravel.log | grep subscription
```

### **What to Monitor:**

1. **Cron execution frequency** - Should run every hour
2. **Number of subscriptions updated** - Check if reasonable
3. **Failed attempts** - Monitor for security issues
4. **Response times** - Should be < 1 second

---

## ⚡ **Quick Start Checklist**

-   [ ] 1. Change `CRON_TOKEN` in `.env` to a secure random string
-   [ ] 2. Run `php artisan config:clear` to apply changes
-   [ ] 3. Test health endpoint: `/cron/health`
-   [ ] 4. Test update endpoint with your token
-   [ ] 5. Sign up on cron-job.org
-   [ ] 6. Create cron job with your URL + token
-   [ ] 7. Wait 1 hour and check logs
-   [ ] 8. Monitor for first few days

---

## 🎨 **Visual Flow**

### **User Flow:**

```
User Logs In
    ↓
Dashboard Loads
    ↓
Check Cache (10 min)
    ↓
    ├─ Cache Hit → Skip Check
    └─ Cache Miss → Update User's Subscriptions
                        ↓
                   Show Dashboard
```

### **Cron Flow:**

```
External Cron (Every Hour)
    ↓
Call /cron/update-subscriptions?token=XXX
    ↓
Validate Token
    ↓
Check Cache (1 hour)
    ↓
    ├─ Cache Hit → Return "Already ran"
    └─ Cache Miss → Update All Expired
                        ↓
                   Return Success + Count
                        ↓
                   Log to File
```

---

## 🔧 **Troubleshooting**

### **Problem: "Unauthorized access" error**

**Solution:** Check that your token in the URL matches `CRON_TOKEN` in `.env`

### **Problem: "Already ran recently" message**

**Solution:** This is normal. Cache prevents excessive runs. Wait for cache to expire (1 hour).

### **Problem: Subscriptions not updating**

**Solution:**

1. Check if cron job is running (check cron-job.org dashboard)
2. Check Laravel logs: `storage/logs/laravel.log`
3. Test endpoint manually with curl

### **Problem: Performance issues**

**Solution:**

1. Verify cache is working (check logs for frequency)
2. Ensure you're not calling update endpoint too frequently
3. Check database indexes on `subscriptions` table

---

## 📞 **Support**

If you encounter issues:

1. Check `storage/logs/laravel.log` first
2. Verify your `.env` settings
3. Test endpoints manually with curl
4. Check cron-job.org execution history

---

## ✅ **Best Practices**

1. ✅ **Run cron every 1 hour** (not more frequently)
2. ✅ **Monitor logs** for first few days
3. ✅ **Keep token secure** - never commit to git
4. ✅ **Test after deployment** to production
5. ✅ **Use HTTPS** for cron endpoints
6. ✅ **Enable failure notifications** on cron-job.org

---

**System Status:** ✅ Fully Implemented and Ready for Production

**Last Updated:** October 20, 2025
