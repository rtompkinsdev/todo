const clear = document.querySelector(".clear");
const dateElement = document.getElementById("date");
const list = document.getElementById("list");
const input = document.getElementById("input");

const CHECK = "fa-check-circle";
const UNCHECK = "fa-circle-thin";
const LINE_THROUGH = "lineThrough";

let TODOLIST, id;
const options = { weekday: "long", month: "short", day: "numeric" };
const today = new Date();

dateElement.innerHTML = today.toLocaleDateString("en-US", options);

getToDoArr();

clear.addEventListener("click", function () {
  localStorage.clear();
  location.reload();
});

function getToDoArr() {
  $.ajax({
    url: "getTodoList.php",
    method: "GET",
    success: function (res) {
      let todoObj = JSON.parse(res);

      for (var key in todoObj) {
        if (typeof todoObj[key].id !== "undefined") {
          var isComplete = false;
          if (todoObj[key].complete == 1) {
            isComplete = true;
          }

          var id = todoObj[key].id;
          var thisName = todoObj[key].name;
          var completed = isComplete ? CHECK : UNCHECK;
          var doStrikeThru = isComplete ? LINE_THROUGH : "";
          var list = document.getElementById("list");

          var todoItm = `<li class="item">
          <i class="fa ${completed} co" job="complete" id="${id}"></i>
          <p class="text ${doStrikeThru}">${thisName}</p>
          <i class="fa fa-trash-o de" job="delete" id="${id}"></i>
        </li>`;

          var position = "beforeend";
          list.insertAdjacentHTML(position, todoItm);
        }
      }
    }
  });
}

function addToDo(name, complete, trash) {
  $.ajax({
    url: "insertTodo.php?name=" + name + "&complete=false&trash=false",
    data: { list: TODOLIST },
    success: function (res) {
      console.log(res);

      completed = complete ? CHECK : UNCHECK;
      const LINE_THROUGH = "";
      var list = document.getElementById("list");

      var todoItm = `<li class="item">
                          <i class="fa ${completed} co" job="complete" id="${id}"></i>
                          <p class="text ${LINE_THROUGH}">${name}</p>
                          <i class="fa fa-trash-o de" job="delete" id="${id}"></i>
                        </li>
                      `;

      var position = "beforeend";

      list.insertAdjacentHTML(position, todoItm);
    }
  });
}

document.addEventListener("keyup", function (even) {
  if (event.keyCode == 13) {
    const name = input.value;

    if (name) {
      addToDo(name, false, false);
    }
    input.value = "";
  }
});

list.addEventListener("click", function (event) {
  //   console.log(event.target);

  let element, elementJob, id;
  complete = false;

  if (event.target.classList.contains("de")) {
    if (event.target.attributes.job.value == "delete") {
      event.target.parentNode.parentNode.removeChild(event.target.parentNode);
      id = event.target.id;
      deleteTodo(id);
    }
  } else if (event.target.classList.contains("co")) {
    element = event.target;
    elementJob = element.attributes.job.value;
    element.classList.toggle(CHECK);
    element.classList.toggle(UNCHECK);
    element.parentNode.querySelector(".text").classList.toggle(LINE_THROUGH);

    if (element.nextElementSibling.classList.contains("lineThrough")) {
      complete = true;
    }
    id = element.id;

    if (elementJob == "complete") {
      completeToDo(id, complete);
    }
  } else if (event.target.classList.contains("text")) {
    element = event.target;
    elementJob = element.previousElementSibling.attributes.job.value;
    element.previousElementSibling.classList.toggle(CHECK);
    element.previousElementSibling.classList.toggle(UNCHECK);
    element.parentNode.querySelector(".text").classList.toggle(LINE_THROUGH);

    if (element.classList.contains("lineThrough")) {
      complete = true;
    }

    id = element.previousElementSibling.id;

    if (elementJob == "complete") {
      completeToDo(id, complete);
    }
  }
});

function completeToDo(id, complete) {
  $.ajax({
    url: "completeTodo.php?id=" + id + "&complete=" + complete,
    method: "GET",
    success: function (res) {
      console.log(res);
    }
  });
}

function deleteTodo(id) {
  $.ajax({
    url: "deleteTodo.php?id=" + id,
    method: "GET",
    success: function (res) {
      console.log(res);
    }
  });
}
