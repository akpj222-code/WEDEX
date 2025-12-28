<?php
require_once '../config.php';
require_once 'partials/header.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice Generator - Wedex Healthcare</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: Arial, sans-serif;
            padding: 20px;
            background: #f5f5f5;
        }
        
        .controls {
            max-width: 900px;
            margin: 0 auto 20px;
            background: white;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            border-radius: 8px;
        }
        
        .controls h2 {
            margin-bottom: 20px;
            color: #333;
        }
        
        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin-bottom: 20px;
        }
        
        .form-group {
            display: flex;
            flex-direction: column;
        }
        
        .form-group.full-width {
            grid-column: 1 / -1;
        }
        
        .form-group label {
            font-weight: bold;
            margin-bottom: 5px;
            font-size: 14px;
            color: #555;
        }
        
        .form-group input {
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
        }
        
        .items-section {
            margin-top: 20px;
        }
        
        .items-section h3 {
            margin-bottom: 15px;
            color: #333;
        }
        
        .item-row {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr auto;
            gap: 10px;
            margin-bottom: 10px;
            align-items: end;
        }
        
        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            font-weight: bold;
            transition: background 0.3s;
        }
        
        .btn-primary {
            background: #4CAF50;
            color: white;
        }
        
        .btn-primary:hover {
            background: #45a049;
        }
        
        .btn-secondary {
            background: #2196F3;
            color: white;
        }
        
        .btn-secondary:hover {
            background: #0b7dda;
        }
        
        .btn-danger {
            background: #f44336;
            color: white;
            padding: 8px 15px;
            font-size: 12px;
        }
        
        .btn-danger:hover {
            background: #da190b;
        }
        
        .btn-container {
            display: flex;
            gap: 10px;
            margin-top: 20px;
        }
        
        .invoice-container {
            max-width: 900px;
            margin: 0 auto;
            background: white;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        
        .header {
            text-align: center;
            margin-bottom: 10px;
        }
        
        .company-name {
            font-size: 14px;
            font-weight: bold;
            color: #333;
            margin-bottom: 3px;
        }
        
        .company-details {
            font-size: 9px;
            color: #666;
            line-height: 1.4;
            margin-bottom: 5px;
        }
        
        .invoice-title {
            font-size: 24px;
            font-weight: bold;
            letter-spacing: 6px;
            margin: 10px 0;
        }
        
        .invoice-number {
            font-size: 12px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        
        .invoice-info {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin-bottom: 10px;
            font-size: 10px;
        }
        
        .info-section {
            display: grid;
            grid-template-columns: 100px 1fr;
            gap: 5px 10px;
        }
        
        .info-label {
            font-weight: bold;
        }
        
        .buyer-name {
            font-weight: bold;
            font-size: 11px;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0;
            font-size: 10px;
        }
        
        th {
            background: #f0f0f0;
            padding: 6px;
            text-align: left;
            font-weight: bold;
            border: 1px solid #ddd;
        }
        
        td {
            padding: 4px 6px;
            border: 1px solid #ddd;
        }
        
        .text-right {
            text-align: right;
        }
        
        .total-row {
            font-weight: bold;
            background: #f9f9f9;
        }
        
        .total-words {
            text-align: center;
            font-weight: bold;
            margin: 8px 0;
            font-size: 10px;
        }
        
        .terms {
            margin-top: 15px;
            font-size: 9px;
        }
        
        .terms h3 {
            font-size: 11px;
            margin-bottom: 5px;
        }
        
        .terms p {
            line-height: 1.4;
            margin-bottom: 4px;
        }
        
        .signature-section {
            margin-top: 20px;
            text-align: right;
            position: relative;
        }
        
        .stamp-container {
            display: inline-block;
            position: relative;
        }
        
        .stamp-image {
            width: 200px;
            height: auto;
            opacity: 0.9;
        }
        
        .signature-line {
            margin-top: 5px;
            font-size: 9px;
            font-style: italic;
        }
        
        .footer {
            margin-top: 15px;
            text-align: center;
            font-size: 8px;
            color: #666;
            padding-top: 10px;
            border-top: 1px solid #ddd;
        }
        
        @media print {
            .controls {
                display: none;
            }
            
            body {
                background: white;
                padding: 0;
            }
            
            .invoice-container {
                box-shadow: none;
                padding: 15px;
            }
        }
    </style>
</head>
<body>
    <div class="controls">
        <h2>Create Sales Invoice</h2>
        
        <div class="form-grid">
            <div class="form-group">
                <label>Invoice Number:</label>
                <input type="text" id="invoiceNumber" placeholder="INV/2025/10/267">
            </div>
            <div class="form-group">
                <label>Invoice Date:</label>
                <input type="date" id="invoiceDate">
            </div>
            <div class="form-group full-width">
                <label>Buyer Name:</label>
                <input type="text" id="buyerName" placeholder="Customer/Business Name">
            </div>
            <div class="form-group">
                <label>Customer Reference:</label>
                <input type="text" id="customerRef" placeholder="e.g., 002-2025">
            </div>
            <div class="form-group">
                <label>Delivery Note:</label>
                <input type="text" id="deliveryNote" placeholder="e.g., WHO/OUT/05789">
            </div>
            <div class="form-group">
                <label>Terms of Delivery:</label>
                <input type="text" id="termsDelivery" placeholder="Optional">
            </div>
            <div class="form-group">
                <label>Buyer's Order No:</label>
                <input type="text" id="buyerOrderNo" placeholder="Optional">
            </div>
        </div>
        
        <div class="items-section">
            <h3>Invoice Items</h3>
            <div id="itemsContainer">
                <div class="item-row">
                    <div class="form-group">
                        <label>Description:</label>
                        <input type="text" class="item-desc" placeholder="Product description">
                    </div>
                    <div class="form-group">
                        <label>Quantity:</label>
                        <input type="number" class="item-qty" value="1" min="1">
                    </div>
                    <div class="form-group">
                        <label>Unit Price (₦):</label>
                        <input type="number" class="item-price" value="0" step="0.01">
                    </div>
                    <button class="btn btn-danger" onclick="removeItem(this)">Remove</button>
                </div>
            </div>
            <button class="btn btn-secondary" onclick="addItem()">+ Add Item</button>
        </div>
        
        <div class="btn-container">
            <button class="btn btn-primary" onclick="generateInvoice()">Generate Invoice</button>
            <button class="btn btn-secondary" onclick="downloadPDF()">Download as PDF</button>
        </div>
    </div>
    
    <div class="invoice-container" id="invoicePreview">
        <div class="header">
            <div class="company-name">WEDEX HEALTHCARE SERVICES LIMITED</div>
            <div class="company-details">
                Manufacturer's Representative, Hospital Consumables, Laboratory Consumables/Reagents and Medical Equipment Wholesalers and Retails.<br>
                Office: NO 57 JEDDO ROAD BY PEARL AND PURITY PLAZA OPPOSITE SUPASA STATION JEDDO TOWN, OKPE LGA DELTA STATE<br>
                Tel/Mobile: +234 803 516 1651, +234 706 886 6864, +234 815 278 0300<br>
                E-mail: wedexhealthcareservices@gmail.com | Website: www.wedexhealthcareservices.com
            </div>
        </div>
        
        <div style="text-align: center;">
            <div class="invoice-title">SALES INVOICE</div>
            <div class="invoice-number" id="displayInvoiceNumber">INV/2025/10/001</div>
        </div>
        
        <div class="invoice-info">
            <div class="info-section">
                <span class="info-label">Buyer:</span>
                <span class="buyer-name" id="displayBuyerName">Customer Name</span>
                
                <span class="info-label">Invoice Date:</span>
                <span id="displayInvoiceDate">01/01/2025</span>
                
                <span class="info-label">Customer Ref:</span>
                <span id="displayCustomerRef">-</span>
            </div>
            
            <div class="info-section">
                <span class="info-label">Terms of Delivery:</span>
                <span id="displayTermsDelivery">-</span>
                
                <span class="info-label">Delivery Note:</span>
                <span id="displayDeliveryNote">-</span>
                
                <span class="info-label">Buyer's Order No:</span>
                <span id="displayBuyerOrderNo">-</span>
            </div>
        </div>
        
        <table>
            <thead>
                <tr>
                    <th>DESCRIPTION</th>
                    <th style="width: 80px;">Quantity</th>
                    <th style="width: 120px;" class="text-right">Unit Price (₦)</th>
                    <th style="width: 120px;" class="text-right">Amount (₦)</th>
                </tr>
            </thead>
            <tbody id="invoiceItemsTable">
                <tr>
                    <td colspan="4" style="text-align: center; padding: 20px; color: #999;">
                        Add items above and click "Generate Invoice" to preview
                    </td>
                </tr>
            </tbody>
        </table>
        
        <div class="total-words" id="totalWords">
            **** ZERO NAIRA ONLY ****
        </div>
        
        <div class="terms">
            <h3>TERMS AND CONDITIONS</h3>
            <p>Kindly verify that the product(s), date, quality and quantity are to your satisfaction within 48hours.</p>
            <p>We ensure that goods supplied will be in good quality.</p>
        </div>
        
        <div class="signature-section">
            <div class="stamp-container">
                <img src="wedexstamp.jpg" alt="Company Stamp" class="stamp-image">
            </div>
            <div class="signature-line">for Wedex Healthcare Services Limited</div>
        </div>
        
        <div class="footer">
            Page: 1/1
        </div>
    </div>

    <script>
        // Set today's date as default
        document.getElementById('invoiceDate').valueAsDate = new Date();
        
        function addItem() {
            const container = document.getElementById('itemsContainer');
            const newRow = document.createElement('div');
            newRow.className = 'item-row';
            newRow.innerHTML = `
                <div class="form-group">
                    <label>Description:</label>
                    <input type="text" class="item-desc" placeholder="Product description">
                </div>
                <div class="form-group">
                    <label>Quantity:</label>
                    <input type="number" class="item-qty" value="1" min="1">
                </div>
                <div class="form-group">
                    <label>Unit Price (₦):</label>
                    <input type="number" class="item-price" value="0" step="0.01">
                </div>
                <button class="btn btn-danger" onclick="removeItem(this)">Remove</button>
            `;
            container.appendChild(newRow);
        }
        
        function removeItem(btn) {
            const container = document.getElementById('itemsContainer');
            if (container.children.length > 1) {
                btn.closest('.item-row').remove();
            } else {
                alert('You must have at least one item.');
            }
        }
        
        function formatNumber(num) {
            return num.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ',');
        }
        
        function numberToWords(num) {
            const ones = ['', 'ONE', 'TWO', 'THREE', 'FOUR', 'FIVE', 'SIX', 'SEVEN', 'EIGHT', 'NINE'];
            const tens = ['', '', 'TWENTY', 'THIRTY', 'FORTY', 'FIFTY', 'SIXTY', 'SEVENTY', 'EIGHTY', 'NINETY'];
            const teens = ['TEN', 'ELEVEN', 'TWELVE', 'THIRTEEN', 'FOURTEEN', 'FIFTEEN', 'SIXTEEN', 'SEVENTEEN', 'EIGHTEEN', 'NINETEEN'];
            
            if (num === 0) return 'ZERO';
            
            let words = '';
            
            if (num >= 1000000) {
                words += numberToWords(Math.floor(num / 1000000)) + ' MILLION ';
                num %= 1000000;
            }
            
            if (num >= 1000) {
                words += numberToWords(Math.floor(num / 1000)) + ' THOUSAND ';
                num %= 1000;
            }
            
            if (num >= 100) {
                words += ones[Math.floor(num / 100)] + ' HUNDRED ';
                num %= 100;
            }
            
            if (num >= 20) {
                words += tens[Math.floor(num / 10)] + ' ';
                num %= 10;
            } else if (num >= 10) {
                words += teens[num - 10] + ' ';
                return words.trim();
            }
            
            if (num > 0) {
                words += ones[num] + ' ';
            }
            
            return words.trim();
        }
        
        function generateInvoice() {
            // Update header info
            document.getElementById('displayInvoiceNumber').textContent = 
                document.getElementById('invoiceNumber').value || 'INV/2025/10/001';
            
            const date = new Date(document.getElementById('invoiceDate').value);
            const formattedDate = date.toLocaleDateString('en-GB').replace(/\//g, '/');
            document.getElementById('displayInvoiceDate').textContent = formattedDate;
            
            document.getElementById('displayBuyerName').textContent = 
                document.getElementById('buyerName').value || 'Customer Name';
            document.getElementById('displayCustomerRef').textContent = 
                document.getElementById('customerRef').value || '-';
            document.getElementById('displayDeliveryNote').textContent = 
                document.getElementById('deliveryNote').value || '-';
            document.getElementById('displayTermsDelivery').textContent = 
                document.getElementById('termsDelivery').value || '-';
            document.getElementById('displayBuyerOrderNo').textContent = 
                document.getElementById('buyerOrderNo').value || '-';
            
            // Generate items table
            const items = document.querySelectorAll('.item-row');
            const tableBody = document.getElementById('invoiceItemsTable');
            tableBody.innerHTML = '';
            
            let total = 0;
            
            items.forEach(item => {
                const desc = item.querySelector('.item-desc').value;
                const qty = parseFloat(item.querySelector('.item-qty').value) || 0;
                const price = parseFloat(item.querySelector('.item-price').value) || 0;
                const amount = qty * price;
                total += amount;
                
                if (desc) {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td>${desc}</td>
                        <td>${qty}</td>
                        <td class="text-right">${formatNumber(price)}</td>
                        <td class="text-right">${formatNumber(amount)}</td>
                    `;
                    tableBody.appendChild(row);
                }
            });
            
            // Add total row
            const totalRow = document.createElement('tr');
            totalRow.className = 'total-row';
            totalRow.innerHTML = `
                <td colspan="3" style="text-align: right; padding-right: 20px;">TOTAL:</td>
                <td class="text-right">${formatNumber(total)}</td>
            `;
            tableBody.appendChild(totalRow);
            
            // Update total in words
            const totalWords = numberToWords(Math.floor(total));
            document.getElementById('totalWords').textContent = 
                `**** ${totalWords} NAIRA ONLY ****`;
        }
        
        function downloadPDF() {
            const element = document.getElementById('invoicePreview');
            const opt = {
                margin: 10,
                filename: `Invoice_${document.getElementById('invoiceNumber').value || 'Draft'}.pdf`,
                image: { type: 'jpeg', quality: 0.98 },
                html2canvas: { scale: 2 },
                jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait' }
            };
            
            html2pdf().set(opt).from(element).save();
        }
    </script>
</body>
</html>
