<?php
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../src/login/api/AuthService.php';

class AuthServiceTest extends TestCase {
    private $authService;

    protected function setUp(): void {
        $this->authService = new AuthService();
    }

    public function testLoginSuccess() {
        $password = 'password123';

        $mockUser = [
            'id' => 1,
            'username' => 'quyen',
            'email' => 'quyen@test.com',
            'password' => password_hash($password, PASSWORD_DEFAULT)
        ];

        $result = $this->authService->verify('quyen', $password, $mockUser);

        $this->assertEquals(200, $result['status']);
        $this->assertTrue($result['data']['success']);
    }

    public function testEmptyUsername() {
        $result = $this->authService->verify('', '123', null);

        $this->assertEquals(400, $result['status']);
    }

    public function testEmptyPassword() {
        $result = $this->authService->verify('user', '', null);

        $this->assertEquals(400, $result['status']);
    }

    public function testUserNotFound() {
        $result = $this->authService->verify('abc', '123', null);

        $this->assertEquals(401, $result['status']);
    }

    public function testWrongPassword() {
        $mockUser = [
            'id' => 1,
            'username' => 'quyen',
            'email' => 'quyen@test.com',
            'password' => password_hash('correct_pass', PASSWORD_DEFAULT)
        ];

        $result = $this->authService->verify('quyen', 'wrong_pass', $mockUser);

        $this->assertEquals(401, $result['status']);
    }

    public function testMissingFieldUser() {
        $mockUser = [
            'id' => 1,
            'username' => 'quyen',
            'password' => password_hash('123', PASSWORD_DEFAULT)
        ];

        $result = $this->authService->verify('quyen', '123', $mockUser);

        $this->assertEquals(401, $result['status']);
    }

    public function testLongUsername() {
        $longUsername = str_repeat('a', 100);

        $result = $this->authService->verify($longUsername, '123', null);

        $this->assertEquals(401, $result['status']);
    }

    public function testSpecialCharacters() {
        $result = $this->authService->verify("admin' OR '1'='1", '123', null);

        $this->assertEquals(401, $result['status']);
    }

    public function testPasswordWithSpaces() {
        $password = ' pass123 ';

        $mockUser = [
            'id' => 1,
            'username' => 'quyen',
            'email' => 'quyen@test.com',
            'password' => password_hash($password, PASSWORD_DEFAULT)
        ];

        $result = $this->authService->verify('quyen', $password, $mockUser);

        $this->assertEquals(200, $result['status']);
    }

    public function testCaseSensitiveUsername() {
        $mockUser = [
            'id' => 1,
            'username' => 'Quyen',
            'email' => 'quyen@test.com',
            'password' => password_hash('123', PASSWORD_DEFAULT)
        ];

        $result = $this->authService->verify('quyen', '123', $mockUser);

        $this->assertEquals(401, $result['status']);
    }
}