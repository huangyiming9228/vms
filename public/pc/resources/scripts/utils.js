var Utils = {
  // 获取明天的日期
  getTomorrowDate: function() {
    var date = new Date();
    date.setTime(date.getTime() + 24*60*60*1000);
    var month = date.getMonth()+1;
    month = month < 10 ? ('0' + month) : month;
    return date.getFullYear() + '-' + month + '-' + date.getDate();
  },

  // 验证车牌号
  checkCarNo: function(carNo) {
    // 新能源车牌号正则
    var regNEV = /^[京津沪渝冀豫云辽黑湘皖鲁新苏浙赣鄂桂甘晋蒙陕吉闽贵粤青藏川宁琼使领A-Z]{1}[A-Z]{1}(([0-9]{5}[DF]$)|([DF][A-HJ-NP-Z0-9][0-9]{4}$))/;
    // 其他汽车正则
    var regOTC = /^[京津沪渝冀豫云辽黑湘皖鲁新苏浙赣鄂桂甘晋蒙陕吉闽贵粤青藏川宁琼使领A-Z]{1}[A-Z]{1}[A-HJ-NP-Z0-9]{4}[A-HJ-NP-Z0-9挂学警港澳]{1}$/;
    if (carNo.length === 7) {
      return regOTC.test(carNo);
    } else if (carNo.length === 8) {
      return regNEV.test(carNo);
    } else {
      return false;
    }
  },

  // 利用xlsx解析excel
  // 利用promise异步解析
  // @2018-10-25
  // @return array
  import_excel_data: function(file_obj) {
    return new Promise(function (resolve, reject) {
      let real_data;
      let excel_data;
      if (!file_obj.files) {
        reject('not a files');
        return;
      }
      var file = file_obj.files[0];
      var reader = new FileReader();
      reader.onload = function (e) {
        var data = e.target.result;
        excel_data = XLSX.read(data, {
          type: 'binary'
        });
        real_data = XLSX.utils.sheet_to_json(excel_data.Sheets[excel_data.SheetNames[0]]);
        resolve(real_data);
      };
      reader.readAsBinaryString(file);
    });
  }
}