<?php
class Order
{
    private $db;

    public function __construct($database)
    {
        $this->db = $database->getConnection();
    }

    public function getAllOrders($limit = 10, $offset = 0)
    {
        $limit = intval($limit);
        $offset = intval($offset);
        $query = "SELECT * FROM orders LIMIT :limit OFFSET :offset";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':limit', $limit, SQLITE3_INTEGER);
        $stmt->bindValue(':offset', $offset, SQLITE3_INTEGER);
        $result = $stmt->execute();

        $orders = [];
        while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
            $orders[] = $row;
        }
        return $orders;
    }

    public function getOrder($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM orders WHERE id = :id");
        $stmt->bindValue(':id', $id, SQLITE3_INTEGER);
        $result = $stmt->execute();
        return $result->fetchArray(SQLITE3_ASSOC);
    }

    public function getLastInsertId()
    {
        return $this->db->lastInsertRowID();
    }

    public function createOrder($data)
    {
        $stmt = $this->db->prepare("INSERT INTO orders (type, status, customer_id, vendor_id, created_by) VALUES (:type, :status, :customer_id, :vendor_id, :created_by)");
        $stmt->bindValue(':type', $data['type'], SQLITE3_TEXT);
        $stmt->bindValue(':status', $data['status'], SQLITE3_TEXT);
        $stmt->bindValue(':customer_id', $data['customer_id'], SQLITE3_INTEGER);
        $stmt->bindValue(':vendor_id', $data['vendor_id'], SQLITE3_INTEGER);
        $stmt->bindValue(':created_by', $data['created_by'], SQLITE3_INTEGER);

        if ($stmt->execute()) {
            $orderId = $this->getLastInsertId();
            return ['status' => 'success', 'message' => 'Order created successfully', 'order_id' => $orderId];
        } else {
            return ['status' => 'error', 'message' => 'Failed to create order'];
        }
    }

    public function updateOrder($id, $data)
    {
        $stmt = $this->db->prepare("UPDATE orders SET type = :type, status = :status, customer_id = :customer_id, vendor_id = :vendor_id, created_by = :created_by WHERE id = :id");
        $stmt->bindValue(':type', $data['type'], SQLITE3_TEXT);
        $stmt->bindValue(':status', $data['status'], SQLITE3_TEXT);
        $stmt->bindValue(':customer_id', $data['customer_id'], SQLITE3_INTEGER);
        $stmt->bindValue(':vendor_id', $data['vendor_id'], SQLITE3_INTEGER);
        $stmt->bindValue(':created_by', $data['created_by'], SQLITE3_INTEGER);
        $stmt->bindValue(':id', $id, SQLITE3_INTEGER);

        if ($stmt->execute()) {
            return ['status' => 'success', 'message' => 'Order updated successfully'];
        } else {
            return ['status' => 'error', 'message' => 'Failed to update order'];
        }
    }

    public function deleteOrder($id)
    {
        $stmt = $this->db->prepare("DELETE FROM orders WHERE id = :id");
        $stmt->bindValue(':id', $id, SQLITE3_INTEGER);
        return $stmt->execute();
    }
}
