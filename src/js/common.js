// 点击注册/登陆界面上方图标切换界面配色方案（深色和浅色）
function changeColor() {
    const settings = document.getElementById('settings');
    const icon = document.getElementById('icon');
    if (icon.className === "original") {
        settings.href = './css/index&register-inverse.css';
        settings.className = "inverse";
        icon.src = "./img/icon-inverse.png";
        icon.className = "inverse";
    } else {
        settings.href = './css/index&register-original.css';
        settings.className = "original";
        icon.src = "./img/icon.png";
        icon.className = "original";
    }
}

/*根据id找到父容器并设置其下所有图片大小，当flag为true，比例较小边变为父容器尺寸（表现为一个方向贴合，另一个溢出）；
为false时，比例较大边变为父容器尺寸（表现为一个方向贴合，另一个在内部）*/
function resizeAndReposition(width, height, id, flag) {
    const parent = document.getElementById(id);
    const img_group = parent.getElementsByTagName("img");

    for (let i = 0; i < img_group.length; i++) {
        const obj = img_group[i];
        let scale;
        if (flag) {
            scale = Math.max(height / obj.height, width / obj.width);
        } else {
            scale = Math.min(height / obj.height, width / obj.width);
        }
        obj.width = obj.width * scale;
        obj.style.top = (height - obj.height) / 2 + "px";
        obj.style.left = (width - obj.width) / 2 + "px";
    }
}

// 控制返回顶部按钮
$(document).ready(function () {
    $("#to-top").hide();
    $(function () {
        //当页面到顶部距离大于300px时，在0.5秒内显现，反之隐藏
        $(window).scroll(function () {
            if ($(window).scrollTop() > 300) {
                $("#to-top").fadeIn(500);
            } else {
                $("#to-top").fadeOut(500);
            }
        });
        //点击"返回顶部"
        $("#to-top").click(function () {
            $('body,html').animate({
                scrollTop: 0
            }, 500);
            return false;
        });
    });
});