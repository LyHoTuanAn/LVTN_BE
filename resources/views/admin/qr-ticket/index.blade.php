@extends('layouts.app')

@section('title', __('QR Ticket Scanner'))

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800">{{ __('QR Ticket Scanner') }}</h1>
        <p class="text-gray-600 mt-1">{{ __('Scan barcode from mobile app to complete booking') }}</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Scanner Section -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path>
                </svg>
                {{ __('Barcode Scanner') }}
            </h2>

            <!-- Camera Preview -->
            <div class="relative mb-4">
                <div id="scanner-container" class="w-full h-64 bg-gray-900 rounded-lg overflow-hidden relative">
                    <video id="video-preview" class="w-full h-full object-cover" playsinline></video>
                    <!-- Scan line animation -->
                    <div id="scan-line" class="absolute left-0 right-0 h-0.5 bg-green-500 opacity-75 hidden" style="animation: scan 2s linear infinite;"></div>
                    <!-- Overlay when camera is off -->
                    <div id="camera-off-overlay" class="absolute inset-0 flex flex-col items-center justify-center bg-gray-800">
                        <svg class="w-16 h-16 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                        </svg>
                        <p class="text-gray-400 mt-2">{{ __('Camera is off') }}</p>
                    </div>
                </div>
            </div>

            <!-- Camera Controls -->
            <div class="flex gap-2 mb-4">
                <button id="btn-start-camera" class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors flex items-center justify-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                    </svg>
                    {{ __('Start Camera') }}
                </button>
                <button id="btn-stop-camera" class="flex-1 px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors flex items-center justify-center hidden">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 10a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1h-4a1 1 0 01-1-1v-4z"></path>
                    </svg>
                    {{ __('Stop Camera') }}
                </button>
            </div>

            <!-- Manual Input -->
            <div class="border-t pt-4 mt-4">
                <h3 class="text-sm font-medium text-gray-700 mb-2">{{ __('Manual Input') }}</h3>
                <div class="manual-input-container flex flex-wrap gap-2">
                    <input 
                        type="text" 
                        id="manual-code-input" 
                        class="flex-1 min-w-0 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 uppercase"
                        placeholder="{{ __('Enter booking code...') }}"
                        maxlength="50"
                    >
                    <div class="manual-input-buttons flex gap-2">
                        <button id="btn-lookup" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                            {{ __('Lookup') }}
                        </button>
                        <button id="btn-complete" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                            {{ __('Complete') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Result Section -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                {{ __('Scan Result') }}
            </h2>

            <!-- Status Messages -->
            <div id="status-message" class="mb-4 hidden">
                <div class="p-4 rounded-lg flex items-start">
                    <svg class="w-5 h-5 mr-2 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span class="message-text"></span>
                </div>
            </div>

            <!-- Booking Details -->
            <div id="booking-details" class="hidden">
                <div class="space-y-4">
                    <!-- Booking Code & Status -->
                    <div class="flex items-center justify-between pb-4 border-b">
                        <div>
                            <p class="text-sm text-gray-500">{{ __('Booking Code') }}</p>
                            <p id="detail-code" class="text-xl font-bold text-gray-800"></p>
                        </div>
                        <span id="detail-status" class="px-3 py-1 rounded-full text-sm font-medium"></span>
                    </div>

                    <!-- Movie Info -->
                    <div class="pb-4 border-b">
                        <p class="text-sm text-gray-500 mb-1">{{ __('Movie') }}</p>
                        <p id="detail-movie" class="font-semibold text-gray-800"></p>
                        <div class="flex gap-4 mt-1 text-sm text-gray-600">
                            <span id="detail-date"></span>
                            <span id="detail-time"></span>
                            <span id="detail-room"></span>
                        </div>
                    </div>

                    <!-- Seats -->
                    <div class="pb-4 border-b">
                        <p class="text-sm text-gray-500 mb-1">{{ __('Seats') }}</p>
                        <p id="detail-seats" class="font-semibold text-gray-800"></p>
                    </div>

                    <!-- Customer Info -->
                    <div class="pb-4 border-b">
                        <p class="text-sm text-gray-500 mb-1">{{ __('Customer') }}</p>
                        <p id="detail-customer-name" class="font-semibold text-gray-800"></p>
                        <p id="detail-customer-email" class="text-sm text-gray-600"></p>
                        <p id="detail-customer-phone" class="text-sm text-gray-600"></p>
                    </div>

                    <!-- Payment Info -->
                    <div>
                        <p class="text-sm text-gray-500 mb-1">{{ __('Payment') }}</p>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">{{ __('Total Price') }}:</span>
                            <span id="detail-total-price" class="font-bold text-green-600"></span>
                        </div>
                        <div id="detail-voucher-row" class="flex justify-between items-center text-sm hidden">
                            <span class="text-gray-500">{{ __('Voucher Discount') }}:</span>
                            <span id="detail-voucher-amount" class="text-red-500"></span>
                        </div>
                        <div class="flex justify-between items-center text-sm mt-1">
                            <span class="text-gray-500">{{ __('Payment Method') }}:</span>
                            <span id="detail-payment-method" class="capitalize"></span>
                        </div>
                        <div class="flex justify-between items-center text-sm mt-1">
                            <span class="text-gray-500">{{ __('Payment Status') }}:</span>
                            <span id="detail-is-paid"></span>
                        </div>
                    </div>
                </div>

                <!-- Action Button -->
                <div id="action-container" class="mt-6 hidden">
                    <button id="btn-mark-complete" class="w-full px-4 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors flex items-center justify-center text-lg font-semibold">
                        <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        {{ __('Mark as Completed') }}
                    </button>
                </div>
            </div>

            <!-- Empty State -->
            <div id="empty-state" class="text-center py-12">
                <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path>
                </svg>
                <p class="text-gray-500">{{ __('Scan a barcode to see booking details') }}</p>
            </div>
        </div>
    </div>

    <!-- Recent Scans -->
    <div class="mt-6 bg-white rounded-lg shadow-md p-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            {{ __('Recent Scans') }}
        </h2>
        <div id="recent-scans" class="divide-y">
            <p class="text-gray-500 py-4 text-center">{{ __('No recent scans') }}</p>
        </div>
    </div>
</div>

<style>
    @keyframes scan {
        0% { top: 0; }
        50% { top: 100%; }
        100% { top: 0; }
    }

    /* Mobile responsive for manual input section */
    @media (max-width: 520px) {
        .manual-input-container {
            flex-direction: column;
        }
        
        .manual-input-container input {
            width: 100%;
            flex: none;
        }
        
        .manual-input-buttons {
            width: 100%;
        }
        
        .manual-input-buttons button {
            flex: 1;
        }
    }
</style>

<script src="https://cdn.jsdelivr.net/npm/@nicecode-biz/barcode-reader@4.0.0/dist/barcode-reader.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const videoPreview = document.getElementById('video-preview');
    const scanLine = document.getElementById('scan-line');
    const cameraOffOverlay = document.getElementById('camera-off-overlay');
    const btnStartCamera = document.getElementById('btn-start-camera');
    const btnStopCamera = document.getElementById('btn-stop-camera');
    const manualCodeInput = document.getElementById('manual-code-input');
    const btnLookup = document.getElementById('btn-lookup');
    const btnComplete = document.getElementById('btn-complete');
    const statusMessage = document.getElementById('status-message');
    const bookingDetails = document.getElementById('booking-details');
    const emptyState = document.getElementById('empty-state');
    const actionContainer = document.getElementById('action-container');
    const btnMarkComplete = document.getElementById('btn-mark-complete');
    const recentScans = document.getElementById('recent-scans');

    let stream = null;
    let scanning = false;
    let currentBookingCode = null;
    let recentScansList = [];

    // Start camera
    btnStartCamera.addEventListener('click', async function() {
        try {
            stream = await navigator.mediaDevices.getUserMedia({
                video: { facingMode: 'environment' }
            });
            videoPreview.srcObject = stream;
            videoPreview.play();
            
            cameraOffOverlay.classList.add('hidden');
            scanLine.classList.remove('hidden');
            btnStartCamera.classList.add('hidden');
            btnStopCamera.classList.remove('hidden');
            
            scanning = true;
            scanBarcode();
        } catch (err) {
            console.error('Camera error:', err);
            showStatus('error', '{{ __("Could not access camera. Please check permissions.") }}');
        }
    });

    // Stop camera
    btnStopCamera.addEventListener('click', function() {
        stopCamera();
    });

    function stopCamera() {
        if (stream) {
            stream.getTracks().forEach(track => track.stop());
            stream = null;
        }
        videoPreview.srcObject = null;
        scanning = false;
        
        cameraOffOverlay.classList.remove('hidden');
        scanLine.classList.add('hidden');
        btnStopCamera.classList.add('hidden');
        btnStartCamera.classList.remove('hidden');
    }

    // Barcode scanning loop
    async function scanBarcode() {
        if (!scanning || !stream) return;

        const canvas = document.createElement('canvas');
        const ctx = canvas.getContext('2d');
        canvas.width = videoPreview.videoWidth;
        canvas.height = videoPreview.videoHeight;
        
        if (canvas.width > 0 && canvas.height > 0) {
            ctx.drawImage(videoPreview, 0, 0);
            
            try {
                // Use BarcodeDetector API if available
                if ('BarcodeDetector' in window) {
                    const barcodeDetector = new BarcodeDetector({
                        formats: ['code_39', 'code_128', 'qr_code']
                    });
                    const barcodes = await barcodeDetector.detect(canvas);
                    
                    if (barcodes.length > 0) {
                        const code = barcodes[0].rawValue;
                        handleScannedCode(code);
                        return;
                    }
                }
            } catch (err) {
                console.log('Barcode detection error:', err);
            }
        }

        if (scanning) {
            requestAnimationFrame(scanBarcode);
        }
    }

    // Handle scanned code
    function handleScannedCode(code) {
        if (!code) return;
        
        manualCodeInput.value = code.toUpperCase();
        processCode(code, true);
    }

    // Manual lookup
    btnLookup.addEventListener('click', function() {
        const code = manualCodeInput.value.trim();
        if (code) {
            lookupBooking(code);
        }
    });

    // Manual complete
    btnComplete.addEventListener('click', function() {
        const code = manualCodeInput.value.trim();
        if (code) {
            processCode(code, true);
        }
    });

    // Enter key on input
    manualCodeInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            const code = manualCodeInput.value.trim();
            if (code) {
                processCode(code, true);
            }
        }
    });

    // Mark complete button
    btnMarkComplete.addEventListener('click', function() {
        if (currentBookingCode) {
            processCode(currentBookingCode, true);
        }
    });

    // Lookup booking without completing
    async function lookupBooking(code) {
        showStatus('info', '{{ __("Looking up booking...") }}');
        
        try {
            const response = await fetch('{{ route("admin.qr-ticket.lookup") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ code: code })
            });

            const data = await response.json();
            
            if (data.success) {
                showStatus('success', data.message);
                displayBookingDetails(data.data.booking, false);
            } else {
                showStatus('error', data.message);
                hideBookingDetails();
            }
        } catch (err) {
            console.error('Lookup error:', err);
            showStatus('error', '{{ __("An error occurred while looking up the booking") }}');
        }
    }

    // Process code (scan and complete)
    async function processCode(code, markComplete = true) {
        if (!markComplete) {
            lookupBooking(code);
            return;
        }

        showStatus('info', '{{ __("Processing...") }}');
        
        try {
            const response = await fetch('{{ route("admin.qr-ticket.scan") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ code: code })
            });

            const data = await response.json();
            
            if (data.success) {
                showStatus('success', data.message);
                displayBookingDetails(data.data.booking, true);
                addToRecentScans(data.data.booking, true);
                
                // Play success sound
                playSound('success');
            } else {
                showStatus('error', data.message);
                if (data.data && data.data.booking) {
                    displayBookingDetails(data.data.booking, data.code === 'BOOKING_ALREADY_COMPLETED');
                    if (data.code !== 'BOOKING_ALREADY_COMPLETED') {
                        addToRecentScans(data.data.booking, false);
                    }
                } else {
                    hideBookingDetails();
                }
                
                // Play error sound
                playSound('error');
            }
        } catch (err) {
            console.error('Scan error:', err);
            showStatus('error', '{{ __("An error occurred while processing the booking") }}');
            playSound('error');
        }
    }

    // Display booking details
    function displayBookingDetails(booking, isCompleted) {
        currentBookingCode = booking.code;
        
        document.getElementById('detail-code').textContent = booking.code;
        
        const statusEl = document.getElementById('detail-status');
        statusEl.textContent = booking.status.charAt(0).toUpperCase() + booking.status.slice(1);
        statusEl.className = 'px-3 py-1 rounded-full text-sm font-medium ' + getStatusClass(booking.status);
        
        document.getElementById('detail-movie').textContent = booking.showtime?.movie?.title || '-';
        document.getElementById('detail-date').textContent = booking.showtime?.date || '-';
        document.getElementById('detail-time').textContent = booking.showtime?.start_time + ' - ' + booking.showtime?.end_time;
        document.getElementById('detail-room').textContent = booking.showtime?.room?.name || '-';
        
        document.getElementById('detail-seats').textContent = booking.seats || '-';
        
        document.getElementById('detail-customer-name').textContent = booking.user?.name || '-';
        document.getElementById('detail-customer-email').textContent = booking.user?.email || '';
        document.getElementById('detail-customer-phone').textContent = booking.user?.phone || '';
        
        document.getElementById('detail-total-price').textContent = booking.total_price;
        document.getElementById('detail-payment-method').textContent = booking.payment_method || '-';
        
        const isPaidEl = document.getElementById('detail-is-paid');
        isPaidEl.textContent = booking.is_paid ? '{{ __("Paid") }}' : '{{ __("Unpaid") }}';
        isPaidEl.className = booking.is_paid ? 'text-green-600 font-medium' : 'text-red-600 font-medium';
        
        if (booking.voucher_amount) {
            document.getElementById('detail-voucher-row').classList.remove('hidden');
            document.getElementById('detail-voucher-amount').textContent = '-' + booking.voucher_amount;
        } else {
            document.getElementById('detail-voucher-row').classList.add('hidden');
        }
        
        // Show/hide action button
        if (isCompleted || booking.status === 'completed' || booking.status === 'canceled' || !booking.is_paid) {
            actionContainer.classList.add('hidden');
        } else {
            actionContainer.classList.remove('hidden');
        }
        
        emptyState.classList.add('hidden');
        bookingDetails.classList.remove('hidden');
    }

    function hideBookingDetails() {
        currentBookingCode = null;
        bookingDetails.classList.add('hidden');
        emptyState.classList.remove('hidden');
        actionContainer.classList.add('hidden');
    }

    function getStatusClass(status) {
        switch (status) {
            case 'completed': return 'bg-green-100 text-green-800';
            case 'confirmed': return 'bg-blue-100 text-blue-800';
            case 'pending': return 'bg-yellow-100 text-yellow-800';
            case 'canceled': return 'bg-red-100 text-red-800';
            default: return 'bg-gray-100 text-gray-800';
        }
    }

    function showStatus(type, message) {
        const container = statusMessage.querySelector('div');
        statusMessage.classList.remove('hidden');
        
        container.className = 'p-4 rounded-lg flex items-start ';
        switch (type) {
            case 'success':
                container.className += 'bg-green-100 text-green-800';
                break;
            case 'error':
                container.className += 'bg-red-100 text-red-800';
                break;
            case 'info':
                container.className += 'bg-blue-100 text-blue-800';
                break;
        }
        
        container.querySelector('.message-text').textContent = message;
    }

    function addToRecentScans(booking, success) {
        const existingIndex = recentScansList.findIndex(s => s.code === booking.code);
        if (existingIndex > -1) {
            recentScansList.splice(existingIndex, 1);
        }
        
        recentScansList.unshift({
            ...booking,
            scannedAt: new Date().toLocaleTimeString(),
            success: success
        });
        
        // Keep only last 10
        if (recentScansList.length > 10) {
            recentScansList = recentScansList.slice(0, 10);
        }
        
        renderRecentScans();
    }

    function renderRecentScans() {
        if (recentScansList.length === 0) {
            recentScans.innerHTML = '<p class="text-gray-500 py-4 text-center">{{ __("No recent scans") }}</p>';
            return;
        }
        
        recentScans.innerHTML = recentScansList.map(scan => `
            <div class="flex items-center justify-between py-3">
                <div class="flex items-center">
                    <span class="w-3 h-3 rounded-full mr-3 ${scan.success ? 'bg-green-500' : 'bg-red-500'}"></span>
                    <div>
                        <p class="font-medium text-gray-800">${scan.code}</p>
                        <p class="text-sm text-gray-500">${scan.showtime?.movie?.title || '-'} - ${scan.seats}</p>
                    </div>
                </div>
                <div class="text-right">
                    <span class="px-2 py-1 rounded text-xs font-medium ${getStatusClass(scan.status)}">${scan.status}</span>
                    <p class="text-xs text-gray-400 mt-1">${scan.scannedAt}</p>
                </div>
            </div>
        `).join('');
    }

    function playSound(type) {
        // Create audio context for simple beep sounds
        try {
            const audioContext = new (window.AudioContext || window.webkitAudioContext)();
            const oscillator = audioContext.createOscillator();
            const gainNode = audioContext.createGain();
            
            oscillator.connect(gainNode);
            gainNode.connect(audioContext.destination);
            
            oscillator.type = 'sine';
            oscillator.frequency.value = type === 'success' ? 800 : 300;
            gainNode.gain.value = 0.3;
            
            oscillator.start();
            oscillator.stop(audioContext.currentTime + 0.2);
        } catch (e) {
            console.log('Audio not supported');
        }
    }
});
</script>
@endsection
