<?php
class OrderItemController
{
    private $model;

    public function __construct($database)
    {
        $this->model = new OrderItem($database);
    }

    public function index($orderId)
    {
        $orderItems = $this->model->getOrderItems($orderId);
        JsonView::render($orderItems);
    }

    public function store($data)
    {
        if (!isset($data['order_id'])) {
            JsonView::render(['message' => 'order_id is required'], 400);
            return;
        }
        $result = $this->model->addOrderItems($data);
        if ($result['status'] === 'success') {
            JsonView::render(['message' => $result['message']], 201);
        } else {
            JsonView::render(['message' => $result['message']], 400);
        }
    }

    public function update($id, $data)
    {
        $result = $this->model->updateOrderItem($id, $data);
        if ($result['status'] === 'success') {
            JsonView::render(['message' => $result['message']]);
        } else {
            JsonView::render(['message' => $result['message']], 400);
        }
    }

    public function destroy($id)
    {
        if ($this->model->deleteOrderItem($id)) {
            JsonView::render(['message' => 'Order item deleted successfully']);
        } else {
            JsonView::render(['message' => 'Failed to delete order item'], 400);
        }
    }
}
