<?php
class Customer
{
    private $db;

    public function __construct($database)
    {
        $this->db = $database->getConnection();
    }

    public function getAllCustomers($limit = 10, $offset = 0)
    {
        // Ensure $limit and $offset are integers
        $limit = intval($limit);
        $offset = intval($offset);

        // Prepare the SQL query with LIMIT and OFFSET
        $query = "SELECT * FROM customers LIMIT :limit OFFSET :offset";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':limit', $limit, SQLITE3_INTEGER);
        $stmt->bindValue(':offset', $offset, SQLITE3_INTEGER);

        // Execute the query
        $result = $stmt->execute();

        // Fetch the customers from the result
        $customers = [];
        while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
            $customers[] = $row;
        }

        return $customers;
    }

    public function getCustomer($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM customers WHERE id = :id");
        $stmt->bindValue(':id', $id, SQLITE3_INTEGER);
        $result = $stmt->execute();
        return $result->fetchArray(SQLITE3_ASSOC);
    }

    public function createCustomer($data)
    {
        // Sanitize input
        $vat_number = $data['vat_number'];

        // Check if a customer with the same name already exists
        $checkStmt = $this->db->prepare("SELECT COUNT(*) AS count FROM customers WHERE vat_number = :vat_number");
        $checkStmt->bindValue(':vat_number', $vat_number, SQLITE3_TEXT);
        $checkResult = $checkStmt->execute();
        $checkRow = $checkResult->fetchArray(SQLITE3_ASSOC);

        if ($checkRow['count'] > 0) {
            // A customer with the same name already exists
            return [
                'status' => 'error',
                'message' => 'Customer with the same vat number already exists'
            ];
        }

        // Prepare the SQL query to insert a new customer
        $stmt = $this->db->prepare("INSERT INTO customers (company_name, contact_person, address, phone, email, vat_number, created_by) VALUES (:company_name, :contact_person, :address, :phone, :email, :vat_number, :created_by)");
        $stmt->bindValue(':company_name', $data['company_name'], SQLITE3_TEXT);
        $stmt->bindValue(':contact_person', $data['contact_person'], SQLITE3_TEXT);
        $stmt->bindValue(':address', $data['address'], SQLITE3_TEXT);
        $stmt->bindValue(':phone', $data['phone'], SQLITE3_TEXT);
        $stmt->bindValue(':email', $data['email'], SQLITE3_TEXT);
        $stmt->bindValue(':vat_number', $data['vat_number'], SQLITE3_TEXT);
        $stmt->bindValue(':created_by', $data['created_by'], SQLITE3_INTEGER);

        // Execute the statement and return success or error response
        if ($stmt->execute()) {
            return [
                'status' => 'success',
                'message' => 'Customer created successfully'
            ];
        } else {
            return [
                'status' => 'error',
                'message' => 'Failed to create customer'
            ];
        }
    }

    public function updateCustomer($id, $data)
    {
        // Sanitize inputs
        $vat_number = $data['vat_number'];

        // Check if a customer with the same name already exists
        $checkStmt = $this->db->prepare("SELECT COUNT(*) AS count FROM customers WHERE vat_number = :vat_number AND id != :id");
        $checkStmt->bindValue(':vat_number', $vat_number, SQLITE3_TEXT);
        $checkStmt->bindValue(':id', $id, SQLITE3_INTEGER);
        $checkResult = $checkStmt->execute();
        $checkRow = $checkResult->fetchArray(SQLITE3_ASSOC);

        if ($checkRow['count'] > 0) {
            // A customer with the same name exists
            return [
                'status' => 'error',
                'message' => 'Customer with the same vat number already exists'
            ];
        }

        // Prepare the SQL query to update the customer
        $stmt = $this->db->prepare("UPDATE customers SET company_name = :company_name, contact_person = :contact_person, address = :address, phone = :phone, email = :email, vat_number = :vat_number WHERE id = :id");
        $stmt->bindValue(':company_name', $data['company_name'], SQLITE3_TEXT);
        $stmt->bindValue(':contact_person', $data['contact_person'], SQLITE3_TEXT);
        $stmt->bindValue(':address', $data['address'], SQLITE3_TEXT);
        $stmt->bindValue(':phone', $data['phone'], SQLITE3_TEXT);
        $stmt->bindValue(':email', $data['email'], SQLITE3_TEXT);
        $stmt->bindValue(':vat_number', $data['vat_number'], SQLITE3_TEXT);
        $stmt->bindValue(':id', $id, SQLITE3_INTEGER);

        // Execute the statement and return success or error response
        if ($stmt->execute()) {
            return [
                'status' => 'success',
                'message' => 'Customer updated successfully'
            ];
        } else {
            return [
                'status' => 'error',
                'message' => 'Failed to update customer'
            ];
        }
    }

    public function deleteCustomer($id)
    {
        $stmt = $this->db->prepare("DELETE FROM customers WHERE id = :id");
        $stmt->bindValue(':id', $id, SQLITE3_INTEGER);
        return $stmt->execute();
    }
}
