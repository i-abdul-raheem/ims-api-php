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
        $query = "SELECT * FROM orders";
        $stmt = $this->db->prepare($query);
        $result = $stmt->execute();

        $orders = [];
        while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
            if ($row["customer_id"] != 0) {
                $stmt1 = $this->db->prepare("SELECT * FROM customers WHERE id = :id");
                $stmt1->bindValue(':id', $row["customer_id"], SQLITE3_INTEGER);
                $result1 = $stmt1->execute();
                $row["customer"] = $result1->fetchArray(SQLITE3_ASSOC);
            } else {
                $stmt1 = $this->db->prepare("SELECT * FROM vendors WHERE id = :id");
                $stmt1->bindValue(':id', $row["vendor_id"], SQLITE3_INTEGER);
                $result1 = $stmt1->execute();
                $row["vendor"] = $result1->fetchArray(SQLITE3_ASSOC);
            }
            $orders[] = $row;
        }
        return $orders;
    }

    public function getOrder($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM orders WHERE id = :id");
        $stmt->bindValue(':id', $id, SQLITE3_INTEGER);
        $result = $stmt->execute();
        $row = $result->fetchArray(SQLITE3_ASSOC);
        if ($row["customer_id"] != 0) {
            $stmt1 = $this->db->prepare("SELECT * FROM customers WHERE id = :id");
            $stmt1->bindValue(':id', $row["customer_id"], SQLITE3_INTEGER);
            $result1 = $stmt1->execute();
            $row["company"] = $result1->fetchArray(SQLITE3_ASSOC);
        } else {
            $stmt1 = $this->db->prepare("SELECT * FROM vendors WHERE id = :id");
            $stmt1->bindValue(':id', $row["vendor_id"], SQLITE3_INTEGER);
            $result1 = $stmt1->execute();
            $row["company"] = $result1->fetchArray(SQLITE3_ASSOC);
        }
        return $row;
    }

    public function getLastInsertId()
    {
        return $this->db->lastInsertRowID();
    }

    public function createOrder($data)
    {
        $stmt = $this->db->prepare("INSERT INTO orders (type, status, customer_id, vendor_id, created_by) VALUES (:type, :status, :customer_id, :vendor_id, :created_by)");
        $stmt->bindValue(':type', $data['type'], SQLITE3_TEXT);
        $stmt->bindValue(':status', 'pending', SQLITE3_TEXT);
        $stmt->bindValue(':customer_id', $data['customer_id'], SQLITE3_INTEGER);
        $stmt->bindValue(':vendor_id', $data['vendor_id'], SQLITE3_INTEGER);
        $stmt->bindValue(':created_by', $_SESSION['user_id'], SQLITE3_INTEGER);

        if ($stmt->execute()) {
            $orderId = $this->getLastInsertId();
            return ['status' => 'success', 'message' => 'Order created successfully', 'order_id' => $orderId];
        } else {
            return ['status' => 'error', 'message' => 'Failed to create order'];
        }
    }

    public function updateOrder($id, $data)
    {
        $stmt = $this->db->prepare("UPDATE orders SET status = :status WHERE id = :id");
        $stmt->bindValue(':status', $data['status'], SQLITE3_TEXT);
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
