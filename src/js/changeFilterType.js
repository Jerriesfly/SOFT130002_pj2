const title = document.getElementById("form-title");
const description = document.getElementById("form-description");
const filter_title = document.getElementById("filter-title");
const filter_description = document.getElementById("filter-description");

// 显示标题搜索表格
function titleOnDisplay() {
    title.className = "active";
    description.className = "";
    filter_title.className = "right selection selected";
    filter_description.className = "right selection";
}

// 显示描述搜索表格
function descriptionOnDisplay() {
    title.className = "";
    description.className = "active";
    filter_title.className = "right selection";
    filter_description.className = "right selection selected";
}

