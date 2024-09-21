<?php
class Database
{
    private $connection;

    public function __construct()
    {
        $this->connection = new SQLite3('database.db');
        if (!$this->connection) {
            die("Connection failed: " . $this->connection->lastErrorMsg());
        }

        // Create tables if they don't exist
        $this->createTables();
    }

    public function getConnection()
    {
        return $this->connection;
    }

    // Function to create necessary tables if they do not exist
    private function createTables()
    {
        // USER TABLE
        $createUserTable = "
        CREATE TABLE IF NOT EXISTS users (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            username TEXT NOT NULL UNIQUE,
            password TEXT NOT NULL,
            last_login DATETIME,
            email TEXT NOT NULL UNIQUE
        );";
        $this->connection->exec($createUserTable);

        //Category Table
        $createCategoryTable = "
            CREATE TABLE IF NOT EXISTS categories (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                title TEXT NOT NULL UNIQUE
            );
        ";
        $this->connection->exec($createCategoryTable);

        // Customers Table
        $createCustomerTable = "
            CREATE TABLE IF NOT EXISTS customers (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                company_name TEXT NOT NULL,
                contact_person TEXT NOT NULL,
                address TEXT NOT NULL,
                phone TEXT NOT NULL,
                email TEXT NOT NULL,
                vat_number TEXT NOT NULL UNIQUE,
                created_by INTEGER NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (created_by) REFERENCES users(id)
            );
        ";
        $this->connection->exec($createCustomerTable);

        // Vendors Table
        $createVendorTable = "
            CREATE TABLE IF NOT EXISTS vendors (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                company_name TEXT NOT NULL,
                contact_person TEXT NOT NULL,
                address TEXT NOT NULL,
                phone TEXT NOT NULL,
                email TEXT NOT NULL,
                vat_number TEXT NOT NULL UNIQUE,
                created_by INTEGER NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (created_by) REFERENCES users(id)
            );
        ";
        $this->connection->exec($createVendorTable);

        // Materials Table
        $createMaterialTable = "
            CREATE TABLE IF NOT EXISTS materials (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                code TEXT NOT NULL UNIQUE,
                title TEXT NOT NULL UNIQUE,
                category INTEGER NOT NULL,
                FOREIGN KEY (category) REFERENCES categories(id)
            );
        ";
        $this->connection->exec($createMaterialTable);

        // Orders Table
        $createOrderTable = "
        CREATE TABLE IF NOT EXISTS orders (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            type TEXT CHECK(type IN ('sell', 'purchase')),
            status TEXT CHECK(status IN ('pending', 'delivered', 'shipped', 'cancelled')),
            customer_id INTEGER,
            vendor_id INTEGER,
            created_by INTEGER NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (created_by) REFERENCES users(id),
            FOREIGN KEY (customer_id) REFERENCES customers(id),
            FOREIGN KEY (vendor_id) REFERENCES vendors(id)
        );
        ";
        $this->connection->exec($createOrderTable);

        // Order Items Table
        $createOrderItemsTable = "
        CREATE TABLE IF NOT EXISTS order_items (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            order_id INTEGER NOT NULL,
            material_id INTEGER,
            quantity INTEGER CHECK (quantity >= 0),
            received INTEGER CHECK (received >= 0),
            FOREIGN KEY (order_id) REFERENCES orders(id),
            FOREIGN KEY (material_id) REFERENCES materials(id)
        );
        ";
        $this->connection->exec($createOrderItemsTable);

        // ERROR
        if ($this->connection->lastErrorCode() !== 0) {
            die("Error creating tables: " . $this->connection->lastErrorMsg());
        }
    }
}
