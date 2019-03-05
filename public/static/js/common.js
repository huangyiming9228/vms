var Global = {
  // 获取明天的日期
  getTomorrowDate: function() {
    var date = new Date();
    date.setTime(date.getTime() + 24*60*60*1000);
    var month = date.getMonth()+1;
    month = month < 10 ? ('0' + month) : month;
    var day = date.getDate();
    day = day < 10 ? ('0' + day) : day;
    return date.getFullYear() + '-' + month + '-' + day;
  },

  // 获取特定日期未来几天的日期
  getFutureDate: function(start_date, n) {
    var date = new Date(start_date);
    date.setTime(date.getTime() + 24*60*60*1000*(n - 1));
    var month = date.getMonth()+1;
    month = month < 10 ? ('0' + month) : month;
    var day = date.getDate();
    day = day < 10 ? ('0' + day) : day;
    return date.getFullYear() + '-' + month + '-' + day;
  },

  // 验证车牌号
  checkCarNo: function(carNo) {
    // 新能源车牌号正则
    const regNEV = /^[京津沪渝冀豫云辽黑湘皖鲁新苏浙赣鄂桂甘晋蒙陕吉闽贵粤青藏川宁琼使领A-Z]{1}[A-Z]{1}(([0-9]{5}[DF]$)|([DF][A-HJ-NP-Z0-9][0-9]{4}$))/;
    // 其他汽车正则
    const regOTC = /^[京津沪渝冀豫云辽黑湘皖鲁新苏浙赣鄂桂甘晋蒙陕吉闽贵粤青藏川宁琼使领A-Z]{1}[A-Z]{1}[A-HJ-NP-Z0-9]{4}[A-HJ-NP-Z0-9挂学警港澳]{1}$/;
    if (carNo.length === 7) {
      return regOTC.test(carNo);
    } else if (carNo.length === 8) {
      return regNEV.test(carNo);
    } else {
      return false;
    }
  },

  // 验证身份证号
  checkIdCard: function(t) {
    var arr2 = [7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2];
    var arr3 = [1, 0, 'X', 9, 8, 7, 6, 5, 4, 3, 2];
    if (t.length == 18) {
      var arr = t.split('');
      var s;
      var reg = /^\d+$/;
      var pd = 0;
      for (i = 0; i < 17; i++) {
        if (reg.test(arr[i])) {
          s = true;
          pd = arr[i] * arr2[i] + pd;
        } else {
          s = false;
          break;
        }
      }
      if (s = true) {
        var r = pd % 11;
        if (arr[17] == arr3[r]) {
          //alert("身份证号合法  尾号为："+arr3[r]);
          return true;
        } else {
          //alert("非合法身份证号");
          return false;
        }
      }

    } else {
      //alert("非合法身份证号");
      return false;
    }
  }
};