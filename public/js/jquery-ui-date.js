$(function() {
  if ($("#from").datepicker) {
    $("#from").datepicker({
      changeMonth: true,
          changeYear: true,
          dateFormat:"yymm",
          changeMonth: true,
          nextText:'next',
          showOn: "both",
          buttonImage: "/ams/public/images/date.png",
          buttonImageOnly: true,
          monthNamesShort: ["1月", "2月", "3月", "4月", "5月", "6月", "7月", "8月", "9月", "10月", "11月", "12月"],
          //dayNamesMin: ["日", "一", "二", "三", "四", "五", "六"]
          onClose: function(dateText, inst) {

                     
          },
          beforeShow:function(){
              setTimeout(function(){$("#ui-datepicker-div table").css("display","none")},1)
              
          },
          onChangeMonthYear   :function(){
              setTimeout(function(){$("#ui-datepicker-div table").css("display","none")},1)

              var month = parseInt($("#ui-datepicker-div .ui-datepicker-month :selected").val())+1;
              var year = $("#ui-datepicker-div .ui-datepicker-year :selected").val();
              if (month<10) {

                  month = '0'+month;
              }
              $("#from").val(year+month)
          }
      });
  }

    
	if ($("#to").datepicker) {
		$("#to").datepicker({
			    changeMonth: true,
      		changeYear: true,
      		dateFormat:"yymm",
      		showOn: "both",
          buttonImage: "/ams/public/images/date.png",
	        buttonImageOnly: true,
	        monthNamesShort: ["1月", "2月", "3月", "4月", "5月", "6月", "7月", "8月", "9月", "10月", "11月", "12月"],
          onClose: function(dateText, inst) {
                     
          },
          beforeShow:function(){
              setTimeout(function(){$("#ui-datepicker-div table").css("display","none")},1)
              
          },
          onChangeMonthYear   :function(){
                    setTimeout(function(){$("#ui-datepicker-div table").css("display","none")},1)
                    var month = parseInt($("#ui-datepicker-div .ui-datepicker-month :selected").val())+1;
                    var year = $("#ui-datepicker-div .ui-datepicker-year :selected").val();
                    if (month<10) {
                      month = '0'+month;
                    }
                    $("#to").val(year+month)
          }
        });
  }

  if ($("#day").datepicker) {
    $("#day").datepicker({
        changeMonth: true,
        changeYear: true,
        dateFormat:"yy-mm-dd",
        showOn: "both",
        buttonImage: "/ams/public/images/date.png",
        buttonImageOnly: true,
        monthNamesShort: ["1月", "2月", "3月", "4月", "5月", "6月", "7月", "8月", "9月", "10月", "11月", "12月"],
        dayNamesMin: ["日", "一", "二", "三", "四", "五", "六"]
    });
  }

  if ($("#ToDay").datepicker) {
    $("#ToDay").datepicker({
        changeMonth: true,
        changeYear: true,
        dateFormat:"yy-mm-dd",
        showOn: "both",
        buttonImage: "/ams/public/images/date.png",
        buttonImageOnly: true,
        monthNamesShort: ["1月", "2月", "3月", "4月", "5月", "6月", "7月", "8月", "9月", "10月", "11月", "12月"],
        dayNamesMin: ["日", "一", "二", "三", "四", "五", "六"]
    });
  }

  if ($("#time").datetimepicker) {
        $("#time").datetimepicker({
			    changeMonth: true,
      		changeYear: true,
      		dateFormat:"yy-mm-dd",
          timeFormat: "HH:mm",
      		showOn: "both",
          buttonImage: "/ams/public/images/date.png",
	        buttonImageOnly: true,
	        monthNamesShort: ["1月", "2月", "3月", "4月", "5月", "6月", "7月", "8月", "9月", "10月", "11月", "12月"],
          dayNamesMin: ["日", "一", "二", "三", "四", "五", "六"],
          timeText:"时间",
          minuteText:"分钟",
          hourText:"小时",
          currentText:"当前",
          closeText:"关闭"
        });
  }

  if ($("#Totime").datetimepicker) {
        $("#Totime").datetimepicker({
          changeMonth: true,
          changeYear: true,
          dateFormat:"yy-mm-dd",
          timeFormat: "HH:mm",
          showOn: "both",
          buttonImage: "/ams/public/images/date.png",
          buttonImageOnly: true,
          monthNamesShort: ["1月", "2月", "3月", "4月", "5月", "6月", "7月", "8月", "9月", "10月", "11月", "12月"],
          dayNamesMin: ["日", "一", "二", "三", "四", "五", "六"],
          timeText:"时间",
          minuteText:"分钟",
          hourText:"小时",
          currentText:"当前",
          closeText:"关闭"
        });
  }

  if ($("#reset").datetimepicker) {
    $("#reset").datetimepicker({
          changeMonth: true,
          changeYear: true,
          dateFormat:"yy-mm-dd",
          timeFormat: "HH:mm",
          showOn: "both",
          buttonImage: "/ams/public/images/date.png",
          buttonImageOnly: true,
          monthNamesShort: ["1月", "2月", "3月", "4月", "5月", "6月", "7月", "8月", "9月", "10月", "11月", "12月"],
          dayNamesMin: ["日", "一", "二", "三", "四", "五", "六"],
          timeText:"时间",
          minuteText:"分钟",
          hourText:"小时",
          currentText:"当前",
          closeText:"关闭"
        });
  }

});
