<?php

use PHPUnit\Framework\TestCase;

class LoginTest extends TestCase
{
    private $users;

    protected function setUp(): void
    {
        $this->users = [
            "admin" => "123456Aa@"
        ];
    }

    public function login($username, $password)
    {
        if ($username === null || $username === "") {
            return [400, "Username is required"];
        }

        if ($password === null || $password === "") {
            return [400, "Password is required"];
        }

        $username = trim($username);

        if (strlen($username) > 50) {
            return [400, "Username too long"];
        }

        if (strpos($username, "<script>") !== false) {
            return [400, "Invalid format"];
        }

        if (!preg_match('/^(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{6,}$/', $password)) {
            return [400, "Password must include uppercase, number, special char"];
        }

        if (preg_match('/(\'|--|or 1=1)/i', $username)) {
            return [401, "Invalid credentials"];
        }

        if (!array_key_exists($username, $this->users)) {
            return [401, "Invalid credentials"];
        }

        if ($this->users[$username] !== $password) {
            return [401, "Invalid credentials"];
        }

        return [200, "Login success"];
    }

    public function test01()
    {
        [$c] = $this->login("", "123456Aa@");
        $this->assertEquals(400, $c);
    }

    public function test02()
    {
        [$c] = $this->login("admin", "");
        $this->assertEquals(400, $c);
    }

    public function test03()
    {
        $u = str_repeat("a", 51);
        [$c] = $this->login($u, "123456Aa@");
        $this->assertEquals(400, $c);
    }

    public function test04()
    {
        [$c] = $this->login("admin", "12345");
        $this->assertEquals(400, $c);
    }

    public function test05()
    {
    [$c] = $this->login("' OR 1=1 --", "any");
    $this->assertEquals(400, $c);
    }

    public function test06()
    {
        [$c] = $this->login("not_exist", "123456Aa@");
        $this->assertEquals(401, $c);
    }

    public function test07()
    {
        [$c] = $this->login("admin", "WrongPass123!");
        $this->assertEquals(401, $c);
    }

    public function test08()
    {
        [$c] = $this->login("<script>alert(1)</script>", "123456Aa@");
        $this->assertEquals(400, $c);
    }

    public function test09()
    {
        [$c] = $this->login(" admin ", "123456Aa@");
        $this->assertEquals(200, $c);
    }

    public function test10()
    {
        [$c] = $this->login("admin", "123456Aa@");
        $this->assertEquals(200, $c);
    }

    public function test11()
    {
        [$c] = $this->login("admin", "123456Aa@");
        $this->assertEquals(200, $c);
    }

    public function test12()
    {
        [$c] = $this->login(null, "123456Aa@");
        $this->assertEquals(400, $c);
    }
}