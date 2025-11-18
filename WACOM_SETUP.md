# Wacom STU-500 Integration Setup

This implementation provides hybrid signature support with both Wacom STU-500 hardware and browser-based fallback signatures.

## Features

- **Hardware Signature**: Professional Wacom STU-500 LCD signature pad
- **Browser Fallback**: TallStackUI signature component for when hardware isn't available
- **Automatic Detection**: Switches between methods based on hardware availability
- **Real-time Preview**: Shows signature preview for both methods

## Setup Instructions

### 1. Hardware Setup

1. **Connect Wacom STU-500**:
   - Connect the STU-500 via USB to the computer
   - Ensure the device is powered on

2. **Install Drivers**:
   - Download and install Wacom STU drivers from [Wacom Developer Portal](https://developer.wacom.com/)
   - Restart the computer after installation

3. **Install Real SDK**:
   - Replace `public/js/wacom-stu-sdk.js` with the actual Wacom STU SDK
   - Download from [Wacom STU SDK](https://developer.wacom.com/en-us/developer-sdks/stu)

### 2. Development/Testing Setup

For development without hardware, use the mock implementation:

```javascript
// Enable mock mode in browser console
WacomSTUUtils.enableMock();

// Test signature capture
WacomSTUUtils.testSignature();

// Disable mock mode
WacomSTUUtils.disableMock();
```

### 3. Browser Permissions

The application may require USB device permissions:

1. **Chrome/Edge**: Enable "Experimental Web Platform Features"
2. **Firefox**: Set `dom.webusb.enabled` to `true`
3. **Production**: Consider using HTTPS for hardware access

## Usage

### In the Shipping Modal

1. **Automatic Mode**: The system automatically detects available signature methods
2. **Manual Selection**: Users can choose between Wacom and browser signatures
3. **Fallback**: If Wacom is unavailable, automatically falls back to browser

### Status Indicators

- **Green**: Wacom connected and ready
- **Red**: Wacom not available, using browser
- **Yellow**: Connecting or processing

### Signature Flow

1. Complete certification checkboxes
2. Choose signature method (if both available)
3. Sign using selected method
4. Preview and clear if needed
5. Proceed to payment

## Technical Details

### Wacom STU-500 Specifications

- **Display**: 5" LCD (640×480 resolution)
- **Input**: EMR (Electromagnetic Resonance)
- **Pressure Levels**: 512 levels
- **Resolution**: 2540 lpi
- **Connection**: USB (HID)
- **Power**: USB powered

### Integration Points

- **Frontend**: Alpine.js component with hybrid logic
- **Backend**: Laravel Livewire for signature storage
- **Storage**: Base64 encoded PNG images

### File Structure

```
public/js/wacom-stu-sdk.js          # SDK implementation
resources/views/.../index.blade.php  # Hybrid signature UI
```

## Troubleshooting

### Common Issues

1. **"Wacom SDK not loaded"**:
   - Check if `wacom-stu-sdk.js` is accessible
   - Verify script tag in the blade template

2. **"No Wacom device found"**:
   - Ensure STU-500 is connected via USB
   - Check device manager for proper driver installation
   - Try enabling mock mode for testing

3. **"Connection failed"**:
   - Restart the browser
   - Check USB connection
   - Verify driver installation

4. **Signature not capturing**:
   - Check browser console for errors
   - Ensure proper pressure on the STU-500 pad
   - Try clearing and re-signing

### Development Mode

```javascript
// Check if mock is enabled
WacomSTUUtils.isMockEnabled();

// Enable mock for testing
WacomSTUUtils.enableMock();

// Test signature functionality
WacomSTUUtils.testSignature();
```

## Security Considerations

1. **Signature Integrity**: Signatures are captured as PNG images with timestamp
2. **Hardware Validation**: Real hardware provides better signature authentication
3. **Fallback Security**: Browser signatures are still legally valid but less secure
4. **Storage**: Signatures are stored as base64 in the database

## Legal Compliance

The Wacom STU-500 provides:
- **Biometric Data**: Pressure, speed, and stroke patterns
- **Legal Standards**: Meets most digital signature requirements
- **Audit Trail**: Timestamp and device information
- **Authentication**: Hardware-based signature verification

## Production Deployment

1. Replace mock SDK with real Wacom STU SDK
2. Install Wacom drivers on client machines
3. Configure USB permissions
4. Test with actual STU-500 hardware
5. Train users on proper signing technique

## Support

- **Wacom Developer Portal**: [https://developer.wacom.com/](https://developer.wacom.com/)
- **STU-500 Manual**: Check Wacom documentation
- **SDK Documentation**: Available with the official SDK download