<?php
class AuthService {
    public function verify(string $username, string $password, ?array $user): array {
        $username = trim($username);

        if ($username === '' || $password === '') {
            return [
                "status" => 400,
                "data" => [
                    "success" => false,
                    "message" => "Vui lòng nhập đầy đủ thông tin"
                ]
            ];
        }

        if (
            !$user ||
            !isset($user['password'], $user['id'], $user['username'], $user['email'])
        ) {
            return [
                "status" => 401,
                "data" => [
                    "success" => false,
                    "message" => "Sai tài khoản hoặc mật khẩu"
                ]
            ];
        }

        if ($username !== $user['username']) {
            return [
                "status" => 401,
                "data" => [
                    "success" => false,
                    "message" => "Sai tài khoản hoặc mật khẩu"
                ]
            ];
        }

        if (!password_verify($password, $user['password'])) {
            return [
                "status" => 401,
                "data" => [
                    "success" => false,
                    "message" => "Sai tài khoản hoặc mật khẩu"
                ]
            ];
        }

        return [
            "status" => 200,
            "data" => [
                "success" => true,
                "message" => "Đăng nhập thành công",
                "user" => [
                    "id" => $user['id'],
                    "username" => $user['username'],
                    "email" => $user['email']
                ]
            ]
        ];
    }
}