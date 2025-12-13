# Quick Start Guide - Customer Payment Integration

## 🚀 Quick Setup (5 Minutes)

### Step 1: Verify Migration ✅
Migration already run! Check with:
```bash
php artisan migrate:status
```

### Step 2: Add Stripe Customer ID to Customer

#### Option A: Through Stripe Dashboard (Easiest)
1. Go to https://dashboard.stripe.com/test/customers
2. Click "Create customer"
3. Fill in:
   - Name: Customer name
   - Email: customer@example.com
4. Click "Add payment method"
5. Use test card: `4242 4242 4242 4242`
6. Set as default payment method
7. Copy customer ID (e.g., `cus_xxxxxxxxxxxxx`)

#### Option B: Directly in Database
```sql
UPDATE customers 
SET stripe_customer_id = 'cus_xxxxxxxxxxxxx' 
WHERE id = 1;
```

### Step 3: Test the Flow

1. **Login as customer** (with stripe_customer_id set)
2. **Create a shipment**:
   - Fill in ship from/to addresses
   - Add package details
   - Click "Get Rates"
3. **Select a rate**
4. **Click "Create Label"**
5. **Sign and certify**
6. **Payment automatically processed!** ✨

---

## 🧪 Testing Scenarios

### ✅ Success Case
- Customer with `stripe_customer_id` + valid card
- Result: Payment succeeds, label created

### ⚠️ Error Cases to Test

1. **No Stripe Customer ID**
   ```
   Customer: No stripe_customer_id in database
   Expected: "No payment method found"
   ```

2. **Invalid Stripe Customer ID**
   ```
   Customer: stripe_customer_id = 'invalid_id'
   Expected: Stripe API error
   ```

3. **No Default Payment Method**
   ```
   Customer: Valid stripe_customer_id but no card attached
   Expected: "No default payment method found"
   ```

4. **Declined Card**
   ```
   Use test card: 4000 0000 0000 0002
   Expected: "card_declined" error
   ```

---

## 📝 Test Cards (Stripe Test Mode)

| Card Number | Scenario |
|-------------|----------|
| 4242 4242 4242 4242 | Success |
| 4000 0000 0000 0002 | Card declined |
| 4000 0000 0000 9995 | Insufficient funds |
| 4000 0000 0000 9987 | Lost card |
| 4000 0000 0000 0069 | Expired card |

---

## 🔍 Debugging

### Check Logs
```bash
# Laravel logs
tail -f storage/logs/laravel.log

# Filter payment logs
tail -f storage/logs/laravel.log | grep "Customer payment"
```

### Log Messages to Look For

**Success Flow:**
```
Processing customer payment
Customer charged successfully
Payment intent created
```

**Error Flow:**
```
Customer payment failed: No stripe_customer_id
Charge customer error: [error message]
```

---

## 🔄 Switching Between Flows

### Use NEW Flow (Current - Default)
In `Index.php` line ~1073:
```php
// NEW FLOW active
$this->processCustomerPayment();
```

### Use OLD Flow (Terminal Reader)
In `Index.php` line ~1073:
```php
// Comment out NEW
// $this->processCustomerPayment();

// Uncomment OLD
$this->showPaymentModal();
```

Then uncomment all old methods (remove `/*` and `*/`).

---

## 🎯 Key Files Modified

| File | Changes |
|------|---------|
| `Customer.php` | Added `stripe_customer_id` to fillable |
| `TerminalController.php` | Added 2 new methods for customer charging |
| `routes/web.php` | Added 2 new routes |
| `Index.php` | Added `processCustomerPayment()`, commented old methods |
| `index.blade.php` | Commented terminal modal |
| Migration | Added `stripe_customer_id` column |

---

## 🛠️ Admin Dashboard TODO

### Add to Customer Form

```blade
<!-- In customer create/edit form -->
<div class="mb-4">
    <label for="stripe_customer_id" class="block text-sm font-medium text-gray-700">
        Stripe Customer ID
    </label>
    <input 
        type="text" 
        id="stripe_customer_id" 
        name="stripe_customer_id" 
        wire:model="stripe_customer_id"
        placeholder="cus_xxxxxxxxxxxxx"
        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
    >
    <p class="mt-1 text-sm text-gray-500">
        Customer ID from Stripe (optional). Required for automatic payment processing.
    </p>
</div>
```

### Add to Customer List View

```blade
<!-- In customer table -->
<td>
    @if($customer->stripe_customer_id)
        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
            <i class="fas fa-check"></i> Card Saved
        </span>
        <span class="text-xs text-gray-500 block mt-1">
            {{ Str::limit($customer->stripe_customer_id, 20) }}
        </span>
    @else
        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
            <i class="fas fa-exclamation-circle"></i> No Card
        </span>
    @endif
</td>
```

---

## 📊 Monitoring

### Key Metrics to Track

1. **Payment Success Rate**
   ```sql
   SELECT 
       COUNT(*) as total_attempts,
       SUM(CASE WHEN stripe_payment_intent_id IS NOT NULL THEN 1 ELSE 0 END) as successful,
       (SUM(CASE WHEN stripe_payment_intent_id IS NOT NULL THEN 1 ELSE 0 END) * 100.0 / COUNT(*)) as success_rate
   FROM shipments
   WHERE created_at >= NOW() - INTERVAL 7 DAY;
   ```

2. **Customers Without Cards**
   ```sql
   SELECT COUNT(*) 
   FROM customers 
   WHERE stripe_customer_id IS NULL;
   ```

3. **Recent Payment Failures**
   - Check `storage/logs/laravel.log` for "Charge customer error"

---

## 🚨 Common Issues & Solutions

### Issue: "No payment method found"
**Cause**: Customer doesn't have `stripe_customer_id`
**Solution**: Add Stripe customer ID to database

### Issue: Payment succeeds but label not created
**Cause**: Error in `createActualLabel()` method
**Solution**: Check ShipEngine API credentials and logs

### Issue: Customer charged twice
**Cause**: User clicked twice or browser refresh
**Solution**: Add loading state/disable button (already implemented)

### Issue: Can't find customer in Stripe
**Cause**: Wrong Stripe mode (test vs live)
**Solution**: Verify `TerminalController` constructor test mode setting

---

## ✅ Deployment Checklist

Before deploying to production:

- [ ] Test with real Stripe test customers
- [ ] Test all error scenarios
- [ ] Backup database
- [ ] Run migration on production
- [ ] Update admin forms to include `stripe_customer_id`
- [ ] Set correct Stripe mode in constructor
- [ ] Monitor logs for first 24 hours
- [ ] Have rollback plan ready (uncomment old code)

---

## 📞 Support

For issues or questions:
1. Check logs: `storage/logs/laravel.log`
2. Check Stripe Dashboard: https://dashboard.stripe.com/logs
3. Review `CUSTOMER_PAYMENT_INTEGRATION.md` for detailed docs

---

**Status**: ✅ Ready for Testing
**Last Updated**: December 12, 2025
