/**
 * Wacom STU-500 SDK Integration
 * This is a mock/wrapper implementation for the Wacom STU SDK
 * 
 * To use this with real hardware, you'll need to:
 * 1. Download the actual Wacom STU SDK from Wacom Developer Portal
 * 2. Install the STU drivers on the client machine
 * 3. Replace this mock implementation with the real SDK
 * 4. Ensure the STU-500 is connected via USB
 */

// Mock implementation for development/testing
if (typeof Module === 'undefined') {
    window.Module = {};
}

// STU SDK Mock Implementation
Module.STU = {
    Tablet: function() {
        return {
            connected: false,
            canvas: null,
            context: null,
            isDrawing: false,
            paths: [],
            
            // Get available devices
            async getDevices() {
                // In real implementation, this would detect connected STU devices
                // For now, we'll simulate finding a device if the mock is enabled
                const mockEnabled = localStorage.getItem('wacom-mock-enabled') === 'true';
                
                if (mockEnabled) {
                    return [{
                        id: 'STU-500-MOCK',
                        name: 'Wacom STU-500 (Mock)',
                        type: 'STU-500'
                    }];
                }
                
                return []; // No devices found
            },
            
            // Connect to device
            async connect(device) {
                this.connected = true;
                console.log('Connected to Wacom device:', device.name);
                
                // Create a virtual canvas for the mock
                this.canvas = document.createElement('canvas');
                this.canvas.width = 640;
                this.canvas.height = 480;
                this.context = this.canvas.getContext('2d');
                this.context.strokeStyle = '#000000';
                this.context.lineWidth = 2;
                this.context.lineCap = 'round';
                
                return true;
            },
            
            // Clear the screen
            clearScreen() {
                if (this.context) {
                    this.context.clearRect(0, 0, this.canvas.width, this.canvas.height);
                    this.context.fillStyle = '#ffffff';
                    this.context.fillRect(0, 0, this.canvas.width, this.canvas.height);
                }
                this.paths = [];
                console.log('Tablet screen cleared');
            },
            
            // Write text on tablet
            writeText(x, y, text, fontSize = 12, fontWeight = 'normal') {
                if (this.context) {
                    this.context.fillStyle = '#000000';
                    this.context.font = `${fontWeight} ${fontSize}px Arial`;
                    this.context.fillText(text, x, y);
                }
                console.log(`Text written: "${text}" at (${x}, ${y})`);
            },
            
            // Draw rectangle
            drawRectangle(x, y, width, height, lineWidth = 1) {
                if (this.context) {
                    this.context.strokeStyle = '#000000';
                    this.context.lineWidth = lineWidth;
                    this.context.strokeRect(x, y, width, height);
                }
                console.log(`Rectangle drawn: ${x}, ${y}, ${width}x${height}`);
            },
            
            // Begin path for drawing
            beginPath() {
                if (this.context) {
                    this.context.beginPath();
                }
                this.isDrawing = true;
            },
            
            // Move to position
            moveTo(x, y) {
                if (this.context) {
                    this.context.moveTo(x, y);
                }
            },
            
            // Draw line to position
            lineTo(x, y) {
                if (this.context) {
                    this.context.lineTo(x, y);
                }
            },
            
            // Stroke the path
            stroke() {
                if (this.context) {
                    this.context.stroke();
                }
            },
            
            // Close the path
            closePath() {
                if (this.context) {
                    this.context.closePath();
                }
                this.isDrawing = false;
            },
            
            // Capture image from tablet
            async captureImage() {
                if (!this.canvas) {
                    throw new Error('No canvas available for capture');
                }
                
                // Convert canvas to base64
                const dataURL = this.canvas.toDataURL('image/png');
                const base64 = dataURL.split(',')[1]; // Remove data:image/png;base64, prefix
                
                console.log('Signature captured from tablet');
                return base64;
            },
            
            // Event handlers (these would be set by the application)
            onPenDown: null,
            onPenMove: null,
            onPenUp: null,
            
            // Simulate pen events (for testing)
            simulatePenDown(x, y, pressure = 512) {
                if (this.onPenDown) {
                    this.onPenDown(x, y, pressure);
                }
            },
            
            simulatePenMove(x, y, pressure = 512) {
                if (this.onPenMove) {
                    this.onPenMove(x, y, pressure);
                }
            },
            
            simulatePenUp(x, y, pressure = 0) {
                if (this.onPenUp) {
                    this.onPenUp(x, y, pressure);
                }
            }
        };
    }
};

// Developer utilities
window.WacomSTUUtils = {
    // Enable mock mode for testing
    enableMock() {
        localStorage.setItem('wacom-mock-enabled', 'true');
        console.log('Wacom STU Mock mode enabled');
    },
    
    // Disable mock mode
    disableMock() {
        localStorage.setItem('wacom-mock-enabled', 'false');
        console.log('Wacom STU Mock mode disabled');
    },
    
    // Check if mock is enabled
    isMockEnabled() {
        return localStorage.getItem('wacom-mock-enabled') === 'true';
    },
    
    // Test signature capture
    async testSignature() {
        try {
            const tablet = new Module.STU.Tablet();
            const devices = await tablet.getDevices();
            
            if (devices.length === 0) {
                console.log('No Wacom devices found. Enable mock mode with WacomSTUUtils.enableMock()');
                return;
            }
            
            await tablet.connect(devices[0]);
            tablet.clearScreen();
            tablet.writeText(10, 10, 'Test Signature', 16);
            tablet.drawRectangle(10, 30, 200, 100);
            
            // Simulate some drawing
            tablet.beginPath();
            tablet.moveTo(50, 50);
            tablet.lineTo(150, 80);
            tablet.lineTo(100, 110);
            tablet.stroke();
            tablet.closePath();
            
            const signature = await tablet.captureImage();
            console.log('Test signature captured:', signature.substring(0, 50) + '...');
            
            return signature;
        } catch (error) {
            console.error('Test signature failed:', error);
        }
    }
};

// Initialize
console.log('Wacom STU SDK loaded');

// Auto-enable mock for development if no real device is connected
if (typeof navigator !== 'undefined' && navigator.usb) {
    // Check for USB devices (this is a simplified check)
    navigator.usb.getDevices().then(devices => {
        const wacomDevice = devices.find(device => 
            device.vendorId === 0x056a // Wacom vendor ID
        );
        
        if (!wacomDevice && !localStorage.getItem('wacom-mock-enabled')) {
            console.log('No Wacom device detected. Consider enabling mock mode for testing.');
        }
    }).catch(() => {
        // USB API not available or permission denied
        console.log('USB device detection not available');
    });
}