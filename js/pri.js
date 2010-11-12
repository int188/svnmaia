var offsetX = 10;                  //自定义X坐标偏移量
var offsetY = 0;                   //自定义Y坐标偏移量
var indiv=0;
var loadingInfo = "正在加载……";   //加载过程提示信息
var tipDiv;                         //定义存放提示信息的div对象
var currKeyword;                    //当前正在加载的keyword

var xmlHttp;                        //用于保存XMLHttpRequest对象的全局变量
var tipCache = new Array();         //用于缓存提示信息的数组

//关键词的直接调用函数入口，用于显示提示信息
function showTip(keyword, srcObj) {
    currKeyword = keyword;                      //将传入的keyword存储到当前关键词变量中

    //如果存放提示信息的div对象尚未建立，则创建一个新的div对象
    if(!tipDiv) {
        tipDiv = document.createElement("div"); //创建新的div对象
        tipDiv.style.position = "absolute";     //新div的定位方式设置为绝对定位
        tipDiv.id = "tipDiv";                   //设置新div的id值设置
        tipDiv.setAttribute("onmouseover","nohide()");//Firefox浏览器
        if(navigator.userAgent.indexOf("MSIE")>0) 
           tipDiv.attachEvent("onmouseover",nohide);//IE浏览器
        tipDiv.setAttribute("onmouseout","hide()");
        if(navigator.userAgent.indexOf("MSIE")>0) 
           tipDiv.attachEvent("onmouseout",hide);
        //tipDiv.onmouseout="hide()";
        document.body.appendChild(tipDiv);      //在页面中追加该对象
    }

    locateTip(srcObj);                          //根据关键词位置确定详细信息的显示位置
    loadTip(keyword);                           //根据关键词标识加载详细信息
    tipDiv.style.display = "block";             //设置对象的显示状态为“显示”
}

//根据关键词位置确定详细信息的显示位置
function locateTip(srcObj) {
    var tipLeft = srcObj.offsetLeft + srcObj.offsetWidth + offsetX; //确定左边距
    var tipTop = srcObj.offsetTop + offsetY;    //确定右边距

    tipDiv.style.left = tipLeft + "px";         //设定左边距，单位为像素
    tipDiv.style.top = tipTop + "px";           //设定右边距，单位为像素
}

//隐藏详细信息
function hideTip1() {
	if(indiv==0){
    tipDiv.style.display = "none";
  }
}
function hide() {
	  indiv=0;
    tipDiv.style.display = "none";
}
function hideTip() {
    setTimeout("hideTip1()",300);
}
function nohide()
{
	indiv=1;
	tipDiv.style.display = "block";
}

//将详细信息的具体内容写入tipDiv中
function displayTip(content, isLoading) {
    tipDiv.innerHTML = content;                 //将内容写入tipDiv

    //当信息不是加载过程的提示信息时，将获取的结果写入缓存数组
    if (!isLoading) {
        tipCache[currKeyword] = content;
    }
}

//用于创建XMLHttpRequest对象
function createXmlHttp() {
    //根据window.XMLHttpRequest对象是否存在使用不同的创建方式
    if (window.XMLHttpRequest) {
       xmlHttp = new XMLHttpRequest();                  //FireFox、Opera等浏览器支持的创建方式
    } else {
       xmlHttp = new ActiveXObject("Microsoft.XMLHTTP");//IE浏览器支持的创建方式
    }
}

//从服务器加载关键词的详细信息
function loadTip(currKeyword) {
    //如果缓存中存在需要加载的关键词详细信息，直接从缓存读取
    if(tipCache[currKeyword]){
        tipDiv.innerHTML = tipCache[currKeyword];
        return;                                     //从缓存读取后终止函数调用
    }
    displayTip(loadingInfo, true);                  //显示“正在加载……”提示信息

    createXmlHttp();                                //创建XMLHttpRequest对象
    xmlHttp.onreadystatechange = loadTipCallBack;   //设置回调函数
    xmlHttp.open("GET", "../priv/viewpriv.php?u=" + currKeyword, true);
    xmlHttp.send(null);
}

//获取查询选项的回调函数
function loadTipCallBack() {
    if (xmlHttp.readyState == 4) {
        displayTip(xmlHttp.responseText);           //显示加载完毕的详细信息
    }
}
