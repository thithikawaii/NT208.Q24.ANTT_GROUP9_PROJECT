async function register() {
  const usernameInput = document.getElementById("registerUsername");
  const emailInput = document.getElementById("registerEmail");
  const passwordInput = document.getElementById("registerPassword");
  const confirmPasswordInput = document.getElementById("registerConfirmPassword");
  const message = document.getElementById("registerMessage");

  if (!usernameInput || !emailInput || !passwordInput || !confirmPasswordInput || !message) {
    return;
  }

  const username = usernameInput.value.trim();
  const email = emailInput.value.trim();
  const password = passwordInput.value.trim();
  const confirmPassword = confirmPasswordInput.value.trim();

  if (username === "" || email === "" || password === "" || confirmPassword === "") {
    message.innerText = "Vui lòng nhập đầy đủ thông tin";
    return;
  }

  if (password !== confirmPassword) {
    message.innerText = "Mật khẩu nhập lại không khớp";
    return;
  }

  try {
    const res = await fetch("api/register.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/json"
      },
      body: JSON.stringify({
        username: username,
        email: email,
        password: password,
        confirmPassword: confirmPassword
      })
    });

    const data = await res.json();
    message.innerText = data.message;

    if (data.success) {
      alert("Đăng ký thành công");
      window.location.href = "index.html";
    }
  } catch (error) {
    console.log(error);
    message.innerText = "Không kết nối được API";
  }
}

async function login() {
  const usernameInput = document.getElementById("loginUsername");
  const passwordInput = document.getElementById("loginPassword");
  const message = document.getElementById("loginMessage");

  if (!usernameInput || !passwordInput || !message) {
    console.log("Thiếu id ở trang index.html");
    return;
  }

  const username = usernameInput.value.trim();
  const password = passwordInput.value.trim();

  if (username === "" || password === "") {
    message.innerText = "Vui lòng nhập đầy đủ thông tin";
    return;
  }

  try {
    const res = await fetch("api/login.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/json"
      },
      body: JSON.stringify({
        username: username,
        password: password
      })
    });

    const data = await res.json();
    message.innerText = data.message;

    if (data.success) {
      localStorage.setItem("user", JSON.stringify(data.user));
      alert("Đăng nhập thành công");
      window.location.href = "home.html";
    }
  } catch (error) {
    console.log(error);
    message.innerText = "Không kết nối được API";
  }
}

function logout() {
  localStorage.removeItem("user");
  window.location.href = "index.html";
}

const welcome = document.getElementById("welcome");
if (welcome) {
  const user = JSON.parse(localStorage.getItem("user"));

  if (user) {
    welcome.innerText = "Xin chào, " + user.username + " (" + user.email + ")";
  } else {
    window.location.href = "index.html";
  }
}