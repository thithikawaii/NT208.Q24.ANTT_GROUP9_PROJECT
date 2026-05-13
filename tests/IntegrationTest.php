<?php

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../src/login/api/AuthService.php';

class IntegrationTest extends TestCase
{
    public function testIntegrationLogic(): void
    {
        $auth = new AuthService();

        $result = $auth->verify("user_test", "123456", null);

        $this->assertEquals(
            401,
            $result['status'],
            "Lỗi tích hợp: Logic xử lý đăng nhập không đúng!"
        );
    }
}