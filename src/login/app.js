const strongPasswordRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^A-Za-z\d]).{8,}$/;
const taskStore = new Map();

async function parseJsonResponse(response) {
  let data = null;

  try {
    data = await response.json();
  } catch (error) {
    data = null;
  }

  if (!response.ok) {
    const message = data?.message || `Request failed with status ${response.status}`;
    throw new Error(message);
  }

  if (!data || typeof data !== "object") {
    throw new Error("API không trả về JSON hợp lệ");
  }

  return data;
}

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
  const password = passwordInput.value;
  const confirmPassword = confirmPasswordInput.value;

  if (!username || !email || !password || !confirmPassword) {
    message.innerText = "Vui lòng nhập đầy đủ thông tin";
    return;
  }

  if (!strongPasswordRegex.test(password)) {
    message.innerText = "Mật khẩu phải có ít nhất 8 ký tự, chữ hoa, chữ thường, số và ký tự đặc biệt";
    return;
  }

  if (password !== confirmPassword) {
    message.innerText = "Mật khẩu nhập lại không khớp";
    return;
  }

  try {
    const response = await fetch("api/register.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/json"
      },
      body: JSON.stringify({
        username,
        email,
        password,
        confirmPassword
      })
    });

    const data = await parseJsonResponse(response);
    message.innerText = data.message || "";

    if (data.success) {
      window.location.href = "login.html";
    }
  } catch (error) {
    message.innerText = error.message || "Không kết nối được API";
  }
}

async function login() {
  const usernameInput = document.getElementById("loginUsername");
  const passwordInput = document.getElementById("loginPassword");
  const message = document.getElementById("loginMessage");

  if (!usernameInput || !passwordInput || !message) {
    return;
  }

  const username = usernameInput.value.trim();
  const password = passwordInput.value;

  if (!username || !password) {
    message.innerText = "Vui lòng nhập đầy đủ thông tin";
    return;
  }

  try {
    const response = await fetch("api/login.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/json"
      },
      body: JSON.stringify({
        username,
        password
      })
    });

    const data = await parseJsonResponse(response);
    if (!data.user) {
      throw new Error(data.message || "API không trả về thông tin user");
    }
    localStorage.setItem("user", JSON.stringify(data.user));
    window.location.href = "../tasks/index.html";
  } catch (error) {
    message.innerText = error.message || "Không kết nối được API";
  }
}

function logout() {
  localStorage.removeItem("user");
  window.location.href = "login.html";
}

function getStoredUser() {
  const rawUser = localStorage.getItem("user");

  if (!rawUser) {
    return null;
  }

  try {
    return JSON.parse(rawUser);
  } catch (error) {
    localStorage.removeItem("user");
    return null;
  }
}

function requireAuth() {
  const user = getStoredUser();

  if (!user) {
    window.location.href = "login.html";
    return null;
  }

  return user;
}

function setTaskMessage(text, isError = true) {
  const taskMessage = document.getElementById("taskMessage");

  if (!taskMessage) {
    return;
  }

  taskMessage.style.color = isError ? "#dc2626" : "#15803d";
  taskMessage.innerText = text;
}

function resetTaskForm() {
  const taskId = document.getElementById("taskId");
  const title = document.getElementById("taskTitle");
  const description = document.getElementById("taskDescription");
  const status = document.getElementById("taskStatus");

  if (taskId) taskId.value = "";
  if (title) title.value = "";
  if (description) description.value = "";
  if (status) status.value = "todo";

  setTaskMessage("", false);
}

function fillTaskForm(task) {
  const taskId = document.getElementById("taskId");
  const title = document.getElementById("taskTitle");
  const description = document.getElementById("taskDescription");
  const status = document.getElementById("taskStatus");

  if (taskId) taskId.value = task.id;
  if (title) title.value = task.title;
  if (description) description.value = task.description || "";
  if (status) status.value = task.status;

  setTaskMessage("Đang sửa task #" + task.id, false);
  window.scrollTo({ top: 0, behavior: "smooth" });
}

function escapeHtml(value) {
  return String(value)
    .replaceAll("&", "&amp;")
    .replaceAll("<", "&lt;")
    .replaceAll(">", "&gt;")
    .replaceAll('"', "&quot;")
    .replaceAll("'", "&#39;");
}

function startEditTask(taskId) {
  const task = taskStore.get(Number(taskId));
  if (!task) {
    setTaskMessage("Không tìm thấy task cần sửa");
    return;
  }

  fillTaskForm(task);
}

function renderTasks(tasks) {
  const taskList = document.getElementById("taskList");

  if (!taskList) {
    return;
  }

  if (!Array.isArray(tasks) || tasks.length === 0) {
    taskList.innerHTML = '<div class="task-card"><p>Chưa có task nào.</p></div>';
    return;
  }

  taskStore.clear();
  tasks.forEach((task) => {
    taskStore.set(Number(task.id), task);
  });

  taskList.innerHTML = tasks.map((task) => `
    <article class="task-card">
      <h3>${escapeHtml(task.title)}</h3>
      <div class="task-meta">
        <span class="badge ${escapeHtml(task.status)}">${escapeHtml(task.status.toUpperCase())}</span>
        <span class="badge todo">#${escapeHtml(String(task.id))}</span>
      </div>
      <p>${escapeHtml(task.description || "Không có mô tả")}</p>
      <div class="task-actions">
        <button type="button" onclick="startEditTask(${task.id})">Sửa</button>
        <button type="button" class="ghost-button" onclick="changeTaskStatus(${task.id}, 'todo')">Todo</button>
        <button type="button" class="ghost-button" onclick="changeTaskStatus(${task.id}, 'doing')">Doing</button>
        <button type="button" class="ghost-button" onclick="changeTaskStatus(${task.id}, 'done')">Done</button>
      </div>
    </article>
  `).join("");
}

async function loadTasks() {
  const user = requireAuth();
  if (!user) return;

  try {
    const response = await fetch("../tasks/api/list.php");
    const data = await parseJsonResponse(response);
    renderTasks(data.data || []);
  } catch (error) {
    setTaskMessage(error.message || "Không tải được danh sách task");
  }
}

async function createTask() {
  const user = requireAuth();
  if (!user) return;

  const title = document.getElementById("taskTitle")?.value.trim() || "";
  const description = document.getElementById("taskDescription")?.value.trim() || "";
  const status = document.getElementById("taskStatus")?.value || "todo";

  if (!title) {
    setTaskMessage("Tiêu đề task không được để trống");
    return;
  }

  try {
    const response = await fetch("../tasks/api/create.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/json"
      },
      body: JSON.stringify({
        title,
        description,
        status,
        createdBy: user.id
      })
    });

    const data = await parseJsonResponse(response);
    resetTaskForm();
    setTaskMessage(data.message, false);
    await loadTasks();
  } catch (error) {
    setTaskMessage(error.message || "Không tạo được task");
  }
}

async function updateTask() {
  const user = requireAuth();
  if (!user) return;

  const id = document.getElementById("taskId")?.value.trim() || "";
  const title = document.getElementById("taskTitle")?.value.trim() || "";
  const description = document.getElementById("taskDescription")?.value.trim() || "";
  const status = document.getElementById("taskStatus")?.value || "todo";

  if (!id) {
    setTaskMessage("Hãy chọn task cần sửa");
    return;
  }

  if (!title) {
    setTaskMessage("Tiêu đề task không được để trống");
    return;
  }

  try {
    const response = await fetch("../tasks/api/update.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/json"
      },
      body: JSON.stringify({
        id,
        title,
        description,
        status
      })
    });

    const data = await parseJsonResponse(response);
    resetTaskForm();
    setTaskMessage(data.message, false);
    await loadTasks();
  } catch (error) {
    setTaskMessage(error.message || "Không cập nhật được task");
  }
}

async function changeTaskStatus(id, status) {
  const user = requireAuth();
  if (!user) return;

  try {
    const response = await fetch("../tasks/api/update-status.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/json"
      },
      body: JSON.stringify({
        id,
        status
      })
    });

    const data = await parseJsonResponse(response);
    setTaskMessage(data.message, false);
    await loadTasks();
  } catch (error) {
    setTaskMessage(error.message || "Không đổi được trạng thái task");
  }
}

document.addEventListener("DOMContentLoaded", () => {
  const welcomeText = document.getElementById("welcomeText");

  if (!welcomeText) {
    return;
  }

  const user = requireAuth();
  if (!user) {
    return;
  }

  welcomeText.innerText = `Xin chào ${user.username} (${user.email})`;
  loadTasks();
});
