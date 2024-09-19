<?php
class UserController
{
    private $model;

    public function __construct($database)
    {
        $this->model = new User($database);
    }

    public function store($data)
    {
        if ($this->model->createUser($data)) {
            JsonView::render(['message' => 'User created successfully'], 201);
        } else {
            JsonView::render(['message' => 'Failed to create user'], 400);
        }
    }

    public function updatePassword($id, $data)
    {
        if ($this->model->updatePassword($id, $data)) {
            JsonView::render(['message' => 'Password updated successfully']);
        } else {
            JsonView::render(['message' => 'Failed to update user'], 400);
        }
    }

    public function updateEmail($id, $data)
    {
        if ($this->model->updateEmail($id, $data)) {
            JsonView::render(['message' => 'Email updated successfully']);
        } else {
            JsonView::render(['message' => 'Failed to update user'], 400);
        }
    }

    public function updateUsername($id, $data)
    {
        if ($this->model->updateUsername($id, $data)) {
            JsonView::render(['message' => 'Username updated successfully']);
        } else {
            JsonView::render(['message' => 'Failed to update user'], 400);
        }
    }

    public function login($data)
    {
        // Attempt to log in the user using the model
        $loginResult = $this->model->login($data);

        if ($loginResult['status'] === 'success') {
            $_SESSION['user_id'] = $loginResult['user']['id'];
            $_SESSION['username'] = $loginResult['user']['username'];
            $_SESSION['logged_in'] = true;
            JsonView::render([
                'message' => 'Login successful',
                'user' => $loginResult['user']
            ]);
        } else {
            JsonView::render([
                'message' => 'Invalid username or password'
            ], 401);
        }
    }
}
