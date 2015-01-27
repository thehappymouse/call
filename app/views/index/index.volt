<!--header--><!-- import css-->
{{ stylesheet_link('css/navibar.css') }}

{{ stylesheet_link('css/infobar.css') }}
<STYLE type=text/css>
    BODY {
        MARGIN: 0px;
        BACKGROUND-COLOR: #eaeff5
    }

    .logo {
        BACKGROUND: url(/call/public/images/logo_default.gif);
        HEIGHT: 54px;
        no-repeat: left
    }
</STYLE>
<script>
    $(document).ready(function () {
    getNum = function() {
            $.ajax({
                dataType:'json',
                url:'/ams/message/count',
                type:'POST',
                success:function(v) {
                    var n="";
                    if (v.success) {
                        if (v.data > 0) {
                            n = v.data;
                        }
                        $("#messageCount").remove();
                        var tpl = "<span id='messageCount' style='color:red'>"+n+"</span>"
                        $("#message").append(tpl);
                    }
                }
            })
        }

        setT = function() {
            getNum();
            setTimeout('setT()',50000);
        }

        setT();

        var obj = {"#mainMenu_tab_1": "#mainMenu_menu_1", "#mainMenu_tab_2": "#mainMenu_menu_2", "#mainMenu_tab_3": "#mainMenu_menu_3", "#mainMenu_tab_4": "#mainMenu_menu_4", "#mainMenu_tab_5": "#mainMenu_menu_5", "#mainMenu_tab_6": "#mainMenu_menu_6"}
        $("UL:eq(0) LI").each(function (item) {
            var select = "#mainMenu_tab_" + (parseInt(item) + 1);
            $(select).click(function () {
                var id = obj[select];
                for (var i in obj) {
                    $(i + " a").removeClass("active");
                    if (obj[i] != id) {
                        $(obj[i]).css("display", "none");
                    }
                }
                $(select + " a").addClass("active");
                $(id).css("display", "block");
            })
        })

        $("#mainMenu_menu UL").each(function (item) {
            $(this).children().each(function (litem) {
                $(this).children().click(function (item) {
                    var parentText = $(".active").text();
                    var childrenText = $(this).text();
                    var str = "&nbsp;&nbsp;" + parentText + ">>" + childrenText;
                    $("#TitlePath").empty();
                    $("#TitlePath").append(str);
                });
            })
        })

        logout = function () {
            window.location.href = "index/logout";
        }

    })
</script>
<div id="main" style="height: 90px;">
    <TABLE id="navTab" cellSpacing=0 cellPadding=0 width="100%" border=0>
        <TBODY>
        <TR class=logo>
            <TD class=topRight id=navimenuTableID>

                <DIV class=naviBar>
                    <UL class=naviTabs id=mainMenu_NaviTabs><!--onclick=javascript:mainMenu.setTab(1)-->

                        {{ elements.getMenu() }}
                    </UL>
                </DIV>
                <DIV class=naviMenu id="mainMenu_menu">

                    {{ elements.getSubMenu() }}
                </DIV>
            </TD>
        </TR>
        </TBODY>
    </TABLE>

    <TABLE class=infobar style="margin-top:-2px;" cellSpacing=0 cellPadding=0 width="100%" border=0>
        <TBODY>
        <TR>
<!--            收费模块>>收费-->
            <TD class="infopath">当前位置:&nbsp;<SPAN id="TitlePath">&nbsp;&nbsp;</SPAN></TD>
            <TD align="right" width=330>
                <TABLE class="buttonInfo" id="buttonInfo" cellSpacing="0" cellPadding="0"
                       border="0">
                    <TBODY>
                    <TR><TD style="cursor:pointer;" onclick="passWinAlert()"><A title=员工名 style="FONT-SIZE:14px;WIDTH: 100%; HEIGHT: 100%">
                                <?php echo $name; ?>&nbsp;&nbsp;</A>
                        </TD>
                        <TD width="120">
                            <DIV class="time" id="serverDateField"
                                 style="PADDING-RIGHT: 10px"></DIV>
                        </TD>

                        <TD id="message" class="issueRegisterButton" style="cursor:pointer;" onclick="messageaAlert()"><A title=消息
                                                                                                            style="WIDTH: 100%; HEIGHT: 100%"></A>
                        </TD>
                        <TD class="logoutbutton" onclick="logout()" style="cursor:pointer;"><A title="退出"
                                                                                               style="WIDTH: 100%; HEIGHT: 100%"></A>
                        </TD>
                    </TR>
                    </TBODY>
                </TABLE>
            </TD>
        </TR>
        </TBODY>
    </TABLE>
</div>

<script type=text/javascript>

    var curDate = new Date();
    var serverTime = 1409644390152;
    var interval = serverTime - curDate.getTime();
    Date.prototype.Format = function (fmt) { //author: meizz
        var o = {
            "M+": this.getMonth() + 1, //月份
            "d+": this.getDate(), //日
            "h+": this.getHours(), //小时
            "m+": this.getMinutes(), //分
            "s+": this.getSeconds(), //秒
            "q+": Math.floor((this.getMonth() + 3) / 3), //季度
            "S": this.getMilliseconds() //毫秒
        };
        if (/(y+)/.test(fmt)) fmt = fmt.replace(RegExp.$1, (this.getFullYear() + "").substr(4 - RegExp.$1.length));
        for (var k in o)
        if (new RegExp("(" + k + ")").test(fmt)) fmt = fmt.replace(RegExp.$1, (RegExp.$1.length == 1) ? (o[k]) : (("00" + o[k]).substr(("" + o[k]).length)));
        return fmt;
    }
    function setServerTime(getid) {
        document.getElementById(getid).innerText =  new Date().Format("yyyy-MM-dd hh:mm:ss");
                setTimeout("setServerTime('" + getid + "')", 1000);
    }
    setServerTime("serverDateField");
</script>


<script type="text/javascript">

    Ext.onReady(function () {
        var menu = new Ext.Panel({
            region: 'north',
            el: main
        });
        var p = new Ext.Panel({
            region: 'center',
            html: "<iframe name=PageFrame src='site/index2' frameborder='no'  width='100%' scrolling='no' height='100%' />"
        });
        new Ext.Viewport(
            {
                layout: 'border',
                items: [menu, p]
            });
    });
</script>