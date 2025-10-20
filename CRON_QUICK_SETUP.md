# 🎯 Quick Setup Guide - Subscription Auto-Expiry System

## ✅ What's Been Implemented

You now have a smart subscription status update system that works perfectly on shared hosting!

---

## 🚀 **How It Works**

### **Automatic (No Setup Needed):**

✅ When users access their dashboard, their subscriptions are checked
✅ Only checks that specific user's subscriptions (fast!)
✅ Cached for 10 minutes to prevent repeated checks

### **External Cron (Recommended Setup):**

✅ Updates ALL subscriptions once per hour
✅ Uses external free cron service (cron-job.org)
✅ Cached for 1 hour to prevent excessive runs

---

## 📋 **Your Cron Endpoints**

### **1. Health Check (Test First)**

```
https://yourdomain.com/cron/health
```

👉 Use this to verify your endpoint is working (no token needed)

### **2. Update All Subscriptions (Main Endpoint)**

```
https://yourdomain.com/cron/update-subscriptions?token=YOUR_CRON_TOKEN
```

👉 **Set this up on cron-job.org to run every 1 hour**

### **3. Get Statistics (Optional)**

```
https://yourdomain.com/cron/subscription-stats?token=YOUR_CRON_TOKEN
```

👉 Check how many subscriptions are active, expired, expiring soon

---

## ⚙️ **Quick Setup (5 Minutes)**

### **Step 1: Change Your Cron Token** 🔒

1. Open `.env` file
2. Find this line:

```env
CRON_TOKEN=veesta_cron_secret_2025_change_this_token
```

3. Change it to something random and secure:

```env
CRON_TOKEN=my_super_secret_token_abc123xyz789
```

4. Save the file

### **Step 2: Set Up cron-job.org** ⏰

1. Go to: **https://cron-job.org/**
2. Click "Sign Up" (FREE forever)
3. After login, click "Create Cronjob"
4. Fill in these details:

```
Title: Veesta - Update Subscriptions
URL: https://yourdomain.com/cron/update-subscriptions?token=YOUR_CRON_TOKEN
Schedule:
  - Minute: 0
  - Hour: Every hour (*)
  - Day: Every day (*)
  - Month: Every month (*)

Advanced Settings:
  - Request Method: GET
  - Email on failure: ✅ Enable
```

5. Click "Create"
6. Done! ✅

### **Step 3: Test It** 🧪

**Test 1 - Health Check:**

```
Visit: https://yourdomain.com/cron/health
Expected: {"status":"ok","service":"Veesta Cron Service",...}
```

**Test 2 - Manual Run:**

```
Visit: https://yourdomain.com/cron/update-subscriptions?token=YOUR_TOKEN
Expected: {"success":true,"updated":X,...}
```

**Test 3 - User Dashboard:**

1. Log in as any user
2. Go to dashboard
3. Subscriptions should auto-update if expired

---

## 🎉 **That's It!**

Your subscription system is now fully automated:

-   ✅ Users see updated status instantly on their dashboard
-   ✅ Background cron keeps everything in sync hourly
-   ✅ Efficient caching prevents performance issues
-   ✅ Works perfectly on shared hosting

---

## 🔍 **Check If It's Working**

### **Option 1: Check Logs**

```
Look in: storage/logs/laravel.log
Search for: "subscription"
```

### **Option 2: Check cron-job.org Dashboard**

```
1. Log into cron-job.org
2. View your cron job
3. Check "Execution History"
4. Should see successful runs every hour
```

### **Option 3: Test with Expired Subscription**

```
1. Find a subscription with past end_date
2. Log in as that user
3. Go to dashboard
4. Status should auto-update to "expired"
```

---

## 📞 **Need Help?**

Check the detailed documentation in:

-   `SUBSCRIPTION_UPDATE_SYSTEM.md` - Complete guide with troubleshooting

---

## 🔐 **Security Reminder**

⚠️ **IMPORTANT:** Make sure to:

1. Change the default `CRON_TOKEN` to something secure
2. Never share your cron token publicly
3. Keep your `.env` file secure
4. Use HTTPS for your production site

---

**System Status:** ✅ Ready to Deploy!

**Your Next Steps:**

1. Change CRON_TOKEN in .env
2. Set up cron-job.org (5 minutes)
3. Deploy to production
4. Monitor for 24 hours to ensure it's working

Good luck! 🚀
