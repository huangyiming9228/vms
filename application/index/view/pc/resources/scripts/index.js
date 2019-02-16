$('#btn-submit').on('click', function(e){
    e.preventDefault();
    const form = document.querySelector('#form-submit');
    let validateFlag = true;

    function _alert(errormsg) {
        $('#warning').text(errormsg);
        validateFlag = false;
    }

    if(validateFlag) {
        validateFlag && ((form.emp_no.value) || _alert('员工号为空！'));
        validateFlag && ((form.psw.value) || _alert('密码为空！'))
    }

    if(validateFlag) {
        _alert('登录成功！')
        $(location).attr('href', './resources/html/main.html');
        $.post('http://www.huangcongfu.cn/vms/index/index/login_check',$('#form-submit').serializeArray(),function(response) {
            if(response.code) {
                $(location).attr('href', './resources/html/main.html');
            }else {
                _alert(response.msg);
            }
        });
    }



    // var emp_no = $('#emp_no').val();
    // var psw = $('#psw').val();
    // if(!emp_no || !psw) {
    // 	$('#warning').text('员工号或密码为空！');
    // }else {
    // 	if(emp_no == 'shmily' && psw == '123456') {
    // 		// window.location.href = './resources/html/main.html';
    // 		$(location).attr('href', './resources/html/main.html');
    // 	}else {
    // 		$('#warning').text('员工号或密码错误！');
    // 	}
        
    //     var send_data = {
    //         emp_no: emp_no,
    //         psw: psw,
    //     };
    //     $.post('http://www.huangcongfu.cn/vms/index/index/login_check', send_data, function(response) {
    //         console.log(response);
                // if(response.code){
                // 	$(location).attr('href', './resources/html/main.html');
                // }else {
                // 	$('#warning').text(response.msg);
                // }
    //     });
    // }
    // }
});