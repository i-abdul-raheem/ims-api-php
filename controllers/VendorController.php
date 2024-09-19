<?php
class VendorController
{
    private $model;

    public function __construct($database)
    {
        $this->model = new Vendor($database);
    }

    public function index()
    {
        $limit = isset($_GET['limit']) ? intval($_GET['limit']) : 10;
        $offset = isset($_GET['offset']) ? intval($_GET['offset']) : 0;
        $vendors = $this->model->getAllVendors($limit, $offset);
        JsonView::render($vendors);
    }

    public function show($id)
    {
        $vendor = $this->model->getVendor($id);
        if ($vendor) {
            JsonView::render($vendor);
        } else {
            JsonView::render(['message' => 'Vendor not found'], 404);
        }
    }

    public function store($data)
    {
        $result = $this->model->createVendor($data);
        if ($result['status'] === 'success') {
            JsonView::render(['message' => $result['message']], 201);
        } else {
            JsonView::render(['message' => $result['message']], 400);
        }
    }

    public function update($id, $data)
    {
        $result = $this->model->updateVendor($id, $data);
        if ($result['status'] === 'success') {
            JsonView::render(['message' => $result['message']]);
        } else {
            JsonView::render(['message' => $result['message']], 400);
        }
    }

    public function destroy($id)
    {
        if ($this->model->deleteVendor($id)) {
            JsonView::render(['message' => 'Vendor deleted successfully']);
        } else {
            JsonView::render(['message' => 'Failed to delete vendor'], 400);
        }
    }
}
