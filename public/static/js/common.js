var Global = {
  // 获取明天的日期
  getTomorrowDate: function() {
    var date = new Date();
    date.setTime(date.getTime() + 24*60*60*1000);
    var month = date.getMonth()+1;
    month = month < 10 ? ('0' + month) : month;
    return date.getFullYear() + '-' + month + '-' + date.getDate();
  }
};