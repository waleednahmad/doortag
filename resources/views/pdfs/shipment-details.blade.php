<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>Shipment Receipt — Order #{{ !empty($orderNumber) ? str_pad($orderNumber, 6, '0', STR_PAD_LEFT) : '000000' }}
    </title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --accent: #0b5ed7;
            --muted: #666;
            --border: #e6e6e6;
            --pad: 8px;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html,
        body {
            margin: 0;
            background: #fff;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            color: #111;
            font-size: 12px;
        }

        .page {
            max-width: 8.5in;
            margin: 12px auto;
            padding: 12px;
            border: 1px solid var(--border);
            border-radius: 6px;
        }

        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 12px;
        }

        header img {
            height: 40px;
        }

        .header-right {
            text-align: right;
            font-size: 12px;
            line-height: 1.3;
        }

        h1 {
            margin: 0 0 12px 0;
            font-size: 20px;
        }

        .summary {
            display: flex;
            justify-content: space-between;
            gap: 12px;
            margin-bottom: 12px;
        }

        .summary .left {
            flex: 1 1 60%;
        }

        .summary .right {
            width: 160px;
            border: 1px solid var(--border);
            padding: 8px;
            border-radius: 6px;
            font-size: 13px;
        }

        .summary .right .muted {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
        }

        table.info,
        table.items,
        table.tax {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
            margin-bottom: 12px;
        }

        table.info td,
        table.items td,
        table.items th,
        table.tax td {
            padding: 4px 6px;
            vertical-align: top;
        }

        table.items th {
            text-align: left;
            border-bottom: 1px solid var(--border);
            font-weight: 600;
        }

        table.items td {
            border-bottom: 1px solid var(--border);
        }

        .tracking-highlight {
            font-size: 16px;
            font-weight: 700;
            color: var(--accent);
        }

        .addresses {
            display: flex;
            gap: 12px;
            margin-bottom: 12px;
        }

        .addr {
            flex: 1;
            border: 1px dashed var(--border);
            padding: 8px;
            border-radius: 6px;
        }

        .addr b {
            display: block;
            margin-bottom: 4px;
        }

        .addr .muted {
            font-size: 12px;
            color: var(--muted);
            margin-top: 4px;
        }

        .addr .value {
            font-weight: 600;
            margin-top: 2px;
        }

        .package-box {
            border: 1px solid var(--border);
            border-radius: 6px;
            padding: 10px;
            margin-bottom: 12px;
            display: flex;
            flex-wrap: wrap;
            font-size: 13px;
            gap: 8px;
        }

        .package-box b {
            width: 100%;
            margin-bottom: 6px;
        }

        .package-col {
            flex: 1 1 45%;
        }

        .customs-box {
            border: 1px solid var(--border);
            border-radius: 6px;
            padding: 12px;
            margin-bottom: 12px;
            font-size: 13px;
        }

        .customs-box b {
            display: block;
            margin-bottom: 8px;
        }

        .customs-row {
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
            margin-bottom: 12px;
        }

        .customs-col {
            flex: 1 1 45%;
        }

        .cert-box {
            border: 1px solid var(--border);
            border-radius: 6px;
            padding: 10px;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-top: 12px;
            font-size: 12px;
            line-height: 1.3;
        }

        .cert-text {
            flex: 1;
            padding-right: 10px;
        }

        .cert-signature {
            text-align: right;
        }

        .cert-signature img {
            width: 160px;
            border-top: 1px solid var(--border);
            padding-top: 4px;
            margin-top: 8px;
        }

        .muted {
            color: var(--muted);
            font-size: 12px;
        }

        .mono {
            font-family: 'Courier New', Courier, monospace;
        }

        .value {
            font-weight: 600;
        }

        .charge-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 4px;
        }

        .charge-label {
            color: #666;
            font-size: 12px;
        }

        .charge-value {
            font-weight: 600;
            font-size: 13px;
        }

        .charge-total {
            border-top: 1px solid var(--border);
            margin-top: 6px;
            padding-top: 6px;
            display: flex;
            justify-content: space-between;
        }

        .charge-total .charge-label {
            color: #666;
            font-size: 12px;
        }

        .charge-total .charge-value {
            font-weight: 700;
            font-size: 15px;
        }

        .residential-badge {
            display: inline-block;
            margin-top: 6px;
            padding: 3px 8px;
            font-size: 10px;
            border: 1px solid black;
        }

        .no-print {
            display: none;
        }

        @media print {
            .no-print {
                display: none;
            }

            .page {
                border: none;
                margin: 0.2in;
            }

            h1 {
                font-size: 18px;
            }
        }
    </style>
    <script>
        // Auto-print when page loads
        window.onload = function() {
            window.print();
        };
    </script>
</head>

<body>
    <div class="page">
        <!-- HEADER -->
        <header>
            <div>
                @if (!empty($logoBase64))
                    <img src="{{ $logoBase64 }}" alt="Logo">
                @else
                    <img src="https://doortag.com/assets/images/logo-black.png" alt="Logo">
                @endif
            </div>
            <div class="header-right">
                <div><strong>Phone:</strong> (813) 903-1774</div>
                <div><strong>Email:</strong> support@doortag.com</div>
            </div>
        </header>
        <h1>Shipment Receipt</h1>

        <!-- SUMMARY -->
        <div class="summary">
            <div class="left">
                <table class="info">
                    @if (!empty($orderNumber))
                        <tr>
                            <td class="muted">Order #</td>
                            <td class="value mono">{{ str_pad($orderNumber, 6, '0', STR_PAD_LEFT) }}</td>
                        </tr>
                    @endif
                    @if (!empty($paymentNumber))
                        <tr>
                            <td class="muted">Payment</td>
                            <td class="value mono">{{ $paymentNumber }}</td>
                        </tr>
                    @endif
                    @if (!empty($trackingNumber))
                        <tr>
                            <td class="muted">Tracking</td>
                            <td class="tracking-highlight">{{ $trackingNumber }}</td>
                        </tr>
                    @endif
                    @if ($selectedRate)
                        <tr>
                            <td class="muted">Carrier / Service</td>
                            <td>{{ strtoupper($selectedRate['carrier_code'] ?? 'N/A') }} —
                                {{ ucwords(str_replace('_', ' ', $selectedRate['service_type'] ?? 'N/A')) }}</td>
                        </tr>
                    @endif
                </table>
            </div>
            <div class="right">
                <div class="muted">CHARGES</div>
                <br />
                @if ($packaging_amount > 0)
                    <div class="charge-row">
                        <div class="charge-label">Shipping</div>
                        <div class="charge-value">
                            ${{ number_format(($stripe_amount_paid ?? 0) - ($packaging_amount ?? 0), 2) }}</div>
                    </div>
                    <div class="charge-row">
                        <div class="charge-label">Packaging</div>
                        <div class="charge-value">${{ number_format($packaging_amount ?? 0, 2) }}</div>
                    </div>
                @endif
                <div class="charge-total">
                    <div class="charge-label">Total Paid</div>
                    <div class="charge-value">${{ number_format($stripe_amount_paid ?? 0, 2) }}</div>
                </div>
            </div>
        </div>

        <!-- ADDRESSES -->
        <div class="addresses">
            @if (is_array($shipFromAddress) && !empty($shipFromAddress))
                <div class="addr">
                    <b>From</b>
                    <div class="muted">Name</div>
                    <div class="value">{{ $shipFromAddress['name'] ?? 'N/A' }}</div>
                    @if (!empty($shipFromAddress['company_name']))
                        <div class="muted">Company</div>
                        <div class="value">{{ $shipFromAddress['company_name'] }}</div>
                    @else
                        <div class="muted">Company</div>
                        <div class="value">—</div>
                    @endif
                    @if (!empty($shipFromAddress['phone']))
                        <div class="muted">Phone</div>
                        <div class="value">{{ $shipFromAddress['phone'] }}</div>
                    @else
                        <div class="muted">Phone</div>
                        <div class="value">—</div>
                    @endif
                    @if (!empty($shipFromAddress['email']))
                        <div class="muted">Email</div>
                        <div class="value">{{ $shipFromAddress['email'] }}</div>
                    @else
                        <div class="muted">Email</div>
                        <div class="value">—</div>
                    @endif
                    <div class="muted">Address</div>
                    <div class="value">
                        {{ $shipFromAddress['address_line1'] ?? '' }}
                        @if (!empty($shipFromAddress['address_line2']))
                            {{ $shipFromAddress['address_line2'] }}
                        @endif
                    </div>
                    <div class="value">
                        {{ $shipFromAddress['city_locality'] ? $shipFromAddress['city_locality'] . ', ' : '' }}
                        @if (!empty($shipFromAddress['state_province']))
                            {{ $shipFromAddress['state_province'] }}
                        @endif
                        {{ $shipFromAddress['postal_code'] ?? '' }}, United States
                    </div>
                    @if (isset($shipFromAddress['address_residential_indicator']) &&
                            strtolower($shipFromAddress['address_residential_indicator']) === 'yes')
                        <div class="residential-badge">Residential Address</div>
                    @else
                        <div class="residential-badge">Business Address</div>
                    @endif
                </div>
            @endif

            @if (is_array($shipToAddress) && !empty($shipToAddress))
                <div class="addr">
                    <b>To</b>
                    <div class="muted">Name</div>
                    <div class="value">{{ $shipToAddress['name'] ?? 'N/A' }}</div>
                    @if (!empty($shipToAddress['company_name']))
                        <div class="muted">Company</div>
                        <div class="value">{{ $shipToAddress['company_name'] }}</div>
                    @else
                        <div class="muted">Company</div>
                        <div class="value">—</div>
                    @endif
                    @if (!empty($shipToAddress['phone']))
                        <div class="muted">Phone</div>
                        <div class="value">{{ $shipToAddress['phone'] }}</div>
                    @else
                        <div class="muted">Phone</div>
                        <div class="value">—</div>
                    @endif
                    @if (!empty($shipToAddress['email']))
                        <div class="muted">Email</div>
                        <div class="value">{{ $shipToAddress['email'] }}</div>
                    @else
                        <div class="muted">Email</div>
                        <div class="value">—</div>
                    @endif
                    <div class="muted">Address</div>
                    <div class="value">
                        {{ $shipToAddress['address_line1'] ?? 'N/A' }}
                        @if (!empty($shipToAddress['address_line2']))
                            {{ $shipToAddress['address_line2'] }}
                        @endif
                    </div>
                    <div class="value">
                        {{ $shipToAddress['city_locality'] ? $shipToAddress['city_locality'] . ', ' : '' }}
                        @if (!empty($shipToAddress['state_province']))
                            {{ $shipToAddress['state_province'] }}
                        @endif
                        {{ $shipToAddress['postal_code'] ?? '' }},
                        {{ $ship_to_address_country_full_name ?? 'United States' }}
                    </div>
                    @if (isset($shipToAddress['address_residential_indicator']) &&
                            strtolower($shipToAddress['address_residential_indicator']) === 'yes')
                        <div class="residential-badge">Residential Address</div>
                    @else
                        <div class="residential-badge">Business Address</div>
                    @endif
                </div>
            @endif
        </div>

        <!-- PACKAGE DETAILS -->
        <div class="package-box">
            <b>Package Details</b>
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
            <div class="package-col">
                <div>
                    <span class="muted">
                        Type:
                    </span>
                    <span class="value">
                        {{ $selectedPackage['name'] ?? 'Package / Box' }}
                    </span>
                </div>
                @if (
                    !empty($package) &&
                        isset($package['insured_value']) &&
                        $package['insured_value'] > 100)
                    <div>
                        <span class="muted">
                            Declared Value:
                        </span>
                        <span class="value">
                            ${{ number_format($package['insured_value'] ?? 0, 2) }}
                        </span>
                    </div>
                @endif
                @if (!empty($shipDate))
                    <div><span class="muted">Ship Date:</span> <span
                            class="value">{{ \Carbon\Carbon::parse($shipDate)->format('M d, Y') }}</span></div>
                @endif
            </div>
            <div class="package-col">
                <div><span class="muted">Weight:</span> <span class="value">{{ $package['weight'] ?? 'N/A' }}
                        lbs</span></div>
                @if (!empty($package['length']) && !empty($package['width']) && !empty($package['height']))
                    <div><span class="muted">Dimensions:</span> <span class="value">{{ $package['length'] }} ×
                            {{ $package['width'] }} × {{ $package['height'] }} in</span></div>
                @endif
            </div>
        </div>

        <!-- CUSTOMS INFORMATION -->
        @if (is_array($shipToAddress) && ($shipToAddress['country_code'] ?? 'US') != 'US' && !empty($customs['customs_items']))
            <div class="customs-box">
                <b>Customs Information</b>
                <!-- Contents Type & Non-Delivery Action -->
                <div class="customs-row">
                    <div class="customs-col">
                        <span class="muted">Contents Type:</span>
                        <span class="value">{{ ucfirst($customs['contents'] ?? 'Merchandise') }}</span>
                    </div>
                    <div class="customs-col">
                        <span class="muted">Non-Delivery Action:</span>
                        <span
                            class="value">{{ ucfirst(str_replace('_', ' ', $customs['non_delivery'] ?? 'Return to Sender')) }}</span>
                    </div>
                </div>
                <!-- Items Table -->
                <table class="items">
                    <thead>
                        <tr>
                            <th>Description</th>
                            <th>Qty</th>
                            <th>Value</th>
                            <th>Weight</th>
                            <th>HS Code</th>
                            <th>Origin</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($customs['customs_items'] as $item)
                            @if (!empty($item['description']))
                                <tr>
                                    <td>{{ $item['description'] ?? 'N/A' }}</td>
                                    <td>{{ $item['quantity'] ?? 'N/A' }}</td>
                                    <td>${{ number_format($item['value']['amount'] ?? 0, 2) }}</td>
                                    <td>{{ $item['weight']['value'] ?? 'N/A' }} {{ $item['weight']['unit'] ?? 'lb' }}
                                    </td>
                                    <td>{{ $item['harmonized_tariff_code'] ?? '—' }}</td>
                                    <td>{{ $item['country_of_origin'] ?? 'N/A' }}</td>
                                </tr>
                            @endif
                        @endforeach
                    </tbody>
                </table>
                <!-- International Tax IDs -->
                @if (!empty($tax_identifiers) && count($tax_identifiers) > 0)
                    <div class="customs-row">
                        @foreach ($tax_identifiers as $identifier)
                            @if (!empty($identifier['value']))
                                @if ($identifier['taxable_entity_type'] == 'shipper')
                                    <div class="customs-col">
                                        <span class="muted">Sender Tax ID:</span>
                                        <span class="value mono">{{ $identifier['value'] }}</span>
                                    </div>
                                @elseif ($identifier['taxable_entity_type'] == 'recipient')
                                    <div class="customs-col">
                                        <span class="muted">Recipient Tax ID:</span>
                                        <span class="value mono">{{ $identifier['value'] }}</span>
                                    </div>
                                @endif
                            @endif
                        @endforeach
                    </div>
                @endif
            </div>
        @endif

        <!-- CERTIFICATION & SIGNATURE -->
        <div class="cert-box">
            <div class="cert-text">
                <div>I certify that the shipment does not contain any undeclared hazardous materials or any matter
                    prohibited by law or postal regulation.</div>
                @if (is_array($shipToAddress) && ($shipToAddress['country_code'] ?? 'US') != 'US')
                    <div style="margin-top:6px;">I hereby certify that the information on this invoice is true and
                        correct and the contents and value of this shipment are as stated above.</div>
                @endif
            </div>
            @if (!empty($signatureBase64))
                <div class="cert-signature">
                    <div style="margin-bottom:4px; font-size:12px; color:#666;">Signature</div>
                    <img src="{{ $signatureBase64 }}" alt="Signature">
                </div>
            @endif
        </div>

        <div class="muted" style="margin-top:12px; font-size:12px; clear:both;">Issued on:
            {{ now()->format('Y-m-d H:i:s') }}</div>
    </div>
</body>

</html>
