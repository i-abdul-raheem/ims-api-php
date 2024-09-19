<?php
class CategoryController
{
    private $model;

    public function __construct($database)
    {
        $this->model = new Category($database);
    }

    public function index()
    {
        $limit = isset($_GET['limit']) ? intval($_GET['limit']) : 10;
        $offset = isset($_GET['offset']) ? intval($_GET['offset']) : 0;
        $categories = $this->model->getAllCategories($limit, $offset);
        JsonView::render($categories);
    }

    public function show($id)
    {
        $category = $this->model->getCategory($id);
        if ($category) {
            JsonView::render($category);
        } else {
            JsonView::render(['message' => 'Category not found'], 404);
        }
    }

    public function store($data)
    {
        $result = $this->model->createCategory($data);
        if ($result['status'] === 'success') {
            JsonView::render(['message' => $result['message']], 201);
        } else {
            JsonView::render(['message' => $result['message']], 400);
        }
    }

    public function update($id, $data)
    {
        $result = $this->model->updateCategory($id, $data);
        if ($result['status'] === 'success') {
            JsonView::render(['message' => $result['message']]);
        } else {
            JsonView::render(['message' => $result['message']], 400);
        }
    }

    public function destroy($id)
    {
        if ($this->model->deleteCategory($id)) {
            JsonView::render(['message' => 'Category deleted successfully']);
        } else {
            JsonView::render(['message' => 'Failed to delete category'], 400);
        }
    }
}
