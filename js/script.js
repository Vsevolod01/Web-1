let X, Y, R;
const GRAPH_WIDTH = 300;
const GRAPH_HEIGHT = 300;
const yTextField = $("#Y-text")[0];
// const video = $("#myVideo");

// document.addEventListener("keypress", function () {
//     video[0].currentTime = video[0].duration;
// });

// Слушатель для кнопок submit и clear
document.addEventListener("DOMContentLoaded", function () {
    $("#submit-button").on("click", submit);
    $("#clear-button").on("click", clearButton);
});

// Проверка Y
function checkY() {
    Y = yTextField.value.replace(",", ".");
    if (Y.trim() === "") {
        errorY("Заполните поле");
        return false;
    } else if (!isFinite(Y)) {
        errorY("Y должно быть числом!");
        return false;
    } else {
        let dot_pos = Y.indexOf(".");
        if (Y < 5 && Y > -5 || Y.substring(0, dot_pos) === "-4" || Y.substring(0, dot_pos) === "4") {
            yTextField.setCustomValidity("");
            return true;
        } else {
            errorY("Вы вышли за пределы диапазона (-5; 5)!");
            return false;
        }
    }
}

// Окно ошибки и красные границы поля ввода
function errorY(error) {
    yTextField.setCustomValidity(error);
    yTextField.reportValidity();
    yTextField.style.borderColor = "red";
}

// Сеттер X и R
function setX() {
    X = $("option[name=x]:selected").val();
}

function setR() {
    R = $("input[name=r]:checked").val();
}

// Функции для расчёта координат точки
function calculateX(x, r) {
    return x / r * 100 + GRAPH_WIDTH / 2;
}

function calculateY(y, r) {
    return GRAPH_HEIGHT / 2 - y / r * 100;
}

// Функция для кнопки Submit
function submit(e) {
    e.preventDefault();
    if (!checkY()) return;
    setX();
    setR();

    let point = $("circle");
    let request = ("x=" + X + "&y=" + Y + "&r=" + R);
    const xGraph = calculateX(X, R), yGraph = calculateY(Y, R);

    point.attr({
        cx: xGraph, cy: yGraph, visibility: "visible"
    });
    fetch("api/check.php", {
        method: 'POST', headers: {
            'Content-Type': 'application/x-www-form-urlencoded;charset=UTF-8'
        }, body: request
    })
        .then(response => response.text())
        .then(response => addToTable(response));
}

// Добавление в таблицу
function addToTable(response) {
    response = JSON.parse(response);
    // if (response['atArea'] === "Промах") {
    //     video[0].setAttribute("src", "fat_man.mp4");
    // } else {
    //     video[0].setAttribute("src", "kaboom.mp4");
    // }
    // video.show();
    // video[0].play();
    // video[0].addEventListener("ended", function () {
    //     video.hide();
        $("#time").text("Время на сервере: " + response['currentTime'] + "  Скрипт отработал за: " + response['time'] + "ms");
        $("#table-check").find('tbody')
           // .append($('<tr style="animation: anim-table 0.5s ease-in-out forwards;">')
		.append($('<tr>')
                .append($('<td>')
                    .text(response['r']))
                .append($('<td>')
                    .text(response['x']))
                .append($('<td>')
                    .text(response['y']))
                .append($('<td style=\"background-color: ' + (response['atArea'] === "Попадание" ? "green;" : "red;") + "\">")
                    .text(response['atArea'])));
    // }, {once: true});
}

// Функция для кнопки Clear
const clearButton = function (e) {
    e.preventDefault();
    $("#table-check tr").remove();
    $("#table-check").find('tbody')
        .append($('<tr class="table-header">')
            .append($('<th scope="col">')
                .text("R"))
            .append($('<th scope="col">')
                .text("X"))
            .append($('<th scope="col">')
                .text("Y"))
            .append($('<th scope="col">')
                .text("Результат")));
};
