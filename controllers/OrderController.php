<?php
class OrderController
{
    private $model;

    public function __construct($database)
    {
        $this->model = new Order($database);
    }

    public function index()
    {
        $limit = isset($_GET['limit']) ? intval($_GET['limit']) : 10;
        $offset = isset($_GET['offset']) ? intval($_GET['offset']) : 0;
        $orders = $this->model->getAllOrders($limit, $offset);
        JsonView::render($orders);
    }

    public function show($id)
    {
        $order = $this->model->getOrder($id);
        if ($order) {
            JsonView::render($order);
        } else {
            JsonView::render(['message' => 'Order not found'], 404);
        }
    }

    public function store($data)
    {
        $result = $this->model->createOrder($data);
        if ($result['status'] === 'success') {
            JsonView::render(['message' => $result['message'], 'order_id' => $result['order_id']], 201);
        } else {
            JsonView::render(['message' => $result['message']], 400);
        }
    }

    public function update($id, $data)
    {
        $result = $this->model->updateOrder($id, $data);
        if ($result['status'] === 'success') {
            JsonView::render(['message' => $result['message'], 'status' => 'success']);
        } else {
            JsonView::render(['message' => $result['message']], 400);
        }
    }

    public function destroy($id)
    {
        if ($this->model->deleteOrder($id)) {
            JsonView::render(['message' => 'Order deleted successfully']);
        } else {
            JsonView::render(['message' => 'Failed to delete order'], 400);
        }
    }
}
