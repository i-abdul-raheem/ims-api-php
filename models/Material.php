<?php
class Material
{
    private $db;

    public function __construct($database)
    {
        $this->db = $database->getConnection();
    }

    public function getAllMaterials($limit = 10, $offset = 0)
    {
        // Ensure $limit and $offset are integers
        $limit = intval($limit);
        $offset = intval($offset);

        // Prepare the SQL query with LIMIT and OFFSET
        $query = "SELECT * FROM materials LIMIT :limit OFFSET :offset";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':limit', $limit, SQLITE3_INTEGER);
        $stmt->bindValue(':offset', $offset, SQLITE3_INTEGER);

        // Execute the query
        $result = $stmt->execute();

        // Fetch the materials from the result
        $materials = [];
        while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
            $materials[] = $row;
        }

        return $materials;
    }

    public function getAllMaterialsByCategory($id, $limit = 10, $offset = 0)
    {
        // Ensure $limit and $offset are integers
        $limit = intval($limit);
        $offset = intval($offset);

        // Prepare the SQL query with LIMIT and OFFSET
        $query = "SELECT * FROM materials WHERE category = :id LIMIT :limit OFFSET :offset";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':limit', $limit, SQLITE3_INTEGER);
        $stmt->bindValue(':offset', $offset, SQLITE3_INTEGER);
        $stmt->bindValue(':id', $id, SQLITE3_INTEGER);

        // Execute the query
        $result = $stmt->execute();

        // Fetch the materials from the result
        $materials = [];
        while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
            $materials[] = $row;
        }

        return $materials;
    }

    public function getMaterial($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM materials WHERE id = :id");
        $stmt->bindValue(':id', $id, SQLITE3_INTEGER);
        $result = $stmt->execute();
        return $result->fetchArray(SQLITE3_ASSOC);
    }

    public function createMaterial($data)
    {
        // Sanitize input
        $title = $data['title'];
        $category = $data['category'];

        // Check if a material with the same name already exists
        $checkStmt = $this->db->prepare("SELECT COUNT(*) AS count FROM materials WHERE title = :title AND category = :category");
        $checkStmt->bindValue(':title', $title, SQLITE3_TEXT);
        $checkStmt->bindValue(':category', $category, SQLITE3_INTEGER);
        $checkResult = $checkStmt->execute();
        $checkRow = $checkResult->fetchArray(SQLITE3_ASSOC);

        if ($checkRow['count'] > 0) {
            // A material with the same name already exists
            return [
                'status' => 'error',
                'message' => 'Material with the same name already exists'
            ];
        }

        // Prepare the SQL query to insert a new material
        $stmt = $this->db->prepare("INSERT INTO materials (title, category) VALUES (:title, :category)");
        $stmt->bindValue(':title', $title, SQLITE3_TEXT);
        $stmt->bindValue(':category', $category, SQLITE3_INTEGER);

        // Execute the statement and return success or error response
        if ($stmt->execute()) {
            return [
                'status' => 'success',
                'message' => 'Material created successfully'
            ];
        } else {
            return [
                'status' => 'error',
                'message' => 'Failed to create material'
            ];
        }
    }

    public function updateMaterial($id, $data)
    {
        // Sanitize inputs
        $title = $data['title'];
        $category = $data['category'];

        // Check if a material with the same name already exists
        $checkStmt = $this->db->prepare("SELECT COUNT(*) AS count FROM materials WHERE title = :title AND category = :category AND id != :id");
        $checkStmt->bindValue(':title', $title, SQLITE3_TEXT);
        $checkStmt->bindValue(':category', $category, SQLITE3_INTEGER);
        $checkStmt->bindValue(':id', $id, SQLITE3_INTEGER);
        $checkResult = $checkStmt->execute();
        $checkRow = $checkResult->fetchArray(SQLITE3_ASSOC);

        if ($checkRow['count'] > 0) {
            // A material with the same name exists
            return [
                'status' => 'error',
                'message' => 'Material with the same name already exists'
            ];
        }

        // Prepare the SQL query to update the material
        $stmt = $this->db->prepare("UPDATE materials SET title = :title, category = :category WHERE id = :id");
        $stmt->bindValue(':title', $title, SQLITE3_TEXT);
        $stmt->bindValue(':category', $category, SQLITE3_TEXT);
        $stmt->bindValue(':id', $id, SQLITE3_INTEGER);

        // Execute the statement and return success or error response
        if ($stmt->execute()) {
            return [
                'status' => 'success',
                'message' => 'Material updated successfully'
            ];
        } else {
            return [
                'status' => 'error',
                'message' => 'Failed to update material'
            ];
        }
    }

    public function deleteMaterial($id)
    {
        $stmt = $this->db->prepare("DELETE FROM materials WHERE id = :id");
        $stmt->bindValue(':id', $id, SQLITE3_INTEGER);
        return $stmt->execute();
    }
}
