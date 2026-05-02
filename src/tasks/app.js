const taskStore = new Map();

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
    window.location.href = "../login/index.html";
    return null;
  }

  return user;
}

function logout() {
  localStorage.removeItem("user");
  window.location.href = "../login/index.html";
}

async function parseJsonResponse(response) {
  let data = null;

  try {
    data = await response.json();
  } catch (error) {
    data = null;
  }

  if (!response.ok) {
    throw new Error(data?.message || `Request failed with status ${response.status}`);
  }

  return data;
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

  setTaskMessage("Dang sua task #" + task.id, false);
  window.scrollTo({ top: 0, behavior: "smooth" });
}

function renderTasks(tasks) {
  const taskList = document.getElementById("taskList");

  if (!taskList) {
    return;
  }

  if (!Array.isArray(tasks) || tasks.length === 0) {
    taskList.innerHTML = '<div class="task-card"><p>Chua co task nao.</p></div>';
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
      <p>${escapeHtml(task.description || "Khong co mo ta")}</p>
      <div class="task-actions">
        <button type="button" onclick="startEditTask(${task.id})">Sua</button>
        <button type="button" class="ghost-button" onclick="changeTaskStatus(${task.id}, 'todo')">Todo</button>
        <button type="button" class="ghost-button" onclick="changeTaskStatus(${task.id}, 'doing')">Doing</button>
        <button type="button" class="ghost-button" onclick="changeTaskStatus(${task.id}, 'done')">Done</button>
      </div>
    </article>
  `).join("");
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
    setTaskMessage("Khong tim thay task can sua");
    return;
  }

  fillTaskForm(task);
}

async function loadTasks() {
  requireAuth();

  try {
    const response = await fetch("api/list.php");
    const data = await parseJsonResponse(response);
    renderTasks(data.data || []);
  } catch (error) {
    setTaskMessage(error.message || "Khong tai duoc danh sach task");
  }
}

async function createTask() {
  const user = requireAuth();
  if (!user) return;

  const title = document.getElementById("taskTitle")?.value.trim() || "";
  const description = document.getElementById("taskDescription")?.value.trim() || "";
  const status = document.getElementById("taskStatus")?.value || "todo";

  if (!title) {
    setTaskMessage("Tieu de task khong duoc de trong");
    return;
  }

  try {
    const response = await fetch("api/create.php", {
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
    setTaskMessage(error.message || "Khong tao duoc task");
  }
}

async function updateTask() {
  requireAuth();

  const id = document.getElementById("taskId")?.value.trim() || "";
  const title = document.getElementById("taskTitle")?.value.trim() || "";
  const description = document.getElementById("taskDescription")?.value.trim() || "";
  const status = document.getElementById("taskStatus")?.value || "todo";

  if (!id) {
    setTaskMessage("Hay chon task can sua");
    return;
  }

  if (!title) {
    setTaskMessage("Tieu de task khong duoc de trong");
    return;
  }

  try {
    const response = await fetch("api/update.php", {
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
    setTaskMessage(error.message || "Khong cap nhat duoc task");
  }
}

async function changeTaskStatus(id, status) {
  requireAuth();

  try {
    const response = await fetch("api/update-status.php", {
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
    setTaskMessage(error.message || "Khong doi duoc trang thai task");
  }
}

document.addEventListener("DOMContentLoaded", () => {
  const user = requireAuth();
  if (!user) return;

  const welcomeText = document.getElementById("welcomeText");
  if (welcomeText) {
    welcomeText.innerText = `Xin chao ${user.username} (${user.email})`;
  }

  loadTasks();
});
