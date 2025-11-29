<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="x-apple-disable-message-reformatting" />
    <title>Shipment Receipt — Order #{{ !empty($orderNumber) ? str_pad($orderNumber, 6, '0', STR_PAD_LEFT) : '000000' }}</title>
    <!--[if mso]>
    <style type="text/css">
        body, table, td {font-family: Arial, Helvetica, sans-serif !important;}
    </style>
    <![endif]-->
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html,
        body {
            margin: 0;
            padding: 0;
            background: #f5f5f5;
            width: 100%;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            color: #111;
            font-size: 12px;
            line-height: 1.5;
        }

        .page {
            max-width: 650px;
            margin: 20px auto;
            padding: 20px;
            background: #fff;
            border: 1px solid var(--border);
            border-radius: 6px;
        }

        header {
            text-align: center;
            margin-bottom: 12px;
        }

        header img {
            height: 24px;
            display: block;
            margin: 0 auto;
        }

        .header-right {
            display: none;
        }

        h1 {
            margin: 0 0 12px 0;
            font-size: 20px;
        }

        .summary {
            width: 100%;
            margin-bottom: 12px;
        }

        .summary table {
            width: 100%;
            border-collapse: collapse;
        }

        .summary .left {
            width: 60%;
            vertical-align: top;
        }

        .summary .right {
            width: 200px;
            vertical-align: top;
            border: 1px solid #e6e6e6;
            padding: 8px;
            font-size: 12px;
        }

        .summary .right .section-title {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #666;
        }

        table.info,
        table.items,
        table.tax {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
            margin-bottom: 12px;
        }

        table.info td,
        table.items td,
        table.items th,
        table.tax td {
            padding: 4px 6px;
            vertical-align: top;
        }

        table.items {
            min-width: 500px;
        }

        table.items th {
            text-align: left;
            border-bottom: 1px solid #e6e6e6;
            font-weight: 600;
            font-size: 11px;
        }

        table.items td {
            border-bottom: 1px solid #e6e6e6;
            font-size: 11px;
        }

        .tracking-highlight {
            font-size: 16px;
            font-weight: 700;
            color: #0b5ed7;
        }

        .addresses {
            width: 100%;
            margin-bottom: 12px;
        }

        .addresses table {
            width: 100%;
            border-collapse: collapse;
        }

        .addr {
            width: 50%;
            vertical-align: top;
            border: 1px dashed #e6e6e6;
            padding: 8px;
        }

        .addr b {
            display: block;
            margin-bottom: 4px;
        }

        .addr .muted {
            font-size: 12px;
            color: #666;
            margin-top: 4px;
        }

        .addr .value {
            font-weight: 600;
            margin-top: 2px;
        }

        .package-box {
            border: 1px solid #e6e6e6;
            padding: 10px;
            margin-bottom: 12px;
            font-size: 12px;
        }

        .package-box table {
            width: 100%;
            border-collapse: collapse;
        }

        .package-box b {
            display: block;
            margin-bottom: 6px;
        }

        .package-col {
            width: 50%;
            vertical-align: top;
            padding-right: 8px;
        }

        .customs-box {
            border: 1px solid #e6e6e6;
            padding: 12px;
            margin-bottom: 12px;
            font-size: 12px;
        }

        .customs-box b {
            display: block;
            margin-bottom: 8px;
        }

        .customs-row {
            width: 100%;
            margin-bottom: 12px;
        }

        .customs-row table {
            width: 100%;
            border-collapse: collapse;
        }

        .customs-col {
            width: 50%;
            vertical-align: top;
            padding-right: 8px;
        }

        .cert-box {
            border: 1px solid #e6e6e6;
            padding: 10px;
            margin-top: 12px;
            font-size: 12px;
            line-height: 1.3;
        }

        .cert-box table {
            width: 100%;
            border-collapse: collapse;
        }

        .cert-text {
            width: 60%;
            vertical-align: top;
            padding-right: 10px;
        }

        .cert-signature {
            width: 40%;
            vertical-align: top;
            text-align: right;
        }

        .cert-signature img {
            width: 160px;
            max-width: 100%;
            border-top: 1px solid #e6e6e6;
            padding-top: 4px;
            margin-top: 8px;
        }

        .muted {
            color: #666;
            font-size: 12px;
        }

        .mono {
            font-family: 'Courier New', Courier, monospace;
        }

        .value {
            font-weight: 600;
        }

        .charge-row {
            margin-bottom: 4px;
        }

        .charge-row table {
            width: 100%;
            border-collapse: collapse;
        }

        .charge-label {
            color: #666;
            font-size: 12px;
            text-align: left;
        }

        .charge-value {
            font-weight: 600;
            font-size: 12px;
            text-align: right;
        }

        .charge-total {
            border-top: 1px solid #e6e6e6;
            margin-top: 6px;
            padding-top: 6px;
        }

        .charge-total table {
            width: 100%;
            border-collapse: collapse;
        }

        .charge-total .charge-label {
            color: #666;
            font-size: 12px;
            text-align: left;
        }

        .charge-total .charge-value {
            font-weight: 700;
            font-size: 14px;
            text-align: right;
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

        @media only screen and (max-width: 650px) {
            .page {
                margin: 0 !important;
                padding: 12px !important;
                border: none !important;
                max-width: 100% !important;
                width: 100% !important;
            }

            .summary .left,
            .summary .right {
                display: block !important;
                width: 100% !important;
            }

            .addr {
                display: block !important;
                width: 100% !important;
            }

            .package-col {
                display: block !important;
                width: 100% !important;
            }

            .customs-col {
                display: block !important;
                width: 100% !important;
            }

            .cert-text,
            .cert-signature {
                display: block !important;
                width: 100% !important;
                text-align: left !important;
                margin-top: 12px !important;
            }

            table.info,
            table.items {
                font-size: 11px !important;
            }

            h1 {
                font-size: 18px !important;
            }
        }
    </style>
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
                <div><strong>Email:</strong> info@doortag.com</div>
            </div>
        </header>
        <h1>Shipment Receipt</h1>

        <!-- SUMMARY -->
        <table class="summary" cellpadding="0" cellspacing="0">
            <tr>
                <td class="left">
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
                            <td class="tracking-highlight">
                                @if (!empty($trackingUrl))
                                    <a href="{{ $trackingUrl }}" target="_blank" style="color: inherit; text-decoration: underline;">
                                        {{ $trackingNumber }}
                                    </a>
                                @else
                                    {{ $trackingNumber }}
                                @endif
                            </td>
                        </tr>
                    @endif

                    @if (!empty($estimated_delivery_date))
                        <tr>
                            <td class="muted">Estimated Delivery</td>
                            <td class="value">
                                {{ \Carbon\Carbon::parse($estimated_delivery_date)->format('l m/d') }} by
                                {{ \Carbon\Carbon::parse($estimated_delivery_date)->format('h:i A') }}
                            </td>
                        </tr>
                    @elseif (!empty($carrier_delivery_days))
                        <tr>
                            <td class="muted">Estimated Delivery</td>
                            <td class="value">
                                {{ $carrier_delivery_days }}
                            </td>
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
                </td>
                <td class="right">
                    <div class="section-title">CHARGES</div>
                    <br />
                    @if ($packaging_amount > 0)
                        <table cellpadding="0" cellspacing="0" style="width:100%;margin-bottom:4px;">
                            <tr>
                                <td class="charge-label">Shipping</td>
                                <td class="charge-value">
                                    ${{ number_format(($stripe_amount_paid ?? 0) - ($packaging_amount ?? 0), 2) }}</td>
                            </tr>
                        </table>
                        <table cellpadding="0" cellspacing="0" style="width:100%;margin-bottom:4px;">
                            <tr>
                                <td class="charge-label">Packaging</td>
                                <td class="charge-value">${{ number_format($packaging_amount ?? 0, 2) }}</td>
                            </tr>
                        </table>
                    @endif
                    <div class="charge-total">
                        <table cellpadding="0" cellspacing="0" style="width:100%;">
                            <tr>
                                <td class="charge-label">Total Paid</td>
                                <td class="charge-value">${{ number_format($stripe_amount_paid ?? 0, 2) }}</td>
                            </tr>
                        </table>
                    </div>
                </td>
            </tr>
        </table>

        <!-- ADDRESSES -->
        <table class="addresses" cellpadding="0" cellspacing="0">
            <tr>
                @if (is_array($shipFromAddress) && !empty($shipFromAddress))
                    <td class="addr" style="padding-right:6px;">
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
                    </td>
                @endif

                @if (is_array($shipToAddress) && !empty($shipToAddress))
                    <td class="addr" style="padding-left:6px;">
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
                    </td>
                @endif
            </tr>
        </table>

        <!-- PACKAGE DETAILS -->
        @if (!empty($packages) && is_array($packages))
            @foreach ($packages as $pkgIndex => $package)
                <div class="package-box" style="{{ $pkgIndex > 0 ? 'margin-top: 12px;' : '' }}">
                    <b>{{ count($packages) > 1 ? 'Package ' . ($pkgIndex + 1) . ' of ' . count($packages) : 'Package Details' }}</b>
                    <table cellpadding="0" cellspacing="0" style="width:100%;">
                        <tr>
                            <td class="package-col">
                                <div>
                                    <span class="muted">Type:</span>
                                    <span class="value">{{ $package['package_name'] ?? 'Package / Box' }}</span>
                                </div>
                                @if (!empty($package['insured_value']) && $package['insured_value'] > 0)
                                    <div>
                                        <span class="muted">Declared Value:</span>
                                        <span class="value">${{ number_format($package['insured_value'], 2) }}</span>
                                    </div>
                                @endif
                            </td>
                            <td class="package-col">
                                <div><span class="muted">Weight:</span> <span class="value">{{ $package['weight'] ?? 'N/A' }}
                                        {{ $package['weight_unit'] ?? 'lbs' }}</span></div>
                                @if (!empty($package['length']) && !empty($package['width']) && !empty($package['height']))
                                    <div><span class="muted">Dimensions:</span> <span class="value">{{ $package['length'] }} ×
                                            {{ $package['width'] }} × {{ $package['height'] }} {{ $package['dimension_unit'] ?? 'in' }}</span></div>
                                @endif
                            </td>
                        </tr>
                    </table>
                </div>
            @endforeach
        @endif

        <!-- TOTAL WEIGHT -->
        @if (!empty($total_weight))
            <div class="package-box" style="border: 1px solid var(--border); background-color: #fafafa;">
                <b>Total Weight</b>
                <div class="package-col">
                    <div style="font-size: 14px;"><span class="value">{{ number_format($total_weight, 2) }} lbs</span></div>
                </div>
            </div>
        @endif

        <!-- CUSTOMS INFORMATION -->
        @if (is_array($shipToAddress) && ($shipToAddress['country_code'] ?? 'US') != 'US' && !empty($customs['customs_items']))
            <div class="customs-box">
                <b>Customs Information</b>
                <!-- Contents Type & Non-Delivery Action -->
                <table cellpadding="0" cellspacing="0" style="width:100%;margin-bottom:12px;">
                    <tr>
                        <td class="customs-col">
                            <span class="muted">Contents Type:</span>
                            <span class="value">{{ ucfirst($customs['contents'] ?? 'Merchandise') }}</span>
                        </td>
                        <td class="customs-col">
                            <span class="muted">Non-Delivery Action:</span>
                            <span
                                class="value">{{ ucfirst(str_replace('_', ' ', $customs['non_delivery'] ?? 'Return to Sender')) }}</span>
                        </td>
                    </tr>
                </table>
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
                    <table cellpadding="0" cellspacing="0" style="width:100%;">
                        <tr>
                            @foreach ($tax_identifiers as $identifier)
                                @if (!empty($identifier['value']))
                                    @if ($identifier['taxable_entity_type'] == 'shipper')
                                        <td class="customs-col">
                                            <span class="muted">Sender Tax ID:</span>
                                            <span class="value mono">{{ $identifier['value'] }}</span>
                                        </td>
                                    @elseif ($identifier['taxable_entity_type'] == 'recipient')
                                        <td class="customs-col">
                                            <span class="muted">Recipient Tax ID:</span>
                                            <span class="value mono">{{ $identifier['value'] }}</span>
                                        </td>
                                    @endif
                                @endif
                            @endforeach
                        </tr>
                    </table>
                @endif
            </div>
        @endif

        <!-- CERTIFICATION & SIGNATURE -->
        <div class="cert-box">
            <table cellpadding="0" cellspacing="0" style="width:100%;">
                <tr>
                    <td class="cert-text">
                        <div>I certify that the shipment does not contain any undeclared hazardous materials or any matter
                            prohibited by law or postal regulation.</div>
                        @if (is_array($shipToAddress) && ($shipToAddress['country_code'] ?? 'US') != 'US')
                            <div style="margin-top:6px;">I hereby certify that the information on this invoice is true and
                                correct and the contents and value of this shipment are as stated above.</div>
                        @endif
                    </td>
                    @if (!empty($signatureBase64))
                        <td class="cert-signature">
                            <div style="margin-bottom:4px; font-size:12px; color:#666;">Signature</div>
                            <img src="{{ $signatureBase64 }}" alt="Signature">
                        </td>
                    @endif
                </tr>
            </table>
        </div>

        <div class="muted" style="margin-top:12px; font-size:12px; clear:both;">Issued on:
            {{ now()->format('Y-m-d H:i:s') }}</div>
    </div>
</body>

</html>
