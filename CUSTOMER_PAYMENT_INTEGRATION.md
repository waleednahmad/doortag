# Customer Payment Integration - Automatic Card Charging

## Overview
This document outlines the new customer payment integration that allows automatic charging of saved customer cards in Stripe, replacing the previous terminal reader payment flow.

## Changes Summary

### ✅ Completed Changes

#### 1. Database Migration
- **File**: `database/migrations/2025_12_12_152227_add_stripe_customer_id_to_customers_table.php`
- **Changes**: Added `stripe_customer_id` column to `customers` table
- **Migration Run**: ✅ Successfully executed

#### 2. Customer Model
- **File**: `app/Models/Customer.php`
- **Changes**: Added `stripe_customer_id` to fillable fields

#### 3. Terminal Controller
- **File**: `app/Http/Controllers/TerminalController.php`
- **New Methods Added**:
  - `createPaymentIntentWithCustomer()` - Creates payment intent with saved customer card
  - `chargeCustomer()` - Directly charges customer's default payment method

#### 4. Routes
- **File**: `routes/web.php`
- **New Routes Added**:
  - `POST /terminal/create-customer-payment` → `TerminalController@createPaymentIntentWithCustomer`
  - `POST /terminal/charge-customer` → `TerminalController@chargeCustomer`

#### 5. Livewire Component
- **File**: `app/Livewire/Shipping/ShipEngine/Index.php`
- **Changes**:
  - **New Method**: `processCustomerPayment()` - Handles automatic customer card charging
  - **Modified**: `createLabel()` - Now calls `processCustomerPayment()` instead of showing terminal modal
  - **Commented Out** (kept for reference):
    - `showPaymentModal()`
    - `loadReaders()`
    - `processPayment()`
    - `pollPaymentStatus()`
    - `retryPayment()`
    - `resetPaymentState()`

#### 6. Blade View
- **File**: `resources/views/livewire/shipping/shipengine/index.blade.php`
- **Changes**: 
  - Commented out entire terminal payment modal (lines ~1380-1595)
  - Modal kept in code with `{{-- --}}` for future reference

---

## New Payment Flow

### Before (Terminal Reader Flow):
1. Enter shipment data
2. Get rates
3. Select specific rate
4. Open confirmation modal
5. **Select terminal reader**
6. **Send amount to terminal**
7. **Customer presents card to reader**
8. Create actual shipment

### After (Saved Card Flow):
1. Enter shipment data
2. Get rates
3. Select specific rate
4. Open confirmation modal
5. **Automatic charge to saved card** ← NEW
6. Create actual shipment

---

## How It Works

### Flow Diagram
```
User Confirms Shipment
    ↓
processCustomerPayment() called
    ↓
Retrieve authenticated customer
    ↓
Check for stripe_customer_id
    ↓
Call POST /terminal/charge-customer
    ↓
TerminalController->chargeCustomer()
    ↓
Retrieve customer from Stripe
    ↓
Get default payment method
    ↓
Create & confirm payment intent
    ↓
Payment successful → createActualLabel()
```

### Code Example

```php
// In Livewire Component
public function processCustomerPayment()
{
    $customer = Auth::guard('customer')->user();
    
    if (!$customer || !$customer->stripe_customer_id) {
        $this->toast()->error('No payment method found')->send();
        return;
    }

    $response = Http::post(route('terminal.charge-customer'), [
        'amount' => (int)($this->selectedRate['calculated_amount'] * 100),
        'customer_id' => $customer->stripe_customer_id,
        'description' => 'Shipping Label - ' . $this->selectedRate['service_type'],
    ]);

    if ($response->successful() && $response->json()['success']) {
        $this->createActualLabel($response->json()['payment_intent']);
    }
}
```

---

## Setting Up Customer Cards in Stripe

### Option 1: Using Stripe Dashboard
1. Go to Stripe Dashboard → Customers
2. Create/find customer
3. Add payment method
4. Set as default payment method
5. Copy the customer ID (starts with `cus_`)
6. Add to database customer record

### Option 2: Using Stripe API (Programmatically)

```php
// Create Stripe customer
$stripeCustomer = $stripe->customers->create([
    'email' => $customer->email,
    'name' => $customer->name,
    'description' => 'Customer for ' . $customer->name,
]);

// Save to database
$customer->update([
    'stripe_customer_id' => $stripeCustomer->id
]);

// Collect payment method (requires frontend integration)
// Use Stripe Elements or Stripe Checkout
```

### Option 3: Setup Intent (Recommended for Production)

```javascript
// Frontend: Collect card without charging
const {setupIntent, error} = await stripe.confirmCardSetup(
    clientSecret,
    {
        payment_method: {
            card: cardElement,
            billing_details: {
                name: customerName,
            },
        },
    }
);

// Backend: Attach payment method to customer
$stripe->paymentMethods->attach(
    $setupIntent->payment_method,
    ['customer' => $customer->stripe_customer_id]
);

// Set as default
$stripe->customers->update($customer->stripe_customer_id, [
    'invoice_settings' => [
        'default_payment_method' => $setupIntent->payment_method
    ]
]);
```

---

## Admin Dashboard Updates Needed

### Customer Create/Edit Form

Add field to customer forms:

```blade
<x-input 
    label="Stripe Customer ID" 
    wire:model="stripe_customer_id"
    placeholder="cus_xxxxxxxxxxxxx"
    hint="Customer ID from Stripe (starts with cus_)"
/>
```

### Customer Table Display

Add column to show Stripe status:

```blade
@if($customer->stripe_customer_id)
    <span class="badge badge-success">
        <i class="fas fa-check"></i> Card Saved
    </span>
@else
    <span class="badge badge-warning">
        <i class="fas fa-times"></i> No Card
    </span>
@endif
```

---

## Testing

### Test with Stripe Test Mode

1. **Set test mode in constructor**:
   ```php
   // TerminalController.php
   public function __construct(bool $testMode = true) // Set to true
   ```

2. **Use Stripe test cards**:
   - Success: `4242 4242 4242 4242`
   - Decline: `4000 0000 0000 0002`
   - Insufficient funds: `4000 0000 0000 9995`

3. **Create test customer in Stripe Dashboard**:
   - Go to test mode
   - Create customer
   - Add test card
   - Copy customer ID
   - Add to your database customer

### Testing Checklist

- [ ] Customer without `stripe_customer_id` gets proper error message
- [ ] Customer with valid `stripe_customer_id` gets charged successfully
- [ ] Payment success creates shipment label
- [ ] Payment failure shows appropriate error
- [ ] Admin users can still access system
- [ ] Logs show proper payment tracking

---

## Error Handling

### Common Errors & Solutions

| Error | Cause | Solution |
|-------|-------|----------|
| "No payment method found" | Customer has no `stripe_customer_id` | Add Stripe customer ID to database |
| "No default payment method found" | Customer exists but no card attached | Add payment method in Stripe |
| "card_declined" | Card was declined by bank | Use different payment method |
| "insufficient_funds" | Card has insufficient funds | Add funds or use different card |
| "Payment failed with status: requires_payment_method" | Payment method failed | Customer needs to add new card |

---

## Reverting to Old Flow

If you need to revert to the terminal reader flow:

1. **In `Index.php`**:
   ```php
   // Comment out NEW flow
   // $this->processCustomerPayment();
   
   // Uncomment OLD flow
   $this->showPaymentModal();
   ```

2. **Uncomment old methods** in `Index.php`:
   - Remove `/*` and `*/` around:
     - `showPaymentModal()`
     - `loadReaders()`
     - `processPayment()`
     - `pollPaymentStatus()`
     - `retryPayment()`
     - `resetPaymentState()`

3. **In blade file**:
   ```blade
   <!-- Remove {{-- and --}} around terminal modal -->
   <x-modal wire="showPaymentModal" size="4xl" persistent>
   ```

---

## Security Considerations

### Important Notes:

1. **PCI Compliance**: 
   - We never store card details in our database
   - All card data is handled by Stripe
   - Only store `stripe_customer_id`

2. **Authentication**:
   - Always verify customer identity before charging
   - Use Laravel's authentication guards

3. **Authorization**:
   - Ensure customer can only charge their own account
   - Implement proper role checks for admin users

4. **Logging**:
   - All payment attempts are logged
   - Failed payments are tracked
   - Use Laravel's Log facade for debugging

---

## API Endpoints Documentation

### POST /terminal/charge-customer

Charges a customer's saved payment method.

**Request**:
```json
{
    "amount": 5000,
    "customer_id": "cus_xxxxxxxxxxxxx",
    "description": "Shipping Label - FedEx Ground",
    "service_type": "FedEx Ground",
    "carrier": "FedEx"
}
```

**Response (Success)**:
```json
{
    "success": true,
    "payment_intent": {
        "id": "pi_xxxxxxxxxxxxx",
        "status": "succeeded",
        "amount": 5000,
        ...
    },
    "status": "succeeded",
    "amount_paid": 50.00,
    "payment_method_id": "pm_xxxxxxxxxxxxx"
}
```

**Response (Error)**:
```json
{
    "error": "No default payment method found for this customer"
}
```

---

## Maintenance Notes

### Future Enhancements

1. **Add Setup Intent Flow**: Allow customers to add cards through your application
2. **Multiple Payment Methods**: Let customers choose from multiple saved cards
3. **Payment History**: Show customers their payment history
4. **Failed Payment Retry**: Automatic retry with exponential backoff
5. **Payment Notifications**: Email/SMS notifications for successful/failed payments

### Database Backup

Before running migration in production:
```bash
php artisan backup:run
```

### Monitoring

Monitor these metrics:
- Payment success rate
- Failed payment reasons
- Average payment processing time
- Customers without saved cards

---

## Support & Troubleshooting

### Log Files to Check

1. `storage/logs/laravel.log` - Application logs
2. Stripe Dashboard → Logs - Stripe API logs

### Common Issues

**Issue**: Payment goes through but label not created
- **Check**: `createActualLabel()` method logs
- **Solution**: Verify ShipEngine API connection

**Issue**: Customer charged multiple times
- **Check**: Stripe Dashboard for duplicate charges
- **Solution**: Implement idempotency keys

---

## Conclusion

The new payment flow is **simpler**, **faster**, and provides a **better user experience** by:
- ✅ No terminal reader needed
- ✅ Automatic payment processing
- ✅ Faster checkout
- ✅ Better for remote/online operations
- ✅ All old code preserved for rollback

**Status**: ✅ Implementation Complete & Ready for Testing
