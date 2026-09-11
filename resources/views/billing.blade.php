<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Store Billing - New Order</title>

    <!-- <style>
        body {
            font-family: Arial, sans-serif;
            background: #f5f6f8;
            margin: 0;
            padding: 30px;
        }

        .container {
            max-width: 1100px;
            margin: auto;
            background: #ffffff;
            padding: 25px;
            border-radius: 8px;
        }

        h1 {
            margin-top: 0;
        }

        .section {
            margin-bottom: 30px;
        }

        .row {
            display: flex;
            gap: 15px;
            margin-bottom: 15px;
        }

        .field {
            flex: 1;
        }

        label {
            display: block;
            margin-bottom: 6px;
            font-weight: bold;
        }

        input,
        select {
            width: 100%;
            padding: 10px;
            box-sizing: border-box;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }

        button {
            padding: 10px 18px;
            cursor: pointer;
        }

        .summary {
            max-width: 400px;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
        }
    </style> -->

    <style>
    * {
        box-sizing: border-box;
    }

    body {
        font-family: Arial, sans-serif;
        background: #f4f6f8;
        margin: 0;
        padding: 30px;
        color: #222;
    }

    .container {
        max-width: 1100px;
        margin: 0 auto;
        background: #fff;
        padding: 30px;
        border-radius: 12px;
        box-shadow: 0 4px 18px rgba(0, 0, 0, 0.08);
    }

    h1 {
        margin-top: 0;
        margin-bottom: 30px;
        font-size: 28px;
    }

    h3 {
        margin-bottom: 15px;
    }

    .section {
        margin-bottom: 30px;
        padding: 20px;
        border: 1px solid #e5e7eb;
        border-radius: 10px;
        background: #fafafa;
    }

    .row {
        display: flex;
        gap: 15px;
        margin-bottom: 15px;
    }

    .field {
        flex: 1;
    }

    label {
        display: block;
        margin-bottom: 6px;
        font-weight: 600;
    }

    input,
    select {
        width: 100%;
        padding: 10px 12px;
        border: 1px solid #ccc;
        border-radius: 6px;
        font-size: 14px;
        background: #fff;
    }

    input:focus,
    select:focus {
        outline: none;
        border-color: #666;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 10px;
        background: #fff;
    }

    th,
    td {
        border: 1px solid #ddd;
        padding: 12px;
        text-align: left;
        vertical-align: middle;
    }

    th {
        background: #f1f3f5;
        font-weight: 600;
    }

    button {
        border: none;
        border-radius: 6px;
        padding: 10px 16px;
        font-size: 14px;
        cursor: pointer;
    }

    #addProductBtn {
        background: #e9ecef;
    }

    #generateBillBtn {
        width: 100%;
        background: #222;
        color: #fff;
        font-size: 16px;
        padding: 12px;
    }

    .remove-row {
        background: #f1f1f1;
    }

    #lowStockList {
        background: #fff3cd;
        border: 1px solid #ffe69c;
        padding: 12px;
        border-radius: 6px;
        line-height: 1.8;
    }

    .summary {
        max-width: 450px;
        margin-left: auto;
    }

    .summary-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 12px;
        font-size: 15px;
    }

    .summary-row strong {
        font-size: 16px;
    }

    #grandTotal {
        font-size: 20px;
    }

    #message {
        margin-top: 20px;
        font-weight: 600;
    }

    #message p {
        padding: 12px;
        border-radius: 6px;
        margin: 0;
        background: #f8f9fa;
    }

    @media (max-width: 768px) {
        body {
            padding: 15px;
        }

        .container {
            padding: 18px;
        }

        .row {
            flex-direction: column;
        }

        table {
            display: block;
            overflow-x: auto;
        }

        .summary {
            max-width: 100%;
        }
    }
</style>
</head>
<body>

<div class="container">

    <h1>Store Billing - New Order</h1>

    <div class="section">
        <h3>Customer</h3>

        <div class="row">
            <div class="field">
                <label>Email</label>
                <input type="email" id="customerEmail">
            </div>

            <div class="field">
                <label>Name</label>
                <input type="text" id="customerName">
            </div>
        </div>
    </div>

    <div class="section">
        <h3>Products</h3>

        <table>
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Qty</th>
                    <th>Price</th>
                    <th>Line Total</th>
                    <th>Action</th>
                </tr>
            </thead>

            <tbody id="productRows">
            </tbody>
        </table>

        <br>

        <button type="button" id="addProductBtn">
            + Add Product
        </button>
    </div>

    <div class="section">
        <h3>Low Stock Alert</h3>

        <div id="lowStockList">
            Loading...
        </div>
    </div>

    <div class="section summary">
        <h3>Payment</h3>

        <div class="summary-row">
            <span>Subtotal</span>
            <strong id="subtotal">₹0.00</strong>
        </div>

        <div class="summary-row">
            <span>Tax</span>
            <strong id="tax">₹0.00</strong>
        </div>

        <div class="summary-row">
            <span>Grand Total</span>
            <strong id="grandTotal">₹0.00</strong>
        </div>

        <div class="field">
            <label>Amount Given by Customer</label>
            <input type="number" id="amountGiven" min="0" step="0.01">
        </div>

        <br>

        <div class="summary-row">
            <span>Balance to Return</span>
            <strong id="balance">₹0.00</strong>
        </div>

        <br>

        <button type="button" id="generateBillBtn">
            Generate Bill
        </button>
    </div>

    <div id="message"></div>

</div>

<script>
    let products = [];

    document.addEventListener('DOMContentLoaded', async () => {
        await loadProducts();
        await loadLowStockProducts();
    });

    async function loadProducts() {
        const response = await fetch('/api/products');

        if (!response.ok) {
            console.error('Unable to load products.');
            return;
        }

        products = await response.json();

        addProductRow();
    }

    async function loadLowStockProducts() {
        const response = await fetch('/api/products/low-stock');

        if (!response.ok) {
            document.getElementById('lowStockList').innerHTML =
                'Unable to load low-stock products.';
            return;
        }

        const data = await response.json();

        const container = document.getElementById('lowStockList');

        if (data.products.length === 0) {
            container.innerHTML = 'No low-stock products.';
            return;
        }

        container.innerHTML = data.products
            .map(product => `
                <div>
                    ${product.name} — ${product.stock} units left
                </div>
            `)
            .join('');
    }

    function addProductRow() {
        const tbody = document.getElementById('productRows');

        const row = document.createElement('tr');

        row.innerHTML = `
            <td>
                <select class="product-select">
                    <option value="">Select Product</option>

                    ${products.map(product => `
                        <option value="${product.id}">
                            ${product.name}
                        </option>
                    `).join('')}
                </select>
            </td>

            <td>
                <input
                    type="number"
                    class="quantity"
                    value="1"
                    min="1"
                >
            </td>

            <td class="price">
                ₹0.00
            </td>

            <td class="line-total">
                ₹0.00
            </td>

            <td>
                <button type="button" class="remove-row">
                    Remove
                </button>
            </td>
        `;

        row.querySelector('.product-select')
            .addEventListener('change', () => updateRow(row));

        row.querySelector('.quantity')
            .addEventListener('input', () => updateRow(row));

        row.querySelector('.remove-row')
            .addEventListener('click', () => {
                row.remove();
                calculateTotals();
            });

        tbody.appendChild(row);
    }

    function updateRow(row) {
    const select = row.querySelector('.product-select');
    const quantityInput = row.querySelector('.quantity');
    const priceCell = row.querySelector('.price');
    const lineTotalCell = row.querySelector('.line-total');

    const product = products.find(
        product => product.id == select.value
    );

    const quantity = parseInt(quantityInput.value) || 0;

    if (!product) {
        priceCell.textContent = '₹0.00';
        lineTotalCell.textContent = '₹0.00';
        calculateTotals();
        return;
    }

    const price = parseFloat(product.price);
    const lineTotal = price * quantity;

    priceCell.textContent = `₹${price.toFixed(2)}`;
    lineTotalCell.textContent = `₹${lineTotal.toFixed(2)}`;

    calculateTotals();
}

function calculateTotals() {
    let subtotal = 0;
    let tax = 0;

    document.querySelectorAll('#productRows tr').forEach(row => {
        const select = row.querySelector('.product-select');
        const quantityInput = row.querySelector('.quantity');

        const product = products.find(
            product => product.id == select.value
        );

        const quantity = parseInt(quantityInput.value) || 0;

        if (!product || quantity <= 0) {
            return;
        }

        // const lineSubtotal =
        //     parseFloat(product.price) * quantity;

        // const lineTax =
        //     (lineSubtotal * parseFloat(product.tax_percentage)) / 100;

        const lineSubtotal =
            Math.round((parseFloat(product.price) * quantity) * 100) / 100;

        const lineTax =
            Math.round(
                (lineSubtotal * parseFloat(product.tax_percentage) / 100) * 100
            ) / 100;

        subtotal += lineSubtotal;
        tax += lineTax;
    });

    subtotal = Math.round(subtotal * 100) / 100;
    tax = Math.round(tax * 100) / 100;

    const grandTotal =
        Math.round((subtotal + tax) * 100) / 100;

    document.getElementById('subtotal').textContent =
        `₹${subtotal.toFixed(2)}`;

    document.getElementById('tax').textContent =
        `₹${tax.toFixed(2)}`;

    document.getElementById('grandTotal').textContent =
        `₹${grandTotal.toFixed(2)}`;

    calculateBalance();
}

function calculateBalance() {
    const grandTotalText =
        document.getElementById('grandTotal').textContent;

    const grandTotal = parseFloat(
        grandTotalText.replace('₹', '')
    ) || 0;

    const amountGiven = parseFloat(
        document.getElementById('amountGiven').value
    ) || 0;

    const balance = amountGiven - grandTotal;

    document.getElementById('balance').textContent =
        `₹${balance > 0 ? balance.toFixed(2) : '0.00'}`;
}

async function createOrder() {
    const customerName =
        document.getElementById('customerName').value.trim();

    const customerEmail =
        document.getElementById('customerEmail').value.trim();

    const items = [];

    document.querySelectorAll('#productRows tr').forEach(row => {
        const productId =
            row.querySelector('.product-select').value;

        const quantity =
            parseInt(row.querySelector('.quantity').value) || 0;

        if (productId && quantity > 0) {
            items.push({
                product_id: parseInt(productId),
                quantity: quantity
            });
        }
    });

    const response = await fetch('/api/orders', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            customer: {
                name: customerName,
                email: customerEmail
            },
            items: items
        })
    });

    const data = await response.json();

    const message = document.getElementById('message');

    // if (!response.ok) {
    //     message.innerHTML = `
    //         <p style="color:red;">
    //             ${data.message ?? 'Unable to create order.'}
    //         </p>
    //     `;

    //     return;
    // }

    if (!response.ok) {
        let errorMessage = data.message ?? 'Unable to create order.';

        if (data.errors) {
            const errors = Object.values(data.errors).flat();
            errorMessage = errors.join('<br>');
        }

        message.innerHTML = `
            <p style="color:red;">
                ${errorMessage}
            </p>
        `;

        return;
    }

    message.innerHTML = `
        <p style="color:green;">
            Order #${data.data.id} created successfully.
            Grand Total: ₹${data.data.grand_total}
        </p>
    `;

    // await loadLowStockProducts();

    await loadProducts();
    await loadLowStockProducts();

    document.getElementById('customerName').value = '';
    document.getElementById('customerEmail').value = '';
    document.getElementById('amountGiven').value = '';

    document.getElementById('productRows').innerHTML = '';

    document.getElementById('subtotal').textContent = '₹0.00';
    document.getElementById('tax').textContent = '₹0.00';
    document.getElementById('grandTotal').textContent = '₹0.00';
    document.getElementById('balance').textContent = '₹0.00';

    addProductRow();
}

    document.getElementById('generateBillBtn')
        .addEventListener('click', createOrder);

    document.getElementById('amountGiven')
        .addEventListener('input', calculateBalance);
        
    document.getElementById('addProductBtn')
        .addEventListener('click', addProductRow);
</script>
</body>
</html>