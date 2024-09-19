<?php
class Category
{
    private $db;

    public function __construct($database)
    {
        $this->db = $database->getConnection();
    }

    public function getAllCategories($limit = 10, $offset = 0)
    {
        // Ensure $limit and $offset are integers
        $limit = intval($limit);
        $offset = intval($offset);

        // Prepare the SQL query with LIMIT and OFFSET
        $query = "SELECT * FROM categories LIMIT :limit OFFSET :offset";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':limit', $limit, SQLITE3_INTEGER);
        $stmt->bindValue(':offset', $offset, SQLITE3_INTEGER);

        // Execute the query
        $result = $stmt->execute();

        // Fetch the categories from the result
        $categories = [];
        while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
            $categories[] = $row;
        }

        return $categories;
    }

    public function getCategory($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM categories WHERE id = :id");
        $stmt->bindValue(':id', $id, SQLITE3_INTEGER);
        $result = $stmt->execute();
        return $result->fetchArray(SQLITE3_ASSOC);
    }

    public function createCategory($data)
    {
        // Sanitize input
        $title = $data['title'];

        // Check if a category with the same name already exists
        $checkStmt = $this->db->prepare("SELECT COUNT(*) AS count FROM categories WHERE title = :title");
        $checkStmt->bindValue(':title', $title, SQLITE3_TEXT);
        $checkResult = $checkStmt->execute();
        $checkRow = $checkResult->fetchArray(SQLITE3_ASSOC);

        if ($checkRow['count'] > 0) {
            // A category with the same name already exists
            return [
                'status' => 'error',
                'message' => 'Category with the same name already exists'
            ];
        }

        // Prepare the SQL query to insert a new category
        $stmt = $this->db->prepare("INSERT INTO categories (title) VALUES (:title)");
        $stmt->bindValue(':title', $title, SQLITE3_TEXT);

        // Execute the statement and return success or error response
        if ($stmt->execute()) {
            return [
                'status' => 'success',
                'message' => 'Category created successfully'
            ];
        } else {
            return [
                'status' => 'error',
                'message' => 'Failed to create category'
            ];
        }
    }

    public function updateCategory($id, $data)
    {
        // Sanitize inputs
        $title = $data['title'];

        // Check if a category with the same name already exists
        $checkStmt = $this->db->prepare("SELECT COUNT(*) AS count FROM categories WHERE title = :title AND id != :id");
        $checkStmt->bindValue(':title', $title, SQLITE3_TEXT);
        $checkStmt->bindValue(':id', $id, SQLITE3_INTEGER);
        $checkResult = $checkStmt->execute();
        $checkRow = $checkResult->fetchArray(SQLITE3_ASSOC);

        if ($checkRow['count'] > 0) {
            // A category with the same name exists
            return [
                'status' => 'error',
                'message' => 'Category with the same name already exists'
            ];
        }

        // Prepare the SQL query to update the category
        $stmt = $this->db->prepare("UPDATE categories SET title = :title WHERE id = :id");
        $stmt->bindValue(':title', $title, SQLITE3_TEXT);
        $stmt->bindValue(':id', $id, SQLITE3_INTEGER);

        // Execute the statement and return success or error response
        if ($stmt->execute()) {
            return [
                'status' => 'success',
                'message' => 'Category updated successfully'
            ];
        } else {
            return [
                'status' => 'error',
                'message' => 'Failed to update category'
            ];
        }
    }

    public function deleteCategory($id)
    {
        $stmt = $this->db->prepare("DELETE FROM categories WHERE id = :id");
        $stmt->bindValue(':id', $id, SQLITE3_INTEGER);
        return $stmt->execute();
    }
}
