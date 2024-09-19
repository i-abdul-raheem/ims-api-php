<?php
class Vendor
{
    private $db;

    public function __construct($database)
    {
        $this->db = $database->getConnection();
    }

    public function getAllVendors($limit = 10, $offset = 0)
    {
        // Ensure $limit and $offset are integers
        $limit = intval($limit);
        $offset = intval($offset);

        // Prepare the SQL query with LIMIT and OFFSET
        $query = "SELECT * FROM vendors LIMIT :limit OFFSET :offset";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':limit', $limit, SQLITE3_INTEGER);
        $stmt->bindValue(':offset', $offset, SQLITE3_INTEGER);

        // Execute the query
        $result = $stmt->execute();

        // Fetch the vendors from the result
        $vendors = [];
        while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
            $vendors[] = $row;
        }

        return $vendors;
    }

    public function getVendor($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM vendors WHERE id = :id");
        $stmt->bindValue(':id', $id, SQLITE3_INTEGER);
        $result = $stmt->execute();
        return $result->fetchArray(SQLITE3_ASSOC);
    }

    public function createVendor($data)
    {
        // Sanitize input
        $vat_number = $data['vat_number'];

        // Check if a vendor with the same name already exists
        $checkStmt = $this->db->prepare("SELECT COUNT(*) AS count FROM vendors WHERE vat_number = :vat_number");
        $checkStmt->bindValue(':vat_number', $vat_number, SQLITE3_TEXT);
        $checkResult = $checkStmt->execute();
        $checkRow = $checkResult->fetchArray(SQLITE3_ASSOC);

        if ($checkRow['count'] > 0) {
            // A vendor with the same name already exists
            return [
                'status' => 'error',
                'message' => 'Vendor with the same vat number already exists'
            ];
        }

        // Prepare the SQL query to insert a new vendor
        $stmt = $this->db->prepare("INSERT INTO vendors (company_name, contact_person, address, phone, email, vat_number, created_by) VALUES (:company_name, :contact_person, :address, :phone, :email, :vat_number, :created_by)");
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
                'message' => 'Vendor created successfully'
            ];
        } else {
            return [
                'status' => 'error',
                'message' => 'Failed to create vendor'
            ];
        }
    }

    public function updateVendor($id, $data)
    {
        // Sanitize inputs
        $vat_number = $data['vat_number'];

        // Check if a vendor with the same name already exists
        $checkStmt = $this->db->prepare("SELECT COUNT(*) AS count FROM vendors WHERE vat_number = :vat_number AND id != :id");
        $checkStmt->bindValue(':vat_number', $vat_number, SQLITE3_TEXT);
        $checkStmt->bindValue(':id', $id, SQLITE3_INTEGER);
        $checkResult = $checkStmt->execute();
        $checkRow = $checkResult->fetchArray(SQLITE3_ASSOC);

        if ($checkRow['count'] > 0) {
            // A vendor with the same name exists
            return [
                'status' => 'error',
                'message' => 'Vendor with the same vat number already exists'
            ];
        }

        // Prepare the SQL query to update the vendor
        $stmt = $this->db->prepare("UPDATE vendors SET company_name = :company_name, contact_person = :contact_person, address = :address, phone = :phone, email = :email, vat_number = :vat_number WHERE id = :id");
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
                'message' => 'Vendor updated successfully'
            ];
        } else {
            return [
                'status' => 'error',
                'message' => 'Failed to update vendor'
            ];
        }
    }

    public function deleteVendor($id)
    {
        $stmt = $this->db->prepare("DELETE FROM vendors WHERE id = :id");
        $stmt->bindValue(':id', $id, SQLITE3_INTEGER);
        return $stmt->execute();
    }
}
