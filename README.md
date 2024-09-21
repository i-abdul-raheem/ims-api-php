# REST API Documentation

## Overview
This API provides endpoints to manage users, categories, customers, vendors, materials, orders, and order items. It uses standard HTTP methods and returns JSON responses.

## Base URL
http://yourdomain.com/api/

## Authentication
Sessions are used for authentication. Public endpoints do not require authentication, while others require a valid session.

### Public Endpoints
- **POST /login**: Authenticates a user and creates a session.
- **POST /reset-password**: Resets a user's password.
- **POST /forgot-password**: Initiates the password recovery process.

## Endpoints

### Users

- **GET /users**
  - Retrieves a list of all users.
  
- **GET /users/{id}**
  - Retrieves a specific user by ID.

- **POST /users**
  - Creates a new user.
  - **Request Body**: `{ "username": "string", "password": "string", "email": "string" }`

- **PUT /users/{id}**
  - Updates user information by ID.

- **DELETE /users/{id}**
  - Deletes a user by ID.

### Categories

- **GET /category**
  - Retrieves a list of all categories.

- **GET /category/{id}**
  - Retrieves a specific category by ID.

- **POST /category**
  - Creates a new category.
  - **Request Body**: `{ "title": "string" }`

- **PUT /category/{id}**
  - Updates a category by ID.

- **DELETE /category/{id}**
  - Deletes a category by ID.

### Customers

- **GET /customer**
  - Retrieves a list of all customers.

- **GET /customer/{id}**
  - Retrieves a specific customer by ID.

- **POST /customer**
  - Creates a new customer.
  - **Request Body**: `{ "company_name": "string", "contact_person": "string", "address": "string", "phone": "string", "email": "string", "vat_number": "string" }`

- **PUT /customer/{id}**
  - Updates a customer by ID.

- **DELETE /customer/{id}**
  - Deletes a customer by ID.

### Vendors

- **GET /vendor**
  - Retrieves a list of all vendors.

- **GET /vendor/{id}**
  - Retrieves a specific vendor by ID.

- **POST /vendor**
  - Creates a new vendor.
  - **Request Body**: `{ "company_name": "string", "contact_person": "string", "address": "string", "phone": "string", "email": "string", "vat_number": "string" }`

- **PUT /vendor/{id}**
  - Updates a vendor by ID.

- **DELETE /vendor/{id}**
  - Deletes a vendor by ID.

### Materials

- **GET /material**
  - Retrieves a list of all materials.

- **GET /material/{id}**
  - Retrieves a specific material by ID.

- **POST /material**
  - Creates a new material.
  - **Request Body**: `{ "code": "string", "title": "string", "category": "integer" }`

- **PUT /material/{id}**
  - Updates a material by ID.

- **DELETE /material/{id}**
  - Deletes a material by ID.

### Orders

- **GET /order**
  - Retrieves a list of all orders.

- **GET /order/{id}**
  - Retrieves a specific order by ID.

- **POST /order**
  - Creates a new order.
  - **Request Body**: `{ "type": "string", "status": "string", "customer_id": "integer", "vendor_id": "integer" }`

- **PUT /order/{id}**
  - Updates an order by ID.

- **DELETE /order/{id}**
  - Deletes an order by ID.

### Order Items

- **GET /order-item/{order_id}**
  - Retrieves order items for a specific order.

- **POST /order-item**
  - Creates a new order item.
  - **Request Body**: `{ "order_id": "integer", "material_id": "integer", "quantity": "integer" }`

- **PUT /order-item/{id}**
  - Updates an order item by ID.

- **DELETE /order-item/{id}**
  - Deletes an order item by ID.

## Error Handling
Responses will return HTTP status codes to indicate success or failure:
- **200 OK**: Successful request.
- **201 Created**: Resource created successfully.
- **400 Bad Request**: Invalid request.
- **401 Unauthorized**: Authentication required.
- **404 Not Found**: Resource not found.
- **500 Internal Server Error**: An unexpected error occurred.

## Example Request
### Login
```bash
curl -X POST http://yourdomain.com/api/login \
-H "Content-Type: application/json" \
-d '{ "username": "user", "password": "pass" }'
