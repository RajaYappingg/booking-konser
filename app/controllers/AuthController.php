<?php

declare(strict_types=1);

class AuthController extends Controller
{
    public function showLogin(): void
    {
        $this->view('auth/login', ['title' => 'Login']);
    }

    public function login(): void
    {
        [$isValid, $errors, $clean] = Validation::validate($_POST, [
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);

        if (!$isValid) {
            flash('danger', 'Please check your login details.');
            redirect('login');
        }

        $userModel = new User();
        $user = $userModel->findByEmail((string)$clean['email']);

        if (!$user || !password_verify((string)$clean['password'], $user['password'])) {
            flash('danger', 'Invalid email or password.');
            redirect('login');
        }

        $_SESSION['user'] = [
            'id' => $user['id'],
            'name' => $user['name'],
            'email' => $user['email'],
            'role' => $user['role'],
        ];

        flash('success', 'Welcome back, ' . $user['name'] . '!');
        redirect('');
    }

    public function showRegister(): void
    {
        $this->view('auth/register', ['title' => 'Create Account']);
    }

    public function register(): void
    {
        [$isValid, $errors, $clean] = Validation::validate($_POST, [
            'name' => 'required|min:3|max:80',
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);

        if (!$isValid) {
            flash('danger', 'Please review the form and try again.');
            redirect('register');
        }

        $userModel = new User();

        if ($userModel->findByEmail((string)$clean['email'])) {
            flash('warning', 'Email is already registered.');
            redirect('register');
        }

        $userModel->create(
            (string)$clean['name'],
            (string)$clean['email'],
            (string)$clean['password']
        );

        flash('success', 'Account created. Please log in.');
        redirect('login');
    }

    public function logout(): void
    {
        session_destroy();
        session_start();
        flash('success', 'You have been logged out.');
        redirect('');
    }
}
