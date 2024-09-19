<?php
class CustomerController
{
    private $model;

    public function __construct($database)
    {
        $this->model = new Customer($database);
    }

    public function index()
    {
        $limit = isset($_GET['limit']) ? intval($_GET['limit']) : 10;
        $offset = isset($_GET['offset']) ? intval($_GET['offset']) : 0;
        $customers = $this->model->getAllCustomers($limit, $offset);
        JsonView::render($customers);
    }

    public function show($id)
    {
        $customer = $this->model->getCustomer($id);
        if ($customer) {
            JsonView::render($customer);
        } else {
            JsonView::render(['message' => 'Customer not found'], 404);
        }
    }

    public function store($data)
    {
        $result = $this->model->createCustomer($data);
        if ($result['status'] === 'success') {
            JsonView::render(['message' => $result['message']], 201);
        } else {
            JsonView::render(['message' => $result['message']], 400);
        }
    }

    public function update($id, $data)
    {
        $result = $this->model->updateCustomer($id, $data);
        if ($result['status'] === 'success') {
            JsonView::render(['message' => $result['message']]);
        } else {
            JsonView::render(['message' => $result['message']], 400);
        }
    }

    public function destroy($id)
    {
        if ($this->model->deleteCustomer($id)) {
            JsonView::render(['message' => 'Customer deleted successfully']);
        } else {
            JsonView::render(['message' => 'Failed to delete customer'], 400);
        }
    }
}
