<?php
class MaterialController
{
    private $model;

    public function __construct($database)
    {
        $this->model = new Material($database);
    }

    public function index()
    {
        $limit = isset($_GET['limit']) ? intval($_GET['limit']) : 10;
        $offset = isset($_GET['offset']) ? intval($_GET['offset']) : 0;
        $materials = $this->model->getAllMaterials($limit, $offset);
        JsonView::render($materials);
    }

    public function indexByCategory($id)
    {
        $limit = isset($_GET['limit']) ? intval($_GET['limit']) : 10;
        $offset = isset($_GET['offset']) ? intval($_GET['offset']) : 0;
        $materials = $this->model->getAllMaterialsByCategory($id, $limit, $offset);
        JsonView::render($materials);
    }

    public function show($id)
    {
        $material = $this->model->getMaterial($id);
        if ($material) {
            JsonView::render($material);
        } else {
            JsonView::render(['message' => 'Material not found'], 404);
        }
    }

    public function store($data)
    {
        $result = $this->model->createMaterial($data);
        if ($result['status'] === 'success') {
            JsonView::render(['message' => $result['message']], 201);
        } else {
            JsonView::render(['message' => $result['message']], 400);
        }
    }

    public function update($id, $data)
    {
        $result = $this->model->updateMaterial($id, $data);
        if ($result['status'] === 'success') {
            JsonView::render(['message' => $result['message']]);
        } else {
            JsonView::render(['message' => $result['message']], 400);
        }
    }

    public function destroy($id)
    {
        if ($this->model->deleteMaterial($id)) {
            JsonView::render(['message' => 'Material deleted successfully']);
        } else {
            JsonView::render(['message' => 'Failed to delete material'], 400);
        }
    }
}
