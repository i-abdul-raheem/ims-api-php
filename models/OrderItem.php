<?php
class OrderItem
{
    private $db;

    public function __construct($database)
    {
        $this->db = $database->getConnection();
    }

    public function addOrderItems($data)
    {
        $this->db->exec('BEGIN TRANSACTION'); // Start a transaction for batch insertion
        $results = [
            'status' => 'success', // Default status
            'message' => "" // Array to collect message
        ];

        $stmt = $this->db->prepare("INSERT INTO order_items (order_id, material_id, quantity, received) VALUES (:order_id, :material_id, :quantity, :received)");

        foreach ($data['items'] as $item) {
            // Check if the required keys exist
            if (isset($item['material_id'], $item['quantity'])) {
                $stmt->bindValue(':order_id', $data['order_id'], SQLITE3_INTEGER);
                $stmt->bindValue(':material_id', $item['material_id'], SQLITE3_INTEGER);
                $stmt->bindValue(':quantity', $item['quantity'], SQLITE3_INTEGER);
                $stmt->bindValue(':received', 0, SQLITE3_INTEGER);

                if (!$stmt->execute()) {
                    $results['status'] = 'error'; // Set status to error if any insert fails
                    $results['message'] = 'Failed to add order item: ' . $this->db->lastErrorMsg();
                } else {
                    $results['message'] = 'Order item added successfully';
                }
            } else {
                $results['status'] = 'error'; // Set status to error if missing fields
                $results['message'][] = 'Missing required fields in item';
            }
        }

        $this->db->exec('COMMIT'); // Commit the transaction
        return $results;
    }

    public function getOrderItems($orderId)
    {
        $stmt = $this->db->prepare("SELECT * FROM order_items WHERE order_id = :order_id");
        $stmt->bindValue(':order_id', $orderId, SQLITE3_INTEGER);
        $result = $stmt->execute();

        $orderItems = [];
        while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
            $orderItems[] = $row;
        }
        return $orderItems;
    }

    public function updateOrderItem($data)
    {
        // Start a transaction to improve performance and ensure atomicity
        $this->db->exec('BEGIN TRANSACTION');

        // Prepare both statements once to avoid re-preparing inside the loop
        $updateStmt = $this->db->prepare("UPDATE order_items SET material_id = :material_id, quantity = :quantity, received = :received WHERE id = :id");
        $insertStmt = $this->db->prepare("INSERT INTO order_items (order_id, material_id, quantity, received) VALUES (:order_id, :material_id, :quantity, :received)");

        foreach ($data['items'] as $item) {
            if (isset($item['id'])) {
                $this->bindUpdateParameters($updateStmt, $item);
                $updateStmt->execute();
            } else {
                $this->bindInsertParameters($insertStmt, $item);
                $insertStmt->execute();
            }
        }

        // Commit the transaction
        $this->db->exec('COMMIT');

        return ['status' => 'success', 'message' => 'Order item updated successfully'];
    }

    // Function to bind values for updating an order item
    private function bindUpdateParameters($stmt, $data)
    {
        $stmt->bindValue(':material_id', $data['material_id'], SQLITE3_INTEGER);
        $stmt->bindValue(':quantity', $data['quantity'], SQLITE3_INTEGER);
        $stmt->bindValue(':received', $data['received'], SQLITE3_INTEGER);
        $stmt->bindValue(':id', $data['id'], SQLITE3_INTEGER);
    }

    // Function to bind values for inserting an order item
    private function bindInsertParameters($stmt, $data)
    {
        $stmt->bindValue(':order_id', $data['order_id'], SQLITE3_INTEGER);
        $stmt->bindValue(':material_id', $data['material_id'], SQLITE3_INTEGER);
        $stmt->bindValue(':quantity', $data['quantity'], SQLITE3_INTEGER);
        $stmt->bindValue(':received', $data['received'], SQLITE3_INTEGER);
    }

    public function deleteOrderItem($id)
    {
        $stmt = $this->db->prepare("DELETE FROM order_items WHERE id = :id");
        $stmt->bindValue(':id', $id, SQLITE3_INTEGER);
        return $stmt->execute();
    }
}
