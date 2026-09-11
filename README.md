# Store Order & Inventory Mini-System

A Laravel 12 application for managing products, customers, orders, inventory, and low-stock reporting.

The project includes a REST API and a simple Blade-based billing interface.

## Features

- Create customer orders with one or more products
- Automatic subtotal, tax, and grand total calculation
- Stock validation and deduction during order creation
- Concurrency-safe inventory updates
- Customer order history
- Configurable low-stock reporting
- Queued order confirmation simulation
- Simple Blade billing interface
- Automated tests for order creation, insufficient stock, and concurrent ordering

## Requirements

- PHP 8.2 or higher
- Composer
- MySQL
- Laravel 12

## Installation

1. Clone the repository:

   ```bash
   git clone <repository-url>
   cd store-order-system
   ```

2. Install PHP dependencies:

   ```bash
   composer install
   ```

3. Create the environment file:

   ```bash
   cp .env.example .env
   ```

   On Windows:

   ```powershell
   copy .env.example .env
   ```

4. Generate the application key:

   ```bash
   php artisan key:generate
   ```

5. Create a MySQL database and configure `.env`:

   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=store_order_system
   DB_USERNAME=root
   DB_PASSWORD=
   ```

6. Configure the low-stock threshold if required:

   ```env
   LOW_STOCK_THRESHOLD=10
   ```

7. Run migrations and seed sample data:

   ```bash
   php artisan migrate --seed
   ```

8. Start the application:

   ```bash
   php artisan serve
   ```

Open:

```text
http://127.0.0.1:8000
```

This displays the Store Billing interface.

## Web UI

A simple Blade-based billing screen is included for demonstrating the application.

The screen supports:

- Customer name and email
- Multiple product rows
- Product quantity selection
- Product price and line total
- Low-stock alerts
- Subtotal
- Tax
- Grand total
- Amount given by customer
- Balance to return
- Order creation through the Generate Bill button
- Success and validation/error messages

The UI communicates with the Laravel API using JavaScript `fetch()` requests.

`Amount Given` and `Balance to Return` are presentation-only values and are not persisted because payment persistence is outside the required scope.

## API Endpoints

### 1. Create Order

**POST**

```text
/api/orders
```

Example request:

```json
{
    "customer": {
        "name": "Arun Kumar",
        "email": "arun@example.com"
    },
    "items": [
        {
            "product_id": 1,
            "quantity": 1
        },
        {
            "product_id": 2,
            "quantity": 2
        }
    ]
}
```

The API:

- Validates customer and order data
- Checks available stock
- Calculates subtotal, tax, and grand total
- Creates the order and order items
- Deducts product stock
- Dispatches the order confirmation job

### 2. Customer Order History

**GET**

```text
/api/customers/orders?email=arun@example.com
```

Returns the customer details and their previous orders.

### 3. Low-Stock Products

**GET**

```text
/api/products/low-stock
```

Returns products whose stock is below the configured low-stock threshold.

### 4. Product List

**GET**

```text
/api/products
```

Returns the available products used by the billing interface.

## Low-Stock Configuration

The low-stock threshold is configurable through `.env`:

```env
LOW_STOCK_THRESHOLD=10
```

The value is exposed through:

```text
config/inventory.php
```

Products with stock strictly below this value are returned by the low-stock API.

## Queue

Order creation dispatches a queued `SendOrderConfirmation` job after the database transaction commits.

The job simulates sending an order confirmation email by writing the order information to the Laravel application log. No external SMTP service is required.

For normal development, start the queue worker:

```bash
php artisan queue:work
```

Create an order and check:

```text
storage/logs/laravel.log
```

for the simulated confirmation entry.

## Testing

A separate MySQL testing database is used so automated tests do not affect development data.

Create:

```text
store_order_system_test
```

Create a `.env.testing` file:

```env
APP_ENV=testing
APP_KEY=<your-application-key>

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=store_order_system_test
DB_USERNAME=root
DB_PASSWORD=

QUEUE_CONNECTION=sync
```

Ensure `phpunit.xml` is configured to use the MySQL testing database:

```xml
<env name="DB_CONNECTION" value="mysql"/>
<env name="DB_DATABASE" value="store_order_system_test"/>
```

Run:

```bash
php artisan test
```

Current test suite:

```text
3 tests passed
22 assertions
```

The tests cover:

- Successful order creation
- Subtotal, tax, and grand total calculation
- Order-item creation
- Stock deduction
- Insufficient-stock validation
- Prevention of partial order creation when stock is insufficient
- Concurrent attempts to purchase the final available unit

## Concurrency Handling

Stock validation and deduction are performed inside a database transaction.

Products are retrieved using Laravel's:

```php
lockForUpdate()
```

before their stock is checked and deducted.

This prevents two concurrent requests from both purchasing the same final unit.

When an order contains multiple products, product IDs are processed in a consistent order before acquiring locks. This reduces the possibility of database deadlocks.

A concurrency integration test starts two independent PHP processes that attempt to purchase a product with stock `1` at approximately the same time.

Expected behavior:

- Exactly one order succeeds
- Exactly one competing order fails
- Only one order is stored
- Final product stock is `0`

## Assumptions

- A customer is uniquely identified by email address.
- If a customer with the provided email already exists, the existing customer record is used.
- Each product can appear only once in an order request.
- An order must contain at least one product.
- Order quantities must be positive integers.
- Product price and tax percentage are stored in `order_items` when the order is created so historical order values remain unchanged if the product is updated later.
- Tax is calculated per order line and rounded to two decimal places.
- Order tax is the sum of the rounded individual line taxes.
- A product is considered low stock when its stock is strictly less than the configured threshold.
- Stock validation and deduction occur inside a database transaction using row-level locking.
- MySQL/InnoDB is used for transactional and row-locking behavior.
- Order confirmation is simulated through a queued job and application logging.
- No external SMTP service is required.
- Amount given and balance displayed by the billing UI are not persisted.

## Project Structure

Key implementation files:

```text
app/
├── Http/
│   ├── Controllers/
│   │   └── Api/
│   │       └── OrderController.php
│   └── Requests/
│       └── StoreOrderRequest.php
├── Jobs/
│   └── SendOrderConfirmation.php
├── Models/
│   ├── Customer.php
│   ├── Order.php
│   ├── OrderItem.php
│   └── Product.php
└── Services/
    └── OrderService.php

config/
└── inventory.php

resources/
└── views/
    └── billing.blade.php

routes/
├── api.php
└── web.php

tests/
├── Feature/
│   ├── OrderCreationTest.php
│   └── OrderConcurrencyTest.php
└── Support/
    └── attempt_order.php

prompts/
└── AI prompt screenshots
```

## AI-Assisted Development

AI-assisted tools were used during development for implementation guidance, debugging, code review, concurrency handling, testing, and documentation.

Screenshots of the prompts used are included in:

```text
/prompts
```

This prompt log is included as part of the submission requirements.