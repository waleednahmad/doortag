<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #1f2937;
            font-size: 12px;
            line-height: 1.6;
        }

        .container {
            max-width: 100%;
            padding: 20px;
        }

        /* Header with Logo */
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #e5e7eb;
            padding-bottom: 15px;
        }

        .logo {
            max-height: 60px;
            margin-bottom: 10px;
        }

        .header h1 {
            font-size: 20px;
            color: #1f2937;
            margin-top: 10px;
        }

        .header-date {
            color: #6b7280;
            font-size: 11px;
            margin-top: 5px;
        }

        /* Section Styling */
        .section {
            margin-bottom: 25px;
            page-break-inside: avoid;
        }

        .section-title {
            font-size: 14px;
            font-weight: 600;
            color: #1f2937;
            margin-bottom: 12px;
            padding-bottom: 8px;
            border-bottom: 2px solid #dbeafe;
            display: flex;
            align-items: center;
        }

        .section-title i {
            margin-right: 8px;
        }

        .section-title.from {
            border-bottom-color: #dbeafe;
        }

        .section-title.to {
            border-bottom-color: #dcfce7;
        }

        .section-title.package {
            border-bottom-color: #e9d5ff;
        }

        .section-title.label {
            border-bottom-color: #e0e7ff;
        }

        .section-title.customs {
            border-bottom-color: #fed7aa;
        }

        /* Grid Layout */
        .grid {
            display: table;
            width: 100%;
            margin-bottom: 12px;
        }

        .grid-col {
            display: table-cell;
            padding-right: 20px;
            width: 50%;
            vertical-align: top;
        }

        .grid-col:last-child {
            padding-right: 0;
        }

        .grid-col-full {
            display: table-cell;
            width: 100%;
            padding-right: 0;
        }

        /* Label & Value Styling */
        .label {
            font-size: 10px;
            font-weight: 600;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 3px;
        }

        .value {
            font-size: 12px;
            color: #1f2937;
            font-weight: 500;
        }

        /* Address Box */
        .address-box {
            background-color: #f9fafb;
            border: 1px solid #e5e7eb;
            border-left: 4px solid #3b82f6;
            padding: 10px;
            margin-top: 8px;
            border-radius: 4px;
            font-size: 11px;
        }

        .address-line {
            margin-bottom: 4px;
            color: #1f2937;
        }

        /* Badge Styling */
        .badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 10px;
            font-weight: 600;
            margin-top: 5px;
        }

        .badge.residential {
            background-color: #dbeafe;
            color: #0c4a6e;
        }

        .badge.insured {
            background-color: #e9d5ff;
            color: #581c87;
        }

        .badge.residential.to {
            background-color: #dcfce7;
            color: #14532d;
        }

        /* Package Details */
        .package-details {
            background-color: #f3f4f6;
            border: 1px solid #e5e7eb;
            padding: 12px;
            border-radius: 4px;
            margin-top: 10px;
        }

        .detail-row {
            display: table;
            width: 100%;
            margin-bottom: 8px;
        }

        .detail-row:last-child {
            margin-bottom: 0;
        }

        .detail-label {
            display: table-cell;
            width: 40%;
            font-weight: 600;
            color: #4b5563;
            font-size: 11px;
        }

        .detail-value {
            display: table-cell;
            width: 60%;
            color: #1f2937;
            font-size: 11px;
        }

        /* Shipping Label Details */
        .label-details {
            background-color: #f0f4ff;
            border: 1px solid #c7d2fe;
            border-left: 4px solid #4f46e5;
            padding: 12px;
            border-radius: 4px;
            margin-top: 10px;
        }

        .price-box {
            border-top: 2px solid #c7d2fe;
            margin-top: 10px;
            padding-top: 10px;
            text-align: right;
        }

        .price-label {
            font-size: 11px;
            font-weight: 600;
            color: #4f46e5;
        }

        .price-value {
            font-size: 16px;
            font-weight: 700;
            color: #4f46e5;
        }

        /* Customs Section */
        .customs-item {
            border-left: 4px solid #f97316;
            background-color: #fff7ed;
            border: 1px solid #fed7aa;
            border-left: 4px solid #f97316;
            padding: 10px;
            margin-bottom: 10px;
            border-radius: 4px;
        }

        .customs-item-grid {
            display: table;
            width: 100%;
        }

        .customs-item-col {
            display: table-cell;
            width: 50%;
            padding-right: 10px;
            vertical-align: top;
            font-size: 10px;
        }

        /* Signature Section */
        .signature-section {
            margin-top: 20px;
            border-top: 1px solid #e5e7eb;
            padding-top: 15px;
        }

        .checkboxes {
            margin: 10px 0;
            font-size: 10px;
        }

        .checkbox-item {
            margin-bottom: 8px;
            page-break-inside: avoid;
        }

        .checkbox-label {
            color: #1f2937;
            line-height: 1.5;
        }

        /* Footer */
        .footer {
            text-align: center;
            color: #9ca3af;
            font-size: 9px;
            margin-top: 30px;
            border-top: 1px solid #e5e7eb;
            padding-top: 10px;
        }

        /* Page Break */
        .page-break {
            page-break-after: always;
        }

        /* Empty space */
        .space {
            margin-bottom: 15px;
        }
    </style>
</head>

<body>
    <div class="container">
        <!-- Header with Logo -->
        <div class="header">
            @if (!empty($logoBase64))
                <img src="{{ $logoBase64 }}" alt="Company Logo" class="logo">
            @endif
            <h1>Shipment Details Review</h1>
            <div class="header-date">Generated on {{ now()->format('F d, Y \a\t H:i A') }}</div>
        </div>

        <!-- Ship From Section -->
        @if (is_array($shipFromAddress) && !empty($shipFromAddress))
            <div class="section">
                <div class="section-title from">
                    Ship From
                </div>
                <div class="grid">
                    <div class="grid-col">
                        <div class="label">Name</div>
                        <div class="value">{{ $shipFromAddress['name'] ?? 'N/A' }}</div>
                    </div>
                    <div class="grid-col">
                        <div class="label">Phone</div>
                        <div class="value">{{ $shipFromAddress['phone'] ?? 'N/A' }}</div>
                    </div>
                </div>

                @if (!empty($shipFromAddress['company_name']))
                    <div class="grid">
                        <div class="grid-col">
                            <div class="label">Company</div>
                            <div class="value">{{ $shipFromAddress['company_name'] }}</div>
                        </div>
                        @if (!empty($shipFromAddress['email']))
                            <div class="grid-col">
                                <div class="label">Email</div>
                                <div class="value">{{ $shipFromAddress['email'] }}</div>
                            </div>
                        @endif
                    </div>
                @elseif (!empty($shipFromAddress['email']))
                    <div class="grid">
                        <div class="grid-col">
                            <div class="label">Email</div>
                            <div class="value">{{ $shipFromAddress['email'] }}</div>
                        </div>
                    </div>
                @endif

                @if (!empty($shipFromAddress['address_line1']))
                    <div style="margin-top: 10px;">
                        <div class="label">Complete Address</div>
                        <div class="address-box">
                            <div class="address-line">
                                {{ $shipFromAddress['address_line1'] ?? '' }}
                                @if (!empty($shipFromAddress['address_line2']))
                                    {{ $shipFromAddress['address_line2'] }}
                                @endif
                            </div>
                            <div class="address-line">
                                {{ $shipFromAddress['city_locality'] ? $shipFromAddress['city_locality'] . ', ' : '' }}
                                @if (!empty($shipFromAddress['state_province']))
                                    {{ $shipFromAddress['state_province'] ? $shipFromAddress['state_province'] : '' }}
                                @endif
                                {{ $shipFromAddress['postal_code'] ? ' ' . $shipFromAddress['postal_code'] : '' }},
                                United States
                            </div>
                        </div>
                    </div>
                @endif

                @if (!empty($shipFromAddress['address_residential_indicator']))
                    <span class="badge residential">Residential Address</span>
                @endif
            </div>
        @endif

        <!-- Ship To Section -->
        @if (is_array($shipToAddress) && !empty($shipToAddress))
            <div class="section">
                <div class="section-title to">
                    Ship To
                </div>
                <div class="grid">
                    <div class="grid-col">
                        <div class="label">Name</div>
                        <div class="value">{{ $shipToAddress['name'] ?? 'N/A' }}</div>
                    </div>
                    <div class="grid-col">
                        <div class="label">Phone</div>
                        <div class="value">{{ $shipToAddress['phone'] ?? 'N/A' }}</div>
                    </div>
                </div>

                @if (!empty($shipToAddress['company_name']))
                    <div class="grid">
                        <div class="grid-col">
                            <div class="label">Company</div>
                            <div class="value">{{ $shipToAddress['company_name'] }}</div>
                        </div>
                        @if (!empty($shipToAddress['email']))
                            <div class="grid-col">
                                <div class="label">Email</div>
                                <div class="value">{{ $shipToAddress['email'] }}</div>
                            </div>
                        @endif
                    </div>
                @elseif (!empty($shipToAddress['email']))
                    <div class="grid">
                        <div class="grid-col">
                            <div class="label">Email</div>
                            <div class="value">{{ $shipToAddress['email'] }}</div>
                        </div>
                    </div>
                @endif

                <div style="margin-top: 10px;">
                    <div class="label">Complete Address</div>
                    <div class="address-box">
                        <div class="address-line">
                            {{ $shipToAddress['address_line1'] ?? 'N/A' }}
                            @if (!empty($shipToAddress['address_line2']))
                                {{ $shipToAddress['address_line2'] }}
                            @endif
                        </div>
                        <div class="address-line">
                            {{ $shipToAddress['city_locality'] ? $shipToAddress['city_locality'] . ', ' : '' }}
                            @if (!empty($shipToAddress['state_province']))
                                {{ $shipToAddress['state_province'] ? $shipToAddress['state_province'] : '' }}
                            @endif
                            @if (!empty($shipToAddress['postal_code']))
                                {{ $shipToAddress['postal_code'] }},
                                {{ $ship_to_address_country_full_name }}
                            @endif
                        </div>
                    </div>
                </div>

                @if (!empty($shipToAddress['address_residential_indicator']))
                    <span class="badge residential to">Residential Address</span>
                @endif
            </div>
        @endif

        <!-- Package Details Section -->
        <div class="section">
            <div class="section-title package">
                Package Details
            </div>
            <div class="package-details">
                @php
                    // Use passed selectedPackage or calculate from carrierPackaging
                    if (empty($selectedPackage) || !is_array($selectedPackage)) {
                        $selectedPackage = collect($carrierPackaging)->firstWhere('package_code', $selectedPackaging);
                    }
                    if (empty($selectedPackage)) {
                        $selectedPackage = [
                            'name' => ucfirst(str_replace('_', ' ', $selectedPackaging ?? 'Package')),
                            'package_code' => $selectedPackaging ?? 'package',
                        ];
                    }
                @endphp

                <div class="detail-row">
                    <div class="detail-label">Package Type:</div>
                    <div class="detail-value">{{ $selectedPackage['name'] ?? 'N/A' }}</div>
                </div>

                <div class="detail-row">
                    <div class="detail-label">Weight:</div>
                    <div class="detail-value">{{ $package['weight'] ?? 'N/A' }} lbs</div>
                </div>

                @if (!empty($package['length']) && !empty($package['width']) && !empty($package['height']))
                    <div class="detail-row">
                        <div class="detail-label">Dimensions (inches):</div>
                        <div class="detail-value">
                            {{ $package['length'] }} × {{ $package['width'] }} × {{ $package['height'] }}
                        </div>
                    </div>
                @endif

                @if (!empty($package['insured_value']) && $isInsuranceChecked)
                    <div class="detail-row">
                        <div class="detail-label">Insurance:</div>
                        <div class="detail-value">
                            <span class="badge insured">🛡️ Insured:
                                ${{ number_format($package['insured_value'], 2) }}</span>
                        </div>
                    </div>
                @endif

                @if (!empty($shipDate))
                    <div class="detail-row">
                        <div class="detail-label">Ship Date:</div>
                        <div class="detail-value">
                            {{ \Carbon\Carbon::parse($shipDate)->format('F d, Y') }}
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Shipping Label Details -->
        @if ($selectedRate)
            <div class="section">
                <div class="section-title label">
                    Shipping Label Details
                </div>
                <div class="label-details">
                    <div class="detail-row">
                        <div class="detail-label">Service Type:</div>
                        <div class="detail-value">
                            {{ ucwords(str_replace('_', ' ', $selectedRate['service_type'] ?? 'N/A')) }}
                        </div>
                    </div>

                    <div class="detail-row">
                        <div class="detail-label">Carrier:</div>
                        <div class="detail-value">{{ strtoupper($selectedRate['carrier_code'] ?? 'N/A') }}</div>
                    </div>

                    @if (!empty($selectedRate['estimated_delivery_date']))
                        <div class="detail-row">
                            <div class="detail-label">Estimated Delivery:</div>
                            <div class="detail-value">
                                {{ \Carbon\Carbon::parse($selectedRate['estimated_delivery_date'])->format('F d, Y') }}
                            </div>
                        </div>
                    @endif

                    <div class="price-box">
                        <div class="price-label">Total Cost:</div>
                        <div class="price-value">
                            @auth('customer')
                                ${{ number_format($end_user_total ?? 0, 2) }}
                            @else
                                ${{ $selectedRate['calculated_amount'] ?? 'N/A' }}
                            @endauth
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Customs Section (International Only) -->
        @if (is_array($shipToAddress) && ($shipToAddress['country_code'] ?? 'US') != 'US' && !empty($customs['customs_items']))
            <div class="section">
                <div class="section-title customs">
                    Customs Information
                </div>

                <div class="grid">
                    <div class="grid-col">
                        <div class="label">Contents Type</div>
                        <div class="value">{{ ucfirst($customs['contents'] ?? 'N/A') }}</div>
                    </div>
                    <div class="grid-col">
                        <div class="label">Non-Delivery Action</div>
                        <div class="value">{{ ucfirst(str_replace('_', ' ', $customs['non_delivery'] ?? 'N/A')) }}
                        </div>
                    </div>
                </div>

                @if (!empty($customs['signer']))
                    <div class="grid">
                        <div class="grid-col-full">
                            <div class="label">Signed By</div>
                            <div class="value">{{ $customs['signer'] }}</div>
                        </div>
                    </div>
                @endif

                <div class="space"></div>
                <div class="label">Items ({{ count($customs['customs_items']) }})</div>

                @foreach ($customs['customs_items'] as $itemIndex => $item)
                    @if (!empty($item['description']))
                        <div class="customs-item">
                            <div class="detail-row">
                                <div class="detail-label">Description:</div>
                                <div class="detail-value">{{ $item['description'] ?? 'N/A' }}</div>
                            </div>
                            <div class="detail-row">
                                <div class="detail-label">Quantity:</div>
                                <div class="detail-value">{{ $item['quantity'] ?? 'N/A' }}</div>
                            </div>
                            <div class="detail-row">
                                <div class="detail-label">Value:</div>
                                <div class="detail-value">
                                    ${{ number_format($item['value']['amount'] ?? 0, 2) }}
                                    {{ strtoupper($item['value']['currency'] ?? 'USD') }}
                                </div>
                            </div>
                            <div class="detail-row">
                                <div class="detail-label">Weight:</div>
                                <div class="detail-value">
                                    {{ $item['weight']['value'] ?? 'N/A' }} {{ $item['weight']['unit'] ?? 'lbs' }}
                                </div>
                            </div>
                            @if (!empty($item['harmonized_tariff_code']))
                                <div class="detail-row">
                                    <div class="detail-label">HS Code:</div>
                                    <div class="detail-value">{{ $item['harmonized_tariff_code'] }}</div>
                                </div>
                            @endif
                            <div class="detail-row">
                                <div class="detail-label">Country of Origin:</div>
                                <div class="detail-value">{{ $item['country_of_origin'] ?? 'N/A' }}</div>
                            </div>
                        </div>
                    @endif
                @endforeach

                @if (!empty($tax_identifiers) && count($tax_identifiers) > 0)
                    <div style="margin-top: 10px;">
                        <div class="label">Tax Identifiers</div>
                        @foreach ($tax_identifiers as $identifier)
                            @if (!empty($identifier['value']))
                                <div
                                    style="background-color: #f9fafb; border: 1px solid #e5e7eb; padding: 8px; margin-top: 5px; border-radius: 4px;">
                                    <div class="label" style="margin-bottom: 2px;">
                                        {{ ucfirst(str_replace('_', ' ', $identifier['taxable_entity_type'])) }} ID
                                    </div>
                                    <div class="value" style="font-family: 'Courier New', monospace;">
                                        {{ $identifier['value'] }}</div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                @endif
            </div>
        @endif

        <!-- Tracking Number Section -->
        @if (!empty($trackingNumber))
            <div class="section">
                <div class="section-title label">
                    Tracking Information
                </div>
                <div class="label-details">
                    <div class="detail-row">
                        <div class="detail-label">Tracking Number:</div>
                        <div class="detail-value" style="font-weight: 700; color: #1f2937; font-size: 13px;">
                            {{ $trackingNumber }}
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Signature Section -->
        <div class="signature-section">
            <div class="section-title">
                Certification & Signature
            </div>
            <div class="checkboxes">
                <div class="checkbox-item">
                    I certify that the shipment does not contain any undeclared hazardous materials
                    (perfume, nail polish, hair spray, dry ice, lithium batteries, firearms, lighters, fuels, etc.) or
                    any matter prohibited by law or postal regulation.
                </div>

                @if ($shipToAddress['country_code'] != 'US')
                    <div class="checkbox-item">
                        I hereby certify that the information on this invoice is true and correct and
                        the contents and value of this shipment is as stated above.
                    </div>
                @endif
            </div>

            @if (!empty($signatureBase64))
                <div style="margin-top: 20px; border-top: 1px solid #e5e7eb; padding-top: 15px;">
                    <div class="label">Signature on File</div>
                    <div style="margin-top: 10px;">
                        <img src="{{ $signatureBase64 }}" alt="Customer Signature"
                            style="max-width: 200px; height: auto; border: 1px solid #d1d5db; padding: 5px;">
                    </div>
                </div>
            @endif
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>This document was generated automatically. Please retain for your records.</p>
            <p>Generated at {{ now()->format('Y-m-d H:i:s') }}</p>
        </div>
    </div>
</body>

</html>
