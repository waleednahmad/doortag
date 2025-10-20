# FedEx Shipping Integration

This document explains the FedEx shipping integration that has been added to the doortag-shipper application.

## Overview

A complete FedEx shipping integration has been implemented with authentication, rate quotes, and a user-friendly form interface. The integration follows the existing application patterns and includes proper configuration management.

## Files Created/Modified

### 1. Core Component
- **`app/Livewire/Shipping/Fedex/Index.php`** - Main FedEx shipping component
- **`resources/views/livewire/shipping/fedex/index.blade.php`** - FedEx shipping form view

### 2. Configuration
- **`config/fedex.php`** - FedEx API configuration file
- **`.env.example`** - Added FedEx environment variables

### 3. Routes
- **`routes/web.php`** - Added route for `/shipping/fedex`

## Features Implemented

### 1. Authentication
- **Token Management**: Automatic OAuth token generation and caching
- **Environment Configuration**: Secure credential management through config files
- **Sandbox/Production Mode**: Toggle between sandbox and production APIs

### 2. Form Features
- **Ship From/To Addresses**: Complete address forms with postal codes, states, and countries
- **Package Management**: Add/remove multiple packages with dimensions and weights
- **Service Options**: Dropdown for FedEx service types (2Day, Ground, Overnight, etc.)
- **Pickup Types**: Options for drop-off locations or scheduled pickups
- **Currency Selection**: Support for multiple currencies (USD, CAD, EUR, GBP)

### 3. API Integration
- **Rate Quotes**: Real-time rate quotes from FedEx API
- **Error Handling**: Comprehensive error handling with user-friendly messages
- **Response Processing**: Proper parsing of FedEx API responses

### 4. User Interface
- **Responsive Design**: Mobile-friendly form layout
- **Dark Mode Support**: Full light/dark theme compatibility
- **Ship To/Shipment Details**: Collapsible summary sections (matching existing design)
- **Rate Display**: Clean presentation of shipping quotes with pricing breakdown

## API Endpoints Used

### 1. Authentication
```
POST https://apis-sandbox.fedex.com/oauth/token
```
**Data Required:**
- `client_id`: FedEx API client ID
- `client_secret`: FedEx API client secret  
- `grant_type`: "client_credentials"

### 2. Rate Quotes
```
POST https://apis-sandbox.fedex.com/rate/v1/rates/quotes
```
**Data Required:**
- Bearer token from authentication
- Account number
- Shipper/recipient addresses
- Package details (weight, dimensions)
- Service type and pickup options

## Configuration

### Environment Variables
Add these to your `.env` file:
```bash
FEDEX_CLIENT_ID=l7f6de69eda6c243fa95e9f43a444e5ad3
FEDEX_CLIENT_SECRET=7db8633fbb044c98b890d6e08341893c
FEDEX_ACCOUNT_NUMBER=XXXXX7364
FEDEX_SANDBOX_MODE=true
```

### Default Values
The config file (`config/fedex.php`) includes sensible defaults:
- **Pickup Type**: Drop off at FedEx Location
- **Service Type**: FedEx 2Day
- **Currency**: USD
- **Weight Unit**: LB (pounds)
- **Dimension Unit**: IN (inches)

## Usage

1. **Navigate to the FedEx shipping page**: `/shipping/fedex`
2. **Fill in shipping details**:
   - Ship from address (auto-populated from user profile)
   - Ship to address (recipient details)
   - Package dimensions and weight
   - Service preferences
3. **Get Rates**: Click "Get FedEx Rates" to retrieve quotes
4. **View Results**: Review rate quotes with pricing breakdown

## Technical Details

### Token Caching
- Access tokens are cached to avoid unnecessary API calls
- Cache duration is set to token expiry minus 60 seconds for safety
- Automatic token refresh when expired

### Form Validation
- Required fields: postal codes, countries, package weights and dimensions
- Minimum values enforced for weights and dimensions
- Email validation for contact information

### Error Handling
- Network errors are caught and displayed to users
- API errors show specific FedEx error messages
- Validation errors highlight problematic fields

### Package Management
- Support for multiple packages in a single shipment
- Add/remove package functionality
- Individual weight and dimension settings per package

## Integration with Existing System

The FedEx integration follows the same patterns as the existing shipping system:
- Uses same Livewire component structure
- Matches UI/UX patterns from the main shipping form
- Integrates with user authentication and profile data
- Supports the same responsive design principles

## Future Enhancements

Potential areas for expansion:
1. **Label Creation**: Add FedEx label generation functionality
2. **Tracking**: Implement shipment tracking features
3. **Additional Services**: Add insurance, signature requirements, etc.
4. **Rate Comparison**: Combine with existing shipping providers for comparison
5. **Saved Preferences**: Store frequently used shipping configurations

## Security Notes

- API credentials are stored in environment variables
- Sensitive data is not logged or exposed in error messages
- Token caching uses Laravel's secure cache system
- All API communications use HTTPS

## Support

For FedEx API documentation and support:
- [FedEx Developer Portal](https://developer.fedex.com/)
- [API Documentation](https://developer.fedex.com/api/en-us/catalog/rate/v1/docs.html)